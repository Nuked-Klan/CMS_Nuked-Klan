<?php
/**
 * table.modules.c.i.u.php
 *
 * `[PREFIX]_modules` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_modules');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'nom', 'niveau', 'admin');
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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_modules` (
            `id` int(2) NOT NULL auto_increment,
            `nom` varchar(50) NOT NULL default \'\',
            `niveau` int(1) NOT NULL default \'0\',
            `admin` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);

    $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_modules` VALUES
        (1, \'News\', 0, 2),
        (2, \'Forum\', 0, 2),
        (3, \'Wars\', 0, 2),
        (4, \'Irc\', 0, 2),
        (5, \'Survey\', 0, 3),
        (6, \'Links\', 0, 3),
        (7, \'Sections\', 0, 3),
        (8, \'Server\', 0, 3),
        (9, \'Download\', 0, 3),
        (10, \'Gallery\', 0, 3),
        (11, \'Guestbook\', 0, 3),
        (12, \'Suggest\', 0, 3),
        (13, \'Textbox\', 0, 9),
        (14, \'Calendar\', 0, 2),
        (15, \'Members\', 0, 9),
        (16, \'Team\', 0, 9),
        (17, \'Defy\', 0, 3),
        (18, \'Recruit\', 0, 3),
        (19, \'Comment\', 0, 9),
        (20, \'Vote\', 0, 9),
        (21, \'Stats\', 0, 2),
        (22, \'Contact\', 0, 3);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    $insert = $insertModules = array();

    $sql = 'SELECT nom
        FROM `'. $this->_session['db_prefix'] .'_modules`';

    $dbsModules = $this->_db->selectMany($sql);

    $modules = array();

    foreach ($dbsModules as $row)
        $modules[] = $row['nom'];

    // install / update 1.7.9 RC5
    if (in_array('PackageMgr', $modules)) {
        $sql = 'DELETE
            FROM `'. $this->_session['db_prefix'] .'_modules`
            WHERE nom = \'PackageMgr\'';

        $dbTable->deleteData(array('DELETE_MODULE', 'PackageMgr'), $sql);
    }

    // install / update 1.7.9 RC1
    if (! in_array('Stats', $modules)) {
        $insert[]           = '(\'Stats\', 0, 2)';
        $insertModules[]    = 'Stats';
    }

    // install / update 1.7.9 RC1
    if (! in_array('Contact', $modules)) {
        $insert[]           = '(\'Contact\', 0, 3)';
        $insertModules[]    = 'Contact';
    }

    if (! empty($insert)) {
        $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_modules`
            (`nom`, `niveau`, `admin`) VALUES '. implode(', ', $insert);

        $dbTable->insertData(array('ADD_MODULE', implode(', ', $insertModules)), $sql);
    }
}

?>