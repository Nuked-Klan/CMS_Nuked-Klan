<?php
/**
 * prune.php
 *
 * Backend of Forum module - Forum prune management
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Forum'))
    return;


// Display Forum prune selection.
function prune() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/backend/config/prune.php';
    require 'modules/Forum/config/selectForumOptions.php';

    nkTemplate_addJS(
'$("#pruneForumForm").submit(function() {
    if (document.getElementById("pfPruneDay").value.length == 0) {
        alert("'. __('NO_DAY') .'");
        return false;
    }
    return true;
});', 'jqueryDomReady');

    $pruneForumForm = getPruneForumFormCfg();

    $pruneForumForm['items']['prune_id']['options'] = getForumOptions();

    echo applyTemplate('contentBox', array(
        'title'     => __('ADMIN_FORUM') .' - '. __('PRUNE'),
        'helpFile'  => 'Forum',
        'content'   => getMenuOfModuleAdmin() . nkForm_generate($pruneForumForm)
    ));
}

// Execute Forum prune.
function doPrune() {
    if ($_POST['prune_day'] == '' || ! ctype_digit($_POST['prune_day'])) {
        printNotification(__('INCORRECT_PRUNE_DAY'), 'error');
        redirect('index.php?admin=Forum&page=prune', 2);
        return;
    }

    $prunedate = time() - (86400 * $_POST['prune_day']);

    if (strpos($_POST['prune_id'], 'cat_') === 0) {
        $cat = str_replace('cat_', '', $_POST['prune_id']);
        $and = 'AND cat = '. nkDB_quote($cat);

        $dbrForumCat = nkDB_selectMany(
            'SELECT nom
            FROM '. FORUM_CAT_TABLE .'
            WHERE id = '. nkDB_quote($cat)
        );

        $name = $dbrForumCat['nom'];
    }
    else if ($_POST['prune_id'] != '') {
        $and = 'AND forum_id = '. nkDB_quote($_POST['prune_id']);

        $dbrForum = nkDB_selectMany(
            'SELECT nom
            FROM '. FORUM_TABLE .'
            WHERE id = '. nkDB_quote($_POST['prune_id'])
        );

        $name = $dbrForum['nom'];
    }
    else {
        $and    = '';
        $name   = __('ALL');
    }

    $dbrForumThreads = nkDB_selectMany(
        'SELECT id, sondage
        FROM '. FORUM_THREADS_TABLE .'
        WHERE '. $prunedate .' >= last_post AND annonce = 0 '. $and
    );

    foreach ($dbrForumThreads as $forumThreads) {
        if ($forumThreads['sondage'] == 1) {
            $dbrForumPoll = nkDB_selectMany(
                'SELECT id
                FROM '. FORUM_POLL_TABLE .'
                WHERE thread_id = '. nkDB_quote($forumThreads['id'])
            );

            nkDB_delete(FORUM_POLL_TABLE, 'id = '. nkDB_quote($dbrForumPoll['id']));
            nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. nkDB_quote($dbrForumPoll['id']));
            nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. nkDB_quote($dbrForumPoll['id']));
        }

        nkDB_delete(FORUM_MESSAGES_TABLE, 'thread_id = '. nkDB_quote($forumThreads['id']));
        nkDB_delete(FORUM_THREADS_TABLE, 'id = '. nkDB_quote($forumThreads['id']));
    }

    saveUserAction(__('ACTION_PRUNE_FORUM') .': '. $name);

    printNotification(__('FORUM_PRUNE'), 'success');
    redirect('index.php?admin=Forum', 2);
}


// Action handle
switch ($GLOBALS['op']) {
    case 'do' :
        doPrune();
        break;

    default:
        prune();
        break;
}

?>
