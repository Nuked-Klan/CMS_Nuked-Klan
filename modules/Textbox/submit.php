<?php
/**
 * submit.php
 *
 * Frontend of Textbox module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

translate('modules/Textbox/lang/'. $language .'.lang.php');


global $visiteur;

$redirection = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : 'index.php';
$level_access = nivo_mod("Textbox");

if (! isset($_REQUEST['ajax'])) opentable();

if ($visiteur >= $level_access && $level_access > -1)
{
    // Captcha check
    if (initCaptcha() && ! validCaptchaCode()) {
        if (! isset($_REQUEST['ajax'])) closetable();
        return;
    }

    if (isset($user[2]))
    {
        $pseudo = $user[2];
    }
    else
    {
    	$_REQUEST['auteur'] =  utf8_decode($_REQUEST['auteur']);
        $_REQUEST['auteur'] = nkHtmlEntities($_REQUEST['auteur'], ENT_QUOTES);
        $_REQUEST['auteur'] = checkNickname($_REQUEST['auteur']);

        if (($error = getCheckNicknameError($_REQUEST['auteur'])) !== false) {
            printNotification(nkHtmlEntities($error), 'error');

            if (! isset($_REQUEST['ajax'])) {
                redirect($redirection, 2);
                closetable();
            }

            return;
        }

        $pseudo = $_REQUEST['auteur'];
    }

    $sql2 = mysql_query("SELECT auteur, ip, date FROM " . TEXTBOX_TABLE . " ORDER BY id DESC LIMIT 0, 1");
    list($author, $flood_ip, $flood_date) = mysql_fetch_array($sql2);

    $anti_flood = $flood_date + 5;

    $date = time();

	$_REQUEST['texte'] =  utf8_decode($_REQUEST['texte']);

    $_REQUEST['texte'] = mysql_real_escape_string(stripslashes($_REQUEST['texte']));
    $pseudo = mysql_real_escape_string(stripslashes($pseudo));

    if ($user_ip == $flood_ip && $date < $anti_flood && $visiteur == 0)
    {
        printNotification(nkHtmlEntities(_NOFLOOD), 'error');

        if (! isset($_REQUEST['ajax'])) redirect($redirection, 2);
    }

    else if ($_REQUEST['texte'] != "")
    {
        $sql = mysql_query("INSERT INTO " . TEXTBOX_TABLE . " ( `id` , `auteur` , `ip` , `texte` , `date` ) VALUES ( '' , '" . $pseudo . "' ,'" . $user_ip . "' , '" . $_REQUEST['texte'] . "' , '" . $date . "' )");

        printNotification(nkHtmlEntities(_SHOUTSUCCES), 'success');

        if (! isset($_REQUEST['ajax'])) redirect($redirection, 2);
    }

    else
    {
        printNotification(nkHtmlEntities(_NOTEXT), 'error');

        if (! isset($_REQUEST['ajax'])) redirect($redirection, 2);
    }
}
else
{
    printNotification(nkHtmlEntities(__('NO_ENTRANCE')), 'error');

    if (! isset($_REQUEST['ajax'])) redirect($redirection, 2);
}

if (! isset($_REQUEST['ajax'])) closetable();

?>