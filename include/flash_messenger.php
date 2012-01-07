<?php
/**
 * Loads the flash messenger class.
 *
 * @copyright (C) 2008-2012 PunBB
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


class FlashMessenger
{
	const TEMPLATE_MSG_BLOCK = '%s';
	const TEMPLATE_MSG = '<span class="%s">%s</span>';

	//
	const MSG_TYPE_ERROR = 'message_error';
	const MSG_TYPE_WARNING = 'message_warning';
	const MSG_TYPE_INFO = 'message_info';

	//
	private $message;


	public function __construct()
	{
		global $forum_config;

		// Do not use with redirect
		$disabled = isset($forum_config['o_redirect_delay']) && intval($forum_config['o_redirect_delay'], 10) > 0;

		if (!$disabled)
		{
			forum_session_start();
		}

		$this->message = $this->get_message();
	}


	// Add error message
	public function add_error($msg)
	{
		$this->add_message($msg, self::MSG_TYPE_ERROR);
	}


	// Add warning message
	public function add_warning($msg)
	{
		$this->add_message($msg, self::MSG_TYPE_WARNING);
	}


	// Add info message
	public function add_info($msg)
	{
		$this->add_message($msg, self::MSG_TYPE_INFO);
	}


	//
	public function show($just_return = false)
	{
		if (empty($this->message))
			return;

		$message = sprintf(self::TEMPLATE_MSG, forum_htmlencode($this->message[1]), forum_htmlencode($this->message[0]));

		$m = sprintf(self::TEMPLATE_MSG_BLOCK, $message);
		if ($just_return) {
			$this->clear();
			return $m;
		}

		echo $m;

		$this->clear();
	}


	//
	private function clear()
	{
		$this->message = NULL;
		$this->save_message();
	}


	//
	private function add_message($message, $type)
	{
		$this->message = array($message, $type);
		$this->save_message();
	}


	private function save_message()
	{
		$_SESSION['punbb_forum_flash'] = serialize($this->message);
	}


	private function get_message()
	{
		$message = NULL;

		if (isset($_SESSION['punbb_forum_flash'])) {
			$tmp_message = unserialize($_SESSION['punbb_forum_flash']);

			if (!is_null($tmp_message) && !empty($tmp_message))
			{
				if (is_array($tmp_message) && !empty($tmp_message[0]) && !empty($tmp_message[1]))
				{
					$message = $tmp_message;
				}
			}
		}

		return $message;
	}
}

// Create the flash messenger adapter object
$forum_flash = new FlashMessenger();
