<?php
/**
 * Loads the flash messenger class.
 *
 * @copyright (C) 2008-2011 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


class FlashMessenger
{
	const TEMPLATE_MSG_BLOCK = '<span id="flash_messages">%s</span>';
	const TEMPLATE_MSG = '<span class="%s">%s</span>';

	//
	const MSG_TYPE_ERROR = 0;
	const MSG_TYPE_WARNING = 1;
	const MSG_TYPE_INFO = 2;

	//
	private $messages;


	public function __construct()
	{
		$result = session_start();
		$this->messages = $this->get_messages();
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
	public function show()
	{
		if (empty($this->messages))
			return;

		foreach ($this->messages as $msg)
		{


		}

		$this->clear();
	}


	//
	private function clear()
	{
		$this->messages = array();
		$this->save_messages();
	}


	//
	private function add_message($message, $type)
	{
		array_push($this->messages, array($message, $type));

		$this->save_messages();
	}


	private function save_messages()
	{
		 $_SESSION['forum_flash_messages'] = serialize($this->messages);
	}


	private function get_messages()
	{
		$messages = array();

		if (isset($_SESSION['forum_flash_messages'])) {
			$tmp_messages = unserialize($_SESSION['forum_flash_messages']);

			if (is_array($tmp_messages))
				$messages = $tmp_messages;
		}

		return $messages;
	}
}


// Create the flash messenger adapter object
$forum_flash_messenger = new FlashMessenger();
