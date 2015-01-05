<?php

require_once 'MNP.php';
require_once 'TCustomer.php';
require_once 'TSeason.php';

/**
* MiniRate - display or input for four rates; all of one customer type
*
* @access public
* @package View
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class MiniRate
{
  var $tmp;
  var $types;

  /**
   * In a1, a2, c1, c2 order
   *
   * @access public
   * @param  object $obj a TReservation or TCustomer
   */
  function MiniRate($obj=null)
  {
    $this->types = array('a1Rate', 'a2Rate', 'c1Rate', 'c2Rate');
    $this->tmp =   new StdClass;

    foreach ($this->types as $t) {
      $this->obj->$t = isset($obj->$t) ? $obj->$t : '';
    }

    
  }

  function toHTML($editable=false, $disabled=false)
  {
    if ($editable) {
      foreach ($this->types as $t) {
	$this->obj->$t = MNP::input_number($t, $this->obj->$t, $disabled);
      }
    }
    return MNP::bufferedOutputTemplate('miniRateEdit.html', $this->obj);
  }
}

?>