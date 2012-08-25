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

global $user, $language, $nuked, $cookie_captcha, $random_code, $bgcolor3;

translate("modules/Forum/lang/" . $language . ".lang.php");
include("modules/Forum/template.php");

// Inclusion système Captcha
include_once("Includes/nkCaptcha.php");

// On determine si le captcha est actif ou non
if (_NKCAPTCHA == "off") $captcha = 0;
else if ((_NKCAPTCHA == 'auto' OR _NKCAPTCHA == 'on') && $user[1] > 0)  $captcha = 0;
else $captcha = 1;

opentable();

if (!$user)
{
    $visiteur = 0;
}
else
{
    $visiteur = $user[1];
}
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{
    define('EDITOR_CHECK', 1);

    $sql = mysql_query("SELECT nom, cat, level_poll FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
    $level_ok = mysql_num_rows($sql);

    if ($level_ok == 0)
    {
        echo "<br /><br /><div style=\"text-align: center;\">" . _NOACCESSFORUM . "</div><br /><br />";
    }
    else
    {
        list($nom, $cat, $level_poll) = mysql_fetch_array($sql);

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
        list($modos) = mysql_fetch_array($result);

        $select_cat = mysql_query('SELECT nom FROM ' . FORUM_CAT_TABLE . ' WHERE id = ' . $cat);
        list($nom2) = mysql_fetch_array($select_cat);

        if ($user && $modos != "" && strpos($user[0], $modos))
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        if ($_REQUEST['do'] == "edit" || $_REQUEST['do'] == "quote")
        {
            $result = mysql_query("SELECT txt, titre, auteur, usersig, emailnotify FROM " . FORUM_MESSAGES_TABLE . " WHERE id = '" . $_REQUEST['mess_id'] . "' AND forum_id = '" . $_REQUEST['forum_id'] . "'");
            list($e_txt, $e_titre, $author, $usersig, $emailnotify) = mysql_fetch_array($result);

            $e_titre = printSecuTags($e_titre);
        }

        if ($_REQUEST['thread_id'] != "")
        {
            $action = "index.php?file=Forum&amp;op=reply";
            $action_name = _POSTREPLY;
        }
        else if ($_REQUEST['do'] == "edit")
        {
            $action = "index.php?file=Forum&amp;op=edit";
            $action_name = _POSTEDIT;
        }
        else
        {
            $action = "index.php?file=Forum&amp;op=post";
            $action_name = _POSTNEWTOPIC;
        }

        echo "<br /><form method=\"post\" action=\"" . $action . "\" enctype=\"multipart/form-data\">\n"
    . "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\">\n"
    . "<tr><td valign=\"bottom\"><a href=\"index.php?file=Forum\"><b>" . _INDEXFORUM . "</b></a> -&gt; <a href=\"index.php?file=Forum&amp;cat=" . $cat . "\"><b>" . $nom2 . "</b></a> -&gt; <a href=\"index.php?file=Forum&amp;page=viewforum&amp;forum_id=" . $_REQUEST['forum_id'] . "\"><b>" . $nom . "</b></a></td></tr></table>\n"
    . "<table style=\"background: " . $color3 . ";\" width=\"100%\" cellspacing=\"1\" cellpadding=\"4\" border=\"0\">\n"
    . "<tr " . $background . "><td colspan=\"2\" align=\"center\"><b>" . $action_name . "</b></td></tr>\n"
    . "<tr><td style=\"width: 25%;background: " . $color1 . ";\"><big><b>" . _PSEUDO . "</b></big></td><td style=\"width: 75%;background: " . $color2 . ";\">";

        if ($_REQUEST['do'] == "edit")
        {
            echo $author;
    }
        else if ($user[2] != "")
        {
            echo $user[2] . "&nbsp;[<a href=\"index.php?file=User&amp;nuked_nude=index&amp;op=logout\">" . _FLOGOUT . "</a>]"
            . "<input type=\"hidden\" name=\"auteur\" value=\"" . $user[2] . "\" />";
        }
        else
        {
            echo "<input type=\"text\" name=\"auteur\" size=\"35\"  maxlength=\"35\" />"
            . "&nbsp;[<a href=\"index.php?file=User&amp;op=login_screen\">" . _FLOGIN . "</a>]</td></tr>\n";
        }

        echo "<tr><td style=\"width: 25%;background: " . $color1 . ";\"><big><b>" . _TITLE . "</b></big></td><td style=\"width: 75%;background: " . $color2 . ";\">";

        if ($_REQUEST['thread_id'] != "")
        {
            $sql1 = mysql_query("SELECT titre, annonce FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $_REQUEST['thread_id'] . "' AND forum_id = '" . $_REQUEST['forum_id'] . "'");
            list($titre, $annonce) = mysql_fetch_array($sql1);
            $titre = htmlentities($titre);
            $titre = preg_replace("`&amp;lt;`i", "&lt;", $titre);
            $titre = preg_replace("`&amp;gt;`i", "&gt;", $titre);
            $re_titre = "RE : " . $titre;

            echo "<input id=\"forum_titre\" type=\"text\" size=\"70\"  maxlength=\"70\" name=\"titre\" value=\"" . $re_titre . "\" />";
        }
        else if ($_REQUEST['do'] == "edit")
        {
            echo "<input id=\"forum_titre\" type=\"text\" size=\"70\"  maxlength=\"70\" name=\"titre\" value=\"" . $e_titre . "\" />";
        }
        else
        {
            echo "<input id=\"forum_titre\" size=\"70\"  maxlength=\"70\" type=\"text\" name=\"titre\" />";
        }

        if ($_REQUEST['do'] == "edit")
        {
            echo "<input type=\"hidden\" name=\"author\" value=\"" . $author . "\" />";
        }

        echo "</td></tr><tr><td style=\"width: 25%;background: " . $color1 . ";\" valign=\"top\"><big><b>" . _MESSAGE . "</b></big><br /><br />\n"
        . "</td><td style=\"width: 75%;background: " . $color2 . ";\">";

        if ($_REQUEST['do'] == "edit")
        {
            $ftexte = $e_txt;
        }
        else if ($_REQUEST['do'] == "quote")
        {
            $ftexte = '<blockquote style="border: 1px dashed ' . $bgcolor3 . '; background: #FFF; color: #000; padding: 5px"><strong>' . _QUOTE . ' ' . _BY . ' ' . $author . ' :</strong><br />' . $e_txt . '</blockquote>';
        }

        if ($_REQUEST['do'] == "quote")
        {
            echo "<textarea id=\"e_advanced\" name=\"texte\" cols=\"70\" rows=\"15\">" . $ftexte . "<p></p></textarea>";
        }
        else
        {
            echo "<textarea id=\"e_advanced\" name=\"texte\" cols=\"70\" rows=\"15\">" . $ftexte . "</textarea>";
        }


        if ($_REQUEST['do'] == "edit" && $usersig == 1)
        {
            $checked1 = "checked=\"checked\"";
        }
        else if ($_REQUEST['do'] == "edit" && $usersig == 0)
        {
            $checked1 = "";
        }
        else
        {
            $checked1 = "checked=\"checked\"";
        }

        if ($emailnotify == 1)
        {
            $checked2 = "checked=\"checked\"";
        }
        else
        {
                $checked2 = "";
        }

        if ($annonce == 1)
        {
            $checked3 = "checked=\"checked\"";
        }
        else
        {
                $checked3 = "";
        }


        echo "</td></tr><tr><td style=\"width: 25%;background: " . $color1 . ";\" valign=\"top\"><big><b>" . _OPTIONS . "</b></big></td><td style=\"width: 75%;background: " . $color2 . ";\">";

        if ($visiteur > 0)
        {
            echo "<input id=\"forum_sign\" type=\"checkbox\" class=\"checkbox\" name=\"usersig\" value=\"1\" " . $checked1 . " />&nbsp;" . _USERSIGN . "<br />\n"
            . "<input type=\"checkbox\" class=\"checkbox\" name=\"emailnotify\" value=\"1\" " . $checked2 . " />&nbsp;" . _EMAILNOTIFY . "<br />\n";
        }

        if ($_REQUEST['do'] == "edit")
        {
            if($force_edit_message == 'on' && $administrator != 1){
                echo '<input type="hidden" name="edit_text" value="1" />'."\n";
            }
            else{
                echo '<input type="checkbox" name="edit_text" value="1" checked="checked" />&nbsp;' . _EDITTEXT . "\n";
            }
        }

        if ($_REQUEST['thread_id'] != "" || $_REQUEST['do'] == "edit")
        {
            echo "<br />";
        }
        else
        {

            if ($user[1] >= admin_mod("Forum") || $administrator == 1)
            {
                echo "<input type=\"checkbox\" class=\"checkbox\" name=\"annonce\" value=\"1\" " . $checked3 . " />&nbsp;" . _ANNONCE . "<br />\n";
            }

        }

        if ($visiteur < $level_poll || $_REQUEST['thread_id'] != "" || $_REQUEST['do'] == "edit")
        {
            echo "<br />";
        }
        else
        {
            echo "</td></tr><tr><td style=\"width: 25%;background: " . $color1 . ";\" valign=\"top\"><big><b>" . _SURVEY . "</b></big></td><td style=\"width: 75%;background: " . $color2 . ";\">"
            . "<input type=\"checkbox\" class=\"checkbox\" name=\"survey\" value=\"1\">&nbsp;" . _POSTSURVEY . "<br />\n"
            . "<input type=\"text\" name=\"survey_field\" size=\"2\" value=\"4\">&nbsp;" . _SURVEYFIELD . "&nbsp;(" . _MAX . " : " . $nuked['forum_field_max'] . ")";
        }

        if ($visiteur >= $nuked['forum_file_level'] && $nuked['forum_file'] == "on" && $nuked['forum_file_maxsize'] > 0 && $_REQUEST['do'] != "edit")
        {
            if ($nuked['forum_file_maxsize'] >= 1000)
            {
                $max_size = $nuked['forum_file_maxsize'] * 1000;
                $maxfile = $nuked['forum_file_maxsize'] / 1000;
                $maxfilesize = $maxfile . "&nbsp;" . _MO;
            }
            else
            {
                $maxfilesize = $nuked['forum_file_maxsize'] . "&nbsp;" . _KO;
            }

            echo "</td></tr><tr><td style=\"width: 25%;background: " . $color1 . ";\" valign=\"top\"><big><b>" . _ATTACHFILE . "</b></big></td><td style=\"width: 75%;background: " . $color2 . ";\">"
            . "<input type=\"file\" name=\"fichiernom\" size=\"30\" />&nbsp;(" . _MAXFILESIZE . " : " . $maxfilesize . ")\n"
            . "<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"" . $max_size . "\" />";
        }

        echo "</td></tr>\n";

        if ($captcha == 1)
        {
                echo "<tr><td style=\"width: 25%;background: " . $color1 . ";\" valign=\"top\"><big><b>" . _SECURITYCODE . "</b></big></td><td style=\"width: 75%;background: " . $color2 . ";\"><table>";
                
                create_captcha(1);

                echo "</table><br /></td></tr>\n";
        }

        echo" <tr><td style=\"background: " . $color2 . ";\" colspan=\"2\" align=\"center\">"
        . "<input type=\"submit\" value=\"" . _SEND . "\" />\n"
        . "<input type=\"hidden\" name=\"forum_id\" value=\"" . $_REQUEST['forum_id'] . "\" />\n"
        . "<input type=\"hidden\" name=\"thread_id\" value=\"" . $_REQUEST['thread_id'] . "\" />\n"
        . "<input type=\"hidden\" name=\"mess_id\" value=\"" . $_REQUEST['mess_id'] . "\" />\n"
        . "</td></tr></table></form>\n";

        if ($_REQUEST['thread_id'] != "")
        {
            echo "<div style=\"margin-left: auto;margin-right: auto;text-align: left;width: 100%; height: 200px; overflow: auto;\">\n"
            . "<table style=\"background: " . $color3 . ";\" cellspacing=\"1\" cellpadding=\"4\" width=\"96%\" border=\"0\">\n";

            $sql2 = mysql_query("SELECT txt, auteur, date FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "' AND forum_id = '" . $_REQUEST['forum_id'] . "' ORDER BY date DESC LIMIT 0, 20");
            while (list($txt, $auteur, $date) = mysql_fetch_row($sql2))
            {
                $date = nkDate($date);
                $tmpcnt++ % 2 == 1 ? $color = $color1 : $color = $color2;
                $auteur = nk_CSS($auteur);

                echo "<tr style=\"background: " . $color . ";\"><td style=\"width: 20%;\" valign=\"top\"><b>" . $auteur . "</b></td><td style=\"width: 80%;\"><img src=\"images/posticon.gif\" alt=\"\" />" . _POSTEDON . "&nbsp;" . $date . "<br /><br />" . $txt . "<br /><br /></td></tr>\n";
            }

            echo "</table></div><br />\n";
        }
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