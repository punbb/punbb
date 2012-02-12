<?php

    if (!defined('FORUM_QUIET_VISIT')) {
        define('FORUM_QUIET_VISIT', 1);
    }

    define('FORUM_ROOT', __DIR__ . '/../../../');

    require_once FORUM_ROOT . 'include/essentials.php';
    require_once FORUM_ROOT . 'lang/English/common.php';
    require_once FORUM_ROOT . 'include/parser.php';

    if (!defined('FORUM_DEBUG')) {
        define('FORUM_DEBUG', 1);
    }

    forum_remove_bad_characters();
