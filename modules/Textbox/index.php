<?php
/**
 * index.php
 *
 * Frontend of Textbox module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

global $language;

nkTemplate_moduleInit('Textbox');

translate('modules/Textbox/lang/'. $language .'.lang.php');


function index() {
    global $nuked, $user, $theme, $bgcolor1, $bgcolor2, $bgcolor3, $visiteur, $p;

    $level_access = nivo_mod("Textbox");
    $level_admin = admin_mod("Textbox");

    if ($visiteur >= $level_access && $level_access > -1) {
        $nb_mess = $nuked['max_shout'];

        $sql = nkDB_execute("SELECT id FROM " . TEXTBOX_TABLE);
        $count = nkDB_numRows($sql);

        $start = $p * $nb_mess - $nb_mess;

        $pagination = number($count, $nb_mess, 'index.php?file=Textbox', true);

        echo "<div class=\"nkAlignCenter\"><h1>" . _SHOUTBOX . "</h1></div>\n"
            . $pagination
            ."<div id=\"nkTextboxWrapper\">\n";

        if ($visiteur >= $level_admin && $level_admin > -1) {
            echo "<script type=\"text/javascript\">\n"
            . "<!--\n"
            . "\n"
            . "if ('function' != typeof(deleteShoutboxMsg)){\n"
            . "function deleteShoutboxMsg(pseudo, id)\n"
            . "{\n"
            . "if (confirm('" . _DELETETEXT . " '+pseudo+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?admin=Textbox&op=delete&id='+id;}\n"
            . "}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";
        }

        $sql2 = nkDB_execute(
            "SELECT id, auteur, ip, texte, date
            FROM " . TEXTBOX_TABLE . "
            ORDER BY id DESC LIMIT " . $start . ", " . $nb_mess
        );

        $j = 0;

        while (list($mid, $auteur, $ip, $texte, $date) = nkDB_fetchArray($sql2)) {
            $texte = printSecuTags($texte);
            $texte = nk_CSS($texte);

            $texte = ' ' . $texte;
            $texte = preg_replace("#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="\2://\3" onclick="window.open(this.href); return false;">\2://\3</a>', $texte);
            $texte = preg_replace("#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="http://\2.\3" onclick="window.open(this.href); return false;">\2.\3</a>', $texte);
            $texte = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $texte);

            $texte = icon($texte);
            $date = nkDate($date);

            $auteur = nk_CSS($auteur);

            $sql_aut = nkDB_execute(
                'SELECT U.country, TM.color
                FROM '. USER_TABLE .' AS U
                LEFT JOIN '. TEAM_RANK_TABLE .' AS TM
                ON TM.id = U.rang
                WHERE U.pseudo = '. nkDB_quote($auteur)
            );

            list($country, $rank_color) = nkDB_fetchArray($sql_aut);

            if ($rank_color != '')
                $style = ' style="color:#'. $rank_color .'"';
            else
                $style = '';

            $pays = ($country) ? '<img src="images/flags/' . $country . '" alt="' . $country . '" style="margin-right:2px;"/>' : '';
            $url_auteur = '<a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($auteur) . '"'. $style .'>' . $auteur . '</a>';

            $bg = ($j++ % 2 == 1) ? $bgcolor1 : $bgcolor2;

            if ($visiteur >= $level_admin && $level_admin > -1) {
                $admin = "<div style=\"text-align: right;\"><div class=\"nkButton-group\"><span class=\"nkButton icon alone pin small\" title=\"" . $ip . "\"></span><a href=\"index.php?admin=Textbox&amp;op=edit&amp;id=" . $mid . "\" class=\"nkButton icon alone edit small\" title=\"" . __('EDIT_THIS_SHOUTBOX_MESSAGE') . "\"></a>"
                . "&nbsp;<a href=\"javascript:deleteShoutboxMsg('" . addslashes($auteur) . "', '" . $mid . "');\" class=\"nkButton icon alone remove small danger\" title=\"" . __('DELETE_THIS_SHOUTBOX_MESSAGE') . "\"></a></div></div>";
            }
            else {
                $admin = "";
            }

            echo "<div class=\"nkShootboxTinyRow\" style=\"background: " . $bg . ";padding:5px;\">\n"
            . "<div class=\"nkInlineBlock nkFloatRight\" style=\"padding:5px 0\">". $admin ."</div>\n"
            . "<div>" . $pays . "&lsaquo;<strong>" . $url_auteur . "</strong>&rsaquo;" . $texte . "\n"
            . "<br /><span class=\"nkShootDate\">" . $date . "<span></div></div>\n"
            . "<div class=\"nkClear\"></div>\n";
        }

        if ($count == 0) echo "<div class=\"nkAlignCenter\">" . _NOMESS . "</div>\n";

        echo "</div>"
            . $pagination
            . "<br /><div class=\"nkAlignCenter\"><small><i>( " . _THEREIS . "&nbsp;" . $count . "&nbsp;" . _SHOUTINDB . " )</i></small></div><br />\n";
    }
    else if ($level_access == -1) {
        // On affiche le message qui previent l'utilisateur que le module est désactivé
        echo applyTemplate('nkAlert/moduleOff');
    }
    else if ($level_access == 1 && $visiteur == 0) {
        // On affiche le message qui previent l'utilisateur qu'il n'as pas accés à ce module
        echo applyTemplate('nkAlert/userEntrance');
    }
    else {
        // On affiche le message qui previent l'utilisateur que le module est désactivé
        echo applyTemplate('nkAlert/noEntrance');
    }
}

function smilies() {
    global $bgcolor3;

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(__('ADD_SMILEY'));
    nkTemplate_addJSFile('media/js/smilies.js');

    echo "<script type=\"text/javascript\">\n"
    . "<!--\n"
    . "\n"
    . "function eff(){\n"
    . "if (opener.document.getElementById('" . $_REQUEST['textarea'] . "').value == '" . _YOURMESS . "')\n"
    . "{\n"
    . "opener.document.getElementById('" . $_REQUEST['textarea'] . "').value='';\n"
    . "}\n"
    . "}\n"
    . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div style=\"text-align: center;\"><big><b>" . _LISTSMILIES . "</b></big></div>\n"
    . "<table width=\"100%\" cellpadding=\"3\" cellspacing=\"0\"><tr><td colspan=\"2\">&nbsp;</td></tr>\n"
    . "<tr style=\"background: $bgcolor3;\"><td align=\"center\"><b>" . _CODE . "</b></td><td align=\"center\"><b>" . _IMAGE . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT code, url, name FROM " . SMILIES_TABLE . " ORDER BY id");
    while (list($code, $url, $name) = nkDB_fetchArray($sql))
    {
        $name = printSecuTags($name);
        $code = printSecuTags($code);

        echo "<tr><td align=\"center\"><a href=\"javascript:eff();PopupinsertAtCaret('" . $_REQUEST['textarea'] . "', ' " . $code . " ', '')\" title=\"" . $name . "\">" . $code . "</a></td>\n"
        . "<td align=\"center\"><a href=\"javascript:eff();PopupinsertAtCaret('" . $_REQUEST['textarea'] . "', ' " . $code . " ')\"><img style=\"border: 0;\" src=\"images/icones/" . $url . "\" alt=\"\" title=\"" . $name . "\" /></a></td></tr>\n";
    } 

    echo "</table><div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"javascript:window.close()\"><b>" . __('CLOSE_WINDOW') . "</b></a></div>";
}

function cesure_href($matches) {
    return '<a href="' . $matches[1] . '" title="' . $matches[1] . '" >['. _TLINK .']</a>';
}

function ajax() {
    global $nuked, $user, $visiteur, $language, $bgcolor1, $bgcolor2;

    $level_admin = admin_mod("Textbox");

    header('Content-type: text/html; charset=iso-8859-1');
    nkTemplate_setPageDesign('none');

    require("modules/Textbox/config.php");

    $active = 2;
    $width = $box_width;
    $height = $box_height;
    $max_chars = $max_string;
    $mess_max = $max_texte;
    $pseudo_max = $max_pseudo;
    $nb_messages = 40;

    $sql = nkDB_execute('SELECT count(id) FROM '.TEXTBOX_TABLE.' ');
    list($index_limit) = nkDB_fetchArray($sql);
    $index_start = $index_limit - $nb_messages;
    $index_start = $index_start < 0 ? 0 : $index_start;

    $sql = nkDB_execute("SELECT id, auteur, ip, texte, date FROM " . TEXTBOX_TABLE . " ORDER BY id ASC LIMIT ".$index_start.", ".$index_limit." ");
    $counterBgColor = 0;
    while (list($id, $auteur, $ip, $texte, $date) = nkDB_fetchArray($sql)) {
        // On coupe le texte si trop long
        if (strlen($texte) > $mess_max) $texte = substr($texte, 0, $mess_max) . '...';

        $date_jour = nkDate($date);

        $block_text = '';

        // On coupe les mots trop longs
        $text = explode(' ', $texte);
        $nbWords = count($text);

        for ($i = 0; $i < $nbWords; $i++) {
            $text[$i] = " " . $text[$i];

            if (strlen($text[$i]) > $max_chars
                && ! preg_match("`http:`i", $text[$i])
                && ! preg_match("`www\.`i", $text[$i])
                && ! preg_match("`@`i", $text[$i])
                && ! preg_match("`ftp\.`i", $text[$i])
            )
                $text[$i] = '<span title="' . $text[$i] . '">' . substr($text[$i], 0, $max_chars) . '...</span>';

            $text[$i] = preg_replace_callback('`((https?|ftp)://\S+)`', 'cesure_href', $text[$i]); 
            $block_text .= $text[$i];
        }

        $texte = nkHtmlEntities($texte, ENT_NOQUOTES);
        $texte = nk_CSS($texte);

        if (strlen($auteur) > $pseudo_max)
        {
            $auteurDisplay = '<span title="' . nk_CSS($auteur) . '">' . nk_CSS(substr($auteur, 0, $pseudo_max)) . '...</span>';
        }
        else
        {
            $auteurDisplay = $auteur;
        }

        $block_text = icon($block_text);

        $sql_aut = nkDB_execute(
            'SELECT U.id, U.country, U.avatar, U.niveau, TM.color
            FROM '. USER_TABLE .' AS U
            LEFT JOIN '. TEAM_RANK_TABLE .' AS TM
            ON TM.id = U.rang
            WHERE U.pseudo = '. nkDB_quote($auteur)
        );

        list($user_id, $country, $avatar, $niveau, $rank_color) = nkDB_fetchArray($sql_aut);
        $test_aut = nkDB_numRows($sql_aut);

        if ($rank_color != '')
            $style = ' style="color:#'. $rank_color .'"';
        else
            $style = '';

        $pays = ($country) ? '<img src="images/flags/' . $country . '" alt="' . $country . '" style="margin-right:2px;"/>' : '';

        $sql_on = nkDB_execute("SELECT user_id FROM " . NBCONNECTE_TABLE . " WHERE username = '" . $auteur . "' ORDER BY date");
        $count_ok = nkDB_numRows($sql_on);

        $online = (isset($user_id) && $count_ok == 1) ? '<div class="nkIconOnline nkIconOnlineGreen" title="Online !"></div>' : '<div class="nkIconOnline nkIconOnlineGrey" title="Offline"></div>';

        if ($counterBgColor == 0) {
            $bg = $bgcolor1;
            $counterBgColor++;
        } else {
            $bg = $bgcolor2;
            $counterBgColor =0;
        }

        $url_auteur = ($test_aut == 1) ? '<a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($auteur) . '"' . $style . ' title="' . $date_jour . '">' . $auteurDisplay . '</a>' : $auteurDisplay;
        $avatarDisplay = ($avatar != '') ? '<img src="' . $avatar . '" class="nkFloatLeft nkShootAvatar nkBorderColor2" />' : '<img src="modules/User/images/noavatar.png" alt="noavatar" class="nkFloatLeft nkShootAvatar nkBorderColor2" />';
        $post_time =strftime("%H:%M:%S", $date);
        $messageAuthor = nkDB_realEscapeString(stripslashes($auteur));

        if ($nuked['textbox_avatar'] == 'on') {
            echo "<div class=\"nkShootboxRow nkBorderColor2\">\n"
            . "" . $avatarDisplay ."\n"
            . "<div>" . $pays . "<strong>" . $url_auteur . "</strong>\n";
            if ($visiteur >= $level_admin) {
                echo "<div class=\"nkFloatRight\">\n"
                . "<a href=\"javascript:deleteShoutboxMsg('" . $messageAuthor . "', '" . $id . "');\">\n"
                . "<div class=\"nkIconOnline nkIconOnlineRed\" title=\"" . __('DELETE_THIS_SHOUTBOX_MESSAGE') . "\"></div>\n"
                . "</a>". $online ."\n"
                . "</div><br /><span class=\"nkShootDate\">" . $date_jour . "<span></div>\n";
            }

            else {
                echo "<div class=\"nkFloatRight\">". $online ."\n"
                . "</div><br /><span class=\"nkShootDate\">" . $date_jour . "<span></div>\n";
            }
            echo "<div><p>" . $block_text . "</p></div></div>\n";
        }
        else {
            echo "<div class=\"nkShootboxTinyRow\">\n"
            . "<div>" . $pays . "&lsaquo;<strong>" . $url_auteur . "</strong>&rsaquo;" . $block_text . "\n";
            if ($visiteur >= $level_admin) {
                echo "<div class=\"nkInlineBlock nkFloatRight\" style=\"margin-right:6px;\">\n"
                . "<a href=\"javascript:deleteShoutboxMsg('" . $messageAuthor . "', '" . $id . "');\">\n"
                . "<div class=\"nkIconOnline nkIconOnlineRed\" title=\"" . __('DELETE_THIS_SHOUTBOX_MESSAGE') . "\"></div>\n"
                . "</a>". $online ."\n"
                . "</div></div></div>\n";
            }

            else {
                echo "<div class=\"nkInlineBlock nkFloatRight\" style=\"margin-right:6px;\">". $online ."</div></div></div>\n";
            }
        }

        echo "<div class=\"nkClear\"></div>\n";
    }
}

function submit() {
    global $visiteur, $user, $user_ip;

    $redirection = $_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : 'index.php';
    $level_access = nivo_mod('Textbox');

    if ($visiteur >= $level_access && $level_access > -1) {
        // Captcha check
        if (initCaptcha() && ! validCaptchaCode())
            return;

        $_POST = array_map('stripslashes', $_POST);

        if ($user) {
            $pseudo = $user[2];
        }
        else {
            $_POST['auteur'] =  utf8_decode($_POST['auteur']);
            $_POST['auteur'] = nkHtmlEntityDecode($_POST['auteur']);
            $_POST['auteur'] = nkHtmlEntities($_POST['auteur'], ENT_QUOTES);
            $_POST['auteur'] = checkNickname($_POST['auteur']);

            if (($error = getCheckNicknameError($_POST['auteur'])) !== false) {
                printNotification(nkHtmlEntities($error), 'error');

                if (! isset($_POST['ajax']))
                    redirect($redirection, 2);

                return;
            }

            $pseudo = $_POST['auteur'];
        }

        if ($visiteur == 0) {
            $dbrLastTextboxMsg = nkDB_selectOne(
                'SELECT ip, date
                FROM '. TEXTBOX_TABLE,
                array('id'), 'DESC', 1
            );
        }

        $date = time();

        $_POST['texte'] = utf8_decode($_POST['texte']);

        if ($visiteur == 0
            && $user_ip == $dbrLastTextboxMsg['ip']
            && $date < ($dbrLastTextboxMsg['date'] + 60)
        ) {
            printNotification(nkHtmlEntities(_TNOFLOOD), 'error');

            if (! isset($_POST['ajax'])) redirect($redirection, 2);
        }
        else if ($_POST['texte'] != '') {
            nkDB_insert(TEXTBOX_TABLE, array(
                'auteur' => $pseudo,
                'ip'     => $user_ip,
                'texte'  => $_POST['texte'],
                'date'   => $date
            ));

            printNotification(nkHtmlEntities(_SHOUTSUCCES), 'success');

            if (! isset($_POST['ajax'])) redirect($redirection, 2);
        }
        else {
            printNotification(nkHtmlEntities(_NOTEXT), 'error');

            if (! isset($_POST['ajax'])) redirect($redirection, 2);
        }
    }
    else {
        printNotification(nkHtmlEntities(__('NO_ENTRANCE')), 'error');

        if (! isset($_POST['ajax'])) redirect($redirection, 2);
    }
}



switch ($GLOBALS['op']) {
    case 'smilies' :
        smilies();
        break;

    case 'submit' :
        if (! isset($_POST['ajax'])) opentable();
        submit();
        if (! isset($_POST['ajax'])) closetable();
        break;

    case 'ajax' :
        ajax();
        break;

    default:
        opentable();
        index();
        closetable();
        break;
}

?>
