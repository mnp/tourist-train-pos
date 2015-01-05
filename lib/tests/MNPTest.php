<?php

require_once 'MNP.php';
require_once 'PHPUnit.php';

class MNPTest extends PHPUnit_TestCase {

  // constructor of the test suite
  function MNPTest ($name) {
    $this->PHPUnit_TestCase($name);
  }

  function setUp()  {
    $this->dates = MNP::dates_in_range("1/28/2001", "2/2/2001");
    $this->dates2 = MNP::date_range_from_offset("1/28/2001", 5);
  }
  
  function test_fuzzyprep0() 
  {
    $this->assertEquals('FBR', MNP::fuzzyprep('FoobaR,baz'));
  }

  function test_fuzzyprep1() 
  {
    $this->assertEquals('FBR', MNP::fuzzyprep('FooBar'));
  }

  function test_fuzzymatch0() 
  {
    $x = MNP::fuzzymatch('YoMommaSoUgly,SheSqueals', 'YuhMoomieSogly'); // YMMSKL 
    $this->assertEquals(0, $x);
  }

  function test_fuzzymatch1() 
  {
    $x = MNP::fuzzymatch('ClarkKent', 'KentClark'); // KLRKNT, KNTKLRK
    $this->assertEquals(5, $x);
  }

  function test_datesInRange0 ()  {
    $this->assertTrue(is_array($this->dates) && count($this->dates) == 6);
  }

  function test_datesInRange1 ()  {
    $this->assertTrue(!strcmp($this->dates[0],'2001-01-28'));
  }
 
  function test_datesInRange2 ()  {
    $this->assertTrue(!strcmp($this->dates[5],'2001-02-02'));
  }

  function test_date_range_from_offset0() {
    $this->assertTrue(!strcmp($this->dates2[0],'2001-01-28'));
  }

  function test_date_range_from_offset1() {
    $this->assertTrue(!strcmp($this->dates2[5],'2001-02-02'));
  }
}

?>