<?
// for all reservations, set amountDue to computed value and save
require_once '../lib/base.php';
require_once 'TReservation.php';
require_once 'TCustomer.php';
?>

<html>
<head>
<body>

<?
set_time_limit(2 * 60);

$res = new DataObjects_TReservation();
$nf = $res->find();
echo "$nf = ";
$okay=0;
$fail=0;

while ($res->fetch()) 
{
  $tmp = $res;
  $tmp->getLinks();  
  $err = $tmp->changeRes();
  if ($err == true) {
    $okay++;
  }
  else {
    $fail++;
    MNP::dp($err);
  }
}
echo "$okay okay + $fail fail<br>";
echo "done";

?></body></html>