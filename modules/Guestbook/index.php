<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $nuked, $language, $user, $cookie_captcha;
translate("modules/Guestbook/lang/" . $language . ".lang.php");

// Inclusion système Captcha
include_once("Includes/nkCaptcha.php");

// On determine si le captcha est actif ou non
if (_NKCAPTCHA == "off") $captcha = 0;
else if ((_NKCAPTCHA == 'auto' OR _NKCAPTCHA == 'on') && $user[1] > 0)  $captcha = 0;
else $captcha = 1;

$visiteur = (!$user) ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{
    compteur("Guestbook");

    function post_book()
    {
        global $user, $nuked, $captcha;

		define('EDITOR_CHECK', 1);

        opentable();

        echo "<script type=\"text/javascript\">\n"
		."<!--\n"
		. "\n"
		."function trim(string)\n"
		."{"
		."return string.replace(/(^\s*)|(\s*$)/g,'');"
		."}\n"
		."\n"
		. "function verifchamps()\n"
		. "{\n"
		. "\n"
		. "if (trim(document.getElementById('guest_name').value) == \"\")\n"
		. "{\n"
		. "alert('" . _NONICK . "');\n"
		. "return false;\n"
		. "}\n"
		. "\n"
		. "if (document.getElementById('guest_mail').value.indexOf('@') == -1)\n"
		. "{\n"
		. "alert('" . _ERRORMAIL . "');\n"
		. "return false;\n"
		. "}\n"
		. "\n"
		. "// -->\n"
	. "</script>\n";

        if ($user)
        {
            $sql = mysql_query("SELECT url, email FROM " . USER_TABLE . " WHERE pseudo = '" . $user[2] . "'");
            list($url, $mail) = mysql_fetch_array($sql);
        }

		echo "<br /><div style=\"text-align: center;\"><big><b>" . _GUESTBOOK . "</b></big></div><br />\n"
		. "<form method=\"post\" action=\"index.php?file=Guestbook&amp;op=send_book\">\n"
		. "<table style=\"margin: auto; width: 98%; text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
		. "<tr><td><b>" . _AUTHOR . " :</b></td><td>";
		if ($user) echo '<b>' . $user[2] . '</b></td></tr>'; else echo "<input id=\"guest_name\" type=\"text\" name=\"name\" value=\"\" size=\"20\" maxlength=\"30\" /></td></tr>\n";
		echo "<tr><td><b>" . _MAIL . " :</b></td><td>"; if ($mail) echo '<b>' . $mail . '</b></td></tr>'; else echo "<input id=\"guest_mail\" type=\"text\" name=\"email\" value=\"\" size=\"40\" maxlength=\"80\" /></td></tr>\n";
		echo "<tr><td><b>" . _URL . " :</b></td><td>"; if ($url) echo '<b>' . $url . '</b></td></tr>'; else echo "<input type=\"text\" name=\"url\" value=\"\" size=\"40\" maxlength=\"80\" /></td></tr>\n";

		if ($captcha == 1) create_captcha(2);


		echo "<tr><td colspan=\"2\"><b>" . _COMMENT . " :</b></td></tr>\n"
		. "<tr><td colspan=\"2\"><textarea id=\"e_basic\" name=\"comment\" cols=\"65\" rows=\"12\"></textarea></td></tr>\n"
		. "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\"" . _SEND . "\" />&nbsp;<input type=\"button\" value=\"" . _CANCEL . "\" onclick=\"javascript:history.back()\" /></td></tr></table></form><br />\n";

        closetable();
    }

    function send_book($name, $email, $url, $comment)
    {
        global $user, $nuked, $user_ip, $captcha;

        opentable();

        // Verification code captcha
        if ($captcha == 1 && !ValidCaptchaCode($_REQUEST['code_confirm']))
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _BADCODECONFIRM . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div><br /><br />";
            closetable();
            footer();
            exit();
        }

        if ($user[2] != "")
        {
            $pseudo = $user[2];
        }
        else
        {
            $name = verif_pseudo($name);
            $name = htmlentities($name, ENT_QUOTES);

            if ($name == "error1")
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _PSEUDOFAILDED . "</div><br /><br />";
                redirect("index.php?file=Guestbook&op=post_book", 2);
                closetable();
                footer();
                exit();
            }
            else if ($name == "error2")
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _RESERVNICK . "</div><br /><br />";
                redirect("index.php?file=Guestbook&op=post_book", 2);
                closetable();
                footer();
                exit();
            }
            else if ($name == "error3")
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _BANNEDNICK . "</div><br /><br />";
                redirect("index.php?file=Guestbook&op=post_book", 2);
                closetable();
                footer();
                exit();
            }
            else
            {
                $pseudo = $name;
            }
        }

        $email = htmlentities($email);
        $sql3 = mysql_query("SELECT email FROM " . BANNED_TABLE . " WHERE email = '" . $email . "'");
        $nb_ban = mysql_num_rows($sql3);

        if ($nb_ban > 0)
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _BANNEDEMAIL . "</div><br /><br />";
            redirect("index.php?file=Guestbook&op=post_book", 2);
            closetable();
            footer();
            exit();
        }

        $sql2 = mysql_query("SELECT date, host FROM " . GUESTBOOK_TABLE . " ORDER BY id DESC LIMIT 0, 1");
        list($flood_date, $flood_ip) = mysql_fetch_array($sql2);

        $anti_flood = $flood_date + 60;

        $date = time();

        if ($user_ip == $flood_ip && $date < $anti_flood)
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _NOFLOOD . "</div><br /><br />";
            redirect("index.php?file=Guestbook", 2);
            closetable();
            footer();
            exit();
        }
        else if ($comment != "")
        {
            $date = time();
            $comment = secu_html(html_entity_decode($comment));
            $comment = mysql_real_escape_string(stripslashes($comment));
            $pseudo = mysql_real_escape_string(stripslashes($pseudo));
            $email = mysql_real_escape_string(stripslashes($email));
            
            if (!empty($url) && !is_int(stripos($url, 'http://')))
            {
                $url = "http://" . mysql_real_escape_string(stripslashes($url));
            }

            $sql = mysql_query("INSERT INTO " . GUESTBOOK_TABLE . " ( `id` , `name` , `email` , `url` , `date` , `host` , `comment` ) VALUES ( '' , '" . $pseudo . "' , '" . $email . "' , '" . $url . "' , '" . $date . "' , '" . $user_ip . "' , '" . $comment . "' )");
            echo "<br /><br /><div style=\"text-align: center;\">" . _POSTADD . "</div><br /><br />";
            redirect("index.php?file=Guestbook", 2);
            closetable();
        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _NOTEXT . "</div><br /><br />";
            redirect("index.php?file=Guestbook", 2);
            closetable();
            footer();
            exit();
        }
    }

    function index()
    {
        global $nuked, $language, $bgcolor1, $bgcolor2, $bgcolor3, $user, $visiteur;

        opentable();

        $nb_mess_guest = $nuked['mess_guest_page'];

        $sql = mysql_query("SELECT id FROM " . GUESTBOOK_TABLE);
        $count = mysql_num_rows($sql);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_mess_guest - $nb_mess_guest;

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _GUESTBOOK . "</b></big>\n"
        . "<br /><br />[ <a href=\"index.php?file=Guestbook&amp;op=post_book\">" . _SIGNGUESTBOOK . "</a> ]</div><br />\n";

        if ($count > $nb_mess_guest)
        {
            number($count, $nb_mess_guest, "index.php?file=Guestbook");
        }

        echo "<table style=\"background: " . $bgcolor3 . ";margin:auto\" width=\"98%\" cellpadding=\"3\" cellspacing=\"1\">\n"
        . "<tr style=\"background: " . $bgcolor3 . ";\">\n"
        . "<td style=\"width: 30%;\" align=\"center\"><b>" . _AUTHOR . "</b></td>\n"
        . "<td style=\"width: 70%;\" align=\"center\"><b>" . _COMMENT . "</b></td></tr>\n";

        $sql2 = mysql_query("SELECT id, name, comment, email, url, date, host FROM " . GUESTBOOK_TABLE . " ORDER BY id DESC LIMIT " . $start . ", " . $nb_mess_guest."");
        while (list($id, $name, $comment, $email, $url, $date, $ip) = mysql_fetch_array($sql2))
        {
            $date = nkDate($date);

            $url = htmlentities($url);

            $url = nk_CSS($url);
            $email = nk_CSS($email);

            $comment = icon($comment);

            if (strlen($name) > 30)
            {
                $name = substr($name, 0, 30) . "...";
            }

            $name = nk_CSS($name);

            if ($j == 0)
            {
                $bg = $bgcolor2;
                $j++;
            }
            else
            {
                $bg = $bgcolor1;
                $j = 0;
            }

            if ($url != "")
            {
                $website = "&nbsp;<a href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"modules/Forum/images/website.gif\" alt=\"\" title=\"" . $url . "\" /></a>";
            }
            else
            {
                $website = "";
            }
            if ($email != "")
            {
                $usermail = "<a href=\"mailto:" . $email . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/email.gif\" alt=\"\" title=\"" . $email . "\" /></a>";
            }
            else
            {
                $usermail = "";
            }

            if ($visiteur >= admin_mod("Guestbook"))
            {
                echo "<script type=\"text/javascript\">\n"
                . "<!--\n"
                . "\n"
                . "function delmess(pseudo, id)\n"
                . "{\n"
                . "if (confirm('" . _SIGNDELETE . " '+pseudo+' ! " . _CONFIRM . "'))\n"
                . "{document.location.href = 'index.php?file=Guestbook&page=admin&op=del_book&gid='+id;}\n"
                . "}\n"
                . "\n"
                . "// -->\n"
                . "</script>\n";

                $admin = "&nbsp;<a href=\"index.php?file=Guestbook&amp;page=admin&amp;op=edit_book&amp;gid=" . $id . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/edit.gif\" alt=\"\" /></a>"
                . "&nbsp;<a href=\"javascript:delmess('" . mysql_real_escape_string(stripslashes($name)) . "', '" . $id . "');\"><img style=\"border: 0;\" src=\"modules/Forum/images/delete.gif\" alt=\"\" /></a>";
            }
            else
            {
                $admin = "";
            }

            echo "<tr style=\"background: " . $bg . ";\"><td style=\"width: 30%;\" valign=\"top\"><b>" . $name . "</b>";

            if ($visiteur >= admin_mod("Guestbook"))
            {
                echo "<br />Ip : " . $ip;
            }

            echo "</td><td style=\"width: 70%;\"><img src=\"images/posticon.gif\" alt=\"\" /><small> " . _POSTED . " : " . $date . "</small>\n"
            . "<br /><br />" . $comment . "<br /><br /></td></tr>\n"
            . "<tr style=\"background: " . $bg . ";\"><td style=\"width: 30%;\">&nbsp;</td><td style=\"width: 70%;\">" . $usermail . $website . $admin . "</td></tr>\n";
        }

        if ($count == 0)
        {
            echo "<tr style=\"background: " . $bgcolor2 . ";\"><td align=\"center\" colspan=\"2\">" . _NOSIGN . "</td></tr>\n";
        }

        echo "</table>\n";

        if ($count > $nb_mess_guest)
        {
            number($count, $nb_mess_guest, "index.php?file=Guestbook");
        }

        echo "<br /><div style=\"text-align: center;\"><small><i>( " . _THEREIS . "&nbsp;" . $count . "&nbsp;" . _SIGNINDB . " )</i></small></div><br />\n";

        closetable();
    }

    switch ($_REQUEST['op'])
    {
        case "post_book":
            post_book();
            break;

        case "send_book":
            send_book($_REQUEST['name'], $_REQUEST['email'], $_REQUEST['url'], $_REQUEST['comment']);
            break;

        default:
            index();
            break;
    }
}
else if ($level_access == -1)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
}
else if ($level_access == 1 && $visiteur == 0)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>";
    closetable();
}
else
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
}

?>