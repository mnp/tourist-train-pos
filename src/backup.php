<?  // manually initiate database backup

require_once '../lib/base.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Manual Backup</title>
<body>

 <a href="#" onClick="return window.close();" class="button"> Close </a>
 <br>
 <br>
Manual backup beginning.<br><pre>

<?
$status = 0;
$bufile='/usr/local/www/data/httpd/htdocs/Touristtrain/backups/dump.gz';
$cmd = "mysqldump --extended-insert --user=uTourist --password=TouristMysqlUser Touristmain > $bufile 2>&1";

echo system($cmd, $status);
echo "</pre>Done with status $status.<br>";

?>
</body></html>