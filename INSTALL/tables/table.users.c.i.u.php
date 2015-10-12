<?php
/**
 * table.users.c.i.u.php
 *
 * `[PREFIX]_users` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_users');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of user database table
 */
function updateUserRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('UPDATE_PASSWORD', $updateList))
        $setFields['pass'] = hash::apply($vars['HASHKEY'], $row['pass']);

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['signature'] = $vars['bbcode']->apply(stripslashes($row['signature']));

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'pass', 'signature');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_users` (
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
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install 1.7.9 RC3 - update 1.7.9 RC1
    if (! $dbTable->fieldExist('erreur'))
        $dbTable->addField('erreur', array('type' => 'INT(10)', 'null' => false, 'default' => '\'0\''));

    // install / update 1.7.9
    if (! $dbTable->fieldExist('token'))
        $dbTable->addField('token', array('type' => 'VARCHAR(13)', 'null' => true, 'default' => 'NULL'))
             ->addField('token_time', array('type' => 'VARCHAR(10)', 'null' => false, 'default' => '\'0\''));

    $dbTable->alterTable();

    if (! isset($this->_session['updateUserPassword'])) {
        $sql = 'SELECT pass
            FROM `'. $this->_session['db_prefix'] .'_users`
            ORDER BY RAND()
            LIMIT 1';

        $dbsUsers = $this->_db->selectOne($sql);
        $firstChr = substr($dbsUsers['pass'], 0, 1);

        if ($firstChr != '%' && $firstChr != '#')
            $this->_session['updateUserPassword'] = true;
        else
            $this->_session['updateUserPassword'] = false;
    }

    if ($this->_session['updateUserPassword']) {
        $dbTable->setCallbackFunctionVars(array('HASHKEY' => $this->_session['HASHKEY']))
            ->setUpdateFieldData('UPDATE_PASSWORD', 'pass');
    }

    // update 1.7.9 RC3
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'signature');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateUserRow');
}

?>