<?php
/**
 * block.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', SUPER_ADMINISTRATOR_ACCESS))
    return;


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

                if ($blok == 'survey') $blokname = __('SURVEY_MODNAME');
                else if ($blok == 'menu') $blokname = _NAV;
                else if ($blok == 'suggest') $blokname = __('SUGGEST_MODNAME');
                else if ($blok == 'event') $blokname = __('CALENDAR_MODNAME');
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

    echo '<div class="content-box">',"\n" //<!-- Start Content Box -->
    . '<div class="content-box-header"><h3>' . _BLOCKADMIN . '</h3>',"\n"
    . '<div style="text-align:right"><a href="help/' . $language . '/block.php" rel="modal">',"\n"
    . '<img style="border: 0" src="help/help.gif" alt="" title="' . _HELP . '" /></a>',"\n"
    . '</div></div>',"\n"
    . '<div class="tab-content" id="tab2"><form method="post" action="index.php?file=Admin&amp;page=block&amp;op=send_block">',"\n"
    . '<table style="margin: auto;text-align: left" cellspacing="0" cellpadding="2" border="0">',"\n"
    . '<tr><td><b>' . _BLOCKTITLE . ' :</b> <input type="text" name="titre" size="40" value="" /></td></tr>',"\n"
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
    . '<tr><td align="center"></td></tr></table>',"\n"
    . '<div style="text-align: center"><br /><input class="button" type="submit" value="' . _CREATEBLOCK . '" /><a class="buttonLink" href="index.php?file=Admin&amp;page=block">' . __('BACK') . '</a></div></form><br /></div></div>',"\n";
}

function send_block($titre, $type, $nivo, $pages)
{
    global $nuked, $user;

    if ($pages != '')
    {
        $pages = implode('|', $pages);
    }

    $titre = nkDB_realEscapeString(stripslashes($titre));
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
    $module = nkDB_realEscapeString(stripslashes($module));
    $type = nkDB_realEscapeString(stripslashes($type));
    $nivo = nkDB_realEscapeString(stripslashes($nivo));
    $pages = nkDB_realEscapeString(stripslashes($pages));

    $sql = nkDB_execute("INSERT INTO " . BLOCK_TABLE . " ( `bid` , `active` , `position` , `module` , `titre` , `content` , `type` , `nivo` , `page` ) VALUES ( '' , '0' , '' , '" . $module . "' , '" . $titre . "' , '' , '" . $type . "' , '" . $nivo . "' , '" . $pages . "' )");

    $sql2 = nkDB_execute("SELECT bid FROM " . BLOCK_TABLE . " WHERE titre = '" . $titre . "' AND type = '" . $type . "'");
    list($bid) = nkDB_fetchArray($sql2);

    saveUserAction(_ACTIONADDBLOCK .': '. $titre);

    printNotification(_BLOCKSUCCES, 'success');
    redirect('index.php?file=Admin&page=block&op=edit_block&bid=' . $bid, 2);
}

function del_block($bid)
{
    global $nuked, $user;

    $bid = nkDB_realEscapeString(stripslashes($bid));

    $sql2 = nkDB_execute("SELECT titre FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
    list($titre) = nkDB_fetchArray($sql2);
    $sql = nkDB_execute("DELETE FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");

    saveUserAction(_ACTIONDELBLOCK .': '. $titre);

    printNotification(_BLOCKCLEAR, 'success');
    redirect('index.php?file=Admin&page=block', 2);
}

function select_mod2($mod)
{
    global $nuked;

    $sql = nkDB_execute("SELECT nom FROM " . MODULES_TABLE . " ORDER BY nom");
    $mod = explode('|', $mod);
    while (list($nom) = nkDB_fetchArray($sql))
    {
        $checked = $checked_tous = $checked_team = $checked_user = $checked_admin = '';

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
        if ($f != '.' && $f != '..' && $f != 'CVS' && $f != 'index.html'  && strpos($f, '.') === false)
        {
            $moduleNameConst = strtoupper($f) .'_MODNAME';

            if (translationExist($moduleNameConst))
                $moduleName = __($moduleNameConst);
            else
                $moduleName = $f;

            array_push($modules, $moduleName . '|' . $f);
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

    $bid = nkDB_realEscapeString(stripslashes($bid));

    $sql = nkDB_execute("SELECT type FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
    list($type) = nkDB_fetchArray($sql);

    include_once('Includes/blocks/block_' . $type . '.php');

    $function = 'edit_block_' . $type;
    $function($bid);
}

function modif_block($data)
{
    global $nuked, $user;

    $function = 'modif_advanced_' . $data['type'];

    include_once('Includes/blocks/block_' . $data['type'] . '.php');

    if (function_exists($function))
    {
        $data = $function($data);
    }

    if ($data['pages'] != '') $data['pages'] = implode('|', $data['pages']);

    if(!isset($data['content'])){
        $data['content'] = '';
    }

    $data['titre'] = nkDB_realEscapeString(stripslashes($data['titre']));
    $data['content'] = nkDB_realEscapeString(stripslashes($data['content']));

    if (array_key_exists('module', $data) && $data['module'] != '')
    {
        list ($t, $module) = explode ('|', $data['module']);
    }
    else
    {
        $module = '';
    }

    $sql = nkDB_execute("UPDATE " . BLOCK_TABLE . " SET active = '" . $data['active'] . "', position = '" . $data['position'] . "', module = '" . $module . "', titre = '" . $data['titre'] . "', content = '" . $data['content'] . "', type = '" . $data['type'] . "', nivo = '" . $data['nivo'] . "', page = '" . $data['pages'] . "' WHERE bid = '" . $data['bid'] . "'");

    saveUserAction(_ACTIONMODIFBLOCK .': '. $data['titre']);

    printNotification(_BLOCKMODIF, 'success');
    setPreview('index.php', 'index.php?file=Admin&page=block');
}

function modif_position_block($bid, $method)
{
    global $nuked;

    $sql2 = nkDB_execute("SELECT titre, position FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
    list($titre, $position) = nkDB_fetchArray($sql2);

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

    $sql = nkDB_execute("UPDATE " . BLOCK_TABLE . " SET position = '" . $position . "' WHERE bid = '" . $bid . "'");

    saveUserAction(_ACTIONPOSBLOCK .': '. $titre);

    printNotification(_BLOCKMODIF, 'success');
    setPreview('index.php', 'index.php?file=Admin&page=block');
}

function main()
{
    global $nuked, $language;

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
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu();

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _BLOCK . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _TYPE . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _LEVEL . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT active, position, titre, module, content, type, nivo, bid FROM " . BLOCK_TABLE . " ORDER BY active DESC, position, nivo");
    while (list($active, $position, $titre, $module, $content, $type, $nivo, $bid) = nkDB_fetchArray($sql))
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
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:delblock('" . addslashes($titre) . "','" . $bid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _BLOCKDEL . "\" /></a></td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function nkAdminMenu()
{
    global $language, $user, $nuked;
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li>
                <a class="shortcut-button" href="index.php?file=Admin&amp;page=block&amp;op=add_block">
                    <img src="modules/Admin/images/icons/add.png" alt="icon" />
                    <span><?php echo _BLOCKADD; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($GLOBALS['op']) {
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

?>
