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

        $titre = htmlentities($titre);

        if ($img != "")
        {
            echo" <td style=\"border: 1px dashed " . $bgcolor3 . ";\" align=\"center\"><a href=\"index.php?file=Gallery&amp;op=description&amp;sid=" . $sid . "\"><b>" . $titre . "</b></a><br />\n"
            . "<a href=\"" . $url . "\" class=\"thickbox\" title=\"" . $titre . "\">" . $image . "</a></td>\n";
        }
    }
    echo "</tr></table>\n";
}
else
{
    $sql = mysql_query("SELECT sid, titre, url, url2 FROM " . GALLERY_TABLE . " ORDER BY sid DESC LIMIT 0, 1");
    list($sid, $titre, $url, $url2) = mysql_fetch_array($sql);
    $titre = htmlentities($titre);

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
        . "<div style=\"text-align: center;\"><a href=\"" . $url . "\" class=\"thickbox\" title=\"" . $titre . "\">" . $image . "</a></div>\n";
    }
}
?>
<script type="text/javascript">
        //<![CDATA[
        var xtralink = "non";
            function screenon(lien,lien2)
            {
                xtralink = lien2;
                document.getElementById("iframe").innerHTML = "<iframe style=\"border:0px;\" width=\"100%\" height=\"80%\" src=\""+lien+"\"></iframe>";
                document.getElementById("screen").style.display="block";
            }
            function screenoff()
            {
                document.getElementById("screen").style.display="none";
            }
        //]]>
</script>
<?php

if($_REQUEST['file'] != 'Gallery' || ($_REQUEST['file'] == 'Gallery' && isset($_REQUEST['op']) && $_REQUEST['op'] != 'description')){
    echo '<div id="screen" onclick="screenoff()" style="display:none;position:absolute;width:100%;height:100%;background:  url(modules/Admin/images/bg.png) repeat;z-index:10000;top:0px; left:0px;">
        <div id="iframe" style="margin-left:5%;margin-top:5%; width:90%;height:90%;">
            
        </div>
        <div style="display:block;width:295px;height:25px;background:url(images/croix.png) no-repeat;position:absolute;right:3%;bottom:3%;z-index:20000;">&nbsp;</div>
        </div>';
}
?>