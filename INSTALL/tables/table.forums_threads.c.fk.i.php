<?php
/**
 * table.forums_threads.c.i.php
 *
 * `[PREFIX]_forums_threads` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_forums_threads');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$forumTopicsTableCfg = array(
    'fields' => array(
        'id'        => array('type' => 'int(5)',      'null' => false, 'autoIncrement' => true),
        'titre'     => array('type' => 'text',        'null' => false),
        'date'      => array('type' => 'varchar(10)', 'null' => true,  'default' => 'NULL'),
        'closed'    => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\''),
        'auteur'    => array('type' => 'varchar(30)', 'null' => false),
        'auteur_id' => array('type' => 'varchar(20)', 'null' => true,  'default' => '\'\''),
        'forum_id'  => array('type' => 'int(5)',      'null' => false, 'default' => '\'0\''),
        'last_post' => array('type' => 'varchar(20)', 'null' => false, 'default' => '\'\''),
        'view'      => array('type' => 'int(10)',     'null' => false, 'default' => '\'0\''),
        'annonce'   => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\''),
        'sondage'   => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\''),
        'nbReplies' => array('type' => 'int(10)',     'null' => false, 'default' => '\'0\'')
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'auteur'    => 'auteur',
        'auteur_id' => 'auteur_id',
        'forum_id'  => 'forum_id'
    ),
    'engine' => 'InnoDB'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of forums topics database table
 */
function updateForumsTopicsRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('UPDATE_NB_MESSAGE', $updateList)) {
        $dbrForumMessages = $vars['db']->selectOne(
            'SELECT COUNT(*) AS `nbMessages`
            FROM `'. $vars['dbPrefix'] .'_forums_messages`
            WHERE thread_id = '. $row['id']
        );

        $setFields['nbReplies'] = $dbrForumMessages['nbMessages'] - 1;
    }

    return $setFields;
}

/*
 * Add author Id foreign key of forums topics database table
 */
function addAuthorIdForeignKey($dbTable, $dbprefix) {
    $dbTable->addForeignKey(
        'FK_forumTopics_authorId', 'auteur_id',
        $dbprefix .'_users', 'id',
        array('ON DELETE SET NULL')
    );
}

/*
 * Add author Id foreign key of forums topics database table
 */
function addAuthorForeignKey($dbTable, $dbprefix) {
    $dbTable->addForeignKey(
        'FK_forumTopics_author', 'auteur',
        $dbprefix .'_users', 'pseudo',
        array('ON UPDATE CASCADE')
    );
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'auteur', 'auteur_id');
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
    $dbTable->createTable($forumTopicsTableCfg);

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
    // install / update 1.8
    if ($dbTable->fieldExist('auteur')) {
        if ($dbTable->getFieldType('auteur') != 'varchar(30)')
            $dbTable->modifyField('auteur', $forumTopicsTableCfg['fields']['auteur']);

        if (! $dbTable->checkFieldIsIndex('auteur'))
            $dbTable->addFieldIndex('auteur');
    }

    if ($dbTable->fieldExist('auteur_id')) {
        if (! $dbTable->checkFieldIsNull('auteur_id'))
            $dbTable->modifyField('auteur_id', $forumTopicsTableCfg['fields']['auteur_id']);

        if (! $dbTable->checkFieldIsIndex('auteur_id'))
            $dbTable->addFieldIndex('auteur_id');
    }

    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine($this->_session['db_prefix'] .'_forums_threads'))
        $this->_db->execute('ALTER TABLE `'. $this->_session['db_prefix'] .'_forums_threads` ENGINE=InnoDB;');

    if (! $dbTable->foreignKeyExist('FK_forumTopics_authorId'))
        addAuthorIdForeignKey($dbTable, $this->_session['db_prefix']);

    if (! $dbTable->foreignKeyExist('FK_forumTopics_author'))
        addAuthorForeignKey($dbTable, $this->_session['db_prefix']);

    if (! $dbTable->fieldExist('nbReplies')) {
        $dbTable->addField('nbReplies', $forumTopicsTableCfg['fields']['nbReplies'])
            ->setCallbackFunctionVars(array('dbPrefix' => $this->_session['db_prefix'], 'db' => $this->_db))
            ->setUpdateFieldData('UPDATE_NB_REPLY', 'nbReplies');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateForumsTopicsRow');
}

?>