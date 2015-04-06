<?php

$id = 2;

$maxEntries = 6;

$levelAccess = array_key_exists(1, $GLOBALS['user']) ? $GLOBALS['user'][1] : 0;

$dbsMenu = 'SELECT content FROM '.BLOCK_TABLE.' WHERE `type`="menu" AND `bid` = "'.$id.'" ';
$dbeMenu = mysql_query($dbsMenu) or die (mysql_error());
$dbrMenu = mysql_fetch_assoc($dbeMenu);

$menuRow = explode('NEWLINE', $dbrMenu['content']);

$arrayTemp = array();

$i = 1;
foreach ($menuRow as $row) {
    $row = explode('|', $row);
    $arrayTemp[$i]['link'] = $row[0];
    $arrayTemp[$i]['title'] = $row[1];
    $arrayTemp[$i]['Comment'] = $row[2];
    $arrayTemp[$i]['level'] = $row[3];
    $arrayTemp[$i]['blank'] = $row[4];
    $i++;
}

$arrayMenu = array(
    array(
        'title' => 'home',
        'link'  => 'index.php',
        'blank' => null
    )
);
$i = 1;
$j = 1;
$k = 1;
$subnav = false;
foreach ($arrayTemp as $menuItem) {

    if (preg_match('#\[([A-Za-z0-9_\-]+)\]#', $menuItem['link'])) {
        $link = 'index.php?file='.substr($menuItem['link'], 1, -1);
    }
    else {
        $link = $menuItem['link'];
    }

    $blank = null;

    if ($menuItem['blank'] == 1) {
        $blank = ' target="_blank" ';
    }

    $title = $menuItem['title'];

    if((empty($menuItem['link']) && $subnav === true)){
        $subnav = false;
        $i++;
    }

    if ($menuItem['level'] <= $levelAccess) {
        if ($subnav === true) {
            $arrayMenu[$i]['subnav'][$j]['title'] = $title;
            $arrayMenu[$i]['subnav'][$j]['link'] = $link;
            $arrayMenu[$i]['subnav'][$j]['blank'] = $blank;
            $j++;
        }
        else {
            $arrayMenu[$i]['title'] = $title;
            $arrayMenu[$i]['link'] = $link;
            $arrayMenu[$i]['blank'] = $blank;
            if(!empty($menuItem['link'])){
                $i++;
            }

        }
    }

    if(empty($menuItem['link'])){
        $subnav = true;
        $k++;
        $j = 1;
    }
}

$this->assign('mainNavContent', $arrayMenu);