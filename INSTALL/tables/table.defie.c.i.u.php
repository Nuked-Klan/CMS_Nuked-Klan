<?php
/**
 * table.defie.c.i.u.php
 *
 * `[PREFIX]_defie` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_defie');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$defyTableCfg = array(
    'fields' => array(
        'id'      => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'send'    => array('type' => 'varchar(12)',  'null' => false, 'default' => '\'\''),
        'pseudo'  => array('type' => 'text',         'null' => false),
        'clan'    => array('type' => 'text',         'null' => false),
        'mail'    => array('type' => 'varchar(80)',  'null' => false, 'default' => '\'\''),
        'icq'     => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'irc'     => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'url'     => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'pays'    => array('type' => 'text',         'null' => false),
        'date'    => array('type' => 'varchar(20)',  'null' => false, 'default' => '\'\''),
        'heure'   => array('type' => 'varchar(10)',  'null' => false, 'default' => '\'\''),
        'serveur' => array('type' => 'text',         'null' => false),
        'game'    => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'type'    => array('type' => 'text',         'null' => false),
        'map'     => array('type' => 'text',         'null' => false),
        'comment' => array('type' => 'text',         'null' => false)
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of defy database table
 */
function updateDefyRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['comment'] = $vars['bbcode']->apply(stripslashes($row['comment']));

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'comment');
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
    $dbTable->createTable($defyTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'comment');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateDefyRow');
}

?>