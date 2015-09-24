<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_sondage_check`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_sondage_check` (
            `ip` varchar(40) NOT NULL default \'\',
            `pseudo` varchar(50) NOT NULL default \'\',
            `heurelimite` int(14) NOT NULL default \'0\',
            `sid` varchar(30) NOT NULL default \'\',
            KEY `sid` (`sid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_sondage_check');

    // install / update 1.7.13
    if ($udt->fieldExist('ip')) {
        if ($udt->getFieldType('ip') != 'varchar(40)')
            $udt->modifyField('ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));
    }
    else {
        die(sprintf($this->_i18n['MISSING_FIELD'], 'ip', $this->data['db_prefix'] .'_sondage_check'));
    }

    $udt->modifyTable();

    if ($udt->isUpdated())
        return 'UPDATED';
    else
        return 'NOTHING_TO_DO';
}

?>