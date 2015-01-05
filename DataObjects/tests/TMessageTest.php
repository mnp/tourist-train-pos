<?php

//
// Test case for class DataObjects_TMessage
// Automatically generated from TMessage.php
//

require_once 'TMessage.php';
require_once 'PHPUnit.php';

class DataObjects_TMessageTest extends PHPUnit_TestCase {

  // contains the object handle of the string class
  var $objh;

  // constructor of the test suite
  function DataObjects_TMessageTest ($name) {
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
    $result = DataObjects_TMessage::staticGet('motd');
    $this->assertTrue($result !== FALSE);
    $this->assertTrue(!empty($result));
  }


}

?>
