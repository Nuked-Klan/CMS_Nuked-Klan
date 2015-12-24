<?php
/**
 * table.vote.c.i.u.php
 *
 * `[PREFIX]_vote` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_vote');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('ip');
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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_vote` (
            `id` int(11) NOT NULL auto_increment,
            `module` varchar(30) NOT NULL default \'0\',
            `vid` int(100) default NULL,
            `ip` varchar(40) NOT NULL default \'\',
            `vote` int(2) default NULL,
            PRIMARY KEY  (`id`),
            KEY `vid` (`vid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.14
    if ($dbTable->getFieldType('ip') != 'varchar(40)')
        $dbTable->modifyField('ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));

    $dbTable->alterTable();
}

?>