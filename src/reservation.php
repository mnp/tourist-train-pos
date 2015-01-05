<?php

error_reporting(E_ALL);

// Reservations and Customers
// $Id: reservation.php,v 1.5 2003/03/21 20:56:41 mitch Exp $
// $Source: /4g/cvsroot/horde/admin/reservation.php,v $

require_once '../lib/base.php';

require_once 'ReservationSelectionView.php';
require_once 'ReservationDetailView.php';
require_once 'HTML/Template/Flexy.php';
require_once 'TReservation.php';
require_once 'TCustomer.php';
require_once 'TTrain.php';
require_once 'TSchedRun.php';

$page_activity = 'Resvn';
$page_title = 'Reservation Management';
require ADMIN_TEMPLATES . '/common-header.inc';

// the form
$formname = 'reservation';
require ADMIN_TEMPLATES . '/form-header.inc';

//
// Form Processing - view request as a GET from another form
//

$res = new DataObjects_TReservation;
$haveres    = false;
$resId 	    = 0;
$havewalkup = false;
$havecust   = false;
$cust       = null;

function setupCustomer(&$res)
{
  global $havewalkup;
  global $havecust;
  global $cust;
  global $haveres;
  if (!empty($res->tCustomer_id) && $res->tCustomer_id > 0) {
    if (empty($res->_tCustomer_id)) {
      MNP::error("Inconsistent DB: Reservation {$res->resId} has no customer.",
		 1);
    }
    $cust = $res->_tCustomer_id;
    $custId = $cust->custId;
    $havecust = true;
    echo MNP::hiddenHtml('custId', $cust->custId);
  }
  else if (isset($_POST['custId'])) {
    $custId = $_POST['custId'];
    $cust = DataObjects_TCustomer::staticGet($custId);
    $res->_tCustomer_id = $cust;    
    $res->tCustomer_id = $custId;
    $havecust = true;
    echo MNP::hiddenHtml('custId', $cust->custId);
  }
  else if ($haveres) {
    $havewalkup = true;
  }
}

// load a res any way we can
function loadReservation () 
{
  global $haveres;
  global $resId;
  global $res;

  if (isset($_POST['actionId']) && $_POST['actionId'] > 0) {
    $resId = $_POST['actionId'];
  }
  else if (isset($_POST['resId']) && $_POST['resId'] > 0) {
    $resId = $_POST['resId'];
  }
  else {
    $haveres = false;
    return;
  }
  $res->get($resId);
  $res->getLinks();  
  setupCustomer($res);
  $haveres = true;
}


if (@$_POST['actionName'])
{
  switch ($_POST['actionName']) {
  case 'makeBlankRes':
    // In this case, actionId is the customer, and there is not
    // yet any res.  We display a readonly cust and an empty res.
    $cust = DataObjects_TCustomer::staticGet($_POST['actionId']);
    assert($cust);
    $havecust = true;
    $res->tCustomer_id = $cust->custId;
    echo MNP::hiddenHtml('custId', $cust->custId);
    break;

  case 'create':
    // custId was set in POST by caller, above, as he knew he had one.
    // TODO: merge w makeBlankRes case?
    $custId = $_POST['custId'];
    assert($custId);
    $cust = DataObjects_TCustomer::staticGet($custId);
    $havecust = true;
    echo MNP::hiddenHtml('custId', $cust->custId);
    $res->setFrom($_POST);
    $res->tCustomer_id = $custId;

    // optimize summarized name lookup: both real and walkups keep name here
    $res->walkupName = $cust->toString(); 

    // important: must be zero to insert.
    if ($res->resId != 0) {
      MNP::error('The ID can not be set when creating');
    }
    else {      
      MNP::okay($res->createRes(), "Creating reservation");
    }
    break;

  case 'setTrain1':
  case 'setTrain2':
    // We had a not-found train, user was offered a choice of an existing one,
    // and that link led to here.
    $res->setFrom($_POST);
    setupCustomer($res);
    if ($_POST['actionName'] == 'setTrain1') {
      $res->setTrain(1, $_POST['actionId']);
      $dir = 'Outbound';
    }
    else {
      $res->setTrain(2, $_POST['actionId']);
      $dir = 'Return';
    }
    MNP::message("Adjusted $dir train.");
    break;

  case 'edit':
  case 'view':
    // User selecting from a choose list, after a multiple result
    // find, or from the checkin screen.
    $resId = $_POST['actionId'];
    $res->get($resId);
    $res->getlinks();
    $haveres = true;
    setupCustomer($res);
    MNP::message('One reservation found.');
    break;

  case 'find':
    //
    // Find based on res info.
    // Note: all reservations have an associated cust record.
    //
    $res->setFrom($_POST, true);    
    $numrows = $res->findRes();
    if ($numrows < 1) {
      MNP::message('No reservations found as specified.  You may fill in the rest of the information as needed and press [Create] or change the information and [Find] again.');
      $haveres = false;
      $havewalkup = false;
      $havecust = false;
    }
    elseif ($numrows == 1) {
      $res->fetch();
      $haveres = true;
      MNP::message("One reservation found, id=$res->resId.");
      $res->getLinks();
      setupCustomer($res);
    }
    else { // $numrows > 1
      if ($numrows > MANYSTOP) {
	MNP::warning("$numrows reservations found.  Showing only " . MANYSTOP
		     . " Narrow your search if needed.");
      }
      else {
	MNP::message("$numrows reservations found.  Please choose one.");
      }
      $resview = new ReservationSelectionView($formname, $res);
      $resview->makeTable();
      echo $resview->toHtml();
      echo '<p>' . MNP::action($formname, '', 'Find Again') . '</p>';
      include ADMIN_TEMPLATES . '/form-footer.inc';
      include ADMIN_TEMPLATES . '/common-footer.inc';
      exit;
    }
    break;

  case 'delete':
    loadReservation();
    MNP::okay($res->deleteRes(), "Deleting reservation $resId");
    $resId = null;
    $res = new DataObjects_TReservation;
    $haveres = false;
    $havecust = false;
    break;

  case 'update':
    loadReservation();
    $res->setFrom($_POST);	// overwrite
    setupCustomer($res);
    MNP::okay($res->changeRes(), "Updating reservation $resId");
    break;

  case 'checkin':
    loadReservation();
    setupCustomer($res);	// ??
    MNP::okay($res->checkin(), "Checking in reservation $resId");
    break;

  case 'release':
    loadReservation();
    setupCustomer($res);	// ??
    MNP::okay($res->release(), "Releasing reservation $resId");
    break;

  default:
    assert(0);
  }
}
else {
    MNP::message('No reservation, no customer. Use [Find] or go to Customer screen and choose a customer.');
}

//
// Form display
//

$view = new ReservationDetailView($formname, $res);
$view->makeTable();
$restable = $view->toHtml();
$righttable = null;

if ($havecust)
{
  include_once 'CustomerDetailView.php';
  $cust->getLinks();
  $custview = new CustomerDetailView($formname, $cust, FALSE);
  $custview->makeDisplay();
  $righttable = $custview->toHtml();
}
else if ($havewalkup)
{
  include_once 'WalkupDisplayDetailView.php';
  $wupview = new WalkupDisplayDetailView($formname, $res, FALSE);
  $wupview->makeDisplay();
  $righttable = $wupview->toHtml();
}

if ($righttable)
{
  include_once 'MnpLayout.php';
  $layout = new MnpLayout(array($restable, $righttable));
  $layout->output_horizontal();
}
else
{
  echo $restable;
}


include ADMIN_TEMPLATES . '/form-footer.inc';
include ADMIN_TEMPLATES . '/common-footer.inc';

?>