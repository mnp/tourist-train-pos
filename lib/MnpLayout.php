<?php

require_once "HTML/Template/Flexy.php";

class MnpLayout
{
  var $items;  

  function MnpLayout($i) 
  {
    $this->items = $i;
  }
  
  function output_horizontal()
  {
    HTML_Template_Flexy::staticQuickTemplate('layout_horiz.html', $this);
  }

  function output_vertical()
  {
    HTML_Template_Flexy::staticQuickTemplate('layout_vert.html', $this);
  }

  function &get_horizontal()
  {
    return HTML_Template_Flexy::staticBufferedQuickTemplate(
	'layout_horiz.html', $this);
  }

  function &get_vertical()
  {
    return HTML_Template_Flexy::staticBufferedQuickTemplate(
	'layout_vert.html', $this);
  }
}

?>