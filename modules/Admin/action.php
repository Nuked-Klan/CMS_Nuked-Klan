<?php
/**
 * action.php
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

    $nbActions = 50;

    $sqlNbActions = nkDB_execute("SELECT id FROM " . ACTION_TABLE);
    $count = mysql_num_rows($sqlNbActions);

    if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
    $start = $_REQUEST['p'] * $nbActions - $nbActions;

    echo '<div class="content-box">',"\n" //<!-- Start Content Box -->
    . '<div class="content-box-header"><h3>' . _ADMINACTION . '</h3>',"\n"
    . '<div style="text-align:right"><a href="help/' . $language . '/Action.php" rel="modal">',"\n"
    . '<img style="border: 0" src="help/help.gif" alt="" title="' . _HELP . '" /></a>',"\n"
    . '</div></div>',"\n"
    . '<div class="tab-content" id="tab2">',"\n";

    printNotification(_INFOACTION);

    if ($count > $nbActions){
        echo "<table width=\"100%\"><tr><td>";
        number($count, $nbActions, "index.php?file=Admin&page=action");
        echo"</td></tr></table>\n";
    }

    echo '<br /><table><tr><td><b>' . _DATE . '</b>',"\n"
    . '</td><td><b>' . _INFORMATION . '</b>',"\n"
    . '</td></tr>',"\n";

    $sql = nkDB_execute(
        "SELECT date, author, action
        FROM ". ACTION_TABLE ."
        ORDER BY date DESC
        LIMIT " . $start . ", " . $nbActions
    );

    while (list($date, $author, $texte) = nkDB_fetchArray($sql))
    {
        $date = nkDate($date);

        echo '<tr><td>' . $date . '</td>',"\n"
        . '<td>' . $author . ' ' . $texte . '</td></tr>',"\n";

    }

    echo '</table>';

    if ($count > $nbActions){
        echo "<table width=\"100%\"><tr><td>";
        number($count, $nbActions, "index.php?file=Admin&page=action");
        echo "</td></tr></table>";
    }

    echo '<div style="text-align: center"><br /><a class="buttonLink" href="index.php?file=Admin">' . __('BACK') . '</a></div></form><br /></div></div>',"\n";
    $theday = time();
    $compteur = 0;
    $delete = nkDB_execute("SELECT id, date  FROM " . ACTION_TABLE . " ORDER BY date DESC");
    while (list($id, $date) = nkDB_fetchArray($delete))
    {
        $limit_time = $date + 1209600;

        if ($limit_time < $theday)
        {
            $del = nkDB_execute("DELETE FROM " . ACTION_TABLE . " WHERE id = '" . $id . "'");
            $compteur++;
        }
    }
    if ($compteur > 0)
    {
        if($compteur ==1) $text = $compteur. ' ' ._1NBRNOTACTION;
        else $text = $compteur . ' ' . _NBRNOTACTION;

        saveNotification($text, 3);
    }
}


switch ($GLOBALS['op']) {
    case 'main':
        main();
        break;
    default:
        main();
        break;
}

?>