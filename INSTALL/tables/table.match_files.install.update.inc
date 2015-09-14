<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_match_files`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_match_files` (
            `id` int(10) NOT NULL auto_increment,
            `module` varchar(30) NOT NULL default \'\',
            `im_id` int(10) NOT NULL default \'0\',
            `type` varchar(30) NOT NULL default \'\',
            `url` varchar(200) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `im_id` (`im_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    $update = array();

    // install / update 1.7.9 RC6
    if (! $this->tableExist('match_files')) {
        if ($this->tableExist('fichiers_joins')) {
            $sql = 'ALTER TABLE `'. $db_prefix .'_fichiers_joins` RENAME AS `'. $db_prefix .'_match_files`';
            mysql_query($sql) or die(mysql_error());
            $update[] = 'rename table';
        }
        else {
            die('Missing `'. $db_prefix .'_fichiers_joins` table !');
        }
    }

    if (! empty($update)) return 'UPDATED';
}

return 'NOTHING_TO_DO';

?>