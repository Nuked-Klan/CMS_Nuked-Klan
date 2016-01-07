<?php
/**
 * admin.php
 *
 * Backend of Textbox module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Textbox'))
    return;


function edit_shout($mid)
{
    global $nuked, $language;

    $sql = mysql_query("SELECT auteur, texte, ip FROM " . TEXTBOX_TABLE . " WHERE id = '" . $mid . "'");
    list($pseudo, $texte, $ip) = mysql_fetch_array($sql);
    $texte = nkHtmlSpecialChars($texte);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _EDITTHISMESS . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Textbox.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Textbox&amp;page=admin&amp;op=modif_shout\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"60%\" border=\"0\">\n"
    . "<tr><td><b>" . _NICKNAME . " :</b> " . $pseudo . " ( " . $ip . " )</td></tr>\n"
    . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _SHOUT . " :</b></td></tr>\n"
    . "<tr><td><textarea name=\"texte\" cols=\"65\" rows=\"10\">" . $texte . "</textarea></td></tr>\n"
    . "<tr><td align=\"center\"><input type=\"hidden\" name=\"mid\" value=\"" . $mid . "\" />&nbsp;</td></tr>\n"
    . "</table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIF . "\" /><a class=\"buttonLink\" href=\"index.php?file=Textbox&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function modif_shout($mid, $texte)
{
    global $nuked, $user;

    $texte = mysql_real_escape_string(stripslashes($texte));

    $sql = mysql_query("UPDATE " . TEXTBOX_TABLE . " SET texte = '" . $texte . "' WHERE id = '" . $mid . "'");

    saveUserAction(_ACTIONMODIFSHO .'.');

    printNotification(_MESSEDIT, 'success');
    redirect("index.php?file=Textbox&page=admin", 2);
}

function del_shout($mid)
{
    global $nuked, $user;

    $sql = mysql_query("DELETE FROM " . TEXTBOX_TABLE . " WHERE id = '" . $mid . "'");

    saveUserAction(_ACTIONDELSHO .'.');

    printNotification(_MESSDEL, 'success');
    redirect("index.php?file=Textbox&page=admin", 2);
}

function del_all_shout()
{
    global $nuked, $user;

    $sql = mysql_query("DELETE FROM " . TEXTBOX_TABLE);

    saveUserAction(_ACTIONALLDELSHO .'.');

    printNotification(_ALLMESSDEL, 'success');
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
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(1);

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
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_shout('" . addslashes($auteur) . "', '" . $id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISMESS . "\" /></a></td></tr>\n";
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

    echo "<div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function main_pref()
{
    global $nuked, $language;

    $checked1 = false;

    if ($nuked['textbox_avatar'] == "on") $checked1 = true;

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
    . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Textbox.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(2);

    printNotification(_NOTIF_INFOS_DISPLAY);

    echo "<form method=\"post\" action=\"index.php?file=Textbox&amp;page=admin&amp;op=change_pref\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
    . "<tr><td>" . _NUMBERSHOUT . " :</td><td> <input type=\"text\" name=\"max_shout\" size=\"2\" value=\"" . $nuked['max_shout'] . "\" /></td></tr>\n"
    . "<tr><td>" . _DISPLAY_AVATAR . " :</td><td>";

    checkboxButton('textbox_avatar', 'textbox_avatar', $checked1, false);

    echo "</td></tr>\n"
    . "</table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" name=\"Submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Textbox&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function change_pref($max_shout, $textbox_avatar)
{
    global $nuked, $user;

    if ($textbox_avatar != "on") {
        $textbox_avatar = "off";
    }

    $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_shout . "' WHERE name = 'max_shout'");
    $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $textbox_avatar . "' WHERE name = 'textbox_avatar'");

    saveUserAction(_ACTIONCONFSHO .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect("index.php?file=Textbox&page=admin", 2);
}

    function nkAdminMenu($tab = 1)
{
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Textbox&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _SHOUTBOX; ?></span>
                </a>
            </li>

            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Textbox&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="javascript:delall();">
                    <img src="modules/Admin/images/icons/remove_from_database.png" alt="icon" />
                    <span><?php echo _DELALLMESS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($_REQUEST['op']) {
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
        change_pref($_REQUEST['max_shout'], $_REQUEST['textbox_avatar']);
        break;

    default:
        main();
        break;
}

?>