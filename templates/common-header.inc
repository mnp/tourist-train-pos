<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"DTD/xhtml1-transitional.dtd">
<html>
<head>

<title><?= $page_title ?></title>
<link href="../styles/style.css" rel="stylesheet" type="text/css" />

<body 
  <? if (isset($onLoadAlert)) { echo "onLoad=\"alert('$onLoadAlert');\""; } ?>
>

<?php

if (!isset($no_menu)) {
  include '../templates/menu.inc';
}

// this depends on base being loaded first
if ($session_data['groupName'] == 'dev') {
  MNP::dp($_POST, "_POST");
  //phpinfo(32);
}

// Now check permissions for this page
if (!Permissions::hasPermission($page_activity)) {
  MNP::error("You don't have permission to see this page", true);
}

include_once 'Popup.js';

?>

<script language="Javascript" src="../popupcalendar/calendar.js"></script>
