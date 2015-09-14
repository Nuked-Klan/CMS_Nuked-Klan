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
    $tableData = $this->getTableData('banned');
    $update = $alterTable = array();

    // install / update 1.7.9 RC1
    if (! array_key_exists('date', $tableData))
        $alterTable[] = 'ADD `date` VARCHAR(20)  NULL AFTER `email`';

    // install / update 1.7.9 RC1
    if (! array_key_exists('dure', $tableData))
        $alterTable[] = 'ADD `dure` VARCHAR(20)  NULL AFTER `date`';

    if (! empty($alterTable)) {
        $sql = 'ALTER TABLE `'. $db_prefix .'_banned` '. implode(', ', $alterTable);
        mysql_query($sql) or die(mysql_error());
        $update[] = 'alter table';
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>