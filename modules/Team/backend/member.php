<?php
/**
 * member.php
 *
 * Backend of Team module - Team members management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Team'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'dataName'              => 'teamMember',
    'tableName'             => TEAM_MEMBERS_TABLE,
    //'titleField_dbTable'    => 'userId',           TODO : Faire quelque chose pour recuperer le pseudo
    'previewUrl'            => 'index.php?file=Team'
));


/* Team member list function */

/**
 * Callback function for nkList.
 * Format Team member row.
 *
 * @param array $row : The Team member row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Team member row formated.
 */
function formatTeamMemberRow($row, $nbData, $r, $functionData) {
    $row['date'] = nkDate($row['date'], true);

    return $row;
}

/* Team member edit form function */

/**
 * Get administrator list options.
 *
 * @param void
 * @return array : The administrator list for input select option.
 */
function getAdministratorOptions() {
    $options = array();

    $dbrUser = nkDB_selectMany(
        'SELECT id, pseudo
        FROM '. USER_TABLE .'
        WHERE niveau >= 2',
        array('pseudo')
    );

    foreach ($dbrUser as $administrator)
        $options[$administrator['id']] = printSecuTags($administrator['pseudo']);

    return $options;
}

/**
 * Get Team list options.
 *
 * @param void
 * @return array : The Team list for input select option.
 */
function getTeamOptions() {
    $options = array();

    $dbrTeam = nkDB_selectMany(
        'SELECT cid, titre
        FROM '. TEAM_TABLE,
        array('game', 'ordre')
    );

    foreach ($dbrTeam as $team)
        $options[$team['cid']] = printSecuTags($team['titre']);

    return $options;
}

/**
 * Get Team status list options.
 *
 * @param void
 * @return array : The Team status list for input select option.
 */
function getTeamStatusOptions() {
    $options = array();

    $dbrTeamStatus = nkDB_selectMany(
        'SELECT id, name
        FROM '. TEAM_STATUS_TABLE,
        array('name')
    );

    foreach ($dbrTeamStatus as $teamStatus)
        $options[$teamStatus['id']] = printSecuTags($teamStatus['name']);

    return $options;
}

/**
 * Get Team rank list options.
 *
 * @param void
 * @return array : The Team rank list for input select option.
 */
function getTeamRankOptions() {
    $options = array();

    $dbrTeamRank = nkDB_selectMany(
        'SELECT id, titre
        FROM '. TEAM_RANK_TABLE,
        array('ordre', 'titre')
    );

    foreach ($dbrTeamRank as $teamRank)
        $options[$teamRank['id']] = printSecuTags($teamRank['titre']);

    return $options;
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to add Team member.
 *
 * @param array $form : The Team member form configuration.
 * @return array : The Team member form configuration prepared.
 */
function prepareFormForAddTeamMember(&$form) {
    $form['items']['userId']['options'] = getAdministratorOptions();
    $form['items']['team']['options']   = getTeamOptions();
    $form['items']['status']['options'] = getTeamStatusOptions();
    $form['items']['rank']['options']   = getTeamRankOptions();
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Team member.
 *
 * @param array $form : The Team member form configuration.
 * @param array $teamMember : The Team member data.
 * @return array : The Team member form configuration prepared.
 */
function prepareFormForEditTeamMember(&$form, $teamMember, $id) {
    $form['items']['userId']['options'] = getAdministratorOptions();
    $form['items']['team']['options']   = getTeamOptions();
    $form['items']['status']['options'] = getTeamStatusOptions();
    $form['items']['rank']['options']   = getTeamRankOptions();
}

/* Team member save form function */

/**
 * Callback function for nkAction_edit functions.
 * Additional process after check Team member form.
 * Check Team member options fields.
 *
 * @param array $data : The valid data issue of form submission.
 * @param int $id : The Team member id.
 * @return bool
 */
function postCheckformTeamMemberValidation($data, $id) {
    if ($id === null) {
        $check = nkDB_totalNumRows(
            'FROM '. TEAM_MEMBERS_TABLE .'
            WHERE userId = '. nkDB_escape($data['userId']) .'
            AND team = '. (int) $data['team']
        );

        if ($check >= 1) {
            printNotification(__('MEMBER_ALREADY_REGISTRED_IN_TEAM'), 'error');
            return false;
        }
    }

    return true;
}

// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Team member form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Team member.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Team member.
        nkAction_delete();
        break;

    default:
        // Display Team member list.
        nkAction_list();
        break;
}

?>