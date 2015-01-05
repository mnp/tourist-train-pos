<?php

require_once 'SelectionView.php';
require_once 'TTrain.php';

/**
* TrainSelectionView - Used in checkin screen, shows all trains in a date
*
* @access public
* @package ReportSelectionView
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class TrainSelectionView extends SelectionView
{

  //
  // ====== Trains 2003-05-05 =====
  // Train      Avail	Actions
  //
  // 09:45 HC     161     [Reserve]
  // 01:15 KS     101     [Reserve]
  //

  /**
   * makeDisplay - 
   *
   * @access public
   */
  function makeDisplay ($date)
  {
    $trains = $this->data_object; // array   

    $this->table_name = "Checkin Train Selection";
    $this->table_title = "Trains $date";
    $this->titles = array('Train', 'Avail', 'Out', 'Ret', '');
    $this->nonefound = count($trains) < 1;

    foreach ($trains as $t) {
      $seats = $t->getAvailableSeats();
      $name = $t->colored($t->toBriefString(), $seats);
      $this->values[$t->trainId] = array
	( $name,
	  $seats,
	  MNP::input_radio('tTrain_id_1', $t->trainId),
	  MNP::input_radio('tTrain_id_2', $t->trainId),
	  MNP::action($this->formname, 'showTrain', 'Go', $t->trainId)
	  );
    }
  }
}
