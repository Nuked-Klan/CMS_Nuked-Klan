<?php
/**
 * index.php
 *
 * Frontend of Userbox module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

global $language, $user;

nkTemplate_moduleInit('Userbox');

translate('modules/Userbox/lang/'. $language .'.lang.php');


function select_user(){
    global $nuked;

    $sql = nkDB_execute('SELECT id, pseudo, niveau FROM '.USER_TABLE.' WHERE niveau > 0 ORDER BY niveau DESC, pseudo');
    while($row = nkDB_fetchAssoc($sql)){

        if ($row['niveau'] == 9) $nivo = "****";
        elseif ($row['niveau'] > 2) $nivo = "**";
        else if ($row['niveau'] == 2) $nivo = "*";
        else $nivo = "";
        echo '<option value="'.$row['id'].'">'.$row['pseudo'] . $nivo.'</option>';
    }
}

function post_message(){
    global $user, $bgcolor3;

    define('EDITOR_CHECK', 1);

    $forUsername = null;

    if (!empty($_REQUEST['for']) && preg_match("`^[a-zA-Z0-9]+$`", $_REQUEST['for'])){
        $sql = nkDB_execute("SELECT pseudo FROM ".USER_TABLE." WHERE id = '{$_REQUEST['for']}'");
        list($forUsername) = nkDB_fetchArray($sql);
    }

    $title = $reply = '';

    if (!empty($_REQUEST['titre'])){
        $_REQUEST['titre'] = stripslashes($_REQUEST['titre']);
        $_REQUEST['titre'] = nkHtmlEntities($_REQUEST['titre']);
        if (!preg_match("/\bRE:\b/i", $_REQUEST['titre'])) $title = "RE:" . $_REQUEST['titre'];
        else $title = $_REQUEST['titre'];
    }

    if (!empty($_REQUEST['message'])){
        $_REQUEST['message'] = secu_html(nkHtmlEntityDecode($_REQUEST['message']));
        $_REQUEST['message'] = stripslashes($_REQUEST['message']);
        $reply = '<br /><table style="background:'.$bgcolor3.';" cellpadding="3" cellspacing="1" width="100%" border="0"><tr><td style="background: #FFF;color: #000"><b>'.$forUsername.' :</b><br />'.$_REQUEST['message'].'</td></tr></table><br />';
    }

    echo '<script type="text/javascript">
    function checkPostPM(){
        if(document.getElementById(\'pmTitle\').value.length == 0){
            alert(\''. _TITLEPMFORGOT .'\');
            return false;
        }
        if($.trim(getEditorContent(\'e_basic\')) == ""){
            alert(\''. _TEXTPMFORGOT .'\');
            return false;
        }

        return true;
    }
    </script>';

    echo '<br /><form onsubmit="return checkPostPM()" method="post" action="index.php?file=Userbox&amp;op=send_message">'
         .'<table style="margin: auto;text-align:left;width: 98%">'
         .'<tr><td align="center"><big><b>'._POSTMESS.'</b></big><br /><br /></td></tr>'
         .'<tr><td><b>'.__('AUTHOR').' :</b> '.$user[2].'</td></tr>'
         .'<tr><td><b>'._USERFOR.' :</b> ';

    if ($forUsername !== null){
        echo '<i>'.$forUsername.'</i><input type="hidden" name="user_for" value="'.$_REQUEST['for'].'" />';
    }
    else {
        echo '<select name="user_for" >';
        select_user();
        echo '</select>';
    }

    echo '</td></tr><tr><td><b>'._SUBJECT.' :</b> <input id="pmTitle" type="text" name="titre" size="30" value="'.$title.'" /></td></tr>
            <tr><td><b>'._USERMESS.' :</b><br /><textarea id="e_basic" name="message" cols="65" rows="10">'.$reply.'</textarea></td></tr>
            <tr><td align="center"><br /><input type="submit" name="send" value="'.__('SEND').'" />&nbsp;<input type="button" value="'._CANCEL.'" onclick="javascript:history.back()" /></td></tr></table></form><br />';
}

function send_message($titre, $user_for, $message){
    global $user, $nuked;

    if (empty($titre) || empty($user_for) || empty($message)){
        printNotification(_EMPTYFIELD, 'error', array('backLinkUrl' => 'javascript:history.back()'));
	}
	else {
		if (!empty($user_for) && preg_match("`^[a-zA-Z0-9]+$`", $user_for)) {
			$sql2 = nkDB_execute("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '$user_for'");
			$nb = nkDB_numRows($sql2);
		}
		else $nb = 0;

		if ($nb == 0) {
			printNotification(_UNKNOWMEMBER, 'error', array('backLinkUrl' => 'javascript:history.back()'));
		}
		else {
			$flood = nkDB_execute("SELECT date FROM " . USERBOX_TABLE . " WHERE user_from = '" . $user[0] . "' ORDER BY date DESC LIMIT 0, 1");
			list($flood_date) = nkDB_fetchArray($flood);
			$anti_flood = $flood_date + $nuked['post_flood'];
			$date = time();

			if ($date < $anti_flood){
				printNotification(_UNOFLOOD, 'error');// TODO : Backlink ?
				redirect('index.php?file=Userbox', 2);
				return;
			}

			$message = secu_html(nkHtmlEntityDecode($message));
			$titre = nkDB_realEscapeString(stripslashes($titre));
			$message = nkDB_realEscapeString(stripslashes($message));
			$user_for = nkDB_realEscapeString(stripslashes($user_for));
			$titre = nkHtmlEntities($titre);

			nkDB_execute(
                "INSERT INTO ". USERBOX_TABLE ."
                (`user_from`, `user_for`, `titre`, `message`, `date`)
                VALUES
                ('{$user[0]}', '$user_for', '$titre', '$message', '$date')");

			printNotification(_MESSSEND, 'success');
			redirect("index.php?file=Userbox", 2);
		}
	}
}

function show_message($mid){
    global $user, $nuked, $bgcolor2, $bgcolor3;

    $mid = (int) $mid;

    echo '<script type="text/javascript">function del_mess(pseudo, id){if (confirm(\''._DELETEMESS.' \'+pseudo+\' ! '._CONFIRM.'\')){document.location.href = \'index.php?file=Userbox&op=del_message&mid=\'+id;}}</script>';

    nkDB_execute("UPDATE " . USERBOX_TABLE . " SET status = 1 WHERE mid = '$mid' AND user_for = '{$user[0]}'");

    echo '<br /><div style="text-align:center;"><big><b>'._PRIVATEMESS.'</b></big></div><br /><br />';

    $sql2 = nkDB_execute("SELECT titre, message, user_from, date FROM " . USERBOX_TABLE . " WHERE mid = '" . $mid . "' AND user_for = '" . $user[0] . "'");
    $row = nkDB_fetchAssoc($sql2);

    if ($row) {
        $row['date'] = nkDate($row['date']);

        if(strlen($row['titre']) >= 50)
            $row['titre'] = printSecuTags(substr($row['titre'], 0, 47))."...";
        else
            $row['titre'] = printSecuTags($row['titre']);

        $sql_member = nkDB_execute("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '{$row['user_from']}'");
        list($pseudo) = nkDB_fetchArray($sql_member);

        echo '<table style="margin:0 auto;text-align:left;background:'.$bgcolor3.';" width="90%" cellspacing="1" cellpadding="4">
                <tr style="background:'.$bgcolor3.';"><td>'._OF.'  <a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($pseudo).'"><b>'.$pseudo.'</b></a> '._THE.'&nbsp;'.$row['date'].'</td></tr>
                <tr style="background:'.$bgcolor2.';"><td><b>'._SUBJECT.' :</b> '.$row['titre'].'</td></tr>
                <tr style="background:'.$bgcolor2.';"><td>'.$row['message'].'</td></tr></table>
                <br /><form method="post" action="index.php?file=Userbox&amp;op=post_message&amp;for='.$row['user_from'].'">
                <div style="text-align:center;">
                <input type="hidden" name="message" value="'.nkHtmlEntities($row['message']).'" />
                <input type="hidden" name="titre" value="'.nkHtmlEntities($row['titre']).'" />
                <input type="submit" value="'._REPLY.'" />&nbsp;
                <input type="button" value="'._DEL.'" onclick="javascript:del_mess(\''.addslashes($pseudo).'\', \''.$mid.'\');" />
                <br /><br />[ <a href="index.php?file=Userbox"><b>'.__('BACK').'</b></a> ]</div></form><br />';
    }
    else {
        echo applyTemplate('nkAlert/noEntrance');
    }
}

function del_message($mid){
    global $user, $nuked;

    $mid = (int) $mid;

    $sql = nkDB_execute("SELECT mid FROM " . USERBOX_TABLE . " WHERE  mid = '$mid' AND user_for = '{$user[0]}'");
    $nbr = nkDB_numRows($sql);

    if($nbr > 0){
        nkDB_execute("DELETE FROM " . USERBOX_TABLE . " WHERE mid = '$mid' AND user_for = '{$user[0]}'");

        printNotification(_MESSDEL, 'success');
    }
    else
        printNotification(_UNKNOWPM, 'error');

    redirect('index.php?file=Userbox', 2);
}

function del_message_form($mid){
    global $user, $nuked;

    if (isset($_REQUEST['confirm']) && $_REQUEST['confirm'] == 'ok'){
        $sql = nkDB_execute("SELECT mid FROM " . USERBOX_TABLE . " WHERE user_for = '{$user[0]}' ORDER BY mid");
        $nb_mess = nkDB_numRows($sql);

        if ($nb_mess > 0 && is_array($mid)){
            $get_mid = 0;

            while ($nb_mess > $get_mid){
                if (isset($mid[$get_mid]))
                    nkDB_execute("DELETE FROM " . USERBOX_TABLE . " WHERE mid = '".$mid[$get_mid]."'");

                $get_mid++;
            }
        }

        printNotification(_MESSAGESDEL, 'success');
        redirect('index.php?file=Userbox', 2);

    }else{

        if (!$mid){
            printNotification(_NOSELECTMESS, 'error');
            redirect('index.php?file=Userbox', 2);
            return;
        }

        $sql = nkDB_execute("SELECT mid FROM " . USERBOX_TABLE . " WHERE user_for = '{$user[0]}' ORDER BY mid");
        $nb_mess = nkDB_numRows($sql);

        echo '<form method="post" action="index.php?file=Userbox&amp;op=del_message_form&amp;confirm=ok">
                <div style="text-align:center;"><br /><br /><b>'._DELETEMESSAGES.' :</b><br /><br />';

        if ($nb_mess > 0 && is_array($mid)){
            $get_mid = 0;

            while ($nb_mess > $get_mid){
                if (isset($mid[$get_mid])){
                    $sql_mess = nkDB_execute("SELECT user_from, date FROM " . USERBOX_TABLE . " WHERE user_for = '{$user[0]}' AND mid = '". $mid[$get_mid] ."'");
                    $row = nkDB_fetchAssoc($sql_mess);
                    $row['date'] = nkDate($row['date']);

                    $sql_member = nkDB_execute("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '{$row['user_from']}'");
                    list($pseudo) = nkDB_fetchArray($sql_member);

                    echo '<b><big>·</big></b>&nbsp;'._OF.'&nbsp;'.$pseudo.' ( '.$row['date'].' )<br />
                    <input type="hidden" name="mid[]" value="'.$mid[$get_mid].'" />';
                }

                $get_mid++;
            }
        }

        echo '<br /><br /><input type="submit" value="'._DELCONFIRM.'" />
                &nbsp;<input type="button" value="'._CANCEL.'" onclick="document.location=\'index.php?file=Userbox\'" /></div></form><br />';
    }
}

function index(){
    global $user, $nuked, $bgcolor1, $bgcolor2, $bgcolor3;

    echo '<script type="text/javascript">function setCheckboxes(checkbox, nbcheck, do_check){for (var i = 0; i < nbcheck; i++){cbox = checkbox + i;document.getElementById(cbox).checked = do_check;}return true;}</script>
            <form method="post" action="index.php?file=Userbox&amp;op=del_message_form">
            <div style="text-align:center;"><br /><big><b>'._PRIVATEMESS.'</b></big><br /></div>
            <table style="background:'.$bgcolor2.';border:1px solid '.$bgcolor3.';" width="100%" cellpadding="2" cellspacing="1">
            <tr style="background:'.$bgcolor3.';">
            <td style="width:3%;" align="center">&nbsp;</td>
            <td align="center"><b>'._FROM.'</b></td>
            <td align="center"><b>'._SUBJECT.'</b></td>
            <td align="center"><b>'._DATE.'</b></td>
            <td align="center"><b>'._STATUS.'</b></td>
            <td align="center"><b>'._READMESS.'</b></td></tr>';

    $sql = nkDB_execute("SELECT mid, titre, user_from, date, status FROM " . USERBOX_TABLE . " WHERE user_for = '{$user[0]}' ORDER BY date DESC");
    $nb_mess = nkDB_numRows($sql);
    $i = 0;
    $j = 0;
    while($row = nkDB_fetchAssoc($sql)){

        $row['date'] = nkDate($row['date']);

        $sql_member = nkDB_execute("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '{$row['user_from']}'");
        list($pseudo) = nkDB_fetchArray($sql_member);

        $etat = ($row['status'] == 1) ? _UREAD : _UNOTREAD;

        if ($j == 0){
            $bg = $bgcolor2;
            $j++;
        }else{
            $bg = $bgcolor1;
            $j = 0;
        }

        if(strlen($row['titre']) >= 50)
            $row['titre'] = printSecuTags(substr($row['titre'], 0, 47))."...";
        else
            $row['titre'] = printSecuTags($row['titre']);

        echo '<tr style="background:'.$bg.';">
                <td><input id="box'.$i.'" type="checkbox" class="checkbox" name="mid[]" value="'.$row['mid'].'" /></td>
                <td align="center">'.$pseudo.'</td>
                <td align="center">'.$row['titre'].'</td>
                <td align="center">'.$row['date'].'</td>
                <td align="center">'.$etat.'</td>
                <td align="center"><a href="index.php?file=Userbox&amp;op=show_message&amp;mid='.$row['mid'].'"><img style="border:none;" src="modules/Userbox/images/read.png" alt="" /></a></td></tr>';

        $i++;
    }

    if($nb_mess == 0) echo '<tr style="background:'.$bgcolor2.';"><td colspan="6" align="center">'._NOMESSPV.'</td></tr>';

    echo '</table>';

    if ($nb_mess > 1){
        echo '<div style="text-align:left;">&nbsp;<img src="modules/Userbox/images/flech_coch.png" alt="" style="margin-top:4px" />
                <a href="#" onclick="setCheckboxes(\'box\', \''.$nb_mess.'\', true);">'._CHECKALL.'</a> /
                <a href="#" onclick="setCheckboxes(\'box\', \''.$nb_mess.'\', false);">'._UNCHECKALL.'</a><br /></div>';
    }

    echo '<div style="text-align:center;"><br />';

    if ($nb_mess > 0){
        $button = _SENDNEWMESS;
        echo '<input type="submit" value="'._DEL.'" />&nbsp;';
    }
    else $button = _POSTMESS;

    echo '<input type="button" value="'.$button.'" onclick="document.location=\'index.php?file=Userbox&amp;op=post_message\'" />
            <br /><br />[ <a href="index.php?file=User"><b>'.__('BACK').'</b></a> ]</div></form><br />';
}


if ($user) {
    switch ($GLOBALS['op']){

        case 'post_message':
        opentable();
        post_message();
        closetable();
        break;

        case 'send_message':
        opentable();
        send_message($_REQUEST['titre'], $_REQUEST['user_for'], $_REQUEST['message']);
        closetable();
        break;

        case 'show_message':
        opentable();
        show_message($_REQUEST['mid']);
        closetable();
        break;

        case 'del_message':
        opentable();
        del_message($_REQUEST['mid']);
        closetable();
        break;

        case 'del_message_form':
        opentable();
        del_message_form($_REQUEST['mid']);
        closetable();
        break;

        default:
        opentable();
        index();
        closetable();
        break;
    }
} else {
    opentable();
    echo applyTemplate('nkAlert/userEntrance');
    closetable();
}

?>
