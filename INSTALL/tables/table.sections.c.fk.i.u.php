<?php
/**
 * table.sections.c.fk.i.u.php
 *
 * `[PREFIX]_sections` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(SECTIONS_TABLE);

require_once 'includes/fkLibs/authorForeignKey.php';

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$articlesTableCfg = array(
    'fields' => array(
        'artid'     => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'secid'     => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'title'     => array('type' => 'text',         'null' => false),
        'content'   => array('type' => 'text',         'null' => false),
        'coverage'  => array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''),
        'autor'     => array('type' => 'varchar(30)',  'null' => false),
        'autor_id'  => array('type' => 'varchar(20)',  'null' => true,  'default' => '\'\''),
        'counter'   => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'date'      => array('type' => 'varchar(12)',  'null' => false, 'default' => '\'\'')// TODO : Too low ?
    ),
    'primaryKey' => array('artid'),
    'index' => array(
        'secid'     => 'secid',
        'autor'     => 'autor',
        'autor_id'  => 'autor_id'
    ),
    'engine' => 'InnoDB'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of articles database table
 */
function updateSectionsDbTableRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['content'] = $vars['bbcode']->apply(stripslashes($row['content']));

    if (in_array('UPDATE_AUTHOR_DATA', $updateList)) {
        $userData = getUserData($row['autor_id']);

        if ($userData === false)
            $setFields['autor_id'] = null;
        else
            $setFields['autor'] = $userData['pseudo'];
    }

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field (artid & content) exist in 1.6.x version
    // autor & autor_id field exist in 1.7.x version
    $dbTable->checkIntegrity('artid', 'autor', 'autor_id', 'content');
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
    $dbTable->createTable($articlesTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    if ($dbTable->fieldExist('autor')) {
        if ($dbTable->getFieldType('autor') != 'varchar(30)')
            $dbTable->modifyField('autor', $articlesTableCfg['fields']['autor'])
                ->setUpdateFieldData('UPDATE_AUTHOR_DATA', array('autor', 'autor_id'));

        if (! $dbTable->checkFieldIsIndex('autor'))
            $dbTable->addFieldIndex('autor');
    }

    if ($dbTable->fieldExist('autor_id')) {
        if (! $dbTable->checkFieldIsNull('autor_id'))
            $dbTable->modifyField('autor_id', $articlesTableCfg['fields']['autor_id']);

        if (! $dbTable->checkFieldIsIndex('autor_id'))
            $dbTable->addFieldIndex('autor_id');
    }

    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine(SECTIONS_TABLE) == 'MyISAM')
        $this->_db->execute('ALTER TABLE `'. SECTIONS_TABLE .'` ENGINE=InnoDB;');

    if (! $dbTable->fieldExist('coverage'))
        $dbTable->addField('coverage', $articlesTableCfg['fields']['coverage']);

    if ($dbTable->fieldExist('bbcodeoff'))
        $dbTable->dropField('bbcodeoff');

    if ($dbTable->fieldExist('smileyoff'))
        $dbTable->dropField('smileyoff');

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'content');
    }

    $dbTable->applyUpdateFieldListToData();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Add foreign key of table
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'addForeignKey') {
    if (! $dbTable->foreignKeyExist('FK_articles_authorId'))
        addAuthorIdForeignKey('articles', 'autor_id');

    if (! $dbTable->foreignKeyExist('FK_articles_author'))
        addAuthorForeignKey('articles', 'autor');
}

?>
