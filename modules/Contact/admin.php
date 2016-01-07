<?php
/**
 * admin.php
 *
 * Backend of Contact module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Contact'))
    return;


function main(){
    global $nuked, $language;

    echo '<script type="text/javascript">'."\n"
    . '<!--'."\n"
    . "\n"
    . 'function delmail(nom, id)'."\n"
    . "{\n"
    . 'if (confirm(\'' . _DELETEMESSAGEFROM . ' : \'+nom+\' ?\'))'."\n"
    . '{document.location.href = \'index.php?file=Contact&page=admin&op=del&mid=\'+id;}'."\n"
    . "}\n"
    . "\n"
    . '// -->'."\n"
    . '</script>'."\n";

    echo '<div class="content-box">'."\n" //<!-- Start Content Box -->
    . '<div class="content-box-header"><h3>' . _ADMINCONTACT . '</h3>'."\n"
    . '<div style="text-align:right"><a href="help/' . $language . '/Contact.php" rel="modal">'."\n"
    . '<img style="border: 0" src="help/help.gif" alt="" title="' . _HELP . '" /></a>',"\n"
    . '</div></div>'."\n"
    . '<div class="tab-content" id="tab2">'."\n";

    nkAdminMenu(1);

    echo '<table style="margin: auto;text-align: left" width="90%"  border="0" cellspacing="1" cellpadding="2">'."\n"
    . '<tr>'."\n"
    . '<td style="width: 10%;text-align:center;" ><b>#</b></td>'."\n"
    . '<td style="width: 30%;text-align:center;" ><b>' . _TITLE . '</b></td>'."\n"
    . '<td style="width: 20%;text-align:center;" ><b>' . _NAME . '</b></td>'."\n"
    . '<td style="width: 20%;text-align:center;" ><b>' . _DATE . '</b></td>'."\n"
    . '<td style="width: 10%;text-align:center;" ><b>' . _READMESS . '</b></td>'."\n"
    . '<td style="width: 10%;text-align:center;" ><b>' . _DEL . '</b></td></tr>'."\n";

    $sql = mysql_query('SELECT id, titre, nom, email, date FROM ' . CONTACT_TABLE . ' ORDER BY id');
    $count = mysql_num_rows($sql);
    $l = 0;

    while (list($id, $titre, $nom, $email, $date) = mysql_fetch_array($sql)){
        $day = nkDate($date);
        $l++;

        if (strlen($titre) > 45) $title = substr($titre, 0, 45) . '...';
        else $title = $titre;

        $name = addslashes($nom);
        echo '<tr>'."\n"
        . '<td style="width: 10%;text-align:center;" >' . $l . '</td>'."\n"
        . '<td style="width: 30%;text-align:center;" >' . $title . '</td>'."\n"
        . '<td style="width: 20%;text-align:center;" ><a href="mailto:' . $email . '">' . $nom . '</a></td>'."\n"
        . '<td style="width: 20%;text-align:center;" >' . $day . '</td>'."\n"
        . '<td style="width: 10%;text-align:center;" ><a href="index.php?file=Contact&amp;page=admin&amp;op=view&amp;mid=' . $id . '"><img style="border: 0" src="images/report.gif" alt="" title="' . _READTHISMESS. '" /></a></td>'."\n"
        . '<td style="width: 10%;text-align:center;" ><a href="javascript:delmail(\'' . $name . '\',\'' . $id . '\');"><img style="border: 0" src="images/del.gif" alt="" title="' . _DELTHISMESS . '" /></a></td></tr>'."\n";
    }

    if ($count == 0) echo '<tr><td align="center" colspan="6">' . _NOMESSINDB . '</td></tr>'."\n";

    echo '</table><br /><div style="text-align: center"><a class="buttonLink" href="index.php?file=Admin">' . __('BACK') . '</a></div><br /></div></div>'."\n";
}

function view($mid){
    global $nuked, $language;

    $sql = mysql_query('SELECT titre, message, nom, ip, email, date FROM ' . CONTACT_TABLE . ' WHERE id = ' . $mid);
    list($titre, $message, $nom, $ip, $email, $date) = mysql_fetch_array($sql);

    $day = nkDate($date);

    $message = str_replace('\r', '', $message);
    $message = str_replace('\n', '<br />', $message);
    $name = addslashes($nom);

    echo '<script type="text/javascript">'."\n"
    . '<!--'."\n"
    . "\n"
    . 'function delmail(nom, id)'."\n"
    . "{\n"
    . 'if (confirm(\'' . _DELETEMESSAGEFROM . ' : \'+nom+\' ?\'))'."\n"
    . '{document.location.href = \'index.php?file=Contact&page=admin&op=del&mid=\'+id;}'."\n"
    . "}\n"
    . "\n"
    . '// -->'."\n"
    . '</script>'."\n";

    echo '<div class="content-box">'."\n" //<!-- Start Content Box -->
    . '<div class="content-box-header"><h3>' . _ADMINCONTACT . '</h3>'."\n"
    . '<div style="text-align:right"><a href="help/' . $language . '/Contact.php" rel="modal">'."\n"
    . '<img style="border: 0" src="help/help.gif" alt="" title="' . _HELP . '" /></a>'."\n"
    . '</div></div>'."\n"
    . '<div class="tab-content" id="tab2"><table style="margin: auto;text-align: left" width="90%" cellspacing="1" cellpadding="4">'."\n"
    . '<tr><td>' . _FROM . '  <a href="mailto:' . $email . '"><b>' . $nom . '</b></a> (IP : ' . $ip . ') ' . _THE . ' ' . $day . '</td></tr>'."\n"
    . '<tr><td><b>' . _YSUBJECT . ' :</b> ' . $titre . '</td></tr>'."\n"
    . '<tr><td><br />' . $message . '</td></tr></table>'."\n"
    . '<div style="text-align: center"><br /><input class="button" type="button" value="' . _DELTHISMESS . '" onclick="javascript:delmail(\'' . $name . '\', \'' . $mid . '\');" /><a class="buttonLink" href="index.php?file=Contact&amp;page=admin">' . __('BACK') . '</a>'."\n"
    . '</div><br /></div></div>'."\n";
}

function del($mid){
    global $nuked, $user;

    $sql = mysql_query('DELETE FROM ' . CONTACT_TABLE . ' WHERE id = ' . $mid);

    saveUserAction(_ACTIONDELCONTACT);

    printNotification(_MESSDELETE, 'success');
    redirect('index.php?file=Contact&page=admin', 2);
}

function main_pref()
{
    global $nuked, $language;

    echo '<div class="content-box">',"\n" //<!-- Start Content Box -->
    . '<div class="content-box-header"><h3>' . _ADMINCONTACT . '</h3>',"\n"
    . '<div style="text-align:right"><a href="help/' . $language . '/Contact.php" rel="modal">',"\n"
    . '<img style="border: 0" src="help/help.gif" alt="" title="' . _HELP . '" /></a>',"\n"
    . '</div></div>',"\n"
    . '<div class="tab-content" id="tab2">'."\n";

    nkAdminMenu(2);

    echo '<form method="post" action="index.php?file=Contact&amp;page=admin&amp;op=change_pref">',"\n"
    . '<table style="margin: auto;text-align: left" border="0" cellspacing="0" cellpadding="3">',"\n"
    . '<tr><td align="center"><big>' . _PREFS . '</big></td></tr>',"\n"
    . '<tr><td>' . _EMAILCONTACT . ' : <input type="text" name="contact_mail" size="40" value="' . $nuked['contact_mail'] . '" /></td></tr>',"\n"
    . '<tr><td>' . _FLOODCONTACT . ' : <input type="text" name="contact_flood" size="2" value="' . $nuked['contact_flood'] . '" /></td></tr></table>',"\n"
    . '<div style="text-align: center"><br /><input class="button" type="submit" value="' . __('SEND') . '" /><a class="buttonLink" href="index.php?file=Contact&amp;page=admin">' . __('BACK') . '</a><br />',"\n"
    . '</div></form><br /></div></div>',"\n";
}

function change_pref($contact_mail, $contact_flood){
    global $nuked, $user;

    $upd1 = mysql_query('UPDATE ' . CONFIG_TABLE . ' SET value = \'' . $contact_mail . '\' WHERE name = \'contact_mail\'');
    $upd2 = mysql_query('UPDATE ' . CONFIG_TABLE . ' SET value = \'' . $contact_flood . '\' WHERE name = \'contact_flood\'');

    saveUserAction(_ACTIONPREFCONT);

    printNotification(_PREFUPDATED, 'success');
    redirect('index.php?file=Contact&page=admin', 2);
}

function nkAdminMenu($tab = 1) {
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Contact&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _LISTMAIL; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=Contact&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch($_REQUEST['op']) {
    case 'view':
    view($_REQUEST['mid']);
    break;

    case 'del':
    del($_REQUEST['mid']);
    break;

    case 'main_pref':
    main_pref();
    break;

    case 'change_pref':
    change_pref($_REQUEST['contact_mail'], $_REQUEST['contact_flood']);
    break;

    default:
    main();
    break;
}

?>