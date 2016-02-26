<?php
/**
 * table.discussion.c.fk.i.u.php
 *
 * `[PREFIX]_discussion` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(DISCUSSION_TABLE);

require_once 'includes/fkLibs/authorForeignKey.php';

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$discussionTableCfg = array(
    'fields' => array(
        'id'       => array('type' => 'int(11)',     'null' => false, 'autoIncrement' => true),
        'date'     => array('type' => 'varchar(30)', 'null' => false,  'default' => '\'0\''),
        'author'   => array('type' => 'varchar(30)', 'null' => false),
        'authorId' => array('type' => 'varchar(20)', 'null' => true,  'default' => '\'\''),
        'texte'    => array('type' => 'text',        'null' => false)
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
 * Callback function for update row of discussion database table
 */
function updateDiscussionDbTableRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['texte'] = $vars['bbcode']->apply(stripslashes($row['texte']));

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
    if ($process == 'checkIntegrity' && $dbTable->tableExist()) {
        // 
        $dbTable->checkIntegrity('id', array('pseudo', null), 'texte');
    }
    else
        $dbTable->setJqueryAjaxResponse('NO_TABLE_TO_CHECK_INTEGRITY');
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

$discussionTableCreated = false;

// install / update 1.7.9 RC1
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist())) {
    $dbTable->createTable($discussionTableCfg);

    $discussionTableCreated = true;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    if ($discussionTableCreated)
        return;

    // install / update 1.8
    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine(DISCUSSION_TABLE) == 'MyISAM')
        $this->_db->execute('ALTER TABLE `'. DISCUSSION_TABLE .'` ENGINE=InnoDB;');

    if ($dbTable->fieldExist('pseudo') && $dbTable->getFieldType('pseudo') == 'text') {
        $dbTable->modifyField('pseudo', array_merge(array('newField' => 'authorId'), $discussionTableCfg['fields']['authorId']))
            ->addFieldIndex('authorId')
            ->setUpdateFieldData('UPDATE_AUTHOR_DATA', array('authorId', 'author'));
    }

    if (! $dbTable->fieldExist('author')) {
        $dbTable->addField('author', $discussionTableCfg['fields']['author']);
        $dbTable->addFieldIndex('author');
    }

    // Update BBcode
    // update 1.7.9 RC3
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'texte');
    }

    $dbTable->applyUpdateFieldListToData();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Add foreign key of table
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'addForeignKey') {
    if (! $dbTable->foreignKeyExist('FK_discussion_authorId'))
        addAuthorIdForeignKey('discussion');

    if (! $dbTable->foreignKeyExist('FK_discussion_author'))
        addAuthorForeignKey('discussion');
}

?>