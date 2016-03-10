<?php
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
if (!defined("INDEX_CHECK")){
    exit('You can\'t run this file alone.');
}

global $nuked, $user, $language;
translate("modules/Download/lang/" . $language . ".lang.php");

function form($content, $sug_id){
    global $page, $op, $nuked, $user;

    include "modules/Suggest/config.php";

    if (is_array($content)) {
        $titre = "<strong>" . _VALIDDOWNLOAD . "</strong>";
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

        $refuse = "&nbsp;<input class=\"button\" type=\"button\" value=\"" . _REMOVE . "\" onclick=\"javascript:del_sug('" . $sug_id . "');\" />\n"
                        . "<a class=\"buttonLink\" href=\"index.php?file=Suggest&amp;page=admin\">" . __('BACK') . "</a></div></form><br />\n";
    }
    else{
        $titre = "<strong> " . _DOWNLOAD . " </strong></div>\n"
                    . "<div style=\"text-align: center;\"><br />\n"
                    . "[ <a href=\"index.php?file=Download\" style=\"text-decoration: underline\">" . _INDEXDOWNLOAD . "</a> | "
                    . "<a href=\"index.php?file=Download&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSFILE . "</a> | "
                    . "<a href=\"index.php?file=Download&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _POPULAR . "</a> | "
                    . _SUGGESTFILE . " ]";

        $action = "index.php?file=Suggest&amp;op=add_sug&amp;module=Download";
        $date = time();
        $content = array("", "", "", "", "http://", "", "", "http://", "", "http://");

        $refuse = "</div></form><br />\n";

        if ($user)
            $content[6] = $user[2];
        else
            $content[6] = '';
    }

    echo "<br /><div style=\"text-align: center;\">" . $titre . "</div><br />\n"
            . "<form method=\"post\" action=\"" . $action . "\"  enctype=\"multipart/form-data\">\n"
            . "<table style=\"margin: auto; text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" value=\"" . $content[0] . "\" /></td></tr>"
            . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"0\">* " . _NONE . "</option>\n";

    $sql = nkDB_execute("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($cid, $titre) = nkDB_fetchArray($sql)){
        $titre = printSecuTags($titre);

        if ($content){
            if ($cid == $content[1]) $selected = "selected=\"selected\"";
            else $selected = "";
        }

        echo "<option value=\"" . $cid . "\" " . $selected . ">* " . $titre . "</option>\n";

        $sql2 = nkDB_execute("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre");
        while (list($s_cid, $s_titre) = nkDB_fetchArray($sql2)){
            $s_titre = printSecuTags($s_titre);

            if ($content){
                if ($s_cid == $content[1]) $selected1 = "selected=\"selected\"";
                else $selected1 = "";
            }

            echo "<option value=\"" . $s_cid . "\" " . $selected1 . ">&nbsp;&nbsp;&nbsp;" . $s_titre . "</option>\n";
        }
    }

    if($op == "show_suggest" && $content[9] != ""){$button = "<input type=\"button\" name=\"bscreen\" value=\"" . _VIEW . "\" Onclick=\"window.open('$content[9]', 'screen','width=1024,height=768');\" /></input>";}
    if($op == "show_suggest" && $content[4] != ""){$botton = "<input type=\"button\" name=\"bscreen\" value=\"" . _DOWNLOAD . "\" Onclick=\"window.open('$content[4]', 'download','width=100,height=100');\" /></input>";}

    echo "</select></td></tr>\n"
            . "<tr><td><b>" . __('AUTHOR') . " :</b> <input type=\"text\" name=\"autor\" size=\"40\" value=\"" . $content[6] . "\" /></td></tr>\n"
            . "<tr><td><b>" . _SITE . " :</b> <input type=\"text\" name=\"site\" size=\"55\" value=\"" . $content[7] . "\" /></td></tr>\n";

    echo "<tr><td><b>" . _DESCR . " :</b><br />\n"
            . "<textarea ";

    echo $page == 'admin' ? 'class="editor" ' : 'id="e_advanced" ';

    echo " name=\"description\" rows=\"10\" cols=\"65\">" . $content[2] . "</textarea></td></tr>\n";

    if ($upload_dl == "off") echo "<tr><td><b>" . _SIZE . " :</b> <input type=\"text\" name=\"taille\" size=\"10\" value=\"" . $content[3] . "\" /> (" . _KO . ")</td></tr>\n";
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

    echo "</table>\n";

    if (initCaptcha()) echo create_captcha();

    echo "<input type=\"hidden\" name=\"date\" value=\"" . $date . "\" />\n"
        . "<input type=\"hidden\" name=\"sug_id\" value=\"" . $sug_id . "\" />\n"
        . "<div style=\"text-align: center;\"><br /><input class=\"button\" style=\"margin-right:10px\" type=\"submit\" value=\"" . __('SEND') . "\" />" . $refuse;
}

function make_array($data){
    include("modules/Suggest/config.php");

    if (! isset($data['taille'])) $data['taille'] = '';
    if (! isset($data['url'])) $data['url'] = '';
    if (! isset($data['screen'])) $data['screen'] = '';

    $data['titre'] = printSecuTags($data['titre']);
    $data['cat'] = printSecuTags($data['cat']);
    $data['taille'] = nkHtmlEntities($data['taille']);
    $data['url'] = nkHtmlEntities($data['url']);
    $data['date'] = nkHtmlEntities($data['date']);
    $data['autor'] = nkHtmlEntities($data['autor']);
    $data['site'] = nkHtmlEntities($data['site']);
    $data['comp'] = printSecuTags($data['comp']);
    $data['screen'] = nkHtmlEntities($data['screen']);
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

    $url_file = upload('fichiernom', $data['url'], $upload_dl, $file_filter, $file_filtre, $rep_dl);
    if ($url_file === false) return false;
    $url_img = upload('imagenom', $data['screen'], $upload_img, $file_filter, $file_filtre, $rep_dl_screen);
    if ($url_img === false) return false;

    $content = $data['titre'] . "|" . $data['cat'] . "|" . $data['description'] . "|" . $data['taille'] . "|" . $url_file . "|" . $data['date'] . "|" . $data['autor'] . "|" . $data['site'] . "|" . $data['comp'] . "|" . $url_img;
    return $content;
}

function upload($filename = '', $url = '', $upload_dl, $file_filter, $file_filtre, $rep_dl){
    require_once 'Includes/nkUpload.php';

    $fileUrl = '';

    if ($upload_dl == 'on' && $_FILES[$filename]['name'] != '') {
        $fileCfg = array(
            'fileType'  => 'all',
            'uploadDir' => $rep_dl,
            //'fileSize'  => 100000
            'fileRename' => true,
            'renameExtension' => array(
                'php' => 'txt',
                'htm' => 'txt'
            )
        );

        if ($file_filter == 'on')
            $fileCfg['allowedExt'] = $file_filtre;

        list($fileUrl, $uploadError, $fileExt) = nkUpload_check($filename, $fileCfg);

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            redirect('index.php?file=Suggest&module=Download', 3);
            return false;
        }
    }
    else {
        $fileUrl = $url;
    }

    return $fileUrl;
}

function move($upload_dl, $url = '', $rep_dl, $rep_dl_ok){
    if ($upload_dl == 'on'
        && stripos($url, 'http://') === false
        && $rep_dl != $rep_dl_ok
        && stripos($url, $rep_dl) !== false
    ){
        $url_ok = str_replace($rep_dl, $rep_dl_ok, $url);
        $is_ok  = @rename($url, $url_ok);

        if ($is_ok){
            if (is_file($url)){
                @chmod($url, 0666);
                @unlink($url);
            }
            $url_file = $url_ok;
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

    $data['description'] = nkHtmlEntityDecode($data['description']);
    $data['titre'] = mysql_real_escape_string(stripslashes($data['titre']));
    $data['description'] = mysql_real_escape_string(stripslashes($data['description']));
    $data['autor'] = mysql_real_escape_string(stripslashes($data['autor']));
    $data['comp'] = mysql_real_escape_string(stripslashes($data['comp']));

    if (isset($data['taille']))
        $data['taille'] = str_replace(",", ".", $data['taille']);
    else
        $data['taille'] = '';

    if ($data['site'] == "http://") $data['site'] = "";
    if ($data['screen'] == "http://") $data['screen'] = "";
    if ($data['url'] == "http://") $data['url'] = "";
    if ($data['site'] != "" && !preg_match("`http://`i", $data['site'])) $data['site'] = "http://" . $data['site'];

    $url_file = move($upload_dl, $data['url'], $rep_dl, $rep_dl_ok);
    $url_img = move($upload_img, $data['screen'], $rep_dl_screen, $rep_dl_screen_ok);

    $upd = nkDB_execute("INSERT INTO " . DOWNLOAD_TABLE . "  ( `date` , `taille` , `titre` , `description` , `type` , `url` ,  `autor` , `url_autor`  , `comp` , `screen` )  VALUES ( '" . $data['date'] . "' , '" . $data['taille'] . "' , '" . $data['titre'] . "' , '" . $data['description'] . "' , '" . $data['cat'] . "' , '" . $url_file . "' , '" . $data['autor'] . "' , '" . $data['site'] . "' , '" . $data['comp'] . "' , '" . $url_img . "' )");

    $sql = nkDB_execute("SELECT id FROM " . DOWNLOAD_TABLE . " WHERE date = '" . $data['date'] . "' AND titre = '" . $data['titre'] . "'");
    list($id) = nkDB_fetchArray($sql);

    setPreview('index.php?file=Download&op=description&dl_id='. $id, 'index.php?file=Suggest&page=admin');
}

function del_suggest($data){
    $data = explode('|', $data);
    @unlink($data[4]);
    @unlink($data[9]);
    //var_dump($data);
}

?>