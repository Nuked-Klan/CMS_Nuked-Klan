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
 * Format last message date for Forum / topic Forum.
 *
 * @param int : The timestamp of last message.
 * @return string : The formated date.
 */
function formatLastMessageDate($date) {
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