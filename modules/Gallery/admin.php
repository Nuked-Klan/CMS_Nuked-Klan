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
translate("modules/Gallery/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
admintop();

if (!$user)
{
    $visiteur = 0;
} 
else
{
    $visiteur = $user[1];
} 
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1)
{
    function add_screen()
    {
        global $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Gallery&amp;page=admin\">" . _GALLERY . "</a> | "
        . "</b>" . _ADDSCREEN . "<b> | "
        . "<a href=\"index.php?file=Gallery&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
        . "<a href=\"index.php?file=Gallery&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
        . "<form method=\"post\" action=\"index.php?file=Gallery&amp;page=admin&amp;op=send_screen\" enctype=\"multipart/form-data\" onsubmit=\"backslash('img_texte');\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"	
        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"44\" /></td></tr>\n"
        . "<tr><td><b>" . _CAT . "</b>: <select name=\"cat\">\n";

        select_cat();

        echo "</select></td></tr><tr><td><b>" . _AUTHOR . " :</b> <input type=\"text\" name=\"auteur\" size=\"30\" /></td></tr>\n";

        echo "</td></tr><tr><td><b>" . _DESCR . " :</b></td></tr>\n"
        . "<tr><td><textarea class=\"editor\" id=\"img_texte\" name=\"description\" cols=\"66\" rows=\"10\"></textarea></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _URLIMG . " :</b> <input type=\"text\" name=\"url\" size=\"50\" maxlength=\"200\" value=\"http://\" /></td></tr>\n"
        . "<tr><td><b>" . _UPIMG . " :</b><br /><input type=\"file\" name=\"fichiernom\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" />&nbsp;" . _REPLACE . "</td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _URLIMG2 . " :</b> <input type=\"text\" name=\"url2\" size=\"46\" maxlength=\"200\" value=\"http://\" /></td></tr>\n"
        . "<tr><td><b>" . _URLFILE . " :</b> <input type=\"text\" name=\"url_file\" size=\"51\" maxlength=\"200\" value=\"http://\" /></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . _ADDSCREEN . "\" /></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function send_screen($titre, $description, $auteur, $fichiernom, $maxi, $cat, $url, $url2, $url_file, $ecrase_screen)
    {
        global $nuked, $user;
        include("modules/Gallery/config.php");

        if ($url == "http://") $url = "";
        if ($url2 == "http://") $url2 = "";
        if ($url_file == "http://") $url_file = ""; 

        if ($_FILES['fichiernom']['name'] != "" || $url != "")
        {        
            if ($_FILES['fichiernom']['name'] != "")
            {
                $filename = $_FILES['fichiernom']['name'];
                $filename = str_replace(" ", "_", $filename);
                $url_screen = $rep_img . $filename;
                $url = $url_screen;
            }
            else
            {
                $filename = substr(strrchr($url, '/'), 1 );
            }
                                
                
            if (($_FILES['fichiernom']['name'] == "" && $url != "") || (!is_file($url_screen) || ( $ecrase_screen == 1 && is_file($url_screen))))
            {
                if ($_FILES['fichiernom']['name'] != "" && (!is_file($url_screen) || ( $ecrase_screen == 1 && is_file($url_screen))))
                {
                    $ext = pathinfo($filename, PATHINFO_EXTENSION);

                    if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG")
                    {
                        move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_screen) or die ("Upload file failed !!!");
                        @chmod ($url_screen, 0644);
                    }
                    else
                    {
                     echo "<div class=\"notification error png_bg\">\n"
                    . "<div>\n"
                    . "No image file !"
                    . "</div>\n"
                    . "</div>\n";
                    
                    redirect("index.php?file=Gallery&page=admin&op=add_screen", 2);
                    adminfoot();
                    exit();
                    }
                }

                if ($url2 == "" && $image_gd == "on" && @extension_loaded('gd') && !preg_match("`http://`i", $url) && is_file($url))
                {
                    $size = @getimagesize($url);

                    if ($size && $size[0] > $img_screen1)
                    {
                        $f = explode(".", $filename);
                        $end = count($f) - 1;
                        $ext = $f[$end];
                        $file_name = str_ireplace("." . $ext, "", $filename);

                        if (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext)) $src = @imagecreatefromjpeg($url);
                        if (preg_match("`png`i", $ext)) $src = @imagecreatefrompng($url);
                        if (preg_match("`gif`i", $ext)) $src = @imagecreatefromgif($url);
                        if (preg_match("`bmp`i", $ext)) $src = @imagecreatefromwbmp($url);

                        $img = @imagecreatetruecolor($img_screen1, round(($img_screen1/$size[0])*$size[1]));
                        if (!$img) $img = @imagecreate($img_screen1, round(($img_screen1/$size[0])*$size[1]));

                        @imagecopyresampled($img, $src, 0, 0, 0, 0, $img_screen1, round($size[1]*($img_screen1/$size[0])), $size[0], $size[1]);

                        $temp = $rep_img_gd . $file_name . "_tmb." . $ext;
                        if (is_file($temp)) $miniature = $rep_img_gd . time() . $file_name . "_tmb." . $ext;
                        else  $miniature = $temp;

                        if (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext)) @ImageJPEG($img, $miniature);
                        if (preg_match("`png`i", $ext)) @ImagePNG($img, $miniature);
                        if (preg_match("`bmp`i", $ext)) @imagewbmp($img, $miniature);

                        if (preg_match("`gif`i", $ext) && @function_exists("imagegif")) @ImageGIF($img, $miniature);
                        else @ImageJPEG($img, $miniature);

                        if (is_file($miniature)) $url2 = $miniature;
                    }
                }
                        
                $titre = mysql_real_escape_string(stripslashes($titre));
                $description = html_entity_decode($description);
                $description = mysql_real_escape_string(stripslashes($description));
                $auteur = mysql_real_escape_string(stripslashes($auteur));
                $date = time();

                $sql = mysql_query("INSERT INTO " . GALLERY_TABLE . " ( `sid` , `titre` , `description` , `url` , `url2` , `url_file` , `cat` , `date` , `autor` ) VALUES ( '' , '" . $titre . "' , '" . $description . "' , '" . $url . "' , '" . $url2 . "' , '" . $url_file . "' , '" . $cat . "' , '" . $date . "' , '" . $auteur . "')");
                
                // Action
                $texteaction = "". _ACTIONADDGAL .": ".$titre."";
                $acdate = time();
                $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
                // Fin action
                echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _SCREENADD . "\n"
                . "</div>\n"
                . "</div>\n";
                $sqls = mysql_query("SELECT sid FROM " . GALLERY_TABLE . " WHERE date = '" . $date . "' AND titre='" . $titre . "'");
                list($sid) = mysql_fetch_array($sqls);
                echo "<script>\n"
                ."setTimeout('screen()','3000');\n"
                ."function screen() { \n"
                ."screenon('index.php?file=Gallery&op=description&sid=".$sid."&orderby=news', 'index.php?file=Gallery&page=admin');\n"
                ."}\n"
                ."</script>\n";
            }
            else 
            {
                echo "<div class=\"notification error png_bg\">\n"
                . "<div>\n"
                . "" . _DEJASCREEN . "<br />" . _REPLACEIT . "<br /><a href=\"javascript:history.back();\"><b>" . _BACK . "</b></a>"
                . "</div>\n"
                . "</div>\n";
            }
        }
        else
        {
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . ""._SPECIFY.""
            . "</div>\n"
            . "</div>\n";
            
            redirect("index.php?file=Gallery&page=admin&op=add_screen", 3);
        }

    } 

    function del_screen($sid)
    {
        global $nuked, $user;

        $sqls = mysql_query("SELECT titre FROM " . GALLERY_TABLE . " WHERE sid = '" . $sid . "'");
        list($titre) = mysql_fetch_array($sqls);
        $titre = mysql_real_escape_string($titre);
        $sql = mysql_query("DELETE FROM " . GALLERY_TABLE . " WHERE sid = '" . $sid . "'");
        $del_com = mysql_query("DELETE FROM " . COMMENT_TABLE . " WHERE im_id = '" . $sid . "' AND module = 'Gallery'");
        $del_vote = mysql_query("DELETE FROM " . VOTE_TABLE . " WHERE vid = '" . $sid . "' AND module = 'Gallery'");
        // Action
        $texteaction = "". _ACTIONDELGAL .": ".$titre."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _SCREENDEL . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Gallery&page=admin", 1);
    } 


    function modif_img($sid, $titre, $description, $auteur, $fichiernom, $maxi, $cat, $url, $url2, $url_file, $ecrase_screen)
    {
        global $nuked, $user;

        include("modules/Gallery/config.php");

        $titre = mysql_real_escape_string(stripslashes($titre));
        $description = html_entity_decode($description);
        $description = mysql_real_escape_string(stripslashes($description));
        $auteur = mysql_real_escape_string(stripslashes($auteur));

        if ($_FILES['fichiernom']['name'] != "")
        {     
        $img_name = $_FILES['fichiernom']['name'];
        $img_name = str_replace(" ", "_", $img_name);
        $img_url = $rep_img . $img_name;

        if (!is_file($img_url) || $ecrase_screen == 1)
        {
            $ext = pathinfo($img_name, PATHINFO_EXTENSION);

            if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG")
            {
                move_uploaded_file($_FILES['fichiernom']['tmp_name'], $img_url) or die ("Upload file failed !!!");
                @chmod ($img_url, 0644);
            }
            else
            {
                echo "<br /><br /><div style=\"text-align: center;\">No image file !!!</div><br /><br />";
                redirect("index.php?file=Gallery&page=admin&op=edit_screen&sid=" . $sid, 2);
                adminfoot();
                exit();
            }
        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _DEJASCREEN . "<br />" . _REPLACEIT . "<br /><br /><a href=\"javascript:history.back();\"><b>" . _BACK . "</b></a></div><br /><br />";
            adminfoot();
            exit();
        }
    }
    else
    {
        $img_url = $url;
        $img_name = substr(strrchr($img_url, '/'), 1 );
    }

    if ($url2 == "" && $image_gd == "on" && @extension_loaded('gd') && !preg_match("`http://`i", $img_url) && is_file($img_url))
    {
        $size = @getimagesize($img_url);

        if ($size[0] > $img_screen1)
        {
            $f = explode(".", $img_name);
            $end = count($f) - 1;
            $ext = $f[$end];
            $file_name = preg_replace("." . $ext, "", $img_name);

            if (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext)) $src = @imagecreatefromjpeg($img_url);
            if (preg_match("`png`i", $ext)) $src = @imagecreatefrompng($img_url);
            if (preg_match("`gif`i", $ext)) $src = @imagecreatefromgif($img_url);
            if (preg_match("`bmp`i", $ext)) $src = @imagecreatefromwbmp($img_url);

            $img = @imagecreatetruecolor($img_screen1, round(($img_screen1/$size[0])*$size[1]));
            if (!$img) $img = @imagecreate($img_screen1, round(($img_screen1/$size[0])*$size[1]));

            @imagecopyresampled($img, $src, 0, 0, 0, 0, $img_screen1, round($size[1]*($img_screen1/$size[0])), $size[0], $size[1]);

            $temp = $rep_img_gd . $file_name . "_tmb." . $ext;
            if (is_file($temp)) $miniature = $rep_img_gd . time() . $file_name . "_tmb." . $ext;
            else  $miniature = $temp;	

            if (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext)) @ImageJPEG($img, $miniature);
            if (preg_match("`png`i", $ext)) @ImagePNG($img, $miniature);
            if (preg_match("`bmp`i", $ext)) @imagewbmp($img, $miniature);

            if (preg_match("`gif`i", $ext) && @function_exists("imagegif")) @ImageGIF($img, $miniature);
            else @ImageJPEG($img, $miniature);	
     
            if (is_file($miniature)) $url2 = $miniature;
        }
    }

        $sql = mysql_query("UPDATE " . GALLERY_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "', autor = '" . $auteur . "', url = '" . $img_url . "', url2 = '" . $url2 . "', url_file = '" . $url_file . "', cat = '" . $cat . "' WHERE sid = '" . $sid . "'");
        // Action
        $texteaction = "". _ACTIONMODIFGAL .": ".$titre."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _SCREENMODIF . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script>\n"
        ."setTimeout('screen()','3000');\n"
        ."function screen() { \n"
        ."screenon('index.php?file=Gallery&op=description&sid=".$sid."&orderby=news', 'index.php?file=Gallery&page=admin');\n"
        ."}\n"
        ."</script>\n";
    } 



    function main()
    {
        global $nuked, $language;

        $nb_img_guest = 30;

        $sql3 = mysql_query("SELECT sid FROM " . GALLERY_TABLE);
        $count = mysql_num_rows($sql3);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_img_guest - $nb_img_guest;

        echo"<script type=\"text/javascript\">\n"
        ."<!--\n"
        ."\n"
        . "function del_img(titre, id)\n"
        . "{\n"
        . "if (confirm('" . _SCREENDELETE . " '+titre+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=Gallery&page=admin&op=del_screen&sid='+id;}\n"
        . "}\n"
            . "\n"
        . "// -->\n"
        . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _GALLERY . "<b> | "
        . "<a href=\"index.php?file=Gallery&amp;page=admin&amp;op=add_screen\">" . _ADDSCREEN . "</a> | "
        . "<a href=\"index.php?file=Gallery&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
        . "<a href=\"index.php?file=Gallery&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n";

        if ($_REQUEST['orderby'] == "date")
        {
            $order_by = "G.sid DESC";
        } 
        else if ($_REQUEST['orderby'] == "name")
        {
            $order_by = "G.titre";
        } 
        else if ($_REQUEST['orderby'] == "cat")
        {
            $order_by = "GC.titre, GC.parentid";
        } 
        else
        {
            $order_by = "G.sid DESC";
        } 

        echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">\n"
    . "<tr><td align=\"right\">" . _ORDERBY . " : ";

        if ($_REQUEST['orderby'] == "date" || !$_REQUEST['orderby'])
        {
            echo "<b>" . _DATE . "</b> | ";
        } 
        else
        {
            echo "<a href=\"index.php?file=Gallery&amp;page=admin&amp;orderby=date\">" . _DATE . "</a> | ";
        } 

        if ($_REQUEST['orderby'] == "name")
        {
            echo "<b>" . _TITLE . "</b> | ";
        } 
        else
        {
            echo "<a href=\"index.php?file=Gallery&amp;page=admin&amp;orderby=name\">" . _TITLE . "</a> | ";
        } 

        if ($_REQUEST['orderby'] == "cat")
        {
            echo "<b>" . _CAT . "</b>";
        } 
        else
        {
            echo "<a href=\"index.php?file=Gallery&amp;page=admin&amp;orderby=cat\">" . _CAT . "</a>";
        } 

        echo "&nbsp;</td></tr></table>\n";

        if ($count > $nb_img_guest)
        {
            echo "<div>";
            $url_page = "index.php?file=Gallery&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
            number($count, $nb_img_guest, $url_page);
            echo "</div>\n";
        } 

        echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
        . "<td style=\"width: 30%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT G.sid, G.titre, G.cat, G.url, G.date, GC.parentid, GC.titre FROM " . GALLERY_TABLE . " AS G LEFT JOIN " . GALLERY_CAT_TABLE . " AS GC ON GC.cid = G.cat ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_img_guest."");
        while (list($sid, $titre, $cat, $url, $date, $parentid, $namecat) = mysql_fetch_array($sql))
        {

            $titre = printSecuTags($titre);
            $date = nkDate($date);

            if ($cat == "0")
            {
                $categorie = _NONE;
            } 
            else if ($parentid == 0)
            {
                $categorie = $namecat;
            } 
            else
            {
                $sql3 = mysql_query("SELECT titre FROM " . GALLERY_CAT_TABLE . " WHERE cid = '" . $parentid . "' ORDER BY position, titre");
                list($parentcat) = mysql_fetch_array($sql3);
                $categorie = "$parentcat -> $namecat";
                $categorie = printSecuTags($categorie);
            } 

            echo "<tr style=\"background: " . $bg . ";\">\n"
            . "<td style=\"width: 20%;\"><a href=\"" . $url . "\" title=\"" . $url . "\" onclick=\"window.open(this.href); return false;\">" . $titre . "</a></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td>\n"
            . "<td style=\"width: 30%;\" align=\"center\">" . $categorie . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Gallery&amp;page=admin&amp;op=edit_screen&amp;sid=" . $sid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISSCREEN . "\" /></a></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_img('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $sid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISSCREEN . "\" /></a></td></tr>\n";
        } 

        if ($count == 0) echo "<tr><td colspan=\"5\" align=\"center\">" . _NOSCREENINDB . "</td></tr>\n";

        echo "</table>\n";

        if ($count > $nb_img_guest)
        {
            echo "<div>";
            $url_page = "index.php?file=Gallery&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
            number($count, $nb_img_guest, $url_page);
            echo "</div>\n";
        } 

        echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function edit_screen($sid)
    {
        global $nuked, $language;

        include("modules/Gallery/config.php");

        $sql = mysql_query("SELECT cat, titre, description, autor, url, url2, url_file FROM " . GALLERY_TABLE . " WHERE sid = '" . $sid . "'");
        list($cat, $titre, $description, $autor, $url, $url2, $url_file) = mysql_fetch_array($sql);

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
        $image = "<img style=\"border: 1px solid #000000;\" src=\"" . $img . "\" " . $width . " alt=\"\" title=\"" .  _CLICTOSCREEN . "\" />";

        $name = strrchr($img, '/');
        $name = substr($name, 1);
        $name_enc = rawurlencode($name);
        $img = str_replace($name, $name_enc, $img);
    
        if ($cat > 0)
        {
            $sql2 = mysql_query("SELECT titre FROM " . GALLERY_CAT_TABLE . " WHERE cid = '" . $cat . "'");
            list($cat_name) = mysql_fetch_array($sql2);
            $cat_name = printSecuTags($cat_name);
        }
        else
        {
            $cat_name = _NONE;
        }

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Gallery&amp;page=admin&amp;op=modif_img\" enctype=\"multipart/form-data\" onsubmit=\"backslash('img_texte');\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellpadding=\"10\" cellspacing=\"0\" border=\"0\">\n"
        . "<tr><td>\n"
        . "<a href=\"#\" onclick=\"javascript:window.open('" . $url . "','Gallery','toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600,top=30,left=0')\">" . $image . "</a></td></tr></table><br />\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"	
        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"44\" value=\"" . $titre . "\" /></td></tr>\n"
        . "<tr><td><b>" . _CAT . "</b>: <select name=\"cat\"><option value=\"" . $cat . "\">" . $cat_name . "</option>\n";

        select_cat();

        echo "</select></td></tr><tr><td><b>" . _AUTHOR . " :</b> <input type=\"text\" name=\"auteur\" size=\"30\" value=\"" . $autor . "\" /></td></tr>\n";

        echo "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
        . "<tr><td><textarea class=\"editor\" id=\"img_texte\" name=\"description\" cols=\"66\" rows=\"10\" onselect=\"storeCaret('img_texte');\" onclick=\"storeCaret('img_texte');\" onkeyup=\"storeCaret('img_texte');\">" . $description . "</textarea></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _URLIMG . " :</b> <input type=\"text\" name=\"url\" size=\"50\" maxlength=\"200\" value=\"" . $url . "\" /></td></tr>\n"
        . "<tr><td><b>" . _UPIMG . " :</b><br /><input type=\"file\" name=\"fichiernom\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" />&nbsp;" . _REPLACE . "</td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _URLIMG2 . " :</b> <input type=\"text\" name=\"url2\" size=\"46\" maxlength=\"200\" value=\"" . $url2 . "\" /></td></tr>\n"
        . "<tr><td><b>" . _URLFILE . " :</b> <input type=\"text\" name=\"url_file\" size=\"51\" maxlength=\"200\" value=\"" . $url_file . "\" /></td></tr>\n"
        . "<tr><td>&nbsp;<input type=\"hidden\" name=\"sid\" value=\"" . $sid . "\" /></td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . _MODIFTHISSCREEN . "\" /></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function main_cat()
    {
        global $nuked, $language;

        echo"<script type=\"text/javascript\">\n"
        ."<!--\n"
        ."\n"
        . "function delcat(titre, id)\n"
        . "{\n"
        . "if (confirm('" . _SCREENDELETE  . " '+titre+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=Gallery&page=admin&op=del_cat&cid='+id;}\n"
        . "}\n"
            . "\n"
        . "// -->\n"
        . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Gallery&amp;page=admin\">" . _GALLERY . "</a> | "
        . "<a href=\"index.php?file=Gallery&amp;page=admin&amp;op=add_screen\">" . _ADDSCREEN . "</a> | "
        . "</b>" . _CATMANAGEMENT . "<b> | "
        . "<a href=\"index.php?file=Gallery&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
        . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CATPARENT . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT cid, titre, parentid, position FROM " . GALLERY_CAT_TABLE . " ORDER BY parentid, position");
        $nbcat = mysql_num_rows($sql);

        if ($nbcat > 0)
        {
            while (list($cid, $titre, $parentid, $position) = mysql_fetch_array($sql))
            {
                $titre = printSecuTags($titre);

                echo "<tr>\n"
                . "<td style=\"width: 35%;\" align=\"center\">" . $titre . "</td>\n"
                . "<td style=\"width: 35%;\" align=\"center\">\n";

                if ($parentid > 0)
                {
                    $sql2 = mysql_query("SELECT titre FROM " . GALLERY_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
                    list($pnomcat) = mysql_fetch_array($sql2);
                    $pnomcat = printSecuTags($pnomcat);

                    echo "<i>" . $pnomcat . "</i>";
                } 
                else
                {
                    echo _NONE;
                } 

                echo "</td><td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Gallery&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=down\" title=\"" . _MOVEDOWN . "\">&lt;</a>"
                . "&nbsp;" . $position . "&nbsp;<a href=\"index.php?file=Gallery&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=up\" title=\"" . _MOVEUP . "\">&gt;</a></td>\n"
                . "<td align=\"center\"><a href=\"index.php?file=Gallery&amp;page=admin&amp;op=edit_cat&amp;cid=" . $cid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
                . "<td align=\"center\"><a href=\"javascript:delcat('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
            } 
        } 
        else
        { 
            echo "<tr><td align=\"center\" colspan=\"5\">" . _NONE . "&nbsp;" . _CAT . "&nbsp;" . _INDATABASE . "</td></tr>\n";
        }

        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery&amp;page=admin&amp;op=add_cat\"><b>" . _ADDCAT . "</b></a> ]</div>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function add_cat()
    {
        global $language, $nuked;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Gallery&amp;page=admin&amp;op=send_cat\">\n"
        . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
        . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\"><option value=\"0\">" . _NONE . "</option>\n";

        $sql = mysql_query("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
        while (list($cid, $nomcat) = mysql_fetch_array($sql))
        {
            $nomcat = printSecuTags($nomcat);

            echo "<option value=\"" . $cid . "\">" . $nomcat . "</option>\n";
        } 

        echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"0\" /></td></tr>\n"
        . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
        . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _CREATECAT . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function send_cat($titre, $description, $parentid, $position)
    {
        global $nuked, $user;

        $titre = mysql_real_escape_string(stripslashes($titre));
        
        if (empty($titre))
        {
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "" . _TITLECATFORGOT . "\n"
            . "</div>\n"
            . "</div>\n";
            redirect("index.php?file=Gallery&page=admin&op=main_cat", 4);
        }
        else
        {
            $description = html_entity_decode($description);
            $description = mysql_real_escape_string(stripslashes($description));
        
            $sql = mysql_query("INSERT INTO " . GALLERY_CAT_TABLE . " ( `parentid` , `titre` , `description` , `position` ) VALUES ('" . $parentid . "', '" . $titre . "', '" . $description . "', '" . $position . "')");
            // Action
            $texteaction = "". _ACTIONADDCATGAL .": ".$titre."";
            $acdate = time();
            $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
            //Fin action
            echo "<div class=\"notification success png_bg\">\n"
            . "<div>\n"
            . "" . _CATADD . "\n"
            . "</div>\n"
            . "</div>\n";
            $sqlq = mysql_query("SELECT cid FROM " . GALLERY_CAT_TABLE . " WHERE parentid='".$parentid."' AND titre='".$titre."'");
            list($cid) = mysql_fetch_array($sqlq);
            echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php?file=Gallery&op=categorie&cat=".$cid."', 'index.php?file=Gallery&page=admin&op=main_cat');\n"
            ."}\n"
            ."</script>\n";
        }
    } 

    function edit_cat($cid)
    {
        global $nuked, $language;

        $sql = mysql_query("SELECT titre, description, parentid, position FROM " . GALLERY_CAT_TABLE . " WHERE cid='".$cid."'");
        list($titre, $description, $parentid, $position) = mysql_fetch_array($sql);

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Gallery&amp;page=admin&amp;op=modif_cat\">\n"
        . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre . "\" /></td></tr>\n"
        . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\">\n";

        if ($parentid > 0)
        {
            $sql2 = mysql_query("SELECT titre FROM " . GALLERY_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
            list($pnomcat) = mysql_fetch_array($sql2);
            $pnomcat = printSecuTags($pnomcat);

            echo "<option value=\"" . $parentid . "\">" . $pnomcat . "</option>\n";
        } 

        echo "<option value=\"0\">" . _NONE . "</option>\n";

        $sql3 = mysql_query("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
        while (list($catid, $nomcat) = mysql_fetch_array($sql3))
        {
            $nomcat = printSecuTags($nomcat);

            if ($nomcat != $titre)
            {
                echo "<option value=\"" . $catid . "\">" . $nomcat . "</option>\n";
            } 
        } 

        echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"" . $position . "\" /></td></tr>\n"
        . "<tr><td><b>" . _DESCR . " :</b><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /></td></tr>\n"
        . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\">" . $description . "</textarea></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _MODIFTHISCAT . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function modif_cat($cid, $titre, $description, $parentid, $position)
    {
        global $nuked, $user;

        $titre = mysql_real_escape_string(stripslashes($titre));
        
        if (empty($titre))
        {
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "" . _TITLEARTFORGOT . "\n"
            . "</div>\n"
            . "</div>\n";
            redirect("index.php?file=Gallery&page=admin&op=main_cat", 4);
        }
        else
        {
            $description = html_entity_decode($description);
            $description = mysql_real_escape_string(stripslashes($description));
        
            $sql = mysql_query("UPDATE " . GALLERY_CAT_TABLE . " SET parentid = '" . $parentid . "', titre = '" . $titre . "', description = '" . $description . "', position = '" . $position . "' WHERE cid = '" . $cid . "'");
            // Action
            $texteaction = "". _ACTIONMODIFCATGAL .": ".$titre."";
            $acdate = time();
            $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
            //Fin action
            echo "<div class=\"notification success png_bg\">\n"
            . "<div>\n"
            . "" . _CATMODIF . "\n"
            . "</div>\n"
            . "</div>\n";
            echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php?file=Gallery&op=categorie&cat=".$cid."', 'index.php?file=Gallery&page=admin&op=main_cat');\n"
            ."}\n"
            ."</script>\n";
        }
    } 

    function select_cat()
    {
        global $nuked;

        $sql = mysql_query("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
        while (list($cid, $titre) = mysql_fetch_array($sql))
        {
            $titre = printSecuTags($titre);

            echo "<option value=\"" . $cid . "\">* " . $titre . "</option>\n";

            $sql2 = mysql_query("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre");
            while (list($s_cid, $s_titre) = mysql_fetch_array($sql2))
            {
                $s_titre = printSecuTags($s_titre);

                echo"<option value=\"" . $s_cid . "\">&nbsp;&nbsp;&nbsp;" . $s_titre . "</option>\n";
            } 
        } 
        echo "<option value=\"0\">* " . _NONE . "</option>\n";
    } 

    function del_cat($cid)
    {
        global $nuked, $user;

        $sqlq = mysql_query("SELECT titre FROM " . GALLERY_CAT_TABLE . " WHERE cid='".$cid."'");
        list($titre) = mysql_fetch_array($sqlq);
        $titre = mysql_real_escape_string($titre);
        $sql = mysql_query("DELETE FROM " . GALLERY_CAT_TABLE . " WHERE cid = '" . $cid . "'");
        $sql = mysql_query("UPDATE " . GALLERY_CAT_TABLE . " SET parentid = 0 WHERE parentid = '" . $cid . "'");
        $sql = mysql_query("UPDATE " . GALLERY_TABLE . " SET cat = 0 WHERE cat = '" . $cid . "'");
        // Action
        $texteaction = "". _ACTIONDELCATGAL .": ".$titre."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _CATDEL . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Gallery&page=admin&op=main_cat", 2);
    } 

    function main_pref()
    {
        global $nuked, $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINGALLERY . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Gallery&amp;page=admin\">" . _GALLERY . "</a> | "
        . "<a href=\"index.php?file=Gallery&amp;page=admin&amp;op=add_screen\">" . _ADDSCREEN . "</a> | "
        . "<a href=\"index.php?file=Gallery&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
        . "</b>" . _PREFS . "</div><br />\n"
        . "<form method=\"post\" action=\"index.php?file=Gallery&amp;page=admin&amp;op=change_pref\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td align=\"center\" colspan=\"2\"><big>" . _PREFS . "</big></td></tr>\n"
        . "<tr><td>" . _GALLERYTITLE . " : </td><td> <input type=\"text\" name=\"gallery_title\" size=\"40\" value=\"" . $nuked['gallery_title']. "\" /></td></tr>\n"
        . "<tr><td>" . _NUMBERIMG . " : </td><td><input type=\"text\" name=\"max_img\" size=\"2\" value=\"" . $nuked['max_img'] . "\" /></td></tr>\n"
        . "<tr><td>" . _NUMBERIMG2 . " : </td><td><input type=\"text\" name=\"max_img_line\" size=\"2\" value=\"" . $nuked['max_img_line'] . "\" /></td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Gallery&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function change_pref($gallery_title, $max_img, $max_img_line)
    {
        global $nuked, $user;

        $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $gallery_title . "' WHERE name = 'gallery_title'");
        $upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_img . "' WHERE name = 'max_img'");
        $upd3 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_img_line . "' WHERE name = 'max_img_line'");
        // Action
        $texteaction = "". _ACTIONPREFGAL .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _PREFUPDATED . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Gallery&page=admin", 2);
    } 

    function modif_position($cid, $method)
    {
        global $nuked, $user;

        $sqlq = mysql_query("SELECT titre, position FROM " . GALLERY_CAT_TABLE . " WHERE cid='".$cid."'");
        list($titre, $position) = mysql_fetch_array($sqlq);
        if ($position <=0 AND $method == "up")
        {
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "" . _CATERRORPOS . "\n"
            . "</div>\n"
            . "</div>\n";
            redirect("index.php?file=Gallery&page=admin&op=main_cat", 2);
            exit();
        }
        if ($method == "up") $upd = mysql_query("UPDATE " . GALLERY_CAT_TABLE . " SET position = position - 1 WHERE cid = '" . $cid . "'");
        else if ($method == "down") $upd = mysql_query("UPDATE " . GALLERY_CAT_TABLE . " SET position = position + 1 WHERE cid = '" . $cid . "'");
        // Action
        $texteaction = "". _ACTIONPOSCATGAL .": ".$titre."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _CATMODIF . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Gallery&page=admin&op=main_cat", 2);
    } 

    switch ($_REQUEST['op'])
    {
        case "add_screen":
            add_screen();
            break;

        case "del_screen":
            del_screen($_REQUEST['sid']);
            break;

        case "send_screen":
            send_screen($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['auteur'], $_REQUEST['fichiernom'], $_REQUEST['maxi'], $_REQUEST['cat'], $_REQUEST['url'], $_REQUEST['url2'], $_REQUEST['url_file'], $_REQUEST['ecrase_screen']);
            break;

        case "edit_screen":
            edit_screen($_REQUEST['sid']);
            break;

        case "modif_img":
            modif_img($_REQUEST['sid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['auteur'], $_REQUEST['fichiernom'], $_REQUEST['maxi'], $_REQUEST['cat'], $_REQUEST['url'], $_REQUEST['url2'], $_REQUEST['url_file'], $_REQUEST['ecrase_screen']);
            break;

        case "send_cat":
            send_cat($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['parentid'], $_REQUEST['position']);
            break;

        case "add_cat":
            add_cat();
            break;

        case "main_cat":
            main_cat();
            break;

        case "edit_cat":
            edit_cat($_REQUEST['cid']);
            break;

        case "modif_cat":
            modif_cat($_REQUEST['cid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['parentid'], $_REQUEST['position']);
            break;

        case "del_cat":
            del_cat($_REQUEST['cid']);
            break;

        case "main_pref":
            main_pref();
            break;

        case "change_pref":
            change_pref($_REQUEST['gallery_title'], $_REQUEST['max_img'], $_REQUEST['max_img_line']);
            break;

        case "modif_position":
            modif_position($_REQUEST['cid'], $_REQUEST['method']);
            break;

        default:
            main();
            break;
    } 

} 
else if ($level_admin == -1)
{
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
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
