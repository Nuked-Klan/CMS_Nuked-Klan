<?php
/**
 * table.staff.i.u.php
 *
 * `[PREFIX]_staff` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_staff');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

// install / update 1.8
if ($process == 'install' || ($process == 'update' && ! $dbTable->tableExist())) {
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_staff` (
        `id` int(11) NOT NULL auto_increment,
        `membre_id` varchar(20) NOT NULL default \'\',
        `categorie_id` int(11) NOT NULL default \'0\',
        `date` int(11) NOT NULL default \'0\',
        `status_id` varchar(25) NOT NULL default \'\',
        `rang_id` varchar(25) NOT NULL default \'\',
        PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

?>