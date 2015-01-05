<?php

//
// Test case for class DataObjects_TStoredQuery
// Automatically generated from TStoredQuery.php
//

require_once 'TStoredQuery.php';
require_once 'PHPUnit.php';

class DataObjects_TStoredQueryTest extends PHPUnit_TestCase {

  // contains the object handle of the string class
  var $objh;

  // constructor of the test suite
  function DataObjects_TStoredQueryTest ($name) {
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
    $result = DataObjects_TStoredQuery::staticGet(1);
    $this->assertTrue($result !== FALSE);
    $this->assertTrue(!empty($result));
  }
}

?>
