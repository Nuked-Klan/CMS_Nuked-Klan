<?php 
/**
 * viewtopic.php
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

$captcha = initCaptcha();

global $nuked, $user, $language, $theme, $visiteur;

include 'modules/Forum/template.php';
require_once 'modules/Forum/core.php';


/**
 * Highlight researched string in title and message of Forum topic.
 *
 * @param string $highlight : The researched string.
 * @param string $title : The message title.
 * @param string $text : The message content
 * @return array : A numerical indexed array with title and message content highlighted.
 */
function highlightText($highlight, $title, $text) {
    $highlight = trim($highlight);
    $highlight = printSecuTags($highlight);

    $title = str_replace($highlight, '<span style="color: #FF0000">'. $highlight .'</span>', $title);

    $search = explode(' ', $highlight);
    $nbWord = count($search);

    for($i = 0; $i < $nbWord; $i++) {
        $tab = preg_split('`(<\w+.*?>)`', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($tab as $key => $val) {
            if (preg_match('`^<\w+`', $val))
                $tab[$key] = $val;
            else
                $tab[$key] = preg_replace('/'. preg_quote($search[$i], '/') .'/', '<span style="color: #FF0000;"><b>\0</b></span>', $val);
        }

        $text = implode($tab);
    }

    return array($title, $text);
}

/**
 * Format data of Forum topic message Author.
 *
 * @param array $topicMessage : Raw data of Forum topic message.
 * @param bool $administrator : If user have administrator / moderator right.
 * @return array : The Forum topic message Author data formated.
 */
function getAuthorInfo($topicMessage, $administrator) {
    $authorDefaultInfo = array(
        'status'        => 'unregistered',
        'name'          => $topicMessage['auteur'],
        'avatar'        => '<img src="modules/Forum/images/noAvatar.png" alt="" />',
        'userInfo'      => '',
        'totalUserPost' => '',
        'displayUserIp' => '',
        'gameName'      => '',
        'gameIcon'      => '',
        'gamePref1'     => '',
        'gamePref2'     => '',
        'gamePref3'     => '',
        'gamePref4'     => '',
        'gamePref5'     => '',
        'rankName'      => '',
        'rankImage'     => '',
        'rankStyle'     => ''
    );

    // For anonymous message, return default values
    if ($topicMessage['auteur_id'] == '')
        return $authorDefaultInfo;

    // Get user data of author
    $authorInfo = nkDB_selectOne(
        'SELECT pseudo AS name, niveau, rang, avatar, signature, date, email,
        icq, msn, aim, yim, xfire, facebook, origin, steam, twitter, skype,
        url AS homepage, country, count, game
        FROM '. USER_TABLE .'
        WHERE id = '. nkDB_escape($topicMessage['auteur_id'])
    );

    // For deleted user, return default values
    if (! $authorInfo)
        return $authorDefaultInfo;

    $authorInfo = array_merge($authorDefaultInfo, $authorInfo);
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
        WHERE id = '. $authorInfo['game']
    );

    if ($dbrGame !== false) {
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

    if ($dbrUserDetail !== false)
        $authorInfo = array_merge($authorInfo, array_map('nkHtmlEntities', $dbrUserDetail));

    // Get author team rank if enabled
    if ($authorInfo['rang'] > 0 && $nuked['forum_rank_team'] == 'on') {
        $dbrTeamRank = nkDB_selectOne(
            'SELECT titre AS rankName, image AS rankImage, couleur AS rankColor
            FROM '. TEAM_RANK_TABLE .'
            WHERE id = '. $authorInfo['rang']
        );

        $authorInfo = array_merge($authorInfo, $dbrTeamRank);

        $authorInfo['rankName'] = printSecuTags($authorInfo['rankName']);
    }
    // ...Or use forum rank
    else {
        $order = $dir = $limit = false;

        if ($authorInfo['niveau'] >= admin_mod('Forum'))
            $whereClause = 'type = 2';
        else if ($dbrCurrentForum['moderateurs'] != '' && strpos($dbrCurrentForum['moderateurs'], $topicMessage['auteur_id']) !== false)
            $whereClause = 'type = 1';
        else {
            $whereClause = $authorInfo['count'] .' >= post AND type = 0';
            $order = array('post');
            $dir = 'DESC';
            $limit = 1;
        }

        $dbrForumRank = nkDB_selectOne(
            'SELECT nom AS rankName, image AS rankImage
            FROM '. FORUM_RANK_TABLE .'
            WHERE '. $whereClause,
            $order, $dir, $limit
        );

        $authorInfo = array_merge($authorInfo, $dbrForumRank);

        $authorInfo['rankName'] = printSecuTags($authorInfo['rankName']);
    }

    if ($authorInfo['rankImage'] != '')
        $authorInfo['rankImage'] = '<img src="'. $authorInfo['rankImage'] .'" alt="" />';

    if ($authorInfo['rang'] > 0 && $nuked['forum_user_details'] == 'on')
        $authorInfo['rankStyle'] = 'style="color:#'. $authorInfo['rankColor'] .'"';
    else
        $authorInfo['rankStyle'] = '';

    // Set user info
    $authorInfo['userInfo'] =
        '<img src="images/flags/'. $authorInfo['country'] .'" alt="'. $authorInfo['country'] .'" class="nkForumOnlineFlag" />'
        . '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($authorInfo['name']) .'">'
        . $authorInfo['name'] .'</a>';

    if ($topicMessage['auteur_id'] == $dbrOnlineConnect['user_id'])
        $authorInfo['userInfo'] .= '<div class="nkOnlineIcon" title="'. _ISONLINE .'"></div>';

    // Set user or default avatar
    if ($authorInfo['avatar'] != '') {
        if ($avatar_resize == 'off'
            || (stripos($authorInfo['avatar'], 'http://') !== false && $avatar_resize == 'local')
        )
            $style = 'style="border:0;"';
        else
            $style = 'style="border: 0; overflow: auto; max-width: '. $avatar_width .'px; width: expression(this.scrollWidth >= '. $avatar_width .'? \''. $avatar_width .'px\' : \'auto\');"';

        $authorInfo['avatar'] = '<img src="'. checkimg($authorInfo['avatar']) .'" '. $style .'alt="" />';
    }
    else {
        $authorInfo['avatar'] = '<img src="modules/Forum/images/noAvatar.png" alt="" />';
    }

    // Add total user post
    $authorInfo['totalUserPost'] = _MESSAGES .' : '. $authorInfo['count'] .'<br />'. _REGISTERED .': ';

    // Valeur TRUE = Pas d'heure/minute.
    $authorInfo['totalUserPost'] .= nkDate($authorInfo['date'], TRUE);

    //On détermine si le visiteur est un administrateur et on lui affiche l'IP du posteur
    if ($administrator)
        $authorInfo['displayUserIp'] = _IP .' : '. $topicMessage['auteur_ip'];
    else
        $authorInfo['displayUserIp'] = '';

    return $authorInfo;
}

/**
 * Format data of Forum topic message.
 *
 * @param array $topicMessage : Raw data of Forum topic message.
 * @param bool $administrator : If user have administrator / moderator right.
 * @param int $forumId : The forum ID.
 * @param int $threadId : The topic ID.
 * @return array : The Forum topic message formated.
 */
function formatTopicMessage($topicMessage, $administrator, $forumId, $threadId) {
    global $user;

    $topicMessage['titre'] = printSecuTags($topicMessage['titre']);

    if (isset($_REQUEST['highlight']) && $_REQUEST['highlight'] != '') {
        list($topicMessage['titre'], $topicMessage['txt']) = highlightText(
            $_REQUEST['highlight'], $topicMessage['titre'], $topicMessage['txt']
        );
    }

    if ($topicMessage['file'] != '' && is_file('upload/Forum/'. $topicMessage['file'])) {
        $fileUrl = 'upload/Forum/'. $topicMessage['file'];

        if ($user && $topicMessage['auteur_id'] == $user['id'] || $administrator)
            $del = '&nbsp;<a href="index.php?file=Forum&amp;op=del_file&amp;forum_id='. $forumId .'&amp;thread_id='. $threadId .'&amp;mess_id='. $topicMessage['id'] .'" class="nkButton icon trash danger">'. _DELFILE .'</a>';
        else
            $del = '';

        $roundedFilesize = ceil((int) filesize($fileUrl) / 1024);

        $topicMessage['joinedFile'] = '<div class="nkForumViewAttachedFile"><strong><a href="'. $fileUrl .'" onclick="window.open(this.href); return false;" title="'. _DOWNLOADFILE .'">'. $topicMessage['file'] .'</a> ('. $roundedFilesize .' Ko)'. $del .'</strong></div>';

    }
    else {
        $topicMessage['joinedFile'] = '';
    }

    return $topicMessage;
}

$forumId    = (isset($_REQUEST['forum_id'])) ? (int) $_REQUEST['forum_id'] : 0;
$threadId   = (isset($_REQUEST['thread_id'])) ? (int) $_REQUEST['thread_id'] : 0;
$p          = (isset($_REQUEST['p'])) ? (int) $_REQUEST['p'] : 1;

// Get current Forum data
$dbrCurrentForum = getForumData(
    'F.nom AS forumName, F.moderateurs, F.cat, F.level, F.niveau AS forumLevel,
    FC.nom AS catName, FC.niveau AS catLevel', 'forumId', $forumId
);

// Check forum access, forum category access and forum exist
$error = false;
if (! $dbrCurrentForum) $error = _NOFORUMEXIST;
if ($visiteur < $dbrCurrentForum['catLevel']) $error = _NOACCESSFORUMCAT;
if ($visiteur < $dbrCurrentForum['forumLevel']) $error = _NOACCESSFORUM;

// Check if topic exists
if (! $error) {
    // Get current Forum topic data
    $dbrCurrentTopic = nkDB_selectOne(
        'SELECT titre, closed, annonce, last_post, auteur_id, sondage
        FROM '. FORUM_THREADS_TABLE .'
        WHERE id = '. $threadId
    );

    if (! $dbrCurrentTopic) $error = _NOTOPICEXIST;
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

$dbrCurrentForum['forumName']  = printSecuTags($dbrCurrentForum['forumName']);
$dbrCurrentForum['catName']    = printSecuTags($dbrCurrentForum['catName']);


$moderator      = isModerator($dbrCurrentForum['moderateurs']);
$administrator  = $visiteur >= admin_mod('Forum') || $moderator;

$dbrCurrentTopic['titre'] = printSecuTags($dbrCurrentTopic['titre']);
$dbrCurrentTopic['titre'] = nk_CSS($dbrCurrentTopic['titre']);

// Update view counter
nkDB_update(FORUM_THREADS_TABLE, array(
        'view' => array('view + 1', 'no-escape')
    ),
    'id = '. $threadId
);

$next = $prev = '';
$topicForumUrl = 'index.php?file=Forum&amp;page=viewtopic&amp;forum_id='. $forumId;

// Get next topic link if exist
$dbrNextTopic = nkDB_selectOne(
    'SELECT id
    FROM '. FORUM_THREADS_TABLE .'
    WHERE last_post > '. $dbrCurrentTopic['last_post'] .'
    AND forum_id = '. $forumId,
    array('last_post'), 'ASC', 1
);

if (isset($dbrNextTopic['id']))
    $next = '<a href="'. $topicForumUrl .'&amp;thread_id=' . $dbrNextTopic['id'] .'" class="nkButton icon arrowright">'. _NEXTTHREAD .'</a>';

// Get last topic link if exist
$dbrLastTopic = nkDB_selectOne(
    'SELECT id
    FROM '. FORUM_THREADS_TABLE .'
    WHERE last_post < '. $dbrCurrentTopic['last_post'] .'
    AND forum_id = '. $forumId,
    array('last_post'), 'DESC', 1
);

if (isset($dbrLastTopic['id']))
    $prev = '<a href="'. $topicForumUrl .'&amp;thread_id='. $dbrLastTopic['id'] .'" class="nkButton icon arrowleft">'. _LASTTHREAD .'</a>';


// Prepare Forum breadcrumb
$breadcrumb = getForumBreadcrump(
    $dbrCurrentForum['catName'], $dbrCurrentForum['cat'],
    $dbrCurrentForum['forumName'], $forumId
);

//Détection du nombre de pages
$count = nkDB_totalNumRows('FROM '. FORUM_MESSAGES_TABLE .' WHERE thread_id = '. $threadId);

$start = $p * $nuked['mess_forum_page'] - $nuked['mess_forum_page'];

// Get pagination
$url_page = $topicForumUrl .'&amp;thread_id='. $threadId;
$pagination = '';

if (isset($_REQUEST['highlight']) && $_REQUEST['highlight'] != '')
    $url_page .= '&amp;highlight='. urlencode($_REQUEST['highlight']);


if ($count > $nuked['mess_forum_page'])
    $pagination = number($count, $nuked['mess_forum_page'], $url_page, true);

// Get topic poll data
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
            WHERE poll_id = '. $dbrTopicPoll['id'] .' AND option_text != \'\'',
            array('id')
        );
    }
}

// Get topic messages list
$dbrTopicMessages = nkDB_selectMany(
    'SELECT id, titre, auteur, auteur_id, auteur_ip, txt, date, edition, usersig, file
    FROM '. FORUM_MESSAGES_TABLE .'
    WHERE thread_id = '. $threadId,
    array('date'), 'ASC', $nuked['mess_forum_page'], $start
);

$notify = null;

if ($user['id'] != '') {
    $notify = nkDB_totalNumRows(
        'FROM '. FORUM_MESSAGES_TABLE .'
        WHERE thread_id = '. $threadId .' AND emailnotify = 1
        AND auteur_id = '. nkDB_escape($user['id'])
    );
}

opentable();

echo applyTemplate('modules/Forum/viewTopic', array(
    'theme'                 => $theme,
    'forumId'               => $forumId,
    'threadId'              => $threadId,
    'breadcrumb'            => $breadcrumb,
    'prev'                  => $prev,
    'next'                  => $next,
    'dbrCurrentTopic'       => $dbrCurrentTopic,
    'pagination'            => $pagination,
    'dbrCurrentForum'       => $dbrCurrentForum,
    'visiteur'              => $visiteur,
    'user'                  => $user,
    'nuked'                 => $nuked,
    'administrator'         => $administrator,
    'moderator'             => $moderator,
    'dbrTopicPoll'          => $dbrTopicPoll,
    'userPolled'            => $userPolled,
    'dbrTopicPollOptions'   => $dbrTopicPollOptions,
    'dbrTopicMessages'      => $dbrTopicMessages,
    'notify'                => $notify
));

closetable();

?>