<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_team`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_team` (
            `cid` int(11) NOT NULL auto_increment,
            `titre` varchar(50) NOT NULL default \'\',
            `tag` text NOT NULL,
            `tag2` text NOT NULL,
            `ordre` int(5) NOT NULL default \'0\',
            `game` int(11) NOT NULL default \'0\',
            PRIMARY KEY  (`cid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>