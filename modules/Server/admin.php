<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die('<div style="text-align: center;">You cannot open this page directly</div>'); 

translate('modules/Server/lang/' . $language . '.lang.php');
include 'modules/Admin/design.php';
admintop();
$visiteur = $user ? $user[1] : 0; 
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);

if ($visiteur >= $level_admin && $level_admin > -1) {
    function main_cat() {
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
           . "<!--\n"
           . "\n"
           . "function del_cat(titre, id)\n"
           . "{\n"
           . "if (confirm('" . _DELETESERV . " '+titre+' ! " . _CONFIRM . "'))\n"
           . "{document.location.href = 'index.php?file=Server&page=admin&op=del_cat&cid='+id;}\n"
           . "}\n"
           . "\n"
           . "// -->\n"
           . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
           . "<div class=\"content-box-header\"><h3>" . _ADMINSERVER . "</h3>\n"
           . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Server.php\" rel=\"modal\">\n"
           . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
           . "</div></div>\n"
           . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Server&amp;page=admin\">" . _SERVER . "</a> | "
           . "<a href=\"index.php?file=Server&amp;page=admin&amp;op=add_serveur\">" . _ADDSERVER . "</a> | "
           . "</b>" . _CATMANAGEMENT . "<b> | "
           . "<a href=\"index.php?file=Server&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
           . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
           . "<tr>\n"
           . "<td style=\"width: 60%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
           . "<td style=\"width: 20%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
           . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT cid, titre FROM " . SERVER_CAT_TABLE . " ORDER BY titre");
        $nbcat = mysql_num_rows($sql);
        if ($nbcat > 0) {
            while (list($cid, $titre) = mysql_fetch_row($sql)) {
                $titre = printSecuTags($titre);

                echo "<tr>\n"
                   . "<td align=\"center\">" . $titre . "</td>\n"
                   . "<td align=\"center\"><a href=\"index.php?file=Server&amp;page=admin&amp;op=edit_cat&amp;cid=" . $cid . "\" ><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
                   . "<td align=\"center\"><a href=\"javascript:del_cat('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
        }
    } else  {
        echo "<tr><td align=\"center\" colspan=\"3\">" . _NONECATINDATABASE . "</td></tr>\n"; 
    }

    echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Server&amp;page=admin&amp;op=add_cat\"><b>" . _ADDCAT . "</b></a> ]</div>\n"
       . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Server&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function add_cat() {
        global $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
           . "<div class=\"content-box-header\"><h3>" . _ADMINSERVER . "</h3>\n"
           . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Server.php\" rel=\"modal\">\n"
           . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
           . "</div></div>\n"
           . "<div class=\"tab-content\" id=\"tab2\"><br /><form method=\"post\" action=\"index.php?file=Server&amp;page=admin&amp;op=send_cat\">\n"
           . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
           . "<tr><td align=\"center\"><b>" . _TITLE . " : </b><input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
           . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _DESCR . " :</b></td></tr>\n"
           . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
           . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _CREATECAT . "\" /></div>\n"
           . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Server&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function send_cat($titre, $description) {
        global $nuked, $user;

        $description = html_entity_decode($description);
        $description = mysql_real_escape_string(stripslashes($description));
        $titre = mysql_real_escape_string(stripslashes($titre));
        
        $sql = mysql_query("INSERT INTO " . SERVER_CAT_TABLE . " ( `cid` , `titre` , `description` ) VALUES ( '" . $cid . "' , '" . $titre . "' , '" . $description . "' )");
        // Action
        $texteaction =  _ACTIONADDCATSER . ': ' . $titre . '.';
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
           . "<div>\n"
           . _CATADD . "\n"
           . "</div>\n"
           . "</div>\n";
        echo "<script>\n"
           . "setTimeout('screen()','3000');\n"
           . "function screen() { \n"
           . "screenon('index.php?file=Server', 'index.php?file=Server&page=admin&op=main_cat');\n"
           . "}\n"
           . "</script>\n";
    } 

    function edit_cat($cid) {
        global $nuked, $language;

        $sql = mysql_query("SELECT titre, description FROM " . SERVER_CAT_TABLE . " WHERE cid = '" . $cid . "'");
        list($titre, $description) = mysql_fetch_array($sql);

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
           . "<div class=\"content-box-header\"><h3>" . _ADMINSERVER . "</h3>\n"
           . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Server.php\" rel=\"modal\">\n"
           . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
           . "</div></div>\n"
           . "<div class=\"tab-content\" id=\"tab2\"><br /><form method=\"post\" action=\"index.php?file=Server&amp;page=admin&amp;op=modif_cat\">\n"
           . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
           . "<tr><td align=\"center\"><b>" . _TITLE . " : </b><input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre . "\" /></td></tr>\n"
           . "<tr><td>&nbsp;<input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /></td></tr><tr><td><b>" . _DESCR . " :</b></td></tr>\n"
           . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\">" . $description . "</textarea></td></tr></table>\n"
           . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _MODIFTHISCAT . "\" /></div>\n"
           . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Server&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function modif_cat($cid, $titre, $description) {
        global $nuked, $user;

        $description = html_entity_decode($description);
        $description = mysql_real_escape_string(stripslashes($description));
        $titre = mysql_real_escape_string(stripslashes($titre));

        $sql = mysql_query("UPDATE " . SERVER_CAT_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "' WHERE cid = '" . $cid . "'");
        // Action
        $texteaction =  _ACTIONMODIFCATSER . ': ' . $titre .' .';
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
           . "<div>\n"
           . _CATMODIF . "\n"
           . "</div>\n"
           . "</div>\n";
        echo "<script>\n"
           . "setTimeout('screen()','3000');\n"
           . "function screen() { \n"
           . "screenon('index.php?file=Server', 'index.php?file=Server&page=admin&op=main_cat');\n"
           . "}\n"
           . "</script>\n";
    } 

    function del_cat($cid) {
        global $nuked, $user;

        $sqls = mysql_query("SELECT titre FROM " . SERVER_CAT_TABLE . " WHERE cid = '" . $cid . "'");
        list($titre) = mysql_fetch_array($sqls);
        $titre = mysql_real_escape_string(stripslashes($titre));
        $sql = mysql_query("DELETE FROM " . SERVER_CAT_TABLE . " WHERE cid = '" . $cid . "'");
        // Action
        $texteaction = _ACTIONDELCATSER . ': ' . $titre . '.';
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
           . "<div>\n"
           . _CATDEL . "\n"
           . "</div>\n"
           . "</div>\n";
        echo "<script>\n"
           . "setTimeout('screen()','3000');\n"
           . "function screen() { \n"
           . "screenon('index.php?file=Server', 'index.php?file=Server&page=admin&op=main_cat');\n"
           . "}\n"
           . "</script>\n";
    } 

    function select_serv_cat() {
        global $nuked;

        $sql = mysql_query("SELECT cid, titre FROM " . SERVER_CAT_TABLE . " ORDER BY titre");
        while (list($cid, $titre) = mysql_fetch_row($sql)) {
            $titre = printSecuTags($titre);

            echo "<option value=\"" . $cid . "\">" . $titre . "</option>\n";
        } 
    } 

    function add_serveur() {
        global $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
           . "<div class=\"content-box-header\"><h3>" . _ADMINSERVER . "</h3>\n"
           . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Server.php\" rel=\"modal\">\n"
           . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
           . "</div></div>\n"
           . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Server&amp;page=admin\">" . _SERVER . "</a> | "
           . "</b>" . _ADDSERVER . "<b> | "
           . "<a href=\"index.php?file=Server&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
           . "<a href=\"index.php?file=Server&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
           . "<form method=\"post\" action=\"index.php?file=Server&amp;page=admin&amp;op=send_serveur\">\n"
           . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
           . "<tr><td><b>" . _SERVIP . "</b> <input type=\"text\" name=\"ip_serv\" size=\"30\" />&nbsp;<b>" . _SERVPORT . "</b> <input type=\"text\" size=\"10\" maxlength=\"20\" name=\"port\" /></td></tr>\n"
           . "<tr><td><b>" . _SERVERGAME . "</b> <select name=\"game\">\n"
           . "<option value=\"CSS\">CSS</option>\n"
           . "<option value=\"HL2\">HL2</option>\n"
           . "<option value=\"HL\">HL</option>\n"
           . "<option value=\"DOOM3\">DOOM3</option>\n"
           . "<option value=\"FARCRY\">FARCRY</option>\n"
           . "<option value=\"Q3\">Q3</option>\n"
           . "<option value=\"MOHAA\">MOHAA</option>\n"
           . "<option value=\"RTCW\">RTCW</option>\n"
           . "<option value=\"COD\">COD</option>\n"
           . "<option value=\"UT\">UT</option>\n"
           . "<option value=\"UT2003\">UT2003</option>\n"
           . "<option value=\"UT2004\">UT2004</option>\n"
           . "<option value=\"IGI2\">IGI2</option>\n"
           . "<option value=\"NWN\">NWN</option>\n"
           . "<option value=\"AA\">AA</option>\n"
           . "<option value=\"BTF1942\">BTF1942</option>\n"
           . "</select>&nbsp;<b>" . _SERVERPASS . " :</b> <input type=\"text\" name=\"pass\" size=\"10\" /></td></tr>\n"
           . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\">\n";

        select_serv_cat();

        echo "</select></td></tr></table>\n"
           . "<div style=\"text-align: center;\"><br /><input type=\"submit\" name=\"send\" value=\"" . _ADDTHISSERV . "\" /></div>\n"
           . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Server&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>";
    } 

    function send_serveur($ip_serv, $port, $game, $pass, $cat) {
        global $nuked, $user;

        $sql = mysql_query("INSERT INTO " . SERVER_TABLE . " ( `sid` , `game` , `ip` , `port` , `pass` , `cat` ) VALUES ( '' , '" . $game . "' , '" . $ip_serv . "' , '" . $port . "' , '" . $pass . "' , '" . $cat . "' )");
        // Action
        $texteaction = _ACTIONADDSER . '.';
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
           . "<div>\n"
           . _SERVERADD . "\n"
           . "</div>\n"
           . "</div>\n";
        echo "<script>\n"
           . "setTimeout('screen()','3000');\n"
           . "function screen() { \n"
           . "screenon('index.php?file=Server', 'index.php?file=Server&page=admin');\n"
           . "}\n"
           . "</script>\n";
    } 

    function del_serveur($sid) {
        global $nuked, $user;

        $sql = mysql_query("DELETE FROM " . SERVER_TABLE . " WHERE sid = '" . $sid . "'");
        // Action
        $texteaction = _ACTIONDELSER . '.';
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
           . "<div>\n"
           . _SERVERDEL . "\n"
           . "</div>\n"
           . "</div>\n";
        echo "<script>\n"
           . "setTimeout('screen()','3000');\n"
           . "function screen() { \n"
           . "screenon('index.php?file=Server', 'index.php?file=Server&page=admin');\n"
           . "}\n"
           . "</script>\n";
    } 

    function edit_serveur($sid) {
        global $nuked, $language;

        $sql = mysql_query("SELECT game, ip, port, pass, cat FROM " . SERVER_TABLE . " WHERE sid = '" . $sid . "'");
        list($game, $ip_serv, $port, $pass, $cat) = mysql_fetch_array($sql);

        $sql2 = mysql_query("SELECT cid, titre FROM " . SERVER_CAT_TABLE . " WHERE cid = '" . $cat . "'");
        list($cid, $categorie) = mysql_fetch_array($sql2);
        $categorie = printSecuTags($categorie);

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
           . "<div class=\"content-box-header\"><h3>" . _ADMINSERVER . "</h3>\n"
           . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Server.php\" rel=\"modal\">\n"
           . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
           . "</div></div>\n"
           . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Server&amp;page=admin&amp;op=modif_serveur\">\n"
           . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
           . "<tr><td><b>" . _SERVIP . "</b> <input type=\"text\" name=\"ip_serv\" size=\"30\" value=\"" . $ip_serv . "\" />&nbsp;<b>" . _SERVPORT . "</b> <input type=\"text\" size=\"10\" maxlength=\"20\" name=\"port\" value=\"" . $port . "\" /></td></tr>\n"
           . "<tr><td><b>" . _SERVERGAME . "</b> <select name=\"game\"><option>" . $game . "</option>\n"
           . "<option value=\"CSS\">CSS</option>\n"
           . "<option value=\"HL2\">HL2</option>\n"
           . "<option value=\"HL\">HL</option>\n"
           . "<option value=\"DOOM3\">DOOM3</option>\n"
           . "<option value=\"FARCRY\">FARCRY</option>\n"
           . "<option value=\"Q3\">Q3</option>\n"
           . "<option value=\"MOHAA\">MOHAA</option>\n"
           . "<option value=\"RTCW\">RTCW</option>\n"
           . "<option value=\"COD\">COD</option>\n"
           . "<option value=\"UT\">UT</option>\n"
           . "<option value=\"UT2003\">UT2003</option>\n"
           . "<option value=\"UT2004\">UT2004</option>\n"
           . "<option value=\"IGI2\">IGI2</option>\n"
           . "<option value=\"NWN\">NWN</option>\n"
           . "<option value=\"AA\">AA</option>\n"
           . "<option value=\"BTF1942\">BTF1942</option>\n"
           . "</select>&nbsp;<b>" . _SERVERPASS . " :</b> <input type=\"text\" name=\"pass\" size=\"10\" value=\"" . $pass . "\" /></td></tr>\n"
           . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"" . $cid . "\">" . $categorie . "</option>\n";

        select_serv_cat();

        echo "</select><input type=\"hidden\" name=\"sid\" value=\"" . $sid . "\" /></td></tr></table>\n"
           . "<div style=\"text-align: center;\"><br /><input type=\"submit\" name=\"send\" value=\"" . _MODIFTHISSERV . "\" /></div>\n"
           . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Server&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>";
    } 

    function modif_serveur($sid, $ip_serv, $port, $game, $pass, $cat) {
        global $nuked, $user;

        $sql = mysql_query("UPDATE " . SERVER_TABLE . " SET game = '" . $game . "', ip = '" . $ip_serv . "', port = '" . $port . "', pass = '" . $pass . "', cat = '" . $cat . "' WHERE sid = '" . $sid . "'");
        // Action
        $texteaction = _ACTIONMODIFSER . '.';
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
           . "<div>\n"
           . _SERVERMODIF . "\n"
           . "</div>\n"
           . "</div>\n";
        echo "<script>\n"
           . "setTimeout('screen()','3000');\n"
           . "function screen() { \n"
           . "screenon('index.php?file=Server', 'index.php?file=Server&page=admin&op=main_cat');\n"
           . "}\n"
           . "</script>\n";
    } 

    function main() {
        global $nuked, $language;


        echo "<script type=\"text/javascript\">\n"
           . "<!--\n"
           . "\n"
           . "function del_server(ip, id)"
           . "{\n"
           . "if (confirm('" . _DELETESERV . " '+ip+' ! " . _CONFIRM . "'))\n"
           . "{document.location.href = 'index.php?file=Server&page=admin&op=del_serveur&sid='+id;}\n"
           . "}\n"
           . "\n"
           . "// -->\n"
           . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
           . "<div class=\"content-box-header\"><h3>" . _ADMINSERVER . "</h3>\n"
           . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Server.php\" rel=\"modal\">\n"
           . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
           . "</div></div>\n"
           . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _SERVER . "<b> | "
           . "<a href=\"index.php?file=Server&amp;page=admin&amp;op=add_serveur\">" . _ADDSERVER . "</a> | "
           . "<a href=\"index.php?file=Server&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
           . "<a href=\"index.php?file=Server&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
           . "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
           . "<tr>\n"
           . "<td style=\"width: 30%;\" align=\"center\"><b>" . _SERVIP . "</b></td>\n"
           . "<td style=\"width: 20%;\" align=\"center\"><b>" . _SERVERGAME . "</b></td>\n"
           . "<td style=\"width: 20%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
           . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
           . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT sid, ip, port, game, cat FROM " . SERVER_TABLE . " ORDER BY cat, ip");
        $count = mysql_num_rows($sql);
        while (list($sid, $ip_serv, $port, $game, $cat) = mysql_fetch_row($sql)) {
            $sql2 = mysql_query("SELECT titre FROM " . SERVER_CAT_TABLE . " WHERE cid = '" . $cat . "'");
            list($categorie) = mysql_fetch_array($sql2);
            $categorie = printSecuTags($categorie);


            echo "<tr>\n"
               . "<td style=\"width: 30%;\" align=\"center\">" . $ip_serv . ":" . $port . "</td>\n"
               . "<td style=\"width: 20%;\" align=\"center\">" . $game . "</td>\n"
               . "<td style=\"width: 20%;\" align=\"center\">" . $categorie . "</td>\n"
               . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Server&amp;page=admin&amp;op=edit_serveur&amp;sid=" . $sid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISSERV . "\" /></a></td>\n"
               . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_server('" . $ip_serv . "', '" . $sid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISSERV . "\" /></a></td></tr>\n";
        } 

        if ($count == 0) {
            echo "<tr><td colspan=\"5\" align=\"center\">" . _NOSERV . "</td></tr>\n";
        } 

        echo "</table><br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function main_pref() {
        global $nuked, $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
           . "<div class=\"content-box-header\"><h3>" . _ADMINSERVER . "</h3>\n"
           . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Server.php\" rel=\"modal\">\n"
           . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
           . "</div></div>\n"
           . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Server&amp;page=admin\">" . _SERVER . "</a> | "
           . "<a href=\"index.php?file=Server&amp;page=admin&amp;op=add_serveur\">" . _ADDSERVER . "</a> | "
           . "<a href=\"index.php?file=Server&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
           . "</b>" . _PREFS . "</div><br />\n"
           . "<form method=\"post\" action=\"index.php?file=Server&amp;page=admin&amp;op=change_pref\">\n"
           . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
           . "<tr><td align=\"center\"><big>" . _SERVERMONITOR . "</big></td></tr>\n"
           . "<tr><td><b>" . _SERVIP . "</b> <input type=\"text\" name=\"server_ip\" size=\"30\" value=\"" . $nuked['server_ip'] . "\" />&nbsp;<b>" . _SERVPORT . "</b> <input type=\"text\" size=\"10\" maxlength=\"20\" name=\"server_port\" value=\"" . $nuked['server_port'] . "\" /></td></tr>\n"
           . "<tr><td><b>" . _SERVERGAME . "</b> <select name=\"server_game\"><option>" . $nuked['server_game'] . "</option>\n"
           . "<option value=\"CSS\">CSS</option>\n"
           . "<option value=\"HL2\">HL2</option>\n"
           . "<option value=\"HL\">HL</option>\n"
           . "<option value=\"DOOM3\">DOOM3</option>\n"
           . "<option value=\"FARCRY\">FARCRY</option>\n"
           . "<option value=\"Q3\">Q3</option>\n"
           . "<option value=\"MOHAA\">MOHAA</option>\n"
           . "<option value=\"RTCW\">RTCW</option>\n"
           . "<option value=\"COD\">COD</option>\n"
           . "<option value=\"UT\">UT</option>\n"
           . "<option value=\"UT2003\">UT2003</option>\n"
           . "<option value=\"UT2004\">UT2004</option>\n"
           . "<option value=\"IGI2\">IGI2</option>\n"
           . "<option value=\"NWN\">NWN</option>\n"
           . "<option value=\"AA\">AA</option>\n"
           . "<option value=\"BTF1942\">BTF1942</option>\n"
           . "</select>&nbsp;<b>" . _SERVERPASS . " :</b> <input type=\"text\" name=\"server_pass\" size=\"10\" value=\"" . $nuked['server_pass'] . "\" /></td></tr>\n"
           . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
           . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Server&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function change_pref($server_ip, $server_port, $server_game, $server_pass) {
        global $nuked, $user;

        $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $server_ip . "' WHERE name = 'server_ip'");
        $upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $server_port . "' WHERE name = 'server_port'");
        $upd3 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $server_game . "' WHERE name = 'server_game'");
        $upd4 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $server_pass . "' WHERE name = 'server_pass'");
        // Action
        $texteaction =  _ACTIONCONFIGSER . '.';
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
           . "<div>\n"
           . _PREFUPDATED . "\n"
           . "</div>\n"
           . "</div>\n";
        redirect('index.php?file=Server&page=admin', 2);
    } 

    switch ($_REQUEST['op'])
    {
        case 'edit_serveur':
            edit_serveur($_REQUEST['sid']);
            break;

        case 'modif_serveur':
            modif_serveur($_REQUEST['sid'], $_REQUEST['ip_serv'], $_REQUEST['port'], $_REQUEST['game'], $_REQUEST['pass'], $_REQUEST['cat']);
            break;

        case 'add_serveur':
            add_serveur();
            break;

        case 'send_cat':
            send_cat($_REQUEST['titre'], $_REQUEST['description']);
            break;

        case 'add_cat':
            add_cat();
            break;

        case 'main_cat':
            main_cat();
            break;

        case 'edit_cat':
            edit_cat($_REQUEST['cid']);
            break;

        case 'modif_cat':
            modif_cat($_REQUEST['cid'], $_REQUEST['titre'], $_REQUEST['description']);
            break;

        case 'del_cat':
            del_cat($_REQUEST['cid']);
            break;

        case 'del_serveur':
            del_serveur($_REQUEST['sid']);
            break;

        case 'send_serveur':
            send_serveur($_REQUEST['ip_serv'], $_REQUEST['port'], $_REQUEST['game'], $_REQUEST['pass'], $_REQUEST['cat']);
            break;

        case 'main_pref':
            main_pref();
            break;

        case 'change_pref':
            change_pref($_REQUEST['server_ip'], $_REQUEST['server_port'], $_REQUEST['server_game'], $_REQUEST['server_pass']);
            break;

        default:
            main();
            break;
    } 

} else if ($level_admin == -1) {
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
