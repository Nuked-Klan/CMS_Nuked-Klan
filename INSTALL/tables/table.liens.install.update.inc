<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_liens`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_liens` (
            `id` int(10) NOT NULL auto_increment,
            `date` varchar(12) NOT NULL default \'\',
            `titre` text NOT NULL,
            `description` text NOT NULL,
            `url` varchar(200) NOT NULL default \'\',
            `cat` int(11) NOT NULL default \'0\',
            `webmaster` text NOT NULL,
            `country` varchar(50) NOT NULL default \'\',
            `count` int(11) NOT NULL default \'0\',
            `broke` int(11) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of links database table
     */
    function updateLinksRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList))
            $setFields['description'] = $vars['bbcode']->apply(stripslashes($row['description']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_liens');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('description')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'description');
    }

    return $udt->execute('id', 'updateLinksRow');
}

?>