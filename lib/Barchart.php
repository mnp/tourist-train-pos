<?
// Simple html bar chart class that abuses tables.

class Barchart 
{
  var $data;
  var $title;
  var $segLabels;

  var $segmentColors = array('red','green','blue','black','orange');

  var $width = 700;    /** @var max width of table, in pixels */
  var $barHeight = 10; /** @var height of a bar */
  var $datumWidth = 2;   /** @var unit width of a datum */

  /*
   * @param $data array of label=>array(segments)
   */
  function Barchart($title, $segLabels, $data)
  {
    $this->title = $title;
    $this->data = $data;
    $this->segLabels = $segLabels;
  }
  
  function toHtml($attrs='')
  {
    $out = "<table width=\"100%\"><tr>"
    . '<td align="left"><font size="+1"><b>'
      	. $this->title 
      	. '</b></font></td>'
    . '<td align="right">' . $this->legendHtml() . '</td>'
    . '</tr></table>';

    $out .= "<table width=\"100%\" border=1 cellspacing=0><tr><td><table $attrs>";

    $out .= (count($this->data) < 1)
      ? '<tr><td>No data applies</tr></td>'
      : $this->htmlCore();

    $out .= '</table></td></tr></table> <br><br>';
    return $out;
  }
  
  function htmlCore()
  {
    $out = '';
    // Find largest segment and define scale factor so that the largest
    // segment is drawn to $width.
    $maxseg = 0;
    foreach ($this->data as $label=>$segments) {      
      $maxseg = max($maxseg, array_sum($segments));
    }

    $wouldWidth = $maxseg * $this->datumWidth + 1; // no dead data
    $hscale = $this->width / $wouldWidth;
    if ($hscale > 1) {
      $hscale = 1;
    }

    foreach ($this->data as $label=>$segments) {
      $n = 0;
      foreach ($segments as $s) {
	$n += $s;
      }
      $out .= '<tr>';      
      $out .= "<td valign=\"middle\">$label</td>";
      $out .= "<td valign=\"middle\">$n</td>";
      $out .= '<td valign="middle">'
	. $this->hbar($segments, $hscale) 
	. '</td>';
      $out .= "</tr>\n";      
    }
    return $out;
  }

  function legendHtml()
  {
    $out = '<table border="1" cellspacing="0"><tr>';
    $out .= '<td valign="middle"><b><i>Legend:</i></b></td>';
    $i=0;
    foreach ($this->segLabels as $s) {
      $out .= '<td><table><tr><td>'
	. $this->hseg($i++, 1) 
	. '</td><td>' . $s . '</td></tr></table></td>';
    }
    $out .= '</tr></table><br>';
    return $out;
  }

  /**
   *  one bar: it is itself a table so its segments can have different widths
   *
   * @access private
   * @param  array $segments
   * @return string
   */
  function hbar ($segments, $hscale)
  {
    $out = '<table vspace="0" cellspacing="0" cellpadding="0"><tr>';
    $i = 0;
    $ncolors = count($this->segmentColors);
    foreach ($segments as $sn) {
      $c = $this->segmentColors[$i % $ncolors ];
      $h = $this->barHeight;
      $w = $sn * $this->datumWidth * $hscale;
      $out .= "<td valign=\"middle\" height=\"{$h}px\" width=\"{$w}px\""
	. "bgcolor=\"{$c}\" />";
      $i++;
    }    
    $out .= '</tr></table>';
    return $out;
  }

  function hseg($segnum)
  {
    $h = $this->barHeight;
    $c = $this->segmentColors[$segnum];
    return '<table vspace="0" cellspacing="0" cellpadding="0">'
      . "<td valign=\"middle\" height=\"{$h}px\" width=\"{$h}px\""
      . "bgcolor=\"{$c}\" />"
      . '</tr></table>';
  }
}
?>
