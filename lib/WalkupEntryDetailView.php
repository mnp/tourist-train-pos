<?php

require_once 'DetailView.php';
require_once 'HTML/Select/Common/USState.php';
require_once 'MiniCountry.php';

/**
* WalkupDetailView
*
* @access public
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class WalkupEntryDetailView extends DetailView
{
  function reserveWalkup($action)
  {
    return "<a class=\"button\" "
      . "href=\"javascript:reserveWalkup('$action', '', '');\" >"
      . '&nbsp;Reserve&nbsp;</a>';
  }
  

  /**
   * @access public
   */
  function makeTable()
  {
    global $nextDepartureTime;
    $s = new HTML_Select_Common_USState();
    $c = new MiniCountry();
    
    $this->table_name = "Create Walkup Reservation";
    $this->table_title = "Create Walkup Reservation";
    $this->items['Adult'] = MNP::input_number('adults', 0);
    $this->items['Child'] = MNP::input_number('children', 0);
    $this->items['Lap'] = MNP::input_number('laps', 0);
    $this->items['Escorts'] = MNP::input_number('escorts', 0);
    $this->items['Special'] = MNP::input_number('specials', 0);
    $this->items['Box Lunches'] = MNP::input_number('boxLunches', 0);
    $this->items['Name'] = MNP::input_string('walkupName', '', 25);
    $this->items['State'] = $s->toHTML('walkupState');
    $this->items['Country'] = $c->toHTML('walkupCountry');
    $this->items['Rack'] = 
      $this->reserveWalkup('wupRackBook')
      . '&nbsp;'
      . MNP::action($this->formname, 'wupRackBookCI', 'Reserve and Checkin') 
      . '&nbsp;';

    $this->items['Group'] = 
      $this->reserveWalkup('wupGroupBook')
      . '&nbsp;'
      . MNP::action($this->formname, 'wupGroupBookCI', 'Reserve and Checkin') 
      . '&nbsp;';
    
    $this->editable = FALSE;	// disable red fields
  }
}

