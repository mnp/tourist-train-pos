<?
require_once 'PHPUnit.php';
require_once 'MagneticStripeCardTest.php';

$suite  = new PHPUnit_TestSuite("MagneticStripeCardTest");
$result = PHPUnit::run($suite);
echo $result->toString();
?>