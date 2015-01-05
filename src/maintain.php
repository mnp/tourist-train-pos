<?php

require_once '../lib/base.php';
require_once ADMIN_BASE . '/lib/GroupManagementView.php';
require_once ADMIN_BASE . '/lib/UserManagementView.php';
require_once 'HTML/Template/Flexy.php';
require_once 'TMessage.php';

$page_activity = 'Maint';
$page_title = 'System Administration';
require ADMIN_TEMPLATES . '/common-header.inc';

$group = new DataObjects_TAuthGroup;
$user  = new DataObjects_TUser;

if (@$_POST['actionName']) {
  $id = $_POST['actionId'];
  
  switch ($_POST['actionName']) {
  case 'changemotd':
    $mo = new DataObjects_TMessage();
    $mo->id = 'motd';
    $mo->message = stripslashes(@$_POST['newmotd']);    
    MNP::okay($mo->update(), 'MOTD Changed');
    break;

  case 'updateGroup':
    $group->mapOp('update', $id, 
		  array('comment' => $_POST['groupComment_' . $id]));
    break;
    
  case 'createUser':
    $user->mapOp('insert', null,
		 array('user_pass'     => md5($_POST['passwordCreate']),
		       'user_uid'     => $_POST['userNameCreate'],
		       'tAuthGroup_id' => $_POST['groupSelectCreate'],
		       'comment'       => $_POST['userCommentCreate']));
    break;
    
  case 'updateUser':
    $args = array();
    if ($_POST['password_' . $id]) {
      $args['user_pass']  = md5($_POST['password_' . $id]);
    }
    if ($_POST['userComment_' . $id]) {      
      $args['comment']  = $_POST['userComment_' . $id];
    }
    $args['tAuthGroup_id'] = $_POST['groupSelect_' . $id];
    $user->mapOp('update', $id, $args);
    break;    

  case 'deleteUser':
    $user->mapOp('delete', $id);
    break;
    
  default:
    MNP::error('switch oops ' . $_POST['actionName']);
  }
}


$mo = DataObjects_TMessage::staticGet('motd');
$oldmotd = $mo->message;

$formname = "maintain";
include ADMIN_TEMPLATES . '/form-header.inc';

$user->find();
$userview = new UserManagementView($formname, $user);
$userview->makeTable();
$usertable = $userview->toHtml();

/* 
$group->find();
$groupview = new GroupManagementView($formname, $group);
$groupview->makeTable();
$grouptable = $groupview->toHtml();
*/

$m = new StdClass;
//$m->table_title = 'Reservation System Maintenance';
$m->items = array(
		  'Message of the Day' => 
		  	MNP::input_comment('newmotd', $oldmotd, 8, 80)
			. "<br>"
		  	. MNP::action($formname, 'changemotd', 
				      'Change Message'),
		  'Maintain Users' => $usertable,
		  /**
			  'Maintain Groups' => $grouptable,
		  **/
		  'Database' =>  
		  	MNP::action($formname, 'x', 'Consistency Check',
				      $id=-1, $page='dbcheck.php') 
		  	. ' '
			. MNP::action($formname, 'x', 'Save Backup', -1,
				      'backup.php'),
		  'Maintainer' => 
 			MNP::action($formname, 'x', 'Run Tests', $id=-1, 
				  $page='test.php') 
		  	. ' '
		  	. MNP::action($formname, 'x', 'Collected Help', 
				    $id=-1, $page='printhelp.php') 
		  	. ' '
		  	. MNP::action($formname, 'x', 'Shell', 
				    $id=-1, $page='../devutil/shell.php') 
		  	. ' '
		  	. MNP::action($formname, 'x', 'phpMyAdmin', 
				    $id=-1, $page='../phpMyAdmin/index.php') 
		  );

$tpl = new HTML_Template_Flexy();
$tpl->compile('twocol.html');
$tpl->outputObject($m);

  //<a href="dump.php" target="blank">Database Dump to Local Machine</a>

include ADMIN_TEMPLATES . '/form-footer.inc';

include ADMIN_TEMPLATES . '/common-footer.inc';

?>