<? 

// Report reservations with deposits that were not checked in.

require_once '../lib/base.php';
require_once 'TReservation.php';
require_once 'CheckinSelectionView.php';

// just die if these aren't defined, error from parent
$date1 = $_GET['date1'];
$date2 = $_GET['date2'];

$page_title = "No-Show Reservations With Deposits - $date1 to $date2";
$page_style = "../styles/report.css";
include '../templates/report-header.inc';

$res = new DataObjects_TReservation;
$cols = join(',', array_keys($res->_get_table()));
$sql = "SELECT $cols from tReservation,tTrain WHERE
	(tTrain_id_1=trainId OR tTrain_id_2=trainId)
 	AND date>='$date1'
 	AND date<='$date2'
 	AND deposit > 0
	AND status <> 1";

$res->query($sql);
$rs =& $res->fetchAllDataObjects();
$resview = new CheckinSelectionView('-- NO FORM --', $rs, FALSE);
$resview->makeTable(false, $page_title, true);
$resview->table_title = '';
echo $resview->toHtml();

include '../templates/report-footer.inc';

?>