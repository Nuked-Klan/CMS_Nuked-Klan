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

$captcha = initCaptcha();

// do :
// post : index.php?file=Forum&page=post&forum_id=X
// reply : index.php?file=Forum&page=post&forum_id=X&thread_id=X
// edit : index.php?file=Forum&page=post&forum_id=X&mess_id=X&do=edit
// quote : index.php?file=Forum&page=post&forum_id=X&thread_id=X&mess_id=X&do=quote

$do         = (isset($_REQUEST['do'])) ? $_REQUEST['do'] : '';
$forumId    = (int) $_REQUEST['forum_id'];
$threadId   = (isset($_REQUEST['thread_id'])) ? (int) $_REQUEST['thread_id'] : 0;
$messId     = (isset($_REQUEST['mess_id'])) ? (int) $_REQUEST['mess_id'] : 0;


define('EDITOR_CHECK', 1);

$dbrForum = nkDB_selectOne(
    'SELECT nom, cat, level_poll, moderateurs
    FROM '. FORUM_TABLE .'
    WHERE '. $visiteur .' >= niveau AND id = '. $forumId
);

// No user access
if (nkDB_numRows() == 0) {
    opentable();
    printNotification(_NOACCESSFORUM, 'error');
    closetable();
    return;
}

// User access
$dbrForumCat = nkDB_selectOne(
    'SELECT nom
    FROM '. FORUM_CAT_TABLE .'
    WHERE id = '. nkDB_escape($dbrForum['cat'])
);

// Check moderator
if ($user && $dbrForum['moderateurs'] != '' && strpos($user['id'], $dbrForum['moderateurs']))
    $administrator = true;
else
    $administrator = false;


if ($threadId > 0) {
    $action     = 'index.php?file=Forum&amp;op=reply';
    $actionName = _POSTREPLY;
}
else if ($do == 'edit') {
    $action     = 'index.php?file=Forum&amp;op=edit';
    $actionName = _POSTEDIT;
}
else {
    $action     = 'index.php?file=Forum&amp;op=post';
    $actionName = _POSTNEWTOPIC;
}

// Construction du Breadcrump
$category = '-> <a href="index.php?file=Forum&amp;cat='.$dbrForum['cat'].'"><strong>'.$dbrForumCat['nom'].'</strong></a>&nbsp;';
$topic = '-> <a href="index.php?file=Forum&amp;page=viewforum&amp;forum_id=' . $forumId . '"><strong>'.$dbrForum['nom'].'</strong></a>&nbsp;';
$nav = $category . $topic;

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

if ($threadId > 0) {
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

if ($do == 'edit') {
    $author = $dbrForumMessage['auteur'];
    $postTitle = printSecuTags($dbrForumMessage['titre']);
    $postText = $dbrForumMessage['txt'];
    $usersigChecked     = ($dbrForumMessage['usersig'] == 1) ? 'checked="checked"' : '';
    $emailnotifyChecked = ($dbrForumMessage['emailnotify'] == 1) ? 'checked="checked"' : '';
}
else if ($threadId > 0) {
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
    'nav'                   => $nav,
    'actionName'            => $actionName,
    'visiteur'              => $visiteur,
    'user'                  => $user,
    'nuked'                 => $nuked,
    'administrator'         => $administrator,
    'pollLevel'             => $dbrForum['level_poll'],
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