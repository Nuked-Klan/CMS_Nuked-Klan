<?php
/**
 * post.php
 *
 * Frontend of Forum module - Forum post management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Forum'))
    return;

require_once 'modules/Forum/core.php';
require_once 'Includes/nkAction.php';

global $forumId, $threadId, $redirect;

$forumId    = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
$threadId   = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;
$messId     = (isset($_GET['mess_id'])) ? (int) $_GET['mess_id'] : 0;

$redirect = false;

nkAction_setParams(array(
    'dataName'                  => 'forumPost',
    'tableName'                 => FORUM_MESSAGES_TABLE,
    'deleteConfirmation'        => __('CONFIRM_DELETE_POST'),
    // delete uriData
    'uriData'                   => array('forum_id' => $forumId, 'thread_id' => $threadId, 'mess_id' => $messId),
    'backRedirection'           => 'forumPostRedirect'
));


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
 * Check if user can post a new message.
 *
 * @param string $username : The user name.
 * @return bool : Return true id user can post again, false also.
 */
function checkForumPostFlood($username) {
    global $nuked, $visiteur, $user_ip;
// TODO : FINISH HIM =D
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
        && $_FILES['uploadFile']['name'] != ''
    ) {
        list($filename, $uploadError) = nkUpload_check('uploadFile', array(
            'disallowedExtension'   => array('php', 'html'),
            'uploadDir'             => 'upload/Forum',
            'fileSize'              => $nuked['forum_file_maxsize'],
            'fileRename'            => true
        ));

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
 * Return redirection url after save / delete Forum post.
 *
 * @param void
 * @return string
 */
function getForumPostRedirectUrl() {
    global $nkAction, $forumId, $threadId, $redirect;

    if ($nkAction['actionType'] == 'delete') {
        if ($redirect)
            return $redirect;

        return 'index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId;
    }
}


// Display Forum post form.
function displayForumPostForm() {
    global $user, $language, $nuked, $visiteur;

    require_once 'Includes/nkToken.php';
    // TODO : Missing $force_edit_message var. Commented in template.php file
    include 'modules/Forum/template.php';

    $do         = (isset($_REQUEST['do'])) ? $_REQUEST['do'] : 'post';
    $forumId    = (isset($_REQUEST['forum_id'])) ? (int) $_REQUEST['forum_id'] : 0;
    $threadId   = (isset($_REQUEST['thread_id'])) ? (int) $_REQUEST['thread_id'] : 0;
    $messId     = (isset($_REQUEST['mess_id'])) ? (int) $_REQUEST['mess_id'] : 0;

    if ($do == 'post' && $threadId > 0) $do = 'reply';

    define('EDITOR_CHECK', 1);


    // Get current Forum data
    $dbrCurrentForum = getForumData(
        'F.nom AS forumName, F.cat, F.niveau AS forumLevel, F.level_poll,
        FC.nom AS catName, FC.niveau AS catLevel', 'forumId',  $forumId
    );

    // Check forum access, forum category access and forum exist
    $error = false;
    if (! $dbrCurrentForum) $error = __('FORUM_NO_EXIST');
    if ($visiteur < $dbrCurrentForum['catLevel'] ) $error = __('NO_ACCESS_FORUM_CATEGORY');
    if ($visiteur < $dbrCurrentForum['forumLevel'] ) $error = __('NO_ACCESS_FORUM');

    if ($error) {
        printNotification($error, 'error');
        return;
    }

    // Check moderator
    $moderator      = isModerator($forumId);
    $administrator  = $visiteur >= admin_mod('Forum') || $moderator;

    if ($do == 'edit') {
        $action     = 'index.php?file=Forum&amp;page=post&amp;op=update';
        $actionName = __('POST_EDIT');
        $tokenName  = 'editForumPost'. $forumId . $threadId . $messId;
    }
    elseif ($do == 'post') {
        $action     = 'index.php?file=Forum&amp;page=post&amp;op=save';
        $actionName = __('POST_NEW_TOPIC');
        $tokenName  = 'addForumPost'. $forumId;
    }
    else {
        $action     = 'index.php?file=Forum&amp;page=post&amp;op=reply';
        $actionName = __('POST_REPLY');
        $tokenName  = 'replyForumPost'. $forumId . $threadId;
    }

    // Prepare Forum breadcrumb
    $breadcrumb = getForumBreadcrump(
        $dbrCurrentForum['catName'], $dbrCurrentForum['cat'],
        $dbrCurrentForum['forumName'], $forumId
    );

    // Initialisation de la couleur des catégories en fonction du bgcolor
    if (isset($GLOBALS['bgcolor1']) && isset($GLOBALS['bgcolor2']) && isset($GLOBALS['bgcolor3']) && isset($GLOBALS['bgcolor4']))
        nkTemplate_addCSS('.nkForumPostHead { background: '. $GLOBALS['bgcolor3'] .'}');

    // Get message data for editing / quote
    if ($do == 'edit' || $do == 'quote') {
        $dbrForumMessage = nkDB_selectOne(
            'SELECT txt, titre, auteur, usersig, emailnotify
            FROM '. FORUM_MESSAGES_TABLE .'
            WHERE id = '. $messId
        );
    }

    $dbrLastMessageList = null;

    if ($do == 'reply' || $do == 'quote') {
        $dbrForumThread = nkDB_selectOne(
            'SELECT titre, annonce
            FROM '. FORUM_THREADS_TABLE .'
            WHERE id = '. $threadId
        );

        $dbrLastMessageList = nkDB_selectMany(
            'SELECT txt, auteur, date
            FROM '. FORUM_MESSAGES_TABLE .'
            WHERE thread_id = '. $threadId .' AND forum_id = '. $forumId,
            array('date'), 'DESC', 20
        );
    }

    $postTitle = $postText = $emailnotifyChecked = $announceChecked = $author = '';
    $usersigChecked = 'checked=checked';

    if ($do == 'edit')
        $author = $dbrForumMessage['auteur'];
    else if ($user && $user['name'] != '')
        $author = $user['name'];

    if ($do == 'edit') {
        $postTitle = printSecuTags($dbrForumMessage['titre']);
        $postText = $dbrForumMessage['txt'];
        $usersigChecked     = ($dbrForumMessage['usersig'] == 1) ? 'checked="checked"' : '';
        $emailnotifyChecked = ($dbrForumMessage['emailnotify'] == 1) ? 'checked="checked"' : '';
    }
    else if ($do == 'reply' || $do == 'quote') {
        if ($do == 'quote') {
            $postTitle = $dbrForumMessage['titre'];

            $postText = '<blockquote class="nkForumBlockQuote"><cite>'. __('QUOTE') .' '. __('BY')
                . ' '. $dbrForumMessage['auteur'] .' :</cite><br />'
                . $dbrForumMessage['txt'] .'</blockquote>';
        }
        else
            $postTitle = $dbrForumThread['titre'];

        $postTitle = nkHtmlEntities($postTitle);
        $postTitle = str_ireplace(array('&amp;lt;', '&amp;gt;'), array('&lt;', '&gt;'), $postTitle);
        $postTitle = 'RE : '. $postTitle;

        $announceChecked = ($dbrForumThread['annonce'] == 1) ? 'checked="checked"' : '';
    }

    $postText = editPhpCkeditor($postText);

    // Display Forum post form
    echo applyTemplate('modules/Forum/post', array(
        'nuked'                 => $nuked,
        'user'                  => $user,
        'visiteur'              => $visiteur,
        'forumId'               => $forumId,
        'threadId'              => $threadId,
        'messId'                => $messId,
        'do'                    => $do,
        'action'                => $action,
        'breadcrumb'            => $breadcrumb,
        'actionName'            => $actionName,
        'administrator'         => $administrator,
        'moderator'             => $moderator,
        'pollLevel'             => $dbrCurrentForum['level_poll'],
        'author'                => $author,
        'postTitle'             => $postTitle,
        'postText'              => $postText,
        'usersigChecked'        => $usersigChecked,
        'emailnotifyChecked'    => $emailnotifyChecked,
        //'force_edit_message'    => $force_edit_message,
        'announceChecked'       => $announceChecked,
        'dbrLastMessageList'    => $dbrLastMessageList,
        'token'                 => nkToken_generate($tokenName)
    ));
}

// Send a new Forum message.
function saveForumPost() {
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
        printNotification(__('TOKEN_NO_VALID'), 'error');
        redirect($referer, 2);
        return;
    }

    // Check if Forum exist and Forum posting level
    $dbrForum = nkDB_selectOne(
        'SELECT niveau, level, level_poll
        FROM '. FORUM_TABLE .'
        WHERE id = '. $forumId
    );

    if (! $dbrForum) $error = __('FORUM_NO_EXIST');
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
                $error = __('NO_FLOOD');
            }
        }
    }
    else {
        $error = __('FIELD_EMPTY');
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
        && $_POST['annonce'] == 1
        && $visiteur >= admin_mod('Forum')
    )
        $announcement = 1;
    else
        $announcement = 0;

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
        $url = 'index.php?file=Forum&page=poll&op=edit&forum_id='. $forumId .'&thread_id='. $threadId .'&survey_field='. $surveyField;
    else
        $url = 'index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId;

    printNotification(__('MESSAGE_SEND'), 'success');
    redirect($url, 2);
}

// Save a edited Forum message.
function updateForumPost() {
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
        printNotification(__('TOKEN_NO_VALID'), 'error');
        redirect($referer, 2);
        return;
    }

    // Check if Forum message exist and if user can edit it
    $dbrCurrentForumMsg = nkDB_selectOne(
        'SELECT auteur_id
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE id = '. $messId
    );

    if (! $dbrCurrentForumMsg
        || ! isForumAdministrator($forumId)
        || ! ($user && $dbrCurrentForumMsg['auteur_id'] == $user['id'])
    ) {
        printNotification(__('ZONE_ADMIN'), 'error');
        redirect('index.php?file=Forum', 2);
        return;
    }

    // Check post fields are really filled
    if ($_POST['titre'] == '' || @ctype_space($_POST['titre'])
        || $_POST['texte'] == '' || @ctype_space($_POST['texte'])
    ) {
        printNotification(__('FIELD_EMPTY'), 'error');
        redirect($referer, 2);
        return;
    }

    // Prepare Forum post data
    $postData = prepareForumPostData();

    if ($_POST['edit_text'] == 1)
        $postData['edition'] = __('EDIT_BY') .'&nbsp;'. $user['name'] .'&nbsp;'. __('THE') .'&nbsp;'. nkDate(time());

    // Get first Forum message ID of topic and check if Forum topic exist
    $dbrFirstForumMsg = nkDB_selectOne(
        'SELECT id
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE thread_id = '. $threadId,
        array('id'), 'ASC', 1
    );

    if (! $dbrFirstForumMsg) {
        printNotification(__('TOPIC_NO_EXIST'), 'error');
        redirect($referer, 2);
        return;
    }

    // Update Forum message
    nkDB_update(FORUM_MESSAGES_TABLE, $postData, 'id = '. $messId);

    // Update Forum topic title for first Forum message of topic
    if ($dbrFirstForumMsg['id'] == $messId)
        nkDB_update(FORUM_THREADS_TABLE, array('titre' => $postData['titre']), 'id = '. $threadId);

    list($url) = getForumMessageUrl($forumId, $threadId, $messId);

    printNotification(__('MESSAGE_MODIFIED'), 'success');
    redirect($url, 2);
}

// Save a Forum topic reply.
function replyForumPost() {
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
        printNotification(__('TOKEN_NO_VALID'), 'error');
        // TODO : Only for reply, no quote :/
        redirect($referer, 2);
        return;
    }

    // Check if Forum topic is closed and Forum posting level
    $dbrForum = nkDB_selectOne(
        'SELECT F.level, FT.closed, FT.nbReplies
        FROM '. FORUM_TABLE .' AS F
        INNER JOIN '. FORUM_THREADS_TABLE .' AS FT
        ON F.id = FT.forum_id
        WHERE FT.id = '. $threadId
    );

    if (! $dbrForum) {
        printNotification(__('FORUM_NO_EXIST'), 'error');
        redirect('index.php?file=Forum', 2);
        return;
    }

    if ($dbrForum
        && ! isForumAdministrator($forumId)
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
                $error = __('NO_FLOOD');
            }
        }
    }
    else {
        $error = __('FIELD_EMPTY');
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
        $subject    = __('MESSAGE') .' : '. $postData['titre'];
        $corps      = __('EMAIL_REPLY_NOTIFY') ."\r\n"
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

    printNotification(__('MESSAGE_SEND'), 'success');
    redirect($url, 2);
}

/* Forum post delete function */

/**
 * Callback function for nkAction_init.
 * Check if the user has the right to access Forum posting form.
 *
 * @param void
 * @return bool
 */
function checkForumPostAccess() {
    global $nkAction, $forumId, $threadId;

    if ($nkAction['actionType'] == 'delete') {
        if (! isForumAdministrator($forumId)) {
            printNotification(__('ZONE_ADMIN'), 'error');
            redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId, 2);
            return false;
        }
    }

    return true;
}

/**
 * Callback function for nkAction_delete.
 * Delete Forum post and his additional data.
 *
 * @param int $id : The Forum post id.
 * @return void
 */
function deleteForumPostData($id) {
    global $redirect, $forumId, $threadId;

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

    $redirect = 'index.php?file=Forum&page=viewforum&forum_id='. $forumId;

    // Check first Forum message of topic
    if ($dbrFirstForumMsg['id'] == $id) {
        // Get Forum topic data
        $dbrForumTopic = nkDB_selectOne(
            'SELECT nbReplies, sondage
            FROM '. FORUM_THREADS_TABLE .'
            WHERE id = '. $threadId
        );

        // Delete Forum poll data if needed
        if ($dbrForumTopic['sondage'] == 1) {
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
        nkDB_delete(FORUM_MESSAGES_TABLE, 'id = '. $id);

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

        $redirect .= '&thread_id='. $threadId;
    }
}


opentable();

// Action handle
switch ($GLOBALS['op']) {
    case 'save' :
        saveForumPost();
        break;

    case 'reply' :
        replyForumPost();
        break;

    case 'update' :
        updateForumPost();
        break;

    case 'delete' :
        // Delete Forum post.
        nkAction_delete();
        break;

    default :
        displayForumPostForm();
        break;
}

closetable();

?>
