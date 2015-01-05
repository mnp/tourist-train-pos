<?php

/**
* View - GUI wrapper for displaying some DataObjects in a Form
*
* @access public
* @package View
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class View
{
  /**
   * Form name for links, javascript, etc.
   *
   * @var     string
   * @access  private
   */
  var $formname;

  /**
   * The DataObject_ this will be accessing.
   *
   * @var     string
   * @access  private
   */
  var $data_object;

  /**
   * Form is editable if true: generates fields versus labels, etc.
   *
   * @var     boolean
   * @access  private
   */
  var $editable;

  /**
   * Create view object linked to a Data Object.
   *
   * @access public
   * @param  string formname
   * @param  object the DataObject
   */
  function View($formname, $obj, $editable=true)
  {
    $this->editable = $editable;    
    $this->data_object = $obj;    
    $this->formname = $formname;    
  }
}

?>