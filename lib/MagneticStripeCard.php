<?

/*
 * Package to decode, check, and represent a magnetic stripe financial
 * transaction card.
 * 	 
 * References
 *
 * Magnetic Stripe Card Standards - Card Reading
 * http://www.magtek.com/documentation/public/99800004-1.pdf
 *
 * Application Note - Character Conversion 
 * http://www.magtek.com/documentation/public/99875065-4.pdf
 *
 */

require_once 'PEAR.php';
require_once 'Validate.php';

/*
 * Error codes
 */
define('MSC_ERR_NO_DATA', 	-1);
define('MSC_ERR_BAD_FORMAT', 	-2);
define('MSC_ERR_BAD_PAN', 	-3);

// Quick summary of card data format from:
// http://blackmarket-press.net/info/plastic/magstripe/Magstripe_standard.htm
// 
// # Track 1:
// 
// %B1234567890123445^PADILLA VISDOMINE/LUIS    ^9901XXX0000000000000**YYY******?*
// ^^^               ^^                         ^^   ^                 ^        ^^
// |||_ Card number  ||_ Card holder            ||   |_ Number1        |        ||_ LRC
// ||_ Format code   |_ Field separator         ||_ Expiration         |        |_ End sentinel
// |_ Start sentinel                            |_ Field separator     |_ Number2


define('SS2', ';');		// start sentinel track 2
define('SS3', ';');		// start sentinel track 2
define('FS2', '=');		// field separator track 2
define('FS3', '=');		// field separator track 3

// Regexp to recognize track 1
define('TRACK1_RE', 
		        # code   description			 matchnum
       '/		# ----   -----------			 --------
	%		# SS1  : track 1 start sentinel			
        (.)		# FC   : format code				1
        (\d*?)		# PAN  : primary account number			2
        \^		# FS1  : track 1 field separator 
        (.*?)		#      : card holder name 			3
        \^		# FS1  : track 1 field separator 
        		#      : Additional and Discretionary		
        (\d\d)		#      :   expire year				4
        (\d\d)		#      :   expire month				5
	(...)		#      :   service code				6
	(.)		# PVKI :   PIN Verification Code Indicator	7
	(....)		# PVV  :   PIN Verification Value (or Offset)	8
	(...)		# CVV  :   Card Verification Value (or CVC)	9
	(.*?)		#      :   ... rest of A&D		       10
	\?		# ES   : track 1 end sentinel
       /x');

/**
 *  Class MagneticStripeCard
 */
class MagneticStripeCard extends PEAR
{
  /**
   *  Primary Account Number
   *
   * @access  public
   * @var     string	19 digits max
   */
  var $pan;

  /**
   *  Name
   *
   * @access  public
   * @var     string	25 digits max
   */
  var $name;
  var $expireMonth;
  var $expireYear;
  var $serviceCode;
  var $pvki;			// PIN Verification Key Indicator
  var $pvv;			// PIN Verification Value
  var $cvc;			// Card Verification Value

  /**
   * MagneticStripeCard
   *
   * @static
   * @access public
   * @return object 	 Self or PEAR error object
   */
  function MagneticStripeCard()
  {
    $this->PEAR();
  }
  
  /**
   * parse - parse one card data stripe
   *
   * @static
   * @param string $str  The string resulting from swiping a card
   * @access public
   * @return return TRUE or PEAR error object
   */
  function parse($swipe) 
  {
    if (empty($swipe)) {
      return new PEAR_Error('No card data given', MSC_ERR_NO_DATA);
    }

    if (preg_match(TRACK1_RE, $swipe, $matches)) {
      $track1 = $matches[0];
      $this->formatCode  = $matches[1];
      $this->pan 	 = $matches[2];
      $this->name 	 = trim($matches[3]);
      $this->expireYear  = $matches[4];
      $this->expireMonth = $matches[5];
      $this->serviceCode = $matches[6];
      $this->pvki	 = $matches[7];
      $this->pvv	 = $matches[8];
      $this->cvc	 = $matches[9];
    }
    else {     
      return new PEAR_Error('Unrecognized card track format', MSC_ERR_BAD_FORMAT);
    }

    if (true !== Validate::creditCard($this->pan)) {
      return new PEAR_Error('Invalid credit card number', MSC_ERR_BAD_PAN);
    }

    return true;
  }

  function getFormatCode()           { return $this->formatCode;  }
  function getPrimaryAccountNumber() { return $this->pan;         }
  function getName()                 { return $this->name;        }
  function getExpireYear()           { return $this->expireYear;  }
  function getExpireMonth()          { return $this->expireMonth; }
  function getServiceCode()          { return $this->serviceCode; }
  function getPVKI()                 { return $this->pvki;        }
  function getCVC()                  { return $this->cvc;         }
  function getPVV()                  { return $this->pvv;         }

  /* Destructor */
  function _MagneticStripeCard()
  {
    $this->_PEAR();
  }
}

// Local Variables:
// compile-command: "php -d include_path=.:..:../pear tests/runMagneticStripeCardTest.php"
// End:
?>