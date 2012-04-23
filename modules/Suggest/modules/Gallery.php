<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - Portal PHP                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")){
    exit('You can\'t run this file alone.');
}

global $nuked, $user, $language;
translate("modules/Gallery/lang/" . $language . ".lang.php");

function form($content, $sug_id){
    global $nuked, $user, $captcha;

    include("modules/Suggest/config.php");

    if ($content != ""){
        $titre = "<big><b>" . _VALIDIMG . "</b></big>";
        $action = "index.php?file=Suggest&amp;page=admin&op=valid_suggest&amp;module=Gallery";
        $autor = $content[6];

        echo "<script type=\"text/javascript\">\n"
                . "<!--\n"
                . "\n"
                . "function del_sug(id)\n"
                . "{\n"
                . "if (confirm('" . _DELETESUG . " '+id+' ! " . _CONFIRM . "'))\n"
                . "{document.location.href = 'index.php?file=Suggest&page=admin&op=raison&sug_id='+id;}\n"
                . "}\n"
                . "\n"
                . "// -->\n"
                . "</script>\n";

        $refuse = "&nbsp;<input type=\"button\" value=\"" . _REMOVE . "\" onclick=\"javascript:del_sug('" . $sug_id . "');\" /></div>\n"
                     . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Suggest&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br />\n";

    }
    else{
        if ($nuked['gallery_title'] != ""){
            $title = $nuked['gallery_title'];
        }
        else {
            $title = _GALLERY;
        }

        $titre = "<big><b> " . $title . " </b></big></div>\n"
                    . "<div style=\"text-align: center;\"><br />\n"
                    . "[ <a href=\"index.php?file=Gallery\" style=\"text-decoration: underline\">" . _INDEXGALLERY . "</a> | "
                    . "<a href=\"index.php?file=Gallery&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSIMG . "</a> | "
                    . "<a href=\"index.php?file=Gallery&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _TOPIMG . "</a> | "
                    . _SUGGESTIMG . " ]";

        $action = "index.php?file=Suggest&amp;op=add_sug&amp;module=Gallery";
        $autor = $user[2];

        $refuse = "</div></form><br />\n";
    }

    echo "<br /><div style=\"text-align: center;\">" . $titre . "</div><br />\n"
            . "<form method=\"post\" action=\"" . $action . "\" enctype=\"multipart/form-data\">\n"
            . "<table style=\"margin: auto; width: 98%; text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" value=\"" . $content[0] . "\" size=\"40\" /></td></tr>\n"
            . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"0\">* " . _NONE . "</option>\n";

    $sql = mysql_query("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($cid, $titre) = mysql_fetch_array($sql)){
        $titre = printSecuTags($titre);

        if ($content){
            if ($cid == $content[4]) $selected = "selected=\"selected\"";
            else $selected = "";
        }

        echo "<option value=\"" . $cid . "\" " . $selected . ">* " . $titre . "</option>\n";

        $sql2 = mysql_query("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre");
        while (list($s_cid, $s_titre) = mysql_fetch_array($sql2)){
            $s_titre = printSecuTags($s_titre);

            if ($content){
                if ($s_cid == $content[4]) $selected1 = "selected=\"selected\"";
                else $selected1 = "";
            }
            echo "<option value=\"" . $s_cid . "\" " . $selected1 . ">&nbsp;&nbsp;&nbsp;" . $s_titre . "</option>\n";
        }
    }

    echo "</select></td></tr>\n"
            . "<tr><td><b>" . _AUTHOR . " :</b> <input type=\"text\" name=\"auteur\" size=\"30\" value=\"" . $autor . "\" /></td></tr>\n";


    if($_REQUEST['op'] == "show_suggest" && $content[1] != ""){$button = "<input type=\"button\" name=\"bscreen\" value=\"" . _VIEW . "\" Onclick=\"window.open('$content[1]', 'screen','width=1024,height=768');\" /></input>";}
    if($_REQUEST['op'] == "show_suggest" && $content[5] != ""){$botton = "<input type=\"button\" name=\"bscreen\" value=\"" . _DOWNLOAD . "\" Onclick=\"window.open('$content[5]', 'download','width=100,height=100');\" /></input>";}

    echo "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
            . "<tr><td><textarea ";
            
    echo $_REQUEST['page'] == 'admin' ? 'class="editor" ' : 'id="e_advanced" ';
    
    echo " name=\"description\" rows=\"10\" cols=\"65\">" . $content[3] . "</textarea></td></tr>\n";

    if ($sug_id != ""){
        echo"<tr><td><b>" . _URLIMG . " :</b> <input type=\"text\" name=\"url\" value=\"" . $content[1] . "\" size=\"49\" /> " . $button . "</td></tr>\n";
    }
    else{
            echo "<tr><td><b>" . _URLIMG . " :</b> <input type=\"text\" name=\"url\" size=\"49\" value=\"http://\" /></td></tr>\n";
            if ($upload_img == "on") echo "<tr><td align=\"left\"><b>" . _UPIMG . " :</b><br /><input type=\"file\" name=\"fichiernom\" /></td></tr><tr><td>&nbsp;</td></tr>\n";
    }

    echo "<tr><td><b>" . _URLIMG2 . " :</b> <input type=\"text\" name=\"url2\" value=\"" . $content[2] . "\" size=\"45\" /></td></tr>\n"
            . "<tr><td><b>" . _URLFILE . " :</b> <input type=\"text\" name=\"url_file\" value=\"" . $content[5] . "\" size=\"45\" /> " . $botton . "</td></tr>\n";

    if ($captcha == 1) create_captcha(1);

    echo "<tr><td>&nbsp;<input type=\"hidden\" name=\"sug_id\" value=\"" . $sug_id . "\" /></td></tr>\n"
            . "</table><div style=\"text-align: center;\"><input type=\"submit\" value=\"" . _SEND . "\" />" . $refuse;
}

function make_array($data){
    include("modules/Suggest/config.php");

    $data['titre'] = printSecuTags($data['titre']);
    $data['auteur'] = htmlentities($data['auteur']);
    $data['url'] = htmlentities($data['url']);
    $data['url2'] = htmlentities($data['url2']);
    $data['url_file'] = htmlentities($data['url_file']);
    $data['cat'] = printSecuTags($data['cat']);
    
    $data['titre'] = str_replace("|", "&#124;", $data['titre']);
    $data['description'] = str_replace("|", "&#124;", $data['description']);
    $data['auteur'] = str_replace("|", "&#124;", $data['auteur']);

    $filename = $_FILES['fichiernom']['name'];
    $filename = str_replace(" ", "_", $filename);

    if ($filename != "" && $upload_img == "on"){
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG"){
            $url_file = $rep_img . time() . "." . $ext;
    
            move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_file) or die ("<br /><br /><div style=\"text-align: center;\"><b>Upload file failed !!!</b></div><br /><br />");
            @chmod ($url_file, 0644);
        }
        else{    
            $url_file = "Error : no image file !";
        }
    }
    else{
        $url_file = $data['url'];
    }

    $content = $data['titre'] . "|" . $url_file . "|" . $data['url2'] . "|" . $data['description'] . "|" . $data['cat'] . "|" . $data['url_file'] . "|" . $data['auteur'];
    return $content;
}

function del_suggest($data){
    $data = explode('|', $data);
    @unlink($data[1]);
}

function send($data){
    global $nuked;

    include("modules/Suggest/config.php");

    $data['description'] = html_entity_decode($data['description']);
    $data['titre'] = mysql_real_escape_string(stripslashes($data['titre']));
    $data['description'] = mysql_real_escape_string(stripslashes($data['description']));
    $data['auteur'] = mysql_real_escape_string(stripslashes($data['auteur']));
    $date = time();

    if ($upload_img == "on" && !preg_match("`http://`i", $data['url']) && $rep_img != $rep_img_ok && stripos($rep_img, $data['url'])){
        $url_ok = str_replace($rep_img, $rep_img_ok, $data['url']);
        $url_dest = $url_ok;
    
        $is_ok = @rename($data['url'], $url_dest);
    
        if ($is_ok){
            if (is_file($data['url'] )){
                @chmod($data['url'], 0666);
                @unlink($data['url']);
            }
    
            $url_img = $url_dest;
        }
        else{
            $url_img = $data['url'];
        }

        @chmod ($url_img, 0644);
    }
    else{
        $url_img = $data['url'];
    }

        $upd = mysql_query("INSERT INTO " . GALLERY_TABLE . " ( `sid` , `titre` , `description` , `url` , `url2` , `url_file` , `cat`, `date` , `autor` ) VALUES ( '' , '" . $data['titre'] . "' , '" . $data['description'] . "' , '" . $url_img . "' , '" . $data['url2'] . "' , '" . $data['url_file'] . "' , '" . $data['cat'] . "' , '" . $date . "' , '" . $data['auteur'] . "' )");
        $sqls = mysql_query("SELECT sid FROM " . GALLERY_TABLE . " WHERE date = '" . $date . "' AND titre='" . $data['titre'] . "'");
        list($sid) = mysql_fetch_array($sqls);
        echo "<script>\n"
                ."setTimeout('screen()','3000');\n"
                ."function screen() { \n"
                ."screenon('index.php?file=Gallery&op=description&sid=".$sid."&orderby=news', 'index.php?file=Suggest&page=admin');\n"
                ."}\n"
                ."</script>\n";
}
?>