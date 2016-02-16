<?php
/**
 * index.php
 *
 * Frontend of Members module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Members'))
    return;

compteur('Members');

require_once 'Includes/nkUserSocial.php';


/**
 * Display members list.
 *
 * @param void
 * @return void
 */
function membersList() {
    global $theme, $nuked;

    $tsOther = __('OTHER');

    $alpha = array(
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N',
        'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', $tsOther
    );

    if (array_key_exists('letter', $_GET) && in_array($_GET['letter'], $alpha))
        $currentLetter = $_GET['letter'];
    else
        $currentLetter = '';

    if ($currentLetter == $tsOther) {
        $and = 'AND pseudo NOT REGEXP \'^[a-zA-Z].\'';
    }
    else if ($currentLetter != '' && preg_match('`^[A-Z]+$`', $currentLetter)) {
        $and = 'AND pseudo LIKE \''. nkDB_escape($currentLetter, true) .'%\'';
    }
    else {
        $and = '';
    }

    $nbMembers = nkDB_totalNumRows(
        'SELECT pseudo
        FROM '. USER_TABLE .'
        WHERE team = \'\' AND niveau > 0 '. $and
    );

    if (array_key_exists('p', $_GET))
        $p = max(1, (int) $_GET['p']);
    else
        $p = 1;

    if ($nbMembers > $nuked['max_members'])
        $pagination = number($nbMembers, $nuked['max_members'], 'index.php?file=Members&amp;letter='. $currentLetter, true);
    else
        $pagination = '';

    $userSocialFields = nkUserSocial_getActiveFields();
    $userSocialFields = ($userSocialFields) ? ', '. implode(', ', $userSocialFields) : '';

    // TODO : Check while update if url with preg_match("`http://`i", $url)

    $dbrMembers = nkDB_selectMany(
        'SELECT pseudo AS nickname, country'. $userSocialFields .'
        FROM '. USER_TABLE .'
        WHERE team = \'\' '. $and .' AND niveau > 0',
        array('pseudo'), 'ASC',
        $nuked['max_members'], ($p - 1) * $nuked['max_members']
    );

    if ($currentLetter == '' && $nbMembers > 0) {
        $dbrLastMember = nkDB_selectOne(
            'SELECT pseudo
            FROM '. USER_TABLE .'
            WHERE team = \'\'',
            array('date'), 'DESC', 1
        );

        $lastMember = $dbrLastMember['pseudo'];
    }
    else
        $lastMember = '';

    echo applyTemplate('modules/Members/list', array(
        'alpha'          => $alpha,
        'currentLetter'  => $currentLetter,
        'pagination'     => $pagination,
        'membersList'    => $dbrMembers,
        'nbMembers'      => $nbMembers,
        'lastMember'     => $lastMember,
        'userSocialData' => nkUserSocial_getConfig()
    ));
}

/**
 * Calculate age with birthday member and return it.
 *
 * @param string $birthday : The member birthday.
 * @return string : The age of member or empty string if user don't set birthday date.
 */
// TODO : Check while update if age field is valid
function getMemberAge($birthday) {
    if ($birthday != '') {
        list($bDay, $bMonth, $bYear) = explode ('/', $birthday);

        $currentMonth = date('m');

        $age = date('Y') - $bYear;

        if ($currentMonth < $bMonth)
            $age = $age - 1;

        if (date('d') < $bDay && $currentMonth == $bMonth)
            $age = $age - 1;

        return $age;
    }

    return '';
}

/**
 * Get cleaned string for display in flash element.
 *
 * @param string $text : The raw text to display.
 * @return array : The text cleaned. (accents removed)
 */
function flashTextCleaning($text) {
    $a = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
    $b = 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';

    return strtr(@nkHtmlEntityDecode($text), $a, $b);
}

/**
 * Display member detail. (personal info, hardware configuration & game preferences)
 *
 * @param void
 * @return void
 */
function memberDetail() {
    global $nuked, $user, $visiteur;

    $author = nkHtmlEntities($_GET['autor'], ENT_QUOTES);

    $userSocialFields = nkUserSocial_getActiveFields();
    $userSocialFields = ($userSocialFields) ? ', U.'. implode(', U.', $userSocialFields) : '';

    // TODO : Check while update if url with preg_match("`http://`i", $url)

    $dbrMember = nkDB_selectOne(
        'SELECT U.id, U.date, U.game, U.country AS countryImg, S.last_used'. $userSocialFields .'
        FROM '. USER_TABLE .' AS U
        LEFT OUTER JOIN '. SESSIONS_TABLE .' AS S ON U.id = S.user_id
        WHERE U.pseudo = '. nkDB_escape($author)
    );

    if (nkDB_numRows() !== 1) {
        echo '<br /><br /><div class="nkAlignCenter">'. __('NO_MEMBER') .'</div><br /><br />' ."\n";
        return;
    }

    $dbrMember['country'] = pathinfo($dbrMember['countryImg'], PATHINFO_FILENAME);

    $dbrMemberDetail = nkDB_selectOne(
        'SELECT prenom, age, sexe, ville, motherboard, cpu, ram, video, resolution, son, ecran,
        souris, clavier, connexion, system, photo, pref_1, pref_2, pref_3, pref_4, pref_5
        FROM '. USER_DETAIL_TABLE .'
        WHERE user_id = \''. $dbrMember['id'] .'\''
    );

    $dbrGame = nkDB_selectOne(
        'SELECT titre, pref_1, pref_2, pref_3, pref_4, pref_5
        FROM '. GAMES_TABLE .'
        WHERE id = '. $dbrMember['game']
    );

    if ($visiteur == 9 && $dbrMember['id'] != $user['id']) {
        echo "<script type=\"text/javascript\">\n"
            . "<!--\n"
            . "\n"
            . "function deluser(pseudo, id)\n"
            . "{\n"
            . "if (confirm('" . _DELETEUSER . " '+pseudo+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Admin&page=user&op=del_user&id_user='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";
    }

    echo applyTemplate('modules/Members/detail', array(
        'member'        => $dbrMember,
        'memberDetail'  => $dbrMemberDetail,
        'game'          => $dbrGame,
        'author'        => $author
    ));
}

opentable();

// Action handle
switch ($GLOBALS['op']) {
    case 'detail' :
        memberDetail();
        break;

    default:
        membersList();
}

closetable();

?>