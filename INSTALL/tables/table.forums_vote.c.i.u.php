<?php
/**
 * table.forums_vote.c.i.u.php
 *
 * `[PREFIX]_forums_vote` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(FORUM_VOTE_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$forumVoteTableCfg = array(
    'fields' => array(
        'poll_id'   => array('type' => 'int(11)',     'null' => false, 'default' => '\'0\''),
        'author_id' => array('type' => 'varchar(20)', 'null' => false, 'default' => '\'\''),
        'author_ip' => array('type' => 'varchar(40)', 'null' => false, 'default' => '\'\'')
    ),
    'index' => array(
        'poll_id' => 'poll_id'
    ),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table create in 1.7.x version
    $dbTable->checkIntegrity(array('auteur_id', 'author_id'), array('auteur_ip', 'author_ip'));
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
    $dbTable->createTable($forumVoteTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.14  => varchar(40)
    // install / update 1.8     => Rename to author_ip
    if ($dbTable->fieldExist('auteur_ip') && $dbTable->getFieldType('auteur_ip') != 'varchar(40)')
        $dbTable->modifyField('auteur_ip', array_merge(array('newField' => 'author_ip'), $forumVoteTableCfg['fields']['author_ip']));

    // install / update 1.8     => Rename to author_id
    if ($dbTable->fieldExist('auteur_id'))
        $dbTable->modifyField('auteur_id', array_merge(array('newField' => 'author_id'), $forumVoteTableCfg['fields']['author_id']));
}

?>