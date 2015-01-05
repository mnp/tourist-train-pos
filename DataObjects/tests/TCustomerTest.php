<?php

//
// Test case for class DataObjects_TCustomer
// Automatically generated from TCustomer.php
//

require_once 'TCustomer.php';
require_once 'PHPUnit.php';
error_reporting(E_ALL);

class DataObjects_TCustomerTest extends PHPUnit_TestCase {

  var $c;			// a customer
  var $cid;			//   its id
  var $t1;			// a train
  var $t1id;			//   its id
  var $r1;			// a res
  var $r2;			// a res

  // constructor of the test suite
  function DataObjects_TCustomerTest ($name) {
    $this->PHPUnit_TestCase($name);
  }

  function runTest() {
    echo 'Cust: ' . $this->_name . ' '; 
    parent::runTest();
    echo " done<br>";
  }

  function setUp() 
  {
    $n = new DataObjects_TCustomer;    
    $r = new DataObjects_TReservation();
    $n->query("DELETE FROM tCustomer");
    $n->query("DELETE from tReservation");
    $n->query("DELETE from tTrain");
    $n->query("DELETE from tSchedRun");
    $n->query("INSERT INTO tTrain VALUES (1,3,'2003-05-05',198,NULL,27,30,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)");
    $n->query("INSERT INTO tTrain VALUES (2,3,'2003-05-06',198,NULL,10,45,NULL,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL)");
    
    $n = new DataObjects_TCustomer;
    $n->last = 'asdfasjiow4lkj234l21kj41234';
    $n->first = 'bb';
    $n->address1 = 'cc';
    $n->address2 = 'dd';
    $n->city = 'ee';
    $n->state = 'Maryland';
    $n->province = 'gg';
    $n->zip = 'hh';
    $n->country = 'us';
    $n->phone = 'jj';
    $n->email = 'kk';
    $n->tCustType_id = 2;
    $n->custRate = 123;
    $n->tSourceType_id = 2;
    $n->custComment = 'mm';

    $errs = $n->checkRequired();
    
    if ($errs !== TRUE) {
      echo __FILE__;
      print_r($errs);
    }

    $this->cid = $n->insert();

    $this->assertTrue(is_int($this->cid));
    $this->assertTrue($this->cid > 0);
    
    $this->c =& $n;

    // schedrun
    $this->sr = new DataObjects_TSchedrun;
    $this->sr->tSeason_id = 999;
    $this->sr->tStation_id = 999;
    $this->sr->tTime_id = 999;
    $this->srid = $this->sr->insert();

    // make a train
    /*
    $this->t1 = new DataObjects_TTrain;
    $this->t1->date = '1111-11-11';
    $this->t1->tSchedRun_id = $this->srid;
    $this->t1->aRes = 34;
    $this->t1->seats = 400;
    $this->t1id = $this->t1->insert();
    */
    $this->t1id = 1;

    // make a couple reservations
    $this->r1 = new DataObjects_TReservation();
    $this->r1->adults = 7;
    $this->r1->tCustomer_id = $this->cid;
    $this->r1->tTrain_id_1 = $this->t1id;
    $this->r1->comment = 'asdlkjaoiqrtzvd';
    $this->r1id = $this->r1->insert();

    // make a couple reservations
    $this->r2 = new DataObjects_TReservation();
    $this->r2->adults = 13;
    $this->r2->tCustomer_id = $this->cid;
    $this->r2->tTrain_id_1 = $this->t1id;
    $this->r2->comment = 'asdlkjaoiqrtzvd';
    $this->r2id = $this->r2->insert();   
  }

  function tearDown()
  {
    $x = new DataObjects_TReservation();
    if ($x->get($this->r1id)) {
      $x->delete();
    }
    $x = new DataObjects_TReservation();
    if ($x->get($this->r2id)) {
      $x->delete();
    }
    $x = new DataObjects_TTrain();
    if ($x->get($this->t1id)) {
      $x->delete();
    }
    $x = new DataObjects_TSchedRun();
    if ($x->get($this->srid)) {
      $x->delete();
    }
    $x = new DataObjects_TCustomer();
    if ($x->get($this->cid)) { 
      $x->delete(); 
    }    
  }

  function checkCust($c, $id)
  {
    return 
      $c->custId == $id
      && $c->last == 'asdfasjiow4lkj234l21kj41234'
      && $c->custComment == 'mm';
  }
  
  function test_insert_get() 
  {
    $this->assertTrue(is_int($this->cid) && $this->cid > 0, "inserting");
    $this->assertTrue($this->checkCust($this->c, $this->cid), 'postinsert');
  }
  
  function test_get() 
  {
    $x = new DataObjects_TCustomer;
    $numrows = $x->get($this->c->custId);
    $this->assertEquals(1, $numrows, "get {$this->c->custId} numrows==$numrows");
    $this->assertTrue($this->checkCust($x, $this->c->custId), 'got okay');
  }
  
  function test_staticGet() 
  {
    $x = DataObjects_TCustomer::staticGet($this->c->custId);
    $this->assertTrue($this->checkCust($x, $this->c->custId), 'staticGet ok');
  }
  
    
  function test_find() 
  {    
    $x = new DataObjects_TCustomer();   
    $x->last = 'asdfasjiow4lkj234l21kj41234';
    $numfound = $x->find();
    $this->assertEquals(1, $numfound);
    $this->assertTrue($x->fetch());
    $this->assertTrue($this->checkCust($x, $this->c->custId), "check find");
  }

  function test_update() 
  {    
    $this->c->first = '0xdeadbeef';
    $this->c->phone = 'pqrst123';    
    $res = $this->c->update();
    $rest = is_array($res) ? join('/', $res) : '...';
    $this->assertTrue($res == TRUE, "update:" . $rest);

    // $x = DataObjects_TCustomer::staticGet('custId', $this->cid);
    $x = new DataObjects_TCustomer;
    $x->get($this->cid);

    //    MNP::dp($res,'update result');
    //    MNP::dp($x,'test_update');
    
    $this->assertTrue($x->phone == 'pqrst123' && $x->first=='0xdeadbeef', 
		      "check update");
  }
  
  function test_delete () 
  {
    $delres = $this->c->delete();
    $this->assertTrue($delres === true, "delete");

    $x = new DataObjects_TCustomer();    
    $this->assertFalse($x->get($this->cid), "get after delete");
    
    $y = new DataObjects_TCustomer();
    $y->last = 'asdfasjiow4lkj234l21kj41234';
    $numfound = $y->find();
    $this->assertEquals($numfound, 0, "find after delete");
    
    $z = new DataObjects_TCustomer();
    $this->assertFalse($z->get($this->cid), "get after delete");
  }

  // We want a customer delete to remove all his reservations
  function test_delete_sideffects()
  {
    $id = $this->c->custId;	// save it bc obj is dying

    $t = new DataObjects_TTrain;
    $this->assertTrue($t->get($this->t1id), 'the train lives');    
    $px0 = $t->getWupRes();

    // should be some reservations
    $r = new DataObjects_TReservation;
    $r->whereAdd("tCustomer_id = $id");
    $nf = $r->find();
    $this->assertTrue($nf > 0, 'should find some res for cust');   

    // del cust and kids
    $errs = $this->c->deleteCustAndRes();
    $this->assertTrue($errs === true, "delete");
    
    // check del
    $c = new DataObjects_TCustomer;
    $x = $c->get($id);
    $this->assertTrue(0 === $x, 'cust deleted');

    // now there shouldn't
    $r = new DataObjects_TReservation;
    $r->whereAdd("tCustomer_id = $id");
    $nf = $r->find();    
    $this->assertEquals(0, $nf, 'shouldnt find any res for cust');
        
    // check the train, should have dropped also, by the riders from all the
    // reservations
    $t = new DataObjects_TTrain;
    $this->assertTrue($t->get($this->t1id), 'the train lives');    
    $px1 = $t->getWupRes();

    $exp0 = array(27, 30, null, 4, null);
    $exp1 = array(7 , 30, null, 4, null);

    $this->assertEquals(0, $this->intArrayCompare($px0, $exp0));
    $this->assertEquals(0, $this->intArrayCompare($px1, $exp1));
  }

  function intArrayCompare($a,$b)
  {
    return count(array_diff(array_map('intval', $a),
			    array_map('intval', $b)));
  }

  function test_checkRequired() 
  {
    $a =& $this->c;
    $this->assertTrue($a->requiredField('last'),'a');
    $this->assertFalse($a->requiredField('city'),'b');
    $this->assertTrue($a->checkRequired(),'c');
    $this->assertTrue($a->checkRequired(),'d');
    $a->city = '';
    $errs = $a->checkRequired();
    $this->assertFalse(is_array($errs),'e');

    $a->last = '';
    $errs = $a->checkRequired();
    //echo __FILE__;
    //print_r($errs);
    
    $this->assertTrue(is_array($errs),'f');
  }
}

?>
