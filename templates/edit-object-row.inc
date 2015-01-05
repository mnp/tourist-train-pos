    <tr>
      <td class="light" align="right"><b><?= $nicename ?></b></td>
      <td> <?= $input ?> </td>
      <td width="15">
        <?php 
	  echo ($conf['user']['online_help'] && 
	        $browser->hasFeature('javascript')) 
		    ? Help::link('passwd', 'passwd-old-password') 
		    : '&nbsp;' 
        ?>
      </td>
    </tr>
