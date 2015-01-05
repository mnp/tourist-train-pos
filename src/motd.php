<?php

// Display Message of the Day
// $Id:$
// $Source:$

require_once '../lib/base.php';
require_once 'TMessage.php';

$page_activity = 'MOTD';
$page_title = 'Message of the Day';
include ADMIN_TEMPLATES . '/common-header.inc';
require_once ROOTPATH . "/version.php";

$mo = DataObjects_TMessage::staticGet('motd');
$str = wordwrap($mo->message, 95);

include ADMIN_TEMPLATES . '/motd.html';
include ADMIN_TEMPLATES . '/common-footer.inc';
?>