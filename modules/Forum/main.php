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


/**
 * Get Forum list of Forum category.
 *
 * @param int $forumCatId : The forum category ID.
 * @return array : The result of nkDB query.
 */
function getForumList($forumCatId) {
    global $visiteur;

    return nkDB_selectMany(
        'SELECT id, nom, comment, image, moderateurs
        FROM '. FORUM_TABLE .'
        WHERE cat = '. $forumCatId .' AND '. $visiteur .' >= niveau',
        array('ordre', 'nom')
    );
}

/**
 * Format and return Forum moderator list.
 * Check actual username and add Team rank colorization if needed.
 *
 * @param string $rawModeratorList : The raw Forum moderator list issues of Forum database table.
 * @return array : The Forum moderator list formated.
 */
function getModeratorList($rawModeratorList) {
    global $nuked;

    $moderatorList  = explode('|', $rawModeratorList);
    $nbModerator    = count($moderatorList);
    $moderatorLink  = array();

    if ($nuked['forum_user_details'] == 'on') {
        $teamRank   = getTeamRank();
        $field      = ', rang';
    }
    else
        $field = '';

    for ($i = 0; $i < $nbModerator; $i++) {
        $dbrUser = nkDB_selectOne(
            'SELECT pseudo'. $field .'
            FROM '. USER_TABLE .'
            WHERE id = '. nkDB_escape($moderatorList[$i])
        );

        if ($nuked['forum_user_details'] == 'on' && array_key_exists($dbrUser['rang'], $teamRank))
            $style = ' style="color: #'. $teamRank[$dbrUser['rang']]['color'] .';"';
        else
            $style = '';

        $moderatorLink[] = '<a href="index.php?file=Members&op=detail&autor='. urlencode($dbrUser['pseudo']) .'" alt="'. _SEEMODO . $dbrUser['pseudo'] .'" title="'. _SEEMODO . $dbrUser['pseudo'] .'"'. $style .'><b>'. $dbrUser['pseudo'] .'</b></a>';
    }

    return $moderatorLink;
}

/**
 * Get number of thread in Forum.
 *
 * @param int $forumId : The forum ID.
 * @return int : The result of nkDB query.
 */
function getNbThreadInForum($forumId) {
    return nkDB_totalNumRows(
        'FROM '. FORUM_THREADS_TABLE .'
        WHERE forum_id = '. nkDB_escape($forumId)
    );
}

/**
 * Get number of message in Forum.
 *
 * @param int $forumId : The forum ID.
 * @return int : The result of nkDB query.
 */
function getNbMessageInForum($forumId) {
    return nkDB_totalNumRows(
        'FROM '. FORUM_MESSAGES_TABLE .'
        WHERE forum_id = '. nkDB_escape($forumId)
    );
}

/**
 * Get last message data.
 *
 * @param int $forumId : The forum ID.
 * @return array : The last message formated data.
 */
function getLastMessageInForum($forumId) {
    global $nuked;

    $lastMessageData = array();

    $dbrForumMessage = nkDB_selectOne(
        'SELECT id, titre, thread_id, date, auteur, auteur_id
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE forum_id = '. nkDB_escape($forumId),
        array('id'), 'DESC', 1
    );

    $dbrForumMessage['auteur'] = nk_CSS($dbrForumMessage['auteur']);

    if ($dbrForumMessage['auteur_id'] != '') {
        if ($nuked['forum_user_details'] == 'on') {
            $teamRank   = getTeamRank();
            $field      = ', rang';
        }
        else
            $field = '';

        $dbrUser = nkDB_selectOne(
            'SELECT pseudo, avatar, country'. $field .'
            FROM '. USER_TABLE .'
            WHERE id = '. nkDB_escape($dbrForumMessage['auteur_id'])
        );

        if ($nuked['forum_user_details'] == 'on' && array_key_exists($dbrUser['rang'], $teamRank))
            $style = ' style="color: #'. $teamRank[$dbrUser['rang']]['color'] .';"';
        else
            $style = '';

        if (! empty($dbrUser) && $dbrUser['pseudo'] != '')
            $autor = $dbrUser['pseudo'];
        else
            $autor = $dbrForumMessage['auteur'];

        // On remplace l'avatar vide par le noAvatar
        if ($dbrUser['avatar'] != '')
            $lastMessageData['authorAvatar'] = $dbrUser['avatar'];
        else
            $lastMessageData['authorAvatar'] = 'modules/Forum/images/noAvatar.png';

        $lastMessageData['author'] = '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($autor) .'" '. $style .'>'. $autor .'</a>';
    }
    else {
        $lastMessageData['author']          = $dbrForumMessage['auteur'];
        $lastMessageData['authorAvatar']    = 'modules/Forum/images/noAvatar.png';
    }

    // Formatage de la date
    if (strftime('%d %m %Y', time()) ==  strftime('%d %m %Y', $dbrForumMessage['date'])) {
        $lastMessageData['date'] = _FTODAY .'&nbsp;'. strftime('%H:%M', $dbrForumMessage['date']);
    }
    else if (strftime('%d', $dbrForumMessage['date']) == (strftime('%d', time()) - 1)
        && strftime('%m %Y', time()) == strftime('%m %Y', $dbrForumMessage['date'])
    ) {
        $lastMessageData['date'] = _FYESTERDAY .'&nbsp;'. strftime('%H:%M', $dbrForumMessage['date']);
    }
    else
        $lastMessageData['date'] = nkDate($dbrForumMessage['date']);

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

/**
 * Get team rank list.
 *
 * @param void
 * @return array : The Team rank list formated for colorization.
 */
function getTeamRank() {
    static $data = array();

    if (! empty($data)) return $data;

    $dbrTeamRank = nkDB_selectMany(
        'SELECT id, titre, couleur
        FROM '. TEAM_RANK_TABLE,
        array('ordre'), 'ASC', 20
    );

    foreach ($dbrTeamRank as $teamRank) {
        $data[$teamRank['id']] = array(
            'color'     => $teamRank['couleur'],
            'title'     => $teamRank['titre'],
            'formated'  => '<span style="color: #'. $teamRank['couleur'] .';"><strong>'. $teamRank['titre'] .'</strong></span>'
        );
    }

    return $data;
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

if (! empty($_REQUEST['cat'])) {
    $dbrForumCat = nkDB_selectOne(
        'SELECT nom
        FROM '. FORUM_CAT_TABLE .'
        WHERE id = '. nkDB_escape($_REQUEST['cat'])
    );

    if (isset($dbrForumCat['nom']))
        $breadcrumb = '-> <strong>'. printSecuTags($dbrForumCat['nom']) .'</strong>';
}

// Get last visit date
if ($user && $user['lastUsed'] != '')
    $lastVisitMessage = _LASTVISIT .' : '. nkDate($user['lastUsed']);
else
    $lastVisitMessage = '';

/*
$sql = 'SELECT F.id AS forumId, F.nom AS forumName, F.comment, F.image AS forumImage, F.moderateurs,
    FC.id As catId, FC.nom As catName, FC.image AS catImage
    FROM '. FORUM_TABLE .' AS F
    INNER JOIN '. FORUM_CAT_TABLE .' AS FC
    ON FC.id = F.cat
    WHERE '. $visiteur .' >= FC.niveau AND '. $visiteur .' >= F.niveau ORDER BY F.ordre, F.nom';
*/

// Get Forum category list
$sql = 'SELECT id, nom, image
    FROM '. FORUM_CAT_TABLE .'
    WHERE '. $visiteur .' >= niveau';

if (!empty($_REQUEST['cat']))
    $dbrForumCat = nkDB_selectMany($sql .' AND id = '. nkDB_escape($_REQUEST['cat']));
else
    $dbrForumCat = nkDB_selectMany($sql, array('ordre', 'nom'));

// Count all Forum message and all user
// TODO : All user or all valid user ?
$nbTotalMessages    = nkDB_totalNumRows('FROM '. FORUM_MESSAGES_TABLE);
$nbTotalUsers       = nkDB_totalNumRows('FROM '. USER_TABLE);

// Get last member username
$dbrUser = nkDB_selectOne(
    'SELECT pseudo
    FROM '. USER_TABLE,
    array('date'), 'DESC', 1
);

$lastUser = $dbrUser['pseudo'];

// Prepare online members info
$onlineList = array();

$dbrConnected = nkDB_selectMany(
    'SELECT username
    FROM '. NBCONNECTE_TABLE .'
    WHERE type > 0',
    array('date')
);

if ($nuked['forum_user_details'] == 'on') {
    $teamRank   = getTeamRank();
    $field      = ', rang';
}
else
    $field = '';

foreach ($dbrConnected as $connected) {
    $dbrUser = nkDB_selectOne(
        'SELECT pseudo, country'. $field .'
        FROM '. USER_TABLE .'
        WHERE id = '. nkDB_escape($connected['username'])
    );

    if ($nuked['forum_user_details'] == 'on' && array_key_exists($dbrUser['rang'], $teamRank))
        $style = ' style="color: #'. $teamRank[$dbrUser['rang']]['color'] .';"';
    else
        $style = '';

    $onlineList[] = '<img src="images/flags/'. $dbrUser['country'] .'" alt="'. $dbrUser['country'] .'" class="nkForumOnlineFlag" />'
        . '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($connected['username']) .'"'. $style .'>'
        . $connected['username'] .'</a>';
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
    'dbrForumCat'       => $dbrForumCat,
    'nuked'             => $nuked,
    'nbTotalMessages'   => $nbTotalMessages,
    'nbTotalUsers'      => $nbTotalUsers,
    'lastUser'          => $lastUser,
    'nb'                => nbvisiteur(),
    'onlineList'        => $onlineList,
    'teamRankList'      => $teamRankList,
    'birthdayMessage'   => $birthdayMessage,
    'user'              => $user
));

?>