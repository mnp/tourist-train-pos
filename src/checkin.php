<?php
// Main agent checkin page

require_once '../lib/base.php';
require_once 'TTrain.php';
require_once 'TReservation.php';
require_once 'TSchedRun.php';
require_once 'TrainSelectionView.php';
require_once 'CheckinSelectionView.php';
require_once 'WalkupEntryDetailView.php';
require_once 'TCustType.php';
require_once 'LineItem.php';
require_once 'Receipt.php';

// TODO: this could work but only if message if formed by page's caller.  If
// this page wants an onload, we have to do all page work, THEN show page.
// Adopt Horizon's Page class here.
// 
// // arg to common-header
// if (isset($_POST['alertMessage'])) {
//   $onLoadAlert = $_POST['alertMessage'];
// }

//
// Get date or trainId args.  
//
//  - date only: trains on that (and date + 1) are shown.
// 		 rsvns for the FIRST train are shown, unless date is
//		 today, then rsvns for (first train > now) are shown.
//
//  - trainid:   date as above, rsvns for given train
//
//  - nothing:   default. show today & tomorrow.  
//		 rsvns from today, up to last train.
//
$time = time();
$stationId = $session_data['userLocationId'];
$page_activity = 'Checkin';
$page_title = 'Passenger Checkin';
unset($trainId);

// open a form
$formname = "agentmain";
$layout = new StdClass;
require ADMIN_TEMPLATES . '/common-header.inc';
require ADMIN_TEMPLATES . '/form-header.inc';

if (isset($_POST['actionName']) && $_POST['actionName'] == 'viewDate') {
  // a specific date was requested
  $date1 = $_POST['date'];
  $date2 = date('Y-m-d', strtotime($date1) + DAY_SECS);
  $train = DataObjects_TTrain::nextDeparture($date1, null, $stationId);
}
else if (isset($_POST['actionName']) && $_POST['actionName'] == 'showTrain') {
  $trainId = $_POST['actionId']; 	// train 'go' button pressed
}
else if (isset($_POST['trainId'])) {
  $trainId = $_POST['trainId'];		// previously selected train
}
  
if (isset($trainId)) {
  // subtle: derive dates to show from the requested train
  $train = DataObjects_TTrain::staticGet($trainId);
  $date1 = $train->date;
  $date2 = date('Y-m-d', strtotime($date1) + DAY_SECS);
  echo MNP::hiddenHtml('trainId', $trainId);
}

if (!isset($date1)) {
  if (isset($_POST['date'])) {
      $date1 = $_POST['date'];
      $date2 = date('Y-m-d', strtotime($date1) + DAY_SECS);
  }
  else {
    $date1 = $today;
    $date2 = $tomorrow;
  }
  $train = DataObjects_TTrain::nextDeparture($date1, null, $stationId);
}

if (empty($train)) {
  MNP::message("There are no trains today.  Choose another date below and press \"Choose Trains\".", MESSAGE_LEVEL);
}

//
// Form Processing
//

if (@$_POST['actionName']) {
  switch ($_POST['actionName']) {
    
  case 'wupRackBook':
    loadres();
    $r->setFrom($_POST);
    $id = $r->walkupReserve(CUST_TYPE_RACK, false);
    MNP::okay($id, "Rack walkup reserve " . id($id));
    break;

  case 'wupRackBookCI':
    loadres();
    $r->setFrom($_POST);
    $id = $r->walkupReserve(CUST_TYPE_RACK, true);
    checkinOkay($id, $r, "Rack walkup reserve and checkin");
    break;
    
  case 'wupGroupBook':
    loadres();
    $r->setFrom($_POST);
    $id = $r->walkupReserve(CUST_TYPE_GROUP, false);
    MNP::okay($id, "Group walkup reserve " . id($id));
    break;
    
  case 'wupGroupBookCI': 
    loadres();
    $r->setFrom($_POST);
    $id = $r->walkupReserve(CUST_TYPE_GROUP, true);
    checkinOkay($id, $r, "Group walkup reserve and checkin");
    break;
    
  case 'checkin':
    loadres(); 
    checkinOkay($r->checkin(), $r, "Checkin reservation");
    break;

  case 'paynow':
    loadres();
    $payment = @$_POST['payment'];
    MNP::okay($r->checkin($payment), "Credited \$$payment; checking in reservation $resId");
    break;

  case 'quickCheckinName':
    // FIXME, TODO: separate out finding by name from checkin, then we can use
    // checkinOkay() here. 
    MNP::okay(DataObjects_TReservation::quickCheckin(@$_POST['quickCheckinName'], $train),
		"Checking in passenger " . @$_POST['quickCheckinName'] . " by name");
    break;

  case 'release':
    loadres();
    MNP::okay($r->release(), "Releasing reservation $resId");
    break;

  case 'releaseAll':
    MNP::okay(DataObjects_TReservation::releaseAll($train),
	      "Released all reservations which were not checked in.");
    break;

  case 'delete':
    loadres();
    MNP::okay($r->deleteRes(), "Deleting reservation $resId");
    break;

  case 'showTrain':
  case 'viewDate':
    break;

  default:
    assert(0);
  }
}

//
// Display
//

// We load the train lists here because we want any side effects from
// the switch above to be reflected here.
$todayTrains    = DataObjects_TTrain::getDateTrains($date1);
$tomorrowTrains = DataObjects_TTrain::getDateTrains($date2);

//
// Walkup reserve JS.  TODO: check px not zero and check that 1 or 2
// trains were selected.  This will avoid missing work from refreshes.
//
$js = "function reserveWalkup(action) {
  if (document.$formname.walkupName.value == '') {
    alert('If this party isn\'t going to be checked in immediately, it needs a name so they can be checked in later.');
    return;
  }
  document.$formname.actionName.value = action;
  document.$formname.submit();
}
";
echo MNP::wrapJavascript($js);

$layout->datebutton = MNP::date_button_string($formname, 'date', $date1, true);
$layout->ciaction = MNP::action($formname, 'viewDate', 'Checkins');
$layout->tcaction = MNP::popup_action('Trains', 
		      'traincaps.php',
		      'traincaps_date',
		      "document.$formname.date.value");
$layout->rlaction = MNP::popup_action('Reservations', 
		      'reslist.php',
		      'reslist_date',
		      "document.$formname.date.value");

$layout->quickcheckin = 
    MNP::input_string('quickCheckinName', '', 25)
     . '&nbsp;'
     . MNP::action($formname, 'quickCheckinName', 'Go');

/*** Schedule/Avail table ***/
$layout->today    = showAvailTrains($todayTrains, $formname, $date1);
$layout->tomorrow = showAvailTrains($tomorrowTrains, $formname, $date2);

/*** Walkup Checkin ***/
$wupview = new WalkupEntryDetailView($formname, NULL, TRUE);
$wupview->makeTable();
$layout->walkup = $wupview->toHtml();

/*** reservation list ***/
$resviews = '';
$trainstr = (empty($train) ? '(no train)' : $train->toString());
$rs = DataObjects_TReservation::getTrainReservations($train);  
$resview = new CheckinSelectionView($formname, $rs, FALSE);
$resview->makeTable(true, "Checkin - $trainstr");
$resviews .= '<br>' . $resview->toHtml();
$layout->reservations = $resviews;

MNP::outputTemplate('checkin.html', $layout);

include ADMIN_TEMPLATES . '/form-footer.inc';
require ADMIN_TEMPLATES . '/common-footer.inc';

// ----------------------------------------------------------------------

function id($id) 
{
  return ($id && is_int($id)) ? 'ID ' . $id : '';
}

function loadres () 
{
  global $r;
  global $resId;
  $r = new DataObjects_TReservation;
  if (@$_POST['actionId']) {
    $resId = $_POST['actionId'];
    if ($resId > 0) {
      $r = DataObjects_TReservation::staticGetOrError($resId);
    }
  }
}

function showAvailTrains($todayTrains, $formname, $date)
{
  $view = new TrainSelectionView($formname, $todayTrains, FALSE);
  $view->makeDisplay($date);
  return $view->toHtml();
}

function checkinOkay($id, $r, $msg)
{
  if (is_int($id) && $id > 0 || true === $id) {
    if (true === $id) {
      $id = $r->resId;
    }
    $msg .= " ID {$id}: okay.  Please ring as follows:";
    $na = isset($r->adults) ? $r->adults : 0;
    $nc = isset($r->children) ? $r->children : 0;
    $nlcs = @$r->laps +  @$r->specials +  @$r->escorts;
    $items = array();
    $type = $r->getType();
    $typestr = DataObjects_TReservation::typeToString($type);
    
    if ($r->tTrain_id_2) {
      $dir = "Round Trip";
      $arate = $r->a2Rate;
      $crate = $r->c2Rate;
    }
    else {
      $dir = "One Way";
      $arate = $r->a1Rate;
      $crate = $r->c1Rate;
    }

    $items[] =& new LineItem("Adult $typestr $dir", $na, $arate);
    $items[] =& new LineItem("Child $typestr $dir", $nc, $crate);
    //$items[] =& new LineItem("L/C/S $typestr $dir", $nlcs, 0);
    if ($r->deposit > 0) {
      $items[] =& new LineItem("Deposit",             0,     0, - $r->deposit);
    }
    /*
    $rcpt = new Receipt($items);
    $rest = $rcpt->toHtml();
    MNP::message($msg, MESSAGE_LEVEL, $rest);
    */
    MNP::message($msg, MESSAGE_LEVEL);
  }
  else {
    MNP::okay($id, $msg . " failed");
  }
}

?>