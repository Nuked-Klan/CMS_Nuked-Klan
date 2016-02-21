<?php
/**
 * category.php
 *
 * Backend of Forum module - Forum category management
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
    'dataName'              => 'forumCategory',
    'tableName'             => FORUM_CAT_TABLE,
    'titleField_dbTable'    => 'nom',
    'previewUrl'            => 'index.php?file=Forum'
));


/* Forum category list function */

/**
 * Callback function for nkList.
 * Format Forum category row.
 *
 * @param array $row : The Forum category row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Forum category row formated.
 */
function formatForumCategoryRow($row, $nbData, $r, $functionData) {
    $row['nom'] = printSecuTags(stripslashes($row['nom']));

    return $row;
}

/* Forum category edit form function */

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to add Forum category.
 *
 * @param array $form : The Forum category form configuration.
 * @return array : The Forum category form configuration prepared.
 */
function prepareFormForAddForumCategory(&$form) {
    unset($form['items']['htmlForumCategoryImage']);
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Forum category.
 *
 * @param array $form : The Forum category form configuration.
 * @param array $forumCategory : The Forum category data.
 * @param int $id : The Forum category id.
 * @return array : The Forum category form configuration prepared.
 */
function prepareFormForEditForumCategory(&$form, $forumCategory, $id) {
    if ($forumCategory['image'] !='')
        $form['items']['htmlForumCategoryImage'] = '<img id="forumCategoryImgPreview" src="'. $forumCategory['image'] .'" alt="" />';
}

/* Forum category save form function */

/**
 * Callback function for nkAction_save.
 * Additional process before save Forum category.
 *
 * @param int $id : The Forum category id.
 * @param array $forumCategory : The Forum category data.
 * @return void
 */
function preSaveForumCategoryData($id, $forumCategory) {
    if ($id !== null) {
        nkDB_update(FORUM_TABLE, array(
                'niveau' => $forumCategory['niveau']
            ),
            'cat = '. nkDB_escape($id)
        );
    }
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Forum category form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Forum category.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Forum category.
        nkAction_delete();
        break;

    default:
        // Display Forum category list.
        nkAction_list();
        break;
}

?>