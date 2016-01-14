<?php
/**
 * admin.php
 *
 * Backend of Comment module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Comment'))
    return;


function edit_com($cid){
    global $nuked, $language;

    $sql = mysql_query("SELECT autor, autor_id, titre, comment, autor_ip FROM " . COMMENT_TABLE . " WHERE id = '" . $cid . "'");
    list($auteur, $autor_id, $titre, $texte, $ip) = mysql_fetch_array($sql);
    $auteur = nkHtmlSpecialChars($auteur);

    $titre = nkHtmlEntities($titre);

    if($autor_id != ""){
        $sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
        $test = mysql_num_rows($sql_member);
    }

    if($autor_id != "" && $test > 0){
        list($autor) = mysql_fetch_array($sql_member);
    }
    else{
        $autor = $auteur;
    }
    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _EDITTHISCOM . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Comment.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n"
            . "<form method=\"post\" action=\"index.php?file=Comment&amp;page=admin&amp;op=modif_com\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">\n"
            . "<tr><td><b>" . _NICK . " :</b> " . $autor . " ( " . $ip . " )</td></tr>\n"
            . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" value=\"" . $titre . "\" size=\"40\" /></td></tr>\n"
            . "<tr><td><b>" . _MESSAGE . " :</b></td></tr>\n"
            . "<tr><td><textarea class=\"editor\" name=\"texte\" cols=\"65\" rows=\"10\">" . $texte . "</textarea></td></tr>\n"
            . "<tr><td align=\"center\"><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" />\n"
            . "&nbsp;</td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" name=\"send\" value=\"" . _MODIF . "\" /><a class=\"buttonLink\" href=\"javascript:history.back();\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function modif_com($cid, $titre, $texte){
    global $nuked, $user;

    $texte = secu_html(nkHtmlEntityDecode($texte));
    $texte = mysql_real_escape_string(stripslashes($texte));
    $titre = mysql_real_escape_string(stripslashes($titre));

    $sql = mysql_query("UPDATE " . COMMENT_TABLE . " SET titre = '" . $titre . "', comment = '" . $texte . "' WHERE id = '" . $cid . "'");

    saveUserAction(_ACTIONMODIFCOM .'.');

    printNotification(_COMMENTMODIF, 'success');
    redirect("index.php?file=Comment&page=admin", 2);
}

function del_com($cid){
    global $nuked, $user;

    $sql = mysql_query("DELETE FROM " . COMMENT_TABLE . " WHERE id = '" . $cid . "'");

    saveUserAction(_ACTIONDELCOM .'.');

    printNotification(_COMMENTDEL, 'success');
    redirect("index.php?file=Comment&page=admin", 2);
}

function main(){
    global $nuked, $language;

    $nb_com = 30;

    $sql2 = mysql_query("SELECT id FROM " . COMMENT_TABLE);
    $count = mysql_num_rows($sql2);

    if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
    $start = $_REQUEST['p'] * $nb_com - $nb_com;

    echo "<script type=\"text/javascript\">\n"
            . "<!--\n"
            . "\n"
            . "function del_mess(pseudo, id)\n"
            . "{\n"
            . "if (confirm('" . _DELCOMMENT . " '+pseudo+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Comment&page=admin&op=del_com&cid='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADMINCOMMENT . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Comment.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(1);

    if ($count > $nb_com){
        echo "<table width=\"100%\"><tr><td>";
        number($count, $nb_com, "index.php?file=Comment&amp;page=admin");
        echo"</td></tr></table>\n";
    }

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr>\n"
            . "<td style=\"width: 25%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
            . "<td style=\"width: 25%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><b>" . _MODULE . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = mysql_query("SELECT id, im_id, date, autor, autor_id, module FROM " . COMMENT_TABLE . " ORDER BY id DESC LIMIT " . $start . ", " . $nb_com);
    while (list($id, $im_id, $date, $auteur, $autor_id, $module) = mysql_fetch_array($sql)){
        $date = nkDate($date);
        $auteur = nkHtmlSpecialChars($auteur);

        if($autor_id != ""){
            $sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
            $test = mysql_num_rows($sql_member);
        }

        if($autor_id != "" && $test > 0){
            list($autor) = mysql_fetch_array($sql_member);
        }
        else{
            $autor = $auteur;
        }

        echo "<tr><td style=\"width: 25%;\" align=\"center\">" . $date . "</td><td style=\"width: 25%;\" align=\"center\">" . $autor . "</td><td style=\"width: 20%;\"align=\"center\">" . $module . "</td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Comment&amp;page=admin&amp;op=edit_com&amp;cid=" . $id . "\" title=\"" . _EDITTHISCOM . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" /></a></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_mess('" . $autor . "', '" . $id . "');\" title=\"" . _DELTHISCOM . "\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" /></a></td></tr>\n";
    }

    if ($count == "0"){
        echo "<tr><td colspan=\"5\" align=\"center\">" . _NOCOMMENT . "</td></tr>\n";
    }

    echo "</table>";

    if ($count > $nb_com){
        echo "<table width=\"100%\"><tr><td>";
        number($count, $nb_com, "index.php?file=Comment&amp;page=admin");
        echo "</td></tr></table>";
    }
    echo "<div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function module_send_com($news, $download, $sections, $links, $wars, $gallery, $survey){
    global $nuked, $user;

    $sql1 = mysql_query("UPDATE " . COMMENT_MODULES_TABLE . " SET active = '" . $news . "' WHERE module = 'news'");
    $sql2 = mysql_query("UPDATE " . COMMENT_MODULES_TABLE . " SET active = '" . $download . "' WHERE module = 'download'");
    $sql3 = mysql_query("UPDATE " . COMMENT_MODULES_TABLE . " SET active = '" . $sections . "' WHERE module = 'sections'");
    $sql4 = mysql_query("UPDATE " . COMMENT_MODULES_TABLE . " SET active = '" . $links . "' WHERE module = 'links'");
    $sql5 = mysql_query("UPDATE " . COMMENT_MODULES_TABLE . " SET active = '" . $wars . "' WHERE module = 'wars'");
    $sql6 = mysql_query("UPDATE " . COMMENT_MODULES_TABLE . " SET active = '" . $gallery . "' WHERE module = 'gallery'");
    $sql7 = mysql_query("UPDATE " . COMMENT_MODULES_TABLE . " SET active = '" . $survey . "' WHERE module = 'survey'");

    saveUserAction(_ACTIONMODIFCOMMOD .'.');

    printNotification(_COMMENTMODIFMOD, 'success');
    redirect("index.php?file=Comment&page=admin&op=module_com", 2);
}

function module_com(){
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _COMMENTMOD . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Comment.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(2);

            echo "<form method=\"post\" action=\"index.php?file=Comment&amp;page=admin&amp;op=module_send_com\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">\n"
            . "<tr><td><b>" . _LISTI . " : </b></td><td></td></tr>\n";

    $sql = mysql_query("SELECT module, active FROM " . COMMENT_MODULES_TABLE);

    while(list($module, $active) = mysql_fetch_array($sql)){
    ?>
        <tr><td><b><?php echo $module; ?>:</b></td><td>
    <?php if( $active == 0){
    ?>
            <select name="<?php echo $module; ?>">
                <option value="0"><?php echo _DESACTIVER; ?></option>
                <option value="1"><?php echo _ACTIVER; ?></option>
            </select></td></tr>
    <?php
        }
        else{
    ?>
            <select name="<?php echo $module; ?>">
                <option value="1"><?php echo _ACTIVER; ?></option>
                <option value="0"><?php echo _DESACTIVER; ?></option>
            </select></td></tr>
    <?php
        }
    }	
    echo "<tr><td align=\"center\"><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" />\n"
            . "</td></tr></table>\n"
            . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" name=\"send\" value=\"" . _MODIF . "\" /><a class=\"buttonLink\" href=\"javascript:history.back();\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function nkAdminMenu($tab = 1) {
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Comment&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _COMMENTINDEX; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Comment&amp;page=admin&amp;op=module_com">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _COMMENTMOD; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($GLOBALS['op']) {
    case "edit_com":
        edit_com($_REQUEST['cid']);
        break;

    case "modif_com":
        modif_com($_REQUEST['cid'], $_REQUEST['titre'], $_REQUEST['texte']);
        break;

    case "del_com":
        del_com($_REQUEST['cid']);
        break;

    case "module_com":
        module_com();
        break;

    case "module_send_com":
        module_send_com($_REQUEST['news'],$_REQUEST['download'],$_REQUEST['sections'],$_REQUEST['links'],$_REQUEST['wars'],$_REQUEST['gallery'],$_REQUEST['survey']);
        break;

    default:
        main();
        break;
}

?>