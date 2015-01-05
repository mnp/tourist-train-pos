<?php

//
// Test case for class DataObjects_TSchedRun
// Automatically generated from TSchedRun.php
//

require_once 'TSchedRun.php';
require_once 'PHPUnit.php';

class DataObjects_TSchedRunTest extends PHPUnit_TestCase {

  // contains the object handle of the string class
  var $objh;

  // constructor of the test suite
  function DataObjects_TSchedRunTest ($name) {
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
  }

  // test the staticGet function
  function test_staticGet () {
    $result = DataObjects_TSchedRun::staticGet(1);
    $this->assertTrue($result !== FALSE);
    $this->assertTrue(isset($result->tStation_id));
    $this->assertTrue(isset($result->tSeason_id));
    $this->assertTrue(isset($result->tTime_id));
  }

  function test_getAll() 
  {
    $r = DataObjects_TSchedRun::getAll();
    $this->assertTrue(is_array($r), 'array');
    $this->assertTrue(count($r) > 0, 'count');
  }

  function test_getAllNames() 
  {
    $r = DataObjects_TSchedRun::getAllNames();
    $this->assertTrue(is_array($r), 'array');
    $this->assertTrue(count($r) > 0, 'count');
  }

  // test the toString function
  function test_toString () {
    $this->assertTrue(true);
  }

}

?>
