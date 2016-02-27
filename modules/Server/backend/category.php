<?php
/**
 * category.php
 *
 * Backend of Server module - Server category management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Server'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'dataName'              => 'serverCategory',
    'tableName'             => SERVER_CAT_TABLE,
    'tableId'               => SERVER_CAT_TABLE_ID,
    'titleField_dbTable'    => 'titre',
    'previewUrl'            => 'index.php?file=Server'
));


/* Server category list function */

/**
 * Callback function for nkList.
 * Format Server category row.
 *
 * @param array $row : The Server category row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Server category row formated.
 */
function formatServerCategoryRow($row, $nbData, $r, $functionData) {
    $row['titre'] = printSecuTags(stripslashes($row['titre']));

    return $row;
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Server category form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Server category.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Server category.
        nkAction_delete();
        break;

    default:
        // Display Server category list.
        nkAction_list();
        break;
}

?>