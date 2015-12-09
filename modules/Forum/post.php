<?php
/**
 * post.php
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

global $user, $language, $nuked, $bgcolor3, $visiteur;

// TODO : Missing $force_edit_message var. Commented in template.php file
include 'modules/Forum/template.php';
require_once 'modules/Forum/core.php';

$captcha = initCaptcha();


$do         = (isset($_REQUEST['do'])) ? $_REQUEST['do'] : 'post';
$forumId   = (isset($_REQUEST['forum_id'])) ? (int) $_REQUEST['forum_id'] : 0;
$threadId   = (isset($_REQUEST['thread_id'])) ? (int) $_REQUEST['thread_id'] : 0;
$messId     = (isset($_REQUEST['mess_id'])) ? (int) $_REQUEST['mess_id'] : 0;

if ($do == 'post' && $threadId > 0) $do = 'reply';

define('EDITOR_CHECK', 1);


// Get current Forum data
$dbrCurrentForum = getForumData(
    'F.nom AS forumName, F.moderateurs, F.cat, F.niveau AS forumLevel, F.level_poll,
    FC.nom AS catName, FC.niveau AS catLevel', 'forumId',  $forumId
);

// Check forum access, forum category access and forum exist
$error = false;
if (! $dbrCurrentForum) $error = _NOFORUMEXIST;
if ($visiteur < $dbrCurrentForum['catLevel'] ) $error = _NOACCESSFORUMCAT;
if ($visiteur < $dbrCurrentForum['forumLevel'] ) $error = _NOACCESSFORUM;

if ($error) {
    opentable();
    printNotification($error, 'error');
    closetable();
    return;
}

// Check moderator
$moderator      = isModerator($dbrCurrentForum['moderateurs']);
$administrator  = $visiteur >= admin_mod('Forum') || $moderator;

if ($do == 'edit') {
    $action     = 'index.php?file=Forum&amp;op=edit';
    $actionName = _POSTEDIT;
}
elseif ($do == 'post') {
    $action     = 'index.php?file=Forum&amp;op=post';
    $actionName = _POSTNEWTOPIC;
}
else {
    $action     = 'index.php?file=Forum&amp;op=reply';
    $actionName = _POSTREPLY;
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

        $postText = '<blockquote class="nkForumBlockQuote"><cite>'. _QUOTE .' '. _BY .' '. $dbrForumMessage['auteur'] .' :</cite><br />'
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
opentable();

echo applyTemplate('modules/Forum/post', array(
    'action'                => $action,
    'breadcrumb'            => $breadcrumb,
    'actionName'            => $actionName,
    'visiteur'              => $visiteur,
    'user'                  => $user,
    'nuked'                 => $nuked,
    'administrator'         => $administrator,
    'moderator'             => $moderator,
    'pollLevel'             => $dbrCurrentForum['level_poll'],
    'do'                    => $do,
    'forumId'               => $forumId,
    'threadId'              => $threadId,
    'messId'                => $messId,
    'author'                => $author,
    'postTitle'             => $postTitle,
    'postText'              => $postText,
    'usersigChecked'        => $usersigChecked,
    'emailnotifyChecked'    => $emailnotifyChecked,
    //'force_edit_message'    => $force_edit_message,
    'announceChecked'       => $announceChecked,
    'dbrLastMessageList'    => $dbrLastMessageList
));

closetable();

?>