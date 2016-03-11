<?php
/**
 * ban.php
 *
 * Display user ban page or remove ban if delay is exceed
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
define('INDEX_CHECK', 1);
ini_set('default_charset', 'ISO8859-1');

require_once 'Includes/fatal_errors.php';
require_once 'globals.php';
require_once 'conf.inc.php';
require_once 'nuked.php';


if (filter_var($_GET['ip_ban'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6))
    die(WAYTODO);

translate('lang/'. $language .'.lang.php');

$escapeBannedIp = nkDB_quote($_GET['ip_ban']);

$dbrBanned = nkDB_selectOne(
    'SELECT texte, date, dure, pseudo
    FROM '. BANNED_TABLE .'
    WHERE ip = '. $escapeBannedIp .' OR pseudo = '. nkDB_quote($_GET['user'])
);

$time = time();

if (nkDB_numrows() > 0) {
    // On supprime les bans dépassés, 0 = A vie
    if ($dbrBanned['dure'] != 0 && ($dbrBanned['date'] + $dbrBanned['dure']) < $time) {
        // On supprime l'entrée SQL
        nkDB_delete(BANNED_TABLE, 'ip = '. $escapeBannedIp);

        // On supprime le cookie
        setcookie('ip_ban', '', $time - 3600);

        // On notifie dans l'administration
        saveNotification($dbrBanned['pseudo'] . __('BAN_FINISHED'), NOTIFICATION_WARNING);

        // On redirige vers le site
        redirect('index.php');
    }

    // Sinon on prolongue la durée de vie du cookie.
    setcookie('ip_ban', $_GET['ip_ban'], $time + 9999999, '', '', '');

    if ($dbrBanned['dure'] == 0) $duration = __('FOREVER');
    else if ($dbrBanned['dure'] == 86400) $duration = __('1DAY');
    else if ($dbrBanned['dure'] == 604800) $duration = __('7JOUR');
    else if ($dbrBanned['dure'] == 2678400) $duration = __('1MONTH');
    else if ($dbrBanned['dure'] == 31708800) $duration = __('1YEAR');

    nkTemplate_setBgColors();
    nkTemplate_setPageDesign('nudePage');

    $content = applyTemplate('banishmentMessage', array(
        'reason'    => $dbrBanned['texte'],
        'duration'  => $duration
    ));

    echo nkTemplate_renderPage($content);
}
else {
    if (isset($_COOKIE['ip_ban']) && $_COOKIE['ip_ban'] != '')
        setcookie('ip_ban', '', $time - 3600);

    redirect('index.php');
}

?>