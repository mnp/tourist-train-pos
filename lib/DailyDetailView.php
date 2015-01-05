<?php

require_once 'DetailView.php';
require_once 'TBoothActivity.php';

/**
* DailyDetailView - container for template operations
*
* @access public
* @package DailyDetailView
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class DailyDetailView extends DetailView
{
  /**
   * @access public
   */
  function makeTable(&$trains)
  {
    $this->table_name = "Daily";
    $this->table_title = "Daily Booth Activity";
    parent::setEditItems();

    $trainbuttons = '';
    foreach ($trains as $t) {
      $trainbuttons .= '<td>'
	. MNP::popup_action($t->toBriefString(), 'train-accounting.php', 
			    'actionId', $t->trainId)
	.'</td>';
    }
    $this->items['Edit Trains'] = '<table cellspacing="5"><tr>' 
      . $trainbuttons . '</tr></table>';

    $ba = &$this->data_object;

    $this->actions[] = <<<EOS
<table><tr><td align="left">
Instructions:<ol>
<li>Edit each train of the day by pressing its 'Edit Train' button</li>
<li>On the form that pops up, fill in ticket numbers and receipts there, then 'Save Changes and Close'</li>
<li>Fill in the values on this sheet after counting out.</li>
<li>Press 'Create'.</li>
<li>You may make changes any time today with the 'Save Changes' button. </li>
</ol></td></tr></table>
EOS;
    if (isset($ba->id)) {
      // There is a record
      $this->actions[] = MNP::action($this->formname, 'update', 
				     'Save Changes', $ba->id);
      $this->actions[] = MNP::popup_action('View Check Page', 
					   'daily-check.php', 
					   'actionId', $ba->id);
      $this->actions[] = MNP::popup_action('View Report', 'daily-report.php', 
					   'actionId', $ba->id);
    }
    else {
      $this->actions[] = MNP::action($this->formname, 'create', 'Create', 
				     null);
    }  
  }

  function subT($r) 
  {
    return '<span class="total">' . MNP::dollars($r) . '</span><p>';
  }

  /**
   * makeDisplay - 
   *
   * @access public
   */
  function makeDisplay (&$totals)
  {
    $ba =& $this->data_object; 
    $this->table_name = "Daily Summary";
    $this->table_title = "Daily Summary";

    // sets $this->items
    parent::setDisplayItems(null, false); 

    // now hijack it

    $this->items['comments'] = nl2br($this->items['comment']);

    $this->items['totalPassengersPaid'] = $totals['paidTicketTotal'];
    $this->items['ticketRevenue']       = MNP::dollars($totals['ticketRevenue']);
    $this->items['lunchRevenue'] 	= MNP::dollars($totals['lunchRevenue']);
    $this->items['lunchReceipts'] 	= MNP::dollars($totals['lunchReceipts']);
    $this->items['ticketReceipts'] 	= MNP::dollars($totals['ticketReceipts']);
    $this->items['ticketTax'] 	        = MNP::dollars($totals['ticketTax']);
    $this->items['lunchTax'] 		= MNP::dollars($totals['lunchTax']);
    $this->items['totalCurrency'] 	= MNP::dollars($ba->currency + $ba->coins + 
						      $ba->ccards + $ba->travChecks + 
						      $ba->persChecks);
    $this->items['totalRevenue'] 	= MNP::dollars($totals['totalRevenue']);
    $this->items['tax'] 		= MNP::dollars($totals['tax']);
    $this->items['totalReceipts'] 	= MNP::dollars($totals['receiptTotal']);
    $this->items['lessDeposits']        = MNP::dollars($totals['deposits']);    
    $this->items['acctsReceivable']     = MNP::dollars($totals['receivables']);
    $this->items['overpayments']        = MNP::dollars($totals['overpayments']);    
    $this->items['amtToDeposit']        = MNP::dollars($totals['amtToDeposit']);
    $this->valueString = MNP::bufferedOutputTemplate('dailySummary.html', $this->items);
    
    return;

    $comment_save = $this->items['Comments'];
    $ls_save_k = 'Cash Over (+) or Short (-)';
    $ls_save_v = $this->items[$ls_save_k];
    unset($this->items[$ls_save_k]);
    unset($this->items['Comments']);

    $this->items['Total Currency'] = 
      $this->subT($ba->currency + $ba->coins + $ba->ccards + $ba->travChecks + 
		  $ba->persChecks);

    $this->items['Total Passengers Paid'] = MNP::ralign($i1, $totals['paidTicketTotal']);
    $this->items['Ticket Revenue']    = MNP::ralign($i2, MNP::dollars($totals['ticketRvenue']));
    $this->items['Box Lunch Revenue'] = MNP::ralign($i2, MNP::dollars($totals['boxLunchRevenue']));
	
    $this->items['Total Revenue'] = MNP::dollars($totals['ticketRevenue']);
    $this->items['Tax'] = MNP::dollars($totals['tax']);
    $this->items['Total Receipts'] = MNP::dollars($totals['receiptTotal']);

    $this->items['Less Deposits'] = MNP::dollars($totals['deposits']);    
    $this->items['Accts Receivable'] = MNP::dollars($totals['receivables']);
    $this->items['Overpayments'] = MNP::dollars($totals['overpayments']);    
    $this->items[$ls_save_k] = $ls_save_v;
    $this->items['Amount to Deposit'] = $this->subT($totals['amtToDeposit']);

    $this->items['Daily Comment'] = nl2br($comment_save);
  }
}
