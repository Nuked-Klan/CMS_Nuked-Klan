<?php 
/**
 * @version     1.7.10
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
if (preg_match("`block_roster.php`i", $_SERVER['PHP_SELF'])){
    die ("You cannot open this page directly");
} 

function affich_block_roster($blok){
    global $nuked;

    $team_id = $blok['content'];
    $blok['content'] = '';

    if ($team_id != ''){
        $where = 'WHERE team = \'' . $team_id . '\' OR team2 = \'' . $team_id . '\' OR team3 = \'' . $team_id . '\' ';
    }
    else{
		$sql_team = mysql_query("SELECT cid FROM " . TEAM_TABLE);
		$nb_team = mysql_num_rows($sql_team);
		
		if ($nb_team > 0) $where = 'WHERE team > 0 OR team2 > 0 OR team3 > 0'; 
		else $where = 'WHERE niveau > 1'; 
    }

    $blok['content'] .= '<table style="width:100%;" cellspacing="0" cellpadding="1">'."\n";

    $sql = mysql_query('SELECT pseudo, mail, country FROM ' . USER_TABLE . ' ' . $where . ' ORDER BY ordre, pseudo');
    while (list($pseudo, $mail, $country) = mysql_fetch_array($sql)){
        list ($pays, $ext) = explode ('.', $country);

        $nick_team = $nuked['tag_pre'] . $pseudo . $nuked['tag_suf'];

        if (is_file('themes/' . $theme . '/images/mail.gif')){
            $img = 'themes/' . $theme . '/images/mail.gif';
        } 
        else{
            $img = 'modules/Team/images/mail.gif';
        } 

        $blok['content'] .= '<tr><td style="width: 20%;text-align:center;" ><img src="images/flags/' . $country . '" alt="" title="' . $pays . '" /></td>'."\n"
								. '<td style="width: 60%;"><a href="index.php?file=Team&amp;op=detail&amp;autor=' . urlencode($pseudo) . '"><b>' . $nick_team . '</b></a></td>'."\n"
								. '<td style="width: 20%;text-align:center;" ><a href="mailto:' . $mail . '"><img style="border: 0;" src="' . $img . '" alt="" title="' . $mail . '" /></a></td></tr>'."\n";
	} 

    $blok['content'] .= '</table>'."\n";
    return $blok;
} 

function edit_block_roster($bid){
    global $nuked, $language;

    $sql = mysql_query('SELECT active, position, titre, module, content, type, nivo, page FROM ' . BLOCK_TABLE . ' WHERE bid = \'' . $bid . '\' ');
    list($active, $position, $titre, $modul, $content, $type, $nivo, $pages) = mysql_fetch_array($sql);
    
    $titre = printSecuTags($titre);

    if ($active == 1) $checked1 = "selected=\"selected\"";
    else if ($active == 2) $checked2 = "selected=\"selected\"";
    else $checked0 = "selected=\"selected\"";

    echo '<div class="content-box">',"\n" //<!-- Start Content Box -->
			, '<div class="content-box-header"><h3>' , _BLOCKADMIN , '</h3>',"\n"
			, '<div style="text-align:right;"><a href="help/' , $language , '/block.html" rel="modal">',"\n"
			, '<img style="border: 0;" src="help/help.gif" alt="" title="' , _HELP , '" /></a>',"\n"
			, '</div></div>',"\n"
			, '<div class="tab-content" id="tab2"><form method="post" action="index.php?file=Admin&amp;page=block&amp;op=modif_block">',"\n"
			, '<table style="margin-left: auto;margin-right: auto;text-align: left;" cellspacing="0" cellpadding="2" border="0">',"\n"
			, '<tr><td><b>' , _TITLE , '</b></td><td><b>' , _BLOCK , '</b></td><td><b>' , _POSITION , '</b></td><td><b>' , _LEVEL , '</b></td></tr>',"\n"
			, '<tr><td><input type="text" name="titre" size="40" value="' , $titre , '" /></td>',"\n"
			, '<td><select name="active">',"\n"
			, '<option value="1" ' , $checked1 , '>' , _LEFT , '</option>',"\n"
			, '<option value="2" ' , $checked2 , '>' , _RIGHT , '</option>',"\n"
			, '<option value="0" ' , $checked0 , '>' , _OFF , '</option></select></td>',"\n"
			, '<td><input type="text" name="position" size="2" value="' , $position , '" /></td>',"\n"
			, '<td><select name="nivo"><option>' , $nivo , '</option>',"\n"
			, '<option>0</option>',"\n"
			, '<option>1</option>',"\n"
			, '<option>2</option>',"\n"
			, '<option>3</option>',"\n"
			, '<option>4</option>',"\n"
			, '<option>5</option>',"\n"
			, '<option>6</option>',"\n"
			, '<option>7</option>',"\n"
			, '<option>8</option>',"\n"
			, '<option>9</option></select></td></tr><tr><td colspan="4"><b>' , _TEAM , ' :</b>&nbsp;<select name="content"><option value="">' , _INFOALL , '</option>',"\n";

    $sql2 = mysql_query('SELECT cid, titre FROM ' . TEAM_TABLE . ' ORDER BY ordre, titre');
    while (list($team_id, $team) = mysql_fetch_array($sql2)){
        $team = printSecuTags($team);

        if ($team_id == $content) $checked3 = 'selected="selected"';
        else $checked3 = '';

        echo '<option value="' . $team_id . '" ' . $checked3 . '>' . $team . '</option>'."\n";
    }

    echo '</select></td></tr><tr><td colspan="4">&nbsp;</td></tr>'."\n"
			. '<tr><td colspan="4"><b>' . _PAGESELECT . ' :</b></td></tr><tr><td colspan="4">&nbsp;</td></tr>'."\n"
			. '<tr><td colspan="4"><select name="pages[]" size="8" multiple="multiple">'."\n";

    select_mod2($pages);

    echo '</select></td></tr><tr><td colspan="4" align="center"><br />',"\n"
		, '<input type="hidden" name="type" value="' , $type , '" />',"\n"
		, '<input type="hidden" name="bid" value="' , $bid , '" />',"\n"
		, '</td></tr></table>',"\n"
		, '<div style="text-align: center;"><br /><input class="button" type="submit" name="send" value="' , _MODIFBLOCK , '" /><a class="buttonLink" href="index.php?file=Admin&amp;page=block">' , _BACK , '</a></div></form><br /></div></div>',"\n";

}
?>