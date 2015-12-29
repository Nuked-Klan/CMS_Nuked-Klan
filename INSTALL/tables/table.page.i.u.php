<?php
/**
 * table.page.i.u.php
 *
 * `[PREFIX]_page` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_page');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$pageTableCfg = array(
    'fields' => array(
        'id'         => array('type' => 'int(11)',     'null' => false, 'autoIncrement' => true),
        'niveau'     => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\''),
        'titre'      => array('type' => 'varchar(50)', 'null' => false, 'default' => '\'\''),
        'content'    => array('type' => 'text',        'null' => false),
        'url'        => array('type' => 'varchar(80)', 'null' => false, 'default' => '\'\''),
        'type'       => array('type' => 'varchar(5)',  'null' => false, 'default' => '\'\''),
        'show_title' => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\''),
        'members'    => array('type' => 'text',        'null' => false)
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'titre' => 'titre'
    ),
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

if ($process == 'drop')
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

// install / update 1.8
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist()))
    $dbTable->createTable($pageTableCfg);

?>