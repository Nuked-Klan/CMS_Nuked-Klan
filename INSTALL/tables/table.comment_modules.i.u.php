<?php
/**
 * table.comment_modules.i.u.php
 *
 * `[PREFIX]_comment_modules` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

define('COMMENT_MODULES_TABLE', $this->_session['db_prefix'] .'_comment_modules');
define('COMMENT_MOD_TABLE', $this->_session['db_prefix'] .'_comment_mod');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$commentModTableCfg = array(
    'fields' => array(
        // old
        'id'     => array('type' => 'tinyint(2)',  'null' => false, 'unsigned' => true, 'autoIncrement' => true),
        'module' => array('type' => 'varchar(50)', 'null' => false),
        'active' => array('type' => 'tinyint(1)',  'null' => false, 'unsigned' => true)
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation') {
    if ($dbTable->tableExist(COMMENT_MODULES_TABLE)) {
        $dbTable->setTable(COMMENT_MODULES_TABLE);
    }
    else if ($dbTable->tableExist(COMMENT_MOD_TABLE)) {
        $dbTable->setTable(COMMENT_MOD_TABLE);
    }
    else
        return;

    $dbTable->checkAndConvertCharsetAndCollation();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop') {
    if ($dbTable->tableExist(COMMENT_MODULES_TABLE)) {
        $dbTable->setTable(COMMENT_MODULES_TABLE);
    }
    else if ($dbTable->tableExist(COMMENT_MOD_TABLE)) {
        $dbTable->setTable(COMMENT_MOD_TABLE);
    }
    else
        return;

    $dbTable->dropTable();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

// install / update 1.7.9 RC1
if ($process == 'install'
    || ($process == 'update' && ! $dbTable->tableExist(COMMENT_MODULES_TABLE) && ! $dbTable->tableExist(COMMENT_MOD_TABLE))) {
    $dbTable->createTable($commentModTableCfg);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_comment_modules` (`id`, `module`, `active`) VALUES
        (1, \'news\', 1),
        (2, \'download\', 1),
        (3, \'links\', 1),
        (4, \'survey\', 1),
        (5, \'wars\', 1),
        (6, \'gallery\', 1),
        (7, \'sections\', 1);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);

    return;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    if (! $dbTable->tableExist(COMMENT_MODULES_TABLE)) {
        if ($dbTable->tableExist(COMMENT_MOD_TABLE)) {
            $dbTable->setTable(COMMENT_MOD_TABLE)
                ->renameTable(COMMENT_MODULES_TABLE);
        }
    }

    if ($dbTable->fieldExist('id') && $dbTable->getFieldType('id') != 'tinyint(2)')
        $dbTable->modifyField('id', $forumMsgTableCfg['fields']['id']);

    if ($dbTable->fieldExist('module') && $dbTable->getFieldType('module') != 'varchar(50)')
        $dbTable->modifyField('module', $forumMsgTableCfg['fields']['module']);

    if ($dbTable->fieldExist('active') && $dbTable->getFieldType('active') != 'tinyint(1)')
        $dbTable->modifyField('active', $forumMsgTableCfg['fields']['active']);
}

?>