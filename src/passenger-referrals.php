<?  

// Passenger source report

require_once '../lib/base.php';
require_once 'TSourceType.php';
require_once 'Barchart.php';

// just die if these aren't defined, error from parent
$date1 = $_GET['date1'];
$date2 = $_GET['date2'];

// 
// Raw SQL access because none of these things are objects
//
if (DB::isError($db = DB::connect(DSN))) {
  die ($db->getMessage());
}
$db->setFetchMode(DB_FETCHMODE_ORDERED);
 
$types = array(CUST_TYPE_RACK, CUST_TYPE_GROUP, CUST_TYPE_SPECIAL);
$sources = array();		// (id => (numRack, numGroup, numSpecial))

foreach ($types as $type) {
  $sql = "SELECT tSourceType_id, count(*) FROM tCustomer
	WHERE tCustType_id=$type
	AND created>='$date1'
	AND created<='$date2'
	GROUP BY tSourceType_id";
  query($sources, $type, $sql);
}

$db->disconnect();

$page_title = "Customer Referrals - Customers Created $date1 to $date2";
$page_style = "../styles/report.css";
include '../templates/report-header.inc';

$s2 = array();
foreach ($sources as $id=>$n) {
  if ($id > 0) {
    $st = DataObjects_TSourceType::staticGet($id);
    $name = is_null($st) ? 'Unknown' : $st->name;  
  }
  else {
    $name = 'Unknown';
  }  
  $s2[$name] = $n;
}

// sort descending by total passengers
function cmp ($a, $b) {
  global $arr;
  $asum = array_sum($arr[$a]);
  $bsum = array_sum($arr[$b]);
  if ($asum == $bsum) return 0;
  return ($asum > $bsum) ? -1 : 1; 
}
$arr = &$s2;
uksort($s2, 'cmp');

$b = new Barchart('Customer Referrals', 
		  array('Rack', 'Group', 'Special'),
		  $s2);
echo $b->toHtml();

// ----------------------------------------------------------------------

function query(&$a, $type, $sql)
{
  global $db;
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

include '../templates/report-footer.inc';

?>
