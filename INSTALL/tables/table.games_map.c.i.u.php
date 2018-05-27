<?php
/**
 * table.games_map.c.i.u.php
 *
 * `[PREFIX]_games_map` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(GAMES_MAP_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$gamesMapTableCfg = array(
    'fields' => array(
        'id'          => array('type' => 'int(11)',      'null' => false, 'unsigned' => true, 'autoIncrement' => true),
        'name'        => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'image'       => array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''),
        'game'        => array('type' => 'int(11)',      'null' => false, 'unsigned' => true, 'default' => '\'0\''),
        'description' => array('type' => 'text',         'null' => false)
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    if ($dbTable->tableExist())
        $dbTable->checkIntegrity();
    else
        $dbTable->setJqueryAjaxResponse('NO_TABLE_TO_CHECK_INTEGRITY');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation') {
    if ($dbTable->tableExist())
        $dbTable->checkAndConvertCharsetAndCollation();
    else
        $dbTable->setJqueryAjaxResponse('NO_TABLE_TO_CONVERT');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

// install / update 1.8
if ($process == 'install' || ($process == 'createTable' && ! $dbTable->tableExist()))
    $dbTable->createTable($gamesMapTableCfg);

if ($process == 'install') {
    $sql = 'INSERT INTO `'. GAMES_MAP_TABLE .'`
        (`name`, `game`)
        VALUES
        (\'de_dust2\', 1),
        (\'de_inferno\', 1);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

?>
