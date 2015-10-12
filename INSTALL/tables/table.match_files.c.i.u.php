<?php
/**
 * table.match_files.c.i.u.php
 *
 * `[PREFIX]_match_files` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
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
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $dbTable->setTable($this->_session['db_prefix'] .'_match_files');

    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_match_files` (
            `id` int(10) NOT NULL auto_increment,
            `module` varchar(30) NOT NULL default \'\',
            `im_id` int(10) NOT NULL default \'0\',
            `type` varchar(30) NOT NULL default \'\',
            `url` varchar(200) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `im_id` (`im_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.9 RC6
    if (! $dbTable->tableExist($this->_session['db_prefix'] .'_match_files')) {
        if ($dbTable->tableExist($this->_session['db_prefix'] .'_fichiers_joins')) {
            $dbTable->setTable($this->_session['db_prefix'] .'_fichiers_joins');
            $dbTable->renameTable($this->_session['db_prefix'] .'_match_files');
        }
    }

    $dbTable->alterTable();
}

?>