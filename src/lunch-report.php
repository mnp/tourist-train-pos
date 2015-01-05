<?  // generic report

require_once '../lib/base.php';

$date = $_GET['boxlunch_date'];
$description = "Box Lunches Reserved $date";
$query = "SELECT SUM(boxLunches) as Lunches, time_format(runTime, '%h:%i %p') as 'Run Time'
	 FROM tReservation, tTrain, tSchedRun, tTime
	 WHERE (tTrain_id_1=trainId OR tTrain_id_2=trainId) 
	     AND tSchedRun_id=runId AND tTime_id=tTime.id AND date='$date'
	 GROUP BY tTime.id";


$obj = execute($query, $description, array('Lunches'));

$page_style = "../styles/report.css";
include '../templates/report-header.inc';
MNP::outputTemplate('executeQuery.html', $obj);
include '../templates/report-footer.inc';

// ------------------------------------------------------------------------------

function execute($query, $description, $sumcols=null)
{
  foreach ($sumcols as $col) {
    $sums[$col] = 0;
  }
  
  $out = new StdClass;
  $out->description = $description;
  $out->date = date('g:ia n/j/y');
  $found = 0;
  $result = MNP::rawQuery($query);

  // run the the query 
  while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
    $out->values[] = $row;
    if ($sumcols) {
      foreach ($sumcols as $col) {
	if ($row[$col]) { 
	  $sums[$col] += $row[$col]; 
	}
      }
    }
    $found = true;
  }

  // Titles
  if ($found) {
    $out->okay   = true;
    $out->titles = array_keys($out->values[0]);
  }

  // Totals
  if ($sumcols && $found) {
    $sumrow = array();
    foreach ($out->titles as $title) {
      $sumrow[$title] = array_key_exists($title, $sums) 
	? $sums[$title]
	: '';
    }
    $out->totals = $sumrow;
  }
  
  return $out;
}

?>
