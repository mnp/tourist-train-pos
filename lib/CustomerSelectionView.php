<?php

require_once 'SelectionView.php';
require_once 'TCustomer.php';

/**
* CustomerManagementView - Customer management
*
* @access public
* @package CustomerManagementView
* @author Mitchell Perilstein <mitch@enetis.net>
*/
class CustomerSelectionView extends SelectionView
{
  /**
   * @access public
   * @return string table html
   */
  function makeTable()
  {
    $customers = $this->data_object->fetchDataObjects();
    $this->table_name = "SelectCustomer";
    $this->table_title = "Select Customer";
    $this->titles = array('Id', 'Customer', 'City', 'State', 'Country', 
			  'Phone', 'Action'); 
    
    foreach($customers as $i) {      
      $this->values[$i->custId] = array(
 		$i->custId,
		$i->toString(),
		$i->city ? $i->city : '-',
		$i->state ? $i->state : '-',
		$i->country ? $i->country : '-',
		$i->phone ? $i->phone : '-',
		MNP::action($this->formname, 'view', 'Edit', $i->custId)
		. '&nbsp;'
		. MNP::confirmedAction($this->formname, 'delete', 'Delete', 
				       $i->custId)
		. '&nbsp;'
		. MNP::action($this->formname, 'makeBlankRes', 
			      'Make Reservation', 
			      $i->custId, 
			      'reservation.php')
		);
    }
  }
}
?>

