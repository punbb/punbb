<?php

class GenerateAvatarMarkupTest extends PHPUnit_Framework_TestCase {
    public function testGenerateAvatarMarkup() {
        global $forum_config, $base_url;

        $this->assertEquals('<img src="'.$base_url.'/'.$forum_config['o_avatars_dir'].'/1.gif" width="60" height="60" alt="" />', generate_avatar_markup(1, FORUM_AVATAR_GIF, 60, 60));
        $this->assertEquals('<img src="'.$base_url.'/'.$forum_config['o_avatars_dir'].'/5.png" width="60" height="70" alt="" />', generate_avatar_markup(5, FORUM_AVATAR_PNG, 60, 70));
        $this->assertEquals('<img src="'.$base_url.'/'.$forum_config['o_avatars_dir'].'/5.jpg" width="60" height="70" alt="" />', generate_avatar_markup(5, FORUM_AVATAR_JPG, 60, 70));

        $this->assertEquals('', generate_avatar_markup(5, FORUM_AVATAR_NONE, 60, 70));
        $this->assertEquals('', generate_avatar_markup(5, FORUM_AVATAR_NONE, 60, 70, TRUE));
        $this->assertEquals('', generate_avatar_markup(5, FORUM_AVATAR_PNG, 0, 70, TRUE));

        $this->assertEquals('<img src="'.$base_url.'/'.$forum_config['o_avatars_dir'].'/1.gif" width="60" height="60" alt="ami" />', generate_avatar_markup(1, FORUM_AVATAR_GIF, 60, 60, 'ami'));
        $this->assertEquals('<img src="'.$base_url.'/'.$forum_config['o_avatars_dir'].'/1.gif" width="60" height="60" alt="ami&gt;" />', generate_avatar_markup(1, FORUM_AVATAR_GIF, 60, 60, 'ami>'));
        $this->assertEquals('<img src="'.$base_url.'/'.$forum_config['o_avatars_dir'].'/1.gif" width="60" height="60" alt="ami&lt;script&gt;alert(1)&lt;/script&gt;" />', generate_avatar_markup(1, FORUM_AVATAR_GIF, 60, 60, 'ami<script>alert(1)</script>'));
    }
}
