<?

/**
 * Class to represent one *data* column (one train) of a daily report and
 * derived numbers.
 * 
 * Its data is copied or derived from BoothActivity, Train, Reservation, 
 * etc.   Field names coinciding with vars in those classes are direct copies.
 * 
 * This thing is NOT a DB object, it's just to make test and display easier.
 * 
 *   tickets
 *   receipts
 *   totals
 *
 * NOTE: Group tickets were discontinued for the 2004 season.  We still
 * might need to display them for 2003 season runs so the sheets make sense
 * historically.  If any of the group vals are nonzero we activate those 
 * portions of the sheet. -- MNP 5/2004
 */

class DailyTrainColumn
{
  function DailyTrainColumn(&$train) 
  {
    $trainVars = array('aRes', 'cRes', 'lRes', 'eRes', 'sRes',
      'adultBegTix', 'adultEndTix', 'childBegTix', 'childEndTix',
      'groupBegTix', 'groupEndTix', 'lcsBegTix', 'lcsEndTix', 'aRT', 'aOW',
      'aTRT', 'aTOW', 'cRT', 'cOW', 'cTRT', 'cTOW', 'oRT', 'oOW');

    // copy from train
    foreach ($trainVars as $v) {
      $this->$v   = $train->$v;
    }

    $this->train = $train->toBriefString();

    // passenger totals
    // the abs() is in case the beg and end get mixed up
    $this->adultTix = abs($this->adultEndTix - $this->adultBegTix);
    $this->childTix = abs($this->childEndTix - $this->childBegTix);
    $this->groupTix = abs($this->groupEndTix - $this->groupBegTix);
    $this->lcsTix   = abs($this->lcsEndTix   - $this->lcsBegTix);

    $this->totalTickets = $this->adultTix + $this->childTix + 
      $this->groupTix + $this->lcsTix;
    $this->totalPayingTickets = $this->adultTix + $this->childTix + 
      $this->groupTix;

    // receipt totals
    $this->adultRcptTotal = $this->aRT + $this->aOW + $this->aTRT +
      $this->aTOW;
    $this->childRcptTotal = $this->cRT + $this->cOW + $this->cTRT +
      $this->cTOW;
    $this->openRcptTotal = $this->oRT + $this->oOW;

    $this->totalReceipts = $this->adultRcptTotal + $this->childRcptTotal + 
      $this->openRcptTotal;

    foreach (get_object_vars($this) as $k=>$v) {
      if (empty($v)) {
	$this->$k = '0';
      }
    }
  }   

  function niceName($fieldname) 
  {
    $names = array ('adultTix' => 'Adult Tickets #', 
		    'childTix' => 'Child Tickets #', 
		    'groupTix' => 'Group Tickets #', 
		    'lcsTix'   => 'L/C/S Tickets #', 
		    'adultRcptTotal' => 'Adult Total $', 
		    'childRcptTotal' => 'Child Total $', 
		    'openRcptTotal'  => 'Special Total $');
    return key_exists($fieldname, $names) 
      ? $names[$fieldname] 
      : DataObjects_TTrain::niceName($fieldname);
  }

  /**
   * MUST be called statically.  Returns an assoc array of totals over
   * all args DTC's and a BA.
   *
   * @access public
   * @param  array of DTC objects.
   * @return array
   */
  function computeTotals(&$dtcs)
  {
    $out = array();
    $out['adultTix'] = 0;
    $out['childTix'] = 0;
    $out['groupTix'] = 0;
    $out['lcsTix'] = 0;
    $out['aRT'] = 0;
    $out['aOW'] = 0;
    $out['aTRT'] = 0;
    $out['aTOW'] = 0;
    $out['cRT'] = 0;
    $out['cOW'] = 0;
    $out['cTRT'] = 0;
    $out['cTOW'] = 0;
    $out['oRT'] = 0;
    $out['oOW'] = 0;
    $out['adultRcptTotal'] = 0;
    $out['childRcptTotal'] = 0;
    $out['openRcptTotal']  = 0;

    foreach ($dtcs as $d) {
      $out['adultTix']  += $d->adultTix;      
      $out['childTix']  += $d->childTix;
      $out['groupTix']  += $d->groupTix;
      $out['lcsTix']    += $d->lcsTix;      
      $out['aRT'] 	+= $d->aRT;
      $out['aOW'] 	+= $d->aOW;
      $out['aTRT']	+= $d->aTRT;
      $out['aTOW']	+= $d->aTOW;
      $out['cRT'] 	+= $d->cRT;
      $out['cOW'] 	+= $d->cOW;
      $out['cTRT'] 	+= $d->cTRT;
      $out['cTOW'] 	+= $d->cTOW;
      $out['oRT'] 	+= $d->oRT;
      $out['oOW'] 	+= $d->oOW;
      $out['adultRcptTotal'] += $d->adultRcptTotal;
      $out['childRcptTotal'] += $d->childRcptTotal;
      $out['openRcptTotal']  += $d->openRcptTotal;
    }

    $out['ticketTotal'] = $out['adultTix'] + $out['childTix'] +
      $out['groupTix'] + $out['lcsTix'];
    $out['paidTicketTotal'] = $out['adultTix'] + $out['childTix'] +
      $out['groupTix'];

    $out['ticketReceipts'] = $out['aRT'] + $out['aOW'] + $out['aTRT'] +
      $out['aTOW'] + $out['cRT'] + $out['cOW'] + $out['cTRT'] +
      $out['cTOW'] + $out['oRT'] + $out['oOW'];
    
    foreach ($out as $k=>$v) {
      if (empty($v)) {
	$out[$k] = '0';
      }
    }

    return $out;    
  }

}

?>