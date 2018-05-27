<?php
/**
 * blok.php
 *
 * Display block of Stats module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or exit('You can\'t run this file alone.');

global $nuked, $language;

translate('modules/Stats/lang/'. $language .'.lang.php');


if ($active == 3 || $active == 4)
{
    $sql = nkDB_execute("SELECT SUM(count) FROM " . STATS_TABLE . " WHERE type = 'pages'");
    list($counter) = nkDB_fetchArray($sql);

    $date_install = nkDate($nuked['date_install']);

    echo "<div style=\"text-align: center;\">" . _WERECEICED . "&nbsp;" . $counter . "&nbsp;" . _PAGESEE . "&nbsp;" . $date_install . ".</div><br />\n";

    $sql_users = nkDB_execute("SELECT id FROM " . USER_TABLE);
    $nb_users = nkDB_numRows($sql_users);

    echo "&nbsp;<b><big>·</big></b>&nbsp;<b>" . $nb_users . "</b> " . _MEMBERSRECORD . "<br />\n";

    if (nivo_mod("News") != -1)
    {
        $sql_news = nkDB_execute("SELECT id FROM " . NEWS_TABLE);
        $nb_news = nkDB_numRows($sql_news);
        echo "&nbsp;<b><big>·</big></b>&nbsp;<b>" . $nb_news . "</b> " . _SNEWSINDB . "<br />\n";
    }

    if (nivo_mod("Download") != -1)
    {
        $sql_dl = nkDB_execute("SELECT id FROM " . DOWNLOAD_TABLE);
        $nb_downloads = nkDB_numRows($sql_dl);
        echo "&nbsp;<b><big>·</big></b>&nbsp;<b>" . $nb_downloads . "</b> " . _FILESINDB . "<br />\n";
    }

    if (nivo_mod("Links") != -1)
    {
        $sql_links = nkDB_execute("SELECT id FROM " . LINKS_TABLE);
        $nb_liens = nkDB_numRows($sql_links);
        echo "&nbsp;<b><big>·</big></b>&nbsp;<b>" . $nb_liens . "</b> " . _LINKSINDB . "<br /><br />\n";
    }

    echo "<div style=\"text-align: center;\"><a href=\"index.php?file=Stats\">" . _STATSBLOCK . "</a>/<a href=\"index.php?file=Stats&amp;op=top10\">" . _TOPBLOCK . "</a></div>\n";
}
else
{
    $sql = nkDB_execute("SELECT SUM(count) FROM " . STATS_TABLE . " WHERE type = 'pages'");
    list($counter) = nkDB_fetchArray($sql);

    $date_install = nkDate($nuked['date_install']);

    $sql_users = nkDB_execute("SELECT id FROM " . USER_TABLE);
    $nb_users = nkDB_numRows($sql_users);

    echo "<div style=\"text-align: center;\">" . _PAGESEE . "<br />" . $date_install . " : " . $counter . "</div>\n"
    . "<hr style=\"height: 1px;\" />"
    . "&nbsp;<b><big>·</big></b>&nbsp;" . _NBMEMBERS . " : <b>" . $nb_users . "</b><br />\n";

    if (nivo_mod("News") != -1)
    {
        $sql_news = nkDB_execute("SELECT id FROM " . NEWS_TABLE);
        $nb_news = nkDB_numRows($sql_news);
        echo "&nbsp;<b><big>·</big></b>&nbsp;" . _NBNEWS . " : <b>" . $nb_news . "</b><br />\n";
    }

    if (nivo_mod("Download") != -1)
    {
        $sql_dl = nkDB_execute("SELECT id FROM " . DOWNLOAD_TABLE);
        $nb_downloads = nkDB_numRows($sql_dl);
        echo "&nbsp;<b><big>·</big></b>&nbsp;" . _NBDOWNLOAD . " : <b>" . $nb_downloads . "</b><br />\n";
    }

    if (nivo_mod("Links") != -1)
    {
        $sql_links = nkDB_execute("SELECT id FROM " . LINKS_TABLE);
        $nb_liens = nkDB_numRows($sql_links);
        echo "&nbsp;<b><big>·</big></b>&nbsp;" . _NBLINKS . " : <b>" . $nb_liens . "</b><br />\n";
    }

    echo "<hr style=\"height: 1px;\" />\n"
    . "<div style=\"text-align: center;\"><a href=\"index.php?file=Stats\">" . _STATSBLOCK . "</a>/<a href=\"index.php?file=Stats&amp;page=top\">" . _TOPBLOCK . "</a></div>\n";
}

?>
