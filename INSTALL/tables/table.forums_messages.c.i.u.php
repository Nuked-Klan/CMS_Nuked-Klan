<?php
/**
 * table.forums_messages.c.i.u.php
 *
 * `[PREFIX]_forums_messages` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_forums_messages');

/*
 * Callback function for update row of forums messages database table
 */
function updateForumsMessagesRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['txt'] = $vars['bbcode']->apply(stripslashes($row['txt']));

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'txt', 'auteur_ip');
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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_forums_messages` (
            `id` int(5) NOT NULL auto_increment,
            `titre` text NOT NULL,
            `txt` text NOT NULL,
            `date` varchar(12) NOT NULL default \'\',
            `edition` text NOT NULL,
            `auteur` text NOT NULL,
            `auteur_id` varchar(20) NOT NULL default \'\',
            `auteur_ip` varchar(40) NOT NULL default \'\',
            `bbcodeoff` int(1) NOT NULL default \'0\',
            `smileyoff` int(1) NOT NULL default \'0\',
            `cssoff` int(1) NOT NULL default \'0\',
            `usersig` int(1) NOT NULL default \'0\',
            `emailnotify` int(1) NOT NULL default \'0\',
            `thread_id` int(5) NOT NULL default \'0\',
            `forum_id` mediumint(10) NOT NULL default \'0\',
            `file` varchar(200) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `auteur_id` (`auteur_id`),
            KEY `thread_id` (`thread_id`),
            KEY `forum_id` (`forum_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.15
    if ($dbTable->getFieldType('auteur_ip') != 'varchar(40)')
        $dbTable->modifyField('auteur_ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));

    $dbTable->alterTable();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'txt');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateForumsMessagesRow');
}

?>