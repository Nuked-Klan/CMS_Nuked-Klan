<?php
/**
 * table._forums_rank.c.i.php
 *
 * `[PREFIX]_forums_rank` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_forums_rank');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table exist in 1.6.x version
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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_forums_rank` (
            `id` int(10) NOT NULL auto_increment,
            `nom` varchar(100) NOT NULL default \'\',
            `type` int(1) NOT NULL default \'0\',
            `post` int(4) NOT NULL default \'0\',
            `image` varchar(200) NOT NULL default \'\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_forums_rank` VALUES
        (1, \''. $this->_db->quote($this->_i18n['NEWBIE']) .'\', 0, 0, \'modules/Forum/images/rank/star1.gif\'),
        (2, \''. $this->_db->quote($this->_i18n['JUNIOR_MEMBER']) .'\', 0, 10, \'modules/Forum/images/rank/star2.gif\'),
        (3, \''. $this->_db->quote($this->_i18n['MEMBER']) .'\', 0, 100, \'modules/Forum/images/rank/star3.gif\'),
        (4, \''. $this->_db->quote($this->_i18n['SENIOR_MEMBER']) .'\', 0, 500, \'modules/Forum/images/rank/star4.gif\'),
        (5, \''. $this->_db->quote($this->_i18n['POSTING_FREAK']) .'\', 0, 1000, \'modules/Forum/images/rank/star5.gif\'),
        (6, \''. $this->_db->quote($this->_i18n['MODERATOR']) .'\', 1, 0, \'modules/Forum/images/rank/mod.gif\'),
        (7, \''. $this->_db->quote($this->_i18n['ADMINISTRATOR']) .'\', 2, 0, \'modules/Forum/images/rank/mod.gif\');';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

?>