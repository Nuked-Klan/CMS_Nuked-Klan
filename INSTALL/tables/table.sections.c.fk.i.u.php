<?php
/**
 * table.sections.c.i.u.php
 *
 * `[PREFIX]_sections` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_sections');

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
        'bbcodeoff' => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'smileyoff' => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'date'      => array('type' => 'varchar(12)',  'null' => false, 'default' => '\'\'')
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
function updateSectionsRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['content'] = $vars['bbcode']->apply(stripslashes($row['content']));

    return $setFields;
}

/*
 * Add author Id foreign key of articles database table
 */
function addAuthorIdForeignKey($dbTable, $dbprefix) {
    $dbTable->addForeignKey(
        'FK_articles_authorId', 'autor_id',
        $dbprefix .'_users', 'id',
        array('ON DELETE SET NULL')
    );
}

/*
 * Add author Id foreign key of articles database table
 */
function addAuthorForeignKey($dbTable, $dbprefix) {
    $dbTable->addForeignKey(
        'FK_articles_author', 'autor',
        $dbprefix .'_users', 'pseudo',
        array('ON UPDATE CASCADE')
    );
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

if ($process == 'drop')
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install')
    $dbTable->createTable($articlesTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Add foreign key of table
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'addForeignKey') {
    addAuthorIdForeignKey($dbTable, $this->_session['db_prefix']);
    addAuthorForeignKey($dbTable, $this->_session['db_prefix']);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine($this->_session['db_prefix'] .'_sections'))
        $this->_db->execute('ALTER TABLE `'. $this->_session['db_prefix'] .'_sections` ENGINE=InnoDB;');

    if (! $dbTable->foreignKeyExist('FK_articles_authorId'))
        addAuthorIdForeignKey($dbTable, $this->_session['db_prefix']);

    if (! $dbTable->foreignKeyExist('FK_articles_author'))
        addAuthorForeignKey($dbTable, $this->_session['db_prefix']);

    if ($dbTable->getFieldType('autor') != 'varchar(30)')
        $dbTable->modifyField('autor', $articlesTableCfg['fields']['autor']);

    if (! $dbTable->checkFieldIsNull('autor_id'))
        $dbTable->modifyField('autor_id', $articlesTableCfg['fields']['autor_id']);

    if (! $dbTable->checkFieldIsIndex('autor'))
        $dbTable->addFieldIndex('autor');

    if (! $dbTable->checkFieldIsIndex('autor_id'))
        $dbTable->addFieldIndex('autor_id');

    // install / update 1.8
    if (! $dbTable->fieldExist('coverage'))
        $dbTable->addField('coverage', $articlesTableCfg['fields']['coverage']);

    $dbTable->alterTable();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'content');
    }

    $dbTable->applyUpdateFieldListToData('artid', 'updateSectionsRow');
}

?>