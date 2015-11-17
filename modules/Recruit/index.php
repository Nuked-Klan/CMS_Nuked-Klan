<?php
/**
 * index.php
 *
 * Frontend of Recruit module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Recruit'))
    return;

compteur('Recruit');

$captcha = initCaptcha();

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
            . "<tr><td>&nbsp;</td></tr><tr><td>" . nkHtmlEntityDecode($nuked['recrute_charte']) . "</td></tr></table>\n"
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

        echo "<script type=\"text/javascript\">\n"
        ."<!--\n"
        ."\n"
        . "function verifchamps()\n"
        . "{\n"
        . "if (document.getElementById('recruit_pseudo').value.length == 0)\n"
        . "{\n"
        . "alert('" . _NONICK . "');\n"
        . "return false;\n"
        . "}\n"
        . "\n"
        . "if (document.getElementById('recruit_lastname').value.length == 0)\n"
        . "{\n"
        . "alert('" . _NOLASTNAME . "');\n"
        . "return false;\n"
        . "}\n"
        . "\n"
        . "if (document.getElementById('recruit_age').value.length == 0)\n"
        . "{\n"
        . "alert('" . _NOAGE . "');\n"
        . "return false;\n"
        . "}\n"
        ."\n"
        . "if (isNaN(document.getElementById('recruit_age').value))\n"
        . "{\n"
        . "alert('" . _BADAGE . "');\n"
        . "return false;\n"
        . "}\n"
        ."\n"
        ."if (document.getElementById('recruit_mail').value.indexOf('@') == -1)\n"
        ."{\n"
        ."alert('" . _BADMAIL . "');\n"
        ."return false;\n"
        ."}\n"
        ."\n"
        . "if (document.getElementById('recruit_icq').value.length == 0)\n"
        . "{\n"
        . "alert('" . _NOICQ . "');\n"
        . "return false;\n"
        . "}\n"
        ."\n"
        . "return true;\n"
        . "}\n"
        ."\n"
        . "// -->\n"
        . "</script>\n";

        if(array_key_exists(2, $user)){
            $userName = $user[2];
        }
        else{
            $userName = '';
        }

        echo "<br /><form method=\"post\" action=\"index.php?file=Recruit\" onsubmit=\"return verifchamps();\">\n"
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

        $sql = mysql_query("SELECT id, name FROM " . GAMES_TABLE . " ORDER BY name");
        while (list($game_id, $nom) = mysql_fetch_array($sql))
        {
            $nom = nkHtmlEntities($nom);
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
        . "</select></td></tr><tr><td style=\"width: 20%;\"><b>" . _COMMENT . " : </b></td><td><textarea id=\"e_basic\" name=\"comment\" cols=\"60\" rows=\"10\"></textarea></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n";

        if ($GLOBALS['captcha'] === true) echo create_captcha();

        echo "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"" . _SEND . "\" /><input type=\"hidden\" name=\"op\" value=\"send_recruit\" /></td></tr></table></form><br />\n";
    }

    function send_recruit($pseudo, $prenom, $age, $mail, $icq, $country, $game, $connex, $exp, $dispo, $comment)
    {
        global $nuked;

        // Checking captcha
        if ($GLOBALS['captcha'] === true) {
            ValidCaptchaCode();
        }

        if (!is_numeric($age)) {
            echo "<br /><br /><div style=\"text-align: center;\">" . _BADAGE . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div><br /><br />";
            closetable();
            footer();
            exit();
        }

        $inbox = $nuked['recrute_inbox'];
        $email = $nuked['recrute_mail'];
        $date = time();
        $date2 = nkDate($date);

        $comment = secu_html(nkHtmlEntityDecode($comment));

        $pseudo = mysql_real_escape_string(stripslashes($pseudo));
        $prenom = mysql_real_escape_string(stripslashes($prenom));
        $age = intval($age);
        $mail = mysql_real_escape_string(stripslashes($mail));
        $icq = mysql_real_escape_string(stripslashes($icq));
        $country = mysql_real_escape_string(stripslashes($country));
        $connex = mysql_real_escape_string(stripslashes($connex));
        $exp = mysql_real_escape_string(stripslashes($exp));
        $dispo = mysql_real_escape_string(stripslashes($dispo));
        $comment = mysql_real_escape_string(stripslashes($comment));

        $pseudo = nkHtmlEntities($pseudo);
        $prenom = nkHtmlEntities($prenom);
        $mail = nkHtmlEntities($mail);
        $icq = nkHtmlEntities($icq);
        $country = nkHtmlEntities($country);
        $connex = nkHtmlEntities($connex);
        $exp = nkHtmlEntities($exp);
        $dispo = nkHtmlEntities($dispo);

        $sql = mysql_query("INSERT INTO " . RECRUIT_TABLE . " ( `id` , `date` , `pseudo` , `prenom` , `age` , `mail` , `icq` , `country` , `game` , `connection` , `experience` , `dispo` , `comment` ) VALUES ( '' , '" . $date . "' , '" . $pseudo . "' , '" . $prenom . "' , '" . $age . "' , '" . $mail . "' , '" . $icq . "' , '" . $country . "' , '" . $game . "' , '" . $connex . "' , '" . $exp . "' , '" . $dispo . "' , '" . $comment. "' )");
        $upd2 = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$date."', '1', '"._NOTDEM.": [<a href=\"index.php?file=Recruit&page=admin\">lien</a>].')");

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
            $sql2 = mysql_query("INSERT INTO " . USERBOX_TABLE . " ( `mid` , `user_from` , `user_for` , `titre` , `message` , `date` , `status` ) VALUES ( '' , '" . $inbox . "' , '" . $inbox . "' , '" . $subject . "' , '" . $corps . "' , '" . $date . "' , '0' )");
        }

        echo "<br /><br /><div style=\"text-align: center;\">" . _SENDRECRUIT . "</div><br /><br />";
        redirect("index.php", 2);
    }

    switch ($_REQUEST['op'])
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
    echo "<br /><br /><div style=\"text-align: center;\">" . _RECRUITOFF . "</div><br /><br />";
}

closetable();

?>