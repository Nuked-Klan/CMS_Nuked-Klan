<?php
/**
 * table.news.c.i.u.php
 *
 * `[PREFIX]_news` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_news');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$newsTableCfg = array(
    'fields' => array(
        'id'        => array('type' => 'int(11)',      'null' => false, 'autoIncrement' => true),
        'cat'       => array('type' => 'varchar(30)',  'null' => false, 'default' => '\'\''),
        'titre'     => array('type' => 'text',         'null' => true),
        'coverage'  => array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''),
        'auteur'    => array('type' => 'varchar(30)',  'null' => false),
        'auteur_id' => array('type' => 'varchar(20)',  'null' => true,  'default' => '\'\''),
        'texte'     => array('type' => 'text',         'null' => true),
        'suite'     => array('type' => 'text',         'null' => true),
        'date'      => array('type' => 'varchar(30)',  'null' => false, 'default' => '\'\''),
        'bbcodeoff' => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'smileyoff' => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\'')
    ),
    'primaryKey' => 'id',
    'index' => array(
        'cat'       => 'cat',
        'auteur'    => 'auteur',
        'auteur_id' => 'auteur_id'
    ),
    'engine' => 'InnoDB'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of news database table
 */
function updateNewsRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList)) {
        $setFields['texte'] = $vars['bbcode']->apply(stripslashes($row['texte']));

        if ($row['suite'] != '')
            $setFields['suite'] = $vars['bbcode']->apply(stripslashes($row['suite']));
    }

    return $setFields;
}

/*
 * Add author Id foreign key of news database table
 */
function addAuthorIdForeignKey($dbTable, $dbprefix) {
    $dbTable->addForeignKey(
        'FK_news_authorId', 'auteur_id',
        $dbprefix .'_users', 'id',
        array('ON DELETE SET NULL')
    );
}

/*
 * Add author Id foreign key of news database table
 */
function addAuthorForeignKey($dbTable, $dbprefix) {
    $dbTable->addForeignKey(
        'FK_news_author', 'auteur',
        $dbprefix .'_users', 'pseudo',
        array('ON UPDATE CASCADE')
    );
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field (id, auteur, texte & suite) exist in 1.6.x version
    // auteur_id field exist in 1.7.x version
    $dbTable->checkIntegrity('id', 'auteur', 'auteur_id', 'texte', 'suite');
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
    $dbTable->createTable($newsTableCfg);

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
    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine($this->_session['db_prefix'] .'_news'))
        $this->_db->execute('ALTER TABLE `'. $this->_session['db_prefix'] .'_news` ENGINE=InnoDB;');

    if (! $dbTable->foreignKeyExist('FK_news_authorId'))
        addAuthorIdForeignKey($dbTable, $this->_session['db_prefix']);

    if (! $dbTable->foreignKeyExist('FK_news_author'))
        addAuthorForeignKey($dbTable, $this->_session['db_prefix']);

    if ($dbTable->getFieldType('auteur') != 'varchar(30)' || $dbTable->checkFieldIsNull('auteur'))
        $dbTable->modifyField('auteur', $newsTableCfg['fields']['autor']);

    if (! $dbTable->checkFieldIsNull('auteur_id'))
        $dbTable->modifyField('auteur_id', $newsTableCfg['fields']['auteur_id']);

    if (! $dbTable->checkFieldIsIndex('auteur'))
        $dbTable->addFieldIndex('auteur');

    if (! $dbTable->checkFieldIsIndex('auteur_id'))
        $dbTable->addFieldIndex('auteur_id');

    if (! $dbTable->fieldExist('coverage')) {
        $dbTable->addField('coverage', $newsTableCfg['fields']['coverage']);
    }

    $dbTable->alterTable();

    // Update BBcode
    // update 1.7.9 RC1 (only texte) / 1.7.9 RC6 (texte & suite)
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', array('texte', 'suite'));
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateNewsRow');
}

?>