<?php

// install / update 1.7.9 RC1
if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('comment_mod'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_comment_mod`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_comment_mod` (
            `id` int(11) NOT NULL auto_increment,
            `module` text NOT NULL,
            `active` int(1) NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    $sql = 'INSERT INTO `'. $db_prefix .'_comment_mod` VALUES
        (1, \'news\', 1),
        (2, \'download\', 1),
        (3, \'links\', 1),
        (4, \'survey\', 1),
        (5, \'wars\', 1),
        (6, \'gallery\', 1),
        (7, \'sections\', 1);';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>