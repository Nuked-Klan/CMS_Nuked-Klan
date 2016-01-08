<?php
/**
 * table.games_prefs.c.i.php
 *
 * `[PREFIX]_games_prefs` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_games_prefs');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$gamesPrefsTableCfg = array(
    'fields' => array(
        'id'      => array('type' => 'int(11)',     'null' => false, 'autoIncrement' => true),
        'name'    => array('type' => 'int(11)',     'null' => false, 'default' => '\'0\''),
        'user_id' => array('type' => 'varchar(20)', 'null' => false, 'default' => '\'\''),
        'pref_1'  => array('type' => 'text',        'null' => false),
        'pref_2'  => array('type' => 'text',        'null' => false),
        'pref_3'  => array('type' => 'text',        'null' => false),
        'pref_4'  => array('type' => 'text',        'null' => false),
        'pref_5'  => array('type' => 'text',        'null' => false),
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table create in 1.7.x version
    $dbTable->checkIntegrity();
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
    $dbTable->createTable($gamesPrefsTableCfg);

?>