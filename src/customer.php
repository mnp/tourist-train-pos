<?php

// Customers
// $Id: customer.php,v 1.1 2003/03/21 20:56:41 mitch Exp $
// $Source: /4g/cvsroot/horde/admin/customer.php,v $

require_once '../lib/base.php';

require_once ADMIN_BASE . '/lib/CustomerSelectionView.php';
require_once ADMIN_BASE . '/lib/CustomerDetailView.php';
require_once 'MnpLayout.php';
require_once 'HTML/Template/Flexy.php';
require_once 'TSeason.php';
require_once 'TCustomer.php';

$page_activity = 'Cust';
$page_title = 'Customer Management';
require ADMIN_TEMPLATES . '/common-header.inc';

echo MNP::hiddenHtml('custId');
echo MNP::hiddenHtml('resId');

// Get standard rack and group rates.
$rates = DataObjects_TSeason::getAllRates($current_season);

// the form
$formname = "customer";
require ADMIN_TEMPLATES . '/form-header.inc';

$setrates = <<< EOSTUFF
function setrack() {
  document.{$formname}.a1Rate.value = {$rates['kA1Rate']};
  document.{$formname}.c1Rate.value = {$rates['kC1Rate']};
  document.{$formname}.a2Rate.value = {$rates['kA2Rate']};
  document.{$formname}.c2Rate.value = {$rates['kC2Rate']};
  disable('a1Rate');
  disable('a2Rate');
  disable('c1Rate');
  disable('c2Rate');
}

function setgroup() {
  document.{$formname}.a1Rate.value = {$rates['gA1Rate']};
  document.{$formname}.c1Rate.value = {$rates['gC1Rate']};
  document.{$formname}.a2Rate.value = {$rates['gA2Rate']};
  document.{$formname}.c2Rate.value = {$rates['gC2Rate']};
  disable('a1Rate');
  disable('a2Rate');
  disable('c1Rate');
  disable('c2Rate');
  }

function setspecial() {
  document.{$formname}.a1Rate.value = {$rates['sA1Rate']};
  document.{$formname}.c1Rate.value = {$rates['sC1Rate']};
  document.{$formname}.a2Rate.value = {$rates['sA2Rate']};
  document.{$formname}.c2Rate.value = {$rates['sC2Rate']};
  enable('a1Rate');
  enable('a2Rate');
  enable('c1Rate');
  enable('c2Rate');
}

function clearRates () {  
  document.customer.a1Rate.value = '';
  document.customer.a2Rate.value = '';
  document.customer.c1Rate.value = '';
  document.customer.c2Rate.value = '';
}

function pasteRates() {
  switch(document.{$formname}.tCustType_id.selectedIndex) {
  case 0: clearRates(); break;
  case 1: setrack();    break;
  case 2: setgroup();   break;
  case 3: setspecial(); break;
  default:
    alert("unexpected cust type " + 
	  document.{$formname}.tCustType_id.selectedIndex);
  }
}

EOSTUFF;

echo MNP::wrapJavascript($setrates);

//
// Form Processing - view request as a GET from another form
//

$cust = new DataObjects_TCustomer;

if (@$_POST['custId']) {
  $havecust = true;
  $custId = $_POST['custId'];
}
elseif (@$_POST['actionId']) {
  $havecust = true;
  $custId = $_POST['actionId'];
}
else {
  $havecust = false;
}

if ($havecust && $custId == -1) {
  $havecust = false;
}

if (@$_POST['actionName']) {
  $id = $_POST['actionId'];
  switch ($_POST['actionName']) {

  case 'view':
    // User selected from a choose list, after a multiple result find.
    MNP::message("One customer found.");
    $cust->get(@$_POST['actionId']);
    break;

  case 'find':
    $cust->setFrom($_POST, true);
    if (isset($_POST['custId']) && !empty($_POST['custId'])) {
      $cust->custId = $_POST['custId'];
    }
    
    $numrows = $cust->find();
    if ($numrows < 1) {
      MNP::message("No customers found as specified.  You may fill in the rest of the information as needed and press [Create] or change the information and [Find] again.");
      $cust->custId = '';
      $havecust = 0;
    }
    elseif ($numrows == 1) {
      MNP::message("One customer found.");      
      $cust->fetch();
      $havecust = 1;
      $custId = $cust->custId;
    }
    else {   // $numrows > 1
      if ($numrows > MANYSTOP) {
	MNP::warning("$numrows customers found.  Showing only " . MANYSTOP 
		     . " Narrow your search if needed.");
      }
      else {
	MNP::message("$numrows customers found.  Please choose one.");
      }
      $custview = new CustomerSelectionView($formname, $cust);
      $custview->makeTable();
      echo $custview->toHtml();
      include ADMIN_TEMPLATES . '/form-footer.inc';
      include ADMIN_TEMPLATES . '/common-footer.inc';
      exit;
    }
    break;

  case 'create':
    $cust->setFrom($_POST);
    // important: must be zero to insert.
    if ($cust->custId != 0) {
      MNP::error('The ID can not be set when creating');
    }
    else {
      $custId = $cust->insert();
      MNP::okay($custId, "Creating customer $custId");
    }
    break;

  case 'delete':
    $cust->get(@$_POST['actionId']);
    $results = $cust->deleteCustAndRes();
    MNP::okay($results, "Deleting customer $custId");
    $cust = new DataObjects_TCustomer;
    $havecust = false;
    break;

  case 'update':
    $cust->get(@$_POST['actionId']);
    $custId = $_POST['actionId'];
    $orig = $cust;		// save original
    $cust->setFrom($_POST);
    $cust->custId = $custId;    
    MNP::okay($cust->update($orig), "Updating customer $custId");    
    break;

  default:
    assert(0);
  }   // case
}     // if action
else {
    MNP::message('No customer. Use [Find] or [Create].');
    
    // set anyway in case of refresh, but we don't havecust.
    $cust->setFrom($_POST);
}
    
// Are there any reservations for this customer?
if ($havecust) {
  include_once 'TReservation.php';
  include_once 'ReservationSelectionView.php';

  $res = new DataObjects_TReservation();
  $numresfound = $res->findCustSeasonRes($custId, @$_POST['findResOnlySeason']);
  if ($numresfound > 0) {
    $resview = new ReservationSelectionView($formname, $res, FALSE);
    $resview->makeTable(false);
    $resstr = $resview->toHtml();
  }
}

//
// Form display
//

$custview = new CustomerDetailView($formname, $cust);
$custview->makeTable();
$custstr = $custview->toHtml();

if ($havecust && $numresfound > 0) {
  $layout = new MnpLayout(array($custstr, $resstr));
  $layout->output_horizontal();
}
else {
  echo $custstr;
}

include ADMIN_TEMPLATES . '/form-footer.inc';
include ADMIN_TEMPLATES . '/common-footer.inc';

?>