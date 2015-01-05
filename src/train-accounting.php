<?php

require_once '../lib/base.php';
require_once 'TSchedRun.php';
require_once 'TReservation.php';
require_once 'TrainAccountingDetailView.php';

$page_activity = 'Train Accounting';
$page_title = 'Train Accounting';
$no_menu = 1;

// Train id passed as arg.  Fetch.

$trainId = isset($_GET['actionId']) 
     ? $_GET['actionId'] 
     : @$_POST['actionId'];
assert(!empty($trainId));
$train = new DataObjects_TTrain;
assert($train->get($trainId));
$train->getLinks();

// Input
if (isset($_POST['actionName'])) {
  switch ($_POST['actionName']) {
  case 'save':
    $train->setFrom($_POST);
    $err = $train->update();
    if (true === $err) {
      echo '<html><head>'
	. MNP::wrapJavascript("function bye() {window.close();}")
	. '</head><body><body onLoad="bye()"></body></html>';
      exit;
    }
    break;
  default:
    die("bad switch");
  }
}

// MNP::okay($train->update(), "Saving train {$train->trainId}");    

// Display
require_once ADMIN_TEMPLATES . '/common-header.inc';
$formname = "TrainAccounting";
require_once ADMIN_TEMPLATES . '/form-header.inc';

$view = new TrainAccountingDetailView($formname, $train, true);
$view->makeTable();
echo $view->toHtml(false);

include ADMIN_TEMPLATES . '/form-footer.inc';
include ADMIN_TEMPLATES . '/common-footer.inc';

// ---------------------------------------------------------------------------

?>