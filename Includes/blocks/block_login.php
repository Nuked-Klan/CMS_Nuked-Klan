<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")){
	exit('You can\'t run this file alone.');
}

function affich_block_login($blok){
    global $user, $nuked, $bgcolor3, $bgcolor1 ;

    list($login, $messpv, $members, $online, $avatar) = explode('|', $blok['content']);
    $blok['content'] = '';

	$c = 0;
	
	if($login != 'off'){
		if (!$user){
			$blok['content'] = '<form action="index.php?file=User&amp;nuked_nude=index&amp;op=login" method="post">'."\n"
			. '<table style="margin-left: auto;margin-right: auto;text-align: left;">'."\n"
			. '<tr><td>' . _NICK . ' :</td><td><input type="text" name="pseudo" size="10" maxlength="250" /></td></tr>'."\n"
			. '<tr><td>' . _PASSWORD . ' :</td><td><input type="password" name="pass" size="10" maxlength="15" /></td></tr>'."\n"
			. '<tr><td colspan="2"><input type="checkbox" class="checkbox" name="remember_me" value="ok" checked="checked" />&nbsp;' . _SAVE . '</td></tr>'."\n"
			. '<tr><td colspan="2" align="center"><input type="submit" value="' . _BLOGIN . '" /></td></tr>'."\n"
			. '<tr><td colspan="2"><a href="index.php?file=User&amp;op=reg_screen">' . _REGISTER . '</a><br />'."\n"
			. '<a href="index.php?file=User&amp;op=oubli_pass">' . _FORGETPASS . '</a> ?</td></tr></table></form>'."\n";
		}
		else{
			$blok['content'] = '<div style="text-align: center;">' . _WELCOME . ', <b>' . $user[2] . '</b><br /><br />'."\n";
			if ($avatar != 'off'){
				$sql_avatar=mysql_query('SELECT avatar FROM ' . USER_TABLE . ' WHERE id = \'' . $user[0] . '\' ');
				list($avatar_url) = mysql_fetch_array($sql_avatar);
				if($avatar_url) $blok['content'] .= '<img src="' . $avatar_url . '" style="border:1px ' . $bgcolor3 . ' dashed; width:100px; background:' . $bgcolor1 . '; padding:2px;" alt="' . $user[2] . ' avatar" /><br /><br />';
			}
			$blok['content'] .= '<a href="index.php?file=User">' . _ACCOUNT . '</a> / <a href="index.php?file=User&amp;nuked_nude=index&amp;op=logout">' . _LOGOUT . '</a></div>'."\n";
		}
		$c++;
	}

    if($messpv != 'off' && $user[0] != ''){
		if ($c > 0) $blok['content'] .= '<hr style="height: 1px;" />'."\n";
	
		$sql2 = mysql_query('SELECT mid FROM ' . USERBOX_TABLE . ' WHERE user_for = \'' . $user[0] . '\' AND status = 1');
		$nb_mess_lu = mysql_num_rows($sql2);
	
		$blok['content'] .= '&nbsp;<img width="14" height="12" src="images/message.gif" alt="" />&nbsp;<span style="text-decoration: underline"><b>' . _MESSPV . '</b></span><br />'."\n";
	
		if ($user[5] > 0){
			$blok['content'] .= '&nbsp;<b><big>·</big></b>&nbsp;' . _NOTREAD . ' : <a href="index.php?file=Userbox"><b>' . $user[5] . '</b></a>'."\n";
		}
		else{
			$blok['content'] .= '&nbsp;<b><big>·</big></b>&nbsp;' . _NOTREAD . ' : <b>' . $user[5] . '</b>'."\n";
		}
	
		if ($nb_mess_lu > 0){
			$blok['content'] .= '<br />&nbsp;<b><big>·</big></b>&nbsp;' . _READ . ' : <a href="index.php?file=Userbox"><b>' . $nb_mess_lu . '</b></a>'."\n";
		}
		else{
			$blok['content'] .= '<br />&nbsp;<b><big>·</big></b>&nbsp;' . _READ . ' : <b>' . $nb_mess_lu . '</b>'."\n";
		}
	
		$c++;
    }

	if ($members != 'off'){
		if ($c > 0) $blok['content'] .= '<hr style="height: 1px;" />'."\n";

    	$blok['content'] .= '&nbsp;<img width="16" height="13" src="images/memberslist.gif" alt="" />&nbsp;<span style="text-decoration: underline"><b>' . _MEMBERS . '</b></span><br />'."\n";

    	$sql_users = mysql_query('SELECT id FROM ' . USER_TABLE . ' WHERE niveau < 3');
    	$nb_users = mysql_num_rows($sql_users);

    	$sql_admin = mysql_query('SELECT id FROM ' . USER_TABLE . ' WHERE niveau > 2');
    	$nb_admin = mysql_num_rows($sql_admin);

    	$sql_lastmember = mysql_query('SELECT pseudo FROM ' . USER_TABLE . ' ORDER BY date DESC LIMIT 0, 1');
    	list($lastmember) = mysql_fetch_array($sql_lastmember);

    	$blok['content'] .= '&nbsp;<b><big>·</big></b>&nbsp;' . _ADMINS . ' : <b>' . $nb_admin . '</b><br />&nbsp;<b><big>·</big></b>&nbsp;' . _MEMBERS . ' :'
    	. '&nbsp;<b>' . $nb_users . '</b> [<a href="index.php?file=Members">' . _LIST . '</a>]<br />'."\n"
		. '&nbsp;<b><big>·</big></b>&nbsp;' . _LASTMEMBER . ' : <a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($lastmember) . '"><b>' . $lastmember . '</b></a>'."\n";

		 $c++;
	}

	if ($online != 'off'){
		if ($c > 0) $blok['content'] .= '<hr style="height: 1px;" />'."\n";

    	$blok['content'] .= '&nbsp;<img width="16" height="13" src="images/online.gif" alt="" />&nbsp;<span style="text-decoration: underline"><b>' . _WHOISONLINE . '</b></span><br />'."\n";

    	$nb = nbvisiteur();

    	if ($nb[1] > 0){
			$sql4 = mysql_query('SELECT username FROM ' . NBCONNECTE_TABLE . ' WHERE type BETWEEN 1 AND 2 ORDER BY date');
			while (list($nom) = mysql_fetch_array($sql4)){
				   $user_online .= '&nbsp;<b><big>·</big></b>&nbsp;<b>' . $nom . '</b><br />';
			}
	
			$user_list = '&nbsp;[<a href="#" onmouseover="AffBulle(\'&nbsp;&nbsp;' . _WHOISONLINE . '\', \'' . htmlentities(mysql_real_escape_string($user_online), ENT_NOQUOTES) . '\', 150)" onmouseout="HideBulle()">' . _LIST . '</a>]';
			}
    	else{
			$user_list = '';
    	}

		if ($nb[2] > 0){
			$sql5 = mysql_query('SELECT username FROM ' . NBCONNECTE_TABLE . ' WHERE type > 2 ORDER BY date');
			while (list($name) = mysql_fetch_array($sql5)){
				   $admin_online .= '&nbsp;<b><big>·</big></b>&nbsp;<b>' . $name . '</b><br />';
			}
	
			$admin_list = '&nbsp;[<a href="#" onmouseover="AffBulle(\'&nbsp;&nbsp;' . _WHOISONLINE . '\', \'' . htmlentities(mysql_real_escape_string($admin_online), ENT_NOQUOTES) . '\', 150)" onmouseout="HideBulle()">' . _LIST . '</a>]';
		}
		else{
			$admin_list = '';
		}
	
		$blok['content'] .= '&nbsp;<b><big>·</big></b>&nbsp;' . _VISITOR;
		if ($nb[0] > 1) $blok['content'] .= 's';
		$blok['content'] .= ' : <b>' . $nb[0] . '</b><br />&nbsp;<b><big>·</big></b>&nbsp;' . _MEMBER;
		if ($nb[1] > 1) $blok['content'] .= 's';
		$blok['content'] .= ' : <b>' . $nb[1] . '</b>' . $user_list . '<br />&nbsp;<b><big>·</big></b>&nbsp;' . _ADMIN;
		if ($nb[2] > 1) $blok['content'] .= 's';
		$blok['content'] .= ' : <b>' . $nb[2] . '</b>' . $admin_list . '<br />'."\n";
	
		$c++;
   }

   return $blok;
}

function edit_block_login($bid){
    global $nuked, $language;

    $sql = mysql_query('SELECT active, position, titre, module, content, type, nivo, page FROM ' . BLOCK_TABLE . ' WHERE bid = \'' . $bid . '\' ');
    list($active, $position, $titre, $modul, $content, $type, $nivo, $pages) = mysql_fetch_array($sql);
    $titre = printSecuTags($titre);
    list($login, $messpv, $members, $online, $avatar) = explode('|', $content);

    if ($active == 1) $checked1 = 'selected="selected"';
    else if ($active == 2) $checked2 = 'selected="selected"';
    else $checked0 = 'selected="selected"';

    if ($login == 'off') $checked3 = 'selected="selected"'; else $checked3 = '';
    if ($messpv == 'off') $checked4 = 'selected="selected"'; else $checked4 = '';
    if ($members == 'off') $checked5 = 'selected="selected"'; else $checked5 = '';
    if ($online == 'off') $checked6 = 'selected="selected"'; else $checked6 = '';
	if ($avatar == 'off') $checked7 = 'selected="selected"'; else $checked7 = '';

    echo '<div class="content-box">',"\n" //<!-- Start Content Box -->
			, '<div class="content-box-header"><h3>' , _BLOCKADMIN , '</h3>',"\n"
			, '<a href="help/' , $language , '/block.html" rel="modal">',"\n"
			, '<img style="border: 0;" src="help/help.gif" alt="" title="' , _HELP , '" /></a>',"\n"
			, '</div>',"\n"
			, '<div class="tab-content" id="tab2"><form method="post" action="index.php?file=Admin&amp;page=block&amp;op=modif_block">',"\n"
			, '<table style="margin-left: auto;margin-right: auto;text-align: left;" cellspacing="0" cellpadding="2" border="0">',"\n"
			, '<tr><td><b>' , _TITLE , '</b></td><td><b>' , _BLOCK , '</b></td><td><b>' , _POSITION , '</b></td><td><b>' , _LEVEL , '</b></td></tr>',"\n"
			, '<tr><td align="center"><input type="text" name="titre" size="40" value="' , $titre , '" /></td>',"\n"
			, '<td align="center"><select name="active">',"\n"
			, '<option value="1" ' , $checked1 , '>' , _LEFT , '</option>',"\n"
			, '<option value="2" ' , $checked2 , '>' , _RIGHT , '</option>',"\n"
			, '<option value="0" ' , $checked0 , '>' , _OFF , '</option></select></td>',"\n"
			, '<td align="center"><input type="text" name="position" size="2" value="' , $position , '" /></td>',"\n"
			, '<td align="center"><select name="nivo"><option>' , $nivo , '</option>',"\n"
			, '<option>0</option>',"\n"
			, '<option>1</option>',"\n"
			, '<option>2</option>',"\n"
			, '<option>3</option>',"\n"
			, '<option>4</option>',"\n"
			, '<option>5</option>',"\n"
			, '<option>6</option>',"\n"
			, '<option>7</option>',"\n"
			, '<option>8</option>',"\n"
			, '<option>9</option></select></td></tr>',"\n"
			, '<tr><td colspan="4">' , _LOGIN , ' : <select name="login">',"\n"
			, '<option value="on">' , _YES , '</option>',"\n"
			, '<option value="off" ' , $checked3 , '>' , _NO , '</option></select>',"\n"
			, '&nbsp;' , _MESSPV , '  : <select name="messpv">',"\n"
			, '<option value="on">' , _YES , '</option>',"\n"
			, '<option value="off" ' , $checked4 , '>' , _NO , '</option></select>',"\n"
			, '&nbsp;' , _MEMBERS , ' : <select name="members">',"\n"
			, '<option value="on">' , _YES , '</option>',"\n"
			, '<option value="off" ' , $checked5 , '>' , _NO , '</option></select>',"\n"
			, '</td></tr><tr><td colspan="4">&nbsp;' , _WHOISONLINE , ' : <select name="online">',"\n"
			, '<option value="on">' , _YES , '</option>',"\n"
			, '<option value="off" ' , $checked6 , '>' , _NO , '</option></select>',"\n"
			, '&nbsp;' , _SHOWAVATAR , ' : <select name="avatar">',"\n"
			, '<option value="on">' , _YES , '</option>',"\n"
			, '<option value="off" ' , $checked7 , '>' , _NO , '</option></select>',"\n"
			, '</td></tr><tr><td colspan="4">&nbsp;</td></tr>',"\n"
			, '<tr><td colspan="4" align="center"><b>' , _PAGESELECT , ' :</b></td></tr><tr><td colspan="4">&nbsp;</td></tr>',"\n"
			, '<tr><td colspan="4" align="center"><select name="pages[]" size="8" multiple="multiple">',"\n";

    select_mod2($pages);

    echo '</select></td></tr><tr><td colspan="4" align="center"><br />',"\n"
		, '<input type="hidden" name="type" value="' , $type , '" />',"\n"
		, '<input type="hidden" name="bid" value="' , $bid , '" />',"\n"
		, '<input type="submit" name="send" value="' , _MODIFBLOCK , '" />',"\n"
		, '</td></tr></table>'
		, '<div style="text-align: center;"><br />[ <a href="index.php?file=Admin&amp;page=block"><b>' , _BACK , '</b></a> ]</div></form><br /></div></div>',"\n";

}

function modif_advanced_login($data){
	$data['content'] = $data['login'] . '|' . $data['messpv'] . '|' . $data['members'] . '|' . $data['online']. '|' . $data['avatar'];
	return $data;
}
?>