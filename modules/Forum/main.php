<?php
/**
 * main.php
 *
 * Frontend of Forum module - Display main Forum page
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

global $user, $nuked, $visiteur, $rankField;

require_once 'modules/Forum/core.php';


/**
 * Format Forum row data.
 *
 * @param array $forum : The raw Forum row data.
 * @return array : The Forum row data formated.
 */
function formatForumRow($forum) {
    global $nuked;

    $forum['forumName']  = printSecuTags($forum['forumName']);
    $forum['readStatus'] = getForumReadStatusImg($forum['id'], $forum['nbTopics']);

    if ($nuked['forum_display_modos'] == 'on')
        $forum['moderatorsList'] = formatModeratorsList($forum['moderateurs']);

    if ($forum['nbMessages'] > 0)
        $forum['lastMessage'] = formatLastForumMsg($forum['id']);
    else
        $forum['lastMessage'] = false;

    return $forum;
}

/**
 * Format last Forum message data.
 *
 * @param int $forumId : The Forum ID.
 * @return array : The last Forum message formated data.
 */
function formatLastForumMsg($forumId) {
    global $nuked, $rankField;

    // Get last Forum message data
    // TODO : country field unused
    $dbrLastForumMsg = getLastForumMessageData('forum_id', $forumId,
        'FM.id, FM.titre, FM.thread_id, FM.date, FM.auteur, U.pseudo, U.avatar, U.country'. $rankField
    );

    if (! $dbrLastForumMsg) return false;

    // Set last Forum message author, formated date and full title
    $lastMsgData = array(
        'author'    => nkNickname($dbrLastForumMsg),
        'date'      => formatForumMessageDate($dbrLastForumMsg['date']),
        'title'     => printSecuTags($dbrLastForumMsg['titre'])
    );

    // Set default avatar if undefined
    if ($dbrLastForumMsg['avatar'] != '')
        $lastMsgData['authorAvatar'] = $dbrLastForumMsg['avatar'];
    else
        $lastMsgData['authorAvatar'] = 'modules/Forum/images/noAvatar.png';

    // Clean and truncate title of last Forum message if needed
    $lastMsgData['cleanedTitle'] = str_replace('RE : ', '', $dbrLastForumMsg['titre']);

    if (strlen($lastMsgData['cleanedTitle']) > 20)
        $lastMsgData['cleanedTitle'] = printSecuTags(substr($lastMsgData['cleanedTitle'], 0, 20)) .'...';
    else
        $lastMsgData['cleanedTitle'] = printSecuTags($lastMsgData['cleanedTitle']);

    // Add url of last Forum message
    list($lastMsgData['url']) = getForumMessageUrl(
        $forumId, $dbrLastForumMsg['thread_id'], $dbrLastForumMsg['id']
    );

    return $lastMsgData;
}

/**
 * Get Forum read status image of Forum.
 *
 * @param int $forumId : The Forum ID.
 * @param int $nbTopicsInForum : The number of topics in Forum.
 * @return string : The image path of Forum read status.
 */
function getForumReadStatusImg($forumId, $nbTopicsInForum) {
    global $user;

    if ($user) {
        $dbrForumRead = nkDB_selectOne(
            'SELECT forum_id
            FROM '. FORUM_READ_TABLE .'
            WHERE user_id = '. nkDB_escape($user['id']) .'
            AND forum_id LIKE \'%,'. nkDB_escape($forumId, true) .',%\''
        );

        if ($nbTopicsInForum > 0 && strrpos($dbrForumRead['forum_id'], ','. $forumId .',') === false)
            return 'modules/Forum/images/forum_new.png';
    }

    return 'modules/Forum/images/forum.png';
}


$catId   = (isset($_GET['cat'])) ? (int) $_GET['cat'] : 0;
$error   = false;
$catName = '';

// Get current Forum category if defined and check it if needed
if ($catId > 0) {
    $dbrCurrentForumCat = nkDB_selectOne(
        'SELECT nom
        FROM '. FORUM_CAT_TABLE .'
        WHERE id = '. $catId
    );

    if (! $dbrCurrentForumCat)
        $error = _NOFORUMCATEXIST;
    else
        $catName = $dbrCurrentForumCat['nom'];
}

if (! $error) {
    // Get Forum list sorted by Forum category or Forum list of one Forum category
    $dbrForumList = getForumData(
        'F.id, F.nom AS forumName, F.comment, F.cat, F.image AS forumImage, F.moderateurs,
        F.nbTopics, F.nbMessages,
        FC.nom As catName, FC.image AS catImage, FC.niveau AS catLevel',
        'catId', $catId
    );

    // Check Forum category access and Forum category exist
    if (! $dbrForumList) $error = _NOACCESSFORUMCAT;
}

if ($error) {
    opentable();
    printNotification($error, 'error');
    closetable();
    return;
}

// Prepare breadcrumb
$breadcrumb = getForumBreadcrump($catName, $catId);

// Get Forum title and Forum description
if ($nuked['forum_title'] != '') {
    $forumTitle = $nuked['forum_title'];
    $forumDesc  = $nuked['forum_desc'];
}
else {
    $forumTitle = $nuked['name'];
    $forumDesc  = $nuked['slogan'];
}

// Count all Forum message and all user
$nbTotalMessages    = nkDB_totalNumRows('FROM '. FORUM_MESSAGES_TABLE);
$nbTotalUsers       = nkDB_totalNumRows('FROM '. USER_TABLE .' WHERE niveau > 0');

// Get last member username
$dbrLastUser = nkDB_selectOne(
    'SELECT pseudo
    FROM '. USER_TABLE .' WHERE niveau > 0',
    array('date'), 'DESC', 1
);

// Prepare rank legend
if ($nuked['forum_user_details'] == 'on') {
    $teamRank       = getTeamRank();
    $rankField      = ', U.rang';
    $teamRankList   = array_column($teamRank, 'formated');
    $teamRankList   = ($teamRankList) ? implode(',', $teamRankList) : '<em>'. __('NONE') .'</em>';
}
else {
    $rankField      = '';
    $teamRankList   = '';
}

// Prepare online members info
$onlineList = array();

$dbrOnline = nkDB_selectMany(
    'SELECT U.pseudo, U.country'. $rankField .'
    FROM '. NBCONNECTE_TABLE .' AS NC
    INNER JOIN '. USER_TABLE .' AS U
    ON NC.user_id = U.id
    WHERE NC.type > 0',
    array('NC.date')
);

foreach ($dbrOnline as $online) {
    if ($nuked['forum_user_details'] == 'on' && array_key_exists($online['rang'], $teamRank))
        $style = ' style="color: #'. $teamRank[$online['rang']]['color'] .';"';
    else
        $style = '';

    $onlineList[] = '<img src="images/flags/'. $online['country'] .'" alt="'. $online['country'] .'" class="nkForumOnlineFlag" />'
        . '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($online['pseudo']) .'"'. $style .'>'
        . $online['pseudo'] .'</a>';
}

// Prepare birthday message and user birthday list
$birthdayMessage = null;

if ($nuked['forum_birthday'] == 'on') {
    $currentYear    = date('Y');
    $currentMonth   = date('n');
    $currentDay     = date('j');

    $dbrUserDetail = nkDB_selectMany(
        'SELECT UD.age, U.pseudo'. $rankField .'
        FROM '. USER_DETAIL_TABLE .' AS UD
        INNER JOIN '. USER_TABLE .' AS U
        ON UD.user_id = U.id
        WHERE U.niveau > 0 AND UD.age LIKE \''. $currentDay .'/'. $currentMonth .'/%\''
    );

    $birthdayList = array();

    foreach ($dbrUserDetail as $userDetail) {
        list($birthdayDay, $birthdayMonth, $birthdayYear) = explode('/', $userDetail['age']);

        if ($currentDay == $birthdayDay && $currentMonth == $birthdayMonth) {
            //$userDetail['pseudo'] = stripslashes($userDetail['pseudo']);

            if ($nuked['forum_user_details'] == 'on' && array_key_exists($userDetail['rang'], $teamRank))
                $style = ' style="color: #'. $teamRank[$userDetail['rang']]['color'] .';"';
            else
                $style = '';

            $birthdayList[] = '<a href="index.php?file=Members&amp;op=detail&amp;autor='
                . urlencode($userDetail['pseudo']) .'"'. $style .'><strong>'. $userDetail['pseudo']
                . '</strong></a> ('. ($currentYear - $birthdayYear) .' '. _ANS .')';
        }
    }

    $nbBirthday = count($birthdayList);

    if ($nbBirthday == 0)
        $birthdayMessage = _NOBIRTHDAY;
    elseif ($nbBirthday == 1)
        $birthdayMessage = _ONEBIRTHDAY .'&nbsp;';
    else
        $birthdayMessage = _THEREARE2 .'&nbsp;'. $nbBirthday .'&nbsp;'. _MANYBIRTHDAY .'&nbsp;';

    $birthdayMessage .= implode(',', $birthdayList);
}

// Set page title
$title = _NAVFORUM;

if ($catName != '')
    $title .= ' - '. printSecuTags($catName);

nkTemplate_setTitle($title);

// Display main Forum list
echo applyTemplate('modules/Forum/main', array(
    'nuked'             => $nuked,
    'user'              => $user,
    'forumTitle'        => $forumTitle,
    'forumDesc'         => $forumDesc,
    'breadcrumb'        => $breadcrumb,
    'forumList'         => $dbrForumList,
    'nbTotalMessages'   => $nbTotalMessages,
    'nbTotalUsers'      => $nbTotalUsers,
    'lastUser'          => $dbrLastUser['pseudo'],
    'onlineStats'       => nbvisiteur(),
    'onlineList'        => $onlineList,
    'teamRankList'      => $teamRankList,
    'birthdayMessage'   => $birthdayMessage
));

?>