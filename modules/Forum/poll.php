<?php
/**
 * vote.php
 *
 * Frontend of Forum module - Forum poll management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
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
$surveyField = (isset($_GET['survey_field'])) ? (int) $_GET['survey_field'] : null;

nkAction_setParams(array(
    'dataName'                  => 'forumPoll',
    'tableName'                 => FORUM_POLL_TABLE,
    'deleteConfirmation'        => __('CONFIRM_DELETE_POLL'),
    'uriData'                   => array('forum_id' => $forumId, 'thread_id' => $threadId, 'survey_field' => $surveyField),
    'backRedirection'           => 'forumPollRedirect'
));

/* Forum poll edit form function */

/**
 * Callback function for nkAction_init.
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
        printNotification(__('TOPIC_NO_EXIST'), 'error');
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
            printNotification(__('FORUM_NO_EXIST'), 'error');
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
    $maxOptions = ($surveyField === null) ? 2 : max(2, $surveyField);
    $maxOptions = min($maxOptions, $nuked['forum_field_max']);

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
 * @param int $id : The Forum poll id.
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
 * Callback function for nkAction_edit function.
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
 * Callback function for nkAction_edit functions.
 * Additional process after check Forum poll form.
 * Check Forum poll options fields.
 *
 * @param array $data : The valid data issue of form submission.
 * @param int $id : The Forum poll id.
 * @return bool
 */
function postCheckformForumPollValidation($data, $id) {
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
 * Additional process before save Forum poll data.
 *
 * @param int $id : The Forum poll id.
 * @param array $forumPoll : The Forum poll data.
 * @return void
 */
function preSaveForumPollData($id, &$forumPoll) {
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
 * Callback function for nkAction_delete function.
 * Additional process before delete Forum poll data.
 * Delete Forum poll additionnal data.
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
 * Callback function for nkAction_save & nkAction_delete functions.
 * Return redirection url after save / delete Forum poll.
 *
 * @param void
 * @return string
 */
function getForumPollRedirectUrl() {
    global $forumId, $threadId;

    return 'index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId;
}

// Save survey result of viewtopic page.
function addUserVote() {
    global $visiteur, $user, $user_ip, $forumId, $threadId;

    $pollId   = (isset($_GET['id'])) ? (int) $_GET['id'] : 0;
    $optionId = (isset($_POST['voteid'])) ? (int) $_POST['voteid'] : 0;

    if ($optionId > 0) {
        if ($visiteur > 0) {
            $dbrForum = nkDB_selectOne(
                'SELECT level_vote
                FROM '. FORUM_TABLE .'
                WHERE id = '. $forumId
            );

            if ($visiteur >= $dbrForum['level_vote']) {
                $alreadyVote = nkDB_totalNumRows(
                    'FROM '. FORUM_VOTE_TABLE .'
                    WHERE author_id = '. nkDB_escape($user['id']) .'
                    AND poll_id = '. $pollId
                );

                if ($alreadyVote == 0) {
                    $dbu = nkDB_update(FORUM_OPTIONS_TABLE, array(
                            'option_vote' => array('option_vote + 1', 'no-escape')
                        ),
                        'id = '. $optionId .' AND poll_id = '. $pollId
                    );

                    if (! $dbu) {
                        printNotification(__('FORUM_POLL_NO_EXIST'), 'error');
                        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
                        return;
                    }

                    nkDB_insert(FORUM_VOTE_TABLE, array(
                        'poll_id'   => $pollId,
                        'author_id' => $user['id'],
                        'author_ip' => $user_ip
                    ));

                    printNotification(__('VOTE_SUCCES'), 'success');
                }
                else {
                    printNotification(__('ALREADY_VOTE'), 'warning');
                }
            }
            else {
                printNotification(__('BAD_VOTE_LEVEL'), 'error');
            }
        }
        else {
            printNotification(__('ONLY_MEMBERS_VOTE'), 'error');
        }
    }
    else {
        printNotification(__('NO_OPTION'), 'warning');
    }

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
}


opentable();

// Action handle
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

    case 'vote' :
        addUserVote();
        break;

    default :
        break;
}

closetable();

?>