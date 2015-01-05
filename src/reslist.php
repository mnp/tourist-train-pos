<?  // Reservation List

require_once '../lib/base.php';
require_once 'TTrain.php';
require_once 'CheckinSelectionView.php';
require_once 'TReservation.php';

$date = isset($_GET['reslist_date']) ? $_GET['reslist_date'] : $today;
$c = 0;

$page_title = "Reservations - $date";
$page_style = "../styles/report.css";
include '../templates/report-header.inc';

$trains = DataObjects_TTrain::getDateTrains($date);

foreach ($trains as $t) {  
  $rs = DataObjects_TReservation::getTrainReservations($t);
  $resview = new CheckinSelectionView('-- NO FORM --', $rs, FALSE);
  //'Reservations - ' . $t->toBriefString());
  $resview->makeTable(false);
  $resview->table_title = '';
  echo '<p><span class="header">' . $t->toBriefString() . '</span><br>' 
    . $resview->toHtml() . '</p>';
}

include '../templates/report-footer.inc';
?>
