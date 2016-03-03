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
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Forum.
 *
 * @param array $form : The Forum form configuration.
 * @param array $forum : The Forum data.
 * @return array : The Forum form configuration prepared.
 */
function prepareFormForEditForum(&$form, $forum, $id) {
    if ($forum['image'] !='')
        $form['items']['image']['html'] = '<img id="forumImgPreview" src="'. $forum['image'] .'" title="'. $forum['nom'] .'" alt="" />';
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

    default:
        // Display Forum list.
        nkAction_list();
        break;
}

?>