<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined("INDEX_CHECK") or die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
global $language, $user;
translate("modules/Download/lang/" . $language . ".lang.php");

$visiteur = $user ? $user[1] : 0;

$sql2 = mysql_query("SELECT active FROM " . BLOCK_TABLE . " WHERE bid='$bid'");
list($active) = mysql_fetch_array($sql2);
if ($active == 3 || $active == 4) {

    if (is_file("themes/" . $theme . "/images/files.gif")) {
        $img = "<img src=\"themes/" . $theme . "/images/files.gif\" alt=\"\" />";
    } else {
        $img = "<img src=\"modules/Download/images/files.gif\" alt=\"\" />";
    }


    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\">\n"
       . "<tr><td style=\"width: 45%;\" valign=\"top\"><a href=\"index.php?file=Download&amp;op=classe&amp;orderby=news\"><big><b>" . _LASTDOWN . "</b></big></a><br /><br />\n";

    $i = 0;
    $sql = mysql_query("SELECT id, titre, date, type, description FROM " . DOWNLOAD_TABLE . " WHERE " . $visiteur . " >= level ORDER BY id DESC LIMIT 0, 10");
    while (list($dl_id, $titre, $date, $cat, $description) = mysql_fetch_array($sql)) {
        $titre = printSecuTags($titre);
        $date = nkDate($date);
        $description = strip_tags($description);
        $description = strlen($description > 150) ? substr($description, 0, 147) . '..' : $description;

        $sql4 = mysql_query("SELECT titre, parentid FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cat . "'");
        list($cat_name, $parentid) = mysql_fetch_array($sql4);
        $cat_name = printSecuTags($cat_name);

        if ($cat == 0) {
            $category = "";
        } else if ($parentid > 0) {
            $sql5 = mysql_query("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
            list($parent_name) = mysql_fetch_array($sql5);
            $parent_name = printSecuTags($parent_name);

            $category = $parent_name . " - " . $cat_name;
        } else {
            $category = $cat_name;
        }

        $i++;

        echo "<b>" . $i . " . <a href=\"index.php?file=Download&amp;op=description&amp;dl_id=" . $dl_id . "\" style=\"text-decoration: underline\" title=\"" . $description . "\">" . $titre . "</a></b><br />\n";

        if ($category != "") echo $category . "<br />\n";
    }

    echo "</td><td style=\"width: 10%;\">&nbsp;</td><td style=\"width: 45%;\" align=\"left\" valign=\"top\"><a href=\"index.php?file=Download&amp;op=classe&amp;orderby=count\"><big><b>" . _TOPDOWN . "</b></big></a><br /><br />\n";

    $l = 0;
    $sql3 = mysql_query("SELECT id, titre, count, type, description FROM " . DOWNLOAD_TABLE . " WHERE " . $visiteur . " >= level ORDER BY count DESC LIMIT 0, 10");
    while (list($tdl_id, $ttitre, $tcount, $tcat, $tdesc) = mysql_fetch_array($sql3)) {
        $sql4 = mysql_query("SELECT titre, parentid FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $tcat . "'");
        list($tcat_name, $tparentid) = mysql_fetch_array($sql4);
        $tcat_name = printSecuTags($tcat_name);
        $tdesc = strip_tags($tdesc);
        $tdesc = strlen($tdesc > 150) ? substr($tdesc, 0, 147) . '..' : $tdesc;

        if ($tcat == 0) {
            $tcategory = "";
        } else if ($tparentid > 0) {
            $sql5 = mysql_query("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $tparentid . "'");
            list($tparent_name) = mysql_fetch_array($sql5);
            $tparent_name = printSecuTags($tparent_name);

            $tcategory = $tparent_name . " - " . $tcat_name;
        } else {
            $tcategory = $tcat_name;
        }

        $l++;

        echo "<b>" . $l . " . <a href=\"index.php?file=Download&amp;op=description&amp;dl_id=" . $tdl_id . "\" style=\"text-decoration: underline\" title=\"" . $tdesc . "\">" . $ttitre . "</a></b><br />\n";

        if ($tcategory != "") echo $tcategory . "<br />\n";
    }

    echo "</td></tr><tr><td style=\"width: 45%;\" align=\"right\"><a href=\"index.php?file=Download&amp;op=classe&amp;orderby=news\"><small>+ " . _MORELAST . "</small></a></td>\n"
       . "<td style=\"width: 10%;\"></td><td style=\"width: 45%;\" align=\"right\"><a href=\"index.php?file=Download&amp;op=classe&amp;orderby=count\"><small>+ " . _MORETOP . "</small></a></td></tr></table>\n";
} else {
    $i = 0;
    $sql = mysql_query("SELECT id, titre, date, description FROM " . DOWNLOAD_TABLE . " WHERE " . $visiteur . " >= level ORDER BY date DESC LIMIT 0, 10");
    while (list($dl_id, $titre, $date, $description) = mysql_fetch_array($sql)) {
        $titre = printSecuTags($titre);
        $description = strip_tags($description);
        $description = strlen($description > 150) ? substr($description, 0, 147) . '..' : $description;
        $date = nkDate($date);
        $i++;

        echo "<div><b>" . $i . " . <a href=\"index.php?file=Download&amp;op=description&amp;dl_id=" . $dl_id . "\" title=\"" . $description . "\">" . $titre . "</a></b> (" . $date . ")</div>\n";

    }
}

?>