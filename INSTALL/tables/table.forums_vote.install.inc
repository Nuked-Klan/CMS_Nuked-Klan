<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_forums_vote`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_forums_vote` (
            `poll_id` int(11) NOT NULL default \'0\',
            `auteur_id` varchar(20) NOT NULL default \'\',
            `auteur_ip` varchar(40) NOT NULL default \'\',
            KEY `poll_id` (`poll_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {
    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_forums_vote');

    // install / update 1.7.13
    if ($udt->fieldExist('auteur_ip')) {
        if ($udt->getFieldType('auteur_ip') != 'varchar(40)')
            $udt->modifyField('auteur_ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));
    }
    else {
        die(sprintf($this->_i18n['MISSING_FIELD'], 'auteur_ip', $this->data['db_prefix'] .'_forums_vote'));
    }

    $udt->modifyTable();

    if ($udt->isUpdated())
        return 'UPDATED';
    else
        return 'NOTHING_TO_DO';
}

?>