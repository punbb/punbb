<?

/***********************************************************************

	Copyright (C) 2008  PunBB

	PunBB is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published
	by the Free Software Foundation; either version 2 of the License,
	or (at your option) any later version.

	PunBB is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston,
	MA  02111-1307  USA

***********************************************************************/

if (!defined('FORUM')) die();

function plus_mark_to_post( $post_id )
{
	global $forum_db, $forum_user;

	//Check if post_id exists
	$query = array(
		'SELECT'	=> 'id',
		'FROM'		=> 'posts',
		'WHERE'		=> 'id = '.$post_id
	);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$pid = $forum_db->fetch_row($result);
	if (!$pid)
		return false;

	//Check if user tries to vote for his own post
	$query = array(
		'SELECT'	=> 'id',
		'FROM'		=> 'posts',
		'WHERE'		=> 'poster_id = '.$forum_user['id'].' AND id = '.$post_id
	);

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$pid = $forum_db->fetch_row($result);

	if ($pid[0] > 0)
		return false;

	$query = array(
		'SELECT'	=> '1',
		'FROM'		=> 'pun_karma',
		'WHERE'		=> 'user_id = '.$forum_user['id'].' AND post_id = '.$post_id
	);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if (!$forum_db->num_rows($result))
		$query = array(
			'INSERT'		=> 'user_id, post_id, mark',
			'INTO'			=> 'pun_karma',
			'VALUES'		=> $forum_user['id'].', '.$post_id.', 1'
		);
	else
		$query = array(
			'UPDATE'		=> 'pun_karma',
			'SET'			=> 'mark = 1',
			'WHERE'			=> 'user_id = '.$forum_user['id'].' AND post_id = '.$post_id
		);

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	return true;
}

function minus_mark_to_post( $post_id )
{
	global $forum_db, $forum_user;

	//Check if post_id exists
	$query = array(
		'SELECT'	=> 'id',
		'FROM'		=> 'posts',
		'WHERE'		=> 'id = '.$post_id
	);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$pid = $forum_db->fetch_row($result);
	if (!$pid)
		return false;

	//Check if user tries to vote for his own post
	$query = array(
		'SELECT'	=> 'id',
		'FROM'		=> 'posts',
		'WHERE'		=> 'poster_id = '.$forum_user['id'].' AND id = '.$post_id
	);

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$pid = $forum_db->fetch_row($result);

	if ($pid[0] > 0)
		return false;

	$query = array(
		'SELECT'	=> '1',
		'FROM'		=> 'pun_karma',
		'WHERE'		=> 'user_id = '.$forum_user['id'].' AND post_id = '.$post_id
	);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if (!$forum_db->num_rows($result))
		$query = array(
			'INSERT'		=> 'user_id, post_id, mark',
			'INTO'			=> 'pun_karma',
			'VALUES'		=> $forum_user['id'].', '.$post_id.', -1'
		);
	else
		$query = array(
			'UPDATE'		=> 'pun_karma',
			'SET'			=> 'mark = -1',
			'WHERE'			=> 'user_id = '.$forum_user['id'].' AND post_id = '.$post_id
		);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	return true;
};

function cancel_mark_to_post( $post_id )
{
	global $forum_db, $forum_user;

	//Check if post_id exists
	$query = array(
		'SELECT'	=> 'id',
		'FROM'		=> 'posts',
		'WHERE'		=> 'id = '.$post_id
	);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	$pid = $forum_db->fetch_row($result);
	if (!$pid)
		return false;

	//Check if user tries to vote for his own post
	$query = array(
		'SELECT'	=> 'id',
		'FROM'		=> 'posts',
		'WHERE'		=> 'poster_id = '.$forum_user['id'].' AND id = '.$post_id
	);

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	$pid = $forum_db->fetch_row($result);

	if ($pid[0] > 0)
		return false;

	$query = array(
		'SELECT'	=> '1',
		'FROM'		=> 'pun_karma',
		'WHERE'		=> 'user_id = '.$forum_user['id'].' AND post_id = '.$post_id
	);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	if (!$forum_db->num_rows($result))
		return false;

	$query = array(
		'DELETE'		=> 'pun_karma',
		'WHERE'			=> 'user_id = '.$forum_user['id'].' AND post_id = '.$post_id
	);
	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);

	return true;
}

function post_mark( $post_id )
{
	global $forum_db;

	$query = array(
		'SELECT'	=> 'SUM(mark)',
		'FROM'		=> 'pun_karma',
		'WHERE'		=> 'post_id = '.$post_id,
		'GROUP BY'	=> 'post_id'
	);

	$result = $forum_db->query_build($query) or error(__FILE__, __LINE__);
	if ($forum_db->num_rows($result))
	{
		list($sum) = $forum_db->fetch_row($result);
		return $sum;
	}
	else
		return 0;
}

function delete_post_karma( $post_id )
{
	global $forum_db;

	// Delete the post from pun_karma
	$query = array(
		'DELETE'	=> 'pun_karma',
		'WHERE'		=> 'post_id = '.$post_id
	);

	$forum_db->query_build($query) or error(__FILE__, __LINE__);
}

function delete_topic_karma( $topic_id )
{
	global $forum_db;

	$qdelete = 'DELETE FROM '.$forum_db->prefix.'pun_karma
		USING '.$forum_db->prefix.'pun_karma, '.$forum_db->prefix.'posts
		WHERE '.$forum_db->prefix.'posts.topic_id = '.$topic_id.'
			AND '.$forum_db->prefix.'pun_karma.post_id = '.$forum_db->prefix.'posts.id';

	$forum_db->query( $qdelete ) or error(__FILE__, __LINE__);
}

?>
