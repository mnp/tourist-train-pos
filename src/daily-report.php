<? 

require_once '../lib/base.php';
require_once 'TTrain.php';
require_once 'TReservation.php';
require_once 'TBoothActivity.php';
require_once 'TStation.php';
require_once 'TSeason.php';
require_once 'DailyDetailView.php';
require_once 'DailyTrainColumn.php';
require_once 'HTML/Table.php';


$baid = @$_GET['actionId'];
if (!$baid) {
  die ("Internal error - no id in POST");
}
$c = 0;

$ba = new DataObjects_TBoothActivity;
$ba->get($baid);
$staobj = DataObjects_TStation::staticGet($ba->tStation_id);
$station = $staobj->name;
$date = $ba->date;

$page_title = "Daily Accounting Report for $station Depot on $date";
$page_style = "../styles/report.css";
include '../templates/report-header.inc';

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
}
else {
  // admin or greater
  $is_admin = true;
}

$trains = DataObjects_TTrain::getDateTrains($date, $ba->tStation_id);
if (count($trains) < 1) {
  MNP::error("There are no trains on $date.", 1);
}

$data = array();
$trainNames = array();
$rsvns = array();

$season = DataObjects_TSeason::staticGet($current_season);
$lunches = array();		// lunches[train][bought_or_not]
$lunchPrice = $season->boxLunchRate;

foreach ($trains as $train ) {
  $data[]       = new DailyTrainColumn($train);
  $trainNames[] = $train->toBriefString();
  $lunches[$train->trainId][1] = 0;
  $lunches[$train->trainId][0] = 0;
}

$rtmp = new DataObjects_TReservation;
$rsvns = $rtmp->getDateReservations($date, $ba->tStation_id);

// Layout
$tableAttrs    = array('class' => 'item', 'border'=>'1', 'cellspacing'=>'0');
$headerAttrs   = array();
$leftHeaderAttrs = array('align' => 'right',
			 'width' => '160');
$cellAttrs     = array('class' => 'number');
$totalAttrs    = array('class' => 'total');
$subTotalAttrs = array('class' => 'subtotal');

$titles = array();
$titles[] = '&nbsp';
$titles = array_merge($titles, $trainNames);
$titles[] = 'Totals';

// ----------------------------------------------------------------------
// gather some totals
// ----------------------------------------------------------------------
$dataTotals = DailyTrainColumn::computeTotals($data);
$dataTotals['deposits'] = 0;
$dataTotals['tourAdultTix'] = 0;
$dataTotals['tourChildTix'] = 0;

   /* Note: fixed bug where computeTotals' result for lctTix was overwritten
      with the ticket numbers derived from reservations; as opposed to
      totalling tickets sold.  I don't know why I did that originally.
      16 Sep 03 MNP */

// $dataTotals['lcsTix']       = 0;	// 16 Sep 03 MNP

foreach ($rsvns as $res) {
  // gather some stats for daily sheets - applies to ALL rsvns 
  $rt = !empty($res->tTrain_id_2);
  $custType = $res->getType();
  $dataTotals['deposits'] += $res->deposit;

  // 16 Sep 03 MNP
  //  $dataTotals['lcsTix']   += $res->laps + $res->specials + $res->escorts;

  if ($custType == CUST_TYPE_SPECIAL || CUST_TYPE_GROUP) {
    $dataTotals['tourAdultTix'] += $res->adults;
    $dataTotals['tourChildTix'] += $res->children;
  }

  if ($res->boxLunches > 0) {
    $wasbought = ($res->status == RES_CHECKEDIN);
    // We will only have one half of the res on this sheet; no matter.
    // Just figger out which train so we can index using it.
    if (isset($trains[$res->tTrain_id_1])) {
      $lunchtrain = $res->tTrain_id_1;
    }
    else if (isset($trains[$res->tTrain_id_2])) {
      $lunchtrain = $res->tTrain_id_2;
    }
    else {
      assert(false);
    }
    $lunches[$lunchtrain][$wasbought] += $res->boxLunches;
  }
}

// ----------------------------------------------------------------------
// Ticket Table
// ----------------------------------------------------------------------

$tixFields = array('adultBegTix', 'adultEndTix', 'adultTix',
		'childBegTix', 'childEndTix', 'childTix',
		'groupBegTix', 'groupEndTix', 'groupTix',
		   'lcsBegTix', 'lcsEndTix', 'lcsTix');

$table = makeTable($titles, 'Tickets');

// left header
$row = 2;
foreach ($tixFields as $field) {
  $nn = DailyTrainColumn::niceName($field);
  $table->setHeaderContents($row, 0, $nn);
  $table->setCellAttributes($row, 0, $leftHeaderAttrs);
  $row++;  
  if (preg_match('/.*Tickets.*/', $nn)) {
    $table->setHeaderContents($row++, 0, '');
  }
}
$table->setHeaderContents($row++, 0, 'Total Tickets');
$table->setHeaderContents($row++, 0, 'Total Paid Tickets');

// finally the data
$trainCols = array();
$col = 1;
foreach ($data as $datum) {
  $row = 2;
  $table->setColAttributes($col, $cellAttrs);
  foreach ($tixFields as $field) {
    $nn = DailyTrainColumn::niceName($field);
    $table->setCellContents($row++, $col, empty($datum->$field)
					   ? '0'
					   : $datum->$field);
    if (preg_match('/Tickets/', $nn)) {
      $table->setCellAttributes($row-1, $col, $subTotalAttrs);
      $table->setHeaderContents($row++, 0, '');
    }
  }

  // bottom totals
  setTotalCell($table, $row++, $col, $datum->totalTickets);
  setTotalCell($table, $row, $col, $datum->totalPayingTickets);

  $col++;
}
  
// right totals column

setTotalCell($table, 4,  $col, $dataTotals['adultTix']); 
setTotalCell($table, 8,  $col, $dataTotals['childTix']); 
setTotalCell($table, 12, $col, $dataTotals['groupTix']); 
setTotalCell($table, 16, $col, $dataTotals['lcsTix']); 
setTotalCell($table, 18, $col, $dataTotals['ticketTotal']); 
setTotalCell($table, 19, $col, $dataTotals['paidTicketTotal']); 

$table->setRowAttributes(1, $headerAttrs);

$ticketStr = $table->toHtml();

// ----------------------------------------------------------------------
// Lunches Table
// ----------------------------------------------------------------------
$lunchFields = array('Reserved', 'Not Purchased', 
		     'Purchased at ' . MNP::dollars($lunchPrice),
		     'Total Lunch Revenue');
$table = makeTable($titles, 'Box Lunches');
$row = 2;
foreach ($lunchFields as $field) {
  $table->setHeaderContents($row, 0, $field);
  $table->setCellAttributes($row, 0, $leftHeaderAttrs);
  $row++;  
}
$col = 1;
$lunchesBoughtTot = 0;
$notlunchesBoughtTot = 0;
foreach ($trains as $train) {  
  $lunchesBought    = $lunches[$train->trainId][1];
  $notlunchesBought = $lunches[$train->trainId][0];
  $lunchesBoughtTot += $lunchesBought;
  $notlunchesBoughtTot += $notlunchesBought;
  $table->setColAttributes($col, $cellAttrs);
  $table->setCellContents(2, $col, $lunchesBought + $notlunchesBought);
  $table->setCellContents(3, $col, $notlunchesBought);
  $table->setCellContents(4, $col, $lunchesBought);
  $table->setCellContents(5, $col, MNP::money($lunchesBought * $lunchPrice)); 
  $col++;
}

$table->setCellContents(2, $col, $lunchesBoughtTot + $notlunchesBoughtTot);
$table->setCellContents(3, $col, $notlunchesBoughtTot);
$table->setCellContents(4, $col, $lunchesBoughtTot);
$table->setCellContents(5, $col, MNP::money($lunchesBoughtTot * $lunchPrice));

$table->setColAttributes($col, $totalAttrs);
$table->setRowAttributes(1, $headerAttrs);
$table->setRowAttributes(5, $totalAttrs);
$lunchStr = $table->toHtml();


// ----------------------------------------------------------------------
// Ticket Receipts Table
// ----------------------------------------------------------------------

$rcptFields = array('aRT', 'aOW', 'aTRT', 'aTOW', 'adultRcptTotal',
		'cRT', 'cOW', 'cTRT', 'cTOW', 'childRcptTotal',
		'oRT', 'oOW', 'openRcptTotal');

$table = makeTable($titles, 'Ticket Receipts');

$row = 2;
foreach ($rcptFields as $field) {
  $nn = DailyTrainColumn::niceName($field);
  $table->setHeaderContents($row, 0, $nn);
  $table->setCellAttributes($row, 0, $leftHeaderAttrs);
  $row++;
  if (preg_match('/Total/', $nn)) {
    $table->setHeaderContents($row++, 0, '');
  }
}
$table->setHeaderContents($row++, 0, 'Total Receipts $');

// finally the data
$trainCols = array();
$col = 1;
foreach ($data as $datum) {
  $row = 2;
  $table->setColAttributes($col, $cellAttrs);
  foreach ($rcptFields as $field) {
    $nn = DailyTrainColumn::niceName($field);
    $table->setCellContents($row++, $col, MNP::money($datum->$field));
    if (preg_match('/Total/', $nn)) {
      $table->setCellAttributes($row-1, $col, $subTotalAttrs);
      $table->setHeaderContents($row++, 0, '');
    }
  }

  // bottom totals
  setTotalCell($table, $row, $col, MNP::money($datum->totalReceipts));
  $col++;
}

// right totals column
$row = 2;
setSubTotalCell($table, $row++, $col, MNP::money($dataTotals['aRT'])); 
setSubTotalCell($table, $row++, $col, MNP::money($dataTotals['aOW'])); 
setSubTotalCell($table, $row++, $col, MNP::money($dataTotals['aTRT'])); 
setSubTotalCell($table, $row++, $col, MNP::money($dataTotals['aTOW'])); 
setTotalCell($table,    $row++, $col, MNP::money($dataTotals['adultRcptTotal']));
$row++;

setSubTotalCell($table, $row++, $col, MNP::money($dataTotals['cRT'])); 
setSubTotalCell($table, $row++, $col, MNP::money($dataTotals['cOW'])); 
setSubTotalCell($table, $row++, $col, MNP::money($dataTotals['cTRT'])); 
setSubTotalCell($table, $row++, $col, MNP::money($dataTotals['cTOW'])); 
setTotalCell($table,    $row++, $col, MNP::money($dataTotals['childRcptTotal']));
$row++;

setSubTotalCell($table, $row++, $col, MNP::money($dataTotals['oRT'])); 
setSubTotalCell($table, $row++, $col, MNP::money($dataTotals['oOW'])); 
setTotalCell($table, $row++, $col, MNP::money($dataTotals['openRcptTotal']));
$row++;

setTotalCell($table, $row++, $col, MNP::money($dataTotals['ticketReceipts'])); 

$table->setRowAttributes(1, $headerAttrs);

$receiptStr = $table->toHtml();

// ----------------------------------------------------------------------
// Notable Customers for Accounting 
// ----------------------------------------------------------------------
$specCheckedinStr = specialCustTable('Checked-In Customers With Deposits or Amounts Due',
				     true,
				     $dataTotals['receivables']);
$specReleasedStr = specialCustTable('Released and No-Show Customers With Deposits',
				    false,
				    $dataTotals['overpayments']);


// -----------------------------------------------------------------------
// Taxes - ticket and lunch prices include tax; "back out" the tax
// -----------------------------------------------------------------------

$dataTotals['lunchReceipts'] = $lunchesBoughtTot * $lunchPrice;
$dataTotals['lunchRevenue']  = $dataTotals['lunchReceipts'] / 1.08;
$dataTotals['lunchTax']      = $dataTotals['lunchReceipts'] - $dataTotals['lunchRevenue'];

$dataTotals['ticketRevenue'] = $dataTotals['ticketReceipts'] / 1.08;
$dataTotals['ticketTax']     = $dataTotals['ticketReceipts'] - $dataTotals['ticketRevenue'];

$dataTotals['receiptTotal']  = $dataTotals['lunchReceipts'] + $dataTotals['ticketReceipts'];
$dataTotals['tax']           = $dataTotals['lunchTax']      + $dataTotals['ticketTax'];
$dataTotals['totalRevenue']  = $dataTotals['lunchRevenue']  + $dataTotals['ticketRevenue'];


// ----------------------------------------------------------------------
// Summary cover page
// ----------------------------------------------------------------------

$dataTotals['amtToDeposit'] = $dataTotals['receiptTotal'] 
	- $dataTotals['deposits'] - $dataTotals['receivables'] + $ba->longOrShort;

$view = new DailyDetailView(null, $ba, false);
$view->makeDisplay($dataTotals);
$summaryStr = $view->toHtml();

// ----------------------------------------------------------------------
// Render
// ----------------------------------------------------------------------

echo "<p>$ticketStr</p>
      <p>$lunchStr</p>
      <p>$receiptStr</p>
      <p>$specCheckedinStr</p>
      <p>$specReleasedStr</p>
      <p>$summaryStr</p>";

include ADMIN_TEMPLATES . '/common-footer.inc';

// ------------------------------------------------------------------------

function makeTable($titles, $title)
{
  global $tableAttrs;
  
  $table = new HTML_Table($tableAttrs);
  $table->setAutoGrow(true);
  $table->setCellContents(0, 0, $title);
  $table->setCellAttributes(0, 0, array('colspan' => count($titles),
					'align' => 'center',
					'class' => 'header'));
  $col = 0; 
  foreach ($titles as $title) {
    $table->setHeaderContents(1, $col++, $title);
  }
  return $table;
}


function setTotalCell(&$t, $r, $c, $val)
{
  global $totalAttrs;  
  $t->setCellContents  ($r, $c, $val); 
  $t->setCellAttributes($r, $c, $totalAttrs); 
}

function setSubTotalCell(&$t, $r, $c, $val)
{
  global $subTotalAttrs;  
  $t->setCellContents  ($r, $c, $val); 
  $t->setCellAttributes($r, $c, $subTotalAttrs); 
}

function tableTitle(&$table, $cols, $title) 
{
  $table->setCellContents(0, 0, $title);
  $table->setCellAttributes(0, 0, array('colspan' => $cols,
					'align' => 'center',
					'class' => 'header'));
}

function fmtDetail($n, $rate, $tot) 
{
  return "$n @ " . MNP::dollars($rate) . " = " . MNP::dollars($tot);
}

/**
* @access public
* @param  string $title
* @param  bool   $checkedin
* @param  bool   $specialonly
* @return string
*/
function specialCustTable($title, $checkedin, &$return_total)
{
  global $rsvns;
  global $trains;
  global $headerAttrs;

  $titles = array('Res Id', 
		  'Customer',
		  'Type',
		  'Train',
		  'Adult RT',
		  'Child RT',
		  'Adult OW',
		  'Child OW',
		  'Total',
		  'Deposit',
		  'Due');

  $table = makeTable($titles, $title);
  $return_total = 0;
  
  $alternator = 0;
  foreach ($trains as $train) {
    $rsvns = DataObjects_TReservation::getTrainReservations($train);
    foreach ($rsvns as $res) {     
      // -- Only checked in customers with nonzero DUE or DEPOSIT below here --
      if ($checkedin) {
	// want only checkins
	if ($res->status != RES_CHECKEDIN || 
	    ($res->amountDue == 0 && $res->deposit == 0)) {
	  continue;
	}
	$return_total += $res->amountDue;
      }
      else {
	// want only released, with deposit
	if ($res->status == RES_CHECKEDIN || $res->deposit == 0) {
	  continue;
	}
	$return_total += $res->deposit;
      }

      // (open) special customer
      $rt = !empty($res->tTrain_id_2);
      $na1 = $rt ? 0 : $res->adults;
      $na2 = $rt ? $res->adults : 0;
      $nc1 = $rt ? 0 : $res->children;
      $nc2 = $rt ? $res->children : 0; 
      $a1 = $na1 * $res->a1Rate;
      $a2 = $na2 * $res->a2Rate;
      $c1 = $nc1 * $res->c1Rate;
      $c2 = $nc2 * $res->c2Rate;

      $type = $res->getType();
      $typeName = $res->typeToString($type);

      // the display items
      $values = array($res->resId, 
		      $res->getName(),
		      $typeName,
		      $train->toBriefString(),
		      fmtDetail($na2, $res->a2Rate, $a2),
		      fmtDetail($nc2, $res->c2Rate, $c2),
		      fmtDetail($na1, $res->a1Rate, $a1),
		      fmtDetail($nc1, $res->c1Rate, $c1),
		      MNP::dollars($a1 + $a2 + $c1 + $c2),
		      MNP::dollars($res->deposit ? $res->deposit : '0'),
		      MNP::dollars($res->amountDue ? $res->amountDue : '0')
		      );
      $attrs = array('class' => 'item' . $alternator);
      $table->addRow($values, $attrs);
    }  
    $alternator = !$alternator;
  }
  $table->updateColAttributes(7, array('align' => 'right'));
  $table->updateColAttributes(8, array('align' => 'right'));
  $table->setRowAttributes(1, $headerAttrs);

  return $table->toHtml();
}

include '../templates/report-footer.inc';
?>