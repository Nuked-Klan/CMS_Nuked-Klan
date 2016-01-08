<?php
/**
 * table.userbox.c.i.u.php
 *
 * `[PREFIX]_userbox` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_userbox');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$userboxTableCfg = array(
    'fields' => array(
        'mid'       => array('type' => 'int(50)',     'null' => false, 'autoIncrement' => true),
        'user_from' => array('type' => 'varchar(30)', 'null' => false, 'default' => '\'\''),
        'user_for'  => array('type' => 'varchar(30)', 'null' => false, 'default' => '\'\''),
        'titre'     => array('type' => 'varchar(50)', 'null' => false, 'default' => '\'\''),
        'message'   => array('type' => 'text',        'null' => false),
        'date'      => array('type' => 'varchar(30)', 'null' => false, 'default' => '\'\''),
        'status'    => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\'')
    ),
    'primaryKey' => array('mid'),
    'index' => array(
        'user_from' => 'user_from',
        'user_for'  => 'user_for'
    ),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of userbox database table
 */
function updateUserboxRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['message'] = $vars['bbcode']->apply(stripslashes($row['message']));

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table exist in 1.6.x version
    $dbTable->checkIntegrity('mid', 'message');
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
    $dbTable->createTable($userboxTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'message');
    }

    $dbTable->applyUpdateFieldListToData('mid', 'updateUserboxRow');
}

?>