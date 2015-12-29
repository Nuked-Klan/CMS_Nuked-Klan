<?php
/**
 * table.comment.c.i.u.php
 *
 * `[PREFIX]_comment` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_comment');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$commentTableCfg = array(
    'fields' => array(
        'id'       => array('type' => 'int(10)',     'null' => false, 'autoIncrement' => true),
        'module'   => array('type' => 'varchar(30)', 'null' => false, 'default' => '\'0\''),
        'im_id'    => array('type' => 'int(100)',    'null' => true,  'default' => 'NULL'),
        'autor'    => array('type' => 'varchar(30)', 'null' => false),
        'autor_id' => array('type' => 'varchar(20)', 'null' => true,  'default' => '\'\''),
        'titre'    => array('type' => 'text',        'null' => false),
        'comment'  => array('type' => 'text',        'null' => true),
        'date'     => array('type' => 'varchar(12)', 'null' => true,  'default' => 'NULL'),
        'autor_ip' => array('type' => 'varchar(40)', 'null' => true,  'default' => 'NULL')
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'im_id'     => 'im_id',
        'autor'     => 'autor',
        'autor_id'  => 'autor_id'
    ),
    'engine' => 'InnoDB'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of comment database table
 */
function updateCommentRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['comment'] = $vars['bbcode']->apply(stripslashes($row['comment']));

    return $setFields;
}

/*
 * Add author Id foreign key of comment database table
 */
function addAuthorIdForeignKey($dbTable, $dbprefix) {
    $dbTable->addForeignKey(
        'FK_comment_authorId', 'autor_id',
        $dbprefix .'_users', 'id',
        array('ON DELETE SET NULL')
    );
}

/*
 * Add author Id foreign key of comment database table
 */
function addAuthorForeignKey($dbTable, $dbprefix) {
    $dbTable->addForeignKey(
        'FK_comment_author', 'autor',
        $dbprefix .'_users', 'pseudo',
        array('ON UPDATE CASCADE')
    );
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field (id, autor, comment & autor_ip) exist in 1.6.x version
    // autor_id exist in 1.7.x version
    $dbTable->checkIntegrity('id', 'autor', 'autor_id', 'comment', 'autor_ip');
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
    $dbTable->createTable($commentTableCfg);

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
    // install / update 1.7.14
    if ($dbTable->fieldExist('autor_ip') && $dbTable->getFieldType('autor_ip') != 'varchar(40)')
        $dbTable->modifyField('autor_ip', $commentTableCfg['fields']['autor_ip']);

    // install / update 1.8
    if ($dbTable->fieldExist('autor')) {
        if ($dbTable->getFieldType('autor') != 'varchar(30)' || $dbTable->checkFieldIsNull('autor'))
            $dbTable->modifyField('autor', $commentTableCfg['fields']['autor']);

        if (! $dbTable->checkFieldIsIndex('autor'))
            $dbTable->addFieldIndex('autor');
    }

    if ($dbTable->fieldExist('autor_id')) {
        if (! $dbTable->checkFieldIsNull('autor_id'))
            $dbTable->modifyField('autor_id', $commentTableCfg['fields']['autor_id']);

        if (! $dbTable->checkFieldIsIndex('autor_id'))
            $dbTable->addFieldIndex('autor_id');
    }

    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine($this->_session['db_prefix'] .'_comment'))
        $this->_db->execute('ALTER TABLE `'. $this->_session['db_prefix'] .'_comment` ENGINE=InnoDB;');

    if (! $dbTable->foreignKeyExist('FK_comment_authorId'))
        addAuthorIdForeignKey($dbTable, $this->_session['db_prefix']);

    if (! $dbTable->foreignKeyExist('FK_comment_author'))
        addAuthorForeignKey($dbTable, $this->_session['db_prefix']);

    // Update BBcode
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'comment');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateCommentRow');
}

?>