<?php
//
// $Id: schedule.php,v 1.7 2003/03/21 20:56:41 mitch Exp $ 
// $Source: /4g/cvsroot/horde/admin/schedule.php,v $ 
//

require_once '../lib/base.php';
require_once 'MnpLayout.php';
require_once 'TSchedRun.php';
require_once 'TTrain.php';

$page_activity = 'Sched';
$page_title = 'Scheduled Run Management';
require ADMIN_TEMPLATES . '/common-header.inc';

//
// Form Processing
//
$selectedTrains = array();

if (isset($_POST['actionName']) 
    && isset($_POST['actionId'])
    && !empty($_POST['actionName']) 
    && !empty($_POST['actionId'])) 
{
  
  // Gather array of selected train id's
  foreach ($_POST as $key=>$val) 
  {
    if (preg_match('/checked_(.*)/', $key, $matches) && $val=='on') {
      $selectedTrains[] = $matches[1];
    }
  }
  
  // perform action
  $id = $_POST['actionId'];
  $t = new DataObjects_TTrain;

  switch($_POST['actionName']) {
  case 'delete':
    $res = $t->mapOp('delete', $selectedTrains);
    break;
  case 'update':
    $mapargs = array();
    if (@$_POST['changeRunCheck'] == 'on' && @$_POST['run_change']) {
      $mapargs['tSchedRun_id'] = $_POST['run_change'];
    }
    if (@$_POST['changeSeatsCheck'] == 'on' && @$_POST['seats_change']) {
      $mapargs['seats'] = $_POST['seats_change'];
    }
    if (count($mapargs) > 0) {
      $res = $t->mapOp('update', $selectedTrains, $mapargs);
    }
    else {
      $res = array(0, "Must check \"Change seats\", \"Change run\", or both when pressing [change checed trains].");
    }
    break;
  case 'create':
    $res = DataObjects_TTrain::staticCreate($_POST['fromdate'],
    					    $_POST['todate'],
    					    $_POST['run_create'],
    					    $_POST['seats_create']);
    break;
  default:
    assert(0);
  }

  // Display action status

  list($id, $msg) = $res;
  MNP::okay($id, $msg);
}

//
// Display
//

// open a form
$formname = "schedule";
require ADMIN_TEMPLATES . '/form-header.inc';

// a title bar
//$selector = DataObjects_TSeason::formCode(true, $current_season);
//MNP::title_bar("Edit Schedule - $current_season",
//		 $selector,
//		 null);

// precomputes
$run = new DataObjects_TSchedRun;
$schedruns = $run->getAll($current_season);

if (!is_array($schedruns) || count($schedruns) < 1) 
{
  MNP::message("There are no scheduled runs for this season. If you want to create any trains, you need to go to the Season screen and create some.", 1);
  require ADMIN_TEMPLATES . '/common-footer.inc';
  exit;
}

$date_buttons = "<table><tr valign=\"bottom\"><td>From</td> <td>" .
	MNP::date_button_string($formname, 'fromdate') . 
	"</td></tr>" .
	"<tr valign=\"bottom\"><td>To</td> <td>" . 
	MNP::date_button_string($formname, 'todate') . 
	"</td></tr></table>";


// table header
$titles   = array('Train Id', 'Date', 'Run', 'Seats', 'Select');
$table_title = 'Scheduled Trains - ' . $current_season;
$help_link = MNP::popupHelpLink($table_title);
$width = '';
$olddate  = '';
$bit = 0;
$today = date('Y-m-d');

ob_start();
require ADMIN_TEMPLATES . '/table-header.inc';

$train = new DataObjects_TTrain;
$cols = join(',', array_keys($train->_get_table()));
$train->query("SELECT $cols from tTrain,tSchedRun"
	. " WHERE tTrain.tSchedRun_id=tSchedRun.runId"
	. " AND tSchedRun.tSeason_id=$current_season"
        . " ORDER BY tTrain.date, tSchedRun.tTime_id"); 
	// FIXME: must descend into tTime and get the real time to sort by
	// this will fail if times were not created in order
$numfound = 0;

while($train->fetch()) {
  $numfound++;

  $train->getLinks();
  $items = array($train->trainId,
		 $train->date,
		 $train->_tSchedRun_id->toString(),
		 $train->seats,
		 MNP::input_bool('checked_' . $train->trainId));

  // Indicate date group changes
  if (strcmp($olddate, $train->date)) {
    $bit = !$bit;
  }   

  $rowclass = ($train->date == $today)
    ? 'hotitem'
    : 'item' . $bit; 
  $olddate = $train->date;

  include ADMIN_TEMPLATES . '/table-row.inc';
}

if ($numfound < 1) {
  include ADMIN_TEMPLATES . '/none-found.inc';
}
include ADMIN_TEMPLATES . '/table-footer.inc';
$train_list_table = ob_get_contents();
ob_end_clean();


// Change Table

ob_start();
$table_title = 'Change Selected Trains';
$help_link = MNP::popupHelpLink($table_title);
$titles   = array('Run', 'Seats', 'Actions');
include ADMIN_TEMPLATES . '/table-header.inc';

$run_selector = DataObjects_TSchedRun::formCode($formname, 'run_change', 
						null, $current_season, false);

$items = array($run_selector
	       . "<br><br>Change run? "
	       . MNP::input_bool("changeRunCheck", false),

	       MNP::input_number("seats_change", $train->seats)
	       . "<br><br>Change seats? "
	       . MNP::input_bool("changeSeatsCheck", false),

	       MNP::reset_form($formname, 'uncheck all trains')
	       . "<br><br>"
	       . MNP::action($formname, 'delete', 'delete checked trains', -1)
	       . "<br><br>"
	       . MNP::action($formname, 'update', 'change checked trains', -1));
include ADMIN_TEMPLATES . '/table-row.inc';
include ADMIN_TEMPLATES . '/table-footer.inc';


// Creation Table

$table_title = "Create Trains";
$help_link = MNP::popupHelpLink($table_title);
$titles   = array('Dates', 'Run', 'Seats', 'Actions');
include ADMIN_TEMPLATES . '/table-header.inc';

$items = array($date_buttons, 
	       $run->formCode($formname, 'run_create', null, $current_season,
			      false),
	       MNP::input_number("seats_create", $train->seats),
	       MNP::action($formname, 'create', 'create trains', -1));
include ADMIN_TEMPLATES . '/table-row.inc';
require ADMIN_TEMPLATES . '/table-footer.inc';
$create_and_change_tables = ob_get_contents();
ob_end_clean();

$layout = new MnpLayout(array($train_list_table, $create_and_change_tables));
$layout->output_horizontal();

include ADMIN_TEMPLATES . '/form-footer.inc';
require ADMIN_TEMPLATES . '/common-footer.inc';

?>