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

global $language, $user;
translate("modules/Team/lang/" . $language . ".lang.php");

$visiteur = $user ? $user[1] : 0;

$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{
    compteur('Team');

    function index()
    {
        global $bgcolor1, $bgcolor2, $bgcolor3, $theme, $nuked;

        opentable();

        echo '<br />';

        if ($_REQUEST['cid'] != '') $where2 = "WHERE cid = '" . $_REQUEST['cid'] . "'"; else $where2 = '';
        $sql = mysql_query("SELECT cid, titre, tag, tag2, game FROM " . TEAM_TABLE . " " . $where2 . " ORDER BY ordre, titre");
        $nb_team = mysql_num_rows($sql);
        $res = mysql_fetch_row($sql);
        $where = '';

        if ($nb_team == 0)
        {
            $titre = @html_entity_decode($nuked['name']);
            $team_tag = @html_entity_decode($nuked['tag_pre']);
            $tag2 = @html_entity_decode($nuked['tag_suf']);
            $res = array ('', "$titre", "$team_tag", "$tag2", '0');
        }



        while (is_array($res))
        {
            list($team, $titre, $team_tag, $tag2, $_REQUEST['game']) = $res;

            $titre = printSecuTags($titre);
            $team_tag = printSecuTags($team_tag);
            $tag2 = printSecuTags($tag2);

            if ($team != '') $link_titre = '<a href="index.php?file=Team&amp;cid=' . urlencode(html_entity_decode($team)) . '"><big><b>' . $titre . '</b></big></a>';
            else $link_titre = '<big><b>' . $titre . '</b></big>';

            echo "<div style=\"text-align: center;\">$link_titre</div>"
            . "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
            . "<tr style=\"background: " . $bgcolor3 . ";\">\n"
            . "<td style=\"width: 5%;\">&nbsp;</td>\n"
            . "<td style=\"width: 30%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _MAIL . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _ICQ . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _MSN . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _AIM . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _YIM . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _RANK . "</b></td></tr>\n";

            if($team != "") $where="WHERE team = '" . $team . "' OR team2 = '" . $team . "' OR team3 = '" . $team . "'"; else $where = "WHERE niveau > 1";

            $sql2 = mysql_query("SELECT id, pseudo, email, icq, msn, aim, yim, rang, country FROM " . USER_TABLE . " " . $where . " AND niveau > 0 ORDER BY ordre, pseudo");
            $nb_members = mysql_num_rows($sql2);
            if ($nb_members > 0)
            {
                while (list($id_user, $pseudo, $email, $icq, $msn, $aim, $yim, $rang, $country) = mysql_fetch_array($sql2))
                {
                    list ($pays, $ext) = explode ('.', $country);
                    $temp = $team_tag . $pseudo . $tag2;
                    $pseudo = html_entity_decode($pseudo);

                    if (is_file("themes/" . $theme . "/images/mail.gif"))
                    {
                        $img = "themes/" . $theme . "/images/mail.gif";
                    }
                    else
                    {
                        $img = "modules/Team/images/mail.gif";
                    }

                    if ($rang != "" && $rang > 0)
                    {
                        $sql_rank = mysql_query("SELECT titre FROM " . TEAM_RANK_TABLE . " WHERE id = '" . $rang . "'");
                        list($rank_name) = mysql_fetch_array($sql_rank);
                        $rank_name = printSecuTags($rank_name);
                    }
                    else
                    {
                        $rank_name = "N/A";
                    }

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

                    if ($_REQUEST['game'] > 0)
                    {
                        $sql3 = mysql_query("SELECT * FROM " . GAMES_PREFS_TABLE . " WHERE game = '" . $_REQUEST['game'] . "' AND user_id = '" . $id_user . "'");
                        $test = mysql_num_rows($sql3);

                        if ($test > 0)
                        {
                            $url_member = "index.php?file=Team&amp;op=detail&amp;autor=" . urlencode($pseudo) . "&amp;game=" . $_REQUEST['game'];
                        }
                        else
                        {
                            $url_member = "index.php?file=Team&amp;op=detail&amp;autor=" . urlencode($pseudo);
                        }
                    }
                    else
                    {
                        $url_member = "index.php?file=Team&amp;op=detail&amp;autor=" . urlencode($pseudo);
                    }

                    echo "<tr style=\"background: " . $bg . ";\">\n"
                    . "<td style=\"width: 5%;\" align=\"center\"><img src=\"images/flags/" . $country . "\" alt=\"\" title=\"" . $pays . "\" /></td>\n"
                    . "<td style=\"width: 25%;\"><a href=\"" . $url_member . "\" title=\"" . _VIEWPROFIL . "\"><b>" . $temp . "</b></a></td>\n"
                    . "<td style=\"width: 10%;\" align=\"center\">\n";

                    if ($email != "")
                    {
                        echo "<a href=\"mailto:" . $email . "\"><img style=\"border: 0;\" src=\"" . $img . "\" alt=\"\" title=\"" . $email . "\" /></a>";
                    }
                    else
                    {
                        echo "N/A";
                    }

                    echo "</td><td style=\"width: 10%;\" align=\"center\">\n";

                    if ($icq != "")
                    {
                        echo "<a href=\"http://web.icq.com/whitepages/add_me?uin=" . $icq . "&amp;action=add\"><img style=\"border: 0;\" src=\"modules/Team/images/icq.gif\" alt=\"\" title=\"" . $icq . "\" /></a>";
                    }
                    else
                    {
                        echo "N/A";
                    }

                    echo "</td><td style=\"width: 10%;\" align=\"center\">\n";

                    if ($msn != "")
                    {
                        echo "<a href=\"mailto:" . $msn . "\"><img style=\"border: 0;\" src=\"modules/Team/images/msn.gif\" alt=\"\" title=\"" . $msn . "\" /></a>";
                    }
                    else
                    {
                        echo "N/A";
                    }

                    echo "</td><td style=\"width: 10%;\" align=\"center\">\n";

                    if ($aim != "")
                    {
                        echo "<a href=\"aim:goim?screenname=" . $aim . "&amp;message=Hi+" . $aim . "+Are+you+there+?\"><img style=\"border: 0;\" src=\"modules/Team/images/aim.gif\" alt=\"\" title=\"" . $aim . "\" /></a>";
                    }
                    else
                    {
                        echo "N/A";
                    }

                    echo"</td><td style=\"width: 10%;\" align=\"center\">\n";

                    if ($yim != "")
                    {
                        echo "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . $yim . "&amp;.src=pg\"><img style=\"border: 0;\" src=\"modules/Team/images/yim.gif\" alt=\"\" title=\"" . $yim . "\" /></a>";
                    }
                    else
                    {
                        echo "N/A";
                    }

                    echo "</td><td style=\"width: 20%;\" align=\"center\">" . $rank_name . "</td></tr>\n";
                }
            }
            else
            {
                echo "<tr><td align=\"center\" colspan=\"8\">" . _NOMEMBERS . "</td></tr>\n";
            }

            echo "</table><br /><br />\n";
            $j = 0;
            $res = mysql_fetch_row($sql);
        }
        closetable();
    }

    function detail($autor)
    {
        global $nuked, $bgcolor1, $bgcolor2, $bgcolor3, $user, $visiteur;

        opentable();

        $autor = htmlentities($autor, ENT_QUOTES);

        $sql = mysql_query("SELECT id, icq, msn, aim, yim, email, url, game, country FROM " . USER_TABLE . " WHERE pseudo = '" . $autor . "'");
        $test = mysql_num_rows($sql);

        if ($test > 0)
        {
            list($id_user, $icq, $msn, $aim, $yim, $email, $url, $game_id, $country) = mysql_fetch_array($sql);
            list ($pays, $ext) = explode ('.', $country);

            if ($email != "")
            {
                $mail = "<a href=\"mailto:" . $email . "\">" . $email . "</a>";
            }
            else
            {
                $mail = "";
            }

            $sql2 = mysql_query("SELECT prenom, age, sexe, ville, motherboard, cpu, ram, video, resolution, son, ecran, souris, clavier, connexion, system, photo, pref_1, pref_2, pref_3, pref_4, pref_5 FROM " . USER_DETAIL_TABLE . " WHERE user_id = '" . $id_user . "'");
            $res = mysql_num_rows($sql2);
            list($prenom, $birthday, $sexe, $ville, $motherboard, $cpu, $ram, $video, $resolution, $sons, $ecran, $souris, $clavier, $connexion, $osystem, $photo, $pref1, $pref2, $pref3, $pref4, $pref5) = mysql_fetch_array($sql2);

            if ($_REQUEST['game'] != "")
            {
                $sql3 = mysql_query("SELECT titre, pref_1, pref_2, pref_3, pref_4, pref_5 FROM " . GAMES_TABLE . " WHERE id = '" . $_REQUEST['game'] . "'");
                list($titre, $pref_1, $pref_2, $pref_3, $pref_4, $pref_5) = mysql_fetch_array($sql3);


                $titre = printSecuTags($titre);
                $pref_1 = printSecuTags($pref_1);
                $pref_2 = printSecuTags($pref_2);
                $pref_3 = printSecuTags($pref_3);
                $pref_4 = printSecuTags($pref_4);
                $pref_5 = printSecuTags($pref_5);


                $sql4 = mysql_query("SELECT pref_1, pref_2, pref_3, pref_4, pref_5 FROM " . GAMES_PREFS_TABLE . " WHERE game = '" . $_REQUEST['game'] . "' AND user_id = '" . $id_user . "'");
                list($gpref1, $gpref2, $gpref3, $gpref4, $gpref5) = mysql_fetch_array($sql4);

            }
            else
            {
                $sql3 = mysql_query("SELECT titre, pref_1, pref_2, pref_3, pref_4, pref_5 FROM " . GAMES_TABLE . " WHERE id = '" . $game_id . "'");
                list($titre, $pref_1, $pref_2, $pref_3, $pref_4, $pref_5) = mysql_fetch_array($sql3);


                $titre = printSecuTags($titre);
                $pref_1 = printSecuTags($pref_1);
                $pref_2 = printSecuTags($pref_2);
                $pref_3 = printSecuTags($pref_3);
                $pref_4 = printSecuTags($pref_4);
                $pref_5 = printSecuTags($pref_5);

                $gpref1 = $pref1;
                $gpref2 = $pref2;
                $gpref3 = $pref3;
                $gpref4 = $pref4;
                $gpref5 = $pref5;
            }

            if ($birthday != "")
            {
                list ($jour, $mois, $an) = explode ('/', $birthday);
                $age = date("Y") - $an;
                if (date("m") < $mois)
                {
                    $age = $age-1;
                }
                if (date("d") < $jour && date("m") == $mois)
                {
                    $age = $age - 1;
                }
            }
            else
            {
                $age = "";
            }


            if ($sexe == "male")
            {
              $sex = _MALE;
            }
            else if ($sexe == "female")
            {
                $sex = _FEMALE;
            }
            else
            {
                $sex = "";
            }

            if ($visiteur == 9)
            {
               echo "<div style=\"text-align: right;\"><a href=\"index.php?file=Admin&amp;page=user&amp;op=edit_user&amp;id_user=" . $id_user . "\"><img style=\"border: 0;\" src=\"images/edition.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a>";

            if ($id_user != $user[0])
            {
                echo "<script type=\"text/javascript\">\n"
                ."<!--\n"
                ."\n"
                . "function deluser(pseudo, id)\n"
                . "{\n"
                . "if (confirm('" . _DELETEUSER . " '+pseudo+' ! " . _CONFIRM . "'))\n"
                . "{document.location.href = 'index.php?file=Admin&page=user&op=del_user&id_user='+id;}\n"
                . "}\n"
                . "\n"
                . "// -->\n"
                . "</script>\n";

                echo "<a href=\"javascript:deluser('" . mysql_real_escape_string(stripslashes($autor)) . "', '" . $id_user . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DELETE . "\" /></a>";
            }
        echo "&nbsp;</div>\n";
        }

            $a = "¿¡¬√ƒ≈‡·‚„‰Â“”‘’÷ÿÚÛÙıˆ¯»… ÀËÈÍÎ«ÁÃÕŒœÏÌÓÔŸ⁄€‹˘˙˚¸ˇ—Ò";
            $b = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
            $flash_autor = @html_entity_decode($autor);
            $flash_autor = strtr($flash_autor, $a, $b);

            echo "<br /><object type=\"application/x-shockwave-flash\" data=\"modules/Members/images/title.swf\" width=\"100%\" height=\"50\">\n"
            . "<param name=\"movie\" value=\"modules/Members/images/title.swf\" />\n"
            . "<param name=\"pluginurl\" value=\"http://www.macromedia.com/go/getflashplayer\" />\n"
            . "<param name=\"wmode\" value=\"transparent\" />\n"
            . "<param name=\"menu\" value=\"false\" />\n"
            . "<param name=\"quality\" value=\"best\" />\n"
            . "<param name=\"scale\" value=\"exactfit\" />\n"
            . "<param name=\"flashvars\" value=\"text=" . $flash_autor . "\" /></object>\n";

            if ($res > 0)
            {
                echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
                . "<tr style=\"background: " . $bgcolor3 . ";\"><td style=\"height: 20px\" colspan=\"2\" align=\"center\"><big><b>" . _INFOPERSO . "</b></big></td></tr>\n"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"width: 100%\"><table cellpadding=\"1\" cellspacing=\"0\">\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _NICK . " :</b></td><td>" . $autor . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _LASTNAME . " :</b></td><td>" . $prenom . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _AGE . " :</b></td><td>" . $age . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _SEXE . " :</b></td><td>" . $sex . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _CITY . " :</b></td><td>" . $ville . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _COUNTRY . " :</b></td><td>" . $pays . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _MAIL . " :</b></td><td>" . $mail . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _URL . " :</b></td><td>\n";

                if ($url != "" && preg_match("`http://`i", $url))
                {
                    echo "<a href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\">" . $url . "</a>";
                }

                echo "</td></tr><tr><td><b>&nbsp;&nbsp;ª " . _ICQ . " :</b></td><td>\n";

                if ($icq != "")
                {
                    echo "<a href=\"http://web.icq.com/whitepages/add_me?uin=" . $icq . "&amp;action=add\">" . $icq . "</a>";
                }

                echo "</td></tr><tr><td><b>&nbsp;&nbsp;ª " . _MSN . " :</b></td><td>\n";

                if ($msn != "")
                {
                    echo"<a href=\"mailto:" . $msn . "\">" . $msn . "</a>";
                }

                echo "</td></tr><tr><td><b>&nbsp;&nbsp;ª " . _AIM . " :</b></td><td>\n";

                if ($aim != "")
                {
                    echo "<a href=\"aim:goim?screenname=" . $aim . "&amp;message=Hi+" . $aim . "+Are+you+there+?\">" . $aim . "</a>";
                }

                echo "</td></tr><tr><td><b>&nbsp;&nbsp;ª " . _YIM . " :</b></td><td>\n";

                if ($yim != "")
                {
                    echo "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=" . $yim . "&amp;.src=pg\">" . $yim . "</a>";
                }

                echo"</td></tr></table></td><td align=\"center\">\n";

                if ($photo != "")
                {
                    echo "&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"" . $photo . "\" width=\"100\" height=\"100\" alt=\"\" style=\"border: 1px solid #000000;\" />&nbsp;&nbsp;&nbsp;&nbsp;";
                }
                else
                {
                    echo "&nbsp;&nbsp;&nbsp;&nbsp;<img src=\"modules/Team/images/pas_image.jpg\" width=\"100\" height=\"100\" alt=\"\" style=\"border: 1px solid #000000;\" />&nbsp;&nbsp;&nbsp;&nbsp;";
                }

                echo "</td></tr>\n"
                . "<tr style=\"background: " . $bgcolor3 . ";\"><td colspan=\"2\" style=\"height: 20px\" align=\"center\"><big><b>" . _HARDCONFIG . "</b></big></td></tr>\n"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"width: 100%\" colspan=\"2\"><table cellpadding=\"1\" cellspacing=\"0\">\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _PROCESSOR . " : </b></td><td>" . $cpu . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _MEMORY . " : </b></td><td>" . $ram . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _MOTHERBOARD . " : </b></td><td>" . $motherboard . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _VIDEOCARD . " : </b></td><td>" . $video . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _RESOLUTION . " : </b></td><td>" . $resolution . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _SOUNDCARD . " : </b></td><td>" . $sons . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _MOUSE . " : </b></td><td>" . $souris . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _KEYBOARD . " : </b></td><td>" . $clavier . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _MONITOR . " : </b></td><td>" . $ecran . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _SYSTEMOS . " : </b></td><td>" . $osystem . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . _CONNECT . " :</b></td><td>" . $connexion . "</td></tr>\n"
                . "</table></td></tr>"
                . "<tr style=\"background: " . $bgcolor3 . ";\"><td colspan=\"2\" style=\"height: 20px\" align=\"center\"><big><b>" . $titre . " :</b></big></td></tr>\n"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td colspan=\"2\"><table cellpadding=\"1\" cellspacing=\"0\">\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . $pref_1 . " :</b></td><td>" . $gpref1 . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . $pref_2 . " :</b></td><td>" . $gpref2 . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . $pref_3 . " :</b></td><td>" . $gpref3 . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . $pref_4 . " :</b></td><td>" . $gpref4 . "</td></tr>\n"
                . "<tr><td><b>&nbsp;&nbsp;ª " . $pref_5 . " :</b></td><td>" . $gpref5 . "</td></tr>\n"
                . "</table></td></tr></table><br />";
            }
            else
            {
                echo "<br /><div style=\"text-align: center;\">" . _NOPREF . "</div><br />\n";
            }

            if ($user)
            {
                echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Userbox&amp;op=post_message&amp;for=" . $id_user . "\">" . _SENDPV . "</a> ]</div><br />\n";
            }
        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _NOMEMBER . "</div><br /><br />\n";
        }

        closetable();
    }

    switch ($_REQUEST['op'])
    {
        case"index":
            index();
            break;

        case"detail":
            detail($_REQUEST['autor']);
            break;

        default:
            index();
    }

}
else if ($level_access == -1)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    closetable();
}
else if ($level_access == 1 && $visiteur == 0)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | "
    . "<a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b></div><br /><br />";
    closetable();
}
else
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    closetable();
}

?>