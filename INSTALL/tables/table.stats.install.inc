<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_stats`';
    mysql_query($sql) or die(mysql_error());

    $sql='CREATE TABLE `'. $this->data['db_prefix'] .'_stats` (
            `nom` varchar(50) NOT NULL default \'\',
            `type` varchar(50) NOT NULL default \'\',
            `count` int(11) NOT NULL default \'0\',
            PRIMARY KEY  (`nom`,`type`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_stats` VALUES
        (\'Gallery\', \'pages\', 0),
        (\'Archives\', \'pages\', 0),
        (\'Calendar\', \'pages\', 0),
        (\'Defy\', \'pages\', 0),
        (\'Download\', \'pages\', 0),
        (\'Guestbook\', \'pages\', 0),
        (\'Irc\', \'pages\', 0),
        (\'Links\', \'pages\', 0),
        (\'Wars\', \'pages\', 0),
        (\'News\', \'pages\', 0),
        (\'Search\', \'pages\', 0),
        (\'Recruit\', \'pages\', 0),
        (\'Sections\', \'pages\', 0),
        (\'Server\', \'pages\', 0),
        (\'Members\', \'pages\', 0),
        (\'Team\', \'pages\', 0),
        (\'Forum\', \'pages\', 0);';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>