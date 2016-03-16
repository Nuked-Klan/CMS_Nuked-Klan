<?php
/**
 * index.php
 *
 * Frontend of Defy module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Defy'))
    return;

compteur('Defy');


function index(){
    global $nuked;

    if (!empty($nuked['defie_charte'])) {
        echo "<br /><table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
                . "<tr><td align=\"center\"><big><b>" . _DEFY . "</b></big></td></tr>\n"
                . "<tr><td>&nbsp;</td></tr><tr><td>" . $nuked['defie_charte'] . "</td></tr></table>\n"
                . "<form method=\"post\" action=\"index.php?file=Defy\">\n"
                . "<div style=\"text-align: center;\"><input type=\"hidden\" name=\"op\" value=\"form\" />\n"
                . "<input type=\"submit\" value=\"" . _IAGREE . "\" />&nbsp;<input type=\"button\" value=\"" . _IDESAGREE . "\" onclick=\"javascript:history.back()\" /></div></form>\n";
    } else {
        form();
    }
}

function form(){
    global $nuked, $user, $language;

    define('EDITOR_CHECK', 1);

    if ($language == 'french')
        $date = date('d-m-Y');
    else
        $date = date('m-d-Y');

    $hour = date('H:i');

    if (!empty($nuked['server_ip']) && !empty($nuked['server_port'])) {
        $server_ip = $nuked['server_ip'] . ':' . $nuked['server_port'];
    } else {
        $server_ip = '';
    }

    echo '<script type="text/javascript">
    function checkAddDefy(){
        if (document.getElementById(\'defy_pseudo\').value.length == 0){
            alert(\''. _NONICK .'\');
            return false;
        }
        if (document.getElementById(\'defy_clan\').value.length == 0){
            alert(\''. _NOCLAN .'\');
            return false;
        }
        if (! isEmail(\'defy_mail\')){
            alert(\''. _BADMAIL .'\');
            return false;
        }
        if (document.getElementById(\'defy_icq\').value.length == 0){
            alert(\''. _NOICQ .'\');
            return false;
        }

        defyDate = document.getElementById(\'defyDate\').value;

        if (defyDate != "" && ! checkDateValue(\''. $language . '\', defyDate, \'-\')){
            alert(\''. _BADDATE .'\');
            return false;
        }

        defyHour = document.getElementById(\'defyHour\').value;

        if (defyHour != "" && ! checkTimeValue(defyHour)){
            alert(\''. _BADTIME .'\');
            return false;
        }

        return true;
    }
    </script>';

    if($user){
        $userName = $user[2];
    }
    else{
        $userName = '';
    }

    echo "<br /><form method=\"post\" action=\"index.php?file=Defy\" onsubmit=\"return checkAddDefy();\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
            . "<tr><td colspan=\"2\" align=\"center\"><big><b>" . _DEFY . "</b></big></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _NICK . " : </b></td><td><input id=\"defy_pseudo\" type=\"text\" name=\"pseudo\" value=\"" . $userName . "\" size=\"20\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _CLAN . " : </b></td><td><input id=\"defy_clan\" type=\"text\" name=\"clan\" size=\"20\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _COUNTRY . " : </b></td><td><select name=\"country\">\n";

    if ($language == 'french'){
        $pays = 'France.gif';
    }

    $rep = array();
    $handle = @opendir('images/flags');
    while (false !== ($f = readdir($handle))){
        if ($f != '..' && $f != '.' && $f != 'index.html' && $f != 'Thumbs.db'){
            $rep[] = $f;
        }
    }

    closedir($handle);
    sort($rep);
    reset($rep);

    while (list($key, $filename) = each($rep)) {
            if ($filename == $pays){
                $checked = 'selected="selected"';
            }
            else{
                $checked = '';
            }

            list ($country, $ext) = explode('.', $filename);
            echo "<option value=\"" . $filename . "\" " . $checked . ">" . $country . "</option>\n";
    }

    echo "</select></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _MAIL . " : </b></td><td><input id=\"defy_mail\" type=\"text\" name=\"mail\" size=\"25\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _ICQMSN . " : </b></td><td><input id=\"defy_icq\" type=\"text\" name=\"icq\" size=\"25\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _CHANIRC . " : </b></td><td><input type=\"text\" name=\"irc\" size=\"25\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _WEBSITE . " : </b></td><td><input type=\"text\" name=\"url\" value=\"http://\" size=\"30\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _DATE . " : </b></td><td><input id=\"defyDate\" type=\"text\" name=\"date\" value=\"" . $date . "\" size=\"15\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _DHOUR . " : </b></td><td><input id=\"defyHour\" type=\"text\" name=\"heure\" value=\"" . $hour . "\" size=\"6\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _GAME . " : </b></td><td><select name=\"game\">\n";

    $sql = nkDB_execute('SELECT id, name FROM ' . GAMES_TABLE . ' ORDER BY name');
    while (list($game_id, $nom) = nkDB_fetchArray($sql)){
        $nom = printSecuTags($nom);
        echo "<option value=\"" . $game_id . "\">" . $nom . "</option>\n";
    }

    echo "</select></td></tr><tr><td style=\"width: 20%;\"><b>" . _SERVER . " : </b></td><td><input type=\"text\" name=\"serveur\" value=\"" . $server_ip . "\" size=\"30\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _TYPE . " : </b></td><td><input type=\"text\" name=\"type\" value=\"\" size=\"20\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _DMAP . " : </b></td><td><input type=\"text\" name=\"map\" value=\"\" size=\"20\" /></td></tr>\n"
            . "<tr><td style=\"width: 20%;\"><b>" . _COMMENT . " : </b></td><td><textarea id=\"e_basic\" name=\"comment\" cols=\"60\" rows=\"10\"></textarea></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n";

    echo "<tr><td colspan=\"2\" align=\"center\">";

    if (initCaptcha()) echo create_captcha();

    echo "<input type=\"submit\" value=\"" . __('SEND') . "\" /><input type=\"hidden\" name=\"op\" value=\"send_defie\" /></td></tr></table></form><br />\n";
}

function send_defie($pseudo, $clan, $country, $mail, $icq, $irc, $url, $date, $heure, $game, $serveur, $type, $map, $comment){
    global $nuked, $language;

    // Verification code captcha
    if (initCaptcha() && ! validCaptchaCode())
        return;

    // TODO Check if username is ban ?
    if ($pseudo == '' || ctype_space($pseudo)) {
        printNotification(stripslashes(_NONICK), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    if ($clan == '' || ctype_space($clan)) {
        printNotification(stripslashes(_NOCLAN), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    // TODO Check if email is ban ?
    $mail = stripslashes($mail);
    $mail = nkHtmlEntities(nkHtmlEntityDecode($mail));

    $mail = checkEmail($mail, false, false);

    if (($error = getCheckEmailError($mail)) !== false) {
        printNotification($error, 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    if ($icq == '' || ctype_space($icq)) {
        printNotification(stripslashes(_NOICQ), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    $defyHour = $defyDate = '';

    if ($heure != '') {
        $timeArray = explode(':', $heure, 2);
        $hour      = (isset($timeArray[0])) ? (int) $timeArray[0] : null;
        $minute    = (isset($timeArray[1])) ? (int) $timeArray[1] : null;

        if ($hour === null || $minute === null || $hour > 24 || $hour < 0 || $minute > 60 || $minute < 0) {
            printNotification(_BADTIME, 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }

        $defyHour = $hour .':'. $minute;
    }

    if ($date != '') {
        $dateArray = explode('-', $date, 3);

        if ($language == 'french') {
            $day   = (isset($dateArray[0])) ? (int) $dateArray[0] : null;
            $month = (isset($dateArray[1])) ? (int) $dateArray[1] : null;
        }
        else {
            $month = (isset($dateArray[0])) ? (int) $dateArray[0] : null;
            $day   = (isset($dateArray[1])) ? (int) $dateArray[1] : null;
        }

        $year = (isset($dateArray[2])) ? (int) $dateArray[2] : null;

        if ($day === null || $month === null || $year === null || ! checkdate($month, $day, $year)) {
            printNotification(_BADDATE, 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }

        $defyDate = $day .'-'. $month .'-'. $year;
    }

    $url = trim($url);

    if ($url == 'http://') {
        $url = '';
    }
    else if ($url != '' && stripos($url, 'http://') === 0)
    {
        $url = "http://" . $url;
    }

    $email = $nuked['defie_mail'];
    $inbox = $nuked['defie_inbox'];
    $time = time();
    $date2 = nkDate($time);
    $comment = secu_html(nkHtmlEntityDecode($comment));
    $pseudo = nkDB_realEscapeString(stripslashes($pseudo));
    $clan = nkDB_realEscapeString(stripslashes($clan));
    $country = nkDB_realEscapeString(stripslashes($country));
    $mail = nkDB_realEscapeString($mail);
    $icq = nkDB_realEscapeString(stripslashes($icq));
    $irc = nkDB_realEscapeString(stripslashes($irc));
    $url = nkDB_realEscapeString(stripslashes($url));
    $serveur = nkDB_realEscapeString(stripslashes($serveur));
    $type = nkDB_realEscapeString(stripslashes($type));
    $map = nkDB_realEscapeString(stripslashes($map));
    $comment = nkDB_realEscapeString(stripslashes($comment));

    $game = (int) $game;

    $pseudo = printSecuTags($pseudo);
    $clan = printSecuTags($clan);
    $country = nkHtmlEntities($country);
    $icq = nkHtmlEntities($icq);
    $irc = nkHtmlEntities($irc);
    $url = nkHtmlEntities($url);
    $serveur = nkHtmlEntities($serveur);
    $type = printSecuTags($type);
    $map = printSecuTags($map);

    nkDB_execute(
        "INSERT INTO ". DEFY_TABLE ."
        (`send`, `pseudo`, `clan`, `mail`, `icq`, `irc`, `url`, `pays`, `date`, `heure`, `serveur`, `game`, `type`, `map` ,`comment`)
        VALUES
        ('". $time ."', '". $pseudo ."', '". $clan ."', '". $mail ."', '". $icq ."', '". $irc ."', '". $url ."', '". $country ."', '". $defyDate ."', '". $defyHour ."', '". $serveur ."', '". $game ."', '". $type ."', '". $map ."', '". $comment ."')"
    );

    saveNotification(_NOTDEF .': [<a href="index.php?file=Defy&page=admin">'. _TLINK .'</a>].');

    $subject = _DEFY . ', ' .$date2;
    $corps = $pseudo . " " . _NEWDEFY . "\r\n" . $nuked['url'] . "/index.php?file=Defy&page=admin\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
    $from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $mail;

    if (!empty($email)) {
        @mail($email, @nkHtmlEntityDecode($subject), @nkHtmlEntityDecode($corps), @nkHtmlEntityDecode($from));
    }

    if (!empty($inbox)){
        nkDB_execute(
            "INSERT INTO ". USERBOX_TABLE ."
            (`user_from`, `user_for`, `titre`, `message`, `date`
            VALUES
            ('". nkDB_realEscapeString($inbox) ."', '". nkDB_realEscapeString($inbox) ."', '". nkDB_realEscapeString($subject) ."', '". nkDB_realEscapeString($corps) ."', '". $time ."')"
        );
    }

    printNotification(_SENDMAIL, 'success');
    redirect('index.php', 2);
}


opentable();

switch ($GLOBALS['op']){
    case 'index':
    index();
    break;

    case 'form':
    form();
    break;

    case 'send_defie':
    send_defie($_REQUEST['pseudo'], $_REQUEST['clan'], $_REQUEST['country'], $_REQUEST['mail'], $_REQUEST['icq'], $_REQUEST['irc'], $_REQUEST['url'], $_REQUEST['date'], $_REQUEST['heure'], $_REQUEST['game'], $_REQUEST['serveur'], $_REQUEST['type'], $_REQUEST['map'], $_REQUEST['comment']);
    break;

    default:
    index();
    break;
}

closetable();

?>
