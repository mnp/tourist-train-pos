<?  // Reservations Over Time, by group and individual

require_once '../lib/base.php';
require_once 'TTrain.php';
require_once 'TReservation.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Reservations</title>
<body>

<a href="#" onClick="return window.close();" class="button"> Close </a>

<div class="th" align="center">
<font size=+2>Reservations by Month</font>
</div>

<?

for ($month=5; $month<11; $month++) 
{
  $res = new DataObjects_TReservation();
  $trn = new DataObjects_TTrain();
  
select resId from tReservation,tCustomer where tCustomer_id=custId and tCustType_id > 1;

  // by group+spec, ind, wupgrp, wupind

  $trn->date = $date;
  $res->joinAdd($trn);
  $res->find();
  //$rs = $res->fetchDataObjects();	// array of found rsvns

$resview = new CheckinSelectionView('--no-form--', $res, FALSE);
$resview->makeTable(false);
echo $resview->toHtml();

?>
</body></html>