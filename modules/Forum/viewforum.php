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

global $nuked, $user, $visiteur, $forumId, $rankField;

require_once 'modules/Forum/core.php';


/**
 * Format Forum topic row data.
 *
 * @param array $forumTopic : The raw Forum topic row data.
 * @return array : The Forum topic row data formated.
 */
function formatTopicRow($forumTopic) {
    global $nuked, $forumId;

    // Get total of joined file in Forum topic
    // TODO Add new field in FORUM_THREADS_TABLE ?
    $dbrForumMessage = nkDB_selectMany(
        'SELECT file
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE thread_id = '. $forumTopic['id']
    );

    $forumTopic['joinedFiles'] = 0;

    foreach ($dbrForumMessage as $forumMessage)
        if ($forumMessage['file'] != '') $forumTopic['joinedFiles']++;

    $forumTopic['iconStatus']   = getForumTopicIcon($forumTopic);
    $forumTopic['author']       = nkForumNickname($forumTopic);
    $forumTopic['lastMessage']  = formatLastForumTopicMsg($forumTopic['id']);

    // Add last message url of Forum topic
    list($forumTopic['lastMessage']['url'], $nbTopicPage) = getForumMessageUrl(
        $forumId, $forumTopic['id'], $forumTopic['lastMessage']['id'], $forumTopic['nbReplies'] + 1
    );

    // Add Forum topic pagination
    $forumTopic['pagination'] = '';

    if ($nbTopicPage > 1) {
        $forumTopic['pagination'] .= '<small>';

        for ($l = 1; $l <= $nbTopicPage; $l++) {
            $forumTopic['pagination'] .= ' <a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id='. $forumId
                . '&amp;thread_id='. $forumTopic['id'] .'&amp;p='. $l .'" class="nkForumLinkMultipage2">'. $l .'</a>';
        }

        $forumTopic['pagination'] .= '</small>';
    }

    // Truncate title of last Forum topic message if needed
    if (strlen($forumTopic['titre']) > 30)
        $forumTopic['cleanedTitle'] = printSecuTags(substr($forumTopic['titre'], 0, 30)) .'...';
    else
        $forumTopic['cleanedTitle'] = printSecuTags($forumTopic['titre']);

    $forumTopic['titre'] = printSecuTags($forumTopic['titre']);

    return $forumTopic;
}

/*
function formatInfobulleContent() {
    $sql8 = mysql_query("SELECT txt FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $forumthread['id'] . "' ORDER BY id LIMIT 0, 1");
    list($txt) = mysql_fetch_array($sql8);

    $txt = str_replace("\r", "", $txt);
    $txt = str_replace("\n", " ", $txt);

    $texte = strip_tags($txt);

    if (!preg_match("`[a-zA-Z0-9\?\.]`i", $texte)) {
       $texte = _NOTEXTRESUME;
    }

    if (strlen($texte) > 150) {
       $texte = substr($texte, 0, 150) . "...";
    }

    $texte = nkHtmlEntities($texte);
    $texte = nk_CSS($texte);

    $title = nkHtmlEntities(printSecuTags($forumthread['titre']));
}
*/

/**
 * Format last message data of Forum topic.
 *
 * @param int $threadId : The Forum topic ID.
 * @return array : The last message formated data of Forum topic.
 */
function formatLastForumTopicMsg($threadId) {
    global $nuked, $forumId, $rankField;

    // Get last message data of Forum topic
    $dbrLastForumTopicMsg = getLastForumMessageData('thread_id', $threadId,
        'FM.id, FM.date, FM.auteur, U.pseudo, U.country, U.avatar'. $rankField
    );

    // Set last message author and formated date of Forum topic
    $lastMsgData = array(
        'id'        => $dbrLastForumTopicMsg['id'],
        'author'    => nkForumNickname($dbrLastForumTopicMsg),
        'date'      => formatForumMessageDate($dbrLastForumTopicMsg['date'])
    );

    // Set default avatar if undefined
    if ($dbrLastForumTopicMsg['avatar'] != '')
        $lastMsgData['authorAvatar'] = $dbrLastForumTopicMsg['avatar'];
    else
        $lastMsgData['authorAvatar'] = 'modules/Forum/images/noAvatar.png';

    return $lastMsgData;
}

/**
 * Get CSS class of Forum topic status.
 *
 * @param array $forumTopic : The Forum topic row data.
 * @return string : The CSS class of Forum topic status.
 */
function getForumTopicIcon($forumTopic) {
    global $user, $nuked;

    if ($user) {
        $user_visitx = nkDB_totalNumRows(
            'FROM '. FORUM_READ_TABLE .'
            WHERE user_id = '. nkDB_escape($user['id']) .'
            AND `thread_id` LIKE \'%,'. $forumTopic['id'] .',%\''
        );
    }
    else
        $user_visitx = 0;

    if ($user && $forumTopic['closed'] == 1 && $user_visitx == 0) {
        return 'nkForumNewTopicLock';
    }
    else if ($forumTopic['closed'] == 1) {
        return 'nkForumTopicLock';
    }
    else if ($user && $forumTopic['nbReplies'] >= $nuked['hot_topic'] && $user_visitx == 0) {
        return 'nkForumNewTopicPopular';
    }
    else if ($user && $user_visitx >= 0 && $forumTopic['nbReplies'] >= $nuked['hot_topic']) {
        return 'nkForumTopicPopular';
    }
    else if ($user && $user_visitx == 0 && $forumTopic['nbReplies'] < $nuked['hot_topic']) {
        return 'nkForumNewTopic';
    }

    return '';
}


$dateMax    = (isset($_GET['date_max'])) ? (int) $_GET['date_max'] : 0;
$forumId    = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
$p          = (isset($_GET['p'])) ? max((int) $_GET['p'], 1) : 1;
$error      = false;

// Get current Forum data
$field = ($dateMax > 0) ? '' : ', F.nbTopics';

$dbrCurrentForum = getForumData(
    'F.nom AS forumName, F.comment, F.moderateurs, F.image, F.cat, F.niveau AS forumLevel,
    FC.nom As catName, FC.niveau AS catLevel'. $field, 'forumId', $forumId
);

// Check Forum access, Forum category access and Forum exist
if (! $dbrCurrentForum) $error = __('FORUM_NO_EXIST');
if ($visiteur < $dbrCurrentForum['catLevel']) $error = __('NO_ACCESS_FORUM_CATEGORY');
if ($visiteur < $dbrCurrentForum['forumLevel']) $error = __('NO_ACCESS_FORUM');

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

// Prepare data of Forum topics list
$dbrCurrentForum['forumName']  = printSecuTags($dbrCurrentForum['forumName']);
$dbrCurrentForum['catName']    = printSecuTags($dbrCurrentForum['catName']);

$moderatorsList = formatModeratorsList($dbrCurrentForum['moderateurs']);

$forumWriteLevel = $dbrCurrentForum['forumLevel'] == 0
    || $visiteur >= $dbrCurrentForum['forumLevel']
    || isModerator($dbrCurrentForum['moderateurs']);

// Get Forum topics list
$rankField = ($nuked['forum_user_details'] == 'on') ? ', U.niveau, U.count, U.rang' : '';

$sql = 'SELECT FT.id, FT.titre, FT.date, FT.auteur, FT.view, FT.closed, FT.annonce,
    FT.sondage, FT.nbReplies, U.pseudo, U.country'. $rankField .'
    FROM '. FORUM_THREADS_TABLE .' AS FT
    LEFT JOIN '. USER_TABLE .' AS U
    ON U.id = FT.auteur_id
    WHERE FT.forum_id = '. $forumId;

if ($dateMax > 0) {
    $dateMaxTimestamp = time() - $dateMax;

    $sql .= ' AND FT.date > '. $dateMaxTimestamp;
}

$dbrForumTopics = nkDB_selectMany($sql,
    array('FT.annonce', 'FT.last_post'), 'DESC',
    $nuked['thread_forum_page'], ($p - 1) * $nuked['thread_forum_page']
);

// Generate pagination only if more one page is filled
$pagination = '';

if (count($dbrForumTopics) > $nuked['thread_forum_page']) {
    // Get total of Forum topics
    if ($dateMax > 0) {
        $nbTopics = nkDB_totalNumRows(
            'FROM '. FORUM_THREADS_TABLE .'
            WHERE forum_id = '. $forumId .' AND date > '. $dateMaxTimestamp
        );
    }
    else
        $nbTopics = $dbrCurrentForum['nbTopics'];

    // Get pagination of Forum topics list
    if ($nbTopics > $nuked['thread_forum_page']) {
        $url = 'index.php?file=Forum&amp;page=viewforum&amp;forum_id='. $forumId;

        if ($dateMax > 0)
            $url .= '&amp;date_max='. $dateMax;

        $pagination = number($nbTopics, $nuked['thread_forum_page'], $url, true);
    }
}

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

// Set page title
nkTemplate_setTitle(__('FORUM') .' - '. $dbrCurrentForum['forumName']);

// Display Forum topics list
opentable();

echo applyTemplate('modules/Forum/viewForum', array(
    'nuked'             => $nuked,
    'user'              => $user,
    'visiteur'          => $visiteur,
    'forumId'           => $forumId,
    'currentForum'      => $dbrCurrentForum,
    'breadcrumb'        => $breadcrumb,
    'moderatorsList'    => $moderatorsList,
    'forumWriteLevel'   => $forumWriteLevel,
    'pagination'        => $pagination,
    'forumTopicsList'   => $dbrForumTopics,
    'forumList'         => $forumList
));

closetable();

?>