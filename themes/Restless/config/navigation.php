<?php

$id = 2;

$maxEntries = 6;

$levelAccess = array_key_exists(1, $GLOBALS['user']) ? $GLOBALS['user'][1] : 0;

$dbsMenu = 'SELECT content FROM '.BLOCK_TABLE.' WHERE `type`="menu" AND `bid` = "'.$id.'" ';
$dbeMenu = mysql_query($dbsMenu) or die (mysql_error());
$dbrMenu = mysql_fetch_assoc($dbeMenu);

$menuRow = explode('NEWLINE', $dbrMenu['content']);

$arrayMenu = array(
                array(
                    'title'  => 'Home',
                    'link'   => 'index.php',
                    'blank'  => null
                )
            );

foreach($menuRow as $row) {

    if (count($arrayMenu) > $maxEntries) {
        break;
    }

    $arrayRow = explode('|', $row);

    $arrayRow[1] = preg_replace('#<img(.*)/>#', '', $arrayRow[1]);

    if(empty($arrayRow[0])) {
        $arrayMenu[count($arrayMenu)] = array(
                                'title'  => $arrayRow[1],
                                'link'   => '#',
                                'blank'  => !empty($arrayRow[4])
                            );
    }
    else{
        if($arrayRow[3] <= $levelAccess) {


            $link = preg_match('#^\[([A-Za-z_\-0-9]{3,})\]$#', $arrayRow[0]) ? 'index.php?file='.substr($arrayRow[0], 1, -1) : $arrayRow[0];
            $temp = array('title' => $arrayRow[1], 'link' => $link, 'blank' => !empty($arrayRow[4]));
            if(count($arrayMenu) > 1) {
                $arrayMenu[count($arrayMenu) - 1]['subnav'][] = $temp;
            }
            else{
                $arrayMenu[count($arrayMenu)] = $temp;
            }

        }
    }
}

$this->mainNavContent = $arrayMenu;