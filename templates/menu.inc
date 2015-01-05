<table border="0" cellpadding="2" cellspacing="0" width="100%">
<tr>
    <td class="menu">

<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
<?php

function printItem($link, $title, $image, $linkExtras='')
{
  // Check permissions, don't even display the icon if not.
  if (!Permissions::hasPermission($title)) {
    return;
  }

  $image = GRAPHICS . '/' . $image;
  
  echo <<<EOX
<td align="center" valign="bottom" nowrap="nowrap">
<a href="{$link}" onmouseout="window.status='';" 
  onmouseover="window.status='{$title}'; return true;" 
  $linkExtras
  class="menuitem">
<img src="{$image}" border="0" alt="{$title}" title="{$title}" />
<br />{$title}</a>&nbsp;</td>
EOX;
}

/* menu items */
printItem('checkin.php', 'Checkin', 'checkmark.png');
printItem('reservation.php', 'Resvn', 'ticket.png');
printItem('customer.php', 'Cust', 'head.png');
printItem('daily.php', 'Daily', 'dollar.png');
printItem('schedule.php', 'Sched', 'schedule.png');
printItem('season.php', 'Season', 'season.png');
printItem('reports.php', 'Reports', 'paper.png');
printItem('maintain.php', 'Maint', 'cdrom.png');
printItem(ROOTURL . '/lib/HelpContents.php', 'Help', 'book.png',
	  "onClick=\"return popup(this, '-')\"");

?>


<td>
  &nbsp;&nbsp;  &nbsp;&nbsp;
</td>

<td>
    <table border="0" cellpadding="2" cellspacing="0">
	<tr> 
		<td class="menu">Next Departure:</td> 
		<td class="smallheader"><?= $nextDepartureTime ?></td> 
		<td> &nbsp; </td> 
		<td class="menu">Currently:</td> 
		<td class="smallheader"><?= $timeNow ?></td> 
	</tr>
	<tr> 
		<td class="menu">Rsvns Release:</td> 
		<td class="smallheader"><?= $releaseTime ?></td> 
		<td> &nbsp; </td> 
		<td class="menu">Agent:</td> 
		<td class="smallheader">
		  <?= $session_data['userName'] . ', ' . 
			$session_data['userLocationName'] ?>
		</td> 
	</tr>
    </table>
</td>

<td>
    <a href="motd.php?actionName=logout" class="button">Log Out</a>
</td>

</tr></table></td></tr></table>

<br />
