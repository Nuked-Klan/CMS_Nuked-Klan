<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_nbconnecte`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_nbconnecte` (
            `IP` varchar(30) NOT NULL default \'\',
            `type` int(10) NOT NULL default \'0\',
            `date` int(14) NOT NULL default \'0\',
            `user_id` varchar(20) NOT NULL default \'\',
            `username` varchar(40) NOT NULL default \'\',
            PRIMARY KEY  ( `IP` , `user_id` )
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>