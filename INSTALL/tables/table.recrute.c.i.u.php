<?php
/**
 * table.recrute.c.i.u.php
 *
 * `[PREFIX]_recrute` database table script
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_recrute');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of recruit database table
 */
function updateRecruitRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['comment'] = $vars['bbcode']->apply(stripslashes($row['comment']));

    return $setFields;
}

function repairRecruitTable($db, $dbTable, $dbPrefix) {
    if (! $dbTable->fieldExist('prenom'))
        $dbTable->addField('prenom', array('type' => 'text', 'null' => false), 'pseudo');

    if (! $dbTable->fieldExist('age'))
        $dbTable->addField('age', array('type' => 'int(3)', 'null' => false, 'default' => '\'0\''), 'prenom');

    if (! $dbTable->fieldExist('mail'))
        $dbTable->addField('mail', array('type' => 'varchar(80)', 'null' => false, 'default' => '\'\''), 'age');

    if (! $dbTable->fieldExist('icq'))
        $dbTable->addField('icq', array('type' => 'varchar(50)', 'null' => false, 'default' => '\'\''), 'mail');

    if (! $dbTable->fieldExist('country'))
        $dbTable->addField('country', array('type' => 'text', 'null' => false), 'icq');

    if (! $dbTable->fieldExist('game')) {
        $dbTable->addField('game', array('type' => 'int(11)', 'null' => false, 'default' => '\'0\''), 'country');
        $db->execute('ALTER TABLE `'. $dbPrefix .'_recrute` ADD INDEX (`game`)');
    }

    if (! $dbTable->fieldExist('connection'))
        $dbTable->addField('connection', array('type' => 'text', 'null' => false), 'game');

    if (! $dbTable->fieldExist('experience'))
        $dbTable->addField('experience', array('type' => 'text', 'null' => false), 'connection');

    if (! $dbTable->fieldExist('dispo'))
        $dbTable->addField('dispo', array('type' => 'text', 'null' => false), 'experience');

    if (! $dbTable->fieldExist('comment'))
        $dbTable->addField('comment', array('type' => 'text', 'null' => false), 'dispo');

    $dbTable->alterTable();
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity('id', 'comment');

    if ($dbTable->getJqueryAjaxResponse() == 'INTEGRITY_FAIL') {
        repairRecruitTable($this->_db, $dbTable, $this->_session['db_prefix']);

        if ($dbTable->getJqueryAjaxResponse() == 'UPDATED')
            $dbTable->setJqueryAjaxResponse('INTEGRITY_ACCEPTED');
    }
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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_recrute` (
            `id` int(11) NOT NULL auto_increment,
            `date` varchar(12) NOT NULL default \'\',
            `pseudo` text NOT NULL,
            `prenom` text NOT NULL,
            `age` int(3) NOT NULL default \'0\',
            `mail` varchar(80) NOT NULL default \'\',
            `icq` varchar(50) NOT NULL default \'\',
            `country` text NOT NULL,
            `game` int(11) NOT NULL default \'0\',
            `connection` text NOT NULL,
            `experience` text NOT NULL,
            `dispo` text NOT NULL,
            `comment` text NOT NULL,
            PRIMARY KEY  (`id`),
            KEY `game` (`game`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // Update BBcode
    // update 1.7.9 RC1
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'comment');
    }

    $dbTable->applyUpdateFieldListToData('id', 'updateRecruitRow');
}

?>