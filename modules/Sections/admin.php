<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined('INDEX_CHECK')) die('<div style="text-align:center;">You cannot open this page directly</div>');

global $user, $language;
translate("modules/Sections/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");

$visiteur = (!$user) ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1){
    function main(){
        global $nuked, $language;

        $nb_max = 30;

        $sql3 = mysql_query("SELECT artid FROM " . SECTIONS_TABLE);
        $nb_art = mysql_num_rows($sql3);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_max - $nb_max;

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
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _NAVART . "<b> | "
                . "<a href=\"index.php?file=Sections&amp;page=admin&amp;op=add\">" . _ADDART . "</a> | "
                . "<a href=\"index.php?file=Sections&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
                . "<a href=\"index.php?file=Sections&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n";

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
            echo "<b>" . _AUTHOR . "</b> | ";
        } 
        else{
            echo "<a href=\"index.php?file=Sections&amp;page=admin&amp;orderby=author\">" . _AUTHOR . "</a> | ";
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
                . "<td style=\"width: 20%;\" align=\"center\"><b>" . _AUTHOR . "</b></td>\n"
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
                    . "<td style=\"width: 10%;\" align=\"center\"><a href=\"javascript:del_art('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $art_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISART . "\" /></a></td></tr>\n";
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

        echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>";
    } 

    function add(){
        global $language;
        
        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINSECTIONS . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Sections&amp;page=admin\">" . _NAVART . "</a> | "
                . "</b>" . _ADDART . "<b> | "
                . "<a href=\"index.php?file=Sections&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
                . "<a href=\"index.php?file=Sections&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
                . "<form method=\"post\" action=\"index.php?file=Sections&amp;page=admin&amp;op=do_add\" onsubmit=\"backslash('art_texte');\">\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
                . "<tr><td><b>" . _TITLE . " :</b>&nbsp;<input id=\"art_titre\" type=\"text\" name=\"titre\" maxlength=\"100\" size=\"45\" /></td></tr>\n"
                . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\">\n";

        select_art_cat();

        echo "</select></td></tr>\n";

        echo "<tr><td><b>" . _TEXT . " :</b><br /><textarea class=\"editor\" class=\"editor\" id=\"art_texte\" name=\"texte\" cols=\"70\" rows=\"15\"></textarea></td></tr>\n"
                . "</table>\n"
                . "<div style=\"text-align: center;\"><br /><input type=\"submit\" name=\"Submit\" value=\"" . _ADDART . "\" />"
                . "</div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Sections&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function do_add($titre, $texte, $cat){
        global $nuked, $user;
        
        $titre = mysql_real_escape_string(stripslashes($titre));
        
        if (empty($titre)){
            echo "<div class=\"notification error png_bg\">\n"
                    . "<div>\n"
                    . "" . _TITLEARTFORGOT . "\n"
                    . "</div>\n"
                    . "</div>\n";
                    
            redirect("index.php?file=Sections&page=admin&op=add", 4);
        }
        else {
            $texte = html_entity_decode($texte);
            $texte = mysql_real_escape_string(stripslashes($texte));
            $date = time();
            $auteur = $user[2];
            $auteur_id = $user[0];
        
            $sql = mysql_query("INSERT INTO " . SECTIONS_TABLE . " ( `artid` , `secid` , `title` , `content` , `autor` , `autor_id`, `counter` , `date`) VALUES ( '' , '" . $cat . "' , '" . $titre . "' , '" . $texte . "' , '" . $auteur . "' , '" . $auteur_id . "' , '' , '" . $date . "' )");
            // Action
            $texteaction = "". _ACTIONADDSEC .": ". $titre .".";
            $acdate = time();
            $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
            //Fin action
            echo "<div class=\"notification success png_bg\">\n"
                    . "<div>\n"
                    . "" . _ARTADD . "\n"
                    . "</div>\n"
                    . "</div>\n";
                    
            $sql2 = mysql_query("SELECT artid FROM " . SECTIONS_TABLE . " WHERE title = '" . $titre . "' AND date='".$date."'");
            list($artid) = mysql_fetch_array($sql2);
            
            echo "<script type=\"text/javascript\">\n"
                    ."//<![CDATA[\n"
                    ."setTimeout('screen()','3000');\n"
                    ."function screen() { \n"
                    ."screenon('index.php?file=Sections&op=article&artid=".$artid."', 'index.php?file=Sections&page=admin');\n"
                    ."}\n"
                    ."//]]>\n"
                    ."</script>\n";
        }
    } 

    function edit($art_id){
        global $nuked, $language;
        
        $sql = mysql_query("SELECT title, content, secid FROM " . SECTIONS_TABLE . " WHERE artid = '" . $art_id . "'");
        list($titre, $texte, $cat) = mysql_fetch_array($sql);
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
                . "<div class=\"content-box-header\"><h3>" . _ADMINSECTIONS . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Sections&amp;page=admin&amp;op=do_edit\" onsubmit=\"backslash('art_texte');\">\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
                . "<tr><td><b>" . _TITLE . " :</b>&nbsp;<input id=\"art_titre\" type=\"text\" name=\"titre\" maxlength=\"100\" size=\"45\" value=\"" . $titre . "\" /></td></tr>\n"
                . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"" . $cid . "\">" . $categorie . "</option>\n";

        select_art_cat();

        echo "</select></td></tr>\n";
        
        echo "<tr><td><b>" . _TEXT . " :</b><br /><textarea class=\"editor\" id=\"art_texte\" name=\"texte\" cols=\"70\" rows=\"15\" >" . $texte . "</textarea></td></tr>\n"
                . "<tr><td>&nbsp;<input type=\"hidden\" name=\"artid\" value=\"" . $art_id . "\" /></td></tr></table>\n"
                . "<div style=\"text-align: center;\"><br /><input type=\"submit\" name=\"Submit\" value=\"" . _MODIFTHISART . "\" />"
                . "</div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Sections&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function do_edit($art_id, $titre, $texte, $cat){
        global $nuked, $user;
        
        $titre = mysql_real_escape_string(stripslashes($titre));
        
        if (empty($titre)){
            echo "<div class=\"notification error png_bg\">\n"
                    . "<div>\n"
                    . "" . _TITLEARTFORGOT . "\n"
                    . "</div>\n"
                    . "</div>\n";

            redirect("index.php?file=Sections&page=admin&op=add", 4);
        }
        else{
            $texte = html_entity_decode($texte);
            $texte = mysql_real_escape_string(stripslashes($texte));
        
            $upd = mysql_query("UPDATE " . SECTIONS_TABLE . " SET secid = '" . $cat . "', title = '" . $titre . "', content = '" . $texte . "' WHERE artid = '" . $art_id . "'");
            // Action
            $texteaction = "". _ACTIONMODIFSEC .": ". $titre .".";
            $acdate = time();
            $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
            //Fin action
            echo "<div class=\"notification success png_bg\">\n"
                    . "<div>\n"
                    . "" . _ARTMODIF . "\n"
                    . "</div>\n"
                    . "</div>\n";
                    
            echo "<script type=\"text/javascript\">\n"
                    ."//<![CDATA[\n"
                    ."setTimeout('screen()','3000');\n"
                    ."function screen() { \n"
                    ."screenon('index.php?file=Sections&op=article&artid=".$art_id."', 'index.php?file=Sections&page=admin');\n"
                    ."}\n"
                    ."//]]>\n"
                    ."</script>\n";
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
        // Action
        $texteaction = "". _ACTIONDELSEC .": ". $titre .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _ARTDEL . "\n"
                . "</div>\n"
                . "</div>\n";
                
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
                . "<div class=\"content-box-header\"><h3>" . _ADMINSECTIONS . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Sections&amp;page=admin\">" . _NAVART . "</a> | "
                . "<a href=\"index.php?file=Sections&amp;page=admin&amp;op=add\">" . _ADDART . "</a> | "
                . "</b>" . _CATMANAGEMENT . "<b> | "
                . "<a href=\"index.php?file=Sections&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
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
                        . "<td style=\"width: 10%;\" align=\"center\"><a href=\"javascript:del_cat('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
            } 
        } 
        else{
            echo "<tr><td align=\"center\" colspan=\"5\">" . _NONE . "&nbsp;" . _CAT . "&nbsp;" . _INDATABASE . "</td></tr>\n";
        }

        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Sections&amp;page=admin&amp;op=add_cat\"><b>" . _ADDCAT . "</b></a> ]</div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Sections&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function add_cat(){
        global $language, $nuked;

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINSECTIONS . "</h3>\n"
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
                . "<div style=\"text-align: center;\"><br /><input type=\"submit\" name=\"send\" value=\"" . _CREATECAT . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Sections&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function send_cat($parentid, $titre, $description, $position){
        global $nuked, $user;
        
        $titre = mysql_real_escape_string(stripslashes($titre));
        
        if (empty($titre)){
            echo "<div class=\"notification error png_bg\">\n"
                    . "<div>\n"
                    . "" . _TITLECATFORGOT . "\n"
                    . "</div>\n"
                    . "</div>\n";
                    
            redirect("index.php?file=Sections&page=admin&op=main_cat", 4);
        }
        else{
            $description = html_entity_decode($description);
            $description = mysql_real_escape_string(stripslashes($description));
            $position = intval($position);
        
            $sql = mysql_query("INSERT INTO " . SECTIONS_CAT_TABLE . " ( `parentid` , `secname` , `description`, `position` ) VALUES ( '" . $parentid . "' , '" . $titre . "' , '" . $description. "' , '" . $position ."' )");
            // Action
            $texteaction = "". _ACTIONADDCATSEC .": ". $titre .".";
            $acdate = time();
            $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
            //Fin action
            echo "<div class=\"notification success png_bg\">\n"
                    . "<div>\n"
                    . "" . _CATADD . "\n"
                    . "</div>\n"
                    . "</div>\n";
                    
            $sql = mysql_query("SELECT secid FROM " . SECTIONS_CAT_TABLE . " WHERE secname = '" . $titre . "' AND parentid='" . $parentid . "'");
            list($secid) = mysql_fetch_array($sql);
            
            echo "<script type=\"text/javascript\">\n"
                    ."//<![CDATA[\n"
                    ."setTimeout('screen()','3000');\n"
                    ."function screen() { \n"
                    ."screenon('index.php?file=Sections&op=categorie&secid=".$secid."', 'index.php?file=Sections&page=admin&op=main_cat');\n"
                    ."}\n"
                    ."//]]>\n"
                    ."</script>\n";
        }
    } 

    function edit_cat($cid){
        global $nuked, $language;

        $sql = mysql_query("SELECT secname, parentid, description, position FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $cid . "'");
        list($titre, $parentid, $description, $position) = mysql_fetch_array($sql);

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINSECTIONS . "</h3>\n"
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

        echo "</select></td></tr>\n"
                . "<tr><td><b>" . _POSITION . " :</b> <input type=\"text\" name=\"position\" size=\"2\" value=\"" . $position . "\" /></td></tr>\n"
                . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
                . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\">" . $description . "</textarea></td></tr>\n"
                . "<tr><td>&nbsp;<input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /></td></tr></table>\n"
                . "<div style=\"text-align: center;\"><input type=\"submit\" name=\"send\" value=\"" . _MODIFTHISCAT . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Sections&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
     } 

    function modif_cat($cid, $parentid, $titre, $description, $position){
        global $nuked, $user;
        
        $titre = mysql_real_escape_string(stripslashes($titre));
        
        if (empty($titre)){
            echo "<div class=\"notification error png_bg\">\n"
                    . "<div>\n"
                    . "" . _TITLECATFORGOT . "\n"
                    . "</div>\n"
                    . "</div>\n";
                    
            redirect("index.php?file=Sections&page=admin&op=main_cat", 4);
        }
        else{
            $description = html_entity_decode($description);
            $description = mysql_real_escape_string(stripslashes($description));
            $position = intval($position);
        
            $sql = mysql_query("UPDATE " . SECTIONS_CAT_TABLE . " SET parentid = '" . $parentid . "', secname = '" . $titre . "', description = '" . $description. "', position = '" . $position . "' WHERE secid = '" . $cid . "'");
            // Action
            $texteaction = "". _ACTIONMODIFCATSEC .": ". $titre .".";
            $acdate = time();
            $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
            //Fin action
            echo "<div class=\"notification success png_bg\">\n"
                    . "<div>\n"
                    . "" . _CATMODIF . "\n"
                    . "</div>\n"
                    . "</div>\n";
                    
            echo "<script type=\"text/javascript\">\n"
                    ."//<![CDATA[\n"
                    ."setTimeout('screen()','3000');\n"
                    ."function screen() { \n"
                    ."screenon('index.php?file=Sections&op=categorie&secid=".$cid."', 'index.php?file=Sections&page=admin&op=main_cat');\n"
                    ."}\n"
                    ."//]]>\n"
                    ."</script>\n";
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
        // Action
        $texteaction = "". _ACTIONDELCATSEC .": ". $titre .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _CATDEL . "\n"
                . "</div>\n"
                . "</div>\n";
                
        redirect("index.php?file=Sections&page=admin&op=main_cat", 2);
    } 

    function main_pref(){
        global $nuked, $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINSECTIONS . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Sections.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Sections&amp;page=admin\">" . _NAVART . "</a> | "
                . "<a href=\"index.php?file=Sections&amp;page=admin&amp;op=add\">" . _ADDART . "</a> | "
                . "<a href=\"index.php?file=Sections&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
                . "</b>" . _PREFS . "</div><br />\n"
                . "<form method=\"post\" action=\"index.php?file=Sections&amp;page=admin&amp;op=change_pref\">\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
                . "<tr><td colspan=\"2\" align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
                . "<tr><td>" . _SECTIONSPG . " :</td><td><input type=\"text\" name=\"max_sections\" size=\"2\" value=\"" . $nuked['max_sections'] . "\" /></td></tr></table>\n"
                . "<div style=\"text-align: center;\"><br /><input type=\"submit\" name=\"Submit\" value=\"" . _SEND . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Sections&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function change_pref($max_sections){
        global $nuked, $user;

        $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_sections . "' WHERE name = 'max_sections'");
        // Action
        $texteaction = "". _ACTIONCONFSEC .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _PREFUPDATED . "\n"
                . "</div>\n"
                . "</div>\n";
                
        redirect("index.php?file=Sections&page=admin", 2);
    } 

    function modif_position($cid, $method){
        global $nuked, $user;

        $sql = mysql_query("SELECT secname, position FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $cid . "'");
        list($titre, $position) = mysql_fetch_array($sql);
        if ($position <= 0 AND $method == "up"){
            echo "<div class=\"notification error png_bg\">\n"
                    . "<div>\n"
                    . "" . _CATERRORPOS . "\n"
                    . "</div>\n"
                    . "</div>\n";
                    
            redirect("index.php?file=Sections&page=admin&op=main_cat", 2);
            exit();
        }
        $titre = mysql_real_escape_string(stripslashes($titre));
        if ($method == "up") $upd = mysql_query("UPDATE " . SECTIONS_CAT_TABLE . " SET position = position - 1 WHERE secid = '" . $cid . "'");
        else if ($method == "down") $upd = mysql_query("UPDATE " . SECTIONS_CAT_TABLE . " SET position = position + 1 WHERE secid = '" . $cid . "'");
        
        // Action
        $texteaction = "". _ACTIONPOSSEC .": ". $titre ."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _CATMODIF . "\n"
                . "</div>\n"
                . "</div>\n";
                
        redirect("index.php?file=Sections&page=admin&op=main_cat", 2);
    }
    
    switch ($_REQUEST['op']){
        case "main":
            admintop(); 
            main();
            adminfoot();
            break;
        case "add":
            admintop(); 
            add();
            adminfoot();
            break;
        case "do_add":
            admintop(); 
            do_add($_REQUEST['titre'], $_REQUEST['texte'], $_REQUEST['cat']);
            adminfoot();
            break;
        case "edit":
            admintop(); 
            edit($_REQUEST['artid']);
            adminfoot();
            break;
        case "do_edit":
            admintop(); 
            do_edit($_REQUEST['artid'], $_REQUEST['titre'], $_REQUEST['texte'], $_REQUEST['cat']);
            adminfoot();
            break;
        case "del":
            admintop(); 
            del($_REQUEST['artid']);
            adminfoot();
            break;
        case "send_cat":
            admintop(); 
            send_cat($_REQUEST['parentid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['position']);
            adminfoot();
            break;
        case "add_cat":
            admintop(); 
            add_cat();
            adminfoot();
            break;
        case "main_cat":
            admintop(); 
            main_cat();
            adminfoot();
            break;
        case "edit_cat":
            admintop(); 
            edit_cat($_REQUEST['cid']);
            adminfoot();
            break;
        case "modif_cat":
            admintop(); 
            modif_cat($_REQUEST['cid'], $_REQUEST['parentid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['position']);
            adminfoot();
            break;
        case "del_cat":
            admintop(); 
            del_cat($_REQUEST['cid']);
            adminfoot();
            break;
        case "main_pref":
            admintop(); 
            main_pref();
            adminfoot();
            break;
        case "change_pref":
            admintop(); 
            change_pref($_REQUEST['max_sections']);
            adminfoot();
            break;
        case "modif_position":
            admintop(); 
            modif_position($_REQUEST['cid'], $_REQUEST['method']);
            adminfoot();
            break;
        default:
            admintop();
            main();
            adminfoot();
            break;
    } 
} 
else if ($level_admin == -1){
    admintop();
    
    echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
            . "</div>\n"
            . "</div>\n";
            
    adminfoot();
}
else if ($visiteur > 1){
    admintop();
    
    echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
            . "</div>\n"
            . "</div>\n";
        
    adminfoot();
}
else{
    admintop();
    
    echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
            . "</div>\n"
            . "</div>\n";
    adminfoot();
}
?>