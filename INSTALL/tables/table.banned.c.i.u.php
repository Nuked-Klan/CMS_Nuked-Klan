<?php
/**
 * table.banned.c.i.u.php
 *
 * `[PREFIX]_banned` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_banned');

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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_banned` (
            `id` int(11) NOT NULL auto_increment,
            `ip` varchar(40) NOT NULL default \'\',
            `pseudo` varchar(50) NOT NULL default \'\',
            `email` varchar(80) NOT NULL default \'\',
            `date` VARCHAR(20)  NULL,
            `dure` VARCHAR(20)  NULL,
            `texte` text NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.9 RC1
    if (! $dbTable->fieldExist('date'))
        $dbTable->addField('date', array('type' => 'VARCHAR(20)', 'null' => true), 'email');

    // install / update 1.7.9 RC1
    if (! $dbTable->fieldExist('dure'))
        $dbTable->addField('dure', array('type' => 'VARCHAR(20)', 'null' => true), 'date');

    // install / update 1.7.14
    if ($dbTable->getFieldType('ip') != 'varchar(40)')
        $dbTable->modifyField('ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));

    $dbTable->alterTable();
}

?>