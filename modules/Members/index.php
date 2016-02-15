<?php
/**
 * index.php
 *
 * Frontend of Members module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Members'))
    return;

compteur('Members');

require_once 'Includes/nkUserSocial.php';

function index(){
    global $bgcolor1, $bgcolor2, $bgcolor3, $theme, $nuked;

    $nb_membres = $nuked['max_members'];

    if (array_key_exists('letter', $_REQUEST) && $_REQUEST['letter'] == _OTHER){
        $and = "AND pseudo NOT REGEXP '^[a-zA-Z].'";
    }
    else if (array_key_exists('letter', $_REQUEST) && $_REQUEST['letter'] != "" && preg_match("`^[A-Z]+$`", $_REQUEST['letter'])){
        $and = "AND pseudo LIKE '" . $_REQUEST['letter'] . "%'";
    }
    else{
        $and = "";
    }

    $sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE team = '' AND niveau > 0 " . $and);
    $count = mysql_num_rows($sql2);

    if(array_key_exists('p', $_REQUEST)){
        $page = $_REQUEST['p'];
    }
    else{
        $page = 1;
    }
    $start = $page * $nb_membres - $nb_membres;

    opentable();

    echo "<br /><table width=\"100%\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">\n"
            . "<tr><td align=\"center\"><br /><big><b>" . _SITEMEMBERS . "</b></big><br /><br /></td></tr>\n";

    $alpha = array ("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "" . _OTHER . "");

    echo "<tr><td align=\"center\"><small>[ <a href=\"index.php?file=Members\">" . _ALL . "</a> | ";

    $num = count($alpha) - 1;
    $counter = 0;
    while (list(, $lettre) = each($alpha)){
        echo "<a href=\"index.php?file=Members&amp;letter=" . $lettre . "\">" . $lettre . "</a>";

        if ($counter == round($num / 2)){
            echo " ]<br />[ ";
        }
        else if ($counter != $num){
            echo " | ";
        }

        $counter++;
    }

    echo " ]</small><br /><br /></td></tr></table>";

    if ($count > $nb_membres){
        $url_members = "index.php?file=Members&amp;letter=" . $_REQUEST['letter'];
        number($count, $nb_membres, $url_members);
    }

    echo "<table id=\"nkMembersList\" style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
            . "<tr style=\"background: " . $bgcolor3 . ";\">\n"
            . "<td align=\"center\">&nbsp;</td>\n"
            . "<td align=\"center\"><b>" . _NICK . "</b></td>\n";

    $userSocialData = nkUserSocial_getConfig();

    foreach ($userSocialData as $userSocial) {
        echo '<td class="', $userSocial['cssClass'], '" align="center"><b>', nkUserSocial_getLabel($userSocial), '</b></td>', "\n";
    }

    echo "</tr>\n";

    $userSocialFields = nkUserSocial_getActiveFields();
    $userSocialFields = ($userSocialFields) ? ', '. implode(', ', $userSocialFields) : '';

    $dbrMember = nkDB_selectMany(
        'SELECT pseudo AS nickname, country'. $userSocialFields .'
        FROM '. USER_TABLE .'
        WHERE team = \'\' '. $and .' AND niveau > 0
        ORDER BY pseudo LIMIT '. $start .', '. $nb_membres
    );

    $j = 0;

    // TODO : Check while update if url with preg_match("`http://`i", $url)

    foreach ($dbrMember as $member) {
        list ($pays, $ext) = explode ('.', $member['country']);

        echo "<tr style=\"background: " . (($j++ % 2 == 1) ? $bgcolor1 : $bgcolor2) . ";\">\n"
            . "<td align=\"center\"><img src=\"images/flags/" . $member['country'] . "\" alt=\"\" title=\"" . $pays . "\" /></td>\n"
            . "<td><a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($member['nickname']) . "\" title=\"" . _VIEWPROFIL . "\"><b>" . $member['nickname'] . "</b></a></td>\n";

        foreach ($userSocialData as $userSocial) {
            echo '<td class="', $userSocial['cssClass'], '" align="center">'
                , nkUserSocial_formatImgLink($userSocial, $member)
                , '</td>', "\n";
        }

        echo "</tr>\n";
    }

    if ($count == 0) {
        echo "<tr><td colspan=\"". (2 + count($userSocialData)) ."\" align=\"center\">" . _NOMEMBERS . "</td></tr>\n";
    }

    echo "</table>";

    if ($count > $nb_membres){
        $url_members = "index.php?file=Members&amp;letter=" . $_REQUEST['letter'];
        number($count, $nb_membres, $url_members);
    }

    $date_install = nkDate($nuked['date_install']);

    if (array_key_exists('letter', $_REQUEST) && $_REQUEST['letter'] != ""){
        $_REQUEST['letter'] = nkHtmlEntities($_REQUEST['letter']);
        $_REQUEST['letter'] = nk_CSS($_REQUEST['letter']);

        echo "<br /><div style=\"text-align: center;\">" . $count . "&nbsp;" . _MEMBERSFOUND . " <b>" . $_REQUEST['letter'] . "</b></div><br />\n";
    }
    else{
        echo "<br /><div style=\"text-align: center;\">" . _THEREARE . "&nbsp;" . $count . "&nbsp;" . _MEMBERSREG . "&nbsp;" . $date_install . "<br />\n";

        if ($count > 0){
            $sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE team = '' ORDER BY date DESC LIMIT 0, 1");
            list($member) = mysql_fetch_array($sql_member);
            echo _LASTMEMBER . " <a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($member) . "\"><b>" . $member . "</b></a></div><br />\n";
        }
        else{
            echo "</div><br />\n";
        }
}

    closetable();
}

function detail($autor){
    global $nuked, $bgcolor1, $bgcolor2, $bgcolor3, $user, $visiteur;

    opentable();

    $autor = nkHtmlEntities($autor, ENT_QUOTES);

    $userSocialFields = nkUserSocial_getActiveFields();
    $userSocialFields = ($userSocialFields) ? ', U.'. implode(', U.', $userSocialFields) : '';

    // TODO : Check while update if url with preg_match("`http://`i", $url)

    $dbrMember = nkDB_selectOne(
        'SELECT U.id, U.date, U.game, U.country, S.last_used'. $userSocialFields .'
        FROM '. USER_TABLE .' AS U
        LEFT OUTER JOIN '. SESSIONS_TABLE .' AS S ON U.id = S.user_id
        WHERE U.pseudo = '. nkDB_escape($autor)
    );

    $test = nkDB_numRows();

    if ($test > 0){
        list ($pays, $ext) = explode ('.', $dbrMember['country']);

        $sql2 = mysql_query("SELECT prenom, age, sexe, ville, motherboard, cpu, ram, video, resolution, son, ecran, souris, clavier, connexion, system, photo, pref_1, pref_2, pref_3, pref_4, pref_5 FROM " . USER_DETAIL_TABLE . " WHERE user_id = '" . $dbrMember['id'] . "'");
        list($prenom, $birthday, $sexe, $ville, $motherboard, $cpu, $ram, $video, $resolution, $sons, $ecran, $souris, $clavier, $connexion, $osystem, $photo, $pref1, $pref2, $pref3, $pref4, $pref5) = mysql_fetch_array($sql2);

        $sql3 = mysql_query("SELECT titre, pref_1, pref_2, pref_3, pref_4, pref_5 FROM " . GAMES_TABLE . " WHERE id = '" . $dbrMember['game'] . "'");
        list($titre, $pref_1, $pref_2, $pref_3, $pref_4, $pref_5) = mysql_fetch_array($sql3);

        $dbrMember['date'] = nkDate($dbrMember['date']);
        $dbrMember['last_used'] = ($dbrMember['last_used'] > 0) ? nkDate($dbrMember['last_used']) : '';

        $titre = nkHtmlEntities($titre);
        $pref_1 = nkHtmlEntities($pref_1);
        $pref_2 = nkHtmlEntities($pref_2);
        $pref_3 = nkHtmlEntities($pref_3);
        $pref_4 = nkHtmlEntities($pref_4);
        $pref_5 = nkHtmlEntities($pref_5);

        if ($birthday != ""){
            list ($jour, $mois, $an) = explode ('/', $birthday);
            $age = date("Y") - $an;

            if (date("m") < $mois){
                $age = $age - 1;
            }

            if (date("d") < $jour && date("m") == $mois){
                $age = $age - 1;
            }
        }
        else{
            $age = "";
        }

        if ($sexe == "male"){
            $sex = _MALE;
        }
        else if ($sexe == "female"){
            $sex = _FEMALE;
        }
        else{
            $sex = "";
        }

        if ($visiteur == 9){
            echo "<div style=\"text-align: right;\"><a href=\"index.php?file=Admin&amp;page=user&amp;op=edit_user&amp;id_user=" . $dbrMember['id'] . "\"><img style=\"border: 0;\" src=\"images/edition.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a>";

            if ($dbrMember['id'] != $user[0]){
                echo "<script type=\"text/javascript\">\n"
                        ."<!--\n"
                        ."\n"
                        . "function deluser(pseudo, id)\n"
                        . "{\n"
                        . "if (confirm('" . _DELETEUSER . " '+pseudo+' ! " . _CONFIRM . "'))\n"
                        . "{document.location.href = 'index.php?file=Admin&page=user&op=del_user&id_user='+id;}\n"
                        . "}\n"
                        . "\n"
                        . "// -->\n"
                        . "</script>\n";

                echo "<a href=\"javascript:deluser('" . addslashes($autor) . "', '" . $dbrMember['id'] . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DELETE . "\" /></a>";
            }

        echo "&nbsp;</div>\n";
        }

        $a = "¿¡¬√ƒ≈‡·‚„‰Â“”‘’÷ÿÚÛÙıˆ¯»… ÀËÈÍÎ«ÁÃÕŒœÏÌÓÔŸ⁄€‹˘˙˚¸ˇ—Ò";
        $b = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
        $flash_autor = @nkHtmlEntityDecode($autor);
        $flash_autor = strtr($flash_autor, $a, $b);

        echo "<br /><object type=\"application/x-shockwave-flash\" data=\"modules/Members/images/title.swf\" width=\"100%\" height=\"50\">\n"
                . "<param name=\"movie\" value=\"modules/Members/images/title.swf\" />\n"
                . "<param name=\"pluginurl\" value=\"http://www.macromedia.com/go/getflashplayer\" />\n"
                . "<param name=\"wmode\" value=\"transparent\" />\n"
                . "<param name=\"menu\" value=\"false\" />\n"
                . "<param name=\"quality\" value=\"best\" />\n"
                . "<param name=\"scale\" value=\"exactfit\" />\n"
                . "<param name=\"flashvars\" value=\"text=" . $flash_autor . "\" /></object>\n";

        echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
                ."<tr style=\"background: " . $bgcolor3 . ";\"><td style=\"height: 20px\" colspan=\"2\" align=\"center\"><big><b>" . _INFOPERSO . "</b></big></td></tr>\n"
                ."<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"width: 100%\"><table cellpadding=\"1\" cellspacing=\"1\">\n"
                ."<tr><td><b>&nbsp;&nbsp;ª " . _NICK . "&nbsp;:&nbsp;</b></td><td><img src=\"images/flags/" . $dbrMember['country'] . "\" alt=\"" . $pays . "\" />&nbsp;" . $autor . "</td></tr>\n";

        if ($prenom) echo "<tr><td><b>&nbsp;&nbsp;ª " . _LASTNAME . "&nbsp;:&nbsp;</b></td><td>" . $prenom . "</td></tr>\n";
        if ($age) echo "<tr><td><b>&nbsp;&nbsp;ª " . _AGE . "&nbsp;:&nbsp;</b></td><td>" . $age . "</td></tr>\n";
        if ($sex) echo "<tr><td><b>&nbsp;&nbsp;ª " . _SEXE . "&nbsp;:&nbsp;</b></td><td>" . $sex . "</td></tr>\n";
        if ($ville) echo "<tr><td><b>&nbsp;&nbsp;ª " . _CITY . "&nbsp;:&nbsp;</b></td><td>" . $ville . "</td></tr>\n";
        if ($pays) echo "<tr><td><b>&nbsp;&nbsp;ª " . _COUNTRY . "&nbsp;:&nbsp;</b></td><td>" . $pays . "</td></tr>\n";

        if ($visiteur >= $nuked['user_social_level']) {
            foreach (nkUserSocial_getConfig() as $userSocial) {
                if (isset($dbrMember[$userSocial['field']]) && $dbrMember[$userSocial['field']] != '') {
                    echo '<tr><td><b>&nbsp;&nbsp;ª ', nkUserSocial_getLabel($userSocial), ' :</b></td><td><a href="'
                        , nkUserSocial_getLinkUrl($userSocial, $dbrMember[$userSocial['field']])
                        , '"', nkUserSocial_openUrlPage($userSocial), '>', $dbrMember[$userSocial['field']]
                        , '</a></td></tr>', "\n";
                }
            }
        }

        if ($dbrMember['date']) echo "<tr><td><b>&nbsp;&nbsp;ª " . _DATEUSER . "&nbsp;:&nbsp;</b></td><td>" . $dbrMember['date'] . "</td></tr>";
        if ($dbrMember['last_used']) echo "<tr><td><b>&nbsp;&nbsp;ª " . _LASTVISIT . "&nbsp;:&nbsp;</b></td><td>" . $dbrMember['last_used'] . "</td></tr>";

        echo "</table></td><td style=\"padding: 5px;\" align=\"right\">\n";

        if ($photo != ""){
            echo "<img style=\"border: 1px solid " . $bgcolor3 . "; background:" . $bgcolor1 . "; padding: 2px; overflow: auto; max-width: 100px;  width: expression(this.scrollWidth >= 100? '100px' : 'auto');\" src=\"" . checkimg($photo) . "\" alt=\"\" />";
        }
        else{
            echo "<img src=\"modules/Members/images/noAvatar.png\" width=\"100\" alt=\"\" style=\"border: 1px solid " . $bgcolor3 . "; background:" . $bgcolor1 . "; padding: 2px;\" />";
        }

        echo "</td></tr>\n";

        if ( $cpu || $ram || $motherboard || $video || $resolution || $sons || $souris || $clavier || $ecran || $osystem || $connexion ){
            echo "<tr style=\"background: " . $bgcolor3 . ";\"><td colspan=\"2\" style=\"height: 20px\" align=\"center\"><big><b>" . _HARDCONFIG . "</b></big></td></tr>\n"
                    ."<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"width: 100%\" colspan=\"2\"><table cellpadding=\"1\" cellspacing=\"1\">\n";

            if ($cpu) echo "<tr><td><b>&nbsp;&nbsp;ª " . _PROCESSOR . "&nbsp;:&nbsp;</b></td><td>" . $cpu . "</td></tr>\n";
            if ($ram) echo "<tr><td><b>&nbsp;&nbsp;ª " . _MEMORY . "&nbsp;:&nbsp;</b></td><td>" . $ram . "</td></tr>\n";
            if ($motherboard) echo "<tr><td><b>&nbsp;&nbsp;ª " . _MOTHERBOARD . "&nbsp;:&nbsp;</b></td><td>" . $motherboard . "</td></tr>\n";
            if ($video) echo "<tr><td><b>&nbsp;&nbsp;ª " . _VIDEOCARD . "&nbsp;:&nbsp;</b></td><td>" . $video . "</td></tr>\n";
            if ($resolution) echo "<tr><td><b>&nbsp;&nbsp;ª " . _RESOLUTION . "&nbsp;:&nbsp;</b></td><td>" . $resolution . "</td></tr>\n";
            if ($sons) echo "<tr><td><b>&nbsp;&nbsp;ª " . _SOUNDCARD . "&nbsp;:&nbsp;</b></td><td>" . $sons . "</td></tr>\n";
            if ($souris) echo "<tr><td><b>&nbsp;&nbsp;ª " . _MOUSE . "&nbsp;:&nbsp;</b></td><td>" . $souris . "</td></tr>\n";
            if ($clavier) echo "<tr><td><b>&nbsp;&nbsp;ª " . _KEYBOARD . "&nbsp;:&nbsp;</b></td><td>" . $clavier . "</td></tr>\n";
            if ($ecran) echo "<tr><td><b>&nbsp;&nbsp;ª " . _MONITOR . "&nbsp;:&nbsp;</b></td><td>" . $ecran . "</td></tr>\n";
            if ($osystem) echo "<tr><td><b>&nbsp;&nbsp;ª " . _SYSTEMOS . "&nbsp;:&nbsp;</b></td><td>" . $osystem . "</td></tr>\n";
            if ($connexion) echo "<tr><td><b>&nbsp;&nbsp;ª " . _CONNECT . "&nbsp;:&nbsp;</b></td><td>" . $connexion . "</td></tr>\n";

            echo "</table></td></tr>\n";
        }

        if ( $pref1 || $pref2 || $pref3 || $pref4 || $pref5 ){
            echo "<tr style=\"background: " . $bgcolor3 . ";\"><td colspan=\"2\" style=\"height: 20px\" align=\"center\"><big><b>" . $titre . " :</b></big></td></tr>\n";
            echo "<tr style=\"background: " . $bgcolor1 . ";\"><td colspan=\"2\"><table cellpadding=\"1\" cellspacing=\"1\">\n";

            if ($pref1) echo "<tr><td><b>&nbsp;&nbsp;ª " . $pref_1 . "&nbsp;:&nbsp;</b></td><td>" . $pref1 . "</td></tr>\n";
            if ($pref2) echo "<tr><td><b>&nbsp;&nbsp;ª " . $pref_2 . "&nbsp;:&nbsp;</b></td><td>" . $pref2 . "</td></tr>\n";
            if ($pref3) echo "<tr><td><b>&nbsp;&nbsp;ª " . $pref_3 . "&nbsp;:&nbsp;</b></td><td>" . $pref3 . "</td></tr>\n";
            if ($pref4) echo "<tr><td><b>&nbsp;&nbsp;ª " . $pref_4 . "&nbsp;:&nbsp;</b></td><td>" . $pref4 . "</td></tr>\n";
            if ($pref5) echo "<tr><td><b>&nbsp;&nbsp;ª " . $pref_5 . "&nbsp;:&nbsp;</b></td><td>" . $pref5 . "</td></tr>\n";

            echo "</table>";
        }

        echo "</td></tr></table><br />\n"
                ."<br /><div style=\"text-align: center;\">\n";

        if ($user){
            echo "&nbsp;[&nbsp;<a href=\"index.php?file=Userbox&amp;op=post_message&amp;for=" . $dbrMember['id'] . "\">" . _SENDPV . "</a>&nbsp;]&nbsp;\n";
        }

        echo "&nbsp;[&nbsp;<a href=\"index.php?file=Search&amp;op=mod_search&amp;autor=" . $autor . "\">" . _FINDSTUFF . "</a>&nbsp;]&nbsp;</div><br />\n";
    }
    else{
        printNotification(_NOMEMBER, 'error');
    }

    closetable();
}

function listing($q,$type='right',$limit=100){
    $q	= strtolower($q);
    $q = nk_CSS($q);
    $q = nkHtmlEntities($q, ENT_QUOTES);	
    if (!$q) return;

    if (!is_numeric($limit)) $limit = 0;
    if ($limit > 0) $str_limit = "LIMIT 0," . $limit;
    else $str_limit = '';

    if ($type=='full') $left = '%';
    else $left = '';

    $req_list = "SELECT pseudo FROM " . USER_TABLE . " WHERE lower(pseudo) like '" . $left . $q . "%' ORDER BY pseudo DESC " . $str_limit;
    $sql_list = mysql_query($req_list);

    while (list($pseudo) = mysql_fetch_array($sql_list)){
        $pseudo = str_replace('|','',$pseudo);
        echo $pseudo . "\n";
    }
}

switch ($GLOBALS['op']){
    case"index":
    index();
    break;

    case"detail":
    detail($_REQUEST['autor']);
    break;

    case"list":
    listing($_REQUEST['q'],$_REQUEST['type'],$_REQUEST['limit']);
    break;

    default:
    index();
}

?>