<?
/*
 * An item sold.
 */

class LineItem 
{
  var $desc;
  var $quan;
  var $each;
  var $amt;

  function LineItem($desc, $quan, $each, $amt=null)
  {
    $this->desc = $desc;
    $this->quan = $quan;
    $this->each = $each;
    $this->amt  = is_null($amt) ? $quan * $each : $amt;
  }
}
?>