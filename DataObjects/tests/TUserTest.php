<?php

//
// Test case for class DataObjects_TUser
// Automatically generated from TUser.php
//

require_once 'TUser.php';
require_once 'PHPUnit.php';

class DataObjects_TUserTest extends PHPUnit_TestCase {

  // contains the object handle of the string class
  var $objh;

  // constructor of the test suite
  function DataObjects_TUserTest ($name) {
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

  // test the staticGet function
  function test_staticGet () {
    $result = DataObjects_TUser::staticGet(1);
    $this->assertTrue(FALSE !== $result);
    $this->assertTrue(!empty($result->user_uid));
    $this->assertTrue(!empty($result->user_pass));
  }

  // test the getAll function
  function test_getAll () {
    $result = DataObjects_TUser::getAll();
    $this->assertTrue(count($result) > 1);
  }
}

?>
