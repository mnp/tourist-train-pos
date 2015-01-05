<?php

/** Money - Viciously simple money class. Uses PEAR number validation.
* Internally represents monetary values as integer cents; but reads and
* writes them as fixed point.  See DDJ, 5/4, for discussion.
*
* NB: Maximum amount is $21,474,836.47
*
* @access public
* @author Mitchell Perilstein <mitch@enetis.net>
*/

// thanks to http://www.vanjohnson.com/geek/article.php?sid=1
define('MONEY_REGEXP', '^([0-9]+|[0-9]{1,3}(,[0-9]{3})*)(.[0-9]{1,2})?$');

if (!defined('MAXINT')) {
  define('MAXINT', 0xffffffff);
}

class Money extends PEAR
{
  var $cents;

  /**
   * Money
   * 
   * @access public
   * @param  string $topic if null, all topics are given
   */
  function Money($str)
  {
    $str = trim($str);
    if (preg_match(MONEY_REGEXP, $str)) {
      $this->cents = intval($str * 100); // truncation here if > 2 decimal places
    }
    else {
      PEAR::raiseError("Money: string not proper format");
    }
  }

  function toString($parens=true)
  {
    return $this->cents / 100;	// rounding here
  }

  function dollars($n)
  {
    return ($n < 0) ? sprintf("$(%0.2f)", abs($n)) : sprintf("$%0.2f", $n);
  }
}

?>