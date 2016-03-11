<?php
/**
 * admin.php
 *
 * Backend of Gallery module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Gallery'))
    return;


function add_screen()
{
    global $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADDSCREEN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(2);

    echo "<form method=\"post\" action=\"index.php?file=Gallery&amp;page=admin&amp;op=send_screen\" enctype=\"multipart/form-data\" onsubmit=\"backslash('img_texte');\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"	
    . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"44\" /></td></tr>\n"
    . "<tr><td><b>" . _CAT . "</b>: <select name=\"cat\">\n";

    select_cat();

    echo "</select></td></tr><tr><td><b>" . __('AUTHOR') . " :</b> <input type=\"text\" name=\"auteur\" size=\"30\" /></td></tr>\n";

    echo "</td></tr><tr><td><b>" . _DESCR . " :</b></td></tr>\n"
    . "<tr><td><textarea class=\"editor\" id=\"img_texte\" name=\"description\" cols=\"66\" rows=\"10\"></textarea></td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><b>" . _URLIMG . " :</b> <input type=\"text\" name=\"url\" size=\"50\" maxlength=\"200\" value=\"http://\" /></td></tr>\n"
    . "<tr><td><b>" . _UPIMG . " :</b><br /><input type=\"file\" name=\"fichiernom\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" />&nbsp;" . __('OVERWRITE') . "</td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><b>" . _URLIMG2 . " :</b> <input type=\"text\" name=\"url2\" size=\"46\" maxlength=\"200\" value=\"http://\" /></td></tr>\n"
    . "<tr><td><b>" . _URLFILE . " :</b> <input type=\"text\" name=\"url_file\" size=\"51\" maxlength=\"200\" value=\"http://\" /></td></tr>\n"
    . "<tr><td>&nbsp;</td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _ADDSCREEN . "\" /><a class=\"buttonLink\" href=\"index.php?file=Gallery&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function send_screen($titre, $description, $auteur, $cat, $url, $url2, $url_file)
{
    global $nuked, $user;

    require_once 'Includes/nkUpload.php';
    include("modules/Gallery/config.php");

    if ($url == "http://") $url = "";
    if ($url2 == "http://") $url2 = "";
    if ($url_file == "http://") $url_file = "";

    if ($_FILES['fichiernom']['name'] == '' && $url == '') {
        printNotification(_SPECIFY, 'error');
        redirect('index.php?file=Gallery&page=admin&op=add_screen', 3);
        return;
    }

    //Upload du fichier
    if ($_FILES['fichiernom']['name'] != '') {
        $imageCfg = array(
            'allowedExtension'  => array('jpg', 'jpeg', 'png', 'gif'),
            'uploadDir'         => $rep_img
        );

        if (isset($_POST['ecrase_screen']) && $_POST['ecrase_screen'] == 1)
            $imageCfg['overwrite'] = true;
        else
            $imageCfg['overwrite'] = false;

        list($url, $uploadError, $imageExt) = nkUpload_check('fichiernom', $imageCfg);

        if ($uploadError !== false) {
            if ($uploadError == __('FILE_ALREADY_EXIST')) {
                printNotification(
                    $uploadError . '<br />'. __('REPLACE_FILE'),
                    'warning',
                    array('backLinkUrl' => 'javascript:history.back()')
                );
            }
            else {
                printNotification($uploadError, 'error');
                redirect('index.php?file=Gallery&page=admin&op=add_screen', 3);
            }

            return;
        }
    }

    if ($url != '') {
        if ($url2 == ''
            && $image_gd == 'on'
            && @extension_loaded('gd')
            && stripos($url, 'http://') === false
            && is_file($url)
        ) {
            $imgInfo = @getimagesize($url);

            if ($imgInfo && $imgInfo[0] > $img_screen1) {
                $filename = substr(strrchr($url, '/'), 1);
                $f = explode('.', $filename);
                $ext = array_pop($f);
                $file_name = implode('.', $f);

                if ($imgInfo[2] == IMAGETYPE_JPEG) {
                    if (! in_array($ext, array('jpg', 'jpeg'))) $ext = 'jpg';

                    $src = @imagecreatefromjpeg($url);
                }
                else if ($imgInfo[2] == IMAGETYPE_GIF) {
                    if ($ext != 'gif') $ext = 'gif';

                    $src = @imagecreatefromgif($url);
                }
                else if ($imgInfo[2] == IMAGETYPE_PNG) {
                    if ($ext != 'png') $ext = 'png';

                    $src = @imagecreatefrompng($url);
                }
                else if ($imgInfo[2] == IMAGETYPE_BMP) {// TODO : Or IMAGETYPE_WBMP ?
                    if ($ext != 'bmp') $ext = 'bmp';

                    // http://php.net/manual/fr/function.imagecreatefromwbmp.php
                    $src = @imagecreatefromwbmp($url);
                }

                $height = round(($img_screen1 / $imgInfo[0]) * $imgInfo[1]);

                $img = @imagecreatetruecolor($img_screen1, $height);

                if (! $img) $img = @imagecreate($img_screen1, $height);

                @imagecopyresampled($img, $src, 0, 0, 0, 0, $img_screen1, $height, $imgInfo[0], $imgInfo[1]);

                $miniature = $rep_img_gd . $file_name .'_tmb.'. $ext;

                if (is_file($miniature))
                    $miniature = $rep_img_gd . time() . $file_name .'_tmb.'. $ext;

                if ($imgInfo[2] == IMAGETYPE_JPEG) @ImageJPEG($img, $miniature);
                if ($imgInfo[2] == IMAGETYPE_PNG) @ImagePNG($img, $miniature);
                if ($imgInfo[2] == IMAGETYPE_PNG) @imagewbmp($img, $miniature);

                if ($imgInfo[2] == IMAGETYPE_GIF && @function_exists('imagegif'))
                    @ImageGIF($img, $miniature);
                else
                    @ImageJPEG($img, $miniature);

                if (is_file($miniature)) $url2 = $miniature;
            }
        }

        $titre = nkDB_realEscapeString(stripslashes($titre));
        $description = nkHtmlEntityDecode($description);
        $description = nkDB_realEscapeString(stripslashes($description));
        $auteur = nkDB_realEscapeString(stripslashes($auteur));
        $date = time();

        $sql = nkDB_execute("INSERT INTO " . GALLERY_TABLE . " ( `sid` , `titre` , `description` , `url` , `url2` , `url_file` , `cat` , `date` , `autor` ) VALUES ( '' , '" . $titre . "' , '" . $description . "' , '" . $url . "' , '" . $url2 . "' , '" . $url_file . "' , '" . $cat . "' , '" . $date . "' , '" . $auteur . "')");

        saveUserAction(_ACTIONADDGAL .': '. $titre);

        printNotification(_SCREENADD, 'success');

        $sqls = nkDB_execute("SELECT sid FROM " . GALLERY_TABLE . " WHERE date = '" . $date . "' AND titre='" . $titre . "'");
        list($sid) = nkDB_fetchArray($sqls);

        setPreview('index.php?file=Gallery&op=description&sid='. $sid .'&orderby=news', 'index.php?file=Gallery&page=admin');
    }
}

function del_screen($sid)
{
    global $nuked, $user;

    $sqls = nkDB_execute("SELECT titre FROM " . GALLERY_TABLE . " WHERE sid = '" . $sid . "'");
    list($titre) = nkDB_fetchArray($sqls);
    $titre = nkDB_realEscapeString($titre);
    $sql = nkDB_execute("DELETE FROM " . GALLERY_TABLE . " WHERE sid = '" . $sid . "'");
    $del_com = nkDB_execute("DELETE FROM " . COMMENT_TABLE . " WHERE im_id = '" . $sid . "' AND module = 'Gallery'");
    $del_vote = nkDB_execute("DELETE FROM " . VOTE_TABLE . " WHERE vid = '" . $sid . "' AND module = 'Gallery'");

    saveUserAction(_ACTIONDELGAL .': '. $titre);

    printNotification(_SCREENDEL, 'success');
    redirect("index.php?file=Gallery&page=admin", 1);
}

function modif_img($sid, $titre, $description, $auteur, $cat, $url, $url2, $url_file)
{
    global $nuked, $user;

    require_once 'Includes/nkUpload.php';
    include("modules/Gallery/config.php");

    if ($_FILES['fichiernom']['name'] == '' && $url == '') {
        printNotification(_SPECIFY, 'error');
        redirect('index.php?file=Gallery&page=admin&op=edit_screen&sid='. $sid, 3);
        return;
    }

    //Upload du fichier
    if ($_FILES['fichiernom']['name'] != '') {
        $imageCfg = array(
            'allowedExtension'  => array('jpg', 'jpeg', 'png', 'gif'),
            'uploadDir'         => $rep_img
        );

        if (isset($_POST['ecrase_screen']) && $_POST['ecrase_screen'] == 1)
            $imageCfg['overwrite'] = true;
        else
            $imageCfg['overwrite'] = false;

        list($url, $uploadError, $imageExt) = nkUpload_check('fichiernom', $imageCfg);

        if ($uploadError !== false) {
            if ($uploadError == __('FILE_ALREADY_EXIST')) {
                printNotification(
                    $uploadError . '<br />'. __('REPLACE_FILE'),
                    'warning',
                    array('backLinkUrl' => 'javascript:history.back()')
                );
            }
            else {
                printNotification($uploadError, 'error');
                redirect('index.php?file=Gallery&page=admin&op=edit_screen&sid='. $sid, 3);
            }

            return;
        }
    }

    if ($url != '') {
        if ($url2 == ''
            && $image_gd == 'on'
            && @extension_loaded('gd')
            && stripos($url, 'http://') === false
            && is_file($url)
        ) {
            $imgInfo = @getimagesize($url);

            if ($imgInfo && $imgInfo[0] > $img_screen1) {
                $filename = substr(strrchr($url, '/'), 1);
                $f = explode('.', $filename);
                $ext = array_pop($f);
                $file_name = implode('.', $f);

                if ($imgInfo[2] == IMAGETYPE_JPEG) {
                    if (! in_array($ext, array('jpg', 'jpeg'))) $ext = 'jpg';

                    $src = @imagecreatefromjpeg($url);
                }
                else if ($imgInfo[2] == IMAGETYPE_GIF) {
                    if ($ext != 'gif') $ext = 'gif';

                    $src = @imagecreatefromgif($url);
                }
                else if ($imgInfo[2] == IMAGETYPE_PNG) {
                    if ($ext != 'png') $ext = 'png';

                    $src = @imagecreatefrompng($url);
                }
                else if ($imgInfo[2] == IMAGETYPE_BMP) {// TODO : Or IMAGETYPE_WBMP ?
                    if ($ext != 'bmp') $ext = 'bmp';

                    // http://php.net/manual/fr/function.imagecreatefromwbmp.php
                    $src = @imagecreatefromwbmp($url);
                }

                $height = round(($img_screen1 / $imgInfo[0]) * $imgInfo[1]);

                $img = @imagecreatetruecolor($img_screen1, $height);

                if (! $img) $img = @imagecreate($img_screen1, $height);

                @imagecopyresampled($img, $src, 0, 0, 0, 0, $img_screen1, $height, $imgInfo[0], $imgInfo[1]);

                $miniature = $rep_img_gd . $file_name .'_tmb.'. $ext;

                if (is_file($miniature))
                    $miniature = $rep_img_gd . time() . $file_name .'_tmb.'. $ext;

                if ($imgInfo[2] == IMAGETYPE_JPEG) @ImageJPEG($img, $miniature);
                if ($imgInfo[2] == IMAGETYPE_PNG) @ImagePNG($img, $miniature);
                if ($imgInfo[2] == IMAGETYPE_PNG) @imagewbmp($img, $miniature);

                if ($imgInfo[2] == IMAGETYPE_GIF && @function_exists('imagegif'))
                    @ImageGIF($img, $miniature);
                else
                    @ImageJPEG($img, $miniature);

                if (is_file($miniature)) $url2 = $miniature;
            }
        }

        $titre = nkDB_realEscapeString(stripslashes($titre));
        $description = nkHtmlEntityDecode($description);
        $description = nkDB_realEscapeString(stripslashes($description));
        $auteur = nkDB_realEscapeString(stripslashes($auteur));

        $sql = nkDB_execute("UPDATE " . GALLERY_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "', autor = '" . $auteur . "', url = '" . $url . "', url2 = '" . $url2 . "', url_file = '" . $url_file . "', cat = '" . $cat . "' WHERE sid = '" . $sid . "'");

        saveUserAction(_ACTIONMODIFGAL .': '. $titre);

        printNotification(_SCREENMODIF, 'success');
        setPreview('index.php?file=Gallery&op=description&sid='. $sid .'&orderby=news', 'index.php?file=Gallery&page=admin');
    }
}

function main()
{
    global $nuked, $language;

    $nb_img_guest = 30;

    $sql3 = nkDB_execute("SELECT sid FROM " . GALLERY_TABLE);
    $count = nkDB_numRows($sql3);

    if(array_key_exists('p', $_REQUEST)){
        $page = $_REQUEST['p'];
    }
    else{
        $page = 1;
    }
    $start = $page * $nb_img_guest - $nb_img_guest;

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
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(1);

    if(!array_key_exists('orderby', $_REQUEST)){
        $_REQUEST['orderby'] = '';
    }

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

    $sql = nkDB_execute("SELECT G.sid, G.titre, G.cat, G.url, G.date, GC.parentid, GC.titre FROM " . GALLERY_TABLE . " AS G LEFT JOIN " . GALLERY_CAT_TABLE . " AS GC ON GC.cid = G.cat ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_img_guest."");
    while (list($sid, $titre, $cat, $url, $date, $parentid, $namecat) = nkDB_fetchArray($sql))
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
            $sql3 = nkDB_execute("SELECT titre FROM " . GALLERY_CAT_TABLE . " WHERE cid = '" . $parentid . "' ORDER BY position, titre");
            list($parentcat) = nkDB_fetchArray($sql3);
            $categorie = "$parentcat -> $namecat";
            $categorie = printSecuTags($categorie);
        }

        echo "<tr>\n"
        . "<td style=\"width: 20%;\"><a href=\"" . $url . "\" title=\"" . $url . "\" onclick=\"window.open(this.href); return false;\">" . $titre . "</a></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td>\n"
        . "<td style=\"width: 30%;\" align=\"center\">" . $categorie . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Gallery&amp;page=admin&amp;op=edit_screen&amp;sid=" . $sid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISSCREEN . "\" /></a></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_img('" . addslashes($titre) . "', '" . $sid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISSCREEN . "\" /></a></td></tr>\n";
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

    echo "<br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function edit_screen($sid)
{
    global $nuked, $language;

    include("modules/Gallery/config.php");

    $sql = nkDB_execute("SELECT cat, titre, description, autor, url, url2, url_file FROM " . GALLERY_TABLE . " WHERE sid = '" . $sid . "'");
    list($cat, $titre, $description, $autor, $url, $url2, $url_file) = nkDB_fetchArray($sql);

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
        $sql2 = nkDB_execute("SELECT titre FROM " . GALLERY_CAT_TABLE . " WHERE cid = '" . $cat . "'");
        list($cat_name) = nkDB_fetchArray($sql2);
        $cat_name = printSecuTags($cat_name);
    }
    else
    {
        $cat_name = _NONE;
    }

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _EDITTHISSCREEN . "</h3>\n"
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

    echo "</select></td></tr><tr><td><b>" . __('AUTHOR') . " :</b> <input type=\"text\" name=\"auteur\" size=\"30\" value=\"" . $autor . "\" /></td></tr>\n";

    echo "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
    . "<tr><td><textarea class=\"editor\" id=\"img_texte\" name=\"description\" cols=\"66\" rows=\"10\" onselect=\"storeCaret('img_texte');\" onclick=\"storeCaret('img_texte');\" onkeyup=\"storeCaret('img_texte');\">" . $description . "</textarea></td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><b>" . _URLIMG . " :</b> <input type=\"text\" name=\"url\" size=\"50\" maxlength=\"200\" value=\"" . $url . "\" /></td></tr>\n"
    . "<tr><td><b>" . _UPIMG . " :</b><br /><input type=\"file\" name=\"fichiernom\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" />&nbsp;" . __('OVERWRITE') . "</td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><b>" . _URLIMG2 . " :</b> <input type=\"text\" name=\"url2\" size=\"46\" maxlength=\"200\" value=\"" . $url2 . "\" /></td></tr>\n"
    . "<tr><td><b>" . _URLFILE . " :</b> <input type=\"text\" name=\"url_file\" size=\"51\" maxlength=\"200\" value=\"" . $url_file . "\" /></td></tr>\n"
    . "<tr><td>&nbsp;<input type=\"hidden\" name=\"sid\" value=\"" . $sid . "\" /></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISSCREEN . "\" /><a class=\"buttonLink\" href=\"index.php?file=Gallery&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
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
    . "<div class=\"content-box-header\"><h3>" . _CATMANAGEMENT . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(3);

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
    . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CATPARENT . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT cid, titre, parentid, position FROM " . GALLERY_CAT_TABLE . " ORDER BY parentid, position");
    $nbcat = nkDB_numRows($sql);

    if ($nbcat > 0)
    {
        while (list($cid, $titre, $parentid, $position) = nkDB_fetchArray($sql))
        {
            $titre = printSecuTags($titre);

            echo "<tr>\n"
            . "<td style=\"width: 35%;\" align=\"center\">" . $titre . "</td>\n"
            . "<td style=\"width: 35%;\" align=\"center\">\n";

            if ($parentid > 0)
            {
                $sql2 = nkDB_execute("SELECT titre FROM " . GALLERY_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
                list($pnomcat) = nkDB_fetchArray($sql2);
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
            . "<td align=\"center\"><a href=\"javascript:delcat('" . addslashes($titre) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
        }
    }
    else
    {
        echo "<tr><td align=\"center\" colspan=\"5\">" . _NONE . "&nbsp;" . _CAT . "&nbsp;" . _INDATABASE . "</td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Gallery&amp;page=admin&amp;op=add_cat\">" . _ADDCAT . "</a><a class=\"buttonLink\" href=\"index.php?file=Gallery&amp;page=admin\">" . __('BACK') . "</a></div>\n"
    . "<br /></div></div>\n";
}

function add_cat()
{
    global $language, $nuked;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADDCAT . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Gallery&amp;page=admin&amp;op=send_cat\">\n"
    . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
    . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\"><option value=\"0\">" . _NONE . "</option>\n";

    $sql = nkDB_execute("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($cid, $nomcat) = nkDB_fetchArray($sql))
    {
        $nomcat = printSecuTags($nomcat);

        echo "<option value=\"" . $cid . "\">" . $nomcat . "</option>\n";
    }

    echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"0\" /></td></tr>\n"
    . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
    . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _CREATECAT . "\" /><a class=\"buttonLink\" href=\"index.php?file=Gallery&amp;page=admin&amp;op=main_cat\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function send_cat($titre, $description, $parentid, $position)
{
    global $nuked, $user;

    $titre = nkDB_realEscapeString(stripslashes($titre));

    if (empty($titre))
    {
        printNotification(_TITLECATFORGOT, 'error');
        redirect("index.php?file=Gallery&page=admin&op=main_cat", 4);
    }
    else
    {
        $description = nkHtmlEntityDecode($description);
        $description = nkDB_realEscapeString(stripslashes($description));

        $sql = nkDB_execute("INSERT INTO " . GALLERY_CAT_TABLE . " ( `parentid` , `titre` , `description` , `position` ) VALUES ('" . $parentid . "', '" . $titre . "', '" . $description . "', '" . $position . "')");

        saveUserAction(_ACTIONADDCATGAL .': '. $titre);

        printNotification(_CATADD, 'success');

        $sqlq = nkDB_execute("SELECT cid FROM " . GALLERY_CAT_TABLE . " WHERE parentid='".$parentid."' AND titre='".$titre."'");
        list($cid) = nkDB_fetchArray($sqlq);

        setPreview('index.php?file=Gallery&op=categorie&cat='. $cid, 'index.php?file=Gallery&page=admin&op=main_cat');
    }
}

function edit_cat($cid)
{
    global $nuked, $language;

    $sql = nkDB_execute("SELECT titre, description, parentid, position FROM " . GALLERY_CAT_TABLE . " WHERE cid='".$cid."'");
    list($titre, $description, $parentid, $position) = nkDB_fetchArray($sql);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _EDITTHISCAT . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Gallery&amp;page=admin&amp;op=modif_cat\">\n"
    . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
    . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre . "\" /></td></tr>\n"
    . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\">\n";

    if ($parentid > 0)
    {
        $sql2 = nkDB_execute("SELECT titre FROM " . GALLERY_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
        list($pnomcat) = nkDB_fetchArray($sql2);
        $pnomcat = printSecuTags($pnomcat);

        echo "<option value=\"" . $parentid . "\">" . $pnomcat . "</option>\n";
    }

    echo "<option value=\"0\">" . _NONE . "</option>\n";

    $sql3 = nkDB_execute("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($catid, $nomcat) = nkDB_fetchArray($sql3))
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
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISCAT . "\" /><a class=\"buttonLink\" href=\"index.php?file=Gallery&amp;page=admin&amp;op=main_cat\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function modif_cat($cid, $titre, $description, $parentid, $position)
{
    global $nuked, $user;

    $titre = nkDB_realEscapeString(stripslashes($titre));

    if (empty($titre))
    {
        printNotification(_TITLEARTFORGOT, 'error');
        redirect("index.php?file=Gallery&page=admin&op=main_cat", 4);
    }
    else
    {
        $description = nkHtmlEntityDecode($description);
        $description = nkDB_realEscapeString(stripslashes($description));

        $sql = nkDB_execute("UPDATE " . GALLERY_CAT_TABLE . " SET parentid = '" . $parentid . "', titre = '" . $titre . "', description = '" . $description . "', position = '" . $position . "' WHERE cid = '" . $cid . "'");

        saveUserAction(_ACTIONMODIFCATGAL .': '. $titre);

        printNotification(_CATMODIF, 'success');
        setPreview('index.php?file=Gallery&op=categorie&cat='. $cid, 'index.php?file=Gallery&page=admin&op=main_cat');
    }
}

function select_cat()
{
    global $nuked;

    $sql = nkDB_execute("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($cid, $titre) = nkDB_fetchArray($sql))
    {
        $titre = printSecuTags($titre);

        echo "<option value=\"" . $cid . "\">* " . $titre . "</option>\n";

        $sql2 = nkDB_execute("SELECT cid, titre FROM " . GALLERY_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre");
        while (list($s_cid, $s_titre) = nkDB_fetchArray($sql2))
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

    $sqlq = nkDB_execute("SELECT titre FROM " . GALLERY_CAT_TABLE . " WHERE cid='".$cid."'");
    list($titre) = nkDB_fetchArray($sqlq);
    $titre = nkDB_realEscapeString($titre);
    $sql = nkDB_execute("DELETE FROM " . GALLERY_CAT_TABLE . " WHERE cid = '" . $cid . "'");
    $sql = nkDB_execute("UPDATE " . GALLERY_CAT_TABLE . " SET parentid = 0 WHERE parentid = '" . $cid . "'");
    $sql = nkDB_execute("UPDATE " . GALLERY_TABLE . " SET cat = 0 WHERE cat = '" . $cid . "'");

    saveUserAction(_ACTIONDELCATGAL .': '. $titre);

    printNotification(_CATDEL, 'success');
    redirect("index.php?file=Gallery&page=admin&op=main_cat", 2);
}

function main_pref()
{
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Gallery.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(4);

    echo "<form method=\"post\" action=\"index.php?file=Gallery&amp;page=admin&amp;op=change_pref\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
    . "<tr><td>" . _GALLERYTITLE . " : </td><td> <input type=\"text\" name=\"gallery_title\" size=\"40\" value=\"" . $nuked['gallery_title']. "\" /></td></tr>\n"
    . "<tr><td>" . _NUMBERIMG . " : </td><td><input type=\"text\" name=\"max_img\" size=\"2\" value=\"" . $nuked['max_img'] . "\" /></td></tr>\n"
    . "<tr><td>" . _NUMBERIMG2 . " : </td><td><input type=\"text\" name=\"max_img_line\" size=\"2\" value=\"" . $nuked['max_img_line'] . "\" /></td></tr>\n"
    . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Gallery&amp;page=admin\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function change_pref($gallery_title, $max_img, $max_img_line)
{
    global $nuked, $user;

    $upd1 = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $gallery_title . "' WHERE name = 'gallery_title'");
    $upd2 = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_img . "' WHERE name = 'max_img'");
    $upd3 = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_img_line . "' WHERE name = 'max_img_line'");

    saveUserAction(_ACTIONPREFGAL .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect("index.php?file=Gallery&page=admin", 2);
}

function modif_position($cid, $method)
{
    global $nuked, $user;

    $sqlq = nkDB_execute("SELECT titre, position FROM " . GALLERY_CAT_TABLE . " WHERE cid='".$cid."'");
    list($titre, $position) = nkDB_fetchArray($sqlq);
    if ($position <=0 AND $method == "up")
    {
        printNotification(_CATERRORPOS, 'error');
        redirect("index.php?file=Gallery&page=admin&op=main_cat", 2);
        exit();
    }
    if ($method == "up") $upd = nkDB_execute("UPDATE " . GALLERY_CAT_TABLE . " SET position = position - 1 WHERE cid = '" . $cid . "'");
    else if ($method == "down") $upd = nkDB_execute("UPDATE " . GALLERY_CAT_TABLE . " SET position = position + 1 WHERE cid = '" . $cid . "'");

    saveUserAction(_ACTIONPOSCATGAL .': '. $titre);

    printNotification(_CATMODIF, 'success');
    redirect("index.php?file=Gallery&page=admin&op=main_cat", 2);
}

function nkAdminMenu($tab = 1) {
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Gallery&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _GALLERY; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Gallery&amp;page=admin&amp;op=add_screen">
                    <img src="modules/Admin/images/icons/add_image.png" alt="icon" />
                    <span><?php echo _ADDSCREEN; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Gallery&amp;page=admin&amp;op=main_cat">
                    <img src="modules/Admin/images/icons/folder_full.png" alt="icon" />
                    <span><?php echo _CATMANAGEMENT; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 4 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Gallery&amp;page=admin&amp;op=main_pref">
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
    case "add_screen":
        add_screen();
        break;

    case "del_screen":
        del_screen($_REQUEST['sid']);
        break;

    case "send_screen":
        send_screen($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['auteur'], $_REQUEST['cat'], $_REQUEST['url'], $_REQUEST['url2'], $_REQUEST['url_file']);
        break;

    case "edit_screen":
        edit_screen($_REQUEST['sid']);
        break;

    case "modif_img":
        modif_img($_REQUEST['sid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['auteur'], $_REQUEST['cat'], $_REQUEST['url'], $_REQUEST['url2'], $_REQUEST['url_file']);
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

?>
