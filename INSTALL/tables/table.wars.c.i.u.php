<?php
/**
 * table.match.c.i.u.php
 *
 * `[PREFIX]_match` database table script
 *
 * @version 1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

$dbTable->setTable(WARS_TABLE);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table configuration
///////////////////////////////////////////////////////////////////////////////////////////////////////////

$matchTableCfg = array(
    'fields' => array(
        'warid'       => array('type' => 'int(10)',      'null' => false, 'autoIncrement' => true),
        'etat'        => array('type' => 'int(1)',       'null' => false, 'default' => '\'0\''),
        'team'        => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'game'        => array('type' => 'int(11)',      'null' => false, 'default' => '\'0\''),
        'adversaire'  => array('type' => 'text',         'null' => true),
        'url_adv'     => array('type' => 'varchar(60)',  'null' => true,  'default' => 'NULL'),
        'pays_adv'    => array('type' => 'varchar(50)',  'null' => false, 'default' => '\'\''),
        'image_adv'   => array('type' => 'varchar(255)', 'null' => false, 'default' => '\'\''),
        'type'        => array('type' => 'varchar(100)', 'null' => true,  'default' => 'NULL'),
        'style'       => array('type' => 'varchar(100)', 'null' => false, 'default' => '\'\''),
        'date_jour'   => array('type' => 'int(2)',       'null' => true,  'default' => 'NULL'),
        'date_mois'   => array('type' => 'int(2)',       'null' => true,  'default' => 'NULL'),
        'date_an'     => array('type' => 'int(4)',       'null' => true,  'default' => 'NULL'),
        'heure'       => array('type' => 'varchar(10)',  'null' => false, 'default' => '\'\''),
        'map'         => array('type' => 'text',         'null' => true),
        'tscore_team' => array('type' => 'float',        'null' => true,  'default' => 'NULL'),
        'tscore_adv'  => array('type' => 'float',        'null' => true,  'default' => 'NULL'),
        'score_team'  => array('type' => 'text',         'null' => false),
        'score_adv'   => array('type' => 'text',         'null' => false),
        'report'      => array('type' => 'text',         'null' => true),
        'auteur'      => array('type' => 'varchar(50)',  'null' => true,  'default' => 'NULL'),
        'url_league'  => array('type' => 'varchar(100)', 'null' => true,  'default' => 'NULL'),
        'dispo'       => array('type' => 'text',         'null' => true),
        'pas_dispo'   => array('type' => 'text',         'null' => true)
    ),
    'primaryKey' => array('warid'),
    'engine' => 'MyISAM'
);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table function
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
 * Callback function for update row of match database table
 */
function updateWarsDbTableRow($updateList, $row, $vars) {
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
// Table drop
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'drop' && $dbTable->tableExist())
    $dbTable->dropTable();

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table creation
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'install')
    $dbTable->createTable($matchTableCfg);

///////////////////////////////////////////////////////////////////////////////////////////////////////////
// Table update
///////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($process == 'update') {
    // install / update 1.7.9 RC1
    if (! $dbTable->fieldExist('map')) {
        $dbTable->addField('map', $matchTableCfg['fields']['map'], 'heure')
            ->setUpdateFieldData('UPDATE_FIELD map', array('map_1', 'map_2', 'map_3'));
    }

    // install / update 1.7.9 RC6
    if (version_compare($this->_session['version'], '1.7.9', '<=')) {
        $dbTable->setCallbackFunctionVars(array('bbcode' => new bbcode($this->_db, $this->_session, $this->_i18n)))
            ->setUpdateFieldData('APPLY_BBCODE', 'report');
    }

    // install / update 1.7.9 RC1
    if (! $dbTable->fieldExist('tscore_team')) {
        $dbTable->addField('tscore_team', $matchTableCfg['fields']['tscore_team'])
            ->setUpdateFieldData('UPDATE_FIELD tscore_team', 'score_team');
    }

    // install / update 1.7.9 RC1
    if (! $dbTable->fieldExist('tscore_adv')) {
        $dbTable->addField('tscore_adv', $matchTableCfg['fields']['tscore_adv'])
            ->setUpdateFieldData('UPDATE_FIELD tscore_adv', 'score_adv');
    }

    // install / update 1.8
    if (! $dbTable->fieldExist('image_adv'))
        $dbTable->addField('image_adv', $matchTableCfg['fields']['image_adv']);

    $dbTable->applyUpdateFieldListToData();

    if (($response = $dbTable->getJqueryAjaxResponse()) == 'UPDATED') {
        if ($dbTable->fieldExist('map_1'))
            $dbTable->dropField('map_1');

        if ($dbTable->fieldExist('map_2'))
            $dbTable->dropField('map_2');

        if ($dbTable->fieldExist('map_3'))
            $dbTable->dropField('map_3');
    }
}

?>
