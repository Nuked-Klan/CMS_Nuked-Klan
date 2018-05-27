<?php
/**
 * table.gallery.c.i.u.php
 *
 * `[PREFIX]_gallery` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(GALLERY_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$galleryTableCfg = array(
    'fields' => array(
        'sid'         => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'titre'       => array('type' => 'text',         'null' => false),
        'description' => array('type' => 'text',         'null' => false),
        'url'         => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'url2'        => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'url_file'    => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'cat'         => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'date'        => array('type' => 'varchar(12)',  'null' => false, 'default' => '\'\''),
        'count'       => array('type' => 'int(10)',      'null' => false, 'default' => '\'0\''),
        'autor'       => array('type' => 'text',         'null' => false)
    ),
    'primaryKey' => array('sid'),
    'index' => array(
        'cat' => 'cat'
    ),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of gallery database table
 */
function updateGalleryDbTableRow($updateList, $row, $vars) {
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
    $dbTable->checkIntegrity('sid', 'description');
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

if ($process == 'install')
    $dbTable->createTable($galleryTableCfg);

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

    $dbTable->applyUpdateFieldListToData();
}

?>
