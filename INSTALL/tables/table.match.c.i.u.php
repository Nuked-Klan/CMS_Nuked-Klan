<?php
/**
 * table.match.c.i.u.php
 *
 * `[PREFIX]_match` database table script
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable($this->_session['db_prefix'] .'_match');

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of match database table
 */
function updateMatchRow($updateList, $row, $vars) {
    $setFields = array();

    if (in_array('UPDATE_FIELD map', $updateList))
        $setFields['map'] = $row['map_1'] .'|'. $row['map_2'] .'|'. $row['map_3'];

    if (in_array('UPDATE_FIELD tscore_team', $updateList))
        $setFields['tscore_team'] = $row['score_team'];

    if (in_array('UPDATE_FIELD tscore_adv', $updateList))
        $setFields['tscore_adv'] = $row['score_adv'];

    if (in_array('APPLY_BBCODE', $updateList))
        $setFields['report'] = $vars['bbcode']->apply(stripslashes($row['report']));

    return $setFields;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Check table integrity
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'checkIntegrity') {
    // table and field exist in 1.6.x version
    $dbTable->checkIntegrity(
        'warid', 'report', 'score_team', 'score_adv', array(null, 'map_1'), array(null, 'map_2'), array(null, 'map_3')
    );
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
    $sql = 'CREATE TABLE `'. $this->_session['db_prefix'] .'_match` (
            `warid` int(10) NOT NULL auto_increment,
            `etat` int(1) NOT NULL default \'0\',
            `team` int(11) NOT NULL default \'0\',
            `game` int(11) NOT NULL default \'0\',
            `adversaire` text,
            `url_adv` varchar(60) default NULL,
            `pays_adv` varchar(50) NOT NULL default \'\',
            `image_adv` varchar(255) NOT NULL default \'\',
            `type` varchar(100) default NULL,
            `style` varchar(100) NOT NULL default \'\',
            `date_jour` int(2) default NULL,
            `date_mois` int(2) default NULL,
            `date_an` int(4) default NULL,
            `heure` varchar(10) NOT NULL default \'\',
            `map` text,
            `tscore_team` float default NULL,
            `tscore_adv` float default NULL,
            `score_team` text NOT NULL,
            `score_adv` text NOT NULL,
            `report` text,
            `auteur` varchar(50) default NULL,
            `url_league` varchar(100) default NULL,
            `dispo` text,
            `pas_dispo` text,
            PRIMARY KEY  (`warid`)
        ) ENGINE=MyISAM DEFAULT CHARSET='. db::CHARSET .' COLLATE='. db::COLLATION .';';

    $dbTable->dropTable()->createTable($sql);
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.9 RC1
    if (! $dbTable->fieldExist('map')) {
        $dbTable->addField('map', array('type' => 'TEXT', 'null' => false))
            ->setUpdateFieldData('UPDATE_FIELD map', array('map_1', 'map_2', 'map_3'));
    }

    // install / update 1.7.9 RC6
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'report');
    }

    // install / update 1.7.9 RC1
    if (! $dbTable->fieldExist('tscore_team')) {
        $dbTable->addField('tscore_team', array('type' => 'FLOAT', 'default' => 'NULL'))
            ->setUpdateFieldData('UPDATE_FIELD tscore_team', 'score_team');
    }

    // install / update 1.7.9 RC1
    if (! $dbTable->fieldExist('tscore_adv')) {
        $dbTable->addField('tscore_adv', array('type' => 'FLOAT', 'default' => 'NULL'))
            ->setUpdateFieldData('UPDATE_FIELD tscore_adv', 'score_adv');
    }

    // install / update 1.8
    if (! $dbTable->fieldExist('image_adv')) {
        $dbTable->addField('image_adv', array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''));
    }

    $dbTable->alterTable();
    $dbTable->applyUpdateFieldListToData('warid', 'updateMatchRow');

    if (($response = $dbTable->getJqueryAjaxResponse()) == 'UPDATED') {
        if ($dbTable->fieldExist('map_1'))
            $dbTable->dropField('map_1');

        if ($dbTable->fieldExist('map_2'))
            $dbTable->dropField('map_2');

        if ($dbTable->fieldExist('map_3'))
            $dbTable->dropField('map_3');

        $dbTable->alterTable();
    }
}

?>