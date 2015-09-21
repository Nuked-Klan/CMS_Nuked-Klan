<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_vote`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_vote` (
            `id` int(11) NOT NULL auto_increment,
            `module` varchar(30) NOT NULL default \'0\',
            `vid` int(100) default NULL,
            `ip` varchar(20) NOT NULL default \'\',
            `vote` int(2) default NULL,
            PRIMARY KEY  (`id`),
            KEY `vid` (`vid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>