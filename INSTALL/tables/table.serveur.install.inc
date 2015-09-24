<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_serveur`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_serveur` (
            `sid` int(30) NOT NULL auto_increment,
            `game` varchar(30) NOT NULL default \'\',
            `ip` varchar(40) NOT NULL default \'\',
            `port` varchar(10) NOT NULL default \'\',
            `pass` varchar(10) NOT NULL default \'\',
            `cat` varchar(30) NOT NULL default \'\',
            PRIMARY KEY  (`sid`),
            KEY `game` (`game`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_serveur');

    // install / update 1.7.13
    if ($udt->fieldExist('ip')) {
        if ($udt->getFieldType('ip') != 'varchar(40)')
            $udt->modifyField('ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));
    }
    else {
        die(sprintf($this->_i18n['MISSING_FIELD'], 'ip', $this->data['db_prefix'] .'_serveur'));
    }

    $udt->modifyTable();

    if ($udt->isUpdated())
        return 'UPDATED';
    else
        return 'NOTHING_TO_DO';
}

?>