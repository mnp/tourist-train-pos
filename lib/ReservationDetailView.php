<?php

require_once 'DetailView.php';
require_once 'TReservation.php';

/**
* ReservationManagementView - Reservation management
*
* @access public
* @package ReservationManagementView
* @author Mitchell Perilstein <mitch@enetis.net>
*/
class ReservationDetailView extends DetailView
{
  /**
   * @access public
   * @return string table html
   */
  function makeTable()
  {
    $this->table_name = "Reservation";
    $this->table_title = "Reservation";
    $res = &$this->data_object;
    
    // set $this->items
    parent::setEditItems(false);   
    
    // One special addition; total field is computed and displayed but not
    // stored.
    $this->items['total'] = MNP::dollars($this->data_object->computeDue());

    if (@$res->resId && $res->resId >0) {
      //
      // There is a reservation
      //
      $res->getLinks();      
      //      assert($res->_tCustomer_id);
      if (!empty($res->_tTrain_id_1)) {
	$res->_tTrain_id_1->getLinks();
      }
      if (!empty($res->_tTrain_id_2)) {
	$res->_tTrain_id_2->getLinks();	      
      }

      $this->actions[] = MNP::action($this->formname, 'update', 
				       'Save Changes', $res->resId);

      if ($res->status == RES_CHECKEDIN) {
	/*	
        $this->actions[] = MNP::box('warning', 'Please Note: Reservation is checked in.  In order to change it, please <b>Release</b> it, <b>save</b> your changes, then <b>Checkin</b> again.') . '<br>';	
	 */
	$this->actions[] = MNP::action($this->formname, 'release', 'Release',
				       $res->resId);
      }
      else {
	$this->actions[] = MNP::action($this->formname, 'checkin', 'Checkin',
				       $res->resId);
      }
      $this->actions[] = MNP::action($this->formname, 'delete', 'Delete',
				     $res->resId);
    }
     
    // now use $this->items to make a custom layout block
    $this->valueString = MNP::bufferedOutputTemplate('reservation.html', $this->items);
    
    // This happens when we want to show a blank res and a known cust,
    // as in makeBlankRes reservation.php.
    //else if (@$res->tCustomer_id) {
    if (@$res->tCustomer_id) {
      $this->actions[] = MNP::action($this->formname, 'create', 'Create');
    }
  }
}
?>

