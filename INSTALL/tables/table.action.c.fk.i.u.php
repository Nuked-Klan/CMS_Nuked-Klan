<?php
/**
 * table.action.i.u.php
 *
 * `[PREFIX]_action` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_action');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$actionTableCfg = array(
    'fields' => array(
        'id'       => array('type' => 'int(11)',     'null' => false, 'autoIncrement' => true),
        'date'     => array('type' => 'varchar(30)', 'null' => true,  'default' => '\'0\''),
        'author'   => array('type' => 'varchar(30)', 'null' => false),
        'authorId' => array('type' => 'varchar(20)', 'null' => true,  'default' => '\'\''),
        'action'   => array('type' => 'text',        'null' => false)
    ),
    'primaryKey' => 'id',
    'index' => array(
        'author'   => 'author',
        'authorId' => 'authorId'
    ),
    'engine' => 'InnoDB'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of action database table
 */
function updateActionRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('UPDATE_AUTHOR', $updateList)) {
        $dbrUsers = $vars['db']->selectOne(
            'SELECT `pseudo`
            FROM `'. $vars['dbPrefix'] .'_users`
            WHERE id = '. $row['authorId']
        );

        $setFields['author'] = $dbrUsers['pseudo'];
    }

    return $setFields;
}

/*
 * Add author Id foreign key of action database table
 */
function addAuthorIdForeignKey($dbTable, $dbPrefix) {
    $dbTable->addForeignKey(
        'FK_action_authorId', 'authorId',
        $dbPrefix .'_users', 'id',
        array('ON DELETE SET NULL')
    );
}

/*
 * Add author Id foreign key of action database table
 */
function addAuthorForeignKey($dbTable, $dbPrefix) {
    $dbTable->addForeignKey(
        'FK_action_author', 'author',
        $dbPrefix .'_users', 'pseudo',
        array('ON UPDATE CASCADE')
    );
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity' && $dbTable->tableExist()) {
    // table and field exist in 1.7.9 RC1 version
    $dbTable->checkIntegrity('id', ('pseudo', null));
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

// install /update 1.7.9 RC1
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist()))
    $dbTable->createTable($actionTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Add foreign key of table
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'addForeignKey') {
    addAuthorIdForeignKey($dbTable, $this->_session['db_prefix']);
    addAuthorForeignKey($dbTable, $this->_session['db_prefix']);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    if ($dbTable->fieldExist('pseudo') && $dbTable->getFieldType('pseudo') == 'text') {
        $dbTable->modifyField('pseudo', array_merge(array('newField' => 'authorId'), $actionTableCfg['fields']['authorId']));
        $dbTable->addFieldIndex('authorId');
        $dbTable->setCallbackFunctionVars(array('dbPrefix' => $this->_session['db_prefix'], 'db' => $this->_db))
            ->setUpdateFieldData('UPDATE_AUTHOR', 'authorId');
    }

    if (! $dbTable->fieldExist('author')) {
        $dbTable->addField('author', $actionTableCfg['fields']['author']);
        $dbTable->addFieldIndex('author');
    }

    // TODO : Add them after update ?
    if (! $dbTable->foreignKeyExist('FK_action_authorId'))
        addAuthorIdForeignKey($dbTable, $this->_session['db_prefix']);

    if (! $dbTable->foreignKeyExist('FK_action_author'))
        addAuthorForeignKey($dbTable, $this->_session['db_prefix']);

    $dbTable->alterTable();

    $dbTable->applyUpdateFieldListToData('id', 'updateActionRow');
}

?>