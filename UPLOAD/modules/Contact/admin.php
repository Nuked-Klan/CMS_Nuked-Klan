<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal                                                //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $user, $language;
translate('modules/Contact/lang/' . $language . '.lang.php');
include('modules/Admin/design.php');
admintop();

$visiteur = ($user) ? $user[1] : 0;

$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1){
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
        . '<div class="tab-content" id="tab2"><div style="text-align: center">' . _LISTMAIL . '<b> | '
        . '<a href="index.php?file=Contact&amp;page=admin&amp;op=main_pref">' . _PREFS . '</a></b></div><br />'."\n"
        . '<table style="margin: auto;text-align: left" width="90%"  border="0" cellspacing="1" cellpadding="2">'."\n"
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

        echo '</table><br /><div style="text-align: center">[ <a href="index.php?file=Admin"><b>' . _BACK . '</b></a> ]</div><br /></div></div>'."\n";
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
        . '<div style="text-align: center"><br /><input type="button" value="' . _DELTHISMESS . '" onclick="javascript:delmail(\'' . $name . '\', \'' . $mid . '\');" />'."\n"
        . '<br /><br />[ <a href="index.php?file=Contact&amp;page=admin"><b>' . _BACK . '</b></a> ]</div><br /></div></div>'."\n";
    }

    function del($mid){
        global $nuked, $user;

        $sql = mysql_query('DELETE FROM ' . CONTACT_TABLE . ' WHERE id = ' . $mid);

        // Action
        $texteaction = _ACTIONDELCONTACT;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action

        echo '<div class="notification success png_bg">'."\n"
        . '<div>' . _MESSDELETE . ''."\n"
        . '</div>'."\n"
        . '</div>'."\n";

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
        . '<div class="tab-content" id="tab2"><div style="text-align: center"><b><a href="index.php?file=Contact&amp;page=admin">' . _LISTMAIL . '</a> | '
        . '</b>' . _PREFS . '</div><br />',"\n"
        . '<form method="post" action="index.php?file=Contact&amp;page=admin&amp;op=change_pref">',"\n"
        . '<table style="margin: auto;text-align: left" border="0" cellspacing="0" cellpadding="3">',"\n"
        . '<tr><td align="center"><big>' . _PREFS . '</big></td></tr>',"\n"
        . '<tr><td>' . _EMAILCONTACT . ' : <input type="text" name="contact_mail" size="40" value="' . $nuked['contact_mail'] . '" /></td></tr>',"\n"
        . '<tr><td>' . _FLOODCONTACT . ' : <input type="text" name="contact_flood" size="2" value="' . $nuked['contact_flood'] . '" /></td></tr></table>',"\n"
        . '<div style="text-align: center"><br /><input type="submit" value="' . _SEND . '" /><br />',"\n"
        . '<br />[ <a href="index.php?file=Contact&amp;page=admin"><b>' . _BACK . '</b></a> ]</div></form><br /></div></div>',"\n";
    } 

    function change_pref($contact_mail, $contact_flood){
        global $nuked, $user;

        $upd1 = mysql_query('UPDATE ' . CONFIG_TABLE . ' SET value = \'' . $contact_mail . '\' WHERE name = \'contact_mail\'');
        $upd2 = mysql_query('UPDATE ' . CONFIG_TABLE . ' SET value = \'' . $contact_flood . '\' WHERE name = \'contact_flood\'');

        // Action
        $texteaction = _ACTIONPREFCONT;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action

        echo '<div class="notification success png_bg">'."\n"
        . '<div>' . _PREFUPDATED . '</div>'."\n"
        . '</div>'."\n";

        redirect('index.php?file=Contact&page=admin', 2);
    } 

    switch($_REQUEST['op']){
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

} 
else if ($level_admin == -1){
    echo '<div class="notification error png_bg">'."\n"
    . '<div>'."\n"
    . '<br /><br /><div style="text-align: center">' . _MODULEOFF . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a></div><br /><br />'."\n"
    . '</div>'."\n"
    . '</div>'."\n";
}
else if ($visiteur > 1){
    echo '<div class="notification error png_bg">'."\n"
    . '<div>'."\n"
    . '<br /><br /><div style="text-align: center">' . _NOENTRANCE . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a></div><br /><br />'."\n"
    . '</div>'."\n"
    . '</div>'."\n";
}
else{
    echo '<div class="notification error png_bg">'."\n"
    . '<div>'."\n"
    . '<br /><br /><div style="text-align: center">' . _ZONEADMIN . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a></div><br /><br />'."\n"
    . '</div>'."\n"
    . '</div>'."\n";
}

adminfoot();
?>