<?php
/**
 * admin.php
 *
 * Backend of Wars module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Wars'))
    return;


function main(){
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function del_match(adversaire, id)\n"
            . "{\n"
            . "if (confirm('" . _DELETEMATCH . " '+adversaire+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Wars&page=admin&op=del_war&war_id='+id;}\n"
            . "}\n"
                . "\n"
            . "// -->\n"
            . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADMINMATCH . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Wars.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(1);

            echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _STATUS . "</b></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><b>" . _OPPONENT . "</b></td>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><b>" . _TEAM . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT warid, team, adversaire, url_adv, etat, date_jour, date_mois, date_an FROM " . WARS_TABLE . " ORDER BY etat, date_an DESC, date_mois DESC, date_jour DESC");
    $count = nkDB_numRows($sql);
    while (list($war_id, $team, $adv_name, $adv_url, $status, $jour, $mois, $an) = nkDB_fetchArray($sql)){
        $adv_name = printSecuTags($adv_name);

        if ($status > 0){
            $etat = _FINISH;
        }
        else{
            $etat = _HASTOPLAY;
        }

        if ($team > 0){
            $sql2 = nkDB_execute("SELECT titre FROM " . TEAM_TABLE . " WHERE cid = '" . $team . "'");
            list($team_name) = nkDB_fetchArray($sql2);
            $team_name = printSecuTags($team_name);
        }
        else{
            $team_name = __('NA');
        }

        if ($language == "french"){
            $date = $jour . "/" . $mois . "/" . $an;
        }
        else{
            $date = $mois . "/" . $jour . "/" . $an;
        }

        echo "<tr>\n"
                . "<td style=\"width: 15%;\" align=\"center\">" . $date . "</td>\n"
                . "<td style=\"width: 15%;\" align=\"center\">" . $etat . "</td>\n"
                . "<td style=\"width: 20%;\" align=\"center\">";

        if ($adv_url != ""){
            echo "<a href=\"" . $adv_url . "\" title=\"" . $adv_url . "\" onclick=\"window.open(this.href); return false;\">" . $adv_name . "</a>";
        }
        else{
            echo $adv_name;
        }

        echo "</td><td style=\"width: 20%;\" align=\"center\">" . $team_name . "</td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Wars&amp;page=admin&amp;op=match&amp;do=edit&amp;war_id=" . $war_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISMATCH . "\" /></a></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_match('" . addslashes($adv_name) . "', '" . $war_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISMATCH . "\" /></a></td></tr>\n";
    }

    if ($count == 0){
        echo "<tr><td colspan=\"6\" align=\"center\">" . _NOMATCH . "</td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>";
}

function match(){
    global $nuked, $user, $language;

    if(array_key_exists('war_id', $_REQUEST)){
        $war_id = (int) $_REQUEST['war_id'];
    }
    else{
        $war_id = '';
    }

    $status = $team = $game = $adv_name = $adv_url = $pays_adv = $type = $style = $jour = $mois = $an = $heure = $score_team = $score_adv = $tscore_team = $tscore_adv = $report = $url_league = '';

    if ($_REQUEST['do'] == "edit") {
        $sql = nkDB_execute("SELECT etat, team, game, adversaire, url_adv, pays_adv, image_adv, type, style, date_jour, date_mois, date_an, heure, map, score_team, score_adv, tscore_team, tscore_adv, report, url_league FROM " . WARS_TABLE . " WHERE warid='".$war_id."'");
        list($status, $team, $game, $adv_name, $adv_url, $pays_adv, $logo_adv, $type, $style, $jour, $mois, $an, $heure, $map, $score_team, $score_adv, $tscore_team, $tscore_adv, $report, $url_league) = nkDB_fetchArray($sql);
        $adv_name = nkHtmlSpecialChars($adv_name);
        $mapList = explode('|', $map);
        $score_team = explode('|', $score_team);
        $score_adv = explode('|', $score_adv);
        $nbr = count($mapList);
        $_REQUEST['game'] = (int) $game;
        $adminTitle = _EDITTHISMATCH;
    }

    if ($_REQUEST['do'] == "add"){
        $adminTitle = _ADDMATCH;
    }

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . $adminTitle . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Wars.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

    if ($_REQUEST['do'] == "add"){
        nkAdminMenu(2);
    }

    if ($_REQUEST['do'] == "edit"){
        $action = "do_edit&amp;war_id=" . $war_id;
    }
    else if ($_REQUEST['do'] == "add"){
        $action = "add_war";
        $mapList = array();

        if(array_key_exists('nbr', $_REQUEST)){
            $nbr = $_REQUEST['nbr'];
        }
        else{
            $nbr = 0;
        }
    }

    $checked1 = $checked2 = '';

    if ($status > 0){
        $etat = _FINISH;
        $checked1 = "selected=\"selected\"";
    }
    else{
        $etat = _HASTOPLAY;
        $checked2 = "selected=\"selected\"";
    }

    if ($_REQUEST['do'] == "add" && !isset($_REQUEST['nbr']) && !isset($_REQUEST['game']) && ! ctype_digit($_REQUEST['nbr'])){
        echo '<script type="text/javascript">
        function checkAddMatch(){
            if(! document.getElementById(\'matchNbMap\').value.match(/^\d+$/)){
                alert(\''. _NB_MAP_NO_INTEGER .'\');
                return false;
            }

            return true;
        }
        </script>';

        echo "<form onsubmit=\"return checkAddMatch()\" method=\"post\" action=\"index.php?file=Wars&amp;page=admin&amp;op=match&amp;do=add\">\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n"
                . "<tr><td align=\"center\"><b>" . _NOMBRE . " :</b><br/></td></tr>\n"
                . "<tr><td align=\"center\"><input id=\"matchNbMap\" type=\"text\" name=\"nbr\" maxlength=\"2\" size=\"10\" value=\"0\" /></td></tr>\n"
                . "<tr><td align=\"center\"><b>" . _GAME . " : </b><select name=\"game\">\n";

        $sql3 = nkDB_execute("SELECT id, name FROM " . GAMES_TABLE . " ORDER BY name");
        while (list($id, $name) = nkDB_fetchArray($sql3)){
            $name = printSecuTags($name);

            if ($id == $game){
                $checked4 = "selected=\"selected\"";
            }
            else{
                $checked4 = "";
            }

            echo "<option value=\"" . $id . "\" " . $checked4 . ">" . $name . "</option>\n";
        }

        echo "</select></table>\n"
                . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Wars&amp;page=admin\">" . __('BACK') . "</a></div>\n"
                . "</form><br /></div></div>\n";

        return;
    }

    echo '<script type="text/javascript">
    function checkAddMatch(){
        matchDay = ("0" + document.getElementById(\'matchDay\').value).slice(-2);
        matchMonth = ("0" + document.getElementById(\'matchMonth\').value).slice(-2);
        matchYear = document.getElementById(\'matchYear\').value;
        matchDate = matchMonth + \'-\' + matchDay + \'-\' + matchYear;

        if (matchDate != "" && ! checkDateValue(\'english\', matchDate, \'-\')){
            alert(\''. _BADDATE .'\');
            return false;
        }

        matchHour = document.getElementById(\'matchHour\').value;

        if (matchHour != "" && ! checkTimeValue(matchHour)){
            alert(\''. _BADTIME .'\');
            return false;
        }

        if(document.getElementById(\'matchOpponent\').value.length == 0){
            alert(\''. _NO_OPPONENT .'\');
            return false;
        }

        // TODO : Check type ?
        // TODO : Check style ?
        ';

    for ($i = 1; $i <= $nbr; $i++) {
            echo '
        if(! document.getElementById(\'matchTeamScore'. $i .'\').value.match(/^\d+$/)){
            alert(\''. sprintf(_TEAM_SCORE_NO_INTEGER, $i) .'\');
            return false;
        }
        if(! document.getElementById(\'matchOpponentScore'. $i .'\').value.match(/^\d+$/)){
            alert(\''. sprintf(_TEAM_OPPONENT_NO_INTEGER, $i) .'\');
            return false;
        }
        ';
    }

    echo '
        return true;
    }
    </script>';

    echo "<form onsubmit=\"return checkAddMatch()\" method=\"post\" action=\"index.php?file=Wars&amp;page=admin&amp;op=" . $action . "\" enctype=\"multipart/form-data\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n"
            . "<tr><td align=\"center\"><b>" . _STATUS . " :</b> <select name=\"etat\">\n"
            . "<option value=\"1\" " . $checked1 . ">" . _FINISH . "</option>\n"
            . "<option value=\"0\" " . $checked2 . ">" . _HASTOPLAY . "</option>\n"
            . "</select>&nbsp;&nbsp;<b>" . _TEAM . " : </b><select name=\"team\"><option value=\"\">" . __('NONE_TEAM') . "</option>\n";

    $sql2 = nkDB_execute("SELECT cid, titre FROM " . TEAM_TABLE . " ORDER BY ordre, titre");
    while (list($cid, $titre) = nkDB_fetchArray($sql2)){
        $titre = nkHtmlEntities($titre);

        if ($cid == $team){
            $checked3 = "selected=\"selected\"";
        }
        else{
            $checked3 = "";
        }

        echo "<option value=\"" . $cid . "\" " . $checked3 . ">" . $titre . "</option>\n";
    }

    echo "</select></td></tr><tr><td align=\"center\"><b>" . _DATE . " : </b><select id=\"matchDay\" name=\"jour\">\n";

    $day = 1;
    while ($day < 32){
        if ($day == date("d") && $_REQUEST['do'] == "add"){
            echo "<option value=\"$day\" selected=\"selected\">" . $day . "</option>\n";
        }
        else if ($day == $jour && $_REQUEST['do'] == "edit"){
            echo "<option value=\"" . $day . "\" selected=\"selected\">" . $day . "</option>\n";
        }
        else{
            echo "<option value=\"" . $day . "\">" . $day . "</option>\n";
        }
        $day++;
    }

    echo "</select>&nbsp;<select id=\"matchMonth\" name=\"mois\">";

    $month = 1;
    while ($month < 13){
        if ($month == date("m") && $_REQUEST['do'] == "add"){
            echo "<option value=\"" . $month . "\" selected=\"selected\">" . $month . "</option>\n";
        }
        else if ($month == $mois && $_REQUEST['do'] == "edit"){
            echo "<option value=\"" . $month . "\" selected=\"selected\">" . $month . "</option>\n";
        }
        else{
            echo "<option value=\"" . $month . "\">" . $month . "</option>\n";
        }
        $month++;
    }

    echo"</select>&nbsp;<select id=\"matchYear\" name=\"annee\">";

    if ($_REQUEST['do'] == "edit"){
        $prevprevprevyear = $an - 3;
        $prevprevyear = $an - 2;
        $prevyear = $an - 1;
        $year = $an;
        $nextyear = $an + 1;
        $nextnextyear = $an + 2;
    }
    else{
        $prevprevprevyear = date("Y") - 3;
        $prevprevyear = date("Y") - 2;
        $prevyear = date("Y") - 1;
        $year = date("Y");
        $nextyear = date("Y") + 1;
        $nextnextyear = date("Y") + 2;
    }

    echo "<option value=\"" . $prevprevprevyear . "\">" . $prevprevprevyear . "</option>\n"
            . "<option value=\"" . $prevprevyear . "\">" . $prevprevyear . "</option>\n"
            . "<option value=\"" . $prevyear . "\">" . $prevyear . "</option>\n"
            . "<option value=\"" . $year . "\" selected=\"selected\">" . $year . "</option>\n";

    if ($_REQUEST['do'] == "add"){
        $heure = date("H:i");
        $logo_adv = null;
    }

    echo "<option value=\"" . $nextyear . "\">" . $nextyear . "</option>\n"
            . "<option value=\"" . $nextnextyear . "\">" . $nextnextyear . "</option>\n"
            . "</select>&nbsp;&nbsp;<b>" . _HOUR . " :</b> <input id=\"matchHour\" type=\"text\" name=\"heure\" size=\"5\" maxlength=\"5\" value=\"" . $heure . "\" /></td></tr></table>\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\"><tr><td>&nbsp;</td></tr>\n"
            . "<tr><td align=\"center\"><h5>" . _OPPONENT . "</h5></td></tr>\n"
            . "<tr><td><b>" . _NAME . " : </b><input id=\"matchOpponent\" type=\"text\" name=\"adversaire\" maxlength=\"100\" size=\"20\" value=\"" . $adv_name . "\" />&nbsp;&nbsp;<b>" . _URL . " : </b><input type=\"text\" name=\"url_adv\" size=\"30\" maxlength=\"100\" value=\"" . $adv_url . "\" /></td></tr>\n"
            . "<tr><td><b>" . _LOGOADV . " :</b> <input type=\"text\" name=\"urlImage\" size=\"42\" value=\"" . $logo_adv . "\"/>\n";

            if ($logo_adv != "" && $_REQUEST['do'] == "edit"){
                echo "<img src=\"" . $logo_adv . "\" title=\"" . $adv_name . "\" style=\"margin-left:20px; width:60px; height:auto; vertical-align:middle;\" />\n";
            }

            echo "</td></tr>\n"
            . "<tr><td><b>" . _UPLOADIMAGE . " :</b> <input type=\"file\" name=\"upImage\" /></td></tr>\n"
            . "<tr><td><b>" . _COUNTRY . " : </b> <select name=\"country\">\n";

    if ($_REQUEST['do'] == "add" && $language == "french"){
        $pays_adv = "France.gif";
    }

    $rep = Array();
    $handle = @opendir("images/flags");
    while (false !== ($f = readdir($handle))){
        if ($f != ".." && $f != "." && $f != "index.html" && $f != "Thumbs.db"){
            $rep[] = $f;
        }
    }

    closedir($handle);
    sort ($rep);
    reset ($rep);

    while (list ($key, $filename) = each ($rep)){
        if ($filename == $pays_adv){
            $checked5 = "selected=\"selected\"";
        }
        else{
            $checked5 = "";
        }

        list ($country, $ext) = explode ('.', $filename);
        echo "<option value=\"" . $filename . "\" " . $checked5 . ">" . $country . "</option>\n";
    }

    echo "</select><input type=\"hidden\" name=\"game\" value=\"".$_REQUEST['game']."\"/>"
    . "<input type=\"hidden\" name=\"nbr\" value=\"" . (string) $nbr . "\" /></td></tr></table>\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\"><tr><td>&nbsp;</td></tr>\n"
    . "<tr><td align=\"center\"><h5>" . _MATCH . "</h5></td></tr>\n"
    . "<tr><td><b>" . _TYPE . " : </b><input type=\"text\" name=\"type\" maxlength=\"100\" size=\"20\" value=\"" . $type . "\" />&nbsp;&nbsp;<b>" . _STYLE . " : </b><input type=\"text\" name=\"style\" maxlength=\"100\" size=\"20\" value=\"" . $style . "\" /></td></tr>\n";

    $dbrGameMaps = nkDB_selectMany(
        'SELECT id, name
        FROM '. GAMES_MAP_TABLE .'
        WHERE game = '. nkDB_quote($_REQUEST['game']),
        array('name')
    );

    for ($i = 1; $i <= $nbr; $i++) {
        echo '<tr><td><b>Map n° '. $i .': </b></td></tr><tr><td><select name="map_'. $i .'">' ."\n";

        foreach ($dbrGameMaps as $map) {
            $map['name'] = printSecuTags($map['name']);

            if ($map['id'] == $mapList[$i - 1])
                $checked3 = ' selected="selected"';
            else
                $checked3 = '';

            if ($map['name'] != '')
                echo '<option value="'. $map['id'] .'"'. $checked3 .'>'. $map['name'] .'</option>' ."\n";
        }

        if (is_array($score_team)) {
            $scoreTeam = $score_team[$i - 1];
        }
        else {
            $scoreTeam = 0;
        }

        if (is_array($score_adv)) {
            $scoreAdv = $score_adv[$i - 1];
        }
        else {
            $scoreAdv =  0;
        }

        echo "</select>"
            . "</td></tr><tr><td><b>" . _OURSCORE . " : </b>"
            . "<input id=\"matchTeamScore". $i ."\" type=\"text\" name=\"score_team". $i ."\" maxlength=\"10\" size=\"5\" value=\"". $scoreTeam ."\" />"
            . "&nbsp;&nbsp;<b>" . _OPPSCORE . " : </b>"
            . "<input id=\"matchOpponentScore". $i ."\" type=\"text\" name=\"score_adv". $i ."\" maxlength=\"10\" size=\"5\" value=\"". $scoreAdv ."\" />"
            . "</td></tr>\n";
    }

    echo "</table>\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\"><tr><td>&nbsp;</td></tr>\n"
        . "<tr><td align=\"center\"><h5>" . _REPORT . "</h5></td></tr>\n"
        . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"report\" cols=\"60\" rows=\"10\">" . $report . "</textarea></td></tr>\n"
        . "<tr><td align=\"center\"><b>" . _URLREPORT . " :</b> <input type=\"text\" name=\"url_league\" size=\"35\" maxlength=\"200\" value=\"" . $url_league . "\" /></td></tr></table>\n";

    if ($_REQUEST['do'] == "edit"){
        $sql4 = nkDB_execute("SELECT id FROM " . WARS_FILES_TABLE . " WHERE module = 'Wars' AND im_id = '" . $war_id ."'");
        $nb_file = nkDB_numRows($sql4);

        if ($nb_file > 0) {
            echo "<div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"javascript:window.open('index.php?file=Wars&amp;page=admin&amp;op=main_file&amp;im_id=" . $war_id . "','popup','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=340,top=30,left=0');return(false)\">" . _ADDMODFILE . "</a></div>\n";
        } else {
            echo "<div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"javascript:window.open('index.php?file=Wars&amp;page=admin&amp;op=add_file&amp;im_id=" . $war_id ."','popup','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=340,top=30,left=0');return(false)\">" . _WADDFILE . "</a></div>\n";
        }
    }

    echo "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Wars&amp;page=admin\">" . __('BACK') . "</a></div>\n"
            . "</form><br /></div></div>\n";
}

function add_war($etat, $team, $game, $jour, $mois, $annee, $heure, $adversaire, $url_adv, $country, $type, $style, $report, $url_league){
    global $nuked, $user;

    require_once 'Includes/nkUpload.php';

    $hour = $minute = 0;

    if ($heure != '') {
        $timeArray = explode(':', $heure, 2);
        $hour      = (isset($timeArray[0])) ? (int) $timeArray[0] : null;
        $minute    = (isset($timeArray[1])) ? (int) $timeArray[1] : null;

        if ($hour === null || $minute === null || $hour > 24 || $hour < 0 || $minute > 60 || $minute < 0) {
            printNotification(_BADTIME, 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }
    }

    $mois = (int) $mois;
    $jour = (int) $jour;
    $annee = (int) $annee;

    if (mktime($hour, $minute, 0, $mois, $jour, $annee) === false) {
        printNotification(_BADDATE, 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    if ($adversaire == '' || ctype_space($adversaire)) {
        printNotification(stripslashes(_NO_OPPONENT), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    $autor = $user[2];
    $report = secu_html(nkHtmlEntityDecode($report));
    $adversaire = nkDB_realEscapeString(stripslashes($adversaire));
    $report = nkDB_realEscapeString(stripslashes($report));
    $type = nkDB_realEscapeString(stripslashes($type));
    $style = nkDB_realEscapeString(stripslashes($style));

    $mapList = $scoreTeamList = $scoreAdvList = array();
    $tscore_team = $tscore_adv = 0;
    $end = $_REQUEST['nbr'] + 1;

    for ($i = 1; $i < $end; $i++){
        $mapList[]       = $_REQUEST['map_'. $i];
        $scoreTeamList[] = str_replace('|', '&#124;', $_REQUEST['score_team'. $i]);
        $tscore_team    += (int) $_REQUEST['score_team'. $i];
        $scoreAdvList[]  = str_replace('|', '&#124;', $_REQUEST['score_adv'. $i]);
        $tscore_adv     += (int) $_REQUEST['score_adv'. $i];
    }

    $map        = implode('|', $mapList);
    $score_team = implode('|', $scoreTeamList);
    $score_adv  = implode('|', $scoreAdvList);

    if ($url_adv != "" && !preg_match("`http://`i", $url_adv)){
        $url_adv = "http://" . $url_adv;
    }

    if ($url_league != "" && !preg_match("`http://`i", $url_league)){
        $url_league = "http://" . $url_league;
    }

    //Upload du logo adv
    $imageUrl = '';

    $imageCfg = array(
        'allowedExtension'  => array('jpg', 'jpeg', 'png', 'gif'),
        'uploadDir'         => 'upload/Wars'
    );

    if ($_FILES['upImage']['name'] != '') {
        list($imageUrl, $uploadError, $imageExt) = nkUpload_check('upImage', $imageCfg);

        if ($uploadError !== false) {
            printNotification($uploadError, 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }
    }
    else if ($_POST['urlImage'] != '') {
        $ext = strtolower(substr(strrchr($_POST['urlImage'], '.'), 1));

        if (! in_array($ext, $imageCfg['allowedExtension'])) {
            printNotification(__('BAD_IMAGE_FORMAT'), 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }

        $imageUrl = stripslashes($_POST['urlImage']);
    }

    $etat = stripslashes($etat);
    $team = stripslashes($team);
    $game = stripslashes($game);
    $url_adv = stripslashes($url_adv);
    $country = stripslashes($country);
    $imageUrl = stripslashes($imageUrl);
    $type = stripslashes($type);
    $style = stripslashes($style);
    $map = stripslashes($map);
    $score_team = stripslashes($score_team);
    $score_adv = stripslashes($score_adv);
    $url_league = stripslashes($url_league);

    $etat = nkDB_realEscapeString($etat);
    $team = nkDB_realEscapeString($team);
    $game = nkDB_realEscapeString($game);
    $url_adv = nkDB_realEscapeString($url_adv);
    $country = nkDB_realEscapeString($country);
    $imageUrl = nkDB_realEscapeString($imageUrl);
    $type = nkDB_realEscapeString($type);
    $style = nkDB_realEscapeString($style);
    $map = nkDB_realEscapeString($map);
    $score_team = nkDB_realEscapeString($score_team);
    $score_adv = nkDB_realEscapeString($score_adv);
    $autor = nkDB_realEscapeString($autor);
    $url_league = nkDB_realEscapeString($url_league);

    nkDB_execute(
        "INSERT INTO ". WARS_TABLE ."
        (`etat`, `team`, `game`, `adversaire`, `url_adv`, `pays_adv`, `image_adv`, `type`, `style`, `date_jour`, `date_mois`, `date_an`, `heure`, `map`,  `score_team`, `score_adv`, `tscore_team`, `tscore_adv`, `report`, `auteur`, `url_league`, `dispo`, `pas_dispo`)
        VALUES
        ('". $etat ."', '". $team ."', '". $game ."', '". $adversaire ."', '". $url_adv ."', '". $country ."', '". $imageUrl ."', '". $type."', '". $style ."', '". $jour ."', '". $mois ."', '". $annee ."', '". $heure ."', '". $map ."', '". $score_team ."', '". $score_adv ."', '". $tscore_team ."', '". $tscore_adv ."', '". $report ."', '". $autor ."', '". $url_league ."', '', '')"
    );

    saveUserAction(_ACTIONADDWAR .'.');

    printNotification(_MATCHADD, 'success');
    setPreview('index.php?file=Wars', 'index.php?file=Wars&page=admin');
}

function del_war($war_id){
    global $nuked, $user;

    $war_id = (int) $war_id;

    nkDB_execute("DELETE FROM " . WARS_TABLE . " WHERE warid = '" . $war_id . "'");
    nkDB_execute("DELETE FROM " . COMMENT_TABLE . " WHERE im_id = '" . $war_id . "' AND module = 'Wars'");
    nkDB_execute("DELETE FROM " . WARS_FILES_TABLE . " WHERE im_id = '" . $war_id . "' AND module = 'Wars'");

    saveUserAction(_ACTIONDELWAR .'.');

    printNotification(_MATCHDEL, 'success');
    setPreview('index.php?file=Wars', 'index.php?file=Wars&page=admin');
}

function do_edit($war_id, $etat, $team, $game, $jour, $mois, $annee, $heure, $adversaire, $url_adv, $country, $type, $style, $report, $url_league){
    global $nuked, $user;

    require_once 'Includes/nkUpload.php';

    $war_id = (int) $war_id;

    $hour = $minute = 0;

    if ($heure != '') {
        $timeArray = explode(':', $heure, 2);
        $hour      = (isset($timeArray[0])) ? (int) $timeArray[0] : null;
        $minute    = (isset($timeArray[1])) ? (int) $timeArray[1] : null;

        if ($hour === null || $minute === null || $hour > 24 || $hour < 0 || $minute > 60 || $minute < 0) {
            printNotification(_BADTIME, 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }
    }

    $mois = (int) $mois;
    $jour = (int) $jour;
    $annee = (int) $annee;

    if (mktime($hour, $minute, 0, $mois, $jour, $annee) === false) {
        printNotification(_BADDATE, 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    if ($adversaire == '' || ctype_space($adversaire)) {
        printNotification(stripslashes(_NO_OPPONENT), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    $report = secu_html(nkHtmlEntityDecode($report));
    $adversaire = nkDB_realEscapeString(stripslashes($adversaire));
    $report = nkDB_realEscapeString(stripslashes($report));
    $type = nkDB_realEscapeString(stripslashes($type));
    $style = nkDB_realEscapeString(stripslashes($style));

    $mapList = $scoreTeamList = $scoreAdvList = array();
    $tscore_team = $tscore_adv = 0;
    $end = $_REQUEST['nbr'] + 1;

    for ($i = 1; $i < $end; $i++){
        $mapList[]       = $_REQUEST['map_'. $i];
        $scoreTeamList[] = str_replace('|', '&#124;', $_REQUEST['score_team'. $i]);
        $tscore_team    += (int) $_REQUEST['score_team'. $i];
        $scoreAdvList[]  = str_replace('|', '&#124;', $_REQUEST['score_adv'. $i]);
        $tscore_adv     += (int) $_REQUEST['score_adv'. $i];
    }

    $map        = implode('|', $mapList);
    $score_team = implode('|', $scoreTeamList);
    $score_adv  = implode('|', $scoreAdvList);

    if ($url_adv != "" && !preg_match("`http://`i", $url_adv)){
        $url_adv = "http://" . $url_adv;
    }

    if ($url_league != "" && !preg_match("`http://`i", $url_league)){
        $url_league = "http://" . $url_league;
    }

    //Upload du logo adv
    $imageUrl = '';

    $imageCfg = array(
        'allowedExtension'  => array('jpg', 'jpeg', 'png', 'gif'),
        'uploadDir'         => 'upload/Wars'
    );

    if ($_FILES['upImage']['name'] != '') {
        list($imageUrl, $uploadError, $imageExt) = nkUpload_check('upImage', $imageCfg);

        if ($uploadError !== false) {
            printNotification($uploadError, 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }
    }
    else if ($_POST['urlImage'] != '') {
        $ext = strtolower(substr(strrchr($_POST['urlImage'], '.'), 1));

        if (! in_array($ext, $imageCfg['allowedExtension'])) {
            printNotification(__('BAD_IMAGE_FORMAT'), 'error', array('backLinkUrl' => 'javascript:history.back()'));
            return;
        }

        $imageUrl = stripslashes($_POST['urlImage']);
    }

    $team = (int) $team;
    $game = (int) $game;
    $etat = nkDB_realEscapeString(stripslashes($etat));
    $url_adv = nkDB_realEscapeString(stripslashes($url_adv));
    $country = nkDB_realEscapeString(stripslashes($country));
    $imageUrl = nkDB_realEscapeString($imageUrl);
    $map = nkDB_realEscapeString(stripslashes($map));
    $score_team = nkDB_realEscapeString(stripslashes($score_team));
    $score_adv = nkDB_realEscapeString(stripslashes($score_adv));
    $url_league = nkDB_realEscapeString(stripslashes($url_league));

    nkDB_execute(
        "UPDATE ". WARS_TABLE ."
        SET etat = '" . $etat . "',
        team = '" . $team . "',
        game = '" . $game . "',
        adversaire = '" . $adversaire . "',
        url_adv = '" . $url_adv . "',
        pays_adv = '" . $country . "',
        image_adv = '" . $imageUrl . "',
        type = '" . $type . "',
        style = '" . $style . "',
        date_jour = '" . $jour . "',
        date_mois = '" . $mois . "',
        date_an = '" . $annee . "',
        heure = '" . $heure . "',
        map = '" . $map . "',
        score_team = '" . $score_team . "',
        score_adv = '" . $score_adv . "',
        tscore_team = '" . $tscore_team . "',
        tscore_adv = '" . $tscore_adv . "',
        report = '" . $report . "',
        url_league = '" . $url_league . "'
        WHERE warid = '" . $war_id . "'"
    );

    saveUserAction(_ACTIONMODIFWAR .'.');

    printNotification(_MATCHMODIF, 'success');
    setPreview('index.php?file=Wars', 'index.php?file=Wars&page=admin');
}

function main_file($im_id){
    global $bgcolor1, $bgcolor2, $bgcolor3;

    $im_id = (int) $im_id;

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_ADMINMATCH);

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function del_file(type, id)\n"
            . "{\n"
            . "if (confirm('" . _DEL . " '+type+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Wars&page=admin&op=del_file&fid='+id;}\n"
            . "}\n"
                . "\n"
            . "// -->\n"
            . "</script>\n";

    echo "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Wars&amp;page=admin&amp;op=add_file&amp;im_id=" . $im_id . "\"><b>" . _WADDFILE . "</b></a> ]<br /><br /></div>\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
            . "<tr style=\"background: ". $bgcolor3 . "\">\n"
            . "<td align=\"center\"><b>" . _TYPE . "</b></td>\n"
            . "<td align=\"center\"><b>" . _EDIT . "</b></td>\n"
            . "<td align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $j = 0;

    $sql = nkDB_execute("SELECT id, type, url FROM " . WARS_FILES_TABLE . " WHERE module = 'Wars' AND im_id = '" . $im_id . "'");
    while (list($fid, $type, $url) = nkDB_fetchArray($sql)){
        if ($type == "screen"){
            $typename = _IMG;
        }
        else if ($type == "demo"){
            $typename = _DEMO;
        }
        else{
            $typename = $type;
        }

        if ($j == 0){
            $bg = $bgcolor2;
            $j++;
        }
        else{
            $bg = $bgcolor1;
            $j = 0;
        }

        echo "<tr style=\"background: ". $bg . "\">\n"
                . "<td align=\"center\"><a href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\">" . $typename . "</a></td>\n"
                . "<td align=\"center\"><a href=\"index.php?file=Wars&amp;page=admin&amp;op=edit_file&amp;fid=" . $fid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITFILE . "\" /></a></td>\n"
                . "<td align=\"center\"><a href=\"javascript:del_file('" . addslashes($typename) . "', '" . $fid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELETEFILE . "\" /></a></td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"javascript:window.close()\"><b>" . __('CLOSE_WINDOW') . "</b></a></div>";
}

function add_file($im_id){
    $im_id = (int) $im_id;

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_ADMINMATCH);

    echo "<form method=\"post\" action=\"index.php?file=Wars&amp;page=admin&amp;op=send_file\" enctype=\"multipart/form-data\">\n"
        . "<div style=\"text-align: center;\"><br /><big><b>" . _WADDFILE . "</b></big></div>\n"
        . "<div><br /><b>" . _URL . " :</b> <input type=\"text\" size=\"40\" name=\"url_file\" /><br />\n"
        . "<br /><b>" . _WUPFILE . " :</b><br /><input type=\"file\" name=\"fichiernom\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" /> " . __('OVERWRITE') . "<br />\n"
        . "<b>" . _TYPE . " :</b> <select name=\"file_type\"><option value=\"screen\">" . _IMG . "</option><option value=\"demo\">" . _DEMO . "</option></select><br />\n"
        . "<input type=\"hidden\" name=\"im_id\" value=\"" . $im_id . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . __('SEND') . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"javascript:window.close()\"><b>" . __('CLOSE_WINDOW') . "</b></a></div></form>";
}

function edit_file($fid){
    $fid = (int) $fid;

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_ADMINMATCH);

    $sql = nkDB_execute("SELECT im_id, type, url FROM " . WARS_FILES_TABLE . " WHERE id = '" . $fid . "'");
    list($im_id, $type, $url) = nkDB_fetchArray($sql);

    $checked1 = $checked2 = '';

    if ($type == "screen"){
        $typename = _IMG;
        $checked1 = "selected=\"selected\"";

    }
    else if ($type == "demo"){
        $typename = _DEMO;
        $checked2 = "selected=\"selected\"";
    }
    else{
        $typename = $type;
    }

    echo "<form method=\"post\" action=\"index.php?file=Wars&amp;page=admin&amp;op=modif_file\" enctype=\"multipart/form-data\">\n"
        . "<div style=\"text-align: center;\"><br /><big><b>" . _WADDFILE . "</b></big></div>\n"
        . "<div><br /><b>" . _URL . " :</b> <input type=\"text\" size=\"40\" name=\"url_file\" value=\"" . $url . "\" /><br />\n"
        . "<br /><b>" . _WUPFILE . " :</b><br /><input type=\"file\" name=\"fichiernom\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" /> " . __('OVERWRITE') . "<br />\n"
        . "<b>" . _TYPE . " :</b> <select name=\"file_type\"><option value=\"screen\" " . $checked1 . ">" . _IMG . "</option><option value=\"demo\" " . $checked2 . ">" . _DEMO . "</option></select><br />\n"
        . "<input type=\"hidden\" name=\"im_id\" value=\"" . $im_id . "\" /><input type=\"hidden\" name=\"fid\" value=\"" . $fid . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . __('SEND') . "\" /></div>\n"
        . "<div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"javascript:window.close()\"><b>" . __('CLOSE_WINDOW') . "</b></a></div></form>";
}

function send_file($im_id, $file_type){
    $im_id = (int) $im_id;
    $fid = (int) $fid;

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_ADMINMATCH);

    require_once 'Includes/nkUpload.php';

    if ($_FILES['fichiernom']['name'] != '' || $_POST['url_file'] != '') {
        $fileCfg = array(
            'uploadDir' => 'upload/Wars'
        );

        if ($file_type == 'demo')
            $fileCfg['disallowedExtension'] = array('php', 'html');
        else
            $fileCfg['allowedExtension'] = array('jpg', 'jpeg', 'png', 'gif');

        if (isset($_POST['ecrase_screen']) && $_POST['ecrase_screen'] == 1)
            $fileCfg['overwrite'] = true;
        else
            $fileCfg['overwrite'] = false;

        $fileUrl = '';

        if ($_FILES['fichiernom']['name'] != '' && in_array($file_type, array('demo', 'screen'))) {
            list($fileUrl, $uploadError, $imageExt) = nkUpload_check('fichiernom', $fileCfg);

            if ($uploadError !== false) {
                if ($uploadError == __('FILE_ALREADY_EXIST'))
                    $uploadError .= '<br />'. __('REPLACE_FILE');

                printNotification($uploadError, 'error', array('backLinkUrl' => 'javascript:history.back()'));
                return;
            }
        }
        else if ($_POST['url_file'] != '') {
            $ext = strtolower(substr(strrchr($_POST['url_file'], '.'), 1));

            if (isset($fileCfg['allowedExtension']) && ! in_array($ext, $fileCfg['allowedExtension'])) {
                printNotification(__('BAD_IMAGE_FORMAT'), 'error', array('backLinkUrl' => 'javascript:history.back()'));
                return;
            }

            $fileUrl = stripslashes($_POST['url_file']);
        }

        $file_type = nkDB_realEscapeString(stripslashes($file_type));
        $fileUrl = nkDB_realEscapeString($fileUrl);

        nkDB_execute(
            "INSERT INTO ". WARS_FILES_TABLE ."
            (`module`, `im_id`, `type`, `url`)
            VALUES
            ('Wars', '". $im_id ."', '". $file_type ."', '". $fileUrl ."')"
        );

        printNotification(_WFILEADD, 'success');
        redirect('index.php?file=Wars&page=admin&op=main_file&im_id='. $im_id, 2);
    }
    else{
        printNotification(_WSPECIFY, 'error');
        redirect('index.php?file=Wars&page=admin&op=add_file&im_id='. $im_id, 3);
    }
}

function modif_file($im_id, $fid, $file_type){
    $im_id = (int) $im_id;
    $fid = (int) $fid;

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_ADMINMATCH);

    require_once 'Includes/nkUpload.php';

    if ($_FILES['fichiernom']['name'] != '' || $_POST['url_file'] != '') {
        $fileCfg = array(
            'uploadDir' => 'upload/Wars'
        );

        if ($file_type == 'demo')
            $fileCfg['disallowedExtension'] = array('php', 'html');
        else
            $fileCfg['allowedExtension'] = array('jpg', 'jpeg', 'png', 'gif');

        if (isset($_POST['ecrase_screen']) && $_POST['ecrase_screen'] == 1)
            $fileCfg['overwrite'] = true;
        else
            $fileCfg['overwrite'] = false;

        $fileUrl = '';

        if ($_FILES['fichiernom']['name'] != '' && in_array($file_type, array('demo', 'screen'))) {
            list($fileUrl, $uploadError, $imageExt) = nkUpload_check('fichiernom', $fileCfg);

            if ($uploadError !== false) {
                if ($uploadError == __('FILE_ALREADY_EXIST'))
                    $uploadError .= '<br />'. __('REPLACE_FILE');

                printNotification($uploadError, 'error', array('backLinkUrl' => 'javascript:history.back()'));
                return;
            }
        }
        else if ($_POST['url_file'] != '') {
            $ext = strtolower(substr(strrchr($_POST['url_file'], '.'), 1));

            if (isset($fileCfg['allowedExtension']) && ! in_array($ext, $fileCfg['allowedExtension'])) {
                printNotification(__('BAD_IMAGE_FORMAT'), 'error', array('backLinkUrl' => 'javascript:history.back()'));
                return;
            }

            $fileUrl = stripslashes($_POST['url_file']);
        }

        $file_type = nkDB_realEscapeString(stripslashes($file_type));
        $fileUrl = nkDB_realEscapeString($fileUrl);

        nkDB_execute(
            "UPDATE ". WARS_FILES_TABLE ."
            SET type = '" . $file_type . "',
            url = '" . $fileUrl . "'
            WHERE id = '" . $fid . "'"
        );

        printNotification(_FILEADD, 'success');
        redirect('index.php?file=Wars&page=admin&op=main_file&im_id='. $im_id, 2);
    }
    else{
        printNotification(_WSPECIFY, 'error');
        redirect('index.php?file=Wars&page=admin&op=add_file&im_id='. $im_id, 3);
    }
}

function del_file($fid){
    $fid = (int) $fid;

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_ADMINMATCH);

    $sql = nkDB_execute("SELECT im_id FROM " . WARS_FILES_TABLE . " WHERE id = '" . $fid . "'");
    list($im_id) = nkDB_fetchArray($sql);

    nkDB_execute("DELETE FROM " . WARS_FILES_TABLE . " WHERE id = '" . $fid . "'");

    printNotification(_WFILEDEL, 'success');
    redirect("index.php?file=Wars&page=admin&op=main_file&im_id=" . $im_id, 2);
}

function main_pref(){
    global $nuked, $language;

    echo '<script type="text/javascript">
    function checkMatchSetting(){
        if(! document.getElementById(\'nbMatch\').value.match(/^\d+$/)){
            alert(\''. _NB_MATCH_NO_INTEGER .'\');
            return false;
        }

        return true;
    }
    </script>';

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
            . "<div class=\"content-box-header\"><h3>" . _ADMINMATCH . "</h3>\n"
            . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Wars.php\" rel=\"modal\">\n"
            . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
            . "</div></div>\n"
            . "<div class=\"tab-content\" id=\"tab2\">\n";

            nkAdminMenu(3);

            echo "<form onsubmit=\"return checkMatchSetting()\" method=\"post\" action=\"index.php?file=Wars&amp;page=admin&amp;op=change_pref\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
            . "<tr><td>" . _NUMBERWARS . " :</td><td> <input id=\"nbMatch\" type=\"text\" name=\"max_wars\" size=\"2\" value=\"" . $nuked['max_wars'] . "\" /></td></tr>\n"
            . "</table><div style=\"text-align: center;\"></br><a href=\"index.php?file=Admin&amp;page=games\">"._MANAGETEAMMAP."</a><br/>\n"
            . "<br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Wars&amp;page=admin\">" . __('BACK') . "</a></div>\n"
            . "</form><br /></div></div>\n";
}

function change_pref($max_wars){
    global $nuked, $user;

    if ($max_wars == '' || ! ctype_digit($max_wars)) {
        printNotification(stripslashes(_NB_MATCH_NO_INTEGER), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    nkDB_execute("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_wars . "' WHERE name = 'max_wars'");

    saveUserAction(_ACTIONCONFWAR .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect("index.php?file=Wars&page=admin", 2);
}

function nkAdminMenu($tab = 1)
{
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Wars&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _MATCHES; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Wars&amp;page=admin&amp;op=match&amp;do=add">
                    <img src="modules/Admin/images/icons/snooker_ball.png" alt="icon" />
                    <span><?php echo _ADDMATCH; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Wars&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}

switch ($GLOBALS['op']){
    case "main":
        main();
        break;

    case "match":
        match();
        break;

    case "add_war":
        add_war($_REQUEST['etat'], $_REQUEST['team'], $_REQUEST['game'], $_REQUEST['jour'], $_REQUEST['mois'], $_REQUEST['annee'], $_REQUEST['heure'], $_REQUEST['adversaire'], $_REQUEST['url_adv'], $_REQUEST['country'], $_REQUEST['type'], $_REQUEST['style'], $_REQUEST['report'], $_REQUEST['url_league']);
        break;

    case "del_war":
        del_war($_REQUEST['war_id']);
        break;

    case "do_edit":
        do_edit($_REQUEST['war_id'], $_REQUEST['etat'], $_REQUEST['team'], $_REQUEST['game'], $_REQUEST['jour'], $_REQUEST['mois'], $_REQUEST['annee'], $_REQUEST['heure'], $_REQUEST['adversaire'], $_REQUEST['url_adv'], $_REQUEST['country'], $_REQUEST['type'], $_REQUEST['style'], $_REQUEST['report'], $_REQUEST['url_league']);
        break;

    case "main_file":
        main_file($_REQUEST['im_id']);
        break;

    case "add_file":
        add_file($_REQUEST['im_id']);
        break;

    case "edit_file":
        edit_file($_REQUEST['fid']);
        break;

    case "send_file":
        send_file($_REQUEST['im_id'], $_REQUEST['file_type']);
        break;

    case "modif_file":
        modif_file($_REQUEST['im_id'], $_REQUEST['fid'], $_REQUEST['file_type']);
        break;

    case "del_file":
        del_file($_REQUEST['fid']);
        break;

    case "main_pref":
        main_pref();
        break;

    case "change_pref":
        change_pref($_REQUEST['max_wars']);
        break;

    default:
        main();
        break;
}

?>
