<?php
/**
 * table.links.c.i.u.php
 *
 * `[PREFIX]_liens` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(LINKS_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$linksTableCfg = array(
    'fields' => array(
        'id'          => array('type' => 'int(10)',      'null' => false, 'autoIncrement' => true),
        'date'        => array('type' => 'varchar(12)',  'null' => false, 'default' => '\'\''),
        'titre'       => array('type' => 'text',         'null' => false),
        'description' => array('type' => 'text',         'null' => false),
        'url'         => array('type' => 'varchar(200)', 'null' => false, 'default' => '\'\''),
        'cat'         => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'webmaster'   => array('type' => 'text',         'null' => false),
        'country'     => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'count'       => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'broke'       => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\'')
    ),
    'primaryKey' => array('id'),
    'index' => array(
        'cat' => 'cat'
    ),
    'engine' => 'MyISAM'
);

/*
 * Callback function for update row of links database table
 */
function updateLinksDbTableRow($updateList, $row, $vars) {
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

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install')
    $dbTable->createTable($linksTableCfg);

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
