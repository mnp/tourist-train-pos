<?php 

//
// Create a PHPUnit test case for a php class file.  Groks for methods
// and class names.  BUG: Ignores comments, needs preprocessing.
//
// Mitchell Perilstein <mitch@enetis.net>
//

$fd=fopen($argv[1], "r");
while ($line=fgets($fd,1000)) {
  if (preg_match('/function\s+&?([^( \t]+)/', $line, $matches)) {
    $funs[] = $matches[1];
  }
  elseif (preg_match('/class\s+([^{ \t]+)/', $line, $matches)) {
    if (isset($class)) {
      die ("more than one class in {$argv[1]}\n");
    }
    $class = $matches[1];
  }
}
fclose ($fd);

echo "<?php\n";
?>

//
// Test case for class <?= $class ?>

// Automatically generated from <?= $argv[1] ?>

//

require_once '<?= $argv[1] ?>';
require_once 'PHPUnit.php';

class <?= $class . 'Test' ?> extends PHPUnit_TestCase {

  // contains the object handle of the string class
  var $objh;

  // constructor of the test suite
  function <?= $class . 'Test' ?> ($name) {
    $this->PHPUnit_TestCase($name);
    
  }

  // called before the test functions will be executed    
  // this function is defined in PHPUnit_TestCase and overwritten
  // here
  function setUp() {
    $this->objh = new <?= $class ?> ();
  }

  // called after the test functions are executed    
  // this function is defined in PHPUnit_TestCase and overwritten
  // here    
  function tearDown() {
    // delete your instance
    unset($this->objh);
  }

<?php
  foreach ($funs as $fun) 
  {
    echo "  // test the $fun function\n";
    echo "  function test_{$fun} () {\n";
    echo "    // \$result = \$this->objh->{$fun}();\n";
    echo "    \$this->assertTrue(FALSE);\n";
    echo "  }\n\n";
  }
  

?>

}

?>
