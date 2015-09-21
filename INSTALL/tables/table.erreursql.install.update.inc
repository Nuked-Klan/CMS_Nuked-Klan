<?php

// install / update 1.7.9 RC1
if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('erreursql'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_erreursql`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_erreursql` (
            `id` int(11) NOT NULL auto_increment,
            `date` varchar(30) NOT NULL default \'0\',
            `lien`  text NOT NULL,
            `texte`  text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>