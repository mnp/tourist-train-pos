<?php

//
// Test case for class DataObjects_TStation
// Automatically generated from TStation.php
//

require_once 'TStation.php';
require_once 'PHPUnit.php';

class DataObjects_TStationTest extends PHPUnit_TestCase {

  // contains the object handle of the string class
  var $objh;

  // constructor of the test suite
  function DataObjects_TStationTest ($name) {
    $this->PHPUnit_TestCase($name);
    
  }

  // called before the test functions will be executed    
  // this function is defined in PHPUnit_TestCase and overwritten
  // here
  function setUp() {
    $this->objh = new DataObjects_TStation ();
  }

  // called after the test functions are executed    
  // this function is defined in PHPUnit_TestCase and overwritten
  // here    
  function tearDown() {
    // delete your instance
    unset($this->objh);
  }

  // test the __clone function
  function test___clone () {
    $result = $this->objh->__clone();
    $this->assertTrue($result);
  }

  // test the staticGet function
  function test_staticGet () {
    $result = DataObjects_TStation::staticGet(1);
    $this->assertTrue($result !== FALSE);
    $this->assertTrue(isset($result->name));
    $this->assertTrue(isset($result->code));
  }

  // test the getAll function
  function test_getAll () {
    $result = DataObjects_TStation::getAll();
    $this->assertTrue(is_array($result));
    $this->assertTrue(count($result) > 0);
  }
}

?>
