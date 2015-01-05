<?php

require_once 'DetailView.php';

/**
* WalkupDetailView
*
* @access public
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class WalkupDisplayDetailView extends DetailView
{
  /**
   * @access public
   */
  function makeDisplay()
  {
    $this->table_title = "Walkup Customer";
    $this->table_name = "Walkup Customer";
    $this->items['Name'] = $this->data_object->walkupName
      ? $this->data_object->walkupName
      : '- &nbsp;&nbsp;&nbsp;&nbsp;';
    $type = $this->data_object->getType();
    $this->items['Type'] = $this->data_object->typeToString($type);
    if (isset($this->data_object->walkupState)) {
      include_once 'HTML/Select/Common/USState.php';
      $s = new HTML_Select_Common_USState();
      $this->items['State'] = $s->getName($this->data_object->walkupState);
    }
    else {
      $this->items['State'] = '-';
    }
    if (isset($this->data_object->walkupCountry)) {
      include_once 'MiniCountry.php';
      $c = new MiniCountry();
      $this->items['Country'] = $c->getName($this->data_object->walkupCountry);
    }
    else {
      $this->items['Country'] = '-';
    }
  }
}

