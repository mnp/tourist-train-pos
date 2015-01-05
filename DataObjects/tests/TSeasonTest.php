<?php

//
// Test case for class DataObjects_TSeason
// Automatically generated from TSeason.php
//

require_once 'TSeason.php';
require_once 'PHPUnit.php';

class DataObjects_TSeasonTest extends PHPUnit_TestCase {

  // constructor of the test suite
  function DataObjects_TSeasonTest ($name) {
    $this->PHPUnit_TestCase($name);
    
  }

  // called before the test functions will be executed    
  // this function is defined in PHPUnit_TestCase and overwritten
  // here
  function setUp() {
    $s = new DataObjects_TSeason();    
    if ($s->get('1976')) {
      $s->delete();
    }
    $s = new DataObjects_TSeason();
    $s->id = '1976';
    $s->comment = 'x';
    $s->gA1Rate = 5.6;
    $s->gC1Rate = 6.7;
    $s->gA2Rate = 8.9;
    $s->gC2Rate = 9.10;
    $s->kA1Rate = 10.11;
    $s->kC1Rate = 11.12;
    $s->kA2Rate = 12.13;
    $s->kC2Rate = 13.14;
    $s->sA1Rate = 14.15;
    $s->sC1Rate = 15.16;
    $s->sA2Rate = 16.17;
    $s->sC2Rate = 17.18;
    $this->assertTrue($s->insert());
  }

  // called after the test functions are executed    
  // this function is defined in PHPUnit_TestCase and overwritten
  // here    
  function tearDown() {
    $s = DataObjects_TSeason::staticGet(1976);
    $this->assertTrue($s->delete());
  }

  function test_staticGet () {
    $s = DataObjects_TSeason::staticGet(1976);
    $this->assertTrue($s);
  }

  // test the getAll function
  function test_getAll () {
    $result = DataObjects_TSeason::getAll();
    $this->assertTrue(count($result) > 1);
  }

  function test_getTypeRatesK() 
  {
    $rack = array(10.11, 11.12, 12.13, 13.14);
    
    $rs = DataObjects_TSeason::getTypeRates(1, 1976);
    $this->assertEquals(count($rs), 4);
    $this->assertTrue(count(array_diff(array_values($rs), $rack)) == 0);
  }
  
  function test_getTypeRatesG()
  {
    $group = array(5.6,6.7,8.9,9.10);
    $rs = DataObjects_TSeason::getTypeRates(2, 1976);
    $this->assertEquals(count($rs), 4);
    $this->assertTrue(count(array_diff(array_values($rs), $group)) == 0);
  }
    
  function test_getTypeRatesS()
  {
    $recep = array (14.15, 15.16, 16.17, 17.18);
    $rs = DataObjects_TSeason::getTypeRates(3, 1976);
    $this->assertEquals(count($rs), 4);
    $this->assertTrue(count(array_diff(array_values($rs), $recep)) == 0);
  }
    
  function test_getAllRates()
  {

    $rates = array(10.11, 12.13, 11.12, 13.14, 5.6, 8.9, 6.7, 9.1, 14.15, 
		   16.17, 15.16, 17.18);
    $rs = DataObjects_TSeason::getAllRates(1976);   
    $this->assertEquals(count($rs), 12);
    $this->assertTrue(count(array_diff(array_values($rs), $rates)) == 0);
  }
}

?>
