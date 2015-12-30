<?php
/**
 * admin.php
 *
 * Backend of Page module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Page'))
    return;


function main()
{
global $nuked, $language, $bgcolor1, $bgcolor2, $bgcolor3;

    echo"<script type=\"text/javascript\">\n"
."<!--\n"
."\n"
. "function del_page(titre, id)\n"
. "{\n"
. "if (confirm('" . _DELETEPAGE . " '+titre+' ! " . _CONFIRM . "'))\n"
. "{document.location.href = 'index.php?file=Page&page=admin&op=del&page_id='+id;}\n"
. "}\n"
    . "\n"
. "// -->\n"
. "</script>\n";
    
    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>Gestion des Pages</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Page.html\" rel=\"modal\">\n"
. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
. "</div></div>\n"
. "<div class=\"tab-content\" id=\"tab2\">\n";

nkAdminMenu(1);

echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
. "<tr>\n"
. "<td style=\"width: 30%;\" align=\"center\"><b>" . _PAGENAME . "</b></td>\n"
. "<td style=\"width: 30%;\" align=\"center\"><b>" . _PAGEFILE . "</b></td>\n"
. "<td style=\"width: 10%;text-align: center;\"><b>" . _PAGETYPE . "</b></td>\n"
. "<td style=\"width: 15%;text-align: center;\"><b>" . _EDIT . "</b></td>\n"
. "<td style=\"width: 15%;text-align: center;\"><b>" . _DEL . "</b></td></tr>\n";

$i = 0;
$sql = mysql_query("SELECT id, titre, url, type FROM " . PAGE_TABLE . " ORDER BY titre");
$nb_page = mysql_num_rows($sql);

while (list($page_id, $titre, $url, $type) = mysql_fetch_array($sql))
{ 
    if ($url != "") $pagename = $url;
    else $pagename = _NOFILE;

        if ($i == 0)
        {
            $bg = $bgcolor2;
            $i++;
        } 
        else
        {
            $bg = $bgcolor1;
            $i = 0;
        } 

    echo "<tr>\n"
    . "<td style=\"width: 30%;\">&nbsp;<a href=\"index.php?file=Page&amp;name=" . $titre . "\" onclick=\"window.open(this.href); return false;\">" . $titre . "</a></td>\n"
    . "<td style=\"width: 30%;\"align=\"center\">" . $pagename . "</td>\n"
    . "<td style=\"width: 10%;text-align: center;\">" . $type . "</td>\n"
    . "<td style=\"width: 15%;text-align: center;\"><a href=\"index.php?file=Page&amp;page=admin&amp;op=edit&amp;page_id=" . $page_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISPAGE . "\" /></a></td>\n"
    . "<td style=\"width: 15%;text-align: center;\"><a href=\"javascript:del_page('" . addslashes($titre) . "','" . $page_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISPAGE . "\" /></a></td></tr>\n";
}

if ($nb_page == 0) echo"<tr><td colspan=\"5\" align=\"center\">" . _NOPAGE . "</td></tr>\n";

echo "</table><br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . _BACK . "</a></div><br /></div></div>";
}

function add()
{
    global $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>Ajouter une Page</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Page.html\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(2);

    echo "<form method=\"post\" action=\"index.php?file=Page&amp;page=admin&amp;op=do_add\" enctype=\"multipart/form-data\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
    . "<tr><td><b>" . _PAGENAME . " : </b> <input type=\"text\" name=\"titre\" maxlength=\"50\" size=\"30\" /><span style=\"margin-left:30px;\"><b>" . _SHOWTITLE . " :</b>\n";

    checkboxButton('show_title', 'show_title', $show_title, false);

    echo "</span></td></tr>\n"
    . "<tr><td style=\"vertical-align:middle\"><b>" . _PAGETYPE . " :</b>\n"
    . "<select name=\"type\" onchange=\"checkType(this.options[this.selectedIndex].value, 'add');\"><option value=\"html\">HTML</option><option value=\"php\">PHP</option></select></td></tr>"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td style=\"vertical-align:middle\">\n";

    printNotification(_NOTIFIPAGELEVEL, 'warning');

    echo "<b>" . _PAGELEVEL ." :</b> <select name=\"niveau\">\n" 
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select>\n"
    . "<span style=\"margin-left:30px;\"><b>" . _MEMBERSAUTORIZ ." :</b> <select style=\"vertical-align:middle\" multiple=\"multiple\" name=\"members[]\">\n";
    
    $sql_list = mysql_query("SELECT id, pseudo FROM " . USER_TABLE . " ORDER BY pseudo");
    
    while(list($mid, $pseudo) = mysql_fetch_array($sql_list))
    {
        echo '<option value="' . $mid . '">' . $pseudo . '</option>\n';
    }
    echo "</select></span></td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><big><b>" . _CONTENT . " :</b></big></td></tr>\n"
    . "<tr><td align=\"center\"><textarea class=\"editor\" id=\"contents\" name=\"content\" cols=\"85\" rows=\"20\"></textarea></td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><b>"._PAGEFILE." :</b> <select name=\"url\"><option value=\"\">". _NOFILE ."</option><option value=\"\">* HTML</option>\n";
    
    $rep = Array();
    $handle = @opendir("modules/Page/html/");
    while (false !== ($f = readdir($handle)))
    {
        if ($f != ".." && $f != ".")
        {
            $rep[] = $f;
        }
    }

    closedir($handle);
    sort ($rep);
    reset ($rep);

    while (list ($key, $filename) = each ($rep)) 
    {
            echo "<option value=\"" . $filename . "\">&nbsp;&nbsp;&nbsp;" . $filename . "</option>\n";
    } 

    echo "<option value=\"\">* PHP</option>\n";

    $rep2 = Array();
    $handle2 = @opendir("modules/Page/php/");
    while (false !== ($f2 = readdir($handle2)))
    {
        if ($f2 != ".." && $f2 != ".")
        {
            $rep2[] = $f2;
        }
    }

    closedir($handle2);
    sort ($rep2);
    reset ($rep2);

    while (list ($key2, $filename2) = each ($rep2)) 
    {
            echo "<option value=\"" . $filename2 . "\">&nbsp;&nbsp;&nbsp;" . $filename2 . "</option>\n";
    } 

    echo "</select></td></tr>\n"
    . "<tr><td><b>" . _UPLOADPAGE . " : </b><input type=\"file\" size=\"40\" name=\"pagefile\" /></td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><b>" . _ADDMENU . " :</b> <select name=\"menu\"><option value=\"\">". _NOFILE ."</option>\n";
    

    $sql_menu = mysql_query("SELECT  bid, titre FROM " . BLOCK_TABLE . " WHERE type = 'menu'");
    while (list($bid, $menu) = mysql_fetch_array($sql_menu))
    {
        echo "<option value=\"" . $bid . "\">" . $menu . "</option>\n";
    }

    echo "</select></td></tr>";
    echo "<tr><td>&nbsp;</td></tr>\n"
    . "</table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _ADDTHISPAGE . "\" /><a class=\"buttonLink\" href=\"index.php?file=Page&amp;page=admin\">"._BACK."</a></div></form><br /></div></div>\n";
}

function do_add($titre, $type, $niveau, $content, $url, $pagefile, $menu, $show_title, $members)
{
    global $nuked;

    if (isset($members) AND is_array($members))
    {
        foreach($members AS $users)
        {
            $userslist .= $users . '|';
        }
    }
    
    if ($_FILES['pagefile']['name'] != "")
    {
        $temp_page = trim(@fread(@fopen($_FILES['pagefile']['tmp_name'], 'r'), $_FILES['pagefile']['size']));
        $a = "¿¡¬√ƒ≈‡·‚„‰Â“”‘’÷ÿÚÛÙıˆ¯»… ÀËÈÍÎ«ÁÃÕŒœÏÌÓÔŸ⁄€‹˘˙˚¸ˇ—Ò";
        $b = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
        $filename = str_replace(" ", "_", $_FILES['pagefile']['name']);
        $filename = str_replace("'", "_", $filename);
        $filename = str_replace("\"", "_", $filename);
        $filename = strtr($filename, $a, $b);
        $filename = strtolower($filename);

        $f = explode(".", $filename);
        $end = count($f) - 1;
        $ext = $f[$end];

        if ($ext == "htm") $ext = "html";

        if ($ext == $type)
        {
            $url_file = "modules/Page/" . $type . "/" . $filename;
            if (! move_uploaded_file($_FILES['pagefile']['tmp_name'], $url_file)) {
                echo "<br /><br /><div style=\"text-align: center;\"><b>Upload page failed !!!</b></div><br /><br />";
                return;
            }
        }
        else
        {
            echo "<div class=\"notification error png_bg\">\n"
                . "<div>\n"
                . "" . _BADFILEFORMAT . ""
                . "</div>\n"
                . "</div>\n";
            redirect("index.php?file=Page&page=admin&op=add", 5);
            closetable();
            return;
        }
    }
    else if ($url != "" && !ereg("." . $type, $url))// TODO : ereg deprecated
    {
            echo "<div class=\"notification error png_bg\">\n"
            . "<div>\n"
            . "" . _BADFILEFORMAT . ""
            . "</div>\n"
            . "</div>\n";
            redirect("index.php?file=Page&page=admin&op=add", 5);
            closetable();
            return;
    }
    else
    {
            $filename = $url;
    }

    $content = html_entity_decode($content);
    $content = mysql_real_escape_string(stripslashes($content));
    $a1 = "¿¡¬√ƒ≈‡·‚„‰Â“”‘’÷ÿÚÛÙıˆ¯»… ÀËÈÍÎ«ÁÃÕŒœÏÌÓÔŸ⁄€‹˘˙˚¸ˇ—Ò";
    $b1 = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
    $title = str_replace(" ", "_", $titre);
    $title = str_replace("'", "_", $title);
    $title = str_replace("\"", "_", $title);
    $title = strtr($title, $a1, $b1);
    
    $show_title = (isset($show_title)) ? 1 : 0;

    $sql = mysql_query("INSERT INTO " . PAGE_TABLE . " ( `id` , `niveau` , `titre` , `content` , `url` , `type` , `show_title` , `members` ) VALUES ( '', '" . $niveau . "' , '" . $title . "' , '" . $content . "' , '" . $filename . "' , '" . $type . "' , '" . $show_title . "' , '" . $userslist . "' )");

    if ($menu != "")
    {
            $sql_menu = mysql_query("SELECT content FROM " . BLOCK_TABLE . " WHERE bid = '" . $menu . "'");
            list($content) = mysql_fetch_array($sql_menu);
            $content = stripslashes($content);
            $url_page = "index.php?file=Page&name=" . $title;

            $link = explode('NEWLINE', $content);
            $new_line = $url_page . "|" . $title . "||||";
            $count = count($link);
            $link[$count] = $new_line;

            $content = implode('NEWLINE', $link);
            $content = addslashes($content);
            $sql = mysql_query("UPDATE " . BLOCK_TABLE . " SET content = '" . $content . "' WHERE bid = '" . $menu . "'");

            $url_redirect = "index.php?file=Admin&page=menu&op=edit_line&bid=" . $menu . "&lid=" . $count;
    }
    else
    {
            $url_redirect = "index.php?file=Page&page=admin";
    }

    echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _PAGEADD . "\n"
        . "</div>\n"
        . "</div>\n";
    redirect($url_redirect, 2);
}

function edit($page_id)
{
    global $nuked, $language;

    $sql = mysql_query("SELECT niveau, titre, content, url, type, show_title, members FROM " . PAGE_TABLE . " WHERE id = '" . $page_id . "'");
    list($niveau, $titre, $content, $url, $type, $show_title, $members) = mysql_fetch_array($sql);
    $content = stripslashes($content);

    if ($type == "html") $selected1 = "selected=\"selected\"";	 
    else $selected1 = "";

    if ($type == "php") $selected2 = "selected=\"selected\""; 
    else $selected2 = "";

    if ($show_title == 1) $checked_show = true;
    
        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>Editer une Page</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Page.html\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">\n"	
    . "<form method=\"post\" action=\"index.php?file=Page&amp;page=admin&amp;op=do_edit\" enctype=\"multipart/form-data\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
    . "<tr><td><b>" . _PAGENAME . " : </b> <input type=\"text\" name=\"titre\" maxlength=\"50\" size=\"30\" value=\"" . $titre . "\" /><span style=\"margin-left:30px;\"><b>" . _SHOWTITLE . " :</b>\n";

    checkboxButton('show_title', 'show_title', $checked_show, false);

    echo "</span></td></tr>\n"
    . "<tr><td style=\"vertical-align:middle\"><b>" . _PAGETYPE . " :</b>\n"
    . "<select name=\"type\" onchange=\"checkType(this.options[this.selectedIndex].value, 'edit', " . $page_id . ");\"><option value=\"html\" " . $selected1 . ">HTML</option><option value=\"php\" " . $selected2 . ">PHP</option></select>&nbsp;</td></tr>"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td style=\"vertical-align:middle\">\n";

    printNotification(_NOTIFIPAGELEVEL, 'warning');

    echo "<b>" . _PAGELEVEL ." :</b> <select name=\"niveau\"><option>" . $niveau . "</option>\n" 
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    . "<option>8</option>\n"
    . "<option>9</option></select>\n"
    . "<span style=\"margin-left:30px;\"><b>" . _MEMBERSAUTORIZ ." :</b> <select style=\"vertical-align:middle\" multiple=\"multiple\" name=\"members[]\">\n";
    
    $user_array = explode('|', $members);
    
    $sql_list = mysql_query("SELECT id, pseudo FROM " . USER_TABLE . " ORDER BY pseudo");		
    while(list($mid, $pseudo) = mysql_fetch_array($sql_list))
    {
        $sel = (in_array($mid, $user_array)) ? 'selected="selected"' : '';
        echo '<option value="' . $mid . '" ' . $sel . '>' . $pseudo . '</option>\n';
    }
    echo "</select></span></td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><big><b>" . _CONTENT . " :</b></big></td></tr>\n"
    . "<tr><td align=\"center\"><textarea class=\"editor\" id=\"contents\" name=\"content\" cols=\"85\" rows=\"20\">" . $content . "</textarea></td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><b>"._PAGEFILE." :</b> <select name=\"url\"><option value=\"\">". _NOFILE ."</option><option value=\"\">* HTML</option>\n";
    
    $rep = Array();
    $handle = @opendir("modules/Page/html/");
    while (false !== ($f = readdir($handle)))
    {
        if ($f != ".." && $f != ".")
        {
            $rep[] = $f;
        }
    }

    closedir($handle);
    sort ($rep);
    reset ($rep);

    while (list ($key, $filename) = each ($rep)) 
    {
        if ($filename == $url)  $selected3 = "selected=\"selected\""; else $selected3 = "";
        echo "<option value=\"" . $filename . "\" " . $selected3 . ">&nbsp;&nbsp;&nbsp;" . $filename . "</option>\n";
    } 

    echo "<option value=\"\">* PHP</option>\n";

    $rep2 = Array();
    $handle2 = @opendir("modules/Page/php/");
    while (false !== ($f2 = readdir($handle2)))
    {
        if ($f2 != ".." && $f2 != ".")
        {
            $rep2[] = $f2;
        }
    }

    closedir($handle2);
    sort ($rep2);
    reset ($rep2);

    while (list ($key2, $filename2) = each ($rep2)) 
    {
        if ($filename2 == $url)  $selected4 = "selected=\"selected\""; else $selected4 = "";		
        echo "<option value=\"" . $filename2 . "\" " . $selected4 . ">&nbsp;&nbsp;&nbsp;" . $filename2 . "</option>\n";
    } 

    echo "</select></td></tr>\n"
    . "<tr><td><b>" . _UPLOADPAGE . " : </b><input type=\"file\" size=\"40\" name=\"pagefile\" /></td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><b>" . _ADDMENU . " :</b> <select name=\"menu\"><option value=\"\">". _NOFILE ."</option>\n";
    
    $sql_menu = mysql_query("SELECT  bid, titre FROM " . BLOCK_TABLE . " WHERE type = 'menu'");
    while (list($bid, $menu) = mysql_fetch_array($sql_menu))
    {
        echo "<option value=\"" . $bid . "\">" . $menu . "</option>\n";
    }

    echo "</select></td></tr>\n"
    . "<tr><td>&nbsp;<input type=\"hidden\" name=\"page_id\" value=\"" . $page_id . "\" /></td></tr>\n"
    . "</table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISPAGE . "\" /><a class=\"buttonLink\" href=\"index.php?file=Page&amp;page=admin\">"._BACK."</a></div></form><br /></div></div>\n";
}

function do_edit($page_id, $titre, $type, $niveau, $content, $url, $pagefile, $menu, $show_title, $members)
{
    global $nuked;

    if (isset($members) AND is_array($members))
    {
        foreach($members AS $users)
        {
            $userslist .= $users . '|';
        }
    }
    
    if ($_FILES['pagefile']['name'] != "")
    {
        $temp_page = trim(@fread(@fopen($_FILES['pagefile']['tmp_name'], 'r'), $_FILES['pagefile']['size']));
        $a = "¿¡¬√ƒ≈‡·‚„‰Â“”‘’÷ÿÚÛÙıˆ¯»… ÀËÈÍÎ«ÁÃÕŒœÏÌÓÔŸ⁄€‹˘˙˚¸ˇ—Ò";
        $b = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
        $filename = str_replace(" ", "_", $_FILES['pagefile']['name']);
        $filename = str_replace("'", "_", $filename);
        $filename = str_replace("\"", "_", $filename);
        $filename = strtr($filename, $a, $b);
        $filename = strtolower($filename);

        $f = explode(".", $filename);
        $end = count($f) - 1;
        $ext = $f[$end];

        if ($ext == "htm") $ext = "html";

        if ($ext == $type)
        {
            $url_file = "modules/Page/" . $type . "/" . $filename;
            if (! move_uploaded_file($_FILES['pagefile']['tmp_name'], $url_file)) {
                echo "<br /><br /><div style=\"text-align: center;\"><b>Upload page failed !!!</b></div><br /><br />";
                return;
            }
        }
        else
        {
            echo "<div class=\"notification error png_bg\">\n"
                . "<div>\n"
                . "" . _BADFILEFORMAT . ""
                . "</div>\n"
                . "</div>\n";
            redirect("index.php?file=Page&amp;page=admin&op=edit&page_id=" . $page_id, 5);
            closetable();
            return;
        }
    }
    else if ($url != "" && !ereg("." . $type, $url))
    {
        echo "<div class=\"notification error png_bg\">\n"
        . "<div>\n"
        . "" . _BADFILEFORMAT . ""
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Page&amp;page=admin&op=edit&page_id=" . $page_id, 5);
        closetable();
        return;
    }
    else
    {
        $filename = $url;
    }
    
    $content = html_entity_decode($content);
    $content = mysql_real_escape_string(stripslashes($content));
    $a1 = "¿¡¬√ƒ≈‡·‚„‰Â“”‘’÷ÿÚÛÙıˆ¯»… ÀËÈÍÎ«ÁÃÕŒœÏÌÓÔŸ⁄€‹˘˙˚¸ˇ—Ò";
    $b1 = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
    $title = str_replace(" ", "_", $titre);
    $title = str_replace("'", "_", $title);
    $title = str_replace("\"", "_", $title);
    $title = strtr($title, $a1, $b1);
    
    $show_title = (isset($show_title)) ? 1 : 0;

    $upd = mysql_query("UPDATE " . PAGE_TABLE . " SET titre = '" . $title . "', content = '" . $content . "', url = '" . $filename . "', niveau = '" . $niveau . "', type = '" . $type . "', show_title = '" . $show_title . "', members = '" . $userslist . "' WHERE id = '" . $page_id . "'");

    if ($menu != "")
    {
        $sql_menu = mysql_query("SELECT content FROM " . BLOCK_TABLE . " WHERE bid = '" . $menu . "'");
        list($content) = mysql_fetch_array($sql_menu);
        $content = stripslashes($content);
        $url_page = "index.php?file=Page&name=" . $title;

        $link = explode('NEWLINE', $content);
        $new_line = $url_page . "|" . $title . "||||";
        $count = count($link);
        $link[$count] = $new_line;

        $content = implode('NEWLINE', $link);
        $content = addslashes($content);
        $sql = mysql_query("UPDATE " . BLOCK_TABLE . " SET content = '" . $content . "' WHERE bid = '" . $menu . "'");

        $url_redirect = "index.php?file=Admin&page=menu&op=edit_line&bid=" . $menu . "&lid=" . $count;
    }
    else
    {
            $url_redirect = "index.php?file=Page&page=admin";
    }
    echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _PAGEMODIF . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect($url_redirect, 2);
}

function del($page_id)
{
    global  $nuked;

    $del = mysql_query("DELETE FROM " . PAGE_TABLE . " WHERE id = '" . $page_id . "'");
    echo "<div class=\"notification success png_bg\">\n"
        . "<div>\n"
        . "" . _PAGEDELETE . "\n"
        . "</div>\n"
        . "</div>\n";
        redirect("index.php?file=Page&page=admin",2);
}

function main_pref()
{
    global $nuked, $language;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>Gestion des PrÈfÈrences</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Page.html\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu(3);

    echo "<form method=\"post\" action=\"index.php?file=Page&amp;page=admin&amp;op=change_pref\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
    . "<tr><td align=\"center\" colspan=\"2\"><big>" . _PREFS . "</big></td></tr>\n"
    . "<tr><td>" . _PAGEINDEX . " :</td><td><select name=\"index_page\"><option value=\"\">" . _NONE . "</option>\n";

    $sql = mysql_query("SELECT titre FROM " . PAGE_TABLE . " ORDER BY titre");
    while (list($titre) = mysql_fetch_array($sql))
    { 
        if ($titre == $nuked['index_page']) $selected = "selected=\"selected\"";
        else $selected = "";

        echo "<option value=\"" . $titre . "\" " . $selected . ">" . $titre . "</option>\n";
    }

    echo "</select></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _SEND . "\" /><a class=\"buttonLink\" href=\"index.php?file=Page&amp;page=admin\">" . _BACK . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function change_pref($index_page)
{
    global $nuked;

    $upd = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $index_page . "' WHERE name = 'index_page'");
    echo "<div class=\"notification success png_bg\">\n"
    . "<div>\n"
    . "" . _PREFUPDATED . "\n"
    . "</div>\n"
    . "</div>\n";
    redirect("index.php?file=Page&page=admin", 2);
}

function nkAdminMenu($tab = 1) {
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Page&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _PAGE; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Page&amp;page=admin&amp;op=add">
                    <img src="modules/Admin/images/icons/add_page.png" alt="icon" />
                    <span><?php echo _ADDPAGE; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Page&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


?>
<script type="text/javascript">
function checkType( type, action, id )
{
    var ids = document.getElementById('contents');
    var spa = document.getElementById('cke_contents');

    if ( type == 'html' ) {
        if ( action == 'add' )
        {
            window.location.href=('index.php?file=Page&page=admin&op=add');
        }
        else
        {
            window.location.href=('index.php?file=Page&page=admin&op=edit&page_id=' + id);
        }
    }
    else {
        ids.className  = 'noeditor';
        ids.style.display  = 'block';
        spa.parentNode.removeChild( spa );
    }
}
</script>

<?php

switch($_REQUEST['op']) {
    case "add":
    add();
    break;

    case "del":
    del($_REQUEST['page_id']);
    break;

    case "do_edit":
    do_edit($_REQUEST['page_id'], $_REQUEST['titre'], $_REQUEST['type'], $_REQUEST['niveau'], $_REQUEST['content'], $_REQUEST['url'], $_REQUEST['pagefile'], $_REQUEST['menu'], $_REQUEST['show_title'], $_REQUEST['members']);
    break;

    case "edit":
    edit($_REQUEST['page_id']);
    break;

    case "do_add":
    do_add($_REQUEST['titre'], $_REQUEST['type'], $_REQUEST['niveau'], $_REQUEST['content'], $_REQUEST['url'], $_REQUEST['pagefile'], $_REQUEST['menu'], $_REQUEST['show_title'], $_REQUEST['members']);
    break;

    case "main_pref":
    main_pref();
    break;

    case "change_pref":
    change_pref($_REQUEST['index_page']);
    break;

    default:
        main();
    break;
}

?>