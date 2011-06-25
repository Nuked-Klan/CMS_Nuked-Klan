<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
define ('INDEX_CHECK', 1);
include ('globals.php');
include ('conf.inc.php');
include ('nuked.php');
include ('Includes/constants.php');

global $nuked, $language, $theme, $bgcolor1, $bgcolor2, $bgcolor3;

if (preg_match('`\.\.`', $theme) || preg_match('`\.\.`', $language) || preg_match('`[A-Za-z]`', $_GET['ip_ban']))
{
    die('<br /><br /><br /><div style="text-align: center"><big>What are you trying to do ?</big></div>');
}

$theme = trim($theme);
$language = trim($language);

include ('themes/' . $theme . '/colors.php');
translate ('lang/' . $language . '.lang.php');

$ip_ban = $_GET['ip_ban'];

$sql = mysql_query('SELECT texte, dure FROM ' . BANNED_TABLE . ' WHERE ip = "' . $ip_ban . '"');
$count = mysql_num_rows($sql);

if ($count > 0)
{
    list($texte_ban, $dure) = mysql_fetch_array($sql);
    setcookie('ip_ban', $ip_ban, time() + 9999999, '', '', '');

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head><title>' . $nuked['name'] . ' - ' . $nuked['slogan'] . '</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta http-equiv="content-style-type" content="text/css" />
    <link title="style" type="text/css" rel="stylesheet" href="themes/' . $theme . '/style.css" /></head>
    <body style="background : ' . $bgcolor2 . '"><div style="margin: 200px auto; padding: 20px; width: 800px; border: 1px solid ' . $bgcolor3 . '; background: ' . $bgcolor1 . '; text-align: center">
    <big><b>' . $nuked['name'] . ' - ' . $nuked['slogan'] . '</b><br /><br />
    ' . _IPBANNED . '</big>';

    if (!empty($texte_ban))
    {
        echo '<br /><p><hr style="color: ' . $bgcolor3 . ';height: 1px; width: 95%" />
        <big><b>' . _REASON . '</b><br>' . html_entity_decode($texte_ban) . '</big></p>';
    }
    
    if($dure == 0) $temps = _AVIE;
    else if ($dure == 86400) $temps = _1JOUR;
    else if ($dure == 604800) $temps = _7JOUR;
    else if ($dure == 2678400) $temps = _1MOIS;
    else if ($dure == 31708800) $temps = _1AN;

    echo '<hr style="color: ' . $bgcolor3 . ';height: 1px; width: 95%" /><br />' . _DURE . '
    ' . $temps . '<br />
    ' . _CONTACTWEBMASTER . ' : <a href="mailto:' . $nuked['mail'] . '">' . $nuked['mail'] . '</a></div></body></html>';
}
?>