<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_suggest`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_suggest` (
            `id` int(11) NOT NULL auto_increment,
            `module` mediumtext NOT NULL,
            `user_id` varchar(20) NOT NULL default \'\',
            `proposition` longtext NOT NULL,
            `date` varchar(14) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `user_id` (`user_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>