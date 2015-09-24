<?php

// TODO : champ ip a revoir

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_sessions`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_sessions` (
            `id` varchar(50) NOT NULL default \'0\',
            `user_id` varchar(20) NOT NULL default \'0\',
            `date` varchar(30) NOT NULL default \'\',
            `last_used` varchar(30) NOT NULL default \'\',
            `ip` varchar(50) NOT NULL default \'\',
            `vars` blob NOT NULL,
            PRIMARY KEY  (`id`),
            KEY `user_id` (`user_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>