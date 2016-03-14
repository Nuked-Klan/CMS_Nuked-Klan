<?php
/**
 * admin.php
 *
 * Backend of Defy module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Defy'))
    return;


function main(){
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _DEFY . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Defy.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        nkAdminMenu(1);

        echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _CLAN . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _GAME . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _MAIL . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT id, pseudo, send, mail, clan, game FROM " . DEFY_TABLE . " ORDER BY id DESC");
    $count = nkDB_numRows($sql);
    while (list($did, $pseudo, $date, $mail, $clan, $game) = nkDB_fetchArray($sql)){
        $date = nkDate($date);

        $sql2 = nkDB_execute("SELECT name FROM " . GAMES_TABLE . " WHERE id='" . $game . "'");
        list($game_name) = nkDB_fetchArray($sql2);
        $game_name = printSecuTags($game_name);

        echo "<tr>"
            . "<td style=\"width: 25%;\" align=\"center\"><a href=\"index.php?file=Defy&amp;page=admin&amp;op=view&amp;did=" . $did . "\">" . $pseudo . "</a></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $clan . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $game_name . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><a href=\"mailto:" . $mail . "\">" . $mail . "</a></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td></tr>\n";
    }

    if ($count == 0) {
        echo "<tr><td colspan=\"5\" align=\"center\">" . _NODEFY . "</td></tr>\n";
    }
    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function view($did) {
    global $nuked, $language;

    $did = (int) $did;

    echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function del_defie(pseudo, id)\n"
        . "{\n"
        . "if (confirm('" . _DDELETEMATCH . " '+pseudo+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=Defy&page=admin&op=del&did='+id;}\n"
        . "}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _DEFY . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Defy.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Defy&amp;page=admin&amp;op=transfert&amp;did=" . $did . "\">" . _TRANSFERT . "</a></div><br />\n"
        . "<table width=\"90%\" style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\"><tr><td>\n";

    $sql = nkDB_execute("SELECT pseudo, clan, mail, icq, irc, url, pays, date, heure, serveur, game, type, map, comment FROM " . DEFY_TABLE . " WHERE id = '" . $did . "'");
    list($pseudo, $clan, $mail, $icq, $irc, $url, $country, $date, $heure, $serveur, $game, $type, $map, $comment) = nkDB_fetchArray($sql);
    list ($pays, $ext) = explode ('.', $country);

    $sql2 = nkDB_execute("SELECT name FROM " . GAMES_TABLE . " WHERE id = '" . $game . "'");
    list($game_name) = nkDB_fetchArray($sql2);
    $game_name = printSecuTags($game_name);

    $defyDate = $defyHour = __('NA');

    if ($date) {
        $dateArray = explode('-', $date, 3);

        $day   = (isset($dateArray[0])) ? (int) $dateArray[0] : null;
        $month = (isset($dateArray[1])) ? (int) $dateArray[1] : null;
        $year = (isset($dateArray[2])) ? (int) $dateArray[2] : null;

        if ($day !== null && $month !== null && $year !== null) {
            if ($language == 'french')
                $defyDate = $day .'-'. $month .'-'. $year;
            else
                $defyDate = $month .'-'. $day .'-'. $year;
        }
    }

    if ($heure != '') {
        $timeArray = explode(':', $heure, 2);
        $hour      = (isset($timeArray[0])) ? (int) $timeArray[0] : null;
        $minute    = (isset($timeArray[1])) ? (int) $timeArray[1] : null;

        if (! ($hour === null || $minute === null || $hour > 24 || $hour < 0 || $minute > 60 || $minute < 0))
            $defyHour = $hour .':'. sprintf("%'.02d\n", $minute);
    }

    if (! $irc) $irc = __('NA');
    if (! $serveur) $serveur = __('NA');
    if (! $type) $type = __('NA');
    if (! $map) $map = __('NA');
    if (! $comment) $comment = __('NA');

    if ($url)
        $url = '<a href="'. $url .'" onclick="window.open(this.href); return false;">'. $url .'</a>';
    else
        $url = __('NA');

    echo "<b>" . _NICK . " : </b>" . $pseudo . "<br />\n"
        . "<b>" . _CLAN . " : </b>" . $clan . "<br />\n"
        . "<b>" . _MAIL . " : </b><a href=\"mailto:" . $mail . "\">" . $mail . "</a><br />\n"
        . "<b>" . _ICQMSN . " : </b>" . $icq . "<br />\n"
        . "<b>" . _CHANIRC . " : </b>" . $irc . "<br />\n"
        . "<b>" . _URL . " : </b>" . $url . "<br />\n"
        . "<b>" . _COUNTRY . " : </b>" . $pays . "<br />\n"
        . "<b>" . _DATE . " : </b>" . $defyDate . "<br />\n"
        . "<b>" . _DHOUR . " : </b>" . $defyHour . "<br />\n"
        . "<b>" . _SERVER . " : </b>" . $serveur . "<br />\n"
        . "<b>" . _GAME . " : </b>" . $game_name . "<br />\n"
        . "<b>" . _DMATCH . " : </b>" . $type . "<br />\n"
        . "<b>" . _DMAP . " : </b>" . $map . "<br /><br />\n"
        . "<b>" . _COMMENT . " : </b>" . $comment . "<br /><br />\n"
        . "</td></tr></table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _REMOVE . "\" onclick=\"javascript:del_defie('" . addslashes($pseudo) . "', '" . $did . "');\" /><a class=\"buttonLink\" href=\"index.php?file=Defy&amp;page=admin\">" . __('BACK') . "</a></div>\n"
        . "<br /></div></div>\n";
}

function del($did) {
    global $nuked, $user;

    $did = (int) $did;

    $sql = nkDB_execute("SELECT pseudo FROM " . DEFY_TABLE . " WHERE id = '" . $did . "'");
    list($pseudo) = nkDB_fetchArray($sql);

    $del = nkDB_execute("DELETE FROM " . DEFY_TABLE . " WHERE id = '" . $did . "'");

    saveUserAction(_ACTIONDELDEFY .' '. $pseudo);

    printNotification(_DEFIEDELETE, 'success');
    redirect('index.php?file=Defy&page=admin', 2);
}

function transfert($did) {
    global $nuked, $user;

    $did = (int) $did;

    $sql = nkDB_execute("SELECT pseudo, clan, url, pays, date, heure, game, type, map FROM " . DEFY_TABLE . " WHERE id = '" . $did . "'");
    list($pseudo, $clan, $url, $pays, $date, $heure, $game, $type, $map) = nkDB_fetchArray($sql);

    $date_jour = $date_mois = $date_an = '';

    if ($date) {
        $dateArray = explode('-', $date, 3);

        $date_jour   = (isset($dateArray[0])) ? (int) $dateArray[0] : '';
        $date_mois = (isset($dateArray[1])) ? (int) $dateArray[1] : '';
        $date_an = (isset($dateArray[2])) ? (int) $dateArray[2] : '';
    }

    $game      = nkDB_realEscapeString($game);
    $clan      = nkDB_realEscapeString($clan);
    $url       = nkDB_realEscapeString($url);
    $pays      = nkDB_realEscapeString($pays);
    $type      = nkDB_realEscapeString($type);
    $date_jour = nkDB_realEscapeString($date_jour);
    $date_mois = nkDB_realEscapeString($date_mois);
    $date_an   = nkDB_realEscapeString($date_an);
    $heure     = nkDB_realEscapeString($heure);
    $map       = nkDB_realEscapeString($map);
    $author    = nkDB_realEscapeString($user[2]);

    nkDB_execute(
        "INSERT INTO ". WARS_TABLE ."
        (`team`, `game`, `adversaire`, `url_adv`, `pays_adv`, `type`, `date_jour`, `date_mois`, `date_an`, `heure`, `map`, `tscore_team`, `tscore_adv`, `score_team`, `score_adv`, `report`, `auteur`, `url_league`, `dispo`, `pas_dispo`)
        VALUES
        ('0', '". $game ."', '". $clan ."', '". $url ."', '". $pays ."', '". $type ."', '". $date_jour ."', '". $date_mois ."', '". $date_an ."', '". $heure ."', '". $map ."', '', '', '', '', '', '". $author ."', '', '', '')");

    $warid = nkDB_insertId();

    nkDB_execute("DELETE FROM " . DEFY_TABLE . " WHERE id = '" . $did . "'");

    saveUserAction(_ACTIONTRANDEFY .' '. $pseudo);

    printNotification(_DEFIETRANSFERT, 'success');
    $url_redirect = 'index.php?file=Wars&page=admin&op=match&do=edit&war_id=' . $warid;
    redirect($url_redirect, 2);
}

function edit_pref() {
    global $nuked, $language;

    $charte = $nuked['defie_charte'];

    echo '<script type="text/javascript">
    function checkDefySetting(){
        if(document.getElementById(\'defyEmail\').value.length > 0 && ! isEmail(\'defyEmail\')){
            alert(\''. addslashes(__('BAD_EMAIL')) .'\');
            return false;
        }

        return true;
    }
    </script>';

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _DEFY . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Defy.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        nkAdminMenu(2);

        echo "<form onsubmit=\"return checkDefySetting()\" method=\"post\" action=\"index.php?file=Defy&amp;page=admin&amp;op=update_pref\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td><b>" . _MAILAVERT . "</b> : <input id=\"defyEmail\" type=\"text\" size=\"30\" name=\"defie_mail\" value=\"" . $nuked['defie_mail'] . "\" /></td></tr>\n"
        . "<tr><td><b>" . _INBOXAVERT . "</b> : <select name=\"defie_inbox\"><option value=\"\">" . _OFF . "</option>\n";

    $sql2 = nkDB_execute("SELECT id, pseudo FROM " . USER_TABLE . " WHERE niveau > 1 ORDER BY niveau DESC");
    $checked = '';
    while (list($id_user, $pseudo) = nkDB_fetchArray($sql2)) {
        if ($nuked['defie_inbox'] == $id_user) {
            $checked = "selected=\"selected\"";
        }
        echo "<option value=\"" . $id_user . "\" " . $checked . ">" . $pseudo . "</option>\n";
    }
    echo "</select></td></tr><tr><td>&nbsp;</td></tr>\n";

    echo "<tr><td><b>" . _CHARTE . "</b> : <br /><textarea id=\"defyCharter\" class=\"editor\" name=\"defie_charte\" cols=\"65\" rows=\"15\"\">" . $charte . "</textarea></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Defy&amp;page=admin\">" . __('BACK') . "</a></div>\n"
        . "</form><br /></div></div>\n";
}

function update_pref($defie_mail, $defie_inbox, $defie_charte) {
    global $nuked, $user;

    $defie_mail   = stripslashes($defie_mail);
    $defie_charte = stripslashes($defie_charte);
    $defie_inbox  = stripslashes($defie_inbox);

    if ($defie_mail && ($defie_mail = checkEmail($defie_mail, false, false)) === false) {
        printNotification(getCheckEmailError($mail), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    $defie_charte = secu_html(nkHtmlEntityDecode($defie_charte));

    $defie_charte = nkDB_realEscapeString($defie_charte);
    $defie_mail   = nkDB_realEscapeString($defie_mail);
    $defie_inbox  = nkDB_realEscapeString($defie_inbox);

    nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $defie_charte . "' WHERE name = 'defie_charte'");
    nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $defie_mail . "' WHERE name = 'defie_mail'");
    nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $defie_inbox . "' WHERE name = 'defie_inbox'");

    saveUserAction(_ACTIONPREFDEFY .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect('index.php?file=Defy&page=admin', 2);
}

function nkAdminMenu($tab = 1) {
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Defy&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _DEFY; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Defy&amp;page=admin&amp;op=edit_pref">
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
    case 'view':
    view($_REQUEST['did']);
    break;

    case 'del':
    del($_REQUEST['did']);
    break;

    case 'transfert':
    transfert($_REQUEST['did']);
    break;

    case 'edit_pref':
    edit_pref();
    break;

    case 'update_pref':
    update_pref($_REQUEST['defie_mail'], $_REQUEST['defie_inbox'], $_REQUEST['defie_charte']);
    break;

    default:
    main();
    break;
}

?>
