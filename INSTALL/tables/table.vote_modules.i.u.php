<?php
/**
 * table.vote_modules.i.u.php
 *
 * `[PREFIX]_vote_modules` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_vote_modules');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$voteModulesTableCfg = array(
    'fields' => array(
        'id'      => array('type' => 'tinyint(2)',  'null' => false, 'unsigned' => true, 'autoIncrement' => true),
        'module'  => array('type' => 'varchar(50)', 'null' => false),
        'active'  => array('type' => 'tinyint(1)',  'null' => false, 'unsigned' => true)
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

if ($process == 'drop')
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

// install / update 1.7.9 RC1
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist())) {
    $dbTable->createTable($voteModulesTableCfg);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_vote_modules` (`id`, `module`, `active`) VALUES
        (1, \'Download\', 1),
        (2, \'Links\', 1),
        (3, \'Gallery\', 1),
        (4, \'Sections\', 1);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

?>