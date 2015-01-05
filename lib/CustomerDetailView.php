<?php

require_once 'DetailView.php';
require_once 'TCustomer.php';

/**
* CustomerDetailView - container for template operations
*
* @access public
* @package CustomerDetailView
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class CustomerDetailView extends DetailView
{
  /**
   * @access public
   */
  function makeTable()
  {
    $this->table_name = "Customer";
    $this->table_title = "Customer";
    parent::setEditItems();

    $cust = &$this->data_object;
    if (isset($cust->custId) && $cust->custId > 0) {
      // There is a customer
      $this->actions[] = MNP::action($this->formname, 'update', 
				     'Save Changes', $cust->custId);
      $this->actions[] = MNP::confirmedAction($this->formname, 
					      'delete', 
					      'Delete',
					      $cust->custId);
      $this->actions[] = MNP::action($this->formname, 'makeBlankRes', 
				     'Make Reservation', $cust->custId,
				     'reservation.php');
    }
    else {
      $this->actions[] = MNP::action($this->formname, 'create', 'Create');
    }
  }

  /**
   * toHtml - augment parent
   *
   * @static
   * @access (public|private)
   * @param  {  type|objectdefinition } { $varname } [ description ]
   * @return {  type|objectdefinition } [ $varname ] [ description ]
   */
  function toHtml($show_find=true)
  {
    if ($show_find) {
      $default = (isset($_POST['findResOnlySeason']) && !empty($_POST['findResOnlySeason']))
	? $_POST['findResOnlySeason']
	: null;
      $this->findLimit = 'Show rsvns only for: '
	. DataObjects_TSeason::formCode('findResOnlySeason', $default);          
    }
    return parent::toHtml($show_find);
  }


  /**
   * makeDisplay - 
   *
   * @access public
   */
  function makeDisplay ()
  {
    $this->table_name = "Customer";
    $this->table_title = "Customer";
    parent::setDisplayItems(MNP::action($this->formname, 
	'view', 'Edit', $this->data_object->custId, 'customer.php'));
  }

}
