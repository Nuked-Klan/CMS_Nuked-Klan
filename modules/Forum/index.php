<?php
/**
 * index.php
 *
 * Frontend of Forum module
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


// Display main forum page.
function index() {
    require 'modules/Forum/main.php';
}

/* Forum message management */

// Delete joined file of Forum message.
function del_file() {
    global $user;

    $forumId    = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
    $threadId   = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;
    $messId     = (isset($_GET['mess_id'])) ? (int) $_GET['mess_id'] : 0;

    // Get Forum message data
    $dbrForumMessage = nkDB_selectOne(
        'SELECT file, auteur_id
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE id = '. $messId
    );

    // Check if user can delete joined file
    if (! ($user && $dbrForumMessage['auteur_id'] == $user['id'] || isForumAdministrator($forumId))) {
        printNotification(__('ZONE_ADMIN'), 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
        return;
    }

    // Display confirmation
    if (! isset($_POST['confirm'])) {
        echo applyTemplate('confirm', array(
            'url'       => 'index.php?file=Forum&amp;op=del_file&forum_id='. $forumId .'&amp;thread_id='. $threadId .'&amp;mess_id='. $messId,
            'message'   => __('CONFIRM_DELETE_FILE'),
            'fields'    => array(
                'token'     => nkToken_generate('deleteForumJoinedFile'. $forumId . $threadId . $messId)
            ),
        ));
    }
    // User confirmed
    else if ($_POST['confirm'] == __('YES')) {
        // Check delete Forum joined file token
        $tokenValid = nkToken_valid('deleteForumJoinedFile'. $forumId . $threadId . $messId,
            300, array('index.php?file=Forum&op=del_file&forum_id='. $forumId .'&thread_id='. $threadId .'&mess_id='. $messId)
        );

        if (! $tokenValid) {
            printNotification(__('TOKEN_NO_VALID'), 'error');
            redirect('index.php?file=Forum&op=del_file&forum_id='. $forumId .'&thread_id='. $threadId .'&mess_id='. $messId, 2);
            return;
        }

        // Delete joined file if exist
        if (is_file('upload/Forum/'. $dbrForumMessage['file'])) {
            deleteForumMessageFile($dbrForumMessage['file']);

            nkDB_update(FORUM_MESSAGES_TABLE, array('file' => ''), 'id = '. $messId);
            printNotification(__('FILE_DELETED'), 'success');
        }

        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
    }
    // User aborted
    else if ($_POST['confirm'] == __('NO')) {
        printNotification(__('OPERATION_CANCELED'), 'warning');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
    }
}

// Set / unset to send email notification when a user reply at this topic.
function notify() {
    global $user;

    $forumId    = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
    $threadId   = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;
    $do         = (isset($_GET['do'])) ? $_GET['do'] : '';

    // Check if user is logued
    if ($user && $user['id'] != '') {
        if ($do == 'on') {
            $message    = __('NOTIFY_IS_ON');
            $notify     = 1;
        }
        else if ($do == 'off') {
            $message    = __('NOTIFY_IS_OFF');
            $notify     = 0;
        }

        // Update notification status of Forum message
        if (isset($notify)) {
            nkDB_update(FORUM_MESSAGES_TABLE,
                array('emailnotify' => $notify),
                'thread_id = '. $threadId .' AND auteur_id = '. nkDB_quote($user['id'])
            );

            printNotification($message, 'info');
        }
    }
    else
        printNotification(__('ZONE_ADMIN'), 'error');

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
}

/* Forum topic management */

// Delete a Forum topic.
function del_topic() {
    require_once 'Includes/nkToken.php';

    $forumId    = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
    $threadId   = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;

    if (! isForumAdministrator($forumId)) {
        printNotification(__('ZONE_ADMIN'), 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
        return;
    }

    // Display confirmation
    if (! isset($_POST['confirm'])) {
        echo applyTemplate('confirm', array(
            'url'       => 'index.php?file=Forum&amp;op=del_topic&amp;forum_id='. $forumId .'&amp;thread_id='. $threadId,
            'message'   => __('CONFIRM_DELETE_TOPIC'),
            'fields'    => array(
                'token'     => nkToken_generate('deleteForumTopic'. $forumId . $threadId)
            ),
        ));
    }
    // User confirmed
    else if ($_POST['confirm'] == __('YES')) {
        // Check delete Forum topic token
        $tokenValid = nkToken_valid('deleteForumTopic'. $forumId . $threadId,
            300, array('index.php?file=Forum&op=del_topic&forum_id='. $forumId .'&thread_id='. $threadId)
        );

        if (! $tokenValid) {
            printNotification(__('TOKEN_NO_VALID'), 'error');
            redirect('index.php?file=Forum&page=del_topic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
            return;
        }

        // Delete Forum poll data if needed
        $dbrForumThread = nkDB_selectOne(
            'SELECT sondage
            FROM '. FORUM_THREADS_TABLE .'
            WHERE id = '. $threadId
        );

        if ($dbrForumThread && $dbrForumThread['sondage'] == 1) {
            $dbrForumPoll = nkDB_selectOne(
                'SELECT id
                FROM '. FORUM_POLL_TABLE .'
                WHERE thread_id = '. $threadId
            );

            if ($dbrForumPoll) {
                nkDB_delete(FORUM_POLL_TABLE, 'id = '. $dbrForumPoll['id']);
                nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. $dbrForumPoll['id']);
                nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. $dbrForumPoll['id']);
            }
        }

        // Get joined file list of Forum messages
        $dbrForumMessage = nkDB_selectMany(
            'SELECT file
            FROM '. FORUM_MESSAGES_TABLE .'
            WHERE thread_id = '. $threadId
        );

        // Delete joined files
        foreach ($dbrForumMessage as $forumMessage) {
            if ($forumMessage['file'] != '')
                deleteForumMessageFile($forumMessage['file']);
        }

        // Get and update Forum stats
        $dbrForum = nkDB_selectOne(
            'SELECT nbMessages
            FROM '. FORUM_TABLE .'
            WHERE id = '. $forumId
        );

        nkDB_update(FORUM_TABLE, array(
                'nbTopics'      => array('nbTopics - 1', 'no-escape'),
                'nbMessages'    => array('nbMessages - '. $dbrForum['nbMessages'], 'no-escape')
            ),
            'id = '. $forumId
        );

        // Delete Forum topic and messages
        nkDB_delete(FORUM_MESSAGES_TABLE, 'thread_id = '. $threadId);
        nkDB_delete(FORUM_THREADS_TABLE, 'id = '. $threadId);

        printNotification(__('TOPIC_DELETED'), 'success');
        redirect('index.php?file=Forum&page=viewforum&forum_id='. $forumId, 2);
    }
    // User aborted
    else if ($_POST['confirm'] == __('NO')) {
        printNotification(__('OPERATION_CANCELED'), 'warning');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
    }
}

// Move a Forum topic to another Forum.
function move() {
    global $visiteur;

    $forumId  = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
    $threadId = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;

    if (! isForumAdministrator($forumId)) {
        printNotification(__('ZONE_ADMIN'), 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
        return;
    }

    // Display Forum selection to move Forum topic
    if (! isset($_POST['confirm'])) {
        $dbrForum = nkDB_selectMany(
            'SELECT FC.id AS catId, FC.nom AS catName, F.id AS forumId, F.nom AS forumName
            FROM '. FORUM_CAT_TABLE .' AS FC
            INNER JOIN '. FORUM_TABLE .' AS F
            ON F.cat = FC.id
            WHERE '. $visiteur .' >= FC.niveau AND '. $visiteur .' >= F.niveau',
            array('FC.ordre', 'FC.nom', 'F.ordre', 'F.nom')
        );

        $options    = array();
        $currentCat = 0;

        foreach ($dbrForum as $forum) {
            if ($forum['catId'] != $currentCat) {
                if ($currentCat != 0)
                    $options['end-optgroup-'. $forum['catId']] = true;

                $options['start-optgroup-'. $forum['catId']] = '* '. printSecuTags($forum['catName']);
                $currentCat = $forum['catId'];
            }

            $options[$forum['forumId']] = '&nbsp;&nbsp;&nbsp;'. printSecuTags($forum['forumName']);
        }

        echo applyTemplate('modules/Forum/moveThread', array(
            'options'   => $options,
            'forumId'   => $forumId,
            'threadId'  => $threadId,
            'token'     => nkToken_generate('moveForumTopic'. $forumId . $threadId)
        ));
    }
    else if ($_POST['confirm'] == __('YES') && $_POST['newforum'] != '') {
        $newForumId = (int) $_POST['newforum'];

        // Check delete Forum topic token
        $tokenValid = nkToken_valid('moveForumTopic'. $forumId . $threadId,
            300, array('index.php?file=Forum&op=move&forum_id='. $forumId .'&thread_id='. $threadId)
        );

        if (! $tokenValid) {
            printNotification(__('TOKEN_NO_VALID'), 'error');
            redirect('index.php?file=Forum&page=move&forum_id='. $forumId .'&thread_id='. $threadId, 2);
            return;
        }

        // Move Forum topic and message to new forum
        nkDB_update(FORUM_THREADS_TABLE, array('forum_id' => $newForumId), 'id = '. $threadId);
        nkDB_update(FORUM_MESSAGES_TABLE, array('forum_id' => $newForumId), 'thread_id = '. $threadId);

        // Get and update Forum stats
        $dbrForum = nkDB_selectOne(
            'SELECT nbMessages
            FROM '. FORUM_TABLE .'
            WHERE id = '. $forumId
        );

        nkDB_update(FORUM_TABLE, array(
                'nbTopics'      => array('nbTopics - 1', 'no-escape'),
                'nbMessages'    => array('nbMessages - '. $dbrForum['nbMessages'], 'no-escape')
            ),
            'id = '. $forumId
        );

        nkDB_update(FORUM_TABLE, array(
                'nbTopics'      => array('nbTopics + 1', 'no-escape'),
                'nbMessages'    => array('nbMessages + '. $dbrForum['nbMessages'], 'no-escape')
            ),
            'id = '. $newForumId
        );

        $dbrForumRead = nkDB_selectMany(
            'SELECT thread_id, forum_id, user_id
            FROM '. FORUM_READ_TABLE .'
            WHERE forum_id LIKE \'%,'. $forumId .',%\'
            OR forum_id LIKE \'%,'. $newForumId .',%\''
        );

        // Liste des utilisateurs
        $userTMP = array();

        foreach ($dbrForumRead as $forumRead) {
            $userTMP[$forumRead['user_id']] = array(
                'forum_id'  => $forumRead['forum_id'],
                'thread_id' => $forumRead['thread_id']
            );
        }

        // Vieux forum
        $oldTMP = array();

        // Liste des threads de l'ancien forum
        $dbrForumThread = nkDB_selectMany(
            'SELECT id
            FROM '. FORUM_THREADS_TABLE .'
            WHERE forum_id = '. $forumId
        );

        // On vérifie que tous les threads sont lus
        foreach ($dbrForumThread as $forumThread)
            $oldTMP[$forumThread['id']] = $forumThread['id'];

        // Nouveau forum
        $newTMP = array();

        // Liste des threads du nouveau forum
        $dbrForumThread = nkDB_selectMany(
            'SELECT id
            FROM '. FORUM_THREADS_TABLE .'
            WHERE forum_id = '. $newForumId
        );

        // On vérifie que tous les threads sont lus
        foreach ($dbrForumThread as $forumThread)
            $newTMP[$forumThread['id']] = $forumThread['id'];

        $update = array();

        // On boucle les users
        foreach ($userTMP as $key => $member) {
            // On part du fait que tout les posts sont lu
            $read = true;

            foreach ($oldTMP as $old) {
                // Si au moins un post n'est pas lu
                if (strrpos($member['thread_id'], ','. $old .',') === false)
                    $read = false;
            }

            // Si ils sont tous lu, et que le forum est pas dans la liste on le rajoute
            if ($read === true && strrpos($member['forum_id'], ','. $forumId .',') === false) {
                // Nouvelle liste des forums
                $fid = $member['forum_id'] . $forumId .',';

                // Si aucun update n'a eu lieu avant
                $update[] = '(\''. $fid .'\', \''. $key .'\')';
            }

            // On part du fait que tout les posts sont lu
            $read = true;

            foreach($newTMP as $new){
                // Si au moins un post n'est pas lu
                if (strrpos($member['thread_id'], ','. $new .',') === false)
                    $read = false;
            }

            // Si tout n'est pas lu, et que le forum est présent dans la liste on le retire
            if ($read === false && strrpos($fid, ','. $newForumId .',') !== false) {
                // Nouvelle liste des forums
                $fid = str_replace(','. $newForumId .',', ',', $fid);

                // Si aucun n'update n'a eu lieu avant
                $update[] = '(\''. $fid .'\', \''. $key .'\')';
            }

        }

        if (! empty($update)) {
            nkDB_execute(
                'INSERT INTO `'. FORUM_READ_TABLE .'`
                (forum_id, user_id)
                VALUES '. implode(', ', $update) .'
                ON DUPLICATE KEY UPDATE forum_id = VALUES(forum_id);'
            );
        }

        printNotification(__('TOPIC_MOVED'), 'success');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $newForumId .'&thread_id='. $threadId, 2);
    }
    else if ($_POST['confirm'] == __('NO')) {
        printNotification(__('OPERATION_CANCELED'), 'warning');

        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
    }
}

// Lock / unlock a Forum topic.
function lock() {
    $forumId  = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
    $threadId = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;

    // Check if user can lock Forum topic
    if (isForumAdministrator($forumId)) {
        if ($_GET['do'] == 'close') {
            $message    = __('TOPIC_LOCKED');
            $closed     = 1;
        }
        else if ($_GET['do'] == 'open') {
            $message    = __('TOPIC_UNLOCKED');
            $closed     = 0;
        }

        // Update lock status of Forum topic
        if (isset($closed)) {
            nkDB_update(FORUM_THREADS_TABLE, array('closed' => $closed), 'id = '. $threadId);

            printNotification($message, 'success');
        }
    }
    else
        printNotification(__('ZONE_ADMIN'), 'error');

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
}

// Set / unset announce status of Forum topic.
function announce() {
    $forumId  = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
    $threadId = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;

    // Check if user can do a announce
    if (isForumAdministrator($forumId)) {
        if ($_GET['do'] == 'up')
            $announce = 1;
        else if ($_GET['do'] == 'down')
            $announce = 0;

        // Update announce status of Forum topic
        if (isset($announce)) {
            nkDB_update(FORUM_THREADS_TABLE, array('annonce' => $announce), 'id = '. $threadId);

            printNotification(__('TOPIC_MODIFIED'), 'success');
        }
    }
    else
        printNotification(__('ZONE_ADMIN'), 'error');

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
}

// Mark all posts as read of all Forum or a Forum
function mark() {
    global $user, $cookie_forum;

    $forumId = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;

    // Update Forum read data only for logued user
    if ($user) {
        /*if ($forumId > 0) {
            $forumReadIds = $newForumReadIds = array();

            if (isset($_COOKIE[$cookie_forum]) && $_COOKIE[$cookie_forum] != '') {
                if (preg_match('`[^0-9,]`i', $_COOKIE[$cookie_forum]))
                    $_COOKIE[$cookie_forum] = '';
                else
                    $forumReadIds = explode(',', $_COOKIE[$cookie_forum]);
            }

            $dbrForumMessage = nkDB_selectMany(
                'SELECT MAX(id)
                FROM '. FORUM_MESSAGES_TABLE .'
                WHERE forum_id = '. $forumId .' AND date > '. $user['lastUsed'] .'
                GROUP BY thread_id'
            );

            foreach ($dbrForumMessage as $forumMessage) {
                if (! in_array($forumMessage['MAX(id)'], $forumReadIds))
                    $newForumReadIds[] = $forumMessage['MAX(id)'];
            }

            if ($_COOKIE[$cookie_forum] != '' && ! empty($newForumReadIds)) $_COOKIE[$cookie_forum] .= ',';

            setcookie($cookie_forum, $_COOKIE[$cookie_forum] . implode(',', $newForumReadIds));
        }
        else {
            setcookie($cookie_forum, '');

            nkDB_update(SESSIONS_TABLE, array('last_used' => array('date', 'no-escape')), 'user_id = '. nkDB_quote($user['id']));
        }*/

        // On veut modifier la chaine thread_id et forum_id
        $dbrForumRead = nkDB_selectOne(
            'SELECT thread_id, forum_id
            FROM '. FORUM_READ_TABLE .'
            WHERE user_id = '. nkDB_quote($user['id'])
        );

        $sql = 'SELECT id, forum_id
            FROM '. FORUM_THREADS_TABLE;

        if ($forumId > 0) $sql .= 'WHERE forum_id = '. $forumId;

        $dbrForumThread = nkDB_selectMany($sql);

        if (nkDB_numRows() > 0) {
            $tid = ','. substr($dbrForumRead['thread_id'], 1);
            $fid = ','. substr($dbrForumRead['forum_id'], 1);

            foreach ($dbrForumThread as $forumThread) {
                if (strrpos($tid, ','. $forumThread['id'] .',') === false)
                    $tid .= $forumThread['id'] .',';

                if (strrpos($fid, ','. $forumThread['forum_id'] .',') === false)
                    $fid .= $forumThread['forum_id'] .',';
            }

            nkDB_replace(FORUM_READ_TABLE, array(
                'user_id'   => $user['id'],
                'thread_id' => $tid,
                'forum_id'  => $fid
            ));
        }
    }

    printNotification(__('MESSAGES_MARK'), 'success');
    redirect('index.php?file=Forum', 2);
}


opentable();

// Action handle
switch ($GLOBALS['op']) {
    case 'index' :
        index();
        break;

    case 'del_topic' :
        del_topic();
        break;

    case 'move' :
        move();
        break;

    case 'lock' :
        lock();
        break;

    case 'announce' :
        announce();
        break;

    case 'mark' :
        mark();
        break;

    case 'del_file' :
        del_file();
        break;

    case 'notify' :
        notify();
        break;

    default :
        index();
        break;
}

closetable();

?>