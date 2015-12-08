<?php
/**
 * viewforum.php
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

global $nuked, $user, $visiteur;

require_once 'modules/Forum/core.php';


function getForumTopicIcon($topicData, $nbReply) {
    global $user, $nuked;

    if ($user) {
        $user_visitx = nkDB_totalNumRows(
            'FROM '. FORUM_READ_TABLE .'
            WHERE user_id = '. nkDB_escape($user['id']) .'
            AND `thread_id` LIKE \'%,'. $topicData['id'] .',%\''
        );
    }
    else
        $user_visitx = 0;

    if ($user && $topicData['closed'] == 1 && $user_visitx == 0) {
        return 'nkForumNewTopicLock';
    }
    else if ($topicData['closed'] == 1) {
        return 'nkForumTopicLock';
    }
    else if ($user && $nbReply >= $nuked['hot_topic'] && $user_visitx == 0) {
        return 'nkForumNewTopicPopular';
    }
    else if ($user && $user_visitx >= 0 && $nbReply >= $nuked['hot_topic']) {
        return 'nkForumTopicPopular';
        //$labelHot = '<span class="nkForumLabels nkForumOrangeColor">'. _HOT .'</span>';
    }
    else if ($user && $user_visitx == 0 && $nbReply < $nuked['hot_topic']) {
        return 'nkForumNewTopic';
    }

    return '';
}

function formatTopicTitle($topicData, $joinedFiles, $forumId) {
    global $nuked;

    $html = '';

    if ($topicData['annonce'] == 1) {
        if ($nuked['forum_labels_active'] == 'on')
            $html .= '<span class="nkForumLabels nkForumOrangeColor">'. _ANNOUNCE .'</span>&nbsp;';
        else
            $html .= '<img src="modules/Forum/images/announce.png" class="nkForumAlignImg" alt="" title="'. _ANNOUNCE .'" />&nbsp;';
    }

    if ($topicData['sondage'] == 1) {
        if ($nuked['forum_labels_active'] == 'on')
            $html .= '<span class="nkForumLabels nkForumGreenColor">'. _SURVEY .'</span>&nbsp;';
        else
            $html .= '<img src="modules/Forum/images/poll.png" class="nkForumAlignImg" alt="" title="'. _SURVEY .'" />&nbsp;';
    }

    if ($joinedFiles > 0) {
        if ($nuked['forum_labels_active'] == 'on')
            $html .= '<span class="nkForumLabels nkForumGreyColor">'. _ATTACHFILE .'</span>&nbsp;';
        else
            $html .= '<img src="modules/Forum/images/clip.png" class="nkForumAlignImg" alt="" title='. _ATTACHFILE .' ('. $joinedFiles .')" />&nbsp;';
    }

    $html .= '<a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id='. $forumId .'&amp;thread_id='. $topicData['id'] .'" title="'. printSecuTags($topicData['titre']) .'">';

    if (strlen($topicData['titre']) > 30)
        $html .= printSecuTags(substr($topicData['titre'], 0, 30)) .'...';
    else
        $html .= printSecuTags($topicData['titre']);

    $html .= '</a>';

    return $html;
}

function formatTopicRow($forumthread, $forumId) {
    global $nuked;

    $threadData = array();

    //$sql8 = mysql_query("SELECT txt FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $forumthread['id'] . "' ORDER BY id LIMIT 0, 1");
    //list($txt) = mysql_fetch_array($sql8);

    //$txt = str_replace("\r", "", $txt);
    //$txt = str_replace("\n", " ", $txt);

    //$texte = strip_tags($txt);

    //if (!preg_match("`[a-zA-Z0-9\?\.]`i", $texte)) {
    //    $texte = _NOTEXTRESUME;
    //}

    //if (strlen($texte) > 150) {
    //    $texte = substr($texte, 0, 150) . "...";
    //}

    //$texte = nkHtmlEntities($texte);
    //$texte = nk_CSS($texte);

    $title = nkHtmlEntities(printSecuTags($forumthread['titre']));

    $dbrForumMessage = nkDB_selectMany(
        'SELECT file
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE thread_id = '. $forumthread['id']
    );

    $nbMess                 = nkDB_numRows();
    $threadData['nbReply']  = $nbMess - 1;

    // Get total of joined file in Forum topic
    $joinedFiles = 0;

    foreach ($dbrForumMessage as $forumMessage)
        if ($forumMessage['file'] != '') $joinedFiles++;

    // Get last message data of thread
    if ($nuked['forum_user_details'] == 'on') {
        $teamRank   = getTeamRank();
        $field      = ', U.rang';
    }
    else
        $field = '';

    $dbrForumMessage = nkDB_selectOne(
        'SELECT FM.id, FM.date, FM.auteur, FM.auteur_id,
        U.pseudo, U.country, U.avatar'. $field .'
        FROM '. FORUM_MESSAGES_TABLE .' AS FM
        INNER JOIN '. USER_TABLE .' AS U
        ON U.id = FM.auteur_id
        WHERE FM.thread_id = '. $forumthread['id'],
        array('FM.id'), 'DESC', 1
    );

    $threadData['lastMsgDate']  = formatForumMessageDate($dbrForumMessage['date']);
    $threadData['topicIcon']    = getForumTopicIcon($forumthread, $threadData['nbReply']);
    $threadData['topicTitle']   = formatTopicTitle($forumthread, $joinedFiles, $forumId);

    $threadUrl = 'index.php?file=Forum&amp;page=viewtopic&amp;forum_id='. $forumId .'&amp;thread_id='. $forumthread['id'];

    if ($nbMess > $nuked['mess_forum_page']) {
        $topicpages = ceil($nbMess / $nuked['mess_forum_page']);

        $link_post = $threadUrl .'&amp;p='. $topicpages .'#'. $dbrForumMessage['id'];

        $pagelinks = '';

        for ($l = 1; $l <= $topicpages; $l++)
            $pagelinks .= ' <a href="'. $threadUrl .'&amp;p='. $l .'" class="nkForumLinkMultipage2">'. $l .'</a>';

        $threadData['topicPagination'] = '<small>'. $pagelinks .'</small>';
    }
    else {
        $threadData['topicPagination'] = '';
        $link_post = $threadUrl .'#'. $dbrForumMessage['id'];
    }

    // Lien en image vers le message
    $threadData['lastMsgLink'] = '<a href="'. $link_post .'"><img style="border: 0;" src="modules/Forum/images/icon_latest_reply.png" class="nkForumAlignImg" alt="" title="'. _SEELASTPOST .'" /></a>';

    // On identifie l'auteur du message original
    if ($forumthread['pseudo'] != '') {
        if ($nuked['forum_user_details'] == 'on' && array_key_exists($forumthread['rang'], $teamRank))
            $style = ' style="color: #'. $teamRank[$forumthread['rang']]['color'] .';"';
        else
            $style = '';

        $threadData['createdBy'] = '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($forumthread['pseudo']) .'" '. $style .'><b>'. $forumthread['pseudo'] .'</b></a>';
    }
    else {
        $threadData['createdBy'] = '<strong>'. nk_CSS($forumthread['auteur']) .'</strong>';
    }

    // On identifie le dernier posteur
    if ($dbrForumMessage['pseudo'] != '') {
        if ($nuked['forum_user_details'] == 'on' && array_key_exists($dbrForumMessage['rang'], $teamRank))
            $style = ' style="color: #'. $teamRank[$dbrForumMessage['rang']]['color'] .';"';
        else
            $style = '';

        $threadData['lastMsgAuthor'] = '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($dbrForumMessage['pseudo']) .'" '. $style .'>'. $dbrForumMessage['pseudo'] .'</a>';

        if ($dbrForumMessage['avatar'] != '')
            $threadData['lastMsgAuthorAvatar'] = $dbrForumMessage['avatar'];
        else
            $threadData['lastMsgAuthorAvatar'] = 'modules/Forum/images/noAvatar.png';
    }
    else {
        $threadData['lastMsgAuthor']        = nk_CSS($dbrForumMessage['auteur']);
        $threadData['lastMsgAuthorAvatar']  = 'modules/Forum/images/noAvatar.png';
    }

    return $threadData;
}

$forumId = (isset($_REQUEST['forum_id'])) ? (int) $_REQUEST['forum_id'] : 0;

// Get current Forum data
$field = (! empty($_REQUEST['date_max'])) ? '' : ', F.nbThread';

$dbrCurrentForum = getForumData(
    'F.nom AS forumName, F.comment, F.moderateurs, F.image, F.cat, F.niveau AS forumLevel,
    FC.nom As catName, FC.niveau AS catLevel'. $field, 'forumId', $forumId
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

// Prepare Forum breadcrumb
$breadcrumb = getForumBreadcrump(
    $dbrCurrentForum['catName'], $dbrCurrentForum['cat'],
    $dbrCurrentForum['forumName']
);

// Prepare data of Forum topic list
$dbrCurrentForum['forumName']  = printSecuTags($dbrCurrentForum['forumName']);
$dbrCurrentForum['catName']    = printSecuTags($dbrCurrentForum['catName']);

if ($dbrCurrentForum['moderateurs'] != '')
    $moderatorList = implode(',&nbsp;', getModeratorList($dbrCurrentForum['moderateurs']));
else
    $moderatorList = _NONE;

$moderator = isModerator($dbrCurrentForum['moderateurs']);

// Get total of topic Forum
if (! empty($_REQUEST['date_max'])) {
    $dateMaxTimestamp = time() - $_REQUEST['date_max'];

    $nbTopicInForum = nkDB_totalNumRows(
        'FROM '. FORUM_THREADS_TABLE .'
        WHERE forum_id = '. $forumId .' AND date > '. $dateMaxTimestamp
    );
}
else
    $nbTopicInForum = $dbrCurrentForum['nbThread'];


// Get pagination of topic Forum list
if ($nbTopicInForum > $nuked['thread_forum_page']) {
    $url = 'index.php?file=Forum&amp;page=viewforum&amp;forum_id='. $forumId;

    if (! empty($_REQUEST['date_max']))
        $url .= '&amp;date_max='. $_REQUEST['date_max'];

    $pagination = number($nbTopicInForum, $nuked['thread_forum_page'], $url, true);
}
else {
    $pagination = '';
}

// Get topic Forum list
$field = ($nuked['forum_user_details'] == 'on') ? ', U.rang' : '';

$sql = 'SELECT FT.id, FT.titre, FT.date, FT.auteur, FT.view, FT.closed, FT.annonce, FT.sondage,
    U.pseudo, U.country'. $field .'
    FROM '. FORUM_THREADS_TABLE .' AS FT
    INNER JOIN '. USER_TABLE .' AS U
    ON U.id = FT.auteur_id
    WHERE FT.forum_id = '. $forumId;

if (! empty($_REQUEST['date_max']))
    $sql .= ' AND date > '. $dateMaxTimestamp;

$p = ! isset($_GET['p']) ? 1 : $_GET['p'];

$start = $p * $nuked['thread_forum_page'] - $nuked['thread_forum_page'];

$dbrForumthread = nkDB_selectMany($sql, array('annonce', 'last_post'), 'DESC', $nuked['thread_forum_page'], $start);

// Get Forum list for quick shortcuts Forum selection
$dbrForumList = nkDB_selectMany(
    'SELECT id, nom
    FROM '. FORUM_TABLE .'
    WHERE cat = '. $dbrCurrentForum['cat'],
    array('ordre', 'nom')
);

$forumList = array();

foreach ($dbrForumList as $forum)
    $forumList[$forum['id']] = printSecuTags($forum['nom']);

// Display topic Forum list
opentable();

echo applyTemplate('modules/Forum/viewForum', array(
    'dbrForum'          => $dbrCurrentForum,
    'breadcrumb'        => $breadcrumb,
    'nuked'             => $nuked,
    'moderatorList'     => $moderatorList,
    'visiteur'          => $visiteur,
    'moderator'         => $moderator,
    'forumId'           => $forumId,
    'pagination'        => $pagination,
    'dbrForumthread'    => $dbrForumthread,
    'user'              => $user,
    'forumList'         => $forumList
));

closetable();

?>