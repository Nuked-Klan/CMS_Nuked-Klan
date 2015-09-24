<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_userbox`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_userbox` (
            `mid` int(50) NOT NULL auto_increment,
            `user_from` varchar(30) NOT NULL default \'\',
            `user_for` varchar(30) NOT NULL default \'\',
            `titre` varchar(50) NOT NULL default \'\',
            `message` text NOT NULL,
            `date` varchar(30) NOT NULL default \'\',
            `status` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`mid`),
            KEY `user_from` (`user_from`),
            KEY `user_for` (`user_for`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of userbox database table
     */
    function updateUserboxRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList))
            $setFields['signature'] = $vars['bbcode']->apply(stripslashes($row['signature']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_userbox');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('message')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'message');
    }

    return $udt->execute('mid', 'updateUserboxRow');
}

?>