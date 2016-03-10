<?php
/**
 * nkSessions.php
 *
 * Librairy for user session management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

// $id_de_session       => $sessionId
// $id_user             => $userId
// $user_type           => $userLevel
// $user_name           => $userName
// $last_visite         => $lastVisit
// $nb_mess             => $nbNewPM


/**
 * Start a nk session
 *
 * @param void
 * @return void
 */
function nkSessions_init() {
    global $nuked;

    // Starting the native PHP session
    session_name('nuked');
    session_start();

    if (session_id() == '') exit(ERROR_SESSION);

    // Prepares global vars of session
    $GLOBALS['lifetime']        = $nuked['sess_days_limit'] * 86400;
    $GLOBALS['timesession']     = $nuked['sess_inactivemins'] * 60;
    $GLOBALS['time']            = time();
    $GLOBALS['timelimit']       = $GLOBALS['time'] + $GLOBALS['lifetime'];
    $GLOBALS['sessionlimit']    = $GLOBALS['time'] + $GLOBALS['timesession'];

    $GLOBALS['cookie_session']  = $nuked['cookiename'] .'_sess_id';
    $GLOBALS['cookie_theme']    = $nuked['cookiename'] .'_user_theme';
    $GLOBALS['cookie_langue']   = $nuked['cookiename'] .'_user_langue';
    $GLOBALS['cookie_visit']    = $nuked['cookiename'] .'_last_visit';
    $GLOBALS['cookie_forum']    = $nuked['cookiename'] .'_forum_read';
    $GLOBALS['cookie_userid']   = $nuked['cookiename'] .'_userid';

    // Recherche de l'adresse IP
    $GLOBALS['user_ip'] = $_SERVER['REMOTE_ADDR'];

    if (defined('NK_REVERSE_PROXY') && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList             = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $GLOBALS['user_ip'] = trim($ipList[0]);
    }

    if (! filter_var($GLOBALS['user_ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6))
        $GLOBALS['user_ip'] = '';
}

/**
 * Get user, set user level in $GLOBALS['visiteur'] and
 * set admin session status in $GLOBALS['adminSession']
 *
 * @param void
 * @return void
 */
function nkSessions_getUser() {
    // If user session is started, secure user data to $GLOBALS['user']
    if (nkSessions_userSessionCheck() === true) {
        $GLOBALS['user']        = nkSessions_secureUser();
        $GLOBALS['visiteur']    = $GLOBALS['user'][1];
    }
    else {
        $GLOBALS['user']        = array();
        $GLOBALS['visiteur']    = 0;
        $_SESSION['admin']      = false;
    }
}

/**
 * Get secure user data
 *
 * @param void
 * @return array : An array with user detail
 */
function nkSessions_secureUser() {
    global $cookie_userid, $user_ip, $time, $cookie_forum, $timelimit;

    $userId = $_COOKIE[$cookie_userid];

    $dbrUser = nkDB_selectOne(
        'SELECT niveau, pseudo
        FROM '. USER_TABLE .'
        WHERE id = '. nkDB_escape($userId)
    );

    /*
    if (isset($_REQUEST['file']) && isset($_REQUEST['thread_id']) && $_REQUEST['file'] == 'Forum' && is_numeric($_REQUEST['thread_id']) && $_REQUEST['thread_id'] > 0 && $userSecure > 0) {
        $select_thread = "SELECT MAX(id) FROM " . FORUM_MESSAGES_TABLE . " WHERE date > '" . $GLOBALS['last_used'] . "' AND thread_id = '" . $_REQUEST['thread_id'] . "' ";
        $sql_thread = nkDB_execute($select_thread);
        list($max_mess_id) = mysql_fetch_array($sql_thread);

        if ($max_mess_id > 0) {
            if (isset($_REQUEST[$cookie_forum]) && !empty($_REQUEST[$cookie_forum])){
                $id_read_forum = $_REQUEST[$cookie_forum];
                if (preg_match("`[^0-9,]`i", $id_read_forum)) $id_read_forum = '';
                $table_read_forum = explode(',',$id_read_forum);
                if (!in_array($max_mess_id, $table_read_forum)) setcookie($cookie_forum, $id_read_forum.",".$max_mess_id, $timelimit);
            }
            else setcookie($cookie_forum, $max_mess_id, $timelimit);
        }
    }
    */

    $nbNewPM = nkDB_totalNumRows(
        'FROM '. USERBOX_TABLE .'
        WHERE user_for = '. nkDB_escape($userId) .' AND status = 0'
    );

    return array(
        'id'        => $userId,
        'level'     => $dbrUser['niveau'],
        'name'      => $dbrUser['pseudo'],
        'ip'        => $user_ip,
        'lastUsed'  => $GLOBALS['last_used'],
        'nbNewPM'   => $nbNewPM,
        // Numeric index for old module compatibility
        0 => $userId,
        1 => $dbrUser['niveau'],
        2 => $dbrUser['pseudo'],
        3 => $user_ip,
        4 => $GLOBALS['last_used'],
        5 => $nbNewPM
    );
}

/**
 * Check admin session
 *
 * @param void
 * @return bool : Return true if admin session exist or false if nothing
 */
function nkSessions_adminCheck() {
    return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
}

/**
 * Check user session
 *
 * @param void
 * @return bool : Return true if user session exist or false if nothing
 */
function nkSessions_userSessionCheck() {
    global $cookie_session, $cookie_userid, $time, $timesession, $user_ip;

    if (isset($_COOKIE[$cookie_session], $_COOKIE[$cookie_userid])
        && ! empty($_COOKIE[$cookie_session])
        && ! empty($_COOKIE[$cookie_userid])
    ) {
        $sessionId  = $_COOKIE[$cookie_session];
        $userId     = $_COOKIE[$cookie_userid];

        $dbrSessions = nkDB_selectOne(
            'SELECT date, ip, last_used
            FROM '. SESSIONS_TABLE .'
            WHERE id = '. nkDB_escape($sessionId) .' AND user_id = '. nkDB_escape($userId)
        );

        $userSecure = nkDB_numRows();

        if ($dbrSessions['date'] > $time - $timesession && $dbrSessions['ip'] != $user_ip)
            $userSecure = 0;

        if ($userSecure == 1) {
            $GLOBALS['last_used'] = $dbrSessions['last_used'];

            nkDB_update(SESSIONS_TABLE, array('last_used' => $time), 'id = '. nkDB_escape($sessionId));

            return true;
        }
        // Incorrect session information
        else {
            nkDB_delete(SESSIONS_TABLE,
                'id = '. nkDB_escape($sessionId) .' OR user_id = '. nkDB_escape($userId)
            );
        }
    }

    return false;
}

/*// initialise avec les microsecondes
function make_seed() {
    list($usec, $sec) = explode(' ', microtime());

    return (float) $sec + ((float) $usec * 100000);
}*/

/**
 * Reset all user cookies
 *
 * @param void
 * @return void
 */
function nkSessions_resetUserCookie() {
    global $cookie_session, $cookie_userid, $cookie_theme, $cookie_langue, $cookie_forum;

    setcookie($cookie_session, '');
    setcookie($cookie_userid, '');
    setcookie($cookie_theme, '');
    setcookie($cookie_langue, '');
    setcookie($cookie_forum, '');
}

/**
 * Create a new user session
 *
 * @param string $userId : Id of user
 * @param string $rememberMe : Set to 'ok' for keep session cookie or set volatile cookie session
 * @return void
 */
function nkSessions_createNewSession($userId, $rememberMe) {
    global $cookie_session, $cookie_userid, $user_ip, $timelimit, $time;

    // Generate and check unique session ID
    do {
        $sessionId = md5(uniqid());

        nkDB_selectOne(
            'SELECT id
            FROM '. SESSIONS_TABLE .'
            WHERE id = '. nkDB_escape($sessionId)
        );

    } while (nkDB_numRows() !== 0);

    nkSessions_resetUserCookie();

    $dbuSessions = nkDB_update(SESSIONS_TABLE, array(
            'id'        => $sessionId,
            'last_used' => array('date', 'no-escape'),
            'date'      => $time,
            'ip'        => $user_ip
        ),
        'user_id = '. nkDB_escape($userId)
    );

    $dbiSessions = true;

    if (nkDB_affectedRows() == 0) {
        $dbiSessions = nkDB_insert(SESSIONS_TABLE, array(
            'id'        => $sessionId,
            'user_id'   => $userId,
            'date'      => $time,
            'ip'        => $user_ip,
            'vars'      => ''
        ));
    }

    if ($dbuSessions !== false && $dbiSessions !== false) {
        if ($rememberMe == 'ok') {
            setcookie($cookie_session, $sessionId, $timelimit);
            setcookie($cookie_userid, $userId, $timelimit);
        }
        else {
            setcookie($cookie_session, $sessionId);
            setcookie($cookie_userid, $userId);
        }
    }
    else {
        nkDB_delete(SESSIONS_TABLE, 'user_id = '. nkDB_escape($userId));
    }
}

/**
 * Stop user session
 *
 * @param string $userId : Id of user
 * @return void
 */
function nkSessions_stopSession($userId) {
    nkDB_update(SESSIONS_TABLE, array('ip' => ''), 'user_id = '. nkDB_escape($userId));

    nkSessions_resetUserCookie();

    $_SESSION['admin'] = false;
}

?>