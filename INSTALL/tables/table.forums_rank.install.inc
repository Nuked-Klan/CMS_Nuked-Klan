<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_forums_rank`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_forums_rank` (
            `id` int(10) NOT NULL auto_increment,
            `nom` varchar(100) NOT NULL default \'\',
            `type` int(1) NOT NULL default \'0\',
            `post` int(4) NOT NULL default \'0\',
            `image` varchar(200) NOT NULL default \'\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_forums_rank` VALUES
        (1, \''. mysql_real_escape_string($this->_i18n['NEWBIE']) .'\', 0, 0, \'modules/Forum/images/rank/star1.gif\'),
        (2, \''. mysql_real_escape_string($this->_i18n['JUNIOR_MEMBER']) .'\', 0, 10, \'modules/Forum/images/rank/star2.gif\'),
        (3, \''. mysql_real_escape_string($this->_i18n['MEMBER']) .'\', 0, 100, \'modules/Forum/images/rank/star3.gif\'),
        (4, \''. mysql_real_escape_string($this->_i18n['SENIOR_MEMBER']) .'\', 0, 500, \'modules/Forum/images/rank/star4.gif\'),
        (5, \''. mysql_real_escape_string($this->_i18n['POSTING_FREAK']) .'\', 0, 1000, \'modules/Forum/images/rank/star5.gif\'),
        (6, \''. mysql_real_escape_string($this->_i18n['MODERATOR']) .'\', 1, 0, \'modules/Forum/images/rank/mod.gif\'),
        (7, \''. mysql_real_escape_string($this->_i18n['ADMINISTRATOR']) .'\', 2, 0, \'modules/Forum/images/rank/mod.gif\');';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>