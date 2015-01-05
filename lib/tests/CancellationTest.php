<?php

require_once 'TCancellation.php';
require_once 'MemoryCancellation.php';
require_once 'PHPUnit.php';

class CancellationTest extends PHPUnit_TestCase {

  // constructor of the test suite
  function CancellationTest ($name) {
    $this->PHPUnit_TestCase($name);
  }

  function setUp()  {
    $this->m = new MemoryCancellation;
    $this->m->id = 11;
    $this->m->creditType = 'xx';
    $this->m->creditAccount = '1234123412341234';
    $this->m->creditName = 'smith jones';
    $this->m->creditAddress = '111 main';
    $this->m->creditExpirationDate = '020202';
    $this->m->creditCVV2 = '321';
  }

  function tearDown()  {
  }

  function test_enc() 
  {
    $c = new DataObjects_TCancellation;
    $encrypted = $c->encrypt("noriing", $this->m);
    MNP::dp($encrypted);
  }
}

?>