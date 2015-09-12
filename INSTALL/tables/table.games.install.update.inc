<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_games`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_games` (
            `id` int(11) NOT NULL auto_increment,
            `name` varchar(50) NOT NULL default \'\',
            `titre` varchar(50) NOT NULL default \'\',
            `icon` varchar(150) NOT NULL default \'\',
            `pref_1` varchar(50) NOT NULL default \'\',
            `pref_2` varchar(50) NOT NULL default \'\',
            `pref_3` varchar(50) NOT NULL default \'\',
            `pref_4` varchar(50) NOT NULL default \'\',
            `pref_5` varchar(50) NOT NULL default \'\',
            `map` TEXT NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $db_prefix .'_games` VALUES
        (1, \'Counter Strike Source\', \''. _PREFCS .'\', \'images/games/cs.gif\', \''. _OTHERNICK .'\', \''. _FAVMAP .'\', \''. _FAVWEAPON .'\', \''. _SKINT .'\', \''. _SKINCT .'\', \'de_dust2|de_inferno\');';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = false;

    if (! $this->fieldExist('games', 'map')) {
        $sql = 'ALTER TABLE `'. $db_prefix .'_games` ADD `map` TEXT NOT NULL;';
        mysql_query($sql) or die(mysql_error());
        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>