<?php
/**
 * index.php
 *
 * Frontend of Games module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * Display user game preferences fields.
 *
 * @param array $userGamePref : The user game preferences data of user detail database table.
 * @return void
 */
function displayUserGamePrefFields($userGamePref) {
    global $user;

    $dbrTeam = nkDB_selectMany(
        'SELECT T.game
        FROM '. TEAM_MEMBERS_TABLE.' AS TM
        LEFT JOIN '. TEAM_TABLE .' AS T ON T.cid = TM.team
        WHERE TM.userId = '. nkDB_escape($user['id'])
    );

    $nbTeam = nkDB_numRows();

    if ($nbTeam > 0) {
        $sqlIn = implode(', ', array_column($dbrTeam, 'game'));

        $dbrGame = nkDB_selectMany(
            'SELECT id, titre, pref_1, pref_2, pref_3, pref_4, pref_5, gameFile
            FROM '. GAMES_TABLE .'
            WHERE id IN ('. $sqlIn .')'
        );

        $dbrGamePref = nkDB_selectMany(
            'SELECT game, pref_1, pref_2, pref_3, pref_4, pref_5
            FROM '. GAMES_PREFS_TABLE .'
            WHERE game IN ('. $sqlIn .')
            AND user_id = '. nkDB_escape($user['id'])
        );

        foreach ($dbrTeam as $team) {
            $gameKey = array_search($team['game'], array_column($dbrGame, 'id'));

            if ($gameKey === false) continue;

            $gamePrefKey = array_search($team['game'], array_column($dbrGamePref, 'game'));

            if ($gamePrefKey === false) {
                $gamePref = array(
                    'pref_1' => '',
                    'pref_2' => '',
                    'pref_3' => '',
                    'pref_4' => '',
                    'pref_5' => ''
                );
            }
            else {
                $gamePref = $dbrGamePref[$gamePrefKey];
            }

            if ($dbrGame[$gameKey]['gameFile'] != '')
                displayGameFileSettings($dbrGame[$gameKey], $gamePref, true);
            else
                displayCommonGameSettings($dbrGame[$gameKey], $gamePref, true);
        }
    }
    else {
        $dbrGame = nkDB_selectOne(
            'SELECT G.titre, G.pref_1, G.pref_2, G.pref_3, G.pref_4, G.pref_5, G.gameFile
            FROM '. GAMES_TABLE .' AS G
            LEFT JOIN '. USER_TABLE .' AS U ON U.game = G.id
            WHERE U.id = '. nkDB_escape($user['id'])
        );

        if ($dbrGame['gameFile'] != '')
            displayGameFileSettings($dbrGame, $userGamePref);
        else
            displayCommonGameSettings($dbrGame, $userGamePref);
    }
}

/**
 * Display input fields of game preferences file.
 *
 * @param array $game : The game data.
 * @param array $gamePref : The user game preferences data.
 * @param bool $multiple : Return true if user has multiple game preferences.
 * @return void
 */
function displayGameFileSettings($game, $gamePref, $multiple = false) {
    if (file_exists($gameFile = 'modules/Games/includes/'. $game['gameFile'] .'/setting.php')) {
        if (file_exists($gameFunct = 'display'. ucfirst($game['gameFile']) .'Setting')) {
            $gameFunct($game, $gamePref, $mutiple);
        }
        else
            trigger_error('fonction manquante', E_USER_WARNING);
    }
    else
        trigger_error('fichier manquant', E_USER_WARNING);
}

/**
 * Display input fields of common game preferences.
 *
 * @param array $game : The game data.
 * @param array $gamePref : The user game preferences data.
 * @param bool $multiple : Return true if user has multiple game preferences.
 * @return void
 */
function displayCommonGameSettings($game, $gamePref, $multiple = false) {
    $game['titre']  = nkHtmlEntities($game['titre']);
    $game['pref_1'] = nkHtmlEntities($game['pref_1']);
    $game['pref_2'] = nkHtmlEntities($game['pref_2']);
    $game['pref_3'] = nkHtmlEntities($game['pref_3']);
    $game['pref_4'] = nkHtmlEntities($game['pref_4']);
    $game['pref_5'] = nkHtmlEntities($game['pref_5']);

    $suffix = ($multiple) ? '[]' : '';
    $gameId = (isset($game['id'])) ? $game['id'] : 0;

    echo '<tr class="nkBgColor3"><td align="center" colspan="2"><b>'. $game['titre'] .'</b></td></tr>' ."\n"
        . '<tr><td style="width: 30%;" align="left"><b>'. $game['pref_1'] .' :</b></td>'
        . '<td style="width: 70%;" align="left"><input type="text" name="pref1'. $suffix .'" value="'. $gamePref['pref_1'] .'" size="25" /></td></tr>' ."\n"
        . '<tr><td style="width: 30%;" align="left"><b>'. $game['pref_2'] .' :</b></td>'
        . '<td style="width: 70%;" align="left"><input type="text" name="pref2'. $suffix .'" value="'. $gamePref['pref_2'] .'" size="25" /></td></tr>' ."\n"
        . '<tr><td style="width: 30%;" align="left"><b>'. $game['pref_3'] .' :</b></td>'
        . '<td style="width: 70%;" align="left"><input type="text" name="pref3'. $suffix .'" value="'. $gamePref['pref_3'] .'" size="25" /></td></tr>' ."\n"
        . '<tr><td style="width: 30%;" align="left"><b>'. $game['pref_4'] .' :</b></td>'
        . '<td style="width: 70%;" align="left"><input type="text" name="pref4'. $suffix .'" value="'. $gamePref['pref_4'] .'" size="25" /></td></tr>' ."\n"
        . '<tr><td style="width: 30%;" align="left"><b>'. $game['pref_5'] .' :</b></td>'
        . '<td style="width: 70%;" align="left"><input type="text" name="pref5'. $suffix .'" value="'. $gamePref['pref_5'] .'" size="25" />'
        . '<input type="hidden" name="gameId'. $suffix .'" value="'. $gameId .'" size="25" /></td></tr>' ."\n";
}

/**
 * Save input fields values of game preferences.
 *
 * @param array $userGamePref : The user game preferences data of user detail database table.
 * @return void
 */
function saveUserGamePrefFields(&$data) {
    global $user;

    if (is_array($_POST['pref1'])
        && is_array($_POST['pref2'])
        && is_array($_POST['pref3'])
        && is_array($_POST['pref4'])
        && is_array($_POST['pref5'])
        && is_array($_POST['gameId'])
    ) {
        $sqlIn = implode(', ', array_filter($_POST['gameId']));

        $dbrGame = nkDB_selectMany(
            'SELECT id, gameFile
            FROM '. GAMES_TABLE .'
            WHERE id IN ('. $sqlIn .')'
        );

        $dbrUser = nkDB_selectOne(
            'SELECT game
            FROM '. USER_TABLE .'
            WHERE id = \''. $user['id'] .'\''
        );

        $nbPref = count($_POST['pref1']);

        for ($n = 0; $n < $nbPref; $n++) {
            $gameKey = array_search($_POST['gameId'][$n], array_column($dbrGame, 'id'));

            if ($gameKey === false) continue;

            if ($dbrGame[$gameKey]['gameFile'] != '') {
                saveGameFileSettings($data, true);
            }
            else {
                $userGamePref = array(
                    'pref_1' => nkHtmlEntities(stripslashes($_POST['pref1'][$n])),
                    'pref_2' => nkHtmlEntities(stripslashes($_POST['pref2'][$n])),
                    'pref_3' => nkHtmlEntities(stripslashes($_POST['pref3'][$n])),
                    'pref_4' => nkHtmlEntities(stripslashes($_POST['pref4'][$n])),
                    'pref_5' => nkHtmlEntities(stripslashes($_POST['pref5'][$n]))
                );

                saveCommonGameSettings($userGamePref, $_POST['gameId'][$n]);

                if ($_POST['gameId'][$n] == $dbrUser['game']) {
                    $data['pref_1'] = $userGamePref['pref_1'];
                    $data['pref_2'] = $userGamePref['pref_2'];
                    $data['pref_3'] = $userGamePref['pref_3'];
                    $data['pref_4'] = $userGamePref['pref_4'];
                    $data['pref_5'] = $userGamePref['pref_5'];
                }
            }
        }
    }
    else {
        $data['pref_1'] = nkHtmlEntities(stripslashes($_POST['pref1']));
        $data['pref_2'] = nkHtmlEntities(stripslashes($_POST['pref2']));
        $data['pref_3'] = nkHtmlEntities(stripslashes($_POST['pref3']));
        $data['pref_4'] = nkHtmlEntities(stripslashes($_POST['pref4']));
        $data['pref_5'] = nkHtmlEntities(stripslashes($_POST['pref5']));
    }
}

/**
 * Save input fields values of game preferences file.
 *
 * @param array $game : The game data.
 * @param array $gamePref : The user game preferences data.
 * @param bool $multiple : Return true if user has multiple game preferences.
 * @return void
 */
function saveGameFileSettings($data, $multiple = false) {
    if (file_exists($gameFile = 'modules/Games/includes/'. $game['gameFile'] .'/setting.php')) {
        if (file_exists($gameFunct = 'save'. ucfirst($game['gameFile']) .'Setting')) {
            $gameFunct($data, $mutiple);
        }
        else
            trigger_error('fonction manquante', E_USER_WARNING);
    }
    else
        trigger_error('fichier manquant', E_USER_WARNING);
}

/**
 * Save input fields values of common game preferences.
 *
 * @param array $userGamePref : The user game preferences data.
 * @return void
 */
function saveCommonGameSettings($userGamePref, $gameId) {
    global $user;

    $dbrGame = nkDB_selectOne(
        'SELECT gameFile
        FROM '. GAMES_TABLE .'
        WHERE id = '. (int) $gameId
    );

    if ($dbrGame['gameFile'] != '') {
        saveGameFileSettings($data, true);
    }
    else {
        $check = nkDB_totalNumRows(
            'FROM '. GAMES_PREFS_TABLE .'
            WHERE user_id = \''. $user['id'] .'\''
        );

        if ($check > 0) {
            nkDB_update(GAMES_PREFS_TABLE, $userGamePref, 'user_id = \''. $user['id'] .'\' AND game = '. (int) $gameId);
        }
        else {
            $userGamePref['game']    = (int) $gameId;
            $userGamePref['user_id'] = $user['id'];
            nkDB_insert(GAMES_PREFS_TABLE, $userGamePref);
        }
    }
}

?>