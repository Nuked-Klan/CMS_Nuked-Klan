<?php
/**
 * table.notification.i.u.php
 *
 * `[PREFIX]_notification` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(NOTIFICATIONS_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$notificationTableCfg = array(
    'fields' => array(
        'id'    => array('type' => 'int(11)',     'null' => false, 'autoIncrement' => true),
        'date'  => array('type' => 'varchar(30)', 'null' => false, 'default' => '\'0\''),
        'type'  => array('type' => 'text',        'null' => false),
        'texte' => array('type' => 'text',        'null' => false)
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

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

// install / update 1.7.9 RC1
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist())) {
    $dbTable->createTable($notificationTableCfg);

    if (ini_get('suhosin.session.encrypt') == 1) {
        $sql = 'INSERT INTO `'. NOTIFICATIONS_TABLE .'` VALUES
            (\'\', \'\', \'4\', \''. $this->_db->quote($this->_i18n['SUHOSIN']) .'\');';

        $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
    }
}

?>