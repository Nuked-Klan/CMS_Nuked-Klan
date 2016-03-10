<?php
/**
 * admin.php
 *
 * Backend of Calendar module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Calendar'))
    return;


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
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(1);

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _AUTEUR . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT id, titre, auteur, date_jour, date_mois, date_an, heure FROM " . CALENDAR_TABLE . " ORDER BY date_an DESC, date_mois DESC, date_jour DESC");
    $count = nkDB_numRows($sql);
    while (list($eid, $titre, $auteur, $jour, $mois, $an, $heure) = nkDB_fetchArray($sql))
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
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_event('" . addslashes($titre) . "', '" . $eid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISEVENT . "\" /></a></td></tr>\n";
    }
    if ($count == 0)
    {
        echo "<tr><td colspan=\"5\" align=\"center\">" . _NOEVENT . "</td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function add()
{
    global $language;

    $jour = strftime("%d", time());
    $mois = strftime("%m", time());
    $an = strftime("%Y", time());
    $heure = strftime("%H:%M", time());

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADDEVENT . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Calendar.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(2);

    echo "<form method=\"post\" action=\"index.php?file=Calendar&amp;page=admin&amp;op=do_add\"\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
    . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" maxlength=\"100\" size=\"45\" /></td></tr>\n"
    . "<tr><td><b>" . _DESCR . " :</b><br />"
    . "<textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr>\n"
    . "<tr><td><b>" . _JOUR . " :</b> <input type=\"text\" name=\"date_jour\" maxlength=\"2\" size=\"2\" value=\"" . $jour . "\" />&nbsp;"
    . "<b>" . _MOIS . " :</b> <input type=\"text\" name=\"date_mois\" maxlength=\"2\" size=\"2\" value=\"" . $mois . "\" />&nbsp;"
    . "<b>" . _ANNEE . " :</b> <input type=\"text\" name=\"date_an\" maxlength=\"4\" size=\"4\" value=\"" . $an . "\" />&nbsp;"
    . "<b>" . _HEURE . " :</b> <input type=\"text\" name=\"heure\" maxlength=\"5\" size=\"5\" value=\"" . $heure . "\" />\n"
    . "</td></tr></table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _ADDTHISEVENT . "\" /><a class=\"buttonLink\" href=\"index.php?file=Calendar&amp;page=admin&amp;op=main\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function do_add($description, $titre, $heure, $date_an, $date_mois, $date_jour)
{
    global $nuked, $user;

    $description = nkHtmlEntityDecode($description);
    $description = nkDB_realEscapeString(stripslashes($description));
    $titre = nkDB_realEscapeString(stripslashes($titre));

    $sql = nkDB_execute("INSERT INTO " . CALENDAR_TABLE . " ( `id` , `titre` , `description` , `date_jour` , `date_mois` , `date_an` , `heure` , `auteur` ) VALUES ( '' , '" . $titre . "' , '" . $description . "' , '" . $date_jour . "' , '" . $date_mois . "' , '" . $date_an . "' , '" . $heure . "' , '" . $user[2] . "' )");

    saveUserAction(_ACTIONADDCAL .': '. $titre);

    printNotification(_EVENTADD, 'success');

    setPreview('index.php?file=Calendar&m='. $date_mois .'&y='. $date_an, 'index.php?file=Calendar&page=admin');
}

function edit($eid)
{
    global $nuked, $language;

    $sql = nkDB_execute("SELECT id, titre, description, date_jour, date_mois, date_an, heure FROM " . CALENDAR_TABLE . " WHERE id = '" . $eid . "'");
    list($eid, $titre, $description, $jour, $mois, $an, $heure) = nkDB_fetchArray($sql);
    $titre = nkHtmlSpecialChars($titre);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _EDITTHISEVENT . "</h3>\n"
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
    . "</td></tr></table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISEVENT . "\" /><input type=\"hidden\" name=\"eid\" value=\"" . $eid . "\" /><a class=\"buttonLink\" href=\"index.php?file=Calendar&amp;page=admin&amp;op=main\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function do_edit($eid, $description, $titre, $heure, $date_an, $date_mois, $date_jour)
{
    global $nuked, $user;

    $description = nkHtmlEntityDecode($description);
    $titre = nkDB_realEscapeString(stripslashes($titre));
    $description = nkDB_realEscapeString(stripslashes($description));

    $upd = nkDB_execute("UPDATE " . CALENDAR_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "', date_jour = '" . $date_jour . "', date_mois = '" . $date_mois . "', date_an = '" . $date_an . "', heure = '" . $heure . "' WHERE id = '" . $eid . "'");

    saveUserAction(_ACTIONMODIFCAL .': '. $titre);

    printNotification(_EVENTMODIF, 'success');

    setPreview('index.php?file=Calendar&m='. $date_mois .'&y='. $date_an, 'index.php?file=Calendar&page=admin');
}

function del($eid)
{
    global $nuked, $user;

    $sql = nkDB_execute("SELECT titre FROM " . CALENDAR_TABLE . " WHERE id = '" . $eid . "'");
    list($titre) = nkDB_fetchArray($sql);
    $del = nkDB_execute("DELETE FROM " . CALENDAR_TABLE . " WHERE id = '" . $eid . "'");

    saveUserAction(_ACTIONDELCAL .': '. $titre);

    printNotification(_EVENTDEL, 'success');
    redirect('index.php?file=Calendar&page=admin', 2);
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
    . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Calendar.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(3);

    echo "<form method=\"post\" action=\"index.php?file=Calendar&amp;page=admin&amp;op=change_pref\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
    . "<tr><td colspan=\"2\" align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
    . "<tr><td>" . _SHOWBIRTHDAY . " :</td><td><select name=\"birthday\">\n"
    . "<option value=\"off\" " . $checked1 . ">" . _OFF . "</option>\n"
    . "<option value=\"all\" " . $checked2 . ">" . _SHOWALL . "</option>\n"
    . "<option value=\"team\" " . $checked3 . ">" . _SHOWTEAM . "</option>\n"
    . "<option value=\"admin\" " . $checked4 . ">" . _SHOWADMIN . "</option>\n"
    . "</select></td></tr></table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Calendar&amp;page=admin\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function change_pref($birthday)
{
    global $nuked;

    $upd = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $birthday . "' WHERE name = 'birthday'");

    saveUserAction(_ACTIONPREFUPCAL .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect('index.php?file=Calendar&page=admin', 2);
}

function nkAdminMenu($tab = 1) {
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Calendar&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _NAVCALENDAR; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Calendar&amp;page=admin&amp;op=add">
                    <img src="modules/Admin/images/icons/add_event.png" alt="icon" />
                    <span><?php echo _ADDEVENT; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Calendar&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($GLOBALS['op']) {
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

?>