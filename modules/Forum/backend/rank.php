<?php
/**
 * rank.php
 *
 * Backend of Forum module - Forum rank management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Forum'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'dataName'              => 'forumRank',
    'tableName'             => FORUM_RANK_TABLE,
    'titleField_dbTable'    => 'nom'
));


/**
 * Callback function for nkAction_list & nkAction_edit functions.
 *
 * @param int $id : The Forum rank id.
 * @return string : The Forum rank title for list or add / edit form.
 */
function getForumRankTitle($id = null) {
    global $op;

    if ($op == 'edit') {
        if ($id === null)
            return __('ADMIN_FORUM') .' - '. __('ADD_RANK');
        else
            return __('ADMIN_FORUM') .' - '. __('EDIT_THIS_RANK');
    }

    return __('ADMIN_FORUM') .' - '. __('RANK_MANAGEMENT');
}

/* Forum rank list function */

/**
 * Callback function for nkList.
 * Format Forum rank row.
 *
 * @param array $row : The Forum rank row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Forum rank row formated.
 */
function formatForumRankRow($row, $nbData, $r, $functionData) {
    $row['nom'] = printSecuTags(stripslashes($row['nom']));

    if ($row['type'] == 1) {
        $row['nom'] = '<b>'. $row['nom'] .'</b>';
        $row['type'] = __('MODERATOR');
        $row['post'] = '-';
        $row['noDeleteRow'] = true;
    }
    else if ($row['type'] == 2) {
        $row['nom'] = '<b>'. $row['nom'] .'</b>';
        $row['type'] = __('ADMINISTRATOR');
        $row['post'] = '-';
        $row['noDeleteRow'] = true;
    }
    else {
        $row['type'] = __('MEMBER');
    }

    return $row;
}

/* Forum rank edit form function */

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Forum rank.
 *
 * @param array $form : The Forum rank form configuration.
 * @param array $forumRank : The Forum rank data.
 * @param int $id : The Forum rank id.
 * @return array : The Forum rank form configuration prepared.
 */
function prepareFormForEditForumRank(&$form, $forumRank, $id) {
    if ($forumRank['type'] != 0)
        $form['items']['post']['type'] = 'hidden';

    if ($forumRank['image'] !='')
        $form['items']['image']['html'] = '<img id="formRankImgPreview" src="'. $forumRank['image'] .'" title="'. $forumRank['nom'] .'" alt="" />';
}

/* Forum rank save form function */

/**
 * Callback function for nkAction_save.
 * Additional process before formating Forum rank data.
 *
 * @param int $id : The Forum rank id.
 * @param array $forumRank : The Forum rank data.
 * @return void
 */
function preFormatingForumRankData($id, $forumRank) {
    if ($_POST['image'] == 'http://')
        $_POST['image'] = '';
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Forum rank form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Forum rank.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Forum rank.
        nkAction_delete();
        break;

    default:
        // Display Forum rank list.
        nkAction_list();
        break;
}

?>