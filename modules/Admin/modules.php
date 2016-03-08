<?php
/**
 * modules.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', SUPER_ADMINISTRATOR_ACCESS))
    return;


function edit_module($mid)
{
    global $nuked, $language;

    $sql = mysql_query("SELECT id, nom, niveau, admin FROM " . MODULES_TABLE . " WHERE id = '" . $mid . "'");
    list($mid, $nom, $niveau, $level) = mysql_fetch_array($sql);

    if ($niveau > -1 && $level > -1)
    {
        $button = "&nbsp;&nbsp;&nbsp;<input class=\"button\" type=\"button\" value=\"" . _OFFMODULE . "\" onclick=\"document.location='index.php?file=Admin&amp;page=modules&amp;op=desactive&amp;mid=" . $mid . "'\" />";
        $read = "";
    }
    else
    {
        $button = "&nbsp;&nbsp;&nbsp;<input class=\"button\" type=\"button\" value=\"" . _ONMODULE . "\" onclick=\"document.location='index.php?file=Admin&amp;page=modules&amp;op=active&amp;mid=" . $mid . "'\" />"
        . "<input type=\"hidden\" name=\"niveau\" value=\"-1\" /><input type=\"hidden\" name=\"level\" value=\"-1\" />";
        $read = "disabled";
    }
    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _MODULE . "&nbsp;" . $nom . "</h3>\n";

    echo "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/modules.php\" rel=\"modal\">\n"
. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
. "</div></div>\n"
. "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=modules&amp;op=update_module\">\n"
. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\">\n"
. "<tr><td><b>" . _LEVELACCES . " :</b></td><td><select name=\"niveau\" " . $read . "><option>" . $niveau . "</option>\n"
. "<option>0</option>\n"
. "<option>1</option>\n"
. "<option>2</option>\n"
. "<option>3</option>\n"
. "<option>4</option>\n"
. "<option>5</option>\n"
. "<option>6</option>\n"
. "<option>7</option>\n"
. "<option>8</option>\n"
. "<option>9</option></select></td></tr>\n";

    if ($nom == "Team" || $nom == "Members" || $nom == "Vote")
    {
        echo "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"level\" value=\"" . $level . "\" /></td></tr>";
    }
    else
    {
        echo "<tr><td><b>" . _LEVELADMIN . " :</b></td><td><select name=\"level\" " . $read . "><option>" . $level . "</option>\n"
        . "<option>1</option>\n"
        . "<option>2</option>\n"
        . "<option>3</option>\n"
        . "<option>4</option>\n"
        . "<option>5</option>\n"
        . "<option>6</option>\n"
        . "<option>7</option>\n"
        . "<option>8</option>\n"
        . "<option>9</option></select></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n";
    }

    echo "<tr><td colspan=\"2\"><small><b>" . _MEMBERS . " :</b> " . _LEVEL1 . "<br /><b> " . _ADMINFIRST . " :</b> " . _LEVEL2 . " <br /><b> " . _ADMINSUP . " :</b> " . _LEVEL9 . "</small></td></tr>\n"
. "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"mid\" value=\"" . $mid . "\" /></td></tr>\n"
. "<tr><td colspan=\"2\" align=\"center\"></td></tr></table>\n"
. "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _EDITMODULE . "\" />" . $button . "<a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=modules\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function desactive($mid)
{
    global $nuked, $user;

    $mid = mysql_real_escape_string(stripslashes($mid));

    $sql2 = mysql_query("SELECT nom FROM " . MODULES_TABLE . " WHERE id = '" . $mid . "'");
    list($nom) = mysql_fetch_array($sql2);
    $sql = mysql_query("UPDATE " . MODULES_TABLE . " SET niveau = -1, admin = -1 WHERE id = '" . $mid . "'");

    if (in_array($nom, explode('|', $nuked['rssFeed'])))
        updateModuleRssList($nom, 'disabled');

    saveUserAction(_ACTIONDESMOD .': '. $nom);

    printNotification(_MODULEDISABLED, 'success');

    require_once 'Includes/nkSitemap.php';

    if (! nkSitemap_write()) {
        printNotification(__('WRITE_SITEMAP_FAILED'), 'error');
        redirect('index.php?file=Admin&page=modules', 5);
        return;
    }

    redirect("index.php?file=Admin&page=modules", 2);
}

function active($mid)
{
    global $nuked, $user;

    $mid = mysql_real_escape_string(stripslashes($mid));
    $sql2 = mysql_query("SELECT nom FROM " . MODULES_TABLE . " WHERE id = '" . $mid . "'");
    list($nom) = mysql_fetch_array($sql2);
    $sql = mysql_query("UPDATE " . MODULES_TABLE . " SET niveau = 0, admin = 2 WHERE id = '" . $mid . "'");

    if (in_array($nom, explode('|', $nuked['rssFeed'])))
        updateModuleRssList($nom, 'enabled');

    saveUserAction(_ACTIONACTMOD .': '. $nom);

    printNotification(_MODULEENABLED, 'success');

    require_once 'Includes/nkSitemap.php';

    if (! nkSitemap_write()) {
        printNotification(__('WRITE_SITEMAP_FAILED'), 'error');
        redirect('index.php?file=Admin&page=modules', 5);
        return;
    }

    redirect("index.php?file=Admin&page=modules", 2);
}

function update_module($mid, $niveau, $level)
{
    global $nuked, $user;

    $mid = mysql_real_escape_string(stripslashes($mid));

    $sql2 = mysql_query("SELECT nom FROM " . MODULES_TABLE . " WHERE id = '" . $mid . "'");
    list($nom) = mysql_fetch_array($sql2);
    $niveau = mysql_real_escape_string(stripslashes($niveau));
    $level = mysql_real_escape_string(stripslashes($level));
    $sql = mysql_query("UPDATE " . MODULES_TABLE . " SET niveau = '" . $niveau . "', admin = '" . $level . "' WHERE id = '" . $mid . "'");

    saveUserAction(_ACTIONMODIFMOD .': '. $nom);

    printNotification(_MODULEMODIF, 'success');

    require_once 'Includes/nkSitemap.php';

    if (! nkSitemap_write()) {
        printNotification(__('WRITE_SITEMAP_FAILED'), 'error');
        redirect('index.php?file=Admin&page=modules', 5);
        return;
    }

    redirect("index.php?file=Admin&page=modules", 2);
}

function updateModuleRssList($module, $status) {
    global $nuked;

    $rssFeed = explode('|', $nuked['rssFeed']);

    if ($status == 'disabled') {
        $k = array_search($module, $rssFeed);

        if ($k !== false) unset($rssFeed[$k]);
    }
    else if ($status == 'enabled') {
        $rssFeed[] = $module;
        natcasesort($rssFeed);
    }

    nkDB_update(CONFIG_TABLE, array(
            'value' => implode('|', $rssFeed)
        ),
        'name = \'rssFeed\''
    );
}


function main()
{
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _MODULE . "</h3>\n";

    echo "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/modules.php\" rel=\"modal\">\n"
. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
. "</div></div>\n"
. "<div class=\"tab-content\" id=\"tab2\"><table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
. "<tr>\n"
. "<td style=\"width: 30%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
. "<td style=\"width: 20%;\" align=\"center\"><b>" . _STATUS . "</b></td>\n"
. "<td style=\"width: 15%;\" align=\"center\"><b>" . _LEVELACCES . "</b></td>\n"
. "<td style=\"width: 15%;\" align=\"center\"><b>" . _LEVELADMIN . "</b></td>\n"
. "<td style=\"width: 20%;\" align=\"center\"><b>" . _EDIT . "</b></td></tr>\n";

    $mod = array();
    $sql = mysql_query("SELECT id, nom, niveau, admin FROM " . MODULES_TABLE . " ORDER BY nom");
    while (list($mid, $nom, $niveau, $admin) = mysql_fetch_array($sql))
    {
        $moduleNameConst = strtoupper($nom) .'_MODNAME';

        if (translationExist($moduleNameConst))
            $moduleName = __($moduleNameConst);
        else
            $moduleName = $nom;

        if ($niveau > -1 && $admin > -1)
        {
            $status = _ENABLED;
        }
        else
        {
            $status = _DISABLED;
        }

        array_push($mod, $moduleName . "|" . $mid . "|" . $niveau . "|" . $admin . "|" . $status);
    }
    natcasesort($mod);
    foreach($mod as $value)
    {
        $temp = explode("|", $value);


        echo "<tr>\n"
        . "<td style=\"width: 30%;\">&nbsp;" . $temp[0] . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $temp[4] . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\">" . $temp[2] . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\">" . $temp[3] . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=modules&amp;op=edit_module&amp;mid=" . $temp[1] . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _MODULEEDIT . "\" /></a></td></tr>\n";
    }
    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br />\n";
}


switch ($GLOBALS['op']) {
    case "update_module":
        update_module($_REQUEST['mid'], $_REQUEST['niveau'], $_REQUEST['level']);
        break;

    case "desactive":
        desactive($_REQUEST['mid']);
        break;

    case "active":
        active($_REQUEST['mid']);
        break;

    case "edit_module":
        edit_module($_REQUEST['mid']);
        break;

    case "main":
        main();
        break;

    default:
        main();
        break;
}

?>