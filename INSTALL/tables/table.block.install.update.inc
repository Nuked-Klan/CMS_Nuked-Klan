<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_block`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_block` (
            `bid` int(10) NOT NULL auto_increment,
            `active` int(1) NOT NULL default \'0\',
            `position` int(2) NOT NULL default \'0\',
            `module` varchar(100) NOT NULL default \'\',
            `titre` text NOT NULL,
            `content` text NOT NULL,
            `type` varchar(30) NOT NULL default \'0\',
            `nivo` int(1) NOT NULL default \'0\',
            `page` text NOT NULL,
            PRIMARY KEY  (`bid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_block` VALUES
        (1, 2, 1, \'\', \''. mysql_real_escape_string($this->_i18n['BLOCK_LOGIN']) .'\', \'\', \'login\', 0, \'Tous\'),
        (2, 1, 1, \'\', \''. mysql_real_escape_string($this->_i18n['NAV']) .'\', \'[News]|'. mysql_real_escape_string($this->_i18n['NAV_NEWS']) .'||0|NEWLINE[Archives]|'. mysql_real_escape_string($this->_i18n['NAV_ARCHIV']) .'||0|NEWLINE[Forum]|'. mysql_real_escape_string($this->_i18n['NAV_FORUM']) .'||0|NEWLINE[Download]|'. mysql_real_escape_string($this->_i18n['NAV_DOWNLOAD']) .'||0|NEWLINE[Members]|'. mysql_real_escape_string($this->_i18n['NAV_MEMBERS']) .'||0|NEWLINE[Team]|'. mysql_real_escape_string($this->_i18n['NAV_TEAM']) .'||0|NEWLINE[Defy]|'. mysql_real_escape_string($this->_i18n['NAV_DEFY']) .'||0|NEWLINE[Recruit]|'. mysql_real_escape_string($this->_i18n['NAV_RECRUIT']) .'||0|NEWLINE[Sections]|'. mysql_real_escape_string($this->_i18n['NAV_ART']) .'||0|NEWLINE[Server]|'. mysql_real_escape_string($this->_i18n['NAV_SERVER']) .'||0|NEWLINE[Links]|'. mysql_real_escape_string($this->_i18n['NAV_LINKS']) .'||0|NEWLINE[Calendar]|'. mysql_real_escape_string($this->_i18n['NAV_CALENDAR']) .'||0|NEWLINE[Gallery]|'. mysql_real_escape_string($this->_i18n['NAV_GALLERY']) .'||0|NEWLINE[Wars]|'. mysql_real_escape_string($this->_i18n['NAV_MATCHS']) .'||0|NEWLINE[Irc]|'. mysql_real_escape_string($this->_i18n['NAV_IRC']) .'||0|NEWLINE[Guestbook]|'. mysql_real_escape_string($this->_i18n['NAV_GUESTBOOK']) .'||0|NEWLINE[Search]|'. mysql_real_escape_string($this->_i18n['NAV_SEARCH']) .'||0|NEWLINE|<b>'. mysql_real_escape_string($this->_i18n['MEMBER']) .'</b>||1|NEWLINE[User]|'. mysql_real_escape_string($this->_i18n['NAV_ACCOUNT']) .'||1|NEWLINE|<b>'. mysql_real_escape_string($this->_i18n['ADMIN']) .'</b>||2|NEWLINE[Admin]|'. mysql_real_escape_string($this->_i18n['NAV_ADMIN']) .'||2|\', \'menu\', 0, \'Tous\'),
        (3, 1, 2, \'Search\', \''. mysql_real_escape_string($this->_i18n['BLOCK_SEARCH']) .'\', \'\', \'module\', 0, \'Tous\'),
        (4, 2, 2, \'\', \''. mysql_real_escape_string($this->_i18n['POLL']) .'\', \'\', \'survey\', 0, \'Tous\'),
        (5, 2, 3, \'Wars\', \''. mysql_real_escape_string($this->_i18n['NAV_MATCHS']) .'\', \'\', \'module\', 0, \'Tous\'),
        (6, 1, 3, \'Stats\', \''. mysql_real_escape_string($this->_i18n['BLOCK_STATS']) .'\', \'\', \'module\', 0, \'Tous\'),
        (7, 0, 0, \'Irc\', \''. mysql_real_escape_string($this->_i18n['IRC_AWARD']) .'\', \'\', \'module\', 0, \'Tous\'),
        (8, 0, 0, \'Server\', \''. mysql_real_escape_string($this->_i18n['SERVER_MONITOR']) .'\', \'\', \'module\', 0, \'Tous\'),
        (9, 0, 0, \'\', \''. mysql_real_escape_string($this->_i18n['SUGGEST']) .'\', \'\', \'suggest\', 1, \'Tous\'),
        (10, 0, 0, \'Textbox\', \''. mysql_real_escape_string($this->_i18n['BLOCK_SHOUTBOX']) .'\', \'\', \'module\', 0, \'Tous\'),
        (11, 1, 4, \'\', \''. mysql_real_escape_string($this->_i18n['BLOCK_PARTNERS']) .'\', \'<div style="text-align: center;padding: 10px;"><a href="http://www.nuked-klan.org" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/ban.png" alt="" title="Nuked-klaN CMS" /></a></div><div style="text-align: center;padding: 10px;"><a href="http://www.nitroserv.fr" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/nitroserv.png" alt="" title="'. mysql_real_escape_string($this->_i18n['GAME_SERVER_RENTING']) .'" /></a></div>\', \'html\', 0, \'Tous\');';

    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of block database table
     */
    function updateBlockRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList))
            $setFields['content'] = $vars['bbcode']->apply(stripslashes($row['content']));

        return $setFields;
    }

    // install / update 1.7.5
    if (version_compare($this->data['version'], '1.7.5', '<=')) {
        $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_block`
            (active, position, module, titre, content, type, nivo, page) VALUES
            (1, 4, \'\', \''. mysql_real_escape_string($this->_i18n['BLOCK_PARTNERS']) .'\', \'<div style="text-align: center;padding: 10px;"><a href="http://www.nuked-klan.org" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/ban.png" alt="" title="Nuked-klaN CMS" /></a></div><div style="text-align: center;padding: 10px;"><a href="http://www.nitroserv.fr" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/nitroserv.png" alt="" title="'. mysql_real_escape_string($this->_i18n['GAME_SERVER_RENTING']) .'" /></a></div>\', \'html\', 0, \'Tous\');';
        mysql_query($sql) or die(mysql_error());
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_block');

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('content')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'content');
    }

    return $udt->execute('bid', 'updateBlockRow');
}

?>