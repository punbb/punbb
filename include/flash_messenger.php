<?php
/**
 * Loads the flash messenger class.
 *
 * @copyright (C) 2008-2011 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;


class FlashMessenger
{
	const TEMPLATE = '<span class="flash_message %s">%s</span>';

	const MSG_TYPE_ERROR = 0;
	const MSG_TYPE_WARNING = 1;
	const MSG_TYPE_INFO = 2;

	private $messages;


	public __construct()
	{
		$this->messages = array();
	}


	// Add error message
	public function add_error($msg)
	{
		$this->add_message($msg, MSG_TYPE_ERROR);
	}


	// Add warning message
	public function add_warning($msg)
	{
		$this->add_message($msg, MSG_TYPE_WARNING);
	}


	// Add info message
	public function add_info($msg)
	{
		$this->add_message($msg, MSG_TYPE_INFO);
	}


	//
	public function show_message()
	{


	}

	//
	public function clear()
	{

	}


	//
	private function add_message($message, $type)
	{

	}
}


// Create the flash messenger adapter object
$forum_flash_messenger = new FlashMessenger();
