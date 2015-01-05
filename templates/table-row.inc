<?
if (@$rowclass == 'hotitem') 
{
  echo '<tr bgcolor="yellow">';
}
elseif (isset($rowclass))
{
  echo "<tr class=\"$rowclass\">";
}
else 
{
  echo '<tr class="item0">';
}

foreach ($items as $i) {
  echo "<td align=\"center\"><nobr>{$i}</nobr></td>";
}
?>

</tr>