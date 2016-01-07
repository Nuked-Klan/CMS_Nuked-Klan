<?php
/**
 * admin.php
 *
 * Backend of Irc module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Irc'))
    return;


function main(){
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function del_irc(titre, id)\n"
            . "{\n"
            . "if (confirm('" . _AWARDSDELETE . " '+titre+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Irc&page=admin&op=del&irc_id='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADMINIRC . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Irc.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(1);

            echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr>\n"
            . "<td style=\"width: 30%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
            . "<td style=\"width: 40%;\" align=\"center\"><b>" . _TEXT . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = mysql_query("SELECT id, date, text FROM " . IRC_AWARDS_TABLE . " ORDER BY id DESC");
    $count = mysql_num_rows($sql);
    while (list($irc_id, $date, $text) = mysql_fetch_array($sql)){
        $date = nkDate($date);

        if (strlen($text) > 50){
            $texte = substr($text, 0, 50) . "...";
            $texte = nkHtmlEntities($texte);
        } 
        else{
            $texte = strip_tags($text);
        }

        echo "<tr>\n"
                . "<td style=\"width: 30%;\" align=\"center\">" . $date . "</td>\n"
                . "<td style=\"width: 40%;\">" . $texte . "</td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Irc&amp;page=admin&amp;op=edit&amp;irc_id=" . $irc_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISAWARD . "\" /></a></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_irc('" . $irc_id . "','" . $irc_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISAWARD . "\" /></a></td></tr>\n";

    } 

    if ($count == 0){
        echo "<tr><td colspan=\"4\" align=\"center\">" . _NOAWARD . "</td></tr>\n";
    }
    
    echo "</table><br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
} 

function add(){
    global $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADDAWARD . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Irc.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(2);

            echo  "<form method=\"post\" action=\"index.php?file=Irc&amp;page=admin&amp;op=do_add\">\n"
            . "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
            . "<tr><td align=\"center\"><b>" . _TEXT . "</b></td></tr>\n"
            . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"text\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _ADDTHISAWARD . "\" /><a class=\"buttonLink\" href=\"index.php?file=Irc&amp;page=admin\">" . __('BACK') . "</a></div>\n"
            . "</form><br /></div></div>\n";
} 

function do_add($text){
    global $nuked, $user;

    $date = time();
    $text = nkHtmlEntityDecode($text);
    $text = mysql_real_escape_string(stripslashes($text));

    $sql = mysql_query("INSERT INTO " . IRC_AWARDS_TABLE . " ( `id` , `text` , `date` ) VALUES ( '' , '" . $text . "' , '" . $date . "' )");

    saveUserAction(_ACTIONADDIRC .'.');

    printNotification(_AWARDADD, 'success');
    setPreview('index.php?file=Irc&op=awards', 'index.php?file=Irc&page=admin');
} 

function edit($irc_id){
    global $nuked, $language;

    $sql = mysql_query("SELECT text FROM " . IRC_AWARDS_TABLE . " WHERE id = '" . $irc_id . "'");
    list($text) = mysql_fetch_array($sql);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _EDITTHISAWARD . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Irc.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Irc&amp;page=admin&amp;op=do_edit\">\n"
            . "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
            . "<tr><td align=\"center\"><b>" . _TEXT . "</b></td></tr>\n"
            . "<tr><td align=\"center\"><textarea  class=\"editor\" name=\"text\" cols=\"60\" rows=\"10\">" . $text . "</textarea></td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input type=\"hidden\" name=\"irc_id\" value=\"" . $irc_id . "\" /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISAWARD . "\" /><a class=\"buttonLink\" href=\"index.php?file=Irc&amp;page=admin\">" . __('BACK') . "</a></div>\n"
            . "</form><br /></div></div>\n";
} 

function do_edit($irc_id, $text){
    global $nuked, $user;

    $text = secu_html(nkHtmlEntityDecode($text));
    $text = mysql_real_escape_string(stripslashes($text));

    $upd = mysql_query("UPDATE " . IRC_AWARDS_TABLE . " SET text = '" . $text . "' WHERE id = '" . $irc_id . "'");

    saveUserAction(_ACTIONMODIFIRC .'.');

    printNotification(_AWARDMODIF, 'success');
    setPreview('index.php?file=Irc&op=awards', 'index.php?file=Irc&page=admin');
} 

function del($irc_id){
    global $nuked, $user;

    $del = mysql_query("DELETE FROM " . IRC_AWARDS_TABLE . " WHERE id = '" . $irc_id . "'");

    saveUserAction(_ACTIONDELIRC .'.');

    printNotification(_AWARDDELETE, 'success');
    setPreview('index.php?file=Irc&op=awards', 'index.php?file=Irc&page=admin');
} 

function main_pref(){
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Irc.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(3);

            echo "<form method=\"post\" action=\"index.php?file=Irc&amp;page=admin&amp;op=change_pref\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"2\" cellpadding=\"3\">\n"
            . "<tr><td><b>" . _IRCHAN . " : #</b><input type=\"text\" name=\"irc_chan\" size=\"15\" value=\"" . $nuked['irc_chan'] . "\" /> <b>" . _IRCSERV . " :</b> <input type=\"text\" name=\"irc_serv\" size=\"20\" value=\"" . $nuked['irc_serv'] . "\" /></td></tr>\n"
            . "</table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Irc&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
} 

function change_pref($irc_chan, $irc_serv){
    global $nuked, $user;

    $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $irc_chan . "' WHERE name = 'irc_chan'");
    $upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $irc_serv . "' WHERE name = 'irc_serv'");

    saveUserAction(_ACTIONPREFIRC .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect("index.php?file=Irc&page=admin", 2);
} 

function nkAdminMenu($tab = 1)
{
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Irc&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _IRC; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Irc&amp;page=admin&amp;op=add">
                    <img src="modules/Admin/images/icons/ranks.png" alt="icon" />
                    <span><?php echo _ADDAWARD; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Irc&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($_REQUEST['op']) {
    case "add":
        add();
        break;

    case "del":
        del($_REQUEST['irc_id']);
        break;

    case "do_edit":
        do_edit($_REQUEST['irc_id'], $_REQUEST['text']);
        break;

    case "edit":
        edit($_REQUEST['irc_id']);
        break;

    case "do_add":
        do_add($_REQUEST['text']);
        break;

    case "main_pref":
        main_pref();
        break;

    case "change_pref":
        change_pref($_REQUEST['irc_chan'], $_REQUEST['irc_serv']);
        break;

    default:
        main();
        break;
}

?>