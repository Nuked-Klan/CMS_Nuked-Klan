<?php

// install / update 1.7.9 RC5
if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('tmpses'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_tmpses`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_tmpses` (
            `session_id` varchar(64) NOT NULL,
            `session_vars` text NOT NULL,
            `session_start` bigint(20) NOT NULL,
            PRIMARY KEY (`session_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>