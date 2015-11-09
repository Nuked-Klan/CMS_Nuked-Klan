<?php
/**
 * table.games.c.i.u.php
 *
 * `[PREFIX]_games` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_games');

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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_games` (
            `id` int(11) NOT NULL auto_increment,
            `name` varchar(50) NOT NULL default \'\',
            `titre` varchar(50) NOT NULL default \'\',
            `icon` varchar(150) NOT NULL default \'\',
            `pref_1` varchar(50) NOT NULL default \'\',
            `pref_2` varchar(50) NOT NULL default \'\',
            `pref_3` varchar(50) NOT NULL default \'\',
            `pref_4` varchar(50) NOT NULL default \'\',
            `pref_5` varchar(50) NOT NULL default \'\',
            `map` TEXT NOT NULL,
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_games` VALUES
        (1, \'Counter Strike Source\', \''. $this->_db->quote($this->_i18n['PREF_CS']) .'\', \'images/games/cs.gif\', \''. $this->_db->quote($this->_i18n['OTHER_NICK']) .'\', \''. $this->_db->quote($this->_i18n['FAV_MAP']) .'\', \''. $this->_db->quote($this->_i18n['FAV_WEAPON']) .'\', \''. $this->_db->quote($this->_i18n['SKIN_T']) .'\', \''. $this->_db->quote($this->_i18n['SKIN_CT']) .'\', \'de_dust2|de_inferno\');';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install 1.7.9 RC4
    // update 1.7.9 RC5.2
    if (! $dbTable->fieldExist('map'))
        $dbTable->addField('map', array('type' => 'TEXT', 'null' => false));

    $dbTable->alterTable();
}

?>