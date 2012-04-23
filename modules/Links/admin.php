<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $user, $language;
translate('modules/Links/lang/' . $language . '.lang.php');
include('modules/Admin/design.php');
admintop();

$visiteur = ($user) ? $user[1] : 0;

$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1){
    function add_link(){
        global $nuked, $language;
        
        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINLINKS . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Links&amp;page=admin\">" . _NAVLINKS . "</a> | "
                . "</b>" . _ADDLINK . "<b> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_broken\">" . _BROKENLINKS . "</a> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
                . "<form method=\"post\" action=\"index.php?file=Links&amp;page=admin&amp;op=add\" onsubmit=\"backslash('link_texte');\">\n"
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
                . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . _ADDTHISLINK . "\" /></td></tr></table>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Links&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>";
    } 


    function add($titre, $description, $webmaster, $country, $cat, $url){
        global $nuked, $user;

        $date = time();
        $description = html_entity_decode($description);
        $description = mysql_real_escape_string(stripslashes($description));
        $titre = mysql_real_escape_string(stripslashes($titre));
        $webmaster = mysql_real_escape_string(stripslashes($webmaster));

        if ($url != "" && !preg_match("`http://`i", $url)){
            $url = "http://" . $url;
        } 

        $sql = mysql_query("INSERT INTO " . LINKS_TABLE . " ( `id` , `date` , `titre` , `description` , `url` , `cat` , `webmaster`, `country`, `count` , `broke` ) VALUES ( '' , '" . $date . "' , '" . $titre . "' , '" . $description . "' , '" . $url . "' , '" . $cat . "' , '" . $webmaster ."' , '" . $country . "' , '' , '' )");
        // Action
        $texteaction = _ACTIONADDLINK . ': ' . $titre;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _LINKADD . "\n"
                . "</div>\n"
                . "</div>\n";
        $sql = mysql_query("SELECT id FROM " . LINKS_TABLE . " WHERE titre = '" . $titre . "' AND date='".$date."'");
        list($link_id) = mysql_fetch_array($sql);
        echo "<script>\n"
            ."setTimeout('screen()','3000');\n"
            ."function screen() { \n"
            ."screenon('index.php?file=Links&op=description&link_id=".$link_id."', 'index.php?file=Links&page=admin');\n"
            ."}\n"
            ."</script>\n";
    } 

    function del($link_id){
        global $nuked, $user;

        $sql = mysql_query("SELECT titre FROM " . LINKS_TABLE . " WHERE id = '" . $link_id . "'");
        list($titre) = mysql_fetch_array($sql);
        $titre = mysql_real_escape_string(stripslashes($titre));
        $sql = mysql_query("DELETE FROM " . LINKS_TABLE . " WHERE id = '" . $link_id . "'");
        $del_com = mysql_query("DELETE FROM " . COMMENT_TABLE . " WHERE im_id = '" . $link_id . "' AND module = 'Links'");
        $del_vote = mysql_query("DELETE FROM " . VOTE_TABLE . " WHERE vid = '" . $link_id . "' AND module = 'Links'");
        
        // Action
        $texteaction = _ACTIONDELLINK . ': ' . $titre;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _LINKDEL . "\n"
                . "</div>\n"
                . "</div>\n";
        redirect("index.php?file=Links&page=admin", 2);
    } 

    function edit_link($link_id){
        global $nuked, $language;

        $sql = mysql_query("SELECT titre, description, webmaster, country, cat, url, count FROM " . LINKS_TABLE . " WHERE id = '" . $link_id . "'");
        list($titre, $description, $webmaster, $pays, $cat, $url, $count) = mysql_fetch_array($sql);
        
        if ($cat == 0 || !$cat){
            $cid = 0;
            $cat_name = _NONE;
        }
        else{
            $cid = $cat;
            $sql2 = mysql_query("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cat . "'");
            list($cat_name) = mysql_fetch_array($sql2);
            $cat_name = printSecuTags($cat_name);
        }

        if ($pays == '') $checked1 = 'selected="selected"';
       
        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINLINKS . "</h3>\n"
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

        echo "<tr><td><b>" . _DESCR . " : </b><br /><textarea class=\"editor\" id=\"link_texte\" name=\"description\" rows=\"10\" cols=\"65\">" . $description . "</textarea></td></tr>\n"
                . "<tr><td><b>" . _URL . " :</b>  <input type=\"text\" name=\"url\" size=\"55\" value=\"" . $url . "\" /></td></tr>\n"
                . "<tr><td><b>" . _WEBMASTER . " :</b>  <input type=\"text\" name=\"webmaster\" size=\"30\" value=\"" . $webmaster . "\" /></td></tr>\n"
                . "<tr><td><b>" . _VISIT . "</b> : <input type=\"text\" name=\"count\" size=\"7\" value=\"" . $count . "\" /></td></tr>\n"
                . "<tr><td>&nbsp;<input type=\"hidden\" name=\"link_id\" value=\"" . $link_id . "\" /></td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . _MODIFTHISLINK . "\" /></td></tr></table>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Links&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div>";

    } 

    function modif_link($link_id, $titre, $description, $webmaster, $country, $cat, $count, $url){
        global $nuked, $user;

        $description = html_entity_decode($description);
        $description = mysql_real_escape_string(stripslashes($description));
        $titre = mysql_real_escape_string(stripslashes($titre));
        $webmaster = mysql_real_escape_string(stripslashes($webmaster));

        if ($url != "" && !preg_match("`http://`i", $url)){
            $url = "http://" . $url;
        } 

        $sql = mysql_query("UPDATE " . LINKS_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "', webmaster = '" . $webmaster . "', country = '" . $country . "', cat = '" . $cat . "', count = '" . $count. "', url = '" . $url . "' WHERE id = '" . $link_id . "'");
        // Action
        $texteaction = _ACTIONEDITLINK . ': ' . $titre;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _LINKMODIF . "\n"
                . "</div>\n"
                . "</div>\n";
        echo "<script>\n"
                ."setTimeout('screen()','3000');\n"
                ."function screen() { \n"
                ."screenon('index.php?file=Links&op=description&link_id=".$link_id."', 'index.php?file=Links&page=admin');\n"
                ."}\n"
                ."</script>\n";
    } 

    function main(){
        global $nuked, $language;

        $nb_liens = 30;

        $sql3 = mysql_query("SELECT id FROM " . LINKS_TABLE . "");
        $nb_lk = mysql_num_rows($sql3);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_liens - $nb_liens;

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
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _NAVLINKS . "<b> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=add_link\">" . _ADDLINK . "</a> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_broken\">" . _BROKENLINKS . "</a> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n";

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

        $sql = mysql_query("SELECT L.id, L.titre, L.cat, L.url, L.date, LC.titre, LC.parentid FROM " . LINKS_TABLE . " AS L LEFT JOIN " . LINKS_CAT_TABLE . " AS LC ON LC.cid = L.cat ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_liens."");
        while (list($link_id, $titre, $cat, $url, $date, $namecat, $parentid) = mysql_fetch_array($sql)){
            $date = nkDate($date);

            if ($cat == 0)
                $categorie = _NONE;
            else if ($parentid == 0)
                $categorie = printSecuTags($namecat);
            else{
                $sql3 = mysql_query("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
                list($parentcat) = mysql_fetch_array($sql3);
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
                    . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_link('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $link_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISLINK . "\" /></a></td></tr>\n";
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

        echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>";
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
                . "<div class=\"content-box-header\"><h3>" . _ADMINLINKS . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Links&amp;page=admin\">" . _NAVLINKS . "</a> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=add_link\">" . _ADDLINK . "</a> | "
                . "</b>" . _CATMANAGEMENT . "<b><br />"
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_broken\">" . _BROKENLINKS . "</a> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
                . "<tr=>\n"
                . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
                . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CATPARENT . "</b></td>\n"
                . "<td style=\"width: 10%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
                . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
                . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT cid, titre, parentid, position FROM " . LINKS_CAT_TABLE . " ORDER BY parentid, position");
        $nbcat = mysql_num_rows($sql);
        if ($nbcat > 0){
            while (list($cid, $titre, $parentid, $position) = mysql_fetch_array($sql)){
                $titre = printSecuTags($titre);

                echo "<tr>\n"
                        . "<td style=\"width: 35%;\" align=\"center\">" . $titre . "</td>\n"
                        . "<td style=\"width: 35%;\" align=\"center\">\n";

                if ($parentid > 0){
                    $sql2 = mysql_query("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
                    list($pnomcat) = mysql_fetch_array($sql2);
                    $pnomcat = printSecuTags($pnomcat);

                    echo "<i>" . $pnomcat . "</i>";
                } 
                else
                    echo _NONE;

                echo "</td><td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Links&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=down\" title=\"" . _MOVEDOWN . "\">&lt;</a>"
                        . "&nbsp;" . $position . "&nbsp;<a href=\"index.php?file=Links&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=up\" title=\"" . _MOVEUP . "\">&gt;</a></td>\n"
                        . "<td align=\"center\"><a href=\"index.php?file=Links&amp;page=admin&amp;op=edit_cat&amp;cid=" . $cid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
                        . "<td align=\"center\"><a href=\"javascript:delcat('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
            } 
        }
        else {
            echo "<tr><td align=\"center\" colspan=\"5\">" . _NONE . "&nbsp;" . _CAT . "&nbsp;" . _INDATABASE . "</td></tr>\n";
        }

        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Links&amp;page=admin&amp;op=add_cat\"><b>" . _ADDCAT . "</b></a> ]</div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Links&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function add_cat(){
        global $language, $nuked;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINLINKS . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Links&amp;page=admin&amp;op=send_cat\">\n"
                . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
                . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
                . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\"><option value=\"0\">" . _NONE . "</option>\n";

        $sql = mysql_query("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
        while (list($cid, $nomcat) = mysql_fetch_array($sql)){
            $nomcat = printSecuTags($nomcat);

            echo "<option value=\"" . $cid . "\">" . $nomcat . "</option>\n";
        } 

        echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"0\" /></td></tr>\n"
                . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
                . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
                . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _CREATECAT . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Links&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function send_cat($titre, $description, $parentid, $position){
        global $nuked, $user;

        $description = html_entity_decode($description);
        $titre = mysql_real_escape_string(stripslashes($titre));
        $description = mysql_real_escape_string(stripslashes($description));

        $sql = mysql_query("INSERT INTO " . LINKS_CAT_TABLE . " ( `parentid` , `titre` , `description` , `position` ) VALUES ( '" . $parentid . "' , '" . $titre . "' , '" . $description . "' , '" . $position . "' )");
        // Action
        $texteaction = _ACTIONADDCATLINK . ': ' . $titre;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _CATADD . "\n"
                . "</div>\n"
                . "</div>\n";
                
        $sqlc = mysql_query("SELECT cid FROM " . LINKS_CAT_TABLE . " WHERE titre = '" . $titre . "' AND parentid = '" . $parentid . "'");
        list($cid) = mysql_fetch_array($sqlc);
        
        echo "<script>\n"
                ."setTimeout('screen()','3000');\n"
                ."function screen() { \n"
                ."screenon('index.php?file=Links&op=categorie&cat=".$cid."', 'index.php?file=Links&page=admin&op=main_cat');\n"
                ."}\n"
                ."</script>\n";
    } 

    function edit_cat($cid){
        global $nuked, $language;

        $sql = mysql_query("SELECT parentid, titre, description, position FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cid . "'");
        list($parentid, $titre, $description, $position) = mysql_fetch_array($sql);

        $titre = printSecuTags($titre);

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINLINKS . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Links&amp;page=admin&amp;op=modif_cat\">\n"
                . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
                . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre  . "\" /></td></tr>\n"
                . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\">\n";

        if ($parentid > 0){
            $sql2 = mysql_query("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
            list($pcid, $pnomcat) = mysql_fetch_array($sql2);
            
            $pnomcat = printSecuTags($pnomcat);

            echo "<option value=\"" . $pcid . "\">" . $pnomcat . "</option>\n";
        } 

        echo "<option value=\"0\">" . _NONE . "</option>\n";

        $sql3 = mysql_query("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
        while (list($catid, $nomcat) = mysql_fetch_array($sql3)){
            $nomcat = printSecuTags($nomcat);

            if ($nomcat != $titre){
                echo "<option value=\"" . $catid . "\">" . $nomcat . "</option>\n";
            } 
        } 

        echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"" . $position . "\" /></td></tr>\n"
                . "<tr><td><b>" . _DESCR . " :</b><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /></td></tr>\n"
                . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\">" . $description . "</textarea></td></tr></table>\n"
                . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _MODIFTHISCAT . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Links&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function modif_cat($cid, $titre, $description, $parentid, $position){
        global $nuked, $user;

        $description = html_entity_decode($description);
        $titre = mysql_real_escape_string(stripslashes($titre));
        $description = mysql_real_escape_string(stripslashes($description));

        $sql = mysql_query("UPDATE " . LINKS_CAT_TABLE . " SET parentid = '" . $parentid . "', titre = '" . $titre . "', description = '" . $description . "', position = '" . $position . "' WHERE cid = '" . $cid . "'");
         // Action
        $texteaction = _ACTIONMODIFCATLINK . ': ' . $titre;
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
                ."screenon('index.php?file=Links&op=categorie&cat=".$cid."', 'index.php?file=Links&page=admin&op=main_cat');\n"
                ."}\n"
                ."</script>\n";
    } 

    function select_cat(){
        global $nuked;

        $sql = mysql_query("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
        while (list($cid, $titre) = mysql_fetch_array($sql)){
            $titre = printSecuTags($titre);

            echo "<option value=\"" . $cid . "\">* " . $titre . "</option>\n";

            $sql2 = mysql_query("SELECT cid, titre FROM " . LINKS_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre");
            while (list($s_cid, $s_titre) = mysql_fetch_array($sql2)){
                $s_titre = printSecuTags($s_titre);

                echo "<option value=\"" . $s_cid . "\">&nbsp;&nbsp;&nbsp;" . $s_titre . "</option>\n";
            } 
        }
        echo "<option value=\"0\">* " . _NONE . "</option>\n";
    } 

    function del_cat($cid){
        global $nuked, $user;

        $sqlc = mysql_query("SELECT titre FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cid . "'");
        list($titre) = mysql_fetch_array($sqlc);
        $titre = mysql_real_escape_string(stripslashes($titre));
        $sql = mysql_query("DELETE FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cid . "'");
        $sql = mysql_query("UPDATE " . LINKS_CAT_TABLE . " SET parentid = 0 WHERE parentid = '" . $cid . "'");
        $sql = mysql_query("UPDATE " . LINKS_TABLE . " SET cat = 0 WHERE cat = '" . $cid . "'");
        // Action
        $texteaction = _ACTIONDELCATLINK . ': ' . $titre;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _CATDEL . "\n"
                . "</div>\n"
                . "</div>\n";
                
        redirect("index.php?file=Links&page=admin&op=main_cat", 2);
    } 

    function main_pref(){
        global $nuked, $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINLINKS . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Links&amp;page=admin\">" . _NAVLINKS . "</a> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=add_link\">" . _ADDLINK . "</a> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_broken\">" . _BROKENLINKS . "</a> | "
                . "</b>" . _PREFS . "</div><br />\n"
                . "<form method=\"post\" action=\"index.php?file=Links&amp;page=admin&amp;op=change_pref\">\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
                . "<tr><td align=\"center\" colspan=\"2\"><big>" . _PREFS . "</big></td></tr>\n"
                . "<tr><td>" . _NUMBERLINK . " :</td><td><input type=\"text\" name=\"max_liens\" size=\"2\" value=\"" . $nuked['max_liens'] . "\" /></td></tr></table>\n"
                . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Links&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function change_pref($max_liens){
        global $nuked, $user;

        $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_liens . "' WHERE name = 'max_liens'");
        // Action
        $texteaction = _ACTIONCONFLINK;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _PREFUPDATED . "\n"
                . "</div>\n"
                . "</div>\n";
                
        redirect("index.php?file=Links&page=admin", 2);
    } 

    function modif_position($cid, $method){
        global $nuked, $user;

        $sqlc = mysql_query("SELECT titre, position FROM " . LINKS_CAT_TABLE . " WHERE cid = '" . $cid . "'");
        list($titre, $position) = mysql_fetch_array($sqlc);
        $titre = mysql_real_escape_string(stripslashes($titre));
        if ($position <=0 AND $method == "up"){
            echo "<div class=\"notification error png_bg\">\n"
                    . "<div>\n"
                    . "" . _CATERRORPOS . "\n"
                    . "</div>\n"
                    . "</div>\n";
                    
            redirect("index.php?file=Links&page=admin&op=main_cat", 2);
            exit();
        }
        if ($method == "up") $upd = mysql_query("UPDATE " . LINKS_CAT_TABLE . " SET position = position - 1 WHERE cid = '" . $cid . "'");
        else if ($method == "down") $upd = mysql_query("UPDATE " . LINKS_CAT_TABLE . " SET position = position + 1 WHERE cid = '" . $cid . "'");
        // Action
        $texteaction = "". _ACTIONPOSLINK .": ".$titre ."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _CATMODIF . "\n"
                . "</div>\n"
                . "</div>\n";
                
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
                . "<div class=\"content-box-header\"><h3>" . _ADMINLINKS . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Links.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Links&amp;page=admin\">" . _NAVLINKS . "</a> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=add_link\">" . _ADDLINK . "</a> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
                . "</b>" . _BROKENLINKS . "<b> | "
                . "<a href=\"index.php?file=Links&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
                . "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
                . "<tr>\n"
                . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
                . "<td style=\"width: 35%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
                . "<td style=\"width: 10%;\" align=\"center\"><b>X</b></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><b>" . _ERASE . "</b></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n"; 

        $i = 0;
        $l = 0;
        $sql = mysql_query("SELECT id, titre, url, broke FROM " . LINKS_TABLE . " WHERE broke > 0 ORDER BY broke DESC, cat");
        $nb_broke = mysql_num_rows($sql);

        if ($nb_broke > 0){
            while (list($link_id, $titre, $url, $broke) = mysql_fetch_array($sql)){
                $titre = printSecuTags($titre);

                $l++;

                echo "<tr>\n"
                        . "<td style=\"width: 10%;\" align=\"center\">" . $l . "</td>\n"
                        . "<td style=\"width: 35%;\"><a href=\"" . $url . "\" title=\"" . $url . "\" onclick=\"window.open(this.href); return false;\"><b>" . $titre . "</b></a></td>\n"
                        . "<td style=\"width: 10%;\" align=\"center\">" . $broke . "</td>\n"
                        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Links&amp;page=admin&amp;op=del_broke&amp;link_id=" . $link_id. "\"><img style=\"border: 0;\" src=\"modules/Links/images/del.gif\" alt=\"\" title=\"" . _ERASEFROMLIST . "\" /></a></td>\n"
                        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Links&amp;page=admin&amp;op=edit_link&amp;link_id=" . $link_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISLINK . "\" /></a></td>\n"
                        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_link('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $link_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISLINK . "\" /></a></td></tr>\n";
            } 
        } 
        else{
            echo "<tr><td align=\"center\" colspan=\"6\">" . _NOLINKINDB . "</td></tr>\n";
        }

        echo "</table><br /><div style=\"text-align: center;\">[ <a href=\"javascript:delbroke();\"><b>" . _ERASELIST . "</b></a> ]</div>\n"
                . "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Links&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function del_broke($link_id){
        global $nuked, $user;

        $sql = mysql_query("UPDATE " . LINKS_TABLE . " SET broke = 0 WHERE id = '" . $link_id . "'");
        // Action
        $texteaction = _ACTION1BROKELINK;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _LINKERASED . "\n"
                . "</div>\n"
                . "</div>\n";
                
        redirect("index.php?file=Links&page=admin&op=main_broken", 2);
    } 

    function del_broken(){
        global $nuked, $user;
        
        $sql = mysql_query("UPDATE " . LINKS_TABLE . " SET broke = 0");
        // Action
        $texteaction = _ACTIONALLBROKELINK;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _LISTERASED . "\n"
                . "</div>\n"
                . "</div>\n";
                
        redirect("index.php?file=Links&page=admin&op=main_broken", 2);
    } 

    switch ($_REQUEST['op']){
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
} 
else if ($level_admin == -1){
    echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
            . "</div>\n"
            . "</div>\n";
}
else if ($visiteur > 1){
    echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
            . "</div>\n"
            . "</div>\n";
}
else{
    echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
            . "</div>\n"
            . "</div>\n";
} 

adminfoot();
?>