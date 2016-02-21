<?php
/**
 * rank.php
 *
 * Backend of Team module - Team rank management
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
    'dataName'              => 'teamRank',
    'tableName'             => TEAM_RANK_TABLE,
    'titleField_dbTable'    => 'titre'
));


/* Team rank list function */

/**
 * Callback function for nkList.
 * Format Team rank row.
 *
 * @param array $row : The Team rank row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Team rank row formated.
 */
function formatTeamRankRow($row, $nbData, $r, $functionData) {
    $row['titre'] = printSecuTags(stripslashes($row['titre']));

    return $row;
}

/* Team rank edit form function */

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to add Team rank.
 *
 * @param array $form : The Team rank form configuration.
 * @return array : The Team rank form configuration prepared.
 */
function prepareFormForAddTeamRank(&$form) {
    $form['items']['ordre']['value'] = '0';
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Team rank.
 *
 * @param array $form : The Team rank form configuration.
 * @param array $teamRank : The Team rank data.
 * @param int $id : The Team rank id.
 * @return array : The Team rank form configuration prepared.
 */
function prepareFormForEditTeamRank(&$form, $teamRank, $id) {
    $form['items']['titre']['value'] = printSecuTags($form['items']['titre']['value']);
}

/* Team rank save form function */

/**
 * Callback function for nkAction_save.
 * Additional process after save Team rank form process.
 *
 * @param int $id : The Team rank id.
 * @param array $teamRank : The Team rank data.
 * @return void
 */
function postSaveTeamRankData($id, $teamRank) {
    if ($id !== null)
        nkDB_update(USER_TABLE, array('ordre' => $teamRank['ordre']), 'rang = '. $id);
}

/* Forum delete form function */

/**
 * Callback function for nkAction_delete.
 * Additional process after delete Team rank.
 *
 * @param int $id : The Team rank id.
 * @return void
 */
function postDeleteTeamRankData($id) {
        nkDB_update(USER_TABLE, array('ordre' => 0), 'rang = '. $id);
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Team rank form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Team rank.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Team rank.
        nkAction_delete();
        break;

    default:
        // Display Team rank list.
        nkAction_list();
        break;
}

?>