<?php
/**
 * table.action.c.fk.i.u.php
 *
 * `[PREFIX]_action` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(ACTION_TABLE);

require_once 'includes/fkLibs/authorForeignKey.php';

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$actionTableCfg = array(
    'fields' => array(
        'id'       => array('type' => 'int(11)',     'null' => false, 'autoIncrement' => true),
        'date'     => array('type' => 'varchar(30)', 'null' => false, 'default' => '\'0\''),
        'author'   => array('type' => 'varchar(30)', 'null' => false),
        'authorId' => array('type' => 'varchar(20)', 'null' => true,  'default' => '\'\''),
        'action'   => array('type' => 'text',        'null' => false)
    ),
    'primaryKey' => array('id'),
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
function updateActionDbTableRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('UPDATE_AUTHOR_DATA', $updateList)) {
        $userData = getUserData($row['authorId']);

        if ($userData === false)
            $setFields['authorId'] = null;
        else
            $setFields['author'] = $userData['pseudo'];
    }

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    if ($dbTable->tableExist()) {
        // table and field exist in 1.7.9 RC1 version
        $dbTable->checkIntegrity('id', array('pseudo', null));
    }
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

// install /update 1.7.9 RC1
if ($process == 'install' || ($process == 'createTable' && ! $dbTable->tableExist())) {
    $dbTable->createTable($actionTableCfg);

    $actionTableCreated = true;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    if ($actionTableCreated)
        return;

    // install / update 1.8
    if ($dbTable->fieldExist('pseudo') && $dbTable->getFieldType('pseudo') == 'text') {
        $dbTable->modifyField('pseudo', array_merge(array('newField' => 'authorId'), $actionTableCfg['fields']['authorId']))
            ->addFieldIndex('authorId')
            ->setUpdateFieldData('UPDATE_AUTHOR_DATA', array('authorId', 'author'));
    }

    if (! $dbTable->fieldExist('author')) {
        $dbTable->addField('author', $actionTableCfg['fields']['author'])
            ->addFieldIndex('author');
    }

    $dbTable->applyUpdateFieldListToData();

    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine(ACTION_TABLE) == 'MyISAM')
        $this->_db->execute('ALTER TABLE `'. ACTION_TABLE .'` ENGINE=InnoDB;');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Add foreign key of table
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'addForeignKey') {
    if (! $dbTable->foreignKeyExist('FK_action_authorId'))
        addAuthorIdForeignKey('action');

    if (! $dbTable->foreignKeyExist('FK_action_author'))
        addAuthorForeignKey('action');
}

?>