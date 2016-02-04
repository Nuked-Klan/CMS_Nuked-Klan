<?php
/**
 * vote.php
 *
 * Frontend of Forum module - Forum poll management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Forum'))
    return;

compteur('Forum');

require_once 'modules/Forum/core.php';
require_once 'Includes/nkAction.php';

global $forumId, $threadId;

$forumId     = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
$threadId    = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;
$surveyField = (isset($_GET['survey_field'])) ? (int) $_GET['survey_field'] : 0;

nkAction_setParams(array(
    'dataName'                  => 'forumPoll',
    'tableName'                 => FORUM_POLL_TABLE,
    'deleteConfirmation'        => __('CONFIRM_DELETE_POLL'),
    'uriData'                   => array('forum_id' => $forumId, 'thread_id' => $threadId, 'survey_field' => $surveyField),
    'backRedirection'           => 'forumPollRedirect'
));

/* Forum poll edit form function */

/**
 * Check if the user has the right to access Forum survey.
 *
 * @param void
 * @return bool
 */
function checkForumPollAccess() {
    global $nkAction, $visiteur, $user, $forumId, $threadId;

    $fields = ($nkAction['id'] === null) ? ', sondage' : '';

    $dbrForumThread = nkDB_selectOne(
        'SELECT auteur_id'. $fields .'
        FROM '. FORUM_THREADS_TABLE .'
        WHERE id = '. nkDB_escape($threadId)
    );

    if (! $dbrForumThread) {
        printNotification(_NOTOPICEXIST, 'error');
        return false;
    }

    // Get poll access
    $pollAuthorAccess = $user && $user['id'] == $dbrForumThread['auteur_id'];

    if ($nkAction['id'] === null) {
        // Check Forum level poll
        $dbrForum = nkDB_selectOne(
            'SELECT level_poll
            FROM '. FORUM_TABLE .'
            WHERE id = '. $forumId
        );

        if (! $dbrForum) {
            printNotification(_NOFORUMEXIST, 'error');
            return false;
        }

        $access = $pollAuthorAccess && $dbrForumThread['sondage'] == 1
            && $visiteur >= $dbrForum['level_poll'];
    }
    else
        $access = $pollAuthorAccess || isForumAdministrator($forumId);

    if ($access) return true;

    printNotification(__('ZONE_ADMIN'), 'error');

    return false;
}

/**
 * Add Forum poll options in form configuration.
 *
 * @param array $form : The Forum poll form configuration.
 * @param array $forumPollOptions : The Forum poll options.
 * @return void
 */
function setForumPollOptions(&$form, $forumPollOptions) {
    $r = 1;

    foreach ($forumPollOptions as $option) {
        $form['items']['option'. $r] = array(
            'name'  => 'option['. $r .']',
            'label' => __('OPTION') .'&nbsp;'. $r,
            'type'  => 'text',
            'size'  => 40,
            'value' => $option['option_text']
        );

        $r++;
    }
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to add Forum poll.
 *
 * @param array $form : The Forum poll form configuration.
 * @return array : The Forum poll form configuration prepared.
 */
function prepareFormForAddForumPoll(&$form) {
    global $nuked, $surveyField;

    // Check maximum option
    $maxOptions = min(max(2, $surveyField), $nuked['forum_field_max']);

    // Set default option
    $pollOptions = array_fill(1, $maxOptions, array('option_text' => ''));

    setForumPollOptions($form, $pollOptions);

    $form['items']['maxOption'] = array(
        'type'  => 'hidden',
        'value' => $maxOptions
    );
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Forum poll.
 *
 * @param array $form : The Forum poll form configuration.
 * @param array $forumPoll : The Forum data.
 * @return array : The Forum poll form configuration prepared.
 */
function prepareFormForEditForumPoll(&$form, $forumPoll, $id) {
    global $nuked;

    $dbrForumPollOptions = nkDB_selectMany(
        'SELECT id, option_text
        FROM '. FORUM_OPTIONS_TABLE .'
        WHERE poll_id = '. nkDB_escape($id),
        array('id')
    );

    setForumPollOptions($form, $dbrForumPollOptions);

    $nbOptions = count($dbrForumPollOptions);

    // Enabled new option if needed
    if ($nbOptions < $nuked['forum_field_max']) {
        $form['items']['newOption'] = array(
            'name'  => 'newOption',
            'label' => __('OPTION') .'&nbsp;'. $nbOptions++,
            'type'  => 'text',
            'size'  => 40
        );
    }
}

/**
 * Return edit Forum poll view content.
 *
 * @param string $generatedForm : The Forum poll form content.
 * @return string : The full edit Forum poll view content.
 */
function generateForumPollEditView($generatedForm) {
    return applyTemplate('modules/Forum/editPoll', array(
        'form' => $generatedForm
    ));
}

/* Forum poll save form function */

/**
 * Check Forum poll options fields.
 * Additional process after check Forum poll form.
 *
 * @param void
 * @return bool
 */
function postCheckformForumPollValidation() {
    global $nkAction, $forumId, $threadId;

    $nbFilledOption = 0;

    if (isset($_POST['option']) && is_array($_POST['option'])) {
        $nbFilledOption = count(array_filter(array_map('trim', $_POST['option'])));

        if (isset($_POST['newOption']) && ! ctype_space($_POST['newOption']) && $_POST['newOption'] != '')
            $nbFilledOption++;
    }

    if ($nbFilledOption < 2) {
        printNotification(__('2_OPTION_MIN'), 'warning');

        if ($nkAction['id'] === null)
            redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
        else
            redirect('index.php?file=Forum&page=poll&op=edit&id='. $nkAction['id'] .'&forum_id='. $forumId .'&thread_id='. $threadId, 2);

        return false;
    }

    return true;
}

/**
 * Callback function for nkAction_save.
 * Additional process before formating Forum poll data.
 *
 * @param int $id : The Forum poll id.
 * @param array $forumPoll : The Forum poll data.
 * @return void
 */
function preFormatingForumPollData($id, &$forumPoll) {
    global $threadId;

    $forumPoll['thread_id'] = $threadId;
}

/**
 * Callback function for nkAction_save.
 * Additional process after insert / update Forum poll data.
 *
 * @param int $id : The Forum poll id.
 * @param array $forumPoll : The Forum poll data.
 * @return void
 */
function postSaveForumPollData($id, $forumPoll) {
    global $nkAction, $nuked;

    // Check maximum option
    $nbOptions = (isset($_POST['maxOptions'])) ? (int) $_POST['maxOptions'] : count($_POST['option']);

    if ($nbOptions > $nuked['forum_field_max'])
        $maxOptions = $nuked['forum_field_max'];
    else
        $maxOptions = $nbOptions;

    $maxOptions++;

    if ($nkAction['saveAction'] == 'insert')
        insertForumPollOptions($id, $nbOptions, $maxOptions);
    else
        updateForumPollOptions($id, $nbOptions, $maxOptions);
}

/**
 * Add new Forum poll options in database.
 *
 * @param int $id : The Forum poll ID.
 * @param int $nbOptions : The count of Forum poll options.
 * @param int $maxOptions : The maximum of Forum poll options set in Forum post.
 * @return void
 */
function insertForumPollOptions($id, $nbOptions, $maxOptions) {
    global $nuked;

    // Save poll option in database.
    $r = 1;

    while ($r < $maxOptions) {
        if ($_POST['option'][$r] != '') {
            nkDB_insert(FORUM_OPTIONS_TABLE, array(
                'id'            => $r,
                'poll_id'       => $id,
                'option_text'   => stripslashes($_POST['option'][$r]),
                'option_vote'   => 0
            ));
        }
        $r++;
    }

    if ($nbOptions < $nuked['forum_field_max'] && isset($_POST['newOption']) && $_POST['newOption'] != '') {
        nkDB_insert(FORUM_OPTIONS_TABLE, array(
            'id'            => $r,
            'poll_id'       => $id,
            'option_text'   => $_POST['newOption'],
            'option_vote'   => 0
        ));
    }
}

/**
 * Edit Forum poll options in database.
 *
 * @param int $id : The Forum poll ID.
 * @param int $nbOptions : The count of Forum poll options.
 * @param int $maxOptions : The maximum of Forum poll options set in Forum post.
 * @return void
 */
function updateForumPollOptions($id, $nbOptions, $maxOptions) {
    global $nuked;

    // Save poll option in database.
    $r = 1;

    while ($r < $maxOptions) {
        if ($_POST['option'][$r] != '') {
            nkDB_update(FORUM_OPTIONS_TABLE, array(
                    'option_text' => stripslashes($_POST['option'][$r])
                ),
                'poll_id = '. $id .' AND id = '. $r
            );
        }
        else {
            nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. $id .' AND id = '. $r);
        }

        $r++;
    }

    if ($nbOptions < $nuked['forum_field_max'] && isset($_POST['newOption']) && $_POST['newOption'] != '') {
        nkDB_insert(FORUM_OPTIONS_TABLE, array(
            'id'            => $r,
            'poll_id'       => $id,
            'option_text'   => $_POST['newOption'],
            'option_vote'   => 0
        ));
    }
}

/* Forum poll delete form function */

/**
 * Delete Forum poll additionnal data.
 * Additional process before Forum poll data.
 *
 * @param int $id : The Forum poll ID.
 * @return void
 */
function preDeleteForumPollData($id) {
    nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. $id);
    nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. $id);
    nkDB_update(FORUM_THREADS_TABLE, array('sondage' => 0), 'id = '. $id);
}

/**
 * Return redirection url after save / delete Forum poll.
 *
 * @param void
 * @return string
 */
function getForumPollRedirectUrl() {
    global $forumId, $threadId;

    return 'index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId;
}


opentable();

switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Forum poll form.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Forum poll.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Forum poll.
        nkAction_delete();
        break;

    default :
        break;
}

closetable();

?>