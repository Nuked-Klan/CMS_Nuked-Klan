<?php
/**
 * table.users_detail.c.i.php
 *
 * `[PREFIX]_users_detail` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(USER_DETAIL_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$usersDetailTableCfg = array(
    'fields' => array(
        'user_id'     => array('type' => 'varchar(20)',  'null' => false, 'default' => '\'0\''),
        'prenom'      => array('type' => 'text',         'null' => true),
        'age'         => array('type' => 'varchar(10)',  'null' => false, 'default' => '\'\''),
        'sexe'        => array('type' => 'varchar(20)',  'null' => false, 'default' => '\'\''),
        'ville'       => array('type' => 'text',         'null' => true),
        'photo'       => array('type' => 'varchar(150)', 'null' => false, 'default' => '\'\''),
        'motherboard' => array('type' => 'text',         'null' => true),
        'cpu'         => array('type' => 'varchar(50)',  'null' => true,  'default' => 'NULL'),
        'ram'         => array('type' => 'varchar(10)',  'null' => false, 'default' => '\'\''),
        'video'       => array('type' => 'text',         'null' => true),
        'resolution'  => array('type' => 'text',         'null' => true),
        'son'         => array('type' => 'text',         'null' => true),
        'ecran'       => array('type' => 'text',         'null' => true),
        'souris'      => array('type' => 'text',         'null' => true),
        'clavier'     => array('type' => 'text',         'null' => true),
        'connexion'   => array('type' => 'text',         'null' => true),
        'system'      => array('type' => 'text',         'null' => true),
        'pref_1'      => array('type' => 'text',         'null' => false),
        'pref_2'      => array('type' => 'text',         'null' => false),
        'pref_3'      => array('type' => 'text',         'null' => false),
        'pref_4'      => array('type' => 'text',         'null' => false),
        'pref_5'      => array('type' => 'text',         'null' => false)
    ),
    'primaryKey' => array('user_id'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table exist in 1.6.x version
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
    $dbTable->createTable($usersDetailTableCfg);

?>