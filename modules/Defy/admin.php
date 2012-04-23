<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('<div style="text-align: center;">You cannot open this page directly</div>');

translate('modules/Defy/lang/' . $language . '.lang.php');

include 'modules/Admin/design.php';

admintop();

$visiteur = ($user) ? $user[1] : 0;
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);

if ($visiteur >= $level_admin && $level_admin > -1) {
    function main(){
        global $nuked, $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
           . "<div class=\"content-box-header\"><h3>" . _DEFY . "</h3>\n"
           . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Defy.php\" rel=\"modal\">\n"
           . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
           . "</div></div>\n"
           . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _DEFY . "<b> | "
           . "<a href=\"index.php?file=Defy&amp;page=admin&amp;op=edit_pref\">" . _PREFS . "</a></b></div><br />\n"
           . "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
           . "<tr>\n"
           . "<td style=\"width: 25%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
           . "<td style=\"width: 15%;\" align=\"center\"><b>" . _CLAN . "</b></td>\n"
           . "<td style=\"width: 20%;\" align=\"center\"><b>" . _GAME . "</b></td>\n"
           . "<td style=\"width: 20%;\" align=\"center\"><b>" . _MAIL . "</b></td>\n"
           . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, pseudo, send, mail, clan, game FROM " . DEFY_TABLE . " ORDER BY id DESC");
        $count = mysql_num_rows($sql);
        while (list($did, $pseudo, $date, $mail, $clan, $game) = mysql_fetch_array($sql)){
            $date = nkDate($date);
            
            $sql2 = mysql_query("SELECT name FROM " . GAMES_TABLE . " WHERE id='" . $game . "'");
            list($game_name) = mysql_fetch_array($sql2);
            $game_name = printSecuTags($game_name);

            echo "<tr>"
               . "<td style=\"width: 25%;\" align=\"center\"><a href=\"index.php?file=Defy&amp;page=admin&amp;op=view&amp;did=" . $did . "\">" . $pseudo . "</a></td>\n"
               . "<td style=\"width: 15%;\" align=\"center\">" . $clan . "</td>\n"
               . "<td style=\"width: 20%;\" align=\"center\">" . $game_name . "</td>\n"
               . "<td style=\"width: 20%;\" align=\"center\"><a href=\"mailto:" . $mail . "\">" . $mail . "</a></td>\n"
               . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td></tr>\n";
        }
        
        if ($count == 0) {
            echo "<tr><td colspan=\"5\" align=\"center\">" . _NODEFY . "</td></tr>\n";
        }
        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }

    function view($did) {
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
           . "<!--\n"
           . "\n"
           . "function del_defie(pseudo, id)\n"
           . "{\n"
           . "if (confirm('" . _DELETEMATCH . " '+pseudo+' ! " . _CONFIRM . "'))\n"
           . "{document.location.href = 'index.php?file=Defy&page=admin&op=del&did='+id;}\n"
           . "}\n"
           . "\n"
           . "// -->\n"
           . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
           . "<div class=\"content-box-header\"><h3>" . _DEFY . "</h3>\n"
           . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Defy.php\" rel=\"modal\">\n"
           . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
           . "</div></div>\n"
           . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">[ <a href=\"index.php?file=Defy&amp;page=admin&amp;op=transfert&amp;did=" . $did . "\"><b>" . _TRANSFERT . "</b></a> ]</div><br />\n"
           . "<table width=\"90%\" style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\"><tr><td>\n";

        $sql = mysql_query("SELECT pseudo, clan, mail, icq, irc, url, pays, date, heure, serveur, game, type, map, comment FROM " . DEFY_TABLE . " WHERE id = '" . $did . "'");
        list($pseudo, $clan, $mail, $icq, $irc, $url, $country, $date, $heure, $serveur, $game, $type, $map, $comment) = mysql_fetch_array($sql);
        list ($pays, $ext) = explode ('.', $country);

        $sql2 = mysql_query("SELECT name FROM " . GAMES_TABLE . " WHERE id = '" . $game . "'");
        list($game_name) = mysql_fetch_array($sql2);
        $game_name = printSecuTags($game_name);

        echo "<b>" . _NICK . " : </b>" . $pseudo . "<br />\n"
           . "<b>" . _CLAN . " : </b>" . $clan . "<br />\n"
           . "<b>" . _MAIL . " : </b><a href=\"mailto:" . $mail . "\">" . $mail . "</a><br />\n"
           . "<b>" . _ICQMSN . " : </b>" . $icq . "<br />\n"
           . "<b>" . _CHANIRC . " : </b>" . $irc . "<br />\n"
           . "<b>" . _URL . " : </b><a href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\">" . $url . "</a><br />\n"
           . "<b>" . _COUNTRY . " : </b>" . $pays . "<br />\n"
           . "<b>" . _DATE . " : </b>" . $date . "<br />\n"
           . "<b>" . _HOUR . " : </b>" . $heure . "<br />\n"
           . "<b>" . _SERVER . " : </b>" . $serveur . "<br />\n"
           . "<b>" . _GAME . " : </b>" . $game_name . "<br />\n"
           . "<b>" . _MATCH . " : </b>" . $type . "<br />\n"
           . "<b>" . _MAP . " : </b>" . $map . "<br /><br />\n"
           . "<b>" . _COMMENT . " : </b>" . $comment . "<br /><br />\n"
           . "</td></tr></table><div style=\"text-align: center;\"><input type=\"submit\" value=\"" . _REMOVE . "\" onclick=\"javascript:del_defie('" . mysql_real_escape_string(stripslashes($pseudo)) . "', '" . $did . "');\" /></div>\n"
           . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Defy&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }

    function del($did) {
        global $nuked, $user;
        
        $sql = mysql_query("SELECT pseudo FROM " . DEFY_TABLE . " WHERE id = '" . $did . "'");
        list($pseudo) = mysql_fetch_array($sql);
        
        $del = mysql_query("DELETE FROM " . DEFY_TABLE . " WHERE id = '" . $did . "'");
        
        // Action
        $texteaction = _ACTIONDELDEFY . ' ' . $pseudo;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO " . $nuked['prefix'] . "_action  (`date`, `pseudo`, `action`)  VALUES ('" . $acdate . "', '" . $user[0] . "', '" . $texteaction . "')");
        //Fin action
        
        echo "<div class=\"notification success png_bg\">\n"
           . "<div>\n"
           . _DEFIEDELETE . "\n"
           . "</div>\n"
           . "</div>\n";
        redirect('index.php?file=Defy&page=admin', 2);
    }

    function transfert($did) {
        global $nuked, $user;

        $sql = mysql_query("SELECT pseudo, clan, url, pays, date, heure, game, type, map FROM " . DEFY_TABLE . " WHERE id = '" . $did . "'");
        list($pseudo, $clan, $url, $pays, $date, $heure, $game, $type, $map) = mysql_fetch_array($sql);
        list($date_jour, $date_mois, $date_an) = explode('-', $date);

        $insert = mysql_query("INSERT INTO " . WARS_TABLE . " ( `warid` , `etat` , `team` , `game` , `adversaire` , `url_adv` , `pays_adv` , `type` , `style` , `date_jour` , `date_mois` , `date_an` , `heure` , `map` , `tscore_team` , `tscore_adv` , `score_team` , `score_adv` , `report` , `auteur` , `url_league` , `dispo` , `pas_dispo` ) VALUES ( '' , '0' , '' , '" . mysql_real_escape_string($game) . "' , '" . mysql_real_escape_string($clan) . "' , '" . mysql_real_escape_string($url) . "' , '" . mysql_real_escape_string($pays) . "' , '" . mysql_real_escape_string($type) . "' , '' , '" . mysql_real_escape_string($date_jour) . "' , '" .mysql_real_escape_string($date_mois) . "' , '" . mysql_real_escape_string($date_an) . "' , '" . mysql_real_escape_string($heure) . "' , '" . mysql_real_escape_string($map) . "' , '' , '' , '' , '' , '' , '" . $user[2] . "' , '' , '' , '' )");

        $sql_match = mysql_query("SELECT warid FROM " . WARS_TABLE . " WHERE adversaire = '" . $clan . "'");
        list($warid) = mysql_fetch_array($sql_match);

        $del = mysql_query("DELETE FROM " . DEFY_TABLE . " WHERE id = '" . $did . "'");
        // Action
        $texteaction = _ACTIONTRANDEFY . ' ' . $pseudo;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        
        echo "<div class=\"notification success png_bg\">\n"
           . "<div>\n"
           . _DEFIETRANSFERT . "\n"
           . "</div>\n"
           . "</div>\n";

        $url_redirect = 'index.php?file=Wars&page=admin&op=match&do=edit&war_id=' . $warid;
        redirect($url_redirect, 2);
    }

    function edit_pref() {
        global $nuked, $language;

        $charte = $nuked['defie_charte'];

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
           . "<div class=\"content-box-header\"><h3>" . _DEFY . "</h3>\n"
           . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Defy.php\" rel=\"modal\">\n"
           . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
           . "</div></div>\n"
           . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Defy&amp;page=admin\">" . _DEFY . "</a> | "
           . "</b>" . _PREFS . "</div><br />\n"
           . "<form method=\"post\" action=\"index.php?file=Defy&amp;page=admin&amp;op=update_pref\">\n"
           . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
           . "<tr><td align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
           . "<tr><td><b>" . _MAILAVERT . "</b> : <input type=\"text\" size=\"30\" name=\"defie_mail\" value=\"" . $nuked['defie_mail'] . "\" /></td></tr>\n"
           . "<tr><td><b>" . _INBOXAVERT . "</b> : <select name=\"defie_inbox\"><option value=\"\">" . _OFF . "</option>\n";

        $sql2 = mysql_query("SELECT id, pseudo FROM " . USER_TABLE . " WHERE niveau > 1 ORDER BY niveau DESC");
        while (list($id_user, $pseudo) = mysql_fetch_array($sql2)) {
            if ($nuked['defie_inbox'] == $id_user) {
                $checked = "selected=\"selected\"";
            }
            echo "<option value=\"" . $id_user . "\" " . $checked . ">" . $pseudo . "</option>\n";
        }
        echo "</select></td></tr><tr><td>&nbsp;</td></tr>\n";

        echo "<tr><td><b>" . _CHARTE . "</b> : <br /><textarea class=\"editor\" name=\"defie_charte\" cols=\"65\" rows=\"15\"\">" . $charte . "</textarea></td></tr></table>\n"
           . "<div style=\"text-align: center;\"><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
           . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Defy&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function update_pref($defie_mail, $defie_inbox, $defie_charte) {
        global $nuked, $user;

        $defie_charte = html_entity_decode($defie_charte);
        $defie_charte = mysql_real_escape_string(stripslashes($defie_charte));
        
        $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $defie_charte . "' WHERE name = 'defie_charte'");
        $upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $defie_mail . "' WHERE name = 'defie_mail'");
        $upd3 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $defie_inbox . "' WHERE name = 'defie_inbox'");
        // Action
        $texteaction = _ACTIONPREFDEFY . '.';
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
           . "<div>\n"
           . _PREFUPDATE . "\n"
           . "</div>\n"
           . "</div>\n";
        redirect('index.php?file=Defy&page=admin', 2);
    }

    switch ($_REQUEST['op']){
        case 'view':
        view($_REQUEST['did']);
        break;

        case 'del':
        del($_REQUEST['did']);
        break;

        case 'transfert':
        transfert($_REQUEST['did']);
        break;

        case 'edit_pref':
        edit_pref();
        break;

        case 'update_pref':
        update_pref($_REQUEST['defie_mail'], $_REQUEST['defie_inbox'], $_REQUEST['defie_charte']);
        break;

        default:
        main();
        break;
    }

}
else if ($level_admin == -1) {
    echo "<div class=\"notification error png_bg\">\n"
       . "<div>\n"
       . "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
       . "</div>\n"
       . "</div>\n";
} else if ($visiteur > 1) {
    echo "<div class=\"notification error png_bg\">\n"
       . "<div>\n"
       . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
       . "</div>\n"
       . "</div>\n";
} else {
    echo "<div class=\"notification error png_bg\">\n"
       . "<div>\n"
       . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
       . "</div>\n"
       . "</div>\n";
}

adminfoot();
?>