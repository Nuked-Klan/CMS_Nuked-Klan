<?php
/**
 * table.forums_threads.c.i.u.php
 *
 * `[PREFIX]_forums_threads` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(FORUM_THREADS_TABLE);

//require_once 'includes/fkLibs/authorForeignKey.php';

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
        //'auteur_id' => array('type' => 'varchar(20)', 'null' => true,  'default' => '\'\''),
        'auteur_id' => array('type' => 'varchar(20)', 'null' => true,  'default' => 'NULL'),
        'forum_id'  => array('type' => 'int(5)',      'null' => false, 'default' => '\'0\''),
        'last_post' => array('type' => 'varchar(20)', 'null' => false, 'default' => '\'\''),
        'view'      => array('type' => 'int(10)',     'null' => false, 'default' => '\'0\''),
        'annonce'   => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\''),
        'sondage'   => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\''),
        'nbReplies' => array('type' => 'int(10)',     'null' => false, 'default' => '\'0\'')
    ),
    'primaryKey' => array('id'),
    'index' => array(
        //'auteur'    => 'auteur',
        //'auteur_id' => 'auteur_id',
        'forum_id'  => 'forum_id'
    ),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of forums topics database table
 */
function updateForumsThreadsDbTableRow($updateList, $row, $vars) {
    global $db;

    $setFields = array();

    if (in_array('UPDATE_NB_REPLIES', $updateList)) {
        $dbrForumMessages = $db->selectOne(
            'SELECT COUNT(*) AS `nbMessages`
            FROM `'. FORUM_MESSAGES_TABLE .'`
            WHERE thread_id = '. $row['id']
        );

        $setFields['nbReplies'] = $dbrForumMessages['nbMessages'] - 1;
    }

    /*if (in_array('UPDATE_AUTHOR_DATA', $updateList)) {
        $userData = getUserData($row['auteur_id']);

        if ($userData === false)
            $setFields['auteur_id'] = null;
        else
            $setFields['auteur'] = $userData['pseudo'];
    }*/

    return $setFields;
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
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    /*if ($dbTable->fieldExist('auteur')) {
        if ($dbTable->getFieldType('auteur') != 'varchar(30)')
            $dbTable->modifyField('auteur', $forumTopicsTableCfg['fields']['auteur'])
                ->setUpdateFieldData('UPDATE_AUTHOR_DATA', array('auteur_id', 'auteur'));

        if (! $dbTable->checkFieldIsIndex('auteur'))
            $dbTable->addFieldIndex('auteur');
    }*/

    /*if ($dbTable->fieldExist('auteur_id')) {
        if (! $dbTable->checkFieldIsNull('auteur_id'))
            $dbTable->modifyField('auteur_id', $forumTopicsTableCfg['fields']['auteur_id']);

        if (! $dbTable->checkFieldIsIndex('auteur_id'))
            $dbTable->addFieldIndex('auteur_id');
    }*/

    //if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine(FORUM_THREADS_TABLE) == 'MyISAM')
    //    $this->_db->execute('ALTER TABLE `'. FORUM_THREADS_TABLE .'` ENGINE=InnoDB;');

    if (! $dbTable->fieldExist('nbReplies')) {
        $dbTable->addField('nbReplies', $forumTopicsTableCfg['fields']['nbReplies'])
            ->setUpdateFieldData('UPDATE_NB_REPLIES', 'nbReplies');
    }

    $dbTable->applyUpdateFieldListToData();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Add foreign key of table
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*if ($process == 'addForeignKey') {
    if (! $dbTable->foreignKeyExist('FK_forumTopics_authorId'))
        addAuthorIdForeignKey('forumTopics', 'auteur_id');

    if (! $dbTable->foreignKeyExist('FK_forumTopics_author'))
        addAuthorForeignKey('forumTopics', 'auteur');
}*/

?>
