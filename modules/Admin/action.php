<?php
/**
 * @version     1.7.10
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $user, $nuked, $language;
translate('modules/Admin/lang/' . $language . '.lang.php');
include('modules/Admin/design.php');

$visiteur = $user ? $user[1] : 0;

if ($visiteur >= 2)
{
    function main()
    {
        global $user, $nuked, $language;

        echo '<div class="content-box">',"\n" //<!-- Start Content Box -->
        . '<div class="content-box-header"><h3>' . _ADMINACTION . '</h3>',"\n"
        . '<div style="text-align:right"><a href="help/' . $language . '/Action.php" rel="modal">',"\n"
        . '<img style="border: 0" src="help/help.gif" alt="" title="' . _HELP . '" /></a>',"\n"
        . '</div></div>',"\n"
        . '<div class="tab-content" id="tab2"><br />',"\n"
        . '<div style="width:95%; margin:auto;"class="notification information png_bg">',"\n"
        . '<div>' . _INFOACTION . '</div></div>',"\n"
        . '<br /><table><tr><td><b>' . _DATE . '</b>',"\n"
        . '</td><td><b>' . _INFORMATION . '</b>',"\n"
        . '</td></tr>',"\n";

        $sql = mysql_query("SELECT date, pseudo, action  FROM " . $nuked['prefix'] . "_action ORDER BY date DESC LIMIT 0, 50");
        while (list($date, $users, $texte) = mysql_fetch_array($sql))
        {
            if($users != '')
            {
                $users = mysql_real_escape_string($users);
            
                $sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $users . "'");
                list($pseudo) = mysql_fetch_array($sql2);
            }
            else $pseudo = 'N/A';

            $date = nkDate($date);
            $texte = $pseudo . ' ' . $texte;

            echo '<tr><td>' . $date . '</td>',"\n"
            . '<td>' . $texte . '</td></tr>',"\n";

        }

        echo '</table><div style="text-align: center"><br /><a class="buttonLink" href="index.php?file=Admin">' . _BACK . '</a></div></form><br /></div></div>',"\n";
        $theday = time();
        $compteur = 0;
        $delete = mysql_query("SELECT id, date  FROM " . $nuked['prefix'] . "_action ORDER BY date DESC");
        while (list($id, $date) = mysql_fetch_array($delete))
        {
            $limit_time = $date + 1209600;

            if ($limit_time < $theday)
            {
                $del = mysql_query("DELETE FROM " . $nuked['prefix'] . "_action WHERE id = '" . $id . "'");
                $compteur++;
            }
        }
        if ($compteur > 0)
        {
            if($compteur ==1) $text = $compteur. ' ' ._1NBRNOTACTION;
            else $text = $compteur . ' ' . _NBRNOTACTION;

            $upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('" . $theday . "', '3', '" . $text . "')");
        }
    }
    switch ($_REQUEST['op'])
    {
        case 'main':
        admintop();
        main();
        adminfoot();
        break;
        default:
        admintop();
        main();
        adminfoot();
        break;
    }

}
else if ($visiteur > 1)
{
    admintop();
    echo '<div class="notification error png_bg">',"\n"
    . '<div>',"\n"
    . '<br /><br /><div style="text-align: center">' . _NOENTRANCE . '<br /><br /><a class="buttonLink" href="javascript:history.back()">' . _BACK . '</a></div><br /><br />',"\n"
    . '</div>',"\n"
    . '</div>',"\n";
    adminfoot();
}
else
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a class=\"buttonLink\" href=\"javascript:history.back()\">" . _BACK . "</a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
    adminfoot();
}
?>
