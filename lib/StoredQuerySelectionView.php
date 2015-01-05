<?php

require_once 'SelectionView.php';
require_once 'TCustomer.php';

/**
* StoredQuerySelectionView
*
* @access public
* @package StoredQuerySelectionView
* @author Mitchell Perilstein <mitch@enetis.net>
*/
class StoredQuerySelectionView extends SelectionView
{
  /**
   * @access public
   * @return string table html
   */
  function makeTable()
  {
    $reports = $this->data_object->fetchDataObjects();
    $this->table_name = "SelectQuery";
    $this->table_title = "Select Report";
    $this->titles = array('Id', 'Title', 'Description', 'SQL Query', 
			  'Template', 'Actions');

    foreach($reports as $i) {      
      $this->values[$i->id] = array(
	    $i->id,
	    $i->title,
	    $i->description,
	    $i->query,
	    $i->template,
	    MNP::action($this->formname, 'view', 'View', $i->id)
	    . '&nbsp;'
	    . MNP::action($this->formname, 'delete', 'Delete', $i->id));
    }

    $this->creationTitle = 'Create new Report';
    $this->creationRow = 
      array(
	    'Title' => MNP::input_string('title'),
	    'Description' => MNP::input_string('description'),
	    'Query' => MNP::input_comment('query'),
	    'Actions' => MNP::action($this->formname, 'create', 'Create')
	    );
  }
}
?>

