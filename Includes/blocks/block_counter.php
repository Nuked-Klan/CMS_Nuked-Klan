<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

function affich_block_counter($blok){
    global $nuked;

    $sql = mysql_query('SELECT count FROM ' . STATS_TABLE . ' WHERE type = "pages"');
    while (list($count) = mysql_fetch_array($sql))
    {
        $visites = $visites + $count;
    }

    $nb_digits = max(strlen($visites), 8);
    $visites = substr('0000000000' . $visites, - $nb_digits);

    $visites = str_replace('0', '<img src="modules/Stats/images/compteur/0.jpg" alt="" />', $visites);
    $visites = str_replace('1', '<img src="modules/Stats/images/compteur/1.jpg" alt="" />', $visites);
    $visites = str_replace('2', '<img src="modules/Stats/images/compteur/2.jpg" alt="" />', $visites);
    $visites = str_replace('3', '<img src="modules/Stats/images/compteur/3.jpg" alt="" />', $visites);
    $visites = str_replace('4', '<img src="modules/Stats/images/compteur/4.jpg" alt="" />', $visites);
    $visites = str_replace('5', '<img src="modules/Stats/images/compteur/5.jpg" alt="" />', $visites);
    $visites = str_replace('6', '<img src="modules/Stats/images/compteur/6.jpg" alt="" />', $visites);
    $visites = str_replace('7', '<img src="modules/Stats/images/compteur/7.jpg" alt="" />', $visites);
    $visites = str_replace('8', '<img src="modules/Stats/images/compteur/8.jpg" alt="" />', $visites);
    $visites = str_replace('9', '<img src="modules/Stats/images/compteur/9.jpg" alt="" />', $visites);

    $blok['content'] = '<div style="text-align: center; padding-top: 10px">' . $visites . '</div><br />'."\n";

    return $blok;
}

function edit_block_counter($bid){
    global $nuked, $language;

    $sql = mysql_query('SELECT active, position, titre, module, content, type, nivo, page FROM ' . BLOCK_TABLE . ' WHERE bid = \'' . $bid . '\' ');
    list($active, $position, $titre, $modul, $content, $type, $nivo, $pages) = mysql_fetch_array($sql);
    $titre = printSecuTags($titre);

    if ($active == 1) $checked1 = 'selected="selected"';
    else if ($active == 2) $checked2 = 'selected="selected"';
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
			, '<td style="text-align:center;"><select name="active">',"\n"
			, '<option value="1" ' , $checked1 , '>' , _LEFT , '</option>',"\n"
			, '<option value="2" ' , $checked2 , '>' , _RIGHT , '</option>',"\n"
			, '<option value="0" ' , $checked0 , '>' , _OFF , '</option></select></td>',"\n"
			, '<td style="text-align:center;"><input type="text" name="position" size="2" value="' , $position , '" /></td>',"\n"
			, '<td style="text-align:center;"><select name="nivo"><option>' , $nivo , '</option>',"\n"
			, '<option>0</option>',"\n"
			, '<option>1</option>',"\n"
			, '<option>2</option>',"\n"
			, '<option>3</option>',"\n"
			, '<option>4</option>',"\n"
			, '<option>5</option>',"\n"
			, '<option>6</option>',"\n"
			, '<option>7</option>',"\n"
			, '<option>8</option>',"\n"
			, '<option>9</option></select></td></tr><tr><td colspan="4">&nbsp;</td></tr>',"\n"
			, '<tr><td colspan="4" style="text-align:center;"><b>' , _PAGESELECT , ' :</b></td></tr><tr><td colspan="4">&nbsp;</td></tr>',"\n"
			, '<tr><td colspan="4" style="text-align:center;"><select name="pages[]" size="8" multiple="multiple">',"\n";

    select_mod2($pages);

    echo '</select></td></tr><tr><td colspan="4" style="text-align:center;"><br />',"\n"
			, '<input type="hidden" name="type" value="' , $type , '" />',"\n"
			, '<input type="hidden" name="bid" value="' , $bid , '" />',"\n"
			, '<input type="submit" name="send" value="' , _MODIFBLOCK , '" />',"\n"
			, '</td></tr></table>'
			, '<div style="text-align: center;"><br />[ <a href="index.php?file=Admin&amp;page=block"><b>' , _BACK , '</b></a> ]</div></form><br /></div>',"\n";
}
?>