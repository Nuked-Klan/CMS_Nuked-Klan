<?php
/**
 * table.sections_cat.c.i.u.php
 *
 * `[PREFIX]_sections_cat` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */


$dbTable->setTable($this->_session['db_prefix'] .'_sections_cat');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$articlesCatTableCfg = array(
    'fields' => array(
        'secid'       => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'parentid'    => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'secname'     => array('type' => 'varchar(40)',  'null' => false, 'default' => '\'\''),
        'description' => array('type' => 'text',         'null' => false),
        'image'       => array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''),
        'position'    => array('type' => 'int(2)',       'unsigned' => true, 'null' => false, 'default' => '\'0\'')
    ),
    'primaryKey' => array('secid'),
    'index' => array(
        'parentid' => 'parentid'
    ),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of sections category database table
 */
function updateSectionsCatRow($updateList, $row, $vars) {
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
    $dbTable->checkIntegrity('secid', 'description');
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
    $dbTable->createTable($articlesCatTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    if (! $dbTable->fieldExist('image'))
        $dbTable->addField('image', $articlesCatTableCfg['fields']['image']);

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'description');
    }

    $dbTable->applyUpdateFieldListToData('secid', 'updateSectionsCatRow');
}

?>