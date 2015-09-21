<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_forums_threads`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_forums_threads` (
            `id` int(5) NOT NULL auto_increment,
            `titre` text NOT NULL,
            `date` varchar(10) default NULL,
            `closed` int(1) NOT NULL default \'0\',
            `auteur` text NOT NULL,
            `auteur_id` varchar(20) NOT NULL default \'\',
            `forum_id` int(5) NOT NULL default \'0\',
            `last_post` varchar(20) NOT NULL default \'\',
            `view` int(10) NOT NULL default \'0\',
            `annonce` int(1) NOT NULL default \'0\',
            `sondage` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `auteur_id` (`auteur_id`),
            KEY `forum_id` (`forum_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>