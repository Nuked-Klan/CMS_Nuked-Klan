<?php
/**
 * table._forums.c.i.u.php
 *
 * `[PREFIX]_forums` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_forums');

/*
 * Callback function for update row of _forums database table
 */
function updateForumsRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('REMOVE_EDITOR', $updateList))
        $setFields['comment'] = str_replace(array('<p>', '</p>'), '', $row['comment']);

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['comment'] = $vars['bbcode']->apply(stripslashes($row['comment']));

    if (in_array('UPDATE_NB_THREAD', $updateList)) {
        $dbrForumThread = $db->selectOne(
            'SELECT COUNT(*) AS `nbTopics`
            FROM `'. $dbPrefix .'_forums_threads`
            WHERE forum_id = '. $row['id']
        );

        $setFields['nbTopics'] = $dbrForumThread['nbTopics'];
    }

    if (in_array('UPDATE_NB_MESSAGE', $updateList)) {
        $dbrForumMessages = $db->selectOne(
            'SELECT COUNT(*) AS `nbMessages`
            FROM `'. $dbPrefix .'_forums_messages`
            WHERE forum_id = '. $row['id']
        );

        $setFields['nbMessages'] = $dbrForumMessages['nbMessages'];
    }

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'comment');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_forums` (
            `id` int(5) NOT NULL auto_increment,
            `cat` int(11) NOT NULL default \'0\',
            `parentid` int(11) NOT NULL default \'0\',
            `nom` text NOT NULL,
            `comment` text NOT NULL,
            `moderateurs` text NOT NULL,
            `image` varchar(200) NOT NULL default \'\',
            `niveau` int(1) NOT NULL default \'0\',
            `level` int(1) NOT NULL default \'0\',
            `ordre` int(5) NOT NULL default \'0\',
            `level_poll` int(1) NOT NULL default \'0\',
            `level_vote` int(1) NOT NULL default \'0\',
            `nbTopics` int(10) NOT NULL default \'0\',
            `nbMessages` int(10) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_forums` VALUES
        (1, 1, 0, \''. $this->_db->quote($this->_i18n['FORUM']) .'\', \''. $this->_db->quote($this->_i18n['TEST_FORUM']) .'\', \'\', \'\', 0, 0, 0, 1 ,1, 0, 0);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    if (! $dbTable->fieldExist('parentid'))
        $dbTable->addField('parentid', array('type' => 'INT(11)', 'null' => false, 'default' => '\'0\''));

    if (! $dbTable->fieldExist('image'))
        $dbTable->addField('image', array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''));

    if (! $dbTable->fieldExist('nbTopics')) {
        $dbTable->addField('nbTopics', array('type' => 'int(10)', 'null' => false, 'default' => '\'0\''));
        $dbTable->setCallbackFunctionVars(array('dbPrefix' => $this->_session['db_prefix'], 'db' => $this->_db))
            ->setUpdateFieldData('UPDATE_NB_THREAD', 'nbTopics');
    }

    if (! $dbTable->fieldExist('nbMessages')) {
        $dbTable->addField('nbMessages', array('type' => 'int(10)', 'null' => false, 'default' => '\'0\''));
        $dbTable->setCallbackFunctionVars(array('dbPrefix' => $this->_session['db_prefix'], 'db' => $this->_db))
            ->setUpdateFieldData('UPDATE_NB_MESSAGE', 'nbMessages');
    }

    $dbTable->alterTable();

    // TODO : Version ???
    if (version_compare($this->_session['version'], '1.7.9', '='))
        $dbTable->setUpdateFieldData('REMOVE_EDITOR', 'comment');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'comment');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateForumsRow');
}

?>