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
    $and .= "(titre LIKE '%" . $main . "%' OR comment LIKE '%" . $main . "%')";
}
else if ($main != ""){
    $sep = "";
    $and .= "(";
    for($i = 0; $i < count($search); $i++){
        $and .= $sep . "(titre LIKE '%" . $search[$i] . "%' OR comment LIKE '%" . $search[$i] . "%')";
        if ($searchtype == "matchor") $sep = " OR ";
        else $sep = " AND ";
    }
    $and .= ")";
}

$req = "SELECT module,  im_id, autor, titre, date FROM " . COMMENT_TABLE . " WHERE " . $and . " ORDER BY id DESC";
$sql_com = mysql_query($req);

$nb_com = mysql_num_rows($sql_com);

if ($nb_com > 0){
    while (list($com_module, $im_id, $com_autor, $com_titre, $com_date) = mysql_fetch_array($sql_com)){
            if ($com_titre != ""){
                $com_titre = htmlentities($com_titre);
                $com_titre = nk_CSS($com_titre);
            }
            else{
                $com_titre = $com_module;
            }

			$com_date = nkDate($com_date);
			$tab['module'][] = $modname;
			$tab['title'][] = "<b>" . $com_titre . "</b> - " . _BY . "&nbsp;" . $com_autor . "&nbsp;" . _THE . "&nbsp;" . $com_date;

            if ($com_module == "news"){
                $tab['link'][] = "index.php?file=News&amp;op=index_comment&amp;news_id=" . $im_id;
            }
            else if ($com_module == "Gallery"){
                $tab['link'][] = "index.php?file=Gallery&amp;op=description&amp;sid=" . $im_id;
            }
            else if ($com_module == "Wars"){
                $tab['link'][] = "index.php?file=Wars&amp;op=detail&amp;war_id=" . $im_id;
            }
            else if ($com_module == "Links"){
                $tab['link'][] = "index.php?file=Links&amp;op=description&amp;link_id=" . $im_id;
            }
            else if ($com_module == "Download"){
                $tab['link'][] = "index.php?file=Download&amp;op=description&amp;dl_id=" . $im_id;
            }
            else if ($com_module == "Survey"){
                $tab['link'][] = "index.php?file=Survey&amp;op=affich_res&amp;sid=" . $im_id;
            }
            else if ($com_module == "Sections"){
                $tab['link'][] = "index.php?file=Sections&amp;op=article&amp;artid=" . $im_id;
            }
            else $tab['link'][] = "#";
    }
}
?>