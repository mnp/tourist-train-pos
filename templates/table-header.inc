<table border="1" width="<?= isset($width) ? $width : "100%" ?>" 
 cellpadding="3" cellspacing="0" >
<tr>

<?php 
if (isset($table_title)) {
  $n = count($titles);
?>
  <td colspan="<?= $n ?>" class="header">
      <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
      <td align="center" class="header"> <?= $table_title ?></td>
      <td align="center" width="20px" class="item"><?= $help_link ?></td>
      </tr>
      </table>

  </td>

  </tr>
  <tr>
<?php
}
foreach ($titles as $t) { 
?>
    <td align="center" class="header">
       <nobr>
       <?= $t ?>
       </nobr>
    </td>
<?php 
}
?>

</tr>
