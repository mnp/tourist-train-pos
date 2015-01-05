<?php

require_once 'ManagementView.php';
require_once 'TSeason.php';
require_once 'TCustType.php';

/**
* SeasonManagementView 
*
* @access public
* @package SeasonManagementView
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class SeasonManagementView extends ManagementView
{
  /**
   * @access public
   * @return void
   */
  function makeTable()
  {
    global $current_season;
    
    $seasons = $this->data_object->fetchDataObjects();
    $custCodes = DataObjects_TCustType::getAllCodes();
        
    $this->table_title = "ManageSeasons";
    $this->table_title = "Existing Seasons";
    $this->titles = array('Year', 'Values', 'Actions');
    
    foreach ($seasons as $i) {
      $tmp = new StdClass;
      $rates = $i->getAllRates($i->id);
      foreach ($rates as $code=>$rate) {
	$tmp->$code = MNP::input_number($code . '_' . $i->id, $rate);
      }
      $ratetab = MNP::bufferedOutputTemplate('rateInput.html', $tmp);
      $this->values[$i->id] = array(
		$i->id, 
		$this->_stable($ratetab,
			       MNP::input_number('boxLunchRate_' . $i->id, $i->boxLunchRate),
			       MNP::input_string('comment_' . $i->id, $i->comment)),
		MNP::action($this->formname, 'updateSeason', 'Update', $i->id) 
		. ' ' . MNP::action($this->formname, 'deleteSeason', 'Delete', 
			      $i->id) 
		);
				 
    }

    $tmp = new StdClass;
    $rates = DataObjects_TSeason::getAllRates(null);
    foreach ($rates as $code=>$rate) {
      $tmp->$code = MNP::input_number($code, $rate);
    }
    $ratetab = MNP::bufferedOutputTemplate('rateInput.html', $tmp);

    $this->creationTitle = 'Create Season';
    $this->creationRow = array
      ('Year' => MNP::input_number('id', $current_season + 1), 
       'Values' => $this->_stable($ratetab,
				  MNP::input_number('boxLunchRate'), 
				  MNP::input_string('comment')), 
       'Actions' => MNP::action($this->formname, 'createSeason', 'Create')
       );
  }

  function _stable($rates, $lunch, $comment) 
  {
    return '<table border="0">'
      . "<tr><td>Default Rates</td><td>$rates</td></tr>"
      . "<tr><td>Box Lunch Rate</td><td>$lunch</td></tr>"
      . "<tr><td>Comment</td><td>$comment</td></tr></table>";
  }
}
?>

