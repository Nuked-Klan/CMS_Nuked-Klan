<?php
/**
 * table.staff_cat.i.u.php
 *
 * `[PREFIX]_staff_cat` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_staff_cat');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$staffCatTableCfg = array(
    'fields' => array(
        'sid'      => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'nom'      => array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''),
        'img'      => array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''),
        'coverage' => array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''),
        'ordre'    => array('type' => 'int(5)',       'null' => false, 'default' => '\'0\''),
        'tag'      => array('type' => 'text',         'null' => false),
        'tag2'     => array('type' => 'text',         'null' => false)
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
    $dbTable->createTable($staffCatTableCfg);

?>