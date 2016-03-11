<?php
/**
 * core.php
 *
 * Common functions of Forum frontend.
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
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
        WHERE user_id = '. nkDB_quote($user['id'])
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
function getForumMessageUrl($forumId, $threadId, $messId, $nbMessages = false, $uri = '') {
    global $nuked;

    if ($nbMessages === false) {
        $nbMessages = nkDB_totalNumRows(
            'FROM '. FORUM_MESSAGES_TABLE .'
            WHERE thread_id = '. nkDB_quote($threadId)
        );
    }

    $url = 'index.php?file=Forum&page=viewtopic&forum_id='. $forumId .'&thread_id='. $threadId;

    if ($uri != '') $url .= $uri;

    $nbPage = 1;

    if ($nbMessages > $nuked['mess_forum_page']) {
        $nbPage  = ceil($nbMessages / $nuked['mess_forum_page']);
        $url    .= '&p='. $nbPage;
    }

    $url .= '#'. $messId;

    return array($url, $nbPage);
}

/**
 * Return Forum moderator list for legend.
 * Check actual username and add Team rank colorization if needed.
 *
 * @param int $forumId : The forum ID.
 * @return string : The Forum moderator list formated.
 */
function getModeratorsLegend($forumId) {
    global $nuked;

    $forumModeratorList = getModeratorsList($forumId);
    $nbModerator        = count($forumModeratorList);
    $result             = '';

    if ($forumModeratorList) {
        $result  = _n('MODERATOR', $nbModerator) .': ';

        foreach ($forumModeratorList as $forumModerator) {
            $style = '';

            if ($nuked['forum_rank_team'] == 'on' && $forumModerator['rang'] > 0) {
                $teamRank = getTeamRank();

                if (array_key_exists($forumModerator['rang'], $teamRank)
                    && $teamRank[$forumModerator['rang']]['color'] != ''
                )
                    $style = ' style="color: #'. $teamRank[$forumModerator['rang']]['color'] .';"';
            }
            else {
                $forumRank = getForumRank();

                if ($forumRank['moderator']['color'] != '')
                    $style = ' style="color: #'. $forumRank['moderator']['color'] .';"';
            }

            $result .= '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($forumModerator['pseudo'])
                . '" alt="'. __('SEE_MODERATOR') .' '. $forumModerator['pseudo'] .'" title="'. __('SEE_MODERATOR') .' '. $forumModerator['pseudo'] .'"'
                . $style .'><b>'. $forumModerator['pseudo'] .'</b></a>';
        }
    }
    else {
        $result = __('MODERATOR') .': '. __('NONE');
    }

    return $result;
}

/**
 * Return user id list of Forum moderator.
 *
 * @param int $forumId : The forum ID.
 * @return array
 */
function getModeratorsList($forumId) {
    static $data = array();

    if (! array_key_exists($forumId, $data)) {
        $dbrForumModerator = nkDB_selectMany(
            'SELECT U.pseudo, U.rang, FM.userId
            FROM '. FORUM_MODERATOR_TABLE .' AS FM
            INNER JOIN '. USER_TABLE .' AS U ON U.id = FM.userId
            WHERE FM.forum = '. $forumId
        );

        if ($dbrForumModerator)
            $data[$forumId] = $dbrForumModerator;
        else
            $data[$forumId] = array();
    }

    return $data[$forumId];
}

/**
 * Check if user is a Forum administrator / moderator.
 *
 * @param int $forumId : The forum ID.
 * @return bool : Return true if user have Forum right, false also.
 */
function isForumAdministrator($forumId) {
    global $visiteur;

    return $visiteur >= admin_mod('Forum') || isModerator($forumId);
}

/**
 * Check if user is a Forum moderator.
 *
 * @param int $forumId : The forum ID.
 * @return bool : Return true if user is a Forum moderator, false also.
 */
function isModerator($forumId, $userId = null) {
    global $user;

    $forumModeratorList = getModeratorsList($forumId);

    if ($forumModeratorList) {
        $forumModeratorIdList = array_column($forumModeratorList, 'userId');

        if ($userId === null && $user)
            $userId = $user['id'];

        if ($userId !== null && in_array($userId, $forumModeratorIdList))
            return true;
    }

    return false;
}

/**
 * Get Forum rank list.
 *
 * @param void
 * @return array : The Forum rank list formated for colorization.
 */
function getForumRank() {
    static $data = array();

    if (! $data) {
        $dbrForumRank = nkDB_selectMany(
            'SELECT nom, color, image, post, type
            FROM '. FORUM_RANK_TABLE,
            array('type', 'post'), array('DESC', 'ASC')
        );

        $data['user'] = array();

        // TODO : Add post number with title of span tag? Remove span tag and use only strong tag?
        foreach ($dbrForumRank as $forumRank) {
            if ($forumRank['type'] == 2) {
                $data['administrator'] = array(
                    'color'     => $forumRank['color'],
                    'image'     => $forumRank['image'],
                    'title'     => $forumRank['nom'],
                    'formated'  => '<span style="color: #'. $forumRank['color'] .';"><strong>'. $forumRank['nom'] .'</strong></span>'
                );
            }
            else if ($forumRank['type'] == 1) {
                $data['moderator'] = array(
                    'color'     => $forumRank['color'],
                    'image'     => $forumRank['image'],
                    'title'     => $forumRank['nom'],
                    'formated'  => '<span style="color: #'. $forumRank['color'] .';"><strong>'. $forumRank['nom'] .'</strong></span>'
                );
            }
            else {
                $data['user'][$forumRank['post']] = array(
                    'color'     => $forumRank['color'],
                    'image'     => $forumRank['image'],
                    'title'     => $forumRank['nom'],
                    'formated'  => '<span style="color: #'. $forumRank['color'] .';"><strong>'. $forumRank['nom'] .'</strong></span>'
                );
            }
        }
    }

    return $data;
}

/**
 * Get team rank list.
 *
 * @param void
 * @return array : The Team rank list formated for colorization.
 */
function getTeamRank() {
    static $data = array();

    if (! $data) {
        $dbrTeamRank = nkDB_selectMany(
            'SELECT id, titre, color, image
            FROM '. TEAM_RANK_TABLE,
            array('ordre'), 'ASC'
        );

        // TODO : Add post number with title of span tag? Remove span tag and use only strong tag?
        foreach ($dbrTeamRank as $teamRank) {
            $data[$teamRank['id']] = array(
                'color'     => $teamRank['color'],
                'image'     => $teamRank['image'],
                'title'     => $teamRank['titre'],
                'formated'  => '<span style="color: #'. $teamRank['color'] .';"><strong>'. $teamRank['titre'] .'</strong></span>'
            );
        }
    }

    return $data;
}

/**
 * Get user rank to display.
 *
 * @param void
 * @return string
 */
function getUserRank($data, $isModerator) {
    global $nuked;

    if ($nuked['forum_rank_team'] == 'on' && $data['rang'] > 0) {
        $teamRank = getTeamRank();

        if (array_key_exists($data['rang'], $teamRank))
            return $teamRank[$data['rang']];
    }
    else {
        $forumRank = getForumRank();

        if ($data['niveau'] >= admin_mod('Forum')) {
            return $forumRank['administrator'];
        }
        else if ($isModerator) {
            return $forumRank['moderator'];
        }
        else {
            foreach ($forumRank['user'] as $nbForumRankPost => $forumUserRank) {
                $lastRank = $nbForumRankPost;

                if ($data['count'] >= $nbForumRankPost)
                    return $forumRank['user'][$nbForumRankPost];
            }
        }
    }

    return array(
        'color'     => '',
        'image'     => '',
        'title'     => ''
    );
}

function nkForumNickname($data) {
    global $nuked;

    if (is_array($data)) {
        if (isset($data['pseudo']) && $data['pseudo'] != '') {
            $style = '';

            // TODO : Use CSS class instead
            if ($nuked['forum_user_details'] == 'on') {
                static $forumModeratorIdList;

                if ($forumModeratorIdList === null) {
                    $dbrForumModerator = nkDB_selectMany(
                        'SELECT DISTINCT userId
                        FROM '. FORUM_MODERATOR_TABLE
                    );

                    if ($dbrForumModerator)
                        $forumModeratorIdList = array_column($dbrForumModerator, 'userId');
                }

                if ($forumModeratorIdList !== null
                    && $data['auteur_id']
                    && in_array($data['auteur_id'], $forumModeratorIdList)
                )
                    $isModerator = true;
                else
                    $isModerator = false;

                $userRankData = getUserRank($data, $isModerator);

                if ($userRankData['color'] != '')
                    $style = ' style="color: #'. $userRankData['color'] .';"';
            }

            return '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($data['pseudo']) .'"'. $style .'>'
                . $data['pseudo'] .'</a>';
        }

        if (isset($data['auteur']) && $data['auteur'] != '')
            return nk_CSS($data['auteur']);
    }

    return '';
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
    $breadcrumb = '<a href="index.php?file=Forum"><strong>'. __('FORUM_INDEX') .'</strong></a>&nbsp;';

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
        return __('FTODAY') .'&nbsp;'. strftime('%H:%M', $date);
    }
    else if (strftime('%d', $date) == (strftime('%d', time()) - 1)
        && strftime('%m %Y', time()) == strftime('%m %Y', $date)
    ) {
        return __('FYESTERDAY') .'&nbsp;'. strftime('%H:%M', $date);
    }
    else
        return nkDate($date);
}

/**
 * Delete joined file of Forum message.
 *
 * @param string $filename : The basename of joined file.
 * @return void
 */
function deleteForumMessageFile($filename) {
    $path = 'upload/Forum/'. $filename;

    if (is_file($path)) {
        @chmod($path, 0775);
        @unlink($path);
    }
}

?>