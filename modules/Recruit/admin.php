<?php
/**
 * admin.php
 *
 * Backend of Recruit module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Recruit'))
    return;


function main()
{
    global $nuked, $language;

echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _RECRUIT . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Recruit.php\" rel=\"modal\">\n"
. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
. "</div></div>\n"
. "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(1);

echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
. "<tr>\n"
. "<td style=\"width: 25%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
. "<td style=\"width: 20%;\" align=\"center\"><b>" . _FIRSTNAME . "</b></td>\n"
. "<td style=\"width: 15%;\" align=\"center\"><b>" . _GAME . "</b></td>\n"
. "<td style=\"width: 20%;\" align=\"center\"><b>" . _MAIL . "</b></td>\n"
. "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT id, pseudo, prenom, mail, game, date FROM " . RECRUIT_TABLE . " ORDER BY id DESC");
    $count = mysql_num_rows($sql);
    while (list($rid, $pseudo, $prenom, $mail, $game, $date) = mysql_fetch_array($sql))
    {
        $date = strftime("%x", $date);

        $sql2 = nkDB_execute("SELECT name FROM " . GAMES_TABLE . " WHERE id='$game'");
        list($game_name) = mysql_fetch_array($sql2);
        $game_name = nkHtmlEntities($game_name);


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
    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
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
    . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Recruit.php\" rel=\"modal\">\n"
. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
. "</div></div>\n"
. "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(2);

echo "<form method=\"post\" action=\"index.php?file=Recruit&amp;page=admin&amp;op=update_pref\" onsubmit=\"backslash('charte_recruit');\">\n"
. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
. "<tr><td><b>" . _RECRUTE . "</b> : <select name=\"recrute\">\n"
. "<option value=\"" . $nuked['recrute'] . "\">" . $etat . "</option>\n"
. "<option value=\"1\">" . _OPEN . "</option>\n"
. "<option value=\"0\">" . _CLOSE . "</option></select></td></tr>\n"
. "<tr><td><b>" . _MAILAVERT . "</b> : <input type=\"text\" size=\"30\" name=\"recrute_mail\" value=\"" . $nuked['recrute_mail'] . "\" /></td></tr>\n"
. "<tr><td><b>" . _INBOXAVERT . "</b> : <select name=\"recrute_inbox\"><option value=\"\">" . _OFF . "</option>\n";

    $sql2 = nkDB_execute("SELECT id, pseudo FROM " . USER_TABLE . " WHERE niveau > 1 ORDER BY niveau DESC");
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
. "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Recruit&amp;page=admin\">" . __('BACK') . "</a></div>\n"
. "</form><br /></div></div>\n";
}

function update_pref($recrute_mail, $recrute_inbox, $recrute_charte, $recrute)
{
    global $nuked, $user;

    $recrute_charte = nkHtmlEntityDecode($recrute_charte);
    $recrute_charte = mysql_real_escape_string(stripslashes($recrute_charte));

    $upd = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $recrute . "' WHERE name = 'recrute'");
    $upd1 = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $recrute_charte . "' WHERE name = 'recrute_charte'");
    $upd2 = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $recrute_mail . "' WHERE name = 'recrute_mail'");
    $upd3 = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $recrute_inbox . "' WHERE name = 'recrute_inbox'");

    saveUserAction(_ACTIONPREFREC .'.');

    printNotification(_PREFUPDATED, 'success');
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

    $sql = nkDB_execute("SELECT pseudo, prenom, age, mail, icq, country, game, `connection`, experience, dispo, comment FROM " . RECRUIT_TABLE . " WHERE id = '" . $rid . "'");
    list($pseudo, $prenom, $age, $mail, $icq, $country, $game, $connection, $experience, $dispo, $comment) = mysql_fetch_array($sql);
    list ($pays, $ext) = explode ('.', $country);

    $sql2 = nkDB_execute("SELECT name FROM " . GAMES_TABLE . " WHERE id = '" . $game . "'");
    list($game_name) = mysql_fetch_array($sql2);
    $game_name = nkHtmlEntities($game_name);

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

    echo "</td></tr></table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _REMOVE . "\" onclick=\"javascript:del_recruit('" . addslashes($pseudo) . "', '" . $rid . "');\" />\n"
. "<a class=\"buttonLink\" href=\"index.php?file=Recruit&amp;page=admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function del($rid)
{
    global $nuked, $user;

    $del = nkDB_execute("DELETE FROM " . RECRUIT_TABLE . " WHERE id = '" . $rid ."'");

    saveUserAction(_ACTIONDELREC .'.');

    printNotification(_RECRUITDELETE, 'success');
    redirect("index.php?file=Recruit&page=admin", 2);
}

function nkAdminMenu($tab = 1)
{
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Recruit&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _NAVRECRUIT; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Recruit&amp;page=admin&amp;op=edit_pref">
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

?>