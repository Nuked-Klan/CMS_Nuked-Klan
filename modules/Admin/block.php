<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $user, $language;
translate('modules/Admin/lang/' . $language . '.lang.php');
include('modules/Admin/design.php');

$visiteur = $user ? $user[1] : 0;

if ($visiteur == 9)
{
    function sel_block()
    {
        $blocks = array();
        $path = 'Includes/blocks/';
        $handle = @opendir($path);
        while (false !== ($blok = readdir($handle)))
        {
            if ($blok != '.' && $blok != '..' && $blok != 'index.html' && $blok != 'block_module.php')
            {
                if (substr($blok, -3, 3) == 'php')
                {
                    $blok = substr($blok, 6, -4);

                    if ($blok == 'survey') $blokname = _NAMESURVEY;
                    else if ($blok == 'menu') $blokname = _NAV;
                    else if ($blok == 'suggest') $blokname = _NAMESUGGEST;
                    else if ($blok == 'event') $blokname = _NAMECALANDAR;
                    else if ($blok == 'login') $blokname = _LOGIN;
                    else if ($blok == 'center') $blokname = _CENTERBLOCK;
                    else if ($blok == 'html') $blokname = _BLOCKHTML;
                    else if ($blok == 'language') $blokname = _BLOCKLANG;
                    else if ($blok == 'theme') $blokname = _BLOCKTHEME;
                    else if ($blok == 'counter') $blokname = _BLOCKCOUNTER;
                    else $blokname = $blok;

                    array_push($blocks, $blokname . '|' . $blok);
                }
            }
        }
        closedir($handle);
        natcasesort($blocks);

        foreach($blocks as $value)
        {
            $temp = explode('|', $value);
            echo '<option value="b|' . $temp[1] . '">' . $temp[0] . '</option>',"\n";
        }
    }

    function add_block()
    {
        global $language;

        admintop();

        echo '<div class="content-box">',"\n" //<!-- Start Content Box -->
        . '<div class="content-box-header"><h3>' . _BLOCKADMIN . '</h3>',"\n"
        . '<div style="text-align:right"><a href="help/' . $language . '/block.php" rel="modal">',"\n"
        . '<img style="border: 0" src="help/help.gif" alt="" title="' . _HELP . '" /></a>',"\n"
        . '</div></div>',"\n"
        . '<div class="tab-content" id="tab2"><form method="post" action="index.php?file=Admin&amp;page=block&amp;op=send_block">',"\n"
        . '<table style="margin: auto;text-align: left" cellspacing="0" cellpadding="2" border="0">',"\n"
        . '<tr><td><b>' . _BLOCKTITLE . ' :</b> <input type="text" name="titre" size="40" value="' . $titre . '" /></td></tr>',"\n"
        . '<tr><td><b>' . _TYPE . ' : </b><select name="type">',"\n";

        sel_block();

        echo '<option value="b|module">* ' . _MODBLOCK . ' :</option>',"\n";

        select_mod('Tous');

        echo '</select>&nbsp;&nbsp;<b>' . _LEVEL . ' : </b><select name="nivo">',"\n"
        . '<option>0</option>',"\n"
        . '<option>1</option>',"\n"
        . '<option>2</option>',"\n"
        . '<option>3</option>',"\n"
        . '<option>4</option>',"\n"
        . '<option>5</option>',"\n"
        . '<option>6</option>',"\n"
        . '<option>7</option>',"\n"
        . '<option>8</option>',"\n"
        . '<option>9</option></select></td></tr><tr><td>&nbsp;</td></tr>',"\n"
        . '<tr><td align="center"><b>' . _SELECTPAGE . ' :</b></td></tr><tr><td>&nbsp;</td></tr><tr><td align="center"><select name="pages[]" size="8" multiple="multiple">',"\n";

        select_mod2('Tous');

        echo '</select></td></tr><tr><td>&nbsp;</td></tr>',"\n"
        . '<tr><td align="center"><input type="submit" value="' . _CREATEBLOCK . '" /></td></tr></table>',"\n"
        . '<div style="text-align: center"><br />[ <a href="index.php?file=Admin&amp;page=block"><b>' . _BACK . '</b></a> ]</div></form><br /></div></div>',"\n";

        adminfoot();
    }

    function send_block($titre, $type, $nivo, $pages)
    {
        global $nuked, $user;

        admintop();

        if ($pages != '')
        {
            $pages = implode('|', $pages);
        }

        $titre = mysql_real_escape_string(stripslashes($titre));
        $t = explode('|', $type);

        if ($t[0] == 'm')
        {
            $type = 'module';
            $module = $t[1];
        }
        else
        {
            $type = $t[1];
            $module = '';
        }
        $module = mysql_real_escape_string(stripslashes($module));
        $type = mysql_real_escape_string(stripslashes($type));
        $nivo = mysql_real_escape_string(stripslashes($nivo));
        $pages = mysql_real_escape_string(stripslashes($pages));

        $sql = mysql_query("INSERT INTO " . BLOCK_TABLE . " ( `bid` , `active` , `position` , `module` , `titre` , `content` , `type` , `nivo` , `page` ) VALUES ( '' , '0' , '' , '" . $module . "' , '" . $titre . "' , '' , '" . $type . "' , '" . $nivo . "' , '" . $pages . "' )");

        $sql2 = mysql_query("SELECT bid FROM " . BLOCK_TABLE . " WHERE titre = '" . $titre . "' AND type = '" . $type . "'");
        list($bid) = mysql_fetch_array($sql2);

        // Action
        $texteaction = _ACTIONADDBLOCK . ': ' . $titre;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action

        echo '<div class="notification success png_bg">',"\n"
        . '<div>',"\n"
        . '' . _BLOCKSUCCES . '',"\n"
        . '</div>',"\n"
        . '</div>',"\n";
        redirect('index.php?file=Admin&page=block&op=edit_block&bid=' . $bid, 2);

        adminfoot();
    }

    function del_block($bid)
    {
        global $nuked, $user;

        $bid = mysql_real_escape_string(stripslashes($bid));

        admintop();
        $sql2 = mysql_query("SELECT titre FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
        list($titre) = mysql_fetch_array($sql2);
        $sql = mysql_query("DELETE FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");

        // Action
        $texteaction = _ACTIONDELBLOCK . ': ' . $titre;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        echo '<div class="notification success png_bg">',"\n"
        . '<div>',"\n"
        . '' . _BLOCKCLEAR . '',"\n"
        . '</div>',"\n"
        . '</div>',"\n";
        redirect('index.php?file=Admin&page=block', 2);

        adminfoot();
    }

    function select_mod2($mod)
    {
        global $nuked;

        $sql = mysql_query("SELECT nom FROM " . MODULES_TABLE . " ORDER BY nom");
        $mod = explode('|', $mod);
        while (list($nom) = mysql_fetch_array($sql))
        {
            $checked = '';

            foreach ($mod as $mod2)
            {
                $titi = $mod2;
                if ($titi == $nom) $checked = 'selected="selected"';
                if ($titi == 'Tous') $checked_tous = 'selected="selected"';
                if ($titi == 'Admin') $checked_admin = 'selected="selected"';
                if ($titi == 'User') $checked_user = 'selected="selected"';
                if ($titi == 'Team') $checked_team = 'selected="selected"';
            }

            echo '<option value="' . $nom . '" ' . $checked . '>&nbsp;' . $nom . '&nbsp;</option>',"\n";
        }

        echo '<option value="Team" ' . $checked_team . '>&nbsp;' . _TEAM . '&nbsp;</option>',"\n"
        . '<option value="User" ' . $checked_user . '>&nbsp;' . _USER . '&nbsp;</option>',"\n"
        . '<option value="Admin" ' . $checked_admin . '>&nbsp;' . _ADMIN . '&nbsp;</option>',"\n"
        . '<option value="Tous" ' . $checked_tous . '>&nbsp;' . _ALL . '&nbsp;</option>',"\n";
    }

    function select_mod($mod)
    {
        $modules = array();
        $handle = opendir('modules');
        while (false !== ($f = readdir($handle)))
        {
            if ($f != '.' && $f != '..' && $f != 'CVS' && $f != 'index.html'  && !preg_match('`[.]`', $f))
            {

                if ($f == 'Gallery') $modname = _NAMEGALLERY;
                else if ($f == 'Download') $modname = _NAMEDOWNLOAD;
                else if ($f == 'Irc') $modname = _NAMEIRC;
                else if ($f == 'Links') $modname = _NAMELINKS;
                else if ($f == 'Wars') $modname = _NAMEMATCHES;
                else if ($f == 'News') $modname = _NAMENEWS;
                else if ($f == 'Search') $modname = _NAVSEARCH;
                else if ($f == 'Sections') $modname = _NAMESECTIONS;
                else if ($f == 'Server') $modname = _NAMESERVER;
                else if ($f == 'Stats') $modname = _BLOKSTATS;
                else if ($f == 'Forum') $modname = _NAMEFORUM;
                else if ($f == 'Team') $modname = _NAVTEAM;
                else if ($f == 'Textbox') $modname = _NAMESHOUTBOX;
                else $modname = $f;

                array_push($modules, $modname . '|' . $f);
            }
        }

        closedir($handle);
        natcasesort($modules);

    foreach($modules as $value)
    {
            $temp = explode('|', $value);

            if ($mod == $temp[1])
            {
                $checked = 'selected="selected"';
            }
            else
            {
                $checked = '';
            }

            if (is_file('modules/' . $temp[1] . '/blok.php'))
            {
                echo '<option value="m|' . $temp[1] . '" ' . $checked . '>' . $temp[0] . '</option>',"\n";
            }
    }
    }

    function edit_block($bid)
    {
        global $nuked;

        $bid = mysql_real_escape_string(stripslashes($bid));
        admintop();

        $sql = mysql_query("SELECT type FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
        list($type) = mysql_fetch_array($sql);

        include_once('Includes/blocks/block_' . $type . '.php');

        $function = 'edit_block_' . $type;
        $function($bid);

        adminfoot();
    }

    function modif_block($data)
    {
        global $nuked, $user;

        admintop();

        $function = 'modif_advanced_' . $data['type'];

        include_once('Includes/blocks/block_' . $data['type'] . '.php');

        if (function_exists($function))
        {
            $data = $function($data);
        }

        if ($data['pages'] != '') $data['pages'] = implode('|', $data['pages']);

        $data['titre'] = mysql_real_escape_string(stripslashes($data['titre']));
        $data['content'] = mysql_real_escape_string(stripslashes($data['content']));

        if ($data['module'] != '')
        {
            list ($t, $module) = explode ('|', $data['module']);
        }
        else
        {
            $module = '';
        }

        $sql = mysql_query("UPDATE " . BLOCK_TABLE . " SET active = '" . $data['active'] . "', position = '" . $data['position'] . "', module = '" . $module . "', titre = '" . $data['titre'] . "', content = '" . $data['content'] . "', type = '" . $data['type'] . "', nivo = '" . $data['nivo'] . "', page = '" . $data['pages'] . "' WHERE bid = '" . $data['bid'] . "'");

        // Action
        $texteaction = _ACTIONMODIFBLOCK . ': ' . $data['titre'];
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action

        echo '<div class="notification success png_bg">',"\n"
        . '<div>',"\n"
        . '' . _BLOCKMODIF . '',"\n"
        . '</div>',"\n"
        . '</div>',"\n"
        . "<script>\n"
        . "setTimeout('screen()','3000');\n"
        . "function screen() { \n"
        . "screenon('index.php', 'index.php?file=Admin&page=block');\n"
        . "}\n"
        . "</script>\n";

        adminfoot();
    }

    function modif_position_block($bid, $method)
    {
        global $nuked;

        admintop();

        $sql2 = mysql_query("SELECT titre, position FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
        list($titre, $position) = mysql_fetch_array($sql2);

        if ($method == 'up')
        {
            $position--;
        }
        else if ($method == 'down')
        {
            $position++;
        }

        if ($position < 0)
        {
            $position = 0;
        }

        $sql = mysql_query("UPDATE " . BLOCK_TABLE . " SET position = '" . $position . "' WHERE bid = '" . $bid . "'");
         // Action
        $texteaction = _ACTIONPOSBLOCK . ': ' . $titre;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action

        echo '<div class="notification success png_bg">',"\n"
        . '<div>',"\n"
        . '' . _BLOCKMODIF . '',"\n"
        . '</div>',"\n"
        . '</div>',"\n"
        . '<script>',"\n"
        . "setTimeout('screen()','3000');\n"
        . "function screen() { \n"
        . "screenon('index.php', 'index.php?file=Admin&page=block');\n"
        . "}\n"
        . "</script>\n";

        adminfoot();
    }

    function main()
    {
        global $nuked, $language;

        admintop();

        echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function delblock(titre, id)\n"
        . "{\n"
        . "if (confirm('" . _DELBLOCK . " '+titre+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=Admin&page=block&op=del_block&bid='+id;}\n"
        . "}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _BLOCKADMIN . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/block.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin&amp;page=block&amp;op=add_block\"><b>" . _BLOCKADD . "</b></a> ]</div><br />\n"
        . "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _BLOCK . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _TYPE . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _LEVEL . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

        $sql = mysql_query("SELECT active, position, titre, module, content, type, nivo, bid FROM " . BLOCK_TABLE . " ORDER BY active DESC, position, nivo");
        while (list($active, $position, $titre, $module, $content, $type, $nivo, $bid) = mysql_fetch_array($sql))
        {
            $titre = printSecuTags($titre);

            if ($active == 1) $act = _LEFT;
            else if ($active == 2) $act = _RIGHT;
            else if ($active == 3) $act = _CENTERBLOCK;
            else if ($active == 4) $act = _FOOTERBLOCK;
            else $act = _OFF;

            echo "<tr>\n"
            . "<td style=\"width: 20%;\">" . $titre . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $act . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=block&amp;op=modif_position_block&amp;bid=" . $bid . "&amp;method=down\" title=\"" . _BLOCKDOWN . "\">&lt;</a> " . $position . " <a href=\"index.php?file=Admin&amp;page=block&amp;op=modif_position_block&amp;bid=" . $bid . "&amp;method=up\" title=\"" . _BLOCKUP . "\">&gt;</a></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $type . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $nivo . "</td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=block&amp;op=edit_block&amp;bid=" . $bid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _BLOCKEDIT . "\" /></a></td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:delblock('" . mysql_real_escape_string(stripslashes($titre)) . "','" . $bid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _BLOCKDEL . "\" /></a></td></tr>\n";
        }

        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";

        adminfoot();
    }

    switch ($_REQUEST['op'])
    {
        case "edit_block":
            edit_block($_REQUEST['bid']);
            break;

        case "add_block":
            add_block();
            break;

        case "del_block":
            del_block($_REQUEST['bid']);
            break;

        case "send_block":
            send_block($_POST['titre'], $_POST['type'], $_REQUEST['nivo'], $_POST['pages']);
            break;

        case "modif_position_block":
            modif_position_block($_REQUEST['bid'], $_REQUEST['method']);
            break;

        case "modif_block":
            modif_block($_POST);
            break;

        case "main":
            main();
            break;

        default:
            main();
            break;
    }

}
else if ($visiteur > 1)
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
    adminfoot();
}
else
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
    adminfoot();
}

?>
