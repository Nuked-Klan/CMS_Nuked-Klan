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

global $user, $nuked, $visiteur;

require_once 'modules/Forum/core.php';


/**
 * Get last message data.
 *
 * @param int $forumId : The forum ID.
 * @return array : The last message formated data.
 */
function getLastMessageInForum($forumId) {
    global $nuked;

    $lastMessageData = array();

    if ($nuked['forum_user_details'] == 'on') {
        $teamRank   = getTeamRank();
        $field      = ', U.rang';
    }
    else
        $field = '';

    $dbrForumMessage = nkDB_selectOne(
        'SELECT FM.id, FM.titre, FM.thread_id, FM.date, FM.auteur,
        U.pseudo, U.avatar, U.country'. $field .'
        FROM '. FORUM_MESSAGES_TABLE .' AS FM
        INNER JOIN '. USER_TABLE .' AS U
        ON U.id = FM.auteur_id
        WHERE FM.forum_id = '. nkDB_escape($forumId),
        array('FM.id'), 'DESC', 1
    );

    if ($dbrForumMessage['pseudo'] != '') {
        if ($nuked['forum_user_details'] == 'on' && array_key_exists($dbrForumMessage['rang'], $teamRank))
            $style = ' style="color: #'. $teamRank[$dbrForumMessage['rang']]['color'] .';"';
        else
            $style = '';

        $lastMessageData['author'] = '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($dbrForumMessage['pseudo']) .'" '. $style .'>'. $dbrForumMessage['pseudo'] .'</a>';

        if ($dbrForumMessage['avatar'] != '')
            $lastMessageData['authorAvatar'] = $dbrForumMessage['avatar'];
        else
            $lastMessageData['authorAvatar'] = 'modules/Forum/images/noAvatar.png';
    }
    else {
        $lastMessageData['author']          = nk_CSS($dbrForumMessage['auteur']);
        $lastMessageData['authorAvatar']    = 'modules/Forum/images/noAvatar.png';
    }

    // Formatage de la date
    $lastMessageData['date'] = formatForumMessageDate($dbrForumMessage['date']);

    // Lien en image vers le message
    $cleanTopicTitle = str_replace('RE : ', '', $dbrForumMessage['titre']);

    if (strlen($cleanTopicTitle) > 20)
        $cleanTopicTitle = substr($cleanTopicTitle, 0, 20) .'...';

    // Construction du lien vers le post
    $link_post = getLastMessageUrl($forumId, $dbrForumMessage['thread_id'], $dbrForumMessage['id']);

    $lastMessageData['title'] = '<a href="'. $link_post .'" title="'. $dbrForumMessage['titre'] .'"><img style="border: 0;" src="modules/Forum/images/icon_latest_reply.png" class="nkForumAlignImg" alt="" title="'. _SEELASTPOST .'" />&nbsp;'. $cleanTopicTitle .'</a>';

    return $lastMessageData;
}

/**
 * Get Forum read status image of Forum.
 *
 * @param int $forumId : The forum ID.
 * @param int $nbThreadInForum : The number of thread in Forum.
 * @return string : The image path of forum read status.
 */
function getImgForumReadStatus($forumId, $nbThreadInForum) {
    global $user;

    if (! $user)
        return 'modules/Forum/images/forum.png';

    $dbrForumRead = nkDB_selectOne(
        'SELECT forum_id
        FROM '. FORUM_READ_TABLE .'
        WHERE user_id = '. nkDB_escape($user['id']) .'
        AND forum_id LIKE \'%,'. nkDB_escape($forumId, true) .',%\''
    );

    if ($nbThreadInForum > 0 && strrpos($dbrForumRead['forum_id'], ','. $forumId .',') === false)
        return 'modules/Forum/images/forum_new.png';
    else
        return 'modules/Forum/images/forum.png';
}


// Get forum title and forum description
if ($nuked['forum_title'] != '') {
    $forumTitle = $nuked['forum_title'];
    $forumDesc  = $nuked['forum_desc'];
}
else {
    $forumTitle = $nuked['name'];
    $forumDesc  = $nuked['slogan'];
}

// Prepare breadcrumb
$breadcrumb = '';

if (! empty($_GET['cat'])) {
    $dbrForumCat = nkDB_selectOne(
        'SELECT nom
        FROM '. FORUM_CAT_TABLE .'
        WHERE id = '. nkDB_escape($_GET['cat'])
    );

    if (isset($dbrForumCat['nom']))
        $breadcrumb = '-> <strong>'. printSecuTags($dbrForumCat['nom']) .'</strong>';
}

// Get last visit date
if ($user && $user['lastUsed'] != '')
    $lastVisitMessage = _LASTVISIT .' : '. nkDate($user['lastUsed']);
else
    $lastVisitMessage = '';

// Get Forum list
if (! empty($_GET['cat'])) {
    $dbrForum = nkDB_selectMany(
        'SELECT F.id, F.nom AS forumName, F.comment, F.cat, F.image AS forumImage, F.moderateurs, F.nbThread, F.nbMessage,
        FC.nom As catName, FC.image AS catImage
        FROM '. FORUM_TABLE .' AS F
        INNER JOIN '. FORUM_CAT_TABLE .' AS FC
        ON FC.id = F.cat
        WHERE '. $visiteur .' >= FC.niveau AND '. $visiteur .' >= F.niveau AND FC.id = '. nkDB_escape($_GET['cat']),
        array('F.ordre', 'F.nom')
    );
}
else {
    $dbrForum = nkDB_selectMany(
        'SELECT F.id, F.nom AS forumName, F.comment, F.cat, F.image AS forumImage, F.moderateurs, F.nbThread, F.nbMessage,
        FC.nom As catName, FC.image AS catImage
        FROM '. FORUM_TABLE .' AS F
        INNER JOIN '. FORUM_CAT_TABLE .' AS FC
        ON FC.id = F.cat
        WHERE '. $visiteur .' >= FC.niveau AND '. $visiteur .' >= F.niveau',
        array('FC.ordre', 'FC.nom', 'F.ordre', 'F.nom')
    );
}

// Count all Forum message and all user
$nbTotalMessages    = nkDB_totalNumRows('FROM '. FORUM_MESSAGES_TABLE);
$nbTotalUsers       = nkDB_totalNumRows('FROM '. USER_TABLE .'  WHERE niveau = 0');

// Get last member username
$dbrUser = nkDB_selectOne(
    'SELECT pseudo
    FROM '. USER_TABLE,
    array('date'), 'DESC', 1
);

$lastUser = $dbrUser['pseudo'];

// Prepare online members info
$onlineList = array();

if ($nuked['forum_user_details'] == 'on') {
    $teamRank   = getTeamRank();
    $field      = ', U.rang';
}
else
    $field = '';

$dbrConnected = nkDB_selectMany(
    'SELECT U.pseudo, U.country'. $field .'
    FROM '. NBCONNECTE_TABLE .' AS NC
    INNER JOIN '. USER_TABLE .' AS U
    ON NC.user_id = U.id
    WHERE NC.type > 0',
    array('NC.date')
);

foreach ($dbrConnected as $connected) {
    if ($nuked['forum_user_details'] == 'on' && array_key_exists($connected['rang'], $teamRank))
        $style = ' style="color: #'. $teamRank[$connected['rang']]['color'] .';"';
    else
        $style = '';

    $onlineList[] = '<img src="images/flags/'. $connected['country'] .'" alt="'. $connected['country'] .'" class="nkForumOnlineFlag" />'
        . '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($connected['pseudo']) .'"'. $style .'>'
        . $connected['pseudo'] .'</a>';
}

// Prepare rank legend
$teamRankList = ($nuked['forum_user_details'] == 'on') ? array_column($teamRank, 'formated') : array();

// Prepare birthday message and user birthday list
$birthdayMessage = null;

if ($nuked['forum_birthday'] == 'on') {
    $currentYear    = date('Y');
    $currentMonth   = date('n');
    $currentDay     = date('j');

    $field = ($nuked['forum_user_details'] == 'on') ? ', U.rang' : '';

    $dbrUserDetail = nkDB_selectMany(
        'SELECT UD.age, U.pseudo'. $field .'
        FROM '. USER_DETAIL_TABLE .' AS UD
        INNER JOIN '. USER_TABLE .' AS U
        ON UD.user_id = U.id
        WHERE U.niveau > 0 AND UD.age LIKE \''. $currentDay .'/'. $currentMonth .'/%\''
    );

    $birthdayList = array();

    foreach ($dbrUserDetail as $userDetail) {
        list($bDay, $bMonth, $bYear) = explode('/', $userDetail['age']);

        if ($currentDay == $bDay && $currentMonth == $bMonth) {
            //$userDetail['pseudo'] = stripslashes($userDetail['pseudo']);

            if ($nuked['forum_user_details'] == 'on' && array_key_exists($userDetail['rang'], $teamRank))
                $style = ' style="color: #'. $teamRank[$userDetail['rang']]['color'] .';"';
            else
                $style = '';

            $age = $currentYear - $bYear;

            $birthdayList[] = '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($userDetail['pseudo']) .'"'. $style .'><strong>'. $userDetail['pseudo'] .'</strong></a> ('. $age .' '. _ANS .')';
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

echo applyTemplate('modules/Forum/main', array(
    'forumTitle'        => $forumTitle,
    'forumDesc'         => $forumDesc,
    'breadcrumb'        => $breadcrumb,
    'todayDate'         => nkDate(time()),
    'lastVisitMessage'  => $lastVisitMessage,
    'dbrForum'          => $dbrForum,
    'nuked'             => $nuked,
    'nbTotalMessages'   => $nbTotalMessages,
    'nbTotalUsers'      => $nbTotalUsers,
    'lastUser'          => $lastUser,
    'connectedStats'    => nbvisiteur(),
    'onlineList'        => $onlineList,
    'teamRankList'      => $teamRankList,
    'birthdayMessage'   => $birthdayMessage,
    'user'              => $user
));

?>