<div id="brd-main" class="main basic">

	<div class="main-head">
		<h1 class="hn"><span><?= $message ?><?= __('Redirecting') ?></span></h1>
	</div>

	<div class="main-content main-message">
		<p><?php printf(__('Forwarding info'), $forum_config['o_redirect_delay'],
			intval($forum_config['o_redirect_delay']) == 1 ?
				__('second') : __('seconds')) ?><span>
			<a href="<?= $destination_url ?>"><?= __('Click redirect') ?></a></span></p>
	</div>

</div>