<?php
/**
 * table.forums_messages.c.fk.i.u.php
 *
 * `[PREFIX]_forums_messages` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(FORUM_MESSAGES_TABLE);

require_once 'includes/fkLibs/authorForeignKey.php';

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
function updateForumsMessagesDbTableRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['txt'] = $vars['bbcode']->apply(stripslashes($row['txt']));

    if (in_array('UPDATE_AUTHOR_DATA', $updateList)) {
        $userData = getUserData($row['auteur_id']);

        if ($userData === false)
            $setFields['auteur_id'] = null;
        else
            $setFields['auteur'] = $userData['pseudo'];
    }

    return $setFields;
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
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.14
    if ($dbTable->fieldExist('auteur_ip') && $dbTable->getFieldType('auteur_ip') != 'varchar(40)')
        $dbTable->modifyField('auteur_ip', $forumMsgTableCfg['fields']['auteur_ip']);

    // install / update 1.8
    if ($dbTable->fieldExist('auteur')) {
        if ($dbTable->fieldExist('auteur') && $dbTable->getFieldType('auteur') != 'varchar(30)')
            $dbTable->modifyField('auteur', $forumMsgTableCfg['fields']['auteur'])
                ->setUpdateFieldData('UPDATE_AUTHOR_DATA', array('auteur_id', 'auteur'));

        if (! $dbTable->checkFieldIsIndex('auteur'))
            $dbTable->addFieldIndex('auteur');
    }

    if ($dbTable->fieldExist('auteur_id')) {
        if (! $dbTable->checkFieldIsNull('auteur_id'))
            $dbTable->modifyField('auteur_id', $forumMsgTableCfg['fields']['auteur_id']);

        if (! $dbTable->checkFieldIsIndex('auteur_id'))
            $dbTable->addFieldIndex('auteur_id');
    }

    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine(FORUM_MESSAGES_TABLE) == 'MyISAM')
        $this->_db->execute('ALTER TABLE `'. FORUM_MESSAGES_TABLE .'` ENGINE=InnoDB;');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'txt');
    }

    $dbTable->applyUpdateFieldListToData();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Add foreign key of table
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'addForeignKey') {
    if (! $dbTable->foreignKeyExist('FK_forumMessages_authorId'))
        addAuthorIdForeignKey('forumMessages', 'auteur_id');

    if (! $dbTable->foreignKeyExist('FK_forumMessages_author'))
        addAuthorForeignKey('forumMessages', 'auteur');
}

?>