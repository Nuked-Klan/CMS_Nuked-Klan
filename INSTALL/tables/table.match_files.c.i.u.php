<?php
/**
 * table.match_files.c.i.u.php
 *
 * `[PREFIX]_match_files` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$joinedFilesTableCfg = array(
    'fields' => array(
        'id'     => array('type' => 'int(10)',      'null' => false, 'autoIncrement' => true),
        'module' => array('type' => 'varchar(30)',  'null' => false, 'default' => '\'\''),
        'im_id'  => array('type' => 'int(10)',      'null' => false, 'default' => '\'0\''),
        'type'   => array('type' => 'varchar(30)',  'null' => false, 'default' => '\'\''),
        'url'    => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\'')
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'im_id' => 'im_id'
    ),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    if ($dbTable->tableExist($this->_session['db_prefix'] .'_fichiers_joins'))
        $dbTable->setTable($this->_session['db_prefix'] .'_fichiers_joins');
    else if ($dbTable->tableExist($this->_session['db_prefix'] .'_match_files'))
        $dbTable->setTable($this->_session['db_prefix'] .'_match_files');

    if ($dbTable->tableExist($this->_session['db_prefix'] .'_fichiers_joins')
        || $dbTable->tableExist($this->_session['db_prefix'] .'_match_files')
    ) {
        $dbTable->setJqueryAjaxResponse('INTEGRITY_ACCEPTED');
    }
    else {
        $dbTable->setJqueryAjaxResponse('INTEGRITY_FAIL');
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation') {
    if ($dbTable->tableExist($this->_session['db_prefix'] .'_fichiers_joins'))
        $dbTable->setTable($this->_session['db_prefix'] .'_fichiers_joins');
    else if ($dbTable->tableExist($this->_session['db_prefix'] .'_match_files'))
        $dbTable->setTable($this->_session['db_prefix'] .'_match_files');

    $dbTable->checkAndConvertCharsetAndCollation();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop') {
    if ($dbTable->tableExist($this->_session['db_prefix'] .'_fichiers_joins'))
        $dbTable->setTable($this->_session['db_prefix'] .'_fichiers_joins');
    else if ($dbTable->tableExist($this->_session['db_prefix'] .'_match_files'))
        $dbTable->setTable($this->_session['db_prefix'] .'_match_files');
    else
        return;

    $dbTable->dropTable();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $dbTable->setTable($this->_session['db_prefix'] .'_match_files');
    $dbTable->createTable($joinedFilesTableCfg);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.9 RC6
    if (! $dbTable->tableExist($this->_session['db_prefix'] .'_match_files')) {
        if ($dbTable->tableExist($this->_session['db_prefix'] .'_fichiers_joins')) {
            $dbTable->setTable($this->_session['db_prefix'] .'_fichiers_joins')
                ->renameTable($this->_session['db_prefix'] .'_match_files');
        }
    }
}

?>