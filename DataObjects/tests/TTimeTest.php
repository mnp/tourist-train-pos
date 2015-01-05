<?php

//
// Test case for class DataObjects_TTime
// Automatically generated from TTime.php
//

require_once 'TTime.php';
require_once 'PHPUnit.php';

class DataObjects_TTimeTest extends PHPUnit_TestCase {

  // contains the object handle of the string class
  var $objh;

  // constructor of the test suite
  function DataObjects_TTimeTest ($name) {
    $this->PHPUnit_TestCase($name);
    
  }

  // called before the test functions will be executed    
  // this function is defined in PHPUnit_TestCase and overwritten
  // here
  function setUp() {
  }

  // called after the test functions are executed    
  // this function is defined in PHPUnit_TestCase and overwritten
  // here    
  function tearDown() {
    // delete your instance
  }

  function test_staticGet () {
    $result = DataObjects_TTime::staticGet(1);
    $this->assertTrue($result !== FALSE);
  }

  // test the getAll function
  function test_getAll () {
    $result = DataObjects_TTime::getAll();
    $this->assertTrue(count($result) > 1);
  }

  // test the toString function
  function test_toString () {
    $t = new DataObjects_TTime();
    $t->runTime = '01:02:03';
    $s = $t->toString();
    $this->assertTrue($s === '01:02 am');
    $t->runTime = '15:02:03';
    $s = $t->toString();
    $this->assertTrue($s === '03:02 pm');
  }
}

?>
