<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_guestbook`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_guestbook` (
            `id` int(9) NOT NULL auto_increment,
            `name` varchar(50) NOT NULL default \'\',
            `email` varchar(60) NOT NULL default \'\',
            `url` varchar(70) NOT NULL default \'\',
            `date` int(11) NOT NULL default \'0\',
            `host` varchar(60) NOT NULL default \'\',
            `comment` text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of guestbook database table
     */
    function updateGuestbookRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList))
            $setFields['comment'] = $vars['bbcode']->apply(stripslashes($row['comment']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_guestbook');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('comment')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'comment');
    }

    return $udt->execute('id', 'updateGuestbookRow');
}

?>