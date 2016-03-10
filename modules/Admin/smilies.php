<?php
/**
 * smilies.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
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
    . "function delsmiley(name, id)\n"
    . "{\n"
    . "if (confirm('" . _DELBLOCK . " '+name+' ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Admin&page=smilies&op=del_smiley&smiley_id='+id;}\n"
    . "}\n"
        . "\n"
    . "// -->\n"
    . "</script>\n";
    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _SMILIEADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/smilies.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu();

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _SMILEY . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _CODE . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

    $sql = nkDB_execute("SELECT id, code, url, name FROM " . SMILIES_TABLE . " ORDER BY id");
    while (list($smiley_id, $code, $url, $name) = nkDB_fetchArray($sql))
    {
        $name = nkHtmlEntities($name);

        echo "<tr>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><img src=\"images/icones/" . $url . "\" alt=\"\" title=\"$url\" /></td>\n"
        . "<td style=\"width: 25%;\" align=\"center\">" . $name . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $code . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=smilies&amp;op=edit_smiley&amp;smiley_id=" . $smiley_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _SMILEYEDIT . "\" /></a></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><a href=\"javascript:delsmiley('" . mysql_real_escape_string($name) . "', '" . $smiley_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _SMILEYDEL . "\" /></a></td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div><br /></div></div>\n";
}

function add_smiley()
{
    global $language;

    echo "<script type=\"text/javascript\">\n"
    . "<!--\n"
    . "\n"
    . "function update_smiley(newimage)\n"
    . "{\n"
    . "document.getElementById('smiley').src = 'images/icones/' + newimage;\n"
    . "}\n"
        . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _SMILIEADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/smilies.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=smilies&amp;op=send_smiley\" enctype=\"multipart/form-data\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _NAME . " :</b> <input type=\"text\" name=\"nom\" size=\"30\" /></td></tr>\n"
    . "<tr><td><b>" . _CODE . " :</b> <input type=\"text\" name=\"code\" size=\"10\" /></td></tr><tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><b>" . _SMILEY . " :</b> <select name=\"url\" onchange=\"update_smiley(this.options[selectedIndex].value);\">";

    $i = 0;
    $rep = Array();
    $path = "images/icones";
    $handle = opendir($path);
    while (false !== ($ikon = readdir($handle)))
    {
        if ($ikon != "." && $ikon != ".." && $ikon != "index.html" && $ikon != "Thumbs.db" && $ikon != ".htaccess")
        {
            $rep[] = $ikon;

            if ($i == 0)
            {
                $img = "images/icones/" . $ikon;
            }

            $i++;
        }
    }

    closedir($handle);
    sort ($rep);
    reset ($rep);

    while (list ($key, $filename) = each ($rep))
    {
            echo "<option value=\"" . $filename . "\">" . $filename . "</option>\n";
    }

    echo "</select>&nbsp;&nbsp;";

    if ($i > 0)
    {
        echo "<img id=\"smiley\" src=\"" . $img . "\" alt=\"\" />";
    }

    echo "</td></tr><tr><td><b>" . _UPSMILEY . " : </b><input type=\"file\" name=\"fichiernom\" /></td></tr>\n"
    . "<tr><td>&nbsp;</td></tr>\n"
    . "</table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=smilies\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function send_smiley($nom, $code)
{
    global $nuked, $user;

    require_once 'Includes/nkUpload.php';

    if (($nom == $code) || (strpos($code,'"')!==false) || (strpos($code,"'")!==false) || (strpos($nom,'"')!==false) || (strpos($nom,"'")!==false))
    {
        printNotification(_SMILEYNOTAUTHORIZE, 'error');
        redirect("index.php?file=Admin&page=smilies&op=add_smiley", 4);
        return;
    }

    $nom = mysql_real_escape_string(stripslashes($nom));

    $smileyUrl = '';

    if ($_FILES['fichiernom']['name'] != '') {
        list($smileyUrl, $uploadError, $smileyExt) = nkUpload_check('fichiernom', array(
            'fileType'  => 'image',
            'uploadDir' => 'images/icones',
            //'fileSize'  => 100000
        ));

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            redirect('index.php?file=Admin&page=smilies&op=add_smiley', 2);
            return;
        }

        $smileyUrl = basename($smileyUrl);
    }
    else if ($_POST['url'] != '') {
        $ext = strtolower(substr(strrchr($_POST['url'], '.'), 1));

        if (! in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
            printNotification(__('BAD_IMAGE_FORMAT'), 'error');
            redirect('index.php?file=Admin&page=smilies&op=add_smiley', 2);
            return;
        }

        $smileyUrl = $_POST['url'];
    }

    $sql = nkDB_execute("INSERT INTO " . SMILIES_TABLE . " ( `id` , `code` , `url` , `name` ) VALUES ( '' , '" . $code . "' , '" . $smileyUrl . "' , '" . $nom . "')");

    saveUserAction(_ACTIONADDSMILEY .': '. $nom);

    printNotification(_SMILEYSUCCES, 'success');
    redirect("index.php?file=Admin&page=smilies", 2);
}

function edit_smiley($smiley_id)
{
    global $nuked, $language;

    $sql = nkDB_execute("SELECT code, url, name FROM " . SMILIES_TABLE . " WHERE id = '" . $smiley_id . "'");
    list($code, $url, $name) = nkDB_fetchArray($sql);

    echo "<script type=\"text/javascript\">\n"
    . "<!--\n"
    . "\n"
    . "function update_smiley(newimage)\n"
    . "{\n"
    . "document.getElementById('smiley').src = 'images/icones/' + newimage;\n"
    . "}\n"
        . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _SMILIEADMIN . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/smilies.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=smilies&amp;op=modif_smiley\" enctype=\"multipart/form-data\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td><b>" . _NAME . " :</b> <input type=\"text\" name=\"nom\" size=\"30\" value=\"" . $name . "\" /></td></tr>\n"
    . "<tr><td><b>" . _CODE . " :</b> <input type=\"text\" name=\"code\" size=\"10\" value=\"" . $code . "\" /></td></tr><tr><td>&nbsp;</td></tr>\n"
    . "<tr><td><b>" . _SMILEY . " :</b> <select name=\"url\" onchange=\"update_smiley(this.options[selectedIndex].value);\">";

    $rep = Array();
    $path = "images/icones";
    $handle = opendir($path);
    while (false !== ($ikon = readdir($handle)))
    {
        if ($ikon != "." && $ikon != ".." && $ikon != "index.html" && $ikon != "Thumbs.db" && $ikon != ".htaccess")
        {
            $rep[] = $ikon;
        }
    }

    closedir($handle);
    sort ($rep);
    reset ($rep);

    while (list ($key, $filename) = each ($rep))
    {
        if ($url == $filename)
        {
            $checked = "selected=\"selected\"";
        }
        else
        {
            $checked = "";
        }

        echo "<option value=\"" . $filename . "\" " . $checked . ">" . $filename . "</option>\n";
    }

    echo "</select>&nbsp;&nbsp;<img id=\"smiley\" src=\"images/icones/" . $url . "\" alt=\"\" /></td></tr><tr><td><b>" . _UPSMILEY . " : </b><input type=\"file\" name=\"fichiernom\" /></td></tr>\n"
    . "<tr><td>&nbsp;<input type=\"hidden\" name=\"smiley_id\" value=\"" . $smiley_id . "\" /></td></tr>\n"
    . "</table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . __('SEND') . "\" /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=smilies\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function modif_smiley($smiley_id, $nom, $code)
{
    global $nuked, $user;

    require_once 'Includes/nkUpload.php';

    $nom = mysql_real_escape_string(stripslashes($nom));

    if (($nom == $code) || (strpos($code,'"')!==false) || (strpos($code,"'")!==false) || (strpos($nom,'"')!==false) || (strpos($nom,"'")!==false))
    {
        printNotification(_SMILEYNOTAUTHORIZE, 'error');
        redirect("index.php?file=Admin&page=smilies&op=edit_smiley&smiley_id=" . $smiley_id, 4);
        return;
    }

    $smileyUrl = '';

    if ($_FILES['fichiernom']['name'] != '') {
        list($smileyUrl, $uploadError, $smileyExt) = nkUpload_check('fichiernom', array(
            'fileType'  => 'image',
            'uploadDir' => 'images/icones',
            //'fileSize'  => 100000
        ));

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            redirect('index.php?file=Admin&page=smilies&op=edit_smiley&smiley_id='. $smiley_id, 2);
            return;
        }

        $smileyUrl = basename($smileyUrl);
    }
    else if ($_POST['url'] != '') {
        $ext = strtolower(substr(strrchr($_POST['url'], '.'), 1));

        if (! in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
            printNotification(__('BAD_IMAGE_FORMAT'), 'error');
            redirect('index.php?file=Admin&page=smilies&op=edit_smiley&smiley_id='. $smiley_id, 2);
            return;
        }

        $smileyUrl = $_POST['url'];
    }

    $sql = nkDB_execute("UPDATE " . SMILIES_TABLE . " SET code = '" . $code . "', url = '" . $smileyUrl . "', name = '" . $nom . "' WHERE id = '" . $smiley_id . "'");

    saveUserAction(_ACTIONMODIFSMILEY .': '. $nom);

    printNotification(_SMILEYMODIF, 'success');
    redirect("index.php?file=Admin&page=smilies", 2);
}

function del_smiley($smiley_id)
{
    global $nuked,$user;

    $sql2 = nkDB_execute("SELECT name FROM " . SMILIES_TABLE . " WHERE id = '" . $smiley_id . "'");
    list($name) = nkDB_fetchArray($sql2);
    $sql = nkDB_execute("DELETE FROM " . SMILIES_TABLE . " WHERE id = '" . $smiley_id . "'");

    saveUserAction(_ACTIONDELSMILEY .': '. $name);

    printNotification(_SMILEYDELETE, 'success');
    redirect("index.php?file=Admin&page=smilies", 2);
}

function nkAdminMenu()
{
    global $language, $user, $nuked;
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li>
                <a class="shortcut-button" href="index.php?file=Admin&amp;page=smilies&amp;op=add_smiley">
                    <img src="modules/Admin/images/icons/add.png" alt="icon" />
                    <span><?php echo _SMILEYADD; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($GLOBALS['op'])
{
    case "add_smiley":
        add_smiley();
        break;

    case "send_smiley":
        send_smiley($_REQUEST['nom'], $_REQUEST['code']);
        break;

    case "edit_smiley":
        edit_smiley($_REQUEST['smiley_id']);
        break;

    case "modif_smiley":
        modif_smiley($_REQUEST['smiley_id'], $_REQUEST['nom'], $_REQUEST['code']);
        break;

    case "del_smiley":
        del_smiley($_REQUEST['smiley_id']);
        break;

    case "main":
        main();
        break;

    default:
        main();
        break;
}

?>