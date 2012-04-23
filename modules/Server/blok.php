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

global $language;
translate('modules/Server/lang/' . $language . '.lang.php');
include 'modules/Server/includes/gsQuery.php';

$sql2 = mysql_query("SELECT active FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
list($active) = mysql_fetch_array($sql2);
if ($active == 3 || $active == 4) {
    $address = $nuked['server_ip'];
    $port = $nuked['server_port'];
    $game = $nuked['server_game'];
    $password = $nuked['server_pass'];

    if ($game == 'HL' || $game == 'CSS' || $game == 'HL2') {
        $protocol = 'steam';
        $queryport = $port;
    } else if ($game == 'FARCRY') {
        $protocol = 'openQuery';
        $queryport = $port + 123;
    } else if ($game == 'Q2') {
        $protocol = 'q2';
        $queryport = $port;
    } else if ($game == 'Q3') {
        $protocol = 'q3a';
        $queryport = $port;
    } else if ($game == 'MOHAA') {
        $protocol = 'q3a';
        $queryport = $port;
    } else if ($game == 'RTCW') {
        $protocol = 'q3a';
        $queryport = $port;
    } else if ($game == 'COD') {
        $protocol = 'q3a';
        $queryport = $port;
    } else if ($game == 'UT') {
        $protocol = 'gameSpy';
        $queryport = $port + 1;
    } else if ($game == 'UT2003') {
        $protocol = 'gameSpy';
        $queryport = $port + 10;
    } else if ($game == 'UT2004') {
        $protocol = 'ut2004';
        $queryport = $port + 10;
    } else if ($game == 'BTF1942') {
        $protocol = 'gameSpy';
        $queryport = 23000;
    } else if ($game == 'AA') {
        $protocol = 'armyGame';
        $queryport = $port + 1;
    } else if ($game == 'DOOM3') {
        $protocol = 'd3';
        $queryport = $port;
    } else if ($game == 'IGI2') {
        $protocol = 'igi2';
        $queryport = $port;
    } else if ($game == 'NWN') {
        $protocol = 'nwn';
        $queryport = $port;
    }

    $gameserver = gsQuery::createInstance($protocol, $address, $queryport);

    if (!$gameserver|| !$gameserver->query_server(TRUE, TRUE)) {
        echo '<br /><div style="text-align: center;">' . _SEVERDOWN . '</div><br />';
    } else {
        $screen = 'modules/Server/images/' . printSecuTags($gameserver->mapname);
        $screen = preg_replace("`$`", ".jpg", $screen);
        if (is_file($screen)) {
            $mapimage = $screen;
        } else {
            $mapimage = '"modules/Server/images/nopicture.jpg';
        }

        echo "<table width=\"100%\" cellspacing=\"5\" cellpadding=\"0\"><tr>\n"
           . "<td valign=\"top\"><img width=\"120\" src=\"" . $mapimage . "\" alt=\"\" title=\"" . printSecuTags($gameserver->mapname) . "\" /></td>\n"
           . "<td valign=\"top\"><b>" . printSecuTags($gameserver->servertitle) . "</b><br />" . _ADDRESS . " : " . $address . ":" . $port . "<br />" . _SERVERPASS . " :";

        if (!empty($password)) {
            echo "&nbsp;" . $password . "<br />\n";
        } else {
            if ($gameserver->password == 1) {
                $pass = 'yes';
            } else if ($gameserver->password == 0) {
                $pass = 'no';
            } else {
                $pass = 'unknown';
            }
            echo "&nbsp;" . $pass . "<br />\n";
        }

        echo _NBPLAYER . " : " . printSecuTags($gameserver->numplayers) . "/" . printSecuTags($gameserver->maxplayers) . "<br />\n"
           . _MAP . " : " . printSecuTags($gameserver->mapname) . "<br />\n"
           . "<a href=\"index.php?file=Server&amp;op=server\"><b>" . _MOREINFOS . "</b></a></td></tr></table>\n";
    }
} else {
    $address = $nuked['server_ip'];
    $port = $nuked['server_port'];
    $game = $nuked['server_game'];
    $password = $nuked['server_pass'];

    if ($game == 'HL' || $game == 'CSS' || $game == 'HL2') {
        $protocol = 'steam';
        $queryport = $port;
    } else if ($game == 'FARCRY') {
        $protocol = 'openQuery';
        $queryport = $port + 123;
    } else if ($game == 'Q2') {
        $protocol = 'q2';
        $queryport = $port;
    } else if ($game == 'Q3') {
        $protocol = 'q3a';
        $queryport = $port;
    } else if ($game == 'MOHAA') {
        $protocol = 'q3a';
        $queryport = $port;
    } else if ($game == 'RTCW') {
        $protocol = 'q3a';
        $queryport = $port;
    } else if ($game == 'COD') {
        $protocol = 'q3a';
        $queryport = $port;
    } else if ($game == 'UT') {
        $protocol = 'gameSpy';
        $queryport = $port + 1;
    } else if ($game == 'UT2003') {
        $protocol = 'gameSpy';
        $queryport = $port + 10;
    } else if ($game == 'UT2004') {
        $protocol = 'ut2004';
        $queryport = $port + 10;
    } else if ($game == 'BTF1942') {
        $protocol = 'gameSpy';
        $queryport = 23000;
    } else if ($game == 'AA') {
        $protocol = 'armyGame';
        $queryport = $port + 1;
    } else if ($game == 'DOOM3') {
        $protocol = 'd3';
        $queryport = $port;
    } else if ($game == 'IGI2') {
        $protocol = 'igi2';
        $queryport = $port;
    } else if ($game == 'NWN') {
        $protocol = 'nwn';
        $queryport = $port;
    }

    $gameserver = gsQuery::createInstance($protocol, $address, $queryport);

    if (!$gameserver|| !$gameserver->query_server(TRUE, TRUE)) {
        echo '<br /><div style="text-align: center;">' . _SEVERDOWN . '</div><br />';
    } else {
        $screen = 'modules/Server/images/' . printSecuTags($gameserver->mapname);
        $screen = preg_replace("`$`", ".jpg", $screen);
        if (is_file($screen)) {
            $mapimage = $screen;
        } else {
            $mapimage = 'modules/Server/images/nopicture.jpg';
        }

        echo "<div style=\"text-align: center;\"><img width=\"120\" src=\"" . $mapimage . "\" alt=\"\" title=\"" . printSecuTags($gameserver->mapname) . "\" /><br />\n"
           . printSecuTags($gameserver->numplayers) . "/" . printSecuTags($gameserver->maxplayers) . "&nbsp;" . _ON . "&nbsp;" . printSecuTags($gameserver->mapname) . "<br />\n"
           . "<a href=\"index.php?file=Server&amp;op=server\"><b>" . _MOREINFOS . "</b></a></div>";
    }
}

?>