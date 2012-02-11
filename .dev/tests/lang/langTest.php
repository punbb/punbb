<?php

class LangTest {
    private $langElements;
    private $languageData;
    private $installedLangs;

    public function run() {
        $this->setupLangElements();
        $this->findInstalledLangs();

        if (sizeof($this->installedLangs) < 2) {
            error('No additional locales installed.');
        }

        $this->fillLangElements();
        $this->compareLangElements();
    }


    private function setupLangElements() {
        $this->langElements = array(
            'lang_admin_bans'       => 'admin_bans.php',
            'lang_admin_categories' => 'admin_categories.php',
            'lang_admin_censoring'  => 'admin_censoring.php',
            'lang_admin_common'     => 'admin_common.php',
            'lang_admin_ext'        => 'admin_ext.php',
            'lang_admin_forums'     => 'admin_forums.php',
            'lang_admin_groups'     => 'admin_groups.php',
            'lang_admin_index'      => 'admin_index.php',
            'lang_admin_prune'      => 'admin_prune.php',
            'lang_admin_ranks'      => 'admin_ranks.php',
            'lang_admin_reindex'    => 'admin_reindex.php',
            'lang_admin_reports'    => 'admin_reports.php',
            'lang_admin_settings'   => 'admin_settings.php',
            'lang_admin_users'      => 'admin_users.php',
            'lang_common'           => 'common.php',
            'lang_delete'           => 'delete.php',
            'lang_forum'            => 'forum.php',
            'lang_help'             => 'help.php',
            'lang_index'            => 'index.php',
            'lang_install'          => 'install.php',
            'lang_login'            => 'login.php',
            'lang_misc'             => 'misc.php',
            'lang_post'             => 'post.php',
            'lang_profile'          => 'profile.php',
            'lang_search'           => 'search.php',
            'lang_topic'            => 'topic.php',
            'lang_url_replace'      => 'url_replace.php',
            'lang_ul'               => 'userlist.php'
        );
    }

    private function findInstalledLangs() {
        $this->installedLangs = array('English');

        $dirs = dir(FORUM_ROOT.'lang');
        if ($dirs !== FALSE) {
            while (($dir = $dirs->read()) !== FALSE) {
                if ($dir == '.' || $dir == '..' || $dir == 'English') {
                    continue;
                }

                if (is_dir(FORUM_ROOT.'lang/'.$dir) && file_exists(FORUM_ROOT.'lang/'.$dir.'/common.php')) {
                    array_push($this->installedLangs, $dir);
                }
            }
            $dirs->close();
        }
    }

    private function fillLangElements() {
        foreach ($this->installedLangs as $language) {
            $this->languageData[$language] = array();

            foreach ($this->langElements as $langName => $langFile) {
                $this->languageData[$language][$langName] = array('file' => $langFile, 'data' => array());

                if (!file_exists(FORUM_ROOT.'lang/'.$language.'/'.$langFile)) {
                ?>
                    <div class="ct-box error-box">
                        <h2 class="warn hn">
                            <?php echo sprintf('<strong>Language "%s" is broken.</strong> Missed required "%s" file.', $language, $langFile) ?>
                        </h2>
                    </div>
                <?php
                    unset($this->languageData[$language]);
                    break;
                }

                include_once FORUM_ROOT.'lang/'.$language.'/'.$langFile;
                if (isset($$langName)) {
                    $this->languageData[$language][$langName]['data'] = array_keys($$langName);
                    unset($$langName);
                }
            }
        }
    }

    private function compareLangElements() {
        $etalon = $this->languageData['English'];
        unset($this->languageData['English']);

        foreach ($this->languageData as $language => $languageData) {
            echo '<div class="main-content main-frm locale-tests-results">';
            echo '<h3 class="hn">'.$language.'</h3>';
            echo "<ul>";

            foreach ($languageData as $languageElementName => $languageElementItems) {
                $etalonData = $etalon[$languageElementName]['data'];
                $currentLanguageElementData = $languageElementItems['data'];

                $missingItems = array_diff($etalonData, $currentLanguageElementData);
                $unneededItems = array_diff($currentLanguageElementData, $etalonData);

                if (count($missingItems) > 0 || count($unneededItems)) {
                    echo '<li class="failed"><em>Failed</em> - '.$languageElementName.'</li>';
                } else {
                    echo '<li class="ok"><em>OK</em> - '.$languageElementName.'</li>';
                }

                if (count($missingItems) > 0) {
                ?>
                    <div class="ct-box error-box">
                        <h2 class="warn hn"><strong><?php echo (sprintf('%s has missing keys:', $languageElementName)) ?></strong></h2>
                        <ul class="error-list">
                            <li><?php echo implode("</li><li>", $missingItems)."\n" ?></li>
                        </ul>
                    </div>
                <?php
                }

                if (count($unneededItems) > 0) {
                ?>
                    <div class="ct-box error-box">
                        <h2 class="warn hn"><strong><?php echo (sprintf('%s has unneeded keys:', $languageElementName)) ?></strong></h2>
                        <ul class="error-list">
                            <li><?php echo implode("</li><li>", $unneededItems)."\n" ?></li>
                        </ul>
                    </div>
                <?php
                }

            }
            echo "</ul></div>";
        }
    }
}
