<?php

// install / update 1.7.9 RC1
if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('notification'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $db_prefix .'_notification`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $db_prefix .'_notification` (
            `id` int(11) NOT NULL auto_increment,
            `date` varchar(30) NOT NULL default \'0\',
            `type`  text NOT NULL,
            `texte`  text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    if (ini_get('suhosin.session.encrypt') == 1) {
        $sql = 'INSERT INTO `'. $db_prefix .'_notification` VALUES
            (\'\', \'\', \'4\', \''. $this->_i18n['SUHOSIN'] .'\');';
        mysql_query($sql) or die(mysql_error());
    }

    return 'CREATED';
}

return 'NOTHING_TO_DO';

?>