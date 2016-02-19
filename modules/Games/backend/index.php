<?php
/**
 * games.php
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


function main()
{
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
    . "<!--\n"
    . "\n"
    . "function delgame(name, id)\n"
    . "{\n"
    . "if (confirm('" . _DELBLOCK . " '+name+' ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Admin&page=games&op=del_game&game_id='+id;}\n"
    . "}\n"
    . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _GAMESADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/games.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu();

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 50%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

    $sql = mysql_query("SELECT id, name FROM " . GAMES_TABLE . " ORDER BY name");
    while (list($game_id, $name) = mysql_fetch_array($sql))
    {
        $name = nkHtmlEntities($name);

        echo "<tr>\n"
        . "<td style=\"width: 50%;\" align=\"center\">" . $name . "</td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=games&amp;op=edit_game&amp;game_id=" . $game_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _GAMEEDIT . "\" /></a></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><a href=\"javascript:delgame('" . addslashes($name) . "', '" . $game_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _GAMEDEL . "\" /></a></td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function add_game()
{
    global $language;

    echo '<script type="text/javascript">',"\n"
    . 'function nouveau(mapss)
            {
                document.getElementById("listmap").innerHTML += mapss+"<br />";
                document.getElementById("maps").value += mapss+"|";
                document.getElementById("map").value = "";
            }
            function reinitialiser()
            {
                document.getElementById("listmap").innerHTML = "";
                document.getElementById("maps").value = "";
                document.getElementById("map").value = "";
            }';

    echo "<!--\n"
    ."\n"
    . "function verifchamps()\n"
    . "{\n"
    . "if (document.getElementById('game_nom').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    . "\n"
    . "if (document.getElementById('game_icon').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_titre').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_pref1').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_pref2').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_pref3').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_pref4').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_pref5').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "return true;\n"
    . "}\n"
    ."\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _GAMESADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/games.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=games&amp;op=send_game\" onsubmit=\"return verifchamps();\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _NAME . " :</b> <input id=\"game_nom\" type=\"text\" name=\"nom\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _ICON . " :</b> <input id=\"game_icon\" type=\"text\" name=\"icon\" size=\"49\" />&nbsp<a class=\"buttonLink\" href=\"#\" onclick=\"javascript:window.open('index.php?file=Admin&amp;page=games&amp;op=show_icon','" . _ICON . "','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=300,height=125,top=30,left=0');return(false)\">" . _SEEICON . "</a></td></tr>\n"
    . "<tr><td><b>" . _TITLE . " :</b> <input id=\"game_titre\" type=\"text\" name=\"titre\" size=\"50\" /></td></tr>\n"
    . "<tr><td><b>" . _PREFNAME . " 1 :</b> <input id=\"game_pref1\" type=\"text\" name=\"pref1\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _PREFNAME . " 2 :</b> <input id=\"game_pref2\" type=\"text\" name=\"pref2\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _PREFNAME . " 3 :</b> <input id=\"game_pref3\" type=\"text\" name=\"pref3\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _PREFNAME . " 4 :</b> <input id=\"game_pref4\" type=\"text\" name=\"pref4\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _PREFNAME . " 5 :</b> <input id=\"game_pref5\" type=\"text\" name=\"pref5\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _MAP . " :</b> <input type=\"text\" id=\"map\" name=\"map\" value=\"\" /></td></tr>\n"
    . "<tr><td><input class=\"button\" type=\"button\" value=\"". _ADDMAP ."\" onClick=\"javascript:nouveau(map.value);\" />&nbsp;<input class=\"button\" type=\"button\" value=\"". _DELALLMAP ."\" onClick=\"javascript:reinitialiser();\" /></td></tr>\n"
    . "<tr><td><div id=\"listmap\"></div></td></tr>\n"
    . "<tr><td><input type=\"hidden\" id=\"maps\" name=\"maps\" value=\"\" /></td></tr>\n"
    . "</table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=games\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function send_game($nom, $titre, $icon, $pref1, $pref2, $pref3, $pref4, $pref5, $maps)
{
    global $nuked, $user;

    $nom = mysql_real_escape_string(stripslashes($nom));
    $titre = mysql_real_escape_string(stripslashes($titre));
    $pref1 = mysql_real_escape_string(stripslashes($pref1));
    $pref2 = mysql_real_escape_string(stripslashes($pref2));
    $pref3 = mysql_real_escape_string(stripslashes($pref3));
    $pref4 = mysql_real_escape_string(stripslashes($pref4));
    $pref5 = mysql_real_escape_string(stripslashes($pref5));
    $maps = mysql_real_escape_string(stripslashes($maps));

    $sql = mysql_query("INSERT INTO " . GAMES_TABLE . " ( `id` , `name` , `titre` , `icon` , `pref_1` , `pref_2` , `pref_3` , `pref_4` , `pref_5`, `map` ) VALUES ( '' , '" . $nom . "' , '" . $titre . "' , '" . $icon . "' , '" . $pref1 . "' , '" . $pref2 . "' , '" . $pref3 . "' , '" . $pref4 . "' , '" . $pref5 . "', '" . $maps . "')");

    saveUserAction(_ACTIONADDGAME .': '. $nom);

    printNotification(_GAMESUCCES, 'success');
    redirect("index.php?file=Admin&page=games", 2);
}

function edit_game($game_id)
{
    global $nuked, $language;

    $sql = mysql_query("SELECT name, titre, icon, pref_1, pref_2, pref_3, pref_4, pref_5, map FROM " . GAMES_TABLE . " WHERE id = '" . $game_id . "'");
    list($name, $titre, $icon, $pref_1, $pref_2, $pref_3, $pref_4, $pref_5, $maps) = mysql_fetch_array($sql);

    echo "<script type=\"text/javascript\">\n";
    echo 'function nouveau(mapss)
            {
                document.getElementById("listmap").innerHTML += mapss+"<br />";
                document.getElementById("maps").value += mapss+"|";
                document.getElementById("map").value = "";
            }
            function reinitialiser()
            {
                document.getElementById("listmap").innerHTML = "";
                document.getElementById("maps").value = "";
                document.getElementById("map").value = "";
            }';
    echo "<!--\n"
    ."\n"
    . "function verifchamps()\n"
    . "{\n"
    . "if (document.getElementById('game_nom').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    . "\n"
    . "if (document.getElementById('game_icon').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_titre').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_pref1').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_pref2').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_pref3').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_pref4').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "if (document.getElementById('game_pref5').value.length == 0)\n"
    . "{\n"
    . "alert('" . _ERRORCHAMPS . "');\n"
    . "return false;\n"
    . "}\n"
    ."\n"
    . "return true;\n"
    . "}\n"
    ."\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _GAMESADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/games.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=games&amp;op=modif_game\" onsubmit=\"return verifchamps();\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _NAME . " :</b> <input id=\"game_nom\" type=\"text\" name=\"nom\" size=\"30\" value=\"" . $name . "\" /></td></tr>\n"
    . "<tr><td><b>" . _ICON . " :</b> <input id=\"game_icon\" type=\"text\" name=\"icon\" size=\"49\" value=\"" . $icon . "\" />&nbsp<a class=\"buttonLink\" href=\"#\" onclick=\"javascript:window.open('index.php?file=Admin&amp;page=games&amp;op=show_icon','" . _ICON . "','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=300,height=125,top=30,left=0');return(false)\">" . _SEEICON . "</a></td></tr>\n"
    . "<tr><td><b>" . _TITLE . " :</b> <input id=\"game_titre\" type=\"text\" name=\"titre\" size=\"50\" value=\"" . $titre . "\" /></td></tr>\n"
    . "<tr><td><b>" . _PREFNAME . " 1 :</b> <input id=\"game_pref1\" type=\"text\" name=\"pref1\" size=\"30\" value=\"" . $pref_1 . "\" /></td></tr>\n"
    . "<tr><td><b>" . _PREFNAME . " 2 :</b> <input id=\"game_pref2\" type=\"text\" name=\"pref2\" size=\"30\" value=\"" . $pref_2 . "\" /></td></tr>\n"
    . "<tr><td><b>" . _PREFNAME . " 3 :</b> <input id=\"game_pref3\" type=\"text\" name=\"pref3\" size=\"30\" value=\"" . $pref_3 . "\" /></td></tr>\n"
    . "<tr><td><b>" . _PREFNAME . " 4 :</b> <input id=\"game_pref4\" type=\"text\" name=\"pref4\" size=\"30\" value=\"" . $pref_4 . "\" /></td></tr>\n"
    . "<tr><td><b>" . _PREFNAME . " 5 :</b> <input id=\"game_pref5\" type=\"text\" name=\"pref5\" size=\"30\" value=\"" . $pref_5 . "\" /></td></tr>\n"
    . "<tr><td><b>" . _MAP . " :</b> <input type=\"text\" id=\"map\" name=\"map\" value=\"\" /></td></tr>\n"
    . "<tr><td><input class=\"button\" type=\"button\" value=\"". _ADDMAP ."\" onClick=\"javascript:nouveau(map.value);\" />&nbsp;<input class=\"button\" type=\"button\" value=\"". _DELALLMAP ."\" onClick=\"javascript:reinitialiser();\" /></td></tr>\n"
    . "<tr><td><div id=\"listmap\">\n";

    $map = explode('|', $maps);
    $i = 0;
    foreach($map as $mapping)
    {
        echo $mapping."</br/>";
        $i++;
    }
    echo "</div></td></tr>\n"
    . "<tr><td><input type=\"hidden\" id=\"maps\" name=\"maps\" value=\"".$maps."\" /></td></tr>\n"
    . "<tr><td>&nbsp;<input type=\"hidden\" name=\"game_id\" value=\"" . $game_id . "\" /></td></tr>\n"
    . "</table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=games\">" . __('BACK') . "</a></div></form><br /></div></div>\n";

}

function modif_game($game_id, $nom, $titre, $icon, $pref1, $pref2, $pref3, $pref4, $pref5, $maps)
{
    global $nuked, $user;

    $nom = mysql_real_escape_string(stripslashes($nom));
    $titre = mysql_real_escape_string(stripslashes($titre));
    $pref1 = mysql_real_escape_string(stripslashes($pref1));
    $pref2 = mysql_real_escape_string(stripslashes($pref2));
    $pref3 = mysql_real_escape_string(stripslashes($pref3));
    $pref4 = mysql_real_escape_string(stripslashes($pref4));
    $pref5 = mysql_real_escape_string(stripslashes($pref5));
    $maps = mysql_real_escape_string(stripslashes($maps));

    $sql = mysql_query("UPDATE " . GAMES_TABLE . " SET name = '" . $nom . "', titre = '" . $titre . "', icon = '" . $icon . "', pref_1 = '" . $pref1 . "', pref_2 = '" . $pref2 . "', pref_3 = '" . $pref3 . "', pref_4 = '" . $pref4 . "', pref_5 = '" . $pref5 . "', map = '" . $maps . "' WHERE id = '" . $game_id . "'");

    saveUserAction(_ACTIONMODIFGAME .': '. $nom);

    printNotification(_GAMEMODIF, 'success');
    redirect("index.php?file=Admin&page=games", 2);
}

function del_game($game_id)
{
    global $nuked, $user;

    $sql2 = mysql_query("SELECT name FROM " . GAMES_TABLE . " WHERE id = '" . $game_id . "'");
    list($name) = mysql_fetch_array($sql2);

    $sql = mysql_query("DELETE FROM " . GAMES_TABLE . " WHERE id = '" . $game_id . "'");

    saveUserAction(_ACTIONDELGAME .': '. $name);

    printNotification(_GAMEDELETE, 'success');
    redirect("index.php?file=Admin&page=games", 2);
}

function show_icon()
{
    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_ICONLIST);

    echo "<script type=\"text/javascript\">\n"
    . "<!--\n"
    . "\n"
    . "function go(img) {\n"
    . "opener.document.getElementById('game_icon').value=img;\n"
    . "}\n"
    . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div style=\"text-align: center;\"><br /><b>" . _CLICICON . "</b></div>\n"
    . "<div style=\"text-align: center;\"><br />\n";

    if ($dir = @opendir("images/games/"))
    {
        while (false !== ($f = readdir($dir)))
        {
            if ($f != "." && $f != ".." && $f != "index.html"  && $f != "Thumbs.db")
            {
                $icon = "images/games/" . $f;
                echo " <a href=\"#\" onclick=\"javascript:go('" . $icon . "');\"><img style=\"border: 0;\" src=\"images/games/" . $f . "\" alt=\"\" title=\"" . $f . "\" /></a>";
            }
        }

        closedir($dir);
    }

    echo "</div><div style=\"text-align: center;\"><br /><b><a href=\"#\" onclick=\"self.close()\">" . __('CLOSE_WINDOW') . "</a></b></div>";
}

function nkAdminMenu()
{
    global $language, $user, $nuked;
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li>
                <a class="shortcut-button" href="index.php?file=Admin&amp;page=games&amp;op=add_game">
                    <img src="modules/Admin/images/icons/chess.png" alt="icon" />
                    <span><?php echo _GAMEADD; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}

switch ($GLOBALS['op'])
{
    case "add_game":
        add_game();
        break;

    case "send_game":
        send_game($_POST['nom'], $_POST['titre'], $_POST['icon'], $_POST['pref1'], $_POST['pref2'], $_POST['pref3'], $_POST['pref4'], $_POST['pref5'], $_POST['maps']);
        break;

    case "edit_game":
        edit_game($_REQUEST['game_id']);
        break;

    case "modif_game":
        modif_game($_REQUEST['game_id'], $_POST['nom'], $_POST['titre'], $_POST['icon'], $_POST['pref1'], $_POST['pref2'], $_POST['pref3'], $_POST['pref4'], $_POST['pref5'], $_POST['maps']);
        break;

    case "del_game":
        del_game($_REQUEST['game_id']);
        break;

    case "main":
        main();
        break;

    case "show_icon":
        show_icon();
        break;

    default:
        main();
        break;
}

?>