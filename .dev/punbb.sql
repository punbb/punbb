-- Adminer 4.2.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `punbb_bans`;
CREATE TABLE `punbb_bans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `expire` int(10) unsigned DEFAULT NULL,
  `ban_creator` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `punbb_categories`;
CREATE TABLE `punbb_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(80) NOT NULL DEFAULT 'New Category',
  `disp_position` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `punbb_categories` (`id`, `cat_name`, `disp_position`) VALUES
(1,	'Test category',	1),
(2,	'Category 111',	0);

DROP TABLE IF EXISTS `punbb_censoring`;
CREATE TABLE `punbb_censoring` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `search_for` varchar(60) NOT NULL DEFAULT '',
  `replace_with` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `punbb_config`;
CREATE TABLE `punbb_config` (
  `conf_name` varchar(255) NOT NULL DEFAULT '',
  `conf_value` text,
  PRIMARY KEY (`conf_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `punbb_config` (`conf_name`, `conf_value`) VALUES
('o_additional_navlinks',	''),
('o_admin_email',	'admin@example.org'),
('o_announcement',	'0'),
('o_announcement_heading',	'Sample announcement'),
('o_announcement_message',	'<p>Enter your announcement here.</p>'),
('o_avatars',	'1'),
('o_avatars_dir',	'img/avatars'),
('o_avatars_height',	'60'),
('o_avatars_size',	'15360'),
('o_avatars_width',	'60'),
('o_board_desc',	'Unfortunately no one can be told what PunBB is — you have to see it for yourself'),
('o_board_title',	'My PunBB forum'),
('o_censoring',	'0'),
('o_check_for_updates',	'0'),
('o_check_for_versions',	'0'),
('o_cur_version',	'1.4.3'),
('o_database_revision',	'5'),
('o_date_format',	'Y-m-d'),
('o_default_dst',	'0'),
('o_default_email_setting',	'1'),
('o_default_lang',	'Russian'),
('o_default_style',	'Oxygen'),
('o_default_timezone',	'0'),
('o_default_user_group',	'3'),
('o_disp_posts_default',	'25'),
('o_disp_topics_default',	'30'),
('o_gzip',	'0'),
('o_indent_num_spaces',	'4'),
('o_mailing_list',	'admin@example.org'),
('o_maintenance',	'0'),
('o_maintenance_message',	'The forums are temporarily down for maintenance. Please try again in a few minutes.<br /><br />Administrator'),
('o_make_links',	'1'),
('o_mask_passwords',	'1'),
('o_quickjump',	'1'),
('o_quickpost',	'1'),
('o_quote_depth',	'3'),
('o_ranks',	'1'),
('o_redirect_delay',	'0'),
('o_regs_allow',	'1'),
('o_regs_report',	'0'),
('o_regs_verify',	'0'),
('o_report_method',	'0'),
('o_rules',	'0'),
('o_rules_message',	'Enter your rules here.'),
('o_search_all_forums',	'1'),
('o_sef',	'Default'),
('o_show_dot',	'0'),
('o_show_moderators',	'0'),
('o_show_post_count',	'1'),
('o_show_user_info',	'1'),
('o_show_version',	'0'),
('o_signatures',	'1'),
('o_smilies',	'1'),
('o_smilies_sig',	'1'),
('o_smtp_host',	NULL),
('o_smtp_pass',	NULL),
('o_smtp_ssl',	'0'),
('o_smtp_user',	NULL),
('o_subscriptions',	'1'),
('o_timeout_online',	'300'),
('o_timeout_visit',	'5400'),
('o_time_format',	'H:i:s'),
('o_topic_review',	'15'),
('o_topic_views',	'1'),
('o_users_online',	'1'),
('o_webmaster_email',	'admin@example.org'),
('p_allow_banned_email',	'1'),
('p_allow_dupe_email',	'0'),
('p_force_guest_email',	'1'),
('p_message_all_caps',	'1'),
('p_message_bbcode',	'1'),
('p_message_img_tag',	'1'),
('p_sig_all_caps',	'1'),
('p_sig_bbcode',	'1'),
('p_sig_img_tag',	'0'),
('p_sig_length',	'400'),
('p_sig_lines',	'4'),
('p_subject_all_caps',	'1');

DROP TABLE IF EXISTS `punbb_extensions`;
CREATE TABLE `punbb_extensions` (
  `id` varchar(150) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `version` varchar(25) NOT NULL DEFAULT '',
  `description` text,
  `author` varchar(50) NOT NULL DEFAULT '',
  `uninstall` text,
  `uninstall_note` text,
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `dependencies` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `punbb_extension_hooks`;
CREATE TABLE `punbb_extension_hooks` (
  `id` varchar(150) NOT NULL DEFAULT '',
  `extension_id` varchar(50) NOT NULL DEFAULT '',
  `code` text,
  `installed` int(10) unsigned NOT NULL DEFAULT '0',
  `priority` tinyint(1) unsigned NOT NULL DEFAULT '5',
  PRIMARY KEY (`id`,`extension_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `punbb_forums`;
CREATE TABLE `punbb_forums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum_name` varchar(80) NOT NULL DEFAULT 'New forum',
  `forum_desc` text,
  `redirect_url` varchar(100) DEFAULT NULL,
  `moderators` text,
  `num_topics` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `num_posts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `last_post` int(10) unsigned DEFAULT NULL,
  `last_post_id` int(10) unsigned DEFAULT NULL,
  `last_poster` varchar(200) DEFAULT NULL,
  `sort_by` tinyint(1) NOT NULL DEFAULT '0',
  `disp_position` int(10) NOT NULL DEFAULT '0',
  `cat_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `punbb_forums` (`id`, `forum_name`, `forum_desc`, `redirect_url`, `moderators`, `num_topics`, `num_posts`, `last_post`, `last_post_id`, `last_poster`, `sort_by`, `disp_position`, `cat_id`) VALUES
(1,	'Test forum',	'This is just a test forum',	NULL,	NULL,	1,	2,	1427444994,	2,	'admin',	0,	1,	1),
(2,	'Test New Forum',	'111',	NULL,	NULL,	0,	0,	NULL,	NULL,	NULL,	0,	0,	1);

DROP TABLE IF EXISTS `punbb_forum_perms`;
CREATE TABLE `punbb_forum_perms` (
  `group_id` int(10) NOT NULL DEFAULT '0',
  `forum_id` int(10) NOT NULL DEFAULT '0',
  `read_forum` tinyint(1) NOT NULL DEFAULT '1',
  `post_replies` tinyint(1) NOT NULL DEFAULT '1',
  `post_topics` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`group_id`,`forum_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `punbb_forum_subscriptions`;
CREATE TABLE `punbb_forum_subscriptions` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`forum_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `punbb_groups`;
CREATE TABLE `punbb_groups` (
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `g_title` varchar(50) NOT NULL DEFAULT '',
  `g_user_title` varchar(50) DEFAULT NULL,
  `g_moderator` tinyint(1) NOT NULL DEFAULT '0',
  `g_mod_edit_users` tinyint(1) NOT NULL DEFAULT '0',
  `g_mod_rename_users` tinyint(1) NOT NULL DEFAULT '0',
  `g_mod_change_passwords` tinyint(1) NOT NULL DEFAULT '0',
  `g_mod_ban_users` tinyint(1) NOT NULL DEFAULT '0',
  `g_read_board` tinyint(1) NOT NULL DEFAULT '1',
  `g_view_users` tinyint(1) NOT NULL DEFAULT '1',
  `g_post_replies` tinyint(1) NOT NULL DEFAULT '1',
  `g_post_topics` tinyint(1) NOT NULL DEFAULT '1',
  `g_edit_posts` tinyint(1) NOT NULL DEFAULT '1',
  `g_delete_posts` tinyint(1) NOT NULL DEFAULT '1',
  `g_delete_topics` tinyint(1) NOT NULL DEFAULT '1',
  `g_set_title` tinyint(1) NOT NULL DEFAULT '1',
  `g_search` tinyint(1) NOT NULL DEFAULT '1',
  `g_search_users` tinyint(1) NOT NULL DEFAULT '1',
  `g_send_email` tinyint(1) NOT NULL DEFAULT '1',
  `g_post_flood` smallint(6) NOT NULL DEFAULT '30',
  `g_search_flood` smallint(6) NOT NULL DEFAULT '30',
  `g_email_flood` smallint(6) NOT NULL DEFAULT '60',
  PRIMARY KEY (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `punbb_groups` (`g_id`, `g_title`, `g_user_title`, `g_moderator`, `g_mod_edit_users`, `g_mod_rename_users`, `g_mod_change_passwords`, `g_mod_ban_users`, `g_read_board`, `g_view_users`, `g_post_replies`, `g_post_topics`, `g_edit_posts`, `g_delete_posts`, `g_delete_topics`, `g_set_title`, `g_search`, `g_search_users`, `g_send_email`, `g_post_flood`, `g_search_flood`, `g_email_flood`) VALUES
(1,	'Administrators',	'Administrator',	0,	0,	0,	0,	0,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	0,	0,	0),
(2,	'Guest',	NULL,	0,	0,	0,	0,	0,	1,	1,	0,	0,	0,	0,	0,	0,	1,	1,	0,	60,	30,	0),
(3,	'Members',	NULL,	0,	0,	0,	0,	0,	1,	1,	1,	1,	1,	1,	1,	0,	1,	1,	1,	60,	30,	60),
(4,	'Moderators',	'Moderator',	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	1,	0,	0,	0);

DROP TABLE IF EXISTS `punbb_online`;
CREATE TABLE `punbb_online` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '1',
  `ident` varchar(200) NOT NULL DEFAULT '',
  `logged` int(10) unsigned NOT NULL DEFAULT '0',
  `idle` tinyint(1) NOT NULL DEFAULT '0',
  `csrf_token` varchar(40) NOT NULL DEFAULT '',
  `prev_url` varchar(255) DEFAULT NULL,
  `last_post` int(10) unsigned DEFAULT NULL,
  `last_search` int(10) unsigned DEFAULT NULL,
  UNIQUE KEY `punbb_online_user_id_ident_idx` (`user_id`,`ident`(25)),
  KEY `punbb_online_ident_idx` (`ident`(25)),
  KEY `punbb_online_logged_idx` (`logged`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

INSERT INTO `punbb_online` (`user_id`, `ident`, `logged`, `idle`, `csrf_token`, `prev_url`, `last_post`, `last_search`) VALUES
(1,	'127.0.0.1',	1430388153,	0,	'd1667d93246612c28c28ff447de91fd1f87df09f',	'http://user.punbb/',	NULL,	NULL);

DROP TABLE IF EXISTS `punbb_posts`;
CREATE TABLE `punbb_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poster` varchar(200) NOT NULL DEFAULT '',
  `poster_id` int(10) unsigned NOT NULL DEFAULT '1',
  `poster_ip` varchar(39) DEFAULT NULL,
  `poster_email` varchar(80) DEFAULT NULL,
  `message` text,
  `hide_smilies` tinyint(1) NOT NULL DEFAULT '0',
  `posted` int(10) unsigned NOT NULL DEFAULT '0',
  `edited` int(10) unsigned DEFAULT NULL,
  `edited_by` varchar(200) DEFAULT NULL,
  `topic_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `punbb_posts_topic_id_idx` (`topic_id`),
  KEY `punbb_posts_multi_idx` (`poster_id`,`topic_id`),
  KEY `punbb_posts_posted_idx` (`posted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `punbb_posts` (`id`, `poster`, `poster_id`, `poster_ip`, `poster_email`, `message`, `hide_smilies`, `posted`, `edited`, `edited_by`, `topic_id`) VALUES
(1,	'admin',	2,	'127.0.0.1',	NULL,	'If you are looking at this (which I guess you are), the install of PunBB appears to have worked! Now log in and head over to the administration control panel to configure your forum.\nTest ok',	0,	1427349755,	NULL,	NULL,	1),
(2,	'admin',	2,	'127.0.0.1',	NULL,	'test111',	0,	1427444994,	NULL,	NULL,	1);

DROP TABLE IF EXISTS `punbb_ranks`;
CREATE TABLE `punbb_ranks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rank` varchar(50) NOT NULL DEFAULT '',
  `min_posts` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `punbb_ranks` (`id`, `rank`, `min_posts`) VALUES
(1,	'New member',	0),
(2,	'Member',	10);

DROP TABLE IF EXISTS `punbb_reports`;
CREATE TABLE `punbb_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_id` int(10) unsigned NOT NULL DEFAULT '0',
  `forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  `reported_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text,
  `zapped` int(10) unsigned DEFAULT NULL,
  `zapped_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `punbb_reports_zapped_idx` (`zapped`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `punbb_search_cache`;
CREATE TABLE `punbb_search_cache` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `ident` varchar(200) NOT NULL DEFAULT '',
  `search_data` text,
  PRIMARY KEY (`id`),
  KEY `punbb_search_cache_ident_idx` (`ident`(8))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `punbb_search_matches`;
CREATE TABLE `punbb_search_matches` (
  `post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `word_id` int(10) unsigned NOT NULL DEFAULT '0',
  `subject_match` tinyint(1) NOT NULL DEFAULT '0',
  KEY `punbb_search_matches_word_id_idx` (`word_id`),
  KEY `punbb_search_matches_post_id_idx` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `punbb_search_matches` (`post_id`, `word_id`, `subject_match`) VALUES
(1,	3,	0),
(1,	6,	0),
(1,	8,	0),
(1,	9,	0),
(1,	10,	0),
(1,	12,	0),
(1,	14,	0),
(1,	15,	0),
(1,	16,	0),
(1,	18,	0),
(1,	19,	0),
(1,	20,	0),
(1,	21,	0),
(1,	23,	0),
(1,	25,	1),
(1,	24,	1),
(2,	24,	0),
(1,	24,	0);

DROP TABLE IF EXISTS `punbb_search_words`;
CREATE TABLE `punbb_search_words` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  PRIMARY KEY (`word`),
  KEY `punbb_search_words_id_idx` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `punbb_search_words` (`id`, `word`) VALUES
(1,	'you'),
(2,	'are'),
(3,	'looking'),
(4,	'this'),
(5,	'which'),
(6,	'guess'),
(7,	'the'),
(8,	'install'),
(9,	'punbb'),
(10,	'appears'),
(11,	'have'),
(12,	'worked'),
(13,	'now'),
(14,	'log'),
(15,	'and'),
(16,	'head'),
(17,	'over'),
(18,	'administration'),
(19,	'control'),
(20,	'panel'),
(21,	'configure'),
(22,	'your'),
(23,	'forum'),
(24,	'test'),
(25,	'post'),
(26,	'111');

DROP TABLE IF EXISTS `punbb_subscriptions`;
CREATE TABLE `punbb_subscriptions` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `punbb_topics`;
CREATE TABLE `punbb_topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poster` varchar(200) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `posted` int(10) unsigned NOT NULL DEFAULT '0',
  `first_post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `last_post` int(10) unsigned NOT NULL DEFAULT '0',
  `last_post_id` int(10) unsigned NOT NULL DEFAULT '0',
  `last_poster` varchar(200) DEFAULT NULL,
  `num_views` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `num_replies` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `sticky` tinyint(1) NOT NULL DEFAULT '0',
  `moved_to` int(10) unsigned DEFAULT NULL,
  `forum_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `punbb_topics_forum_id_idx` (`forum_id`),
  KEY `punbb_topics_moved_to_idx` (`moved_to`),
  KEY `punbb_topics_last_post_idx` (`last_post`),
  KEY `punbb_topics_first_post_id_idx` (`first_post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `punbb_topics` (`id`, `poster`, `subject`, `posted`, `first_post_id`, `last_post`, `last_post_id`, `last_poster`, `num_views`, `num_replies`, `closed`, `sticky`, `moved_to`, `forum_id`) VALUES
(1,	'admin',	'Test post',	1427349755,	1,	1427444994,	2,	'admin',	128,	1,	0,	0,	NULL,	1);

DROP TABLE IF EXISTS `punbb_users`;
CREATE TABLE `punbb_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL DEFAULT '3',
  `username` varchar(200) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `salt` varchar(12) DEFAULT NULL,
  `email` varchar(80) NOT NULL DEFAULT '',
  `title` varchar(50) DEFAULT NULL,
  `realname` varchar(40) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `twitter` varchar(100) DEFAULT NULL,
  `linkedin` varchar(100) DEFAULT NULL,
  `skype` varchar(100) DEFAULT NULL,
  `jabber` varchar(80) DEFAULT NULL,
  `icq` varchar(12) DEFAULT NULL,
  `msn` varchar(80) DEFAULT NULL,
  `aim` varchar(30) DEFAULT NULL,
  `yahoo` varchar(30) DEFAULT NULL,
  `location` varchar(30) DEFAULT NULL,
  `signature` text,
  `disp_topics` tinyint(3) unsigned DEFAULT NULL,
  `disp_posts` tinyint(3) unsigned DEFAULT NULL,
  `email_setting` tinyint(1) NOT NULL DEFAULT '1',
  `notify_with_post` tinyint(1) NOT NULL DEFAULT '0',
  `auto_notify` tinyint(1) NOT NULL DEFAULT '0',
  `show_smilies` tinyint(1) NOT NULL DEFAULT '1',
  `show_img` tinyint(1) NOT NULL DEFAULT '1',
  `show_img_sig` tinyint(1) NOT NULL DEFAULT '1',
  `show_avatars` tinyint(1) NOT NULL DEFAULT '1',
  `show_sig` tinyint(1) NOT NULL DEFAULT '1',
  `access_keys` tinyint(1) NOT NULL DEFAULT '0',
  `timezone` float NOT NULL DEFAULT '0',
  `dst` tinyint(1) NOT NULL DEFAULT '0',
  `time_format` int(10) unsigned NOT NULL DEFAULT '0',
  `date_format` int(10) unsigned NOT NULL DEFAULT '0',
  `language` varchar(25) NOT NULL DEFAULT 'English',
  `style` varchar(25) NOT NULL DEFAULT 'Oxygen',
  `num_posts` int(10) unsigned NOT NULL DEFAULT '0',
  `last_post` int(10) unsigned DEFAULT NULL,
  `last_search` int(10) unsigned DEFAULT NULL,
  `last_email_sent` int(10) unsigned DEFAULT NULL,
  `registered` int(10) unsigned NOT NULL DEFAULT '0',
  `registration_ip` varchar(39) NOT NULL DEFAULT '0.0.0.0',
  `last_visit` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_note` varchar(30) DEFAULT NULL,
  `activate_string` varchar(80) DEFAULT NULL,
  `activate_key` varchar(8) DEFAULT NULL,
  `avatar` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `avatar_width` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `avatar_height` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `punbb_users_registered_idx` (`registered`),
  KEY `punbb_users_username_idx` (`username`(8))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `punbb_users` (`id`, `group_id`, `username`, `password`, `salt`, `email`, `title`, `realname`, `url`, `facebook`, `twitter`, `linkedin`, `skype`, `jabber`, `icq`, `msn`, `aim`, `yahoo`, `location`, `signature`, `disp_topics`, `disp_posts`, `email_setting`, `notify_with_post`, `auto_notify`, `show_smilies`, `show_img`, `show_img_sig`, `show_avatars`, `show_sig`, `access_keys`, `timezone`, `dst`, `time_format`, `date_format`, `language`, `style`, `num_posts`, `last_post`, `last_search`, `last_email_sent`, `registered`, `registration_ip`, `last_visit`, `admin_note`, `activate_string`, `activate_key`, `avatar`, `avatar_width`, `avatar_height`) VALUES
(1,	2,	'Guest',	'Guest',	NULL,	'Guest',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	0,	0,	1,	1,	1,	1,	1,	0,	0,	0,	0,	0,	'English',	'Oxygen',	0,	NULL,	NULL,	NULL,	0,	'0.0.0.0',	0,	NULL,	NULL,	NULL,	0,	0,	0),
(2,	1,	'admin',	'5b183f489ed256f0da95bc081719fdeafb1927c8',	'Jo*W[%C[<iQZ',	'admin@example.org',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	0,	0,	1,	1,	0,	1,	1,	0,	0,	0,	0,	0,	'Russian',	'Oxygen',	2,	1427444994,	1428565885,	NULL,	1427349755,	'127.0.0.1',	1429957610,	NULL,	NULL,	NULL,	0,	0,	0);

-- 2015-04-30 10:06:03
