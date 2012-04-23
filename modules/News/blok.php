<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('<div style="text-align: center;">You cannot open this page directly</div>');

translate("modules/News/lang/$language.lang.php");

$day = time();

$Str = mysql_query("SELECT active FROM " . BLOCK_TABLE . " WHERE bid = '$bid'");
list($active) = mysql_fetch_array($Str);

$listStyle = ($active == 3 or $active == 4) ? 'list-style:none' : 'list-style:inline';
$margin = ($active == 3 or $active == 4) ? '5px' : '11px';

echo '<ul style="margin:5px ' . $margin . ';padding:5px;' . $listStyle . '">';

$Sql = mysql_query("SELECT N.id, N.titre, N.date, N.auteur, N.auteur_id, U.pseudo FROM " . NEWS_TABLE . " N, " . USER_TABLE . " U WHERE '$day' >= N.date AND U.id = N.auteur_id ORDER BY date DESC LIMIT 0, 5");
while ($row = mysql_fetch_assoc($Sql)) {
	
	$row['date'] = nkDate($row['date']);
	$row['titre'] = printSecuTags($row['titre']);
	$titre = (strlen($row['titre']) > 30) ? substr($row['titre'],0,30).'...' : $row['titre'];
	$auteur = (!empty($row['auteur_id'])) ? $row['pseudo'] : $row['auteur'];
	$title = _BY . ' ' . $auteur . ' ( ' . $row['date'] . ' )';
	
	if ($active == 3 or $active == 4) {
		
		echo '<li>
	              <img src="modules/News/images/folder.png" witdh="22" height="22" alt="Folder" style="float:left;margin-top:4px" />
			      <div style="float:left;padding:0 0 2px 3px">
		              <h2 style="font-size:12px;margin:0;padding:0">
				          <a href="index.php?file=News&amp;op=index_comment&amp;news_id=' . $row['id'] . '" title="' . $row['titre'] . '"><b>' . $row['titre'] . '</b></a>
				      </h2>
				      <p style="margin:0;padding:0">
					      ' . _BY . ' <a href="index.php?file=Team&amp;op=detail&amp;autor=' . urlencode($auteur) . '" title="">' . $auteur . '</a> ( ' . $row['date'] . ' )
					  </p>
				  </div>
				  <hr style="color: ' . $bgcolor3 . ';height:1px;clear:both" />
			 </li>';
		
	} else {
	    
		echo '<li>
	              <a href="index.php?file=News&amp;op=index_comment&amp;news_id=' . $row['id'] . '" title="' . $title . '"><b>' . $titre . '</b></a>
				  <hr style="color: ' . $bgcolor3 . ';height:1px" />
			 </li>';
	    
	}
}

echo '</ul>';
?>