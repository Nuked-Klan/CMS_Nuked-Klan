<?php
/**
 * member.php
 *
 * Backend of Team module - Team members management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
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
 * Callback function for nkAction_init.
 * Check if user can add Team.
 *
 * @param void
 * @return bool
 */
function checkTeamAccess() {
    global $nkAction;

    if ($nkAction['actionType'] == 'edit') {
        require_once 'Includes/nkForm.php';

        $dministratorList = nkForm_loadSelectOptions(array(
            'optionsName' => array('User', 'administrator')
        ));

        if (! $dministratorList) {
            printNotification(__('NO_ADMIN'), 'error');
            return false;
        }

        $teamList = nkForm_loadSelectOptions(array(
            'optionsName' => array('Team', 'team')
        ));

        if (! $teamList) {
            printNotification(__('NO_TEAM'), 'error');
            return false;
        }
    }

    return true;
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
            WHERE userId = '. nkDB_quote($data['userId']) .'
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