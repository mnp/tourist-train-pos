<?php

/**
* storedQuery - Very simple storage and playback of SQL queries.
*
* @access public
* @author Mitchell Perilstein <mitch@enetis.net>
*/

require_once '../lib/base.php';
require_once 'QueryManagementView.php';

MNP::setFromGetOrPost('actionName');
MNP::setFromGetOrPost('actionId');

$query = new DataObjects_TStoredQuery;
$args  = array();

switch (@$actionName) 
{
 case 'create':
   $query->setFrom($_POST);
   $query->insert();
   break;
   
 case 'delete':
   $query->id = $actionId;
   $query->delete();
   break;

 case 'change':
   $args = array('title'       => $_POST['title_' . $actionId],
		 'description' => $_POST['description_' . $actionId],
		 'query'       => $_POST['query_' . $actionId]);
   $query->setFrom($args);
   $query->id = $actionId;
   $query->update();
   break;

 default:
}

$query = new DataObjects_TStoredQuery;
$view = new QueryManagementView(array('formname' => 'query', 
				      'data_object' => $query));
$view->makeTable();
$page_args = array('page_activity'  => 'query',
		   'page_category'  => 'System',
		   'page_title'     => "Stored Queries",
		   'showformhtml'   => true,
		   'formname'       => 'queries');

$p = new Page($view, $page_args);
$p->render();

// ----------------------------------------------------------------------

?>