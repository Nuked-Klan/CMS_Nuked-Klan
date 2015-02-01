<?php
/**
 * @version     1.7.10
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

function affich_block_center($blok){

    $mod = explode('|', $blok['content']);
    $mod1 = $mod[0];
    $mod2 = $mod[1];

    if ($mod1 == 'Gallery') $modname1 = _NAVGALLERY;
    else if ($mod1 == 'Calendar') $modname1 = _NEXTWAR;
    else if ($mod1 == 'Download') $modname1 = _NAVDOWNLOAD;
    else if ($mod1 == 'Irc') $modname1 = _IRCAWARD;
    else if ($mod1 == 'Links') $modname1 = _NAVLINKS;
    else if ($mod1 == 'Wars') $modname1 = _LATESTWAR;
    else if ($mod1 == 'News') $modname1 = _NAVNEWS;
    else if ($mod1 == 'Sections') $modname1 = _NAVART;
    else if ($mod1 == 'Server') $modname1 = _SERVERMONITOR;
    else if ($mod1 == 'Forum') $modname1 = _NAVFORUM;
    else if ($mod1 == 'Textbox') $modname1 = _BLOKSHOUT;
    else if ($mod1 == 'Stats') $modname1 = _BLOKSTATS;
    else if ($mod1 == 'Search') $modname1 = _BLOKSEARCH;
    else $modname1 = $mod1;

    if ($mod2 == 'Gallery') $modname2 = _NAVGALLERY;
    else if ($mod2 == 'Calendar') $modname2 = _NEXTWAR;
    else if ($mod2 == 'Download') $modname2 = _NAVDOWNLOAD;
    else if ($mod2 == 'Irc') $modname2 = _IRCAWARD;
    else if ($mod2 == 'Links') $modname2 = _NAVLINKS;
    else if ($mod2 == 'Wars') $modname2 = _LATESTWAR;
    else if ($mod2 == 'News') $modname2 = _NAVNEWS;
    else if ($mod2 == 'Sections') $modname2 = _NAVART;
    else if ($mod2 == 'Server') $modname2 = _SERVERMONITOR;
    else if ($mod2 == 'Forum') $modname2 = _NAVFORUM;
    else if ($mod2 == 'Textbox') $modname2 = _BLOKSHOUT;
    else if ($mod2 == 'Stats') $modname2 = _BLOKSTATS;
    else if ($mod2 == 'Search') $modname2 = _BLOKSEARCH;
    else $modname2 = $mod2;

    $blok['content'] = '';

    if ($mod2 != ""){
        $blok['content'] .= '<div style="width: 48%; float: left" ><h3 style="text-align: center">' . $modname1 . '</h3>'."\n";
        $bid = '';
    }
	else{
        $blok['content'] .= '<div style="width: 100%">'."\n";
        $bid = $blok['bid'];
    }

    $blok['content'] .= inc_bl1($mod1, $bid);

    $blok['content'] .= '</div>'."\n";

    if ($mod2 != ""){
        $blok['content'] .= '<div style="float: left; width: 4%">&nbsp;</div><div style="float: left; width: 48%"><h3 style="text-align: center">' . $modname2 . '</h3>'."\n";
        $b = 1;
        $blok['content'] .= inc_bl2($mod2);
        $blok['content'] .= '</div>'."\n";
    }

    $blok['content'] .= '<div style="clear: both"></div>'."\n";
    return $blok;
}


function edit_block_center($bid){
    global $nuked, $language;

    $sql = mysql_query('SELECT active, position, titre, module, content, type, nivo, page FROM ' . BLOCK_TABLE . ' WHERE bid = \'' . $bid . '\' ');
    list($active, $position, $titre, $modul, $content, $type, $nivo, $pages) = mysql_fetch_array($sql);
    $titre = printSecuTags($titre);

    if ($active == 3) $checked3 = 'selected="selected"';
    else if ($active == 4) $checked4 = 'selected="selected"';
    else $checked0 = 'selected="selected"';

    $mod = explode("|", $content);
    $mod1 = $mod[0];
    $mod2 = $mod[1];

    echo '<div class="content-box">',"\n" //<!-- Start Content Box -->
			, '<div class="content-box-header"><h3>' , _BLOCKADMIN , '</h3>',"\n"
			, '<div style="text-align:right;"><a href="help/' , $language , '/block.html" rel="modal">',"\n"
			, '<img style="border: 0;" src="help/help.gif" alt="" title="' , _HELP , '" /></a>',"\n"
			, '</div></div>',"\n"
			, '<div class="tab-content" id="tab2"><form method="post" action="index.php?file=Admin&amp;page=block&amp;op=modif_block">',"\n"
			, '<table style="margin-left: auto;margin-right: auto;text-align: left;border: none;" cellspacing="0" cellpadding="2" >',"\n"
			, '<tr><td><b>' , _TITLE , '</b></td><td><b>' , _BLOCK , '</b></td><td><b>' , _POSITION , '</b></td><td><b>' , _LEVEL , '</b></td></tr>',"\n"
			, '<tr><td><input type="text" name="titre" size="40" value="' , $titre , '" /></td>',"\n"
			, '<td><select name="active">',"\n"
			, '<option value="3" ' , $checked3 , '>' , _CENTERBLOCK , '</option>',"\n"
			, '<option value="4" ' , $checked4 , '>' , _FOOTERBLOCK , '</option>',"\n"
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
			, '<option>9</option></select></td></tr><tr><td colspan="4"><b>' , _MODULE , ' 1 :</b> <select name="content[1]"><option value="">' , _NORANK , '</option>',"\n";

    select_module($mod1);

    echo '</select> <b>' , _MODULE , ' 2 :</b> <select name="content[2]"><option value="">' , _NORANK , '</option>',"\n";

    select_module($mod2);

    echo '</select></td></tr><tr><td colspan="4">&nbsp;</td></tr>',"\n"
			, '<tr><td colspan="4" ><b>' ,  _PAGESELECT , ' : </b></td></tr><tr><td colspan="4">&nbsp;</td></tr>',"\n"
			, '<tr><td colspan="4" ><select name="pages[]" size="8" multiple="multiple">',"\n";

    select_mod2($pages);

    echo '</select></td></tr><tr><td colspan="4" style="text-align:center;"><br />'
			, '<input type="hidden" name="type" value="' , $type , '" />',"\n"
			, '<input type="hidden" name="bid" value="' , $bid , '" />',"\n"
			, '</td></tr></table>',"\n"
			, '<div style="text-align: center;"><br /><input class="button" type="submit" value="' , _MODIFBLOCK , '" /><a class="buttonLink" href="index.php?file=Admin&amp;page=block">' , _BACK , '</a></div></form><br /></div></div>',"\n";
}

function modif_advanced_center($data){
    if ($data['content'][1] != '' && $data['content'][2] != ''){
        $sep = '|';
    }
    else{
        $sep = '';
    }

    $content = $data['content'][1] . $sep . $data['content'][2];
    $data['content'] = $content;
    return $data;
}

function select_module($mod){
    $handle = opendir('modules');
    while (false !== ($f = readdir($handle))){
        if ($f != '.' && $f != '..' && $f != 'CVS' && $f != 'index.html'  && !preg_match("/\./", $f)){
            if ($mod == $f) $checked = 'selected="selected"';
            else $checked = '';

            if (is_file('modules/' . $f . '/blok.php')) echo '<option value="' , $f , '" ' , $checked , '>' , $f , '</option>',"\n";
        }
    }
    closedir($handle);
}

function inc_bl1($mod1, $bid){
    ob_start();
    print eval("\$bid = \"$bid\";");
    print eval(' include("modules/" . $mod1 . "/blok.php"); ');
    $blok_content = ob_get_contents();
    ob_end_clean();
    return $blok_content;
}

function inc_bl2($mod2){
    ob_start();
    print eval(' include("modules/" . $mod2 . "/blok.php"); ');
    $blok_content = ob_get_contents();
    ob_end_clean();
    return $blok_content;
}
?>