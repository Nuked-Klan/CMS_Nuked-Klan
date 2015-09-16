<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_config`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_config` (
            `name` varchar(255) NOT NULL default \'\',
            `value` text NOT NULL,
            PRIMARY KEY  (`name`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $websiteUrl = 'http://'. $_SERVER['SERVER_NAME'] . str_replace('INSTALL/index.php', '', $_SERVER['SCRIPT_NAME']);

    if (substr($websiteUrl, -1) == '/') $websiteUrl = substr($websiteUrl, 0, -1);

    if ($this->data['language'] == 'french') {
        $dateFormat = '%d/%m/%Y';
        $dateZone   = '+0100';
    }
    else {
        $dateFormat = '%m/%d/%Y';
        $dateZone   = '+0000';
    }

    $shareStats = $this->data['stats'] == 'no' ? 0 : 1;

    $sql = 'INSERT INTO `'. $db_prefix .'_config` VALUES
        (\'time_generate\', \'on\'),
        (\'dateformat\', \''. $dateFormat.'\'),
        (\'datezone\', \''. $dateZone .'\'),
        (\'version\', \''. self::NK_VERSION .'\'),
        (\'date_install\', \''. time() .'\'),
        (\'langue\', \''. $this->data['language'] .'\'),
        (\'stats_share\', \''. $shareStats .'\'),
        (\'stats_timestamp\', \'0\'),
        (\'name\', \'Nuked-klaN '. self::NK_VERSION .'\'),
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
        (\'irc_serv\', \'quakenet.org\'),
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
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = $nuked = $insertData = $updateData = array();

    $sql = 'SELECT name, value FROM `'. $db_prefix .'_config`';
    $dbsConfig = mysql_query($sql) or die (mysql_error());

    while ($row = mysql_fetch_assoc($dbsConfig))
        $nuked[$row['name']] = $row['value'];

    // used in 1.7.9 RC1, 1.7.9 RC2 & 1.7.9 RC3
    if (array_key_exists('cron_exec', $nuked)) {
        $sql = 'DELETE FROM `'. $db_prefix .'_config` WHERE name = \'cron_exec\'';
        mysql_query($sql) or die (mysql_error());
        $update[] = 'delete cron_exec';
    }

    // Update bbcode
    if (version_compare($this->data['version'], '1.7.9', '<=')) {
        include 'bbcode.php';

        $bbcodeCfgList = array(
            'inscription_mail',
            'inscription_charte',
            'footmessage',
            'recrute_charte',
            'recrute_mail',
            'defie_charte',
            'defie_mail'
        );

        foreach ($bbcodeCfgList as $bbcodeCfgName)
            $updateData[$bbcodeCfgName] = mysql_real_escape_string(bbcode(stripslashes($nuked[$bbcodeCfgName]), $db_prefix, $this->_i18n));
    }

    if (version_compare($this->data['version'], '1.7.8', '<='))
        $updateData['theme'] = 'Impact_Nk';

    if ($this->data['version'] = '1.7.6' && $nuked['level_analys'] == 0)
        $updateData['level_analys'] = '-1';

    if (! array_key_exists('screen', $nuked))
        $insertData['screen'] = 'on';

    if ($nuked['irc_chan'] == 'nuked-klan' && in_array($nuked['irc_serv'], array('quakenet.eu.org', 'quakenet.org')))
        $updateData['irc_serv'] = 'noxether.net';

    // TODO : A finir
    if (version_compare($nuked['version'], install::NK_VERSION) === -1) {
        $updateData['version'] = install::NK_VERSION;

        if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
            // version 1.7.7 ou 1.7.8
            if (! tableExist('contact', $db_prefix)) {
                $sql = 'SELECT value FROM `'. $db_prefix .'_config` WHERE name = \'mail\'';
                $dbsConfig = mysql_query($sql) or die(mysql_error());
                list($adminEmail) = mysql_fetch_row($dbsConfig) or die(mysql_error());

                $data['contact_mail']   = $adminEmail;
                $data['contact_flood']  = '60';
            }
        }

        if (version_compare($nuked['version'], '1.7.9 RC6') === -1) {
            // Tout sauf 1.7.9 RC6
            if ($this->data['language'] == 'french') {
                $data['dateformat'] = '%d/%m/%Y - %H:%M:%S';
                $data['datezone']   = 1;
            }
            else {
                $data['dateformat'] = '%m/%d/%Y - %H:%M:%S';
                $data['datezone']   = 0;
            }

            $data['time_generate'] = 'on';
            $data['video_editeur'] = 'on';
            $data['scayt_editeur'] = 'on';
        }
    }

    if (! empty($insertData)) {
        $values = array();

        foreach ($insertData as $name => $value)
            $values[] = '(\''. $name .'\', \''. $value .'\')';

        $sql = 'INSERT INTO `'. $db_prefix .'_config` (`name`, `value`) VALUES '. implode(', ', $values);
        mysql_query($sql) or die(mysql_error());
        $update[] = 'insert config';
    }

    if (! empty($updateData)) {
        foreach ($updateData as $name => $value) {
            $sql = 'UPDATE `'. $db_prefix .'_config` SET value = \''. $value .'\' WHERE name = \''. $name .'\'';
            mysql_query($sql) or die(mysql_error());
            $update[] = 'update config';
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>