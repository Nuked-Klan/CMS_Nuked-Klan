<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")) die ('<div style="text-align: center;">You cannot open this page directly</div>');
global $user, $language;
translate("modules/Irc/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
admintop();

$visiteur = !$user ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1){
    function main(){
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
				."<!--\n"
				."\n"
				. "function del_irc(titre, id)\n"
				. "{\n"
				. "if (confirm('" . _AWARDSDELETE . " '+titre+' ! " . _CONFIRM . "'))\n"
				. "{document.location.href = 'index.php?file=Irc&page=admin&op=del&irc_id='+id;}\n"
				. "}\n"
				. "\n"
				. "// -->\n"
				. "</script>\n";

      echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
				. "<div class=\"content-box-header\"><h3>" . _ADMINIRC . "</h3>\n"
				. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Irc.php\" rel=\"modal\">\n"
				. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
				. "</div></div>\n"
				. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _IRC . "<b> | "
				. "<a href=\"index.php?file=Irc&amp;page=admin&amp;op=add\">" . _ADDAWARD . "</a> | "
				. "<a href=\"index.php?file=Irc&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
				. "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
				. "<tr>\n"
				. "<td style=\"width: 30%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
				. "<td style=\"width: 40%;\" align=\"center\"><b>" . _TEXT . "</b></td>\n"
				. "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
				. "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, date, text FROM " . IRC_AWARDS_TABLE . " ORDER BY id DESC");
        $count = mysql_num_rows($sql);
        while (list($irc_id, $date, $text) = mysql_fetch_array($sql)){
            $date = nkDate($date);

            if (strlen($text) > 50){
                $texte = substr($text, 0, 50) . "...";
				$texte = htmlentities($texte);
            } 
            else{
                $texte = $text;
            }

            echo "<tr>\n"
					. "<td style=\"width: 30%;\" align=\"center\">" . $date . "</td>\n"
					. "<td style=\"width: 40%;\">" . $texte . "</td>\n"
					. "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Irc&amp;page=admin&amp;op=edit&amp;irc_id=" . $irc_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISAWARD . "\" /></a></td>\n"
					. "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_irc('" . $irc_id . "','" . $irc_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISAWARD . "\" /></a></td></tr>\n";

        } 

        if ($count == 0){
            echo "<tr><td colspan=\"4\" align=\"center\">" . _NOAWARD . "</td></tr>\n";
        }
		
        echo "</table><br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function add(){
        global $language;

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
				. "<div class=\"content-box-header\"><h3>" . _ADMINIRC . "</h3>\n"
				. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Irc.php\" rel=\"modal\">\n"
				. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
				. "</div></div>\n"
				. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Irc&amp;page=admin\">" . _IRC . "</a> | "
				. "</b>" . _ADDAWARD . "<b> | "
				. "<a href=\"index.php?file=Irc&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
				. "<form method=\"post\" action=\"index.php?file=Irc&amp;page=admin&amp;op=do_add\">\n"
				. "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
				. "<tr><td align=\"center\"><b>" . _TEXT . "</b></td></tr>\n"
				. "<tr><td align=\"center\"><textarea class=\"editor\" name=\"text\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
				. "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _ADDTHISAWARD . "\" /></div>\n"
				. "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Irc&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function do_add($text){
        global $nuked, $user;

        $date = time();
		$text = html_entity_decode($text);
        $text = mysql_real_escape_string(stripslashes($text));

        $sql = mysql_query("INSERT INTO " . IRC_AWARDS_TABLE . " ( `id` , `text` , `date` ) VALUES ( '' , '" . $text . "' , '" . $date . "' )");
		
		// Action
		$texteaction = "". _ACTIONADDIRC .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
				. "<div>\n"
				. "" . _AWARDADD . "\n"
				. "</div>\n"
				. "</div>\n";
				
		 echo "<script>\n"
				."setTimeout('screen()','3000');\n"
				."function screen() { \n"
				."screenon('index.php?file=Irc&op=awards', 'index.php?file=Irc&page=admin');\n"
				."}\n"
				."</script>\n";
    } 

    function edit($irc_id){
        global $nuked, $language;

        $sql = mysql_query("SELECT text FROM " . IRC_AWARDS_TABLE . " WHERE id = '" . $irc_id . "'");
        list($text) = mysql_fetch_array($sql);

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
				. "<div class=\"content-box-header\"><h3>" . _ADMINIRC . "</h3>\n"
				. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Irc.php\" rel=\"modal\">\n"
				. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
				. "</div></div>\n"
				. "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Irc&amp;page=admin&amp;op=do_edit\">\n"
				. "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
				. "<tr><td align=\"center\"><b>" . _TEXT . "</b></td></tr>\n"
				. "<tr><td align=\"center\"><textarea  class=\"editor\" name=\"text\" cols=\"60\" rows=\"10\">" . $text . "</textarea></td></tr></table>\n"
				. "<div style=\"text-align: center;\"><br /><input type=\"hidden\" name=\"irc_id\" value=\"" . $irc_id . "\" /><input type=\"submit\" value=\"" . _MODIFTHISAWARD . "\" /></div>\n"
				. "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Irc&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function do_edit($irc_id, $text){
        global $nuked, $user;

		$text = secu_html(html_entity_decode($text));
        $text = mysql_real_escape_string(stripslashes($text));

        $upd = mysql_query("UPDATE " . IRC_AWARDS_TABLE . " SET text = '" . $text . "' WHERE id = '" . $irc_id . "'");
		// Action
		$texteaction = "". _ACTIONMODIFIRC .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
				. "<div>\n"
				. "" . _AWARDMODIF . "\n"
				. "</div>\n"
				. "</div>\n";
				
        echo "<script>\n"
				."setTimeout('screen()','3000');\n"
				."function screen() { \n"
				."screenon('index.php?file=Irc&op=awards', 'index.php?file=Irc&page=admin');\n"
				."}\n"
				."</script>\n";
    } 

    function del($irc_id){
        global $nuked, $user;

        $del = mysql_query("DELETE FROM " . IRC_AWARDS_TABLE . " WHERE id = '" . $irc_id . "'");
		// Action
		$texteaction = "". _ACTIONDELIRC .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
				. "<div>\n"
				. "" . _AWARDDELETE . "\n"
				. "</div>\n"
				. "</div>\n";
        echo "<script>\n"
				."setTimeout('screen()','3000');\n"
				."function screen() { \n"
				."screenon('index.php?file=Irc&op=awards', 'index.php?file=Irc&page=admin');\n"
				."}\n"
				."</script>\n";
    } 

    function main_pref(){
        global $nuked, $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
				. "<div class=\"content-box-header\"><h3>" . _ADMINIRC . "</h3>\n"
				. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Irc.php\" rel=\"modal\">\n"
				. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
				. "</div></div>\n"
				. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Irc&amp;page=admin\">" . _IRC . "</a> | "
				. "<a href=\"index.php?file=Irc&amp;page=admin&amp;op=add\">" . _ADDAWARD . "</a> | "
				. "</b>" . _PREFS . "</div><br />\n"
				. "<form method=\"post\" action=\"index.php?file=Irc&amp;page=admin&amp;op=change_pref\">\n"
				. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n"
				. "<tr><td align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
				. "<tr><td><b>" . _IRCHAN . " : #</b><input type=\"text\" name=\"irc_chan\" size=\"15\" value=\"" . $nuked['irc_chan'] . "\" /> <b>" . _IRCSERV . " :</b> <input type=\"text\" name=\"irc_serv\" size=\"20\" value=\"" . $nuked['irc_serv'] . "\" /></td></tr>\n"
				. "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _SEND . "\" /></td></tr></table>\n"
				. "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Irc&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function change_pref($irc_chan, $irc_serv){
        global $nuked, $user;

        $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $irc_chan . "' WHERE name = 'irc_chan'");
        $upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $irc_serv . "' WHERE name = 'irc_serv'");
		// Action
		$texteaction = "". _ACTIONPREFIRC .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
				. "<div>\n"
				. "" . _PREFUPDATED . "\n"
				. "</div>\n"
				. "</div>\n";
				
        redirect("index.php?file=Irc&page=admin", 2);
    } 

    switch ($_REQUEST['op']){
        case "add":
            add();
            break;

        case "del":
            del($_REQUEST['irc_id']);
            break;

        case "do_edit":
            do_edit($_REQUEST['irc_id'], $_REQUEST['text']);
            break;

        case "edit":
            edit($_REQUEST['irc_id']);
            break;

        case "do_add":
            do_add($_REQUEST['text']);
            break;

        case "main_pref":
            main_pref();
            break;

        case "change_pref":
            change_pref($_REQUEST['irc_chan'], $_REQUEST['irc_serv']);
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