<?php
/**
 * Outputs the footer used by most forum pages.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM')) {
	exit;
}

// TODO use event 'page_render'
// ob_start();
//$tpl_main = forum_trim(ob_get_contents());
//ob_end_clean();

// Last call!
($hook = get_hook('ft_end')) ? eval($hook) : null;

// End the transaction
db()->end_transaction();

// Close the db connection (and free up any result data)
db()->close();

// TODO use event 'page_render'
// Spit out the page
//exit($tpl_main);
