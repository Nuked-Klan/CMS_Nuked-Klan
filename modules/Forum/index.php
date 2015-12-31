<?php
/**
 * index.php
 *
 * Frontend of Forum module
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
require_once 'Includes/nkUpload.php';
require_once 'modules/Forum/core.php';


/* Internal function */

/**
 * Check if user is a Forum administrator / moderator.
 *
 * @param int $forumId : The forum ID.
 * @return bool : Return true if user have Forum right, false also.
 */
function isForumAdministrator($forumId) {
    global $user, $visiteur;

    $dbrForum = nkDB_selectOne(
        'SELECT moderateurs
        FROM '. FORUM_TABLE .'
        WHERE '. $visiteur .' >= level AND id = '. nkDB_escape($forumId)
    );

    return $dbrForum &&
        ($visiteur >= admin_mod('Forum') || isModerator($dbrForum['moderateurs']));
}

/**
 * Check if Forum Thread is a poll.
 *
 * @param int $threadId : The forum thread ID.
 * @return int : Return 1 if it's a poll, 0 also.
 */
function getThreadPollStatus($threadId) {
    $dbrForumThread = nkDB_selectOne(
        'SELECT sondage
        FROM '. FORUM_THREADS_TABLE .'
        WHERE id = '. $threadId
    );

    return (int) $dbrForumThread['sondage'];
}

/**
 * Get poll ID of Forum Thread.
 *
 * @param int $threadId : The forum thread ID.
 * @return int : The poll ID.
 */
function getThreadPollId($threadId) {
    $dbrForumPoll = nkDB_selectOne(
        'SELECT id
        FROM '. FORUM_POLL_TABLE .'
        WHERE thread_id = '. $threadId
    );

    return (int) $dbrForumPoll['id'];
}

/**
 * Delete joined file of Forum message.
 *
 * @param string $filename : The basename of joined file.
 * @return void
 */
function deleteForumMessageFile($filename) {
    $path = 'upload/Forum/'. $filename;

    if (is_file($path)) {
        @chmod($path, 0775);
        @unlink($path);
    }
}

/**
 * Check if the user has the right to access survey.
 *
 * @param int $forumId : The forum ID.
 * @param int $threadId : The forum thread ID.
 * @param int $pollId : The forum poll ID. Default value is 0 for new poll.
 * @return mixed : Return true if user have access to survey or return error message.
 */
function checkForumPollAccess($forumId, $threadId, $pollId = 0) {
    global $visiteur, $user;

    $fields = ($pollId == 0) ? ', sondage' : '';

    $dbrForumThread = nkDB_selectOne(
        'SELECT auteur_id'. $fields .'
        FROM '. FORUM_THREADS_TABLE .'
        WHERE id = '. nkDB_escape($threadId)
    );

    if (! $dbrForumThread) return _NOTOPICEXIST;

    // Get poll access
    $pollAuthorAccess = $user && $user['id'] == $dbrForumThread['auteur_id'];

    if ($pollId == 0) {
        // Check Forum level poll
        $dbrForum = nkDB_selectOne(
            'SELECT level_poll
            FROM '. FORUM_TABLE .'
            WHERE id = '. $forumId
        );

        if (! $dbrForum) return _NOFORUMEXIST;

        $access = $pollAuthorAccess && $dbrForumThread['sondage'] == 1
            && $visiteur >= $dbrForum['level_poll'];
    }
    else
        $access = $pollAuthorAccess || isForumAdministrator($forumId);

    if ($access) return true;

    return __('ZONE_ADMIN');
}

/**
 * Update forum read table.
 *
 * @param int $forumId : The forum ID.
 * @param int $threadId : The forum thread ID.
 * @return void
 */
function updateForumReadTable($forumId, $threadId) {
    $dbrForumRead = nkDB_selectMany(
        'SELECT thread_id, forum_id, user_id
        FROM '. FORUM_READ_TABLE .'
        WHERE thread_id LIKE \'%,'. nkDB_escape($threadId, true) .',%\'
        OR forum_id LIKE \'%,'. nkDB_escape($forumId, true) .',%\''
    );

    $update = array();

    foreach ($dbrForumRead as $forumRead) {
        $tid = $forumRead['thread_id'];
        $fid = $forumRead['forum_id'];

        if (strrpos($fid, ','. $forumId .',') !== false)
            $fid = str_replace(','. $forumId .',', ',', $fid);

        if (strrpos($tid, ','. $threadId .',') !== false)
            $tid = str_replace(','. $threadId .',', ',', $tid);

        $update[] = '(\''. $fid .'\', \''. $tid .'\', \''. $forumRead['user_id'] .'\')';
    }

    if (!empty($update)) {
        nkDB_execute(
            'INSERT INTO `'. FORUM_READ_TABLE .'`
            (forum_id, thread_id, user_id) VALUES '. implode(', ', $update) .'
            ON DUPLICATE KEY UPDATE forum_id=VALUES(forum_id), thread_id=VALUES(thread_id);'
        );
    }
}

/**
 * Check if user can post a new message.
 *
 * @param string $username : The user name.
 * @return bool : Return true id user can post again, false also.
 */
function checkForumPostFlood($username) {
    global $nuked, $visiteur, $user_ip;
return false;
    $dbrForumMessage = nkDB_selectOne(
        'SELECT date
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE auteur = '. nkDB_escape($username) .' OR auteur_ip = '. nkDB_escape($user_ip),
        array('date'), 'DESC', 1
    );

    return ! (time() < $dbrForumMessage['date'] + $nuked['post_flood']
        && $visiteur < admin_mod('Forum'));
}

/**
 * Count Poll field option filled and return result.
 *
 * @param void
 * @return int : The number of Poll field option filled.
 */
function getNbFilledForumPollOption() {
    $nbFilledOption = 0;

    if (isset($_POST['option']) && is_array($_POST['option'])) {
        $nbFilledOption = count(array_filter(array_map('trim', $_POST['option'])));

        if (isset($_POST['newOption']) && ! ctype_space($_POST['newOption']) && $_POST['newOption'] != '')
            $nbFilledOption++;
    }

    return $nbFilledOption;
}

/**
 * Add new poll option in database.
 *
 * @param int $pollId : The forum poll ID.
 * @param int $id : The forum poll option ID.
 * @return void
 */
function addPollOption($pollId, $id) {
    if ($_POST['option'][$id] != '') {
        nkDB_insert(FORUM_OPTIONS_TABLE, array(
            'id'            => $id,
            'poll_id'       => $pollId,
            'option_text'   => stripslashes($_POST['option'][$id]),
            'option_vote'   => 0
        ));
    }
}

/**
 * Edit poll option in database.
 *
 * @param int $pollId : The forum poll ID.
 * @param int $id : The forum poll option ID.
 * @return void
 */
function updatePollOption($pollId, $id) {
    if ($_POST['option'][$id] != '') {
        nkDB_update(FORUM_OPTIONS_TABLE, array(
                'option_text' => stripslashes($_POST['option'][$id])
            ),
            'poll_id = '. $pollId .' AND id = '. $id
        );
    }
    else {
        nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. $pollId .' AND id = '. $id);
    }
}

/**
 * Check Forum post author data.
 *
 * @param void
 * @return array : A numerical indexed array with author ID, author name
 *         and error of username check.
 */
function checkForumPostAuthor() {
    global $user;

    if ($user)
        return array($user['id'], $user['name'], false);

    $author = stripslashes($_POST['author']);
    $author = nkHtmlEntities($author, ENT_QUOTES);
    $author = checkNickname($author);

    return array('', $author, getCheckNicknameError($author));
}

/**
 * Check Forum post joined file.
 *
 * @param void
 * @return array : A numerical indexed array with filename(without path)
 *         and error of upload file check.
 */
function checkForumPostJoinedFile() {
    global $visiteur, $nuked;

    if ($visiteur >= $nuked['forum_file_level']
        && $nuked['forum_file'] == 'on'
        && $_FILES['fichiernom']['name'] != ''
    ) {
        list($filename, $uploadError) = nkUpload_check(
            'fichiernom', 'no-html-php', 'upload/Forum', $nuked['forum_file_maxsize'], true
        );

        if ($uploadError !== false)
            return array('', $uploadError);

        return array(basename($filename), false);
    }

    return array('', false);
}

/**
 * Prepare data of Forum post.
 *
 * @param void
 * @return array : A associative array filled with :
 *         - Keys of Forum message SQL table
 *         - Values of prepared Forum post data.
 */
function prepareForumPostData() {
    $data = array(
        'titre'         => stripslashes($_POST['titre']),
        'usersig'       => 0,
        'emailnotify'   => 0
    );

    $data['txt'] = secu_html(nkHtmlEntityDecode($_POST['texte']));
    $data['txt'] = icon($data['txt']);
    $data['txt'] = stripslashes($data['txt']);
    $data['txt'] = str_replace('<blockquote>', '<blockquote class="nkForumBlockQuote">', $data['txt']);

    if (isset($_POST['usersig']) && is_numeric($_POST['usersig']))
        $data['usersig'] = $_POST['usersig'];

    if (isset($_POST['emailnotify']) && is_numeric($_POST['emailnotify']))
        $data['emailnotify'] = $_POST['emailnotify'];

    return $data;
}


// Display main forum page.
function index() {
    require 'modules/Forum/main.php';
}

/* Forum message management */

// Send a new Forum message.
function post() {
    global $user, $nuked, $user_ip, $visiteur;

    require_once 'Includes/nkToken.php';
    require_once 'Includes/nkUpload.php';

    $forumId = (isset($_POST['forum_id'])) ? (int) $_POST['forum_id'] : 0;
    $error   = false;
    $referer = 'index.php?file=Forum&page=post&forum_id='. $forumId;

    // Check captcha
    if (initCaptcha() && ! validCaptchaCode())
        return;

    // Ckeck Forum post token
    if (! nkToken_valid('addForumPost'. $forumId, 300, array($referer))) {
        printNotification(_TOKEN_INVALID, 'error');
        redirect($referer, 2);
        return;
    }

    // Check if Forum exist and Forum posting level
    $dbrForum = nkDB_selectOne(
        'SELECT niveau, level, level_poll
        FROM '. FORUM_TABLE .'
        WHERE id = '. $forumId
    );

    if (! $dbrForum) $error = _NOFORUMEXIST;
    if ($dbrForum && $visiteur < $dbrForum['level']) $error = __('ZONE_ADMIN');

    if ($error !== false) {
        printNotification($error, 'error');

        if (! $dbrForum || $visiteur < $dbrForum['niveau'])
            redirect('index.php?file=Forum', 2);
        else
            redirect('index.php?file=Forum&page=viewforum&forum_id='. $forumId, 2);

        return;
    }

    // Check post fields are really filled
    if ($_POST['author'] != '' && ! ctype_space($_POST['author'])
        && $_POST['titre'] != '' && ! ctype_space($_POST['titre'])
        && $_POST['texte'] != '' && ! ctype_space($_POST['texte'])
    ) {
        // Check author data
        list($authorId, $author, $error) = checkForumPostAuthor();

        if ($error === false) {
            // Post flood checking
            if (! checkForumPostFlood($author)) {
                // Check joined file
                list($filename, $error) = checkForumPostJoinedFile();
            }
            else {
                $error = _NOFLOOD;
            }
        }
    }
    else {
        $error = _FIELDEMPTY;
    }

    if ($error !== false) {
        printNotification($error, 'error');
        redirect($referer, 2);
        return;
    }

    // Prepare Forum post data
    $postData   = prepareForumPostData();
    $date       = time();

    if (isset($_POST['annonce'])
        && is_numeric($_POST['annonce'])
        && $visiteur >= admin_mod('Forum')
    )
        $announcement = 0;
    else
        $announcement = 1;

    if (isset($_POST['survey'])
        && $_POST['survey'] == 1
        && $visiteur >= $dbrForum['level_poll']
    ) {
        $surveyField    = (isset($_POST['survey_field'])) ? (int) $_POST['survey_field'] : 0;
        $sondage        = ($surveyField > 1) ? 1 : 0;
    }
    else
        $sondage = 0;

    // Add new topic
    nkDB_insert(FORUM_THREADS_TABLE, array(
        'titre'     => $postData['titre'],
        'date'      => $date,
        'closed'    => 0,
        'auteur'    => $author,
        'auteur_id' => $authorId,
        'forum_id'  => $forumId,
        'last_post' => $date,
        'view'      => 0,
        'annonce'   => $announcement,
        'sondage'   => $sondage
    ));

    $threadId = nkDB_insertId();

    // Add Forum post in new Forum topic
    nkDB_insert(FORUM_MESSAGES_TABLE, array_merge($postData, array(
        'date'          => $date,
        'edition'       => '',
        'auteur'        => $author,
        'auteur_id'     => $authorId,
        'auteur_ip'     => $user_ip,
        'thread_id'     => $threadId,
        'forum_id'      => $forumId,
        'file'          => $filename
    )));

    // Update Forum stats
    nkDB_update(FORUM_TABLE, array(
            'nbTopics'      => array('nbTopics + 1', 'no-escape'),
            'nbMessages'    => array('nbMessages + 1', 'no-escape')
        ),
        'id = '. $forumId
    );

    updateForumReadTable($forumId, $threadId);

    // Update user Forum post stats
    if ($user)
        nkDB_update(USER_TABLE, array('count' => array('count + 1', 'no-escape')), 'id = '. nkDB_escape($user['id']));

    // Redirect to Forum poll form or to Forum topic
    if ($sondage == 1)
        $url = 'index.php?file=Forum&op=editPoll&survey_field='. $surveyField .'&forum_id='. $forumId .'&thread_id='. $threadId;
    else
        $url = 'index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId;

    printNotification(_MESSAGESEND, 'success');
    redirect($url, 2);
}

// Save a edited Forum message.
function edit() {
    global $user, $nuked;

    require_once 'Includes/nkToken.php';
    require_once 'Includes/nkUpload.php';

    $forumId    = (isset($_POST['forum_id'])) ? (int) $_POST['forum_id'] : 0;
    $threadId   = (isset($_POST['thread_id'])) ? (int) $_POST['thread_id'] : 0;
    $messId     = (isset($_POST['mess_id'])) ? (int) $_POST['mess_id'] : 0;
    $error      = false;

    $referer = 'index.php?file=Forum&page=post&forum_id='. $forumId
        . '&thread_id='. $threadId .'&mess_id='. $messId .'&do=edit';

    // Check Forum post token
    $tokenValid = nkToken_valid('editForumPost'. $forumId . $threadId . $messId,
        300, array($referer)
    );

    if (! $tokenValid) {
        printNotification(_TOKEN_INVALID, 'error');
        redirect($referer, 2);
        return;
    }

    // Check if Forum message exist and if user can edit it
    $dbrCurrentForumMsg = nkDB_selectOne(
        'SELECT auteur_id
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE id = '. $messId
    );

    if (! $dbrCurrentForumMsg || ! isForumAdministrator($forumId)
        || ! ($user && $dbrCurrentForumMsg['auteur_id'] == $user['name'])
    ) {
        printNotification(__('ZONE_ADMIN'), 'error');
        redirect('index.php?file=Forum', 2);
    }

    // Check post fields are really filled
    if ($_POST['titre'] == '' || @ctype_space($_POST['titre'])
        || $_POST['texte'] == '' || @ctype_space($_POST['texte'])
    ) {
        printNotification(_FIELDEMPTY, 'error');
        redirect($referer, 2);
        return;
    }

    // Prepare Forum post data
    $postData = prepareForumPostData();

    if ($_POST['edit_text'] == 1)
        $postData['edition'] = _EDITBY .'&nbsp;'. $user['name'] .'&nbsp;'. _THE .'&nbsp;'. nkDate(time());

    // Get first Forum message ID of topic and check if Forum topic exist
    $dbrFirstForumMsg = nkDB_selectOne(
        'SELECT id
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE thread_id = '. $threadId,
        array('id'), 'ASC', 1
    );

    if (! $dbrFirstForumMsg) {
        printNotification(_NOTOPICEXIST, 'error');
        redirect($referer, 2);
        return;
    }

    // Update Forum message
    nkDB_update(FORUM_MESSAGES_TABLE, $postData, 'id = '. $messId);

    // Update Forum topic title for first Forum message of topic
    if ($dbrFirstForumMsg['id'] == $messId)
        nkDB_update(FORUM_THREADS_TABLE, array('titre' => $postData['titre']), 'id = '. $threadId);

    list($url) = getForumMessageUrl($forumId, $threadId, $messId);

    printNotification(_MESSMODIF, 'success');
    redirect($url, 2);
}

// Save a Forum topic reply.
function reply() {
    global $user, $nuked, $visiteur, $user_ip;

    require_once 'Includes/nkToken.php';
    require_once 'Includes/nkUpload.php';

    $forumId    = (isset($_POST['forum_id'])) ? (int) $_POST['forum_id'] : 0;
    $threadId   = (isset($_POST['thread_id'])) ? (int) $_POST['thread_id'] : 0;
    $error      = false;
    // TODO : quote ?
    $referer    = 'index.php?file=Forum&page=post&forum_id='. $forumId .'&thread_id='. $threadId;

    // Check captcha
    if (initCaptcha() && ! validCaptchaCode())
        return;

    // Check Forum post reply token
    $tokenValid = nkToken_valid('replyForumPost'. $forumId . $threadId,
        300, array($referer)
    );

    if (! $tokenValid) {
        printNotification(_TOKEN_INVALID, 'error');
        // TODO : Only for reply, no quote :/
        redirect($referer, 2);
        return;
    }

    // Check if Forum topic is closed and Forum posting level
    $dbrForum = nkDB_selectOne(
        'SELECT F.level, FT.closed, FT.nbReplies
        FROM '. FORUM_TABLE .' AS F
        INNER JOIN '. FORUM_THREADS_TABLE .' AS FT
        ON FT.id = FT.forum_id
        WHERE FT.id = '. $threadId
    );

    if (! $dbrForum) {
        printNotification(_NOFORUMEXIST, 'error');
        redirect('index.php?file=Forum', 2);
        return;
    }

    if ($dbrForum && ! isForumAdministrator($forumId)
        && ($dbrForum['closed'] == 1 || $dbrForum['level'] > $visiteur)
    ) {
        printNotification(__('ZONE_ADMIN'), 'error');
        redirect($referer, 2);
        return;
    }

    // Check post fields are really filled
    if ($_POST['author'] != '' && ! ctype_space($_POST['author'])
        && $_POST['titre'] != '' && ! ctype_space($_POST['titre'])
        && $_POST['texte'] != '' && ! ctype_space($_POST['texte'])
    ) {
        // Check author data
        list($authorId, $author, $error) = checkForumPostAuthor();

        if ($error === false) {
            // Post flood checking
            if (! checkForumPostFlood($author)) {
                // Check joined file
                list($filename, $error) = checkForumPostJoinedFile();
            }
            else {
                $error = _NOFLOOD;
            }
        }
    }
    else {
        $error = _FIELDEMPTY;
    }

    if ($error !== false) {
        printNotification($error, 'error');
        redirect($referer, 2);
        return;
    }

    // Prepare Forum post data
    $postData   = prepareForumPostData();
    $date       = time();

    // Update last poste date of Forum topic
    nkDB_update(FORUM_THREADS_TABLE, array('last_post' => $date), 'id = '. $threadId);

    updateForumReadTable($forumId, $threadId);

    // Save Forum post reply
    nkDB_insert(FORUM_MESSAGES_TABLE, array_merge($postData, array(
        'date'          => $date,
        'edition'       => '',
        'auteur'        => $author,
        'auteur_id'     => $authorId,
        'auteur_ip'     => $user_ip,
        'thread_id'     => $threadId,
        'forum_id'      => $forumId,
        'file'          => $filename
    )));

    $messId = nkDB_insertId();

    // Update Forum and Forum topic stats
    nkDB_update(FORUM_TABLE, array(
            'nbMessages' => array('nbMessages + 1', 'no-escape')
        ),
        'id = '. $forumId
    );

    nkDB_update(FORUM_THREADS_TABLE, array(
            'nbReplies' => array('nbReplies + 1', 'no-escape')
        ),
        'id = '. $threadId
    );

    // Get list of Forum message author ID to notify
    $dbrForumMessage = nkDB_selectMany(
        'SELECT FM.auteur_id, U.mail
        FROM '. FORUM_MESSAGES_TABLE .' AS FM
        LEFT JOIN '. USER_TABLE .' AS U
        ON U.id = FM.auteur_id
        WHERE FM.thread_id = '. $threadId .'
        AND FM.emailnotify = 1
        AND FM.auteur_id != '. nkDB_escape($authorId) .'
        GROUP BY FM.auteur_id'
    );

    if (nkDB_numRows() > 0) {
        $subject    = _MESSAGE .' : '. $postData['titre'];
        $corps      = _EMAILNOTIFYMAIL ."\r\n"
                    . $nuked['url'] .'/index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId ."\r\n\r\n\r\n"
                    . $nuked['name'] .' - '. $nuked['slogan'];
        $from       = 'From: '. $nuked['name'] .' <'. $nuked['mail'] .'>' ."\r\n"
                    . 'Reply-To: '. $nuked['mail'];

        $subject    = @nkHtmlEntityDecode($subject);
        $corps      = @nkHtmlEntityDecode($corps);
        $from       = @nkHtmlEntityDecode($from);

        foreach ($dbrForumMessage as $forumMessage)
            mail($dbrForumMessage['mail'], $subject, $corps, $from);
    }

    // Update user Forum post stats
    if ($user)
        nkDB_update(USER_TABLE, array('count' =>array('count + 1', 'no-escape')), 'id = '. nkDB_escape($user['id']));

    list($url) = getForumMessageUrl($forumId, $threadId, $messId, $dbrForum['nbReplies'] + 2);

    printNotification(_MESSAGESEND, 'success');
    redirect($url, 2);
}

// Delete a Forum message.
function del() {
    require_once 'Includes/nkToken.php';

    $forumId    = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
    $threadId   = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;
    $messId     = (isset($_GET['mess_id'])) ? (int) $_GET['mess_id'] : 0;

    // Check if user can delete Forum message
    if (! isForumAdministrator($forumId)) {
        printNotification(__('ZONE_ADMIN'), 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
        return;
    }

    // Display confirmation
    if (! isset($_POST['confirm'])) {
        echo applyTemplate('confirm', array(
            'url'       => 'index.php?file=Forum&amp;op=del&forum_id='. $forumId .'&amp;thread_id='. $threadId .'&amp;mess_id='. $messId,
            'message'   => _CONFIRMDELMESS,
            'fields'    => array(
                'token'     => nkToken_generate('deleteForumPost'. $forumId . $threadId . $messId)
            ),
        ));
    }
    // User confirmed
    else if ($_POST['confirm'] == __('YES')) {
        // Check delete Forum post token
        $tokenValid = nkToken_valid('deleteForumPost'. $forumId . $threadId . $messId,
            300, array('index.php?file=Forum&op=del&forum_id='. $forumId .'&thread_id='. $threadId .'&mess_id='. $messId)
        );

        if (! $tokenValid) {
            printNotification(_TOKEN_INVALID, 'error');
            redirect('index.php?file=Forum&amp;op=del&forum_id='. $forumId .'&thread_id='. $threadId .'&mess_id='. $messId, 2);
            return;
        }

        // TODO : See the code if concept is correct
        //  If first message is deleted, keep files of thread messages?
        $dbrFirstForumMsg = nkDB_selectOne(
            'SELECT id, file
            FROM '. FORUM_MESSAGES_TABLE .'
            WHERE thread_id = '. $threadId,
            array('id'), 'ASC', 1
        );

        // Remove file of first Forum message of topic
        if ($dbrFirstForumMsg['file'] != '')
            deleteForumMessageFile($dbrFirstForumMsg['file']);

        $url = 'index.php?file=Forum&page=viewforum&forum_id='. $forumId;

        // Check first Forum message of topic
        if ($dbrFirstForumMsg['id'] == $messId) {
            // Get Forum topic data
            $dbrForumTopic = nkDB_selectOne(
                'SELECT nbReplies, sondage
                FROM '. FORUM_THREADS_TABLE .'
                WHERE id = '. $threadId
            );

            // Delete Forum poll data if needed
            if ($dbrForumTopic['sondage'] == 1) {
                $pollId = getThreadPollId($threadId);

                nkDB_delete(FORUM_POLL_TABLE, 'id = '. $pollId);
                nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. $pollId);
                nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. $pollId);
            }

            // Delete Forum topic and messages
            nkDB_delete(FORUM_THREADS_TABLE, 'id = '. $threadId);
            nkDB_delete(FORUM_MESSAGES_TABLE, 'thread_id = '. $threadId);

            // Update Forum stats
            nkDB_update(FORUM_TABLE, array(
                    'nbTopics'      => array('nbTopics - 1', 'no-escape'),
                    'nbMessages'    => array('nbMessages - '. ($dbrForumTopic['nbReplies'] + 1), 'no-escape')
                ),
                'id = '. $forumId
            );
        }
        else {
            // Delete Forum message
            nkDB_delete(FORUM_MESSAGES_TABLE, 'id = '. $messId);

            // Update Forum stats
            nkDB_update(FORUM_TABLE, array(
                    'nbMessages' => array('nbMessages - 1', 'no-escape')
                ),
                'id = '. $forumId
            );

            nkDB_update(FORUM_THREADS_TABLE, array(
                    'nbReplies' => array('nbReplies - 1', 'no-escape')
                ),
                'id = '. $threadId
            );

            $url .= '&thread_id='. $threadId;
        }

        printNotification(_MESSDELETED, 'success');
        redirect($url, 2);
    }
    // User aborted
    else if ($_POST['confirm'] == _NO) {
        printNotification(_DELCANCEL, 'warning');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
    }
}

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
            'message'   => _CONFIRMDELFILE,
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
            printNotification(_TOKEN_INVALID, 'error');
            redirect('index.php?file=Forum&op=del_file&forum_id='. $forumId .'&thread_id='. $threadId .'&mess_id='. $messId, 2);
            return;
        }

        // Delete joined file if exist
        if (is_file('upload/Forum/'. $dbrForumMessage['file'])) {
            deleteForumMessageFile($dbrForumMessage['file']);

            nkDB_update(FORUM_MESSAGES_TABLE, array('file' => ''), 'id = '. $messId);
            printNotification(_FILEDELETED, 'success');
        }

        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
    }
    // User aborted
    else if ($_POST['confirm'] == _NO) {
        printNotification(_DELCANCEL, 'warning');
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
            $message    = _NOTIFYISON;
            $notify     = 1;
        }
        else if ($do == 'off') {
            $message    = _NOTIFYISOFF;
            $notify     = 0;
        }

        // Update notification status of Forum message
        if (isset($notify)) {
            nkDB_update(FORUM_MESSAGES_TABLE,
                array('emailnotify' => $notify),
                'thread_id = '. $threadId .' AND auteur_id = '. nkDB_escape($user['id'])
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
            'message'   => _CONFIRMDELTOPIC,
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
            printNotification(_TOKEN_INVALID, 'error');
            redirect('index.php?file=Forum&page=del_topic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
            return;
        }

        // Delete Forum poll data if needed
        if (getThreadPollStatus($threadId) == 1) {
            $pollId = getThreadPollId($threadId);

            nkDB_delete(FORUM_POLL_TABLE, 'id = '. $pollId);
            nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. $pollId);
            nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. $pollId);
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

        printNotification(_TOPICDELETED, 'success');
        redirect('index.php?file=Forum&page=viewforum&forum_id='. $forumId, 2);
    }
    // User aborted
    else if ($_POST['confirm'] == __('NO')) {
        printNotification(_DELCANCEL, 'warning');
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
            printNotification(_TOKEN_INVALID, 'error');
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

        printNotification(_TOPICMOVED, 'success');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $newForumId .'&thread_id='. $threadId, 2);
    }
    else if ($_POST['confirm'] == __('NO')) {
        printNotification(_DELCANCEL, 'warning');

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
            $message    = _TOPICLOCKED;
            $closed     = 1;
        }
        else if ($_GET['do'] == 'open') {
            $message    = _TOPICUNLOCKED;
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

            printNotification(_TOPICMODIFIED, 'success');
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

            nkDB_update(SESSIONS_TABLE, array('last_used' => array('date', 'no-escape')), 'user_id = '. nkDB_escape($user['id']));
        }*/

        // On veut modifier la chaine thread_id et forum_id
        $dbrForumRead = nkDB_selectOne(
            'SELECT thread_id, forum_id
            FROM '. FORUM_READ_TABLE .'
            WHERE user_id = '. nkDB_escape($user['id'])
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

    printNotification(_MESSAGESMARK, 'success');
    redirect('index.php?file=Forum', 2);
}

/* Forum poll management */

// Display Forum poll form.
function editPoll() {
    global $nuked;

    $forumId  = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
    $threadId = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;
    $pollId   = (isset($_GET['poll_id'])) ? (int) $_GET['poll_id'] : 0;

    // Check access
    if (($result = checkForumPollAccess($forumId, $threadId, $pollId)) !== true) {
        $error = $result;
    }
    else {
        $title = '';

        if ($pollId > 0) {
            // Get poll data
            $dbrForumPoll = nkDB_selectOne(
                'SELECT title
                FROM '. FORUM_POLL_TABLE .'
                WHERE id = '. $pollId
            );

            // Check poll exist
            if (! $dbrForumPoll) $error = _NOFORUMPOLLEXIST;

            $title = $dbrForumPoll['title'];
        }
    }

    if (isset($error)) {
        printNotification($error, 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
        return;
    }

    if ($pollId == 0) {
        // Check maximum option
        if (isset($_GET['survey_field']) && ctype_digit($_GET['survey_field']))
            $maxOptions = (int) $_GET['survey_field'];
        else
            $maxOptions = 2;

        if ($maxOptions > $nuked['forum_field_max']) $maxOptions = $nuked['forum_field_max'];

        // Set default option
        $pollOptions = array_fill(1, $maxOptions, array('option_text' => ''));
        $newOptions  = false;
    }
    else {
        $maxOptions = null;

        // Get poll option
        $pollOptions = nkDB_selectMany(
            'SELECT id, option_text
            FROM '. FORUM_OPTIONS_TABLE .'
            WHERE poll_id = '. $pollId,
            array('id')
        );

        // Enabled new option if needed
        $newOption = count($pollOptions) < $nuked['forum_field_max'];
    }

    echo applyTemplate('modules/Forum/editPoll', array(
        'title'         => $title,
        'pollOptions'   => $pollOptions,
        'newOption'     => $newOption,
        'maxOptions'    => $maxOptions,
        'pollId'        => $pollId,
        'threadId'      => $threadId,
        'forumId'       => $forumId
    ));
}

// Save / modify Forum poll.
function savePoll() {
    global $nuked;

    $forumId     = (isset($_POST['forum_id'])) ? (int) $_POST['forum_id'] : 0;
    $threadId    = (isset($_POST['thread_id'])) ? (int) $_POST['thread_id'] : 0;
    $pollId      = (isset($_POST['poll_id'])) ? (int) $_POST['poll_id'] : 0;

    // Check access
    if (($result = checkForumPollAccess($forumId, $threadId, $pollId)) !== true) {
        $error = $result;
    }
    else {
        // Check empty option string
        if (getNbFilledForumPollOption() < 2) $error = _2OPTIONMIN;

        // Check poll title
        if ($_POST['title'] == '' || ctype_space($_POST['title'])) $error = _FIELDEMPTY;
    }

    if (isset($error)) {
        printNotification($error, 'warning');

        if ($pollId == 0)
            redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
        else
            redirect('index.php?file=Forum&op=editPoll&poll_id='. $pollId . '&forum_id='. $forumId .'&thread_id='. $threadId, 2);

        return;
    }

    // Save / modify Forum poll
    $pollData = array('title' => stripslashes($_POST['title']));

    if ($pollId == 0) {
        $pollData['thread_id'] = $threadId;

        nkDB_insert(FORUM_POLL_TABLE, $pollData);

        $pollId = nkDB_insertId();
        $newPoll = true;
    }
    else {
        nkDB_update(FORUM_POLL_TABLE, $pollData, 'id = '. $pollId);
        $newPoll = false;
    }

    // Check maximum option
    $nbOptions = (isset($_POST['maxOptions'])) ? (int) $_POST['maxOptions'] : count($_POST['option']);

    if ($nbOptions > $nuked['forum_field_max'])
        $maxOptions = $nuked['forum_field_max'];
    else
        $maxOptions = $nbOptions;

    $maxOptions++;

    // Save poll option in database.
    $r = 1;

    while ($r < $maxOptions) {
        if ($newPoll)
            addPollOption($pollId, $r);
        else
            updatePollOption($pollId, $r);

        $r++;
    }

    if ($nbOptions < $nuked['forum_field_max'] && isset($_POST['newOption']) && $_POST['newOption'] != '') {
        nkDB_insert(FORUM_OPTIONS_TABLE, array(
            'id'            => $r,
            'poll_id'       => $pollId,
            'option_text'   => stripslashes($_POST['newOption']),
            'option_vote'   => 0
        ));
    }

    if ($newPoll)
        printNotification(_POLLADD, 'success');
    else
        printNotification(_POLLMODIF, 'success');

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
}

// Delete Forum poll.
function deletePoll() {
    $forumId    = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
    $threadId   = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;
    $pollId     = (isset($_GET['poll_id'])) ? (int) $_GET['poll_id'] : 0;

    // Check access
    if (! checkForumPollAccess($forumId, $threadId, $pollId)) {
        printNotification(__('ZONE_ADMIN'), 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
        return;
    }

    //
    if (! isset($_POST['confirm'])) {
        echo applyTemplate('confirm', array(
            'url'       => 'index.php?file=Forum&amp;op=deletePoll&amp;poll_id='. $pollId . '&amp;forum_id='. $forumId .'&amp;thread_id='. $threadId,
            'message'   => _CONFIRMDELPOLL,
            'fields'    => array(
                'token'     => nkToken_generate('deleteForumPoll'. $forumId . $threadId . $pollId)
            ),
        ));
    }
    else if ($_POST['confirm'] == __('YES')) {
        // Check delete Forum poll token
        $tokenValid = nkToken_valid('deleteForumPoll'. $forumId . $threadId . $pollId,
            300, array('index.php?file=Forum&op=deletePoll&poll_id='. $pollId .'&forum_id='. $forumId .'&thread_id='. $threadId)
        );

        if (! $tokenValid) {
            printNotification(_TOKEN_INVALID, 'error');
            redirect('index.php?file=Forum&op=deletePoll&poll_id='. $pollId .'&forum_id='. $forumId .'&thread_id='. $threadId, 2);
            return;
        }

        nkDB_delete(FORUM_POLL_TABLE, 'id = '. $pollId);
        nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. $pollId);
        nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. $pollId);
        nkDB_update(FORUM_THREADS_TABLE, array('sondage' => 0), 'id = '. $threadId);

        printNotification(_POLLDELETE, 'success');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
    }
    else if ($_POST['confirm'] == __('NO')) {
        printNotification(_DELCANCEL, 'warning');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
    }
}

// Save survey result of thread page.
function vote() {
    global $visiteur, $user, $user_ip;

    $forumId  = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
    $threadId = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;
    $pollId   = (isset($_GET['poll_id'])) ? (int) $_GET['poll_id'] : 0;
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
                        printNotification(_NOFORUMPOLLEXIST, 'error');
                        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
                        return;
                    }

                    nkDB_insert(FORUM_VOTE_TABLE, array(
                        'poll_id'   => $pollId,
                        'author_id' => $user['id'],
                        'author_ip' => $user_ip
                    ));

                    printNotification(_VOTESUCCES, 'success');
                }
                else {
                    printNotification(_ALREADYVOTE, 'warning');
                }
            }
            else {
                printNotification(_BADLEVEL, 'error');
            }
        }
        else {
            printNotification(_ONLYMEMBERSVOTE, 'error');
        }
    }
    else {
        printNotification(_NOOPTION, 'warning');
    }

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
}


opentable();

switch ($_REQUEST['op']) {
    case 'index' :
        index();
        break;

    case 'post' :
        post();
        break;

    case 'reply' :
        reply();
        break;

    case 'edit' :
        edit();
        break;

    case 'del' :
        del();
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

    case 'vote' :
        vote();
        break;

    case 'editPoll' :
        editPoll();
        break;

    case 'savePoll' :
        savePoll();
        break;

    case 'deletePoll' :
        deletePoll();
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