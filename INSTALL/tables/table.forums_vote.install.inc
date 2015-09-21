<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_forums_vote`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_forums_vote` (
            `poll_id` int(11) NOT NULL default \'0\',
            `auteur_id` varchar(20) NOT NULL default \'\',
            `auteur_ip` varchar(20) NOT NULL default \'\',
            KEY `poll_id` (`poll_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>