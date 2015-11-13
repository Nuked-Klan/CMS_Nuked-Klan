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

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'texte', 'suite');
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Convert charset and collation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkAndConvertCharsetAndCollation')
    $dbTable->checkAndConvertCharsetAndCollation();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install') {
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_news` (
            `id` int(11) NOT NULL auto_increment,
            `cat` varchar(30) NOT NULL default \'\',
            `titre` text,
            `coverage` varchar(255) NOT NULL default \'\',
            `auteur` text,
            `auteur_id` varchar(20) NOT NULL default \'\',
            `texte` text,
            `suite` text,
            `date` varchar(30) NOT NULL default \'\',
            `bbcodeoff` int(1) NOT NULL default \'0\',
            `smileyoff` int(1) NOT NULL default \'0\',
            PRIMARY KEY  (`id`),
            KEY `cat` (`cat`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.8
    if (! $dbTable->fieldExist('coverage')) {
        $dbTable->addField('coverage', array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''));
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