<?php
/**
 * admin.php
 *
 * Backend of Sections module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Sections'))
    return;


function main(){
    global $nuked, $language;

    $nb_max = 30;

    $sql3 = mysql_query("SELECT artid FROM " . SECTIONS_TABLE);
    $nb_art = mysql_num_rows($sql3);

    if(array_key_exists('p', $_REQUEST)){
        $page = $_REQUEST['p'];
    }
    else{
        $page = 1;
    }
    $start = $page * $nb_max - $nb_max;

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function del_art(titre, id)\n"
            . "{\n"
            . "if (confirm('" . _DELETEART . " '+titre+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Sections&page=admin&op=del&artid='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADMINSECTIONS . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(1);

            echo "</div>\n";

    if(!array_key_exists('orderby', $_REQUEST)){
        $_REQUEST['orderby'] = '';
    }

    if ($_REQUEST['orderby'] == "date"){
        $order_by = "S.artid DESC";
    }
    else if ($_REQUEST['orderby'] == "name"){
        $order_by = "S.title";
    }
    else if ($_REQUEST['orderby'] == "author"){
        $order_by = "S.autor";
    }
    else if ($_REQUEST['orderby'] == "cat"){
        $order_by = "SC.secname, SC.parentid";
    }
    else{
        $order_by = "S.artid DESC";
    }

    echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">\n"
            . "<tr><td align=\"right\">" . _ORDERBY . " : ";

    if ($_REQUEST['orderby'] == "date" || !$_REQUEST['orderby']){
        echo "<b>" . _DATE . "</b> | ";
    }
    else{
        echo "<a href=\"index.php?file=Sections&amp;page=admin&amp;orderby=date\">" . _DATE . "</a> | ";
    }

    if ($_REQUEST['orderby'] == "name"){
        echo "<b>" . _TITLE . "</b> | ";
    }
    else{
        echo "<a href=\"index.php?file=Sections&amp;page=admin&amp;orderby=name\">" . _TITLE . "</a> | ";
    }

    if ($_REQUEST['orderby'] == "author"){
        echo "<b>" . __('AUTHOR') . "</b> | ";
    }
    else{
        echo "<a href=\"index.php?file=Sections&amp;page=admin&amp;orderby=author\">" . __('AUTHOR') . "</a> | ";
    }

    if ($_REQUEST['orderby'] == "cat"){
        echo "<b>" . _CAT . "</b>";
    }
    else{
        echo "<a href=\"index.php?file=Sections&amp;page=admin&amp;orderby=cat\">" . _CAT . "</a>";
    }

    echo "&nbsp;</td></tr></table>\n";

    if ($nb_art > $nb_max){
        echo "<div>";
        $url = "index.php?file=Sections&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
        number($nb_art, $nb_max, $url);
        echo "</div>\n";
    }

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr>\n"
            . "<td style=\"width: 25%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
            . "<td style=\"width: 25%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><b>" . __('AUTHOR') . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = mysql_query("SELECT S.artid, S.title, S.autor, S.autor_id, S.secid, S.date, SC.parentid, SC.secname FROM " . SECTIONS_TABLE . " AS S LEFT JOIN " . SECTIONS_CAT_TABLE . " AS SC ON SC.secid = S.secid ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_max."");
    while (list($art_id, $titre, $autor, $autor_id, $cat, $date, $parentid, $namecat) = mysql_fetch_row($sql)){

        if ($date) $date = nkDate($date);

        if($autor==""){
            $autor = "N/A";
        }

        if ($autor_id != ""){
            $sql4 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
            $test = mysql_num_rows($sql4);
        }

        if ($autor_id != "" && $test > 0){
            list($auteur) = mysql_fetch_array($sql4);
        }
        else{
            $auteur = $autor;
        }

        if ($cat == 0){
            $categorie = _NONE;
        }
        else if ($parentid == 0){
            $categorie = printSecuTags($namecat);
        }
        else{
            $sql3 = mysql_query("SELECT secname FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $parentid . "'");
            list($parentcat) = mysql_fetch_array($sql3);
            $categorie = $parentcat . "->" . $namecat;
            $categorie = printSecuTags($categorie);
        }

        if (strlen($titre) > 25){
            $title = "<span style=\"cursor: hand\" title=\"" . printSecuTags($titre) . "\">" . printSecuTags(substr($titre, 0, 25)) . "...</span>";
        }
        else{
            $title = printSecuTags($titre);
        }

        echo "<tr>\n"
                . "<td style=\"width: 25%;\">" . $title . "</td>\n"
                . "<td style=\"width: 20%;\" align=\"center\">" . $categorie . "</td>\n"
                . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td>\n"
                . "<td style=\"width: 20%;\" align=\"center\">" . $auteur . "</td>\n"
                . "<td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Sections&amp;page=admin&amp;op=edit&amp;artid=" . $art_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISART . "\" /></a></td>\n"
                . "<td style=\"width: 10%;\" align=\"center\"><a href=\"javascript:del_art('" . addslashes($titre) . "', '" . $art_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISART . "\" /></a></td></tr>\n";
    }

    if ($nb_art == 0){
        echo "<tr><td colspan=\"6\" align=\"center\">" . _NOARTINDB . "</td></tr>\n";
    }

    echo "</table>\n";

    if ($nb_art > $nb_max){
        echo "<div>";
        $url = "index.php?file=Sections&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
        number($nb_art, $nb_max, $url);
        echo "</div>\n";
    }

    echo "<br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>";
}

function add(){
    global $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADDART . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(2);

            echo "</div>\n"
            . "<form method=\"post\" action=\"index.php?file=Sections&amp;page=admin&amp;op=do_add\" onsubmit=\"backslash('art_texte');\" enctype=\"multipart/form-data\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
            . "<tr><td><b>" . _TITLE . " :</b>&nbsp;<input id=\"art_titre\" type=\"text\" name=\"titre\" maxlength=\"100\" size=\"45\" /></td></tr>\n"
            . "<tr><td><b>" . _IMAGE . " :</b> <input type=\"text\" name=\"urlImage\" size=\"42\" /></td></tr>\n"
            . "<tr><td><b>" . _UPLOADIMAGE . " :</b> <input type=\"file\" name=\"upImage\" /></td></tr>\n"
            . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\">\n";

    select_art_cat();

    echo "</select></td></tr>\n";

    echo "<tr><td><b>" . _TEXT . " :</b><br /><textarea class=\"editor\" class=\"editor\" id=\"art_texte\" name=\"texte\" cols=\"70\" rows=\"15\"></textarea></td></tr>\n"
            . "</table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" name=\"Submit\" value=\"" . _ADDART . "\" /><a class=\"buttonLink\" href=\"index.php?file=Sections&amp;page=admin\">" . __('BACK') . "</a>"
            . "</div>\n"
            . "</form><br /></div></div>\n";
}

function do_add($titre, $texte, $cat, $urlImage, $upImage){
    global $nuked, $user;

    $titre = mysql_real_escape_string(stripslashes($titre));

    if (empty($titre)){
        printNotification(_TITLEARTFORGOT, 'error');
        redirect("index.php?file=Sections&page=admin&op=add", 4);
    }
    else {
        $texte = secu_html(nkHtmlEntityDecode($texte));
        $texte = mysql_real_escape_string(stripslashes($texte));
        $date = time();
        $auteur = $user[2];
        $auteur_id = $user[0];

        //Upload du fichier
        $filename = $_FILES['upImage']['name'];
        if ($filename != "") {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
                $url_image = "upload/Sections/" . $filename;
                if (! move_uploaded_file($_FILES['upImage']['tmp_name'], $url_image)) {
                    printNotification(_UPLOADFILEFAILED, 'error');
                    redirect('index.php?file=Sections&page=admin&op=add', 2);
                    return;
                }
                @chmod ($url_image, 0644);
            }
            else {
                printNotification(_NOIMAGEFILE, 'error');
                redirect('index.php?file=Sections&page=admin&op=add', 2);
                return;
            }
        }
        else {
            $url_image = $urlImage;
        }

        $sql = mysql_query("INSERT INTO " . SECTIONS_TABLE . " ( `artid` , `secid` , `title` , `content` , `coverage` , `autor` , `autor_id`, `counter` , `date`) VALUES ( '' , '" . $cat . "' , '" . $titre . "' , '" . $texte . "' , '" . $url_image . "' , '" . $auteur . "' , '" . $auteur_id . "' , '' , '" . $date . "' )");

        saveUserAction(_ACTIONADDSEC .': '. $titre .'.');

        printNotification(_ARTADD, 'success');

        $sql2 = mysql_query("SELECT artid FROM " . SECTIONS_TABLE . " WHERE title = '" . $titre . "' AND date='".$date."'");
        list($artid) = mysql_fetch_array($sql2);

        setPreview('index.php?file=Sections&op=article&artid='. $artid, 'index.php?file=Sections&page=admin');
    }
}

function edit($art_id){
    global $nuked, $language;

    $sql = mysql_query("SELECT title, content, coverage, secid FROM " . SECTIONS_TABLE . " WHERE artid = '" . $art_id . "'");
    list($titre, $texte, $coverage, $cat) = mysql_fetch_array($sql);
    $titre = printSecuTags($titre);

    if ($cat == 0 || !$cat){
        $cid = 0;
        $categorie = _NONE;
    }
    else{
        $cid = $cat;
        $sql2 = mysql_query("SELECT secid, secname FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $cat . "'");
        list($cid, $categorie) = mysql_fetch_array($sql2);
        $categorie = printSecuTags($categorie);
    }

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _EDITTHISART . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Sections&amp;page=admin&amp;op=do_edit\" onsubmit=\"backslash('art_texte');\" enctype=\"multipart/form-data\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
            . "<tr><td><b>" . _TITLE . " :</b>&nbsp;<input id=\"art_titre\" type=\"text\" name=\"titre\" maxlength=\"100\" size=\"45\" value=\"" . $titre . "\" /></td></tr>\n"
            . "<tr><td><b>" . _IMAGE . " :</b> <input type=\"text\" name=\"urlImage\" value=\"" . $coverage . "\" size=\"42\" />\n";

            if ($coverage != ""){
                echo "<img src=\"" . $coverage . "\" title=\"" . $titre . "\" style=\"margin-left:20px; width:300px; height:auto; vertical-align:middle;\" />\n";
            }

            echo "</td></tr>\n"
            . "<tr><td><b>" . _UPLOADIMAGE . " :</b> <input type=\"file\" name=\"upImage\" /></td></tr>\n"
            . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"" . $cid . "\">" . $categorie . "</option>\n";

    select_art_cat();

    $texte = editPhpCkeditor($texte);

    echo "</select></td></tr>\n";

    echo "<tr><td><b>" . _TEXT . " :</b><br /><textarea class=\"editor\" id=\"art_texte\" name=\"texte\" cols=\"70\" rows=\"15\" >" . $texte . "</textarea></td></tr>\n"
            . "<tr><td>&nbsp;<input type=\"hidden\" name=\"artid\" value=\"" . $art_id . "\" /></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" name=\"Submit\" value=\"" . _MODIFTHISART . "\" /><a class=\"buttonLink\" href=\"index.php?file=Sections&amp;page=admin\">" . __('BACK') . "</a>"
            . "</div>\n"
            . "</form><br /></div></div>\n";
}

function do_edit($art_id, $titre, $texte, $cat, $urlImage, $upImage){
    global $nuked, $user;

    $titre = mysql_real_escape_string(stripslashes($titre));

    if (empty($titre)){
        printNotification(_TITLEARTFORGOT, 'error');
        redirect("index.php?file=Sections&page=admin&op=add", 4);
    }
    else{
        $texte = secu_html(nkHtmlEntityDecode($texte));
        $texte = mysql_real_escape_string(stripslashes($texte));

        //Upload du fichier
        $filename = $_FILES['upImage']['name'];
        if ($filename != "") {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
                $url_image = "upload/Sections/" . $filename;
                if (! move_uploaded_file($_FILES['upImage']['tmp_name'], $url_image)) {
                    printNotification(_UPLOADFILEFAILED, 'error');
                    redirect('index.php?file=Sections&page=admin&op=edit&artid='. $art_id, 2);
                    return;
                }
                @chmod ($url_image, 0644);
            }
            else {
                printNotification(_NOIMAGEFILE, 'error');
                redirect('index.php?file=Sections&page=admin&op=edit&artid='. $art_id, 2);
                return;
            }
        }
        else {
            $url_image = $urlImage;
        }

        $upd = mysql_query("UPDATE " . SECTIONS_TABLE . " SET secid = '" . $cat . "', title = '" . $titre . "', coverage = '" . $url_image . "', content = '" . $texte . "' WHERE artid = '" . $art_id . "'");

        saveUserAction(_ACTIONMODIFSEC .': '. $titre .'.');

        printNotification(_ARTMODIF, 'success');
        setPreview('index.php?file=Sections&op=article&artid='. $art_id, 'index.php?file=Sections&page=admin');
    }
}

function del($art_id){
    global $nuked, $user;

    $sql = mysql_query("SELECT title FROM " . SECTIONS_TABLE . " WHERE artid = '" . $art_id . "'");
    list($titre) = mysql_fetch_array($sql);
    $titre = mysql_real_escape_string(stripslashes($titre));
    $del = mysql_query("DELETE FROM " . SECTIONS_TABLE . " WHERE artid = '" . $art_id . "'");
    $del_com = mysql_query("DELETE FROM " . COMMENT_TABLE . " WHERE im_id = '" . $art_id . "' AND module = 'Sections'");
    $del_vote = mysql_query("DELETE FROM " . VOTE_TABLE . " WHERE vid = '" . $art_id . "' AND module = 'Sections'");

    saveUserAction(_ACTIONDELSEC .': '. $titre .'.');

    printNotification(_ARTDEL, 'success');
    redirect("index.php?file=Sections&page=admin", 2);
}

function main_cat(){
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function del_cat(titre, id)\n"
            . "{\n"
            . "if (confirm('" . _DELETEART . " '+titre+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Sections&page=admin&op=del_cat&cid='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _CATMANAGEMENT . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(3);

            echo "</div>\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr>\n"
            . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
            . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CATPARENT . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = mysql_query("SELECT secid, secname, parentid, position FROM " . SECTIONS_CAT_TABLE . " ORDER BY parentid, position");
    $nbcat = mysql_num_rows($sql);
    if ($nbcat > 0){
        while (list($cid, $titre, $parentid, $position) = mysql_fetch_row($sql))
        {
            $titre = printSecuTags($titre);

            echo "<tr>\n"
                    . "<td style=\"width: 35%;\" align=\"center\">" . $titre . "</td>\n"
                    . "<td style=\"width: 35%;\" align=\"center\">\n";

            if ($parentid > 0){
                $sql2 = mysql_query("SELECT secname FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $parentid . "'");
                list($pnomcat) = mysql_fetch_array($sql2);
                $pnomcat = printSecuTags($pnomcat);

                echo "<i>" . $pnomcat . "</i>";
            }
            else{
                echo _NONE;
            }

            echo "</td><td style=\"width: 10%;\" align=\"center\">\n"
                    . "<a href=\"index.php?file=Sections&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=down\" title=\"" . _MOVEDOWN . "\">&lt;</a>"
                    . "&nbsp;" . $position . "&nbsp;<a href=\"index.php?file=Sections&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=up\" title=\"" . _MOVEUP . "\">&gt;</a></td>\n"
                    . "<td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Sections&amp;page=admin&amp;op=edit_cat&amp;cid=" . $cid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\"  title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
                    . "<td style=\"width: 10%;\" align=\"center\"><a href=\"javascript:del_cat('" . addslashes($titre) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
        }
    }
    else{
        echo "<tr><td align=\"center\" colspan=\"5\">" . _NONE . "&nbsp;" . _CAT . "&nbsp;" . _INDATABASE . "</td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Sections&amp;page=admin&amp;op=add_cat\">" . _ADDCAT . "</a><a class=\"buttonLink\" href=\"index.php?file=Sections&amp;page=admin\">" . __('BACK') . "</a></div>\n"
            . "<br /></div></div>\n";
}

function add_cat(){
    global $language, $nuked;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADDCAT . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Sections&amp;page=admin&amp;op=send_cat\">\n"
            . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
            . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\"><option value=\"0\">" . _NONE . "</option>\n";

    $sql = mysql_query("SELECT secid, secname FROM " . SECTIONS_CAT_TABLE . " where parentid = 0 ORDER BY position, secname");
    while (list($secid, $nomcat) = mysql_fetch_array($sql)){
        $nomcat = printSecuTags($nomcat);

        echo "<option value=\"" . $secid . "\">" . $nomcat . "</option>\n";
    }

    echo "</select></td></tr>\n"
            . "<tr><td><b>" . _POSITION . " :</b> <input type=\"text\" name=\"position\" size=\"2\" value=\"0\" /></td></tr>\n"
            . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
            . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" name=\"send\" value=\"" . _CREATECAT . "\" /><a class=\"buttonLink\" href=\"index.php?file=Sections&amp;page=admin&amp;op=main_cat\">" . __('BACK') . "</a></div>\n"
            . "</form><br /></div></div>\n";
}

function send_cat($parentid, $titre, $description, $position){
    global $nuked, $user;

    $titre = mysql_real_escape_string(stripslashes($titre));

    if (empty($titre)){
        printNotification(_TITLECATFORGOT, 'error');
        redirect("index.php?file=Sections&page=admin&op=main_cat", 4);
    }
    else {
        $description = secu_html(nkHtmlEntityDecode($description));
        $description = mysql_real_escape_string(stripslashes($description));
        $position = intval($position);

        $sql = mysql_query("INSERT INTO " . SECTIONS_CAT_TABLE . " ( `parentid` , `secname` , `description`, `position` ) VALUES ( '" . $parentid . "' , '" . $titre . "' , '" . $description. "' , '" . $position ."' )");

        saveUserAction(_ACTIONADDCATSEC .': '. $titre .'.');

        printNotification(_CATADD, 'success');

        $sql = mysql_query("SELECT secid FROM " . SECTIONS_CAT_TABLE . " WHERE secname = '" . $titre . "' AND parentid='" . $parentid . "'");
        list($secid) = mysql_fetch_array($sql);

        setPreview('index.php?file=Sections&op=categorie&secid='. $secid, 'index.php?file=Sections&page=admin&op=main_cat');
    }
}

function edit_cat($cid){
    global $nuked, $language;

    $sql = mysql_query("SELECT secname, parentid, description, position FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $cid . "'");
    list($titre, $parentid, $description, $position) = mysql_fetch_array($sql);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _EDITTHISCAT . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Sections&amp;page=admin&amp;op=modif_cat\">\n"
            . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre . "\" /></td></tr>\n"
            . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\">\n";

    if ($parentid > 0){
        $sql2 = mysql_query("SELECT secid, secname FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $parentid . "'");
        list($pcid, $pnomcat) = mysql_fetch_array($sql2);
        $pnomcat = printSecuTags($pnomcat);

        echo "<option value=\"" . $pcid . "\">" . $pnomcat . "</option>\n";
    }

    echo "<option value=\"0\">" . _NONE . "</option>\n";

    $sql3 = mysql_query("SELECT secid, secname FROM " . SECTIONS_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, secname");
    while (list($catid, $nomcat) = mysql_fetch_array($sql3)){
        $nomcat = printSecuTags($nomcat);

        if ($nomcat != $secname){
            echo "<option value=\"" . $catid . "\">" . $nomcat . "</option>\n";
        }
    }

    $description = editPhpCkeditor($description);

    echo "</select></td></tr>\n"
            . "<tr><td><b>" . _POSITION . " :</b> <input type=\"text\" name=\"position\" size=\"2\" value=\"" . $position . "\" /></td></tr>\n"
            . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
            . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\">" . $description . "</textarea></td></tr>\n"
            . "<tr><td>&nbsp;<input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" name=\"send\" value=\"" . _MODIFTHISCAT . "\" /><a class=\"buttonLink\" href=\"index.php?file=Sections&amp;page=admin&amp;op=main_cat\">" . __('BACK') . "</a></div>\n"
            . "</form><br /></div></div>\n";
    }

function modif_cat($cid, $parentid, $titre, $description, $position){
    global $nuked, $user;

    $titre = mysql_real_escape_string(stripslashes($titre));

    if (empty($titre)){
        printNotification(_TITLECATFORGOT, 'error');

        redirect("index.php?file=Sections&page=admin&op=main_cat", 4);
    } else {
        $description = secu_html(nkHtmlEntityDecode($description));
        $description = mysql_real_escape_string(stripslashes($description));
        $position = intval($position);

        $sql = mysql_query("UPDATE " . SECTIONS_CAT_TABLE . " SET parentid = '" . $parentid . "', secname = '" . $titre . "', description = '" . $description. "', position = '" . $position . "' WHERE secid = '" . $cid . "'");

        saveUserAction(_ACTIONMODIFCATSEC .': '. $titre .'.');

        printNotification(_CATMODIF, 'success');
        setPreview('index.php?file=Sections&op=categorie&secid='. $cid, 'index.php?file=Sections&page=admin&op=main_cat');
    }
}

function select_art_cat(){
    global $nuked;

    echo "<option value=\"0\">* " . _NONE . "</option>\n";

    $sql = mysql_query("SELECT secid, secname FROM " . SECTIONS_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, secname");
    while (list($secid, $secname) = mysql_fetch_array($sql)){
        $secname = printSecuTags($secname);

        echo "<option value=\"" . $secid . "\">* " . $secname . "</option>\n";

        $sql2 = mysql_query("SELECT secid, secname FROM " . SECTIONS_CAT_TABLE . " WHERE parentid = '" . $secid . "' ORDER BY position, secname");
        while (list($s_secid, $s_titre) = mysql_fetch_array($sql2)){
            $s_titre = printSecuTags($s_titre);

            echo "<option value=\"" . $s_secid . "\">&nbsp;&nbsp;&nbsp;" . $s_titre . "</option>\n";
        }
    }
}

function del_cat($cid){
    global $nuked, $user;

    $sql = mysql_query("SELECT secname FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $cid . "'");
    list($titre) = mysql_fetch_array($sql);
    $titre = mysql_real_escape_string(stripslashes($titre));
    $sql = mysql_query("DELETE FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $cid . "'");
    $sql = mysql_query("UPDATE " . SECTIONS_CAT_TABLE . " SET parentid = 0 WHERE parentid = '" . $cid . "'");
    $sql = mysql_query("UPDATE " . SECTIONS_TABLE . " SET secid = 0 WHERE secid = '" . $cid . "'");

    saveUserAction(_ACTIONDELCATSEC .': '. $titre .'.');

    printNotification(_CATDEL, 'success');
    redirect("index.php?file=Sections&page=admin&op=main_cat", 2);
}

function main_pref(){
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(4);

            echo "</div>\n"
            . "<form method=\"post\" action=\"index.php?file=Sections&amp;page=admin&amp;op=change_pref\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
            . "<tr><td colspan=\"2\" align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
            . "<tr><td>" . _SECTIONSPG . " :</td><td><input type=\"text\" name=\"max_sections\" size=\"2\" value=\"" . $nuked['max_sections'] . "\" /></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" name=\"Submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Sections&amp;page=admin\">" . __('BACK') . "</a></div>\n"
            . "</form><br /></div></div>\n";
}

function change_pref($max_sections){
    global $nuked, $user;

    $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_sections . "' WHERE name = 'max_sections'");

    saveUserAction(_ACTIONCONFSEC .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect("index.php?file=Sections&page=admin", 2);
}

function modif_position($cid, $method){
    global $nuked, $user;

    $sql = mysql_query("SELECT secname, position FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $cid . "'");
    list($titre, $position) = mysql_fetch_array($sql);
    if ($position <= 0 AND $method == "up"){
        printNotification(_CATERRORPOS, 'error');
        redirect("index.php?file=Sections&page=admin&op=main_cat", 2);
        return;
    }
    $titre = mysql_real_escape_string(stripslashes($titre));
    if ($method == "up") $upd = mysql_query("UPDATE " . SECTIONS_CAT_TABLE . " SET position = position - 1 WHERE secid = '" . $cid . "'");
    else if ($method == "down") $upd = mysql_query("UPDATE " . SECTIONS_CAT_TABLE . " SET position = position + 1 WHERE secid = '" . $cid . "'");

    saveUserAction(_ACTIONPOSSEC .': '. $titre .'.');

    printNotification(_CATMODIF, 'success');
    redirect("index.php?file=Sections&page=admin&op=main_cat", 2);
}

function nkAdminMenu($tab = 1) {
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Sections&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _NAVART; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Sections&amp;page=admin&amp;op=add">
                    <img src="modules/Admin/images/icons/add_page.png" alt="icon" />
                    <span><?php echo _ADDART; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Sections&amp;page=admin&amp;op=main_cat">
                    <img src="modules/Admin/images/icons/folder_full.png" alt="icon" />
                    <span><?php echo _CATMANAGEMENT; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 4 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Sections&amp;page=admin&amp;op=main_pref">
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
    case "main":
        main();
        break;
    case "add":
        add();
        break;
    case "do_add":
        do_add($_REQUEST['titre'], $_REQUEST['texte'], $_REQUEST['cat'], $_REQUEST['urlImage'], $_REQUEST['upImage']);
        break;
    case "edit":
        edit($_REQUEST['artid']);
        break;
    case "do_edit":
        do_edit($_REQUEST['artid'], $_REQUEST['titre'], $_REQUEST['texte'], $_REQUEST['cat'], $_REQUEST['urlImage'], $_REQUEST['upImage']);
        break;
    case "del":
        del($_REQUEST['artid']);
        break;
    case "send_cat":
        send_cat($_REQUEST['parentid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['position']);
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
        modif_cat($_REQUEST['cid'], $_REQUEST['parentid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['position']);
        break;
    case "del_cat":
        del_cat($_REQUEST['cid']);
        break;
    case "main_pref":
        main_pref();
        break;
    case "change_pref":
        change_pref($_REQUEST['max_sections']);
        break;
    case "modif_position":
        modif_position($_REQUEST['cid'], $_REQUEST['method']);
        break;
    default:
        main();
        break;
}

?>