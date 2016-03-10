<?php
/**
 * admin.php
 *
 * Backend of Links module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Links'))
    return;


function add_link(){
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADDLINK . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(2);

            echo "<form method=\"post\" action=\"index.php?file=Links&amp;page=admin&amp;op=add\" onsubmit=\"backslash('link_texte');\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" /></td></tr>\n"
            . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\">\n";

    select_cat();

    echo "</select></td></tr><tr><td><b>" . _COUNTRY . " :</b> <select name=\"country\"><option value=\"\">* " . _NOCOUNTRY . "</option>\n";

    if ($language == 'french') $pays = 'France.gif';

    $rep = Array();
    $handle = opendir('images/flags');
    while (false !== ($f = readdir($handle))){
        if ($f != '..' && $f != '.' && $f != 'index.html' && $f != 'Thumbs.db')
            $rep[] = $f;
    }

    closedir($handle);
    sort ($rep);
    reset ($rep);

    while (list ($key, $filename) = each ($rep)) {
        if ($filename == $pays)
            $checked = 'selected="selected"';
        else
            $checked = '';

        list ($country, $ext) = explode ('.', $filename);
        echo '<option value="' . $filename . '" ' . $checked . '>' . $country . '</option>'."\n";
    }

    echo "</select></td></tr>\n";

    echo "<tr><td><b>" . _DESCR . " : </b><br /><textarea class=\"editor\" id=\"link_texte\" name=\"description\" rows=\"10\" cols=\"65\"></textarea></td></tr>\n"
            . "<tr><td><b>" . _URL . " :</b>  <input type=\"text\" name=\"url\" size=\"55\" value=\"http://\" /></td></tr>\n"
            . "<tr><td><b>" . _WEBMASTER . " :</b>  <input type=\"text\" name=\"webmaster\" size=\"30\" /></td></tr>\n"
            . "<tr><td>&nbsp;</td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _ADDTHISLINK . "\" /><a class=\"buttonLink\" href=\"index.php?file=Links&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div></div>";
}

function add($titre, $description, $webmaster, $country, $cat, $url){
    global $nuked, $user;

    $date = time();
    $description = secu_html(nkHtmlEntityDecode($description));
    $description = mysql_real_escape_string(stripslashes($description));
    $titre = mysql_real_escape_string(stripslashes($titre));
    $webmaster = mysql_real_escape_string(stripslashes($webmaster));

    if ($url != "" && !preg_match("`http://`i", $url)){
        $url = "http://" . $url;
    }

    $sql = nkDB_execute("INSERT INTO " . LINKS_TABLE . " ( `id` , `date` , `titre` , `description` , `url` , `cat` , `webmaster`, `country`, `count` , `broke` ) VALUES ( '' , '" . $date . "' , '" . $titre . "' , '" . $description . "' , '" . $url . "' , '" . $cat . "' , '" . $webmaster ."' , '" . $country . "' , '' , '' )");

    saveUserAction(_ACTIONADDLINK .': '. $titre);

    printNotification(_LINKADD, 'success');

    $sql = nkDB_execute("SELECT id FROM " . LINKS_TABLE . " WHERE titre = '" . $titre . "' AND date='".$date."'");
    list($link_id) = nkDB_fetchArray($sql);

    setPreview('index.php?file=Links&op=description&link_id='. $link_id, 'index.php?file=Links&page=admin');
}

function del($link_id){
    global $nuked, $user;

    $sql = nkDB_execute("SELECT titre FROM " . LINKS_TABLE . " WHERE id = '" . $link_id . "'");
    list($titre) = nkDB_fetchArray($sql);
    $titre = mysql_real_escape_string(stripslashes($titre));
    $sql = nkDB_execute("DELETE FROM " . LINKS_TABLE . " WHERE id = '" . $link_id . "'");
    $del_com = nkDB_execute("DELETE FROM " . COMMENT_TABLE . " WHERE im_id = '" . $link_id . "' AND module = 'Links'");
    $del_vote = nkDB_execute("DELETE FROM " . VOTE_TABLE . " WHERE vid = '" . $link_id . "' AND module = 'Links'");

    saveUserAction(_ACTIONDELLINK .': '. $titre);

    printNotification(_LINKDEL, 'success');
    redirect("index.php?file=Links&page=admin", 2);
}

function edit_link($link_id){
    global $nuked, $language;

    $sql = nkDB_execute("SELECT titre, description, webmaster, country, cat, url, count FROM " . LINKS_TABLE . " WHERE id = '" . $link_id . "'");
    list($titre, $description, $webmaster, $pays, $cat, $url, $count) = nkDB_fetchArray($sql);

    if ($cat == 0 || !$cat){
        $cid = 0;
        $cat_name = _NONE;
    }
    else{
        $cid = $cat;
        $sql2 = nkDB_execute("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cat . "'");
        list($cat_name) = nkDB_fetchArray($sql2);
        $cat_name = printSecuTags($cat_name);
    }

    if ($pays == '') $checked1 = 'selected="selected"';

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _EDITTHISLINK . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Links&amp;page=admin&amp;op=modif_link\" onsubmit=\"backslash('link_texte');\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" value=\"" . $titre . "\" /></td></tr>\n"
            . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"" . $cid . "\">" . $cat_name . "</option>\n";

    select_cat();

    echo "</select></td></tr><tr><td><b>" . _COUNTRY . " :</b> <select name=\"country\"><option value=\"\" " . $checked1 . ">* " . _NOCOUNTRY . "</option>\n";

    $rep = Array();
    $handle = opendir('images/flags');
    while (false !== ($f = readdir($handle))){
        if ($f != '..' && $f != '.' && $f != 'index.html' && $f != 'Thumbs.db'){
            $rep[] = $f;
        }
    }
    closedir($handle);
    sort($rep);
    reset($rep);

    while (list ($key, $filename) = each ($rep)) {
        if ($filename == $pays)
            $checked = 'selected="selected"';
        else
            $checked = '';

        list ($country, $ext) = explode ('.', $filename);
        echo '<option value="' . $filename . '" ' . $checked . '>' . $country . '</option>',"\n";
    }

    echo "</select></td></tr>\n";

    $description = editPhpCkeditor($description);

    echo "<tr><td><b>" . _DESCR . " : </b><br /><textarea class=\"editor\" id=\"link_texte\" name=\"description\" rows=\"10\" cols=\"65\">" . $description . "</textarea></td></tr>\n"
            . "<tr><td><b>" . _URL . " :</b>  <input type=\"text\" name=\"url\" size=\"55\" value=\"" . $url . "\" /></td></tr>\n"
            . "<tr><td><b>" . _WEBMASTER . " :</b>  <input type=\"text\" name=\"webmaster\" size=\"30\" value=\"" . $webmaster . "\" /></td></tr>\n"
            . "<tr><td><b>" . _VISIT . "</b> : <input type=\"text\" name=\"count\" size=\"7\" value=\"" . $count . "\" /></td></tr>\n"
            . "<tr><td>&nbsp;<input type=\"hidden\" name=\"link_id\" value=\"" . $link_id . "\" /></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISLINK . "\" /><a class=\"buttonLink\" href=\"index.php?file=Links&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div>";

}

function modif_link($link_id, $titre, $description, $webmaster, $country, $cat, $count, $url){
    global $nuked, $user;

    $description = secu_html(nkHtmlEntityDecode($description));
    $description = mysql_real_escape_string(stripslashes($description));
    $titre = mysql_real_escape_string(stripslashes($titre));
    $webmaster = mysql_real_escape_string(stripslashes($webmaster));

    if ($url != "" && !preg_match("`http://`i", $url)){
        $url = "http://" . $url;
    }

    $sql = nkDB_execute("UPDATE " . LINKS_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "', webmaster = '" . $webmaster . "', country = '" . $country . "', cat = '" . $cat . "', count = '" . $count. "', url = '" . $url . "' WHERE id = '" . $link_id . "'");

    saveUserAction(_ACTIONEDITLINK .': '. $titre);

    printNotification(_LINKMODIF, 'success');
    setPreview('index.php?file=Links&op=description&link_id='. $link_id, 'index.php?file=Links&page=admin');
}

function main(){
    global $nuked, $language;

    $nb_liens = 30;

    $sql3 = nkDB_execute("SELECT id FROM " . LINKS_TABLE . "");
    $nb_lk = mysql_num_rows($sql3);

    if(array_key_exists('p', $_REQUEST)){
        $page = $_REQUEST['p'];
    }
    else{
        $page = 1;
    }
    $start = $page * $nb_liens - $nb_liens;

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function del_link(titre, id)\n"
            . "{\n"
            . "if (confirm('" . _DELETELINK . " '+titre+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Links&page=admin&op=del&link_id='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADMINLINKS . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(1);

    if(!array_key_exists('orderby', $_REQUEST)){
        $_REQUEST['orderby'] = '';
    }

    if ($_REQUEST['orderby'] == 'date')
        $order_by = 'L.id DESC';
    else if ($_REQUEST['orderby'] == 'name')
        $order_by = 'L.titre';
    else if ($_REQUEST['orderby'] == 'cat')
        $order_by = 'LC.titre, LC.parentid';
    else
        $order_by = 'L.id DESC';

    echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">\n"
            . "<tr><td align=\"right\">" . _ORDERBY . " : ";

    if ($_REQUEST['orderby'] == 'date' || !$_REQUEST['orderby'])
        echo '<b>' . _DATE . '</b> | ';
    else
        echo "<a href=\"index.php?file=Links&amp;page=admin&amp;orderby=date\">" . _DATE . "</a> | ";
    if ($_REQUEST['orderby'] == "name")
        echo "<b>" . _TITLE . "</b> | ";
    else
        echo"<a href=\"index.php?file=Links&amp;page=admin&amp;orderby=name\">" . _TITLE . "</a> | ";
    if ($_REQUEST['orderby'] == "cat")
        echo "<b>" . _CAT . "</b>";
    else
        echo "<a href=\"index.php?file=Links&amp;page=admin&amp;orderby=cat\">" . _CAT . "</a>";

    echo "&nbsp;</td></tr></table>\n";

    if ($nb_lk > $nb_liens){
        echo "<div>";
        $url_page = "index.php?file=Links&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
        number($nb_lk, $nb_liens, $url_page);
        echo "</div>\n";
    }

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr>\n"
            . "<td style=\"width: 25%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
            . "<td style=\"width: 25%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT L.id, L.titre, L.cat, L.url, L.date, LC.titre, LC.parentid FROM " . LINKS_TABLE . " AS L LEFT JOIN " . LINKS_CAT_TABLE . " AS LC ON LC.cid = L.cat ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_liens."");
    while (list($link_id, $titre, $cat, $url, $date, $namecat, $parentid) = nkDB_fetchArray($sql)){
        $date = nkDate($date);

        if ($cat == 0)
            $categorie = _NONE;
        else if ($parentid == 0)
            $categorie = printSecuTags($namecat);
        else{
            $sql3 = nkDB_execute("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
            list($parentcat) = nkDB_fetchArray($sql3);
            $categorie = $parentcat . "->" . $namecat;
            $categorie = printSecuTags($categorie);
        }

        if (strlen($titre) > 25)
            $title = "<a href=\"" . $url . "\" title=\"" . $url . "\" onclick=\"window.open(this.href); return false;\">" . printSecuTags(substr($titre, 0, 25)) . "</a>...";
        else
            $title = "<a href=\"" . $url . "\" title=\"" . $url . "\" onclick=\"window.open(this.href); return false;\">" . printSecuTags($titre) . "</a>";

        echo "<tr>\n"
                . "<td style=\"width: 25%;\">" . $title . "</td>\n"
                . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td>\n"
                . "<td style=\"width: 25%;\" align=\"center\">" . $categorie . "</td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Links&amp;page=admin&amp;op=edit_link&amp;link_id=" . $link_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISLINK . "\" /></a></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_link('" . addslashes($titre) . "', '" . $link_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISLINK . "\" /></a></td></tr>\n";
    }

    if ($nb_lk == 0)
        echo "<tr><td colspan=\"5\" align=\"center\">" . _NOLINKINDB . "</td></tr>\n";

    echo "</table>\n";

    if ($nb_lk > $nb_liens){
        echo "<div>";
        $url_page = "index.php?file=Links&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
        number($nb_lk, $nb_liens, $url_page);
        echo "</div>\n";
    }

    echo "<br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>";
}

function main_cat(){
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function delcat(titre, id)\n"
            . "{\n"
            . "if (confirm('" . _DELETELINK . " '+titre+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Links&page=admin&op=del_cat&cid='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _CATMANAGEMENT . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(3);

            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr=>\n"
            . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
            . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CATPARENT . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT cid, titre, parentid, position FROM " . LINKS_CAT_TABLE . " ORDER BY parentid, position");
    $nbcat = mysql_num_rows($sql);
    if ($nbcat > 0){
        while (list($cid, $titre, $parentid, $position) = nkDB_fetchArray($sql)){
            $titre = printSecuTags($titre);

            echo "<tr>\n"
                    . "<td style=\"width: 35%;\" align=\"center\">" . $titre . "</td>\n"
                    . "<td style=\"width: 35%;\" align=\"center\">\n";

            if ($parentid > 0){
                $sql2 = nkDB_execute("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
                list($pnomcat) = nkDB_fetchArray($sql2);
                $pnomcat = printSecuTags($pnomcat);

                echo "<i>" . $pnomcat . "</i>";
            }
            else
                echo _NONE;

            echo "</td><td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Links&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=down\" title=\"" . _MOVEDOWN . "\">&lt;</a>"
                    . "&nbsp;" . $position . "&nbsp;<a href=\"index.php?file=Links&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=up\" title=\"" . _MOVEUP . "\">&gt;</a></td>\n"
                    . "<td align=\"center\"><a href=\"index.php?file=Links&amp;page=admin&amp;op=edit_cat&amp;cid=" . $cid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
                    . "<td align=\"center\"><a href=\"javascript:delcat('" . addslashes($titre) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
        }
    }
    else {
        echo "<tr><td align=\"center\" colspan=\"5\">" . _NONE . "&nbsp;" . _CAT . "&nbsp;" . _INDATABASE . "</td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Links&amp;page=admin&amp;op=add_cat\">" . _ADDCAT . "</a><a class=\"buttonLink\" href=\"index.php?file=Links&amp;page=admin\">" . __('BACK') . "</a></div>\n"
            . "<br /></div></div>\n";
}

function add_cat(){
    global $language, $nuked;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADDCAT . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Links&amp;page=admin&amp;op=send_cat\">\n"
            . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
            . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\"><option value=\"0\">" . _NONE . "</option>\n";

    $sql = nkDB_execute("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($cid, $nomcat) = nkDB_fetchArray($sql)){
        $nomcat = printSecuTags($nomcat);

        echo "<option value=\"" . $cid . "\">" . $nomcat . "</option>\n";
    }

    echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"0\" /></td></tr>\n"
            . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
            . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _CREATECAT . "\" /><a class=\"buttonLink\" href=\"index.php?file=Links&amp;page=admin&amp;op=main_cat\">" . __('BACK') . "</a></div>\n"
            . "</form><br /></div></div>\n";
}

function send_cat($titre, $description, $parentid, $position){
    global $nuked, $user;

    $description = nkHtmlEntityDecode($description);
    $titre = mysql_real_escape_string(stripslashes($titre));
    $description = mysql_real_escape_string(stripslashes($description));

    $sql = nkDB_execute("INSERT INTO " . LINKS_CAT_TABLE . " ( `parentid` , `titre` , `description` , `position` ) VALUES ( '" . $parentid . "' , '" . $titre . "' , '" . $description . "' , '" . $position . "' )");

    saveUserAction(_ACTIONADDCATLINK .': '. $titre);

    printNotification(_CATADD, 'success');

    $sqlc = nkDB_execute("SELECT cid FROM " . LINKS_CAT_TABLE . " WHERE titre = '" . $titre . "' AND parentid = '" . $parentid . "'");
    list($cid) = nkDB_fetchArray($sqlc);

    setPreview('index.php?file=Links&op=categorie&cat='. $cid, 'index.php?file=Links&page=admin&op=main_cat');
}

function edit_cat($cid){
    global $nuked, $language;

    $sql = nkDB_execute("SELECT parentid, titre, description, position FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cid . "'");
    list($parentid, $titre, $description, $position) = nkDB_fetchArray($sql);

    $titre = printSecuTags($titre);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _EDITTHISCAT . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Links&amp;page=admin&amp;op=modif_cat\">\n"
            . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre  . "\" /></td></tr>\n"
            . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\">\n";

    if ($parentid > 0){
        $sql2 = nkDB_execute("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
        list($pcid, $pnomcat) = nkDB_fetchArray($sql2);

        $pnomcat = printSecuTags($pnomcat);

        echo "<option value=\"" . $pcid . "\">" . $pnomcat . "</option>\n";
    }

    echo "<option value=\"0\">" . _NONE . "</option>\n";

    $sql3 = nkDB_execute("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($catid, $nomcat) = nkDB_fetchArray($sql3)){
        $nomcat = printSecuTags($nomcat);

        if ($nomcat != $titre){
            echo "<option value=\"" . $catid . "\">" . $nomcat . "</option>\n";
        }
    }

    $description = editPhpCkeditor($description);

    echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"" . $position . "\" /></td></tr>\n"
            . "<tr><td><b>" . _DESCR . " :</b><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /></td></tr>\n"
            . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\">" . $description . "</textarea></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISCAT . "\" /><a class=\"buttonLink\" href=\"index.php?file=Links&amp;page=admin&amp;op=main_cat\">" . __('BACK') . "</a></div>\n"
            . "</form><br /></div></div>\n";
}

function modif_cat($cid, $titre, $description, $parentid, $position){
    global $nuked, $user;

    $description = nkHtmlEntityDecode($description);
    $titre = mysql_real_escape_string(stripslashes($titre));
    $description = mysql_real_escape_string(stripslashes($description));

    $sql = nkDB_execute("UPDATE " . LINKS_CAT_TABLE . " SET parentid = '" . $parentid . "', titre = '" . $titre . "', description = '" . $description . "', position = '" . $position . "' WHERE cid = '" . $cid . "'");

    saveUserAction(_ACTIONMODIFCATLINK .': '. $titre);

    printNotification(_CATMODIF, 'success');
    setPreview('index.php?file=Links&op=categorie&cat='. $cid, 'index.php?file=Links&page=admin&op=main_cat');
}

function select_cat(){
    global $nuked;

    $sql = nkDB_execute("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
    while (list($cid, $titre) = nkDB_fetchArray($sql)){
        $titre = printSecuTags($titre);

        echo "<option value=\"" . $cid . "\">* " . $titre . "</option>\n";

        $sql2 = nkDB_execute("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre");
        while (list($s_cid, $s_titre) = nkDB_fetchArray($sql2)){
            $s_titre = printSecuTags($s_titre);

            echo "<option value=\"" . $s_cid . "\">&nbsp;&nbsp;&nbsp;" . $s_titre . "</option>\n";
        }
    }
    echo "<option value=\"0\">* " . _NONE . "</option>\n";
}

function del_cat($cid){
    global $nuked, $user;

    $sqlc = nkDB_execute("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cid . "'");
    list($titre) = nkDB_fetchArray($sqlc);
    $titre = mysql_real_escape_string(stripslashes($titre));
    $sql = nkDB_execute("DELETE FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cid . "'");
    $sql = nkDB_execute("UPDATE " . LINKS_CAT_TABLE . " SET parentid = 0 WHERE parentid = '" . $cid . "'");
    $sql = nkDB_execute("UPDATE " . LINKS_TABLE . " SET cat = 0 WHERE cat = '" . $cid . "'");

    saveUserAction(_ACTIONDELCATLINK .': '. $titre);

    printNotification(_CATDEL, 'success');
    redirect("index.php?file=Links&page=admin&op=main_cat", 2);
}

function main_pref(){
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(5);

            echo "<form method=\"post\" action=\"index.php?file=Links&amp;page=admin&amp;op=change_pref\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
            . "<tr><td align=\"center\" colspan=\"2\"><big>" . _PREFS . "</big></td></tr>\n"
            . "<tr><td>" . _NUMBERLINK . " :</td><td><input type=\"text\" name=\"max_liens\" size=\"2\" value=\"" . $nuked['max_liens'] . "\" /></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Links&amp;page=admin\">" . __('BACK') . "</a></div>\n"
            . "</form><br /></div></div>\n";
}

function change_pref($max_liens){
    global $nuked, $user;

    $upd = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_liens . "' WHERE name = 'max_liens'");

    saveUserAction(_ACTIONCONFLINK);

    printNotification(_PREFUPDATED, 'success');
    redirect("index.php?file=Links&page=admin", 2);
}

function modif_position($cid, $method){
    global $nuked, $user;

    $sqlc = nkDB_execute("SELECT titre, position FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cid . "'");
    list($titre, $position) = nkDB_fetchArray($sqlc);
    $titre = mysql_real_escape_string(stripslashes($titre));
    if ($position <=0 AND $method == "up"){
        printNotification(_CATERRORPOS, 'error');
        redirect("index.php?file=Links&page=admin&op=main_cat", 2);
        return;
    }
    if ($method == "up") $upd = nkDB_execute("UPDATE " . LINKS_CAT_TABLE . " SET position = position - 1 WHERE cid = '" . $cid . "'");
    else if ($method == "down") $upd = nkDB_execute("UPDATE " . LINKS_CAT_TABLE . " SET position = position + 1 WHERE cid = '" . $cid . "'");

    saveUserAction(_ACTIONPOSLINK .': '. $titre);

    printNotification(_CATMODIF, 'success');
    redirect("index.php?file=Links&page=admin&op=main_cat", 2);
}

function main_broken(){
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function del_link(titre, id)\n"
            . "{\n"
            . "if (confirm('" . _DELETELINK . " '+titre+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Links&page=admin&op=del&link_id='+id;}\n"
            . "}\n"
            . "\n"
            . "function delbroke()\n"
            . "{\n"
            . "if (confirm('" . _ERASEALLLIST . "'))\n"
            . "{document.location.href = 'index.php?file=Links&page=admin&op=del_broken';}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _BROKENLINKS . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(4);

            echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
            . "<td style=\"width: 35%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><b>X</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _ERASE . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $i = 0;
    $l = 0;
    $sql = nkDB_execute("SELECT id, titre, url, broke FROM " . LINKS_TABLE . " WHERE broke > 0 ORDER BY broke DESC, cat");
    $nb_broke = mysql_num_rows($sql);

    if ($nb_broke > 0){
        while (list($link_id, $titre, $url, $broke) = nkDB_fetchArray($sql)){
            $titre = printSecuTags($titre);

            $l++;

            echo "<tr>\n"
                    . "<td style=\"width: 10%;\" align=\"center\">" . $l . "</td>\n"
                    . "<td style=\"width: 35%;\"><a href=\"" . $url . "\" title=\"" . $url . "\" onclick=\"window.open(this.href); return false;\"><b>" . $titre . "</b></a></td>\n"
                    . "<td style=\"width: 10%;\" align=\"center\">" . $broke . "</td>\n"
                    . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Links&amp;page=admin&amp;op=del_broke&amp;link_id=" . $link_id. "\"><img style=\"border: 0;\" src=\"modules/Links/images/del.gif\" alt=\"\" title=\"" . _ERASEFROMLIST . "\" /></a></td>\n"
                    . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Links&amp;page=admin&amp;op=edit_link&amp;link_id=" . $link_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISLINK . "\" /></a></td>\n"
                    . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_link('" . addslashes($titre) . "', '" . $link_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISLINK . "\" /></a></td></tr>\n";
        }
    }
    else{
        echo "<tr><td align=\"center\" colspan=\"6\">" . _NOLINKINDB . "</td></tr>\n";
    }

    echo "</table><br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"javascript:delbroke();\">" . _ERASELIST . "</a>\n"
            . "<a class=\"buttonLink\" href=\"index.php?file=Links&amp;page=admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function del_broke($link_id){
    global $nuked, $user;

    $sql = nkDB_execute("UPDATE " . LINKS_TABLE . " SET broke = 0 WHERE id = '" . $link_id . "'");

    saveUserAction(_ACTION1BROKELINK);

    printNotification(_LINKERASED, 'success');
    redirect("index.php?file=Links&page=admin&op=main_broken", 2);
}

function del_broken(){
    global $nuked, $user;

    $sql = nkDB_execute("UPDATE " . LINKS_TABLE . " SET broke = 0");

    saveUserAction(_ACTIONALLBROKELINK);

    printNotification(_LISTERASED, 'success');
    redirect("index.php?file=Links&page=admin&op=main_broken", 2);
}

function nkAdminMenu($tab = 1) {
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Links&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _NAVLINKS; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Links&amp;page=admin&amp;op=add_link">
                    <img src="modules/Admin/images/icons/add_link.png" alt="icon" />
                    <span><?php echo _ADDLINK; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Links&amp;page=admin&amp;op=main_cat">
                    <img src="modules/Admin/images/icons/folder_full.png" alt="icon" />
                    <span><?php echo _CATMANAGEMENT; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 4 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Links&amp;page=admin&amp;op=main_broken">
                    <img src="modules/Admin/images/icons/remove_link.png" alt="icon" />
                    <span><?php echo _BROKENLINKS; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 5 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Links&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($GLOBALS['op']){
    case "edit_link":
        edit_link($_REQUEST['link_id']);
        break;
    case "add_link":
        add_link();
        break;
    case "del":
        del($_REQUEST['link_id']);
        break;
    case "add":
        add($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['webmaster'], $_REQUEST['country'], $_REQUEST['cat'], $_REQUEST['url']);
        break;
    case "modif_link":
        modif_link($_REQUEST['link_id'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['webmaster'], $_REQUEST['country'], $_REQUEST['cat'], $_REQUEST['count'], $_REQUEST['url']);
        break;
    case "main":
        main();
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
        change_pref($_REQUEST['max_liens']);
        break;
    case "modif_position":
        modif_position($_REQUEST['cid'], $_REQUEST['method']);
        break;
    case "main_broken":
        main_broken();
        break;
    case "del_broke":
        del_broke($_REQUEST['link_id']);
        break;
    case "del_broken":
        del_broken();
        break;
    default:
        main();
        break;
}

?>