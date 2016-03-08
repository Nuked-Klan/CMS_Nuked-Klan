<?php
/**
 * moderator.php
 *
 * Backend of Forum module - Forum moderator management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Forum'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'dataName'              => 'moderator',
    'tableName'             => FORUM_MODERATOR_TABLE,
    //'titleField_dbTable'    => 'nom',
    //'previewUrl'            => 'index.php?file=Forum'
));


/* Forum moderator list function */

/**
 * Callback function for nkList.
 * Format Forum moderator row.
 *
 * @param array $row : The Forum moderator row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Forum moderator row formated.
 */
function formatModeratorRow($row, $nbData, $r, $functionData) {
    $row['forumName'] = printSecuTags($row['forumName']);

    return $row;
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Forum moderator form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Forum moderator.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Forum moderator.
        nkAction_delete();
        break;

    default:
        // Display Forum moderator list.
        nkAction_list();
        break;
}

?>