<?php
/**
 * table.modules.c.i.u.php
 *
 * `[PREFIX]_modules` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(MODULES_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$modulesTableCfg = array(
    'fields' => array(
        'id'     => array('type' => 'int(2)',      'null' => false, 'autoIncrement' => true),
        'nom'    => array('type' => 'varchar(50)', 'null' => false, 'default' => '\'\''),
        'niveau' => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\''),
        'admin'  => array('type' => 'int(1)',      'null' => false, 'default' => '\'0\''),
        'type'   => array('type' => 'varchar(30)', 'null' => false, 'default' => '\'standard\''),
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
function getModulesData() {
    global $db;

    $sql = 'SELECT `nom`, `type`
        FROM `'. MODULES_TABLE .'`';

    $dbsModules = $db->selectMany($sql);

    $modules = array();

    foreach ($dbsModules as $row)
        $modules[$row['nom']] = $row['type'];

    return $modules;
}

/*
 * Add new module
 */
function addModule($module, $levelAccess, $adminLevel, $type = 'standard') {
    global $db, $insert, $insertModules;

    $insert[]           = '(\''. $db->quote($module) .'\', '. $levelAccess .', '. $adminLevel .', \''. $db->quote($type) .'\')';
    $insertModules[]    = $module;
}

/*
 * Update module type
 */
function updateModuleType($module, $type) {
    global $db, $updateType, $updateModulesType;

    $updateType[$module] = $type;
    $updateModulesType[] = $module;
}

/*
 * Delete module
 */
function deleteModule($module) {
    global $db, $delete, $deleteModules;

    $insert[]           = 'nom = \''. $db->quote($module) .'\'';
    $deleteModules[]    = $module;
}

/*
 * Update module list
 */
function updateModuleList() {
    global $dbTable, $insert, $insertModules, $updateType, $updateModulesType, $delete, $deleteModules;

    if (! empty($insert)) {
        $sql = 'INSERT INTO `'. MODULES_TABLE .'`
            (`nom`, `niveau`, `admin`, `type`) VALUES '. implode(', ', $insert);

        $dbTable->insertData(array('ADD_MODULE', implode(', ', $insertModules)), $sql);
    }

    if (! empty($updateType)) {
        foreach ($updateType as $name => $type) {
            $sql = 'UPDATE `'. MODULES_TABLE .'`
                SET `type` = \''. $db->quote($type) .'\'
                WHERE nom = \''. $db->quote($name) .'\'';

            //$dbTable->insertData(array('UPDATE_MODULE_TYPE', implode(', ', $updateModulesType)), $sql);
        }
    }

    if (! empty($delete)) {
        $sql = 'DELETE
            FROM `'. MODULES_TABLE .'`
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

    $sql = 'INSERT INTO `'. MODULES_TABLE .'` VALUES
        (1, \'News\', 0, 2, \'standard\'),
        (2, \'Forum\', 0, 2, \'standard\'),
        (3, \'Wars\', 0, 2, \'gaming\'),
        (4, \'Irc\', 0, 2, \'gaming\'),
        (5, \'Survey\', 0, 3, \'standard\'),
        (6, \'Links\', 0, 3, \'standard\'),
        (7, \'Sections\', 0, 3, \'standard\'),
        (8, \'Server\', 0, 3, \'gaming\'),
        (9, \'Download\', 0, 3, \'standard\'),
        (10, \'Gallery\', 0, 3, \'standard\'),
        (11, \'Guestbook\', 0, 3, \'standard\'),
        (12, \'Suggest\', 0, 3, \'standard\'),
        (13, \'Textbox\', 0, 9, \'standard\'),
        (14, \'Calendar\', 0, 2, \'standard\'),
        (15, \'Members\', 0, 9, \'standard\'),
        (16, \'Team\', 0, 9, \'gaming\'),
        (17, \'Defy\', 0, 3, \'gaming\'),
        (18, \'Recruit\', 0, 3, \'gaming\'),
        (19, \'Comment\', 0, 9, \'standard\'),
        (20, \'Vote\', 0, 9, \'standard\'),
        (21, \'Stats\', 0, 2, \'standard\'),
        (22, \'Contact\', 0, 3, \'standard\'),
        (23, \'Page\', 0, 9, \'standard\'),
        (24, \'Game\', 0, 9, \'gaming\');';

    $dbTable->insertData('INSERT_DEFAULT_DATA', $sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    global $insert, $insertModules, $updateType, $updateModulesType, $delete, $deleteModules;

    $insert = $insertModules = $delete = $deleteModules = array();

    $modules = getModuleList();

    // install / update 1.7.9 RC5
    if (array_key_exists('PackageMgr', $modules))
        deleteModule('PackageMgr');

    // install / update 1.7.9 RC1
    if (! array_key_exists('Stats', $modules))
        addModule('Stats', 0, 2);

    if (! array_key_exists('Contact', $modules))
        addModule('Contact', 0, 3);

    // install / update 1.8
    if (! array_key_exists('Page', $modules))
        addModule('Page', 0, 9);

    if (! array_key_exists('Game', $modules, 'gaming'))
        addModule('Game', 0, 9);

    foreach (array('Wars', 'Irc', 'Server', 'Team', 'Defy', 'Recruit') as $moduleName) {
        if (array_key_exists($moduleName, $modules) && $modules[$moduleName] != 'gaming')
            updateModuleType($moduleName, 'gaming');
    }

    updateModuleList();
}

?>
