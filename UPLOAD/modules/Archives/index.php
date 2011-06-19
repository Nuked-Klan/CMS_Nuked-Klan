<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")){
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $language;

translate("modules/Archives/lang/" . $language . ".lang.php");

compteur("Archives");

opentable();
 
$visiteur = (!$user) ? 0 : $user[1];
$level_access = nivo_mod("News");
if ($visiteur >= $level_access && $level_access > -1)
{
    function index()
    {
        global $bgcolor1, $bgcolor2, $bgcolor3, $nuked;

        $nb_news = $nuked['max_archives'];
        $day = time();

        $sql = mysql_query("SELECT date FROM " . NEWS_TABLE . " ORDER BY date DESC");
        $count = mysql_num_rows($sql);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_news - $nb_news;

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _ARCHIVE . "</b></big></div><br />\n"
        . "<table width=\"100%\"><tr><td align=\"right\">" . _ORDERBY . " : ";

        if (!$_REQUEST['orderby'])
        {
            $_REQUEST['orderby'] = "date";
        }
        if ($_REQUEST['orderby'] == "date")
        {
            echo "<b>" . _DATE . "</b>";
        }
        else
        {
            echo " | <a href=\"index.php?file=Archives&amp;orderby=date\">" . _DATE . "</a>";
        }
        if ($_REQUEST['orderby'] == "titre")
        {
            echo " | <b>" . _TITLE . "</b>";
        }
        else
        {
            echo " | <a href=\"index.php?file=Archives&amp;orderby=titre\">" . _TITLE . "</a>";
        }
        if ($_REQUEST['orderby'] == "sujet")
        {
            echo " | <b>" . _SUBJET . "</b>";
        }
        else
        {
            echo " | <a href=\"index.php?file=Archives&amp;orderby=sujet\">" . _SUBJET . "</a>";
        }
        if ($_REQUEST['orderby'] == "auteur")
        {
            echo " | <b>" . _AUTHOR . "</b>";
        }
        else
        {
            echo " | <a href=\"index.php?file=Archives&amp;orderby=auteur\">" . _AUTHOR . "</a>";
        }

        echo "</td></tr></table>\n";

        if ($count > $nb_news)
        {
            $url_page = "index.php?file=Archives&amp;orderby=" . $_REQUEST['orderby'];
            number($count, $nb_news, $url_page);
        }

        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
        . "<tr style=\"background: ". $bgcolor3 . "\">\n"
        . "<td style=\"width: 30%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _SUBJET . "</b></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _AUTHOR . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _OPTION . "&nbsp;</b></td></tr>\n";

        $sql_nb = mysql_query("SELECT nid FROM " . NEWS_CAT_TABLE);
        $nbsujet = mysql_num_rows($sql_nb);

        if ($_REQUEST['orderby'] == "titre")
        {
            $sql2 = mysql_query("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE '" . $day . "' >= date ORDER BY titre LIMIT " . $start . ", " . $nb_news."");
        }
        else if ($_REQUEST['orderby'] == "date")
        {
            $sql2 = mysql_query("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE '" . $day . "' >= date ORDER BY date DESC LIMIT " . $start . ", " . $nb_news."");
        }
        else if ($_REQUEST['orderby'] == "auteur")
        {
            $sql2 = mysql_query("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE " . $day . " >= date ORDER BY auteur LIMIT " . $start . ", " . $nb_news."");
        }
        else if ($_REQUEST['orderby'] == "sujet")
        {
            $sql2 = mysql_query("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE " . $day . " >= date ORDER BY cat LIMIT " . $start . ", " . $nb_news."");
        }
        else
        {
            $sql2 = mysql_query("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE " . $day . " >= date ORDER BY id DESC LIMIT " . $start . ", " . $nb_news."");
        }

        while (list($news_id, $titre, $autor, $autor_id, $date, $cat) = mysql_fetch_array($sql2))
        {
            $date = nkDate($date);

            if (strlen($titre) > 25)
            {
                $title = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $news_id . "\" title=\"" . $titre . "\">" . htmlentities(substr($titre, 0, 25)) . "...</a>";
            }
            else
            {
                $title = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $news_id . "\">" . htmlentities($titre) . "</a>";
            }


            if ($autor_id != "")
            {
                $sql4 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
                $test = mysql_num_rows($sql4);
            }

            if ($autor_id != "" && $test > 0)
            {
                list($auteur) = mysql_fetch_array($sql4);
            }
            else
            {
                $auteur = $autor;
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

            echo "<tr style=\"background: ". $bg . "\"><td style=\"width: 30%;\">" . $title . "</td>\n";

            if ($cat != "")
            {
                $sql3 = mysql_query("SELECT titre FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cat . "'");
                list($categorie) = mysql_fetch_array($sql3);
                $categorie = htmlentities($categorie);

                echo "<td style=\"width: 20%;\" align=\"center\"><a href=\"index.php?file=Archives&amp;op=sujet&amp;cat_id=" . $cat . "\" title= \"" . _SEENEWS . "&nbsp;" . $categorie . "\">" . $categorie . "</a></td>\n";
            }
            else
            {
                echo "<td style=\"width: 20%;\" align=\"center\">N/A</td>\n";
            }

            echo "<td style=\"width: 25%;\" align=\"center\">" . $date . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($auteur) . "\" title=\"" . _DETAILAUTHOR . "&nbsp;" . $auteur . "\">" . $auteur . "</a></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\">"
            . "<a href=\"index.php?file=News&amp;nuked_nude=index&amp;op=pdf&amp;news_id=" . $news_id . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"images/pdf.gif\" alt=\"\" title=\"" . _PDF . "\" /></a>"
            . "&nbsp;<a href=\"index.php?file=News&amp;op=sendfriend&amp;news_id=" . $news_id . "\"><img style=\"border: 0;\" src=\"images/friend.gif\" alt=\"\" title=\"" . _FSEND . "\" /></a></td></tr>\n";
        }

        if ($count == 0)
        {
            echo "<tr><td colspan=\"5\" align=\"center\">" . _NONEWS . "</td></tr>\n";
        }
        echo "</table>";
        if ($count > $nb_news)
        {
            $url_page = "index.php?file=Archives&amp;orderby=" . $_REQUEST['orderby'];
            number($count, $nb_news, $url_page);
        }

        echo "<div style=\"text-align: center;\"><br /><small><i>( " . _THEREIS . "&nbsp;" . $count . "&nbsp;" . _NEWS . " &amp; " . $nbsujet . "&nbsp;" . _SUBNEWS . "&nbsp;" . _INDATABASE . ")</i></small></div><br />\n";
    }

    function sujet($cat_id)
    {
        global $bgcolor1, $bgcolor2, $bgcolor3, $nuked;

        $nb_news = $nuked['max_archives'];
        $day = time();

        $sql = mysql_query("SELECT cat FROM " . NEWS_TABLE . " WHERE cat = '" . $cat_id . "' ORDER BY date DESC");
        $count = mysql_num_rows($sql);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_news - $nb_news;

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _ARCHIVE . "</b></big></div><br />\n"
        . "<table width=\"100%\"><tr><td align=\"right\">" . _ORDERBY . " : ";

        if (!$_REQUEST['orderby'])
        {
            $_REQUEST['orderby'] = "date";
        }
        if ($_REQUEST['orderby'] == "date")
        {
            echo "<b>" . _DATE . "</b> | ";
        }
        else
        {
            echo "<a href=\"index.php?file=Archives&amp;op=sujet&amp;cat_id=" . $cat_id . "&amp;orderby=date\">" . _DATE . "</a> | ";
        }
        if ($_REQUEST['orderby'] == "titre")
        {
            echo "<b>" . _TITLE . "</b> | ";
        }
        else
        {
            echo "<a href=\"index.php?file=Archives&amp;op=sujet&amp;cat_id=" . $cat_id . "&amp;orderby=titre\">" . _TITLE . "</a> | ";
        }
        if ($_REQUEST['orderby'] == "auteur")
        {
            echo "<b>" . _AUTHOR . "</b>";
        }
        else
        {
            echo "<a href=\"index.php?file=Archives&amp;op=sujet&amp;cat_id=" . $cat_id . "&amp;orderby=auteur\">" . _AUTHOR . "</a>";
        }

        echo "</td></tr></table>\n";

        if ($count > $nb_news)
        {
            $url_page = "index.php?file=Archives&amp;op=sujet&amp;cat_id=" . $cat_id . "&amp;orderby=" . $_REQUEST['orderby'];
            number($count, $nb_news, $url_page);
        }

        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
        . "<tr style=\"background: ". $bgcolor3 . "\">\n"
        . "<td style=\"width: 30%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _SUBJET . "</b></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _AUTHOR . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _OPTION . "&nbsp;</b></td></tr>\n";

        if ($_REQUEST['orderby'] == "titre")
        {
            $sql2 = mysql_query("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE cat = '" . $cat_id . "' AND '" . $day . "' >= date ORDER BY titre LIMIT " . $start . ", " . $nb_news."");
        }
        else if ($_REQUEST['orderby'] == "date")
        {
            $sql2 = mysql_query("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE cat = '" . $cat_id . "' AND '" . $day . "' >= date ORDER BY date DESC LIMIT " . $start . ", " . $nb_news."");
        }
        else if ($_REQUEST['orderby'] == "auteur")
        {
            $sql2 = mysql_query("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE cat = '" . $cat_id . "' AND '" . $day . "' >= date ORDER BY auteur LIMIT " . $start . ", " . $nb_news."");
        }
        else
        {
            $sql2 = mysql_query("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE cat = '" . $cat_id . "' AND '" . $day . "' >= date ORDER BY id DESC LIMIT " . $start . ", " . $nb_news."");
        }

        while (list($news_id, $titre, $autor, $autor_id, $date, $cat) = mysql_fetch_array($sql2))
        {

            $date = nkDate($date);

            if (strlen($titre) > 25)
            {
                $title = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $news_id . "\" title=\"" . $titre . "\">" . htmlentities(substr($titre, 0, 25)) . "...</a>";
            }
            else
            {
                $title = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $news_id . "\">" . htmlentities($titre) . "</a>";
            }

            if ($autor_id != "")
            {
                $sql4 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
                $test = mysql_num_rows($sql4);
            }

            if ($autor_id != "" && $test > 0)
            {
                list($auteur) = mysql_fetch_array($sql4);
            }
            else
            {
                $auteur = $autor;
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

            echo "<tr style=\"background: ". $bg . "\"><td style=\"width: 30%;\">" . $title . "</td>\n";

            $sql3 = mysql_query("SELECT titre FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cat . "'");
            list($categorie) = mysql_fetch_array($sql3);
            $categorie = htmlentities($categorie);

            echo "<td style=\"width: 20%;\" align=\"center\"><i>" . $categorie . "</i></td>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $date . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($auteur) . "\" title=\"" . _DETAILAUTHOR . "&nbsp;" . $auteur . "\">" . $auteur . "</a></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\">"
            . "<a href=\"index.php?file=News&amp;nuked_nude=index&amp;op=pdf&amp;news_id=" . $news_id . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"images/pdf.gif\" alt=\"\" title=\"" . _PDF . "\" /></a>"
            . "&nbsp;<a href=\"index.php?file=News&amp;op=sendfriend&amp;news_id=" . $news_id . "\"><img style=\"border: 0;\" src=\"images/friend.gif\" alt=\"\" title=\"" . _FSEND . "\" /></a></td></tr>\n";
        }

        if ($count == 0)
        {
            echo "<tr><td colspan=\"5\" align=\"center\">" . _NONEWS . "</td></tr>\n";
        }
        echo "</table>";
        if ($count > $nb_news)
        {
            $url_page = "index.php?file=Archives&amp;op=sujet&amp;cat_id=" . $cat_id . "&amp;orderby=" . $_REQUEST['orderby'];
            number($count, $nb_news, $url_page);
        }

        echo "<div style=\"text-align: center;\"><br /><small><i>( " . _THEREIS . "&nbsp;" . $count . "&nbsp;" . _NEWS . "&nbsp;" . _INDATABASE . ")</i></small></div><br />\n";
    }

    switch ($_REQUEST['op'])
    {
        case"index":
            index();
            break;

        case"sujet":
            sujet($_REQUEST['cat_id']);
            break;

        default:
            index();
            break;
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

CloseTable();

?>
