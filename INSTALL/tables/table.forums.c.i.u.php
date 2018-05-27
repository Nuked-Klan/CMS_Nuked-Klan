<?php
/**
 * table.forums.c.i.u.php
 *
 * `[PREFIX]_forums` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(FORUM_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$forumTableCfg = array(
    'fields' => array(
        'id'          => array('type' => 'int(5)',       'null' => false, 'autoIncrement' => true),
        'cat'         => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'parentid'    => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'nom'         => array('type' => 'text',         'null' => false),
        'comment'     => array('type' => 'text',         'null' => false),
        'image'       => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'niveau'      => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'level'       => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'ordre'       => array('type' => 'int(5)',       'null' => false, 'default' => '\'0\''),
        'level_poll'  => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'level_vote'  => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'nbTopics'    => array('type' => 'int(10)',      'null' => false, 'default' => '\'0\''),
        'nbMessages'  => array('type' => 'int(10)',      'null' => false, 'default' => '\'0\'')
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'cat' => 'cat'
    ),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of _forums database table
 */
function updateForumsDbTableRow($updateList, $row, $vars) {
    global $db;

    $setFields = array();

    // TODO : Really ?
    if (in_array('REMOVE_EDITOR', $updateList))
        $setFields['comment'] = str_replace(array('<p>', '</p>'), '', $row['comment']);

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['comment'] = $vars['bbcode']->apply(stripslashes($row['comment']));

    if (in_array('UPDATE_NB_THREAD', $updateList)) {
        $dbrForumThread = $db->selectOne(
            'SELECT COUNT(*) AS `nbTopics`
            FROM `'. FORUM_THREADS_TABLE .'`
            WHERE forum_id = '. $row['id']
        );

        $setFields['nbTopics'] = $dbrForumThread['nbTopics'];
    }

    if (in_array('UPDATE_NB_MESSAGE', $updateList)) {
        $dbrForumMessages = $db->selectOne(
            'SELECT COUNT(*) AS `nbMessages`
            FROM `'. FORUM_MESSAGES_TABLE .'`
            WHERE forum_id = '. $row['id']
        );

        $setFields['nbMessages'] = $dbrForumMessages['nbMessages'];
    }

    if (in_array('MOVE_MODERATOR_LIST', $updateList)) {
        $moderatorList = explode('|', $row['moderateurs']);

        if ($moderatorList) {
            foreach ($moderatorList as $userId) {
                $db->execute(
                    'INSERT INTO `'. FORUM_MODERATOR_TABLE .'`
                    (`userId`, `forum`)
                    VALUES
                    (\''. $userId .'\', \''. $row['id'] .'\');'
                );
            }

            $setFields['moderateurs'] = '';
        }
    }

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'comment', array('moderateurs', null));
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

if ($process == 'install') {
    $dbTable->createTable($forumTableCfg);

    $sql = 'INSERT INTO `'. FORUM_TABLE .'`
        (`cat`, `nom`, `comment`, `level_poll`, `level_vote`)
        VALUES
        (1, \''. $this->_db->quote($this->_i18n['FORUM']) .'\', \''. $this->_db->quote($this->_i18n['TEST_FORUM']) .'\', 1 ,1);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    if (! $dbTable->fieldExist('parentid'))
        $dbTable->addField('parentid', $forumTableCfg['fields']['parentid']);

    if (! $dbTable->fieldExist('image'))
        $dbTable->addField('image', $forumTableCfg['fields']['image']);

    if (! $dbTable->fieldExist('nbTopics')) {
        $dbTable->addField('nbTopics', $forumTableCfg['fields']['nbTopics'])
            ->setUpdateFieldData('UPDATE_NB_THREAD', 'nbTopics');
    }

    if (! $dbTable->fieldExist('nbMessages')) {
        $dbTable->addField('nbMessages', $forumTableCfg['fields']['nbMessages'])
            ->setUpdateFieldData('UPDATE_NB_MESSAGE', 'nbMessages');
    }

    if ($dbTable->fieldExist('moderateurs')) {
        $dbTable->setUpdateFieldData('MOVE_MODERATOR_LIST', 'moderateurs');
    }

    // TODO : Version ???
    if (version_compare($this->_session['version'], '1.7.9', '='))
        $dbTable->setUpdateFieldData('REMOVE_EDITOR', 'comment');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'comment');
    }

    $dbTable->applyUpdateFieldListToData();

    if (($response = $dbTable->getJqueryAjaxResponse()) == 'UPDATED') {
        if ($dbTable->fieldExist('moderateurs'))
            $dbTable->dropField('moderateurs');
    }
}

?>
