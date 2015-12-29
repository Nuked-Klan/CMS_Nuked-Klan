<?php
/**
 * table.stats_visitor.c.i.u.php
 *
 * `[PREFIX]_stats_visitor` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_stats_visitor');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$statsVisitorTableCfg = array(
    'fields' => array(
        'id'      => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'user_id' => array('type' => 'varchar(20)',  'null' => false, 'default' => '\'\''),
        'ip'      => array('type' => 'varchar(40)',  'null' => false, 'default' => '\'\''),
        'host'    => array('type' => 'varchar(100)', 'null' => false, 'default' => '\'\''),
        'browser' => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'os'      => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'referer' => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'day'     => array('type' => 'int(2)',       'null' => false, 'default' => '\'0\''),
        'month'   => array('type' => 'int(2)',       'null' => false, 'default' => '\'0\''),
        'year'    => array('type' => 'int(4)',       'null' => false, 'default' => '\'0\''),
        'hour'    => array('type' => 'int(2)',       'null' => false, 'default' => '\'0\''),
        'date'    => array('type' => 'varchar(30)',  'null' => false, 'default' => '\'\'')
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'user_id' => 'user_id',
        'host'    => 'host',
        'browser' => 'browser',
        'os'      => 'os',
        'referer' => 'referer'
    ),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table create in 1.7.x version
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

if ($process == 'drop')
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install')
    $dbTable->createTable($statsVisitorTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.14
    if ($dbTable->fieldExist('ip') && $dbTable->getFieldType('ip') != 'varchar(40)')
        $dbTable->modifyField('ip', $statsVisitorTableCfg['fields']['ip']);
}

?>