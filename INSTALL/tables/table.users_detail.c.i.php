<?php
/**
 * table.users_detail.c.i.php
 *
 * `[PREFIX]_users_detail` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_users_detail');

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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_users_detail` (
            `user_id` varchar(20) NOT NULL default \'0\',
            `prenom` text,
            `age` varchar(10) NOT NULL default \'\',
            `sexe` varchar(20) NOT NULL default \'\',
            `ville` text,
            `photo` varchar(150) NOT NULL default \'\',
            `motherboard` text,
            `cpu` varchar(50) default NULL,
            `ram` varchar(10) NOT NULL default \'\',
            `video` text,
            `resolution` text,
            `son` text,
            `ecran` text,
            `souris` text,
            `clavier` text,
            `connexion` text,
            `system` text,
            `pref_1` text NOT NULL,
            `pref_2` text NOT NULL,
            `pref_3` text NOT NULL,
            `pref_4` text NOT NULL,
            `pref_5` text NOT NULL,
            PRIMARY KEY  (`user_id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

?>