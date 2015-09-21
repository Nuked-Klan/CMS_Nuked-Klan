<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_forums_cat`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_forums_cat` (
            `id` int(11) NOT NULL auto_increment,
            `nom` varchar(100) default NULL,
            `ordre` int(5) NOT NULL default \'0\',
            `niveau` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql='INSERT INTO `'. $this->data['db_prefix'] .'_forums_cat` VALUES
        (1, \''. mysql_real_escape_string($this->_i18n['CATEGORY']) .' 1\', 0, 0);';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>