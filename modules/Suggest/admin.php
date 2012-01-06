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
translate('modules/Suggest/lang/' . $language . '.lang.php');
include('modules/Admin/design.php');
admintop();

echo "<script type=\"text/javascript\">\n"
. "<!--\n"
. "\n"
."function trim(string)\n"
."{"
."return string.replace(/(^\s*)|(\s*$)/g,'');"
."}\n"
. "// -->\n"
. "</script>\n";

$visiteur = !$user ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1){
    function main(){
        global $nuked, $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINSUGGEST . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Suggest.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _SUGGEST . "<b> | "
        . "<a href=\"index.php?file=Suggest&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _SUGGESTID . "</b></td>\n"
        . "<td style=\"width: 30%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
        . "<td style=\"width: 30%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
        . "<td style=\"width: 30%;\" align=\"center\"><b>" . _DATE . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, module, date, user_id FROM " . SUGGEST_TABLE . " ORDER BY module, date");
        $count = mysql_num_rows($sql);
        
        while (list($sug_id, $mod_name, $date, $id_user) = mysql_fetch_array($sql)){
            $date = nkDate($date);

            $sql2 = mysql_query("SELECT id, pseudo FROM " . USER_TABLE . " WHERE id = '" . $id_user . "'");
            $nb_user = mysql_num_rows($sql2);

            if ($nb_user > 0){
                list($user_for, $pseudo) = mysql_fetch_array($sql2);
            }
            else{
                $pseudo = _VISITOR . ' (' . $id_user . ')';
                $user_for = 0;
            }

            echo "<tr>\n"
            . "<td style=\"width: 10%;\" align=\"center\">" . $sug_id . "</td>\n"
            . "<td style=\"width: 30%;\" align=\"center\"><a href=\"index.php?file=Suggest&amp;page=admin&amp;op=show_suggest&amp;sug_id=" . $sug_id . "\" title=\"" . _SEESUGGEST . "\">" . $mod_name . "</a></td>\n"
            . "<td style=\"width: 30%;\" align=\"center\">" . $pseudo . "</td>\n"
            . "<td style=\"width: 30%;\" align=\"center\">" . $date . "</td></tr>\n";
        }

        if ($count == 0){
            echo '<tr><td colspan="4" align="center">' . _NOSUGGEST . '</td></tr>';
        }

        echo "</table><br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }

    function show_suggest($sug_id){
        global $nuked, $language;

        $sql = mysql_query("SELECT module, date, user_id, proposition FROM " . SUGGEST_TABLE . " WHERE id = '" . intval($sug_id) . "'");
        list($mod_name, $date, $id_user, $proposition) = mysql_fetch_array($sql);
        $date = nkDate($date);
        $content = explode('|', $proposition);

        $sql2 = mysql_query("SELECT id, pseudo FROM " . USER_TABLE . " WHERE id = '" . $id_user . "'");
        $nb_user = mysql_num_rows($sql2);

        if ($nb_user > 0){
            list($user_for, $pseudo) = mysql_fetch_array($sql2);
        }
        else{
            $pseudo = _VISITOR . " (" . $id_user . ")";
            $user_for = 0;
        }

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINSUGGEST . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Suggest.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _SUGGESTBY . "&nbsp;" . $pseudo . "&nbsp;" . _THE . "&nbsp;" . $date . "</div>\n";

        require("modules/Suggest/modules/" . $mod_name . ".php");
        form($content, $sug_id, $user_for);
        echo "</div></div>";
    }

    function valid_suggest($data){
        global $nuked, $user;

        require("modules/Suggest/modules/" . $_REQUEST['module'] . ".php");
        send($data);

        $del = mysql_query("DELETE FROM " . SUGGEST_TABLE . " WHERE id = '" . $data['sug_id'] . "'");
        // Action
        $texteaction = "". _ACTIONVALIDSUG .": ". $data['titre'] .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".mysql_real_escape_string(stripslashes($texteaction))."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _SUGGESTADD . "\n"
                . "</div>\n"
                . "</div>\n";
    }

    function del($sug_id){
        global $nuked, $user, $language;

        $sql = mysql_query("SELECT user_id, module, proposition FROM " . SUGGEST_TABLE . " WHERE id='" . intval($sug_id) . "' ");
        list($for, $module, $data) = mysql_fetch_array($sql);

        if(!empty($module))
            include("modules/Suggest/modules/" . $module . ".php");

        $sql = mysql_query("DELETE FROM " . SUGGEST_TABLE . " WHERE id = '" . $sug_id . "'");
        // Action
        $texteaction = _ACTIONDELSUG . '.';
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action

        //Envoi du MP de refus
        if ($for != "" && preg_match("`^[a-zA-Z0-9]+$`", $for) && isset($_REQUEST['subject']) && isset($_REQUEST['corps'])){
            $sql = mysql_query("INSERT INTO " . USERBOX_TABLE . " ( `mid` , `user_from` , `user_for` , `titre` , `message` , `date` , `status` ) VALUES ( '' , '" . $user[0] . "' , '" . $for . "' , '" . $_REQUEST['subject'] . "' , '" . $_REQUEST['corps'] . "' , '" . time() . "' , '0' )");
        }

            echo "<div class=\"notification success png_bg\">\n"
            . "<div>\n"
            . _SUGGESTDEL . "\n"
            . "</div>\n"
            . "</div>\n";

        redirect("index.php?file=Suggest&page=admin", 2);
    }

    function raison($sug_id){
        global $nuked, $user;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINSUGGEST . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Suggest.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><table style=\"width:50%; border:0; align:center; valign:top; margin-left:auto; margin-right:auto\" cellspacing=\"1\" cellpadding=\"1\"><tr>\n"
                . "<td align=\"center\" colspan=\"2\"><form method=\"POST\" action=\"index.php?file=Suggest&amp;page=admin&amp;op=del&amp;sug_id=" . $sug_id . "\"><br><h3>"._RMOTIF."</h3></td></tr>\n"
                . "<tr><td align=\"left\"><b>"._RSUBJECT."</b> : </td><td><input type=\"text\" name=\"subject\" maxlength=\"100\" value=\"" . _REFUS2 . "\" size=\"45\"></td></tr>\n"
                . "<tr><td align=\"left\" valign=\"top\"><b>"._RCORPS."</b> : </td><td><textarea class=\"editor\" id=\"raison\" name=\"corps\" rows=\"10\" cols=\"39\" />" . _REFUS . "\n" . $user[2] . "</textarea></td></tr>\n"
                . "<tr><td colspan=\"2\"><p align=\"center\">&nbsp;<input type=\"submit\" value=\""._SEND."\"> <input type=\"button\" value=\""._CANCEL."\" onclick=\"document.location.href='index.php?file=Suggest&page=admin&op=show_suggest&sug_id=" . $sug_id . "';\"></td></tr>\n"
                . "</form></table><br><center>[ <a href=\"index.php?file=Suggest&amp;page=admin\"><b>"._BACK."</b></a> ]</center><br></div></div>";
    }

    function main_pref(){
        global $nuked, $language;

        if ($nuked['suggest_avert'] == "on"){
            $checked = "checked=\"checked\"";
        }
        else{
                $checked = "";
        }

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
                . "<div class=\"content-box-header\"><h3>" . _ADMINSUGGEST . "</h3>\n"
                . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Suggest.php\" rel=\"modal\">\n"
                . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
                . "</div></div>\n"
                . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Suggest&amp;page=admin\">" . _SUGGEST . "</a> | "
                . "</b>" . _PREFS . "</div><br />\n"
                . "<form method=\"post\" action=\"index.php?file=Suggest&amp;page=admin&amp;op=change_pref\">\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
                . "<tr><td align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
                . "<tr><td>" . _SUGGESTMAIL . " : <input class=\"checkbox\" type=\"checkbox\" name=\"suggest_avert\" value=\"on\" " . $checked. " /></td></tr>\n"
                . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
                . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Suggest&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>";
    }

    function change_pref($suggest_avert){
        global $nuked, $user;

        if ($suggest_avert != 'on'){
            $suggest_avert = "off";
        }

        $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $suggest_avert . "' WHERE name = 'suggest_avert'");
        // Action
        $texteaction = "". _ACTIONCONFSUG .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
                . "<div>\n"
                . "" . _PREFUPDATED . "\n"
                . "</div>\n"
                . "</div>\n";
        redirect("index.php?file=Suggest&page=admin", 2);
    }

    switch ($_REQUEST['op']){
        case"main":
        main();
        break;

        case"module":
        module($_REQUEST['mod_name']);
        break;

        case"show_suggest":
        show_suggest($_REQUEST['sug_id']);
        break;

        case"valid_suggest":
        valid_suggest($_REQUEST);
        break;

        case"del":
        del($_REQUEST['sug_id']);
        break;

        case"raison":
        raison($_REQUEST['sug_id']);
        break;

        case "main_pref":
        main_pref();
        break;

        case "change_pref":
        change_pref($_REQUEST['suggest_avert']);
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