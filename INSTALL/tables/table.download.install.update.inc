<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_downloads`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_downloads` (
            `id` int(11) NOT NULL auto_increment,
            `date` varchar(12) NOT NULL default \'\',
            `taille` varchar(6) NOT NULL default \'0\',
            `titre` text NOT NULL,
            `description` text NOT NULL,
            `type` int(11) NOT NULL default \'0\',
            `count` int(10) NOT NULL default \'0\',
            `url` varchar(200) NOT NULL default \'\',
            `url2` varchar(200) NOT NULL default \'\',
            `broke` int(11) NOT NULL default \'0\',
            `url3` varchar(200) NOT NULL default \'\',
            `level` int(1) NOT NULL default \'0\',
            `hit` int(11) NOT NULL default \'0\',
            `edit` varchar(12) NOT NULL default \'\',
            `screen` varchar(200) NOT NULL default \'\',
            `autor` text NOT NULL,
            `url_autor` varchar(200) NOT NULL default \'\',
            `comp` text NOT NULL,
            PRIMARY KEY  (`id`),
            KEY `type` (`type`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of downloads database table
     */
    function updateDownloadsRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList))
            $setFields['description'] = $vars['bbcode']->apply(stripslashes($row['description']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_downloads');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('description')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'description');
    }

    return $udt->execute('id', 'updateDownloadsRow');
}

?>