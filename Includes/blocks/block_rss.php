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

function affich_block_rss($blok){
    $rssurl = $blok['content'];
    $blok['content'] = '';
    $limit = 10;
    $timeout = 10;
    $rss_host = @parse_url($rssurl);

    if ($fp = @fopen($rssurl, "r")){
		$raw = '';		
		while (!feof($fp)){
			$raw .= fread($fp, 32000);
		}		
		fclose($fp);
    }
    else if ($fp = @fsockopen($rss_host['host'], 80, $errno, $errstr, $timeout)){
		$request = 'GET ' . $rssurl . " HTTP/1.0\r\n";
		$request .= 'HOST: ' . $rsshost . "\r\n\r\n";
		fputs($fp, $request);
		$raw = '';
		while (!feof($fp)){
    	    $raw .= fread($fp, 32000);
    	}
    	fclose($fp);
    }
    else{
		$blok['content'] = '<br /><div style="text-align: center;">Error connecting to rss source !</div><br />';
    }

	@preg_match("`<\?xml version=\"1.0\" encoding=\"(.*)\"\?>`i",$raw, $encode);
	if (isset($encode[1]) && $encode[1] == 'UTF-8') $utfdecode = true;
	else $utfdecode = false;

    if (preg_match("`<item>(.*)</item>`i", $raw, $rawitems)){

		$items = explode("<item>", $rawitems[0]);
		$nb = count($items);
		$max = (($nb-1) < $limit) ? ($nb-1) : $limit;

		for($i = 0; $i < $max; $i++){
		    preg_match("`<title>(.*)</title>`i",$items[$i+1], $title);
		    preg_match("`<link>(.*)</link>`i",$items[$i+1], $link);
		    preg_match("`<pubDate>(.*)</pubDate>`i",$items[$i+1], $date);
		    preg_match("`<description>(.*)</description>`i",$items[$i+1], $description);
		    $texte = '<b>' . $date[1] . '</b><br />' . $description[1];

			if ($utfdecode == true){
				$title[1] = utf8_decode($title[1]);
				$texte = utf8_decode($texte);
			}

		    if ($blok['active'] == 3 || $blok['active'] == 4) $blok['content'] .= '<b><big>·</big></b>&nbsp;<a href="' . $link[1] . '" onclick="window.open(this.href); return false;" onmouseover="AffBulle(\'' . htmlentities(addslashes($title[1]), ENT_NOQUOTES) . '\', \'' . htmlentities(mysql_real_escape_string($texte), ENT_NOQUOTES) . '\', 400)" onmouseout="HideBulle()"><b>' . $title[1] . '</b></a> ( ' . $date[1] . ' )<br />'."\n";
		    else $blok['content'] .= '<b><big>·</big></b>&nbsp;<a href="' . $link[1] . '" onclick="window.open(this.href); return false;" onmouseover="AffBulle(\'' . htmlentities(mysql_real_escape_string($title[1]), ENT_NOQUOTES) . '\', \'' . htmlentities(mysql_real_escape_string($texte), ENT_NOQUOTES) . '\', 400)" onmouseout="HideBulle()">' . $title[1] . '</a><br />'."\n";
		}
    }

    return $blok;
}

function edit_block_rss($bid){
    global $nuked, $language;

    $sql = mysql_query('SELECT active, position, titre, module, content, type, nivo, page FROM ' . BLOCK_TABLE . ' WHERE bid = \'' . $bid . '\' ');
    list($active, $position, $titre, $modul, $content, $type, $nivo, $pages) = mysql_fetch_array($sql);
    $titre = htmlentities($titre);

    if ($active == 1) $checked1 = 'selected="selected"';
    else if ($active == 2) $checked2 = 'selected="selected"';
    else if ($active == 3) $checked3 = 'selected="selected"';
    else if ($active == 4) $checked4 = 'selected="selected"';
    else $checked0 = 'selected="selected"';

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
			, '<option value="3" ' , $checked3 , '>' , _CENTERBLOCK , '</option>',"\n"
			, '<option value="4" ' , $checked4 , '>' , _FOOTERBLOCK , '</option>',"\n"
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
			, '<tr><td colspan="4"><b>' , _URL , ' : </b><input type="text" name="content" size="50" value="' , $content , '" /></td></tr>',"\n"
			, '<tr><td colspan="4">&nbsp;</td></tr><tr><td colspan="4" align="center"><b>' , _PAGESELECT , ' :</b></td></tr><tr><td colspan="4">&nbsp;</td></tr>',"\n"
			, '<tr><td colspan="4" align="center"><select name="pages[]" size="8" multiple="multiple">',"\n";

    select_mod2($pages);

    echo '</select></td></tr><tr><td colspan="4" align="center"><br />',"\n"
		, '<input type="hidden" name="type" value="' , $type , '" />',"\n"
		, '<input type="hidden" name="bid" value="' , $bid , '" />',"\n"
		, '<input type="submit" name="send" value="' , _MODIFBLOCK , '" />',"\n"
		, '</td></tr></table>'
		, '<div style="text-align: center;"><br />[ <a href="index.php?file=Admin&amp;page=block"><b>' , _BACK , '</b></a> ]</div></form><br /></div></div>',"\n";

}
?>