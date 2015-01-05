<?php

//
// Test case for class DataObjects_TBoothActivity
// Automatically generated from TBoothActivity.php
//

require_once 'TBoothActivity.php';
require_once 'PHPUnit.php';

class DataObjects_TBoothActivityTest extends PHPUnit_TestCase {

  // contains the object handle of the string class
  var $objh;

  // constructor of the test suite
  function DataObjects_TBoothActivityTest ($name) {
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

  function makeOne () 
  {
    $b = new DataObjects_TBoothActivity();
    return $b;
  }
  
}

?>
