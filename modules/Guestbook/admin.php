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
translate("modules/Guestbook/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
admintop();

$visiteur = (!$user) ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1)
{
    function edit_book($gid)
    {
        global $nuked, $language;

        $sql = mysql_query("SELECT name, comment, email, url FROM " . GUESTBOOK_TABLE . " WHERE id = '" . $gid . "'");
        list($name, $comment, $email, $url) = mysql_fetch_array($sql);

        $url = htmlentities($url);
        
        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINGUESTBOOK . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Guestbook.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Guestbook&amp;page=admin&amp;op=modif_book\" onsubmit=\"backslash('guest_text');\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
        . "<tr><td><b>" . _AUTHOR . " :</b></td><td>" . $name . "</td></tr>\n"
        . "<tr><td><b>" . _MAIL . " : </b></td><td><input type=\"text\" name=\"email\" size=\"40\" value=\"" . $email . "\" /></td></tr>\n"
        . "<tr><td><b>" . _URL . " : </b></td><td><input type=\"text\" name=\"url\" size=\"40\" value=\"" . $url . "\" /></td></tr>\n";
    
        echo "<tr><td colspan=\"2\"><b>" . _COMMENT . " :</b></td></tr>\n"
        . "<tr><td colspan=\"2\"><textarea class=\"editor\" id=\"guest_text\" name=\"comment\" cols=\"65\" rows=\"12\">" . $comment . "</textarea></td></tr>\n"
        . "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"send\" value=\"" . _MODIF . "\" /><input type=\"hidden\" name=\"gid\" value=\"" . $gid . "\" /></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Guestbook&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div>\n";
    } 

    function modif_book($gid, $comment, $email, $url)
    {
        global $nuked, $user;

        $comment = html_entity_decode($comment);
        $comment = mysql_real_escape_string(stripslashes($comment));

        if (!empty($url) && !is_int(stripos($url, 'http://')))
        {
            $url = "http://" . $url;
        } 
        
        $sql = mysql_query("UPDATE " . GUESTBOOK_TABLE . " SET email = '" . $email . "', url = '" . $url . "', comment = '" . $comment . "' WHERE id = '" . $gid . "'");
        // Action
        $texteaction = "". _ACTIONMODIFBOOK .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _POSTEDIT . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script type=\"text/javascript\">\n"
        ."//<![CDATA[\n"
        ."setTimeout('screen()','3000');\n"
        ."function screen() { \n"
        ."screenon('index.php?file=Guestbook', 'index.php?file=Guestbook&page=admin');\n"
        ."}\n"
        ."//]]>\n"
        ."</script>\n";
    } 

    function del_book($gid)
    {
        global $nuked, $user;

        $sql = mysql_query("DELETE FROM " . GUESTBOOK_TABLE . " WHERE id = '" . $gid . "'");
        // Action
        $texteaction = "". _ACTIONDELBOOK .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _POSTDELETE . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script type=\"text/javascript\">\n"
        ."//<![CDATA[\n"
        ."setTimeout('screen()','3000');\n"
        ."function screen() { \n"
        ."screenon('index.php?file=Guestbook', 'index.php?file=Guestbook&page=admin');\n"
        ."}\n"
        ."//]]>\n"
        ."</script>\n";
    } 

    function main()
    {
        global $nuked, $language;

        $nb_mess_guest = "30";

        $sql2 = mysql_query("SELECT id FROM " . GUESTBOOK_TABLE);
        $count = mysql_num_rows($sql2);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_mess_guest - $nb_mess_guest;

        echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function delmess(pseudo, id)\n"
        . "{\n"
        . "if (confirm('" . _SIGNDELETE . " '+pseudo+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=Guestbook&page=admin&op=del_book&gid='+id;}\n"
        . "}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINGUESTBOOK . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Guestbook.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _GUESTBOOK . "<b> | "
        . "<a href=\"index.php?file=Guestbook&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n";

        if ($count > $nb_mess_guest)
        {
            number($count, $nb_mess_guest, "index.php?file=Guestbook&amp;page=admin");
        } 


        echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _AUTHOR . "</b></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _IP . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, date, name, host FROM " . GUESTBOOK_TABLE . " ORDER BY id DESC LIMIT " . $start . ", " . $nb_mess_guest."");
        while (list($id, $date, $name, $ip) = mysql_fetch_array($sql))
        {
            $date = nkDate($date);
            $name = nk_CSS($name);
            
            echo "<tr>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $name . "</td>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $ip . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Guestbook&amp;page=admin&amp;op=edit_book&amp;gid=" . $id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISPOST . "\" /></a></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:delmess('" . mysql_real_escape_string(stripslashes($name)) . "', '" . $id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISPOST . "\" /></a></td></tr>\n";
        } 

        if ($count == "0")
        {
            echo "<tr><td align=\"center\" colspan=\"5\">" . _NOSIGN . "</td></tr>\n";
        } 

        echo "</table>";

        if ($count > $nb_mess_guest)
        {
            number($count, $nb_mess_guest, "index.php?file=Guestbook&amp;page=admin");
        } 

        echo "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    } 

    function main_pref()
    {
        global $nuked, $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINGUESTBOOK . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Guestbook.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Guestbook&amp;page=admin\">" . _GUESTBOOK . "</a> |</b> " . _PREFS . "</div><br />\n"
        . "<form method=\"post\" action=\"index.php?file=Guestbook&amp;page=admin&amp;op=change_pref\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
        . "<tr><td>" . _GUESTBOOKPG . " :</td><td><input type=\"text\" name=\"mess_guest_page\" size=\"2\" value=\"" . $nuked['mess_guest_page'] . "\" /></td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" name=\"Submit\" value=\"" . _SEND . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Guestbook&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    } 

    function change_pref($mess_guest_page)
    {
        global $nuked, $user;

        $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $mess_guest_page . "' WHERE name = 'mess_guest_page'");
        // Action
        $texteaction = "". _ACTIONPREFBOOK .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _PREFUPDATED . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Guestbook&page=admin", 2);
    } 

    switch ($_REQUEST['op'])
    {
        case "edit_book":
            edit_book($_REQUEST['gid']);
            break;

        case "modif_book":
            modif_book($_REQUEST['gid'], $_REQUEST['comment'], $_REQUEST['email'], $_REQUEST['url']);
            break;

        case "del_book":
            del_book($_REQUEST['gid']);
            break;

        case "main_pref":
            main_pref();
            break;

        case "change_pref":
            change_pref($_REQUEST['mess_guest_page']);
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
