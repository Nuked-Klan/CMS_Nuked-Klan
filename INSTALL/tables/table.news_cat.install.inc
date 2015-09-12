<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_news_cat`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_news_cat` (
            `nid` int(11) NOT NULL auto_increment,
            `titre` text,
            `description` text,
            `image` text,
            PRIMARY KEY  (`nid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $db_prefix .'_news_cat` VALUES
        (\'1\', \'Counter Strike Source\', \''. _BESTMOD .'\', \'modules/News/images/cs.gif\');';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>