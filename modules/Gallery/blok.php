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


global $language, $nuked, $bgcolor1, $bgcolor3;
translate("modules/Gallery/lang/" . $language . ".lang.php");
include("modules/Gallery/config.php");

echo '<script type="text/javascript"><!--'."\n"
. 'document.write(\'<link rel="stylesheet" type="text/css" href="media/shadowbox/shadowbox.css">\');'."\n"
. '--></script>'."\n"
. '<script type="text/javascript" src="media/shadowbox/shadowbox.js"></script>'."\n"
. '<script type="text/javascript">'."\n"
. 'Shadowbox.init();'."\n"
. '</script>'."\n";

$sql2 = mysql_query("SELECT active FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
list($active) = mysql_fetch_array($sql2);
if ($active == 3 || $active == 4)
{
    echo"<table style=\"margin-left:auto; margin-right:auto; text-align:left;\" cellpadding=\"10\" cellspacing=\"10\" border=\"0\">\n"
    . "<tr style=\"background: " . $bgcolor1 . ";\">";

    $sql = mysql_query("SELECT sid, titre, url, url2 FROM " . GALLERY_TABLE . " ORDER BY sid DESC LIMIT 0, 3");
    while (list($sid, $titre, $url, $url2) = mysql_fetch_array($sql))
    {
        if ($url2 != "")
        {
            $img = $url2;
        }
        else
        {
            $img = $url;
        }

        if (!preg_match("`%20`i", $img)) list($w, $h, $t, $a) = @getimagesize($img);
        if ($w != "" && $w <= $img_screen1) $width = "width=\"" . $w . "\"";
        else $width = "width=\"" . $img_screen1 . "\"";
        $image = "<img style=\"border: 1px solid #000000;\" src=\"" . $img . "\" " . $width . " alt=\"" . $titre . "\" title=\"" .  _CLICTOSCREEN . "\" />";

        $titre = printSecuTags($titre);

        if ($img != "")
        {
            echo" <td style=\"border: 1px dashed " . $bgcolor3 . ";\" align=\"center\"><a href=\"index.php?file=Gallery&amp;op=description&amp;sid=" . $sid . "\"><b>" . $titre . "</b></a><br />\n"
            . "<a href=\"" . $url . "\" rel=\"shadowbox\" title=\"" . $titre . "\">" . $image . "</a></td>\n";
        }
    }
    echo "</tr></table>\n";
}
else
{
    $sql = mysql_query("SELECT sid, titre, url, url2 FROM " . GALLERY_TABLE . " ORDER BY sid DESC LIMIT 0, 1");
    list($sid, $titre, $url, $url2) = mysql_fetch_array($sql);
    $titre = printSecuTags($titre);

    if ($url2 != "")
    {
        $img = $url2;
    }
    else
    {
        $img = $url;
    }

    if (!preg_match("`%20`i", $img)) list($w, $h, $t, $a) = @getimagesize($img);
    if ($w != "" && $w <= $img_screen1) $width = "width=\"" . $w . "\"";
    else $width = "width=\"" . $img_screen1 . "\"";
    $image = "<img style=\"border: 1px solid #000000;\" src=\"" . $img . "\" " . $width . " alt=\"" . $titre . "\" title=\"" .  _CLICTOSCREEN . "\" />";

    if ( $img != "")
    {
        echo "<div style=\"text-align: center;\"><a href=\"index.php?file=Gallery&amp;op=description&amp;sid=" . $sid . "\"><b>" . $titre . "</b></a></div>\n"
        . "<div style=\"text-align: center;\"><a href=\"" . $url . "\" rel=\"shadowbox\" title=\"" . $titre . "\">" . $image . "</a></div>\n";
    }
}
?>