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
    $and .= "(webmaster LIKE '%" . $autor . "%') AND ";
}
else if ($autor != ""){
    $and .= "(webmaster LIKE '%" . $autor . "%')";
}

if ($searchtype == "matchexact" && $main != ""){
    $and .= "(titre LIKE '%" . $main. "%' OR description LIKE '%" . $main . "%')";
}
else if ($main != ""){
    $sep = "";
    $and .= "(";
	
    for($i = 0; $i < count($search); $i++){
        $and .= $sep . "(titre LIKE '%" . $search[$i] . "%' OR description LIKE '%" . $search[$i] . "%')";
        if ($searchtype == "matchor") $sep = " OR ";
        else $sep = " AND ";
    }
	
    $and .= ")";
}

$req = "SELECT id, titre, date FROM " . LINKS_TABLE . " WHERE " . $and . " ORDER BY id DESC";
$sql_lk = mysql_query($req);

$nb_lk = mysql_num_rows($sql_lk);

if ($nb_lk > 0){
    while (list($link_id, $link_titre, $lk_date) = mysql_fetch_array($sql_lk)){
        $link_titre = htmlentities($link_titre);
        $lk_date = nkDate($lk_date);
        $tab['module'][] = $modname;
        $tab['title'][] = "<b>" . $link_titre . "</b> - " . _ADDED . "&nbsp;" . $lk_date;
        $tab['link'][] = "index.php?file=Links&amp;op=description&amp;link_id=" . $link_id;
    }
}
?>