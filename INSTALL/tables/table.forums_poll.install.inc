<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_forums_poll`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_forums_poll` (
            `id` int(11) NOT NULL auto_increment,
            `thread_id` int(11) NOT NULL default \'0\',
            `titre` varchar(255) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `thread_id` (`thread_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>