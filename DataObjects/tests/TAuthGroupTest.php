<?php

//
// Test case for class DataObjects_TAuthGroup
// Automatically generated from TAuthGroup.php
//

require_once 'TAuthGroup.php';
require_once 'PHPUnit.php';

class DataObjects_TAuthGroupTest extends PHPUnit_TestCase {

  // contains the object handle of the string class
  var $objh;

  // constructor of the test suite
  function DataObjects_TAuthGroupTest ($name) {
    $this->PHPUnit_TestCase($name);
    
  }

  // called before the test functions will be executed    
  // this function is defined in PHPUnit_TestCase and overwritten
  // here
  function setUp() {
    $this->objh = new DataObjects_TAuthGroup ();
  }

  // called after the test functions are executed    
  // this function is defined in PHPUnit_TestCase and overwritten
  // here    
  function tearDown() {
    // delete your instance
    unset($this->objh);
  }

  // test the staticGet function
  function test_staticGet () {
    $result = DataObjects_TAuthGroup::staticGet(1);
    $this->assertTrue($result !== FALSE);
    $this->assertTrue(!empty($result->gname));
  }


}

?>
