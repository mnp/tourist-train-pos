<?php

error_reporting(E_ALL);

$uname=posix_uname(); 
if (!strcmp($uname['nodename'], 'aspen') || !strcmp($uname['nodename'], 'oak')) 
{
  define('ROOTPATH', dirname(dirname(__FILE__)));
  define('ROOTURL',  'http://localhost/~mitch/Touristtrain');
  define('DB_OBJECTS_CONFIG', ROOTPATH . '/DataObjects/config.aspen.ini');
  define('DSN', 'mysql://uTourist:TouristMysqlUser@localhost/Touristmain');
}
else 
{
  define('ROOTPATH', dirname(dirname(__FILE__)));
  define('ROOTURL',  'https://www.secure.com/Touristtrain');
  define('DB_OBJECTS_CONFIG', ROOTPATH 
	 . '/DataObjects/config.secure.ini'); 
  define('DSN', 'mysql://suTourist:TouristMysqlAdmin@localhost/Touristmain');
}

ini_set('include_path', ROOTPATH . ":"
	. ROOTPATH . '/src:'
	. ROOTPATH . '/lib:'
	. ROOTPATH . '/DataObjects:'
	. ROOTPATH . '/pear');

ini_set('register_globals', 'Off');

define('ADMIN_TEMPLATES', ROOTPATH . '/templates');
define('SRC', ROOTPATH . '/src');
define('GRAPHICS', dirname(dirname($_SERVER['PHP_SELF'])) . '/graphics');
define('ADMIN_BASE', ROOTPATH);
define('WRITABLE', ROOTPATH . '/writable');
define('MOTDFILE', WRITABLE . '/motd.html');
define('STYLE', ROOTURL . '/styles/style.css');
define('MAXINT', 0xffffffff);

define ('YELLOWLEVEL', 20);
define ('REDLEVEL', 5);

/** @define MANYSTOP int Max number of results to show */
define('MANYSTOP', 80);

setlocale(LC_MONETARY, 'en_US');

// 
// file_get_contents not available for < php 4.3
// 
if (!function_exists('file_get_contents')) 
{
  $fun = 'function file_get_contents($f) {
    ob_start();
    $retval = @readfile($f);
    if (false !== $retval) { // no readfile error
      $retval = ob_get_contents();
    }
    ob_end_clean();
    return $retval;
  }';
  eval($fun);
}

?>