<?php
/*
 * Personal, general utilies.
 */

require_once 'HTML/Template/Flexy.php';
require_once 'MNPDataObject.php';
require_once 'TSeason.php';
require_once 'TStation.php';

define('MIN_SECS', 60);
define('HR_SECS',  MIN_SECS * 60);
define('DAY_SECS', HR_SECS  * 24);
define('DEBUG_USER', 'mnp');

define('NUMBER_WIDTH', 8);
define('STRING_WIDTH', 40);
define('COMMENT_WIDTH',45);

define('MESSAGE_LEVEL', 0);
define('ERROR_LEVEL', 1);
define('WARNING_LEVEL', 2);

/* Callback for fixupQuery().  It must be global. */
function _fixupQuery_cb($matches)
{
  return strstr($matches[2], '%')
    ? ('(' . $matches[1] . ' LIKE ' . $matches[2] . ')')
    : $matches[0];
}

class MNP
{
  /* If POST slot isn't set, returns current year Jan-Oct, next year Nov-Dec */
  function getCurrentSeason()
  {
    return isset($_POST['seasonSelect'])
      ? $_POST['seasonSelect']
      : (($m = date('m') > 10) ? date('Y') + 1 : date('Y'));
  }
  
  function dateToSeason($datestr) 
  {
    return date('Y', strtotime($datestr));
  }
  

  /**
   * fixupQuery - Patch "a=b" to "a LIKE b" if b contains any '%'
   *
   * @static
   * @access public
   * @param string $s what to mangle
   */
  function fixupQuery($s)
  {
    return preg_replace_callback('/(\w+)\s*=\s*(\S+)/', '_fixupQuery_cb', $s);
  }
  
  function ralign($n, $str)
  {
    return "<div style=\"text-align:right; width:$n\">$str</div>";
  }

  function dollars($n)
  {
    //return money_format('%(n', $n);      // only in certain phps
    return ($n < 0) ? sprintf("$(%0.2f)", abs($n)) : sprintf("$%0.2f", $n);
  }

  function money($n)
  {
    return ($n < 0) ? sprintf("(%0.2f)", abs($n)) : sprintf("%0.2f", $n);
  }

  function resetTabIndex ()
  {
    global $tabindex;
    $tabindex = 1;
  }

  /**
   * spacer - one line
   *
   * @access public
   * @param  int n
   * @return string
   */
  function spacer ($n)
  {
    $str = '';
    for ($i=0; $i<$n; $i++) {
      $str .= '&nbsp;';
    }
    return $str;
  }

  /**
   * @access public
   * @param  string $class
   * @param  string $str
   * @return string
   */
  function background($class, $str)
  {
    return "<span class=\"$class\">$str</span>";
  }

  /**
   * @access public
   * @param  string $class
   * @param  string $str
   * @return string
   */
  function box($class, $str)
  {
    return "<table class=\"$class\"><tr><td>" 
      . wordwrap($str, 100, '<br>')
      . "</td></tr></table>"; 
  }

  //
  // Returns array of YYYY-MM-DD strings, one element for each day in given 
  // range [astr,bstr] of MM-DD-YYYY date strings.  Does NOT check a and b.
  //
  function dates_in_range ($astr, $bstr) 
  {
    $aTime  = strtotime($astr);
    $bTime  = strtotime($bstr);
    $result = array();
    
    for ($date = $aTime; $date <= $bTime; $date += DAY_SECS) {
      $result[] = date('Y-m-d', $date);
    }
    return $result;
  }

  //
  // Returns array of MM/DD strings, one element for each day in given 
  // range [astr, astr+offset]; astr is MM-DD-YYYY.  Does NOT check args
  //
  function date_range_from_offset($astr, $offsetDays) 
  {
    $aTime = strtotime($astr);
    $bTime = $aTime + $offsetDays * DAY_SECS;
    for ($date = $aTime; $date <= $bTime; $date += DAY_SECS) {
      $result[] = date('Y-m-d', $date);
    }
    return $result;
  }

  // debug print
  function dp($x, $title='-') 
  {
    global $mnpdbg;
    //    if ($mnpdbg) {
      echo '<table border bordercolor="yellow" bgcolor="gray"><tr><td>';
      echo "<font color=\"white\">\n<pre>\n$title: ";
            print_r($x);
      echo '</pre></font></td></tr></table>';
      flush();
      //    }
  }

  // debug dump
  function dd($x, $title='-') 
  {
    global $mnpdbg;
    //    if ($mnpdbg) {
      echo '<table border bordercolor="yellow" bgcolor="gray"><tr><td>';
      echo "<font color=\"white\">\n<pre>\n$title: ";
      var_dump($x);
      echo '</pre></font></td></tr></table>';
      flush();
      //    }
  }

  // debug string
  function dpstr($x, $title='-') 
  {
    ob_start();
    echo "<pre>\n$title: ";
    print_r($x);
    echo '</pre>';
    $str = ob_get_contents();
    ob_end_clean();
  }

  function action($formname, $action, $label, $id=null, $page=null)
  {
    return "<a class=\"button\" href=\"javascript:submitAction('$action', '$id', '$page');\" >"
      . '&nbsp;'. $label . '&nbsp;' . '</a>';
  }

  // Same as action, with NO STYLE
  function link($formname, $action, $label, $id=null, $page=null)
  {
    return "<a href=\"javascript:submitAction('$action', '$id', '$page');\" >"
      . '&nbsp;'. $label . '&nbsp;' . '</a>';
  }

  function popup_action($label, $page=null, $argname=null, $argval=null)
  {
    return "<a class=\"button\" href=\"javascript:void(0);\" onclick=\"javascript:var w=window.open('{$page}?{$argname}=' + $argval, null, 'resizeable=yes,status=no,toolbar=no,scrollbars=yes,height=600,width=800'); w.focus();\">"
      . '&nbsp;'. $label . '&nbsp;' . '</a>';
  }

  // This one takes an k=>v arglist.
  function popup_action2($label, $page=null, $arglist)
  {
    $q = "";
    foreach ($arglist as $k=>$v) {
      $q .= " + '$k=' + " . $v . " + '&'";
    }
    return "<a class=\"button\" href=\"javascript:void(0);\" onclick=\"javascript:var w = window.open('{$page}?'{$q}, null, 'resizeable=yes,status=no,toolbar=no,scrollbars=yes,height=600,width=800'); w.focus();\">"
      . '&nbsp;'. $label . '&nbsp;' . '</a>';
  }

  function close_button($label) 
  {
    return '<a href="#" onClick="return window.close();" class="button">'
      . '&nbsp;' . $label . '&nbsp;</a>';
  }

  function print_button($label) 
  {
    return '<a href="#" onClick="return window.print();" class="button">'
      . '&nbsp;' . $label . '&nbsp;</a>';
  }


  function confirmedAction($formname, $action, $label, $id=null, $page=null)
  {
    /* see
     * http://www.devguru.com/technologies/ecmascript/quickref/win_confirm.html
     */
    return "<a class=\"button\" href=\"javascript:if(confirmSomething('" . 
      $label
      . "')){submitAction('$action', '$id', '$page');}\">"
      . '&nbsp;' . $label . '&nbsp;' . '</a>';
  }

  function reset_form($formname, $label) 
  {
    return "<a class=\"button\" href=\"javascript:document.{$formname}.reset();\">&nbsp; $label &nbsp;</a>";
  }

  function clear_table($tablename, $label) 
  {
    return "<a class=\"button\" href=\"javascript:clear{$tablename}();\">&nbsp; $label &nbsp;</a>";
  }
  
  function format_pear_error($e) 
  {
    if (is_array($e)) {
      //      return '<ul><li>'
      //	. join('<li>', array_values($e))
      //	. '</ul>';
      $str = '';
      foreach($e as $k=>$v) {
	$str .= '<b>-</b>&nbsp;' . $v . '<br>';
      }
      return $str;
    }
    else if (is_string($e)) {
      return $e;
    }
    else if (isset($e) && Pear::isError($e)) {
      ob_start();
      echo '<pre>';
      print_r($e);
      echo '</pre>';
      $x = ob_get_contents();
      ob_end_clean();
      return $x;
    }
    return '';
  }

  // function to grep keys of an array for a pattern
  // returns an array
  function grep_keys($pattern, $array)
  {
    $newarray = Array();
    while( list($key, $val) = each($array))
      {
	if(preg_match($pattern,$key))
	  $newarray[$key] = $val;
      }
    return $newarray;
  }

  /**
   * @access public
   * @param  string filename 
   * @param  string string to write
   * @return mixed true for success or string containing error
   */
  function write_file($filename, $string)
  {
    if (file_exists($filename) && !is_writable($filename)) { 
      return "$filename not writeable";
    }
    if (!($fp = fopen($filename, 'w'))) {
      return "Can't fopen $filename";
    }
    if (!fwrite($fp, $string) || !fclose($fp)) {
      return "Problem writing $filename";
    }
    return true;
  }

  function message($message='MESSAGE UNDEFINED', $level=0, $append='')
  {
    $message = wordwrap($message, 100, '<br>');
    include ADMIN_TEMPLATES . '/message.inc';
  }

  function error($message, $fatal=0, $append='')
  {
    MNP::message($message, ERROR_LEVEL, $append);
    if ($fatal) {
      exit;
    }
  }
  
  function warning($message, $fatal=0, $append='')
  {
    MNP::message($message, WARNING_LEVEL, $append);
    if ($fatal) {
      exit;
    }
  }

  /**
   * pushError - 
   *
   * @access public
   * @param array $errors
   * @param int $id
   * @return true if okay, else false
   */
  function pushError(&$errors, $code)
  {
    if (is_array($code)) {
      $errors += $code;
      return false;
    }
    else if (false === $code || (is_int($code) && $code < 1)) {
      $errors[] = 'unknown error';
      return false;
    }
    return true;
  }

  /**
   * Main result checker
   *
   * @access public
   * @param  mixed  $code true for okay; else string, or array of strings 
   * @param  string $message shown either way
   * @param  bool   $fatal  die if true and we fail
   */
  function okay($code, $message, $fatal=0)
  {       
    if ((true === $code || (is_int($code) && $code > 0))
	&& !Pear::isError($code)) {
      MNP::message($message . ': okay', MESSAGE_LEVEL);
    }
    else {
      MNP::error($message, $fatal, MNP::format_pear_error($code));
    }
  }
   
  /**
   * @access public
   * @return string
   */
  function wrapJavascript ($str)
  {
    return <<<EOT
<script language="JavaScript" type="text/javascript">
<!--\n
{$str}
//-->
</script>
EOT;
  }
  
  function title_bar($ltitle, $rtitle=null, $rcode=null, $help=null)
  {
    include ADMIN_TEMPLATES . '/title-bar.inc';
  }
  
  function headerBox($things) 
  {
    $out = '<table><tr><td class="header"> <nobr>';
    foreach ($things as $t) {
      $out .= '&nbsp;' . $t;
    }
    $out .= '</nobr></td></tr></table><p>';
    return $out;
  }

  function selector_string($name, $options, $submits=0, $offer_none=0, 
			   $selected=null, $extras='')
  {
    global $formname;		// FIXME
    global $tabindex;    
    $tabindex++;
    $str = "<select name=\"$name\" $tabindex $extras";

    if (isset($submits) && $submits) {
      $str .= " onchange=\"document.$formname.submit();\"";
    }
    $str .= ">\n";

    if ($offer_none) {
      // array_unshift messes up the array keys; do it by hand.
      $noptions = array();
      $noptions[] = '-- NONE --';
      foreach ($options as $k=>$v) {
	$noptions[$k] = $v;
      }
      $options = $noptions;
    }

    foreach ($options as $k=>$v) {
      $selstr = (isset($selected) && $selected == $k)
	? 'selected="selected"'
	: '';
      $str .= "<option $selstr value=\"{$k}\"> {$v} </option>\n";
    }
    $str .= "</select>\n";
    return $str;
  }

  function input_string($name, $value='', $size=STRING_WIDTH, $extras='')
  {
    global $tabindex;
    $tabindex++;
    return "<input type=\"text\" size=\"$size\" name=\"$name\" $tabindex value=\"$value\" $extras />";
  }

  function input_password($name, $value='', $size=STRING_WIDTH)
  {
    global $tabindex;
    $tabindex++;
    return "<input type=\"password\" size=\"$size\" name=\"$name\" value=\"$value\" $tabindex />";
  }

  function input_comment($name, $value='', $rows=2, $cols=COMMENT_WIDTH)
  {
    global $tabindex;    
    $tabindex++;
    return "<textarea cols=\"$cols\" rows=\"$rows\" $tabindex name=\"$name\">"
      . $value
      . '</textarea>';
  }

  function readonly($name, $value, $cols=NUMBER_WIDTH, $extras='') 
  {
    return '<input disabled="disabled" type="text" STYLE="background:white; font-weight: bold; text-align:right" size="'
      . $cols
      . "\" name=\"$name\" value=\"$value\" $extras>";
  }
  
  function input_number($name, $value=0, $disabled=false, $cols=NUMBER_WIDTH, $extras='')
  {
    global $tabindex;
    $tabindex++;
    return '<nobr><input type="text" STYLE="text-align:right" size="' 
      . $cols
      . "\" name=\"$name\" value=\"$value\" tabindex=\"$tabindex\" "
      . ($disabled ? 'disabled="true" ' : '')
      . $extras
      . '/>'
      . "<a href=\"javascript:increment('$name');\">"
      . '<img border="0" src="' . GRAPHICS . '/plus.png">'
      . '</a>'
      . "<a href=\"javascript:decrement('$name');\">"
      . '&nbsp;'    
      . '<img border="0" src="' . GRAPHICS . '/minus.png">'
      . '</a></nobr>';
  }

  function input_bool($name, $checked=0)
  {
    $c = $checked ? 'checked' : '';
    return "<input type=\"checkbox\" name=\"$name\" $c >";
  }

  function input_radio($name, $value='')
  {
    return "<input type=\"radio\" value=\"$value\" name=\"$name\">";
  }
  
  function date_button_string($formname, $name, $init=null, $submits=false)
  {
    if ($init == '*today*') {
      $init = date('Y-m-d');
    }
    
    $str = "<input size=\"10\" type=\"text\" name=\"$name\" value=\"{$init}\" />"
      . '<a href="javascript: void(0);"';

   $str .= $submits
      ? "onclick=\"getCalendar(document.$formname.$name);"
     . "document.$formname.submit();\""
     : "onclick=\"return getCalendar(document.$formname.$name);\"";

    $str .= '> <img align="middle" src="../popupcalendar/calendar.png" border="0" /> '
      . ' </a>';
    return $str;
  }

  function bit_string($bit) 
  {
    return $bit ? 'Yes' : 'No';
  }

  function _prepareTemplate($template, &$thing)
  {
    if (is_null($thing)) { MNP::dp($template, 'nullthing'); 
    }
    

    $tpl = new HTML_Template_Flexy();
    $tpl->quickform = null;
    $tpl->compile($template) || die ("compile $template failed");

    if (is_object($thing)) {
      $obj =& $thing;
    }
    else if (is_array($thing)) {   
      $obj = new StdClass;
      foreach($thing as $key => $value) {
	$obj->$key = $value;
      }
    }
   
    return array($tpl, $obj);
  }

  /**
   * @access public
   * @param  string template
   * @param  mixed thing: can be object or array
   * @return string
   */
  function bufferedOutputTemplate($template, &$thing)
  {
    list($tpl, $obj) = MNP::_prepareTemplate($template, $thing);
    return $tpl->bufferedOutputObject($obj);
  }

  /**
   * @access public
   * @param  string template
   * @param  mixed thing: can be object or array
   * @return string
   */
  function outputTemplate($template, &$thing)
  {
    list($tpl, $obj) = MNP::_prepareTemplate($template, $thing);
    $tpl->outputObject($thing);
  }
  
  /**
   * @access public
   * @param  string $name
   * @param  string $value
   * @return string
   */
  function hiddenHtml($name, $value='')
  {
    return "<input type=\"hidden\" name=\"$name\" value=\"$value\" />";
  }

  /**
   * popupHelpLink - return HTML link to trigger a popup
   *
   * @access public
   * @return string
   */
  function popupHelpLink($topic)
  {
    return '<a href="' . ROOTURL . '/lib/HelpLoader.php" ' 
      . "onClick=\"return popup(this, '{$topic}')\">"
      . '<img border="0" src="' . ROOTURL . '/graphics/help.png">'
      . '</a>';
  }

  function fuzzyprep($s) 
  {
    $cpos = strpos($s, ',');
    return metaphone(($cpos === false) ? $s : substr($s, 0, $cpos));
  }

  /**
   * @param  string $a
   * @param  string $b
   * @return int score of match; smaller better, zero best
   */
  function fuzzymatch ($a, $b)
  {
    return levenshtein(MNP::fuzzyprep($a), MNP::fuzzyprep($b));
  }

  /**
   * rawQuery - may throw errors
   *
   * @access public
   * @param  string $query
   * @param  bool $getOne: return first col of first row of query
   * @return object mysql result; or string if error, or array if using getOne
   */
  function rawQuery($query, $getOne=false)
  {
    // Do a raw query and result shovelling with PEAR DB.
    $options = &PEAR::getStaticProperty('DB_DataObject','options');
    $dsn = $options['database'];
    
    $db = DB::connect($dsn, true);
    if (DB::isError($db)) {
      MNP::error($db->getMessage());
      return;
    }
    
    if ($getOne) {
      $result = $db->getOne($query);
    }
    else {
      $result = $db->query($query);
    }
    
    if (DB::isError($result)) {
      MNP::error($result->getMessage());
    }

    return $result;
  }

}
?>
