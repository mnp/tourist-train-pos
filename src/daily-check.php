<?  // Reservation List

require_once '../lib/base.php';
require_once 'TTrain.php';
require_once 'CheckinSelectionView.php';
require_once 'TReservation.php';
require_once 'TBoothActivity.php';
require_once 'DailyTrainColumn.php';

$baid = @$_GET['actionId'];
if (!$baid) {
  die ("Internal error - no id in POST");
}
$c = 0;

$ba = new DataObjects_TBoothActivity;
$ba->get($baid);
$sta = DataObjects_TStation::staticGet($ba->tStation_id);
$date = $ba->date;

$page_title = "Analysis of Daily Accounting Form - {$ba->date} - {$sta->name}";
$page_style = "../styles/report.css";
include '../templates/report-header.inc';

$trains = DataObjects_TTrain::getDateTrains($date, $ba->tStation_id);
$trainData = array();
$rsvnTotals = array();
foreach ($trains as $train ) {
  $trainData[] = new DailyTrainColumn($train);
  $rsvns = DataObjects_TReservation::getTrainReservations($train);
  foreach ($rsvns as $res) {
    $res->computeTotals($rsvnTotals);
  }
}

$trainTotals = DailyTrainColumn::computeTotals($trainData);
$pass = 0;
$fail = 0;
$gripes = array();

define('TRAINMAX', 400);	// max sane px per train


foreach ($trains as $train) {
  note($trainTotals['adultTix'] < TRAINMAX, 
       "Adult tickets for " . $train->toBriefString() . " train too large: "
       . $trainTotals['adultTix']);
  note($trainTotals['childTix'] < TRAINMAX, 
       "Child tickets for " . $train->toBriefString() . " train too large: "
       . $trainTotals['childTix']);
  note($trainTotals['groupTix'] < TRAINMAX, 
       "Group tickets for" . $train->toBriefString() . " train too large:"
       . $trainTotals['groupTix']);
  note($trainTotals['lcsTix'] < TRAINMAX, 
       "L/C/S tickets for " . $train->toBriefString() . " train too large: "
       . $trainTotals['lcsTix']);
}

echo $pass + $fail . " checks performed<br>";
echo "$pass passed<br>";
echo  "$fail failed:<br><ul>";
foreach ($gripes as $g) {
  echo "<li>$g</li>";
}
echo "</ul>";

// ---------------------------------------------------------------------------

function note($expr, $msg) 
{
  global $pass;
  global $fail;
  global $gripes;

  if ($expr) {
    $pass++;
  }
  else {
    $gripes[] = $msg;
    $fail++;
  }
}

include '../templates/report-footer.inc';
?>
