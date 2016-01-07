<?php
/**
 * menu.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', SUPER_ADMINISTRATOR_ACCESS))
    return;


function index()
{
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _MENUADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/menu.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    printNotification(_TO_CREATE_A_MENU);

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 35%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _BLOCK . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _LEVEL . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td></tr>\n";

    $sql = mysql_query("SELECT  bid, active, position, titre, nivo FROM " . BLOCK_TABLE . " WHERE type = 'menu'");
    while (list($bid, $activ, $position, $titre, $nivo) = mysql_fetch_array($sql))
    {
        $titre = nkHtmlEntities($titre);

        if ($activ == 1) $active = _LEFT;
        else if ($activ == 2) $active = _RIGHT;
        else $active = _OFF;


        echo "<tr>\n"
        . "<td style=\"width: 35%;\">" . $titre . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $active . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\">" . $position . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\">" . $nivo . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=menu&amp;op=edit_menu&amp;bid=" . $bid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a></td></tr>\n";

    }
    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin&page=block&op=add_block\">" . _CREATEBLOCK . "</a><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function edit_menu($bid)
{
    global $nuked, $user, $language;

    $sql = mysql_query("SELECT titre, content FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
    list($titre, $content) = mysql_fetch_array($sql);
    $titre = nkHtmlEntities($titre);

    echo "<script type=\"text/javascript\">\n"
    ."<!--\n"
    ."\n"
    ."function setCheckboxes(checkbox, nbcheck, do_check)\n"
    ."{\n"
    ."for (var i = 0; i < nbcheck; i++)\n"
    ."{\n"
    ."cbox = checkbox + i;\n"
    ."document.getElementById(cbox).checked = do_check;\n"
    ."}\n"
    ."return true;\n"
    ."}\n"
    ."\n"
    . "// -->\n"
    . "</script>\n";

    $link = explode('NEWLINE', $content);
    $count = count($link);
    $r = 0;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _MENUADMIN . " : " . $titre . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/menu.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=menu&amp;op=send_line\">\n"
    . "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 5%;\" align=\"center\"><b>&lt; # &gt;</b></td>\n"
    . "<td style=\"width: 5%;\" align=\"center\"><b>" . _DELBOX . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _URL . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _COMMENT . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _NEWPAGE . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _LEVEL . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td></tr>\n";

    if (!empty($content))
    {
        foreach ($link as $link)
        {
            list($url, $title, $comment, $niveau, $blank) = explode('|', $link);
            $title = strip_tags($title);
            $title = nkHtmlEntities($title);

            if ($comment !="" && strlen($comment) > 15)
            {
                $comment = nkHtmlEntities(substr($comment, 0, 15)) . "...";
            }
    else if ($comment != "")
    {
                $comment = nkHtmlEntities($comment);
    }
    else
    {
                $comment = _NOCOMLINK;
    }

            if ($blank == 1)
            {
                $checked = __('YES');
            }
            else
            {
                $checked = __('NO');
            }

            if ($url != "" && substr($url, 0, 1) == "[")
            {
                $url = str_replace("[", "", $url);
                $url = str_replace("]", "", $url);
                $type = _MODULE . " : <i>" . $url ."</i>";
            }
            else if ($url != "")
    {
                $type = _LINK;
    }
            else
    {
                $type = _TITLE;
    }


            echo "<tr><td style=\"width: 5%;\" align=\"center\">";

            if ($r < ($count - 1))
            {
                echo "<a href=\"index.php?file=Admin&amp;page=menu&amp;op=send_line&amp;bid=" . $bid . "&amp;lid=" . $r . "&amp;line=" . ($r + 2) . "\" title=\"" . _DOWN . "\">&lt;</a>";
            }
    else
    {
                echo "&nbsp;&nbsp;";
    }

            echo "&nbsp;" . $r . "&nbsp;";

            if ($r > 0)
            {
                echo "<a href=\"index.php?file=Admin&amp;page=menu&amp;op=send_line&amp;bid=" . $bid . "&amp;lid=" . $r . "&amp;line=" . ($r - 1) . "\"  title=\"" . _UP . "\">&gt;</a>";
            }
    else
    {
                echo "&nbsp;&nbsp;";
    }

            echo "</td><td style=\"width: 5%;\" align=\"center\"><input id=\"box" . $r . "\" class=\"checkbox\" type=\"checkbox\" name=\"cid[" . $r . "]\" value=\"1\" /></td>\n"
            . "<td style=\"width: 25%;\">&nbsp;" . $title . "</td>\n"
            . "<td style=\"width: 20%;\">&nbsp;" . $type . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $comment . "</td>\n"
            . "<td style=\"width: 10%;\" align=\"center\">" . $checked . "</td>\n"
            . "<td style=\"width: 10%;\" align=\"center\">" . $niveau . "</td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=menu&amp;op=edit_line&amp;bid=" . $bid . "&amp;lid=" . $r . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a></td></tr>\n";

            $r++;
        }

        if ($r > 0)
        {

            echo "</table><table width=\"100%\"><tr><td style=\"width: 7%;\">&nbsp;</td><td><img src=\"images/flech_coch.gif\" alt=\"\" />\n"
            . "<a class=\"buttonLink\" href=\"#\" onclick=\"setCheckboxes('box', '" . $r . "', true);\">" . _CHECKALL . "</a>"
            . "<a class=\"buttonLink\" href=\"#\" onclick=\"setCheckboxes('box', '" . $r . "', false);\">" . _UNCHECKALL . "</a></td></tr>\n";
        }
    }

    echo "</table><div style=\"text-align: center;\"><br /><input type=\"hidden\" name=\"bid\" value=\"" . $bid . "\" />\n"
    . "<input class=\"button\" type=\"button\" value=\"" . _DEL . "\" onclick=\"if (confirm('" . _SURDELLINE . "')) submit();\" />\n"
    . "&nbsp;<input class=\"button\" type=\"button\" value=\"" . _ADD . "\" onclick=\"document.location='index.php?file=Admin&amp;page=menu&amp;op=edit_line&amp;bid=" . $bid . "'\" /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=menu\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>";
}

function edit_line($bid, $lid)
{
    global $nuked, $user, $language;

    $sql = mysql_query("SELECT titre, content FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
    list($titre, $content) = mysql_fetch_array($sql);
    $titre = strip_tags($titre);
    $titre = nkHtmlEntities($titre);

    if ($content) $link = explode('NEWLINE', $content);
    list($module, $title, $comment, $niveau, $blank) = explode('|', $link[$lid]);

if ($lid != "") $selected0 = "";
else $selected0 = "selected=\"selected\"";

    if ($blank == true) $checked = true; else $checked = false;
    if (preg_match("`<b>`i", $title)) $chk1 = true; else $chk1 = false;
    if (preg_match("`<i>`i", $title)) $chk2 = true; else $chk2 = false;
    if (preg_match("`underline`i", $title)) $chk3 = true; else $chk3 = false;

    $puce = '';
    $color = '';
    if (preg_match("`<img src=`i", $title))
    {
        preg_match("/^(<img src=\")?([^\"]+)/i", $title, $matches);
        $img = $matches[0];
        $puce = strrchr($img, '/');
        $puce = substr($puce, 1);
        $puce = nkHtmlEntities($puce);
    }

    if (preg_match("`<span style=`i", $title) && preg_match("`color:`i", $title))
    {
        $test = strstr($title, '<span');
        preg_match("/^(<span style=\")?([^;\"]+)/i", $test, $matches);
        $font = $matches[0];
        $color = strrchr($font, '"');
        $color = substr($color, 1);
        $color= str_replace("color: #", "", $color);
        $color = nkHtmlEntities($color);
    }

    $title = strip_tags($title);
    $title = nkHtmlEntities($title);

    if (substr($module, 0, 1) == "[" || $module == "") $url = "http://";
    else $url = $module;

    nkTemplate_addJSFile('modules/Admin/jscolor/jscolor.js');

    echo "<script type=\"text/javascript\">\n"
        ."<!--\n"
        ."\n"
        . "function update_img(newimage)\n"
        . "{\n"
        . "document.getElementById('img_puce').src = 'images/puces/' + newimage;\n"
        . "}\n"
        ."\n"
        . "// -->\n"
        . "</script>\n";


    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _EDITLINE . " : " . $title . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/menu.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n"
    . "<form method=\"post\" action=\"index.php?file=Admin&amp;page=menu&amp;op=send_line&amp;bid=" . $bid . "&amp;lid=" . $lid . "\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr><td colspan=\"3\">&nbsp;</td></tr>\n"
    . "<tr><td colspan=\"2\" width=\"20%\" ><h5>" . _TITLE . " :</h5></td><td><input type=\"text\" name=\"title\" value=\"" . $title . "\" size=\"40\" /></td></tr>\n"
    . "<tr><td colspan=\"3\">&nbsp;</td></tr>\n"        
    . "<tr><td colspan=\"3\"><h5>" . _STYLETITLE . " :</h5></td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _COLOR . " :</strong></td><td><input id=\"couleur\" class=\"color\"type=\"text\" name=\"color\" style=\"width:60px;\" value=\"" . $color . "\" /></td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _BOLD . " :</strong></td><td>\n";

    checkboxButton('b', 'b', $chk1, false);

    echo "</td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _ITAL . " :</strong></td><td>\n";

    checkboxButton('i', 'i', $chk2, false);

    echo "</td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _UNDERLINE . " :</strong></td><td>\n";

    checkboxButton('u', 'u', $chk3, false);

    echo "</td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _PUCE . " :</strong></td>\n"
    . "<td><select name=\"puce\" onchange=\"update_img(this.options[selectedIndex].value);\">\n";

    list_puce($puce);

    if ($puce == "") $puce = "none.gif";

    echo "</select>\n"
    . "<img style=\"margin-left:10px;\" id=\"img_puce\" src=\"images/puces/" . $puce . "\" alt=\"\" /></td></tr>\n"
    . "<tr><td colspan=\"3\">&nbsp;</td></tr>\n"
    . "<tr><td colspan=\"3\"><h5>" . _URL . " :</h5></td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _LINK . " :</strong></td><td><input type=\"text\" name=\"url\" value=\"" . $url . "\" size=\"40\" /></td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _MODULE . " :</strong></td><td><select name=\"module\">\n";

    list_mod($module);

    echo"</select></td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _COMMENT . " :</strong></td><td><input type=\"text\" name=\"comment\" value=\"" . $comment . "\" size=\"40\" /></td></tr>\n"
    . "<tr><td colspan=\"3\">&nbsp;</td></tr>\n"
    . "<tr><td colspan=\"3\"><h5>" . _ACCESSIBILITY . " :</h5></td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _POSITION . " :</strong></td><td><select name=\"line\">\n";

    for($z = 0;$z < count($link);$z++)
    {
        if ($lid == $z) $selected = "selected=\"selected\"";
        else $selected = "";

        list($url2, $title2, $comment2, $niveau2, $blank2) = explode('|', $link[$z]);
        $title2 = strip_tags($title2);
        $title2 = nkHtmlEntities($title2);

        echo "<option value=\"" . $z . "\" " . $selected . ">" . $z . "&nbsp;&nbsp;(" . $title2 . ")</option>\n";
    }

    echo "<option value=\"" . $z . "\"  " . $selected0 . ">" . $z . "&nbsp;&nbsp;" . _LAST . " ...</option>\n"
    . "</select></td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _LEVEL . " :</strong></td><td><select name=\"niveau\">\n";

    for($j = 0;$j < 10;$j++)
    {
        if ($niveau == $j) $selected1 = "selected=\"selected\"";
        else $selected1 = "";

        echo "<option value=\"" . $j . "\" " . $selected1 . ">" . $j . "</option>\n";
    }

    echo "</select></td></tr>\n"
    . "<tr><td colspan=\"2\" style=\"text-align:right;\"><strong>" . _NEWPAGE . " :</strong></td><td>\n";

    checkboxButton('blank', 'blank', $checked, false);

    echo "</td></tr>\n"
    . "<tr><td colspan=\"3\">&nbsp;</td></tr>\n"
    . "</table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=menu&amp;op=edit_menu&amp;bid=" . $bid . "\">" . __('BACK') . "</a></div>\n"
    . "</form><br /></div></div>\n";


}

function send_line($bid, $lid)
{
    global $nuked, $user, $b, $i, $u, $puce, $cid;

    $sql = mysql_query("SELECT titre, content FROM " . BLOCK_TABLE . " WHERE bid = '" . $_REQUEST['bid'] . "'");
    list($titre, $content) = mysql_fetch_array($sql);

    if ($_REQUEST['niveau'] != "")
    {
        if ($_REQUEST['b'] == true) $_REQUEST['title'] = "<b>" . $_REQUEST['title'] . "</b>";
        if ($_REQUEST['i'] == true) $_REQUEST['title'] = "<i>" . $_REQUEST['title'] . "</i>";
        if ($_REQUEST['u'] == true) $_REQUEST['title'] = "<span style=\"text-decoration: underline;\">" . $_REQUEST['title'] . "</span>";
        if ($_REQUEST['color'] != "") $_REQUEST['title'] = "<span style=\"color: #" . $_REQUEST['color']. ";\">" . $_REQUEST['title'] . "</span>";

        if ($_REQUEST['puce'] != "" && $_REQUEST['puce'] != "none.gif") $_REQUEST['title'] = "<img src=\"images/puces/" . $_REQUEST['puce'] . "\" style=\"border: 0;\" alt=\"\" />" . $_REQUEST['title'];

        if ($content) $link = explode('NEWLINE', $content);
        if ($_REQUEST['url'] == "http://" || $_REQUEST['url'] == "") $_REQUEST['url'] = $_REQUEST['module'];

        $new_line = $_REQUEST['url'] . "|" . $_REQUEST['title'] . "|" . $_REQUEST['comment'] . "|" . $_REQUEST['niveau'] . "|" . $_REQUEST['blank'] . "|";
        $count = count($link);
        if ($lid == "") $lid = $count;
        $link[$lid] = $new_line;
        if ($link) $content = implode('NEWLINE', $link);
    }

    if ($_REQUEST['cid'])
    {
        $link = explode('NEWLINE', $content);
        $i = 0;
        foreach($link AS $link)
        {
            if (!$_REQUEST['cid'][$i]) $link2[] = $link;
            $i++;
        }
        if ($link2) $content = implode('NEWLINE', $link2);
        else $content = "";
    }

    if ($_REQUEST['line'] != $lid) $content = move($lid, $_REQUEST['line'], $content);

    $content = mysql_real_escape_string(stripslashes($content));

    $sql = mysql_query("UPDATE " . BLOCK_TABLE . " SET content = '" . $content . "' WHERE bid = '" . $_REQUEST['bid'] . "'");

    saveUserAction(_ACTIONMODIFMENU .': '. $titre);

    if ($_REQUEST['cid'])
    {
        printNotification(_LINEDELETED, 'success');
    }
    else
    {
        printNotification(_LINEMODIFIED, 'success');
    }

    setPreview('index.php', 'index.php?file=Admin&page=menu&op=edit_menu&bid='. $_REQUEST['bid']);
}

function move($start, $end, $content)
{
    $link = explode('NEWLINE', $content);
    $save_line = $link[$start];

    if ($start > $end)
    {
        for($i = $start;$i > $end;$i--)
        {
            $link[$i] = $link[$i-1];
        }
        $link[$end] = $save_line;
    }

    else if ($start < $end)
    {
        for($i = $start;$i < $end-1;$i++)
        {
            $link[$i] = $link[$i + 1];
        }
        $link[$end-1] = $save_line;
    }

    $content = implode('NEWLINE', $link);
    return $content;
}

function list_mod($module)
{
    global $nuked, $user, $language;

    echo "<option value=\"\">-- " . _NONE . " --</option>\n";

    $modules = array();
    $handle = opendir("modules");
    while (false !== ($mod = readdir($handle)))
    {
        if ($mod != "." && $mod != ".." && $mod != "404" && $mod != "index.html" && $mod != "Comment" && $mod != "Vote")
        {
            $rep = $mod;
            $umod = strtoupper($mod);
            $modname = _NAME . $umod;

            if (defined($modname)) $modname = constant($modname);
            else $modname = $mod;


            array_push($modules, $modname . "|" . $mod);
        }
    }

    closedir($handle);
    natcasesort($modules);

foreach($modules as $value)
{
        $temp = explode("|", $value);
        $tmp = "[" . $temp[1] . "]";

        if ($module == $tmp)
        {
    $checked = "selected=\"selected\"";
        }
        else
        {
    $checked = "";
        }

        $sql = mysql_query("SELECT nom FROM " . MODULES_TABLE . " WHERE nom = '" . $temp[1] . "' AND admin = -1 AND niveau = -1");
        $count = mysql_num_rows($sql);

        if (is_file("modules/" . $temp[1] . "/index.php") && $count == 0)
        {
    echo "<option value=\"" . $tmp . "\" " . $checked . ">" . $temp[0] . "</option>\n";
        }
}
}

function list_puce($spuce)
{
    echo "<option value=\"none.gif\">-- " . _NONE . " --</option>\n";

    $path = "images/puces/";
    $handle = opendir($path);
    while (false !== ($puce = readdir($handle)))
    {
        if ($puce != "." && $puce != ".." && $puce != "Thumbs.db" && $puce != "index.html" && $puce != "none.gif")
        {
            if (is_file($path . $puce))
            {
                if ($puce == $spuce) $selected = "selected=\"selected\"";
                else $selected = "";

                echo "<option value=\"" . $puce . "\" " . $selected . ">" . $puce . "</option>\n";
            }
        }
    }
}


switch ($_REQUEST['op']) {
    case "index":
        index();
        break;

    case "edit_menu":
        edit_menu($_REQUEST['bid']);
        break;

    case "edit_line":
        edit_line($_REQUEST['bid'], $_REQUEST['lid']);
        break;

    case "send_line":
        send_line($_REQUEST['bid'], $_REQUEST['lid']);
        break;

    case "move":
        move($_REQUEST['start'], $_REQUEST['end'], $_REQUEST['content']);
        break;

    default:
        index();
        break;
}

?>