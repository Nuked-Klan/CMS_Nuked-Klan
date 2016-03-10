<?php
/**
 * index.php
 *
 * Frontend of User module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

global $language;

nkTemplate_moduleInit('User');

translate('modules/User/lang/'. $language .'.lang.php');
translate('modules/Members/lang/'. $language .'.lang.php');

include_once 'Includes/hash.php';


function index(){
    global $user, $nuked, $bgcolor1, $bgcolor2, $bgcolor3;

    if ($user){
        opentable();

        echo '<div style="text-align: center"><br /><big><b>' . _YOURACCOUNT . '</b></big><br /><br />',"\n"
                . _INFO . '<b> | ',"\n"
                . '<a href="index.php?file=User&amp;op=edit_account">' . _PROFIL . '</a> | ',"\n"
                . '<a href="index.php?file=User&amp;op=edit_pref">' . _PREF . '</a> | ',"\n"
                . '<a href="index.php?file=User&amp;op=change_theme">' . _THEMESELECT . '</a> | ',"\n"
                . '<a href="index.php?file=User&amp;op=logout">' . _USERLOGOUT . '</a></b></div><br />',"\n";

        $sql3 = nkDB_execute('SELECT U.pseudo, U.url, U.mail, U.date, U.avatar, U.count, S.last_used FROM ' . USER_TABLE . ' AS U LEFT OUTER JOIN ' . SESSIONS_TABLE . ' AS S ON U.id = S.user_id WHERE U.id = "' . $user[0] . '"');
        $user_data = mysql_fetch_array($sql3);

        $last_used = $user_data['last_used'] > 0 ? nkDate($user_data['last_used']) : 'N/A';
        $website = !$user_data['url'] ? 'N/A' : $user_data['url'];
        $avatar = !$user_data['avatar'] ? 'modules/User/images/noavatar.png' : checkimg($user_data['avatar']);

        echo '<table style="margin:auto; background:' . $bgcolor2 . '; border:1px solid ' . $bgcolor3 . '; width:75%;" cellpadding="0" cellspacing="1">',"\n"
                . '<tr style="background: '. $bgcolor3 . '"><td colspan="2" align="center" style="padding:2px"><b>' . _ACCOUNT . '</b></td></tr>',"\n"
                . '<tr style="background: '. $bgcolor1 . '"><td align="left" valign="middle" style="width:100%; background:' . $bgcolor1 . '">',"\n"
                . '<ul style="list-style-type:square;margin-left:25px">',"\n"
                . '<li><b>' . _NICK . ' :</b> ' . $user_data['pseudo'] . '</li>',"\n"
                . '<li><b>' . _WEBSITE . ' :</b> ' . $website . '</li>',"\n"
                . '<li><b>' . _MAIL . ' :</b> ' . $user_data['mail'] . '</li>',"\n"
                . '<li><b>' . _DATEUSER . ' : </b> ' . nkDate($user_data['date'], TRUE) . '</li>',"\n"
                . '<li><b>' . __('LAST_VISIT') . ' : </b> ' . $last_used . '</li>',"\n"
                . '</ul>',"\n"
                . '</td>',"\n"
                . '<td align="right" valign="middle" style="padding:5px; background:' . $bgcolor2 . '">',"\n"
                . '<img style="border: 0; overflow: auto; max-width: 100px; width: expression(this.scrollWidth >= 100? \'100px\' : \'auto\');" src="' . $avatar . '" alt="" />',"\n"
                . '</td></tr></table><br />',"\n"
                . '<table style="margin: auto;text-align: left;background: ' . $bgcolor2 . ';border: 1px solid ' . $bgcolor3 . '" width="75%" cellpadding="2" cellspacing="1">',"\n"
                . '<tr style="background: '. $bgcolor3 . '"><td align="center"><b>' . _MESSPV . '</b></td></tr>',"\n";

        $sql2 = nkDB_execute('SELECT mid FROM ' . USERBOX_TABLE . ' WHERE user_for = "' . $user[0] . '" AND status = 1');
        $nb_mess_lu = mysql_num_rows($sql2);

        $msg_not_read = ($user[5] > 0) ? '<a href="index.php?file=Userbox"><b>' . $user[5] . '</b></a>' : '<b>' . $user[5] . '</b>';

        echo '<tr style="background: ' . $bgcolor2 . '"><td>' . _NOTREAD . ' : ' . $msg_not_read . '</td></tr>',"\n";

        $nb_mess_lu = ($nb_mess_lu > 0) ? '<a href="index.php?file=Userbox"><b>' . $nb_mess_lu . '</b></a>' : '<b>' . $nb_mess_lu . '</b>';

        echo '<tr style="background: ' . $bgcolor1 . '"><td>' . _READ . ' : ' . $nb_mess_lu . '</td></tr>',"\n";

        echo '<tr style="background: ' . $bgcolor3 . '"><td align="center">',"\n"
                . '<input type="button" value="' . _READPV . '" onclick="document.location=\'index.php?file=Userbox\'" />&nbsp;',"\n"
                . '<input type="button" value="' . _REQUESTPV . '" onclick="document.location=\'index.php?file=Userbox&amp;op=post_message\'" />',"\n"
                . '</td></tr></table><br /><div style="text-align: center"><big>' . _YOURSTATS . '</big></div>',"\n"
                . '<table style="margin: auto;text-align: left;background: ' . $bgcolor2 . ';border: 1px solid ' . $bgcolor3 . '" width="75%" cellpadding="2" cellspacing="1">',"\n"
                . '<tr style="background: '. $bgcolor3 . '"><td align="center"><b>' . _NAME . '</b></td><td align="center"><b>' . _COUNT . '</b></td></tr>',"\n";

        $sql4 = nkDB_execute("SELECT id FROM " . COMMENT_TABLE . " WHERE autor_id = '" . $user[0] . "'");
        $nb_comment = mysql_num_rows($sql4);

        $sql5 = nkDB_execute("SELECT id FROM " . SUGGEST_TABLE . " WHERE user_id = '" . $user[0] . "'");
        $nb_suggest = mysql_num_rows($sql5);

        echo "<tr style=\"background: ". $bgcolor2 . "\"><td>" . _MESSINFORUM . "</td><td align=\"center\">" . $user_data['count'] . "</td></tr>\n"
                . "<tr style=\"background: ". $bgcolor1 . "\"><td>" . _USERCOMMENT . "</td><td align=\"center\">" . $nb_comment . "</td></tr>\n"
                . "<tr style=\"background: ". $bgcolor2 . "\"><td>" . _USERSUGGEST . "</td><td align=\"center\">" . $nb_suggest . "</td></tr>\n"
                . "</table><br /><div style=\"text-align: center;\"><big>" . _LASTUSERMESS . "</big></div>\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"75%\" cellpadding=\"2\" cellspacing=\"1\">\n"
                . "<tr style=\"background: ". $bgcolor3 . "\">\n"
                . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
                . "<td style=\"width: 50%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
                . "<td style=\"width: 40%;\" align=\"center\"><b>" . _DATE . "</b></td></tr>\n";

        if ($user_data['count'] == 0){
            echo "<tr><td align=\"center\" colspan=\"3\">" . _NOUSERMESS . "</td></tr>\n";
        }
        else{
            $iforum = 0;
            $sql_forum = nkDB_execute("SELECT id, titre, date, thread_id, forum_id FROM " . FORUM_MESSAGES_TABLE . " WHERE auteur_id = '" . $user[0] . "' ORDER BY id DESC LIMIT 0, 10");
            $j = 0;
            while (list($mid, $subject, $date, $tid, $fid) = mysql_fetch_array($sql_forum)){
                $subject = nkHtmlEntities($subject);
                $subject = nk_CSS($subject);
                $date = nkDate($date);

                $iforum++;

                if ($j == 0){
                    $bg = $bgcolor2;
                    $j++;
                }
                else{
                    $bg = $bgcolor1;
                    $j = 0;
                }

                $sql_page = nkDB_execute("SELECT id FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $tid . "'");
                $nb_rep = mysql_num_rows($sql_page);

                if ($nb_rep > $nuked['mess_forum_page']){
                    $topicpages = $nb_rep / $nuked['mess_forum_page'];
                    $topicpages = ceil($topicpages);
                    $link_REQUEST = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $fid . "&amp;thread_id=" . $tid . "&amp;p=" . $topicpages . "#" . $mid;
                }
                else{
                    $link_REQUEST = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $fid . "&amp;thread_id=" . $tid . "#" . $mid;
                }

                echo "<tr style=\"background: ". $bg . "\">\n"
                        . "<td style=\"width: 10%;\" align=\"center\">" . $iforum . "</td>\n"
                        . "<td style=\"width: 50%;\"><a href=\"" . $link_REQUEST . "\">" . $subject . "</a></td>\n"
                        . "<td style=\"width: 40%;\" align=\"center\">" . $date . "</td></tr>\n";
            }

            if ($iforum == 0){
                echo "<tr><td align=\"center\" colspan=\"3\">" . _NOUSERMESS . "</td></tr>\n";
            }
        }

        echo "</table><br /><div style=\"text-align: center;\"><big>" . _LASTUSERCOMMENT . "</big></div>\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"75%\" cellpadding=\"2\" cellspacing=\"1\">\n"
                . "<tr style=\"background: ". $bgcolor3 . "\">\n"
                . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
                . "<td style=\"width: 50%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
                . "<td style=\"width: 40%;\" align=\"center\"><b>" . _DATE . "</b></td></tr>\n";

        if ($nb_comment == 0){
            echo "<tr><td align=\"center\" colspan=\"3\">" . _NOUSERCOMMENT . "</td></tr>\n";
        }
        else{
            $icom = 0;
            $sql_com = nkDB_execute("SELECT im_id, titre, module, date FROM " . COMMENT_TABLE . " WHERE autor_id = '" . $user[0] . "' ORDER BY id DESC LIMIT 0, 10");
            while (list($im_id, $titre, $module, $date) = mysql_fetch_array($sql_com)){
                $titre = nkHtmlEntities($titre);
                $titre = nk_CSS($titre);

                if ($titre != ""){
                    $title = $titre;
                }
                else{
                    $title = $module;
                }

                $date = nkDate($date);

                $icom++;

                if ($j1 == 0){
                    $bg1 = $bgcolor2;
                    $j1++;
                }
                else{
                    $bg1 = $bgcolor1;
                    $j1 = 0;
                }

                if ($module == "news"){
                    $link_title = "<a href=\"index.php?file=News&amp;op=index_comment&amp;news_id=" . $im_id . "\">" . $title . "</a>";
                }
                else if ($module == "Gallery"){
                    $link_title = "<a href=\"index.php?file=Gallery&amp;op=description&amp;sid=" . $im_id . "\">" . $title . "</a>";
                }
                else if ($module == "Wars"){
                    $link_title = "<a href=\"index.php?file=Wars&amp;op=detail&amp;war_id=" . $im_id . "\">" . $title . "</a>";
                }
                else if ($module == "Links"){
                    $link_title = "<a href=\"index.php?file=Links&amp;op=description&amp;link_id=" . $im_id . "\">" . $title . "</a>";
                }
                else if ($module == "Download"){
                    $link_title = "<a href=\"index.php?file=Download&amp;op=description&amp;dl_id=" . $im_id . "\">" . $title . "</a>";
                }
                else if ($module == "Survey"){
                    $link_title = "<a href=\"index.php?file=Survey&amp;op=affich_res&amp;sid=" . $im_id . "\">" . $title . "</a>";
                }
                else if ($module == "Sections"){
                    $link_title = "<a href=\"index.php?file=Sections&amp;op=article&amp;artid=" . $im_id . "\">" . $title . "</a>";
                }

                echo "<tr style=\"background: ". $bg1 . "\">\n"
                        . "<td style=\"width: 10%;\" align=\"center\">" . $icom . "</td>\n"
                        . "<td style=\"width: 50%;\">" . $link_title . "</td>\n"
                        . "<td style=\"width: 40%;\" align=\"center\">" . $date . "</td></tr>\n";
            }
        }

        echo "</table><br />\n";

        closetable();
    }
    else{
        redirect("index.php?file=User&op=login_screen");
    }
}

function reg_screen(){
    global $nuked, $user, $language;

    if ($user){
        redirect("index.php?file=User&op=edit_account");
    }

    if ($nuked['inscription'] != "off"){
        if ($nuked['inscription_charte'] != "" && !isset($_REQUEST['charte_agree'])){
            $disclaimer = nkHtmlEntityDecode($nuked['inscription_charte']);

            echo "<br /><table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
                    . "<tr><td align=\"center\"><big><b>" . _NEWUSERREGISTRATION . "</b></big></td></tr>\n"
                    . "<tr><td>&nbsp;</td></tr><tr><td>" . $disclaimer . "</td></tr></table>\n"
                    . "<form method=\"post\" action=\"index.php?file=User&amp;op=reg_screen\">\n"
                    . "<div style=\"text-align: center;\"><input type=\"hidden\" name=\"charte_agree\" value=\"1\" />\n"
                    . "<input type=\"submit\" value=\"" . _IAGREE . "\" />&nbsp;<input type=\"button\" value=\"" . _IDESAGREE . "\" onclick=\"javascript:history.back()\" /></div></form><br />\n";
        }
        else{
            echo "<script type=\"text/javascript\">\n"
                    ."<!--\n"
                    . "\n"
                    ."function trim(string)\n"
                    ."{"
                    ."return string.replace(/(^\s*)|(\s*$)/g,'');"
                    ."}\n"
                    ."\n"
                    . "function verifchamps()\n"
                    . "{\n"
                    . "pseudo = trim(document.getElementById('reg_pseudo').value);\n"
                    ."\n"
                    . "if (pseudo.length < 3)\n"
                    . "{\n"
                    . "alert('" . _3TYPEMIN . "');\n"
                    . "return false;\n"
                    . "}\n";

            if ($nuked['inscription'] != "mail"){
                echo "\n"
                        . "pass = trim(document.getElementById('reg_pass').value);\n"
                        . "if (pass.length < 4)\n"
                        . "{\n"
                        . "alert('" . _4TYPEMIN . "');\n"
                        . "return false;\n"
                        . "}\n"
                        . "\n"
                        . "if (document.getElementById('reg_pass').value != document.getElementById('conf_pass').value)\n"
                        . "{\n"
                        . "alert('" . _PASSFAILED . "');\n"
                        . "return false;\n"
                        . "}\n";
            }

            echo "if (document.getElementById('reg_mail').value.indexOf('@') == -1)\n"
                    . "{\n"
                    . "alert('" . _MAILFAILED . "');\n"
                    . "return false;\n"
                    . "}\n"
                    . "\n"
                    . "return true;\n"
                    . "}\n"
                    ."\n"
                    . "// -->\n"
                    . "</script>\n";

            # include css and js library checkSecurityPass
            nkTemplate_addCSSFile('media/css/checkSecurityPass.css');
            nkTemplate_addJSFile('media/js/checkSecurityPass.js');

            echo "<br /><div style=\"text-align: center;\"><big><b>" . _NEWUSERREGISTRATION . "</b></big></div><br /><br />\n"
                    . "<form method=\"post\" action=\"index.php?file=User&amp;op=reg\" onsubmit=\"return verifchamps();\">\n"
                    . "<table style=\"margin-left:auto;margin-right:auto;text-align:left;width:70%;\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n"
                    . "<tr><td><b>" . _NICK . "</b> (" . _REQUIRED . ")</td><td><input id=\"reg_pseudo\" type=\"text\" name=\"pseudo\" size=\"30\" maxlength=\"30\" /> *</td></tr>\n";

            if ($nuked['inscription'] != "mail"){
                echo "<tr><td><b>" . _USERPASSWORD . "</b> (" . _REQUIRED . ")</td><td><input id=\"reg_pass\" type=\"password\" onkeyup=\"evalPwd(this.value);\" name=\"pass_reg\" size=\"10\" maxlength=\"15\" /> * \n"
                        . "<div id=\"sm\">" . _PASSCHECK ." <ul><li id=\"weak\" class=\"nrm\">" ._PASSWEAK . "</li><li id=\"medium\" class=\"nrm\">" ._PASSMEDIUM . "</li><li id=\"strong\" class=\"nrm\">" ._PASSHIGH . "</li></ul></div></td></tr>\n"
                        . "<tr><td><b>" . _PASSCONFIRM . "</b> (" . _REQUIRED . ")</td><td><input id=\"conf_pass\" type=\"password\" name=\"pass_conf\" size=\"10\" maxlength=\"15\" /> *</td></tr>\n";
            }

            echo "<tr><td><b>" . _MAIL . " " . _PRIVATE . "</b> (" . _REQUIRED . ")</td><td><input id=\"reg_mail\" type=\"text\" name=\"mail\" size=\"30\" maxlength=\"80\" /> *</td></tr>\n"
                    . "<tr><td><b>" . _MAIL . " " . _PUBLIC . "</b> (" . _OPTIONAL . ")</td><td><input type=\"text\" name=\"email\" size=\"30\" maxlength=\"80\" /></td></tr>\n"
                    . "<tr><td><b>" . _COUNTRY . "</b> (" . _OPTIONAL . ")</td><td><select name=\"country\">";

            if ($language == "french"){
                $pays = "France.gif";
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
                if ($filename == $pays){
                    $checked = "selected=\"selected\"";
                }
                else{
                    $checked = "";
                }

                list ($country, $ext) = explode ('.', $filename);
                echo "<option value=\"" . $filename . "\" " . $checked . ">" . $country . "</option>\n";
            }

            echo "</select></td></tr>\n"
                    . "<tr><td><b>" . _GAME . "</b> (" . _OPTIONAL . ")</td><td><select name=\"game\">\n";

            $sql = nkDB_execute("SELECT id, name FROM " . GAMES_TABLE . " ORDER BY name");
            while (list($game_id, $nom) = mysql_fetch_array($sql)){
                $nom = nkHtmlEntities($nom);
                echo "<option value=\"" . $game_id . "\">" . $nom . "</option>\n";
            }

            echo "</select></td></tr>\n"
                . "<tr><td colspan=\"2\">&nbsp;</td></tr>\n"
                . "<tr><td colspan=\"2\" align=\"center\">";

            if (initCaptcha()) echo create_captcha();

            echo "<input type=\"submit\" value=\"" . _USERREGISTER . "\" /></td></tr></table></form><br />\n";
        }
    }
    else{
        printNotification(_REGISTRATIONCLOSE, 'information', array('backLinkUrl' => 'javascript:history.back()'));
    }
}

function edit_account(){
    global $nuked, $user;

    if (! $user) {
        echo applyTemplate('nkAlert/userEntrance');
        redirect("index.php?file=User&op=login_screen", 2);
        return;
    }

    require_once 'Includes/nkUserSocial.php';

    define('EDITOR_CHECK', 1);

    $userSocialFields = implode(', ', nkUserSocial_getActiveFields());

    if ($userSocialFields != '') $userSocialFields = ', '. $userSocialFields;

    $dbrUser = nkDB_selectOne(
        'SELECT pseudo, mail, avatar, signature, country, rang, game'. $userSocialFields .'
        FROM '. USER_TABLE .'
        WHERE id = '. nkDB_escape($user['id'])
    );

    echo "<br /><div style=\"text-align: center;\"><big><b>" . _YOURACCOUNT . "</b></big></div><br />\n"
            . "<div style=\"text-align: center;\"><b><a href=\"index.php?file=User\">" . _INFO . "</a> | "
            . "</b>" . _PROFIL . "<b> | "
            . "<a href=\"index.php?file=User&amp;op=edit_pref\">" . _PREF . "</a> | "
            . "<a href=\"index.php?file=User&amp;op=change_theme\">" . _THEMESELECT . "</a> | "
            . "<a href=\"index.php?file=User&amp;op=logout\">" . _USERLOGOUT . "</a></b></div><br />\n";

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            . "function verifchamps()\n"
            . "{\n"
            . "\n"
            . "if (document.getElementById('edit_pseudo').value.length < 3)\n"
            . "{\n"
            . "alert('" . _3TYPEMIN . "');\n"
            . "return false;\n"
            . "}\n"
            . "\n"
            . "if (document.getElementById('edit_mail').value.indexOf('@') == -1)\n"
            . "{\n"
            . "alert('" . _MAILFAILED . "');\n"
            . "return false;\n"
            . "}\n"
            . "\n"
            . "return true;\n"
            . "}\n"
            ."\n"
            . "// -->\n"
            . "</script>\n";

    echo "<div style=\"text-align: center;\"><small><i>" . _PASSFIELD . "</i></small></div><br />\n"
            . "<form method=\"post\" action=\"index.php?file=User&amp;op=update\" enctype=\"multipart/form-data\" onsubmit=\"return verifchamps();\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr><td><b>" . _NICK . " : </b></td><td><input id=\"edit_pseudo\" type=\"text\" name=\"nick\" size=\"30\" maxlength=\"30\" value=\"" . $dbrUser['pseudo'] . "\" /> *</td></tr>\n"
            . "<tr><td><b>" . _USERPASSWORD . " : </b></td><td><input type=\"password\" name=\"pass_reg\" size=\"10\" maxlength=\"15\" autocomplete=\"off\" /> *</td></tr>\n"
            . "<tr><td><b>" . _PASSCONFIRM . " : </b></td><td><input type=\"password\" name=\"pass_conf\" size=\"10\" maxlength=\"15\" autocomplete=\"off\" /> *</td></tr>\n"
            . "<tr><td><b>" . _MAIL . " " . _PRIVATE . " : </b></td><td><input id=\"edit_mail\" type=\"text\" name=\"mail\" size=\"30\" maxlength=\"80\" value=\"" . $dbrUser['mail']. "\" /> *</td></tr>\n"
            . "<tr><td colspan=\"2\">&nbsp;</td></tr>\n"
            . "<tr><td><b>" . _USERPASSWORD . " (" . _PASSOLD . ") :</b></td><td><input type=\"password\" name=\"pass_old\" size=\"10\" maxlength=\"15\" /> *</td></tr>\n"
            . "<tr><td colspan=\"2\">&nbsp;</td></tr>\n";

            foreach (nkUserSocial_getConfig() as $userSocial) {
                $userSocialInput = nkUserSocial_getInputConfig($userSocial);

                if (isset($dbrUser[$userSocial['field']]))
                    $value = $dbrUser[$userSocial['field']];
                else
                    $value = '';

                echo '<tr><td><b>', $userSocialInput['label'], ' : </b></td><td><input type="text" name="'
                    , $userSocial['field'], '" size="', $userSocial['size'], '" maxlength="'
                    , $userSocial['maxlength'], '" value="', $value, '" /></td></tr>', "\n";
            }

            echo "<tr><td><b>" . _COUNTRY . " : </b></td><td><select name=\"country\">\n";

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
        if ($filename == $dbrUser['country']){
            $checked = "selected=\"selected\"";
        }
        else{
            $checked = "";
        }

        list ($country, $ext) = explode ('.', $filename);
        echo "<option value=\"" . $filename . "\" " . $checked . ">" . $country . "</option>\n";
    }

    echo "</select></td></tr>"
            . "<tr><td><b>" . _GAME . " :</b></td><td><select name=\"game\">\n";

    $sql = nkDB_execute("SELECT id, name FROM " . GAMES_TABLE . " ORDER BY name");
    while (list($game_id, $nom) = mysql_fetch_array($sql)){
        if ($dbrUser['game'] == $game_id){
            $checked1 = "selected=\"selected\"";;
        }
        else{
            $checked1 = "";
        }
        echo "<option value=\"" . $game_id . "\" " . $checked1 . ">" . $nom . "</option>\n";
    }

    echo "</select></td></tr>";

    $dbrTeam = nkDB_selectMany(
        'SELECT TM.rank, TR.titre AS rankName, T.titre AS teamName
        FROM '. TEAM_MEMBERS_TABLE .' AS TM
        INNER JOIN '. TEAM_TABLE .' AS T
        ON T.cid = TM.team
        INNER JOIN '. TEAM_RANK_TABLE .' AS TR
        ON TR.id = TM.rank
        WHERE TM.userId = '. nkDB_escape($user['id'])
    );

    if ($dbrTeam) {
        echo "<tr><td><b>" . __('DISPLAYED_RANK') . " :</b></td><td><select name=\"rang\">\n";

        foreach ($dbrTeam as $team) {
            if ($team['rank'] == $dbrUser['rang'])
                $selected = ' selected="selected"';
            else
                $selected = '';

            echo '<option value="'. $team['rank'] .'"'. $selected .'>'
                . nkHtmlSpecialChars($team['rankName']) .' - '. __('TEAM') .' '. nkHtmlSpecialChars($team['teamName']) .'</option>' ."\n";
        }

        echo "</select></td></tr>\n";
    }

    echo "<tr><td colspan=\"2\">&nbsp;</td></tr>\n";

    if ($nuked['avatar_upload'] == "on" || $nuked['avatar_url'] == "on"){
        echo "<tr><td><b>" . _AVATAR . " : </b></td>\n";

        if($nuked['avatar_url'] != "on") $disable = 'disabled="disabled"';
        else $disable = "";

        echo "<td><input type=\"text\" id=\"edit_avatar\" name=\"avatar\" size=\"40\" maxlength=\"100\" value=\"" . $dbrUser['avatar'] . "\" ".$disable." />"
                . "&nbsp;[ <a  href=\"#\" onclick=\"javascript:window.open('index.php?file=User&amp;op=show_avatar','Avatar','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=450,top=30,left=0');return(false)\">" . _SEEAVATAR . "</a> ]</td></tr><tr><td>&nbsp;</td>\n";

        if ($nuked['avatar_upload'] == "on"){
            echo "<td><input type=\"file\" name=\"fichiernom\" /></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n";
        }
        else{
            echo "<td>&nbsp;</td></tr>\n";
        }
    }

    echo "<tr><td><b>" . _SIGN . " :</b></td><td><textarea id=\"e_basic\" name=\"signature\" rows=\"10\" cols=\"60\">" . $dbrUser['signature'] . "</textarea></td></tr>\n";

    if ($nuked['user_delete'] == "on"){
        echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\">"._DELMYACCOUNT." <input class=\"checkbox\" type=\"checkbox\" name=\"remove\" value=\"ok\" /></td></tr>\n";
    }

    echo "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"Submit\" value=\"" . _MODIF . "\" />\n"
            . "</td></tr></table></form><br />\n";
}

function edit_pref(){
    global $user, $nuked, $bgcolor3, $bgcolor2, $bgcolor1;

    if (! $user) {
        echo applyTemplate('nkAlert/userEntrance');
        redirect("index.php?file=User&op=login_screen", 2);
        return;
    }

    $dbrUserDetail = nkDB_selectOne(
        'SELECT prenom, age, sexe, ville, motherboard, cpu, ram, video, resolution, son, ecran,
        souris, clavier, connexion, system, photo, pref_1, pref_2, pref_3, pref_4, pref_5
        FROM '. USER_DETAIL_TABLE .'
        WHERE user_id = \''. $user['id'] .'\''
    );

    if ($dbrUserDetail['age'] != ''){
        list ($jour, $mois, $an) = explode ('/', $dbrUserDetail['age']);
    }

    echo "<br /><div style=\"text-align: center;\"><big><b>" . _YOURACCOUNT . "</b></big></div><br />\n"
            . "<div style=\"text-align: center;\"><b><a href=\"index.php?file=User\">" . _INFO . "</a> | "
            . "<a href=\"index.php?file=User&amp;op=edit_account\">" . _PROFIL . "</a> | "
            . "</b>" . _PREF . "<b> | "
            . "<a href=\"index.php?file=User&amp;op=change_theme\">" . _THEMESELECT . "</a> | "
            . "<a href=\"index.php?file=User&amp;op=logout\">" . _USERLOGOUT . "</a></b></div><br />\n";

    echo "<form method=\"post\" action=\"index.php?file=User&amp;op=update_pref\" enctype=\"multipart/form-data\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
            . "<tr style=\"background: " . $bgcolor3 . ";\"><td align=\"center\" colspan=\"2\"><b>" . _INFOPERSO . "</b></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _LASTNAME . " :</b></td>"
            . "<td style=\"width: 70%;\" align=\"left\"><input type=\"text\" name=\"prenom\" value=\"" . $dbrUserDetail['prenom'] . "\" size=\"20\" /></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _BIRTHDAY . " :</b></td>"
            . "<td style=\"width: 70%;\" align=\"left\"><select name=\"jour\">\n";

    if ($jour != ""){
        echo "<option>" . $jour . "</option>\n";
    }
    else{
        $checked1 = "selected=\"selected\"";
    }

    $day = 1;
    while ($day < 32){
        if ($day == date("d")){
            echo "<option value=\"" . $day . "\" " . $checked1 . ">" . $day . "</option>\n";
        }
        else{
            echo "<option value=\"" . $day . "\">" . $day . "</option>\n";
        }
        $day++;
    }

    echo "</select>&nbsp;<select name=\"mois\">\n";

    if ($mois != ""){
        echo "<option value=\"" . $mois . "\">" . $mois . "</option>\n";
    }
    else{
        $checked2 = "selected=\"selected\"";
    }

    $month = 1;
    while ($month < 13){
        if ($month == date("m")){
            echo "<option value=\"" . $month . "\" " . $checked2 . ">" . $month . "</option>\n";
        }
        else{
            echo "<option value=\"" . $month . "\">" . $month . "</option>\n";
        }
        $month++;
    }

    echo "</select>&nbsp;<select name=\"an\">\n";

    if ($an != ""){
        echo "<option value=\"" . $an . "\">" . $an . "</option>\n";
    }
    else{
        $checked3 = "selected=\"selected\"";
    }

    $year = 1900;
    $lastyear = date("Y") + 1;

    while ($year < $lastyear){
        if ($year == date("Y")){
            echo "<option value=\"" . $year . "\" " . $checked3 . ">" . $year . "</option>\n";
        }
        else{
            echo "<option value=\"" . $year . "\">" . $year . "</option>\n";
        }
        $year++;
    }

    echo "</select></td></tr>";

    $checked4 = $checked5 = '';

    if ($dbrUserDetail['sexe'] == "male"){
        $checked4 = "checked=\"checked\"";
    }
    else if ($dbrUserDetail['sexe'] == "female"){
        $checked5 = "checked=\"checked\"";
    }

    echo "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _SEXE . " :</b></td>"
        . "<td style=\"width: 70%;\" align=\"left\"><input type=\"radio\" class=\"checkbox\" name=\"sexe\" value=\"male\" " . $checked4 . " /> " . _MALE . " <input type=\"radio\" class=\"checkbox\" name=\"sexe\" value=\"female\" " . $checked5 . " /> " . _FEMALE . "</td></tr>\n"
        . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _CITY . " :</b></td>"
        . "<td style=\"width: 70%;\" align=\"left\"><input type=\"text\" name=\"ville\" value=\"" . $dbrUserDetail['ville'] . "\" size=\"20\" /></td></tr>\n";


    if ($nuked['avatar_upload'] == "on" || $nuked['avatar_url'] == "on"){
        echo "<tr><td><b>" . _PHOTO . " (100x100) : </b></td>\n";

        if($nuked['avatar_url'] != "on") $disable = "DISABLED=\"DISABLED\"";
        else $disable = "";

        echo"<td align=\"left\"><input type=\"text\" id=\"photo\" name=\"photo\" size=\"40\" maxlength=\"150\" value=\"" . $dbrUserDetail['photo'] . "\" " . $disable . " /></td></tr>\n";

        if ($nuked['avatar_upload'] == "on"){
            echo "<tr><td style=\"width: 30%;\">&nbsp;</td><td style=\"width: 70%;\" align=\"left\"><input type=\"file\" name=\"fichiernom\" /></td></tr>\n";
        }
    }

    echo "<tr style=\"background: " . $bgcolor3 . ";\"><td align=\"center\" colspan=\"2\"><b>" . _HARDCONFIG . "</b></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _MOTHERBOARD . " :</b></td><td style=\"width: 70%;\" align=\"left\"><input type=\"text\" name=\"motherboard\" value=\"" . $dbrUserDetail['motherboard'] . "\" size=\"25\" /></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _PROCESSOR . " :</b></td><td style=\"width: 70%;\" align=\"left\"><input type=\"text\" name=\"cpu\" value=\"" . $dbrUserDetail['cpu'] . "\" size=\"25\" /></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _MEMORY . " :</b></td><td style=\"width: 70%;\" align=\"left\"><select name=\"ram\"><option>" . $dbrUserDetail['ram'] . "</option>\n"
            . "<option>- 512 Mo</option>\n"
            . "<option>1 Go</option>\n"
            . "<option>1,5 Go</option>\n"
            . "<option>2 Go</option>\n"
            . "<option>2,5 Go</option>\n"
            . "<option>3 Go</option>\n"
            . "<option>4 Go</option>\n"
            . "<option>8 Go</option>\n"
            . "<option>+ 8 Go</option>\n"
            . "</select></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _VIDEOCARD . " :</b></td><td style=\"width: 70%;\" align=\"left\"><input type=\"text\" name=\"video\" value=\"" . $dbrUserDetail['video'] . "\" size=\"25\" /></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _RESOLUTION . " :</b></td><td style=\"width: 70%;\" align=\"left\"><select name=\"resolution\"><option>" . $dbrUserDetail['resolution'] . "</option>\n"
            . "<option>640/480</option>\n"
            . "<option>800/600</option>\n"
            . "<option>1024/768</option>\n"
            . "<option>1152/864</option>\n"
            . "<option>1280/1024</option>\n"
            . "<option>1440/900 </option>\n"
            . "<option>1600/1200</option>\n"
            . "<option>1680/1050</option>\n"
            . "<option>1920/1080</option>\n"
            . "<option>1920/1200</option>\n"
            . "<option>2048/1536</option>\n"
            . "<option>2560/1600</option></select></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _SOUNDCARD . " : </b></td><td style=\"width: 70%;\" align=\"left\"><input type=\"text\" name=\"sons\" value=\"" . $dbrUserDetail['son'] . "\" size=\"25\" /></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _MONITOR . " :</b></td><td style=\"width: 70%;\" align=\"left\"><input type=\"text\" name=\"ecran\" value=\"" . $dbrUserDetail['ecran'] . "\" size=\"25\" /></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _MOUSE . " :</b></td><td style=\"width: 70%;\" align=\"left\"><input type=\"text\" name=\"souris\" value=\"" . $dbrUserDetail['souris'] . "\" size=\"25\" /></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _KEYBOARD . " :</b></td><td style=\"width: 70%;\" align=\"left\"><input type=\"text\" name=\"clavier\" value=\"" . $dbrUserDetail['clavier'] . "\" size=\"25\" /></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _CONNECT . " :</b></td><td style=\"width: 70%;\" align=\"left\"><select name=\"connexion\"><option>" . $dbrUserDetail['connexion'] . "</option>\n"
            . "<option>Modem 56K</option>\n"
            . "<option>Modem 128K</option>\n"
            . "<option>ADSL 128K</option>\n"
            . "<option>ADSL 512K</option>\n"
            . "<option>ADSL 1024K</option>\n"
            . "<option>ADSL 2048K</option>\n"
            . "<option>ADSL 3M</option>\n"
            . "<option>ADSL 4M</option>\n"
            . "<option>ADSL 5M</option>\n"
            . "<option>ADSL 8M</option>\n"
            . "<option>ADSL 20M +</option>\n"
            . "<option>Cable 128K</option>\n"
            . "<option>Cable 512K</option>\n"
            . "<option>Cable 1024K</option>\n"
            . "<option>Cable 2048K</option>\n"
            . "<option>Cable 8M</option>\n"
            . "<option>Cable 20M +</option>\n"
            . "<option>T1 1,5M</option>\n"
            . "<option>T2 6M</option>\n"
            . "<option>T3 45M</option>\n"
            . "<option>Fiber 50M</option>\n"
            . "<option>Fiber 100M</option>\n"
            . "<option>" . _OTHER . "</option></select></td></tr>\n"
            . "<tr><td style=\"width: 30%;\" align=\"left\"><b> " . _SYSTEMOS . " :</b></td><td style=\"width: 70%;\" align=\"left\"><select name=\"osystem\">\n";

    $list_os = array(
        'Windows 7',
        'Windows Vista',
        'Windows XP',
        'Windows 2000',
        'Linux',
        'Mac OS X',
        'Autre',
    );

    $detected_os = getOS();

    foreach( $list_os as $os ) {
        echo "    <option" . (($os == $dbrUserDetail['system'] OR $os == $detected_os) ? ' selected="selected"' : '') . ">" . $os . "</option>\n";
    }

    echo "</select></td></tr>\n";

    if ($user['level'] >= nivo_mod('Game') && nivo_mod('Game') > -1) {
        require_once 'modules/Game/index.php';

        displayUserGamePrefFields($dbrUserDetail);
    }

    echo "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _MODIFPREF . "\" /></div></form><br />\n";
}

function login_screen(){

    if ($GLOBALS['user']){
        redirect("index.php?file=User");
    }
    else{
        $arrayErrors = array(
                            1 => _NOFIELD,
                            2 => _BADLOG
                        );
        $printError = null;

        if(array_key_exists('error', $_REQUEST)){
            $printError = $arrayErrors[$_REQUEST['error']];
        }

        if(!empty($printError)){

            ?>
                <div class="nkAlert nkError">
                    <strong><?php echo $printError; ?></strong>
                </div>
            <?php
        }

        ?>
            <div id="nkLoginForm" class="nkCenter">
                <h3><?php echo __('LOGIN_USER') ?></h3>
                <form action="index.php?file=User&amp;op=login" method="post">
                    <p>
                        <label for="pseudo"><?php echo _NICK; ?> :</label>
                        <input type="text" name="pseudo" required="required"/>
                    </p>
                    <p>
                        <label for="pass"><?php echo _PASSWORD; ?> :</label>
                        <input type="password" name="pass" required="required"/>
                    </p>
                    <?php
                        if ((isset($_SESSION['captcha']) && $_SESSION['captcha'] === true) || initCaptcha())
                            echo create_captcha();
                    ?>
                    <p>
                        <input type="checkbox" class="checkbox" name="remember_me" value="ok" checked="checked" />
                        <label for="remember_me"><?php echo _REMEMBERME; ?></label>
                    </p>
                    <p>
                        <input type="submit" value="<?php echo _TOLOG; ?>" />
                    </p>
                    <p>
                        <a href="index.php?file=User&amp;op=reg_screen"><?php echo _USERREGISTER; ?></a> |
                        <a href="index.php?file=User&amp;op=oubli_pass"><?php echo _LOSTPASS; ?></a>
                    </p>
                </form>
            </div>
        <?php
    }
}

function reg($pseudo, $mail, $email, $pass_reg, $pass_conf, $game, $country){
    global $nuked, $cookie_forum, $user_ip;

    // Captcha checking
    if (initCaptcha() && ! validCaptchaCode())
        return;

    $pseudo = nkHtmlEntities($pseudo, ENT_QUOTES);
    $pseudo = checkNickname($pseudo);

    $mail = mysql_real_escape_string(stripslashes($mail));
    $mail = nkHtmlEntities($mail);

    if (($error = getCheckNicknameError($pseudo)) !== false) {
        printNotification($error, 'error');
        redirect('index.php?file=User&op=reg_screen', 2);
        closetable();
        return;
    }

    $mail = nkHtmlEntities($mail);
    $mail = checkEmail($mail, $checkRegistred = true);

    if (($error = getCheckEmailError($mail)) !== false) {
        printNotification($error, 'error');
        redirect('index.php?file=User&op=reg_screen', 2);
        closetable();
        return;
    }

    if ($nuked['inscription'] == "mail"){
        $lettres = "abCdefGhijklmNopqrstUvwXyz0123456789";
        srand(time());
        for ($i = 0;$i < 5;$i++){
            $rand_pass .= substr($lettres, (rand() % (strlen($lettres))), 1);
        }
        $pass_reg = $rand_pass;
        $pass_conf = $rand_pass;
    }

    if ($pass_reg != $pass_conf){
        printNotification(stripslashes(_PASSFAILED), 'error');
        redirect("index.php?file=User&op=reg_screen", 1);
        closetable();
        return;
    }

    $date = time();
    $cryptpass = nk_hash($pass_reg);

    do{
        $user_id = substr(sha1(uniqid()), 0, 20);
        $sql = nkDB_execute('SELECT * FROM ' . USER_TABLE . ' WHERE id=\'' . $user_id . '\'');
    } while (mysql_num_rows($sql) != 0);

    $email = mysql_real_escape_string(stripslashes($email));
    $email = nkHtmlEntities($email);

    if ($nuked['validation'] == "auto"){
        $niveau = 1;
    }
    else{
        $niveau = 0;
    }
    if (!(file_exists("images/flags/".basename($country).""))){
        $country = "France.gif";
    }
    $date2 = nkDate(time());
    $add = nkDB_execute(
        "INSERT INTO ". USER_TABLE ."
        (`id`, `pseudo`, `mail`, `email`, `pass`, `niveau`, `date`, `signature`, `game`, `country`, `xfire`, `facebook`, `origin`, `steam`, `twitter`, `skype`)
        VALUES
        ('". $user_id ."', '". $pseudo ."', '". $mail ."', '". $email ."', '". $cryptpass ."', '". $niveau ."', '". $date ."', '', '". $game ."', '". $country ."', '" . $xfire . "', '". $facebook ."', '". $origin ."', '". $steam ."', '". $twitter ."', '". $skype ."')"
    );

    // Mark read all topics in the forum
    $_COOKIE['cookie_forum'] = '';
    $req = 'UPDATE ' . SESSIONS_TABLE . ' SET last_used = date WHERE user_id = "' . $user_id . '"';
    $sql = nkDB_execute($req);

    $del = nkDB_execute('DELETE FROM ' . FORUM_READ_TABLE . ' WHERE user_id = "' . $user_id . '"');

    $result = nkDB_execute('SELECT id, forum_id FROM ' . FORUM_THREADS_TABLE);
    $nbtopics = mysql_num_rows($result);

    if ($nbtopics > 0) {
        while (list($thread_id, $forum_id) = mysql_fetch_row($result)) {
            $sql = nkDB_execute("INSERT INTO " . FORUM_READ_TABLE . " ( `id` , `user_id` , `thread_id` , `forum_id` ) VALUES ( '' , '" . $user_id . "' , '" . $thread_id . "' , '" . $forum_id . "' )");
        }
    }
    // End

    if ($nuked['validation'] == "mail" && $nuked['inscription'] == "on"){
        $subject = _USERREGISTER . ", " . $date2;
        $corps = _USERVALID . "\r\n" . $nuked['url'] . "/index.php?file=User&op=validation&id_user=" . $user_id . "\r\n\r\n" . _USERMAIL . "\r\n" . _NICK . " : " . $pseudo . "\r\n" . _PASSWORD . " : " . $pass_reg . "\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
        $from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $nuked['mail'];

        $subject = @nkHtmlEntityDecode($subject);
        $corps = @nkHtmlEntityDecode($corps);
        $from = @nkHtmlEntityDecode($from);
        $s_mail = @nkHtmlEntityDecode($mail);

        mail($s_mail, $subject, $corps, $from);
    }
    else{
        if ($nuked['inscription'] == "mail" || ($nuked['inscription_mail'] != "" && $nuked['validation'] == "auto")){
            if ($nuked['inscription_mail'] != ""){
                $inscription_mail = $nuked['inscription_mail'];
            }
            else{
                $inscription_mail = _USERMAIL;
            }

            $subject = _USERREGISTER . ", " .$date2;
            $corps = $inscription_mail . "<br /><br />" . _NICK . " : " . $pseudo . "<br /><br />" . _PASSWORD . " : " . $pass_reg . "<br /><br /><br /><br />" . $nuked['name'] . " - " . $nuked['slogan'];
            $from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $nuked['mail'];
            $from .= "\r\n" . 'MIME-Version: 1.0' . "\r\n";
            $from .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            $subject = @nkHtmlEntityDecode($subject);
            $corps = @nkHtmlEntityDecode($corps);
            $from = @nkHtmlEntityDecode($from);
            $s_mail = @nkHtmlEntityDecode($mail);

            mail($s_mail, $subject, $corps, $from);
        }
    }

    if ($nuked['inscription_avert'] == "on" || $nuked['validation'] == "admin"){
        $subject = _NEWUSER . " : " . $pseudo . ", " .$date2;
        $corps =  $pseudo . " (IP : " . $user_ip . ") " . _NEWREGISTRATION . " " . $nuked['name'] . " " . _NEWREGSUITE . "\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
        $from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $nuked['mail'];

        $subject = @nkHtmlEntityDecode($subject);
        $corps = @nkHtmlEntityDecode($corps);
        $from = @nkHtmlEntityDecode($from);

        mail($nuked['mail'], $subject, $corps, $from);
    }

    if ($nuked['validation'] == "mail" && $nuked['inscription'] == "on"){
        printNotification(_VALIDMAILSUCCES .'&nbsp;'. $mail, 'success');
        redirect("index.php?file=User&op=login_screen", 5);
    }
    else if ($nuked['validation'] == "admin" && $nuked['inscription'] == "on"){
        printNotification(_VALIDADMIN, 'success');
        redirect("index.php", 5);
    }
    else if ($nuked['inscription'] == "mail"){
        printNotification(_USERMAILSUCCES .'&nbsp;'. $mail, 'success');
        redirect("index.php?file=User&op=login_screen", 5);
    }
    else{
        printNotification(_REGISTERSUCCES, 'success');
        redirect("index.php?file=User&op=login&pseudo=" . urlencode($pseudo) . "&pass=" . urlencode($pass_reg) . "&remember_me=ok", 2);
    }
}

function login($pseudo, $pass, $remember_me){
    nkTemplate_setPageDesign('none');

    // Si il manque un champs on stop le script et on redirige vers le formulaire
    if(empty($pseudo) || empty($pass))
        redirect('index.php?file=User&op=login_screen&error=1');

    $dbsLogin = 'SELECT id, pass AS dbPass, user_theme AS userTemplate, user_langue AS userLang, niveau AS level, erreur AS nbErrors
                 FROM '.USER_TABLE.'
                 WHERE pseudo = "'.htmlentities($pseudo, ENT_QUOTES, 'ISO-8859-1').'" ';
    $dbeLogin = nkDB_execute($dbsLogin);

    $checkLogin = mysql_num_rows($dbeLogin);

    if($checkLogin > 0){
        // Un utilisateur pour ce pseudo a été trouvé
        $dbrLogin = mysql_fetch_assoc($dbeLogin);

        if($dbrLogin['nbErrors'] >= 2){
            // Si un visiteur a fait 3 mauvais login
            if (! isset($_SESSION['captcha'])) {
                $_SESSION['captcha'] = true;
                nkNotification(_MSGCAPTCHA);
                redirect('index.php?file=User&op=login_screen', 2);
                return;
            }
            else {
                if (! validCaptchaCode())
                    return;
            }
        }

        if($dbrLogin['level'] > 0){
            // Si le compte de l'utilisateur est validé
            if(!Check_Hash($pass, $dbrLogin['dbPass'])){
                $newNbErrors = $dbrLogin['nbErrors'] + 1;
                // Si les pass ne correspondent pas
                $dbuUser = 'UPDATE '.USER_TABLE.'
                            SET erreur = "'.$newNbErrors.'"
                            WHERE pseudo = "'.htmlentities($pseudo, ENT_QUOTES, 'ISO-8859-1').'" ';
                nkDB_execute($dbuUser);
                redirect('index.php?file=User&op=login_screen&error=2');
            }
            else{
                // Si les identifiants sont bons
                $dbuUser = 'UPDATE '.USER_TABLE.'
                            SET erreur = "0"
                            WHERE pseudo = "'.htmlentities($pseudo, ENT_QUOTES, 'ISO-8859-1').'" ';
                nkDB_execute($dbuUser);
                nkSessions_createNewSession($dbrLogin['id'], $remember_me);
            }

            if(!empty($dbrLogin['userTemplate'])){
                setcookie($GLOBALS['cookie_theme'], $dbrLogin['userTemplate'], $GLOBALS['timelimit']);
            }

            if(!empty($dbrLogin['userLang'])){
                setcookie($GLOBALS['cookie_langue'], $dbrLogin['userLang'], $GLOBALS['timelimit']);
            }

            $referer = $_SERVER['HTTP_REFERER'];

            if (!empty($referer) && !strpos($referer, 'User&op=reg')){
                list($url_ref, $redirect) = explode('?', $referer);
                if(!empty($redirect)) $redirect = '&referer=' . urlencode($redirect);
            }
            else $redirect = '';

            $_SESSION['admin'] = false;
            unset($_SESSION['captcha']);
            redirect('index.php?file=User&op=login_message'. $redirect);
        }
        else{
            // Si le compte n'est pas validé
            nkNotification(_NOVALIDUSER);
            redirect('index.php', 2);
        }
    }
    else {
        // Aucun utilisateur trouvé pour ce pseudo
        nkNotification(_UNKNOWNUSER);
        redirect('index.php', 2);
    }
}

function login_message(){
    global $nuked, $bgcolor1, $bgcolor3, $cookie_session, $referer, $user_ip;

    if (isset($_COOKIE[$cookie_session]) && $_COOKIE[$cookie_session] != ""){
        $test_cookie = $_COOKIE[$cookie_session];
    }
    else{
        $test_cookie = "";
    }

    if(array_key_exists('referer', $_REQUEST)){
        $referer = urldecode($_REQUEST['referer']);
    }
    else{
        $referer = '';
    }

    $referer = str_replace('&amp;', '&', $referer);

    if (!empty($referer) && !stripos($referer, 'User&op=reg')){
        $url = "index.php?" . $referer;
    }
    else{
        $url = "index.php";
    }

    nkTemplate_setPageDesign('nudePage');

    if ($test_cookie != ""){
        echo "<table width=\"400\" style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor3 . ";\" cellspacing=\"1\" cellpadding=\"20\">\n"
            . "<tr><td style=\"background: " . $bgcolor1 . ";\" align=\"center\"><big><b>" . _LOGINPROGRESS . "</b></big></td></tr></table>";

        redirect($url, 2);
    }
    else{
        if ($nuked['sess_inactivemins'] > 0 && $user_ip != "" && $user_ip != "127.0.0.1"){
            $login_text = _LOGINPROGRESS . "<br /><br />" . _SESSIONIPOPEN . "<br /><br />" . _ERRORCOOKIE;
        }
        else{
            $login_text = _ERRORCOOKIE;
        }

        echo "<table width=\"80%\" style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor3 . ";\" cellspacing=\"1\" cellpadding=\"20\">\n"
            . "<tr><td style=\"background: " . $bgcolor1 . ";\" align=\"center\"><big><b>" . $login_text . "</b></big></td></tr></table>";

        redirect($url, 10);
    }
}

function update(){
    global $nuked, $user;

    if (isset($_POST['remove']) && $_POST['remove'] == "ok" && $nuked['user_delete'] == "on"){
        echo "<br /><form action=\"index.php?file=User&amp;op=del_account\" method=\"post\">\n"
            . "<div style=\"text-align: center;\"><big><b>" . _DELMYACCOUNT . "</b></big></div><br />\n"
            . "<table align=\"center\" border=\"0\">\n"
            . "<tr><td align=\"center\">" . _REMOVECONFIRM . "</td></tr>\n"
            . "<tr><td><b>" . _USERPASSWORD . " :</b> <input type=\"password\" name=\"pass\" size=\"10\" maxlength=\"15\" /></td></tr>\n"
            . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . __('SEND') . "\" />&nbsp;"
            ."<input type=\"button\" value=\"" . _CANCEL . "\" onclick=\"document.location='index.php?file=User&amp;op=edit_account'\" /></td></tr></table></form><br />\n";

        return;
    }

    require_once 'Includes/nkUpload.php';
    require_once 'Includes/nkUserSocial.php';

    $data = array();

    $_POST['nick'] = nkHtmlEntities($_POST['nick'], ENT_QUOTES);

    $_POST['mail'] = stripslashes($_POST['mail']);
    $_POST['mail'] = nkHtmlEntities($_POST['mail']);

    $sql = nkDB_execute("SELECT pseudo, mail, pass FROM " . USER_TABLE . " WHERE id = '" . $user[0] . "'");
    list($old_pseudo, $old_mail, $old_pass) = mysql_fetch_array($sql);

    if ($_POST['nick'] != $old_pseudo){
        $_POST['nick'] = checkNickname($_POST['nick']);

        if (($error = getCheckNicknameError($_POST['nick'])) !== false) {
            printNotification($error, 'error');
            redirect('index.php?file=User&op=edit_account', 2);
            closetable();
            return;
        }

        if (!Check_Hash($_POST['pass_old'], $old_pass) || !$_POST['pass_old']){
            printNotification(_BADOLDPASS, 'error');
            redirect("index.php?file=User&op=edit_account", 2);
            closetable();
            return;
        }
        else{
            $data['pseudo'] = $_POST['nick'];
        }
    }

    if ($_POST['mail'] != $old_mail){
        $_POST['mail'] = nkHtmlEntities($_POST['mail']);
        $_POST['mail'] = checkEmail($_POST['mail'], $checkRegistred = true);

        if (($error = getCheckEmailError($_POST['mail'])) !== false) {
            printNotification($error, 'error');
            redirect('index.php?file=User&op=edit_account', 2);
            closetable();
            return;
        }

        if (!Check_Hash($_POST['pass_old'], $old_pass) || !$_POST['pass_old']){
            printNotification(_BADOLDPASS, 'error');
            redirect("index.php?file=User&op=edit_account", 2);
            closetable();
            return;
        }
        else{
            $data['mail'] = $_POST['mail'];
        }
    }

    if ($_POST['pass_reg'] != "" || $_POST['pass_conf'] != ""){
        if ($_POST['pass_reg'] != $_POST['pass_conf']){
            printNotification(stripslashes(_PASSFAILED), 'error');
            redirect("index.php?file=User&op=edit_account", 2);
            closetable();
            return;
        }
        else if (!Check_Hash($_POST['pass_old'], $old_pass) || !$_POST['pass_old']){
            printNotification(_BADOLDPASS, 'error');
            redirect("index.php?file=User&op=edit_account", 2);
            closetable();
            return;
        }
        else{
            $data['pass'] = nk_hash($_POST['pass_reg']);
        }
    }

    if (isset($_POST['rang'])) {
        $dbrRank = nkDB_selectOne(
            'SELECT TM.rank
            FROM '. TEAM_MEMBERS_TABLE .' AS TM
            INNER JOIN '. TEAM_RANK_TABLE .' AS TR
            ON TR.id = TM.rank
            WHERE TM.userId = '. nkDB_escape($user['id'])
        );

        $_POST['rang'] = (int) $_POST['rang'];

        if ($dbrRank && $dbrRank['rank'] == $_POST['rang'])
            $data['rang'] = $_POST['rang'];
    }

    $data['signature'] = secu_html(nkHtmlEntityDecode($_POST['signature']));

    $data['signature'] = stripslashes($data['signature']);
    $data['game']      = stripslashes($_POST['game']);
    $data['country']   = stripslashes($_POST['country']);

    $data['game']    = nkHtmlEntities($data['game']);
    $data['country'] = nkHtmlEntities($data['country']);

    if (! empty($_POST['url']) && stripos($_POST['url'], 'http://') === false)
        $_POST['url'] = 'http://'. $_POST['url'];

    //Upload du fichier
    if ($_FILES['fichiernom']['name'] != '') {
        list($data['avatar'], $uploadError, $avatarExt) = nkUpload_check('fichiernom', array(
            'fileType'   => 'image',
            'uploadDir'  => 'upload/User',
            'fileRename' => true,
            'fileSize'   => 100000
        ));

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            redirect('index.php?file=User&op=edit_account', 5);
            return;
        }
    }
    else if ($_POST['avatar'] != '') {
        $ext = strtolower(substr(strrchr($_POST['avatar'], '.'), 1));

        if (! in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
            printNotification(__('BAD_IMAGE_FORMAT'), 'error');
            redirect('index.php?file=User&op=edit_account', 5);
            return;
        }

        $data['avatar'] = nkHtmlEntities(stripslashes($_POST['avatar']));
    }

    if (! file_exists("images/flags/".$data['country'].""))
        $data['country'] = "France.gif";

    foreach (nkUserSocial_getConfig() as $userSocial) {
        if (isset($_POST[$userSocial['field']])) {
            $data[$userSocial['field']] = nkHtmlEntities(stripslashes($_POST[$userSocial['field']]));
        }
    }

    nkDB_update(USER_TABLE, $data, 'id = '. nkDB_escape($user['id']));

    printNotification(_INFOMODIF, 'success');
    redirect("index.php?file=User", 1);
}

function update_pref() {
    global $user;

    require_once 'Includes/nkUpload.php';

    $data = array(
        'prenom'      => nkHtmlEntities(stripslashes($_POST['prenom'])),
        'ville'       => nkHtmlEntities(stripslashes($_POST['ville'])),
        'motherboard' => nkHtmlEntities(stripslashes($_POST['motherboard'])),
        'cpu'         => nkHtmlEntities(stripslashes($_POST['cpu'])),
        'ram'         => nkHtmlEntities(stripslashes($_POST['ram'])),
        'video'       => nkHtmlEntities(stripslashes($_POST['video'])),
        'resolution'  => nkHtmlEntities(stripslashes($_POST['resolution'])),
        'son'         => nkHtmlEntities(stripslashes($_POST['sons'])),
        'ecran'       => nkHtmlEntities(stripslashes($_POST['ecran'])),
        'souris'      => nkHtmlEntities(stripslashes($_POST['souris'])),
        'clavier'     => nkHtmlEntities(stripslashes($_POST['clavier'])),
        'connexion'   => nkHtmlEntities(stripslashes($_POST['connexion'])),
        'system'      => nkHtmlEntities(stripslashes($_POST['osystem']))
    );

    //Upload du fichier
    if ($_FILES['fichiernom']['name'] != '') {
        list($data['photo'], $uploadError, $imageExt) = nkUpload_check('fichiernom', array(
            'fileType'   => 'image',
            'uploadDir'  => 'upload/User',
            'fileRename' => true,
            'fileSize'   => 100000
        ));

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            redirect('index.php?file=User&op=edit_pref', 5);
            return;
        }
    }
    else if ($_POST['photo'] != '') {
        $ext = strtolower(substr(strrchr($_POST['photo'], '.'), 1));

        if (! in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
            printNotification(__('BAD_IMAGE_FORMAT'), 'error');
            redirect('index.php?file=User&op=edit_pref', 5);
            return;
        }

        $data['photo'] = nkHtmlEntities(stripslashes($_POST['photo']));
    }

    if ($_POST['an'] < date('Y')) {
        // TODO : Chech date validity
        $data['age'] = $_POST['jour'] .'/'. $_POST['mois'] .'/'. $_POST['an'];
    }

    if (array_key_exists('sexe', $_POST) && in_array($_POST['sexe'], array('male', 'female')))
        $data['sexe'] = $_POST['sexe'];

    $check = nkDB_totalNumRows(
        'FROM '. USER_DETAIL_TABLE .'
        WHERE user_id = \''. $user['id'] .'\''
    );

    if ($check > 0) {
        nkDB_update(USER_DETAIL_TABLE, $data, 'user_id = \''. $user['id'] .'\'');
    }
    else {
        $data['user_id'] = $user['id'];

        nkDB_insert(USER_DETAIL_TABLE, $data);
    }

    if ($user['level'] >= nivo_mod('Game') && nivo_mod('Game') > -1) {
        require_once 'modules/Game/index.php';

        saveUserGamePrefFields($data);
    }

    printNotification(_PREFMODIF, 'success');
    redirect("index.php?file=User", 2);
}

function logout() {
    global $user;

    nkTemplate_setPageDesign('none');
    nkSessions_stopSession($user['id']);

    redirect('index.php');
}

function oubli_pass(){
    echo "<br /><form action=\"index.php?file=User&amp;op=envoi_mail\" method=\"post\">\n"
            . "<div style=\"text-align: center;\"><big><b>" . _LOSTPASSWORD . "</b></big></div>\n"
            . "<div style=\"width: 70%;margin-left: auto;margin-right: auto;text-align: left;\"><br />" . _LOSTPASSTXT . "<br /><br /></div>\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">\n"
            . "<tr><td><b>" . _MAIL . " :</b></td><td><input type=\"text\" name=\"email\" size=\"30\" maxlength=\"80\" /></td></tr>\n"
            . "<tr><td colspan=\"2\">&nbsp;</td></tr><tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"" . __('SEND') . "\" /></td></tr></table></form><br />\n";
}

function envoi_mail($email){
    global $nuked;

    $pattern = '#^[a-z0-9]+[a-z0-9._-]*@[a-z0-9.-]+.[a-z0-9]{2,3}$#';
    if(!preg_match($pattern, $email)){
        printNotification(_WRONGMAIL, 'error');
        redirect("index.php?file=User&op=oubli_pass", 3);
        closetable();
        return;
    }

    $sql = nkDB_execute('SELECT pseudo, token, token_time FROM '.USER_TABLE.' WHERE mail = \''.$email.'\' ');
    $count = mysql_num_rows($sql);
    $data = mysql_fetch_assoc($sql);

    if($count > 0){
        if($data['token'] != null && (time() - $data['token_time']) < 3600){
            printNotification(_LINKALWAYSACTIVE, 'error');
            redirect("index.php", 3);
            closetable();
            return;
        }
        elseif($data['token'] == null || ($data['token'] != null && (time() - $data['token_time']) > 3600)){
            $new_token = uniqid();
            nkDB_execute('UPDATE '.USER_TABLE.' SET token = \''.$new_token.'\', token_time = \''.time().'\' WHERE mail = \''.mysql_real_escape_string($email).'\' ');

            $link = '<a href="'.$nuked['url'].'/index.php?file=User&op=envoi_pass&email='.$email.'&token='.$new_token.'">'.$nuked['url'].'/index.php?file=User&op=envoi_pass&email='.$email.'&token='.$new_token.'</a>';

            $message = "<html><body><p>"._HI." ".$data['pseudo'].",<br/><br/>"._LINKTONEWPASSWORD." : <br/><br/>".$link."<br/><br/>"._LINKTIME."</p><p>".$nuked['name']." - ".$nuked['slogan']."</p></body></html>";
            $headers ='From: '.$nuked['name'].' <'.$nuked['mail'].'>'."\n";
            $headers .='Reply-To: '.$nuked['mail']."\n";
            $headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
            $headers .='Content-Transfer-Encoding: 8bit';

            $message = @nkHtmlEntityDecode($message);

            mail($email, _LOSTPASSWORD, $message, $headers);

            printNotification(_MAILSEND, 'success');
            redirect("index.php", 3);
        }
    }
    else{
        printNotification(_MAILNOEXIST, 'error');
        redirect("index.php?file=User&op=oubli_pass", 3);
    }
}

function envoi_pass($email, $token){
    global $nuked;

    $pattern = '#^[a-z0-9]+[a-z0-9._-]*@[a-z0-9.-]+.[a-z0-9]{2,3}$#';
    if(!preg_match($pattern, $email)){
        printNotification(_WRONGMAIL, 'error');
        redirect("index.php", 3);
        closetable();
        return;
    }

    $pattern = '#^[a-z0-9]{13}$#';
    if(!preg_match($pattern, $token)){
        printNotification(_WRONGTOKEN, 'error');
        redirect("index.php", 3);
        closetable();
        return;
    }

    $sql = nkDB_execute('SELECT pseudo, token, token_time FROM '.USER_TABLE.' WHERE mail = \''.$email.'\' ');
    $count = mysql_num_rows($sql);
    $data = mysql_fetch_assoc($sql);

    if($count > 0){
        if($data['token'] != null && (time() - $data['token_time']) < 3600){
            if($token == $data['token']){
                $new_pass = makePass();

                $message = "<html><body><p>"._HI." ".$data['pseudo'].",<br/><br/>"._NEWPASSWORD." : <br/><br/><strong>".$new_pass."</strong><br/></p><p>".$nuked['name']." - ".$nuked['slogan']."</p></body></html>";
                $headers ='From: '.$nuked['name'].' <'.$nuked['mail'].'>'."\n";
                $headers .='Reply-To: '.$nuked['mail']."\n";
                $headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
                $headers .='Content-Transfer-Encoding: 8bit';

                $message = @nkHtmlEntityDecode($message);

                mail($email, _YOURNEWPASSWORD, $message, $headers);

                $new_pass = nk_hash($new_pass);

                nkDB_execute('UPDATE '.USER_TABLE.' SET pass = \''.$new_pass.'\', token = \'null\', token_time = \'0\' WHERE mail = \''.mysql_real_escape_string($email).'\' ');

                printNotification(_NEWPASSSEND, 'success');
                redirect("index.php?file=User&op=login_screen", 3);
            }
            else{
                printNotification(_WRONGTOKEN, 'error');
                redirect("index.php", 3);
                closetable();
                return;
            }
        }
        elseif($data['token'] == null || ($data['token'] != null && (time() - $data['token_time']) > 3600)){
            printNotification(_LINKNOACTIVE, 'error');
            redirect("index.php?file=User&op=oubli_pass", 3);
            closetable();
            return;
        }
    }
    else{
        printNotification(_MAILNOEXIST, 'error');
        redirect("index.php?file=User&op=oubli_pass", 3);
    }
}

function makePass(){
    $makepass = "";
    $syllables = "er,in,tia,wol,fe,pre,vet,jo,nes,al,len,son,cha,ir,ler,bo,ok,tio,nar,sim,ple,bla,ten,toe,cho,co,lat,spe,ak,er,po,co,lor,pen,cil,li,ght,wh,at,the,he,ck,is,mam,bo,no,fi,ve,any,way,pol,iti,cs,ra,dio,sou,rce,sea,rch,pa,per,com,bo,sp,eak,st,fi,rst,gr,oup,boy,ea,gle,tr,ail,bi,ble,brb,pri,dee,kay,en,be,se";
    $syllable_array = explode(",", $syllables);
    srand((double)microtime() * 1000000);
    for ($count = 1;$count <= 4;$count++){
        if (rand() % 10 == 1){
            $makepass .= sprintf("%0.0f", (rand() % 50) + 1);
        }
        else{
            $makepass .= sprintf("%s", $syllable_array[rand() % 62]);
        }
    }
    return($makepass);
}

function show_avatar(){
    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_AVATARLIST);

    echo "<table width=\"100%\"><tr><td align=\"center\"><b>" . _CLICAVATAR . "</b></td></tr>\n"
            . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\">\n";

    echo "<script type=\"text/javascript\">\n"
            ."<!--\n"
            ."\n"
            ."function go(img) {\n"
            ."opener.document.getElementById('edit_avatar').value=img;\n"
            ."}\n"
            ."\n"
            . "// -->\n"
            . "</script>\n";

    if ($dir = @opendir("images/avatar/")){
        while (false !== ($f = readdir($dir))){
            if ($f != "." && $f != ".." && $f != "index.html" && $f != "Thumbs.db"){
                $avatar = "images/avatar/" . $f . "";
                echo " <a href=\"#\" onclick=\"javascript:go('" . $avatar . "');\"><img style=\"border: 0;\" src=\"images/avatar/" . $f . "\" alt=\"\" title=\"" . $f . "\" /></a>";
            }
        }
        closedir($dir);
    }
    echo "</td></tr><tr><td>&nbsp;</td></tr>\n"
            . "<tr><td align=\"center\"><b>[ <a href=\"#\" onclick=\"self.close()\">" . __('CLOSE_WINDOW') . "</a> ]</b></td></tr>\n"
            . "<tr><td>&nbsp;</td></tr></table></body></html>";
}

function change_theme(){
    global $nuked, $cookie_theme;

    if(array_key_exists($cookie_theme, $_COOKIE)){
        $cookietheme = $_COOKIE[$cookie_theme];
    }
    else{
        $cookietheme = '';
    }


    echo "<br /><div style=\"text-align: center;\"><big><b>" . _YOURACCOUNT . "</b></big></div><br />\n"
            . "<div style=\"text-align: center;\"><b><a href=\"index.php?file=User\">" . _INFO . "</a> | "
            . "<a href=\"index.php?file=User&amp;op=edit_account\">" . _PROFIL . "</a> | "
            . "<a href=\"index.php?file=User&amp;op=edit_pref\">" . _PREF . "</a> | "
            . "</b>" . _THEMESELECT . "<b> | "
            . "<a href=\"index.php?file=User&amp;op=logout\">" . _USERLOGOUT . "</a></b></div>\n"
            . "<br /><form method=\"post\" action=\"index.php?file=User&amp;op=modif_theme\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\">\n"
            . "<tr><td>" . _SELECTTHEME . " :</td></tr>\n"
            . "<tr><td align=\"center\"><select name=\"user_theme\">\n";

    if ($cookietheme != ""){
        $mod = $cookietheme;
    }
    else{
        $mod = $nuked['theme'];
    }

    $handle = opendir('themes');
    while (false !== ($f = readdir($handle))){
        if ($f != "." && $f != ".." && $f != "CVS" && $f != "index.html" && strpos($f, '.') === false) {
            if ($mod == $f){
                $checked = "selected=\"selected\"";
            }
            else{
                $checked = "";
            }
            echo "<option value=\"" . $f . "\" " . $checked . ">" . $f . "</option>\n";
        }
    }

    closedir($handle);
    echo "</select></td></tr><tr><td>&nbsp;</td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . _CHANGETHEME . "\" /></td></tr></table></form><br />\n";
}

function modif_theme(){
    global $user, $nuked, $cookie_theme, $timelimit;

    nkTemplate_setPageDesign('none');

    $dir = "themes/" . $_REQUEST['user_theme'];

    if (is_dir($dir) && $_REQUEST['user_theme']){
        setcookie($cookie_theme, $_REQUEST['user_theme'], $timelimit);

        if ($user){
            $upd = nkDB_execute("UPDATE " . USER_TABLE . " SET user_theme = '" . $_REQUEST['user_theme'] . "' WHERE id = '" . $user[0] . "'");
        }
    }

    redirect('index.php');
}

function modif_langue(){
    global $user, $nuked, $cookie_langue, $timelimit;

    nkTemplate_setPageDesign('none');

    if ($_REQUEST['user_langue'] != ""){
        setcookie($cookie_langue, $_REQUEST['user_langue'], $timelimit);

        if ($user){
            $upd = nkDB_execute("UPDATE " . USER_TABLE . " SET user_langue = '" . $_REQUEST['user_langue'] . "' WHERE id = '" . $user[0] . "'");
        }
    }

    redirect('index.php');
}

function validation() {
    global $user, $nuked;

    if ($nuked['validation'] == 'mail') {
        $sql = nkDB_execute('SELECT niveau FROM ' . USER_TABLE . ' WHERE id = "' . $_REQUEST['id_user'] . '"');
        list($niveau) = mysql_fetch_array($sql);

        if ($niveau > 0) {
            printNotification(_ALREADYVALID, 'warning');
            redirect('index.php?file=User', 3);
        }
        else {
            $upd = nkDB_execute('UPDATE ' . USER_TABLE . ' SET niveau = 1 WHERE id = "' . $_REQUEST['id_user'] . '"');

            printNotification(_VALIDUSER, 'success');
            redirect('index.php?file=User&op=login_screen', 3);
        }
    }
    else {
        echo applyTemplate('nkAlert/noEntrance');
        redirect('index.php?file=User&op=login_screen', 2);
    }
}

/**
 * Delete moderator from FORUM_TABLE with a user ID
 * @param integer $idUser : a user ID
 * @return bool : true if delete success, false if not
 */
function delModerator($idUser)
{
    $resultQuery = nkDB_execute("SELECT id,moderateurs FROM " . FORUM_TABLE . " WHERE moderateurs LIKE '%" . $idUser . "%'");
    while (list($forumID, $listModos) = mysql_fetch_row($resultQuery))
    {
        if (is_int(strpos($listModos, '|'))) //Multiple moderators in this category
        {
            var_dump($listModos);
            $tmpListModos = explode('|', $listModos);
            $tmpKey = array_search($idUser, $tmpListModos);
            if ($tmpKey !== false)
            {
                unset($tmpListModos[$tmpKey]);
                $tmpListModos = implode('|', $tmpListModos);
                $updateQuery = nkDB_execute("UPDATE " . FORUM_TABLE . " SET moderateurs = '" . $tmpListModos . "' WHERE id = '" . $forumID . "'");
            }
        }
        else
        {
            if ($idUser == $listModos) // Only one moderator in this category
            {
                $updateQuery = nkDB_execute("UPDATE " . FORUM_TABLE . " SET moderateurs = '' WHERE id = '" . $forumID . "'");
            }
            // Else, no moderator in this category
        }
    }
    if ($resultQuery)
        return true;
    else
        return false;
}


function del_account($pass){
    global $user, $nuked;

    if ($pass != "" && $nuked['user_delete'] == "on"){
        $escapeUserId = nkDB_escape($user['id']);

        $sql = nkDB_execute('SELECT pass FROM '. USER_TABLE .' WHERE id = '. $escapeUserId);
        $dbpass = mysql_fetch_row($sql);
        if (Check_Hash($pass, $dbpass[0])){
            $del1 = delModerator($user['id']);

            nkDB_delete(FORUM_READ_TABLE, 'user_id = '. $escapeUserId);
            nkDB_update(NBCONNECTE_TABLE, array('type' => 0, 'user_id' => ''), 'user_id = '. $escapeUserId);
            nkDB_update(STATS_VISITOR_TABLE, array('user_id' => ''), 'user_id = '. $escapeUserId);
            nkDB_delete(SESSIONS_TABLE, 'user_id = '. $escapeUserId);
            nkDB_delete(USER_TABLE, 'id = '. $escapeUserId);

            printNotification(_ACCOUNTDELETE, 'success');
            redirect("index.php", 2);
        }
        else{
            printNotification(_BADPASSWORD, 'error');
            redirect("index.php?file=User&op=edit_account", 2);
        }
    }
    else{
        printNotification(stripslashes(_NOPASSWORD), 'error');
        redirect("index.php?file=User&op=edit_account", 2);
    }
}

switch ($GLOBALS['op']){
    case"edit_account":
        opentable();
        edit_account();
        closetable();
        break;
    case"edit_pref":
        opentable();
        edit_pref();
        closetable();
        break;
    case"index":
        index();
        break;
    case"reg_screen":
        opentable();
        reg_screen();
        closetable();
        break;
    case"login_screen":
        login_screen();
        break;
    case"reg":
        opentable();
        reg($_REQUEST['pseudo'], $_REQUEST['mail'], $_REQUEST['email'], $_REQUEST['pass_reg'], $_REQUEST['pass_conf'], $_REQUEST['game'], $_REQUEST['country']);
        closetable();
        break;
    case"login":
        login($_REQUEST['pseudo'], $_REQUEST['pass'], $_REQUEST['remember_me']);
        break;
    case"login_message":
        login_message();
        break;
    case"update":
        opentable();
        update();
        closetable();
        break;
    case"update_pref":
        opentable();
        update_pref();
        closetable();
        break;
    case"logout":
        logout();
        break;
    case"oubli_pass":
        opentable();
        oubli_pass();
        closetable();
        break;
    case"envoi_pass":
        opentable();
        envoi_pass($_REQUEST['email'], $_REQUEST['token']);
        closetable();
        break;
    case"show_avatar":
        show_avatar();
        break;
    case"change_theme":
        opentable();
        change_theme();
        closetable();
        break;
    case"modif_theme":
        modif_theme($_REQUEST);
        break;
    case"modif_langue":
        modif_langue($_REQUEST);
        break;
    case"validation":
        opentable();
        validation();
        closetable();
        break;
    case"del_account":
        opentable();
        del_account($_REQUEST['pass']);
        closetable();
        break;
    case"envoi_mail":
        opentable();
        envoi_mail($_REQUEST['email']);
        closetable();
        break;
    default:
        index();
        break;
}

?>