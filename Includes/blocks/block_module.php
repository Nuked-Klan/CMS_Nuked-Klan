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

function affich_block_module($blok){
    $blok['content'] = inc_bl($blok['module'], $blok['bid']);
    return $blok;
}

function edit_block_module($bid){
    global $nuked, $language;

    $sql = mysql_query('SELECT active, position, titre, module, content, type, nivo, page FROM ' . BLOCK_TABLE . ' WHERE bid = \'' . $bid . '\' ');
    list($active, $position, $titre, $modul, $content, $type, $nivo, $pages) = mysql_fetch_array($sql);
    //check des modules
    $handle = opendir('modules/');
	
    while ($mod = readdir($handle)){
        if($mod != 'index.html' && file_exists('modules/'.$mod.'/blok.php')) $autorized_modules[] = $mod;             
    }
	
    if (false===array_search($modul, $autorized_modules)){
         die('<br /><br /><div style="text-align: center;"><big>Blok corrupted, lease delete it!</big></div><br /><br />');
    }
	
    $titre = printSecuTags($titre);

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
			, '<tr><td colspan="4"><b>' , _TYPE , ' : </b> ' , _MODBLOCK , '</td></tr><tr><td colspan="4"><select name="module">',"\n";

    select_mod($modul);

    echo '</select></td></tr><tr><td colspan="4">&nbsp;</td></tr>',"\n"
		, '<tr><td colspan="4" align="center"><b>' , _PAGESELECT , ' :</b></td></tr><tr><td colspan="4">&nbsp;</td></tr>',"\n"
		, '<tr><td colspan="4" align="center"><select name="pages[]" size="8" multiple="multiple">',"\n";

    select_mod2($pages);

    echo '</select></td></tr><tr><td colspan="4" align="center"><br />',"\n"
		, '<input type="hidden" name="type" value="' , $type , '" />',"\n"
		, '<input type="hidden" name="bid" value="' , $bid , '" />',"\n"
		, '<input type="submit" name="send" value="' , _MODIFBLOCK , '" />',"\n"
		, '</td></tr></table>',"\n"
		, '<div style="text-align: center;"><br />[ <a href="index.php?file=Admin&amp;page=block"><b>' , _BACK , '</b></a> ]</div></form><br /></div></div>',"\n";

}

function inc_bl($modul, $bid){
    //check des modules
    $handle = opendir('modules/');
	
    while ($mod = readdir($handle)){
        if($mod != 'index.html' && file_exists('modules/'.$mod.'/blok.php')) $autorized_modules[] = $mod;             
    }
	
    if (false===array_search($modul, $autorized_modules)){
         $blok_content = '';
    }
    else{
        ob_start();
        print eval("\$bid = \"$bid\";");
        print eval(' include ("modules/" . $modul . "/blok.php"); ');
        $blok_content = ob_get_contents();
        ob_end_clean();
    }
    return $blok_content;
}
?>