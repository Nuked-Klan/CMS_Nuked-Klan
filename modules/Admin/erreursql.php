<?php
/**
 * erreursql.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', ADMINISTRATOR_ACCESS))
    return;


function main()
{
    global $user, $nuked, $language;

    echo"<script type=\"text/javascript\">\n"
    ."<!--\n"
    ."\n"
    . "function delfile()\n"
    . "{\n"
    . "if (confirm('" . _DELETEFILE . " " . _VIDERSQL . " ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Admin&page=erreursql&op=delete';}\n"
    . "}\n"
    . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADMINSQLERROR . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Erreursql.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    nkAdminMenu();

    echo '<table><tr><td><b>', __('DATE'), '</b>', "\n"
        , '</td><td><b>', __('URL'), '</b>', "\n"
        , '</td><td style="text-transform:capitalize;"><b>', __('FILE'), '</b>', "\n"
        , '</td><td style="text-transform:capitalize;"><b>', __('LINE'), '</b>', "\n"
        , '</td></tr>', "\n";

    $dbrSqlError = nkDB_selectMany(
        'SELECT `date`, `url`, `error`, `code`, `line`, `file`
        FROM '. SQL_ERROR_TABLE,
        array('date'), 'DESC'
    );

    foreach ($dbrSqlError as $sqlError) {
        echo '<tr><td>', nkDate($sqlError['date']), '</td>', "\n"
            , '<td><a href="', $sqlError['url'], '">', $sqlError['url'], '</a></td>', "\n"
            , '<td>', $sqlError['file'], '</td>', "\n"
            , '<td>', $sqlError['line'], '</td></tr>', "\n"
            , '<tr><td colspan="4">', __('CODE'), ' : ', $sqlError['code'], '<br/>'
            , $sqlError['error'], '</td></tr>', "\n";
    }

    echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . __('BACK') . "</a></div></form><br /></div></div>\n";
}

function delete()
{
    global $user, $nuked, $visiteur;

    if ($visiteur == '9')
        $sql3 = mysql_query("DELETE FROM ". SQL_ERROR_TABLE);

    saveUserAction(_ACTIONVIDERSQL);

    printNotification(_SQLERRORDELETED, 'success');
    redirect('index.php?file=Admin&page=erreursql', 1);
}

function nkAdminMenu()
{
    global $language, $user, $nuked;
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li>
                <a class="shortcut-button" href="javascript:delfile();">
                    <img src="modules/Admin/images/icons/remove_from_database.png" alt="icon" />
                    <span><?php echo _VIDERSQL; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($GLOBALS['op']) {
    case 'main':
        main();
        break;
    case 'delete':
        delete();
        break;
    default:
        main();
        break;
}

?>