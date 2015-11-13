<?php
/**
 * table.page.i.u.php
 *
 * `[PREFIX]_page` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_page');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

// install / update 1.8
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist())) {
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_page` (
        `id` int(11) NOT NULL auto_increment,
        `niveau` int(1) NOT NULL default \'0\',
        `titre` varchar(50) NOT NULL default \'\',
        `content` text NOT NULL,
        `url` varchar(80) NOT NULL default \'\',
        `type` varchar(5) NOT NULL default \'\',
        `show_title` int(1) NOT NULL default \'0\',
        `members` text NOT NULL,
        PRIMARY KEY  (`id`),
        KEY `titre` (`titre`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

?>