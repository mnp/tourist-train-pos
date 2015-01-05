<?php

require_once '../lib/base.php';
require_once 'MnpLayout.php';
require_once ADMIN_BASE . '/lib/SeasonManagementView.php';
require_once ADMIN_BASE . '/lib/SchedRunManagementView.php';

$page_activity = 'Season';
$page_title = 'Season Management';
require ADMIN_TEMPLATES . '/common-header.inc';

$run = new DataObjects_TSchedRun;
$sea = new DataObjects_TSeason;

//
// Form Processing
//

if (isset($_POST['actionName'])) {
  $id = $_POST['actionId'];
  switch ($_POST['actionName']) {
  case 'createSchedRun':
    $err = $run->mapOp('insert', 0, 
		       array('tSeason_id'  => $current_season,
			     'tStation_id' => $_POST['stationCreate'],
			     'tTime_id'    => $_POST['timeCreate']));
    break;
  case 'createSeason':
    $err = $sea->mapOp('insert', 0, $_POST);
    //		       array('comment' => $_POST['seasonCommentCreate'],
    //			     'groupRate' => $_POST['groupRate'],
    //			     'childRate' => $_POST['childRate'],
    //			     'adultRate' => $_POST['adultRate'],
    //		          'id' => $_POST['seasonIdCreate']));
    $current_season = $_POST['id'];
    break;
  case 'updateSeason':
    // Go through POST and create map args like the ones TSeason expects.
    // Strip off year: cC2Rate_1999 ---> cC2Rate
    $mapargs = array();
    foreach ($_POST as $k=>$v) {
      if (preg_match("/(\S+)_$id/", $k, $matches)) {
	$mapargs[$matches[1]] = $v;
      }
    }
    $err = $sea->mapOp('update', $id, $mapargs);
    break;
  case 'deleteSchedRun':
    $err = $run->mapOp('delete', $id);
    break;
  case 'deleteSeason':
    $err = $sea->mapOp('delete', $id);
    break;
  case 'updateSchedRun':
    $err = $run->mapOp('update', $id, 
		       array('tStation_id' => $_POST['station_' . $id],
			     'tTime_id'    => $_POST['time_' . $id],
			     'tSeason_id'  => $current_season));
    break;

  default:
    assert(0);
  }

  // Display action status

  list($id, $msg) = $err;
  MNP::okay($id, $msg);
}

//
// Form display
//

// open a form
$formname = "seasonstuff";
require ADMIN_TEMPLATES . '/form-header.inc';

// Season Table
$sea = new DataObjects_TSeason;	    // use NEW object, not the messy one above
$sea->orderBy("id");
$sea->find();
$seaview = new SeasonManagementView($formname, $sea);
$seaview->makeTable();
echo $seaview->toHtml();

// SchedRun Table
$run = new DataObjects_TSchedRun;    // use NEW object, not the messy one above
$run->whereAdd("tSeason_id = $current_season");
$run->orderBy("tTime_id");
$numfound = $run->find(); 

$runview = new SchedRunManagementView($formname, $run);
$runview->makeTable($current_season);
echo '<p>';

echo $runview->toHtml();

include ADMIN_TEMPLATES . '/form-footer.inc';
include ADMIN_TEMPLATES . '/common-footer.inc';

?>