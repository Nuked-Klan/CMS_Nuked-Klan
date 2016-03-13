<?php
/**
 * index.php
 *
 * Frontend of Archives module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Archives'))
    return;

compteur('Archives');


function index()
{
    global $bgcolor1, $bgcolor2, $bgcolor3, $nuked, $p;

    $nb_news = $nuked['max_archives'];
    $day = time();

    $sql = nkDB_execute("SELECT date FROM " . NEWS_TABLE . " ORDER BY date DESC");
    $count = nkDB_numRows($sql);

    $start = $p * $nb_news - $nb_news;

    echo "<br /><div style=\"text-align: center;\"><big><b>" . _ARCHIVE . "</b></big></div><br />\n"
    . "<table width=\"100%\"><tr><td align=\"right\">" . _ORDERBY . " : ";

    if (!array_key_exists('orderby', $_REQUEST))
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
        echo " | <b>" . __('AUTHOR') . "</b>";
    }
    else
    {
        echo " | <a href=\"index.php?file=Archives&amp;orderby=auteur\">" . __('AUTHOR') . "</a>";
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
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . __('AUTHOR') . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _OPTION . "&nbsp;</b></td></tr>\n";

    $sql_nb = nkDB_execute("SELECT nid FROM " . NEWS_CAT_TABLE);
    $nbsujet = nkDB_numRows($sql_nb);

    if ($_REQUEST['orderby'] == "titre")
    {
        $sql2 = nkDB_execute("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE '" . $day . "' >= date ORDER BY titre LIMIT " . $start . ", " . $nb_news."");
    }
    else if ($_REQUEST['orderby'] == "date")
    {
        $sql2 = nkDB_execute("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE '" . $day . "' >= date ORDER BY date DESC LIMIT " . $start . ", " . $nb_news."");
    }
    else if ($_REQUEST['orderby'] == "auteur")
    {
        $sql2 = nkDB_execute("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE " . $day . " >= date ORDER BY auteur LIMIT " . $start . ", " . $nb_news."");
    }
    else if ($_REQUEST['orderby'] == "sujet")
    {
        $sql2 = nkDB_execute("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE " . $day . " >= date ORDER BY cat LIMIT " . $start . ", " . $nb_news."");
    }
    else
    {
        $sql2 = nkDB_execute("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE " . $day . " >= date ORDER BY id DESC LIMIT " . $start . ", " . $nb_news."");
    }

    $j = 0;
    while (list($news_id, $titre, $autor, $autor_id, $date, $cat) = nkDB_fetchArray($sql2))
    {
        $date = nkDate($date);

        if (strlen($titre) > 25)
        {
            $title = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $news_id . "\" title=\"" . $titre . "\">" . nkHtmlEntities(substr($titre, 0, 25)) . "...</a>";
        }
        else
        {
            $title = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $news_id . "\">" . nkHtmlEntities($titre) . "</a>";
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
            $sql3 = nkDB_execute("SELECT titre FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cat . "'");
            list($categorie) = nkDB_fetchArray($sql3);
            $categorie = nkHtmlEntities($categorie);

            echo "<td style=\"width: 20%;\" align=\"center\"><a href=\"index.php?file=Archives&amp;op=sujet&amp;cat_id=" . $cat . "\" title= \"" . _SEENEWS . "&nbsp;" . $categorie . "\">" . $categorie . "</a></td>\n";
        }
        else
        {
            echo "<td style=\"width: 20%;\" align=\"center\">N/A</td>\n";
        }

        echo "<td style=\"width: 25%;\" align=\"center\">" . $date . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\">";

        if ($autor_id != '') {
            echo "<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($autor) . "\" title=\"" . _DETAILAUTHOR . "&nbsp;" . $autor . "\">" . $autor . "</a>";
        }
        else {
            echo $autor;
        }

        echo "</td>\n"
        . "<td style=\"width: 10%;\" align=\"center\">"
        . "<a href=\"index.php?file=News&amp;op=pdf&amp;news_id=" . $news_id . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"images/pdf.gif\" alt=\"\" title=\"" . _APDF . "\" /></a>"
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
    global $bgcolor1, $bgcolor2, $bgcolor3, $nuked, $p;

    $nb_news = $nuked['max_archives'];
    $day = time();

    $sql = nkDB_execute("SELECT cat FROM " . NEWS_TABLE . " WHERE cat = '" . $cat_id . "' ORDER BY date DESC");
    $count = nkDB_numRows($sql);

    $start = $p * $nb_news - $nb_news;

    echo "<br /><div style=\"text-align: center;\"><big><b>" . _ARCHIVE . "</b></big></div><br />\n"
    . "<table width=\"100%\"><tr><td align=\"right\">" . _ORDERBY . " : ";

    if (! isset($_REQUEST['orderby']))
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
        echo "<b>" . __('AUTHOR') . "</b>";
    }
    else
    {
        echo "<a href=\"index.php?file=Archives&amp;op=sujet&amp;cat_id=" . $cat_id . "&amp;orderby=auteur\">" . __('AUTHOR') . "</a>";
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
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . __('AUTHOR') . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _OPTION . "&nbsp;</b></td></tr>\n";

    if ($_REQUEST['orderby'] == "titre")
    {
        $sql2 = nkDB_execute("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE cat = '" . $cat_id . "' AND '" . $day . "' >= date ORDER BY titre LIMIT " . $start . ", " . $nb_news."");
    }
    else if ($_REQUEST['orderby'] == "date")
    {
        $sql2 = nkDB_execute("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE cat = '" . $cat_id . "' AND '" . $day . "' >= date ORDER BY date DESC LIMIT " . $start . ", " . $nb_news."");
    }
    else if ($_REQUEST['orderby'] == "auteur")
    {
        $sql2 = nkDB_execute("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE cat = '" . $cat_id . "' AND '" . $day . "' >= date ORDER BY auteur LIMIT " . $start . ", " . $nb_news."");
    }
    else
    {
        $sql2 = nkDB_execute("SELECT id, titre, auteur, auteur_id, date, cat FROM " . NEWS_TABLE . " WHERE cat = '" . $cat_id . "' AND '" . $day . "' >= date ORDER BY id DESC LIMIT " . $start . ", " . $nb_news."");
    }

    $j = 0;

    while (list($news_id, $titre, $autor, $autor_id, $date, $cat) = nkDB_fetchArray($sql2))
    {

        $date = nkDate($date);

        if (strlen($titre) > 25)
        {
            $title = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $news_id . "\" title=\"" . $titre . "\">" . nkHtmlEntities(substr($titre, 0, 25)) . "...</a>";
        }
        else
        {
            $title = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $news_id . "\">" . nkHtmlEntities($titre) . "</a>";
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

        $sql3 = nkDB_execute("SELECT titre FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cat . "'");
        list($categorie) = nkDB_fetchArray($sql3);
        $categorie = nkHtmlEntities($categorie);

        echo "<td style=\"width: 20%;\" align=\"center\"><i>" . $categorie . "</i></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\">" . $date . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\">";

        if ($autor_id != '') {
            echo "<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($autor) . "\" title=\"" . _DETAILAUTHOR . "&nbsp;" . $autor . "\">" . $autor . "</a>";
        }
        else {
            echo $autor;
        }

        echo "</td>\n"
        . "<td style=\"width: 10%;\" align=\"center\">"
        . "<a href=\"index.php?file=News&amp;op=pdf&amp;news_id=" . $news_id . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"images/pdf.gif\" alt=\"\" title=\"" . _APDF . "\" /></a>"
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

opentable();

switch ($GLOBALS['op'])
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

closetable();

?>
