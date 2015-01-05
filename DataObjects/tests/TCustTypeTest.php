<?php

//
// Test case for class DataObjects_TCustType
// Automatically generated from TCustType.php
//

require_once 'TCustType.php';
require_once 'PHPUnit.php';

class DataObjects_TCustTypeTest extends PHPUnit_TestCase {

  // constructor of the test suite
  function DataObjects_TCustTypeTest ($name) {
    $this->PHPUnit_TestCase($name);
  }

  function test_getAllNames()
  {
    $x = DataObjects_TCustType::getAllNames();
    $this->assertTrue(!is_null($x));
    $this->assertEquals(count($x), 4);
  }

  function test_getAllCodes()
  {
    $x = DataObjects_TCustType::getAllCodes();
    $this->assertTrue(!is_null($x));
    $this->assertEquals(count($x), 4);
  }
}

?>
