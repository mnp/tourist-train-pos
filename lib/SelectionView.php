<?php

require_once 'View.php';

/**
* SelectionView - container for template operations
*
* @access public
* @package SelectionView
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class SelectionView extends View
{
  /**
   * Title (not name) of table.
   *
   * @var     string
   * @access  private
   */
  var $table_title;

  /**
   * Table row headings.
   *
   * @var     array
   * @access  private
   */
  var $titles = array();

  /**
   * number of columns
   *
   * @var     int
   * @access  private
   */
  var $cols;
  
  /**
   * The table data
   *
   * @var     array 	[row][colnum]=>data
   * @access  private
   */
  var $values = array();

  /**
   * Template to use for display.
   *
   * @var     string
   * @access  private
   */
  var $template = 'list.html';

  /**
   * @access public
   * @return string HTML management table
   */
  function toHtml()
  {
    $this->cols = count($this->titles);
    $this->help_link = MNP::popupHelpLink($this->table_name);
    return MNP::bufferedOutputTemplate($this->template, $this);
  }


}
