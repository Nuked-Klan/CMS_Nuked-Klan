<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_banned`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_banned` (
            `id` int(11) NOT NULL auto_increment,
            `ip` varchar(50) NOT NULL default \'\',
            `pseudo` varchar(50) NOT NULL default \'\',
            `email` varchar(80) NOT NULL default \'\',
            `date` VARCHAR(20)  NULL,
            `dure` VARCHAR(20)  NULL,
            `texte` text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = false;

    if (! $this->fieldExist('banned', 'date')) {
        $sql = 'ALTER TABLE `'. $db_prefix .'_banned` ADD `date` VARCHAR(20)  NULL AFTER `email`';
        mysql_query($sql) or die(mysql_error());
        $update = true;
    }

    if (! $this->fieldExist('banned', 'dure')) {
        $sql = 'ALTER TABLE `'. $db_prefix .'_banned` ADD `dure` VARCHAR(20)  NULL AFTER `date`';
        mysql_query($sql) or die(mysql_error());
        $update = true;
    }

    if ($update) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>