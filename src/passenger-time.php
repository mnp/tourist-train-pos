<?  

// Passenger reports

require_once '../lib/base.php';
require_once 'TReservation.php';
require_once 'Barchart.php';

// just die if these aren't defined, error from parent
$date1 = $_GET['date1'];
$date2 = $_GET['date2'];
$year1 = date('Y', strtotime($date1));
$year2 = date('Y', strtotime($date2));

// total riders for [year][month]
$data = array();

// Raw SQL access because none of these things are objects
if (DB::isError($db = DB::connect(DSN))) {
  die ($db->getMessage());
}
$db->setFetchMode(DB_FETCHMODE_ASSOC);
 
$sql = "SELECT sum(adults) as adults,
		sum(children) as children,
		sum(laps) as laps,
		sum(escorts) as escorts,
		sum(specials) as specials,
		(sum(adults)+sum(children)+sum(laps)+
		    sum(escorts)+sum(specials)) as total,
		concat(month(date), '/', year(date)) as m
	FROM tReservation,tTrain
	WHERE tTrain_id_1=trainId
	AND date<='$date2' AND date>='$date1'
	AND status=1
	GROUP BY month(date)";

if (DB::isError($result = $db->query($sql))) {
  die ($result->getMessage());
} 
$db->disconnect();

while ($row = $result->fetchRow()) {
  $data[$row['m']] =
    array($row['adults'],
	  $row['children'],
	  $row['laps'],
	  $row['escorts'],
	  $row['specials']);
}

$page_title = "Ridership by Month - $date1 to $date2";
$page_style = "../styles/report.css";
include '../templates/report-header.inc';

$b = new Barchart('Ridership by Month', 
		  array('Adults', 'Children', 'Laps', 'Escorts', 'Specials'),
		  $data);
echo $b->toHtml();

// ----------------------------------------------------------------------

include '../templates/report-footer.inc';

?>
