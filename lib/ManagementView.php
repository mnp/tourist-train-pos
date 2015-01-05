<?php

require_once 'View.php';
define('MANAGEMENT_TEMPLATE', 'list.html');

/**
* ManagementView - container for template operations
*
* @access public
* @package ManagementView
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class ManagementView extends View
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
   * If set, draws a creation row with this title
   *
   * @var     string
   * @access  private
   */
  var $creationTitle;

  /**
   * Creation items 
   *
   * @var     array [n]=>data
   * @access  private
   */
  var $creationRow;

  /**
   * @access public
   * @return string HTML management table
   */
  function toHtml()
  {
    $this->cols = count($this->titles);
    $this->help_link = MNP::popupHelpLink($this->table_title);
    return MNP::bufferedOutputTemplate(MANAGEMENT_TEMPLATE, $this);
  }


}
