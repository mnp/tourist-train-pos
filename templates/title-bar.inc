<table border="0" align="center" width="100%" cellpadding="0" cellspacing="0">
<tr><td class="header" align="center">

<table border="0" width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td align="left" width="80%" class="header">
        <b><?= $ltitle ?></b>
    </td>

	<?php
	if (isset($rcode)) {
	  echo '<td align="right" width="70%">';
	  eval($rcode);
	  echo '<td>';
	}
	else {
	  echo '<td align="right" width="70%">';
	  echo "<b>{$rtitle}</b>";
	  echo '<td>';
	}
	?>

</tr>
</table>
</td></tr>
</table>

<p>