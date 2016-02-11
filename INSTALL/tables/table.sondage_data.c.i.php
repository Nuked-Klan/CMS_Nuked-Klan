<?php
/**
 * table.sondage_data.c.i.php
 *
 * `[PREFIX]_sondage_data` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(SURVEY_DATA_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$surveyDataTableCfg = array(
    'fields' => array(
        'sid'         => array('type' => 'int(11)',  'null' => false, 'default' => '\'0\''),
        'optionText'  => array('type' => 'char(50)', 'null' => false, 'default' => '\'\''),
        'optionCount' => array('type' => 'int(11)',  'null' => false, 'default' => '\'0\''),
        'voteID'      => array('type' => 'int(11)',  'null' => false, 'default' => '\'0\'')
    ),
    'index' => array(
        'sid' => 'sid'
    ),
    'engine' => 'MyISAM'
);

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
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $dbTable->createTable($surveyDataTableCfg);

    $sql = 'INSERT INTO `'. SURVEY_DATA_TABLE .'` VALUES
        (1, \''. $this->_db->quote($this->_i18n['ROXX']) .'\', 0, 1),
        (1, \''. $this->_db->quote($this->_i18n['NOT_BAD']) .'\', 0, 2),
        (1, \''. $this->_db->quote($this->_i18n['SHIET']) .'\', 0, 3),
        (1, \''. $this->_db->quote($this->_i18n['WHATS_NK']) .'\', 0, 4);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

?>