<?php
/**
 * table.contact.c.i.u.php
 *
 * `[PREFIX]_contact` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_contact');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$contactTableCfg = array(
    'fields' => array(
        'id'      => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'titre'   => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'message' => array('type' => 'text',         'null' => false),
        'email'   => array('type' => 'varchar(80)',  'null' => false, 'default' => '\'\''),
        'nom'     => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'ip'      => array('type' => 'varchar(40)',  'null' => false, 'default' => '\'\''),
        'date'    => array('type' => 'varchar(30)',  'null' => false, 'default' => '\'\'')
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'titre' => 'titre'
    ),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of contact database table
 */
function updateContactRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['message'] = $vars['bbcode']->apply(stripslashes($row['message']));

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    if ($dbTable->tableExist())
        $dbTable->checkIntegrity('id', 'message', 'ip');
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

$contactTableCreated = false;

// install / update 1.7.9 RC3
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist())) {
    $dbTable->createTable($contactTableCfg);

    $contactTableCreated = true;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    if ($contactTableCreated)
        return;

    // install / update 1.7.14
    if ($dbTable->getFieldType('ip') != 'varchar(40)')
        $dbTable->modifyField('ip', $contactTableCfg['fields']['fields']);

    $dbTable->alterTable();

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'message');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateContactRow');
}

?>