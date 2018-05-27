<?php
/**
 * table.banned.c.i.u.php
 *
 * `[PREFIX]_banned` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(BANNED_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$bannedTableCfg = array(
    'fields' => array(
        'id'     => array('type' => 'int(11)',     'null' => false, 'autoIncrement' => true),
        'ip'     => array('type' => 'varchar(40)', 'null' => false, 'default' => '\'\''),
        'pseudo' => array('type' => 'varchar(50)', 'null' => false, 'default' => '\'\''),
        'email'  => array('type' => 'varchar(80)', 'null' => false, 'default' => '\'\''),
        'date'   => array('type' => 'varchar(20)', 'null' => true),
        'dure'   => array('type' => 'varchar(20)', 'null' => true),
        'texte'  => array('type' => 'text',        'null' => false)
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

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
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install')
    $dbTable->createTable($bannedTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.9 RC1
    if (! $dbTable->fieldExist('date'))
        $dbTable->addField('date', $bannedTableCfg['fields']['date'], 'email');

    // install / update 1.7.9 RC1
    if (! $dbTable->fieldExist('dure'))
        $dbTable->addField('dure', $bannedTableCfg['fields']['dure'], 'date');

    // install / update 1.7.14
    if ($dbTable->getFieldType('ip') != 'varchar(40)')
        $dbTable->modifyField('ip', $bannedTableCfg['fields']['ip']);
}

?>
