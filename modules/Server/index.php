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

translate('modules/Server/lang/' . $language . '.lang.php');
opentable();
$visiteur = ($user) ? $user[1] : 0; 
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);

if ($visiteur >= $level_access && $level_access > -1) {
   compteur('Server');

    function index() {
        global $bgcolor1, $bgcolor2, $bgcolor3, $nuked;

        $sql = mysql_query("SELECT cid, titre, description FROM " . SERVER_CAT_TABLE . " ORDER BY titre");
        while ($row = mysql_fetch_assoc($sql)) {
            $row['titre'] = printSecuTags($row['titre']);
            $serverlist = null;

            echo "<br /><table width=\"100%\" cellspacing=\"5\" cellpadding=\"0\">\n"
               . "<tr><td align=\"center\"><big><b>" . $row['titre'] . "</b></big></td></tr>\n"
               . "<tr><td align=\"center\">" . $row['description'] . "</td></tr></table>\n";

            $test = 0;
            $sql2 = mysql_query("SELECT sid, game, ip, port, pass FROM " . SERVER_TABLE . " WHERE cat = '" . $row['cid'] . "' ORDER BY sid");
            while($raw = mysql_fetch_assoc($sql2)) {
                $test++;
                $serverlist[] = array($raw['ip'], $raw['port'], $raw['pass'], $raw['sid'], $raw['game']);
            } 

            if ($test <> 0) {
                echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
                . "<tr style=\"background: ". $bgcolor3 . "\">\n"
                . "<td style=\"width: 30%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
                . "<td style=\"width: 25%;\" align=\"center\"><b>" . _SERVIP . "</b></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><b>" . _TYPE . "</b></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><b>" . _MAP . "</b></td>\n"
                . "<td style=\"width: 15%;\" align=\"center\"><b>" . _PLAYER . "</b></td></tr>\n";

                $CountServerList = sizeof($serverlist);
                for ($i = 0; $i <= $CountServerList-1; $i++) {
                    $address = $serverlist[$i][0];
                    $port = $serverlist[$i][1];
                    $password = $serverlist[$i][2];
                    $id = $serverlist[$i][3];
                    $game = $serverlist[$i][4];

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

                    if ($j == 0) {
                        $bg = $bgcolor2;
                        $j++;
                    } else {
                        $bg = $bgcolor1;
                        $j = 0;
                    } 

                    $gameserver = queryServer($address, $queryport, $protocol);

                    if (!$gameserver) {
                        echo "<tr style=\"background: ". $bg . "\">\n"
                        . "<td style=\"width: 30%;\">" . _SEVERDOWN . "</td>\n"
                        . "<td style=\"width: 25%;\" align=\"center\">" . $address . ":" . $port . "</td>\n"
                        . "<td style=\"width: 15%;\" align=\"center\">...</td>\n"
                        . "<td style=\"width: 15%;\" align=\"center\">...</td>\n"
                        . "<td style=\"width: 15%;\" align=\"center\">0/0</td></tr>\n";
                    } else {
                        echo "<tr style=\"background: ". $bg . "\">\n"
                        . "<td style=\"width: 30%;\"><a href=\"index.php?file=Server&amp;op=server&amp;server_id=" . $id . "\">" . printSecuTags($gameserver->servertitle) . "</a></td>\n"
                        . "<td style=\"width: 25%;\" align=\"center\">" . $address . ":" . $port . "</td>\n"
                        . "<td style=\"width: 15%;\" align=\"center\">" . $game . "</td>\n"
                        . "<td style=\"width: 15%;\" align=\"center\">" . printSecuTags($gameserver->mapname);

                        if ($gameserver->maptitle) {
                            echo printSecuTags($gameserver->maptitle);
                        } 

                        echo "</td><td style=\"width: 15%;\" align=\"center\">" . printSecuTags($gameserver->numplayers) . "/" . printSecuTags($gameserver->maxplayers) . "</td></tr>\n";
                    } 
                } 

                echo "</table>\n";
            } else {
                echo "<div style=\"text-align: center;\">" . _NOSERVER . "</div>\n";
            } 
        } 

        echo "<br /><form method=\"post\" action=\"index.php?file=Server&amp;op=server\">\n"
           . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellpadding=\"2\" cellspacing=\"0\">\n"
           . "<tr><td colspan=\"4\" align=\"center\"><b>" . _SERVERINFOS . " :</b></td></tr>\n"
           . "<tr><td><i>" . _SERVIP . "</i></td><td><input type=\"text\" size=\"20\" maxlength=\"40\" name=\"address\" /></td>"
           . "<td><i>" . _SERVPORT . "</i></td><td><input type=\"text\" size=\"10\" maxlength=\"20\" name=\"port\" /></td></tr>\n"
           . "<tr><td colspan=\"4\"><i>" . _SERVERGAME . "</i>&nbsp;<select name=\"game\">"
           . "<option value=\"CSS\">CS:Source</option>\n"
           . "<option value=\"HL2\">Half-life 2</option>\n"
           . "<option value=\"HL\">Half-life</option>\n"
           . "<option value=\"DOOM3\">Doom 3</option>\n"
           . "<option value=\"FARCRY\">Far Cry</option>\n"
           . "<option value=\"Q3\">Quake 3</option>\n"
           . "<option value=\"MOHAA\">MOHAA</option>\n"
           . "<option value=\"RTCW\">RTCW</option>\n"
           . "<option value=\"COD\">COD</option>\n"
           . "<option value=\"UT\">UT</option>\n"
           . "<option value=\"UT2003\">UT2003</option>\n"
           . "<option value=\"UT2004\">UT2004</option>\n"
           . "<option value=\"IGI2\">IGI2</option>\n"
           . "<option value=\"NWN\">Neverwinter Nights</option>\n"
           . "<option value=\"AA\">America's Army</option>\n"
           . "<option value=\"BTF1942\">Battlefield 1942</option></select>\n"
           . "&nbsp;<input type=\"submit\" value=\"" . _SEARCH . "\" /></td></tr></table></form><br />";

    } 

    function server($server_id) {
        global $nuked, $address, $port, $game, $sgame, $sortby, $bgcolor1, $bgcolor2, $bgcolor3;

        if (!empty($server_id)) {
            $sql = mysql_query("SELECT game, ip, port, pass FROM " . SERVER_TABLE . " WHERE sid = '" . $server_id . "'");
            $row = mysql_fetch_assoc($sql);
        } else if (!empty($address) && !empty($port)) {
            $address = printSecuTags($row['ip']);
            $port = printSecuTags($row['port']);
            $address = nk_CSS($address);
            $port = nk_CSS($port);
        } else {
            $address = $nuked['server_ip'];
            $port = $nuked['server_port'];
            $game = $nuked['server_game'];
            $password = $nuked['server_pass'];
        } 

        $game = $row['game'];
        $password = $row['pass'];

        if (!empty($sgame)) $game = $sgame;

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

        $gameserver = queryServer($address, $queryport, $protocol);

        if (!$gameserver) {
            echo '<br /><div style="text-align: center;"><big><b>' . $address . ':' . $port . '</b></big><br /><br /><br /><b>' . _SEVERDOWN . '</b></div><br /><br />';
        } else {
            $screen = 'modules/Server/images/' . printSecuTags($gameserver->mapname);
            $screen = preg_replace("`$`", ".jpg", $screen);

            if (is_file($screen)) {
                $mapimage = $screen;
            } else {
                $mapimage = 'modules/Server/images/nopicture.jpg';
            } 

            echo "<br /><div style=\"text-align: center;\"><big><b>" . printSecuTags($gameserver->servertitle) . "</b></big></div><br />\n"
            . "<div style=\"text-align: center;\"><img src=\"$mapimage\" alt=\"\" title=\"" . printSecuTags($gameserver->mapname) . "\" /></div><br />\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"350\" cellpadding=\"2\" cellspacing=\"1\">\n"
            . "<tr style=\"background: ". $bgcolor3 . "\"><td align=\"center\" colspan=\"2\"><b>" . _SERVERDETAIL . "</b></td></tr>\n"
            . "<tr style=\"background: ". $bgcolor2 . "\"><td>&nbsp;<b>" . _ADDRESS . " :</b></td><td>" . $address . ":" . $port . "</td></tr>\n"
            . "<tr style=\"background: ". $bgcolor1 . "\"><td>&nbsp;<b>" . _NBPLAYER . " :</b></td><td>" . printSecuTags($gameserver->numplayers) . "/" . printSecuTags($gameserver->maxplayers) . "</td></tr>\n";

            if (!empty($password)) {
                echo "<tr style=\"background: ". $bgcolor2 . "\"><td>&nbsp;<b>" . _SERVERPASS . " :</b></td><td>" . $password . "</td></tr>\n";
            } else {
                if ($gameserver->password == 1) {
                    $pass = 'yes';
                } else if ($gameserver->password == 0) {
                    $pass = 'no';
                } else {
                    $pass = 'unknown';
                } 
                echo "<tr style=\"background: ". $bgcolor2 . "\"><td>&nbsp;<b>" . _SERVERPASS . " :</b></td><td>" . $pass . "</td></tr>\n";
            } 

            echo "<tr style=\"background: ". $bgcolor1 . "\"><td>&nbsp;<b>" . _GAME . " :</b></td><td>" . printSecuTags($gameserver->gametype) . "</td></tr>\n"
            . "<tr style=\"background: ". $bgcolor2 . "\"><td>&nbsp;<b>" . _MAP . " :</b></td><td>" . printSecuTags($gameserver->mapname) . "</td></tr>\n"
            . "<tr style=\"background: ". $bgcolor1 . "\"><td>&nbsp;<b>" . _SERVERRULES . " :</b></td><td><select style=\"width: 200px\">\n";

            if (!count($gameserver->rules)) {
                echo "<option>No rules</option>\n";
            } 

            foreach($gameserver->rules as $key => $value) {
                if (empty($value)) {
                    $value = "&nbsp;";
                } 
                echo "<option>" . $key . " : " . $value . "</option>\n";
            } 

            echo "</select></td></tr></table><br />\n";

            if (!$sortby) {
                foreach($gameserver->playerkeys as $key => $value) {
                    if ($value && ($key == 'score' || $key == 'frags' || $key == 'deaths' || $key == 'honor')) {
                        $sortby = $key;
                        break;
                    } 
                } 
                if (!$sortby) {
                    $sortby = 'name';
                } 
            } 

            if ($gameserver->playerkeys['team']) {
                $i = 0;
                foreach($gameserver->players as $player) {
                    $teams[$player['team']][$i++] = $player;
                } 
                if (count($teams) == 2) {
                    $i = 0;
                    foreach ($teams as $team) {
                        if (count($team)) {
                            $sortedteams[$i++] = $gameserver->sortPlayers($team, $sortby);
                        } 
                    } 

                    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\">\n"
                       . "<tr><td align=\"center\"><b>" . ucfirst($gameserver->playerteams[0]) . "</b></td>\n"
                       . "<td align=\"center\"><b>" . ucfirst($gameserver->playerteams[1]) . "</b></td></tr>\n"
                       . "<tr><td valign=\"top\">" . listPlayers($server_id, $address, $port, $game, $password, $sortedteams[0], $gameserver->playerkeys, $sortby, true, $lines1) . "</td>\n"
                       . "<td valign=\"top\">" . listPlayers($server_id, $address, $port, $game, $password, $sortedteams[1], $gameserver->playerkeys, $sortby, true, $lines2) . "</td></tr></table>\n";
                }  else {
                    $players = $gameserver->sortPlayers($gameserver->players, $sortby);
                    echo listPlayers($server_id, $address, $port, $game, $password, $players, $gameserver->playerkeys, $sortby);
                } 
            } else {
                $players = $gameserver->sortPlayers($gameserver->players, $sortby);
                echo listPlayers($server_id, $address, $port, $game, $password, $players, $gameserver->playerkeys, $sortby);
            } 

            echo "<br />\n";
        } 
    } 

    function listPlayers($server_id, $address, $port, $game, $password, $players, $keys, $cursort = 'name', $summary = false, $emptylines = 0) {
        global $bgcolor1, $bgcolor2, $bgcolor3;

        if (count($players)) {
            $result = "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"350\" cellpadding=\"2\" cellspacing=\"1\"><tr style=\"background: ". $bgcolor3 . "\">\n";

            if ($cursort != 'name') {
                $result .= "<td>&nbsp;&nbsp;<b><a href=\"index.php?file=Server&amp;op=server&amp;address=" . $address . "&amp;port=" . $port . "&amp;game=" . $game . "&amp;server_id=" . $server_id . "&amp;password=" . $password . "&amp;sortby=name\" style=\"text-decoration: underline\">" . _NICK . "</a></b></td>\n";
            } else {
                $result .= "<td>&nbsp;&nbsp;<b>" . _NICK . "</b></td>\n";
            } 

            if ($keys['score']) {
                if ($cursort != 'score') {
                    $result .= "<td><b><a href=\"index.php?file=Server&amp;op=server&amp;address=" . $address . "&amp;port=" . $port . "&amp;game=" . $game . "&amp;server_id=" . $server_id . "&amp;password=" . $password . "&amp;sortby=score\" style=\"text-decoration: underline\">" . _SCORE . "</a></b></td>\n";
                } else {
                    $result .= "<td><b>" . _SCORE . "</b></td>\n";
                } 
            } 

            if ($keys['honor']) {
                if ($cursort != 'honor') {
                    $result .= "<td><b><a href=\"index.php?file=Server&amp;op=server&amp;address=" . $address . "&amp;port=" . $port . "&amp;game=" . $game . "&amp;server_id=" . $server_id . "&amp;password=" . $password . "&amp;sortby=honor\" style=\"text-decoration: underline\">" . _HONOR . "</a></b></td>\n";
                } else {
                    $result .= "<td><b>" . _HONOR . "</b></td>\n";
                } 
            } 
            if ($keys['frags']) {
                if ($cursort != 'frags') {
                    $result .= "<td><b><a href=\"index.php?file=Server&amp;op=server&amp;address=" . $address . "&amp;port=" . $port . "&amp;game=" . $game . "&amp;server_id=" . $server_id . "&amp;password=" . $password . "&amp;sortby=frags\" style=\"text-decoration: underline\">" . _FRAG . "</a></b></td>\n";
                } else {
                    $result .= "<td><b>" . _FRAG . "</b></td>\n";
                } 
            } 
            if ($keys['deaths']) {
                if ($cursort != 'deaths') {
                    $result .= "<td><b><a href=\"index.php?file=Server&amp;op=server&amp;address=" . $address . "&amp;port=" . $port . "&amp;game=" . $game . "&amp;server_id=" . $server_id . "&amp;password=" . $password . "&amp;sortby=deaths\" style=\"text-decoration: underline\">" . _DEATHS . "</a></b></td>\n";
                } else {
                    $result .= "<td><b>" . _DEATHS . "</b></td>\n";
                } 
            } 
            if ($keys['ping']) {
                $result .= "<td><b>" . _PING . "</b></td>\n";
            } 
            $result .= "</tr>";

            foreach ($players as $player) {
                if ($j == 0) {
                    $bg = $bgcolor2;
                    $j++;
                } else {
                    $bg = $bgcolor1;
                    $j = 0;
                } 

                if ($player['name']) {
                    $result .= "<tr style=\"background: ". $bg . "\"><td>" . printSecuTags($player['name']) . "</td>\n";
                    if ($keys['score']) {
                        $result .= "<td>" . printSecuTags($player['score']) . "</td>\n";
                        $sumscore += $player['score'];
                    } 
                    if ($keys['honor']) {
                        $result .= "<td>" . printSecuTags($player['honor']) . "</td>\n";
                        $sumhonor += $player['honor'];
                    } 
                    if ($keys['frags']) {
                        $result .= "<td>" . printSecuTags($player['frags']) . "</td>\n";
                        $sumfrags += $player['frags'];
                    } 
                    if ($keys['deaths']) {
                        $result .= "<td>" . printSecuTags($player['deaths']) . "</td>\n";
                        $sumdeaths += $player['deaths'];
                    } 
                    if ($keys['ping']) {
                        $result .= "<td>" . printSecuTags($player['ping']) . "</td>\n";
                    } 
                    $result .= "</tr>\n";
                } 
            } 
            for($i = 0;$i < $emptylines;$i++) {
                $result .= "<tr></tr>\n";
            } 
            $result .= "</table>\n";
        } else {
            $result = "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"350\" cellpadding=\"2\" cellspacing=\"1\">\n"
            . "<tr style=\"background: ". $bgcolor3 . "\"><td>&nbsp;&nbsp;<b>" . _NICK . "</b></td><td><b>" . _SCORE . "</b></td></tr>\n"
            . "<tr><td colspan=\"2\" align=\"center\">" . _NOPLAYERS . "</td></tr></table>\n";
        } 
        return $result;
    } 

    function queryServer($address, $port, $protocol) {
        include 'modules/Server/includes/gsQuery.php';

        if (!$address && !$port && !$protocol) {
            return false;
        } 

        $gameserver = gsQuery::createInstance($protocol, $address, $port);
        if (!$gameserver) {
            return false;
        } 

        if (!$gameserver->query_server(true, true)) {
            return false;
        } 
        return $gameserver;
    } 

    switch ($_REQUEST['op']) {
        case 'index':
            index();
            break;

        case 'server':
            server($server_id);
            break;

        default :
            index();
            break;
    } 

} else if ($level_access == -1) {
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
} else if ($level_access == 1 && $visiteur == 0) {
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b></div><br /><br />";
} else {
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
} 

closetable();

?>
