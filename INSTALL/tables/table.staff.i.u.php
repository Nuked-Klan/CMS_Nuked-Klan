<?php
/**
 * table.staff.i.u.php
 *
 * `[PREFIX]_staff` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_staff');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$staffTableCfg = array(
    'fields' => array(
        'id'           => array('type' => 'int(11)',     'null' => false, 'autoIncrement' => true),
        'membre_id'    => array('type' => 'varchar(20)', 'null' => false, 'default' => '\'\''),
        'categorie_id' => array('type' => 'int(11)',     'null' => false, 'default' => '\'0\''),
        'date'         => array('type' => 'int(11)',     'null' => false, 'default' => '\'0\''),
        'status_id'    => array('type' => 'varchar(25)', 'null' => false, 'default' => '\'\''),
        'rang_id'      => array('type' => 'varchar(25)', 'null' => false, 'default' => '\'\'')
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
    $dbTable->createTable($staffTableCfg);

?>