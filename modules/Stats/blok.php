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

global $nuked, $language;
translate("modules/Stats/lang/" . $language . ".lang.php");

$sql2 = mysql_query("SELECT active FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
list($active) = mysql_fetch_array($sql2);
if ($active == 3 || $active == 4)
{
    $sql = mysql_query("SELECT count FROM " . STATS_TABLE . " WHERE type = 'pages'");
    while (list($count) = mysql_fetch_array($sql))
    {
        $counter = $counter + $count;
    }

    $date_install = nkDate($nuked['date_install']);

    echo "<div style=\"text-align: center;\">" . _WERECEICED . "&nbsp;" . $counter . "&nbsp;" . _PAGESEE . "&nbsp;" . $date_install . ".</div><br />\n";

    $sql_users = mysql_query("SELECT id FROM " . USER_TABLE);
    $nb_users = mysql_num_rows($sql_users);

    echo "&nbsp;<b><big>·</big></b>&nbsp;<b>" . $nb_users . "</b> " . _MEMBERSRECORD . "<br />\n";

    if (nivo_mod("News") != -1)
    {
        $sql_news = mysql_query("SELECT id FROM " . NEWS_TABLE);
        $nb_news = mysql_num_rows($sql_news);
        echo "&nbsp;<b><big>·</big></b>&nbsp;<b>" . $nb_news . "</b> " . _NEWSINDB . "<br />\n";
    }

    if (nivo_mod("Download") != -1)
    {
        $sql_dl = mysql_query("SELECT id FROM " . DOWNLOAD_TABLE);
        $nb_downloads = mysql_num_rows($sql_dl);
        echo "&nbsp;<b><big>·</big></b>&nbsp;<b>" . $nb_downloads . "</b> " . _FILESINDB . "<br />\n";
    }

    if (nivo_mod("Links") != -1)
    {
        $sql_links = mysql_query("SELECT id FROM " . LINKS_TABLE);
        $nb_liens = mysql_num_rows($sql_links);
        echo "&nbsp;<b><big>·</big></b>&nbsp;<b>" . $nb_liens . "</b> " . _LINKSINDB . "<br /><br />\n";
    }

    echo "<div style=\"text-align: center;\"><a href=\"index.php?file=Stats\">" . _STATSBLOCK . "</a>/<a href=\"index.php?file=Stats&amp;op=top10\">" . _TOPBLOCK . "</a></div>\n";
}
else
{
    $sql = mysql_query("SELECT count FROM " . STATS_TABLE . " WHERE type = 'pages'");
    while (list($count) = mysql_fetch_array($sql))
    {
        $counter = $counter + $count;
    }

    $date_install = nkDate($nuked['date_install']);

    $sql_users = mysql_query("SELECT id FROM " . USER_TABLE);
    $nb_users = mysql_num_rows($sql_users);

    echo "<div style=\"text-align: center;\">" . _PAGESEE . "<br />" . $date_install . " : " . $counter . "</div>\n"
    . "<hr style=\"height: 1px;\" />"
    . "&nbsp;<b><big>·</big></b>&nbsp;" . _NBMEMBERS . " : <b>" . $nb_users . "</b><br />\n";

    if (nivo_mod("News") != -1)
    {
        $sql_news = mysql_query("SELECT id FROM " . NEWS_TABLE);
        $nb_news = mysql_num_rows($sql_news);
        echo "&nbsp;<b><big>·</big></b>&nbsp;" . _NBNEWS . " : <b>" . $nb_news . "</b><br />\n";
    }

    if (nivo_mod("Download") != -1)
    {
        $sql_dl = mysql_query("SELECT id FROM " . DOWNLOAD_TABLE);
        $nb_downloads = mysql_num_rows($sql_dl);
        echo "&nbsp;<b><big>·</big></b>&nbsp;" . _NBDOWNLOAD . " : <b>" . $nb_downloads . "</b><br />\n";
    }

    if (nivo_mod("Links") != -1)
    {
        $sql_links = mysql_query("SELECT id FROM " . LINKS_TABLE);
        $nb_liens = mysql_num_rows($sql_links);
        echo "&nbsp;<b><big>·</big></b>&nbsp;" . _NBLINKS . " : <b>" . $nb_liens . "</b><br />\n";
    }

    echo "<hr style=\"height: 1px;\" />\n"
    . "<div style=\"text-align: center;\"><a href=\"index.php?file=Stats\">" . _STATSBLOCK . "</a>/<a href=\"index.php?file=Stats&amp;page=top\">" . _TOPBLOCK . "</a></div>\n";
}

?>