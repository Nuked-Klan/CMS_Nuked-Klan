<?php
/**
 * table.shoutbox.c.i.u.php
 *
 * `[PREFIX]_shoutbox` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_shoutbox');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of shoutbox database table
 */
function updateShoutboxRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['texte'] = $vars['bbcode']->apply(stripslashes($row['texte']));

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'ip', 'texte');
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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_shoutbox` (
            `id` int(11) NOT NULL auto_increment,
            `auteur` text,
            `ip` varchar(40) NOT NULL default \'\',
            `texte` text,
            `date` varchar(30) NOT NULL default \'\',
            PRIMARY KEY  (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.15
    if ($dbTable->getFieldType('ip') == 'varchar(20)')
        $dbTable->modifyField('ip', array('type' => 'VARCHAR(40)', 'null' => false, 'default' => '\'\''));

    $dbTable->alterTable();

    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'texte');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateShoutboxRow');
}

?>