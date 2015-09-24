<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_defie`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_defie` (
            `id` int(11) NOT NULL auto_increment,
            `send` varchar(12) NOT NULL default \'\',
            `pseudo` text NOT NULL,
            `clan` text NOT NULL,
            `mail` varchar(80) NOT NULL default \'\',
            `icq` varchar(50) NOT NULL default \'\',
            `irc` varchar(50) NOT NULL default \'\',
            `url` varchar(200) NOT NULL default \'\',
            `pays` text NOT NULL,
            `date` varchar(20) NOT NULL default \'\',
            `heure` varchar(10) NOT NULL default \'\',
            `serveur` text NOT NULL,
            `game` int(11) NOT NULL default \'0\',
            `type` text NOT NULL,
            `map` text NOT NULL,
            `comment` text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of defy database table
     */
    function updateDefyRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('BBCODE', $updateList))
            $setFields['comment'] = $vars['bbcode']->apply(stripslashes($row['comment']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_defie');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('comment')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'comment');
    }

    return $udt->execute('id', 'updateDefyRow');
}

?>