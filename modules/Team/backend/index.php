<?php
/**
 * index.php
 *
 * Backend of Team module - Team management
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
    'dataName'              => 'team',
    'tableId'               => 'cid',
    'tableName'             => TEAM_TABLE,
    'titleField_dbTable'    => 'titre',
    'previewUrl'            => 'index.php?file=Team'
));


/* Team list function */

/**
 * Callback function for nkList.
 * Format Team row.
 *
 * @param array $row : The Team row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Team row formated.
 */
function formatTeamRow($row, $nbData, $r, $functionData) {
    if ($row['gameName'] == '')
        $row['gameName'] = 'N/A';

    return $row;
}

/* Team edit form function */

/**
 * Get game list options.
 *
 * @param void
 * @return array : The game list for input select option.
 */
function getGameOptions() {
    $options = array();

    $dbrGame = nkDB_selectMany(
        'SELECT id, name
        FROM '. GAMES_TABLE,
        array('name')
    );

    foreach ($dbrGame as $game)
        $options[$game['id']] = printSecuTags($game['name']);

    return $options;
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to add Team.
 *
 * @param array $form : The Team form configuration.
 * @return array : The Team form configuration prepared.
 */
function prepareFormForAddTeam(&$form) {
    $form['items']['game']['options'] = getGameOptions();
    unset($form['items']['coverageImg']);
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Team.
 *
 * @param array $form : The Team form configuration.
 * @param array $team : The Team data.
 * @return array : The Team form configuration prepared.
 */
function prepareFormForEditTeam(&$form, $team, $id) {
    $form['items']['game']['options']  = getGameOptions();

    if ($team['coverage'] != '') {
        $form['items']['coverage']['html'] = '<div><img src="'. $team['coverage']
            . '" title="'. printSecuTags($team['titre']) .'" id="teamCoverageImg" /></div>';
    }
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Team form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Team.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Team.
        nkAction_delete();
        break;

    default:
        // Display Team list.
        nkAction_list();
        break;
}

?>