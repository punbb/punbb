<?php

$forum_loader->add_css('style/Oxygen/min/Oxygen.min.css', array('type' => 'file', 'group' => FORUM_CSS_GROUP_SYSTEM, 'media' => 'screen'));

//$forum_loader->add_css('style/Oxygen/Oxygen.css', array('type' => 'file', 'group' => FORUM_CSS_GROUP_SYSTEM, 'media' => 'screen'));
//$forum_loader->add_css('style/Oxygen/Oxygen_mobile.css', array('type' => 'file', 'group' => FORUM_CSS_GROUP_SYSTEM, 'media' => 'screen'));
//$forum_loader->add_css('style/Oxygen/Oxygen_cs.css', array('type' => 'file', 'group' => FORUM_CSS_GROUP_SYSTEM, 'media' => 'screen'));


// IE
$forum_loader->add_css('style/Oxygen/min/Oxygen_ie6.min.css', array('type' => 'file', 'group' => FORUM_CSS_GROUP_SYSTEM, 'media' => 'screen', 'browsers' => array('IE' => 'lte IE 6', '!IE' => false)));
$forum_loader->add_css('style/Oxygen/min/Oxygen_ie7.min.css', array('type' => 'file', 'group' => FORUM_CSS_GROUP_SYSTEM, 'media' => 'screen', 'browsers' => array('IE' => 'IE 7', '!IE' => false)));
$forum_loader->add_css('style/Oxygen/min/Oxygen_ie8.min.css', array('type' => 'file', 'group' => FORUM_CSS_GROUP_SYSTEM, 'media' => 'screen', 'browsers' => array('IE' => 'IE 8', '!IE' => false)));

?>
