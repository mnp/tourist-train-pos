<?php
//
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Alexander Merz <alexmerz@php.net>                            |
// +----------------------------------------------------------------------+
//
// $Id: test.php,v 1.3 2003/03/21 20:56:41 mitch Exp $
//

require_once '../lib/base.php';
require_once '../lib/MNP.php';
require_once "PHPUnit.php";
//PEAR::setErrorHandling(PEAR_ERROR_PRINT); // or _DIE

define('TESTWORLD', '../build/testworld.sql');
define("GRAPH_WIDTH", 200);	// pixels

ini_set('implicit_flush', 'On');
error_reporting(E_ALL);
$tests = array();

if (file_exists(ROOTPATH . '/tests-to-run')) {
  $tests = preg_split ("/[\s,]+/", file_get_contents(ROOTPATH . '/tests-to-run'));
}
else {
  // Array of test dirs - we will snag everything in each
  $dirs = array (ROOTPATH . '/DataObjects/tests',
		 ROOTPATH . '/src/tests',
		 ROOTPATH . '/lib/tests');
  foreach ($dirs as $dirname) {
    $dirp = dir($dirname);
    while (false !== ($f = $dirp->read())) {
      if (!preg_match('/^\./', $f) && !preg_match('/~$/', $f)) {
	$tests[] = $dirp->path . '/' . $f;      
      }
    }
    $dirp->close();
  }
}

//
// Change the options to use the prebuilt test database instead of the
// main one.
//
$options =& PEAR::getStaticProperty('DB_DataObject','options');
$db =& $options['database'];
$db = preg_replace('/main/', 'test', $db);

// Doublecheck!
$options =& PEAR::getStaticProperty('DB_DataObject','options');
if (!preg_match('/test/', $options['database']) ||
    preg_match('/main/', $options['database'])) {
  die ("unit test could not switch to testing database");
}


function printStrs($strs) 
{
  if ($strs) {
    foreach($strs as $str) {
      echo $str;
    }
  }
  else {
    echo "-- None --";
  }
}

/** 
 * Get the name of the first class in a file. Does not handle comments.
 */
function getClassName ($file)
{
  $fp = fopen($file, "r");
  while( !feof($fp) ) {
    $line = fgets($fp, 4096);
    if (preg_match('/^class\s+(\S+)/', $line, $matches)) {
      fclose($fp);
      return $matches[1];
    }
  }
  die("no class in $file");
}

function graph ($okWidth, $notOkWidth)
{
  $out = '<table width="' 
    . GRAPH_WIDTH 
    . '" height="15px" cellspan="0" cellpadding="0">'
    . '<tr>'
    . '<td width="' . $okWidth    . '%" bgcolor="green"></td>'
    . '<td width="' . $notOkWidth . '%" bgcolor="red"></td>'
    . '</tr></table>';
  //    MNP::dp($okWidth);    
    return $out;
}

?>

<html>
<head>
    <title>Unit Test Results</title>
<style>
  textarea {
    font-family : System;
    font-size : 15;
   }
 </style>
</head>
<body>
    <h1>Unit Test Results</h1>

    <table border=1 cellspacing=0 cellpadding=1>
      <tr>
	<th align="left">Test Name</th>
	<th align="right">Runs</th>
	<th align="right">Errs</th>
	<th align="right">Fails</th>
	<th width="<?= GRAPH_WIDTH ?>" align="left">
	  Percent Succeeding <br>
	  <table width="100%">
            <tr>
              <td width="33%" align="left">0</td>
              <td width="33%" align="center">50</td>
              <td width="33%" align="right">100</td>
    	    </tr>
	  </table>    
	</th>
      </tr>

<?php

$totRuns = 0;
$totErrs = 0;
$totFails = 0;

foreach ($tests as $testFile)
{
  if (empty($testFile)) {
    continue;
  }
  
  system("mysql -uroot Touristtest < " . TESTWORLD);
  
  $testName = getClassName($testFile);
  include $testFile;

  echo "$testName<br>";  		

  // run test
  $suite = new PHPUnit_TestSuite($testName);  
  $result = PHPUnit::run($suite);
  
  // do some calculations
  $rc = $result->runCount();
  if ($rc == 0) {
    $graph = '(No result)';
  }
  else {
    $per = 100/$rc;
    $notOkWidth = ($per*$result->errorCount())+($per*$result->failureCount());
    $okWidth = 100 - $notOkWidth ;    
    $failures = $result->failures();
    $errors = $result->errors();

    $totRuns += $result->runCount();
    $totErrs += $result->errorCount();
    $totFails += $result->failureCount();

    if ($failures) {
      $failstrs[] = "---- $testName ----------------------------\n";
    }   
    foreach ($failures as $failure) {
      $failstrs[] = $failure->toString();
    }

    if ($errors) {
      $errstrs[] = "---- $testName -------------------------------\n";
    }   
    foreach ($errors as $error) {
      $errstrs[] = $error->toString();
    }
    $graph = graph($okWidth, $notOkWidth); 
  }
  
  ?>

    <tr>
       <td align="left"> <?= $testName ?> </td>
       <td align="right"> <?= $result->runCount() ?> </td>
       <td align="right"> <?= $result->errorCount() ?> </td>
       <td align="right"> <?= $result->failureCount() ?> </td>
       <td align="left"> <?= $graph ?> </td>
     </tr>

<?php
} // foreach test

?>

     <tr>
       <th align="left"> Totals </th>
       <th align="right"> <?= $totRuns ?> </th>
       <th align="right"> <?= $totErrs ?> </th>
       <th align="right"> <?= $totFails ?> </th>
       <td align="left"> 
     	<?php 
          if ($totRuns == 0) {
	    echo '(No Runs)';
	  }
	  else {
	    $per = 100/$totRuns;
	    $notOkWidth = ($per * $totErrs) + ($per * $totFails);
	    $okWidth = 100 - $notOkWidth ;    
	    echo graph($okWidth, $notOkWidth); 
	  }
	?> 
       </td>
     </tr>

</table>

    <h3>Failures</h3>
        <form>
	<textarea wrap="physical" cols="80" rows="10"><?php printStrs(@$failstrs); ?></textarea>
        </form>        

    <h3>Errors</h3>
        <form>
            <textarea wrap="physical" cols="80" rows="10"><?php printStrs(@$errstrs); ?></textarea>
        </form>        
</body>
</html>


