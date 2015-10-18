<?php
/**
 * table.comment_mod.i.u.php
 *
 * `[PREFIX]_comment_mod` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_comment_mod');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

// install / update 1.7.9 RC1
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist())) {
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_comment_mod` (
            `id` int(11) NOT NULL auto_increment,
            `module` text NOT NULL,
            `active` int(1) NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_comment_mod` VALUES
        (1, \'news\', 1),
        (2, \'download\', 1),
        (3, \'links\', 1),
        (4, \'survey\', 1),
        (5, \'wars\', 1),
        (6, \'gallery\', 1),
        (7, \'sections\', 1);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

?>