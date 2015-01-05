<?php

require_once 'ManagementView.php';
require_once 'TAuthGroup.php';

/**
* GroupManagementView - Group management
*
* @access public
* @package GroupManagementView
* @author Mitchell Perilstein <mitch@enetis.net>
*/
class GroupManagementView extends ManagementView
{
  /**
   * @access public
   * @return string table html
   */
  function makeTable()
  {
    $groups = $this->data_object->fetchDataObjects();
    $this->table_title = "Groups";
    $this->table_name = "Groups";
    $this->titles = array('Id', 'Group Name', 'Comment', 'Actions');

    foreach($groups as $i) {      
      $this->values[$i->id] = array(
 		$i->id,
		$i->groupname,
		MNP::input_string('groupComment_' . $i->id, $i->comment),
		MNP::action($this->formname, 'updateGroup', 'Update', $i->id)
		);
    }
  }

}
?>

