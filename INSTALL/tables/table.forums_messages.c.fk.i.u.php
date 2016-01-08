<?php
/**
 * table.forums_messages.c.i.u.php
 *
 * `[PREFIX]_forums_messages` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_forums_messages');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$forumMsgTableCfg = array(
    'fields' => array(
        'id'          => array('type' => 'int(5)',        'null' => false, 'autoIncrement' => true),
        'titre'       => array('type' => 'text',          'null' => false),
        'txt'         => array('type' => 'text',          'null' => false),
        'date'        => array('type' => 'varchar(12)',   'null' => false, 'default' => '\'\''),
        'edition'     => array('type' => 'text',          'null' => false),
        'auteur'      => array('type' => 'varchar(30)',   'null' => false),
        'auteur_id'   => array('type' => 'varchar(20)',   'null' => true,  'default' => '\'\''),
        'auteur_ip'   => array('type' => 'varchar(40)',   'null' => false, 'default' => '\'\''),
        'bbcodeoff'   => array('type' => 'int(1)',        'null' => false, 'default' => '\'0\''),
        'smileyoff'   => array('type' => 'int(1)',        'null' => false, 'default' => '\'0\''),
        'cssoff'      => array('type' => 'int(1)',        'null' => false, 'default' => '\'0\''),
        'usersig'     => array('type' => 'int(1)',        'null' => false, 'default' => '\'0\''),
        'emailnotify' => array('type' => 'int(1)',        'null' => false, 'default' => '\'0\''),
        'thread_id'   => array('type' => 'int(5)',        'null' => false, 'default' => '\'0\''),
        'forum_id'    => array('type' => 'mediumint(10)', 'null' => false, 'default' => '\'0\''),
        'file'        => array('type' => 'varchar(200)',  'null' => false, 'default' => '\'\'')
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'auteur'    => 'auteur',
        'auteur_id' => 'auteur_id',
        'thread_id' => 'thread_id',
        'forum_id'  => 'forum_id'
    ),
    'engine' => 'InnoDB'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of forums messages database table
 */
function updateForumsMessagesRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['txt'] = $vars['bbcode']->apply(stripslashes($row['txt']));

    return $setFields;
}

/*
 * Add author Id foreign key of forums messages database table
 */
function addAuthorIdForeignKey($dbTable, $dbprefix) {
    $dbTable->addForeignKey(
        'FK_forumMessages_authorId', 'auteur_id',
        $dbprefix .'_users', 'id',
        array('ON DELETE SET NULL')
    );
}

/*
 * Add author Id foreign key of forums messages database table
 */
function addAuthorForeignKey($dbTable, $dbprefix) {
    $dbTable->addForeignKey(
        'FK_forumMessages_author', 'auteur',
        $dbprefix .'_users', 'pseudo',
        array('ON UPDATE CASCADE')
    );
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'auteur', 'auteur_id', 'txt', 'auteur_ip');
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
    $dbTable->createTable($forumMsgTableCfg);

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
    if ($dbTable->fieldExist('auteur_ip') && $dbTable->getFieldType('auteur_ip') != 'varchar(40)')
        $dbTable->modifyField('auteur_ip', $forumMsgTableCfg['fields']['auteur_ip']);

    // install / update 1.8
    if ($dbTable->fieldExist('auteur')) {
        if ($dbTable->fieldExist('auteur') && $dbTable->getFieldType('auteur') != 'varchar(30)')
            $dbTable->modifyField('auteur', $forumMsgTableCfg['fields']['auteur']);

        if (! $dbTable->checkFieldIsIndex('auteur'))
            $dbTable->addFieldIndex('auteur');
    }

    if ($dbTable->fieldExist('auteur_id')) {
        if (! $dbTable->checkFieldIsNull('auteur_id'))
            $dbTable->modifyField('auteur_id', $forumMsgTableCfg['fields']['auteur_id']);

        if (! $dbTable->checkFieldIsIndex('auteur_id'))
            $dbTable->addFieldIndex('auteur_id');
    }

    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine($this->_session['db_prefix'] .'_forums_messages'))
        $this->_db->execute('ALTER TABLE `'. $this->_session['db_prefix'] .'_forums_messages` ENGINE=InnoDB;');

    if (! $dbTable->foreignKeyExist('FK_forumMessages_authorId'))
        addAuthorIdForeignKey($dbTable, $this->_session['db_prefix']);

    if (! $dbTable->foreignKeyExist('FK_forumMessages_author'))
        addAuthorForeignKey($dbTable, $this->_session['db_prefix']);

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'txt');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateForumsMessagesRow');
}

?>