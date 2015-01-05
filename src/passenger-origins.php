<?  
require_once '../lib/base.php';
require_once 'Barchart.php'; 
require_once 'HTML/Select/Common/Country.php';

// just die if these aren't defined, error from parent
$date1 = $_GET['date1'];
$date2 = $_GET['date2'];
$ts = trains($date1, $date2);	// common sql frag
$pxs = px();			// common sql frag
$walkups = array(true, false);
$types   = array(CUST_TYPE_RACK, CUST_TYPE_GROUP, CUST_TYPE_SPECIAL);

// 
// Raw SQL access because none of these things are objects
//
if (DB::isError($db = DB::connect(DSN))) {
  die ($db->getMessage());
}
$db->setFetchMode(DB_FETCHMODE_ORDERED);

// data arrays for barcharts
$states = array();		// (id => (numRack, numGroup, numSpecial))
$countries = array();		// (id => (numRack, numGroup, numSpecial))

foreach ($walkups as $wup) {
  foreach ($types as $type) {
    query($states, $type, stateSql($type, $wup, $pxs, $ts));    
  }
}

foreach ($walkups as $wup) {
  foreach ($types as $type) {
    query($countries, $type, countrySql($type, $wup, $pxs, $ts));
  }
}

// map state abbrvs to uppercase
$tmp = array();
foreach ($states as $id=>$counts) {
  $tmp[strtoupper($id)] = $counts;
}
$states = $tmp;

// Save the xx country, map country codes to names, then add xx as Other.
$countryNames = new HTML_Select_Common_Country();
$c2 = array();
foreach ($countries as $id=>$counts) {
  if ($id == 'xx') {
    $name = 'Other';
  }
  else {
    $name = $countryNames->getName($id);
    $name = join(' ', array_map('ucfirst', 
				preg_split('/\s+/', strtolower($name))));
  }
  $c2[$name] = $counts;
}
$countries = &$c2;

// sort descending by total passengers
function cmp ($a, $b) {
  global $arr;
  $asum = array_sum($arr[$a]);
  $bsum = array_sum($arr[$b]);
  if ($asum == $bsum) return 0;
  return ($asum > $bsum) ? -1 : 1; 
}
$arr = &$states;
uksort($states, 'cmp');
$arr = &$countries;
uksort($countries, 'cmp');

$page_title = "Passenger Origins - $date1 to $date2";
$page_style = "../styles/report.css";
include '../templates/report-header.inc';

$b1 = new Barchart('US Passenger Origins, by State', 
		  array('Rack', 'Group', 'Special'),
		  $states);
$b2 = new Barchart('Foreign Passenger Origins, by Country', 
		  array('Rack', 'Group', 'Special'),
		  $countries);
echo $b1->toHtml();
echo $b2->toHtml();
echo '</body></html>';

// ----------------------------------------------------------------------

function query(&$a, $type, $sql)
{
  global $db;

  $sql = preg_replace('/[\n\t ]/', ' ', $sql);
  if (DB::isError($result = $db->getAll($sql))) {
    die ($result->getMessage());
  } 
  foreach ($result as $row) {
    if (array_key_exists($row[0], $a)) {
      $a[$row[0]][$type - 1] += $row[1];
    }
    else {
      $a[$row[0]] = array(0, 0, 0); // numRack, numGroup, numSpecial)
      $a[$row[0]][$type - 1] = $row[1];
    }
  }
}

function px() 
{
  return "(SUM(adults) + SUM(children) + SUM(laps) + SUM(specials) + 
	SUM(escorts)) AS px";
}

function trains ($date1, $date2) 
{
  return "((tTrain_id_1=trainId OR tTrain_id_2=trainId)
	AND date<='$date2' AND date>='$date1')";
}

//  @return sql (state code, passengers) 
function stateSql($custType, $walkup, $px, $trains)
{
  return $walkup
    ? // walkup reservations which have no customers but have states
    "SELECT walkupState, $px FROM tReservation,tTrain
	WHERE (tCustomer_id IS NULL OR tCustomer_id<1) 
	AND $trains
	AND walkupType=$custType
	AND walkupState IS NOT NULL AND walkupState<>''
	GROUP BY walkupState"
    : // reservations which have real customer records that have states
    "SELECT state, $px FROM tCustomer,tReservation,tTrain
	WHERE tCustomer_id=custId 
	AND $trains
	AND tCustomer_id>0 
	AND tCustType_id=$custType
	AND state IS NOT NULL AND state<>''
	GROUP BY state";
}

//  @return sql (country code, passengers) 
function countrySql ($custType, $walkup, $px, $trains)
{
  return $walkup
    // walkup reservations which have no customers but have countries
    ? "SELECT walkupCountry, $px FROM tReservation,tTrain
	WHERE (tCustomer_id IS NULL OR tCustomer_id<1) 
	AND $trains
	AND walkupCountry IS NOT NULL 
	AND walkupCountry<>''
	AND walkupType=$custType
	GROUP BY walkupCountry ORDER BY px DESC"
    // reservations which have real customer records that have non-US countries
    : "SELECT country, $px FROM tCustomer,tReservation,tTrain
	WHERE tCustomer_id=custId 
	AND $trains
	AND tCustomer_id>0 
	AND tCustType_id=$custType
	AND country<>'' AND country IS NOT NULL AND country<>'us'
	GROUP BY country ORDER BY px DESC";
}

include '../templates/report-footer.inc';

?>


