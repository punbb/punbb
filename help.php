<?php
/**
 * Help page.
 *
 * Provides examples of how to use various features of the forum (ie: BBCode, smilies).
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

require __DIR__ . '/vendor/autoload.php';

($hook = get_hook('he_start')) ? eval($hook) : null;

if (user()->g_read_board == '0') {
	message(__('No view'));
}

$section = isset($_GET['section']) ? $_GET['section'] : null;
if (!$section) {
	message(__('Bad request'));
}

$forum_page['crumbs'] = array(
	array(config()->o_board_title, forum_link($forum_url['help'])),
	__('Help', 'help')
);

define('FORUM_PAGE', 'help');

template()->render([
	'main_view' => 'help/main'
]);
