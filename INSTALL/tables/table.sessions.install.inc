<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_sessions`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_sessions` (
            `id` varchar(50) NOT NULL default \'0\',
            `user_id` varchar(20) NOT NULL default \'0\',
            `date` varchar(30) NOT NULL default \'\',
            `last_used` varchar(30) NOT NULL default \'\',
            `ip` varchar(40) NOT NULL default \'\',
            `vars` blob NOT NULL,
            PRIMARY KEY  (`id`),
            KEY `user_id` (`user_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_sessions');

    // install / update 1.7.13
    if ($udt->fieldExist('ip')) {
        if ($udt->getFieldType('ip') != 'varchar(40)')
            $udt->modifyField('ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));
    }
    else {
        die(sprintf($this->_i18n['MISSING_FIELD'], 'ip', $this->data['db_prefix'] .'_sessions'));
    }

    $udt->modifyTable();

    if ($udt->isUpdated())
        return 'UPDATED';
    else
        return 'NOTHING_TO_DO';
}

?>