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
    $and .= "(titre LIKE '%" . $main . "%' OR description LIKE '%" . $main . "%')";
}
else if ($main != ""){
    $sep = "";
    $and .= "(";
    for($i = 0; $i < count($search); $i++)
    {
        $and .= $sep . "(titre LIKE '%" . $search[$i] . "%' OR description LIKE '%" . $search[$i] . "%')";
        if ($searchtype == "matchor") $sep = " OR ";
        else $sep = " AND ";
    }
    $and .= ")";
}

$req = "SELECT sid, titre, date FROM " . GALLERY_TABLE . " WHERE " . $and . " ORDER BY sid DESC";
$sql_img = mysql_query($req);

$nb_img = mysql_num_rows($sql_img);

if ($nb_img > 0){
    while (list($img_id, $img_titre, $img_date) = mysql_fetch_array($sql_img)){
        $img_titre = htmlentities($img_titre);
        $img_date = nkDate($img_date);
        if ($img_date) $img_date = " - " . _ADDED . " " . $img_date;
        $tab['module'][] = "$modname";
        $tab['title'][] = "<b>" . $img_titre . "</b>" . $img_date;
        $tab['link'][] = "index.php?file=Gallery&amp;op=description&amp;sid=" . $img_id;
    }
}
?>