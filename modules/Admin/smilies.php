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

global $user, $language;
translate("modules/Admin/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
if (!$user)
{
    $visiteur = 0;
}
else
{
    $visiteur = $user[1];
}

admintop();

if ($visiteur == 9)
{

    function main()
    {
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function delsmiley(name, id)\n"
        . "{\n"
        . "if (confirm('" . _DELBLOCK . " '+name+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=Admin&page=smilies&op=del_smiley&smiley_id='+id;}\n"
        . "}\n"
            . "\n"
        . "// -->\n"
        . "</script>\n";
        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _SMILIEADMIN . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/smilies.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin&amp;page=smilies&amp;op=add_smiley\"><b>" . _SMILEYADD . "</b></a> ]</div><br />\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _SMILEY . "</b></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _CODE . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, code, url, name FROM " . SMILIES_TABLE . " ORDER BY id");
        while (list($smiley_id, $code, $url, $name) = mysql_fetch_array($sql))
        {
            $name = htmlentities($name);

            echo "<tr>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><img src=\"images/icones/" . $url . "\" alt=\"\" title=\"$url\" /></td>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $name . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $code . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=smilies&amp;op=edit_smiley&amp;smiley_id=" . $smiley_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _SMILEYEDIT . "\" /></a></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><a href=\"javascript:delsmiley('" . mysql_real_escape_string($name) . "', '" . $smiley_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _SMILEYDEL . "\" /></a></td></tr>\n";
        }

        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }

    function add_smiley()
    {
        global $language;

        echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function update_smiley(newimage)\n"
        . "{\n"
        . "document.getElementById('smiley').src = 'images/icones/' + newimage;\n"
        . "}\n"
            . "\n"
        . "// -->\n"
        . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _SMILIEADMIN . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/smilies.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=smilies&amp;op=send_smiley\" enctype=\"multipart/form-data\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n"
        . "<tr><td><b>" . _NAME . " :</b> <input type=\"text\" name=\"nom\" size=\"30\" /></td></tr>\n"
        . "<tr><td><b>" . _CODE . " :</b> <input type=\"text\" name=\"code\" size=\"10\" /></td></tr><tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _SMILEY . " :</b> <select name=\"url\" onchange=\"update_smiley(this.options[selectedIndex].value);\">";

        $i = 0;
        $rep = Array();
        $path = "images/icones";
        $handle = opendir($path);
        while (false !== ($ikon = readdir($handle)))
        {
            if ($ikon != "." && $ikon != ".." && $ikon != "index.html" && $ikon != "Thumbs.db")
            {
                $rep[] = $ikon;

                if ($i == 0)
                {
                    $img = "images/icones/" . $ikon;
                }

                $i++;
            }
        }

        closedir($handle);
        sort ($rep);
        reset ($rep);

        while (list ($key, $filename) = each ($rep))
        {
                echo "<option value=\"" . $filename . "\">" . $filename . "</option>\n";
        }

        echo "</select>&nbsp;&nbsp;";

        if ($i > 0)
        {
            echo "<img id=\"smiley\" src=\"" . $img . "\" alt=\"\" />";
        }

        echo "</td></tr><tr><td><b>" . _UPSMILEY . " : </b><input type=\"file\" name=\"fichiernom\" /></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n"
        . "<tr><td style=\"text-align:center;\"><input type=\"submit\" value=\"" . _SEND . "\" /></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin&amp;page=smilies\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function send_smiley($nom, $code, $url, $fichiernom)
    {
        global $nuked, $user;
        
        if (($nom == $code) || (strpos($code,'"')!==false) || (strpos($code,"'")!==false))
        {
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . _SMILEYNOTAUTHORIZE . ".\n"
            . "</div>\n"
            . "</div>\n";
            redirect("index.php?file=Admin&page=smilies&op=add_smiley", 2);
            adminfoot();
            footer();
            exit();
        }

        $nom = mysql_real_escape_string(stripslashes($nom));
        $filename = $_FILES['fichiernom']['name'];
        
        if ($filename != "")
        {
            $ext = strrchr($filename, ".");
            $ext = substr($ext, 1);

            if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG")
            {
                $url_image = "images/icones/" . $filename;
                move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_image) or die ("<br /><br /><div style=\"text-align: center;\"><b>Upload file failed !!!</b></div><br /><br />");
                @chmod ($url_image, 0644);
            }
            else
            {
                echo "<div class=\"notification error png_bg\">\n"
                . "<div>\n"
                . "No image file !"
                . "</div>\n"
                . "</div>\n";
                redirect("index.php?file=Admin&page=smilies&op=add_smiley", 2);
                adminfoot();
                footer();
                exit();
            }
        }
        else
        {
            $filename = $url;
        }

        $sql = mysql_query("INSERT INTO " . SMILIES_TABLE . " ( `id` , `code` , `url` , `name` ) VALUES ( '' , '" . $code . "' , '" . $filename . "' , '" . $nom . "')");
        // Action
        $texteaction = "". _ACTIONADDSMILEY .": ".$nom."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _SMILEYSUCCES . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Admin&page=smilies", 2);
    }

    function edit_smiley($smiley_id)
    {
        global $nuked, $language;

        $sql = mysql_query("SELECT code, url, name FROM " . SMILIES_TABLE . " WHERE id = '" . $smiley_id . "'");
        list($code, $url, $name) = mysql_fetch_array($sql);

        echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function update_smiley(newimage)\n"
        . "{\n"
        . "document.getElementById('smiley').src = 'images/icones/' + newimage;\n"
        . "}\n"
            . "\n"
        . "// -->\n"
        . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _SMILIEADMIN . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/smilies.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=smilies&amp;op=modif_smiley\" enctype=\"multipart/form-data\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n"
        . "<tr><td><b>" . _NAME . " :</b> <input type=\"text\" name=\"nom\" size=\"30\" value=\"" . $name . "\" /></td></tr>\n"
        . "<tr><td><b>" . _CODE . " :</b> <input type=\"text\" name=\"code\" size=\"10\" value=\"" . $code . "\" /></td></tr><tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _SMILEY . " :</b> <select name=\"url\" onchange=\"update_smiley(this.options[selectedIndex].value);\">";

        $rep = Array();
        $path = "images/icones";
        $handle = opendir($path);
        while (false !== ($ikon = readdir($handle)))
        {
            if ($ikon != "." && $ikon != ".." && $ikon != "index.html" && $ikon != "Thumbs.db")
            {
                $rep[] = $ikon;
            }
        }

        closedir($handle);
        sort ($rep);
        reset ($rep);

        while (list ($key, $filename) = each ($rep))
        {
            if ($url == $filename)
            {
                $checked = "selected=\"selected\"";
            }
            else
            {
                $checked = "";
            }

            echo "<option value=\"" . $filename . "\" " . $checked . ">" . $filename . "</option>\n";
        }

        echo "</select>&nbsp;&nbsp;<img id=\"smiley\" src=\"images/icones/" . $url . "\" alt=\"\" /></td></tr><tr><td><b>" . _UPSMILEY . " : </b><input type=\"file\" name=\"fichiernom\" /></td></tr>\n"
        . "<tr><td>&nbsp;<input type=\"hidden\" name=\"smiley_id\" value=\"" . $smiley_id . "\" /></td></tr>\n"
        . "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _SEND . "\" /></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin&amp;page=smilies\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function modif_smiley($smiley_id, $nom, $code, $url, $fichiernom)
    {
        global $nuked, $user;

        $nom = mysql_real_escape_string(stripslashes($nom));
        $filename = $_FILES['fichiernom']['name'];
    
        if (($nom == $code) || (strpos($code,'"')!==false) || (strpos($code,"'")!==false))
        {
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . _SMILEYNOTAUTHORIZE . ".\n"
            . "</div>\n"
            . "</div>\n";
            redirect("index.php?file=Admin&page=smilies&op=edit_smiley&smiley_id=" . $smiley_id, 2);
            adminfoot();
            footer();
            exit();
        }

        if ($filename != "")
        {
            $ext = strrchr($filename, ".");
            $ext = substr($filename, 1);

            if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext) || preg_match("`gif`i", $ext) || preg_match("`png`i", $ext)))
            {
                $url_image = "images/icones/" . $filename;
                move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_image) or die ("<br /><br /><div style=\"text-align: center;\"><b>Upload file failed !!!</b></div><br /><br />");
                @chmod ($url_image, 0644);
            }
            else
            {
                echo "<div class=\"notification error png_bg\">\n"
                . "<div>\n"
                . "No image file !"
                . "</div>\n"
                . "</div>\n";
                redirect("index.php?file=Admin&page=smilies&op=edit_smiley&smiley_id=" . $smiley_id, 2);
                adminfoot();
                footer();
                exit();
            }
        }
        else
        {
            $filename = $url;
        }

        $sql = mysql_query("UPDATE " . SMILIES_TABLE . " SET code = '" . $code . "', url = '" . $filename . "', name = '" . $nom . "' WHERE id = '" . $smiley_id . "'");
       // Action
        $texteaction = "". _ACTIONMODIFSMILEY .": ".$nom."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _SMILEYMODIF . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Admin&page=smilies", 2);
    }

    function del_smiley($smiley_id)
    {
        global $nuked,$user;

        $sql2 = mysql_query("SELECT name FROM " . SMILIES_TABLE . " WHERE id = '" . $smiley_id . "'");
        list($name) = mysql_fetch_array($sql2);
        $sql = mysql_query("DELETE FROM " . SMILIES_TABLE . " WHERE id = '" . $smiley_id . "'");
        // Action
        $texteaction = "". _ACTIONDELSMILEY .": ".$name."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _SMILEYDELETE . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Admin&page=smilies", 2);
    }

    switch ($_REQUEST['op'])
    {
        case "add_smiley":
            add_smiley();
            break;

        case "send_smiley":
            send_smiley($_REQUEST['nom'], $_REQUEST['code'], $_REQUEST['url'], $_REQUEST['fichiernom']);
            break;

        case "edit_smiley":
            edit_smiley($_REQUEST['smiley_id']);
            break;

        case "modif_smiley":
            modif_smiley($_REQUEST['smiley_id'], $_REQUEST['nom'], $_REQUEST['code'], $_REQUEST['url'], $_REQUEST['fichiernom']);
            break;

        case "del_smiley":
            del_smiley($_REQUEST['smiley_id']);
            break;

        case "main":
            main();
            break;

        default:
            main();
            break;
    }

}
else if ($visiteur > 1)
{
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
}
else
{
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
}
adminfoot();

?>
