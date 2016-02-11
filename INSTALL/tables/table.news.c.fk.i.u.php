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

$dbTable->setTable(NEWS_TABLE);

require_once 'includes/fkLibs/authorForeignKey.php';

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
    'primaryKey' => array('id'),
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
function updateNewsDbTableRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList)) {
        $setFields['texte'] = $vars['bbcode']->apply(stripslashes($row['texte']));

        if ($row['suite'] != '')
            $setFields['suite'] = $vars['bbcode']->apply(stripslashes($row['suite']));
    }

    if (in_array('UPDATE_AUTHOR_DATA', $updateList)) {
        $userData = getUserData($row['auteur_id']);

        if ($userData === false)
            $setFields['auteur_id'] = null;
        else
            $setFields['auteur'] = $userData['pseudo'];
    }

    return $setFields;
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

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install')
    $dbTable->createTable($newsTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    if ($dbTable->fieldExist('auteur')) {
        if ($dbTable->getFieldType('auteur') != 'varchar(30)' || $dbTable->checkFieldIsNull('auteur'))
            $dbTable->modifyField('auteur', $newsTableCfg['fields']['auteur'])
                ->setUpdateFieldData('UPDATE_AUTHOR_DATA', array('auteur_id', 'auteur'));

        if (! $dbTable->checkFieldIsIndex('auteur'))
            $dbTable->addFieldIndex('auteur');
    }

    if ($dbTable->fieldExist('auteur_id')) {
        if (! $dbTable->checkFieldIsNull('auteur_id'))
            $dbTable->modifyField('auteur_id', $newsTableCfg['fields']['auteur_id']);

        if (! $dbTable->checkFieldIsIndex('auteur_id'))
            $dbTable->addFieldIndex('auteur_id');
    }

    if ($this->_session['db_type'] == 'MySQL' && $this->_db->getTableEngine(NEWS_TABLE) == 'MyISAM')
        $this->_db->execute('ALTER TABLE `'. NEWS_TABLE .'` ENGINE=InnoDB;');

    if (! $dbTable->fieldExist('coverage'))
        $dbTable->addField('coverage', $newsTableCfg['fields']['coverage']);

    // Update BBcode
    // update 1.7.9 RC1 (only texte) / 1.7.9 RC6 (texte & suite)
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', array('texte', 'suite'));
    }

    $dbTable->applyUpdateFieldListToData();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Add foreign key of table
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'addForeignKey') {
    if (! $dbTable->foreignKeyExist('FK_news_authorId'))
        addAuthorIdForeignKey('news', 'auteur_id');

    if (! $dbTable->foreignKeyExist('FK_news_author'))
        addAuthorForeignKey('news', 'auteur');
}

?>