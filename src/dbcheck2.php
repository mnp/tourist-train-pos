<?  // Database Consistency Check

require_once '../lib/base.php';

require_once 'DB.php';
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
<p>This new, improved version performs direct queries, without loading everything into memory.</P>
This WILL take a while. Timeout set to 2 minutes.</p><dl>
<?

if (DB::isError($db = DB::connect(DSN))) {
  die ($db->getMessage());
}
$db->setFetchMode(DB_FETCHMODE_ORDERED);


report('overbooked trains', 
       'SELECT trainId from tTrain where (aRes+cRes+lRes+eRes+sRes) = seats');

report('all outbound trains for which the number of reserved seats does not match the number of seats in corresponding reservations',
'select tTrain_id_1, sum(adults)+sum(children)+sum(laps)+sum(escorts)+sum(specials) as fromres, (aRes+cRes+lRes+eRes+sRes) as fromtrain from tReservation,tTrain where  trainId=tTrain_id_1 group by tTrain_id_1 having fromres <> fromtrain');

report('all return trains for which the number of reserved seats does not match the number of seats in corresponding reservations',
'select tTrain_id_1, sum(adults)+sum(children)+sum(laps)+sum(escorts)+sum(specials) as fromres, (aRes+cRes+lRes+eRes+sRes) as fromtrain from tReservation,tTrain where  trainId=tTrain_id_1 group by tTrain_id_1 having fromres <> fromtrain');

?>

</body></html>

<?  // --------------------------------------------------------------------

//
// Each row returned by the query should consist of one column: the ID of 
// the offending thing.
//

function report ($description, $sql) 
{
  global $db;
  
  echo "$description: ";  
  $result =& $db->getAll($sql);
  if (DB::isError($result)) {
    die ($result->getMessage());
  }
  $n = count($result); 
  if ($n == 0) {
    echo "PASS</br>";
  }
  else {
    echo " $n</br><blockquote>" 
      . join(' ', array_map('end', $result)) 
      . "</blockquote>";
  }
}

?>
