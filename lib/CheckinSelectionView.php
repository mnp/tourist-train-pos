<?php

require_once 'SelectionView.php';
require_once 'TReservation.php';
require_once 'TTrain.php';
require_once 'TCustType.php';
require_once 'TSeason.php';

/**
* Checkin reservation list - Also (hack) used for reservation report
*
* @access public
* @package CheckinSelectionView
* @author Mitchell Perilstein <mitch@enetis.net>
*/
class CheckinSelectionView extends SelectionView
{
  var $first = 1;

  /* reservation sorting callback */
  function _cmp($a, $b)
  {
    return strnatcasecmp($a->getName(), $b->getName());
  }

  function isFirst()
  {
    if ($this->first) {
      $this->first = 0;
      return true;
    }
    else {
      return false;
    }
  }

  function actbutt($formname, $action, $label, $page=null)
  {
    return "<a class=\"button\" "
      . "href=\"javascript:resAction('$action', '$page');\" >"
      . '&nbsp;'. $label . '&nbsp;' . '</a>';
  }

  /**
   * @access public
   * @param  bool $report_mode false means a sort of report mode
   * @return string table html
   */
  function makeTable($report_mode=false, $name='Checkin', $showdates=false)
  {
    $this->template = 'checkin-list.html';
    $this->titles = array('ID', 'Name', '*', 'Out', 'Return', 'Type',
			  'A', 'C', 'LCS', 'Lch', 'Dep', 'Due', 'Status');

    if ($report_mode) {
      $this->titles[] = 'Select';
      $this->titles[] = 'Actions';
    }
    $this->table_title = $name;
    $this->table_name = 'Checkin';

    $sorted =& $this->data_object;  // HACK : an obj array already
    //$sorted =& $this->data_object->fetchDataObjects();

    // We need the list sorted by name.  We can't look in two places
    // for the sort key: either in the customer or in the res.
    //    $sorted =& $this->data_object->fetchDataObjects();

    $this->nonefound = count($sorted) < 1;
    usort($sorted, array($this, "_cmp"));

    // Cache the standard rates
    $sea = new DataObjects_TSeason;
    $rackRates  = $sea->getTypeRates(CUST_TYPE_RACK);
    $groupRates = $sea->getTypeRates(CUST_TYPE_GROUP);

    $alt = 0;			// item alternator

    $a1wup = 0;
    $c1wup = 0;
    $les1wup = 0;
    $a2wup = 0;
    $c2wup = 0;
    $les2wup = 0;

    $a1res = 0;
    $c1res = 0;
    $les1res = 0;
    $a2res = 0;
    $c2res = 0;
    $les2res = 0;

    foreach ($sorted as $name=>$res)
    {
      $type = $res->getType();
      $typeName = $res->typeToString($type);
      $res->getLinks();
      
      if (is_null($name = $res->getName())) {
	// don't do anything with released no-names
	if ($res->status == RES_RELEASED) {
	  continue;
	}
	// don't show checked-in no-names, but tally them
	if (empty($res->_tTrain_id_2)) {
	  $a1wup += @$res->adults;
	  $c1wup += @$res->children;
	  $les1wup += @$res->laps + @$res->escorts + @$res->specials;
	}
  	else {
	  $a2wup += @$res->adults;
	  $c2wup += @$res->children;
	  $les2wup += @$res->laps + @$res->escorts + @$res->specials;
	}
	continue;
      }
      else if ($res->status != RES_RELEASED) {
	// don't count released no-names
	if (empty($res->_tTrain_id_2)) {
	  $a1res += @$res->adults;
	  $c1res += @$res->children;
	  $les1res += @$res->laps + @$res->escorts + @$res->specials;
	}
	else {
	  $a2res += @$res->adults;
	  $c2res += @$res->children;
	  $les2res += @$res->laps + @$res->escorts + @$res->specials;
	}
      }

       switch ($type) {
       case CUST_TYPE_RACK:
	 $rates = $rackRates;
	 break;
       case CUST_TYPE_GROUP:
	 $rates = $groupRates;
	 break;
       default:
	 $rates = $res->getRates();
       }

      // change color of train if it's not today
      // also branch rates on one way / round trip
      if (empty($res->_tTrain_id_2)) {
	$train2 = '-';
	$aRate = $rates[0];	// a1
	$cRate = $rates[2];	// c1
      }
      else {
	$aRate = $rates[1];	// a2
	$cRate = $rates[3];	// c2

	if ($showdates) {
	  $train2 = $res->_tTrain_id_2->toString();
	}
	else {
	  $train2 = ($res->_tTrain_id_1->date != $res->_tTrain_id_2->date)
	    ? '(' . $res->_tTrain_id_2->date . ')'
	    :  $res->_tTrain_id_2->toBriefString();
	}
      }

      $train1 = $showdates
	? $res->_tTrain_id_1->toString()
	: $res->_tTrain_id_1->toBriefString();

      $residjs = "'document.{$this->formname}.resId'";

      // do each row as one string just so we can control the due
      // column.  sorry, template!

      $this->row[$res->resId] =
	"<td class=\"item$alt\">"

	. MNP::link($this->formname, 'view', $res->resId, $res->resId,
		     'reservation.php')
	. '</td>'
	. "<td class=\"item$alt\">"
	     . substr($name, 0, 30)
	. '</td>'
	. "<td class=\"item$alt\">"
	  . (!empty($res->resComment) 
	     ? MNP::background('error', '<b>*</b>') 
	     : '&nbsp;')
	. '</td>'
	. "<td class=\"item$alt\">"
	     . $train1
	. '</td>'
	. "<td class=\"item$alt\">"
	     .  $train2
	. '</td>'
	. "<td class=\"item$alt\">"
	     . $typeName
	. '</td>'
	. "<td class=\"item$alt\">"
	     . ($res->adults
	        ? ($res->adults . ': $' . $res->adults * $aRate)
	        : '&nbsp;')
	. '</td>'
	. "<td class=\"item$alt\">"
	     . ($res->children
	        ? ($res->children  . ': $' . $res->children * $cRate)
	        : '&nbsp;')
	. '</td>'
	. "<td class=\"item$alt\">"
	     . (($res->laps + $res->specials + $res->escorts)
	        ? ($res->laps + $res->specials + $res->escorts) . ': $0'
  	        : '&nbsp;')
	. '</td>'
	. "<td class=\"item$alt\">"
	.   $res->boxLunches
	. '</td>'
	. "<td class=\"item$alt\">"
	  . ($res->deposit ? ('($' . $res->deposit) . ')': '&nbsp;')
	. '</td>'
	. '<td align="right" class="' . ($res->amountDue==0 
					 ? "item$alt" 
					 : 'money') . '">'
	     . '$' . $res->amountDue
	. '</td>'
	. "<td class=\"item$alt\">"
	     . $res->statusString()
	. ($report_mode ? ("<td class=\"item$alt\">" 
			    . MNP::input_radio('editId', $res->resId)
			    . '</td>')
	   		 : '')
	. '</td>';

      if ($report_mode) {
	$this->rightblock =
	  array($this->actbutt($this->formname, 'edit', 'Edit',
			       'reservation.php'),
		$this->actbutt($this->formname, 'release', 'Release'),
		$this->actbutt($this->formname, 'checkin', 'Checkin: Full Pmt'),
		'&nbsp;',
		'Pmt: $' . MNP::input_number('payment', '0.00') ,
		$this->actbutt($this->formname, 'paynow', 
			       'Checkin: Acct Rcvbl'),
		'&nbsp;',
		$this->actbutt($this->formname, 'delete', 'Delete'),
		MNP::action($this->formname, 'releaseAll', 'Release All'),
		'&nbsp;',
		/*
		'<table width="80"><tr><td>'
		. 'Racks & Groups pay at checkin. '
		. 'Specials may pay on edit screen.'
		. '</td></tr></table>'
		*/
		);
      }

      $alt = !$alt;
    }
    $this->res1 = $a1res + $c1res + $les1res;
    $this->res2 = $a2res + $c2res + $les2res;
    $this->wup1 = $a1wup + $c1wup + $les1wup;
    $this->wup2 = $a2wup + $c2wup + $les2wup;
    $this->res1detail = "A:$a1res C:$c1res LCS:$les1res";
    $this->res2detail = "A:$a2res C:$c2res LCS:$les2res";
    $this->wup1detail = "A:$a1wup C:$c1wup LCS:$les1wup";
    $this->wup2detail = "A:$a2wup C:$c2wup LCS:$les2wup";
  }
}
?>
