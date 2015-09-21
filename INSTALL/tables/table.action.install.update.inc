<?php

// install 1.7.9 RC1
if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('action'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_action`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_action` (
            `id` int(11) NOT NULL auto_increment,
            `date` varchar(30) NOT NULL default \'0\',
            `pseudo`  text NOT NULL,
            `action`  text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>