<?php
/**
 * blok.php
 *
 * Display block of News module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or exit('You can\'t run this file alone.');

translate('modules/News/lang/'. $language .'.lang.php');


$day = time();

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