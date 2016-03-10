<?php
/**
 * admin.php
 *
 * Backend of Suggest module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Suggest'))
    return;


function main(){
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADMINSUGGEST . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Suggest.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(1);

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _SUGGESTID . "</b></td>\n"
    . "<td style=\"width: 30%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
    . "<td style=\"width: 30%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
    . "<td style=\"width: 30%;\" align=\"center\"><b>" . _DATE . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT id, module, date, user_id FROM " . SUGGEST_TABLE . " ORDER BY module, date");
    $count = mysql_num_rows($sql);
    
    while (list($sug_id, $mod_name, $date, $id_user) = nkDB_fetchArray($sql)){
        $date = nkDate($date);

        $sql2 = nkDB_execute("SELECT id, pseudo FROM " . USER_TABLE . " WHERE id = '" . $id_user . "'");
        $nb_user = mysql_num_rows($sql2);

        if ($nb_user > 0){
            list($user_for, $pseudo) = nkDB_fetchArray($sql2);
        }
        else{
            $pseudo = __('VISITOR') . ' (' . $id_user . ')';
            $user_for = 0;
        }

        echo "<tr>\n"
        . "<td style=\"width: 10%;\" align=\"center\">" . $sug_id . "</td>\n"
        . "<td style=\"width: 30%;\" align=\"center\"><a href=\"index.php?file=Suggest&amp;page=admin&amp;op=show_suggest&amp;sug_id=" . $sug_id . "\" title=\"" . _SEESUGGEST . "\">" . $mod_name . "</a></td>\n"
        . "<td style=\"width: 30%;\" align=\"center\">" . $pseudo . "</td>\n"
        . "<td style=\"width: 30%;\" align=\"center\">" . $date . "</td></tr>\n";
    }

    if ($count == 0){
        echo '<tr><td colspan="4" align="center">' . _NOSUGGEST . '</td></tr>';
    }

    echo "</table><br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function show_suggest($sug_id){
    global $nuked, $language;

    $sql = nkDB_execute("SELECT module, date, user_id, proposition FROM " . SUGGEST_TABLE . " WHERE id = '" . intval($sug_id) . "'");
    list($mod_name, $date, $id_user, $proposition) = nkDB_fetchArray($sql);
    $date = nkDate($date);
    $content = explode('|', $proposition);

    $sql2 = nkDB_execute("SELECT id, pseudo FROM " . USER_TABLE . " WHERE id = '" . $id_user . "'");
    $nb_user = mysql_num_rows($sql2);

    if ($nb_user > 0){
        list($user_for, $pseudo) = nkDB_fetchArray($sql2);
    }
    else{
        $pseudo = __('VISITOR') . " (" . $id_user . ")";
        $user_for = 0;
    }

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADMINSUGGEST . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Suggest.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _SUGGESTBY . "&nbsp;" . $pseudo . "&nbsp;" . _THE . "&nbsp;" . $date . "</div>\n";

    require("modules/Suggest/modules/" . $mod_name . ".php");
    form($content, $sug_id, $user_for);
    echo "</div></div>";
}

function valid_suggest($data){
    global $nuked, $user;

    require("modules/Suggest/modules/" . $_REQUEST['module'] . ".php");
    send($data);

    $del = nkDB_execute("DELETE FROM " . SUGGEST_TABLE . " WHERE id = '" . $data['sug_id'] . "'");

    saveUserAction(_ACTIONVALIDSUG .': '. $data['titre'] .'.');

    printNotification(_SUGGESTADD, 'success');
}

function del($sug_id){
    global $nuked, $user, $language;

    $sql = nkDB_execute("SELECT user_id, module, proposition FROM " . SUGGEST_TABLE . " WHERE id='" . intval($sug_id) . "' ");
    list($for, $module, $data) = nkDB_fetchArray($sql);

    if(!empty($module))
        include("modules/Suggest/modules/" . $module . ".php");

    $sql = nkDB_execute("DELETE FROM " . SUGGEST_TABLE . " WHERE id = '" . $sug_id . "'");

    saveUserAction(_ACTIONDELSUG .'.');

    //Envoi du MP de refus
    if ($for != "" && preg_match("`^[a-zA-Z0-9]+$`", $for) && isset($_REQUEST['subject']) && isset($_REQUEST['corps'])){
        $sql = nkDB_execute("INSERT INTO " . USERBOX_TABLE . " ( `mid` , `user_from` , `user_for` , `titre` , `message` , `date` , `status` ) VALUES ( '' , '" . $user[0] . "' , '" . $for . "' , '" . $_REQUEST['subject'] . "' , '" . $_REQUEST['corps'] . "' , '" . time() . "' , '0' )");
    }

    printNotification(_SUGGESTDEL, 'success');
    redirect("index.php?file=Suggest&page=admin", 2);
}

function raison($sug_id){
    global $nuked, $user;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADMINSUGGEST . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Suggest.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\"><table style=\"width:50%; border:0; align:center; valign:top; margin-left:auto; margin-right:auto\" cellspacing=\"1\" cellpadding=\"1\"><tr>\n"
            . "<td align=\"center\" colspan=\"2\"><form method=\"POST\" action=\"index.php?file=Suggest&amp;page=admin&amp;op=del&amp;sug_id=" . $sug_id . "\"><br><h3>"._RMOTIF."</h3></td></tr>\n"
            . "<tr><td align=\"left\"><b>"._RSUBJECT."</b> : </td><td><input type=\"text\" name=\"subject\" maxlength=\"100\" value=\"" . _REFUS2 . "\" size=\"45\"></td></tr>\n"
            . "<tr><td align=\"left\" valign=\"top\"><b>"._RCORPS."</b> : </td><td><textarea class=\"editor\" id=\"raison\" name=\"corps\" rows=\"10\" cols=\"39\" />" . _REFUS . "\n" . $user[2] . "</textarea></td></tr>\n"
            . "<tr><td colspan=\"2\"><p align=\"center\">&nbsp;<input class=\"button\" type=\"submit\" value=\"".__('SEND')."\"> <input class=\"button\" type=\"button\" value=\""._CANCEL."\" onclick=\"document.location.href='index.php?file=Suggest&page=admin&op=show_suggest&sug_id=" . $sug_id . "';\"></td></tr>\n"
            . "</form></table><br><center><a class=\"buttonLink\" href=\"index.php?file=Suggest&amp;page=admin\">".__('BACK')."</a></center><br></div></div>";
}

function main_pref(){
    global $nuked, $language;

    $checked = false;

    if ($nuked['suggest_avert'] == "on") $checked = true;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Suggest.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(2);

            echo "<form method=\"post\" action=\"index.php?file=Suggest&amp;page=admin&amp;op=change_pref\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr><td>" . _SUGGESTMAIL . " :\n";

            checkboxButton('suggest_avert', 'suggest_avert', $checked, false);

            echo "</td></tr>\n"
            . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" />\n"
            . "<a class=\"buttonLink\" href=\"index.php?file=Suggest&amp;page=admin\">" . __('BACK') . "</a></div></form><br /></div></div>";
}

function change_pref($suggest_avert){
    global $nuked, $user;

    if ($suggest_avert != 'on'){
        $suggest_avert = "off";
    }

    $upd = nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $suggest_avert . "' WHERE name = 'suggest_avert'");

    saveUserAction(_ACTIONCONFSUG .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect("index.php?file=Suggest&page=admin", 2);
}

function nkAdminMenu($tab = 1)
{
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Suggest&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _SUGGEST; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Suggest&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


echo "<script type=\"text/javascript\">\n"
. "<!--\n"
. "\n"
."function trim(string)\n"
."{"
."return string.replace(/(^\s*)|(\s*$)/g,'');"
."}\n"
. "// -->\n"
. "</script>\n";

switch ($GLOBALS['op']) {
    case"main":
    main();
    break;

    case"module":
    module($_REQUEST['mod_name']);
    break;

    case"show_suggest":
    show_suggest($_REQUEST['sug_id']);
    break;

    case"valid_suggest":
    valid_suggest($_REQUEST);
    break;

    case"del":
    del($_REQUEST['sug_id']);
    break;

    case"raison":
    raison($_REQUEST['sug_id']);
    break;

    case "main_pref":
    main_pref();
    break;

    case "change_pref":
    change_pref($_REQUEST['suggest_avert']);
    break;

    default:
    main();
    break;
}

?>