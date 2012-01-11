<?php
/**
 * Loads various functions used to parse posts.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */


// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// Load the IDNA class for international url handling
if (defined('FORUM_SUPPORT_PCRE_UNICODE') && defined('FORUM_ENABLE_IDNA'))
{
	require FORUM_ROOT.'include/idna/idna_convert.class.php';
}


// Here you can add additional smilies if you like (please note that you must escape singlequote and backslash)
$smilies = array(':)' => 'smile.png', '=)' => 'smile.png', ':|' => 'neutral.png', '=|' => 'neutral.png', ':(' => 'sad.png', '=(' => 'sad.png', ':D' => 'big_smile.png', '=D' => 'big_smile.png', ':o' => 'yikes.png', ':O' => 'yikes.png', ';)' => 'wink.png', ':/' => 'hmm.png', ':P' => 'tongue.png', ':p' => 'tongue.png', ':lol:' => 'lol.png', ':mad:' => 'mad.png', ':rolleyes:' => 'roll.png', ':cool:' => 'cool.png');

($hook = get_hook('ps_start')) ? eval($hook) : null;


//
// Make sure all BBCodes are lower case and do a little cleanup
//
function preparse_bbcode($text, &$errors, $is_signature = false)
{
	global $forum_config;

	$return = ($hook = get_hook('ps_preparse_bbcode_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	if ($is_signature)
	{
		global $lang_profile;

		if (preg_match('#\[quote(=(&quot;|"|\'|)(.*)\\1)?\]|\[/quote\]|\[code\]|\[/code\]|\[list(=([1a\*]))?\]|\[/list\]#i', $text))
			$errors[] = $lang_profile['Signature quote/code/list'];
	}

	if ($forum_config['p_sig_bbcode'] == '1' && $is_signature || $forum_config['p_message_bbcode'] == '1' && !$is_signature)
	{
		// If the message contains a code tag we have to split it up (text within [code][/code] shouldn't be touched)
		if (strpos($text, '[code]') !== false && strpos($text, '[/code]') !== false)
		{
			list($inside, $outside) = split_text($text, '[code]', '[/code]', $errors);
			$text = implode("\x1", $outside);
		}

		// Tidy up lists
		$pattern = array('%\[list(?:=([1a*]))?+\]((?:(?>.*?(?=\[list(?:=[1a*])?+\]|\[/list\]))|(?R))*)\[/list\]%ise');
		$replace = array('preparse_list_tag(\'$2\', \'$1\', $errors)');
		$text = preg_replace($pattern, $replace, $text);

		$text = str_replace('*'."\0".']', '*]', $text);

		if ($forum_config['o_make_links'] == '1')
		{
			$text = do_clickable($text, defined('FORUM_SUPPORT_PCRE_UNICODE'));
		}

		// If we split up the message before we have to concatenate it together again (code tags)
		if (isset($inside))
		{
			$outside = explode("\x1", $text);
			$text = '';

			$num_tokens = count($outside);

			for ($i = 0; $i < $num_tokens; ++$i)
			{
				$text .= $outside[$i];
				if (isset($inside[$i]))
					$text .= '[code]'.$inside[$i].'[/code]';
			}
		}

		$temp_text = false;
		if (empty($errors))
			$temp_text = preparse_tags($text, $errors, $is_signature);

		if ($temp_text !== false)
			$text = $temp_text;

		// Remove empty tags
		while ($new_text = preg_replace('/\[(b|u|i|h|colou?r|quote|code|img|url|email|list)(?:\=[^\]]*)?\]\[\/\1\]/', '', $text))
		{
			if ($new_text != $text)
				$text = $new_text;
			else
				break;
		}

	}

	$return = ($hook = get_hook('ps_preparse_bbcode_end')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	return forum_trim($text);
}


//
// Check the structure of bbcode tags and fix simple mistakes where possible
//
function preparse_tags($text, &$errors, $is_signature = false)
{
	global $lang_common, $forum_config;

	// Start off by making some arrays of bbcode tags and what we need to do with each one

	// List of all the tags
	$tags = array('quote', 'code', 'b', 'i', 'u', 'color', 'colour', 'url', 'email', 'img', 'list', '*', 'h');
	// List of tags that we need to check are open (You could not put b,i,u in here then illegal nesting like [b][i][/b][/i] would be allowed)
	$tags_opened = $tags;
	// and tags we need to check are closed (the same as above, added it just in case)
	$tags_closed = $tags;
	// Tags we can nest and the depth they can be nested to (only quotes )
	$tags_nested = array('quote' => $forum_config['o_quote_depth'], 'list' => 5, '*' => 5);
	// Tags to ignore the contents of completely (just code)
	$tags_ignore = array('code');
	// Block tags, block tags can only go within another block tag, they cannot be in a normal tag
	$tags_block = array('quote', 'code', 'list', 'h', '*');
	// Inline tags, we do not allow new lines in these
	$tags_inline = array('b', 'i', 'u', 'color', 'colour', 'h');
	// Tags we trim interior space
	$tags_trim = array('img');
	// Tags we remove quotes from the argument
	$tags_quotes = array('url', 'email', 'img');
	// Tags we limit bbcode in
	$tags_limit_bbcode = array(
		'*'		=> array('b', 'i', 'u', 'color', 'colour', 'url', 'email', 'list', 'img'),
		'list'	=> array('*'),
		'url'	=> array('b', 'i', 'u', 'color', 'colour', 'img'),
		'email' => array('b', 'i', 'u', 'color', 'colour', 'img'),
		'img'	=> array()
	);
	// Tags we can automatically fix bad nesting
	$tags_fix = array('quote', 'b', 'i', 'u', 'color', 'colour', 'url', 'email', 'h');

	$return = ($hook = get_hook('ps_preparse_tags_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	$split_text = preg_split("/(\[[\*a-zA-Z0-9-\/]*?(?:=.*?)?\])/", $text, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

	$open_tags = array('post');
	$open_args = array('');
	$opened_tag = 0;
	$new_text = '';
	$current_ignore = '';
	$current_nest = '';
	$current_depth = array();
	$limit_bbcode = $tags;

	foreach ($split_text as $current)
	{
		if ($current == '')
			continue;

		// Are we dealing with a tag?
		if (substr($current, 0, 1) != '[' || substr($current, -1, 1) != ']')
		{
			// Its not a bbcode tag so we put it on the end and continue

			// If we are nested too deeply don't add to the end
			if ($current_nest)
				continue;

			$current = str_replace("\r\n", "\n", $current);
			$current = str_replace("\r", "\n", $current);
			if (in_array($open_tags[$opened_tag], $tags_inline) && strpos($current, "\n") !== false)
			{
				// Deal with new lines
				$split_current = preg_split("/(\n\n+)/", $current, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
				$current = '';

				if (!forum_trim($split_current[0], "\n")) // the first part is a linebreak so we need to handle any open tags first
					array_unshift($split_current, '');

				for ($i = 1; $i < count($split_current); $i += 2)
				{
					$temp_opened = array();
					$temp_opened_arg = array();
					$temp = $split_current[$i - 1];

					while (!empty($open_tags))
					{
						$temp_tag = array_pop($open_tags);
						$temp_arg = array_pop($open_args);

						if (in_array($temp_tag , $tags_inline))
						{
							array_push($temp_opened, $temp_tag);
							array_push($temp_opened_arg, $temp_arg);
							$temp .= '[/'.$temp_tag.']';
						}
						else
						{
							array_push($open_tags, $temp_tag);
							array_push($open_args, $temp_arg);
							break;
						}
					}
					$current .= $temp.$split_current[$i];
					$temp = '';
					while (!empty($temp_opened))
					{
						$temp_tag = array_pop($temp_opened);
						$temp_arg = array_pop($temp_opened_arg);
						if (empty($temp_arg))
							$temp .= '['.$temp_tag.']';
						else
							$temp .= '['.$temp_tag.'='.$temp_arg.']';
						array_push($open_tags, $temp_tag);
						array_push($open_args, $temp_arg);
					}
					$current .= $temp;
				}

				if (array_key_exists($i - 1, $split_current))
					$current .= $split_current[$i - 1];
			}

			if (in_array($open_tags[$opened_tag], $tags_trim))
				$new_text .= forum_trim($current);
			else
				$new_text .= $current;

			continue;
		}

		// Get the name of the tag
		$current_arg = '';
		if (strpos($current, '/') === 1)
		{
			$current_tag = substr($current, 2, -1);
		}
		else if (strpos($current, '=') === false)
		{
			$current_tag = substr($current, 1, -1);
		}
		else
		{
			$current_tag = substr($current, 1, strpos($current, '=')-1);
			$current_arg = substr($current, strpos($current, '=')+1, -1);
		}
		$current_tag = strtolower($current_tag);

		// Is the tag defined?
		if (!in_array($current_tag, $tags))
		{
			// Its not a bbcode tag so we put it on the end and continue
			if (!$current_nest)
				$new_text .= $current;

			continue;
		}

		// We definitely have a bbcode tag.

		// Make the tag string lower case
		if ($equalpos = strpos($current,'='))
		{
			// We have an argument for the tag which we don't want to make lowercase
			if (strlen(substr($current, $equalpos)) == 2)
			{
				// Empty tag argument
				$errors[] = sprintf($lang_common['BBCode error 6'], $current_tag);
				return false;
			}
			$current = strtolower(substr($current, 0, $equalpos)).substr($current, $equalpos);
		}
		else
			$current = strtolower($current);

		//This is if we are currently in a tag which escapes other bbcode such as code
		if ($current_ignore)
		{
			if ('[/'.$current_ignore.']' == $current)
			{
				// We've finished the ignored section
				$current = '[/'.$current_tag.']';
				$current_ignore = '';
			}

			$new_text .= $current;

			continue;
		}

		if ($current_nest)
		{
			// We are currently too deeply nested so lets see if we are closing the tag or not.
			if ($current_tag != $current_nest)
				continue;

			if (substr($current, 1, 1) == '/')
				$current_depth[$current_nest]--;
			else
				$current_depth[$current_nest]++;

			if ($current_depth[$current_nest] <= $tags_nested[$current_nest])
				$current_nest = '';

			continue;
		}

		// Check the current tag is allowed here
		if (!in_array($current_tag, $limit_bbcode) && $current_tag != $open_tags[$opened_tag])
		{
			$errors[] = sprintf($lang_common['BBCode error 3'], $current_tag, $open_tags[$opened_tag]);
			return false;
		}

		if (substr($current, 1, 1) == '/')
		{
			//This is if we are closing a tag

			if ($opened_tag == 0 || !in_array($current_tag, $open_tags))
			{
				//We tried to close a tag which is not open
				if (in_array($current_tag, $tags_opened))
				{
					$errors[] = sprintf($lang_common['BBCode error 1'], $current_tag);
					return false;
				}
			}
			else
			{
				// Check nesting
				while (true)
				{
					// Nesting is ok
					if ($open_tags[$opened_tag] == $current_tag)
					{
						array_pop($open_tags);
						array_pop($open_args);
						$opened_tag--;
						break;
					}

					// Nesting isn't ok, try to fix it
					if (in_array($open_tags[$opened_tag], $tags_closed) && in_array($current_tag, $tags_closed))
					{
						if (in_array($current_tag, $open_tags))
						{
							$temp_opened = array();
							$temp_opened_arg = array();
							$temp = '';
							while (!empty($open_tags))
							{
								$temp_tag = array_pop($open_tags);
								$temp_arg = array_pop($open_args);

								if (!in_array($temp_tag, $tags_fix))
								{
									// We couldn't fix nesting
									$errors[] = sprintf($lang_common['BBCode error 5'], array_pop($temp_opened));
									return false;
								}
								array_push($temp_opened, $temp_tag);
								array_push($temp_opened_arg, $temp_arg);

								if ($temp_tag == $current_tag)
									break;
								else
									$temp .= '[/'.$temp_tag.']';
							}
							$current = $temp.$current;
							$temp = '';
							array_pop($temp_opened);
							array_pop($temp_opened_arg);

							while (!empty($temp_opened))
							{
								$temp_tag = array_pop($temp_opened);
								$temp_arg = array_pop($temp_opened_arg);
								if (empty($temp_arg))
									$temp .= '['.$temp_tag.']';
								else
									$temp .= '['.$temp_tag.'='.$temp_arg.']';
								array_push($open_tags, $temp_tag);
								array_push($open_args, $temp_arg);
							}
							$current .= $temp;
							$opened_tag--;
							break;
						}
						else
						{
							// We couldn't fix nesting
							$errors[] = sprintf($lang_common['BBCode error 1'], $current_tag);
							return false;
						}
					}
					else if (in_array($open_tags[$opened_tag], $tags_closed))
						break;
					else
					{
						array_pop($open_tags);
						array_pop($open_args);
						$opened_tag--;
					}
				}
			}

			if (in_array($current_tag, array_keys($tags_nested)))
			{
				if (isset($current_depth[$current_tag]))
					$current_depth[$current_tag]--;
			}

			if (in_array($open_tags[$opened_tag], array_keys($tags_limit_bbcode)))
				$limit_bbcode = $tags_limit_bbcode[$open_tags[$opened_tag]];
			else
				$limit_bbcode = $tags;
			$new_text .= $current;

			continue;
		}
		else
		{
			// We are opening a tag
			if (in_array($current_tag, array_keys($tags_limit_bbcode)))
				$limit_bbcode = $tags_limit_bbcode[$current_tag];
			else
				$limit_bbcode = $tags;

			if (in_array($current_tag, $tags_block) && !in_array($open_tags[$opened_tag], $tags_block) && $opened_tag != 0)
			{
				// We tried to open a block tag within a non-block tag
				$errors[] = sprintf($lang_common['BBCode error 3'], $current_tag, $open_tags[$opened_tag]);
				return false;
			}

			if (in_array($current_tag, $tags_ignore))
			{
				// Its an ignore tag so we don't need to worry about whats inside it,
				$current_ignore = $current_tag;
				$new_text .= $current;
				continue;
			}

			// Deal with nested tags
			if (in_array($current_tag, $open_tags) && !in_array($current_tag, array_keys($tags_nested)))
			{
				// We nested a tag we shouldn't
				$errors[] = sprintf($lang_common['BBCode error 4'], $current_tag);
				return false;
			}
			else if (in_array($current_tag, array_keys($tags_nested)))
			{
				// We are allowed to nest this tag

				if (isset($current_depth[$current_tag]))
					$current_depth[$current_tag]++;
				else
					$current_depth[$current_tag] = 1;

				// See if we are nested too deep
				if ($current_depth[$current_tag] > $tags_nested[$current_tag])
				{
					$current_nest = $current_tag;
					continue;
				}
			}

			// Remove quotes from arguments for certain tags
			if (strpos($current, '=') !== false && in_array($current_tag, $tags_quotes))
			{
				$current = preg_replace('#\['.$current_tag.'=("|\'|)(.*?)\\1\]\s*#i', '['.$current_tag.'=$2]', $current);
			}

			if (in_array($current_tag, array_keys($tags_limit_bbcode)))
				$limit_bbcode = $tags_limit_bbcode[$current_tag];

			$open_tags[] = $current_tag;
			$open_args[] = $current_arg;
			$opened_tag++;
			$new_text .= $current;
			continue;
		}
	}

	// Check we closed all the tags we needed to
	foreach ($tags_closed as $check)
	{
		if (in_array($check, $open_tags))
		{
			// We left an important tag open
			$errors[] = sprintf($lang_common['BBCode error 5'], $check);
			return false;
		}
	}

	if ($current_ignore)
	{
		// We left an ignore tag open
		$errors[] = sprintf($lang_common['BBCode error 5'], $current_ignore);
		return false;
	}

	$return = ($hook = get_hook('ps_preparse_tags_end')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	return $new_text;
}


//
// Preparse the contents of [list] bbcode
//
function preparse_list_tag($content, $type = '*', &$errors)
{
	global $lang_common;

	if (strlen($type) != 1)
		$type = '*';

	if (strpos($content,'[list') !== false)
	{
		$pattern = array('%\[list(?:=([1a*]))?+\]((?:(?>.*?(?=\[list(?:=[1a*])?+\]|\[/list\]))|(?R))*)\[/list\]%ise');
		$replace = array('preparse_list_tag(\'$2\', \'$1\', $errors)');
		$content = preg_replace($pattern, $replace, $content);
	}

	$items = explode('[*]', str_replace('\"', '"', $content));

	$content = '';
	foreach ($items as $item)
	{
		if (forum_trim($item) != '')
			$content .= '[*'."\0".']'.str_replace('[/*]', '', forum_trim($item)).'[/*'."\0".']'."\n";
	}

	return '[list='.$type.']'."\n".$content.'[/list]';
}


//
// Split text into chunks ($inside contains all text inside $start and $end, and $outside contains all text outside)
//
function split_text($text, $start, $end, &$errors, $retab = true)
{
	global $forum_config, $lang_common;

	$tokens = explode($start, $text);

	$outside[] = $tokens[0];

	$num_tokens = count($tokens);
	for ($i = 1; $i < $num_tokens; ++$i)
	{
		$temp = explode($end, $tokens[$i]);

		if (count($temp) != 2)
		{
			$errors[] = $lang_common['BBCode code problem'];
			return array(null, array($text));
		}
		$inside[] = $temp[0];
		$outside[] = $temp[1];
	}

	if ($forum_config['o_indent_num_spaces'] != 8 && $retab)
	{
		$spaces = str_repeat(' ', $forum_config['o_indent_num_spaces']);
		$inside = str_replace("\t", $spaces, $inside);
	}

	return array($inside, $outside);
}


//
// Truncate URL if longer than 55 characters (add http:// or ftp:// if missing)
//
function handle_url_tag($url, $link = '', $bbcode = false)
{
	$return = ($hook = get_hook('ps_handle_url_tag_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	$full_url = str_replace(array(' ', '\'', '`', '"'), array('%20', '', '', ''), $url);
	if (strpos($url, 'www.') === 0)			// If it starts with www, we add http://
		$full_url = 'http://'.$full_url;
	else if (strpos($url, 'ftp.') === 0)	// Else if it starts with ftp, we add ftp://
		$full_url = 'ftp://'.$full_url;
	else if (!preg_match('#^([a-z0-9]{3,6})://#', $url))	// Else if it doesn't start with abcdef://, we add http://
		$full_url = 'http://'.$full_url;

	if (defined('FORUM_SUPPORT_PCRE_UNICODE') && defined('FORUM_ENABLE_IDNA'))
	{
		static $idn;
		static $cached_encoded_urls = null;

		if (is_null($cached_encoded_urls))
			$cached_encoded_urls = array();

		// Check in cache
		$cache_key = md5($full_url);
		if (isset($cached_encoded_urls[$cache_key]))
			$full_url = $cached_encoded_urls[$cache_key];
		else
		{
			if (!isset($idn))
			{
				$idn = new idna_convert();
				$idn->set_parameter('encoding', 'utf8');
				$idn->set_parameter('strict', false);
			}

			$full_url = $idn->encode($full_url);
			$cached_encoded_urls[$cache_key] = $full_url;
		}
	}

	// Ok, not very pretty :-)
	if (!$bbcode)
	{
		if (defined('FORUM_SUPPORT_PCRE_UNICODE') && defined('FORUM_ENABLE_IDNA'))
		{
			$link_name = ($link == '' || $link == $url) ? $url : $link;
			if (preg_match('!^(https?|ftp|news){1}'.preg_quote('://xn--', '!').'!', $link_name))
			{
				$link = $idn->decode($link_name);
			}
		}

		$link = ($link == '' || $link == $url) ? ((utf8_strlen($url) > 55) ? utf8_substr($url, 0 , 39).' … '.utf8_substr($url, -10) : $url) : stripslashes($link);
	}

	$return = ($hook = get_hook('ps_handle_url_tag_end')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	if ($bbcode)
	{
		if (defined('FORUM_SUPPORT_PCRE_UNICODE') && defined('FORUM_ENABLE_IDNA'))
		{
			if (preg_match('!^(https?|ftp|news){1}'.preg_quote('://xn--', '!').'!', $link))
			{
				$link = $idn->decode($link);
			}
		}

		if ($full_url == $link)
			return '[url]'.$link.'[/url]';
		else
			return '[url='.$full_url.']'.$link.'[/url]';
	}
	else
		return '<a href="'.$full_url.'">'.$link.'</a>';
}


//
// Turns an URL from the [img] tag into an <img> tag or a <a href...> tag
//
function handle_img_tag($url, $is_signature = false, $alt = null)
{
	global $lang_common, $forum_user;

	$return = ($hook = get_hook('ps_handle_img_tag_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	if ($alt == null)
		$alt = $url;

	$img_tag = '<a href="'.$url.'">&lt;'.$lang_common['Image link'].'&gt;</a>';

	if ($is_signature && $forum_user['show_img_sig'] != '0')
		$img_tag = '<img class="sigimage" src="'.$url.'" alt="'.forum_htmlencode($alt).'" />';
	else if (!$is_signature && $forum_user['show_img'] != '0')
		$img_tag = '<span class="postimg"><img src="'.$url.'" alt="'.forum_htmlencode($alt).'" /></span>';

	$return = ($hook = get_hook('ps_handle_img_tag_end')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	return $img_tag;
}


//
// Parse the contents of [list] bbcode
//
function handle_list_tag($content, $type = '*')
{
	if (strlen($type) != 1)
		$type = '*';

	if (strpos($content,'[list') !== false)
	{
		$pattern = array('%\[list(?:=([1a*]))?+\]((?:(?>.*?(?=\[list(?:=[1a*])?+\]|\[/list\]))|(?R))*)\[/list\]%ise');
		$replace = array('handle_list_tag(\'$2\', \'$1\')');
		$content = preg_replace($pattern, $replace, $content);
	}

	$content = preg_replace('#\s*\[\*\](.*?)\[/\*\]\s*#s', '<li><p>$1</p></li>', forum_trim($content));

	if ($type == '*')
		$content = '<ul>'.$content.'</ul>';
	else
		if ($type == 'a')
			$content = '<ol class="alpha">'.$content.'</ol>';
		else
			$content = '<ol class="decimal">'.$content.'</ol>';

	return '</p>'.$content.'<p>';
}


//
// Convert BBCodes to their HTML equivalent
//
function do_bbcode($text, $is_signature = false)
{
	global $lang_common, $forum_user, $forum_config;

	$return = ($hook = get_hook('ps_do_bbcode_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	if (strpos($text, '[quote') !== false)
	{
		$text = preg_replace('#\[quote=(&\#039;|&quot;|"|\'|)(.*?)\\1\]#e', '"</p><div class=\"quotebox\"><cite>".str_replace(array(\'[\', \'\\"\'), array(\'&#91;\', \'"\'), \'$2\')." ".$lang_common[\'wrote\'].":</cite><blockquote><p>"', $text);
		$text = preg_replace('#\[quote\]\s*#', '</p><div class="quotebox"><blockquote><p>', $text);
		$text = preg_replace('#\s*\[\/quote\]#S', '</p></blockquote></div><p>', $text);
	}

	if (!$is_signature)
	{
		$pattern[] = '%\[list(?:=([1a*]))?+\]((?:(?>.*?(?=\[list(?:=[1a*])?+\]|\[/list\]))|(?R))*)\[/list\]%ise';
		$replace[] = 'handle_list_tag(\'$2\', \'$1\')';
	}

	$pattern[] = '#\[b\](.*?)\[/b\]#ms';
	$pattern[] = '#\[i\](.*?)\[/i\]#ms';
	$pattern[] = '#\[u\](.*?)\[/u\]#ms';
	$pattern[] = '#\[colou?r=([a-zA-Z]{3,20}|\#[0-9a-fA-F]{6}|\#[0-9a-fA-F]{3})](.*?)\[/colou?r\]#ms';
	$pattern[] = '#\[h\](.*?)\[/h\]#ms';

	$replace[] = '<strong>$1</strong>';
	$replace[] = '<em>$1</em>';
	$replace[] = '<span class="bbu">$1</span>';
	$replace[] = '<span style="color: $1">$2</span>';
	$replace[] = '</p><h5>$1</h5><p>';

	if (($is_signature && $forum_config['p_sig_img_tag'] == '1') || (!$is_signature && $forum_config['p_message_img_tag'] == '1'))
	{
		$pattern[] = '#\[img\]((ht|f)tps?://)([^\s<"]*?)\[/img\]#e';
		$pattern[] = '#\[img=([^\[]*?)\]((ht|f)tps?://)([^\s<"]*?)\[/img\]#e';
		if ($is_signature)
		{
			$replace[] = 'handle_img_tag(\'$1$3\', true)';
			$replace[] = 'handle_img_tag(\'$2$4\', true, \'$1\')';
		}
		else
		{
			$replace[] = 'handle_img_tag(\'$1$3\', false)';
			$replace[] = 'handle_img_tag(\'$2$4\', false, \'$1\')';
		}
	}

	$pattern[] = '#\[url\]([^\[]*?)\[/url\]#e';
	$pattern[] = '#\[url=([^\[]+?)\](.*?)\[/url\]#e';
	$pattern[] = '#\[email\]([^\[]*?)\[/email\]#';
	$pattern[] = '#\[email=([^\[]+?)\](.*?)\[/email\]#';

	$replace[] = 'handle_url_tag(\'$1\')';
	$replace[] = 'handle_url_tag(\'$1\', \'$2\')';
	$replace[] = '<a href="mailto:$1">$1</a>';
	$replace[] = '<a href="mailto:$1">$2</a>';

	$return = ($hook = get_hook('ps_do_bbcode_replace')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	// This thing takes a while! :)
	$text = preg_replace($pattern, $replace, $text);

	$return = ($hook = get_hook('ps_do_bbcode_end')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	return $text;
}


//
// Make hyperlinks clickable
//
function do_clickable($text, $unicode = FALSE)
{
	$text = ' '.$text;

	if ($unicode)
	{
		$text = preg_replace('#(?<=[\s\]\)])(<)?(\[)?(\()?([\'"]?)(https?|ftp|news){1}://([\p{Nd}\p{L}\-]+\.([\p{Nd}\p{L}\-]+\.)*[\p{Nd}\p{L}\-]+(:[0-9]+)?(/[^\s\[]*[^\s.,?!\[;:-]?)?)\4(?(3)(\)))(?(2)(\]))(?(1)(>))(?![^\s]*\[/(?:url|img)\])#ieu', 'stripslashes(\'$1$2$3$4\').handle_url_tag(\'$5://$6\', \'$5://$6\', true).stripslashes(\'$4$10$11$12\')', $text);
		$text = preg_replace('#(?<=[\s\]\)])(<)?(\[)?(\()?([\'"]?)(www|ftp)\.(([\p{Nd}\p{L}\-]+\.)*[\p{Nd}\p{L}\-]+(:[0-9]+)?(/[^\s\[]*[^\s.,?!\[;:-])?)\4(?(3)(\)))(?(2)(\]))(?(1)(>))(?![^\s]*\[/(?:url|img)\])#ieu', 'stripslashes(\'$1$2$3$4\').handle_url_tag(\'$5.$6\', \'$5.$6\', true).stripslashes(\'$4$10$11$12\')', $text);
	}
	else
	{
		$text = preg_replace('#(?<=[\s\]\)])(<)?(\[)?(\()?([\'"]?)(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^\s\[]*[^\s.,?!\[;:-]?)?)\4(?(3)(\)))(?(2)(\]))(?(1)(>))(?![^\s]*\[/(?:url|img)\])#ie', 'stripslashes(\'$1$2$3$4\').handle_url_tag(\'$5://$6\', \'$5://$6\', true).stripslashes(\'$4$10$11$12\')', $text);
		$text = preg_replace('#(?<=[\s\]\)])(<)?(\[)?(\()?([\'"]?)(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^\s\[]*[^\s.,?!\[;:-])?)\4(?(3)(\)))(?(2)(\]))(?(1)(>))(?![^\s]*\[/(?:url|img)\])#ie', 'stripslashes(\'$1$2$3$4\').handle_url_tag(\'$5.$6\', \'$5.$6\', true).stripslashes(\'$4$10$11$12\')', $text);
	}

	return substr($text, 1);
}


//
// Convert a series of smilies to images
//
function do_smilies($text)
{
	global $forum_config, $base_url, $smilies;

	$return = ($hook = get_hook('ps_do_smilies_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	$text = ' '.$text.' ';

	foreach ($smilies as $smiley_text => $smiley_img)
	{
		if (strpos($text, $smiley_text) !== false)
			$text = preg_replace("#(?<=[>\s])".preg_quote($smiley_text, '#')."(?=\W)#m", '<img src="'.$base_url.'/img/smilies/'.$smiley_img.'" width="15" height="15" alt="'.substr($smiley_img, 0, strrpos($smiley_img, '.')).'" />', $text);
	}

	$return = ($hook = get_hook('ps_do_smilies_end')) ? eval($hook) : null;

	return substr($text, 1, -1);
}


//
// Parse message text
//
function parse_message($text, $hide_smilies)
{
	global $forum_config, $lang_common, $forum_user;

	$return = ($hook = get_hook('ps_parse_message_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	if ($forum_config['o_censoring'] == '1')
		$text = censor_words($text);

	$return = ($hook = get_hook('ps_parse_message_post_censor')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	// Convert applicable characters to HTML entities
	$text = forum_htmlencode($text);

	$return = ($hook = get_hook('ps_parse_message_pre_split')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	// If the message contains a code tag we have to split it up (text within [code][/code] shouldn't be touched)
	if (strpos($text, '[code]') !== false && strpos($text, '[/code]') !== false)
	{
		list($inside, $outside) = split_text($text, '[code]', '[/code]', $errors);
		$text = implode("\x1", $outside);
	}

	$return = ($hook = get_hook('ps_parse_message_post_split')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	if ($forum_config['p_message_bbcode'] == '1' && strpos($text, '[') !== false && strpos($text, ']') !== false)
		$text = do_bbcode($text);

	if ($forum_config['o_smilies'] == '1' && $forum_user['show_smilies'] == '1' && $hide_smilies == '0')
		$text = do_smilies($text);

	$return = ($hook = get_hook('ps_parse_message_bbcode')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	// Deal with newlines, tabs and multiple spaces
	$pattern = array("\n", "\t", '  ', '  ');
	$replace = array('<br />', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;');
	$text = str_replace($pattern, $replace, $text);

	$return = ($hook = get_hook('ps_parse_message_pre_merge')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	// If we split up the message before we have to concatenate it together again (code tags)
	if (isset($inside))
	{
		$outside = explode("\x1", $text);
		$text = '';

		$num_tokens = count($outside);

		for ($i = 0; $i < $num_tokens; ++$i)
		{
			$text .= $outside[$i];
			if (isset($inside[$i]))
				$text .= '</p><div class="codebox"><pre><code>'.forum_trim($inside[$i], "\n\r").'</code></pre></div><p>';
		}
	}

	$return = ($hook = get_hook('ps_parse_message_post_merge')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	// Add paragraph tag around post, but make sure there are no empty paragraphs
	$text = preg_replace('#<br />\s*?<br />((\s*<br />)*)#i', "</p>$1<p>", $text);
	$text = str_replace('<p><br />', '<p>', $text);
	$text = str_replace('<p></p>', '', '<p>'.$text.'</p>');

	$return = ($hook = get_hook('ps_parse_message_end')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	return $text;
}


//
// Parse signature text
//
function parse_signature($text)
{
	global $forum_config, $lang_common, $forum_user;

	$return = ($hook = get_hook('ps_parse_signature_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	if ($forum_config['o_censoring'] == '1')
		$text = censor_words($text);

	$return = ($hook = get_hook('ps_parse_signature_post_censor')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	// Convert applicable characters to HTML entities
	$text = forum_htmlencode($text);

	$return = ($hook = get_hook('ps_parse_signature_pre_bbcode')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	if ($forum_config['p_sig_bbcode'] == '1' && strpos($text, '[') !== false && strpos($text, ']') !== false)
		$text = do_bbcode($text, true);

	if ($forum_config['o_smilies_sig'] == '1' && $forum_user['show_smilies'] == '1')
		$text = do_smilies($text);

	$return = ($hook = get_hook('ps_parse_signature_post_bbcode')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	// Deal with newlines, tabs and multiple spaces
	$pattern = array("\n", "\t", '  ', '  ');
	$replace = array('<br />', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;');
	$text = str_replace($pattern, $replace, $text);

	$return = ($hook = get_hook('ps_parse_signature_end')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	return $text;
}

define('FORUM_PARSER_LOADED', 1);
