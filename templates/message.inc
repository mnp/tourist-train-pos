<table cellpadding="3" border="0" align="center" width="70%">
  <tr>
    <td class="header" align="left" >
	<?php
	switch($level) 
	{
	  case ERROR_LEVEL: 
	      echo 'Error'; 
	      $class = 'error';
	      break;
	  case WARNING_LEVEL: 
	      echo 'Warning'; 
	      $class = 'warning';
	      break;
	  case MESSAGE_LEVEL:
	      echo 'Message';
	      $class = 'message';
	      break;
	}
	?>
    </td>
    <td class="<?= $class ?>" align="left">
	<?= $message ?>
    </td>
  </tr>
  
<?php
if (@$append) 
{
?>
  
  <tr>
    <td colspan="2" class="message" align="left">
	<?= $append ?>
    </td>
  </tr>


<?php
}
?>

</table>
<br>