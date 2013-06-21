<?php
/**
 * Session management in Nuked-klan
 *
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

// Initialisation des variables de temps
$lifeTime     = $nuked['sess_days_limit'] * 86400;
$timesession  = $nuked['sess_inactivemins'] * 60;
$time         = time();
$timelimit    = $time + $lifeTime;
$sessionlimit = $time + $timesession;

// Initialisation des variables de cookies
$cookieSession  = $nuked['cookiename'] . '_sess_id';
$cookieTheme   = $nuked['cookiename'] . '_user_theme';
$cookieLang  = $nuked['cookiename'] . '_user_langue';
$cookieVisit   = $nuked['cookiename'] . '_last_visit';
$cookieAdmin   = $nuked['cookiename'] . '_admin_session';
$cookieForum   = $nuked['cookiename'] . '_forum_read';
$cookieUserId  = $nuked['cookiename'] . '_userid';

// Recherche de l'adresse IP
$serverUserIp = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

// Validité adresse IP v4 / v6
if(isset($serverUserIp) && !empty($serverUserIp)) {
    if(preg_match('/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $serverUserIp)) $userIp = $serverUserIp;
    elseif(preg_match('/^(([A-Fa-f0-9]{1,4}:){7}[A-Fa-f0-9]{1,4})$|^([A-Fa-f0-9]{1,4}::([A-Fa-f0-9]{1,4}:){0,5}[A-Fa-f0-9]{1,4})$|^(([A-Fa-f0-9]{1,4}:){2}:([A-Fa-f0-9]{1,4}:){0,4}[A-Fa-f0-9]{1,4})$|^(([A-Fa-f0-9]{1,4}:){3}:([A-Fa-f0-9]{1,4}:){0,3}[A-Fa-f0-9]{1,4})$|^(([A-Fa-f0-9]{1,4}:){4}:([A-Fa-f0-9]{1,4}:){0,2}[A-Fa-f0-9]{1,4})$|^(([A-Fa-f0-9]{1,4}:){5}:([A-Fa-f0-9]{1,4}:){0,1}[A-Fa-f0-9]{1,4})$|^(([A-Fa-f0-9]{1,4}:){6}:[A-Fa-f0-9]{1,4})$/', $serverUserIp)) $userIp = $serverUserIp;
    else $userIp = '';
}

function secure(){
    // VERIFIER L'UTILITE DE CETTE CHOSE
    $lastVisit   = 0;
    $nb_mess       = 0;

    // Initialisation l'ID de session
    $sessionId = '';
    if (isset($_COOKIE[$GLOBALS['cookieSession']]) && !empty($_COOKIE[$GLOBALS['cookieSession']])) {
        $sessionId = $_COOKIE[$GLOBALS['cookieSession']];
    }

    // Initialisation de l'ID utilisateur de session
    $userId = '';
    if (isset($_COOKIE[$GLOBALS['cookieUserId']]) && !empty($_COOKIE[$GLOBALS['cookieUserId']])) {
        $userId = $_COOKIE[$GLOBALS['cookieUserId']];
    }

    // Si la session est enregistré dans les cookies
    if ($sessionId != null && $userId != null) {
        $dbsSession = " SELECT date, ip, last_used AS lastConnect, count(*) AS validSession
                        FROM ".SESSIONS_TABLE."
                        WHERE id = '".$sessionId."' AND user_id = '".$userId."' ";
        $dbeSession = mysql_query($dbsSession);
        $dataSession = mysql_fetch_assoc($dbeSession);

        if ($dataSession['date'] > $GLOBALS['time'] - $GLOBALS['timesession'] && $dataSession['ip'] != $GLOBALS['userIp']) {
            $dataSession['validSession'] = 0;
        }

        if ($dataSession['validSession']  == 1) {
            $dbsUser = "SELECT A.pseudo AS nickName, A.idGroup, A.groupMain, B.color AS nickColor
                        FROM ".USER_TABLE." AS A
                        LEFT JOIN ".GROUP_TABLE." AS B
                        ON B.id = A.groupMain
                        WHERE A.id = '".$userId."' ";
            $dbeUser = mysql_query($dbsUser);
            $dataUser = mysql_fetch_assoc($dbeUser);

            $lastVisit = $dataSession['lastConnect'];

            $arrayGroupId = explode('|', $dataUser['idGroup']);
            if(in_array('2', $arrayGroupId)){
                $userType = '2';
            }
            else if(in_array('1', $arrayGroupId)){
                $userType = '1|2';
            }
            else{
                $userType = '2';
            }

            if(!empty($dataUser['idGroup'])){
                $dataUser['idGroup'] = explode('|', $dataUser['idGroup']);
                $whereId = '';
                foreach ($dataUser['idGroup'] as $id) {
                    if(!empty($whereId)){
                        $whereId .= ' OR ';
                    }
                    else{
                        $whereId = ' WHERE ';
                    }
                    $whereId .= 'id = '.intval($id);
                }
                if(!empty($whereId)){
                    $dbsGroups = "SELECT id, access, accessAdmin FROM ".GROUP_TABLE." ".$whereId." ";

                    $dbeGroups = mysql_query($dbsGroups);

                    $arrayAccess = array();
                    $arrayAccessAdmin = array();

                    while($dataGroups = mysql_fetch_assoc($dbeGroups)){
                        $dataGroups['access']      = explode('|', $dataGroups['access']);
                        $dataGroups['accessAdmin'] = explode('|', $dataGroups['accessAdmin']);

                        if($dataGroups['id'] == '1'){
                            $arrayAccess[] = 'ALL';
                            $arrayAccessAdmin[] = 'ALL';
                        }
                        else{
                            foreach ($dataGroups['access'] as $mod) {
                                if(!empty($mod) && !in_array($mod, $arrayAccess)){
                                    $arrayAccess[] = $mod;
                                }
                            }
                            foreach ($dataGroups['accessAdmin'] as $admin) {
                                if(!empty($admin) && !in_array($admin, $arrayAccess)){
                                    $arrayAccessAdmin[] = $admin;
                                }
                            }

                        }
                    }

                    $accessMods  = implode('|', $arrayAccess);
                    $accessAdmin = implode('|', $arrayAccessAdmin);
                }
            }
            else {
                $accessMods  = '';
                $accessAdmin = '';
            }


            $dbuSession = "UPDATE ".SESSIONS_TABLE."  last_used = '".$GLOBALS['time']."' WHERE id = '".$sessionId."'";
            $dbeSession = mysql_query($dbuSession);

            if (isset($_REQUEST['file'])
                && isset($_REQUEST['thread_id'])
                && $_REQUEST['file'] == 'Forum'
                && is_numeric($_REQUEST['thread_id'])
                && $_REQUEST['thread_id'] > 0
                && $dataSession['validSession'] > 0) {
                    $select_thread = "SELECT MAX(id) FROM " . FORUM_MESSAGES_TABLE . " WHERE date > '" . $dataSession['lastConnect'] . "' AND thread_id = '" . $_REQUEST['thread_id'] . "' ";
                    $sql_thread = mysql_query($select_thread);
                    list($max_mess_id) = mysql_fetch_array($sql_thread);

                    if ($max_mess_id > 0) {
                        if (isset($_REQUEST[$GLOBALS['cookieForum']]) && !empty($_REQUEST[$GLOBALS['cookieForum']])){
                            $id_read_forum = $_REQUEST[$GLOBALS['cookieForum']];
                            if (preg_match("`[^0-9,]`i", $id_read_forum)) $id_read_forum = '';
                            $table_read_forum = explode(',',$id_read_forum);
                            if (!in_array($max_mess_id, $table_read_forum)) setcookie($GLOBALS['cookieForum'], $id_read_forum.",".$max_mess_id, $timelimit);
                        }
                        else setcookie($GLOBALS['cookieForum'], $max_mess_id, $timelimit);
                    }
            }
        }
        // Si les informations de sessions sont incorrect
        else {
            mysql_query("DELETE FROM ".SESSIONS_TABLE." WHERE id = '".$sessionId."' ");
            mysql_query("DELETE FROM ".SESSIONS_TABLE." WHERE user_id = '".$userId."' ");
        }
    }
    //Pas de session enregistré
    else {
        $dataSession['validSession'] = 0;
    }

    if ($dataSession['validSession'] == 1) {
        // Récupération du nombre de message en attente
        $dbsUserbox = "SELECT mid, count(mid) AS count FROM ".USERBOX_TABLE." WHERE user_for = '".$userId."' AND status = 0";
        $dbeUserbox = mysql_query($dbsUserbox);
        $dataUserbox = mysql_fetch_assoc($dbeUserbox);
        // On rempli le tableau $user
        $user = array(
                    'id' => $userId,
                    // On conserve la compatibilité $user[1] = 1 (level membre sur les anciens modules)
                    'idGroup' => '0',
                    'nickName' => mysql_real_escape_string($dataUser['nickName']),
                    'ip' => $GLOBALS['userIp'],
                    'lastVisit' => $lastVisit,
                    'nbMess' => $nb_mess,
                    'accessMods' => $accessMods,
                    'accessAdmin' => $accessAdmin,
                    'userType' => $userType,
                    'nickColor' => $dataUser['nickColor']
                );
    }
    else {
        $user = array();
    }
    return $user;
}

function adminCheck() {
    if(isset($_SESSION['admin']) && $_SESSION['admin'] == true){
        return 1;
    }
    return 0;
}


function sessionCheck() {
    if (isset($_COOKIE[$GLOBALS['cookieSession']]) && !empty($_COOKIE[$GLOBALS['cookieSession']])) {
        $session = 1;
    }
    else {
        $sessionId = '';
        $session = 0;
        $user = array();
    }
    return $session;
}

function initCookie() {
    setcookie($GLOBALS['cookieSession'], '');
    setcookie($GLOBALS['cookieSession'], '');
    setcookie($GLOBALS['cookieUserId'], '');
    setcookie($GLOBALS['cookieTheme'], '');
    setcookie($GLOBALS['cookieLang'], '');
    setcookie($GLOBALS['cookieForum'], '');
}

function sessionNew($userId, $rememberMe) {
    //On prend un ID de session unique
    do {
        $sessionId = md5(uniqid());
        $dbeSession = mysql_query('SELECT id FROM '.SESSIONS_TABLE.' WHERE id = \''.$sessionId.'\'');
    }
    while(mysql_num_rows($dbeSession) !== 0);

    initCookie();

    $dbuSessionUpdate = " UPDATE ".SESSIONS_TABLE."
                    SET `id` = '" . $sessionId . "',
                    last_used = date,
                    `date` =  '" . $GLOBALS['time'] . "',
                    `ip` = '" . $GLOBALS['userIp'] . "'
                    WHERE user_id = '" . $userId . "' ";

    $dbeSessionUpdate = mysql_query($dbuSessionUpdate);

    $ins = true;

    if (mysql_affected_rows() == 0){
        $ins = mysql_query("INSERT INTO ".SESSIONS_TABLE." ( `id` , `user_id` , `date` , `ip` , `vars` ) VALUES( '".$sessionId."' , '".$userId."' , '".$GLOBALS['time']."' , '".$GLOBALS['userIp']."', '' )");
    }

    if ($dbeSessionUpdate !== false && $ins !== false) {
        if ($rememberMe == "ok") {
            setcookie($GLOBALS['cookieSession'], $sessionId, $GLOBALS['timelimit']);
            setcookie($GLOBALS['cookieUserId'], $userId, $GLOBALS['timelimit']);
        }
        else {
            setcookie($GLOBALS['cookieSession'], $sessionId);
            setcookie($GLOBALS['cookieUserId'], $userId);
        }
    }
    else {
        mysql_query("DELETE FROM ".SESSIONS_TABLE." WHERE `user_id` = '".$userId."' ");
    }
}
?>
