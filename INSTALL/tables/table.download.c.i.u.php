<?php
/**
 * table.downloads.c.i.u.php
 *
 * `[PREFIX]_downloads` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_downloads');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$downloadTableCfg = array(
    'fields' => array(
        'id'          => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'date'        => array('type' => 'varchar(12)',  'null' => false, 'default' => '\'\''),
        'taille'      => array('type' => 'varchar(6)',   'null' => false, 'default' => '\'0\''),
        'titre'       => array('type' => 'text',         'null' => false),
        'description' => array('type' => 'text',         'null' => false),
        'type'        => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'count'       => array('type' => 'int(10)',      'null' => false, 'default' => '\'0\''),
        'url'         => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'url2'        => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'broke'       => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'url3'        => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'level'       => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'hit'         => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'edit'        => array('type' => 'varchar(12)',  'null' => false, 'default' => '\'\''),
        'screen'      => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'autor'       => array('type' => 'text',         'null' => false),
        'url_autor'   => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'comp'        => array('type' => 'text',         'null' => false)
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'type' => 'type'
    ),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of downloads database table
 */
function updateDownloadsRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['description'] = $vars['bbcode']->apply(stripslashes($row['description']));

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'description');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop')
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install')
    $dbTable->createTable($downloadTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'description');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateDownloadsRow');
}

?>