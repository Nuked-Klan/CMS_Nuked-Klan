<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_forums_messages`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_forums_messages` (
            `id` int(5) NOT NULL auto_increment,
            `titre` text NOT NULL,
            `txt` text NOT NULL,
            `date` varchar(12) NOT NULL default \'\',
            `edition` text NOT NULL,
            `auteur` text NOT NULL,
            `auteur_id` varchar(20) NOT NULL default \'\',
            `auteur_ip` varchar(40) NOT NULL default \'\',
            `bbcodeoff` int(1) NOT NULL default \'0\',
            `smileyoff` int(1) NOT NULL default \'0\',
            `cssoff` int(1) NOT NULL default \'0\',
            `usersig` int(1) NOT NULL default \'0\',
            `emailnotify` int(1) NOT NULL default \'0\',
            `thread_id` int(5) NOT NULL default \'0\',
            `forum_id` mediumint(10) NOT NULL default \'0\',
            `file` varchar(200) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `auteur_id` (`auteur_id`),
            KEY `thread_id` (`thread_id`),
            KEY `forum_id` (`forum_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of forums messages database table
     */
    function updateForumsMessagesRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList))
            $setFields['txt'] = $vars['bbcode']->apply(stripslashes($row['txt']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_forums_messages');

    if ($udt->fieldExist('auteur_ip')) {
        if ($udt->getFieldType('auteur_ip') != 'varchar(40)')
            $udt->modifyField('auteur_ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));
    }
    else {
        die(sprintf($this->_i18n['MISSING_FIELD'], 'auteur_ip', $this->data['db_prefix'] .'_forums_messages'));
    }

    $udt->modifyTable();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('txt')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'txt');
    }

    return $udt->execute('id', 'updateForumsMessagesRow');
}

?>