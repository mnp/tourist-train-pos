<?php

require_once 'ManagementView.php';
require_once 'TUser.php';

/**
* Users - user management
*
* @access public
* @package UserView
* @author Mitchell Perilstein <mitch@enetis.net>
*/
class UserManagementView extends ManagementView
{
  /**
   * @access public
   * @return void
   */
  function makeTable()
  {
    $users =& $this->data_object->fetchAllDataObjects(true);
    
    $this->table_title = "Users";
    $this->table_name = "Users";
    $this->titles = array('Id', 'Name', 'Password', 'Group', 'Comment', 
			 'Actions');
    foreach($users as $id => $i) {
      $this->values[$id] = array(
 		$id,
		$i->user_uid,
		MNP::input_password('password_' . $id, '', 10),
		$i->_tAuthGroup_id->formCode('groupSelect_' . $id),
		MNP::input_string('userComment_' . $id, $i->comment), 
		MNP::action($this->formname, 'updateUser', 'Update', $id) 
		    . ' ' 
		    . MNP::action($this->formname, 'deleteUser', 'Delete', $id) 
		);
    }

    // creation section
    $this->creationTitle = 'Create User';
    $this->creationRow = 
      array(
	    'Name' => MNP::input_string('userNameCreate', '', 10),
	    'Password' => MNP::input_password('passwordCreate', '', 10),
	    'Group' => DataObjects_TAuthGroup::formCode('groupSelectCreate'),
	    'Comment' => MNP::input_string('userCommentCreate'),
	    'Actions' => MNP::action($this->formname, 'createUser', 'Create')
	    );
  }    

}
?>

