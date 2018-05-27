<?php
/**
 * admin.php
 *
 * Backend of Guestbook module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Guestbook'))
    return;


function edit_book($gid)
{
    global $nuked, $language;

    $gid = (int) $gid;

    $sql = nkDB_execute("SELECT name, comment, email, url FROM " . GUESTBOOK_TABLE . " WHERE id = '" . $gid . "'");
    list($name, $comment, $email, $url) = nkDB_fetchArray($sql);

    $url   = nkHtmlEntities($url);
    $email = nkHtmlEntities($email);

    echo '<script type="text/javascript">
    function checkEditGuestbookMsg(){
        if(! isEmail(\'guestbookMsgEmail\')){
            alert(\''. addslashes(__('BAD_EMAIL')) .'\');
            return false;
        }

        return true;
    }
    </script>';

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _EDITTHISPOST . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Guestbook.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Guestbook&amp;page=admin&amp;op=modif_book\" onsubmit=\"return checkEditGuestbookMsg()\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
    . "<tr><td><b>" . __('AUTHOR') . " :</b></td><td>" . $name . "</td></tr>\n"
    . "<tr><td><b>" . _MAIL . " : </b></td><td><input id=\"guestbookMsgEmail\" type=\"text\" name=\"email\" size=\"40\" value=\"" . $email . "\" /></td></tr>\n"
    . "<tr><td><b>" . _URL . " : </b></td><td><input type=\"text\" name=\"url\" size=\"40\" value=\"" . $url . "\" /></td></tr>\n";

    echo "<tr><td colspan=\"2\"><b>" . _COMMENT . " :</b></td></tr>\n"
    . "<tr><td colspan=\"2\"><textarea class=\"editor\" id=\"guest_text\" name=\"comment\" cols=\"65\" rows=\"12\">" . $comment . "</textarea></td></tr>\n"
    . "<tr><td colspan=\"2\" align=\"center\"><input type=\"hidden\" name=\"gid\" value=\"" . $gid . "\" /></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" name=\"send\" value=\"" . _MODIF . "\" /><a class=\"buttonLink\" href=\"index.php?file=Guestbook&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div>\n";
}

function modif_book($gid, $comment, $email, $url)
{
    global $nuked, $user;

    $gid = (int) $gid;

    $comment = stripslashes($comment);
    $email   = stripslashes($email);
    $url     = stripslashes($url);

    $email = nkHtmlEntities(nkHtmlEntityDecode($email));
    $email = checkEmail($email);

    if (($error = getCheckEmailError($email)) !== false) {
        printNotification($error, 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    $comment = secu_html(nkHtmlEntityDecode($comment));

    if (!empty($url) && stripos($url, 'http://') === false)
    {
        $url = "http://" . $url;
    }

    $email   = nkDB_realEscapeString($email);
    $url     = nkDB_realEscapeString($url);
    $comment = nkDB_realEscapeString($comment);

    nkDB_execute(
        "UPDATE ". GUESTBOOK_TABLE ."
        SET email = '" . $email . "',
        url = '" . $url . "',
        comment = '" . $comment . "'
        WHERE id = '" . $gid . "'"
    );

    saveUserAction(_ACTIONMODIFBOOK .'.');

    printNotification(_POSTEDIT, 'success');
    setPreview('index.php?file=Guestbook', 'index.php?file=Guestbook&page=admin');
}

function del_book($gid)
{
    global $nuked, $user;

    $gid = (int) $gid;

    nkDB_execute("DELETE FROM " . GUESTBOOK_TABLE . " WHERE id = '" . $gid . "'");

    saveUserAction(_ACTIONDELBOOK .'.');

    printNotification(_POSTDELETE, 'success');
    setPreview('index.php?file=Guestbook', 'index.php?file=Guestbook&page=admin');
}

function main()
{
    global $nuked, $language, $p;

    $nb_mess_guest = 30;

    $sql2 = nkDB_execute("SELECT id FROM " . GUESTBOOK_TABLE);
    $count = nkDB_numRows($sql2);

    $start = $p * $nb_mess_guest - $nb_mess_guest;

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
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(1);

    if ($count > $nb_mess_guest)
    {
        number($count, $nb_mess_guest, "index.php?file=Guestbook&amp;page=admin");
    }


    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . __('AUTHOR') . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _IP . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT id, date, name, host FROM " . GUESTBOOK_TABLE . " ORDER BY id DESC LIMIT " . $start . ", " . $nb_mess_guest."");
    while (list($id, $date, $name, $ip) = nkDB_fetchArray($sql))
    {
        $date = nkDate($date);
        $name = nk_CSS($name);

        echo "<tr>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td>\n"
        . "<td style=\"width: 25%;\" align=\"center\">" . $name . "</td>\n"
        . "<td style=\"width: 25%;\" align=\"center\">" . $ip . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Guestbook&amp;page=admin&amp;op=edit_book&amp;gid=" . $id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISPOST . "\" /></a></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:delmess('" . addslashes($name) . "', '" . $id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISPOST . "\" /></a></td></tr>\n";
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

    echo "<div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function main_pref()
{
    global $nuked, $language;

    echo '<script type="text/javascript">
    function checkGuestbookSetting(){
        if(! document.getElementById(\'nbGuestbookMsg\').value.match(/^\d+$/)){
            alert(\''. _NB_GUESTBOOK_MSG_NO_INTEGER .'\');
            return false;
        }

        return true;
    }
    </script>';

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Guestbook.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(2);

    echo "<form onsubmit=\"return checkGuestbookSetting()\" method=\"post\" action=\"index.php?file=Guestbook&amp;page=admin&amp;op=change_pref\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
    . "<tr><td>" . _GUESTBOOKPG . " :</td><td><input id=\"nbGuestbookMsg\" type=\"text\" name=\"mess_guest_page\" size=\"2\" value=\"" . $nuked['mess_guest_page'] . "\" /></td></tr>\n"
    . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" name=\"Submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Guestbook&amp;page=admin\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function change_pref($mess_guest_page)
{
    global $nuked, $user;

    $upd = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $mess_guest_page . "' WHERE name = 'mess_guest_page'");

    saveUserAction(_ACTIONPREFBOOK .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect("index.php?file=Guestbook&page=admin", 2);
}

function nkAdminMenu($tab = 1)
{
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Guestbook&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _GUESTBOOK; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Guestbook&amp;page=admin&amp;op=main_pref">
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

?>
