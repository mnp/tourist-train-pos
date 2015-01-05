<?php

require_once 'DailyTrainColumn.php';
require_once 'PHPUnit.php';

class DailyTrainColumnTest extends PHPUnit_TestCase {

  var $train;
  var $dtc;

  // constructor of the test suite
  function DailyTrainColumnTest ($name) {
    $this->PHPUnit_TestCase($name);
  }

  function setUp()  {
    $this->train = new DataObjects_TTrain;
    $this->train->query("delete from tTrain");
    $this->train->query("INSERT INTO tTrain VALUES (1,3,'2003-05-05',198,NULL,34,34,5,4,NULL,361525,361501,122554,122553,5054,5001,52082,52080,432,NULL,35,NULL,1,NULL,33,NULL,NULL,NULL,NULL,NULL),(2,3,'2003-05-06',198,NULL,31,45,2,7,NULL,361550,361525,122555,122554,35105,35054,52090,52082,432,14,NULL,NULL,NULL,8,NULL,NULL,NULL,NULL,214,NULL)");
    $this->train->get(1);
    $this->dtc = new DailyTrainColumn($this->train);   
  }

  function tearDown()  {
  }

  function test_tix() 
  {
    $this->assertEquals(24, $this->dtc->adultTix);
    $this->assertEquals(1, $this->dtc->childTix);
    $this->assertEquals(53, $this->dtc->groupTix);
    $this->assertEquals(2, $this->dtc->lcsTix);
    $this->assertEquals(80, $this->dtc->totalTickets);
  }
  
  function test_totals()
  {    
    $this->assertEquals(467, $this->dtc->adultRcptTotal);
    $this->assertEquals(34, $this->dtc->childRcptTotal);
    $this->assertEquals(0, $this->dtc->openRcptTotal);
    $this->assertEquals(501, $this->dtc->totalReceipts);        
  }

}

?>