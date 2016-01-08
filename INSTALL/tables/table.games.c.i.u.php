<?php
/**
 * table.games.c.i.u.php
 *
 * `[PREFIX]_games` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_games');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$gamesTableCfg = array(
    'fields' => array(
        'id'     => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'name'   => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'titre'  => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'icon'   => array('type' => 'varchar(150)', 'null' => false, 'default' => '\'\''),
        'pref_1' => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'pref_2' => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'pref_3' => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'pref_4' => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'pref_5' => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'map'    => array('type' => 'text',         'null' => false)
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

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
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $dbTable->createTable($gamesTableCfg);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_games` VALUES
        (1, \'Counter Strike Global Offensive\', \''. $this->_db->quote($this->_i18n['PREF_CS']) .'\', \'images/games/icon_csgo.png\', \''. $this->_db->quote($this->_i18n['OTHER_NICK']) .'\', \''. $this->_db->quote($this->_i18n['FAV_MAP']) .'\', \''. $this->_db->quote($this->_i18n['FAV_WEAPON']) .'\', \''. $this->_db->quote($this->_i18n['SKIN_T']) .'\', \''. $this->_db->quote($this->_i18n['SKIN_CT']) .'\', \'de_dust2|de_inferno\');';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install 1.7.9 RC4
    // update 1.7.9 RC5.2
    if (! $dbTable->fieldExist('map'))
        $dbTable->addField('map', $gamesTableCfg['fields']['map']);
}

?>