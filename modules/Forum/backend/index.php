<?php
/**
 * index.php
 *
 * Backend of Forum module - Forum management
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
    'dataName'              => 'forum',
    'tableName'             => FORUM_TABLE,
    'titleField_dbTable'    => 'nom',
    'previewUrl'            => 'index.php?file=Forum'
));


/**
 * Callback function for nkAction_list & nkAction_edit functions.
 *
 * @param int $id : The Forum id.
 * @return string : The Forum title for list or add / edit form.
 */
function getForumTitle($id = null) {
    global $op;

    if ($op == 'edit') {
        if ($id === null)
            return __('ADMIN_FORUM') .' - '. __('ADD_FORUM');
        else
            return __('ADMIN_FORUM') .' - '. __('EDIT_THIS_FORUM');
    }

    return __('ADMIN_FORUM');
}

/* Forum list function */

/**
 * Callback function for nkList.
 * Format Forum row.
 *
 * @param array $row : The Forum row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Forum row formated.
 */
function formatForumRow($row, $nbData, $r, $functionData) {
    $row['nom']         = printSecuTags(stripslashes($row['nom']));
    $row['category']    = printSecuTags(stripslashes($row['category']));

    return $row;
}

/* Forum edit form function */

/**
 * Get Forum moderator list.
 *
 * @param array $moderatorList : The Forum moderator list.
 * @return array : The Forum moderator list for input select option.
 */
function getModeratorOptions($moderatorList = array()) {
    $options = array('' => __('NONE'));

    $dbrUser = nkDB_selectMany(
        'SELECT id, pseudo
        FROM '. USER_TABLE .'
        WHERE niveau > 0',
        array('niveau', 'pseudo'), array('DESC', 'ASC')
    );

    foreach ($dbrUser as $_user) {
        if (! in_array($_user['id'], $moderatorList))
            $options[$_user['id']] = $_user['pseudo'];
    }

    return $options;
}

function getForumCategoryOptions() {
    $dbrForumCat = nkDB_selectMany(
        'SELECT id, nom
        FROM '. FORUM_CAT_TABLE,
        array('ordre', 'nom')
    );

    $options = array();

    foreach ($dbrForumCat as $forumCat)
        $options[$forumCat['id']] = printSecuTags($forumCat['nom']);

    return $options;
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to add Forum.
 *
 * @param array $form : The Forum form configuration.
 * @return array : The Forum form configuration prepared.
 */
function prepareFormForAddForum(&$form) {
    unset($form['items']['moderatorList']);

    $form['items']['moderateurs']['options'] = getModeratorOptions();
    $form['items']['cat']['options']         = getForumCategoryOptions();
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Forum.
 *
 * @param array $form : The Forum form configuration.
 * @param array $forum : The Forum data.
 * @return array : The Forum form configuration prepared.
 */
function prepareFormForEditForum(&$form, $forum, $id) {
    $moderatorList = array();

    if ($forum['moderateurs'] != '') {
        $moderators  = explode('|', $forum['moderateurs']);
        $nbModerator = count($moderators);

        for ($i = 0; $i < $nbModerator; $i++) {
            $sep = ($i == 0) ? '' : ', ';

            // TODO : Use sql IN() clause?
            $dbrUser = nkDB_selectOne(
                'SELECT id, pseudo
                FROM '. USER_TABLE .'
                WHERE id = '. nkDB_escape($moderators[$i])
            );

            $form['items']['moderatorList']['html'] .= $sep . $dbrUser['pseudo'] .'&nbsp;(<a href="index.php?admin=Forum&amp;op=deleteModerator&amp;user_id='. $dbrUser['id'] .'&amp;forum_id='. $id .'"><img style="border: 0;vertical-align:bottom;" src="modules/Admin/images/icons/cross.png" alt="" title="'. __('DELETE_THIS_MODERATOR') .'" /></a>)';
            $moderatorList[] = $dbrUser['id'];
        }
    }
    else{
        $form['items']['moderatorList']['html'] = __('NONE');
    }

    if ($forum['image'] !='')
        $form['items']['image']['html'] = '<img id="forumImgPreview" src="'. $forum['image'] .'" title="'. $forum['nom'] .'" alt="" />';

    $form['items']['moderateurs']['options'] = getModeratorOptions($moderatorList);
    $form['items']['cat']['options']         = getForumCategoryOptions();
}

/* Forum save form function */

/**
 * Callback function for nkAction_save.
 * Additional process before save Forum.
 *
 * @param int $id : The Forum id.
 * @param array $forum : The Forum data.
 * @return void
 */
function preSaveForumData($id, $forum) {
    if ($id !== null) {
        if ($forum['moderateurs'] != '') {
            $dbrForum = nkDB_selectOne(
                'SELECT moderateurs
                FROM '. FORUM_TABLE .'
                WHERE id = '. nkDB_escape($id)
            );

            if ($dbrForum['moderateurs'] != '')
                $moderators = $dbrForum['moderateurs'] .'|'. $forum['moderateurs'];
            else
                $moderators = $forum['moderateurs'];

            nkDB_update(FORUM_TABLE, array(
                    'moderateurs' => $moderators
                ),
                'id = '. nkDB_escape($id)
            );
        }
    }
}

/* Forum delete form function */

/**
 * Callback function for nkAction_delete.
 * Additional process before delete Forum.
 *
 * @param int $id : The Forum id.
 * @return void
 */
function preDeleteForumData($id) {
    $dbrForumThreads = nkDB_selectMany(
        'SELECT id, sondage
        FROM '. FORUM_THREADS_TABLE .'
        WHERE forum_id = '. nkDB_escape($id)
    );

    foreach ($dbrForumThreads as $forumThreads) {
        if ($forumThreads['sondage'] == 1) {
            $dbrForumPoll = nkDB_selectOne(
                'SELECT id
                FROM '. FORUM_POLL_TABLE .'
                WHERE thread_id = '. nkDB_escape($forumThreads['id'])
            );

            nkDB_delete(FORUM_POLL_TABLE, 'id = '. nkDB_escape($dbrForumPoll['id']));
            nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. nkDB_escape($dbrForumPoll['id']));
            nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. nkDB_escape($dbrForumPoll['id']));
        }
    }

    nkDB_delete(FORUM_THREADS_TABLE, 'forum_id = '. nkDB_escape($id));
    nkDB_delete(FORUM_MESSAGES_TABLE, 'forum_id = '. nkDB_escape($id));
}

// Delete Forum moderator.
function deleteModerator() {
    $dbrForum = nkDB_selectOne(
        'SELECT moderateurs
        FROM '. FORUM_TABLE .'
        WHERE id = '. nkDB_escape($_GET['forum_id'])
    );

    $list       = explode('|', $dbrForum['moderateurs']);
    $end        = count($list) - 1;
    $moderators = '';

    for ($i = 0; $i <= $end; $i++) {
        if ($i == 0 || ($i == 1 && $list[0] == $_GET['user_id']))
            $sep = '';
        else
            $sep = '|';

        if ($list[$i] != $_GET['user_id'])
            $moderators .= $sep . $list[$i];
    }

    nkDB_update(FORUM_TABLE, array('moderateurs' => $moderators), 'id = '. nkDB_escape($_GET['forum_id']));

    $dbrUser = nkDB_selectOne(
        'SELECT pseudo
        FROM '. USER_TABLE .'
        WHERE id = '. nkDB_escape($_GET['user_id'])
    );

    saveUserAction(__('ACTION_DELETE_MODERATOR') .': '. $dbrUser['pseudo']);

    printNotification(__('MODERATOR_DELETED'), 'success');
    redirect('index.php?admin=Forum&op=edit&id='. $_GET['forum_id'], 2);
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Forum form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Forum.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Forum.
        nkAction_delete();
        break;

    case 'deleteModerator' :
        deleteModerator();
        break;

    default:
        // Display Forum list.
        nkAction_list();
        break;
}

?>