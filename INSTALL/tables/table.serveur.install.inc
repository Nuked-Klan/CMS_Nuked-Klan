<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_serveur`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_serveur` (
            `sid` int(30) NOT NULL auto_increment,
            `game` varchar(30) NOT NULL default \'\',
            `ip` varchar(30) NOT NULL default \'\',
            `port` varchar(10) NOT NULL default \'\',
            `pass` varchar(10) NOT NULL default \'\',
            `cat` varchar(30) NOT NULL default \'\',
            PRIMARY KEY  (`sid`),
            KEY `game` (`game`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>