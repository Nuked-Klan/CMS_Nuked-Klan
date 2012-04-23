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
translate("modules/Download/lang/" . $language . ".lang.php");

function form($content, $sug_id){
    global $nuked, $user, $captcha;

    include("modules/Suggest/config.php");

    if ($content != ""){
        $titre = "<big><b>" . _VALIDDOWNLOAD . "</b></big>";
        $action = "index.php?file=Suggest&amp;page=admin&amp;op=valid_suggest&amp;module=Download";
        $date = $content[5];

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
        $titre = "<big><b> " . _DOWNLOAD . " </b></big></div>\n"
                    . "<div style=\"text-align: center;\"><br />\n"
                    . "[ <a href=\"index.php?file=Download\" style=\"text-decoration: underline\">" . _INDEXDOWNLOAD . "</a> | "
                    . "<a href=\"index.php?file=Download&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSFILE . "</a> | "
                    . "<a href=\"index.php?file=Download&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _POPULAR . "</a> | "
                    . _SUGGESTFILE . " ]";

        $action = "index.php?file=Suggest&amp;op=add_sug&amp;module=Download";
        $date = time();
        $content = array("", "", "", "", "http://", "", "", "http://", "", "http://");

        $refuse = "</div></form><br />\n";
    }
    
    echo "<br /><div style=\"text-align: center;\">" . $titre . "</div><br />\n"
            . "<form method=\"post\" action=\"" . $action . "\"  enctype=\"multipart/form-data\">\n"
            . "<table style=\"margin: auto; width: 98%; text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" value=\"" . $content[0] . "\" /></td></tr>"
            . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"0\">* " . _NONE . "</option>\n";

    $sql = mysql_query("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($cid, $titre) = mysql_fetch_array($sql)){
        $titre = printSecuTags($titre);

        if ($content){
            if ($cid == $content[1]) $selected = "selected=\"selected\"";
            else $selected = "";
        }
        
        echo "<option value=\"" . $cid . "\" " . $selected . ">* " . $titre . "</option>\n";

        $sql2 = mysql_query("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre");
        while (list($s_cid, $s_titre) = mysql_fetch_array($sql2)){
            $s_titre = printSecuTags($s_titre);

            if ($content){
                if ($s_cid == $content[1]) $selected1 = "selected=\"selected\"";
                else $selected1 = "";
            }
            
            echo "<option value=\"" . $s_cid . "\" " . $selected1 . ">&nbsp;&nbsp;&nbsp;" . $s_titre . "</option>\n";
        }
    }

    if($_REQUEST['op'] == "show_suggest" && $content[9] != ""){$button = "<input type=\"button\" name=\"bscreen\" value=\"" . _VIEW . "\" Onclick=\"window.open('$content[9]', 'screen','width=1024,height=768');\" /></input>";}
    if($_REQUEST['op'] == "show_suggest" && $content[4] != ""){$botton = "<input type=\"button\" name=\"bscreen\" value=\"" . _DOWNLOAD . "\" Onclick=\"window.open('$content[4]', 'download','width=100,height=100');\" /></input>";}

    echo "</select></td></tr>\n"
            . "<tr><td><b>" . _AUTOR . " :</b> <input type=\"text\" name=\"autor\" size=\"40\" value=\"" . $content[6] . "\" /></td></tr>\n"
            . "<tr><td><b>" . _SITE . " :</b> <input type=\"text\" name=\"site\" size=\"55\" value=\"" . $content[7] . "\" /></td></tr>\n";

    echo "<tr><td><b>" . _DESCR . " :</b><br />\n"
            . "<textarea ";
    
    echo $_REQUEST['page'] == 'admin' ? 'class="editor" ' : 'id="e_advanced" ';
    
    echo " id=\"download_texte\" name=\"description\" rows=\"10\" cols=\"65\">" . $content[2] . "</textarea></td></tr>\n";
    
    if ($upload_dl_ext == "on" || $upload_dl == "off") echo "<tr><td><b>" . _SIZE . " :</b> <input type=\"text\" name=\"taille\" size=\"10\" value=\"" . $content[3] . "\" /> (" . _KO . ")</td></tr>\n";
    echo "<tr><td><b>" . _COMPATIBLE . " :</b> <input type=\"text\" name=\"comp\" size=\"45\" value=\"" . $content[8] . "\" /></td></tr>\n";

    if ($sug_id != ""){
        echo "<tr><td><b>" . _URL . " :</b> <input type=\"text\" name=\"url\" size=\"55\" value=\"" . $content[4] . "\"> " . $botton . "</td></tr>\n";
    }
    else{
        echo "<tr><td>&nbsp;</td></tr>\n";
        
        if ($upload_dl == "off") echo "<tr><td>&nbsp;</td></tr><tr><td><b>" . _URL . " :</b> <input type=\"text\" name=\"url\" size=\"55\"value=\"http://\" /></td></tr>\n";
        else echo "<tr><td align=\"left\"><b>" . _UPFILE . " :</b> <input type=\"file\" name=\"fichiernom\" /></td></tr>\n";
        
        echo "<tr><td>&nbsp;</td></tr>\n";
    }
    if ($sug_id != ""){
        echo "<tr><td><b>" . _CAPTURE . " :</b> <input type=\"text\" name=\"screen\" size=\"55\" value=\"" . $content[9] . "\"> " . $button . "</td></tr>\n";
    }
    else{
        if ($upload_img == "off") echo "<tr><td align=\"left\"><b>" . _CAPTURE . " :</b>  <input type=\"text\" name=\"screen\" size=\"45\" value=\"http://\" /></td></tr>\n";
        else echo "<tr><td align=\"left\"><b>" . _CAPTURE . " :</b>  <input type=\"file\" name=\"imagenom\" /></td></tr>\n";
        
        echo "<tr><td>&nbsp;</td></tr>\n";
    }

    if ($captcha == 1) create_captcha(1);

    echo "</table><input type=\"hidden\" name=\"date\" value=\"" . $date . "\" />\n"
            . "<input type=\"hidden\" name=\"sug_id\" value=\"" . $sug_id . "\" />\n"
            . "<div style=\"text-align: center;\"><input type=\"submit\" value=\"" . _SEND . "\" />" . $refuse;
}

function make_array($data){
    include("modules/Suggest/config.php");

    $data['titre'] = printSecuTags($data['titre']);
    $data['cat'] = printSecuTags($data['cat']);
    $data['taille'] = htmlentities($data['taille']);
    $data['url'] = htmlentities($data['url']);
    $data['date'] = htmlentities($data['date']);
    $data['autor'] = htmlentities($data['autor']);
    $data['site'] = htmlentities($data['site']);
    $data['comp'] = printSecuTags($data['comp']);
    $data['screen'] = htmlentities($data['screen']);
    $data['taille'] = str_replace(",", ".", $data['taille']);

    $data['titre'] = str_replace("|", "&#124;", $data['titre']);
    $data['description'] = str_replace("|", "&#124;", $data['description']);
    $data['autor'] = str_replace("|", "&#124;", $data['autor']);

    if ($data['site'] != "" && !preg_match("`http://`i", $data['site'])){
        $data['site'] = "http://" . $data['site'];
    }

    if ($data['screen'] != "" && !preg_match("`http://`i", $data['screen'])){
        $data['screen'] = "http://" . $data['screen'];
    }

    if ($data['url'] != "" && !preg_match("`http://`i", $data['url'])){
        $data['url'] = "http://" . $data['url'];
    }

    $url_file = upload($_FILES['fichiernom'], $data['url'], $upload_dl, $file_filter, $file_filtre, $rep_dl);
    $url_img = upload($_FILES['imagenom'], $data['screen'], $upload_img, $file_filter, $file_filtre, $rep_dl_screen);

    $content = $data['titre'] . "|" . $data['cat'] . "|" . $data['description'] . "|" . $data['taille'] . "|" . $url_file . "|" . $data['date'] . "|" . $data['autor'] . "|" . $data['site'] . "|" . $data['comp'] . "|" . $url_img;
    return $content;
}

function upload($file = "", $url = "", $upload_dl, $file_filter, $file_filtre, $rep_dl){
    $filename = $file['name'];

    if ($filename != "" && $upload_dl == "on"){
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($file_filter == "on" && !in_array(strtolower($ext), $file_filtre)){
            echo "<br /><br /><div style=\"text-align: center;\"><b>Error : No authorized file !</b></div><br /><br />";
            closetable();
            redirect("index.php?file=Suggest&module=Download", 3);
            footer();
            exit();
         }

        if (preg_match("`php`i", $ext) || preg_match("`htm`i", $ext)) $ext = "txt";

        $url_file = $rep_dl . time() . "." . $ext;
        move_uploaded_file($file['tmp_name'], $url_file) or die ("<br /><br /><div style=\"text-align: center;\"><b>Upload file failed !!!</b></div><br /><br />");
        @chmod ($url_file, 0644);
    }
    else{
        $url_file = $url;
    }
    
    return $url_file;
}

function move($upload_dl, $url = "", $rep_dl, $rep_dl_ok){
    if ($upload_dl == "on" && !preg_match("`http://`i", $url) && $rep_dl != $rep_dl_ok && stripos($rep_dl, $url)){
        $url_ok = str_replace($rep_dl, $rep_dl_ok, $url);
        $url_dest = $url_ok;

        $is_ok = @rename($url, $url_dest);

        if ($is_ok){
            if (is_file($url)){
                @chmod($url, 0666);
                @unlink($url);
            }
            $url_file = $url_dest;
        }
        else{
            $url_file = $url;
        }
        
        @chmod ($url_file, 0644);
    }
    else{
        $url_file = $url;
    }
    
    return $url_file;
}

function send($data){
    global $nuked;

    include("modules/Suggest/config.php");

    $data['description'] = html_entity_decode($data['description']);
    $data['titre'] = mysql_real_escape_string(stripslashes($data['titre']));
    $data['description'] = mysql_real_escape_string(stripslashes($data['description']));
    $data['autor'] = mysql_real_escape_string(stripslashes($data['autor']));
    $data['comp'] = mysql_real_escape_string(stripslashes($data['comp']));
    $data['taille'] = str_replace(",", ".", $data['taille']);

    if ($data['site'] == "http://") $data['site'] = "";
    if ($data['screen'] == "http://") $data['screen'] = "";
    if ($data['url'] == "http://") $data['url'] = "";
    if ($data['site'] != "" && !preg_match("`http://`i", $data['site'])) $data['site'] = "http://" . $data['site'];

    $url_file = move($upload_dl, $data['url'], $rep_dl, $rep_dl_ok);
    $url_img = move($upload_dl_screen, $data['screen'], $rep_dl_screen, $rep_dl_screen_ok);

    $upd = mysql_query("INSERT INTO " . DOWNLOAD_TABLE . "  ( `date` , `taille` , `titre` , `description` , `type` , `url`  , `url2` ,  `autor` , `url_autor`  , `comp` , `screen` )  VALUES ( '" . $data['date'] . "' , '" . $data['taille'] . "' , '" . $data['titre'] . "' , '" . $data['description'] . "' , '" . $data['cat'] . "' , '" . $url_file . "' , '" . $data['url2'] . "' , '" . $data['autor'] . "' , '" . $data['site'] . "' , '" . $data['comp'] . "' , '" . $url_img . "' )");

    $sql = mysql_query("SELECT id FROM " . DOWNLOAD_TABLE . " WHERE date = '" . $data['date'] . "' AND titre = '" . $data['titre'] . "'");
    list($id) = mysql_fetch_array($sql);
    echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php?file=Download&op=description&dl_id=".$id."', 'index.php?file=Suggest&page=admin');\n"
            ."}\n"
            ."</script>\n";
}

function del_suggest($data){
    $data = explode('|', $data);
    @unlink($data[4]);
    @unlink($data[9]);
    var_dump($data);
}
?>