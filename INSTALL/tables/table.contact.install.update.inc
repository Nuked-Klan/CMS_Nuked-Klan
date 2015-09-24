<?php

// TODO : champ ip a revoir

// install / update 1.7.9 RC3
if ($this->data['process'] == 'install' || ($this->data['process'] == 'update' && ! $this->tableExist('contact'))) {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_contact`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_contact` (
            `id` int(11) NOT NULL auto_increment,
            `titre` varchar(200) NOT NULL default \'\',
            `message` text NOT NULL,
            `email` varchar(80) NOT NULL default \'\',
            `nom` varchar(200) NOT NULL default \'\',
            `ip` varchar(50) NOT NULL default \'\',
            `date` varchar(30) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `titre` (`titre`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}

if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of contact database table
     */
    function updateContactRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList))
            $setFields['message'] = $vars['bbcode']->apply(stripslashes($row['message']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_contact');

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('message')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'message');
    }

    return $udt->execute('id', 'updateContactRow');
}

?>