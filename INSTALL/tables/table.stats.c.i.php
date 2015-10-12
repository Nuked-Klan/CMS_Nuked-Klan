<?php
/**
 * table.stats.c.i.php
 *
 * `[PREFIX]_stats` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_stats');

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
    $sql='CREATE TABLE `'. $this->_session['db_prefix'] .'_stats` (
            `nom` varchar(50) NOT NULL default \'\',
            `type` varchar(50) NOT NULL default \'\',
            `count` int(11) NOT NULL default \'0\',
            PRIMARY KEY  (`nom`,`type`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_stats` VALUES
        (\'Gallery\', \'pages\', 0),
        (\'Archives\', \'pages\', 0),
        (\'Calendar\', \'pages\', 0),
        (\'Defy\', \'pages\', 0),
        (\'Download\', \'pages\', 0),
        (\'Guestbook\', \'pages\', 0),
        (\'Irc\', \'pages\', 0),
        (\'Links\', \'pages\', 0),
        (\'Wars\', \'pages\', 0),
        (\'News\', \'pages\', 0),
        (\'Search\', \'pages\', 0),
        (\'Recruit\', \'pages\', 0),
        (\'Sections\', \'pages\', 0),
        (\'Server\', \'pages\', 0),
        (\'Members\', \'pages\', 0),
        (\'Team\', \'pages\', 0),
        (\'Forum\', \'pages\', 0);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

?>