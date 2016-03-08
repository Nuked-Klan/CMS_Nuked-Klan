<?php 
/**
 * viewtopic.php
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

//$captcha = initCaptcha();

global $nuked, $user, $theme, $visiteur, $highlight, $forumId, $threadId, $forumAdmin,
    $avatar_resize, $avatar_width;

include 'modules/Forum/template.php';
require_once 'modules/Forum/core.php';

if ($visiteur >= $nuked['user_social_level'])
    require_once 'Includes/nkUserSocial.php';

/**
 * Highlight researched string in title and message of Forum topic.
 *
 * @param string $title : The message title.
 * @param string $text : The message content.
 * @return array : A numerical indexed array with title and message content highlighted.
 */
// TODO : Rewrite for multiple words in title, walk one time $text...
function highlightText($title, $text) {
    global $highlight;

    $title = str_replace($highlight, '<span style="color: #FF0000">'. $highlight .'</span>', $title);

    $search  = explode(' ', $highlight);
    $nbWords = count($search);

    for ($i = 0; $i < $nbWords; $i++) {
        $tab = preg_split('`(<\w+.*?>)`', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($tab as $key => $val) {
            if (preg_match('`^<\w+`', $val))
                $tab[$key] = $val;
            else
                $tab[$key] = preg_replace(
                    '/'. preg_quote($search[$i], '/') .'/',
                    '<span style="color: #FF0000;"><b>\0</b></span>',
                    $val
                );
        }

        $text = implode($tab);
    }

    return array($title, $text);
}

/**
 * Format data of Forum topic message Author.
 *
 * @param array $topicMessage : Raw data of Forum topic message.
 * @return array : The Forum topic message Author data formated.
 */
function getAuthorInfo($topicMessage) {
    global $nuked, $forumId, $forumAdmin, $avatar_resize, $avatar_width;

    static $formatedAuthorInfo = array();

    // Check if author is already formated
    if (array_key_exists($topicMessage['auteur_id'], $formatedAuthorInfo))
        return $formatedAuthorInfo[$topicMessage['auteur_id']];

    $authorDefaultInfo = array(
        'status'        => 'unregistered',
        'name'          => $topicMessage['auteur'],
        'avatar'        => '<img src="modules/Forum/images/noAvatar.png" alt="" />',
        'userInfo'      => $topicMessage['auteur'],
        // TODO : Display a rank for visitor ?
        'rankName'      => '',
        'rankImage'     => '',
        'rankStyle'     => ''
    );

    // For anonymous message, return default values
    if ($topicMessage['auteur_id'] == '' || $topicMessage['authorName'] === null)
        return $authorDefaultInfo;

    //$authorInfo = array_merge($authorDefaultInfo, $authorInfo);
    $authorInfo = $authorDefaultInfo;
    $authorInfo['status'] = 'registered';

    // Get online status of author
    $dbrOnlineConnect = nkDB_selectOne(
        'SELECT user_id
        FROM '. NBCONNECTE_TABLE .'
        WHERE user_id = '. nkDB_escape($topicMessage['auteur_id'])
    );

    // Get game data of author game
    $dbrGame = nkDB_selectOne(
        'SELECT name AS gameName, icon AS gameIcon, pref_1 AS gamePref1, pref_2 AS gamePref2,
        pref_3 AS gamePref3, pref_4 AS gamePref4, pref_5 AS gamePref5
        FROM '. GAMES_TABLE .'
        WHERE id = '. $topicMessage['game']
    );

    if ($dbrGame) {
        $authorInfo = array_merge($authorInfo, $dbrGame);

        $authorInfo['gameName'] = nkHtmlEntities($authorInfo['gameName']);

        if ($authorInfo['gameIcon'] == '' || ! is_file($authorInfo['gameIcon']))
            $authorInfo['gameIcon'] = 'images/games/icon_nk.png';
    }

    // Get user game preference of author
    // TODO : Default game values ? Or use GAMES_PREFS_TABLE ?
    $dbrUserDetail = nkDB_selectOne(
        'SELECT pref_1 AS gameUserPref1, pref_2 AS gameUserPref2, pref_3 AS gameUserPref3,
        pref_4 AS gameUserPref4, pref_5 AS gameUserPref5
        FROM '. USER_DETAIL_TABLE .'
        WHERE user_id = '. nkDB_escape($topicMessage['auteur_id'])
    );

    if ($dbrUserDetail)
        $authorInfo = array_merge($authorInfo, array_map('nkHtmlEntities', $dbrUserDetail));

    // Get author rank data
    $authorRank = getUserRank($topicMessage, isModerator($forumId, $topicMessage['auteur_id']));

    if ($authorRank['title'] != '')
        $authorInfo['rankName'] = printSecuTags($authorRank['title']);

    $authorInfo['rankImage'] = $authorRank['image'];

    if ($authorRank['color'] != '')
        $authorInfo['rankStyle'] = 'style="color:#'. $authorRank['color'] .'"';

    // Set user info
    $authorInfo['userInfo'] =
        '<img src="images/flags/'. $topicMessage['country'] .'" alt="'. $topicMessage['country']
        . '" class="nkForumOnlineFlag" /><a href="index.php?file=Members&amp;op=detail&amp;autor='
        . urlencode($topicMessage['authorName']) .'">'. $topicMessage['authorName'] .'</a>';

    if ($topicMessage['auteur_id'] == $dbrOnlineConnect['user_id'])
        $authorInfo['userInfo'] .= '<div class="nkOnlineIcon" title="'. __('IS_ONLINE') .'"></div>';

    // Set user or default avatar
    if ($topicMessage['avatar'] != '') {
        if ($avatar_resize == 'off'
            || (stripos($topicMessage['avatar'], 'http://') !== false && $avatar_resize == 'local')
        )
            $style = 'style="border:0;"';
        else
            $style = 'style="border: 0; overflow: auto; max-width: '. $avatar_width .'px; width: expression(this.scrollWidth >= '. $avatar_width .'? \''. $avatar_width .'px\' : \'auto\');"';

        // TODO : Remove checkimg. Check only when user account is updated.
        $authorInfo['avatar'] = '<img src="'. checkimg($topicMessage['avatar']) .'" '. $style .'alt="" />';
    }

    // Add total user post
    $authorInfo['totalUserPost'] = __('MESSAGES') .' : '. $topicMessage['count'] .'<br />'. __('REGISTERED') .': ';

    // Valeur TRUE = Pas d'heure/minute.
    $authorInfo['totalUserPost'] .= nkDate($topicMessage['authorDate'], TRUE);

    // On détermine si le visiteur est un administrateur et on lui affiche l'IP du posteur
    if ($forumAdmin)
        $authorInfo['displayUserIp'] = _('IP') .' : '. $topicMessage['auteur_ip'];

    // Store author info
    $formatedAuthorInfo[$topicMessage['auteur_id']] = $authorInfo;

    return $authorInfo;
}

/**
 * Format data of Forum topic message.
 *
 * @param array $topicMessage : Raw data of Forum topic message.
 * @return array : The Forum topic message formated.
 */
function formatTopicMessage($topicMessage) {
    global $highlight;

    $topicMessage['titre'] = printSecuTags($topicMessage['titre']);

    if ($highlight != '') {
        list($topicMessage['titre'], $topicMessage['txt']) = highlightText(
            $topicMessage['titre'], $topicMessage['txt']
        );
    }

    return $topicMessage;
}

/**
 * Get next or previous Forum topic link.
 *
 * @param string $dir : The position of near Forum topic link. (ASC = next, DESC = previous)
 * @param int $lastPost : The timestamp of last Forum topic message posted in Forum.
 * @return string : The near Forum topic link or a empty string.
 */
function getNearForumTopicLink($dir, $lastPost) {
    global $forumId;

    if ($dir == 'DESC') {
        $comparisonOperator = '<';
        $cssClass           = 'arrowleft';
        $linkText           = __('LAST_THREAD');
    }
    else {
        $comparisonOperator = '>';
        $cssClass           = 'arrowright';
        $linkText           = __('NEXT_THREAD');
    }

    // Get near Forum topic link if exist
    $dbrNearForumTopic = nkDB_selectOne(
        'SELECT id
        FROM '. FORUM_THREADS_TABLE .'
        WHERE last_post '. $comparisonOperator .' '. $lastPost .'
        AND forum_id = '. $forumId,
        array('last_post'), $dir, 1
    );

    if ($dbrNearForumTopic && $dbrNearForumTopic['id'])
        return '<a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id='. $forumId
            . '&amp;thread_id='. $dbrNearForumTopic['id'] .'" class="nkButton icon '. $cssClass .'">'
            . $linkText .'</a>';

    return '';
}


$forumId    = (isset($_GET['forum_id'])) ? (int) $_GET['forum_id'] : 0;
$threadId   = (isset($_GET['thread_id'])) ? (int) $_GET['thread_id'] : 0;
$p          = (isset($_GET['p'])) ? max((int) $_GET['p'], 1) : 1;
$highlight  = (isset($_GET['highlight'])) ? trim($_GET['highlight']) : '';
$error      = false;

// Get current Forum data
$dbrCurrentForum = getForumData(
    'F.nom AS forumName, F.cat, F.level, F.niveau AS forumLevel, F.nbTopics,
    FC.nom AS catName, FC.niveau AS catLevel', 'forumId', $forumId
);

// Check Forum access, Forum category access and Forum exist
if (! $dbrCurrentForum) $error = __('FORUM_NO_EXIST');
if ($visiteur < $dbrCurrentForum['catLevel']) $error = __('NO_ACCESS_FORUM_CATEGORY');
if ($visiteur < $dbrCurrentForum['forumLevel']) $error = __('NO_ACCESS_FORUM');

if (! $error) {
    // Get current Forum topic data
    $dbrCurrentTopic = nkDB_selectOne(
        'SELECT titre, closed, annonce, last_post, auteur_id, sondage, nbReplies
        FROM '. FORUM_THREADS_TABLE .'
        WHERE id = '. $threadId
    );

    // Check if Forum topic exists
    if (! $dbrCurrentTopic) $error = __('TOPIC_NO_EXIST');
}

if ($error) {
    opentable();
    printNotification($error, 'error');
    closetable();
    return;
}

// Fonction message lu / non lu
if ($user) {
    $dbrUserForumRead = nkDB_selectOne(
        'SELECT user_id, thread_id, forum_id
        FROM '. FORUM_READ_TABLE .'
        WHERE user_id = '. nkDB_escape($user['id'])
    );

    if (! $dbrUserForumRead
        || strrpos($dbrUserForumRead['thread_id'], ','. $threadId .',') === false
        || strrpos($dbrUserForumRead['forum_id'], ','. $forumId .',') === false
    ) {
        $dbrCurrentForumThreadList = nkDB_selectMany(
            'SELECT id
            FROM '. FORUM_THREADS_TABLE .'
            WHERE forum_id = '. $forumId
        );

        $tid = substr($dbrUserForumRead['thread_id'], 1); // Thread ID
        $fid = substr($dbrUserForumRead['forum_id'], 1); // Forum ID

        if (strrpos($dbrUserForumRead['thread_id'], ','. $threadId .',') === false)
            $tid .= $threadId .',';

        $read = false;

        foreach ($dbrCurrentForumThreadList as $currentForumThread) {
            if (strrpos(','. $tid, ','. $currentForumThread['id'] .',') === false)
                $read = true;
        }

        if (strrpos($dbrUserForumRead['forum_id'], ','. $forumId .',') === false && $read === false)
            $fid .= $forumId .',';

        // TODO : Replace if read == true ?
        nkDB_replace(FORUM_READ_TABLE, array(
            'user_id'   => $user['id'],
            'thread_id' => $tid,
            'forum_id'  => $fid
        ));
    }
}

// Prepare data of Forum topic
$dbrCurrentForum['forumName']  = printSecuTags($dbrCurrentForum['forumName']);
$dbrCurrentForum['catName']    = printSecuTags($dbrCurrentForum['catName']);

$moderator  = isModerator($forumId);
$forumAdmin = $visiteur >= admin_mod('Forum') || $moderator;

$forumWriteLevel = $moderator
                || $dbrCurrentForum['level'] == 0
                || $visiteur >= $dbrCurrentForum['level'];

$dbrCurrentTopic['titre'] = printSecuTags($dbrCurrentTopic['titre']);
$dbrCurrentTopic['titre'] = nk_CSS($dbrCurrentTopic['titre']);

// Update view counter
nkDB_update(FORUM_THREADS_TABLE, array(
        'view' => array('view + 1', 'no-escape')
    ),
    'id = '. $threadId
);

// Get next and previous Forum topic link if exist
$next = $prev = '';

if ($dbrCurrentForum['nbTopics'] > 1) {
    $next = getNearForumTopicLink('ASC', $dbrCurrentTopic['last_post']);
    $prev = getNearForumTopicLink('DESC', $dbrCurrentTopic['last_post']);
}

// Prepare Forum topic breadcrumb
$breadcrumb = getForumBreadcrump(
    $dbrCurrentForum['catName'], $dbrCurrentForum['cat'],
    $dbrCurrentForum['forumName'], $forumId
);

// Get pagination
$pagination = '';
$nbMessages = $dbrCurrentTopic['nbReplies'] + 1;

if ($nbMessages > $nuked['mess_forum_page']) {
    $url = 'index.php?file=Forum&amp;page=viewtopic&amp;forum_id='. $forumId .'&amp;thread_id='. $threadId;

    if ($highlight != '')
        $url .= '&amp;highlight='. urlencode($highlight);

    $pagination = number($nbMessages, $nuked['mess_forum_page'], $url, true);
}

// Get Forum topic poll data
$dbrTopicPoll = $userPolled = $dbrTopicPollOptions = null;

if ($dbrCurrentTopic['sondage'] == 1) {
    $dbrTopicPoll = nkDB_selectOne(
        'SELECT id, title
        FROM '. FORUM_POLL_TABLE .'
        WHERE thread_id = '. $threadId
    );

    if ($dbrTopicPoll) {
        $userPolled = nkDB_totalNumRows(
            'FROM '. FORUM_VOTE_TABLE .'
            WHERE poll_id = '. $dbrTopicPoll['id'] .'
            AND author_id = '. nkDB_escape($user['id'])
        );

        if ($user && $userPolled > 0 || (isset($_GET['vote']) && $_GET['vote'] == 'view'))
            $fields = 'option_vote, option_text';
        else
            $fields = 'id, option_text';

        $dbrTopicPollOptions = nkDB_selectMany(
            'SELECT '. $fields .'
            FROM '. FORUM_OPTIONS_TABLE .'
            WHERE poll_id = '. $dbrTopicPoll['id'],
            array('id')
        );
    }
}

        $userSocialFields = nkUserSocial_getActiveFields();
        $userSocialFields = ($userSocialFields) ? ', U.'. implode(', U.', $userSocialFields) : '';

// U.url AS homepage
// TODO : Add only user social fields of user database table
// Get Forum topic messages list
$dbrTopicMessages = nkDB_selectMany(
    'SELECT FM.id, FM.titre, FM.auteur, FM.auteur_id, FM.auteur_ip, FM.txt, FM.date,
    FM.edition, FM.usersig, FM.file, U.pseudo AS authorName, U.niveau, U.rang, U.avatar,
    U.signature, U.date AS authorDate, U.country, U.count, U.game'. $userSocialFields .'
    FROM '. FORUM_MESSAGES_TABLE .' AS FM
    LEFT JOIN '. USER_TABLE .' AS U
    ON U.id = FM.auteur_id
    WHERE FM.thread_id = '. $threadId,
    array('FM.date'), 'ASC',
    $nuked['mess_forum_page'], ($p - 1) * $nuked['mess_forum_page']
);

// Check if user have enabled email notification for Forum topic reply
$notify = null;

if ($user && $user['id'] != '') {
    $notify = nkDB_totalNumRows(
        'FROM '. FORUM_MESSAGES_TABLE .'
        WHERE thread_id = '. $threadId .' AND emailnotify = 1
        AND auteur_id = '. nkDB_escape($user['id'])
    );
}

// Secure highlight string
if ($highlight != '')
    $highlight = printSecuTags($highlight);

// Set page title
nkTemplate_setTitle(__('FORUM') .' - '. $dbrCurrentTopic['titre']);

// Display post list of Forum topic
opentable();

echo applyTemplate('modules/Forum/viewTopic', array(
    'nuked'                 => $nuked,
    'user'                  => $user,
    'visiteur'              => $visiteur,
    'theme'                 => $theme,
    'forumId'               => $forumId,
    'threadId'              => $threadId,
    'p'                     => $p,
    'breadcrumb'            => $breadcrumb,
    'prev'                  => $prev,
    'next'                  => $next,
    'currentTopic'          => $dbrCurrentTopic,
    'pagination'            => $pagination,
    'currentForum'          => $dbrCurrentForum,
    'administrator'         => $forumAdmin,
    'moderator'             => $moderator,
    'forumWriteLevel'       => $forumWriteLevel,
    'topicPoll'             => $dbrTopicPoll,
    'userPolled'            => $userPolled,
    'topicPollOptionsList'  => $dbrTopicPollOptions,
    'topicMessagesList'     => $dbrTopicMessages,
    'notify'                => $notify
));

closetable();

?>