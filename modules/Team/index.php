<?php
/**
 * index.php
 *
 * Frontend of Team module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Team'))
    return;

compteur('Team');

translate('modules/Members/lang/'. $language .'.lang.php');
require_once 'Includes/nkUserSocial.php';


/**
 * Prepare Team data.
 *
 * @param array $team : The current Team data.
 * @return void
 */
function prepareTeamData(&$team) {
    $team['titre'] = printSecuTags($team['titre']);
    $team['tag']   = printSecuTags($team['tag']);
    $team['tag2']  = printSecuTags($team['tag2']);

    $userSocialFields = nkUserSocial_getActiveFields();

    if ($team['cid'] != '') {
        $userSocialFields = ($userSocialFields) ? ', U.'. implode(', U.', $userSocialFields) : '';

        $team['teamMembers'] = nkDB_selectMany(
            'SELECT TM.userId, U.pseudo AS nickname, TM.rank, U.country AS countryImg'. $userSocialFields .'
            FROM '. TEAM_MEMBERS_TABLE . ' AS TM
            LEFT JOIN '. USER_TABLE .' AS U ON U.id = TM.userId
            WHERE TM.team = \''. $team['cid'] .'\''
            //ORDER BY ordre, pseudo
        );
    }
    else {
        $userSocialFields = ($userSocialFields) ? ', '. implode(', ', $userSocialFields) : '';

        $team['teamMembers'] = nkDB_selectMany(
            'SELECT id AS userId, pseudo AS nickname, rang, country AS countryImg'. $userSocialFields .'
            FROM '. USER_TABLE . '
            WHERE niveau > 1',
            array('ordre', 'pseudo')
        );
    }

    $team['nbMembers'] = nkDB_numRows();
}

/**
 * Prepare Team member data.
 *
 * @param array $teamMember : The current Team member data.
 * @return void
 */
function prepareTeamMemberData(&$teamMember, $team) {
    list($teamMember['country'], ) = explode ('.', $teamMember['countryImg']);

    $teamMember['fullName'] = $team['tag'] . $teamMember['nickname'] . $team['tag2'];
    $teamMember['nickname'] = nkHtmlEntityDecode($teamMember['nickname']);

    if ($teamMember['rank'] != '' && $teamMember['rank'] > 0) {
        $dbrTeamRank = nkDB_selectOne(
            'SELECT titre
            FROM '. TEAM_RANK_TABLE .'
            WHERE id = '. (int) $teamMember['rank']
        );

        $teamMember['rankName'] = printSecuTags($dbrTeamRank['titre']);
    }
    else {
        $teamMember['rankName'] = 'N/A';
    }

    $teamMember['memberUrl'] = 'index.php?file=Team&amp;op=detail&amp;autor='. urlencode($teamMember['nickname']);

    if ($team['game'] != '' && $team['game'] > 0) {
        $nbGamePref = nkDB_totalNumRows(
            'FROM '. GAMES_PREFS_TABLE .'
            WHERE game = '. (int) $team['game'] .'
            AND user_id = \''. $teamMember['userId'] .'\''
        );

        if ($nbGamePref > 0)
            $teamMember['memberUrl'] .= '&amp;game='. $team['game'];
    }
}

/**
 * Display Team list or one Team.
 *
 * @param void
 * @return void
 */
function teamList() {
    $sql = 'SELECT cid, titre, tag, tag2, game, coverage
        FROM '. TEAM_TABLE;

    if (array_key_exists('cid', $_REQUEST) && $_REQUEST['cid'] != '')
        $sql .= ' WHERE cid = '. nkDB_escape($_REQUEST['cid']);

    $teamList = nkDB_selectMany($sql, array('ordre', 'titre'));
    $nbTeam   = nkDB_numRows();

    if ($nbTeam == 0) {
        $teamList[] = array(
            'cid'   => '',
            'titre' => @nkHtmlEntityDecode($nuked['name']),
            'tag'   => @nkHtmlEntityDecode($nuked['tag_pre']),
            'tag2'  => @nkHtmlEntityDecode($nuked['tag_suf']),
            'game'  => 0
        );
    }

    $userSocialData = nkUserSocial_getConfig();

    $userSocialWebsiteKey = array_search('url', array_column($userSocialData, 'field'));

    if ($userSocialWebsiteKey !== false)
        unset($userSocialData[$userSocialWebsiteKey]);

    echo applyTemplate('modules/Team/list', array(
        'teamList'       => $teamList,
        'userSocialData' => $userSocialData
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

function teamMemberDetail() {
    $author = nkHtmlEntities($_REQUEST['autor'], ENT_QUOTES);

    $userSocialFields = nkUserSocial_getActiveFields();
    $userSocialFields = ($userSocialFields) ? ', '. implode(', ', $userSocialFields) : '';

    $teamMember = nkDB_selectOne(
        'SELECT id, game, country AS countryImg'. $userSocialFields .'
        FROM '. USER_TABLE .'
        WHERE pseudo = '. nkDB_escape($author)
    );

    if (nkDB_numRows() == 0) {
        printNotification(__('NO_MEMBER'), 'error');
        return;
    }

    list($teamMember['country'], ) = explode ('.', $teamMember['countryImg']);

    if (array_key_exists('game', $_REQUEST) && ctype_digit($_REQUEST['game'])) {
        $gameId = (int) $_REQUEST['game'];
        $fields = '';
    }
    else {
        $gameId = null;
        $fields = ', pref_1, pref_2, pref_3, pref_4, pref_5';
    }

    $teamMemberDetail = nkDB_selectOne(
        'SELECT prenom, age, sexe, ville, motherboard, cpu, ram, video, resolution, son,
        ecran, souris, clavier, connexion, system, photo'. $fields .'
        FROM '. USER_DETAIL_TABLE .'
        WHERE user_id = \''. $teamMember['id'] .'\''
    );

    if ($gameId !== null) {
        $dbrGame = nkDB_selectOne(
            'SELECT titre, pref_1, pref_2, pref_3, pref_4, pref_5
            FROM '. GAMES_TABLE .'
            WHERE id = '. $gameId
        );

        $dbrGamePref = nkDB_selectOne(
            'SELECT pref_1, pref_2, pref_3, pref_4, pref_5
            FROM '. GAMES_PREFS_TABLE .'
            WHERE game = '. $gameId .'
            AND user_id = \''. $teamMember['id'] .'\''
        );
    }
    else {
        $dbrGame = nkDB_selectOne(
            'SELECT titre, pref_1, pref_2, pref_3, pref_4, pref_5
            FROM '. GAMES_TABLE .'
            WHERE id = \''. $teamMember['game'] .'\''
        );

        $dbrGamePref['pref_1'] = $teamMemberDetail['pref_1'];
        $dbrGamePref['pref_2'] = $teamMemberDetail['pref_2'];
        $dbrGamePref['pref_3'] = $teamMemberDetail['pref_3'];
        $dbrGamePref['pref_4'] = $teamMemberDetail['pref_4'];
        $dbrGamePref['pref_5'] = $teamMemberDetail['pref_5'];
    }

    echo applyTemplate('modules/Team/detail', array(
        'author'           => $author,
        'teamMember'       => $teamMember,
        'teamMemberDetail' => $teamMemberDetail,
        'game'             => $dbrGame,
        'gamePref'         => $dbrGamePref
    ));
}

opentable();

switch ($GLOBALS['op']) {
    case 'detail' :
        teamMemberDetail();
        break;

    default:
        teamList();
}

closetable();

?>