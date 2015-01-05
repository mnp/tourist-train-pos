<?  // Reservation tracking report -- a week or two 

require_once '../lib/base.php';
require_once 'TSchedRun.php';

define ('DAYS', 20);

$date = isset($_GET['traincaps_date']) ? $_GET['traincaps_date'] : $today;
$time = strtotime($date) + DAYS * DAY_SECS;
$date2 = date('Y-m-d', $time);
$wantyear = date('Y', $time);
$runs = DataObjects_TSchedRun::getAll($wantyear);

$days = MNP::dates_in_range($date, $date2);
$c = 0;

$page_title = "Train Capacities - $date to $date2";
$page_style = "../styles/report.css";
include '../templates/report-header.inc';
?>


<table>
  <tr>
    <td> <i>(Riders/Avail)</i>
    </td>    
    
    <?
    foreach ($runs as $r) {
      echo '<th>' . $r->toString() . '</th>';
    }
    ?>
  </tr>
  <tr>
  </tr>
  <?
  foreach ($days as $d) { 
    echo '<tr>';
    echo '<th>' .  date('D m/d', strtotime($d)) . '</th>'; 

    foreach ($runs as $r) {
      $t = DataObjects_TTrain::findDateRunTrain($r->runId, $d);
      if ($t) {
	$avail = $t->getAvailableSeats();
	if ($avail < REDLEVEL) {
	  $class = 'red';
	}
	else if ($avail < YELLOWLEVEL) {	  
	  $class = 'yellow';	  
	}
	else {
	  $class = "item$c";
	}
	echo "<td class=\"$class\" align=\"right\">";
	echo $t->seats - $avail . '/' . $avail;
      }
      else {
	echo "<td class=\"item{$c}\" align=\"right\">";
	echo '-';	
      }
      echo '</td>';
    }
    echo '</tr>';
    $c = intval(!$c);
  }

  echo '</table>';
  include '../templates/report-footer.inc';
  ?>


