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
translate("modules/Recruit/lang/" . $language . ".lang.php");
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
    function main()
    {
        global $nuked, $language;

	echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _RECRUIT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Recruit.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _NAVRECRUIT . "<b> | "
	. "<a href=\"index.php?file=Recruit&amp;page=admin&amp;op=edit_pref\">" . _PREFS . "</a></b></div><br />"
	. "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
	. "<tr>\n"
	. "<td style=\"width: 25%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _FIRSTNAME . "</b></td>\n"
	. "<td style=\"width: 15%;\" align=\"center\"><b>" . _GAME . "</b></td>\n"
	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _MAIL . "</b></td>\n"
	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, pseudo, prenom, mail, game, date FROM " . RECRUIT_TABLE . " ORDER BY id DESC");
        $count = mysql_num_rows($sql);
        while (list($rid, $pseudo, $prenom, $mail, $game, $date) = mysql_fetch_array($sql))
        {
            $date = strftime("%x", $date);

            $sql2 = mysql_query("SELECT name FROM " . GAMES_TABLE . " WHERE id='$game'");
            list($game_name) = mysql_fetch_array($sql2);
            $game_name = htmlentities($game_name);


            echo "<tr>\n"
            . "<td style=\"width: 25%;\" align=\"center\"><a href=\"index.php?file=Recruit&amp;page=admin&amp;op=view&amp;rid=" . $rid . "\">" . $pseudo . "</a></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $prenom . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $game_name . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><a href=\"mailto:" . $mail . "\">" . $mail . "</a></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td></tr>\n";
        } 
        if ($count == 0)
        {
            echo "<tr><td colspan=\"5\" align=\"center\">" . _NORECRUIT . "</td></tr>\n";
        } 
        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function edit_pref()
    {
        global $nuked, $language;

        $charte = $nuked['recrute_charte'];

        if ($nuked['recrute'] == 1)
        {
            $etat = _OPEN;
        } 
        else
        {
            $etat = _CLOSE;
        } 
        
       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _RECRUIT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Recruit.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Recruit&amp;page=admin\">" . _NAVRECRUIT . "</a> | "
	. "</b>" . _PREFS . "</div><br />\n"
	. "<form method=\"post\" action=\"index.php?file=Recruit&amp;page=admin&amp;op=update_pref\" onsubmit=\"backslash('charte_recruit');\">\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
	. "<tr><td><b>" . _RECRUTE . "</b> : <select name=\"recrute\">\n"
	. "<option value=\"" . $nuked['recrute'] . "\">" . $etat . "</option>\n"
	. "<option value=\"1\">" . _OPEN . "</option>\n"
	. "<option value=\"0\">" . _CLOSE . "</option></select></td></tr>\n"
	. "<tr><td><b>" . _MAILAVERT . "</b> : <input type=\"text\" size=\"30\" name=\"recrute_mail\" value=\"" . $nuked['recrute_mail'] . "\" /></td></tr>\n"
	. "<tr><td><b>" . _INBOXAVERT . "</b> : <select name=\"recrute_inbox\"><option value=\"\">" . _OFF . "</option>\n";

        $sql2 = mysql_query("SELECT id, pseudo FROM " . USER_TABLE . " WHERE niveau > 1 ORDER BY niveau DESC");
        while (list($id_user, $pseudo) = mysql_fetch_array($sql2))
        {
            if ($nuked['recrute_inbox'] == $id_user)
            {
                $checked = "selected=\"selected\"";
            } 
            else
            {
                $checked = "";
            } 

            echo "<option value=\"" . $id_user . "\" " . $checked . ">" . $pseudo . "</option>\n";
        } 
        echo "</select></td></tr><tr><td>&nbsp;</td></tr>\n";

        

        echo "<tr><td><b>" . _CHARTE . "</b> : <br /><textarea class=\"editor\" id=\"charte_recruit\" name=\"recrute_charte\" cols=\"65\" rows=\"15\">" . $charte . "</textarea></td></tr></table>\n"
	. "<div style=\"text-align: center;\"><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
	. "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Recruit&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function update_pref($recrute_mail, $recrute_inbox, $recrute_charte, $recrute)
    {
        global $nuked, $user;

		$recrute_charte = html_entity_decode($recrute_charte);
        $recrute_charte = mysql_real_escape_string(stripslashes($recrute_charte));

        $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $recrute . "' WHERE name = 'recrute'");
        $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $recrute_charte . "' WHERE name = 'recrute_charte'");
        $upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $recrute_mail . "' WHERE name = 'recrute_mail'");
        $upd3 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $recrute_inbox . "' WHERE name = 'recrute_inbox'");
		// Action
		$texteaction = "". _ACTIONPREFREC .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _PREFUPDATE . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Recruit&page=admin", 2);
    } 

    function view($rid)
    {
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
	. "<!--\n"
	. "\n"
	. "function del_recruit(pseudo, id)\n"
	. "{\n"
	. "if (confirm('" . _DELETERECRUTE . " '+pseudo+' ! " . _CONFIRM . "'))\n"
	. "{document.location.href = 'index.php?file=Recruit&page=admin&op=del&rid='+id;}\n"
	. "}\n"
    	. "\n"
	. "// -->\n"
	. "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _RECRUIT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Recruit.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><table width=\"90%\" style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\"><tr><td>\n";

        $sql = mysql_query("SELECT pseudo, prenom, age, mail, icq, country, game, `connection`, experience, dispo, comment FROM " . RECRUIT_TABLE . " WHERE id = '" . $rid . "'");
        list($pseudo, $prenom, $age, $mail, $icq, $country, $game, $connection, $experience, $dispo, $comment) = mysql_fetch_array($sql);
        list ($pays, $ext) = explode ('.', $country);

        $sql2 = mysql_query("SELECT name FROM " . GAMES_TABLE . " WHERE id = '" . $game . "'");
        list($game_name) = mysql_fetch_array($sql2);
        $game_name = htmlentities($game_name);

        echo "<b>" . _NICK . " : </b>" . $pseudo . "<br />\n"
	. "<b>" . _FIRSTNAME . " : </b>" . $prenom . "<br />\n"
	. "<b>" . _AGE . " : </b>" . $age . "<br />\n"
	. "<b>" . _MAIL . " : </b><a href=\"mailto:" . $mail . "\">" . $mail . "</a><br />\n"
	. "<b>" . _ICQMSN . " : </b>" . $icq . "<br />\n"
	. "<b>" . _COUNTRY . " : </b>" . $pays . "<br />\n"
	. "<b>" . _GAME . " : </b>" . $game_name . "<br />\n"
	. "<b>" . _CONNECT . " : </b>" . $connection . "<br />\n"
	. "<b>" . _EXPERIENCE . " : </b>" . $experience . "<br />\n"
	. "<b>" . _AVAILABLE . " : </b>" . $dispo . "<br /><br />\n"
	. "<b>" . _COMMENT . " : </b>" . $comment . "<br /><br />\n";

        echo "</td></tr></table><div style=\"text-align: center;\"><input type=\"submit\" value=\"" . _REMOVE . "\" onclick=\"javascript:del_recruit('" . mysql_real_escape_string(stripslashes($pseudo)) . "', '" . $rid . "');\" /></div>\n"
	. "<div style=\"text-align: center;\"><br /><br />[ <a href=\"index.php?file=Recruit&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function del($rid)
    {
        global $nuked, $user;

        $del = mysql_query("DELETE FROM " . RECRUIT_TABLE . " WHERE id = '" . $rid ."'");
		// Action
		$texteaction = "". _ACTIONDELREC .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _RECRUITDELETE . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Recruit&page=admin", 2);
    } 

    switch ($_REQUEST['op'])
    {
        case "view":
            view($_REQUEST['rid']);
            break;

        case "del":
            del($_REQUEST['rid']);
            break;

        case "edit_pref":
            edit_pref();
            break;

        case "update_pref":
            update_pref($_REQUEST['recrute_mail'], $_REQUEST['recrute_inbox'], $_REQUEST['recrute_charte'], $_REQUEST['recrute']);
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
