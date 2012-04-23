<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die('<div style="text-align:center;">You cannot open this page directly</div>');

global $user, $language;
translate("modules/Wars/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");

$visiteur = !$user ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1){
    function main(){
        global $nuked, $language;

        admintop();

        echo "<script type=\"text/javascript\">\n"
                ."<!--\n"
                ."\n"
                . "function del_match(adversaire, id)\n"
                . "{\n"
                . "if (confirm('" . _DELETEMATCH . " '+adversaire+' ! " . _CONFIRM . "'))\n"
                . "{document.location.href = 'index.php?file=Wars&page=admin&op=del_war&war_id='+id;}\n"
                . "}\n"
                    . "\n"
                . "// -->\n"
                . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINMATCH . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Wars.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _MATCHES . "<b> | "
                . "<a href=\"index.php?file=Wars&amp;page=admin&amp;op=match&amp;do=add\">" . _ADDMATCH . "</a> | "
                . "<a href=\"index.php?file=Wars&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
                . "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
                . "<tr>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><b>" . _STATUS . "</b></td>\n"
                . "<td style=\"width: 20%;\" align=\"center\"><b>" . _OPPONENT . "</b></td>\n"
                . "<td style=\"width: 20%;\" align=\"center\"><b>" . _TEAM . "</b></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT warid, team, adversaire, url_adv, etat, date_jour, date_mois, date_an FROM " . WARS_TABLE . " ORDER BY etat, date_an DESC, date_mois DESC, date_jour DESC");
        $count = mysql_num_rows($sql);
        while (list($war_id, $team, $adv_name, $adv_url, $status, $jour, $mois, $an) = mysql_fetch_array($sql)){
            $adv_name = printSecuTags($adv_name);

            if ($status > 0){
                $etat = _FINISH;
            } 
            else{
                $etat = _HASTOPLAY;
            } 

            if ($team > 0){
                $sql2 = mysql_query("SELECT titre FROM " . TEAM_TABLE . " WHERE cid = '" . $team . "'");
                list($team_name) = mysql_fetch_array($sql2);
                $team_name = printSecuTags($team_name);
            } 
            else{
                $team_name = "N/A";
            } 

            if ($language == "french"){
                $date = $jour . "/" . $mois . "/" . $an;
            } 
            else{
                $date = $mois . "/" . $jour . "/" . $an;
            } 

            echo "<tr>\n"
                    . "<td style=\"width: 15%;\" align=\"center\">" . $date . "</td>\n"
                    . "<td style=\"width: 15%;\" align=\"center\">" . $etat . "</td>\n"
                    . "<td style=\"width: 20%;\" align=\"center\">";

            if ($adv_url != ""){
                echo "<a href=\"" . $adv_url . "\" title=\"" . $adv_url . "\" onclick=\"window.open(this.href); return false;\">" . $adv_name . "</a>";
            } 
            else{
                echo $adv_name;
            } 

            echo "</td><td style=\"width: 20%;\" align=\"center\">" . $team_name . "</td>\n"
                    . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Wars&amp;page=admin&amp;op=match&amp;do=edit&amp;war_id=" . $war_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISMATCH . "\" /></a></td>\n"
                    . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_match('" . mysql_real_escape_string(stripslashes($adv_name)) . "', '" . $war_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISMATCH . "\" /></a></td></tr>\n";
        } 

        if ($count == 0){
            echo "<tr><td colspan=\"6\" align=\"center\">" . _NOMATCH . "</td></tr>\n";
        } 

        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>";

        adminfoot();
    } 

    function match($war_id){
        global $nuked, $user, $language;

        admintop();
        
        if ($_REQUEST['do'] == "edit"){
            $sql = mysql_query("SELECT etat, team, game, adversaire, url_adv, pays_adv, type, style, date_jour, date_mois, date_an, heure, map, score_team, score_adv, tscore_team, tscore_adv, report, url_league FROM " . WARS_TABLE . " WHERE warid='".$war_id."'");
            list($status, $team, $game, $adv_name, $adv_url, $pays_adv, $type, $style, $jour, $mois, $an, $heure, $map, $score_team, $score_adv, $tscore_team, $tscore_adv, $report, $url_league) = mysql_fetch_array($sql);
            
            $adv_name = htmlspecialchars($adv_name);
            $map = explode('|', $map);
            $score_team = explode('|', $score_team);
            $score_adv = explode('|', $score_adv);
            $nbr = count($map);
            $_REQUEST['game'] = $game;
        }
        
        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINMATCH . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Wars.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\">\n";
                
        if ($_REQUEST['do'] == "add"){
            echo "<div style=\"text-align: center;\"><b><a href=\"index.php?file=Wars&amp;page=admin\">" . _MATCHES . "</a> | "
                    . "</b>" . _ADDMATCH . "<b> | "
                    . "<a href=\"index.php?file=Wars&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n";
        } 

        if ($_REQUEST['do'] == "edit"){
            $action = "do_edit&amp;war_id=" . $war_id;
        } 
        else if ($_REQUEST['do'] == "add"){
            $action = "add_war";
            $nbr = $_REQUEST['nbr'];
        } 

        if ($status > 0){
            $etat = _FINISH;
            $checked1 = "selected=\"selected\"";    
        } 
        else{
            $etat = _HASTOPLAY;
            $checked2 = "selected=\"selected\"";
        }
        
        if ($_REQUEST['do'] == "add" && !isset($_REQUEST['nbr']) && !isset($_REQUEST['game'])){
            echo "<form method=\"post\" action=\"index.php?file=Wars&amp;page=admin&amp;op=match&amp;do=add\">\n"
                    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n"
                    . "<tr><td align=\"center\"><b>" . _NOMBRE . " :</b><br/></td></tr>\n"
                    . "<tr><td align=\"center\"><input type=\"text\" name=\"nbr\" maxlength=\"2\" size=\"10\" value=\"0\" /></td></tr>\n"
                    . "<tr><td align=\"center\"><b>" . _GAME . " : </b><select name=\"game\">\n";

            $sql3 = mysql_query("SELECT id, name FROM " . GAMES_TABLE . " ORDER BY name");
            while (list($id, $name) = mysql_fetch_array($sql3)){
                $name = printSecuTags($name);

                if ($id == $game){
                    $checked4 = "selected=\"selected\"";
                } 
                else{
                    $checked4 = "";
                }
                
                echo "<option value=\"" . $id . "\" " . $checked4 . ">" . $name . "</option>\n";
            } 

            echo "</select></table>\n"
                    . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
                    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Wars&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
            
            adminfoot();
            exit();
        }
        
        echo "<form method=\"post\" action=\"index.php?file=Wars&amp;page=admin&amp;op=" . $action . "\">\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n"
                . "<tr><td align=\"center\"><b>" . _STATUS . " :</b> <select name=\"etat\">\n"
                . "<option value=\"1\" " . $checked1 . ">" . _FINISH . "</option>\n"
                . "<option value=\"0\" " . $checked2 . ">" . _HASTOPLAY . "</option>\n"
                . "</select>&nbsp;&nbsp;<b>" . _TEAM . " : </b><select name=\"team\"><option value=\"\">" . _NONE . "</option>\n";

        $sql2 = mysql_query("SELECT cid, titre FROM " . TEAM_TABLE . " ORDER BY ordre, titre");
        while (list($cid, $titre) = mysql_fetch_array($sql2)){
            $titre = htmlentities($titre);

            if ($cid == $team){
                $checked3 = "selected=\"selected\"";
            } 
            else{
                $checked3 = "";
            }
            
            echo "<option value=\"" . $cid . "\" " . $checked3 . ">" . $titre . "</option>\n";
        }
        
        echo "</select></td></tr><tr><td align=\"center\"><b>" . _DATE . " : </b><select name=\"jour\">\n";

        $day = 1;
        while ($day < 32){
            if ($day == date("d") && $_REQUEST['do'] == "add"){
                echo "<option value=\"$day\" selected=\"selected\">" . $day . "</option>\n";
            } 
            else if ($day == $jour && $_REQUEST['do'] == "edit"){
                echo "<option value=\"" . $day . "\" selected=\"selected\">" . $day . "</option>\n";
            } 
            else{
                echo "<option value=\"" . $day . "\">" . $day . "</option>\n";
            }            
            $day++;
        } 

        echo "</select>&nbsp;<select name=\"mois\">";

        $month = 1;
        while ($month < 13){
            if ($month == date("m") && $_REQUEST['do'] == "add"){
                echo "<option value=\"" . $month . "\" selected=\"selected\">" . $month . "</option>\n";
            } 
            else if ($month == $mois && $_REQUEST['do'] == "edit"){
                echo "<option value=\"" . $month . "\" selected=\"selected\">" . $month . "</option>\n";
            } 
            else{
                echo "<option value=\"" . $month . "\">" . $month . "</option>\n";
            } 
            $month++;
        } 

        echo"</select>&nbsp;<select name=\"annee\">";

        if ($_REQUEST['do'] == "edit"){
            $prevprevprevyear = $an - 3;
            $prevprevyear = $an - 2;
            $prevyear = $an - 1;
            $year = $an;
            $nextyear = $an + 1;
            $nextnextyear = $an + 2;
        } 
        else{
            $prevprevprevyear = date(Y) - 3;
            $prevprevyear = date(Y) - 2;
            $prevyear = date(Y) - 1;
            $year = date(Y);
            $nextyear = date(Y) + 1;
            $nextnextyear = date(Y) + 2;
        } 

        echo "<option value=\"" . $prevprevprevyear . "\">" . $prevprevprevyear . "</option>\n"
                . "<option value=\"" . $prevprevyear . "\">" . $prevprevyear . "</option>\n"
                . "<option value=\"" . $prevyear . "\">" . $prevyear . "</option>\n"
                . "<option value=\"" . $year . "\" selected=\"selected\">" . $year . "</option>\n";

        if ($_REQUEST['do'] == "add"){
            $heure = date("H:i");
        } 

        echo "<option value=\"" . $nextyear . "\">" . $nextyear . "</option>\n"
                . "<option value=\"" . $nextnextyear . "\">" . $nextnextyear . "</option>\n"
                . "</select>&nbsp;&nbsp;<b>" . _HOUR . " :</b> <input type=\"text\" name=\"heure\" size=\"5\" maxlength=\"5\" value=\"" . $heure . "\" /></td></tr></table>\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\"><tr><td>&nbsp;</td></tr>\n"
                . "<tr><td align=\"center\"><big>" . _OPPONENT . "</big></td></tr>\n"
                . "<tr><td><b>" . _NAME . " : </b><input type=\"text\" name=\"adversaire\" maxlength=\"100\" size=\"20\" value=\"" . $adv_name . "\" />&nbsp;&nbsp;<b>" . _URL . " : </b><input type=\"text\" name=\"url_adv\" size=\"30\" maxlength=\"100\" value=\"" . $adv_url . "\" /></td></tr>\n"
                . "<tr><td><b>" . _COUNTRY . " : </b> <select name=\"country\">\n";

        if ($_REQUEST['do'] == "add" && $language == "french"){
            $pays_adv = "France.gif";
        } 

        $rep = Array();
        $handle = @opendir("images/flags");
        while (false !== ($f = readdir($handle))){
            if ($f != ".." && $f != "." && $f != "index.html" && $f != "Thumbs.db"){
                $rep[] = $f;
            }
        }

        closedir($handle);
        sort ($rep);
        reset ($rep);

        while (list ($key, $filename) = each ($rep)){
            if ($filename == $pays_adv){
                $checked5 = "selected=\"selected\"";
            } 
            else{
                $checked5 = "";
            } 

            list ($country, $ext) = explode ('.', $filename);
            echo "<option value=\"" . $filename . "\" " . $checked5 . ">" . $country . "</option>\n";
        } 

        echo "</select><input type=\"hidden\" name=\"game\" value=\"".$_REQUEST['game']."\"/></td></tr></table>\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\"><tr><td>&nbsp;</td></tr>\n"
        . "<tr><td align=\"center\"><big>" . _MATCH . "</big></td></tr>\n"
        . "<tr><td><b>" . _TYPE . " : </b><input type=\"text\" name=\"type\" maxlength=\"100\" size=\"20\" value=\"" . $type . "\" />&nbsp;&nbsp;<b>" . _STYLE . " : </b><input type=\"text\" name=\"style\" maxlength=\"100\" size=\"20\" value=\"" . $style . "\" /></td></tr>\n"
        . "<input type=\"hidden\" name=\"nbr\" value=\"" . $nbr . "\" />\n";
        
        $sql3 = mysql_query("SELECT map FROM " . GAMES_TABLE . " WHERE id=".mysql_real_escape_string($_REQUEST['game']) ." ORDER BY name");
        list($mapss) = mysql_fetch_array($sql3);
        $mapss = explode('|', $mapss);
        for($maps =1; $maps <= $nbr; $maps++){
            $mapis = $mapss;
            echo "<tr><td><b>Map n° ".$maps.": </b></td></tr><tr><td><select name=\"map_".$maps."\">\n";
            foreach ($mapis as $mapping){
                $mapping = printSecuTags($mapping);

                if ($mapping == $map[$maps-1]){
                    $checked3 = "selected=\"selected\"";
                } 
                else{
                    $checked3 = "";
                }
                
                if ($mapping != "")
                echo "<option value=\"" . $mapping . "\" " . $checked3 . ">" . $mapping . "</option>\n";
            }
            
            echo "</select>";
            echo "</td></tr><tr><td><b>" . _OURSCORE . " : </b><input type=\"text\" name=\"score_team".$maps."\" maxlength=\"10\" size=\"5\" value=\"" . $score_team[$maps-1] . "\" />&nbsp;&nbsp;<b>" . _OPPSCORE . " : </b><input type=\"text\" name=\"score_adv".$maps."\" maxlength=\"10\" size=\"5\" value=\"" . $score_adv[$maps-1] . "\" /></td></tr>\n";
        }
    
        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\"><tr><td>&nbsp;</td></tr>\n"
                . "<tr><td align=\"center\"><big>" . _REPORT . "</big></td></tr>\n"
                . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"report\" cols=\"60\" rows=\"10\">" . $report . "</textarea></td></tr>\n"
                . "<tr><td align=\"center\"><b>" . _URLREPORT . " :</b> <input type=\"text\" name=\"url_league\" size=\"35\" maxlength=\"200\" value=\"" . $url_league . "\" /></td></tr></table>\n";

        if ($_REQUEST['do'] == "edit"){
            $sql4 = mysql_query("SELECT id FROM " . WARS_FILES_TABLE . " WHERE module = 'Wars' AND im_id = '" . $war_id ."'");
            $nb_file = mysql_num_rows($sql4);

            if ($nb_file > 0){
                echo "<div style=\"text-align: center;\"><br />[ <a href=\"#\" onclick=\"javascript:window.open('index.php?file=Wars&amp;nuked_nude=admin&amp;op=main_file&amp;im_id=" . $war_id . "','popup','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=340,top=30,left=0');return(false)\">" . _ADDMODFILE . "</a> ]</div>\n";
            } 
            else{
                echo "<div style=\"text-align: center;\"><br />[ <a href=\"#\" onclick=\"javascript:window.open('index.php?file=Wars&amp;nuked_nude=admin&amp;op=add_file&amp;im_id=" . $war_id ."','popup','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=340,top=30,left=0');return(false)\">" . _ADDFILE . "</a> ]</div>\n";
            } 
        } 

        echo "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Wars&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";

        adminfoot();
    } 

    Function add_war($etat, $team, $game, $jour, $mois, $annee, $heure, $adversaire, $url_adv, $country, $type, $style, $report, $url_league){
        global $nuked, $user;

        $autor = $user[2];
        $report = html_entity_decode($report);
        $adversaire = mysql_real_escape_string(stripslashes($adversaire));
        $report = mysql_real_escape_string(stripslashes($report));
        $type = mysql_real_escape_string(stripslashes($type));
        $style = mysql_real_escape_string(stripslashes($style));
        
        for($maps =1; $maps < $_REQUEST['nbr']; $maps++){
            $map .= ''.str_replace("|", "&#124;",$_REQUEST['map_'.$maps.'']).'|';
            $score_team .= ''.str_replace("|", "&#124;",$_REQUEST['score_team'.$maps.'']).'|';
            $tscore_team += $_REQUEST['score_team'.$maps.''];
            $score_adv .= ''.str_replace("|", "&#124;",$_REQUEST['score_adv'.$maps.'']).'|';
            $tscore_adv += $_REQUEST['score_adv'.$maps.''];
        }
        
        $map .= ''.str_replace("|", "&#124;",$_REQUEST['map_'.$_REQUEST['nbr'].'']).'';
        $score_team .= ''.str_replace("|", "&#124;",$_REQUEST['score_team'.$_REQUEST['nbr'].'']).'';
        $tscore_team += $_REQUEST['score_team'.$_REQUEST['nbr'].''];
        $score_adv .= ''.str_replace("|", "&#124;",$_REQUEST['score_adv'.$_REQUEST['nbr'].'']).'';
        $tscore_adv += $_REQUEST['score_adv'.$_REQUEST['nbr'].''];
        
        if ($url_adv != "" && !preg_match("`http://`i", $url_adv)){
            $url_adv = "http://" . $url_adv;
        } 

        if ($url_league != "" && !preg_match("`http://`i", $url_league)){
            $url_league = "http://" . $url_league;
        }
        
        $add = mysql_query("INSERT INTO " . WARS_TABLE . " ( `warid` , `etat` , `team` , `game` , `adversaire` , `url_adv` , `pays_adv` , `type` , `style` , `date_jour` , `date_mois` , `date_an` , `heure` , `map` ,  `score_team` , `score_adv` , `tscore_team` , `tscore_adv` , `report` , `auteur` , `url_league` , `dispo` , `pas_dispo` ) VALUES ( '' , '" . $etat . "' , '" . $team . "' , '" . $game . "' , '" . $adversaire . "' , '" . $url_adv . "' , '" . $country ."' , '" . $type. "' , '" . $style . "' , '" . $jour . "' , '" . $mois . "' , '" . $annee . "' , '" . $heure . "' , '" . $map . "' , '" . $score_team . "' , '" . $score_adv . "' , '" . $tscore_team . "' , '" . $tscore_adv . "' , '" . $report . "' , '" . $autor . "' , '" . $url_league . "' , '' , '' )");
        admintop();
        // Action
        $texteaction = "". _ACTIONADDWAR .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _MATCHADD . "\n"
                . "</div>\n"
                . "</div>\n";
        
        echo "<script>\n"
                ."setTimeout('screen()','3000');\n"
                ."function screen() { \n"
                ."screenon('index.php?file=Wars', 'index.php?file=Wars&page=admin');\n"
                ."}\n"
                ."</script>\n";
                
        adminfoot();
    } 

    function del_war($war_id){
        global $nuked, $user;

        $del = mysql_query("DELETE FROM " . WARS_TABLE . " WHERE warid = '" . $war_id . "'");
        $del_com = mysql_query("DELETE FROM " . COMMENT_TABLE . " WHERE im_id = '" . $war_id . "' AND module = 'Wars'");
        $del_file = mysql_query("DELETE FROM " . WARS_FILES_TABLE . " WHERE im_id = '" . $war_id . "' AND module = 'Wars'");
        admintop();
        // Action
        $texteaction = "". _ACTIONDELWAR .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _MATCHDEL . "\n"
                . "</div>\n"
                . "</div>\n";
                
        echo "<script>\n"
                ."setTimeout('screen()','3000');\n"
                ."function screen() { \n"
                ."screenon('index.php?file=Wars', 'index.php?file=Wars&page=admin');\n"
                ."}\n"
                ."</script>\n";
                
        adminfoot();
    } 

    function do_edit($war_id, $etat, $team, $game, $jour, $mois, $annee, $heure, $adversaire, $url_adv, $country, $type, $style, $report, $url_league){
        global $nuked, $user;

        $report = html_entity_decode($report);
        $adversaire = mysql_real_escape_string(stripslashes($adversaire));
        $report = mysql_real_escape_string(stripslashes($report));
        $type = mysql_real_escape_string(stripslashes($type));
        $style = mysql_real_escape_string(stripslashes($style));
        
        for($maps =1; $maps < $_REQUEST['nbr']; $maps++){
            $map .= ''.str_replace("|", "&#124;",$_REQUEST['map_'.$maps.'']).'|';
            $score_team .= ''.str_replace("|", "&#124;",$_REQUEST['score_team'.$maps.'']).'|';
            $tscore_team += $_REQUEST['score_team'.$maps.''];
            $score_adv .= ''.str_replace("|", "&#124;",$_REQUEST['score_adv'.$maps.'']).'|';
            $tscore_adv += $_REQUEST['score_adv'.$maps.''];
        }
        
        $map .= ''.str_replace("|", "&#124;",$_REQUEST['map_'.$_REQUEST['nbr'].'']).'';
        $score_team .= ''.str_replace("|", "&#124;",$_REQUEST['score_team'.$_REQUEST['nbr'].'']).'';
        $tscore_team += $_REQUEST['score_team'.$_REQUEST['nbr'].''];
        $score_adv .= ''.str_replace("|", "&#124;",$_REQUEST['score_adv'.$_REQUEST['nbr'].'']).'';
        $tscore_adv += $_REQUEST['score_adv'.$_REQUEST['nbr'].''];
        
        if ($url_adv != "" && !preg_match("`http://`i", $url_adv)){
            $url_adv = "http://" . $url_adv;
        } 

        if ($url_league != "" && !preg_match("`http://`i", $url_league)){
            $url_league = "http://" . $url_league;
        } 

        $upd = mysql_query("UPDATE " . WARS_TABLE . " SET etat = '" . $etat . "', team = '" . $team . "', game = '" . $game . "', adversaire = '" . $adversaire . "', url_adv = '" . $url_adv . "', pays_adv = '" . $country . "', type = '" . $type . "', style = '" . $style . "', date_jour = '" . $jour . "', date_mois = '" . $mois . "', date_an = '" . $annee . "', heure = '" . $heure . "', map = '" . $map . "', score_team = '" . $score_team . "', score_adv = '" . $score_adv . "', tscore_team = '" . $tscore_team . "', tscore_adv = '" . $tscore_adv . "', report = '" . $report . "', url_league = '" . $url_league . "' WHERE warid = '" . $war_id . "'");
        admintop();
        // Action
        $texteaction = "". _ACTIONMODIFWAR .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _MATCHMODIF . "\n"
                . "</div>\n"
                . "</div>\n";
                
        echo "<script>\n"
                ."setTimeout('screen()','3000');\n"
                ."function screen() { \n"
                ."screenon('index.php?file=Wars', 'index.php?file=Wars&page=admin');\n"
                ."}\n"
                ."</script>\n";
                
        adminfoot();
    } 

    function main_file($im_id){
        global $nuked, $theme, $bgcolor1, $bgcolor2, $bgcolor3;

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _ADMINMATCH . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background: " . $bgcolor2 . ";\">\n";

        echo "<script type=\"text/javascript\">\n"
                ."<!--\n"
                ."\n"
                . "function del_file(type, id)\n"
                . "{\n"
                . "if (confirm('" . _DEL . " '+type+' ! " . _CONFIRM . "'))\n"
                . "{document.location.href = 'index.php?file=Wars&nuked_nude=admin&op=del_file&fid='+id;}\n"
                . "}\n"
                    . "\n"
                . "// -->\n"
                . "</script>\n";

        echo "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Wars&amp;nuked_nude=admin&amp;op=add_file&amp;im_id=" . $im_id . "\"><b>" . _ADDFILE . "</b></a> ]<br /><br /></div>\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
                . "<tr style=\"background: ". $bgcolor3 . "\">\n"
                . "<td align=\"center\"><b>" . _TYPE . "</b></td>\n"
                . "<td align=\"center\"><b>" . _EDIT . "</b></td>\n"
                . "<td align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, type, url FROM " . WARS_FILES_TABLE . " WHERE module = 'Wars' AND im_id = '" . $im_id . "'");
        while (list($fid, $type, $url) = mysql_fetch_array($sql)){
            if ($type == "screen"){
                $typename = _IMG;
            } 
            else if ($type == "demo"){
                $typename = _DEMO;
            } 
            else{
                $typename = $type;
            } 

            if ($j == 0){
                $bg = $bgcolor2;
                $j++;
            } 
            else{
                $bg = $bgcolor1;
                $j = 0;
            } 

            echo "<tr style=\"background: ". $bg . "\">\n"
                    . "<td align=\"center\"><a href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\">" . $typename . "</a></td>\n"
                    . "<td align=\"center\"><a href=\"index.php?file=Wars&amp;nuked_nude=admin&amp;op=edit_file&amp;fid=" . $fid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITFILE . "\" /></a></td>\n"
                    . "<td align=\"center\"><a href=\"javascript:del_file('" . mysql_real_escape_string(stripslashes($typename)) . "', '" . $fid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELETEFILE . "\" /></a></td></tr>\n";
        }
        
        echo "</table><div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"javascript:window.close()\"><b>" . _CLOSEWINDOW . "</b></a></div></body></html>";
    } 

    function add_file($im_id){
        global $nuked, $theme, $bgcolor2;

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _ADMINMATCH . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background: " . $bgcolor2 . ";\">\n"
                . "<form method=\"post\" action=\"index.php?file=Wars&amp;nuked_nude=admin&amp;op=send_file\" enctype=\"multipart/form-data\">\n"
                . "<div style=\"text-align: center;\"><br /><big><b>" . _ADDFILE . "</b></big></div>\n"
                . "<div><br /><b>" . _URL . " :</b> <input type=\"text\" size=\"40\" name=\"url_file\" /><br />\n"
                . "<br /><b>" . _UPFILE . " :</b><br /><input type=\"file\" name=\"fichiernom\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" /> " . _REPLACE . "<br />\n"
                . "<b>" . _TYPE . " :</b> <select name=\"file_type\"><option value=\"screen\">" . _IMG . "</option><option value=\"demo\">" . _DEMO . "</option></select><br />\n"
                . "<input type=\"hidden\" name=\"im_id\" value=\"" . $im_id . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"javascript:window.close()\"><b>" . _CLOSEWINDOW . "</b></a></div></form></body></html>";
    } 

    function edit_file($fid){
        global $nuked, $theme, $bgcolor2;

        $sql = mysql_query("SELECT im_id, type, url FROM " . WARS_FILES_TABLE . " WHERE id = '" . $fid . "'");
        list($im_id, $type, $url) = mysql_fetch_array($sql);

        if ($type == "screen"){
            $typename = _IMG;
            $checked1 = "selected=\"selected\"";

        } 
        else if ($type == "demo"){
            $typename = _DEMO;
            $checked2 = "selected=\"selected\"";
        } 
        else{
            $typename = $type;
        } 

      echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
            . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
            . "<head><title>" . _ADMINMATCH . "</title>\n"
            . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
            . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
            . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
            . "<body style=\"background: " . $bgcolor2 . ";\">\n"
            . "<form method=\"post\" action=\"index.php?file=Wars&amp;nuked_nude=admin&amp;op=modif_file\" enctype=\"multipart/form-data\">\n"
            . "<div style=\"text-align: center;\"><br /><big><b>" . _ADDFILE . "</b></big></div>\n"
            . "<div><br /><b>" . _URL . " :</b> <input type=\"text\" size=\"40\" name=\"url_file\" value=\"" . $url . "\" /><br />\n"
            . "<br /><b>" . _UPFILE . " :</b><br /><input type=\"file\" name=\"fichiernom\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" /> " . _REPLACE . "<br />\n"
            . "<b>" . _TYPE . " :</b> <select name=\"file_type\"><option value=\"screen\" " . $checked1 . ">" . _IMG . "</option><option value=\"demo\" " . $checked2 . ">" . _DEMO . "</option></select><br />\n"
            . "<input type=\"hidden\" name=\"im_id\" value=\"" . $im_id . "\" /><input type=\"hidden\" name=\"fid\" value=\"" . $fid . "\" /></div>\n"
            . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
            . "<div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"javascript:window.close()\"><b>" . _CLOSEWINDOW . "</b></a></div></form></body></html>";
    } 

    function send_file($im_id, $file_type, $url_file, $fichiernom, $ecrase_screen){
        global $nuked, $theme, $bgcolor2;

        if ($_FILES['fichiernom']['name'] != "" || $url_file != ""){     
            $racine_up = "upload/Wars/";
            $filename = $_FILES['fichiernom']['name'];
            $file_url = $racine_up . $filename;
    
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
                
            if (!is_file($file_url) || $ecrase_screen == 1){
                if($filename != "" && $file_type == "screen"){
                    if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext) || preg_match("`gif`i", $ext) || preg_match("`png`i", $ext))){
                        move_uploaded_file($_FILES['fichiernom']['tmp_name'], $file_url) or die ("Upload file failed !!!");
                        @chmod ($file_url, 0644);
                    }
                    else{
                        echo "<br /><br /><div style=\"text-align: center;\">No image file !!!</div><br /><br />";
                        redirect("index.php?file=Wars&nuked_nude=admin&op=add_file&im_id=" . $im_id, 2);
                        exit();
                    }            
                }
                else if($filename != "" && $file_type == "demo"){
                    if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && $filename != ".htaccess"){
                        move_uploaded_file($_FILES['fichiernom']['tmp_name'], $file_url) or die ("Upload file failed !!!");
                        @chmod ($file_url, 0644);
                    }
                    else{
                        echo "<br /><br /><div style=\"text-align: center;\">Unauthorized file !!!</div><br /><br />";
                        redirect("index.php?file=Wars&nuked_nude=admin&op=add_file&im_id=" . $im_id, 2);
                        exit();
                    }    
                }
                else{
                    $file_url = $url_file;
                }
        
                $add = mysql_query("INSERT INTO " . WARS_FILES_TABLE . " ( `id` , `module` , `im_id` , `type` , `url` ) VALUES ( '' , 'Wars' , '" . $im_id . "' , '" . $file_type . "' , '" . $file_url . "' )");
    
                echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                        . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                        . "<head><title>" . _ADMINMATCH . "</title>\n"
                        . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                        . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                        . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                        . "<body style=\"background: " . $bgcolor2 . ";\">\n"
                        . "<div style=\"text-align: center;\"><br /><br /><br /><br />" . _FILEADD . "</div></body></html>";

                redirect("index.php?file=Wars&nuked_nude=admin&op=main_file&im_id=" . $im_id, 2);
            }
            else{
                echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                        . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                        . "<head><title>" . _ADMINMATCH . "</title>\n"
                        . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                        . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                        . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                        . "<body style=\"background: " . $bgcolor2 . ";\">\n"
                        . "<div style=\"text-align: center;\"><br /><br /><br /><br />" . _DEJAFILE . "<br />" . _REPLACEIT . "</div></body></html>";

                redirect("index.php?file=Wars&nuked_nude=admin&op=add_file&im_id=" . $im_id, 3);
            }
        } 
        else{
            echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                    . "<head><title>" . _ADMINMATCH . "</title>\n"
                    . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                    . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                    . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                    . "<body style=\"background: " . $bgcolor2 . ";\">\n"
                    . "<div style=\"text-align: center;\"><br /><br /><br /><br />" . _SPECIFY . "</div></body></html>";

            redirect("index.php?file=Wars&nuked_nude=admin&op=add_file&im_id=" . $im_id, 3);
        }
    } 

    function modif_file($im_id, $fid, $file_type, $url_file, $fichiernom, $ecrase_screen){
        global $nuked, $theme, $bgcolor2;

        if ($_FILES['fichiernom']['name'] != ""){     
            $racine_up = "upload/Wars/";
            $filename = $_FILES['fichiernom']['name'];
            $file_url = $racine_up . $filename;
    
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
                
            if (!is_file($file_url) || $ecrase_screen == 1){
                if($filename != "" && $file_type == "screen"){
                    if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext) || preg_match("`gif`i", $ext) || preg_match("`png`i", $ext))){
                        move_uploaded_file($_FILES['fichiernom']['tmp_name'], $file_url) or die ("Upload file failed !!!");
                        @chmod ($file_url, 0644);
                    }
                    else{
                        echo "<br /><br /><div style=\"text-align: center;\">No image file !!!</div><br /><br />";
                        redirect("index.php?file=Wars&nuked_nude=admin&op=edit_file&fid=" . $fid, 3);
                        exit();
                    }            
                }
                else if($filename != "" && $file_type == "demo"){
                    if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && $filename != ".htaccess"){
                        move_uploaded_file($_FILES['fichiernom']['tmp_name'], $file_url) or die ("Upload file failed !!!");
                        @chmod ($file_url, 0644);
                     }
                     else{
                        echo "<br /><br /><div style=\"text-align: center;\">Unauthorized file !!!</div><br /><br />";
                        redirect("index.php?file=Wars&nuked_nude=admin&op=edit_file&fid=" . $fid, 3);
                        exit();
                    }            
                }
            }
            else{
                echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                        . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                        . "<head><title>" . _ADMINMATCH . "</title>\n"
                        . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                        . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                        . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                        . "<body style=\"background: " . $bgcolor2 . ";\">\n"
                        . "<div style=\"text-align: center;\"><br /><br /><br /><br /><br /><br /><br /><br />" . _DEJAFILE . "<br />" . _REPLACEIT . "</div></body></html>";

                redirect("index.php?file=Wars&nuked_nude=admin&op=edit_file&fid=" . $fid, 3);
                exit();
            }    

        }
        else{
            $file_url = $url_file;
        }

        $upd = mysql_query("UPDATE " . WARS_FILES_TABLE . " SET type = '" . $file_type . "' , url = '" . $file_url . "' WHERE id = '" . $fid . "'");

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _ADMINMATCH . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background: " . $bgcolor2 . ";\">\n"
                . "<div style=\"text-align: center;\"><br /><br /><br /><br />" . _FILEADD . "</div></body></html>";

        redirect("index.php?file=Wars&nuked_nude=admin&op=main_file&im_id=" . $im_id, 2);
    }
    
    function del_file($fid){
        global $nuked, $theme, $bgcolor2;

        $sql = mysql_query("SELECT im_id FROM " . WARS_FILES_TABLE . " WHERE id = '" . $fid . "'");
        list($im_id) = mysql_fetch_array($sql);

        $del = mysql_query("DELETE FROM " . WARS_FILES_TABLE . " WHERE id = '" . $fid . "'");

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
                . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
                . "<head><title>" . _ADMINMATCH . "</title>\n"
                . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
                . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
                . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
                . "<body style=\"background: " . $bgcolor2 . ";\">\n"
                . "<div style=\"text-align: center;\"><br /><br /><br /><br />" . _FILEDEL . "</div></body></html>";

        redirect("index.php?file=Wars&nuked_nude=admin&op=main_file&im_id=" . $im_id, 2);
    } 

    function main_pref(){
        global $nuked, $language;

        admintop();

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINMATCH . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Wars.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Wars&amp;page=admin\">" . _MATCHES . "</a> | "
                . "<a href=\"index.php?file=Wars&amp;page=admin&amp;op=match&amp;do=add\">" . _ADDMATCH . "</a> | "
                . "</b>" . _PREFS . "</div><br />\n"
                . "<form method=\"post\" action=\"index.php?file=Wars&amp;page=admin&amp;op=change_pref\">\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
                . "<tr><td align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
                . "<tr><td>" . _NUMBERWARS . " :</td><td> <input type=\"text\" name=\"max_wars\" size=\"2\" value=\"" . $nuked['max_wars'] . "\" /></td></tr>\n"
                . "</table><div style=\"text-align: center;\"></br><a href=\"index.php?file=Admin&amp;page=games\">"._MANAGETEAMMAP."</a><br/>\n"
                . "<br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Wars&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";

        adminfoot();
    } 

    function change_pref($max_wars){
        global $nuked, $user;

        $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_wars . "' WHERE name = 'max_wars'");
        admintop();
        // Action
        $texteaction = "". _ACTIONCONFWAR .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _PREFUPDATED . "\n"
                . "</div>\n"
                . "</div>\n";
                
        adminfoot();
        redirect("index.php?file=Wars&page=admin", 2);
    } 

    switch ($_REQUEST['op']){
        case "main":
            main();
            break;

        case "match":
            match($_REQUEST['war_id']);
            break;

        case "add_war":
            add_war($_REQUEST['etat'], $_REQUEST['team'], $_REQUEST['game'], $_REQUEST['jour'], $_REQUEST['mois'], $_REQUEST['annee'], $_REQUEST['heure'], $_REQUEST['adversaire'], $_REQUEST['url_adv'], $_REQUEST['country'], $_REQUEST['type'], $_REQUEST['style'], $_REQUEST['report'], $_REQUEST['url_league']);
            break;

        case "del_war":
            del_war($_REQUEST['war_id']);
            break;

        case "do_edit":
            do_edit($_REQUEST['war_id'], $_REQUEST['etat'], $_REQUEST['team'], $_REQUEST['game'], $_REQUEST['jour'], $_REQUEST['mois'], $_REQUEST['annee'], $_REQUEST['heure'], $_REQUEST['adversaire'], $_REQUEST['url_adv'], $_REQUEST['country'], $_REQUEST['type'], $_REQUEST['style'], $_REQUEST['report'], $_REQUEST['url_league']);
            break;

        case "main_file":
            main_file($_REQUEST['im_id']);
            break;

        case "add_file":
            add_file($_REQUEST['im_id']);
            break;

        case "edit_file":
            edit_file($_REQUEST['fid']);
            break;

        case "send_file":
            send_file($_REQUEST['im_id'], $_REQUEST['file_type'], $_REQUEST['url_file'], $_REQUEST['fichiernom'], $_REQUEST['ecrase_screen']);
            break;

        case "modif_file":
            modif_file($_REQUEST['im_id'], $_REQUEST['fid'], $_REQUEST['file_type'], $_REQUEST['url_file'], $_REQUEST['fichiernom'], $_REQUEST['ecrase_screen']);
            break;

        case "del_file":
            del_file($_REQUEST['fid']);
            break;

        case "main_pref":
            main_pref();
            break;

        case "change_pref":
            change_pref($_REQUEST['max_wars']);
            break;

        default:
            main();
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