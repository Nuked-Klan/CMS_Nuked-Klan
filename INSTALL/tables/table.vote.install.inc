<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_vote`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_vote` (
            `id` int(11) NOT NULL auto_increment,
            `module` varchar(30) NOT NULL default \'0\',
            `vid` int(100) default NULL,
            `ip` varchar(40) NOT NULL default \'\',
            `vote` int(2) default NULL,
            PRIMARY KEY  (`id`),
            KEY `vid` (`vid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_vote');

    // install / update 1.7.13
    if ($udt->fieldExist('ip')) {
        if ($udt->getFieldType('ip') != 'varchar(40)')
            $udt->modifyField('ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));
    }
    else {
        die(sprintf($this->_i18n['MISSING_FIELD'], 'ip', $this->data['db_prefix'] .'_vote'));
    }

    $udt->modifyTable();

    if ($udt->isUpdated())
        return 'UPDATED';
    else
        return 'NOTHING_TO_DO';
}

?>