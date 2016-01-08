<?php
/**
 * table.modules.c.i.u.php
 *
 * `[PREFIX]_modules` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_modules');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$modulesTableCfg = array(
    'fields' => array(
        'id'     => array('type' => 'int(2)',      'null' => false, 'autoIncrement' => true),
        'nom'    => array('type' => 'varchar(50)', 'null' => false, 'default' => '\'\''),
        'niveau' => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\''),
        'admin'  => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\'')
    ),
    'primaryKey' => array('id'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Return modules list stored in modules table
 */
function getModuleList($db, $dbPrefix) {
    $sql = 'SELECT nom
        FROM `'. $dbPrefix .'_modules`';

    $dbsModules = $db->selectMany($sql);

    $modules = array();

    foreach ($dbsModules as $row)
        $modules[] = $row['nom'];

    return $modules;
}

/*
 * Add new module
 */
function addModule($db, $module, $levelAccess, $adminLevel, &$insert, &$insertModules) {
    $insert[]           = '(\''. $db->quote($module) .'\', '. $levelAccess .', '. $adminLevel .')';
    $insertModules[]    = $module;
}

/*
 * Delete module
 */
function deleteModule($db, $module, &$delete, &$deleteModules) {
    $insert[]           = 'nom = \''. $db->quote($module) .'\'';
    $deleteModules[]    = $module;
}

/*
 * Update module list
 */
function updateModuleList($dbTable, $dbPrefix, $insert, $insertModules, $delete, $deleteModules) {
    if (! empty($insert)) {
        $sql = 'INSERT INTO `'. $dbPrefix .'_modules`
            (`nom`, `niveau`, `admin`) VALUES '. implode(', ', $insert);

        $dbTable->insertData(array('ADD_MODULE', implode(', ', $insertModules)), $sql);
    }

    if (! empty($delete)) {
        $sql = 'DELETE
            FROM `'. $dbPrefix .'_modules`
            WHERE '. implode(' OR ', $delete);

        $dbTable->deleteData(array('DELETE_MODULE', implode(', ', $deleteModules)), $sql);
    }
}

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
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $dbTable->createTable($modulesTableCfg);

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
        (22, \'Contact\', 0, 3),
        (23, \'Equipe\', 0, 2),
        (24, \'Page\', 0, 9);';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    $insert = $insertModules = $delete = $deleteModules = array();

    $modules = getModuleList($this->_db, $this->_session['db_prefix']);

    // install / update 1.7.9 RC5
    if (in_array('PackageMgr', $modules))
        deleteModule($this->_db, 'PackageMgr', $delete, $deleteModules);

    // install / update 1.7.9 RC1
    if (! in_array('Stats', $modules))
        addModule($this->_db, 'Stats', 0, 2, $insert, $insertModules);

    if (! in_array('Contact', $modules))
        addModule($this->_db, 'Contact', 0, 3, $insert, $insertModules);

    // install / update 1.8
    if (! in_array('Equipe', $modules))
        addModule($this->_db, 'Equipe', 0, 2, $insert, $insertModules);

    if (! in_array('Page', $modules))
        addModule($this->_db, 'Page', 0, 9, $insert, $insertModules);

    updateModuleList($dbTable, $this->_session['db_prefix'], $insert, $insertModules, $delete, $deleteModules);
}

?>