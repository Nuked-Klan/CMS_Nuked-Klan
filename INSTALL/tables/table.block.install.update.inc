<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_block`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_block` (
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

    $sql = 'INSERT INTO `'. $db_prefix .'_block` VALUES
        (1, 2, 1, \'\', \''. _BLOKLOGIN .'\', \'\', \'login\', 0, \'Tous\'),
        (2, 1, 1, \'\', \''. _NAV .'\', \'[News]|'. _NAVNEWS .'||0|NEWLINE[Archives]|'. _NAVARCHIV .'||0|NEWLINE[Forum]|'. _NAVFORUM .'||0|NEWLINE[Download]|'. _NAVDOWNLOAD .'||0|NEWLINE[Members]|'. _NAVMEMBERS .'||0|NEWLINE[Team]|'. _NAVTEAM .'||0|NEWLINE[Defy]|'. _NAVDEFY .'||0|NEWLINE[Recruit]|'. _NAVRECRUIT .'||0|NEWLINE[Sections]|'. _NAVART .'||0|NEWLINE[Server]|'. _NAVSERVER .'||0|NEWLINE[Links]|'. _NAVLINKS .'||0|NEWLINE[Calendar]|'. _NAVCALENDAR .'||0|NEWLINE[Gallery]|'. _NAVGALLERY .'||0|NEWLINE[Wars]|'. _NAVMATCHS .'||0|NEWLINE[Irc]|'. _NAVIRC .'||0|NEWLINE[Guestbook]|'. _NAVGUESTBOOK .'||0|NEWLINE[Search]|'. _NAVSEARCH .'||0|NEWLINE|<b>'. _MEMBER .'</b>||1|NEWLINE[User]|'. _NAVACCOUNT .'||1|NEWLINE|<b>'. _ADMIN .'</b>||2|NEWLINE[Admin]|'. _NAVADMIN .'||2|\', \'menu\', 0, \'Tous\'),
        (3, 1, 2, \'Search\', \''. _BLOKSEARCH .'\', \'\', \'module\', 0, \'Tous\'),
        (4, 2, 2, \'\', \''. _POLL .'\', \'\', \'survey\', 0, \'Tous\'),
        (5, 2, 3, \'Wars\', \''. _NAVMATCHS .'\', \'\', \'module\', 0, \'Tous\'),
        (6, 1, 3, \'Stats\', \''. _BLOKSTATS .'\', \'\', \'module\', 0, \'Tous\'),
        (7, 0, 0, \'Irc\', \''. _IRCAWARD .'\', \'\', \'module\', 0, \'Tous\'),
        (8, 0, 0, \'Server\', \''. _SERVERMONITOR .'\', \'\', \'module\', 0, \'Tous\'),
        (9, 0, 0, \'\', \''. _SUGGEST .'\', \'\', \'suggest\', 1, \'Tous\'),
        (10, 0, 0, \'Textbox\', \''. _BLOKSHOUT .'\', \'\', \'module\', 0, \'Tous\'),
        (11, 1, 4, \'\', \''. _BLOKPARTNERS .'\', \'<div style="text-align: center;padding: 10px;"><a href="http://www.nuked-klan.org" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/ban.png" alt="" title="Nuked-klaN CMS" /></a></div><div style="text-align: center;padding: 10px;"><a href="http://www.nitroserv.fr" onclick="window.open(this.href); return false;"><img style="border: 0;" src="images/nitroserv.png" alt="" title="Location de serveurs de jeux" /></a></div>\', \'html\', 0, \'Tous\');';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = false;

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.7', '=') || version_compare($this->data['version'], '1.7.8', '=')) {
        $sql = 'SELECT bid, content FROM `'. $db_prefix .'_block`';
        $req = mysql_query($sql) or die(mysql_error());

        while ($row = mysql_fetch_row($req)) {
            $row['content'] = mysql_real_escape_string(bbcode(stripslashes($row['content']), $db_prefix));

            $sql = 'UPDATE `'. $db_prefix .'_block` SET content = \''. $row['content'] .'\' WHERE bid = \''. $row['bid'] .'\'';
            mysql_query($sql) or die(mysql_error());
        }

        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>