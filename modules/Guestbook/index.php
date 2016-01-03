<?php
/**
 * index.php
 *
 * Frontend of Guestbook module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Guestbook'))
    return;

compteur('Guestbook');

function post_book()
{
    global $user, $nuked;

    define('EDITOR_CHECK', 1);

    opentable();

    ?>
    <script type="text/javascript">
        function trim(string){
            return string.replace(/(^\s*)|(\s*$)/g,'');
        }

        function verifchamps(){
            if (trim(document.getElementById('guest_name').value) == ""){
                alert('<?php echo _NONICK; ?>');
                return false;
            }
            if (document.getElementById('guest_mail').value.indexOf('@') == -1){
                alert('<?php echo __('BAD_EMAIL') ?>');
                return false;
            }
        }
    </script>
    <?php

    if ($user)
    {
        $sql = mysql_query("SELECT url, email FROM " . USER_TABLE . " WHERE pseudo = '" . $user[2] . "'");
        list($url, $mail) = mysql_fetch_array($sql);
    }

    echo "<br /><div style=\"text-align: center;\"><big><b>" . _GUESTBOOK . "</b></big></div><br />\n"
    . "<form method=\"post\" action=\"index.php?file=Guestbook&amp;op=send_book\">\n"
    . "<table style=\"margin: auto; width: 98%; text-align: left;\" cellspacing=\"0\" cellpadding=\"2\"border=\"0\">\n"
    . "<tr><td><b>" . _AUTHOR . " :</b></td><td>";
    if ($user) echo '<b>' . $user[2] . '</b></td></tr>'; else echo "<input id=\"guest_name\" type=\"text\" name=\"name\" value=\"\" size=\"20\" maxlength=\"30\" /></td></tr>\n";
    echo "<tr><td><b>" . _MAIL . " :</b></td><td>"; if ($mail) echo '<b>' . $mail . '</b></td></tr>'; else echo "<input id=\"guest_mail\" type=\"text\" name=\"email\" value=\"\" size=\"40\" maxlength=\"80\" /></td></tr>\n";
    echo "<tr><td><b>" . _URL . " :</b></td><td>"; if ($url) echo '<b>' . $url . '</b></td></tr>'; else echo "<input type=\"text\" name=\"url\" value=\"\" size=\"40\" maxlength=\"80\" /></td></tr>\n";

    if (initCaptcha()) echo create_captcha();

    echo "<tr><td colspan=\"2\"><b>" . _COMMENT . " :</b></td></tr>\n"
    . "<tr><td colspan=\"2\"><textarea id=\"e_basic\" name=\"comment\" cols=\"65\" rows=\"12\"></textarea></td></tr>\n"
    . "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" value=\"" . _SEND . "\" />&nbsp;<input type=\"button\" value=\"" . _CANCEL . "\" onclick=\"javascript:history.back()\" /></td></tr></table></form><br />\n";

    closetable();
}

function send_book($name, $email, $url, $comment)
{
    global $user, $nuked, $user_ip;

    opentable();

    // Verification code captcha
    if (initCaptcha() && ! validCaptchaCode())
        return;

    if ($user[2] != "")
    {
        $pseudo = $user[2];
    }
    else
    {
        $pseudo = checkNickname($name);
        $pseudo = nkHtmlEntities($pseudo, ENT_QUOTES);

        if (($error = getCheckNicknameError($pseudo)) !== false) {
            printNotification($error, 'error');
            redirect('index.php?file=Guestbook&op=post_book', 2);
            closetable();
            return;
        }
    }

    $email = nkHtmlEntities($email);
    $email = checkEmail($email);

    if (($error = getCheckEmailError($email)) !== false) {
        printNotification($error, 'error');
        redirect('index.php?file=Guestbook&op=post_book', 2);
        closetable();
        return;
    }

    $sql2 = mysql_query("SELECT date, host FROM " . GUESTBOOK_TABLE . " ORDER BY id DESC LIMIT 0, 1");
    list($flood_date, $flood_ip) = mysql_fetch_array($sql2);

    $anti_flood = $flood_date + 60;

    $date = time();

    if ($user_ip == $flood_ip && $date < $anti_flood)
    {
        printNotification(_NOFLOOD, 'error');
        redirect("index.php?file=Guestbook", 2);
        closetable();
    }
    else if ($comment != "")
    {
        $date = time();
        $comment = secu_html(nkHtmlEntityDecode($comment));
        $comment = mysql_real_escape_string(stripslashes($comment));
        $pseudo = mysql_real_escape_string(stripslashes($pseudo));
        $email = mysql_real_escape_string(stripslashes($email));

        if (!empty($url) && !is_int(stripos($url, 'http://')))
        {
            $url = "http://" . mysql_real_escape_string(stripslashes($url));
        }

        $sql = mysql_query("INSERT INTO " . GUESTBOOK_TABLE . " ( `id` , `name` , `email` , `url` , `date` , `host` , `comment` ) VALUES ( '' , '" . $pseudo . "' , '" . $email . "' , '" . $url . "' , '" . $date . "' , '" . $user_ip . "' , '" . $comment . "' )");

        printNotification(_POSTADD, 'success');
        redirect("index.php?file=Guestbook", 2);
        closetable();
    }
    else
    {
        printNotification(_NOTEXT, 'error');
        redirect("index.php?file=Guestbook", 2);
        closetable();
    }
}

function index()
{
    global $nuked, $language, $bgcolor1, $bgcolor2, $bgcolor3, $user, $visiteur;

    opentable();

    $nb_mess_guest = $nuked['mess_guest_page'];

    $sql = mysql_query("SELECT id FROM " . GUESTBOOK_TABLE);
    $count = mysql_num_rows($sql);

    if(array_key_exists('p', $_REQUEST)){
        $page = $_REQUEST['p'];
    }
    else{
        $page = 1;
    }
    $start = $page * $nb_mess_guest - $nb_mess_guest;

    echo "<br /><div style=\"text-align: center;\"><big><b>" . _GUESTBOOK . "</b></big>\n"
    . "<br /><br />[ <a href=\"index.php?file=Guestbook&amp;op=post_book\">" . _SIGNGUESTBOOK . "</a> ]</div><br />\n";

    if ($count > $nb_mess_guest)
    {
        number($count, $nb_mess_guest, "index.php?file=Guestbook");
    }

    echo "<table style=\"background: " . $bgcolor3 . ";margin:auto\" width=\"98%\" cellpadding=\"3\" cellspacing=\"1\">\n"
    . "<tr style=\"background: " . $bgcolor3 . ";\">\n"
    . "<td style=\"width: 30%;\" align=\"center\"><b>" . _AUTHOR . "</b></td>\n"
    . "<td style=\"width: 70%;\" align=\"center\"><b>" . _COMMENT . "</b></td></tr>\n";

    $sql2 = mysql_query("SELECT id, name, comment, email, url, date, host FROM " . GUESTBOOK_TABLE . " ORDER BY id DESC LIMIT " . $start . ", " . $nb_mess_guest."");
    $j = 0;
    while (list($id, $name, $comment, $email, $url, $date, $ip) = mysql_fetch_array($sql2))
    {
        $date = nkDate($date);

        $url = nkHtmlEntities($url);

        $url = nk_CSS($url);
        $email = nk_CSS($email);

        $comment = icon($comment);

        if (strlen($name) > 30)
        {
            $name = substr($name, 0, 30) . "...";
        }

        $name = nk_CSS($name);

        if ($j == 0)
        {
            $bg = $bgcolor2;
            $j++;
        }
        else
        {
            $bg = $bgcolor1;
            $j = 0;
        }

        if ($url != "")
        {
            $website = "<a class=\"nkButton icon alone small website\" href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\" title=\"" . $url . "\"></a>";
        }
        else
        {
            $website = "";
        }
        if ($email != "")
        {
            $usermail = "<a class=\"nkButton icon alone small mail\" href=\"mailto:" . $email . "\" title=\"" . $email . "\"></a>";
        }
        else
        {
            $usermail = "";
        }

        if ($visiteur >= admin_mod("Guestbook"))
        {
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

            $admin = "<a class=\"nkButton icon alone small edit\" href=\"index.php?file=Guestbook&amp;page=admin&amp;op=edit_book&amp;gid=" . $id . "\"></a>"
            . "<a class=\"nkButton icon alone small remove danger\" href=\"javascript:delmess('" . addslashes($name) . "', '" . $id . "');\"></a>";
        }
        else
        {
            $admin = "";
        }

        echo "<tr style=\"background: " . $bg . ";\"><td style=\"width: 30%;\" valign=\"top\"><b>" . $name . "</b>";

        if ($visiteur >= admin_mod("Guestbook"))
        {
            echo "<br />Ip : " . $ip;
        }

        echo "</td><td style=\"width: 70%;\"><img src=\"images/posticon.gif\" alt=\"\" /><small> " . _POSTED . " : " . $date . "</small>\n"
        . "<br /><br />" . $comment . "<br /><br /></td></tr>\n"
        . "<tr style=\"background: " . $bg . ";\"><td style=\"width: 30%;\">&nbsp;</td><td style=\"width: 70%;\"><div class=\"nkButton-group\">" . $usermail . $website . $admin . "</div></td></tr>\n";
    }

    if ($count == 0)
    {
        echo "<tr style=\"background: " . $bgcolor2 . ";\"><td align=\"center\" colspan=\"2\">" . _NOSIGN . "</td></tr>\n";
    }

    echo "</table>\n";

    if ($count > $nb_mess_guest)
    {
        number($count, $nb_mess_guest, "index.php?file=Guestbook");
    }

    echo "<br /><div style=\"text-align: center;\"><small><i>( " . _THEREIS . "&nbsp;" . $count . "&nbsp;" . _SIGNINDB . " )</i></small></div><br />\n";

    closetable();
}

switch ($_REQUEST['op'])
{
    case "post_book":
        post_book();
        break;

    case "send_book":
        send_book($_REQUEST['name'], $_REQUEST['email'], $_REQUEST['url'], $_REQUEST['comment']);
        break;

    default:
        index();
        break;
}

?>