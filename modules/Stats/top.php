<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.eu                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

opentable();

global $nuked, $language, $bgcolor3, $bgcolor2, $bgcolor1;
translate("modules/Stats/lang/" . $language . ".lang.php");

echo "<div style=\"text-align: center;\"><br /><big><b>" . _TOP10 . "&nbsp;" . $nuked['name'] . "</b></big></div>\n";

if (nivo_mod("Download") != -1)
{
    echo "<div style=\"text-align: center;\"><br /><br /><big>" . _TOPDOWNLOAD . "</big></div>\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . "; border: 1px solid " . $bgcolor3 . ";\" width=\"50%\" cellpadding=\"2\" cellspacing=\"1\">\n"
    . "<tr style=\"background: " . $bgcolor3 . ";\">\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
    . "<td style=\"width: 60%;\" align=\"center\"><b>" . _NOM . "</b></td>\n"
    . "<td style=\"width: 30%;\" align=\"center\"><b>" . _DOWNLOADCOUNT . "</b></td></tr>\n";

    $sql = mysql_query("SELECT id, titre, count FROM " . DOWNLOAD_TABLE . " ORDER BY count DESC LIMIT 0, 10");
    $nb_dl = mysql_num_rows($sql);
    if ($nb_dl > 0)
    {
	$idl = 0;
	while (list($dl_id, $dl_titre, $dl_count) = mysql_fetch_array($sql))
	{
            $idl++;
            $dl_titre = htmlentities($dl_titre);

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

            echo "<tr style=\"background: " . $bg . ";\">\n"
            . "<td style=\"width: 10%;\" align=\"center\">" . $idl . "</td>\n"
            . "<td style=\"width: 60%;\"><a href=\"index.php?file=Download&amp;op=description&amp;dl_id=" . $dl_id . "\">" . $dl_titre . "</a></td>\n"
            . "<td style=\"width: 30%;\" align=\"center\">" . $dl_count . "</td></tr>\n";
            } 
    } 
    else
    {
	echo "<tr><td align=\"center\" colspan=\"3\">" . _NODOWNLOAD . "</td></tr>\n";
    }

    echo "</table>\n";
}


if (nivo_mod("Links") != -1)
{
    echo" <div style=\"text-align: center;\"><br /><br /><big>" . _TOPLINK . "</big></div>\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . "; border: 1px solid " . $bgcolor3 . ";\" width=\"50%\" cellpadding=\"2\" cellspacing=\"1\">\n"
    . "<tr style=\"background: " . $bgcolor3 . ";\">\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
    . "<td style=\"width: 60%;\" align=\"center\"><b>" . _NOM . "</b></td>\n"
    ." <td style=\"width: 30%;\" align=\"center\"><b>" . _VISITCOUNT . "</b></td></tr>\n";

    $sql2 = mysql_query("SELECT id, titre, count FROM " . LINKS_TABLE . " ORDER BY count DESC LIMIT 0, 10");
    $nb_link = mysql_num_rows($sql2);
    if ($nb_link > 0)
    {
	$ilink = 0;
	while (list($link_id, $link_titre, $link_count) = mysql_fetch_array($sql2))
	{
            $ilink++;
            $link_titre = htmlentities($link_titre);

            if ($j1 == 0)
            {
                $bg1 = $bgcolor2;
                $j1++;
	    } 
            else
            {
                $bg1 = $bgcolor1;
                $j1 = 0;
            } 

            echo "<tr style=\"background: " . $bg1 . ";\">\n"
	    . "<td style=\"width: 10%;\" align=\"center\">" . $ilink . "</td>\n"
	    . "<td style=\"width: 60%;\"><a href=\"index.php?file=Links&amp;op=description&amp;link_id=" . $link_id . "\">" . $link_titre . "</a></td>\n"
	    . "<td style=\"width: 30%;\" align=\"center\">" . $link_count . "</td></tr>\n";
	} 
    } 
    else
    {
        echo "<tr><td align=\"center\" colspan=\"3\">" . _NOLINK . "</td></tr>\n";
    }

    echo "</table>\n";
}


if (nivo_mod("Sections") != -1)
{
    echo "<div style=\"text-align: center;\"><br /><br /><big>" . _TOPARTICLE . "</big></div>\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . "; border: 1px solid " . $bgcolor3 . ";\" width=\"50%\" cellpadding=\"2\" cellspacing=\"1\">\n"
    . "<tr style=\"background: " . $bgcolor3 . ";\">\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
    . "<td style=\"width: 60%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
    . "<td style=\"width: 30%;\" align=\"center\"><b>" . _READCOUNT . "</b></td></tr>\n";

    $sql3 = mysql_query("SELECT artid, title, counter FROM " . SECTIONS_TABLE . " ORDER BY counter DESC LIMIT 0, 10");
    $nb_art = mysql_num_rows($sql3);
    if ($nb_art > 0)
    {
        $iart = 0;
        while (list($art_id, $art_titre, $art_count) = mysql_fetch_array($sql3))
        {
            $iart++;
            $art_titre = htmlentities($art_titre);

            if ($j2 == 0)
            {
                $bg2 = $bgcolor2;
                $j2++;
            } 
            else
            {
                $bg2 = $bgcolor1;
                $j2 = 0;
            } 

            echo "<tr style=\"background: " . $bg2 . ";\">\n"
	    . "<td style=\"width: 10%;\" align=\"center\">" . $iart . "</td>\n"
	    . "<td style=\"width: 60%;\"><a href=\"index.php?file=Sections&amp;op=article&amp;artid=" . $art_id . "\">" . $art_titre . "</a></td>\n"
	    . "<td style=\"width: 30%;\" align=\"center\">" . $art_count . "</td></tr>\n";
        } 
    } 
    else
    {
        echo "<tr><td align=\"center\" colspan=\"3\">" . _NOART . "</td></tr>\n";
    } 

    echo "</table>\n";
}


if (nivo_mod("Forum") != -1)
{
    echo "<div style=\"text-align: center;\"><br /><br /><big>" . _TOPTHREADS . "</big></div>\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . "; border: 1px solid " . $bgcolor3 . ";\" width=\"50%\" cellpadding=\"2\" cellspacing=\"1\">\n"
    . "<tr style=\"background: " . $bgcolor3 . ";\">\n" 
    . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
    . "<td style=\"width: 60%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
    . "<td style=\"width: 30%;\" align=\"center\"><b>" . _READCOUNT . "</b></td></tr>\n";

    $sql4 = mysql_query("SELECT id, forum_id, titre, view FROM " . FORUM_THREADS_TABLE . " ORDER BY view DESC LIMIT 0, 10");
    $nb_topic = mysql_num_rows($sql4);
    if ($nb_topic > 0)
    {
        $itopic = 0;
        while (list($tid, $fid, $topic_titre, $views) = mysql_fetch_array($sql4))
        {
            $itopic++;
            $topic_titre = htmlentities($topic_titre);
            $topic_titre = nk_CSS($topic_titre);

            if ($j3 == 0)
            {
                $bg3 = $bgcolor2;
                $j3++;
            } 
            else
            {
                $bg3 = $bgcolor1;
                $j3 = 0;
            } 

            echo "<tr style=\"background: " . $bg3 . ";\">\n"
	    . "<td style=\"width: 10%;\" align=\"center\">" . $itopic . "</td>\n"
	    . "<td style=\"width: 60%;\"><a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $fid . "&amp;thread_id=" . $tid . "\">" . $topic_titre . "</a></td>\n"
	    . "<td style=\"width: 30%;\" align=\"center\">" . $views . "</td></tr>\n";
        } 
    } 
    else
    {
        echo "<tr><td align=\"center\" colspan=\"3\">" . _NOTOPIC . "</td></tr>\n";
    } 

    echo "</table><div style=\"text-align: center;\"><br /><br /><big>" . _TOPUSERFORUM . "</big></div>\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . "; border: 1px solid " . $bgcolor3 . ";\" width=\"50%\" cellpadding=\"2\" cellspacing=\"1\">\n"
    . "<tr style=\"background: " . $bgcolor3 . ";\">\n" 
    . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
    . "<td style=\"width: 60%;\" align=\"center\"><b>" . _PSEUDO . "</b></td>\n"
    . "<td style=\"width: 30%;\" align=\"center\"><b>" . _POSTCOUNT . "</b></td></tr>\n";

    $sql5 = mysql_query("SELECT pseudo, count FROM " . USER_TABLE . " ORDER BY count DESC LIMIT 0, 10");

    $iuserf = 0;
    while (list($pseudof, $userfcount) = mysql_fetch_array($sql5))
    {
        $iuserf++;

        if ($j4 == 0)
        {
            $bg4 = $bgcolor2;
            $j4++;
        } 
        else
        {
            $bg4 = $bgcolor1;
            $j4 = 0;
        } 

        echo "<tr style=\"background: " . $bg4 . ";\">\n"
	. "<td style=\"width: 10%;\" align=\"center\">" . $iuserf . "</td>\n"
	. "<td style=\"width: 60%;\"><a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($pseudof) . "\">" . $pseudof . "</a></td>\n"
	. "<td style=\"width: 30%;\" align=\"center\">" . $userfcount . "</td></tr>\n";
    }

    echo "</table>\n";
}

echo" <div style=\"text-align: center;\"><br /><br />[ <a href=\"index.php?file=Stats\">" . _STATISTICS . "</a> ]<br /><br /></div>\n";

closetable();

?>