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


if (filter_var($_GET['ip_ban'], FILTER_VALIDATE_IP,
    FILTER_FLAG_IPV4 |
    FILTER_FLAG_IPV6 |
    FILTER_FLAG_NO_PRIV_RANGE |
    FILTER_FLAG_NO_RES_RANGE)
)
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
        nkDB_insert(NOTIFICATIONS_TABLE,
            array('date', 'type', 'texte'),
            array(time(), 4, $dbrBanned['pseudo'] . _BANFINISHED);

        // On redirige vers le site
        redirect('index.php');
    }

    // Sinon on prolongue la durée de vie du cookie.
    setcookie('ip_ban', $bannedIp, time() + 9999999, '', '', '');

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head><title>' . $nuked['name'] . ' - ' . $nuked['slogan'] . '</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="content-style-type" content="text/css" />
    <link title="style" type="text/css" rel="stylesheet" href="themes/' . $theme . '/style.css" /></head>
    <body style="background : ' . $bgcolor2 . '"><div style="margin: 200px auto; padding: 20px; width: 800px; border: 1px solid ' . $bgcolor3 . '; background: ' . $bgcolor1 . '; text-align: center">
    <big><b>' . $nuked['name'] . ' - ' . $nuked['slogan'] . '</b><br /><br />
    ' . _IPBANNED . '</big>';

    if (!empty($dbrBanned['texte'])) {
        echo '<br /><p><hr style="color: ' . $bgcolor3 . ';height: 1px; width: 95%" />
        <big><b>' . _REASON . '</b><br>' . nkHtmlEntityDecode($dbrBanned['texte']) . '</big></p>';
    }

    if ($dbrBanned['dure'] == 0) $duration = _AVIE;
    else if ($dbrBanned['dure'] == 86400) $duration = _1JOUR;
    else if ($dbrBanned['dure'] == 604800) $duration = _7JOUR;
    else if ($dbrBanned['dure'] == 2678400) $duration = _1MOIS;
    else if ($dbrBanned['dure'] == 31708800) $duration = _1AN;

    echo '<hr style="color: ' . $bgcolor3 . ';height: 1px; width: 95%" /><br />' . _DURE . '
    ' . strtolower($duration) . '<br />
    ' . _CONTACTWEBMASTER . ' : <a href="mailto:' . $nuked['mail'] . '">' . $nuked['mail'] . '</a></div></body></html>';
}
else {
    if (isset($_COOKIE['ip_ban']) && ! empty($_COOKIE['ip_ban']))
        $_COOKIE['ip_ban'] = '';

    redirect('index.php');
}

?>