<?php

require_once 'View.php';
require_once 'TTrain.php';

/** TrainAccountingDetailView - Edit tickets and money for one train, for
* daily acctng
*
* @access public
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class TrainAccountingDetailView extends View // not really a detail one
{
  function makeTable($show_groups=false) 
  {   
    $train = &$this->data_object;

    $this->template = 'ticket-block.html';

    $fareclasses = array('adult', 'child', 'lcs');

    // See comment atop lib/DailyTrainColumn.php
    if (intval(substr($train->date, 0, 4)) < 2004) {
      $fareclasses[] = 'group';
    }

    $recompute = 'function recompute() {';

    foreach ($fareclasses as $f) {
      $beg = $f . 'BegTix';
      $end = $f . 'EndTix';
      $cnt = $f . 'CntTix';
      $this->$beg = MNP::input_string($beg, $train->$beg, NUMBER_WIDTH,
	  "onChange=\"diff('$cnt','$beg','$end');\" onLeave=\"diff('$cnt','$beg','$end');\" ");
      $this->$end = MNP::input_string($end, $train->$end, NUMBER_WIDTH,
	  "onChange=\"diff('$cnt','$beg','$end');\" onLeave=\"diff('$cnt','$beg','$end');\" ");
      $this->$cnt = MNP::readonly($cnt, 0);
    }
    $recompute .= ' }';
    
    $this->recompute = MNP::wrapJavascript($recompute);
    $this->table_name = 'TrainAccounting';
    $this->table_title = 'Accounting for Train : ' . $train->toString();
    $this->actions[] = MNP::action($this->formname, 'save', 
				   'Save Changes and Close');
  }

    /**
   * @access public
   * @return string HTML management table
   */
  function toHtml()
  {
    return MNP::bufferedOutputTemplate($this->template, $this);
  }
}

?>