<?php
/**
 * core.php
 *
 * Common functions of Forum frontend.
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * Get user Forum read data.
 *
 * @param void
 * @return array : An associative array with thread_id and forum_id list.
 */
function getUserForumReadData() {
    global $user;

    static $dbrUserForumRead;

    if ($dbrUserForumRead !== null)
        return $dbrUserForumRead;

    $dbrUserForumRead = nkDB_selectOne(
        'SELECT thread_id, forum_id
        FROM '. FORUM_READ_TABLE .'
        WHERE user_id = '. nkDB_escape($user['id'])
    );

    return $dbrUserForumRead;
}

/**
 * Common function for read Forum data.
 * - Get current Forum data.
 * - Get Forum list sorted by Forum category.
 * - Get Forum list of one Forum category.
 *
 * @param string $fieldList : The field list of SQL query.
 * @param int $idName : The ID name. (forumId or catId)
 * @param int $idValue : The ID value.
 * @return mixed : Return current Forum data array (false if an error occurs) or
 *         return Forum list data array (empty array if no forum founded)
 */
function getForumData($fieldList, $idName, $idValue) {
    global $visiteur;

    $sql = 'SELECT '. $fieldList .'
        FROM '. FORUM_TABLE .' AS F
        INNER JOIN '. FORUM_CAT_TABLE .' AS FC
        ON FC.id = F.cat';

    if ($idName == 'catId') {
        $sql .= ' WHERE '. $visiteur .' >= FC.niveau AND '. $visiteur .' >= F.niveau';

        if ($idValue > 0)
            return nkDB_selectMany($sql .' AND FC.id = '. $idValue, array('F.ordre', 'F.nom'));
        else
            return nkDB_selectMany($sql, array('FC.ordre', 'FC.nom', 'F.ordre', 'F.nom'));
    }

    if ($idValue > 0)
        return nkDB_selectOne($sql .' WHERE F.id = '. $idValue);

    return false;
}

/**
 * Get last Forum message data.
 *
 * @param int $idName : The field ID name used in where SQL clause. (forum_id or thread_id)
 * @param int $idValue : The field ID value used in where SQL clause.
 * @param string $fieldList : The field list of SQL query.
 * @return mixed : Return last Forum message data array (false if an error occurs)
 */
function getLastForumMessageData($idName, $idValue, $fieldList) {
    return nkDB_selectOne(
        'SELECT '. $fieldList .'
        FROM '. FORUM_MESSAGES_TABLE .' AS FM
        LEFT JOIN '. USER_TABLE .' AS U
        ON U.id = FM.auteur_id
        WHERE FM.'. $idName .' = '. $idValue,
        array('FM.id'), 'DESC', 1
    );
}

/**
 * Format url of a thread message.
 *
 * @param int $forumId : The forum ID.
 * @param int $threadId : The forum thread ID.
 * @param int $messId : The message ID.
 * @return string : The url formated (with pagination if needed) and message anchor.
 */
function getForumMessageUrl($forumId, $threadId, $messId, $nbMessage = false) {
    global $nuked;

    if ($nbMessage === false) {
        $nbMessage = nkDB_totalNumRows(
            'FROM '. FORUM_MESSAGES_TABLE .'
            WHERE thread_id = '. nkDB_escape($threadId)
        );
    }

    $url    = 'index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId;
    $nbPage = 1;

    if ($nbMessage > $nuked['mess_forum_page']) {
        $nbPage  = ceil($nbMessage / $nuked['mess_forum_page']);
        $url    .= '&p='. $nbPage;
    }

    $url .= '#'. $messId;

    return array($url, $nbPage);
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
        // TODO : Use IN() SQL clause
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

function isModerator($rawModeratorList) {
    global $user;

    if ($user && $rawModeratorList != '' && strpos($user['id'], $rawModeratorList) !== false)
        return true;

    return  false;
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
        array('ordre'), 'ASC'
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

/**
 * Generate breadcrumb.
 *
 * @param string $catName : The forum category name.
 * @param int $catId : The forum category ID.
 * @param string $forumName : The forum name.
 * @param int $forumId : The forum ID.
 * @return string : The HTML code for breadcrumb.
 */
function getForumBreadcrump($catName = '', $catId, $forumName = '', $forumId = 0) {
    $breadcrumb = '<a href="index.php?file=Forum"><strong>'. _INDEXFORUM .'</strong></a>&nbsp;';

    if ($forumName == '') {
        if ($catName != '')
            $breadcrumb .= '-> <strong>'. $catName.'</strong>';
    }
    else {
        $breadcrumb .= '-> <a href="index.php?file=Forum&amp;cat='. $catId .'"><strong>'. $catName .'</strong></a>&nbsp;';

        // viewforum
        if ($forumId == 0)
            $breadcrumb .= '-> <strong>'. $forumName .'</strong>&nbsp;';
        // viewtopic
        else
            $breadcrumb .= '-> <a href="index.php?file=Forum&amp;page=viewforum&amp;forum_id='. $forumId .'"><strong>'. $forumName .'</strong></a>&nbsp;';
    }

    return $breadcrumb;
}

/**
 * Format message date for Forum / topic Forum.
 *
 * @param int : The timestamp of last message.
 * @return string : The formated date.
 */
function formatForumMessageDate($date) {
    if (strftime('%d %m %Y', time()) ==  strftime('%d %m %Y', $date)) {
        return _FTODAY .'&nbsp;'. strftime('%H:%M', $date);
    }
    else if (strftime('%d', $date) == (strftime('%d', time()) - 1)
        && strftime('%m %Y', time()) == strftime('%m %Y', $date)
    ) {
        return _FYESTERDAY .'&nbsp;'. strftime('%H:%M', $date);
    }
    else
        return nkDate($date);
}

function formatAuthorData() {
}

?>