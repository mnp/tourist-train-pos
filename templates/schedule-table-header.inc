<!-- Special table template just for edit schedule form -->

<table border="1" width="100%" cellpadding="0" cellspacing="0">

<tr>
<td class="header" align="center" rowspan="2">Date</td>
<td class="header" align="center" colspan="<?= count($runids) ?>">Trains</td>
<td class="header" align="center" colspan="2">Per-Day</td>
</tr>
<tr>

<?php 

foreach ($titles as $t) { 
?>
    <td align="center" class="header"><?= $t ?></td>
<?php 
}
?>

</tr>
