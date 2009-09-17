<?php

/**
 * pun_broadcast_email help page
 *
 * @copyright (C) 2009 PunBB
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package pun_broadcast_email
 */

require FORUM_ROOT.'header.php';

// START SUBST - <!-- forum_main -->
ob_start();

?>
<div class="main-subhead">
	<h2 class="hn">
		<span><?php echo $lang_pun_broadcast_email['Ext help'] ?></span>
	</h2>
</div>
<div class="main-content main-forum">
	<div class="ct-box help-box">
		<h3 class="hn">
			<span><?php echo $lang_pun_broadcast_email['Ext help header'] ?></span>
		</h3>
		<?php foreach ($forum_page['help_vars'] as $var_name => $var_info) { ?> 
		<div class="entry-content">
			<code><?php echo $var_name; ?></code>
			<span><?php echo $var_info['description']; ?></span>
			<samp>
				<strong><?php echo $var_info['example']; ?></strong>
			</samp>
		</div>
		<?php } ?>
	</div>
</div>
<?php

$tpl_temp = forum_trim(ob_get_contents());
$tpl_main = str_replace('<!-- forum_main -->', $tpl_temp, $tpl_main);
ob_end_clean();
// END SUBST - <!-- forum_main -->

require FORUM_ROOT.'footer.php';

?>