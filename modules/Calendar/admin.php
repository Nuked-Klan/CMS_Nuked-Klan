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
translate("modules/Calendar/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
admintop();
 
$visiteur = (!$user) ? 0 : $user[1];

$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1)
{
    function main()
    {
        global $nuked, $language;

        echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function del_event(titre, id)\n"
        . "{\n"
        . "if (confirm('" . _EVENTDELETE . " '+titre+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=Calendar&page=admin&op=del&eid='+id;}\n"
        . "}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";
        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINCAL . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Calendar.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _NAVCALENDAR . "<b> | "
        . "<a href=\"index.php?file=Calendar&amp;page=admin&amp;op=add\">" . _ADDEVENT . "</a> | "
        . "<a href=\"index.php?file=Calendar&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
        . "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _AUTEUR . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, titre, auteur, date_jour, date_mois, date_an, heure FROM " . CALENDAR_TABLE . " ORDER BY date_an DESC, date_mois DESC, date_jour DESC");
        $count = mysql_num_rows($sql);
        while (list($eid, $titre, $auteur, $jour, $mois, $an, $heure) = mysql_fetch_array($sql))
        {
            $titre = printSecuTags($titre);

            if ($language == "french")
            {
                $date = $jour . "/" . $mois . "/" .$an;
            }
            else
            {
                $date = $mois . "/" . $jour . "/" . $an;
            }

            echo "<tr>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $date . "&nbsp;" . _AT . "&nbsp;" . $heure . "</td>\n"
            . "<td style=\"width: 25%;\" align=\"center\">" . $titre . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $auteur . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Calendar&amp;page=admin&amp;op=edit&amp;eid=" . $eid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISEVENT . "\" /></a></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_event('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $eid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISEVENT . "\" /></a></td></tr>\n";
        }
        if ($count == 0)
        {
            echo "<tr><td colspan=\"5\" align=\"center\">" . _NOEVENT . "</td></tr>\n";
        }

        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }

    function add()
    {
        global $language;

        $jour = strftime("%d", time());
        $mois = strftime("%m", time());
        $an = strftime("%Y", time());
        $heure = strftime("%H:%M", time());

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINCAL . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Calendar.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Calendar&amp;page=admin\">" . _NAVCALENDAR . "</a> | "
        . "</b>" . _ADDEVENT . "<b> | "
        . "<a href=\"index.php?file=Calendar&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
        . "<form method=\"post\" action=\"index.php?file=Calendar&amp;page=admin&amp;op=do_add\"\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" maxlength=\"100\" size=\"45\" /></td></tr>\n"
        . "<tr><td><b>" . _DESCR . " :</b><br />"
        . "<textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr>\n"
        . "<tr><td><b>" . _JOUR . " :</b> <input type=\"text\" name=\"date_jour\" maxlength=\"2\" size=\"2\" value=\"" . $jour . "\" />&nbsp;"
        . "<b>" . _MOIS . " :</b> <input type=\"text\" name=\"date_mois\" maxlength=\"2\" size=\"2\" value=\"" . $mois . "\" />&nbsp;"
        . "<b>" . _ANNEE . " :</b> <input type=\"text\" name=\"date_an\" maxlength=\"4\" size=\"4\" value=\"" . $an . "\" />&nbsp;"
        . "<b>" . _HEURE . " :</b> <input type=\"text\" name=\"heure\" maxlength=\"5\" size=\"5\" value=\"" . $heure . "\" />\n"
        . "</td></tr></table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _ADDTHISEVENT . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Calendar&amp;page=admin&amp;op=main\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function do_add($description, $titre, $heure, $date_an, $date_mois, $date_jour)
    {
        global $nuked, $user;

        $description = html_entity_decode($description);
        $description = mysql_real_escape_string(stripslashes($description));
        $titre = mysql_real_escape_string(stripslashes($titre));
        
        $sql = mysql_query("INSERT INTO " . CALENDAR_TABLE . " ( `id` , `titre` , `description` , `date_jour` , `date_mois` , `date_an` , `heure` , `auteur` ) VALUES ( '' , '" . $titre . "' , '" . $description . "' , '" . $date_jour . "' , '" . $date_mois . "' , '" . $date_an . "' , '" . $heure . "' , '" . $user[2] . "' )");
       // Action
        $texteaction = "". _ACTIONADDCAL .": ".$titre."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _EVENTADD . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script type=\"text/javascript\">\n"
        ."//<![CDATA[\n"
        ."setTimeout('screen()','3000');\n"
        ."function screen() { \n"
        ."screenon('index.php?file=Calendar&m=".$date_mois."&y=".$date_an."', 'index.php?file=Calendar&page=admin');\n"
        ."}\n"
        ."//]]>\n"
        ."</script>\n";
    }

    function edit($eid)
    {
        global $nuked, $language;

        $sql = mysql_query("SELECT id, titre, description, date_jour, date_mois, date_an, heure FROM " . CALENDAR_TABLE . " WHERE id = '" . $eid . "'");
        list($eid, $titre, $description, $jour, $mois, $an, $heure) = mysql_fetch_array($sql);
        $titre = htmlspecialchars($titre);

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINCAL . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Calendar.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Calendar&amp;page=admin&amp;op=do_edit\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
        . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" maxlength=\"100\" size=\"45\" value=\"" . $titre . "\" /></td></tr>\n"
        . "<tr><td><b>" . _DESCR . " :</b><br />\n"
        . "<textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"\">" . $description . "</textarea></td></tr>\n"
        . "<tr><td><b>" . _JOUR . " :</b> <input type=\"text\" name=\"date_jour\" maxlength=\"2\" size=\"2\" value=\"" . $jour . "\" />&nbsp;"
        . "<b>" . _MOIS . " :</b> <input type=\"text\" name=\"date_mois\" maxlength=\"2\" size=\"2\" value=\"" . $mois . "\" />&nbsp;"
        . "<b>" . _ANNEE . " :</b> <input type=\"text\" name=\"date_an\" maxlength=\"4\" size=\"4\" value=\"" . $an . "\" />&nbsp;"
        . "<b>" . _HEURE . " :</b> <input type=\"text\" name=\"heure\" maxlength=\"5\" size=\"5\" value=\"" . $heure . "\" />\n"
        . "</td></tr></table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _MODIFTHISEVENT . "\" /><input type=\"hidden\" name=\"eid\" value=\"" . $eid . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Calendar&amp;page=admin&amp;op=main\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function do_edit($eid, $description, $titre, $heure, $date_an, $date_mois, $date_jour)
    {
        global $nuked, $user;

        $description = html_entity_decode($description);
        $titre = mysql_real_escape_string(stripslashes($titre));
        $description = mysql_real_escape_string(stripslashes($description));
        
        $upd = mysql_query("UPDATE " . CALENDAR_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "', date_jour = '" . $date_jour . "', date_mois = '" . $date_mois . "', date_an = '" . $date_an . "', heure = '" . $heure . "' WHERE id = '" . $eid . "'");
        // Action
        $texteaction = "". _ACTIONMODIFCAL .": ".$titre."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _EVENTMODIF . "\n"
        . "</div>\n"
        . "</div>\n";
        echo "<script type=\"text/javascript\">\n"
        ."//<![CDATA[\n"
        ."setTimeout('screen()','3000');\n"
        ."function screen() { \n"
        ."screenon('index.php?file=Calendar&m=".$date_mois."&y=".$date_an."', 'index.php?file=Calendar&page=admin');\n"
        ."}\n"
        ."//]]>\n"
        ."</script>\n";
    }

    function del($eid)
    {
        global $nuked, $user;

        $sql = mysql_query("SELECT titre FROM " . CALENDAR_TABLE . " WHERE id = '" . $eid . "'");
        list($titre) = mysql_fetch_array($sql);
        $del = mysql_query("DELETE FROM " . CALENDAR_TABLE . " WHERE id = '" . $eid . "'");
        // Action
        $texteaction = "". _ACTIONDELCAL .": ".$titre."";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _EVENTDEL . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Calendar&page=admin", 2);
    }

    function main_pref()
    {
        global $nuked, $language;

        if ($nuked['birthday'] == "off")
        {
            $checked1 = "selected=\"selected\"";
        }
        else
        {
            $checked1 = "";
        }
        if ($nuked['birthday'] == "all")
        {
            $checked2 = "selected=\"selected\"";
        }
        else
        {
            $checked2 = "";
        }
        if ($nuked['birthday'] == "team")
        {
            $checked3 = "selected=\"selected\"";
        }
        else
        {
            $checked3 = "";
        }
        if ($nuked['birthday'] == "admin")
        {
            $checked4 = "selected=\"selected\"";
        }
        else
        {
            $checked4 = "";
        }

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINCAL . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Calendar.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Calendar&amp;page=admin\">" . _NAVCALENDAR . "</a> | "
        . "<a href=\"index.php?file=Calendar&amp;page=admin&amp;op=add\">" . _ADDEVENT . "</a> | "
        . "</b>" . _PREFS . "</div><br />\n"
        . "<form method=\"post\" action=\"index.php?file=Calendar&amp;page=admin&amp;op=change_pref\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td colspan=\"2\" align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
        . "<tr><td>" . _SHOWBIRTHDAY . " :</td><td><select name=\"birthday\">\n"
        . "<option value=\"off\" " . $checked1 . ">" . _OFF . "</option>\n"
        . "<option value=\"all\" " . $checked2 . ">" . _SHOWALL . "</option>\n"
        . "<option value=\"team\" " . $checked3 . ">" . _SHOWTEAM . "</option>\n"
        . "<option value=\"admin\" " . $checked4 . ">" . _SHOWADMIN . "</option>\n"
        . "</select></td></tr></table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Calendar&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function change_pref($birthday)
    {
        global $nuked;

        $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $birthday . "' WHERE name = 'birthday'");
        // Action
        $texteaction = "". _ACTIONPREFUPCAL .".";
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _PREFUPDATED . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Calendar&page=admin", 2);
    }

    switch ($_REQUEST['op'])
    {
        case "add":
            add();
            break;

        case "del":
            del($_REQUEST['eid']);
            break;

        case "do_edit":
            do_edit($_REQUEST['eid'], $_REQUEST['description'], $_REQUEST['titre'], $_REQUEST['heure'], $_REQUEST['date_an'], $_REQUEST['date_mois'], $_REQUEST['date_jour']);
            break;

        case "edit":
            edit($_REQUEST['eid']);
            break;

        case "do_add":
            do_add($_REQUEST['description'], $_REQUEST['titre'], $_REQUEST['heure'], $_REQUEST['date_an'], $_REQUEST['date_mois'], $_REQUEST['date_jour']);
            break;

        case "main_pref":
            main_pref();
            break;

        case "change_pref":
            change_pref($_REQUEST['birthday']);
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