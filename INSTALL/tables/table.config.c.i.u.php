<?php
/**
 * table.config.c.i.u.php
 *
 * `[PREFIX]_config` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_config');

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
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_config` (
            `name` varchar(255) NOT NULL default \'\',
            `value` text NOT NULL,
            PRIMARY KEY  (`name`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);

    $websiteUrl = 'http://'. $_SERVER['SERVER_NAME'] . str_replace('INSTALL/index.php', '', $_SERVER['SCRIPT_NAME']);

    if (substr($websiteUrl, -1) == '/') $websiteUrl = substr($websiteUrl, 0, -1);

    if ($this->_session['language'] == 'french') {
        $dateFormat = '%d/%m/%Y';
        $dateZone   = '+0100';
    }
    else {
        $dateFormat = '%m/%d/%Y';
        $dateZone   = '+0000';
    }

    $shareStats = ($this->_session['stats'] == 'no') ? 0 : 1;

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_config` VALUES
        (\'time_generate\', \'on\'),
        (\'dateformat\', \''. $dateFormat.'\'),
        (\'datezone\', \''. $dateZone .'\'),
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
        (\'theme\', \'Impact_Nk\'),
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
        (\'forum_rank_team\', \'off\'),
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
        (\'contact_flood\', \'60\');';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    $nuked = $insertData = $updateData = array();

    $sql = 'SELECT name, value
        FROM `'. $this->_session['db_prefix'] .'_config`';

    $dbsConfig = $this->_db->selectMany($sql);

    foreach ($dbsConfig as $row)
        $nuked[$row['name']] = $row['value'];

    // used in 1.7.9 RC1, 1.7.9 RC2 & 1.7.9 RC3
    if (array_key_exists('cron_exec', $nuked)) {
        $sql = 'DELETE FROM `'. $this->_session['db_prefix'] .'_config`
            WHERE name = \'cron_exec\'';

        $dbTable->deleteData(array('DELETE_CONFIG', 'cron_exec'), $sql);
    }

    // install / update 1.7.6
    if ($this->_session['version'] == '1.7.6' && $nuked['level_analys'] == 0)
        $updateData['level_analys'] = '-1';

    // install / update 1.7.8
    if (version_compare($this->_session['version'], '1.7.8', '<='))
        $updateData['theme'] = 'Impact_Nk';

    // install / update 1.7.9 RC2
    if (! array_key_exists('screen', $nuked))
        $insertData['screen'] = 'on';

    // install / update 1.7.9 RC3
    if (! array_key_exists('contact_mail', $nuked))
        $insertData['contact_mail'] = $nuked['mail'];

    if (! array_key_exists('contact_flood', $nuked))
        $insertData['contact_flood'] = '60';

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

    if (array_key_exists('datezone', $nuked)) {
        // BUG Replace bad datezone value (Since 1.7.9 RC6 to 1.7.15)
        if ($nuked['datezone'] === '1') {
            $insertData['datezone'] = '+0100';
        }
        else if ($nuked['datezone'] === '0') {
            $insertData['datezone'] = '+0000';
        }
    }

    if (! array_key_exists('dateformat', $nuked)) {
        if ($this->_session['language'] == 'french') {
            $insertData['dateformat'] = '%d/%m/%Y - %H:%M:%S';
            $insertData['datezone']   = '+0100';
        }
        else {
            $insertData['dateformat'] = '%m/%d/%Y - %H:%M:%S';
            $insertData['datezone']   = '+0000';
        }
    }

    if (! array_key_exists('time_generate', $nuked))
        $insertData['time_generate'] = 'on';

    if (! array_key_exists('video_editeur', $nuked))
        $insertData['video_editeur'] = 'on';

    if (! array_key_exists('scayt_editeur', $nuked))
        $insertData['scayt_editeur'] = 'on';

    // quakenet.eu.org : 1.7.x =>
    // quakenet.org : install 1.7.9 RC5 / UPDATE 1.7.9 RC2
    // noxether.net : install 1.7.14 / update 1.7.11
    if ($nuked['irc_chan'] == 'nuked-klan' && in_array($nuked['irc_serv'], array('quakenet.eu.org', 'quakenet.org')))
        $updateData['irc_serv'] = 'noxether.net';

    if (! empty($insertData)) {
        $values = array();

        foreach ($insertData as $name => $value)
            $values[] = '(\''. $this->_db->quote($name) .'\', \''. $this->_db->quote($value) .'\')';

        $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_config`
            (`name`, `value`) VALUES '. implode(', ', $values);

        $dbTable->insertData(array('ADD_CONFIG', implode('`, `', array_keys($insertData))), $sql);
    }

    if (! empty($updateData)) {
        foreach ($updateData as $name => $value) {
            $sql = 'UPDATE `'. $this->_session['db_prefix'] .'_config`
                SET value = \''. $this->_db->quote($value) .'\'
                WHERE name = \''. $this->_db->quote($name) .'\'';

            $dbTable->updateData(array('UPDATE_CONFIG', $name), $sql);
        }
    }
}

?>