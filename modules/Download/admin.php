<?php
/**
 * admin.php
 *
 * Backend of Download module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Download'))
    return;


function add_file() {
    global $nuked, $language;

    $upload_max_filesize = @ini_get('upload_max_filesize');
    $file_uploads = @ini_get('file_uploads');

    if ($file_uploads == 1 && $upload_max_filesize != "") {
        list($maxfilesize) = explode('M', $upload_max_filesize);
        $upload_status = "(" . _MAX . " : " . $maxfilesize . "&nbsp;" . _MO . ")";
    } else {
        $upload_status = "";
    }

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADDFILE . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(2);

    echo "<form method=\"post\" action=\"index.php?file=Download&amp;page=admin&amp;op=send_file\" enctype=\"multipart/form-data\" onsubmit=\"backslash('dl_texte');\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" /></td></tr>\n"
        . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\">\n";

    select_cat();

    echo "</select></td></tr>\n"
        . "<tr><td align=\"left\" colspan=\"2\"><b>" . _AUTOR . " :</b> <input type=\"text\" name=\"autor\" size=\"40\" /></td></tr>\n"
        . "<tr><td align=\"left\"><b>" . _SITE . " :</b> <input type=\"text\" name=\"site\" size=\"55\" value=\"http://\" /></td></tr>\n"
        . "<tr><td align=\"center\">\n";

    echo"</td></tr><tr><td align=\"center\">\n";


    echo "</td></tr><tr><td><b>" . _DESCR . " : </b><br />\n"
        . "<textarea class=\"editor\" id=\"dl_texte\" name=\"description\" rows=\"10\" cols=\"65\" onselect=\"storeCaret('dl_texte');\" onclick=\"storeCaret('dl_texte');\" onkeyup=\"storeCaret('dl_texte');\"></textarea></td></tr>\n"
        . "<tr><td align=\"left\"><b>" . _SIZE . " :  </b><input type=\"text\" name=\"size\" size=\"5\" /> (" . _KO . ")"
        . "&nbsp;<b>" . _LEVEL . " :</b> <select name=\"level\">\n"
        . "<option>0</option>\n"
        . "<option>1</option>\n"
        . "<option>2</option>\n"
        . "<option>3</option>\n"
        . "<option>4</option>\n"
        . "<option>5</option>\n"
        . "<option>6</option>\n"
        . "<option>7</option>\n"
        . "<option>8</option>\n"
        . "<option>9</option></select></td></tr>\n"
        . "<tr><td><b>" . _COMPATIBLE . " :</b> <input type=\"text\" name=\"comp\" size=\"45\" /></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _URLFILE . " :</b> <input type=\"text\" name=\"url\" size=\"55\" value=\"http://\" /></td></tr>\n"
        . "<tr><td><b>" . _URL2 . " :</b> <input type=\"text\" name=\"url2\" size=\"55\" value=\"http://\" /></td></tr>\n"
        . "<tr><td><b>" . _URL3 . " :</b> <input type=\"text\" name=\"url3\" size=\"55\" value=\"http://\" /></td></tr>\n"
        . "<tr><td><b>" . _UPFILE . " :</b>&nbsp;" . $upload_status . " <input type=\"file\" name=\"copy\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_file\" value=\"1\" /> " . _REPLACE . "</td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _CAPTURE . " :</b> <input type=\"text\" name=\"screen\" size=\"42\" value=\"http://\" /></td></tr>\n"
        . "<tr><td><b>" . _UPIMG . " :</b> <input type=\"file\" name=\"screen2\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" /> " . _REPLACE . "</td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _ADDTHISFILE . "\" /><a class=\"buttonLink\" href=\"index.php?file=Download&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function send_file() {
    global $nuked, $user;

    $arrayRequest = array('date', 'size', 'titre', 'description', 'cat', 'url', 'url2', 'url3', 'level', 'autor', 'site', 'comp', 'screen', 'screen2', 'copy', 'ecrase_file', 'ecrase_screen');

    foreach($arrayRequest as $key){
        if(array_key_exists($key, $_REQUEST)){
            ${$key} = $_REQUEST[$key];
        }
        else{
            ${$key} = '';
        }
    }

    $description = secu_html(nkHtmlEntityDecode($description));
    $description = nkDB_realEscapeString(stripslashes($description));
    $titre = nkDB_realEscapeString(stripslashes($titre));
    $autor = nkDB_realEscapeString(stripslashes($autor));
    $comp = nkDB_realEscapeString(stripslashes($comp));

    $date = time();
    $taille = str_replace(",", ".", $size);

    if ($site == "http://") $site = "";
    if ($url == "http://") $url = "";
    if ($url2 == "http://") $url2 = "";
    if ($url3 == "http://") $url3 = "";
    if ($screen == "http://") $screen = "";

    if ($site != "" && !preg_match("`http://`i", $site)) {
        $site = "http://" . $site;
    }

    $racine_up = "upload/Download/";
    $racine_down = "";

    $deja_file = $deja_screen = '';

    if ($_FILES['copy']['name'] != "") {
        $filename = $_FILES['copy']['name'];
        $filesize = $_FILES['copy']['size'];
        $taille = $filesize / 1024;
        $taille = (round($taille * 100)) / 100;
        $url_file = $racine_up . $filename;

        if (!is_file($url_file) || $ecrase_file == 1) {
            if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && $filename != ".htaccess") {
                if (! move_uploaded_file($_FILES['copy']['tmp_name'], $url_file)) {
                    printNotification('Upload file failed !!!', 'error');
                    return;
                }
                @chmod ($url_file, 0644);
            } else {
                printNotification('Unauthorized file !!!', 'error');
                redirect("index.php?file=Download&page=admin&op=add_file", 2);
                return;
            }
        } else {
            $deja_file = 1;
        }

        $url_full = $racine_down . $url_file;
        $url_full = $url_file;

        if ($url == "") $url = $url_full;
        else if ($url2 == "") $url2 = $url_full;
        else if ($url3 == "") $url3 = $url_full;
        else $url = $url_full;
    }

    if ($_FILES['screen2']['name'] != "") {
        $screenname = $_FILES['screen2']['name'];
        $ext = pathinfo($_FILES['screen2']['name'], PATHINFO_EXTENSION);
        $filename2 = str_replace($ext, "", $screenname);
        $url_screen = $racine_up . $filename2 . $ext;

        if (!is_file($url_screen) || $ecrase_screen == 1) {
            if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
                if (! move_uploaded_file($_FILES['screen2']['tmp_name'], $url_screen)) {
                    printNotification('Upload screen failed !!!', 'error');
                    return;
                }
                @chmod ($url_screen, 0644);
            } else {
                printNotification('No image file !!!', 'error');
                redirect("index.php?file=Download&page=admin&op=add_file", 2);
                return;
            }
        } else {
            $deja_screen = 1;
        }

        $url_full_screen = $racine_down . $url_screen;
        $screen = $url_full_screen;
    }

    if ($deja_file == 1 || $deja_screen == 1) {
        $message = '';

        if ($deja_file == 1) $message .= _DEJAFILE;
        if ($deja_screen == 1) $message .= '&nbsp;'. _DEJASCREEN;

        $message .= '<br />'. _REPLACEIT;

        printNotification($message, 'error', array('backLinkUrl' => 'javascript:history.back()'));
    } else if ($url != "" && $titre != "") {
        $sql = nkDB_execute("INSERT INTO " . DOWNLOAD_TABLE . " ( `date` , `taille` , `titre` , `description` , `type` , `url` , `url2`  , `url3` , `level`, `autor` , `url_autor`  , `comp` , `screen` )  VALUES ( '" . $date . "' , '" . $taille . "' , '" . $titre . "' , '" . $description . "' , '" . $cat . "' , '" . $url . "' , '" . $url2 . "' , '" . $url3 . "' , '" . $level ."' , '" . $autor . "' , '" . $site . "' , '" . $comp . "' , '" . $screen . "' )");

        $id = nkDB_insertId();

        saveUserAction(_ACTIONADDDL .': '. $titre);

        printNotification(_FILEADD, 'success');

        require_once 'Includes/nkSitemap.php';

        if (! nkSitemap_write()) {
            printNotification(__('WRITE_SITEMAP_FAILED'), 'error');
            redirect('index.php?file=Download&page=admin', 5);
            return;
        }

        setPreview('index.php?file=Download&op=description&dl_id='. $id, 'index.php?file=Download&page=admin');
    } else {
        printNotification(_URLORTITLEFAILDED, 'error', array('backLinkUrl' => 'javascript:history.back()'));
    }
}

function del_file($did) {
    global $nuked, $user;

    $sql = nkDB_execute("SELECT titre FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $did . "'");
    list($titre) = nkDB_fetchArray($sql);
    $titre = nkDB_realEscapeString($titre);
    $sql = nkDB_execute("DELETE FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $did . "'");
    $del_com = nkDB_execute("DELETE FROM " . COMMENT_TABLE . " WHERE im_id = '" . $did . "' AND module = 'Download'");
    $del_vote = nkDB_execute("DELETE FROM " . VOTE_TABLE . " WHERE vid = '" . $did . "' AND module = 'Download'");

    saveUserAction(_ACTIONDELDL .': '. $titre);
    printNotification(_FILEDEL, 'success');
    redirect("index.php?file=Download&page=admin", 2);
}

function edit_file($did) {
    global $nuked, $language;

    $sql = nkDB_execute("SELECT titre, description, type, taille, url, url2, url3, count, level, screen, autor, url_autor, comp  FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $did . "'");
    list($titre, $description, $cat, $taille, $url, $url2, $url3, $count, $level, $screen, $autor, $url_autor, $comp) = nkDB_fetchArray($sql);

    $upload_max_filesize = @ini_get('upload_max_filesize');
    $file_uploads = @ini_get('file_uploads');

    $description = editPhpCkeditor($description);

    if ($file_uploads == 1 && $upload_max_filesize != "") {
        list($maxfilesize) = explode('M', $upload_max_filesize);
        $upload_status = "(" . _MAX . " : " . $maxfilesize . "&nbsp;" . _MO . ")";
    } else {
        $upload_status = "";
    }

    if ($cat == 0 || !$cat) {
        $cid = 0;
        $cat_name = _NONE;
    } else {
        $cid = $cat;
        $sql2 = nkDB_execute("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cat . "'");
        list($cat_name) = nkDB_fetchArray($sql2);
        $cat_name = printSecuTags($cat_name);
    }

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _EDITTHISFILE . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Download&amp;page=admin&amp;op=modif_file\" enctype=\"multipart/form-data\" onsubmit=\"backslash('dl_texte');\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" value=\"" . $titre . "\" /></td></tr>\n"
        . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"" . $cid . "\">" . $cat_name . "</option>\n";

    select_cat();

    echo "</select></td></tr>\n"
        . "<tr><td align=\"left\" colspan=\"2\"><b>" . _AUTOR . " :</b> <input type=\"text\" name=\"autor\" size=\"40\" value=\"" . $autor . "\" /></td></tr>\n"
        . "<tr><td align=\"left\"><b>" . _SITE . " :</b> <input type=\"text\" name=\"site\" size=\"55\" value=\"" . $url_autor . "\" /></td></tr>\n"
        . "<tr><td align=\"center\">\n";

    echo"</td></tr><tr><td align=\"center\">\n";


    echo "</td></tr><tr><td><b>" . _DESCR . " : </b><br />\n"
        . "<textarea class=\"editor\" id=\"dl_texte\" name=\"description\" rows=\"10\" cols=\"65\" onselect=\"storeCaret('dl_texte');\" onclick=\"storeCaret('dl_texte');\" onkeyup=\"storeCaret('dl_texte');\">" . $description . "</textarea></td></tr>\n"
        . "<tr><td><b>" . _DOWNLOADED . "</b> : <input type=\"text\" name=\"count\" size=\"7\" value=\"" . $count . "\" />&nbsp;<b>" . _SIZE . " :  </b><input type=\"text\" name=\"taille\" size=\"5\" value=\"" . $taille . "\" /> (" . _KO . ")"
        . "&nbsp;<b>" . _LEVEL . " :</b> <select name=\"level\"><option>" . $level . "</option>\n"
        . "<option>0</option>\n"
        . "<option>1</option>\n"
        . "<option>2</option>\n"
        . "<option>3</option>\n"
        . "<option>4</option>\n"
        . "<option>5</option>\n"
        . "<option>6</option>\n"
        . "<option>7</option>\n"
        . "<option>8</option>\n"
        . "<option>9</option></select></td></tr>\n"
        . "<tr><td><b>" . _COMPATIBLE . " :</b> <input type=\"text\" name=\"comp\" size=\"45\" value=\"" . $comp . "\" /></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _URLFILE . " :</b> <input type=\"text\" name=\"url\" size=\"55\" value=\"" . $url . "\" /></td></tr>\n"
        . "<tr><td><b>" . _URL2 . " :</b> <input type=\"text\" name=\"url2\" size=\"55\" value=\"" . $url2 . "\" /></td></tr>\n"
        . "<tr><td><b>" . _URL3 . " :</b> <input type=\"text\" name=\"url3\" size=\"55\" value=\"" . $url3 . "\" /></td></tr>\n"
        . "<tr><td><b>" . _UPFILE . " :</b>&nbsp;" . $upload_status . " <input type=\"file\" name=\"copy\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_file\" value=\"1\" /> " . _REPLACE . "</td></tr>\n"
        . "<tr><td>&nbsp;</td></tr>\n"
        . "<tr><td><b>" . _CAPTURE . " :</b> <input type=\"text\" name=\"screen\" size=\"42\" value=\"" . $screen . "\" /></td></tr>\n"
        . "<tr><td><b>" . _UPIMG . " :</b> <input type=\"file\" name=\"screen2\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" /> " . _REPLACE . "</td></tr>\n"
        . "<tr><td>&nbsp;<input type=\"hidden\" name=\"did\" value=\"" . $did . "\" /></td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFFILE . "\" /><a class=\"buttonLink\" href=\"index.php?file=Download&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div></div>\n";

}

function modif_file() {
    global $nuked, $user;

    $arrayRequest = array('did', 'date', 'taille', 'titre', 'description', 'cat', 'count', 'url', 'url2', 'url3', 'level', 'autor', 'site', 'comp', 'screen', 'screen2', 'copy', 'ecrase_file', 'ecrase_screen');

    foreach($arrayRequest as $key){
        if(array_key_exists($key, $_REQUEST)){
            ${$key} = $_REQUEST[$key];
        }
        else{
            ${$key} = '';
        }
    }

    $description = secu_html(nkHtmlEntityDecode($description));
    $description = nkDB_realEscapeString(stripslashes($description));
    $titre = nkDB_realEscapeString(stripslashes($titre));
    $autor = nkDB_realEscapeString(stripslashes($autor));
    $comp = nkDB_realEscapeString(stripslashes($comp));

    $day = time();
    $taille = str_replace(",", ".", $taille);

    if ($site == "http://") $site = "";
    if ($url == "http://") $url = "";
    if ($url2 == "http://") $url2 = "";
    if ($url3 == "http://") $url3 = "";
    if ($screen == "http://") $screen = "";

    if ($site != "" && !preg_match("`http://`i", $site)) {
        $site = "http://" . $site;
    }

    $racine_up = "upload/Download/";
    $racine_down = "";

    if ($_FILES['copy']['name'] != "") {
        $filename = $_FILES['copy']['name'];
        $filesize = $_FILES['copy']['size'];
        $taille = $filesize / 1024;
        $taille = (round($taille * 100)) / 100;
        $url_file = $racine_up . $filename;

        if (!is_file($url_file) || $ecrase_file == 1) {
            if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && $filename != ".htaccess") {
                if (! move_uploaded_file($_FILES['copy']['tmp_name'], $url_file)) {
                    printNotification('Upload file failed !!!', 'error');
                    return;
                }
                @chmod ($url_file, 0644);
            } else {
                printNotification('Unauthorized file !!!', 'error');
                redirect("index.php?file=Download&page=admin&op=edit_file&did=" . $did, 2);
                return;
            }
        } else {
            $deja_file = 1;
        }

        $url_full = $racine_down . $url_file;
        $url_full = $url_file;

        if ($url == "") $url = $url_full;
        else if ($url2 == "") $url2 = $url_full;
        else if ($url3 == "") $url3 = $url_full;
        else $url = $url_full;
    }

    if ($_FILES['screen2']['name'] && $url) {

        $screenname = $_FILES['screen2']['name'];
        $ext = strrchr($screenname, ".");
        $ext = substr($screenname, 1);
        $filename2 = str_replace($ext, "", $screenname);
        $url_screen = $racine_up . $filename2 . $ext;

        if (!is_file($url_screen) || $ecrase_screen == 1) {
            if (!preg_match("`\.php`i", $screenname) && !preg_match("`\.htm`i", $screenname) && !preg_match("`\.[a-z]htm`i", $screenname) && (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext) || preg_match("`gif`i", $ext) || preg_match("`png`i", $ext))) {
                if (! move_uploaded_file($_FILES['screen2']['tmp_name'], $url_screen)) {
                    printNotification('Upload screen failed !!!', 'error');
                    return;
                }
                @chmod ($url_screen, 0644);
            } else {
                printNotification('No image file !!!', 'error');
                redirect("index.php?file=Download&page=admin&op=edit_file&did=" . $did, 2);
                return;
            }
        } else {
            $deja_screen = 1;
        }

        $url_full_screen = $racine_down . $url_screen;
        $screen = $url_full_screen;
    }

    if ($deja_file == 1 || $deja_screen == 1) {
        $message = '';

        if ($deja_file == 1) $message .= _DEJAFILE;
        if ($deja_screen == 1) $message .= '&nbsp;'. _DEJASCREEN;

        $message .= '<br />'. _REPLACEIT;

        printNotification($message, 'error', array('backLinkUrl' => 'javascript:history.back()'));
    } else if ($url != "" && $titre != "") {
        $sql = nkDB_execute("UPDATE " . DOWNLOAD_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "', type = '" . $cat . "', count = '" . $count . "', url = '" . $url . "', url2 = '" . $url2 . "', url3 = '" . $url3 . "', taille = '" . $taille . "', level = '" . $level . "', edit = '" . $day . "', autor = '" . $autor . "', url_autor = '" . $site . "', comp = '" . $comp . "', screen = '" . $screen . "' WHERE id = '" . $did . "'");

        saveUserAction(_ACTIONMODIFDL .': '. $titre);

        printNotification(_FILEEDIT, 'success');
        setPreview('index.php?file=Download&op=description&dl_id='.$did, 'index.php?file=Download&page=admin');
    } else {
        printNotification(_URLORTITLEFAILDED, 'error', array('backLinkUrl' => 'javascript:history.back()'));
    }
}

function main_broken() {
    global $nuked, $language;

    echo"<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "	function delfile(titre, id) {\n"
        . "		if (confirm('" . _DELETEFILE . " '+titre+' ! " . _CONFIRM . "')) {\n"
        . "			document.location.href = 'index.php?file=Download&page=admin&op=del_file&did='+id;\n"
        . "		}\n"
        . "	}\n"
        . "\n"
        . "	function delbroke() {\n"
        . "		if (confirm('" . _ERASEALLLIST . "')) {\n"
        . "			document.location.href = 'index.php?file=Download&page=admin&op=del_broken';\n"
        . "		}\n"
        . "	}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _BROKENLINKS . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(4);

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
        . "<td colspan=\"2\" style=\"width: 35%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>X</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _ERASE . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

    $i = 0;
    $l = 0;
    $sql = nkDB_execute("SELECT id, titre, url, broke FROM " . DOWNLOAD_TABLE . " WHERE broke > 0 ORDER BY broke DESC, type");
    $nb_broke = nkDB_numRows($sql);

    if ($nb_broke > 0) {
        while (list($did, $titre, $url, $broke) = nkDB_fetchArray($sql)) {
            $titre = printSecuTags($titre);
            $l++;

            echo "<tr>\n"
                . "<td style=\"width: 10%;\" align=\"center\">" . $l . "</td>\n"
                . "<td style=\"width: 30%;\"><b>" . $titre . "</b></td><td style=\"width: 5%;\" align=\"center\"><a href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"modules/Download/images/download.gif\" alt=\"\" title=\"" . $url . "\" /></a></td>\n"
                . "<td style=\"width: 10%;\" align=\"center\">" . $broke . "</td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=del_broke&amp;did=" . $did. "\"><img style=\"border: 0;\" src=\"modules/Download/images/del.gif\" alt=\"\" title=\"" . _ERASEFROMLIST . "\" /></a></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=edit_file&amp;did=" . $did . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISFILE . "\" /></a></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:delfile('" . addslashes($titre) . "', '" . $did . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISFILE . "\" /></a></td></tr>\n";
        }
    } else {
        echo "<tr><td align=\"center\" colspan=\"6\">" . _NODOWNLOADINDB . "</td></tr>\n";
    }

    echo "</table><br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"javascript:delbroke();\">" . _ERASELIST . "</a>\n"
        . "<a class=\"buttonLink\" href=\"index.php?file=Download&amp;page=admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function del_broke($did) {
    global $nuked, $user;

    $sql2 = nkDB_execute("SELECT titre FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $did . "'");
    list($titre) = nkDB_fetchArray($sql2);
    $titre = nkDB_realEscapeString($titre);
    $sql = nkDB_execute("UPDATE " . DOWNLOAD_TABLE . " SET broke = 0 WHERE id = '" . $did . "'");

    saveUserAction(_ACTION1BROKEDL .': '. $titre);

    printNotification(_FILEERASED, 'success');
    redirect("index.php?file=Download&page=admin&op=main_broken", 2);
}

function del_broken() {
    global $nuked, $user;
    $sql = nkDB_execute("UPDATE " . DOWNLOAD_TABLE . " SET broke = 0");

    saveUserAction(_ACTIONALLBROKEDL .'.');

    printNotification(_LISTERASED, 'success');
    redirect("index.php?file=Download&page=admin&op=main_broken", 2);
}

function main() {
    global $nuked, $language;

    $nb_download = 30;

    $sql3 = nkDB_execute("SELECT id FROM " . DOWNLOAD_TABLE);
    $nb_dl = nkDB_numRows($sql3);

    if(array_key_exists('p', $_REQUEST)){
        $page = $_REQUEST['p'];
    }
    else{
        $page = 1;
    }
    $start = $page * $nb_download - $nb_download;

    echo"<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "	function delfile(titre, id) {\n"
        . "		if (confirm('" . _DELETEFILE . " '+titre+' ! " . _CONFIRM . "')) {\n"
        . "			document.location.href = 'index.php?file=Download&page=admin&op=del_file&did='+id;\n"
        . "		}\n"
        . "	}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINDOWN . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(1);


    if(array_key_exists('orderby', $_REQUEST)){
        if ($_REQUEST['orderby'] == "date") {
            $order_by = "D.id DESC";
            $orderBy = 'date';
        } else if ($_REQUEST['orderby'] == "name") {
            $orderBy = 'name';
            $order_by = "D.titre";
        } else if ($_REQUEST['orderby'] == "cat") {
            $orderBy = 'cat';
            $order_by = "DC.titre, DC.parentid";
        } else {
            $orderBy = 'date';
            $order_by = "D.id DESC";
        }
    }
    else{
        $order_by = "D.id DESC";
        $orderBy = 'date';
    }

    echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">\n"
        . "<tr><td align=\"right\">" . _ORDERBY . " : ";

    if ($orderBy == "date" || !$orderBy) {
        echo "<b>" . _DATE . "</b> | ";
    } else {
        echo "<a href=\"index.php?file=Download&amp;page=admin&amp;orderby=date\">" . _DATE . "</a> | ";
    }

    if ($orderBy == "name") {
        echo "<b>" . _TITLE . "</b> | ";
    } else {
        echo "<a href=\"index.php?file=Download&amp;page=admin&amp;orderby=name\">" . _TITLE . "</a> | ";
    }

    if ($orderBy == "cat") {
        echo "<b>" . _CAT . "</b>";
    } else {
        echo "<a href=\"index.php?file=Download&amp;page=admin&amp;orderby=cat\">" . _CAT . "</a>";
    }

    echo "&nbsp;</td></tr></table>\n";

    if ($nb_dl > $nb_download) {
        echo "<div>";
        $url_page = "index.php?file=Download&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
        number($nb_dl, $nb_download, $url_page);
        echo "</div>\n";
    }

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 30%;\" align=\"center\" colspan=\"2\"><b>" . _TITLE . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

    $i = 0;
    $sql = nkDB_execute("SELECT D.id, D.type, D.titre, D.url, D.date, DC.parentid, DC.titre  FROM " . DOWNLOAD_TABLE . " AS D LEFT JOIN " . DOWNLOAD_CAT_TABLE . " AS DC ON DC.cid = D.type ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_download);
    while (list($did, $cat, $titre, $url, $date, $parentid, $namecat) = nkDB_fetchArray($sql)) {
        $titre = printSecuTags($titre);

        $date = nkDate($date);

        if ($cat == 0) {
            $categorie = _NONE;
        } else if ($parentid == 0) {
            $categorie = $namecat;
        } else {
            $sql3 = nkDB_execute("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
            list($parentcat) = nkDB_fetchArray($sql3);
            $categorie = $parentcat . " -> " . $namecat;
            $categorie = printSecuTags($categorie);
        }

        echo "<tr>\n"
            . "<td style=\"width: 25%;\">" . $titre . "</td><td style=\"width: 5%;\" align=\"center\"><a href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"modules/Download/images/download.gif\" alt=\"\" title=\"" . $url . "\" /></a></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $categorie . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=edit_file&amp;did=" . $did . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISFILE . "\" /></a></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:delfile('" . addslashes($titre) . "', '" . $did . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISFILE . "\" /></a></td></tr>\n";
    }

    if ($nb_dl == 0) {
        echo "<tr><td align=\"center\" colspan=\"6\">" . _NODOWNLOADINDB . "</td></tr>\n";
    }

    echo "</table>\n";

    if ($nb_dl > $nb_download) {
        echo "<div>";
        $url_page = "index.php?file=Download&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
        number($nb_dl, $nb_download, $url_page);
        echo "</div>\n";
    }

    echo "<br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function main_cat() {
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "	function delcat(titre, id) {\n"
        . "		if (confirm('" . _DELETEFILE . " '+titre+' ! " . _CONFIRM . "')) {\n"
        . "			document.location.href = 'index.php?file=Download&page=admin&op=del_cat&cid='+id;\n"
        . "		}\n"
        . "	}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _CATMANAGEMENT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(3);

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
        . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CATPARENT . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

    $i = 0;
    $sql = nkDB_execute("SELECT cid, titre, parentid, position FROM " . DOWNLOAD_CAT_TABLE . " ORDER BY parentid, position");
    $nbcat = nkDB_numRows($sql);

    if ($nbcat > 0) {
        while (list($cid, $titre, $parentid, $position) = nkDB_fetchArray($sql)) {
            $titre = printSecuTags($titre);

            echo "<tr>\n"
            . "<td style=\"width: 35%;\" align=\"center\">" . $titre . "</td>\n"
            . "<td style=\"width: 35%;\" align=\"center\">\n";

            if ($parentid > 0) {
                $sql2 = nkDB_execute("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
                list($pnomcat) = nkDB_fetchArray($sql2);
                $pnomcat = printSecuTags($pnomcat);

                echo "<i>" . $pnomcat . "</i>";
            } else {
                echo _NONE;
            }

            echo "</td><td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=down\" title=\"" . _MOVEDOWN . "\">&lt;</a>"
                . "&nbsp;" . $position . "&nbsp;<a href=\"index.php?file=Download&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=up\" title=\"" . _MOVEUP . "\">&gt;</a></td>\n"
                . "<td align=\"center\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=edit_cat&amp;cid=" . $cid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
                . "<td align=\"center\"><a href=\"javascript:delcat('" . addslashes($titre) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
        }
    }else{
        echo "<tr><td align=\"center\" colspan=\"5\">" . _NONE . "&nbsp;" . _CAT . "&nbsp;" . _INDATABASE . "</td></tr>\n";
    }

    echo "</table><br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Download&amp;page=admin&amp;op=add_cat\">" . _ADDCAT . "</a>\n"
        . "<a class=\"buttonLink\" href=\"index.php?file=Download&amp;page=admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function add_cat() {
    global $language, $nuked;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADDCAT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Download&amp;page=admin&amp;op=send_cat\">\n"
        . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
        . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\"><option value=\"0\">" . _NONE . "</option>\n";

    $sql = nkDB_execute("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($cid, $nomcat) = nkDB_fetchArray($sql)) {
        $nomcat = printSecuTags($nomcat);

        echo "<option value=\"" . $cid . "\">" . $nomcat . "</option>\n";
    }

    echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"0\" />\n"
        . "&nbsp;<b>" . _LEVEL . " :</b> <select name=\"level\">\n"
        . "<option>0</option>\n"
        . "<option>1</option>\n"
        . "<option>2</option>\n"
        . "<option>3</option>\n"
        . "<option>4</option>\n"
        . "<option>5</option>\n"
        . "<option>6</option>\n"
        . "<option>7</option>\n"
        . "<option>8</option>\n"
        . "<option>9</option></select></td></tr>\n"
        . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
        . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _CREATECAT . "\" />\n"
        . "<a class=\"buttonLink\" href=\"index.php?file=Download&amp;page=admin&amp;op=main_cat\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function send_cat($titre, $description, $parentid, $level, $position) {
    global $nuked, $user;

    $description = secu_html(nkHtmlEntityDecode($description));
    $titre = nkDB_realEscapeString(stripslashes($titre));
    $description = nkDB_realEscapeString(stripslashes($description));

    $sql = nkDB_execute("INSERT INTO " . DOWNLOAD_CAT_TABLE . " ( `parentid` , `titre` , `description` , `level` , `position` ) VALUES ( '" . $parentid . "' , '" . $titre . "' , '" . $description . "' , '" . $level . "' , '" . $position . "' )");

    saveUserAction(_ACTIONADDCATDL .': '. $titre);

    printNotification(_CATADD, 'success');

    $sql2 = nkDB_execute("SELECT cid FROM " . DOWNLOAD_CAT_TABLE . " WHERE titre = '" . $titre . "' AND parentid = '" . $parentid . "'");
    list($did) = nkDB_fetchArray($sql2);

    setPreview('index.php?file=Download&op=categorie&cat='. $did, 'index.php?file=Download&page=admin&op=main_cat');
}

function edit_cat($cid) {
    global $nuked, $language;

    $sql = nkDB_execute("SELECT titre, description, parentid, level, position FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cid . "'");
    list($titre, $description, $parentid, $level, $position) = nkDB_fetchArray($sql);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _EDITTHISCAT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Download&amp;page=admin&amp;op=modif_cat\">\n"
        . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre . "\" /></td></tr>\n"
        . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\">\n";

    if ($parentid > 0) {
        $sql2 = nkDB_execute("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
        list($pcid, $pnomcat) = nkDB_fetchArray($sql2);

        $pnomcat = printSecuTags($pnomcat);

        echo "<option value=\"" . $pcid . "\">" . $pnomcat . "</option>\n";
    }

    echo "<option value=\"0\">" . _NONE . "</option>\n";

    $sql3 = nkDB_execute("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($catid, $nomcat) = nkDB_fetchArray($sql3)) {
        $nomcat = printSecuTags($nomcat);

        if ($nomcat != $titre) {
            echo "<option value=\"" . $catid . "\">" . $nomcat . "</option>\n";
        }
    }

    $description = editPhpCkeditor($description);

    echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"" . $position . "\" />\n"
        . "&nbsp;<b>" . _LEVEL . " :</b> <select name=\"level\"><option>" . $level . "</option>\n"
        . "<option>0</option>\n"
        . "<option>1</option>\n"
        . "<option>2</option>\n"
        . "<option>3</option>\n"
        . "<option>4</option>\n"
        . "<option>5</option>\n"
        . "<option>6</option>\n"
        . "<option>7</option>\n"
        . "<option>8</option>\n"
        . "<option>9</option></select></td></tr>\n"
        . "<tr><td><b>" . _DESCR . " :</b> <input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /></td></tr>\n"
        . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\">" . $description . "</textarea></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISCAT . "\" />\n"
        . "<a class=\"buttonLink\" href=\"index.php?file=Download&amp;page=admin&amp;op=main_cat\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function modif_cat($cid, $titre, $description, $parentid, $level, $position) {
    global $nuked, $user;

    $description = secu_html(nkHtmlEntityDecode($description));
    $titre = nkDB_realEscapeString(stripslashes($titre));
    $description = nkDB_realEscapeString(stripslashes($description));

    $sql = nkDB_execute("UPDATE " . DOWNLOAD_CAT_TABLE . " SET parentid = '" . $parentid . "', titre = '" . $titre . "', description = '" . $description . "', level = '" . $level . "', position = '" . $position . "' WHERE cid = '" . $cid . "'");
    $sql_file = nkDB_execute("UPDATE " . DOWNLOAD_TABLE . " SET level = '" . $level . "' WHERE type = '" . $cid . "'");
    $sql_cat = nkDB_execute("UPDATE " . DOWNLOAD_CAT_TABLE . " SET level = '" . $level . "' WHERE parentid = '" . $cid . "'");

    $sql_cat = nkDB_execute("SELECT cid FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = '" . $cid . "'");
    while (list($cat_id) = nkDB_fetchArray($sql_cat)) {
        $sql_file2 = nkDB_execute("UPDATE " . DOWNLOAD_TABLE . " SET level = '" . $level . "' WHERE type = '" . $cat_id . "'");
    }

    saveUserAction(_ACTIONMODIFCATDL .': '. $titre);

    printNotification(_CATMODIF, 'success');
    setPreview('index.php?file=Download&op=categorie&cat='. $cid, 'index.php?file=Download&page=admin&op=main_cat');
}

function select_cat() {
    global $nuked;

    $sql = nkDB_execute("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($cid, $titre) = nkDB_fetchArray($sql)) {
        $titre = printSecuTags($titre);

        echo "<option value=\"" . $cid . "\">* " . $titre . "</option>\n";

        $sql2 = nkDB_execute("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre");
        while (list($s_cid, $s_titre) = nkDB_fetchArray($sql2)) {
            $s_titre = printSecuTags($s_titre);

            echo "<option value=\"" . $s_cid . "\">&nbsp;&nbsp;&nbsp;" . $s_titre . "</option>\n";
        }
    }
    echo "<option value=\"0\">* " . _NONE . "</option>\n";
}

function del_cat($cid) {
    global $nuked, $user;
    $sql2 = nkDB_execute("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cid . "'");
    list($titre) = nkDB_fetchArray($sql2);
    $sql = nkDB_execute("DELETE FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cid . "'");
    $sql = nkDB_execute("UPDATE " . DOWNLOAD_CAT_TABLE . " SET parentid = 0 WHERE parentid = '" . $cid . "'");
    $sql = nkDB_execute("UPDATE " . DOWNLOAD_TABLE . " SET type = 0 WHERE type = '" . $cid . "'");

    saveUserAction(_ACTIONDELCATDL .': '. $titre);

    printNotification(_CATDEL, 'success');
    redirect("index.php?file=Download&page=admin&op=main_cat", 2);
}

function main_pref() {
    global $nuked, $language;

    $checked = false;

    if ($nuked['hide_download'] == "on") $checked = true;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(5);

    echo "<form method=\"post\" action=\"index.php?file=Download&amp;page=admin&amp;op=change_pref\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td>" . _NUMBERFILE . " :</td><td><input type=\"text\" name=\"max_download\" size=\"2\" value=\"" . $nuked['max_download'] . "\" /></td></tr>\n"
        . "<tr><td>" . _HIDEDESC . " :</td><td>\n";

        checkboxButton('hide_download', 'hide_download', $checked, false);

    echo "</td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" />\n"
        . "<a class=\"buttonLink\" href=\"index.php?file=Download&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function change_pref($max_download, $hide_download) {
    global $nuked, $user;

    if ($hide_download != "on") $hide_download = "off";

    $upd1 = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_download . "' WHERE name = 'max_download'");
    $upd2 = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $hide_download . "' WHERE name = 'hide_download'");

    saveUserAction(_ACTIONMODIFPREFDL .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect("index.php?file=Download&page=admin", 2);
}

function modif_position($cid, $method) {
    global $nuked, $user;

    $sql2 = nkDB_execute("SELECT titre, position FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cid . "'");
    list($titre, $position) = nkDB_fetchArray($sql2);
    if ($position <=0 AND $method == "up") {
        printNotification(_CATERRORPOS, 'error');
        redirect("index.php?file=Download&page=admin&op=main_cat", 2);
        return;
    }

    if ($method == "up") $upd = nkDB_execute("UPDATE " . DOWNLOAD_CAT_TABLE . " SET position = position - 1 WHERE cid = '" . $cid . "'");
    else if ($method == "down") $upd = nkDB_execute("UPDATE " . DOWNLOAD_CAT_TABLE . " SET position = position + 1 WHERE cid = '" . $cid . "'");

    saveUserAction(_ACTIONPOSMODIFCATDL .': '. $titre);

    printNotification(_CATMODIF, 'success');
    redirect("index.php?file=Download&page=admin&op=main_cat", 2);
}

function nkAdminMenu($tab = 1)
{
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Download&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _DOWNLOAD; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Download&amp;page=admin&amp;op=add_file">
                    <img src="modules/Admin/images/icons/add_page.png" alt="icon" />
                    <span><?php echo _ADDFILE; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Download&amp;page=admin&amp;op=main_cat">
                    <img src="modules/Admin/images/icons/folder_full.png" alt="icon" />
                    <span><?php echo _CATMANAGEMENT; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 4 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Download&amp;page=admin&amp;op=main_broken">
                    <img src="modules/Admin/images/icons/pages_warning.png" alt="icon" />
                    <span><?php echo _BROKENLINKS; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 5 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Download&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($GLOBALS['op']) {
    case "edit_file":
        edit_file($_REQUEST['did']);
        break;

    case "add_file":
        add_file();
        break;

    case "del_file":
        del_file($_REQUEST['did']);
        break;

    case "send_file":
        send_file();
        break;

    case "modif_file":
        modif_file();
        break;

    case "main":
        main();
        break;

    case "send_cat":
        send_cat($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['parentid'], $_REQUEST['level'], $_REQUEST['position']);
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
        modif_cat($_REQUEST['cid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['parentid'], $_REQUEST['level'], $_REQUEST['position']);
        break;

    case "del_cat":
        del_cat($_REQUEST['cid']);
        break;

    case "main_broken":
        main_broken();
        break;

    case "del_broke":
        del_broke($_REQUEST['did']);
        break;

    case "del_broken":
        del_broken();
        break;

    case "main_pref":
        main_pref();
        break;

    case "change_pref":
        change_pref($_REQUEST['max_download'], $_REQUEST['hide_download']);
        break;

    case "modif_position":
        modif_position($_REQUEST['cid'], $_REQUEST['method']);
        break;

    default:
        main();
        break;
}

?>