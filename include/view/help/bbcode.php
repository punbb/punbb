<?php
namespace punbb;
?>

<div class="main-subhead">
	<h2 class="hn"><span><?php printf(__('Help with', 'help'), __('BBCode')) ?></span></h2>
</div>
<div class="main-content main-frm">
	<div class="ct-box info-box">
		<p><?= __('BBCode info', 'help') ?></p>
	</div>
	<div class="ct-box help-box">
		<h3 class="hn"><span><?= __('Text style', 'help') ?></span></h3>
		<div class="entry-content">
			<code>[b]<?= __('Bold text', 'help') ?>[/b]</code> <span><?= __('produces', 'help') ?></span>
			<samp><strong><?= __('Bold text', 'help') ?></strong></samp>
		</div>
		<div class="entry-content">
			<code>[u]<?= __('Underlined text', 'help') ?>[/u]</code>
			<span><?= __('produces', 'help') ?></span>
			<samp><span class="bbu"><?= __('Underlined text', 'help') ?></span></samp>
		</div>
		<div class="entry-content">
			<code>[i]<?= __('Italic text', 'help') ?>[/i]</code>
			<span><?= __('produces', 'help') ?></span>
			<samp><i><?= __('Italic text', 'help') ?></i></samp>
		</div>
		<div class="entry-content">
			<code>[color=#FF0000]<?= __('Red text', 'help') ?>[/color]</code>
			<span><?= __('produces', 'help') ?></span>
			<samp><span style="color: #ff0000"><?= __('Red text', 'help') ?></span></samp>
		</div>
		<div class="entry-content">
			<code>[color=blue]<?= __('Blue text', 'help') ?>[/color]</code>
			<span><?= __('produces', 'help') ?></span>
			<samp><span style="color: blue"><?= __('Blue text', 'help') ?></span></samp>
		</div>
		<div class="entry-content">
			<code>[b][u]<?= __('Bold, underlined text', 'help') ?>[/u][/b]</code>
			<span><?= __('produces', 'help') ?></span>
			<samp><span class="bbu"><strong><?= __('Bold, underlined text', 'help') ?></strong></span></samp>
		</div>
		<div class="entry-content">
			<code>[h]<?= __('Heading text', 'help') ?>[/h]</code>
			<span><?= __('produces', 'help') ?></span>
			<div class="entry-content"><h5><samp><?= __('Heading text', 'help') ?></samp></h5></div>
		</div>
<?php ($hook = get_hook('he_new_bbcode_text_style')) ? eval($hook) : null; ?>
	</div>
	<div class="ct-box help-box">
		<h3 class="hn"><span><?= __('Links info', 'help') ?></span></h3>
		<div class="entry-content">
			<code>[url=<?php echo $base_url.'/' ?>]<?php echo forum_htmlencode(config()['o_board_title']) ?>[/url]</code>
			<span><?= __('produces', 'help') ?></span>
			<samp><a href="<?php echo $base_url.'/' ?>"><?php echo forum_htmlencode(config()['o_board_title']) ?></a></samp>
		</div>
		<div class="entry-content">
			<code>[url]<?php echo $base_url.'/' ?>[/url]</code>
			<span><?= __('produces', 'help') ?></span>
			<samp><a href="<?php echo $base_url ?>"><?php echo $base_url.'/' ?></a></samp>
		</div>
		<div class="entry-content">
			<code>[email]name@example.com[/email]</code>
			<span><?= __('produces', 'help') ?></span>
			<samp><a href="mailto:name@example.com">name@example.com</a></samp>
		</div>
		<div class="entry-content">
			<code>[email=name@example.com]<?= __('My e-mail address', 'help') ?>[/email]</code>
			<span><?= __('produces', 'help') ?></span>
			<samp><a href="mailto:name@example.com"><?= __('My e-mail address', 'help') ?></a></samp>
		</div>
<?php ($hook = get_hook('he_new_bbcode_link')) ? eval($hook) : null; ?>
	</div>
	<div class="ct-box help-box">
		<h3 class="hn"><span><?= __('Quotes info', 'help') ?></span></h3>
		<div class="entry-content">
			<code>[quote=James]<?= __('Quote text', 'help') ?>[/quote]</code>
			<span><?= __('produces named', 'help') ?></span>
			<div class="quotebox"><cite>James <?= __('wrote') ?>:</cite><blockquote><p><?= __('Quote text', 'help') ?></p></blockquote></div>
			<code>[quote]<?= __('Quote text', 'help') ?>[/quote]</code>
			<span><?= __('produces unnamed', 'help') ?></span>
			<div class="quotebox"><blockquote><p><?= __('Quote text', 'help') ?></p></blockquote></div>
		</div>
	</div>
	<div class="ct-box help-box">
		<h3 class="hn"><span><?= __('Code info', 'help') ?></span></h3>
		<div class="entry-content">
			<code>[code]<?= __('Code text', 'help') ?>[/code]</code>
			<span><?= __('produces code box', 'help') ?></span>
			<div class="codebox"><pre><code><?= __('Code text', 'help') ?></code></pre></div>
			<code>[code]<?= __('Code text long', 'help') ?>[/code]</code>
			<span><?= __('produces scroll box', 'help') ?></span>
			<div class="codebox"><pre><code><?= __('Code text long', 'help') ?></code></pre></div>
		</div>
	</div>
	<div class="ct-box help-box">
		<h3 class="hn"><span><?= __('List info', 'help') ?></span></h3>
		<div class="entry-content">
			<code>[list][*]<?= __('List text 1', 'help') ?>[/*][*]<?= __('List text 2', 'help') ?>[/*][*]<?= __('List text 3', 'help') ?>[/*][/list]</code>
			<span><?= __('produces list', 'help') ?></span>
			<ul>
				<li><?= __('List text 1', 'help') ?></li>
				<li><?= __('List text 2', 'help') ?></li>
				<li><?= __('List text 3', 'help') ?></li>
			</ul>
			<code>[list=1][*]<?= __('List text 1', 'help') ?>[/*][*]<?=
				__('List text 2', 'help') ?>[/*][*]<?= __('List text 3') ?>[/*][/list]</code>
				<span><?= __('produces decimal list', 'help') ?></span>
			<ol class="decimal">
				<li><?= __('List text 1', 'help') ?></li>
				<li><?= __('List text 2', 'help') ?></li>
				<li><?= __('List text 3', 'help') ?></li></ol>
			<code>[list=a][*]<?= __('List text 1', 'help') ?>[/*][*]<?= __('List text 2', 'help') ?>[/*][*]<?= __('List text 3', 'help') ?>[/*][/list]</code>
			<span><?= __('produces alpha list', 'help') ?></span>
			<ol class="alpha">
				<li><?= __('List text 1', 'help') ?></li>
				<li><?= __('List text 2', 'help') ?></li>
				<li><?= __('List text 3', 'help') ?></li></ol>
		</div>
	</div>
<?php ($hook = get_hook('he_new_bbcode_section')) ? eval($hook) : null; ?>
</div>