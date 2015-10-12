<?php
/**
 * table.sondage_data.c.i.php
 *
 * `[PREFIX]_sondage_data` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_sondage_data');

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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_sondage_data` (
            `sid` int(11) NOT NULL default \'0\',
            `optionText` char(50) NOT NULL default \'\',
            `optionCount` int(11) NOT NULL default \'0\',
            `voteID` int(11) NOT NULL default \'0\',
            KEY `sid` (`sid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_sondage_data` VALUES
        (1, \''. $this->_db->quote($this->_i18n['ROXX']) .'\', 0, 1),
        (1, \''. $this->_db->quote($this->_i18n['NOT_BAD']) .'\', 0, 2),
        (1, \''. $this->_db->quote($this->_i18n['SHIET']) .'\', 0, 3),
        (1, \''. $this->_db->quote($this->_i18n['WHATS_NK']) .'\', 0, 4);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

?>