<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_comment`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_comment` (
            `id` int(10) NOT NULL auto_increment,
            `module` varchar(30) NOT NULL default \'0\',
            `im_id` int(100) default NULL,
            `autor` text,
            `autor_id` varchar(20) NOT NULL default \'\',
            `titre` text NOT NULL,
            `comment` text,
            `date` varchar(12) default NULL,
            `autor_ip` varchar(40) default NULL,
            PRIMARY KEY  (`id`),
            KEY `im_id` (`im_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of comment database table
     */
    function updateCommentRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList))
            $setFields['comment'] = $vars['bbcode']->apply(stripslashes($row['comment']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_comment');

    // install / update 1.7.13
    if ($udt->fieldExist('autor_ip')) {
        if ($udt->getFieldType('autor_ip') != 'varchar(40)')
            $udt->modifyField('autor_ip', array('type' => 'VARCHAR(40)', 'default' => 'NULL'));
    }
    else {
        die(sprintf($this->_i18n['MISSING_FIELD'], 'autor_ip', $this->data['db_prefix'] .'_comment'));
    }

    $udt->modifyTable();

    // Update BBcode
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('comment')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'comment');
    }

    return $udt->execute('id', 'updateCommentRow');
}

?>