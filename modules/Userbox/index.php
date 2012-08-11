<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die('<div style="text-align:center;">You cannot open this page directly</div>');

global $language, $user;
translate('modules/Userbox/lang/'.$language.'.lang.php');

function select_user(){
	global $nuked;
	
	$sql = mysql_query('SELECT id, pseudo, niveau FROM '.USER_TABLE.' WHERE niveau > 0 ORDER BY niveau DESC, pseudo');
	while($row = mysql_fetch_assoc($sql)){
		
		if ($row['niveau'] == 9) $nivo = "****";
		elseif ($row['niveau'] > 2) $nivo = "**";
		else if ($row['niveau'] == 2) $nivo = "*";
		else $nivo = "";
		echo '<option value="'.$row['id'].'">'.$row['pseudo'] . $nivo.'</option>';
	}
}

function post_message(){
	global $user;
	define('EDITOR_CHECK', 1);
	
	if (!empty($_REQUEST['for']) && preg_match("`^[a-zA-Z0-9]+$`", $_REQUEST['for'])){
		$sql = mysql_query("SELECT pseudo FROM ".USER_TABLE." WHERE id = '{$_REQUEST['for']}'");
		list($pseudo) = mysql_fetch_array($sql);
	}
	
	if (!empty($_REQUEST['titre'])){
		$_REQUEST['titre'] = stripslashes($_REQUEST['titre']);
		if (!preg_match("/\bRE:\b/i", $_REQUEST['titre'])) $title = "RE:" . $_REQUEST['titre'];
		else $title = $_REQUEST['titre'];
	}
	
	if (!empty($_REQUEST['message'])){
		$_REQUEST['message'] = secu_html(html_entity_decode($_REQUEST['message']));
		$_REQUEST['message'] = stripslashes($_REQUEST['message']);
		$reply = '<br /><table style="background:'.$bgcolor3.';" cellpadding="3" cellspacing="1" width="100%" border="0"><tr><td style="background: #FFF;color: #000"><b>'.$pseudo.' :</b><br />'.$_REQUEST['message'].'</td></tr></table><br />';
	}
	
	echo '<br /><form method="post" action="index.php?file=Userbox&amp;op=send_message">
            <table style="margin: auto;text-align:left;width: 98%">
            <tr><td align="center"><big><b>'._POSTMESS.'</b></big><br /><br /></td></tr>
            <tr><td><b>'._AUTHOR.' :</b> '.$user[2].'</td></tr>
            <tr><td><b>'._USERFOR.' :</b> ';
	
	if (!empty($_REQUEST['for']) && !empty($pseudo)){
		echo '<i>'.$pseudo.'</i><input type="hidden" name="user_for" value="'.$_REQUEST['for'].'" />';
	}else{
		echo '<select name="user_for" >';
		select_user();
		echo '</select>';
	}

    echo '</td></tr><tr><td><b>'._SUBJECT.' :</b> <input type="text" name="titre" size="30" value="'.$title.'" /></td></tr>
            <tr><td><b>'._USERMESS.' :</b><br /><textarea id="e_basic" name="message" cols="65" rows="10">'.$reply.'</textarea></td></tr>
            <tr><td align="center"><br /><input type="submit" name="send" value="'._SEND.'" />&nbsp;<input type="button" value="'._CANCEL.'" onclick="javascript:history.back()" /></td></tr></table></form><br />';
}

function send_message($titre, $user_for, $message){
	global $user, $nuked;
	
	if (empty($titre) || empty($user_for) || empty($message)){
            echo '<br /><br /><div style="text-align:center;">'._EMPTYFIELD.'<br /><br /><a href="javascript:history.back()"><b>'._BACK.'</b></a></div><br /><br />';
	}else{
		
		if (!empty($user_for) && ctype_alnum($user_for)) {
			$sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '$user_for'");
			$nb = mysql_num_rows($sql2);
		}
		else $nb = 0;
		
		if ($nb == 0){
			echo '<br /><br /><div style="text-align:center;\">'._UNKNOWMEMBER.'<br /><br /><a href="javascript:history.back()"><b>'._BACK.'</b></a></div><br /><br />';
		}else{
			$flood = mysql_query("SELECT date FROM " . USERBOX_TABLE . " WHERE user_from = '" . $user[0] . "' ORDER BY date DESC LIMIT 0, 1");
			list($flood_date) = mysql_fetch_array($flood);
			$anti_flood = $flood_date + $nuked['post_flood'];
			$date = time();
			
			if ($date < $anti_flood){
				echo '<br /><br /><div style="text-align:center;">'._NOFLOOD.'</div><br /><br />';
				redirect('index.php?file=Userbox', 2);
				closetable();
				footer();
				exit();
			}
			
			$message = secu_html(html_entity_decode($message));
			$titre = mysql_real_escape_string(stripslashes($titre));
			$message = mysql_real_escape_string(stripslashes($message));
			$user_for = mysql_real_escape_string(stripslashes($user_for));
			$titre = htmlentities($titre);
			
			$sql = mysql_query("INSERT INTO " . USERBOX_TABLE . " ( `mid` , `user_from` , `user_for` , `titre` , `message` , `date` , `status` ) VALUES ( '' , '{$user[0]}' , '$user_for' , '$titre' , '$message' , '$date' , '0' )");
			echo '<br /><br /><div style="text-align:center;">'._MESSSEND.'</div><br /><br />';
			redirect("index.php?file=Userbox", 2);
		}
	}
}

function show_message($mid){
	global $user, $nuked, $bgcolor2, $bgcolor3;
	
	echo '<script type="text/javascript">function del_mess(pseudo, id){if (confirm(\''._DELETEMESS.' \'+pseudo+\' ! '._CONFIRM.'\')){document.location.href = \'index.php?file=Userbox&op=del_message&mid=\'+id;}}</script>';
	
	$sql = mysql_query("UPDATE " . USERBOX_TABLE . " SET status = 1 WHERE mid = '$mid' AND user_for = '{$user[0]}'");
	
	echo '<br /><div style="text-align:center;"><big><b>'._PRIVATEMESS.'</b></big></div><br /><br />';
	
	$sql2 = mysql_query("SELECT titre, message, user_from, date FROM " . USERBOX_TABLE . " WHERE mid = '" . $_REQUEST['mid'] . "' AND user_for = '" . $user[0] . "'");
	$row = mysql_fetch_assoc($sql2);

    if ($row > 1) {
        
        $row['date'] = nkDate($row['date']);
        
        if(strlen($row['titre']) >= 50) $row['titre'] = substr($row['titre'], 0, 47)."...";
        
        $sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '{$row['user_from']}'");
        list($pseudo) = mysql_fetch_array($sql_member);
        
        echo '<table style="margin:0 auto;text-align:left;background:'.$bgcolor3.';" width="90%" cellspacing="1" cellpadding="4">
                <tr style="background:'.$bgcolor3.';"><td>'._OF.'  <a href="index.php?file=Members&amp;op=detail&amp;autor='.urlencode($pseudo).'"><b>'.$pseudo.'</b></a> '._THE.'&nbsp;'.$row['date'].'</td></tr>
                <tr style="background:'.$bgcolor2.';"><td><b>'._SUBJECT.' :</b> '.$row['titre'].'</td></tr>
                <tr style="background:'.$bgcolor2.';"><td>'.html_entity_decode($row['message']).'</td></tr></table>
                <br /><form method="post" action="index.php?file=Userbox&amp;op=post_message&amp;for='.$row['user_from'].'">
                <div style="text-align:center;">
                <input type="hidden" name="message" value="'.htmlentities($row['message']).'" />
                <input type="hidden" name="titre" value="'.htmlentities($row['titre']).'" />
                <input type="submit" value="'._REPLY.'" />&nbsp;
                <input type="button" value="'._DEL.'" onclick="javascript:del_mess(\''.mysql_real_escape_string(stripslashes($pseudo)).'\', \''.$mid.'\');" />
                <br /><br />[ <a href="index.php?file=Userbox"><b>'._BACK.'</b></a> ]</div></form><br />';
    }
    else {
        echo '<p style="text-align: center">' . _NOENTRANCE . '</p>';
    }
}

function del_message($mid){
	global $user, $nuked;
	
	$sql = mysql_query("SELECT mid FROM " . USERBOX_TABLE . " WHERE  mid = '$mid' AND user_for = '{$user[0]}'");
	$nbr = mysql_num_rows($sql);
	
	if($nbr > 0){
		$sql = mysql_query("DELETE FROM " . USERBOX_TABLE . " WHERE mid = '$mid' AND user_for = '{$user[0]}'");
		$MessConf = _MESSDEL;
	}
	else $MessConf = 'Failed...';

	echo '<br /><br /><div style="text-align: center;">'.$MessConf.'</div><br /><br />';
	redirect('index.php?file=Userbox', 2);
}

function del_message_form($mid, $del_oui){
	global $user, $nuked;
	
	if ($del_oui == 'ok'){
		
		$sql = mysql_query("SELECT mid FROM " . USERBOX_TABLE . " WHERE user_for = '{$user[0]}' ORDER BY mid");
		$nb_mess = mysql_num_rows($sql);
		$get_mid = 0;
		
		while ($nb_mess > $get_mid && $nb_mess <> ""){
			$titi = $mid[$get_mid];
			$get_mid++;
			
			if($titi){
				$del = mysql_query("DELETE FROM " . USERBOX_TABLE . " WHERE mid = '{$titi}'");
			}
		}
		echo '<br /><br /><div style="text-align:center;">'._MESSAGESDEL.'</div><br /><br />';
		redirect('index.php?file=Userbox', 2);
		
	}else{
		
		if (!$mid){
			echo '<br /><br /><div style="text-align:center;">'._NOSELECTMESS.'</div><br /><br />';
			redirect('index.php?file=Userbox', 2);
			closetable();
			footer();
			exit();
		}
		
		$sql = mysql_query("SELECT mid FROM " . USERBOX_TABLE . " WHERE user_for = '{$user[0]}' ORDER BY mid");
		$nb_mess = mysql_num_rows($sql);
		
		echo '<form method="post" action="index.php?file=Userbox&amp;op=del_message_form&amp;del_oui=ok">
                <div style="text-align:center;"><br /><br /><b>'._DELETEMESSAGES.' :</b><br /><br />';
		
		$get_mid = 0;
		while ($nb_mess > $get_mid && $nb_mess <> ""){
			$titi = $mid[$get_mid];
			$get_mid++;
			
			if ($titi){
				$sql_mess = mysql_query("SELECT user_from, date FROM " . USERBOX_TABLE . " WHERE user_for = '{$user[0]}' AND mid = '{$titi}'");
				$row = mysql_fetch_assoc($sql_mess);
				$row['date'] = nkDate($row['date']);
				
				$sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '{$row['user_from']}'");
                list($pseudo) = mysql_fetch_array($sql_member);
				
				echo '<b><big>·</big></b>&nbsp;'._OF.'&nbsp;'.$pseudo.' ( '.$row['date'].' )<br />
				<input type="hidden" name="mid[]" value="'.$titi.'" />';
			}
		}
		
		echo '<br /><br /><input type="submit" value="'._DELCONFIRM.'" />
                &nbsp;<input type="button" value="'._CANCEL.'" onclick="document.location=\'index.php?file=Userbox\'" /></div></form><br />';
	}
}

function index(){
	if($_REQUEST['page'] != 'admin'){
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
		
		$sql = mysql_query("SELECT mid, titre, user_from, date, status FROM " . USERBOX_TABLE . " WHERE user_for = '{$user[0]}' ORDER BY date DESC");
		$nb_mess = mysql_num_rows($sql);
		$i = 0;
		while($row = mysql_fetch_assoc($sql)){
			
			$row['date'] = nkDate($row['date']);
			
			$sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '{$row['user_from']}'");
			list($pseudo) = mysql_fetch_array($sql_member);
			
			$etat = ($row['status'] == 1) ? _READ : _NOTREAD;
			
			if ($j == 0){
				$bg = $bgcolor2;
				$j++;
			}else{
				$bg = $bgcolor1;
				$j = 0;
			}
			
			if(strlen($row['titre']) >= 50) $row['titre'] = substr($row['titre'], 0, 47)."...";
			
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
                <br /><br />[ <a href="index.php?file=User"><b>'._BACK.'</b></a> ]</div></form><br />';
	}
} 

if($user){
	if(isset($_REQUEST['op'])){
		switch ($_REQUEST['op']){
            
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
			del_message_form($_REQUEST['mid'], $_REQUEST['del_oui']);
			closetable();
			break;

            default:
			opentable();
			index();
			closetable();
			break;
        } 
    }
}else{
    opentable();
    echo '<br /><br /><div style="text-align:center;">'._USERENTRANCE.'<br /><br /><a href="index.php?file=User&amp;op=login_screen"><b>'._LOGINUSER.'</b></a> | <a href="index.php?file=User&amp;op=reg_screen"><b>'._REGISTERUSER.'</b></a></div><br /><br />';
    closetable();
}
?>