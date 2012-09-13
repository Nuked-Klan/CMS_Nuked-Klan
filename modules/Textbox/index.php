<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $user, $nuked, $language;
translate("modules/Textbox/lang/" . $language . ".lang.php");

$level_access = nivo_mod("Textbox");
$level_admin = admin_mod("Textbox");

if ($user)
{
    $visiteur = $user[1];
} 
else
{
    $visiteur = 0;
} 

function index()
{
    global $nuked, $user, $theme, $bgcolor1, $bgcolor2, $bgcolor3, $level_access, $level_admin, $visiteur;

    if ($visiteur >= $level_access && $level_access > -1)
    {
        opentable();

        $nb_mess = $nuked['max_shout'];

        $sql = mysql_query("SELECT id FROM " . TEXTBOX_TABLE);
        $count = mysql_num_rows($sql);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_mess - $nb_mess;

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _SHOUTBOX . "</b></big></div><br />\n";

        if ($count > $nb_mess)
        {
            number($count, $nb_mess, "index.php?file=Textbox");
        } 

        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"3\" cellspacing=\"1\">\n";

        $sql2 = mysql_query("SELECT id, auteur, ip, texte, date FROM " . TEXTBOX_TABLE . " ORDER BY id DESC LIMIT " . $start . ", " . $nb_mess."");
        while (list($mid, $auteur, $ip, $texte, $date) = mysql_fetch_array($sql2))
        {
            $texte = printSecuTags($texte);
            $texte = nk_CSS($texte);

            $texte = ' ' . $texte;
            $texte = preg_replace("#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="\2://\3" onclick="window.open(this.href); return false;">\2://\3</a>', $texte);
            $texte = preg_replace("#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="http://\2.\3" onclick="window.open(this.href); return false;">\2.\3</a>', $texte);
            $texte = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $texte);

            $texte = icon($texte);
            $date = nkDate($date);

            $auteur = nk_CSS($auteur);
        
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

            if ($visiteur >= $level_admin && $level_admin > -1)
            {
                echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function del_shout(pseudo, id)\n"
        . "{\n"
        . "if (confirm('" . _DELETETEXT . " '+pseudo+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=Textbox&page=admin&op=del_shout&mid='+id;}\n"
        . "}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

                $admin = "<div style=\"text-align: right;\"><a href=\"index.php?file=Textbox&amp;page=admin&amp;op=edit_shout&amp;mid=" . $mid . "\"><img style=\"border: 0;\" src=\"images/edition.gif\" alt=\"\" title=\"" . _EDITTHISMESS . "\" /></a>"
        . "&nbsp;<a href=\"javascript:del_shout('" . mysql_real_escape_string(stripslashes($auteur)) . "', '" . $mid . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DELTHISMESS . "\"></a></div>";
             
        $aff_ip = "( $ip )";
            } 
            else
            {
                $admin = "<br />";
                $aff_ip = "&nbsp;";
            } 

            echo "<tr style=\"background: $bgcolor3;\"><td><b>" . $auteur . "</b> " . $aff_ip . "</td></tr>\n"
            . "<tr style=\"background: $bg;\"><td><img src=\"images/posticon.gif\" alt=\"\" />&nbsp;" . $date . "<br /><br />" . $texte . "<br />" . $admin . "</td></tr>\n";
        } 

        if ($count == 0) echo "<tr style=\"background: $bgcolor2;\"><td align=\"center\">" . _NOMESS . "</td></tr>\n";

        echo"</table>";

        if ($count > $nb_mess)
        {
            number($count, $nb_mess, "index.php?file=Textbox");
        } 

        echo "<br /><div style=\"text-align: center;\"><small><i>( " . _THEREIS . "&nbsp;" . $count . "&nbsp;" . _SHOUTINDB . " )</i></small></div><br />\n";

        closetable();
    } 
    else if ($level_access == -1)
    {
        opentable();
        echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
        closetable();
    } 
    else if ($level_access == 1 && $visiteur == 0)
    {
        opentable();
        echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | " 
    . "<a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b></div><br /><br />";
        closetable();
    } 
    else
    {
        opentable();
        echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
        closetable();
    } 
} 

    function smilies()
    {
        global $theme, $bgcolor3, $bgcolor2;

       echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
       . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
       . "<head><title>" . _SMILEY . "</title>\n"
       . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
       . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
       . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" />\n"
         . "<script type=\"text/javascript\" src=\"media/js/smilies.js\"></script></head>\n"
       . "<body style=\"background: " . $bgcolor2 . ";\">\n";

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

        $sql = mysql_query("SELECT code, url, name FROM " . SMILIES_TABLE . " ORDER BY id");
        while (list($code, $url, $name) = mysql_fetch_array($sql))
        {
            $name = printSecuTags($name);
            $code = printSecuTags($code);

            echo " <tr><td align=\"center\"><a href=\"javascript:eff();PopupinsertAtCaret('" . $_REQUEST['textarea'] . "', ' " . $code . " ', '')\" title=\"" . $name . "\">" . $code . "</a></td>\n"
            . "<td align=\"center\"><a href=\"javascript:eff();PopupinsertAtCaret('" . $_REQUEST['textarea'] . "', ' " . $code . " ')\"><img style=\"border: 0;\" src=\"images/icones/" . $url . "\" alt=\"\" title=\"" . $name . "\" /></a></td></tr>\n";
        } 

        echo "</table><div style=\"text-align: center;\"><br /><a href=\"#\" onclick=\"javascript:window.close()\"><b>" . _CLOSEWINDOW . "</b></a></div></body></html>";
    } 
    
    function cesure_href($matches) { 
        return '<a href="' . $matches[1] . '" title="' . $matches[1] . '" >['. _TLINK .']</a>';      
    }     
    
    function ajax() {

        header('Content-type: text/html; charset=iso-8859-1');
        global $nuked,$user,$language, $bgcolor1, $bgcolor2;

        require("modules/Textbox/config.php");

        $visiteur = $user ? $user[1] : 0;

        if ($visiteur >= 2) {
            echo "<script type=\"text/javascript\">\n"
            . "<!--\n"
            . "\n"
            . "function del_shout(pseudo, id)\n"
            . "{\n"
            . "if (confirm('" . _DELETETEXT . " '+pseudo+' ! " . _CONFIRM . "'))\n"
            . "{document.location.href = 'index.php?file=Textbox&page=admin&op=del_shout&mid='+id;}\n"
            . "}\n"
            . "\n"
            . "// -->\n"
            . "</script>\n";
        }

        $active = 2;
        $width = $box_width;
        $height = $box_height;
        $max_chars = $max_string;
        $mess_max = $max_texte;
        $pseudo_max = $max_pseudo;
        $level_admin = admin_mod('Textbox');
        $level_mod = nivo_mod('Textbox');

        $sql = mysql_query("SELECT id, auteur, ip, texte, date FROM " . TEXTBOX_TABLE . " ORDER BY id DESC LIMIT 0, 20");
        while (list($id, $auteur, $ip, $texte, $date) = mysql_fetch_array($sql)) {
            // On coupe le texte si trop long
            if (strlen($texte) > $mess_max) $texte = substr($texte, 0, $mess_max) . '...';

            $date = nkDate($date);

            $block_text = '';

            // On coupe les mots trop longs
            $text = explode(' ', $texte);
            for($i = 0;$i < count($text);$i++) {
                $text[$i] = " " . $text[$i];

                if (strlen($text[$i]) > $max_chars && !preg_match("`http:`i", $text[$i]) && !preg_match("`www\.`i", $text[$i]) && !preg_match("`@`i", $text[$i]) && !preg_match("`ftp\.`i", $text[$i]))
                $text[$i] = '<span title="' . $text[$i] . '">' . substr($text[$i], 0, $max_chars) . '...</span>';

                $text[$i] = preg_replace_callback('`((https?|ftp)://\S+)`', cesure_href,$text[$i]); 
                $block_text .= $text[$i];
            }

            $texte = htmlentities($texte, ENT_NOQUOTES);
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

            $sql_aut = mysql_query("SELECT id FROM " . USER_TABLE . " WHERE pseudo = '" . $auteur . "'");
            list($user_id) = mysql_fetch_array($sql_aut);
            $test_aut = mysql_num_rows($sql_aut);

            $sqlc = mysql_query("SELECT `country` FROM `" . USER_TABLE . "` WHERE pseudo = '" . $auteur . "'");
            list($country) = mysql_fetch_array($sqlc);

            $pays = ($country) ? '<img src="images/flags/' . $country . '" alt="' . $country . '" />' : '';

            $sql_on = mysql_query("SELECT user_id FROM " . NBCONNECTE_TABLE . " WHERE username = '" . $auteur . "' ORDER BY date");
            $count_ok = mysql_num_rows($sql_on);

            $online = (isset($user_id) && $count_ok == 1) ? '<img src="modules/Textbox/images/on.jpg" alt="online" />' : '<img src="modules/Textbox/images/off.jpg" alt="offline" />';

            $sql2 = mysql_query("SELECT niveau FROM " . USER_TABLE . " WHERE pseudo = '" . $auteur . "'");
            list($niveau) = mysql_fetch_array($sql2);

            $coloring = ($niveau >= 2) ? 'fa1200' : '8452bf';

            if($i2 == 0) {
                $bg = $bgcolor1;
                $i2++;
            }
            else {
                $bg = $bgcolor2;
                $i2 =0;
            }

            $url_auteur = ($test_aut == 1) ? '<a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($auteur) . '" style="color: #' . $coloring . '">' . $auteurDisplay . '</a>' : $auteurDisplay;

            echo "<table width=\"100%\" style=\"background: #" . $bg . "\" cellspacing=\"0\" cellpadding=\"0\">\n"
            . "<tr>\n"
            . "<td width=\"95%\"><span style=\"margin-left: 2px;\">" . $pays . "&nbsp;<b>" . $url_auteur . "</b></span></td>\n";
            if ($visiteur >= 2) {
                echo "<td width=\"5%\"><a href=\"javascript:del_shout('" . mysql_real_escape_string(stripslashes($auteur)) . "', '" . $id . "');\"><img style=\"margin-top: 5px; margin-left: 2px;\" src=\"modules/Textbox/images/delete.png\" alt=\"\" title=\"" . _DELTHISMESS . "\" /></a></td>\n";
            }

            echo "<td width=\"5%\" style=\"padding-right: 2px;\">". $online ."</td></tr>\n"
            . "<tr><td>" . $date . "</td></tr><tr><td>" . $block_text . "<br />&nbsp;</td></tr>\n"
            . "</table>\n";

        }
    }

switch ($_REQUEST['op'])
{

    case"smilies":
        smilies();
        break;

    case"index":
        index();
        break;
        
    case"ajax":
        ajax();
        break;

    default:
        index();
        break;
} 


?>
