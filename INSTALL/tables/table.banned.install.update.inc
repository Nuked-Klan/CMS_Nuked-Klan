<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_banned`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_banned` (
            `id` int(11) NOT NULL auto_increment,
            `ip` varchar(40) NOT NULL default \'\',
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
    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_banned');

    // install / update 1.7.9 RC1
    if (! $udt->fieldExist('date'))
        $udt->addField('date', array('type' => 'VARCHAR(20)', 'null' => true), 'email');

    // install / update 1.7.9 RC1
    if (! $udt->fieldExist('dure'))
        $udt->addField('dure', array('type' => 'VARCHAR(20)', 'null' => true), 'date');

    // install / update 1.7.13
    if ($udt->fieldExist('ip')) {
        if ($udt->getFieldType('ip') != 'varchar(40)')
            $udt->modifyField('ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));
    }
    else {
        die(sprintf($this->_i18n['MISSING_FIELD'], 'ip', $this->data['db_prefix'] .'_banned'));
    }

    $udt->modifyTable();

    if ($udt->isUpdated())
        return 'UPDATED';
    else
        return 'NOTHING_TO_DO';
}

?>