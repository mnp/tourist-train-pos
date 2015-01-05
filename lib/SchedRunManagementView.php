<?php

require_once 'ManagementView.php';
require_once 'TTime.php';
require_once 'TSchedRun.php';

/**
* SchedRunManagementView 
*
* @access public
* @package SchedRunManagementView
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class SchedRunManagementView extends ManagementView
{
  /**
   * @access public
   * @return void
   */
  function makeTable($current_season)
  {
    $runs = $this->data_object->fetchAllDataObjects(true);
    
    $this->table_name = "ManageScheduledRuns";
    $this->table_title = "Manage Scheduled Runs for $current_season";
    $this->titles = array('Id', 'Time', 'Station', 'Actions');
    
    if (count($runs) < 1) {
      $this->nonefound = 1;
    }

    foreach ($runs as $id => $i) {
      $this->values[$id] = array(
		$id, 
		$i->_tTime_id->formcode("time_" . $id),
		$i->_tStation_id->formcode("station_" . $id),
		MNP::action($this->formname, 'updateSchedRun', 'Update', 
			    $id) 
		. ' ' 
		. MNP::action($this->formname, 'deleteSchedRun', 'Delete',
			      $id) 
		);
    }

    $this->creationTitle = 'Create Scheduled Run';    
    $this->creationRow = 
      array(
	    'Time' => DataObjects_TTime::formcode('timeCreate'),
	    'Station' => DataObjects_TStation::formcode('stationCreate'),
	    'Actions' => MNP::action($this->formname, 'createSchedRun', 
				      'Create')
			  . MNP::spacer(8)
			  );
  }
}
?>

