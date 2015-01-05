<?php

/* Display help contents */

require_once 'baseDefs.php';

$text = '';
$files = array();

$dirp = dir('../helpdocs');
while (false !== ($f = $dirp->read())) {
  if (!preg_match('/^\./', $f) && !preg_match('/~$/', $f)) {
    $files[] = $f;
  }
}
$dirp->close();

sort($files);

foreach($files as $f) {
  $topic = basename($f,'.html');
  $topic = preg_replace('/_/', ' ', $topic);
  $text .= '<a class="light" href="HelpLoader.php?topic=' . $topic . '">' 
    . '&gt;&gt;&nbsp; ' . $topic 
    . '</a><br>';
}

$topic = 'Help Contents';
include "../templates/help.html";
?>