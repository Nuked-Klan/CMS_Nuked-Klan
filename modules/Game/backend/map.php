<?php
/**
 * map.php
 *
 * Backend of Games module - Games map management
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Game'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'dataName'              => 'map',
    'tableName'             => GAMES_MAP_TABLE,
    'titleField_dbTable'    => 'name'
));


/* Game map list function */

/**
 * Callback function for nkList.
 * Format Game map row.
 *
 * @param array $row : The Game map row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Game map row formated.
 */
function formatMapRow($row, $nbData, $r, $functionData) {
    $row['name'] = nkHtmlEntities(stripslashes($row['name']));

    return $row;
}

/* Game map edit form function */

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to add Game map.
 *
 * @param array $form : The Game map form configuration.
 * @return array : The Game map form configuration prepared.
 */
function prepareFormForAddMap(&$form) {
    unset($form['items']['image']['html']);
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Game map.
 *
 * @param array $form : The Game map form configuration.
 * @param array $map : The Game map data.
 * @return array : The Game map form configuration prepared.
 */
function prepareFormForEditMap(&$form, $map, $id) {
    if ($map['image'] != '') {
        $form['items']['image']['html'] = '<div><img src="'. $map['image']
            . '" title="'. printSecuTags($map['name']) .'" id="gameMapImg" /></div>';
    }
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Game map form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Game map.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Game map.
        nkAction_delete();
        break;

    default:
        // Display Game map list.
        nkAction_list();
        break;
}

?>
