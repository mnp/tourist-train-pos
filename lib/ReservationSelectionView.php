<?php

require_once 'SelectionView.php';
require_once 'TReservation.php';
require_once 'TTrain.php';

/**
* ReservationManagementView - Reservation management
*
* @access public
* @package ReservationManagementView
* @author Mitchell Perilstein <mitch@enetis.net>
*/
class ReservationSelectionView extends SelectionView
{
  /**
   * @access public
   * @return string table html
   */
  function makeTable($showcust=true)
  {
    $this->table_name = "Select Reservation";
    $this->table_title = "Reservations";
    $this->titles = $showcust
      ? array('Id', 'Customer', 'Outbound Train', 'Return Train', 'Px', 'Action')
      : array('Id', 'Outbound Train', 'Return Train', 'Px', 'Action');
    $nonefound = $this->data_object->N < 1;
    $found = 0;
    
    //	FIXME: this should work?!
    //        $reservations = $this->data_object->fetchDataObjects();
    //foreach($reservations as $i) {

    while ($this->data_object->fetch() && (MANYSTOP==0 || $found < MANYSTOP)) {
      $found++;
      $i = $this->data_object;	// &
      $i->getLinks();      
      assert($i->_tTrain_id_1);
      
      $custStr = !empty($i->_tCustomer_id)
	? $i->_tCustomer_id->toString()
	: '(walkup)';
      
      $t1str = $i->_tTrain_id_1->toString();
      $t2str = !empty($i->_tTrain_id_2)
	  ? $i->_tTrain_id_2->toString()
	  : '(one way)';

      $this->values[$i->resId] = array();
      $this->values[$i->resId][] = $i->resId;
      if ($showcust) {
	$this->values[$i->resId][] = $custStr;
      }
      $this->values[$i->resId][] = $t1str;
      $this->values[$i->resId][] = $t2str;
      $this->values[$i->resId][] =
	 $i->adults + $i->children + $i->laps + $i->specials + $i->escorts;
      $this->values[$i->resId][] =
	MNP::action($this->formname, 'view', 'Edit', $i->resId, 'reservation.php')
	 . '&nbsp;'
	 . MNP::action($this->formname, 'delete', 'Delete', $i->resId,
		     'reservation.php');	
    }    
  }
}
?>

