<?php
/**
 * blok.php
 *
 * Display block of Gallery module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or exit('You can\'t run this file alone.');

global $language, $nuked, $bgcolor1, $bgcolor3;

translate('modules/Gallery/lang/'. $language .'.lang.php');
include 'modules/Gallery/config.php';

# include css and js library shadowbox
nkTemplate_addCSSFile('media/shadowbox/shadowbox.css');
nkTemplate_addJSFile('media/shadowbox/shadowbox.js');
nkTemplate_addJS('Shadowbox.init();');


if ($active == 3 || $active == 4)
{
    echo"<table style=\"margin-left:auto; margin-right:auto; text-align:left;\" cellpadding=\"10\" cellspacing=\"10\" border=\"0\">\n"
    . "<tr style=\"background: " . $bgcolor1 . ";\">";

    $sql = nkDB_execute("SELECT sid, titre, url, url2 FROM " . GALLERY_TABLE . " ORDER BY sid DESC LIMIT 0, 3");
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
    $sql = nkDB_execute("SELECT sid, titre, url, url2 FROM " . GALLERY_TABLE . " ORDER BY sid DESC LIMIT 0, 1");
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