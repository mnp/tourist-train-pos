<?
$file = '';
$fp = fopen($file, 'w');  ... 
fputs($fp, "* * * * * ....");
fclose($fp);

$cmd = "crontab $file";
?>