<?php

/* Load a help file - this runs in popup. */

require_once 'baseDefs.php';

$topic = $_GET['topic'];
$fname = preg_replace('/ /', '_', $topic);
$text = file_get_contents("../helpdocs/{$fname}.html");
include "../templates/help.html";

?>