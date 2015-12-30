<?php
/**
 * ban.php
 *
 * Display user ban page or remove ban if delay is exceed
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
define('INDEX_CHECK', 1);
ini_set('default_charset', 'ISO8859-1');

require_once 'Includes/fatal_errors.php';
require_once 'globals.php';
require_once 'conf.inc.php';
require_once 'nuked.php';


/**
 * Checks for forbidden characters in request parameters
 */
nkHandle_URIInjections();


if (filter_var($_GET['ip_ban'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6))
    die(WAYTODO);

require_once 'themes/'. $theme .'/colors.php';
translate('lang/'. $language .'.lang.php');

$bannedIp       = nkDB_escape($_GET['ip_ban']);
$bannerUsername = nkDB_escape($_GET['user']);

$dbrBanned = nkDB_selectOne(
    'SELECT texte, date, dure, pseudo
    FROM '. BANNED_TABLE .'
    WHERE ip = '. $bannedIp .' OR pseudo = '. $bannerUsername);

if (nkDB_numrows() > 0) {
    // On supprime les bans dépassés, 0 = A vie
    if ($dbrBanned['dure'] != 0 && ($dbrBanned['date'] + $dbrBanned['dure']) < time()) {
        // On supprime l'entrée SQL
        nkDB_delete(BANNED_TABLE, 'ip = ' . $bannedIp)

        // On supprime le cookie
        $_COOKIE['ip_ban'] = '';

        // On notifie dans l'administration
        nkDB_insert(NOTIFICATIONS_TABLE, array(
            'date'  => time(),
            'type'  => 4,
            'texte' => $dbrBanned['pseudo'] . _BANFINISHED
        ));

        // On redirige vers le site
        redirect('index.php');
    }

    // Sinon on prolongue la durée de vie du cookie.
    setcookie('ip_ban', $bannedIp, time() + 9999999, '', '', '');

    if ($dbrBanned['dure'] == 0) $duration = _AVIE;
    else if ($dbrBanned['dure'] == 86400) $duration = _1JOUR;
    else if ($dbrBanned['dure'] == 604800) $duration = _7JOUR;
    else if ($dbrBanned['dure'] == 2678400) $duration = _1MOIS;
    else if ($dbrBanned['dure'] == 31708800) $duration = _1AN;

    echo applyTemplate('banishmentMessage', array(
        'reason'    => $dbrBanned['texte'],
        'duration'  => $duration
    ));
}
else {
    if (isset($_COOKIE['ip_ban']) && ! empty($_COOKIE['ip_ban']))
        $_COOKIE['ip_ban'] = '';

    redirect('index.php');
}

?>