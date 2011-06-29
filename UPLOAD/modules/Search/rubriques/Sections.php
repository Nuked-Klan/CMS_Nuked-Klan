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

global $nuked, $user;

$and = "";

if ($autor != "" && $main != ""){
    $and .= "(autor LIKE '%" . $autor . "%') AND ";
}
else if ($autor != ""){
    $and .= "(autor LIKE '%" . $autor . "%')";
}

if ($searchtype == "matchexact" && $main != ""){
    $and .= "(title LIKE '%" . $main . "%' OR content LIKE '%" . $main . "%')";
}
else if ($main != ""){
    $sep = "";
    $and .= "(";
	
    for($i = 0; $i < count($search); $i++){
        $and .= $sep . "(title LIKE '%" . $search[$i] . "%' OR content LIKE '%" . $search[$i] . "%')";
        if ($searchtype == "matchor") $sep = " OR ";
        else $sep = " AND ";
    }
	
    $and .= ")";
}

$req = "SELECT artid, title, date FROM " . SECTIONS_TABLE . " WHERE " . $and . " ORDER BY artid DESC";
$sql_art = mysql_query($req);

$nb_art = mysql_num_rows($sql_art);

if ($nb_art > 0){
    while (list($art_id, $art_titre, $art_date) = mysql_fetch_array($sql_art)){
        $art_titre = htmlentities($art_titre);
        $art_date = nkDate($art_date);
        $tab['module'][] = $modname;
        $tab['title'][] = "<b>" . $art_titre . "</b> - " . _ADDED . "&nbsp;" . $art_date;
        $tab['link'][] = "index.php?file=Sections&amp;op=article&amp;artid=" . $art_id;
    }
}
?>