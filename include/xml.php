<?php
/**
 * Loads various functions used in parsing XML (mostly for extensions).
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */
namespace punbb;

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;


//
// Parse XML data into an array
//
function xml_to_array($raw_xml)
{
	$xml_parser = xml_parser_create();
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($xml_parser, XML_OPTION_SKIP_WHITE, 0);
	xml_parse_into_struct($xml_parser, $raw_xml, $vals);
	xml_parser_free($xml_parser);

	$_tmp = '';
	foreach ($vals as $xml_elem)
	{
		$x_tag = $xml_elem['tag'];
		$x_level = $xml_elem['level'];
		$x_type = $xml_elem['type'];

		if ($x_level != 1 && $x_type == 'close')
		{
			if (isset($multi_key[$x_tag][$x_level]))
				$multi_key[$x_tag][$x_level] = 1;
			else
				$multi_key[$x_tag][$x_level] = 0;
		}

		if ($x_level != 1 && $x_type == 'complete')
		{
			if ($_tmp == $x_tag)
				$multi_key[$x_tag][$x_level] = 1;

			$_tmp = $x_tag;
		}
	}

	foreach ($vals as $xml_elem)
	{
		$x_tag = $xml_elem['tag'];
		$x_level = $xml_elem['level'];
		$x_type = $xml_elem['type'];

		if ($x_type == 'open')
			$level[$x_level] = $x_tag;

		$start_level = 1;
		$php_stmt = '$xml_array';
		if ($x_type == 'close' && $x_level != 1)
			$multi_key[$x_tag][$x_level]++;

		while ($start_level < $x_level)
		{
			$php_stmt .= '[$level['.$start_level.']]';
			if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
				$php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';

			++$start_level;
		}

		$add = '';
		if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type == 'open' || $x_type == 'complete'))
		{
			if (!isset($multi_key2[$x_tag][$x_level]))
				$multi_key2[$x_tag][$x_level] = 0;
			else
				$multi_key2[$x_tag][$x_level]++;

			$add = '['.$multi_key2[$x_tag][$x_level].']';
		}

		if (isset($xml_elem['value']) && forum_trim($xml_elem['value']) != '' && !array_key_exists('attributes', $xml_elem))
		{
			if ($x_type == 'open')
				$php_stmt_main = $php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
			else
				$php_stmt_main = $php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';

			eval($php_stmt_main);
		}

		if (array_key_exists('attributes', $xml_elem))
		{
			if (isset($xml_elem['value']))
			{
				$php_stmt_main = $php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
				eval($php_stmt_main);
			}

			foreach ($xml_elem['attributes'] as $key=>$value)
			{
				$php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[\'attributes\'][$key] = $value;';
				eval($php_stmt_att);
			}
		}
	}

	if (isset($xml_array))
	{
		// Make sure there's an array of notes (even if there is only one)
		if (isset($xml_array['extension']['note']))
		{
			if (!is_array(current($xml_array['extension']['note'])))
				$xml_array['extension']['note'] = array($xml_array['extension']['note']);
		}
		else
			$xml_array['extension']['note'] = array();

		// Make sure there's an array of hooks (even if there is only one)
		if (isset($xml_array['extension']['hooks']) && isset($xml_array['extension']['hooks']['hook']))
		{
			if (!is_array(current($xml_array['extension']['hooks']['hook'])))
				$xml_array['extension']['hooks']['hook'] = array($xml_array['extension']['hooks']['hook']);
		}
	}

	return isset($xml_array) ? $xml_array : array();
}


//
// Validate the syntax of an extension manifest file
//
function validate_manifest($xml_array, $folder_name)
{
	$errors = array();

	$return = ($hook = get_hook('xm_fn_validate_manifest_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	if (!isset($xml_array['extension']) || !is_array($xml_array['extension']))
		$errors[] = __('extension root error', 'admin_ext');
	else
	{
		$ext = $xml_array['extension'];
		if (!isset($ext['attributes']['engine']))
			$errors[] = __('extension/engine error', 'admin_ext');
		else if ($ext['attributes']['engine'] != '1.0')
			$errors[] = __('extension/engine error2', 'admin_ext');

		if (!isset($ext['id']) || $ext['id'] == '')
			$errors[] = __('extension/id error', 'admin_ext');
		else if ($ext['id'] != $folder_name)
			$errors[] = __('extension/id error2', 'admin_ext');

		if (!isset($ext['title']) || $ext['title'] == '')
			$errors[] = __('extension/title error', 'admin_ext');
		if (!isset($ext['version']) || $ext['version'] == '' || preg_match('/[^a-z0-9\- \.]+/i', $ext['version']))
			$errors[] = __('extension/version error', 'admin_ext');
		if (!isset($ext['description']) || $ext['description'] == '')
			$errors[] = __('extension/description error', 'admin_ext');
		if (!isset($ext['author']) || $ext['author'] == '')
			$errors[] = __('extension/author error', 'admin_ext');
		if (!isset($ext['minversion']) || $ext['minversion'] == '')
			$errors[] = __('extension/minversion error', 'admin_ext');
		if (isset($ext['minversion']) && version_compare(clean_version(config()['o_cur_version']), clean_version($ext['minversion']), '<'))
			$errors[] = sprintf(__('extension/minversion error2', 'admin_ext'), $ext['minversion']);
		if (!isset($ext['maxtestedon']) || $ext['maxtestedon'] == '')
			$errors[] = __('extension/maxtestedon error', 'admin_ext');

		if (isset($ext['note']))
		{
			foreach ($ext['note'] as $note)
			{
				if (!isset($note['content']) || $note['content'] == '')
					$errors[] = __('extension/note error', 'admin_ext');
				if (!isset($note['attributes']['type']) || $note['attributes']['type'] == '')
					$errors[] = __('extension/note error2', 'admin_ext');
			}
		}

		if (isset($ext['hooks']) && is_array($ext['hooks']))
		{
			if (!isset($ext['hooks']['hook']) || !is_array($ext['hooks']['hook']))
				$errors[] = __('extension/hooks/hook error', 'admin_ext');
			else
			{
				foreach ($ext['hooks']['hook'] as $hook)
				{
					if (!isset($hook['content']) || $hook['content'] == '')
						$errors[] = __('extension/hooks/hook error', 'admin_ext');
					if (!isset($hook['attributes']['id']) || $hook['attributes']['id'] == '')
						$errors[] = __('extension/hooks/hook error2', 'admin_ext');
					if (isset($hook['attributes']['priority']) && (!ctype_digit($hook['attributes']['priority']) || $hook['attributes']['priority'] < 0 || $hook['attributes']['priority'] > 10))
						$errors[] = __('extension/hooks/hook error3', 'admin_ext');

					$tokenized_hook = token_get_all('<?php '.$hook['content']);
					$last_element = array_pop($tokenized_hook);
					if (is_array($last_element) && $last_element[0] == T_INLINE_HTML)
						$errors[] = __('extension/hooks/hook error4', 'admin_ext');
				}
			}
		}
	}

	($hook = get_hook('xm_fn_validate_manifest_end')) ? eval($hook) : null;

	return $errors;
}

define('FORUM_XML_FUNCTIONS_LOADED', 1);
