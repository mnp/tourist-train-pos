<?php

//
// Test case for class DataObjects_TTrain
// Automatically generated from TTrain.php
//

require_once 'TTrain.php';
require_once 'PHPUnit.php';

class DataObjects_TTrainTest extends PHPUnit_TestCase {

  // constructor of the test suite
  function DataObjects_TTrainTest ($name) {
    $this->PHPUnit_TestCase($name);

  }

  function runTest() {
    echo 'Train: ' . $this->_name . ' ';
    parent::runTest();
    echo " done<br>";
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

  function test_findDateRunTrain0()
  {
    // first make a train with some runid
    $run = new DataObjects_TSchedRun();
    $this->assertTrue($run->find(true) > 0);
    $t = new DataObjects_TTrain();
    $t->tSchedRun_id = $run->runId;
    $t->date = '1980-03-24';
    $t->insert() or die;
    $resobj = DataObjects_TTrain::findDateRunTrain($run->runId, '1980-03-23');
    $this->assertTrue(is_null($resobj), 'id match');
    $t->delete();
  }

  function test_findDateRunTrain1()
  {
    // first make a train with some runid
    $run = new DataObjects_TSchedRun();
    $this->assertTrue($run->find(true) > 0);
    $t = new DataObjects_TTrain();
    $t->tSchedRun_id = $run->runId;
    $t->date = '1980-03-24';
    $t->insert() or die;
    $resobj = DataObjects_TTrain::findDateRunTrain($run->runId, '1980-03-24');
    $this->assertTrue(!is_null($resobj), 'id match');
    $this->assertTrue($resobj->trainId == $t->trainId, 'id match');
    $t->delete();
  }

  // Return a train with some seats and walkups reserved.  The available
  // seats will be 42.  Some slots are not filled intentionally, as this
  // will be normal.
  function seatPrep ()
  {
    $t = new DataObjects_TTrain();
    $t->trainId = 99999;
    $t->seats = 200;
    $t->comfort = 5;
    $t->eRes = 10;
    $t->cRes = 20;
    $t->sRes = 30;
    $t->aRes = 88;
    return $t;
  }

  function test_getAvailableSeats()
  {
    $t = $this->seatPrep();
    $this->assertEquals($t->getAvailableSeats(), 47);
  }

  function test_adjustPassengers0()
  {
    $t = $this->seatPrep();
    $errs = array();
    $out = $t->adjustPassengers(ADJUST_MODE_ADD,
				array('cRes' => 10, 'aRes' => 3),
				$errs);
    $this->assertTrue($out == TRUE, 'adjusted');
    $this->assertEquals($t->getAvailableSeats(), 34, 'count ok');
  }

  function test_adjustPassengers0a()
  {
    $t = $this->seatPrep();
    $errs = array();
    $out = $t->adjustPassengers(ADJUST_MODE_DEL, array('aRes' => 88), $errs);
    $this->assertTrue($out === TRUE, 'adjusted');
    $this->assertTrue($t->getAvailableSeats() === 135, 'count ok');
  }

  function test_adjustPassengers1()
  {
    $t = $this->seatPrep();
    $errs = array();
    $out = $t->adjustPassengers(ADJUST_MODE_DEL, array('eRes' => 9999), $errs);

    $this->assertEquals(false, $out);
    $this->assertEquals(array("tBS-oops Train : excess eRes seats refunded: 10 currently reserved, 9999 requested for refund."), $errs);

    // want no chg
    $this->assertEquals($t->getAvailableSeats(), 47);
  }

  function test_adjustPassengers2()
  {
    $t = $this->seatPrep();
    $errs = array();
    $out = $t->adjustPassengers(ADJUST_MODE_DEL,
				array('eRes' => 300, 'cRes' => 2),
				$errs);
    $this->assertEquals($out, false);
    $this->assertEquals($errs, array("tBS-oops Train : excess eRes seats refunded: 10 currently reserved, 300 requested for refund."));
    // want no chg
    $this->assertEquals(47, $t->getAvailableSeats());
  }

  function test_adjustPassengers3()
  {
    $t = $this->seatPrep();
    $errs = array();
    $out = $t->adjustPassengers(ADJUST_MODE_DEL, array('eRes' => 333), $errs);
    $this->assertTrue($out !== TRUE);
    $this->assertEquals(array("tBS-oops Train : excess eRes seats refunded: 10 currently reserved, 333 requested for refund."), $errs);
    // s/b no chg
    $this->assertEquals($t->getAvailableSeats(), 47, 'count ok');
  }

  function test_makeTrainErrors()
  {
    $this->assertTrue(0, 'NIY');
  }

  function test_getDateTrains()
  {
    $trains = DataObjects_TTrain::getDateTrains('2030-11-22');
    $this->assertEquals(11, $trains[0]->trainId);
    $this->assertEquals(61, $trains[1]->trainId);
    $this->assertEquals(2, count($trains));
  }

  function test_getDateTrains_hc()
  {
    $trains = DataObjects_TTrain::getDateTrains('2030-11-22', 1);
    $this->assertEquals(11, $trains[0]->trainId);
    $this->assertEquals(1, count($trains));
  }

  function test_getDateTrains_ks()
  {
    $trains = DataObjects_TTrain::getDateTrains('2030-11-22', 2);
    $this->assertEquals(61, $trains[0]->trainId);
    $this->assertEquals(1, count($trains));
  }

  function test_getDateTrains_ofc()
  {
    $trains = DataObjects_TTrain::getDateTrains('2030-11-22', 3);
    $this->assertEquals(11, $trains[0]->trainId);
    $this->assertEquals(61, $trains[1]->trainId);
    $this->assertEquals(2, count($trains));
  }

  function test_returningTrain_0()
  {
    // should fail: no train after 4
    $t1 = DataObjects_TTrain::findDateRunTrain(4, '1111-11-11');
    $this->assertTrue(!is_null($t1));
    $t2 = DataObjects_TTrain::returningTrain($t1);
    $this->assertEquals(null, $t2->trainId);
  }

  function test_returningTrain_1()
  {
    // should find train 6; run 4 is after run 3
    $t1 = DataObjects_TTrain::findDateRunTrain(3, '1111-11-11');
    $this->assertTrue(!is_null($t1));
    $t2 = DataObjects_TTrain::returningTrain($t1);
    $this->assertEquals(6, $t2->trainId);
  }

  function test_nextDeparture_0_0_0()
  {
    $out = DataObjects_TTrain::nextDeparture();    
    $this->assertEquals(null, $out->trainId);
  }

  function test_nextDeparture_1_1_hc()
  {
    $out = DataObjects_TTrain::nextDeparture('2030-11-22', '9:00', 1);
    $this->assertEquals(11, $out->trainId);
  }

  function test_nextDeparture_1_1_ks()
  {
    $out = DataObjects_TTrain::nextDeparture('2030-11-22', '9:00', 2);
    $this->assertEquals(61, $out->trainId);
  }

  function test_nextDeparture_1_1_ofc()
  {
    $out = DataObjects_TTrain::nextDeparture('2030-11-22', '9:00', 3);
    $this->assertEquals(11, $out->trainId);
  }

  //--

  function test_nextDeparture_1()
  {
    $out = DataObjects_TTrain::nextDeparture('2030-11-22');
    $this->assertEquals(11, $out->trainId);
  }

  function test_nextDeparture_1_0_hc()
  {
    $out = DataObjects_TTrain::nextDeparture('2030-11-22', null, 1);
    $this->assertEquals(11, $out->trainId);
  }

  function test_nextDeparture_1_0_ks()
  {
    $out = DataObjects_TTrain::nextDeparture('2030-11-22', null, 2);
    $this->assertEquals(61, $out->trainId);
  }

  function test_nextDeparture_1_0_ofc()
  {
    $out = DataObjects_TTrain::nextDeparture('2030-11-22', null, 3);
    $this->assertEquals(11, $out->trainId);
  }

  //--

  function test_searchTrainArray_midnight()
  {
    $trains = DataObjects_TTrain::getDateTrains('2030-11-22');
    $out = DataObjects_TTrain::searchTrainArray($trains, strtotime('2030-11-22 00:00:00'));
    $this->assertEquals(11, $out->trainId);
  }

  function test_searchTrainArray_middle()
  {
    $trains = DataObjects_TTrain::getDateTrains('2030-11-22');
    $out = DataObjects_TTrain::searchTrainArray($trains, 
					strtotime('2030-11-22 10:00:00'));
    $this->assertEquals(61, $out->trainId);
  }

  function test_searchTrainArray_end()
  {
    $trains = DataObjects_TTrain::getDateTrains('2030-11-22');
    $out = DataObjects_TTrain::searchTrainArray($trains, strtotime('2030-11-22 14:00'));
    $this->assertEquals(61, $out->trainId);
  }

  function test_arrayfindTrainId_found()
  {
    $trains = DataObjects_TTrain::getDateTrains('1111-11-11');
    $out = DataObjects_TTrain::arrayFindTrainId($trains, 2);
    $this->assertEquals(2, $out->trainId);
  }

  function test_arrayfindTrainId_notfound()
  {
    $trains = DataObjects_TTrain::getDateTrains('2030-11-22');
    $out = DataObjects_TTrain::arrayFindTrainId($trains, 888888888);
    $this->assertTrue(is_null($out));
  }
}

?>
