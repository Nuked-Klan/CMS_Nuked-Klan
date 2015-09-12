<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_games_prefs`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_games_prefs` (
            `id` int(11) NOT NULL auto_increment,
            `game` int(11) NOT NULL default \'0\',
            `user_id` varchar(20) NOT NULL default \'\',
            `pref_1` text NOT NULL,
            `pref_2` text NOT NULL,
            `pref_3` text NOT NULL,
            `pref_4` text NOT NULL,
            `pref_5` text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>