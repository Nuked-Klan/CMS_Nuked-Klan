<?php
/**
 * table.vote_modules.c.i.u.php
 *
 * `[PREFIX]_vote_modules` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(VOTE_MODULES_TABLE);

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

// install / update 1.7.9 RC1
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist())) {
    $dbTable->createTable($voteModulesTableCfg);

    $sql = 'INSERT INTO `'. VOTE_MODULES_TABLE .'` (`id`, `module`, `active`) VALUES
        (1, \'download\', 1),
        (2, \'links\', 1),
        (3, \'gallery\', 1),
        (4, \'sections\', 1);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

?>