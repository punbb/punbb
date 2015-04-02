<div class="main-subhead">
	<h2 class="hn"><span><?php printf($lang_help['Help with'], $lang_common['Images']) ?></span></h2>
</div>
<div class="main-content main-frm">
	<div class="ct-box help-box">
		<p class="hn"><?php echo $lang_help['Image info'] ?></p>
		<div class="entry-content">
			<code>[img=PunBB bbcode test]<?php echo $base_url ?>/img/test.png[/img]</code>
			<samp><img src="<?php echo $base_url ?>/img/test.png" alt="PunBB bbcode test" /></samp>
		</div>
	</div>
	<?php ($hook = get_hook('he_new_img_section')) ? eval($hook) : null; ?>
</div>