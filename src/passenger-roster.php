<?  // Reservation List

require_once '../lib/base.php';
require_once 'TTrain.php';
require_once 'CheckinSelectionView.php';
require_once 'TReservation.php';

$run = $_GET['train'];
$date = $_GET['date'];

if (is_null($t = DataObjects_TTrain::findDateRunTrain($run, $date))) {
  MNP::error("No $run train on $date.", 1);
}

$page_title = "Passenger Roster - " . $t->toString();
$page_style = "../styles/report.css";
include '../templates/report-header.inc';

$rs = DataObjects_TReservation::getTrainReservations($t, true, true);

$resview = new CheckinSelectionView('-- NO FORM --', $rs, FALSE);
$resview->makeTable(false);
$resview->table_title = '';
echo '<p><span class="header">' . $t->toBriefString() . '</span><br>' 
  . $resview->toHtml() . '</p>';
 
include '../templates/report-footer.inc';
?>
