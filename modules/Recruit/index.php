<?php
/**
 * index.php
 *
 * Frontend of Recruit module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Recruit'))
    return;

compteur('Recruit');


opentable();

if ($nuked['recrute'] > 0)
{
    function index()
    {
        global $nuked;

        if ($nuked['recrute_charte'] != '')
        {
            echo "<br /><table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
            . "<tr><td align=\"center\"><big><b>" . _RECRUIT . "</b></big></td></tr>\n"
            . "<tr><td>&nbsp;</td></tr><tr><td>" . $nuked['recrute_charte'] . "</td></tr></table>\n"
            . "<form method=\"post\" action=\"index.php?file=Recruit\">\n"
            . "<div style=\"text-align: center;\"><input type=\"hidden\" name=\"op\" value=\"form\" />\n"
            . "<input type=\"submit\" value=\"" . _IAGREE . "\" />&nbsp;<input type=\"button\" value=\"" . _IDESAGREE . "\" onclick=\"javascript:history.back()\" /></div></form>\n";
        }
        else
        {
            form();
        }
    }

    function form()
    {
        global $nuked, $user, $language;

        define('EDITOR_CHECK', 1);

    echo '<script type="text/javascript">
    function checkAddRecruit(){
        if (document.getElementById(\'recruit_pseudo\').value.length == 0){
            alert(\''. _NONICK .'\');
            return false;
        }
        if (document.getElementById(\'recruit_lastname\').value.length == 0){
            alert(\''. _NOLASTNAME .'\');
            return false;
        }
        if (document.getElementById(\'recruit_age\').value.length == 0) {
            alert(\''. _NOAGE .'\');
            return false;
        }
        if(! document.getElementById(\'recruit_age\').value.match(/^\d+$/)){
            alert(\''. _BADAGE .'\');
            return false;
        }
        if (! isEmail(\'recruit_mail\')){
            alert(\''. _BADMAIL .'\');
            return false;
        }
        if (document.getElementById(\'recruit_icq\').value.length == 0){
            alert(\''. _NOICQ .'\');
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

        echo "<br /><form method=\"post\" action=\"index.php?file=Recruit\" onsubmit=\"return checkAddRecruit();\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
        . "<tr><td colspan=\"2\" align=\"center\"><big><b>" . _RECRUIT . "</b></big></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n"
        . "<tr><td style=\"width: 20%;\"><b>" . _NICK . " : </b></td><td><input id=\"recruit_pseudo\" type=\"text\" name=\"pseudo\" value=\"" . $userName . "\" size=\"20\" /></td></tr>\n"
        . "<tr><td style=\"width: 20%;\"><b>" . _FIRSTNAME . " : </b></td><td><input id=\"recruit_lastname\" type=\"text\" name=\"prenom\" size=\"20\" /></td></tr>\n"
        . "<tr><td style=\"width: 20%;\"><b>" . _AGE . " : </b></td><td><input id=\"recruit_age\" type=\"text\" name=\"age\" size=\"3\" /></td></tr>\n"
        . "<tr><td style=\"width: 20%;\"><b>" . _MAIL . " : </b></td><td><input id=\"recruit_mail\" type=\"text\" name=\"mail\" size=\"25\" /></td></tr>\n"
        . "<tr><td style=\"width: 20%;\"><b>" . _ICQMSN . " : </b></td><td><input id=\"recruit_icq\" type=\"text\" name=\"icq\" size=\"25\" /></td></tr>\n"
        . "<tr><td style=\"width: 20%;\"><b>" . _COUNTRY . " : </b></td><td><select name=\"country\">\n";

        if ($language == "french")
        {
            $pays = "France.gif";
        }

        $rep = Array();
        $handle = @opendir("images/flags");
        while (false !== ($f = readdir($handle)))
        {
            if ($f != ".." && $f != "." && $f != "index.html" && $f != "Thumbs.db")
            {
                $rep[] = $f;
            }
        }

        closedir($handle);
        sort ($rep);
        reset ($rep);

        while (list ($key, $filename) = each ($rep))
        {
            if ($filename == $pays)
            {
                $checked = "selected=\"selected\"";
            }
            else
            {
                $checked = "";
            }

            list ($country, $ext) = explode ('.', $filename);
            echo "<option value=\"" . $filename . "\" " . $checked . ">" . $country . "</option>\n";
        }

        echo "</select></td></tr><tr><td style=\"width: 20%;\"><b>" . _GAME . " : </b></td><td><select name=\"game\">\n";

        $sql = nkDB_execute("SELECT id, name FROM " . GAMES_TABLE . " ORDER BY name");
        while (list($game_id, $nom) = nkDB_fetchArray($sql))
        {
            $nom = printSecuTags($nom);
            echo "<option value=\"" . $game_id . "\">" . $nom . "</option>\n";
        }

        echo "</select></td></tr><tr><td style=\"width: 20%;\"><b>" . _CONNECT . " : </b></td><td><select name=\"connex\">\n"
        . "<option>" . _56K . "</option>\n"
        . "<option>" . _NUMERIS . "</option>\n"
        . "<option>" . _ADSL . "</option>\n"
        . "<option>" . _CABLE . "</option>\n"
        . "<option>" . _T1 . "</option>\n"
        . "</select></td></tr><tr><td style=\"width: 20%;\"><b>" . _EXPERIENCE . " : </b></td><td><select name=\"exp\">\n"
        . "<option>" . _LESS1MONTH . "</option>\n"
        . "<option>" . _LESS6MONTH . "</option>\n"
        . "<option>" . _LESS1YEAR . "</option>\n"
        . "<option>" . _MORE1YEAR . "</option>\n"
        . "<option>" . _MORE2YEAR . "</option>\n"
        . "</select></td></tr><tr><td style=\"width: 20%;\"><b>" . _AVAILABLE . " : </b></td><td><select name=\"dispo\">\n"
        . "<option>" . _EVENING . "</option>\n"
        . "<option>" . _WEEKEND . "</option>\n"
        . "<option>" . _HOLIDAY . "</option>\n"
        . "<option>" . _THREE . "</option>\n"
        . "<option>" . _OTHER . "</option>\n"
        . "</select></td></tr><tr><td style=\"width: 20%;\"><b>" . _COMMENT . " : </b></td><td><textarea id=\"e_basic\" name=\"comment\" cols=\"60\" rows=\"10\"></textarea></td></tr>"
        . "<tr><td colspan=\"2\">&nbsp;</td></tr>\n"
        . "<tr><td colspan=\"2\" align=\"center\">";

        if (initCaptcha()) echo create_captcha();

        echo "<input type=\"submit\" value=\"" . __('SEND') . "\" /><input type=\"hidden\" name=\"op\" value=\"send_recruit\" /></td></tr></table></form><br />\n";
    }

    function send_recruit($pseudo, $prenom, $age, $mail, $icq, $country, $game, $connex, $exp, $dispo, $comment)
    {
        global $nuked;

        // Checking captcha
        if (initCaptcha() && ! validCaptchaCode())
            return;

        // TODO Check if username is ban ?
        if ($pseudo == '' || ctype_space($pseudo)) {
            printNotification(stripslashes(_NONICK), 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }

        if ($prenom == '' || ctype_space($prenom)) {
            printNotification(stripslashes(_NOLASTNAME), 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }

        if ($age == '') {
            printNotification(_NOAGE, 'error', array('backLinkUrl' => 'javascript:history.back()'));
            closetable();
            return;
        }

        if (! ctype_digit($age)) {
            printNotification(_BADAGE, 'error', array('backLinkUrl' => 'javascript:history.back()'));
            closetable();
            return;
        }

        // TODO Check if email is ban ?
        if (($mail = checkEmail($mail, false, false)) === false) {
            printNotification(getCheckEmailError($mail), 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }

        if ($icq == '' || ctype_space($icq)) {
            printNotification(stripslashes(_NOICQ), 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }

        $inbox = $nuked['recrute_inbox'];
        $email = $nuked['recrute_mail'];
        $date = time();
        $date2 = nkDate($date);

        $comment = secu_html(nkHtmlEntityDecode($comment));

        $pseudo = nkDB_realEscapeString(stripslashes($pseudo));
        $prenom = nkDB_realEscapeString(stripslashes($prenom));
        $mail = nkDB_realEscapeString(stripslashes($mail));
        $icq = nkDB_realEscapeString(stripslashes($icq));
        $country = nkDB_realEscapeString(stripslashes($country));
        $connex = nkDB_realEscapeString(stripslashes($connex));
        $exp = nkDB_realEscapeString(stripslashes($exp));
        $dispo = nkDB_realEscapeString(stripslashes($dispo));
        $comment = nkDB_realEscapeString(stripslashes($comment));

        $age = (int) $age;

        $pseudo = nkHtmlEntities($pseudo);
        $prenom = nkHtmlEntities($prenom);
        $mail = nkHtmlEntities($mail);
        $icq = nkHtmlEntities($icq);
        $country = nkHtmlEntities($country);
        $connex = nkHtmlEntities($connex);
        $exp = nkHtmlEntities($exp);
        $dispo = nkHtmlEntities($dispo);

        nkDB_execute(
            "INSERT INTO ". RECRUIT_TABLE ."
            (`date`, `pseudo`, `prenom`, `age`, `mail`, `icq`, `country`, `game`, `connection`, `experience`, `dispo`, `comment`)
            VALUES
            ('". $date ."', '". $pseudo ."', '". $prenom ."', '". $age ."', '". $mail ."', '". $icq ."', '". $country ."', '". $game ."', '". $connex ."', '". $exp ."', '". $dispo ."', '". $comment."')"
        );

        saveNotification(_NOTDEM .': [<a href="index.php?file=Recruit&page=admin">'. _TLINK .'</a>].');

        $subject = _RECRUIT . ", " . $date2;
        $corps = $pseudo . " " . _NEWRECRUIT . "\r\n" . $nuked['url'] . "/index.php?file=Recruit&page=admin\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
        $from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $mail;

        $subject = @nkHtmlEntityDecode($subject);
        $corps = @nkHtmlEntityDecode($corps);
        $from = @nkHtmlEntityDecode($from);

        if ($email != "")
        {
            mail($email, $subject, $corps, $from);
        }

        if ($inbox != "")
        {
            nkDB_execute(
                "INSERT INTO ". USERBOX_TABLE ."
                (`user_from`, `user_for`, `titre`, `message`, `date`)
                VALUES
                ('". $inbox ."', '". $inbox ."', '". $subject ."', '". $corps ."', '". $date ."')"
            );
        }

        printNotification(_SENDRECRUIT, 'success');
        redirect("index.php", 2);
    }

    switch ($GLOBALS['op'])
    {
        case"index":
            index();
            break;

        case"form":
            form();
            break;

        case"send_recruit":
            send_recruit($_REQUEST['pseudo'], $_REQUEST['prenom'], $_REQUEST['age'], $_REQUEST['mail'], $_REQUEST['icq'], $_REQUEST['country'], $_REQUEST['game'], $_REQUEST['connex'], $_REQUEST['exp'], $_REQUEST['dispo'], $_REQUEST['comment']);
            break;

        default:
            index();
            break;
    }
}
else
{
    printNotification(_RECRUITOFF);
}

closetable();

?>
