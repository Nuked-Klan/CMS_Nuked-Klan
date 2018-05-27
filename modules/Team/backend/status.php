<?php
/**
 * status.php
 *
 * Backend of Team module - Team status management
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Team'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'dataName'              => 'teamStatus',
    'tableName'             => TEAM_STATUS_TABLE,
    'titleField_dbTable'    => 'name'
));


/* Team status save form function */

/**
 * Callback function for nkAction_edit functions.
 * Additional process after check Team status form.
 * Check Team status options fields.
 *
 * @param array $data : The valid data issue of form submission.
 * @param int $id : The Team status id.
 * @return bool
 */
function postCheckformTeamStatusValidation($data, $id) {
    if ($id === null) {
        $check = nkDB_totalNumRows(
            'FROM '. TEAM_STATUS_TABLE .'
            WHERE name = '. nkDB_quote($data['name'])
        );

        if ($check >= 1) {
            printNotification(__('TEAM_STATUS_ALREADY_EXIST'), 'error');
            return false;
        }
    }

    return true;
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Team status form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Team status.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Team status.
        nkAction_delete();
        break;

    default:
        // Display Team status list.
        nkAction_list();
        break;
}

?>
