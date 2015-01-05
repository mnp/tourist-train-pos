<?php

require_once 'SelectionView.php';
require_once 'TStoredQuery.php';

/**
* ReportSelectionView - container for template operations
*
* @access public
* @package ReportSelectionView
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class ReportSelectionView extends SelectionView
{
  /**
   * makeDisplay - 
   *
   * @access public
   */
  function makeDisplay ()
  {
    $q = $this->data_object;
    $this->table_name = 'Report';
    $this->table_title = $q->title;
        
    // Do a raw query and result shovelling with PEAR DB.
    $options = &PEAR::getStaticProperty('DB_DataObject','options');
    $dsn = $options['database'];

    $db = DB::connect($dsn, true);
    if (DB::isError($db)) {
      MNP::error($db->getMessage(), 1);
    }
        
    $result = $db->query($q->query);
    if (DB::isError($result)) {
      MNP::error($result->getMessage(), 1);
    }
      
    foreach (array_values($result->tableInfo()) as $info) {
      $this->titles[] = $info['name'];
    }

    $count = 0;
    while ($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $this->values[] = $row;
      $count ++;
    }

    if ($count < 1) {
      $this->nonefound = true;
    }
  }
}
