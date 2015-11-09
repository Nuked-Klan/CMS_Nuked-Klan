<?php
/**
 * table.stats_visitor.c.i.u.php
 *
 * `[PREFIX]_stats_visitor` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_stats_visitor');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table create in 1.7.x version
    $dbTable->checkIntegrity('ip');
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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_stats_visitor` (
            `id` int(11) NOT NULL auto_increment,
            `user_id` varchar(20) NOT NULL default \'\',
            `ip` varchar(40) NOT NULL default \'\',
            `host` varchar(100) NOT NULL default \'\',
            `browser` varchar(50) NOT NULL default \'\',
            `os` varchar(50) NOT NULL default \'\',
            `referer` varchar(200) NOT NULL default \'\',
            `day` int(2) NOT NULL default \'0\',
            `month` int(2) NOT NULL default \'0\',
            `year` int(4) NOT NULL default \'0\',
            `hour` int(2) NOT NULL default \'0\',
            `date` varchar(30) NOT NULL default \'\',
            PRIMARY KEY  (`id`),
            KEY `user_id` (`user_id`),
            KEY `host` (`host`),
            KEY `browser` (`browser`),
            KEY `os` (`os`),
            KEY `referer` (`referer`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.14
    if ($dbTable->getFieldType('ip') != 'varchar(40)')
        $dbTable->modifyField('ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));

    $dbTable->alterTable();
}

?>