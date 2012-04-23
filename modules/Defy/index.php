<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('<div style="text-align: center;">You cannot open this page directly</div>');

translate('modules/Defy/lang/' . $language . '.lang.php');

// Inclusion système Captcha
include_once 'Includes/nkCaptcha.php';

// On determine si le captcha est actif ou non
if (_NKCAPTCHA == 'off') $captcha = 0;
else if ((_NKCAPTCHA == 'auto' OR _NKCAPTCHA == 'on') && $user[1] > 0)  $captcha = 0;
else $captcha = 1;

opentable();

$visiteur = ($user) ? $user[1] : 0;
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1){
    compteur('Defy');

    function index(){
        global $nuked;

        if (!empty($nuked['defie_charte'])) {

            echo "<br /><table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
                    . "<tr><td align=\"center\"><big><b>" . _DEFY . "</b></big></td></tr>\n"
                    . "<tr><td>&nbsp;</td></tr><tr><td>" . $nuked['defie_charte'] . "</td></tr></table>\n"
                    . "<form method=\"post\" action=\"index.php?file=Defy\">\n"
                    . "<div style=\"text-align: center;\"><input type=\"hidden\" name=\"op\" value=\"form\" />\n"
                    . "<input type=\"submit\" value=\"" . _IAGREE . "\" />&nbsp;<input type=\"button\" value=\"" . _IDESAGREE . "\" onclick=\"javascript:history.back()\" /></div></form>\n";
        } else {
            form();
        }
    }

    function form(){
        global $nuked, $user, $language, $captcha;

        define('EDITOR_CHECK', 1);

        $date = date('d-m-Y');
        $hour = date('H:i');

        if (!empty($nuked['server_ip']) && !empty($nuked['server_port'])) {
            $server_ip = $nuked['server_ip'] . ':' . $nuked['server_port'];
        } else {
            $server_ip = null;
        }

        echo "<script type=\"text/javascript\">\n"
                ."<!--\n"
                ."\n"
                . "function verifchamps()\n"
                . "{\n"
                . "if (document.getElementById('defy_pseudo').value.length == 0)\n"
                . "{\n"
                . "alert('" . _NONICK . "');\n"
                . "return false;\n"
                . "}\n"
                . "\n"
                . "if (document.getElementById('defy_clan').value.length == 0)\n"
                . "{\n"
                . "alert('" . _NOCLAN . "');\n"
                . "return false;\n"
                . "}\n"
                ."\n"
                ."if (document.getElementById('defy_mail').value.indexOf('@') == -1)\n"
                ."{\n"
                ."alert('" . _BADMAIL . "');\n"
                ."return false;\n"
                ."}\n"
                ."\n"
                . "if (document.getElementById('defy_icq').value.length == 0)\n"
                . "{\n"
                . "alert('" . _NOICQ . "');\n"
                . "return false;\n"
                . "}\n"
                ."\n"
                . "return true;\n"
                . "}\n"
                ."\n"
                . "// -->\n"
                . "</script>\n";

        echo "<br /><form method=\"post\" action=\"index.php?file=Defy\" onsubmit=\"return verifchamps();\">\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
                . "<tr><td colspan=\"2\" align=\"center\"><big><b>" . _DEFY . "</b></big></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _NICK . " : </b></td><td><input id=\"defy_pseudo\" type=\"text\" name=\"pseudo\" value=\"" . $user[2] . "\" size=\"20\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _CLAN . " : </b></td><td><input id=\"defy_clan\" type=\"text\" name=\"clan\" size=\"20\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _COUNTRY . " : </b></td><td><select name=\"country\">\n";

        if ($language == 'french'){
            $pays = 'France.gif';
        }

        $rep = array();
        $handle = @opendir('images/flags');
        while (false !== ($f = readdir($handle))){
            if ($f != '..' && $f != '.' && $f != 'index.html' && $f != 'Thumbs.db'){
                $rep[] = $f;
            }
        }

        closedir($handle);
        sort($rep);
        reset($rep);
    
        while (list($key, $filename) = each($rep)) {
                if ($filename == $pays){
                    $checked = 'selected="selected"';
                }
                else{
                    $checked = null;
                }
    
                list ($country, $ext) = explode('.', $filename);
                echo "<option value=\"" . $filename . "\" " . $checked . ">" . $country . "</option>\n";
        }

        echo "</select></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _MAIL . " : </b></td><td><input id=\"defy_mail\" type=\"text\" name=\"mail\" size=\"25\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _ICQMSN . " : </b></td><td><input id=\"defy_icq\" type=\"text\" name=\"icq\" size=\"25\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _CHANIRC . " : </b></td><td><input type=\"text\" name=\"irc\" size=\"25\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _WEBSITE . " : </b></td><td><input type=\"text\" name=\"url\" value=\"http://\" size=\"30\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _DATE . " : </b></td><td><input type=\"text\" name=\"date\" value=\"" . $date . "\" size=\"15\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _HOUR . " : </b></td><td><input type=\"text\" name=\"heure\" value=\"" . $hour . "\" size=\"6\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _GAME . " : </b></td><td><select name=\"game\">\n";

        $sql = mysql_query('SELECT id, name FROM ' . GAMES_TABLE . ' ORDER BY name');
        while (list($game_id, $nom) = mysql_fetch_array($sql)){
            $nom = printSecuTags($nom);
            echo "<option value=\"" . $game_id . "\">" . $nom . "</option>\n";
        }

        echo "</select></td></tr><tr><td style=\"width: 20%;\"><b>" . _SERVER . " : </b></td><td><input type=\"text\" name=\"serveur\" value=\"" . $server_ip . "\" size=\"30\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _TYPE . " : </b></td><td><input type=\"text\" name=\"type\" value=\"\" size=\"20\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _MAP . " : </b></td><td><input type=\"text\" name=\"map\" value=\"\" size=\"20\" /></td></tr>\n"
                . "<tr><td style=\"width: 20%;\"><b>" . _COMMENT . " : </b></td><td><textarea id=\"e_basic\" name=\"comment\" cols=\"60\" rows=\"10\"></textarea></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n";

        if ($captcha == 1) create_captcha(2);

        echo "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"" . _SEND . "\" /><input type=\"hidden\" name=\"op\" value=\"send_defie\" /></td></tr></table></form><br />\n";
    }

    function send_defie($pseudo, $clan, $country, $mail, $icq, $irc, $url, $date, $heure, $game, $serveur, $type, $map, $comment){
        global $nuked, $captcha;

        // Verification code captcha
        if ($captcha == 1 && !ValidCaptchaCode($_POST['code_confirm'])){
            echo "<br /><br /><div style=\"text-align: center;\">" . _BADCODECONFIRM . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a><br /><br /></div>";
            closetable();
            footer();
            exit();
        }

        $email = $nuked['defie_mail'];
        $inbox = $nuked['defie_inbox'];
        $time = time();
        $date2 = nkDate($time);
        $comment = secu_html(html_entity_decode($comment));
        
        $pseudo = mysql_real_escape_string(stripslashes($pseudo));
        $clan = mysql_real_escape_string(stripslashes($clan));
        $country = mysql_real_escape_string(stripslashes($country));
        $mail = mysql_real_escape_string(stripslashes($mail));
        $icq = mysql_real_escape_string(stripslashes($icq));
        $irc = mysql_real_escape_string(stripslashes($irc));
        $url = mysql_real_escape_string(stripslashes($url));
        $date = mysql_real_escape_string(stripslashes($date));
        $heure = mysql_real_escape_string(stripslashes($heure));
        $game = mysql_real_escape_string(stripslashes($game));
        $serveur = mysql_real_escape_string(stripslashes($serveur));
        $type = mysql_real_escape_string(stripslashes($type));
        $map = mysql_real_escape_string(stripslashes($map));
        $comment = mysql_real_escape_string(stripslashes($comment));
        
        $pseudo = printSecuTags($pseudo);
        $clan = printSecuTags($clan);
        $country = htmlentities($country);
        $mail = htmlentities($mail);
        $icq = htmlentities($icq);
        $irc = htmlentities($irc);
        $url = htmlentities($url);
        $date = htmlentities($date);
        $heure = htmlentities($heure);
        $game = printSecuTags($game);
        $serveur = htmlentities($serveur);
        $type = printSecuTags($type);
        $map = printSecuTags($map);

        $sql = mysql_query("INSERT INTO " . DEFY_TABLE . " ( `id` , `send` , `pseudo` , `clan` , `mail` , `icq` , `irc` , `url` , `pays` , `date` , `heure` , `serveur` , `game` , `type` , `map` , `comment` ) VALUES ( '' , '" . $time . "' , '" . $pseudo . "' , '" . $clan . "' , '" . $mail . "' , '" . $icq . "' , '" . $irc . "' , '" . $url . "' , '" . $country . "' , '" . $date . "' , '" . $heure . "' , '" . $serveur . "' , '" . $game . "' , '" . $type . "' , '" . $map . "' , '" . $comment . "' )");

        $upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$time."', '1', '"._NOTDEF.": [<a href=\"index.php?file=Defy&page=admin\">lien</a>].')");
        $subject = _DEFY . ', ' .$date2;
        $corps = $pseudo . " " . _NEWDEFY . "\r\n" . $nuked['url'] . "/index.php?file=Defy&page=admin\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
        $from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $mail;

        $subject = @html_entity_decode($subject);
        $corps = @html_entity_decode($corps);
        $from = @html_entity_decode($from);

        if (!empty($email)){
            @mail($email, $subject, $corps, $from);
        }
        
        if (!empty($inbox)){
            $sql2 = mysql_query("INSERT INTO " . USERBOX_TABLE . " ( `mid` , `user_from` , `user_for` , `titre` , `message` , `date` , `status` ) VALUES ( '' , '" . $inbox . "' , '" . $inbox . "' , '" . $subject . "' , '" . $corps . "' , '" . $time . "' , '0' )");
        }

        echo '<br /><br /><div style="text-align: center;">' . _SENDMAIL . '</div><br /><br />';
        redirect('index.php', 2);
    }

    switch ($_REQUEST['op']){        
        case 'index':
        index();
        break;

        case 'form':
        form();
        break;

        case 'send_defie':
        send_defie($_REQUEST['pseudo'], $_REQUEST['clan'], $_REQUEST['country'], $_REQUEST['mail'], $_REQUEST['icq'], $_REQUEST['irc'], $_REQUEST['url'], $_REQUEST['date'], $_REQUEST['heure'], $_REQUEST['game'], $_REQUEST['serveur'], $_REQUEST['type'], $_REQUEST['map'], $_REQUEST['comment']);
        break;

        default:
        index();
        break;
    }

}
else if ($level_access == -1){
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
}
else if ($level_access == 1 && $visiteur == 0){
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b></div><br /><br />";
}
else{
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
}

closetable();
?>