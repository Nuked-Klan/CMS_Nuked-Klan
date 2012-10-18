<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $nuked, $language, $user, $cookie_captcha;
translate('modules/Recruit/lang/' . $language . '.lang.php');

// Inclusion système Captcha
include_once('Includes/nkCaptcha.php');

// On determine si le captcha est actif ou non
if (_NKCAPTCHA == 'off') $captcha = 0;
else if ((_NKCAPTCHA == 'auto' OR _NKCAPTCHA == 'on') && $user[1] > 0)  $captcha = 0;
else $captcha = 1;

opentable();

$visiteur = $user ? $user[1] : 0;

$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{
    compteur('Recruit');

    if ($nuked['recrute'] > 0)
    {
        function index()
        {
            global $nuked;

            if ($nuked['recrute_charte'] != '')
            {
				echo "<br /><table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
				. "<tr><td align=\"center\"><big><b>" . _RECRUIT . "</b></big></td></tr>\n"
				. "<tr><td>&nbsp;</td></tr><tr><td>" . html_entity_decode($nuked['recrute_charte']) . "</td></tr></table>\n"
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
            global $nuked, $user, $language, $captcha;

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


			echo "<br /><form method=\"post\" action=\"index.php?file=Recruit\" onsubmit=\"return verifchamps();\">\n"
			. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
			. "<tr><td colspan=\"2\" align=\"center\"><big><b>" . _RECRUIT . "</b></big></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n"
			. "<tr><td style=\"width: 20%;\"><b>" . _NICK . " : </b></td><td><input id=\"recruit_pseudo\" type=\"text\" name=\"pseudo\" value=\"" . $user[2] . "\" size=\"20\" /></td></tr>\n"
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
				$nom = htmlentities($nom);
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

			if ($captcha == 1) create_captcha(2);

			echo "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"" . _SEND . "\" /><input type=\"hidden\" name=\"op\" value=\"send_recruit\" /></td></tr></table></form><br />\n";
        }

        function send_recruit($pseudo, $prenom, $age, $mail, $icq, $country, $game, $connex, $exp, $dispo, $comment)
        {
            global $nuked, $captcha;

			// Verification code captcha
            if ($captcha == 1 && !ValidCaptchaCode($_POST['code_confirm']))
            {
				echo "<br /><br /><div style=\"text-align: center;\">" . _BADCODECONFIRM . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div><br /><br />";
				closetable();
				footer();
				exit();
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

			$comment = secu_html(html_entity_decode($comment));

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

            $pseudo = htmlentities($pseudo);
            $prenom = htmlentities($prenom);
            $mail = htmlentities($mail);
            $icq = htmlentities($icq);
            $country = htmlentities($country);
            $connex = htmlentities($connex);
            $exp = htmlentities($exp);
            $dispo = htmlentities($dispo);

            $sql = mysql_query("INSERT INTO " . RECRUIT_TABLE . " ( `id` , `date` , `pseudo` , `prenom` , `age` , `mail` , `icq` , `country` , `game` , `connection` , `experience` , `dispo` , `comment` ) VALUES ( '' , '" . $date . "' , '" . $pseudo . "' , '" . $prenom . "' , '" . $age . "' , '" . $mail . "' , '" . $icq . "' , '" . $country . "' , '" . $game . "' , '" . $connex . "' , '" . $exp . "' , '" . $dispo . "' , '" . $comment. "' )");
			$upd2 = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$date."', '1', '"._NOTDEM.": [<a href=\"index.php?file=Recruit&page=admin\">lien</a>].')");

            $subject = _RECRUIT . ", " . $date2;
            $corps = $pseudo . " " . _NEWRECRUIT . "\r\n" . $nuked['url'] . "/index.php?file=Recruit&page=admin\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
            $from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $mail;

            $subject = @html_entity_decode($subject);
            $corps = @html_entity_decode($corps);
            $from = @html_entity_decode($from);

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

}
else if ($level_access == -1)
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
}
else if ($level_access == 1 && $visiteur == 0)
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b></div><br /><br />";
}
else
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
}

closetable();

?>