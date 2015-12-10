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

/*
 * Callback function for update row of _forums database table
 */
function updateForumsRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('UPDATE_NB_MESSAGE', $updateList)) {
        $dbrForumMessages = $db->selectOne(
            'SELECT COUNT(*) AS `nbMessages`
            FROM `'. $dbPrefix .'_forums_messages`
            WHERE thread_id = '. $row['id']
        );

        $setFields['nbReplies'] = $dbrForumMessages['nbMessages'] - 1;
    }

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity();
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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_forums_threads` (
            `id` int(5) NOT NULL auto_increment,
            `titre` text NOT NULL,
            `date` varchar(10) default NULL,
            `closed` int(1) NOT NULL default \'0\',
            `auteur` text NOT NULL,
            `auteur_id` varchar(20) NOT NULL default \'\',
            `forum_id` int(5) NOT NULL default \'0\',
            `last_post` varchar(20) NOT NULL default \'\',
            `view` int(10) NOT NULL default \'0\',
            `annonce` int(1) NOT NULL default \'0\',
            `sondage` int(1) NOT NULL default \'0\',
            `nbReplies` int(10) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `auteur_id` (`auteur_id`),
            KEY `forum_id` (`forum_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    if (! $dbTable->fieldExist('nbReplies')) {
        $dbTable->addField('nbReplies', array('type' => 'int(10)', 'null' => false, 'default' => '\'0\''));
        $dbTable->setCallbackFunctionVars(array('dbPrefix' => $this->_session['db_prefix'], 'db' => $this->_db))
            ->setUpdateFieldData('UPDATE_NB_REPLY', 'nbReplies');
    }

    $dbTable->alterTable();

    $dbTable->applyUpdateFieldListToData('id', 'updateForumsRow');
}

?>