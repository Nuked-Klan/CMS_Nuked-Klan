<?php
/**
 * table.forums_threads.c.i.php
 *
 * `[PREFIX]_forums_threads` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_forums_threads');

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
            PRIMARY KEY  (`id`),
            KEY `auteur_id` (`auteur_id`),
            KEY `forum_id` (`forum_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

?>