<?php

if ($this->data['process'] == 'install') {
    $sql = 'DROP TABLE IF EXISTS `'. $this->data['db_prefix'] .'_users`';
    mysql_query($sql) or die(mysql_error());

    $sql = 'CREATE TABLE `'. $this->data['db_prefix'] .'_users` (
            `id` varchar(20) NOT NULL default \'\',
            `team` varchar(80) NOT NULL default \'\',
            `team2` varchar(80) NOT NULL default \'\',
            `team3` varchar(80) NOT NULL default \'\',
            `rang` int(11) NOT NULL default \'0\',
            `ordre` int(5) NOT NULL default \'0\',
            `pseudo` text NOT NULL,
            `mail` varchar(80) NOT NULL default \'\',
            `email` varchar(80) NOT NULL default \'\',
            `icq` varchar(50) NOT NULL default \'\',
            `msn` varchar(80) NOT NULL default \'\',
            `aim` varchar(50) NOT NULL default \'\',
            `yim` varchar(50) NOT NULL default \'\',
            `url` varchar(150) NOT NULL default \'\',
            `pass` varchar(80) NOT NULL default \'\',
            `niveau` int(1) NOT NULL default \'0\',
            `date` varchar(30) NOT NULL default \'\',
            `avatar` varchar(100) NOT NULL default \'\',
            `signature` text NOT NULL,
            `user_theme` varchar(30) NOT NULL default \'\',
            `user_langue` varchar(30) NOT NULL default \'\',
            `game` int(11) NOT NULL default \'0\',
            `country` varchar(50) NOT NULL default \'\',
            `count` int(10) NOT NULL default \'0\',
            `erreur` INT(10) NOT NULL default \'0\',
            `token` varchar(13)  DEFAULT NULL,
            `token_time` varchar(10) NOT NULL DEFAULT \'0\',
            PRIMARY KEY  (`id`),
            KEY `team` (`team`),
            KEY `team2` (`team2`),
            KEY `team3` (`team3`),
            KEY `rang` (`rang`),
            KEY `game` (`game`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. $charset .' COLLATE='. $collate .';';
    mysql_query($sql) or die(mysql_error());

    return 'CREATED';
}
else if ($this->data['process'] == 'update') {

    /*
     * Callback function for update row of user database table
     */
    function updateUserRow($updateList, $row, $vars) {
        $setFields = array();

        if (in_array('UPDATE_PASSWORD', $updateList))
            $setFields['pass'] = install::hashPassword($vars['HASHKEY'], $row['pass']);

        if (in_array('BBCODE', $updateList))
            $setFields['signature'] = $vars['bbcode']->apply(stripslashes($row['signature']));

        return $setFields;
    }

    require_once 'class/updateDbTable.class.php';

    $udt = new updateDbTable($this->data['db_prefix'] .'_users');

    // install 1.7.9 RC3 - update 1.7.9 RC1
    if (! $udt->fieldExist('erreur'))
        $udt->addField('erreur', array('type' => 'INT(10)', 'null' => false, 'default' => '\'0\''));

    // install / update 1.7.9
    if (! $udt->fieldExist('token'))
        $udt->addField('token', array('type' => 'VARCHAR(13)', 'null' => true, 'default' => 'NULL'))
             ->addField('token_time', array('type' => 'VARCHAR(10)', 'null' => false, 'default' => '\'0\''));

    $udt->modifyTable();

    if (! isset($this->data['updateUserPassword'])) {
        $sql = 'SELECT pass
            FROM `'. $this->data['db_prefix'] .'_users`
            ORDER BY RAND()
            LIMIT 1';
        $req = mysql_query($sql);
        $row = mysql_fetch_assoc($req);

        $firstChr = substr($row['pass'], 0, 1);

        if ($firstChr != '%' && $firstChr != '#')
            $_SESSION['updateUserPassword'] = $this->data['updateUserPassword'] = true;
        else
            $_SESSION['updateUserPassword'] = $this->data['updateUserPassword'] = false;
    }

    if ($this->data['updateUserPassword']) {
        $udt->setCallbackFunctionVars(array('HASHKEY' => $this->data['HASHKEY']))
             ->updateFieldData('UPDATE_PASSWORD', 'pass');
    }

    // update 1.7.9 RC3
    if (version_compare($this->data['version'], '1.7.9', '<=') && $udt->fieldExist('signature')) {
        $udt->setCallbackFunctionVars(array('bbcode' => $this->_loadBBcode()))
             ->updateFieldData('BBCODE', 'signature');
    }

    return $udt->execute('id', 'updateUserRow');
}

?>