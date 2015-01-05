<?php

require_once 'PEAR.php';
require_once 'DB.php';     
require_once 'DB/mysql.php';
require_once 'Auth/Auth.php';
require_once 'TUser.php';
require_once 'TAuthGroup.php';
require_once 'TStation.php';

function loginFunction ()
{
  global $xauth;

  $s = $xauth->getStatus();
  switch ($s) {
  case AUTH_EXPIRED: 
    $msg = 'Expired, please log in again.'; 
    break;
  case AUTH_IDLED:   
    $msg = 'Idled, please log in again.'; 
    break;
  case AUTH_WRONG_LOGIN:
    $msg = 'Incorrect login, please try again.';
    break;
  default:
  }

  include "templates/login.html";
  exit;
}

/**
 * makeSessionData 
 *
 * @static
 * @return array of session data
 */
function makeSessionData($username)
{
  $data = array();
  $user = new DataObjects_TUser;
  $user->user_uid = $username;
  $found = $user->find(true);
  assert($found > 0);
  $data['userId'] = $user->id;
  $data['userName'] = $username;

  $group = DataObjects_TAuthGroup::staticGet($user->tAuthGroup_id);
  assert($group);
  $data['groupId'] = $group->id;
  $data['groupName'] = $group->gname;

  return $data;
}

/**
 * Login callback, called by PEAR Auth. Stores user, group stuff in session.
 *
 * @access public
 * @param string $username
 */
function loginCallback($username)
{
  global $xauth;
  $xauth->setIdle(60); 
  $locId = $_POST['stationSelect'];

  $data = makeSessionData($username); 
  $data['userLocationId'] = $locId;
  $station = DataObjects_TStation::staticGet($locId);
  assert($station);
  $data['userLocationName'] = $station->name;

  $xauth->setAuthData($data);
}

// Initialization of database related information
$params = array(            
  "dsn" 	=> DSN,
  "table" 	=> "tUser",            
  "usernamecol" => "user_uid",   
  "passwordcol" => "user_pass"
);    
    
//
// Password is not checked in this case.
//
if (defined("PUBLIC_INTERNET")) {
  $session_data = makeSessionData('internet');
  return;
}

$xauth = new Auth("DB", $params, "loginFunction");
$xauth->setLoginCallback('loginCallback');
$xauth->start();

if (isset($_GET['actionName']) && $_GET['actionName'] == 'logout') {
    $xauth->logout();
    $xauth->start();
}

if (!$xauth->getAuth()) {
  exit;
}

//
// FIXME: this gets done every page submission.  Should it?
//

$xauth->setIdle(3600);          // 1 hour

// Retreive session data
$session_data = $xauth->getAuthData('data');

?>
