<?

require_once 'PEAR.php';
require_once 'MagneticStripeCard.php';
require_once 'PHPUnit.php';

class MagneticStripeCardTest extends PHPUnit_TestCase
{
  var $card;
  var $parseResult;

  var $good = array('data' => '%B1234567890123445^PADILLA VISDOMINE/LUIS    ^9901SSS1222233300000**YYY******?L',
		    'parse' => true,
		    'PrimaryAccountNumber' => '1234567890123445',
		    'Name' => 	 'PADILLA VISDOMINE/LUIS',
		    'FormatCode' =>  'B',
		    'ExpireYear' =>  '99',
		    'ExpireMonth' => '01',  
		    'ServiceCode' => 'SSS', 
		    'PVKI' =>	 '1',  
		    'PVV' =>	 '2222',
		    'CVC' =>	 '333');
  
  var $bad_pan = array('data' => '%B1234567890123745^PADILLA VISDOMINE/LUIS    ^9901SSS1222233300000**YYY******?L',
		    'parse' => false,
		    'errcode' => MSC_ERR_BAD_PAN);

  var $small_trash = array('data' => '%B1234xyy',
			   'parse' => false,
			   'errcode' => MSC_ERR_BAD_FORMAT);

  var $large_trash = array('data' => '%B1234567890123745^PADILLA VISDOMINE/LUIS    ^9901SSS1222233300000**YYY****xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
			   'parse' => false,
			   'errcode' => MSC_ERR_BAD_FORMAT);

  function MagneticStripeCardTest($name)
  {
    $this->PHPUnit_TestCase($name);
  }

  function setUp()
  {
  }

  function tearDown()
  {
  }

  function do_card($card) 
  {
    $o =& new MagneticStripeCard();
    $this->assertNotNull($o);
    $result =& $o->parse($card['data']);
    $this->assertEquals($card['parse'], ($result === true));    

    if (PEAR::isError($result)) {
      $this->assertEquals($card['errcode'], $result->getCode());
    }
    else {
      $this->assertEquals($card['FormatCode'], $o->getFormatCode(), 'fc');
      $this->assertEquals($card['PrimaryAccountNumber'], $o->getPrimaryAccountNumber(), 
			  'pan');
      $this->assertEquals($card['Name'], $o->getName(), 'name');
      $this->assertEquals($card['ExpireYear'], $o->getExpireYear(), 'year');
      $this->assertEquals($card['ExpireMonth'], $o->getExpireMonth(), 'month');
      $this->assertEquals($card['ServiceCode'], $o->getServiceCode(), 'sc');
      $this->assertEquals($card['PVKI'], $o->getPVKI(), 'pvki');
      $this->assertEquals($card['PVV'], $o->getPVV(), 'pvv');
      $this->assertEquals($card['CVC'], $o->getCVC(), 'cvc');
    }

    unset($o);
  }
  
  function test_good() { $this->do_card($this->good); }
  function test_bad_pan() { $this->do_card($this->bad_pan); }
  function test_small_trash() { $this->do_card($this->small_trash); }
  function test_large_trash() { $this->do_card($this->large_trash); }

}

// Local Variables:
// compile-command: "php -d include_path=..:../../pear runMagneticStripeCardTest.php"
// End:
?>