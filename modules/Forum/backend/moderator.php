<?php
/**
 * moderator.php
 *
 * Backend of Forum module - Forum moderator management
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

/* Forum moderator edit form function */

/**
 * Get Users list options.
 *
 * @param array $moderatorList : The Forum moderator list.
 * @return array : The Forum moderator list for input select option.
 */
function getUsersOptions() {
    $options = array();

    $dbrUser = nkDB_selectMany(
        'SELECT id, pseudo
        FROM '. USER_TABLE .'
        WHERE niveau > 0',
        array('niveau', 'pseudo'), array('DESC', 'ASC')
    );

    foreach ($dbrUser as $_user)
        $options[$_user['id']] = $_user['pseudo'];

    return $options;
}

/**
 * Get Forum list options.
 *
 * @param void
 * @return array : The Forum list for input select option.
 */
function getForumOptions() {
    // '' => __('ALL')
    $options = array();

    $dbrForumCat = nkDB_selectMany(
        'SELECT id, nom
        FROM '. FORUM_CAT_TABLE,
        array('ordre', 'nom')
    );

    foreach ($dbrForumCat as $forumCat) {
        $options['start-optgroup-cat_'. $forumCat['id']] = printSecuTags($forumCat['nom']);

        $dbrForum = nkDB_selectMany(
            'SELECT id, nom
            FROM '. FORUM_TABLE .'
            WHERE cat = '. nkDB_escape($forumCat['id']),
            array('ordre', 'nom')
        );

        foreach ($dbrForum as $forum)
            $options[$forum['id']] = printSecuTags($forum['nom']);

        $options['end-optgroup-cat_'. $forumCat['id']] = true;
    }

    return $options;
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to add Forum moderator.
 *
 * @param array $form : The Forum moderator form configuration.
 * @return array : The Forum moderator form configuration prepared.
 */
function prepareFormForAddModerator(&$form) {
    $form['items']['userId']['options'] = getUsersOptions();
    $form['items']['forum']['options']  = getForumOptions();
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Forum moderator.
 *
 * @param array $form : The Forum moderator form configuration.
 * @param array $forum : The Forum moderator data.
 * @return array : The Forum moderator form configuration prepared.
 */
function prepareFormForEditModerator(&$form, $forum, $id) {
    $form['items']['userId']['options'] = getUsersOptions();
    $form['items']['forum']['options']  = getForumOptions();
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