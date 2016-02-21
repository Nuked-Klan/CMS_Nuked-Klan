<?php
/**
 * games.php
 *
 * Backend of Games module - Games management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Game'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'dataName'              => 'game',
    'tableName'             => GAMES_TABLE,
    'titleField_dbTable'    => 'name'
));


/* Game list function */

/**
 * Callback function for nkList.
 * Format Game row.
 *
 * @param array $row : The Game row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Game row formated.
 */
function formatGameRow($row, $nbData, $r, $functionData) {
    $row['name'] = nkHtmlEntities(stripslashes($row['name']));

    return $row;
}

/* Game edit form function */


/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to add Game.
 *
 * @param array $form : The Game form configuration.
 * @return array : The Game form configuration prepared.
 */
function prepareFormForAddGame(&$form) {
    $form['items']['htmlMapList'] = '<div id="mapList" class="nkFormRow"></div>';
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Game.
 *
 * @param array $form : The Game form configuration.
 * @param array $game : The Game data.
 * @return array : The Game form configuration prepared.
 */
function prepareFormForEditGame(&$form, $game, $id) {
    $form['items']['htmlMapList'] = '<div id="mapList" class="nkFormRow">';

    if ($game['map'] != '') {
        foreach(explode('|', $game['map']) as $mapping)
            $form['items']['htmlMapList'] .= $mapping .'</br/>';
    }

    $form['items']['htmlMapList'] .= '</div>';
}

// Display game icon list pop-up
function displayGameIcon() {
    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_ICONLIST);

    $fileList = array();

    if (is_dir('images/games')) {
        $fileList = scandir('images/games');

        if ($fileList !== false)
            $fileList = array_diff($fileList, array('.', '..', 'index.html', 'Thumbs.db'));
        else
            $fileList = array();
    }

    echo applyTemplate('modules/Games/showIcon', array('fileList' => $fileList));
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Game form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Game.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Game.
        nkAction_delete();
        break;

    case 'showIcon' :
        displayGameIcon();
        break;

    default:
        // Display Game list.
        nkAction_list();
        break;
}

?>