<?
class Receipt 
{
  var $template = 'receipt.html';
  var $lineIems;
  var $tquan = 0;
  var $total  = 0;

  /** 
   * Make a receipt out of line items.  It should look exactly the same on
   * screen as on paper.  A template is really no use as we're making heavy
   * use of printf formatting.  We have a 40 char width limit.
   *
   * @access public
   * @param  array of Item
   */
  function Receipt($lineItems) 
  {
    $this->lineItems = $items;
  }

  function text_render()
  {
         // 0123456789012345678901234567890123456789
    $fmt = "%4s  %-11s  %4s  %6s\n";
    $out = '';
    $out .= sprintf($fmt, 'Quan', 'Description', 'Each', 'Amount');
    $out .= sprintf($fmt, '----', '-----------', '----', '------');

    $subtotal = 0;
    $total_quan = 0;
    foreach($items as $i) {
      $this->total      += $i->amt;
      $this->total_quan += $i->quan;
    }

    $out .= sprintf($fmt, '----', '-----------', '----', '------');
    $out .= sprintf($fmt, $total_quan, '', '', $subtotal);
    $this->rendered_text = $out;
  }  

  function toHtml()
  {    
    // The receipt template just sets a white background and tt font.
    $this->text_render();
    return MNP::bufferedOutputTemplate($this->template, $this);
  }
}
?>