<?php

/**
* Help - Viciously simple, quick, and dirty help system
*
* @access public
* @package View
* @author Mitchell Perilstein <mitch@enetis.net>
*/

class Help
{
  /** @var string topic name, or null for all topics. */
  var $_topic;

  /**
   * Help - constructor, locates and loads text into object.
   * 
   * @access public
   * @param  string $topic if null, all topics are given
   */
  function Help($topic=null)
  {
    $this->_topic = $topic;
  }
  
  /**
   * getPopupCode - Generate code to perform a popup with the help text.
   *
   * @access public
   * @return string
   */
  function getPopupCode()
  {
    return file_get_contents(ROOTPATH . );
  }

}

?>