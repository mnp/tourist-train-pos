<?php

require_once '../lib/base.php';
require_once 'TSchedRun.php';
require_once 'TBoothActivity.php';
require_once 'DailyDetailView.php';

$page_activity = 'Daily';
$page_title = 'Daily Booth Activity';
require_once ADMIN_TEMPLATES . '/common-header.inc';

$ba = new DataObjects_TBoothActivity();

// Check permissions. 
//
// Okay if: user is admin or dev; user is
// agent and date is today; user is agent and form is blank.
//
if ($session_data['groupId'] < GROUP_AGENT) {
  MNP::error($session_data['groupName'] . " users may not view daily sheets.", 
	     1);
}
else if ($session_data['groupId'] == GROUP_AGENT) 
{
  $is_admin = false;
  $date = $today;
  $stationId = $session_data['userLocationId'];
}
else {
  // admin or greater - allowed to choose location and date
  $is_admin = true;
  $date = isset($_POST['date']) ? $_POST['date'] : $today;
  $stationId = isset($_POST['stationId']) 
    ? $_POST['stationId'] 
    : $session_data['userLocationId'];
}

if ($stationId == 3 && !$is_admin) {
  MNP::error("There is no daily sheet for the office.  Please make sure you're logged in to the right location", 1);
}

//
// Inputs
//

if (@$_POST['actionName']) {
  switch ($_POST['actionName']) {
  case 'create':
    $ba->setFrom($_POST);
    $ba->id = 0;
    $ba->date = $date;
    $ba->tStation_id = $stationId;    
    $baid = $ba->insert();    
    MNP::okay($baid, "Creating daily activity record $baid");
    break;
    
  case 'view':
    doView($date, $stationId);
    break;

  case 'update':
    if ($date != $today && !$is_admin) {
      MNP::error("Only admin users may see activity reports for days other than today.", 1);
    }
    $ba->setFrom($_POST);
    $ba->id = $_POST['actionId'];
    
    MNP::okay($ba->update(), "Updating daily activity record {$ba->id}.");
    break;

  default:
    assert(0);
  }
}
else {
  $ba->tStation_id = $stationId;
  doView($date, $stationId);
}

//
// Display
//

$formname = "daily";
require_once ADMIN_TEMPLATES . '/form-header.inc';

if ($is_admin) {
  echo MNP::headerBox(array(
    'Date to View:', 
    MNP::date_button_string($formname, 'date', $date),
    'Station: ',
    MNP::selector_string('stationId', 
			 array(1 => 'Metropolis', 
			       2 => 'Gotham City'),
			 false,
			 false,
			 $stationId),
    MNP::action($formname, '', 'Go', null)));
}

$trains = DataObjects_TTrain::getDateTrains($date, $stationId);
if (count($trains) < 1) {
  MNP::error("There are no trains on $date.", 1);
}

// editable daily form

$view = new DailyDetailView($formname, $ba, true);
$view->makeTable($trains);
echo $view->toHtml(false);

include ADMIN_TEMPLATES . '/form-footer.inc';
include ADMIN_TEMPLATES . '/common-footer.inc';

// ---------------------------------------------------------------------------

function doView($wantDate, $wantSta) 
{
  global $trains;
  global $today;
  global $ba;
  global $is_admin;
  
  if ($wantDate != $today && !$is_admin) {
    MNP::error("Only admin users may see activity reports for days other than today.", 1);
  }
  
  if ($wantSta == 3) {	// is admin
    MNP::error("There is no daily sheet for the office.  Please pick another station.", 1);
  }

  // Query: the tBoothActivity record for given date.
  $ba->whereAdd("date='$wantDate'");
  $ba->whereAdd("tStation_id=$wantSta");
  
  $numfound = $ba->find();
  if ($numfound == 1) {
    $ba->fetch();
    MNP::message("Displaying $wantDate activity.");
  }
  else if ($numfound < 1) {
    MNP::message("There is no activity record for " 
		 . DataObjects_TStation::staticToString($wantSta) 
		 . " on $wantDate.  You may add one now.");
    // Set the date and station of blank BA object so they display.
    // If the object gets saved, then they will be his.
    $ba->date = $wantDate;
    $ba->tStation_id = $wantSta;
  }
  else {
    MNP::error("internal error: duplicate $wantDate records", 1);
  }
}

?>