<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $user, $language;
translate("modules/Textbox/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
admintop();

if (!$user)
{
    $visiteur = 0;
} 
else
{
    $visiteur = $user[1];
} 
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1)
{
    function edit_shout($mid)
    {
        global $nuked, $language;

        $sql = mysql_query("SELECT auteur, texte, ip FROM " . TEXTBOX_TABLE . " WHERE id = '" . $mid . "'");
        list($pseudo, $texte, $ip) = mysql_fetch_array($sql);
        $texte = htmlspecialchars($texte);

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _ADMINSHOUTBOX . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Textbox.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Textbox&amp;page=admin&amp;op=modif_shout\">\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"60%\" border=\"0\">\n"
	. "<tr><td><b>" . _NICKNAME . " :</b> " . $pseudo . " ( " . $ip . " )</td></tr>\n"
	. "<tr><td>&nbsp;</td></tr><tr><td><b>" . _SHOUT . " :</b></td></tr>\n"
	. "<tr><td><textarea name=\"texte\" cols=\"65\" rows=\"10\">" . $texte . "</textarea></td></tr>\n"
	. "<tr><td align=\"center\"><input type=\"hidden\" name=\"mid\" value=\"" . $mid . "\" />&nbsp;</td></tr>\n"
	. "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _MODIF . "\" /></td></tr></table>\n"
	. "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Textbox&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function modif_shout($mid, $texte)
    {
        global $nuked, $user;

        $texte = mysql_real_escape_string(stripslashes($texte));

        $sql = mysql_query("UPDATE " . TEXTBOX_TABLE . " SET texte = '" . $texte . "' WHERE id = '" . $mid . "'");
		// Action
		$texteaction = "". _ACTIONMODIFSHO .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _MESSEDIT . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Textbox&page=admin", 2);
    } 

    function del_shout($mid)
    {
        global $nuked, $user;

        $sql = mysql_query("DELETE FROM " . TEXTBOX_TABLE . " WHERE id = '" . $mid . "'");
		// Action
		$texteaction = "". _ACTIONDELSHO .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _MESSDEL . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Textbox&page=admin", 2);
    } 

    function del_all_shout()
    {
        global $nuked, $user;

        $sql = mysql_query("DELETE FROM " . TEXTBOX_TABLE);
		// Action
		$texteaction = "". _ACTIONALLDELSHO .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _ALLMESSDEL . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Textbox&page=admin", 2);
    } 

    function main()
    {
        global $nuked, $language;

        $nb_mess_guest = 30;

        $sql2 = mysql_query("SELECT id FROM " . TEXTBOX_TABLE);
        $count = mysql_num_rows($sql2);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_mess_guest - $nb_mess_guest;

        echo "<script type=\"text/javascript\">\n"
	. "<!--\n"
	. "\n"
	. "function del_shout(pseudo, id)\n"
	. "{\n"
	. "if (confirm('" . _DELETETEXT . " '+pseudo+' ! " . _CONFIRM . "'))\n"
	. "{document.location.href = 'index.php?file=Textbox&page=admin&op=del_shout&mid='+id;}\n"
	. "}\n"
    	. "\n"
	. "function delall()\n"
	. "{\n"
	. "if (confirm('" . _DELETEALLTEXT . "'))\n"
	. "{document.location.href = 'index.php?file=Textbox&page=admin&op=del_all_shout';}\n"
	. "}\n"
    	. "\n"
	. "// -->\n"
	. "</script>\n";

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _ADMINSHOUTBOX . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Textbox.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _SHOUTBOX . "<b> | "
	. "<a href=\"index.php?file=Textbox&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a> | "
	. "<a href=\"javascript:delall();\">" . _DELALLMESS . "</a></b></div><br />\n";

        if ($count > $nb_mess_guest)
        {
            number($count, $nb_mess_guest, "index.php?file=Textbox&page=admin");
        } 

        echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
	. "<tr>\n"
	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
	. "<td style=\"width: 25%;\" align=\"center\"><b>" . _NICKNAME . "</b></td>\n"
	. "<td style=\"width: 25%;\" align=\"center\"><b>" . _IP . "</b></td>\n"
	. "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
	. "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, date, auteur, ip FROM " . TEXTBOX_TABLE . " ORDER BY id DESC LIMIT " . $start . ", " . $nb_mess_guest."");
        while (list($id, $date, $auteur, $ip) = mysql_fetch_array($sql))
        {
            $date = nkDate($date);


            echo "<tr>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $auteur . "</td>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $ip . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Textbox&amp;page=admin&amp;op=edit_shout&amp;mid=" . $id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISMESS . "\" /></a></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_shout('" . mysql_real_escape_string(stripslashes($auteur)) . "', '" . $id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISMESS . "\" /></a></td></tr>\n";
        } 

        if ($count == "0")
        {
            echo "<tr><td align=\"center\" colspan=\"5\">" . _NOMESS . "</td></tr>\n";
        } 

        echo "</table>";

        if ($count > $nb_mess_guest)
        {
            number($count, $nb_mess_guest, "index.php?file=Textbox&amp;page=admin");
        } 

        echo "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function main_pref()
    {
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
	. "<!--\n"
	. "\n"
	. "function delall()\n"
	. "{\n"
	. "if (confirm('" . _DELETEALLTEXT . "'))\n"
	. "{document.location.href = 'index.php?file=Textbox&page=admin&op=del_all_shout';}\n"
	. "}\n"
    	. "\n"
	. "// -->\n"
	. "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _ADMINSHOUTBOX . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Textbox.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Textbox&amp;page=admin\">" . _SHOUTBOX . "</a> | "
	. "</b>" . _PREFS . "<b> | "
	. "<a href=\"javascript:delall();\">" . _DELALLMESS . "</a></b></div><br />\n"
	. "<form method=\"post\" action=\"index.php?file=Textbox&amp;page=admin&amp;op=change_pref\">\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
	. "<tr><td colspan=\"2\" align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
	. "<tr><td>" . _NUMBERSHOUT . " :</td><td> <input type=\"text\" name=\"max_shout\" size=\"2\" value=\"" . $nuked['max_shout'] . "\" /></td></tr>\n"
	. "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"Submit\" value=\"" . _SEND . "\" /></td></tr></table>\n"
	. "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Textbox&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function change_pref($max_shout)
    {
        global $nuked, $user;

        $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_shout . "' WHERE name = 'max_shout'");
		// Action
		$texteaction = "". _ACTIONCONFSHO .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _PREFUPDATED . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Textbox&page=admin", 2);
    } 

    switch ($_REQUEST['op'])
    {
        case "edit_shout":
            edit_shout($_REQUEST['mid']);
            break;

        case "modif_shout":
            modif_shout($_REQUEST['mid'], $_REQUEST['texte']);
            break;

        case "del_shout":
            del_shout($_REQUEST['mid']);
            break;

        case "del_all_shout":
            del_all_shout();
            break;

        case "main_pref":
            main_pref();
            break;

        case "change_pref":
            change_pref($_REQUEST['max_shout']);
            break;

        default:
            main();
            break;
    } 
} 
else if ($level_admin == -1)
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}
else if ($visiteur > 1)
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}
else
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}

adminfoot();

?>
