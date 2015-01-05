<?  // Database Consistency Check

require_once '../lib/base.php';
require_once 'TTrain.php';
require_once 'TReservation.php';
require_once 'TCustomer.php';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Database Consistency Check</title>
<body>
<h2>Database Consistency Check</h2>
<p>This WILL take a while. Timeout set to 2 minutes.</p><dl>
<?

$startmem  = memory_get_usage();
$starttime = time();
set_time_limit(4 * 60 * 60);

// These three fetchAll's are the only DB accesses, the
// rest is tons of memory
$t = new DataObjects_TTrain;
$ts =& $t->fetchAllDataObjects();
$numtrains = count($ts);

$r = new DataObjects_TReservation;
$rs =& $r->fetchAllDataObjects();
$numres = count($rs);

$c = new DataObjects_TCustomer;
$cs =& $c->fetchAllDataObjects();
$numcust = count($c);

$afterloadtime = time();

// make sure each train with reserved seats has exactly that number 
// coming from the reservations
$fails = 0;
$badTrains = '';

foreach ($ts as $tid=>$t) 
{
  $trainres = $t->getTotalReservations(); // reserved seats on train
  $resriders = 0;			  // seats from ALL reservations
  $foundres = '';
  $shouldA = 0;
  $shouldC = 0;
  $shouldL = 0;
  $shouldE = 0;
  $shouldS = 0;

  foreach ($rs as $rid=>$r) 
  {
    if ($r->status == RES_RELEASED || $r->status == RES_CHECKEDIN) {
      continue;
    }
    
    if ($r->tTrain_id_1 == $tid) {
      $resriders += $r->totalRiders();
      $acles = $r->adults . ',' . $r->children . ',' . $r->laps  . ',' 
	. $r->escorts . ',' .  $r->specials;
      $foundres .= "{$rid}($acles) ";
      $shouldA += $r->adults;
      $shouldC += $r->children;
      $shouldL += $r->laps;      
      $shouldE += $r->escorts;      
      $shouldS += $r->specials;
    }
    else if ($r->tTrain_id_2 == $t->trainId) {
      $resriders += $r->totalRiders();
      $acles = $r->adults . ',' . $r->children . ',' . $r->laps  . ',' 
	. $r->escorts . ',' .  $r->specials;
      $foundres .= "{$rid}($acles) ";
      $shouldA += $r->adults;
      $shouldC += $r->children;
      $shouldL += $r->laps;      
      $shouldE += $r->escorts;      
      $shouldS += $r->specials;
    }
  }

  if ($trainres > $t->seats) {
    $badTrains .= 
      " <dd> train $tid has $trainres riders but only {$t->seats} seats</dd>";
    $fails++;
  }
  
  if ($resriders != $trainres) {
    $acles = $t->aRes . ',' . $t->cRes . ',' . $t->lRes  . ',' . $t->eRes 
	 . ',' .  $t->sRes;
    $badTrains .= 
      " <dd> train $tid has $trainres reserved ($acles), but $resriders found from reservations: $foundres</br>";
    $fails++;
    
    $shouldset = "aRes=$shouldA, cRes=$shouldC, lRes=$shouldL, eRes=$shouldE, sRes=$shouldS";   
    $badTrains .= " <dd> recommend UPDATE tTrain SET $shouldset WHERE trainId=$tid</dd>";
  }
}
echo "<dt>1. Trains should match reservations: $fails failures</dt>";
echo "<dd>$badTrains</dd>";

// inverse check.  Make sure each res has at least its riders on the train
$fails = 0;
$rsvnsMatchTrains = '';

$rsvnsWithNoRes = '';
$nonzeroRates = array();
$custsWithNoRes = array();
$calcDueFailures = array();
$calcDetails = '';
$fails = 0;

foreach ($rs as $rid=>$r) 
{
  check2($r, $rid);
  check3($r, $rid);
}

echo "<dt>2. Reservations should match trains: $ck2_fails failures</dt>";
echo "<dt>3. There should be a customer for every reservation with a cust id: $ck3_fails failures</dt>";
echo $rsvnsWithNoRes;

foreach ($cs as $cid=>$c) {
  if ($c->a1Rate + $c->a2Rate + $c->c1Rate + $c->c2Rate < 1) {
    $nonzeroRates[] = $cid;
  }

  $found = 0;
  foreach ($rs as $rid=>$r) {
    if (isset($r->tCustomer_id) && $r->tCustomer_id==$cid) {
      $found = 1;
      break;
    }
  }
  if (!$found) {
    $custsWithNoRes[] = $cid;
  }
}

echo "<dt>4. Sum of rates in customers should be nonzero: " . count($nonzeroRates) 
     . " failures</dt>";
if (count($nonzeroRates) > 0) {
  echo '<dd> custIds: ' . join(' ', $nonzeroRates) . '</dd>';
}


echo "<dt>5. Okay. </dt>";


echo "<dt>6. Reservation amountDue should match cust rate and riders: " .
    count($calcDueFailures) . " failures</dt>";
if (count($calcDueFailures) > 0) {
  echo '<dd> resIds: ' . join(' ', $calcDueFailures) . '</dd>';
  echo "<dd>details: $calcDetails</dd>";
}

echo "<dt>7. There should usually be a res for every cust: " .
    count($custsWithNoRes) . " failures</dt>";
if (count($custsWithNoRes) > 0) {
  echo '<dd> custIds: ' . join(' ', $custsWithNoRes) . '</dd>';
}



$endtime = time();
$endmem  = memory_get_usage();

?>
</dl>
<br>
customers: <?= $numcust ?> <br>
trains: <?= $numtrains ?> <br>
reservations: <?= $numres ?> <br>
loading took <?= $afterloadtime - $starttime ?>s<br>
examination took  <?= $endtime - $afterloadtime ?>s<br>
everything took <?= $endtime - $starttime ?>s<br>
memory used <? $endmem - $startmem ?> bytes<br>

</body></html>

<?  // --------------------------------------------------------------------

function check2(&$r, $rid) 
{
  global $ck2_fails;
  global $rsvnsMatchTrains;
  global $ts;

  $resriders = $r->totalRiders();
  $trainres = 0;
  $foundtrains = '';
  
  $tid1 = $r->tTrain_id_1;
  $tid2 = $r->tTrain_id_2;

  if (!$tid1) {
    $rsvnsMatchTrains .= "<dd>res $rid has no train1 set</dd>";
    $ck2_fails++;
  }
  else if (!array_key_exists($tid1, $ts)) {
    $rsvnsMatchTrains .= 
    "<dd>res $rid has train1 as $tid1 but it does not exist</dd>";
    $ck2_fails++;
  }
  else {
    $t = $ts[$tid1];
    $trainres .= $t->getTotalReservations();
    $foundtrains .= "$tid1 ";
  }
  
  if ($tid2) {
    if (!array_key_exists($tid2, $ts)) {
      $rsvnsMatchTrains .= 
      "<dd>res $rid has train2 as $tid2 but it does not exist</dd>";
      $ck2_fails++;
    }
    else {
      $t = $ts[$tid2];
      $trainres .= $t->getTotalReservations();
      $foundtrains .= "$tid2 ";
    }
  }
  
  if ($resriders > $trainres) {
    $rsvnsMatchTrains .= 
    "<dd>res $rid has $resriders but its trains ($foundtrains) have only $trainres reserved seats</dd>";
  }
}

function check3(&$r, $rid) 
{
  global $ck3_fails;
  global $calcDueFailures;
  global $calcDetails;

  if ($r->tCustomer_id) {
    if (!array_key_exists($r->tCustomer_id, $cs)) {
      $rsvnsWithNoRes .= " * res $rid has tCustomer_id as {$r->tCustomer_id} but it does not exist";
      $ck3_fails++;
    }
    else if (($r->status == RES_RESERVED || $r->status == RES_RELEASED)
	     && $r->amountDue != $r->computeDue()) {
	$calcDueFailures[] = $rid;
	$calcDetails .= 
	  "<dd>res $rid: amountDue={$r->amountDue} but computed="
	  . $r->computeDue() . "</dd>";
    }
  }
}


?>