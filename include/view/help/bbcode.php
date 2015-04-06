<div class="main-subhead">
	<h2 class="hn"><span><?php printf($lang_help['Help with'], __('BBCode')) ?></span></h2>
</div>
<div class="main-content main-frm">
	<div class="ct-box info-box">
		<p><?php echo $lang_help['BBCode info'] ?></p>
	</div>
	<div class="ct-box help-box">
		<h3 class="hn"><span><?php echo $lang_help['Text style'] ?></span></h3>
		<div class="entry-content">
			<code>[b]<?php echo $lang_help['Bold text'] ?>[/b]</code> <span><?php echo $lang_help['produces'] ?></span>
			<samp><strong><?php echo $lang_help['Bold text'] ?></strong></samp>
		</div>
		<div class="entry-content">
			<code>[u]<?php echo $lang_help['Underlined text'] ?>[/u]</code> <span><?php echo $lang_help['produces'] ?></span>
			<samp><span class="bbu"><?php echo $lang_help['Underlined text'] ?></span></samp>
		</div>
		<div class="entry-content">
			<code>[i]<?php echo $lang_help['Italic text'] ?>[/i]</code> <span><?php echo $lang_help['produces'] ?></span>
			<samp><i><?php echo $lang_help['Italic text'] ?></i></samp>
		</div>
		<div class="entry-content">
			<code>[color=#FF0000]<?php echo $lang_help['Red text'] ?>[/color]</code> <span><?php echo $lang_help['produces'] ?></span>
			<samp><span style="color: #ff0000"><?php echo $lang_help['Red text'] ?></span></samp>
		</div>
		<div class="entry-content">
			<code>[color=blue]<?php echo $lang_help['Blue text'] ?>[/color]</code> <span><?php echo $lang_help['produces'] ?></span>
			<samp><span style="color: blue"><?php echo $lang_help['Blue text'] ?></span></samp>
		</div>
		<div class="entry-content">
			<code>[b][u]<?php echo $lang_help['Bold, underlined text'] ?>[/u][/b]</code> <span><?php echo $lang_help['produces'] ?></span>
			<samp><span class="bbu"><strong><?php echo $lang_help['Bold, underlined text'] ?></strong></span></samp>
		</div>
		<div class="entry-content">
			<code>[h]<?php echo $lang_help['Heading text'] ?>[/h]</code> <span><?php echo $lang_help['produces'] ?></span>
			<div class="entry-content"><h5><samp><?php echo $lang_help['Heading text'] ?></samp></h5></div>
		</div>
<?php ($hook = get_hook('he_new_bbcode_text_style')) ? eval($hook) : null; ?>
	</div>
	<div class="ct-box help-box">
		<h3 class="hn"><span><?php echo $lang_help['Links info'] ?></span></h3>
		<div class="entry-content">
			<code>[url=<?php echo $base_url.'/' ?>]<?php echo forum_htmlencode($forum_config['o_board_title']) ?>[/url]</code> <span><?php echo $lang_help['produces'] ?></span>
			<samp><a href="<?php echo $base_url.'/' ?>"><?php echo forum_htmlencode($forum_config['o_board_title']) ?></a></samp>
		</div>
		<div class="entry-content">
			<code>[url]<?php echo $base_url.'/' ?>[/url]</code> <span><?php echo $lang_help['produces'] ?></span>
			<samp><a href="<?php echo $base_url ?>"><?php echo $base_url.'/' ?></a></samp>
		</div>
		<div class="entry-content">
			<code>[email]name@example.com[/email]</code> <span><?php echo $lang_help['produces'] ?></span>
			<samp><a href="mailto:name@example.com">name@example.com</a></samp>
		</div>
		<div class="entry-content">
			<code>[email=name@example.com]<?php echo $lang_help['My e-mail address'] ?>[/email]</code> <span><?php echo $lang_help['produces'] ?></span>
			<samp><a href="mailto:name@example.com"><?php echo $lang_help['My e-mail address'] ?></a></samp>
		</div>
<?php ($hook = get_hook('he_new_bbcode_link')) ? eval($hook) : null; ?>
	</div>
	<div class="ct-box help-box">
		<h3 class="hn"><span><?php echo $lang_help['Quotes info'] ?></span></h3>
		<div class="entry-content">
			<code>[quote=James]<?php echo $lang_help['Quote text'] ?>[/quote]</code> <span><?php echo $lang_help['produces named'] ?></span>
			<div class="quotebox"><cite>James <?= __('wrote') ?>:</cite><blockquote><p><?php echo $lang_help['Quote text'] ?></p></blockquote></div>
			<code>[quote]<?php echo $lang_help['Quote text'] ?>[/quote]</code> <span><?php echo $lang_help['produces unnamed'] ?></span>
			<div class="quotebox"><blockquote><p><?php echo $lang_help['Quote text'] ?></p></blockquote></div>
		</div>
	</div>
	<div class="ct-box help-box">
		<h3 class="hn"><span><?php echo $lang_help['Code info'] ?></span></h3>
		<div class="entry-content">
			<code>[code]<?php echo $lang_help['Code text'] ?>[/code]</code> <span><?php echo $lang_help['produces code box'] ?></span>
			<div class="codebox"><pre><code><?php echo $lang_help['Code text'] ?></code></pre></div>
			<code>[code]<?php echo $lang_help['Code text long'] ?>[/code]</code> <span><?php echo $lang_help['produces scroll box'] ?></span>
			<div class="codebox"><pre><code><?php echo $lang_help['Code text long'] ?></code></pre></div>
		</div>
	</div>
	<div class="ct-box help-box">
		<h3 class="hn"><span><?php echo $lang_help['List info'] ?></span></h3>
		<div class="entry-content">
			<code>[list][*]<?php echo $lang_help['List text 1'] ?>[/*][*]<?php echo $lang_help['List text 2'] ?>[/*][*]<?php echo $lang_help['List text 3'] ?>[/*][/list]</code> <span><?php echo $lang_help['produces list'] ?></span>
			<ul><li><?php echo $lang_help['List text 1'] ?></li><li><?php echo $lang_help['List text 2'] ?></li><li><?php echo $lang_help['List text 3'] ?></li></ul>
			<code>[list=1][*]<?php echo $lang_help['List text 1'] ?>[/*][*]<?php echo $lang_help['List text 2'] ?>[/*][*]<?php echo $lang_help['List text 3'] ?>[/*][/list]</code> <span><?php echo $lang_help['produces decimal list'] ?></span>
			<ol class="decimal"><li><?php echo $lang_help['List text 1'] ?></li><li><?php echo $lang_help['List text 2'] ?></li><li><?php echo $lang_help['List text 3'] ?></li></ol>
			<code>[list=a][*]<?php echo $lang_help['List text 1'] ?>[/*][*]<?php echo $lang_help['List text 2'] ?>[/*][*]<?php echo $lang_help['List text 3'] ?>[/*][/list]</code> <span><?php echo $lang_help['produces alpha list'] ?></span>
			<ol class="alpha"><li><?php echo $lang_help['List text 1'] ?></li><li><?php echo $lang_help['List text 2'] ?></li><li><?php echo $lang_help['List text 3'] ?></li></ol>
		</div>
	</div>
<?php ($hook = get_hook('he_new_bbcode_section')) ? eval($hook) : null; ?>
</div>