<?php
/**
 * mysql.php
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


function main() {
    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADMINMYSQL . "</h3></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=mysql&amp;op=upgrade_db\" enctype=\"multipart/form-data\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td align=\"center\"><b>" . _DATABASE . " :</b> <select onchange=\"if (this.selectedIndex != 0) document.location = this.options[this.selectedIndex].value;\">\n"
    . "<option value=\"0\">" . _ACTION . "</option>\n"
    . "<option value=\"index.php?file=Admin&amp;page=mysql&amp;op=save_db\">" . _SAVEDB . "</option>\n"
    . "<option value=\"index.php?file=Admin&amp;page=mysql&amp;op=optimise\">" . _OPTIMIZEDB . "</option>\n"
    . "</select></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div></form><br /></div>";
}

function save_db()
{
    global $global, $nuked, $user;

    nkTemplate_setPageDesign('none');

    require("modules/Admin/class/iam_backup.php");

    $host = $global['db_host'];
    $base = $global['db_name'];
    $login = $global['db_user'];
    $password = $global['db_pass'];

    $backup = new iam_backup($host, $base, $login, $password, false, true, false);
    $backup->perform_backup();

    saveUserAction(_ACTIONSAVEDB);
}

function optimise()
{
    global $nuked, $global, $user;

    /**
    */
    /* Optimize your database                                                      */
    /*                                                                             */
    /* Copyright (c) 2001 by Xavier JULIE (webmaster@securite-internet.org         */
    /* http://www.securite-internet.org                                            */
    /* Adapted and modified for nuked-klan By Specops (specops57@hotmail.com)      */
    /* And MaStErPsX (masterpsx@numericable.fr) 				       */
    /* 									       */
    /**
    */

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADMINMYSQL . "</h3></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><br />" . _OPTIMIZEDATABASE . " : <b>" . $global['db_name'] . "</b></div>\n"
    . "<br /><div style=\"width:96%\"><table style=\"margin-left: 2%;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 35%;\" align=\"center\"><b>" . _TABLE . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _SIZE . "</b></td>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _STATUT . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _SPACESAVED . "</b></td></tr>\n";

    $db_clean = $global['db_name'];
    $tot_data = 0;
    $tot_idx = 0;
    $tot_all = 0;
    $local_query = 'SHOW TABLE STATUS FROM `' . $global['db_name'] . '`';
    $result = nkDB_execute($local_query);
    if (is_resource($result) && nkDB_numRows($result))
    {
        while ($row = nkDB_fetchArray($result))
        {
            $tot_data = $row['Data_length'];
            $tot_idx = $row['Index_length'];
            $total = $tot_data + $tot_idx;
            $total = $total / 1024 ;
            $total = round ($total, 3);
            $gain = $row['Data_free'];
            $gain = $gain / 1024 ;
            $total_gain += $gain;
            $gain = round ($gain, 3);
            $local_query = "OPTIMIZE TABLE " . $row[0];
            $resultat = nkDB_execute($local_query);


            if ($gain == 0) $statut = _NOOPTIMIZE;
    else $statut = "<b>" . _OPTIMIZE . "</b>";

    echo "<tr>\n"
    . "<td style=\"width: 35%;\">" . $row[0] . "</td>\n"
    . "<td style=\"width: 20%;\" align=\"center\">" . $total . " Kb</td>\n"
    . "<td style=\"width: 25%;\" align=\"center\">" . $statut . "</td>\n"
    . "<td style=\"width: 20%;\" align=\"center\">" . $gain . " Kb</td></tr>\n";
        }
    }
    echo "</table><br />";

    $total_gain = round ($total_gain, 3);

    echo "<div style=\"text-align: center;\"><br /><b>" . _TOTAL . "</b> : " . $total_gain . " Kb</div>\n"
    . "<div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=mysql\">" . __('BACK') . "</a></div><br /></div></div>\n";

    saveUserAction(_ACTIONOPTIDB);
}


switch ($GLOBALS['op']) {
    case "save_db":
        save_db();
        break;

    case "optimise":
        optimise();
        break;

    default:
        main();
        break;
}


?>