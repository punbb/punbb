<?php
namespace punbb;

global $forum_page;

if (substr(FORUM_PAGE, 0, 5) == 'admin' && FORUM_PAGE_TYPE != 'paged') {
	$forum_page['admin_sub'] = generate_admin_menu(true);
?>
	<div class="admin-menu gen-content">
		<ul>
			<?= generate_admin_menu(false) ?>
		</ul>
	</div>
	<?php if ($forum_page['admin_sub'] != '') { ?>
		<div class="admin-submenu gen-content">
			<ul>
			  <?= $forum_page['admin_sub'] ?>
			 </ul>
		</div>
	<?php } ?>
<?php } ?>
