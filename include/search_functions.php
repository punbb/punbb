<?php
/**
 * Loads various functions that are used for searching the forum.
 *
 * @copyright (C) 2008-2012 PunBB, partially based on code (C) 2008-2009 FluxBB.org
 * @license http://www.gnu.org/licenses/gpl.html GPL version 2 or higher
 * @package PunBB
 */

if (!defined('FORUM_SEARCH_MIN_WORD'))
	define('FORUM_SEARCH_MIN_WORD', 3);
if (!defined('FORUM_SEARCH_MAX_WORD'))
	define('FORUM_SEARCH_MAX_WORD', 20);

//
// Cache the results of a search and redirect the user to the results page
//
function create_search_cache($keywords, $author, $search_in = false, $forum = array(-1), $show_as = 'topics', $sort_by = null, $sort_dir = 'DESC')
{
	global $forum_db, $forum_user, $forum_config, $forum_url, $lang_search, $lang_common, $db_type;

	$return = ($hook = get_hook('sf_fn_create_search_cache_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	if (utf8_strlen(str_replace(array('*', '%'), '', $author)) < 2)
		$author = '';

	if (utf8_strlen(str_replace(array('*', '%'), '', $keywords)) < FORUM_SEARCH_MIN_WORD)
		$keywords = '';

	if (!$keywords && !$author)
		message($lang_search['No terms']);

	$keywords = utf8_strtolower($keywords);
	$author = utf8_strtolower($author);

	// Flood protection
	if ($forum_user['last_search'] && (time() - $forum_user['last_search']) < $forum_user['g_search_flood'] && (time() - $forum_user['last_search']) >= 0)
		message(sprintf($lang_search['Search flood'], $forum_user['g_search_flood']));

	if ($forum_user['is_guest'])
	{
		$query = array(
			'UPDATE'	=> 'online',
			'SET'		=> 'last_search='.time(),
			'WHERE'		=> 'ident=\''.$forum_db->escape(get_remote_address()).'\''
		);
	}
	else
	{
		$query = array(
			'UPDATE'	=> 'users',
			'SET'		=> 'last_search='.time(),
			'WHERE'		=> 'id='.$forum_user['id'],
		);

	}

	($hook = get_hook('sf_fn_create_search_cache_qr_update_last_search_time')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	// We need to grab results, insert them into the cache and reload with a search id before showing them
	$keyword_results = $author_results = array();

	// If it's a search for keywords
	if ($keywords)
	{
		// Remove any apostrophes which aren't part of words
		$keywords = substr(preg_replace('((?<=\W)\'|\'(?=\W))', '', ' '.$keywords.' '), 1, -1);

		// Remove symbols and multiple whitespace
		$keywords = preg_replace('/[\^\$&\(\)<>`"\|,@_\?%~\+\[\]{}:=\/#\\\\;!\.\s]+/', ' ', $keywords);

		// Fill an array with all the words
		$keywords_array = array_unique(explode(' ', $keywords));

		// Remove any words that are not indexed
		$keywords_array = array_filter($keywords_array, 'validate_search_word');

		if (empty($keywords_array))
			no_search_results();

		$word_count = 0;
		$match_type = 'and';
		$result_list = array();

		foreach ($keywords_array as $cur_word)
		{
			switch ($cur_word)
			{
				case 'and':
				case 'or':
				case 'not':
					$match_type = $cur_word;
					break;

				default:
				{
					$query = array(
						'SELECT'	=> 'm.post_id',
						'FROM'		=> 'search_words AS w',
						'JOINS'		=> array(
							array(
								'INNER JOIN'	=> 'search_matches AS m',
								'ON'			=> 'm.word_id=w.id'
							)
						),
						'WHERE'		=> 'w.word LIKE \''.$forum_db->escape(str_replace('*', '%', $cur_word)).'\''
					);

					// Search in what?
					if ($search_in)
						$query['WHERE'] .= ($search_in > 0 ? ' AND m.subject_match=0' : ' AND m.subject_match=1');

					($hook = get_hook('sf_fn_create_search_cache_qr_get_keyword_hits')) ? eval($hook) : null;
					$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

					$row = array();
					while (list($post_id) = $forum_db->fetch_row($result))
					{
						$row[$post_id] = 1;

						if (!$word_count)
							$result_list[$post_id] = 1;
						else if ($match_type == 'or')
							$result_list[$post_id] = 1;
						else if ($match_type == 'not')
							$result_list[$post_id] = 0;
					}

					if ($match_type == 'and' && $word_count)
					{
						foreach (array_keys($result_list) as $post_id)
						{
							if (!isset($row[$post_id]))
								$result_list[$post_id] = 0;
						}
					}

					++$word_count;
					$forum_db->free_result($result);

					break;
				}
			}
		}

		foreach ($result_list as $post_id => $matches)
		{
			if ($matches)
				$keyword_results[] = $post_id;
		}

		unset($result_list);
	}

	// If it's a search for author name (and that author name isn't Guest)
	if ($author && $author != 'guest' && $author != utf8_strtolower($lang_common['Guest']))
	{
		$query = array(
			'SELECT'	=> 'u.id',
			'FROM'		=> 'users AS u',
			'WHERE'		=> 'u.username '.($db_type == 'pgsql' ? 'ILIKE' : 'LIKE').' \''.$forum_db->escape(str_replace('*', '%', $author)).'\''
		);

		($hook = get_hook('sf_fn_create_search_cache_qr_get_author')) ? eval($hook) : null;
		$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

		$user_ids = array();
		while ($row = $forum_db->fetch_row($result))
		{
			$user_ids[] = $row[0];
		}

		if (!empty($user_ids))
		{
			$query = array(
				'SELECT'	=> 'p.id',
				'FROM'		=> 'posts AS p',
				'WHERE'		=> 'p.poster_id IN('.implode(',', $user_ids).')'
			);

			($hook = get_hook('sf_fn_create_search_cache_qr_get_author_hits')) ? eval($hook) : null;
			$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

			$search_ids = array();
			while ($row = $forum_db->fetch_row($result))
				$author_results[] = $row[0];

			$forum_db->free_result($result);
		}
	}

	if ($author && $keywords)
	{
		// If we searched for both keywords and author name we want the intersection between the results
		$search_ids = array_intersect($keyword_results, $author_results);
		unset($keyword_results, $author_results);
	}
	else if ($keywords)
		$search_ids = $keyword_results;
	else
		$search_ids = $author_results;

	if (count($search_ids) == 0)
		no_search_results();

	// Setup the default show_as topics search
	$query = array(
		'SELECT'	=> 't.id',
		'FROM'		=> 'posts AS p',
		'JOINS'		=> array(
			array(
				'INNER JOIN'	=> 'topics AS t',
				'ON'			=> 't.id=p.topic_id'
			),
			array(
				'LEFT JOIN'		=> 'forum_perms AS fp',
				'ON'			=> '(fp.forum_id=t.forum_id AND fp.group_id='.$forum_user['g_id'].')'
			)
		),
		'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND p.id IN('.implode(',', $search_ids).')',
		'GROUP BY'	=> 't.id'
	);

	// Search a specific forum?
	if (!in_array(-1, $forum) || ($forum_config['o_search_all_forums'] == '0' && !$forum_user['is_admmod']))
		$query['WHERE'] .= ' AND t.forum_id IN('.implode(',', $forum).')';

	// Adjust the query if show_as posts
	if ($show_as == 'posts')
	{
		$query['SELECT'] = 'p.id';
		unset($query['GROUP BY']);
	}

	($hook = get_hook('sf_fn_create_search_cache_qr_get_hits')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$search_ids = array();
	while ($row = $forum_db->fetch_row($result))
		$search_ids[] = $row[0];

	// Prune "old" search results
	$query = array(
		'SELECT'	=> 'o.ident',
		'FROM'		=> 'online AS o'
	);

	($hook = get_hook('sf_fn_create_search_cache_qr_get_online_idents')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$online_idents = array();
	while ($row = $forum_db->fetch_row($result))
	{
		$online_idents[] = '\''.$forum_db->escape($row[0]).'\'';
	}

	if (!empty($online_idents))
	{
		$query = array(
			'DELETE'	=> 'search_cache',
			'WHERE'		=> 'ident NOT IN('.implode(',', $online_idents).')'
		);

		($hook = get_hook('sf_fn_create_search_cache_qr_delete_old_cached_searches')) ? eval($hook) : null;
		$forum_db->query_build($query) or error(__FILE__, __LINE__);
	}

	// Final search results
	$search_results = implode(',', $search_ids);

	// Fill an array with our results and search properties
	$search_data = serialize(compact('search_results', 'sort_by', 'sort_dir', 'show_as'));
	$search_id = mt_rand(1, 2147483647);
	$ident = ($forum_user['is_guest']) ? get_remote_address() : $forum_user['username'];

	$query = array(
		'INSERT'	=> 'id, ident, search_data',
		'INTO'		=> 'search_cache',
		'VALUES'	=> $search_id.', \''.$forum_db->escape($ident).'\', \''.$forum_db->escape($search_data).'\''
	);

	($hook = get_hook('sf_fn_create_search_cache_qr_cache_search')) ? eval($hook) : null;
	$forum_db->query_build($query) or error(__FILE__, __LINE__);

	$return = ($hook = get_hook('sf_fn_create_search_cache_end')) ? eval($hook) : null;
	if ($return != null)
		return;

	$forum_db->end_transaction();
	$forum_db->close();

	// Redirect the user to the cached result page
	header('Location: '.str_replace('&amp;', '&', forum_link($forum_url['search_results'], $search_id)));
	exit;
}


//
// Generate query to grab the results for a cached search
//
function generate_cached_search_query($search_id, &$show_as)
{
	global $forum_db, $db_type, $forum_user, $forum_config;

	$return = ($hook = get_hook('sf_fn_generate_cached_search_query_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	$ident = ($forum_user['is_guest']) ? get_remote_address() : $forum_user['username'];

	$query = array(
		'SELECT'	=> 'sc.search_data',
		'FROM'		=> 'search_cache AS sc',
		'WHERE'		=> 'sc.id='.$search_id.' AND sc.ident=\''.$forum_db->escape($ident).'\''
	);

	($hook = get_hook('sf_fn_generate_cached_search_query_qr_get_cached_search_data')) ? eval($hook) : null;
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	if ($row = $forum_db->fetch_assoc($result))
	{
		$search_data = unserialize($row['search_data']);

		$search_results = $search_data['search_results'];
		$sort_by = $search_data['sort_by'];
		$sort_dir = $search_data['sort_dir'];
		$show_as = $search_data['show_as'];

		unset($search_data);
	}
	else
		return false;

	// If there are no posts, we don't need to execute the query
	if (empty($search_results))
		return false;

	switch ($sort_by)
	{
		case 1:
			$sort_by_sql = ($show_as == 'topics') ? 't.poster' : 'p.poster';
			break;

		case 2:
			$sort_by_sql = 't.subject';
			break;

		case 3:
			$sort_by_sql = 't.forum_id';
			break;

		default:
			$sort_by_sql = ($show_as == 'topics') ? 't.posted' : 'p.posted';
			($hook = get_hook('sf_fn_generate_cached_search_query_qr_cached_sort_by')) ? eval($hook) : null;
			break;
	}

	if ($show_as == 'posts')
	{
		$query = array(
			'SELECT'	=> 'p.id AS pid, p.poster AS pposter, p.posted AS pposted, p.poster_id, p.message, p.hide_smilies, t.id AS tid, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.forum_id, f.forum_name',
			'FROM'		=> 'posts AS p',
			'JOINS'		=> array(
				array(
					'INNER JOIN'	=> 'topics AS t',
					'ON'			=> 't.id=p.topic_id'
				),
				array(
					'INNER JOIN'	=> 'forums AS f',
					'ON'			=> 'f.id=t.forum_id'
				)
			),
			'WHERE'		=> 'p.id IN('.$search_results.')',
			'ORDER BY'	=> $sort_by_sql . ' ' . $sort_dir
		);

		($hook = get_hook('sf_fn_generate_cached_search_query_qr_get_cached_hits_as_posts')) ? eval($hook) : null;
	}
	else
	{
		$query = array(
			'SELECT'	=> 't.id AS tid, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name',
			'FROM'		=> 'topics AS t',
			'JOINS'		=> array(
				array(
					'INNER JOIN'	=> 'forums AS f',
					'ON'			=> 'f.id=t.forum_id'
				)
			),
			'WHERE'		=> 't.id IN('.$search_results.')',
			'ORDER BY'	=> $sort_by_sql . ' ' . $sort_dir
		);

		// With "has posted" indication
		if (!$forum_user['is_guest'] && $forum_config['o_show_dot'] == '1')
		{
			$query['SELECT'] .= ', p.poster_id AS has_posted';
			$query['JOINS'][]	= array(
				'LEFT JOIN'		=> 'posts AS p',
				'ON'			=> '(p.poster_id='.$forum_user['id'].' AND p.topic_id=t.id)'
			);

			// Must have same columns as in prev SELECT
			$query['GROUP BY'] = 't.id, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name, p.poster_id';

			($hook = get_hook('sf_fn_generate_cached_search_query_qr_get_has_posted')) ? eval($hook) : null;
		}

		($hook = get_hook('sf_fn_generate_cached_search_query_qr_get_cached_hits_as_topics')) ? eval($hook) : null;
	}

	($hook = get_hook('sf_fn_generate_cached_search_query_end')) ? eval($hook) : null;

	return $query;
}


//
// Generate query to grab the results for an action search (i.e. quicksearch)
//
function generate_action_search_query($action, $value, &$search_id, &$url_type, &$show_as)
{
	global $forum_db, $forum_user, $forum_config, $lang_common, $forum_url, $db_type;

	$return = ($hook = get_hook('sf_fn_generate_action_search_query_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	switch ($action)
	{
		case 'show_new':
			if ($forum_user['is_guest'])
				message($lang_common['No permission']);

			$query = array(
				'SELECT'	=> 't.id AS tid, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name',
				'FROM'		=> 'topics AS t',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'forums AS f',
						'ON'			=> 'f.id=t.forum_id'
					),
					array(
						'LEFT JOIN'		=> 'forum_perms AS fp',
						'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
					)
				),
				'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND t.last_post>'.$forum_user['last_visit'].' AND t.moved_to IS NULL',
				'ORDER BY'	=> 't.last_post DESC'
			);

			if ($value != -1)
				$query['WHERE'] .= ' AND f.id='.$value;

			// With "has posted" indication
			if (!$forum_user['is_guest'] && $forum_config['o_show_dot'] == '1')
			{
				$query['SELECT'] .= ', p.poster_id AS has_posted';
				$query['JOINS'][]	= array(
					'LEFT JOIN'		=> 'posts AS p',
					'ON'			=> '(p.poster_id='.$forum_user['id'].' AND p.topic_id=t.id)'
				);

				// Must have same columns as in prev SELECT
				$query['GROUP BY'] = 't.id, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name, p.poster_id';

				($hook = get_hook('sf_fn_generate_action_search_query_qr_get_new_topics_has_posted')) ? eval($hook) : null;
			}

			$url_type = $forum_url['search_new_results'];
			$search_id = $value;

			($hook = get_hook('sf_fn_generate_action_search_query_qr_get_new')) ? eval($hook) : null;

			break;

		case 'show_recent':
			$query = array(
				'SELECT'	=> 't.id AS tid, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name',
				'FROM'		=> 'topics AS t',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'forums AS f',
						'ON'			=> 'f.id=t.forum_id'
					),
					array(
						'LEFT JOIN'		=> 'forum_perms AS fp',
						'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
					)
				),
				'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND t.last_post>'.(time() - $value).' AND t.moved_to IS NULL',
				'ORDER BY'	=> 't.last_post DESC'
			);

			// With "has posted" indication
			if (!$forum_user['is_guest'] && $forum_config['o_show_dot'] == '1')
			{
				$query['SELECT'] .= ', p.poster_id AS has_posted';
				$query['JOINS'][]	= array(
					'LEFT JOIN'		=> 'posts AS p',
					'ON'			=> '(p.poster_id='.$forum_user['id'].' AND p.topic_id=t.id)'
				);

				// Must have same columns as in prev SELECT
				$query['GROUP BY'] = 't.id, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name, p.poster_id';

				($hook = get_hook('sf_fn_generate_action_search_query_qr_get_recent_topics_has_posted')) ? eval($hook) : null;
			}

			$url_type = $forum_url['search_recent_results'];
			$search_id = $value;

			($hook = get_hook('sf_fn_generate_action_search_query_qr_get_recent')) ? eval($hook) : null;

			break;

		case 'show_user_posts':
			$query = array(
				'SELECT'	=> 'p.id AS pid, p.poster AS pposter, p.posted AS pposted, p.poster_id, p.message, p.hide_smilies, t.id AS tid, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.forum_id, f.forum_name',
				'FROM'		=> 'posts AS p',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'topics AS t',
						'ON'			=> 't.id=p.topic_id'
					),
					array(
						'INNER JOIN'	=> 'forums AS f',
						'ON'			=> 'f.id=t.forum_id'
					),
					array(
						'LEFT JOIN'		=> 'forum_perms AS fp',
						'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
					)
				),
				'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND p.poster_id='.$value,
				'ORDER BY'	=> 'pposted DESC'
			);

			$url_type = $forum_url['search_user_posts'];
			$search_id = $value;
			$show_as = 'posts';

			($hook = get_hook('sf_fn_generate_action_search_query_qr_get_user_posts')) ? eval($hook) : null;

			break;

		case 'show_user_topics':
			$query = array(
				'SELECT'	=> 't.id AS tid, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name',
				'FROM'		=> 'topics AS t',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'posts AS p',
						'ON'			=> 't.first_post_id=p.id'
					),
					array(
						'INNER JOIN'	=> 'forums AS f',
						'ON'			=> 'f.id=t.forum_id'
					),
					array(
						'LEFT JOIN'		=> 'forum_perms AS fp',
						'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
					)
				),
				'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND p.poster_id='.$value,
				'ORDER BY'	=> 't.last_post DESC'
			);

			// With "has posted" indication
			if (!$forum_user['is_guest'] && $forum_config['o_show_dot'] == '1')
			{
				$query['SELECT'] .= ', ps.poster_id AS has_posted';
				$query['JOINS'][]	= array(
					'LEFT JOIN'		=> 'posts AS ps',
					'ON'			=> '(ps.poster_id='.$forum_user['id'].' AND ps.topic_id=t.id)'
				);

				// Must have same columns as in prev SELECT
				$query['GROUP BY'] = 't.id, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name, ps.poster_id';

				($hook = get_hook('sf_fn_generate_action_search_query_qr_get_user_topics_has_posted')) ? eval($hook) : null;
			}

			$url_type = $forum_url['search_user_topics'];
			$search_id = $value;

			($hook = get_hook('sf_fn_generate_action_search_query_qr_get_user_topics')) ? eval($hook) : null;

			break;

		case 'show_subscriptions':
			if ($forum_user['is_guest'])
				message($lang_common['Bad request']);

			// Check we're allowed to see the subscriptions we're trying to look at
			if ($forum_user['g_id'] != FORUM_ADMIN && $forum_user['id'] != $value)
				message($lang_common['Bad request']);

			$query = array(
				'SELECT'	=> 't.id AS tid, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name',
				'FROM'		=> 'topics AS t',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'subscriptions AS s',
						'ON'			=> '(t.id=s.topic_id AND s.user_id='.$value.')'
					),
					array(
						'INNER JOIN'	=> 'forums AS f',
						'ON'			=> 'f.id=t.forum_id'
					),
					array(
						'LEFT JOIN'		=> 'forum_perms AS fp',
						'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
					)
				),
				'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1)',
				'ORDER BY'	=> 't.last_post DESC'
			);

			// With "has posted" indication
			if (!$forum_user['is_guest'] && $forum_config['o_show_dot'] == '1')
			{
				$query['SELECT'] .= ', p.poster_id AS has_posted';
				$query['JOINS'][]	= array(
					'LEFT JOIN'		=> 'posts AS p',
					'ON'			=> '(p.poster_id='.$forum_user['id'].' AND p.topic_id=t.id)'
				);

				// Must have same columns as in prev SELECT
				$query['GROUP BY'] = 't.id, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name, p.poster_id';

				($hook = get_hook('sf_fn_generate_action_search_query_qr_get_subscriptions_has_posted')) ? eval($hook) : null;
			}

			$url_type = $forum_url['search_subscriptions'];
			$search_id = $value;

			($hook = get_hook('sf_fn_generate_action_search_query_qr_get_subscriptions')) ? eval($hook) : null;

			break;

		case 'show_forum_subscriptions':
			if ($forum_user['is_guest'])
				message($lang_common['Bad request']);

			// Check we're allowed to see the subscriptions we're trying to look at
			if ($forum_user['g_id'] != FORUM_ADMIN && $forum_user['id'] != $value)
				message($lang_common['Bad request']);

			$query = array(
				'SELECT'	=> 'c.id AS cid, c.cat_name, f.id AS fid, f.forum_name, f.forum_desc, f.redirect_url, f.moderators, f.num_topics, f.num_posts, f.last_post, f.last_post_id, f.last_poster',
				'FROM'		=> 'categories AS c',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'forums AS f',
						'ON'			=> 'c.id=f.cat_id'
					),
					array(
						'INNER JOIN'	=> 'forum_subscriptions AS fs',
						'ON'			=> '(f.id=fs.forum_id AND fs.user_id='.$value.')'
					),
					array(
						'LEFT JOIN'		=> 'forum_perms AS fp',
						'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
					)
				),
				'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1)',
				'ORDER BY'	=> 'c.disp_position, c.id, f.disp_position'
			);

			$url_type = $forum_url['search_forum_subscriptions'];
			$search_id = $value;

			($hook = get_hook('sf_fn_generate_action_search_query_qr_get_forum_subscriptions')) ? eval($hook) : null;

			break;

		case 'show_unanswered':
			$query = array(
				'SELECT'	=> 't.id AS tid, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name',
				'FROM'		=> 'topics AS t',
				'JOINS'		=> array(
					array(
						'INNER JOIN'	=> 'forums AS f',
						'ON'			=> 'f.id=t.forum_id'
					),
					array(
						'LEFT JOIN'		=> 'forum_perms AS fp',
						'ON'			=> '(fp.forum_id=f.id AND fp.group_id='.$forum_user['g_id'].')'
					)
				),
				'WHERE'		=> '(fp.read_forum IS NULL OR fp.read_forum=1) AND t.num_replies=0 AND t.moved_to IS NULL',
				'ORDER BY'	=> 't.last_post DESC'
			);

			// With "has posted" indication
			if (!$forum_user['is_guest'] && $forum_config['o_show_dot'] == '1')
			{
				$query['SELECT'] .= ', p.poster_id AS has_posted';
				$query['JOINS'][]	= array(
					'LEFT JOIN'		=> 'posts AS p',
					'ON'			=> '(p.poster_id='.$forum_user['id'].' AND p.topic_id=t.id)'
				);

				// Must have same columns as in prev SELECT
				$query['GROUP BY'] = 't.id, t.poster, t.subject, t.first_post_id, t.posted, t.last_post, t.last_post_id, t.last_poster, t.num_replies, t.closed, t.sticky, t.forum_id, f.forum_name, p.poster_id';

				($hook = get_hook('sf_fn_generate_action_search_query_qr_get_unanswered_topics_has_posted')) ? eval($hook) : null;
			}

			$url_type = $forum_url['search_unanswered'];

			($hook = get_hook('sf_fn_generate_action_search_query_qr_get_unanswered')) ? eval($hook) : null;
			break;
	}

	($hook = get_hook('sf_fn_generate_action_search_query_end')) ? eval($hook) : null;

	return $query;
}


//
// Get search results for a specified query, returns number of results
//
function get_search_results($query, &$search_set)
{
	global $forum_db, $forum_user, $forum_page, $lang_common;

	$return = ($hook = get_hook('sf_fn_get_search_results_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$search_results = array();
	while ($row = $forum_db->fetch_assoc($result))
	{
		$search_results[] = $row;
	}

	// Make sure we actually have some results
	$num_hits = count($search_results);
	if ($num_hits == 0)
		return 0;

	// Work out the settings for pagination
	$forum_page['num_pages'] = ($forum_page['per_page'] == 0) ? 1 : ceil($num_hits / $forum_page['per_page']);
	$forum_page['page'] = (!isset($_GET['p']) || !is_numeric($_GET['p']) || $_GET['p'] <= 1 || $_GET['p'] > $forum_page['num_pages']) ? 1 : $_GET['p'];

	// Determine the topic or post offset (based on $forum_page['page'])
	$forum_page['start_from'] = $forum_page['per_page'] * ($forum_page['page'] - 1);
	$forum_page['finish_at'] = ($forum_page['per_page'] == 0) ? $num_hits : min(($forum_page['start_from'] + $forum_page['per_page']), $num_hits);

	// Fill $search_set with out search hits
	$search_set = array();
	$row_num = 0;

	foreach ($search_results as $row)
	{
		if ($forum_page['start_from'] <= $row_num && $forum_page['finish_at'] > $row_num)
			$search_set[] = $row;
		++$row_num;
	}

	$forum_db->free_result($result);

	$return = ($hook = get_hook('sf_fn_get_search_results_end')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	return $num_hits;
}


//
// Output a message if there are no results
//
function no_search_results($action = 'search')
{
	global $forum_page, $lang_search, $forum_url;

	$forum_page['search_again'] = '<a href="'.forum_link($forum_url['search']).'">'.$lang_search['Perform new search'].'</a>';

	$return = ($hook = get_hook('sf_fn_no_search_results_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	switch ($action)
	{
		case 'show_new':
			message($lang_search['No new posts'], $forum_page['search_again'], $lang_search['Topics with new']);
			break;

		case 'show_recent':
			message($lang_search['No recent posts'], $forum_page['search_again'], $lang_search['Recently active topics']);
			break;

		case 'show_user_posts':
			message($lang_search['No user posts'], $forum_page['search_again'], $lang_search['Posts by user']);
			break;

		case 'show_user_topics':
			message($lang_search['No user topics'], $forum_page['search_again'], $lang_search['Topics by user']);
			break;

		case 'show_subscriptions':
			message($lang_search['No subscriptions'], $forum_page['search_again'], $lang_search['Subscriptions']);
			break;

		case 'show_forum_subscriptions':
			message($lang_search['No forum subscriptions'], $forum_page['search_again'], $lang_search['Forum subscriptions']);
			break;

		case 'show_unanswered':
			message($lang_search['No unanswered'], $forum_page['search_again'], $lang_search['Unanswered topics']);
			break;

		default:
			message($lang_search['No hits'], $forum_page['search_again'], $lang_search['Search results']);
			break;
	}
}


//
// Generate search breadcrumbs
//
function generate_search_crumbs($action = null)
{
	global $forum_page, $lang_common, $lang_search, $forum_url, $forum_user, $num_hits, $search_set, $search_id, $show_as;

	$return = ($hook = get_hook('sf_fn_generate_search_crumbs_start')) ? eval($hook) : null;
	if ($return != null)
		return;

	switch ($action)
	{
		case 'show_new':
			$forum_page['crumbs'][] = $lang_search['Topics with new'];
			$forum_page['items_info'] = generate_items_info($lang_search['Topics found'], ($forum_page['start_from'] + 1), $num_hits);
			$forum_page['main_head_options']['defined_search'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search']).'">'.$lang_search['User defined search'].'</a></span>';
			$forum_page['main_foot_options']['mark_all'] = '<span'.(empty($forum_page['main_foot_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['mark_read'], generate_form_token('markread'.$forum_user['id'])).'">'.$lang_common['Mark all as read'].'</a></span>';

			// Add link for show all topics, not only new (updated)
			if ($search_id != -1)
				$forum_page['main_head_options']['show_all'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['forum'], $search_set[0]['forum_id']).'">'.$lang_search['All Topics'].'</a></span>';

			break;

		case 'show_recent':
			$forum_page['crumbs'][] = $lang_search['Recently active topics'];
			$forum_page['items_info'] = generate_items_info($lang_search['Topics found'], ($forum_page['start_from'] + 1), $num_hits);
			$forum_page['main_head_options']['defined_search'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search']).'">'.$lang_search['User defined search'].'</a></span>';
			break;

		case 'show_unanswered':
			$forum_page['crumbs'][] = $lang_search['Unanswered topics'];
			$forum_page['items_info'] = generate_items_info($lang_search['Topics found'], ($forum_page['start_from'] + 1), $num_hits);
			$forum_page['main_head_options']['defined_search'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search']).'">'.$lang_search['User defined search'].'</a></span>';
			break;

		case 'show_user_posts':
			$forum_page['crumbs'][] = sprintf($lang_search['Posts by'], $search_set[0]['pposter'], ($forum_page['start_from'] + 1), $num_hits);
			$forum_page['items_info'] = generate_items_info($lang_search['Posts found'], ($forum_page['start_from'] + 1), $num_hits);
			$forum_page['main_head_options']['user_topics'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search_user_topics'], $search_id).'">'.sprintf($lang_search['Topics by'], forum_htmlencode($search_set[0]['pposter'])).'</a></span>';
			$forum_page['main_head_options']['defined_search'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search']).'">'.$lang_search['User defined search'].'</a></span>';
			break;

		case 'show_user_topics':
			$forum_page['crumbs'][] = sprintf($lang_search['Topics by'], $search_set[0]['poster']);
			$forum_page['items_info'] = generate_items_info($lang_search['Topics found'], ($forum_page['start_from'] + 1), $num_hits);
			$forum_page['main_head_options']['user_posts'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search_user_posts'], $search_id).'">'.sprintf($lang_search['Posts by'], forum_htmlencode($search_set[0]['poster'])).'</a></span>';
			$forum_page['main_head_options']['defined_search'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search']).'">'.$lang_search['User defined search'].'</a></span>';
			break;

		case 'show_subscriptions':
			$forum_page['crumbs'][] = $lang_search['Subscriptions'];
			$forum_page['items_info'] = generate_items_info($lang_search['Topics found'], ($forum_page['start_from'] + 1), $num_hits);
			$forum_page['main_head_options']['defined_search'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search']).'">'.$lang_search['User defined search'].'</a></span>';
			break;

		case 'show_forum_subscriptions':
			$forum_page['crumbs'][] = $lang_search['Forum subscriptions'];
			$forum_page['items_info'] = generate_items_info($lang_search['Forums found'], ($forum_page['start_from'] + 1), $num_hits);
			$forum_page['main_head_options']['defined_search'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search']).'">'.$lang_search['User defined search'].'</a></span>';
			break;

		default:
			$forum_page['crumbs'][] = $lang_search['Search results'];
			$forum_page['items_info'] = generate_items_info((($show_as == 'topics') ? $lang_search['Topics found'] : $lang_search['Posts found']), ($forum_page['start_from'] + 1), $num_hits);
			$forum_page['main_head_options']['new_search'] = '<span'.(empty($forum_page['main_head_options']) ? ' class="first-item"' : '').'><a href="'.forum_link($forum_url['search']).'">'.$lang_search['Perform new search'].'</a></span>';
			break;
	}
}


//
// Checks to see if an action is valid
//
function validate_search_action($action)
{
	// A list of valid actions (extensions can add their own actions to the array)
	$valid_actions = array('search', 'show_new', 'show_recent', 'show_user_posts', 'show_user_topics', 'show_subscriptions', 'show_forum_subscriptions', 'show_unanswered');

	$return = ($hook = get_hook('sf_fn_validate_actions_start')) ? eval($hook) : null;
	if ($return != null)
		return $return;

	return in_array($action, $valid_actions);
}
