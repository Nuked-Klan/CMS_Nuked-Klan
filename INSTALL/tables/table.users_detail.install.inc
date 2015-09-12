<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_users_detail`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_users_detail` (
            `user_id` varchar(20) NOT NULL default \'0\',
            `prenom` text,
            `age` varchar(10) NOT NULL default \'\',
            `sexe` varchar(20) NOT NULL default \'\',
            `ville` text,
            `photo` varchar(150) NOT NULL default \'\',
            `motherboard` text,
            `cpu` varchar(50) default NULL,
            `ram` varchar(10) NOT NULL default \'\',
            `video` text,
            `resolution` text,
            `son` text,
            `ecran` text,
            `souris` text,
            `clavier` text,
            `connexion` text,
            `system` text,
            `pref_1` text NOT NULL,
            `pref_2` text NOT NULL,
            `pref_3` text NOT NULL,
            `pref_4` text NOT NULL,
            `pref_5` text NOT NULL,
            PRIMARY KEY  (`user_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>