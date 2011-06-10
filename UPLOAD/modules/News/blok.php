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
translate("modules/News/lang/" . $language . ".lang.php");

$day = time();

$sql2 = mysql_query("SELECT active FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
list($active) = mysql_fetch_array($sql2);
if ($active == 3 || $active == 4)
{

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n";

    $sql = mysql_query("SELECT id, titre, date, auteur, auteur_id FROM " . NEWS_TABLE . " WHERE '" . $day . "' >= date ORDER BY date DESC LIMIT 0, 5");
    while (list($news_id, $titre, $date, $autor, $autor_id) = mysql_fetch_array($sql))
    {
        $date = strftime("%x %H:%M", $date);
        $titre = htmlentities($titre);

            if ($autor_id != "")
            {
            $sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
            list($auteur) = mysql_fetch_array($sql2);
            }
            else
            {
                $auteur = $autor;
            }

        echo "<tr><td>&nbsp;<b><big>&middot;</big></b>&nbsp;<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $news_id . "\"><b>" . $titre . "</b></a> " . _BY . " <a href=\"index.php?file=Team&amp;op=detail&amp;autor=" . urlencode($auteur) . "\">" . $auteur . "</a> ( " . $date . " )</td></tr>\n";
    }
    echo "</table><br />\n";
}

else
{
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n";

    $sql = mysql_query("SELECT id, titre, date, auteur, auteur_id FROM " . NEWS_TABLE . " WHERE " . $day . " >= date ORDER BY date DESC LIMIT 0, 5");
    while (list($news_id, $titre, $date, $autor, $autor_id) = mysql_fetch_array($sql))
    {
        $date = strftime("%x %H:%M", $date);
        $titre = htmlentities($titre);

            if ($autor_id != "")
            {
            $sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
            list($auteur) = mysql_fetch_array($sql2);
            }
            else
            {
                $auteur = $autor;
            }

        echo "<tr><td>&nbsp;<b><big>&middot;</big></b>&nbsp;<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $news_id . "\"><b>" . $titre . "</b></a></td></tr>\n"
	. "<tr><td>" . _BY . " <a href=\"index.php?file=Team&amp;op=detail&amp;autor=" . urlencode($auteur) . "\">" . $auteur . "</a> ( $date )<hr style=\"color: " . $bgcolor3 . ";height: 1px;\" /></td></tr>\n";
    }
    echo "</table><br />\n";
}

?>
