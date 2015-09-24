<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_serveur_cat`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_serveur_cat` (
            `cid` int(30) NOT NULL auto_increment,
            `titre` varchar(30) NOT NULL default \'\',
            `description` text NOT NULL,
            PRIMARY KEY  (`cid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of server category database table
     */
    function updateServerCatRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList))
            $setFields['signature'] = $vars['bbcode']->apply(stripslashes($row['signature']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_serveur_cat');

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('description')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'description');
    }

    return $udt->execute('cid', 'updateServerCatRow');
}

?>