<?php
/**
 * table.team_members.i.u.php
 *
 * `[PREFIX]_team_members` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(TEAM_MEMBERS_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$teamMembersTableCfg = array(
    'fields' => array(
        'id'     => array('type' => 'int(11)',     'null' => false, 'autoIncrement' => true),
        'userId' => array('type' => 'varchar(20)', 'null' => false, 'default' => '\'\''),
        'team'   => array('type' => 'int(11)',     'null' => false, 'default' => '\'0\''),
        'date'   => array('type' => 'int(11)',     'null' => false, 'default' => '\'0\''),
        'status' => array('type' => 'varchar(25)', 'null' => false, 'default' => '\'\''),
        'rank'   => array('type' => 'varchar(25)', 'null' => false, 'default' => '\'\'')
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

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

// install / update 1.8
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist()))
    $dbTable->createTable($teamMembersTableCfg);

?>