<?php
/**
 * @version     1.8
 * @link https://nuked-klan.fr Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
if (!defined("INDEX_CHECK")){
    exit('You can\'t run this file alone.');
}

global $nuked, $user, $language;
translate("modules/Gallery/lang/" . $language . ".lang.php");

function form($content, $sug_id){
    global $page, $op, $nuked, $user;

    include("modules/Suggest/config.php");

    if (is_array($content)) {
        $titre = "<strong>" . _VALIDIMG . "</strong>";
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

        $refuse = "&nbsp;<input class=\"button\" type=\"button\" value=\"" . _REMOVE . "\" onclick=\"javascript:del_sug('" . $sug_id . "');\" />\n"
                     . "<a class=\"buttonLink\" href=\"index.php?file=Suggest&amp;page=admin\">" . __('BACK') . "</a></div></form><br />\n";

    }
    else{
        if ($nuked['gallery_title'] != ""){
            $title = $nuked['gallery_title'];
        }
        else {
            $title = _GALLERY;
        }

        $titre = "<strong> " . $title . " </strong></div>\n"
                    . "<div style=\"text-align: center;\"><br />\n"
                    . "[ <a href=\"index.php?file=Gallery\" style=\"text-decoration: underline\">" . _INDEXGALLERY . "</a> | "
                    . "<a href=\"index.php?file=Gallery&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSIMG . "</a> | "
                    . "<a href=\"index.php?file=Gallery&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _TOPIMG . "</a> | "
                    . _SUGGESTIMG . " ]";

        $action = "index.php?file=Suggest&amp;op=add_sug&amp;module=Gallery";

        if ($user)
            $autor = $user[2];
        else
            $autor = '';

        $refuse = "</div></form><br />\n";
    }

    echo "<br /><div style=\"text-align: center;\">" . $titre . "</div><br />\n"
            . "<form method=\"post\" action=\"" . $action . "\" enctype=\"multipart/form-data\">\n"
            . "<table style=\"margin: auto; text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" value=\"" . $content[0] . "\" size=\"40\" /></td></tr>\n"
            . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"0\">* " . __('NONE_CATEGORY') . "</option>\n";

    $sql = nkDB_execute("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($cid, $titre) = nkDB_fetchArray($sql)){
        $titre = printSecuTags($titre);

        if ($content && $cid == $content[4]){
            $selected = "selected=\"selected\"";

        }
        else $selected = "";

        echo "<option value=\"" . $cid . "\" " . $selected . ">* " . $titre . "</option>\n";

        $sql2 = nkDB_execute("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre");
        while (list($s_cid, $s_titre) = nkDB_fetchArray($sql2)){
            $s_titre = printSecuTags($s_titre);

            if ($content){
                if ($s_cid == $content[4]) $selected1 = "selected=\"selected\"";
                else $selected1 = "";
            }
            echo "<option value=\"" . $s_cid . "\" " . $selected1 . ">&nbsp;&nbsp;&nbsp;" . $s_titre . "</option>\n";
        }
    }

    echo "</select></td></tr>\n"
            . "<tr><td><b>" . __('AUTHOR') . " :</b> <input type=\"text\" name=\"auteur\" size=\"30\" value=\"" . $autor . "\" /></td></tr>\n";

    $button = $botton = '';

    if($op == "show_suggest" && $content[1] != ""){$button = "<input type=\"button\" name=\"bscreen\" value=\"" . _VIEW . "\" Onclick=\"window.open('$content[1]', 'screen','width=1024,height=768');\" /></input>";}
    if($op == "show_suggest" && $content[5] != ""){$botton = "<input type=\"button\" name=\"bscreen\" value=\"" . _DOWNLOAD . "\" Onclick=\"window.open('$content[5]', 'download','width=100,height=100');\" /></input>";}

    echo "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
            . "<tr><td><textarea ";

    echo $page == 'admin' ? 'class="editor" ' : 'id="e_advanced" ';

    echo " name=\"description\" rows=\"10\" cols=\"65\">" . $content[3] . "</textarea></td></tr>\n";

    if ($sug_id != ""){
        echo"<tr><td><b>" . _URLIMG . " :</b> <input type=\"text\" name=\"url\" value=\"" . $content[1] . "\" size=\"49\" /> " . $button . "</td></tr>\n";
    }
    else{
            echo "<tr><td><b>" . _URLIMG . " :</b> <input type=\"text\" name=\"url\" size=\"49\" value=\"http://\" /></td></tr>\n";
            if ($upload_img == "on") echo "<tr><td align=\"left\"><b>" . _GUPIMG . " :</b><br /><input type=\"file\" name=\"fichiernom\" /></td></tr><tr><td>&nbsp;</td></tr>\n";
    }

    echo "<tr><td><b>" . _URLIMG2 . " :</b> <input type=\"text\" name=\"url2\" value=\"" . $content[2] . "\" size=\"45\" /></td></tr>\n"
        . "<tr><td><b>" . _FILE_URL . " :</b> <input type=\"text\" name=\"url_file\" value=\"" . $content[5] . "\" size=\"45\" /> " . $botton . "</td></tr>\n"
        . "<tr><td>&nbsp;\n";

    if (initCaptcha()) echo create_captcha();

    echo "<input type=\"hidden\" name=\"sug_id\" value=\"" . $sug_id . "\" /></td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input style=\"margin-right:10px\" class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" />" . $refuse;
}

function make_array($data){
    include("modules/Suggest/config.php");

    $data['titre'] = printSecuTags($data['titre']);
    $data['auteur'] = nkHtmlEntities($data['auteur']);
    $data['url'] = nkHtmlEntities($data['url']);
    $data['url2'] = nkHtmlEntities($data['url2']);
    $data['url_file'] = nkHtmlEntities($data['url_file']);
    $data['cat'] = printSecuTags($data['cat']);

    $data['titre'] = str_replace("|", "&#124;", $data['titre']);
    $data['description'] = str_replace("|", "&#124;", $data['description']);
    $data['auteur'] = str_replace("|", "&#124;", $data['auteur']);

    require_once 'Includes/nkUpload.php';

    $imageUrl = '';

    if ($upload_img == 'on' && $_FILES['fichiernom']['name'] != '') {
        list($imageUrl, $uploadError, $imageExt) = nkUpload_check('fichiernom', array(
            'allowedExtension'  => array('jpg', 'jpeg', 'png', 'gif'),
            'uploadDir'         => $rep_img,
            'fileRename'        => true
        ));

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            redirect('index.php?file=Suggest&module=Gallery', 3);
            return false;
        }
    }
    else {
        $imageUrl = $data['url'];
    }

    $content = $data['titre'] . "|" . $imageUrl . "|" . $data['url2'] . "|" . $data['description'] . "|" . $data['cat'] . "|" . $data['url_file'] . "|" . $data['auteur'];
    return $content;
}

function del_suggest($data){
    $data = explode('|', $data);
    @unlink($data[1]);
}

function send($data){
    global $nuked;

    include("modules/Suggest/config.php");

    $data['description'] = nkHtmlEntityDecode($data['description']);
    $data['titre'] = nkDB_realEscapeString(stripslashes($data['titre']));
    $data['description'] = nkDB_realEscapeString(stripslashes($data['description']));
    $data['auteur'] = nkDB_realEscapeString(stripslashes($data['auteur']));
    $date = time();

    if ($upload_img == 'on'
        && stripos($url, 'http://') === false
        && $rep_dl != $rep_dl_ok
        && stripos($data['url'], $rep_img) !== false
    ){
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

        $upd = nkDB_execute("INSERT INTO " . GALLERY_TABLE . " ( `sid` , `titre` , `description` , `url` , `url2` , `url_file` , `cat`, `date` , `autor` ) VALUES ( '' , '" . $data['titre'] . "' , '" . $data['description'] . "' , '" . $url_img . "' , '" . $data['url2'] . "' , '" . $data['url_file'] . "' , '" . $data['cat'] . "' , '" . $date . "' , '" . $data['auteur'] . "' )");
        $sqls = nkDB_execute("SELECT sid FROM " . GALLERY_TABLE . " WHERE date = '" . $date . "' AND titre='" . $data['titre'] . "'");
        list($sid) = nkDB_fetchArray($sqls);
        setPreview('index.php?file=Gallery&op=description&sid='. $sid .'&orderby=news', 'index.php?file=Suggest&page=admin');
}
?>
