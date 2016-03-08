<?php
/**
 * table.config.c.i.u.php
 *
 * `[PREFIX]_config` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(CONFIG_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$configTableCfg = array(
    'fields' => array(
        'name'  => array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''),
        'value' => array('type' => 'text',         'null' => false)
    ),
    'primaryKey' => array('name'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Return configuration stored in config table
 */
function getConfiguration() {
    global $db;

    $sql = 'SELECT name, value
        FROM `'. CONFIG_TABLE .'`';

    $dbsConfig = $db->selectMany($sql);

    $nuked = array();

    foreach ($dbsConfig as $row)
        $nuked[$row['name']] = $row['value'];

    return $nuked;
}

/*
 * Set dateformat and datezone value in configuration
 */
function setDateConfig($language, &$cfg) {
    if ($language == 'french') {
        // install
        // $dateFormat = '%d/%m/%Y';

        // update
        $cfg['dateformat'] = '%d/%m/%Y - %H:%M:%S';
        $cfg['datezone']   = '+0100';
    }
    else {
        // install
        // $dateFormat = '%m/%d/%Y';

        // update
        $cfg['dateformat'] = '%m/%d/%Y - %H:%M:%S';
        $cfg['datezone']   = '+0000';
    }
}

/*
 * Add a new default value in configuration if missing
 */
function addDefaultCfgValue($name, $default = '') {
    global $nuked, $insertData;

    if (! array_key_exists($name, $nuked))
        $insertData[$name] = $default;
}

/*
 * Delete value in configuration
 */
function deleteCfgValue($name) {
    global $deleteData;

    $deleteData[] = $name;
}

/*
 * Update configuration
 */
function updateConfiguration() {
    global $dbTable, $db, $insertData, $updateData, $deleteData;

    if (! empty($insertData)) {
        $values = array();

        foreach ($insertData as $name => $value)
            $values[] = '(\''. $db->quote($name) .'\', \''. $db->quote($value) .'\')';

        $sql = 'INSERT INTO `'. CONFIG_TABLE .'`
            (`name`, `value`) VALUES '. implode(', ', $values);

        $dbTable->insertData(array('ADD_CONFIG', implode('`, `', array_keys($insertData))), $sql);
    }

    if (! empty($updateData)) {
        foreach ($updateData as $name => $value) {
            $sql = 'UPDATE `'. CONFIG_TABLE .'`
                SET value = \''. $db->quote($value) .'\'
                WHERE name = \''. $db->quote($name) .'\'';

            $dbTable->updateData(array('UPDATE_CONFIG', $name), $sql);
        }
    }

    if (! empty($deleteData)) {
        foreach ($deleteData as $k => $name) {
            $sql = 'DELETE FROM `'. CONFIG_TABLE .'`
                WHERE name = \''. $db->quote($name) .'\'';

            $dbTable->deleteData(array('DELETE_CONFIG', $name), $sql);
        }
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('name', 'value');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $dbTable->createTable($configTableCfg);

    $websiteUrl = 'http://'. $_SERVER['SERVER_NAME'] . str_replace('INSTALL/index.php', '', $_SERVER['SCRIPT_NAME']);

    if (substr($websiteUrl, -1) == '/') $websiteUrl = substr($websiteUrl, 0, -1);

    $dateCfg = array();

    setDateConfig($this->_session['language'], $dateCfg);

    $shareStats = ($this->_session['stats'] == 'no') ? 0 : 1;

    $sql = 'INSERT INTO `'. CONFIG_TABLE .'` VALUES
        (\'time_generate\', \'on\'),
        (\'dateformat\', \''. $dateCfg['dateformat'].'\'),
        (\'datezone\', \''. $dateCfg['datezone'] .'\'),
        (\'version\', \''. $this->_nkVersion .'\'),
        (\'date_install\', \''. time() .'\'),
        (\'langue\', \''. $this->_session['language'] .'\'),
        (\'stats_share\', \''. $shareStats .'\'),
        (\'stats_timestamp\', \'0\'),
        (\'name\', \'Nuked-klaN '. $this->_nkVersion .'\'),
        (\'slogan\', \'PHP 4 Gamers\'),
        (\'tag_pre\', \'\'),
        (\'tag_suf\', \'\'),
        (\'url\', \''. $websiteUrl .'\'),
        (\'mail\', \'mail@hotmail.com\'),
        (\'footmessage\', \'\'),
        (\'nk_status\', \'open\'),
        (\'index_site\', \'News\'),
        (\'theme\', \'Restless\'),
        (\'keyword\', \'\'),
        (\'description\', \'\'),
        (\'inscription\', \'on\'),
        (\'inscription_mail\', \'\'),
        (\'inscription_avert\', \'\'),
        (\'inscription_charte\', \'\'),
        (\'validation\', \'auto\'),
        (\'user_delete\', \'on\'),
        (\'video_editeur\', \'on\'),
        (\'scayt_editeur\', \'on\'),
        (\'suggest_avert\', \'\'),
        (\'irc_chan\', \'nuked-klan\'),
        (\'irc_serv\', \'noxether.net\'),
        (\'server_ip\', \'\'),
        (\'server_port\', \'\'),
        (\'server_pass\', \'\'),
        (\'server_game\', \'\'),
        (\'forum_title\', \'\'),
        (\'forum_desc\', \'\'),
        (\'forum_rank_team\', \'on\'),
        (\'forum_field_max\', \'10\'),
        (\'forum_file\', \'on\'),
        (\'forum_file_level\', \'1\'),
        (\'forum_file_maxsize\', \'1000\'),
        (\'thread_forum_page\', \'20\'),
        (\'mess_forum_page\', \'10\'),
        (\'hot_topic\', \'20\'),
        (\'post_flood\', \'10\'),
        (\'gallery_title\', \'\'),
        (\'max_img_line\', \'2\'),
        (\'max_img\', \'6\'),
        (\'max_news\', \'5\'),
        (\'max_download\', \'10\'),
        (\'hide_download\', \'on\'),
        (\'max_liens\', \'10\'),
        (\'max_sections\', \'10\'),
        (\'max_wars\', \'30\'),
        (\'max_archives\', \'30\'),
        (\'max_members\', \'30\'),
        (\'max_shout\', \'20\'),
        (\'mess_guest_page\', \'10\'),
        (\'sond_delay\', \'24\'),
        (\'level_analys\', \'-1\'),
        (\'visit_delay\', \'10\'),
        (\'recrute\', \'1\'),
        (\'recrute_charte\', \'\'),
        (\'recrute_mail\', \'\'),
        (\'recrute_inbox\', \'\'),
        (\'defie_charte\', \'\'),
        (\'defie_mail\', \'\'),
        (\'defie_inbox\', \'\'),
        (\'birthday\', \'all\'),
        (\'avatar_upload\', \'on\'),
        (\'avatar_url\', \'on\'),
        (\'cookiename\', \'nuked\'),
        (\'sess_inactivemins\', \'5\'),
        (\'sess_days_limit\', \'365\'),
        (\'nbc_timeout\', \'300\'),
        (\'screen\', \'on\'),
        (\'contact_mail\', \'\'),
        (\'contact_flood\', \'60\'),
        (\'forum_image\', \'on\'),
        (\'forum_cat_image\', \'on\'),
        (\'forum_birthday\', \'on\'),
        (\'forum_gamer_details\', \'on\'),
        (\'forum_user_details\', \'on\'),
        (\'forum_labels_active\', \'on\'),
        (\'forum_display_modos\', \'on\'),
        (\'textbox_avatar\', \'on\'),
        (\'user_social_level\', \'0\'),
        (\'sp_version\', \'off\'),
        (\'index_page\', \'\'),
        (\'editor_type\', \'cke\'),
        (\'rssFeed\', \'news|sections|download|links|gallery|forum\');';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    global $nuked, $insertData, $updateData, $deleteData;

    $insertData = $updateData = $deleteData = array();

    $nuked = getConfiguration();

    // used in 1.7.9 RC1, 1.7.9 RC2 & 1.7.9 RC3
    if (array_key_exists('cron_exec', $nuked))
        deleteCfgValue('cron_exec');

    // install / update 1.7.6
    if ($this->_session['version'] == '1.7.6' && $nuked['level_analys'] == 0)
        $updateData['level_analys'] = '-1';

    // install / update 1.7.8
    if (version_compare($this->_session['version'], '1.7.8', '<=')) // TODO : <= To check
        $updateData['theme'] = 'Restless';

    // install / update 1.7.9 RC2
    addDefaultCfgValue('screen', 'on');

    // install / update 1.7.9 RC3
    addDefaultCfgValue('contact_mail', $nuked['mail']);
    addDefaultCfgValue('contact_flood', '60');

    // update 1.7.9 RC6
    // Update bbcode
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $bbcode = new bbcode($this->_db, $this->_session, $this->_i18n);

        $bbcodeCfgList = array(
            'inscription_mail',
            'inscription_charte',
            'footmessage',
            'recrute_charte',
            'recrute_mail',
            'defie_charte',
            'defie_mail'
        );

        foreach ($bbcodeCfgList as $bbcodeCfgName) {
            // TODO APPLY_BBCODE
            if (array_key_exists($bbcodeCfgName, $nuked))
                $updateData[$bbcodeCfgName] = $bbcode->apply(stripslashes($nuked[$bbcodeCfgName]));
        }
    }

    if (! array_key_exists('dateformat', $nuked))
        setDateConfig($nuked['langue'], $insertData);

    if (array_key_exists('datezone', $nuked)) {
        // BUG Replace bad datezone value (Since 1.7.9 RC6 to 1.7.15)
        if ($nuked['datezone'] === '0') {
            $updateData['datezone'] = '+0000';
        }
        else if ($nuked['datezone'] === '1') {
            $updateData['datezone'] = '+0100';
        }
    }

    addDefaultCfgValue('time_generate', 'on');
    addDefaultCfgValue('video_editeur', 'on');
    addDefaultCfgValue('scayt_editeur', 'on');

    // quakenet.eu.org : 1.7.x =>
    // quakenet.org : install 1.7.9 RC5 / UPDATE 1.7.9 RC2
    // noxether.net : install 1.7.14 / update 1.7.11
    if ($nuked['irc_chan'] == 'nuked-klan' && in_array($nuked['irc_serv'], array('quakenet.eu.org', 'quakenet.org')))
        $updateData['irc_serv'] = 'noxether.net';

    // install / update 1.8
    $shareStats = ($this->_session['stats'] == 'no') ? 0 : 1;

    // TODO forum_rank_team = on ?
    addDefaultCfgValue('forum_image', 'on');
    addDefaultCfgValue('forum_cat_image', 'on');
    addDefaultCfgValue('forum_birthday', 'on');
    addDefaultCfgValue('forum_gamer_details', 'on');
    addDefaultCfgValue('forum_user_details', 'on');
    addDefaultCfgValue('forum_labels_active', 'on');
    addDefaultCfgValue('forum_display_modos', 'on');
    addDefaultCfgValue('textbox_avatar', 'on');
    addDefaultCfgValue('user_social_level', 0);
    addDefaultCfgValue('sp_version', 'off');
    addDefaultCfgValue('index_page', '');
    addDefaultCfgValue('editor_type', 'cke');
    addDefaultCfgValue('rssFeed', 'news|sections|download|links|gallery|forum');
    addDefaultCfgValue('stats_share', $shareStats);
    addDefaultCfgValue('stats_timestamp', 0);

    updateConfiguration();
}

?>