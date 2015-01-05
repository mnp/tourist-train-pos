<?php

//
// Test case for class DataObjects_TReservation
// Automatically generated from TReservation.php
//

require_once 'TCustomer.php';
require_once 'TReservation.php';
require_once 'PHPUnit.php';

class DataObjects_TReservationTest extends PHPUnit_TestCase {

  // test reservation
  var $r;			// the res
  var $rid;			// its id
  var $rw;			// a walkup res
  var $rwid;			// its id
  var $t1id;			// its trains
  var $t2id;
  var $cid;			// its cust

  function runTest() {
    echo 'Res: ' . $this->_name . ' '; 
    //    system("mysql -uroot Touristtest < ../build/testworld.sql");    
    parent::runTest();
    echo " done<br>";
  }
  
  // constructor of the test suite
  function DataObjects_TReservationTest ($name) {
    $this->PHPUnit_TestCase($name);
  }

  
  // called before the test functions will be executed
  // this function is defined in PHPUnit_TestCase and overwritten
  // here
  function setUp() {  
    $this->rid = 1;
    $this->rwid = 2;
    $this->cid = 1;
    $this->t1id = 1;    
    $this->t2id = 52;
  }

  function getR($id) 
  {
    $x = new DataObjects_TReservation;
    $err = $x->get($id);
    if ($err !== 1) {
      die ('****getR oops ***');
    }
    $x->getLinks();
    return $x;    
  }

  function setUp() {
    $this->rid = 1;
    $this->rwid = 2;
    $this->cid = 1;
    $this->t1id = 1;    
    $this->t2id = 52;
  }

  // called after the test functions are executed
  // this function is defined in PHPUnit_TestCase and overwritten
  // here
  function tearDown() {
  }
  
  // intval array compare
  // returns 0 for identical mapped-to-int arrays
  function iac($a,$b)
  {
    return count(array_diff(array_map('intval', $a),
			    array_map('intval', $b)));
  }
  
  function test_setFrom()
  {
    $fakepost = array('actionId' => '-1',
		      'action' => 'create',
		      'custId' => '55',
		      'resId' => '',
		      'train1_run' => '3',
		      'train1_date' => '1111-11-11',
		      'train2_run' => '000000',	 // don't want to find ...
		      'train2_date' => '000000', // ... this train
		      'status' => '0',
		      'deposit' => '77',
		      'dateReceived' => '7777-77-77',
		      'adults' => '7',
		      'children' => '8',
		      'laps' => '9',
		      'specials' => '10',
		      'escorts' => '11',
		      'resComment' => 'c',
		      'a1Rate' => '1',
		      'a2Rate' => '2',
		      'c1Rate' => '3',
		      'c2Rate' => '4');

    $x = new DataObjects_TReservation;
    $x->setFrom($fakepost);
        
    $this->assertEquals(1, $x->a1Rate);
    $this->assertEquals(0, $x->status);
    $this->assertEquals(3, $x->train1_run);
    $this->assertEquals('1111-11-11', $x->train1_date);
   
    // setfrom derives this one from a db lookup: a successful find
    $this->assertTrue($this->t1id == $x->tTrain_id_1, 'ids');

    // setfrom derives this one from a db lookup: an unsuccessful find
    $this->assertEquals(null, $x->tTrain_id_2);
  }

  function test_insert_r()
  {
    $x = $this->getR($this->rid);
    $t1exp = array(34, 4, 47, 10, 8);
    $t1got = $x->_tTrain_id_1->getWupRes();    
    $this->assertEquals(0, $this->iac($t1exp, $t1got));
    $this->assertEquals($x->_tCustomer_id->last, 'jonestest', 'cust insert');
  }

  function test_insert_rw()
  {
    $x = $this->getR($this->rwid);
    $this->assertEquals($x->walkupName, 'Klink', 'cust insert');
    $this->assertEquals($x->walkupState, 'pa', 'cust insert');
    $this->assertEquals($x->_tTrain_id_1->aRes, 34, 'ares 1 insert');
    $this->assertEquals($x->_tTrain_id_1->cRes, 4, 'cres 1 insert');
    $this->assertEquals($x->_tTrain_id_2->aRes, 56, 'ares 2 insert');
    $this->assertEquals($x->_tTrain_id_2->cRes, 14, 'cres 2 insert');
  }

  function test_attempt_big_res()
  {
    $x = new DataObjects_TReservation();
    $x->adults = 100000;
    $x->tCustomer_id = $this->cid;
    $x->tTrain_id_1 = $this->t1id;
    
    $err = $x->createRes();
    $exp = array("HC 09:45 am Train: Insufficient seats: 95 available, 100000 requested.");
    $this->assertEquals($exp, $err);
  }

  function test_staticDeleteRes()
  {    
    $rid = 5;
    
    // The res should be gone, with both trains and cust there.
    $r = $this->getR($rid);
    $q = $r->deleteRes();
    $this->assertTrue($q, 'del');

    $a = new DataObjects_TReservation;
    $this->assertFalse($a->get($rid), 'get res after del');

    $t = new DataObjects_TTrain;
    $this->assertTrue($t->get(3), 'get t1 after del');

    $t = new DataObjects_TTrain;
    $this->assertTrue($t->get(54), 'get t2 after del');

    $c = new DataObjects_TCustomer;
    $this->assertTrue($c->get(1), 'get cust after del');
  }

  // We want a res delete to remove only the res, with his seats getting
  // returned to the trains
  function test_deleteRes_sideffects()
  {
    $r = $this->getR(4);
    $err = $r->deleteRes();
    $this->assertTrue($err, 'del');

    // t1 before delete: 34  4  47 10  8 	56 14 69 20 18
    // chg:		- 1  2   3  4  5       - 1  2  3  4  5
    // t1 after delete : 33  2  44  6  3	55 12 66 16 13 

    $t1 = new DataObjects_TTrain;
    $t1->get(2);
    $t2 = new DataObjects_TTrain;
    $t2->get(53);
    $t1exp = array(33,  2, 44, 6, 3);
    $t2exp = array(55, 12, 66, 16, 13); 

    // should be the trains above minus the riders from the res
    $this->assertEquals(0, $this->iac($t1exp, $t1->getWupRes()), '1');
    $this->assertEquals(0, $this->iac($t2exp, $t2->getWupRes()), '2');
  }

  function test_changeRes0()
  {
    // t1 before: 34 4 47 10  8	     56 14 69 20 18
    // chg     -  1  2  3  4  5	   -  1  2  3  4  5
    // chg     +  2  2  7  4  5    +  2  2  7  4  5
    // t1 after:  35 4 51 10  8  t2: 57 14 73 20 18

    global $cr0;
    $cr0 = 0;
    $r = $this->getR(3);
    $cr0 = 0;

    $t1_exp = array(35,  4, 51, 10,  8);
    $t2_exp = array(57, 14, 73, 20, 18);
    $r_acles_exp = array(2, 2, 7, 4, 5);

    // do an update to the body
    $r->adults = 2;	// was 1
    $r->laps =   7;	// was 3
    
    $errs = $r->changeRes();
   
    $this->assertTrue(true === $errs, 'changeRes0');
    if ($errs !== true) {
      MNP::dp($errs, 'err from changeRes0');
    }

    // re-read the res and trains, make sure everyone got new numbers
    $r  = new DataObjects_TReservation;
    $t1 = new DataObjects_TTrain;
    $t2 = new DataObjects_TTrain;

    $this->assertTrue($r->get(3), 'get r after changeRes0');
    $this->assertTrue($t1->get(1), 'get t1 after changeRes0');
    $this->assertTrue($t2->get(52), 'get t2 after changeRes0');
    //    MNP::dp($t1);
    //      MNP::dp($t2);
    
    // r shouldn't change acles.
    $this->assertEquals(0, $this->iac($r_acles_exp, 
				      array_values($r->_getACLES())), 'r');
    
    // t's should change
    $this->assertEquals(0, $this->iac($t1_exp, $t1->getWupRes()), '1');
    $this->assertEquals(0, $this->iac($t2_exp, $t2->getWupRes()), '2');
  }

  function test_changeRes1()	// move the date of a RT. involves 4 trains!
  {
    // t1 before: 1  2  3  4  5	   -  1  2  3  4  5
    // chg     -  1  2  3  4  5	   -  1  2  3  4  5
    // t1 after:  0  0  0  0  0   t2: 0  0  0  0  0
    // tid=10			  tid=60

    // t3 before: 0  0  0  0  0       0  0  0  0  0
    // chg     +  1  2  3  4  5	   -  1  2  3  4  5
    // t3 after:  1  2  3  4  5	  t4: 1  2  3  4  5
    // tid=11			  tid=61

    $rid  = 8;    
    $t1id = 10;
    $t2id = 60;
    $t3id = 11;
    $t4id = 61;
    $t1_exp = array(0, 0, 0, 0, 0);
    $t2_exp = array(0, 0, 0, 0, 0);
    $t3_exp = array(1, 2, 3, 4, 5);
    $t4_exp = array(1, 2, 3, 4, 5);
    $r_acles_exp = array(1, 2, 3, 4, 5);

    // get and update the res
    $r = $this->getR($rid);
    $r->tTrain_id_1 = $t3id;
    $r->tTrain_id_2 = $t4id;
    
    $errs = $r->changeRes();
    $this->assertTrue(true === $errs, 'changeRes1');
    if ($errs !== true) {
      MNP::dp($errs, 'err from changeRes1');
    }

    // re-read the res and trains, make sure everyone got new numbers
    $r  = new DataObjects_TReservation;
    $t1 = new DataObjects_TTrain;
    $t2 = new DataObjects_TTrain;
    $t3 = new DataObjects_TTrain;
    $t4 = new DataObjects_TTrain;

    $this->assertTrue($r->get($rid), 'get r after changeRes1');    
    $this->assertTrue($t1->get($t1id), 'get t1 after changeRes1');
    $this->assertTrue($t2->get($t2id), 'get t2 after changeRes1');
    $this->assertTrue($t3->get($t3id), 'get t3 after changeRes1');
    $this->assertTrue($t4->get($t4id), 'get t4 after changeRes1');

    // r shouldn't change acles.
    $this->assertEquals(0, $this->iac($r_acles_exp, 
				      array_values($r->_getACLES())), 'r');

    // t's should change
    $this->assertEquals(0, $this->iac($t1_exp, $t1->getWupRes()), '1');
    $this->assertEquals(0, $this->iac($t2_exp, $t2->getWupRes()), '2');
    $this->assertEquals(0, $this->iac($t3_exp, $t3->getWupRes()), '3');
    $this->assertEquals(0, $this->iac($t4_exp, $t4->getWupRes()), '4');
  }

  function test_changeRes2()	// Go from OW to RT
  {
    // t1 before: 34 4 47 10  8	     56 14 69 20 18
    // chg     -		   +  1  2  3  4  5
    // t1 after:  34 4 47 10  8  t2: 57 16 72 24 23

    $r = $this->getR(7);
    $t1_exp = array(34,  4, 47, 10,  8);
    $t2_exp = array(57, 16, 72, 24, 23);    
    $r_acles_exp = array(1, 2, 3, 4, 5);

    // do an update to the body: add train2
    $r->tTrain_id_2 = 56;
    $errs = $r->changeRes();

    $this->assertTrue(true === $errs, 'changeRes2');
    if ($errs !== true) {
      MNP::dp($errs, 'err from changeres2');
    }

    // re-read the res and trains, make sure everyone got new numbers
    $r  = new DataObjects_TReservation;
    $t1 = new DataObjects_TTrain;
    $t2 = new DataObjects_TTrain;

    $this->assertTrue($r->get(7), 'get r after changeRes');
    $this->assertTrue($t1->get(5), 'get t1 after changeRes');
    $this->assertTrue($t2->get(56), 'get t2 after changeRes');

    // r shouldn't change acles.
    $this->assertEquals(0, $this->iac($r_acles_exp, 
				      array_values($r->_getACLES())), 'r');
    
    // t1 shouldn't change. t2 should change.
    $this->assertEquals(0, $this->iac($t1_exp, $t1->getWupRes()), '1');
    $this->assertEquals(0, $this->iac($t2_exp, $t2->getWupRes()), '2');
  }

  function test_changeRes3()	// Go from RT to OW
  {
    // t1 before: 34 4 47 10  8	     56 14 69 20 18
    // chg     -		   -  1  2  3  4  5
    // t1 after:  34 4 47 10  8  t2: 55 12 66 16 13

    $r = $this->getR(6);
    $t1_exp = array(34,  4, 47, 10,  8);
    $t2_exp = array(55, 12, 66, 16, 13);    
    $r_acles_exp = array(1, 2, 3, 4, 5);

    // do an update to the body: drop train2
    unset($r->tTrain_id_2);
    $errs = $r->changeRes();

    $this->assertTrue(true === $errs, 'changeRes3');
    if ($errs !== true) {
      MNP::dp($errs, 'err from changeres3');
    }

    // re-read the res and trains, make sure everyone got new numbers
    $r  = new DataObjects_TReservation;
    $t1 = new DataObjects_TTrain;
    $t2 = new DataObjects_TTrain;

    $this->assertTrue($r->get(6), 'get r after changeRes');
    $this->assertTrue($t1->get(4), 'get t1 after changeRes');
    $this->assertTrue($t2->get(55), 'get t2 after changeRes');

    // r shouldn't change acles.
    $this->assertEquals(0, $this->iac($r_acles_exp, 
				      array_values($r->_getACLES())), 'r');
    
    // t1 shouldn't change. t2 should change.
    $this->assertEquals(0, $this->iac($t1_exp, $t1->getWupRes()), '1');
    $this->assertEquals(0, $this->iac($t2_exp, $t2->getWupRes()), '2');
  }
  
  function test_walkupReserveRT()
  {
    // t1 before: 34 4 47 10  8	      55 12 66 16 13
    // chg     +  10 0  0  0 10    +   10 0  0  0 10
    // ------------------------	  ------------------
    // t1 after:  44 4 47 10 18   t2: 65 12 66 16 23

    $t1exp = array(44,  4, 47, 10, 18);
    $t2exp = array(65, 12, 66, 16, 23);

    $post = array('adults' => 10,
		  'specials' => 10,
		  'walkupName' => 'Timmy',
		  'walkupState' => 'fl',
		  'tTrain_id_1' => 4,
		  'tTrain_id_2' => 55);

    $r = new DataObjects_TReservation;
    $t1 = new DataObjects_TTrain;
    $t2 = new DataObjects_TTrain;

    $r->setFrom($post);
    $err = $r->walkupReserve(1, 0);
    if (!is_int($err) || $err < 1) {
      MNP::dp($err);
    }

    $this->assertTrue(is_int($err) && $err > 0, 'walkupRes');
    if (!is_int($err) || $err < 1) {
      MNP::dp($err);      
    }

    $t1 = new DataObjects_TTrain;
    $t2 = new DataObjects_TTrain;
    $this->assertTrue($t1->get(4), 'get t1 after reserve');
    $this->assertTrue($t2->get(55), 'get t2 after reserve');

    $this->assertEquals(0, $this->iac($t1exp, $t1->getWupRes()));
    $this->assertEquals(0, $this->iac($t2exp, $t2->getWupRes()));
    $r->deleteRes();
  }

  function test_walkupReserveOW()
  {
    // t1 before: 44 4 47 10 18
    // chg     +  10 0  0  0 10
    // ------------------------
    // t1 after:  54 4 47 10 28

    $t1exp = array(54, 4, 47, 10, 28);

    $post = array('adults' => 10,
		  'specials' => 10,
		  'walkupName' => 'Mary',
		  'walkupState' => 'mo',
		  'tTrain_id_1' => 4);
    $r = new DataObjects_TReservation;
    $r->setFrom($post);
    $this->assertTrue($r->walkupReserve(1, 0), 'walkupRes');

    $t1=new DataObjects_TTrain;
    $this->assertTrue($t1->get(4), 'get t1 after reserve');
    $this->assertEquals(0, $this->iac($t1exp, $t1->getWupRes()));
    $r->delete();
  }

  function test_checkin()
  {
    $r = $this->getR($this->rid);
    $this->assertEquals($r->status, RES_RESERVED, 'pre');
    $err = $r->checkin();
    $this->assertTrue($err, 'err');

    $r = $this->getR($this->rid);
    $this->assertEquals($r->status, RES_CHECKEDIN,  'chk');
  }

  function test_release()
  {
    $r = $this->getR($this->rid);
    $this->assertEquals($r->status, RES_CHECKEDIN, 'pre');
    $err = $r->release();
    $this->assertTrue($err, 'err');

    $r = $this->getR($this->rid);
    $this->assertEquals($r->status, RES_RELEASED, 'chk');
  }

  function test_computeDue()
  {
    $r = $this->getR($this->rid);
    $x = $r->computeDue();
    $this->assertEquals(16*1 + 9*2 - 22, $x);
  }

  function test_customerChecks()
  {
    $this->assertTrue(0, 'NIY');
  }
}

?>
