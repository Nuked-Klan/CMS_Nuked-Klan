<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die('<div style="text-align:center;">You cannot open this page directly</div>');

global $nuked, $language, $user;
translate('modules/Wars/lang/' . $language . '.lang.php');

$visiteur = !$user ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1){
    compteur('Wars');

    function index(){
        global $bgcolor1, $bgcolor2, $bgcolor3, $nuked, $theme, $language;

        opentable();

        $sql = mysql_query('SELECT warid FROM '.WARS_TABLE.' WHERE etat = 1');
        $nb_matchs = mysql_num_rows($sql);

        if ($nb_matchs > 0){
            $sql_victory = mysql_query('SELECT warid FROM '.WARS_TABLE.' WHERE etat = 1 AND tscore_team > tscore_adv');
            $nb_victory = mysql_num_rows($sql_victory);
    
            $sql_defeat = mysql_query('SELECT warid FROM '.WARS_TABLE.' WHERE etat = 1 AND tscore_adv > tscore_team');
            $nb_defeat = mysql_num_rows($sql_defeat);
    
            $nb_nul = $nb_matchs - ($nb_victory + $nb_defeat);
        }
        else{
            $nb_victory = 0;
            $nb_defeat = 0;
            $nb_nul = 0;
        }

        $nb_wars = $nuked['max_wars'];
        
        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        
        $start = $_REQUEST['p'] * $nb_wars - $nb_wars;

        if ($nb_matchs == 0){
            echo '<br /><div style="text-align: center"><big><b>'._MATCHES.' - '.$nuked['name'].'</b></big></div>
                    <br /><div style="text-align: center;">'._NOMATCH.'</div><br />';
        } 
        else{
            $sql2 = mysql_query('SELECT A.titre, B.team FROM '.TEAM_TABLE.' AS A LEFT JOIN '.WARS_TABLE.' AS B ON A.cid = B.team WHERE B.etat = 1 GROUP BY B.team ORDER BY A.ordre, A.titre');
            $nb_team = mysql_num_rows($sql2);

            if (!$_REQUEST['tid'] && $nb_team > 1){
                while (list($team_name, $team) = mysql_fetch_array($sql2)){
                    if ($team_name != ''){
                        $team_name = printSecuTags($team_name);
                    }
                    else{
                        $team_name = $nuked['name'];
                    } 

                    echo '<br /><div style="text-align: center"><big><b>'._MATCHES.' - </b></big><a href="index.php?file=Wars&amp;tid='.$team.'"><b><big>'.$team_name.'</b></big></a></div>
                            <table style="margin-left: auto;margin-right: auto;text-align: left;background: '.$bgcolor2.';border: 1px solid '.$bgcolor3.';" width="100%" cellpadding="2" cellspacing="1">
                            <tr style="background: '.$bgcolor3.'">
                            <td style="width: 5%;">&nbsp;</td>
                            <td style="width: 10%;"><b>'._DATE.'</b></td>
                            <td style="width: 30%;text-align:center;"><b>'._OPPONENT.'</b></td>
                            <td style="width: 15%;text-align:center;"><b>'._TYPE.'</b></td>
                            <td style="width: 15%;text-align:center;"><b>'._STYLE.'</b></td>
                            <td style="width: 15%;text-align:center;"><b>'._RESULT.'</b></td>
                            <td style="width: 10%;text-align:center;"><b>'._DETAILS.'</b></td></tr>';

                    $sql6 = mysql_query('SELECT warid FROM '.WARS_TABLE.' WHERE etat = 1 AND team = \''.$team.'\' ');
                    $count = mysql_num_rows($sql6);

                    $sql4 = mysql_query('SELECT warid, adversaire, url_adv, pays_adv, type, style, game, date_jour, date_mois, date_an, tscore_team, tscore_adv FROM '.WARS_TABLE.' WHERE etat = 1 AND team = '.$team.' ORDER BY date_an DESC, date_mois DESC, date_jour DESC LIMIT 0, 10');
                    while (list($war_id, $adv_name, $adv_url, $pays_adv, $type, $style, $game, $jour, $mois, $an, $score_team, $score_adv) = mysql_fetch_array($sql4)){
                        $adv_name = printSecuTags($adv_name);
                        $type = printSecuTags($type);
                        $style = printSecuTags($style);

                        list ($pays, $ext) = explode ('.', $pays_adv);

                        if ($language == 'french'){
                            $date = $jour . '/' . $mois . '/' . $an;
                        } 
                        else{
                            $date = $mois . '/' . $jour . '/' . $an;
                        } 

                        if ($score_team > $score_adv){
                            $color = '#009900';
                        } 
                        else if ($score_team < $score_adv){
                            $color = '#990000';
                        } 
                        else{
                            $color = '#3333FF';
                        } 

                        if ($j == 0){
                            $bg = $bgcolor2;
                            $j++;
                        } 
                        else{
                            $bg = $bgcolor1;
                            $j = 0;
                        } 

                        $sql5 = mysql_query('SELECT name, icon FROM ' . GAMES_TABLE . ' WHERE id = \'' . $game . '\' ');
                        list($game_name, $icon) = mysql_fetch_array($sql5);
                        $game_name = printSecuTags($game_name);

                        if ($icon != '' && is_file($icon)){
                            $icone = $icon;
                        } 
                        else{
                            $icone = 'images/games/nk.gif';
                        } 

                        echo '<tr style="background: '. $bg . '">
                                <td style="width: 5%;">&nbsp;<img src="' . $icone . '" alt="" title="' . $game_name . '" /></td>
                                <td style="width: 10%;">' . $date . '</td>
                                <td style="width: 30%;"><img src="images/flags/' . $pays_adv . '" alt="" title="' . $pays . '" /> ';

                        if ($adv_url != ''){
                            echo '<a href="' . $adv_url . '" onclick="window.open(this.href); return false;">' . $adv_name . '</a>';
                        } 
                        else{
                            echo $adv_name;
                        } 

                        if (is_file('themes/' . $theme . '/images/report.png')){
                            $img = 'themes/' . $theme . '/images/report.png';
                        } 
                        else{
                            $img = 'modules/Wars/images/report.png';
                        } 

                        echo '</td><td style="width: 15%;text-align:center;">' . $type . '</td>
                                <td style="width: 15%;text-align:center;">' . $style . '</td>
                                <td style="background: ' . $color . ';width: 15%;text-align:center;"><span style="color: #FFFFFF;"><b>' . $score_team . '/' . $score_adv . '</b></span></td>
                                <td style="width: 10%;text-align:center;"><a href="index.php?file=Wars&amp;op=detail&amp;war_id=' . $war_id . '"><img style="border: 0;" src="' . $img . '" alt="" /></a></td></tr>';
                    } 
                    echo '</table>';

                    if ($count > 10){
                        echo '<div style="text-align: right;"><a href="index.php?file=Wars&amp;tid=' . $team . '">' . _MORE . '</a></div>';
                    } 
                    $j = 0;
                } 
            } 
            else{
                $nb_wars = $nuked['max_wars'];
                
                if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
                
                $start = $_REQUEST['p'] * $nb_wars - $nb_wars;

                if (!$_REQUEST['tid'] && $team > 0){
                    $_REQUEST['tid'] = $team;
                } 

                if ($_REQUEST['tid'] != ''){
                    $sql6 = mysql_query('SELECT titre FROM ' . TEAM_TABLE . ' WHERE cid = \'' . $_REQUEST['tid'] . '\' ');
                    list($team_name, $team) = mysql_fetch_array($sql6);
                    $team_name = printSecuTags($team_name);
                    $and = 'AND team = \'' . $_REQUEST['tid'] . '\' ';
                    $sql7 = mysql_query('SELECT warid FROM ' . WARS_TABLE . ' WHERE etat = 1 AND team = \'' . $_REQUEST['tid'] . '\' ');
                    $count = mysql_num_rows($sql7);
                } 
                else{
                    $team_name = $nuked['name'];
                    $and = '';
                    $count = $nb_matchs;
                } 

                echo '<br /><div style="text-align: center;"><big><b>' . _MATCHES . ' - ' . $team_name . '</b></big></div>';

                if (!$_REQUEST['orderby']){
                    $_REQUEST['orderby'] = 'date';
                } 

                if ($_REQUEST['orderby'] == 'date'){
                    $order = 'ORDER BY date_an DESC, date_mois DESC, date_jour DESC';
                } 
                else if ($_REQUEST['orderby'] == 'adver'){
                    $order = 'ORDER BY adversaire';
                } 
                else if ($_REQUEST['orderby'] == 'game'){
                    $order = 'ORDER BY game';
                } 
                else if ($_REQUEST['orderby'] == 'type'){
                    $order = 'ORDER BY type';
                } 
                else if ($_REQUEST['orderby'] == 'style'){
                    $order = 'ORDER BY style';
                } 
                else{
                    $order = 'ORDER BY date_an DESC, date_mois DESC, date_jour DESC';
                } 

                if ($count > 1){
                    echo '<br /><table width="100%"><tr><td style="text-align:right;">' . _ORDERBY . ' : </b>';

                    if ($_REQUEST['orderby'] == 'date'){
                        echo '<b>' . _DATE . '</b> | ';
                    } 
                    else{
                        echo '<a href="index.php?file=Wars&amp;tid=' . $_REQUEST['tid'] . '&amp;orderby=date">' . _DATE . '</a> | ';
                    } 

                    if ($_REQUEST['orderby'] == 'adver'){
                        echo '<b>' . _OPPONENT . '</b> | ';
                    } 
                    else{
                        echo '<a href="index.php?file=Wars&amp;tid=' . $_REQUEST['tid'] . '&amp;orderby=adver">' . _OPPONENT . '</a> | ';
                    } 

                    if ($_REQUEST['orderby'] == 'game'){
                        echo '<b>' . _GAME . '</b> | ';
                    } 
                    else{
                        echo '<a href="index.php?file=Wars&amp;tid=' . $_REQUEST['tid'] . '&amp;orderby=game">' . _GAME . '</a> | ';
                    }

                    if ($_REQUEST['orderby'] == 'type'){
                        echo '<b>' . _TYPE . '</b> | ';
                    } 
                    else{
                        echo '<a href="index.php?file=Wars&amp;tid=' . $_REQUEST['tid'] . '&amp;orderby=type">' . _TYPE . '</a> | ';
                    }

                    if ($_REQUEST['orderby'] == 'style'){
                        echo '<b>' . _STYLE . '</b>';
                    } 
                    else{
                        echo '<a href="index.php?file=Wars&amp;tid=' . $_REQUEST['tid'] . '&amp;orderby=style">' . _STYLE . '</a>';
                    }
                    
                    echo '</td></tr></table>';
                } 

                if ($count > $nb_wars){
                    $url_page = 'index.php?file=Wars&amp;tid=' . $_REQUEST['tid'] . '&amp;orderby=' . $_REQUEST['orderby'];
                    number($count, $nb_wars, $url_page);
                } 
    
                echo '<table style="margin-left: auto;margin-right: auto;text-align: left;background: ' . $bgcolor2 . ';border: 1px solid ' . $bgcolor3 . ';" width="100%" cellpadding="2" cellspacing="1">
                <tr style="background: ' . $bgcolor3 . '">
                <td style="width: 5%;">&nbsp;</td>
                <td style="width: 10%;"><b>' . _DATE . '</b></td>
                <td style="width: 30%;text-align:center;"><b>' . _OPPONENT . '</b></td>
                <td style="width: 15%;text-align:center;"><b>' . _TYPE . '</b></td>
                <td style="width: 15%;text-align:center;"><b>' . _STYLE . '</b></td>
                <td style="width: 15%;text-align:center;"><b>' . _RESULT . '</b></td>
                <td style="width: 10%;text-align:center;"><b>' . _DETAILS . '</b></td></tr>';

                $sql4 = mysql_query('SELECT warid, adversaire, url_adv, pays_adv, type, style, game, date_jour, date_mois, date_an, tscore_team, tscore_adv FROM ' . WARS_TABLE . ' WHERE etat = 1 ' . $and . $order . ' LIMIT ' . $start . ',' . $nb_wars.' ');
                while (list($war_id, $adv_name, $adv_url, $pays_adv, $type, $style, $game, $jour, $mois, $an, $score_team, $score_adv) = mysql_fetch_array($sql4)){
                    $adv_name = printSecuTags($adv_name);
                    $type = printSecuTags($type);
                    $style = printSecuTags($style);
                    
                    list ($pays, $ext) = explode ('.', $pays_adv);

                    if ($language == 'french'){
                        $date = $jour . '/' . $mois . '/' . $an;
                    } 
                    else{
                        $date = $mois . '/' . $jour . '/' . $an;
                    } 

                    if ($score_team > $score_adv){
                        $color = '#009900';
                    } 
                    else if ($score_team < $score_adv){
                        $color = '#990000';
                    } 
                    else{
                        $color = '#3333FF';
                    } 

                    if ($j == 0){
                        $bg = $bgcolor2;
                        $j++;
                    } 
                    else{
                        $bg = $bgcolor1;
                        $j = 0;
                    } 

                    $sql5 = mysql_query('SELECT name, icon FROM ' . GAMES_TABLE . ' WHERE id = \'' . $game . '\' ');
                    list($game_name, $icon) = mysql_fetch_array($sql5);
                    $game_name = printSecuTags($game_name);

                    if ($icon != '' && is_file($icon)){
                        $icone = $icon;
                    } 
                    else{
                        $icone = 'images/games/nk.gif';
                    }

                    echo '<tr style="background: '. $bg . '">
                            <td style="width: 5%;">&nbsp;<img src="' . $icone . '" alt="" title="' . $game_name . '" /></td>
                            <td style="width: 10%;">' . $date . '</td>
                            <td style="width: 30%;"><img src="images/flags/' . $pays_adv . '" alt="" title="' . $pays . '" />';
                            
                    if ($adv_url != ''){
                        echo '<a href="' . $adv_url . '" onclick="window.open(this.href); return false;">' . $adv_name . '</a>';
                    } 
                    else{
                        echo $adv_name;
                    } 

                    if (is_file('themes/' . $theme . '/images/report.png')){
                        $img = 'themes/' . $theme . '/images/report.png';
                    } 
                    else{
                        $img = 'modules/Wars/images/report.png';
                    } 

                    echo '</td><td style="width: 15%;text-align:center;">' . $type . '</td>
                    <td style="width: 15%;text-align:center;">' . $style . '</td>
                    <td style="background: ' . $color . ';width: 15%;text-align:center;"><span style="color: #FFFFFF;"><b>' . $score_team . '/' . $score_adv . '</b></span></td>
                    <td style="width: 10%;text-align:center;"><a href="index.php?file=Wars&amp;op=detail&amp;war_id=' . $war_id . '"><img style="border: 0;" src="' . $img . '" alt="" /></a></td></tr>';
                }
                
                echo '</table>';

                if ($count > $nb_wars){
                    $url_page = 'index.php?file=Wars&amp;tid=' . $_REQUEST['tid'] . '&amp;orderby=' . $_REQUEST['orderby'];
                    number($count, $nb_wars, $url_page);
                } 
            } 
        } 

        if ($nb_matchs > 0){
            if ($nb_matchs > 1) $war = _MATCHES; else $war = _MATCH;
            echo '<br /><div style="text-align: center;"><small><b>' . $nb_matchs . '</b> ' . $war . ' : <b><span style="color: #009900;">' . $nb_victory . '</span></b> ' . _WIN . ' - <b><span style="color: #990000;">' . $nb_defeat . '</span></b> ' . _LOST . ' - <b><span style="color: #3333FF;">' . $nb_nul . '</span></b> ' . _DRAW . '</small></div><br />';
        }

        if ($_REQUEST['p'] == 1 OR !isset($_REQUEST['p'])){
            $sqlx = mysql_query("SELECT warid FROM " . WARS_TABLE . " WHERE etat = 0");
            $nb_matchs2 = mysql_num_rows($sqlx);

            if ($nb_matchs2 > 0){
                echo '<br /><div style="text-align: center;"><big><b>' . _NEXTMATCHES . '</b></big></div><br />';
                
                echo '<table style="margin-left: auto;margin-right: auto;text-align: left;background: ' . $bgcolor2 . ';border: 1px solid ' . $bgcolor3 . ';" width="100%" cellpadding="2" cellspacing="1">
                        <tr style="background: ' . $bgcolor3 . '">
                        <td style="width: 5%;">&nbsp;</td>
                        <td style="width: 10%;"><b>' . _DATE . '</b></td>
                        <td style="width: 30%;text-align:center;"><b>' . _OPPONENT . '</b></td>
                        <td style="width: 20%;text-align:center;"><b>' . _TYPE . '</b></td>
                        <td style="width: 20%;text-align:center;"><b>' . _STYLE . '</b></td>
                        <td style="width: 15%;text-align:center;"><b>' . _DETAILS2 . '</b></td>';

                $sql4x = mysql_query('SELECT warid, adversaire, url_adv, pays_adv, type, style, game, date_jour, date_mois, date_an, tscore_team, tscore_adv FROM ' . WARS_TABLE . ' WHERE etat = 0 ' . $and . $order . ' LIMIT ' . $start . ',' . $nb_wars.' ');
                while (list($war_id, $adv_name, $adv_url, $pays_adv, $type, $style, $game, $jour, $mois, $an, $score_team, $score_adv) = mysql_fetch_array($sql4x)){
                    $adv_name = printSecuTags($adv_name);
                    $type = printSecuTags($type);
                    $style = printSecuTags($style);

                    list ($pays, $ext) = explode ('.', $pays_adv);

                    if ($language == 'french'){
                        $date = $jour . '/' . $mois . '/' . $an;
                    } 
                    else{
                        $date = $mois . '/' . $jour . '/' . $an;
                    } 

                    if ($score_team > $score_adv){
                        $color = '#009900';
                    } 
                    else if ($score_team < $score_adv){
                        $color = '#990000';
                    } 
                    else{
                        $color = '#3333FF';
                    } 

                    if ($j == 0){
                        $bg = $bgcolor2;
                        $j++;
                    } 
                    else{
                        $bg = $bgcolor1;
                        $j = 0;
                    } 

                    $sql5 = mysql_query('SELECT name, icon FROM ' . GAMES_TABLE . ' WHERE id = \'' . $game . '\' ');
                    list($game_name, $icon) = mysql_fetch_array($sql5);
                    $game_name = printSecuTags($game_name);

                    if ($icon != '' && is_file($icon)){
                        $icone = $icon;
                    } 
                    else{
                        $icone = 'images/games/nk.gif';
                    }

                    echo '<tr style="background: '. $bg . '">
                            <td style="width: 5%;">&nbsp;<img src="' . $icone . '" alt="" title="' . $game_name . '" /></td>
                            <td style="width: 10%;">' . $date . '</td>
                            <td style="width: 30%;"><img src="images/flags/' . $pays_adv . '" alt="" title="' . $pays . '" />';
                    
                    if ($adv_url != ''){
                        echo '<a href="' . $adv_url . '" onclick="window.open(this.href); return false;">' . $adv_name . '</a>';
                    } 
                    else{
                        echo $adv_name;
                    } 

                    if (is_file('themes/' . $theme . '/images/report.png')){
                        $img = 'themes/' . $theme . '/images/report.png';
                    } 
                    else{
                        $img = 'modules/Wars/images/report.png';
                    } 

                    echo '</td><td style="width: 20%;text-align:center;">' . $type . '</td>
                            <td style="width: 20%;text-align:center;">' . $style . '</td>
                            <td style="width: 15%;text-align:center;"><a href="index.php?file=Wars&amp;op=detail&amp;war_id=' . $war_id . '"><img style="border: 0;" src="' . $img . '" alt="" /></a></td>';
                } 
                echo '</table>';
            }
        }
        closetable();
    } 

    function detail($war_id){
        global $nuked, $user, $visiteur, $language, $bgcolor1, $bgcolor2, $bgcolor3;

        opentable();

        echo '<script type="text/javascript"><!--'."\n"
        . 'document.write(\'<link rel="stylesheet" type="text/css" href="media/shadowbox/shadowbox.css">\');'."\n"
        . '--></script>'."\n"
        . '<script type="text/javascript" src="media/shadowbox/shadowbox.js"></script>'."\n"
        . '<script type="text/javascript">'."\n"
        . 'Shadowbox.init();'."\n"
        . '</script>'."\n";

        $sql = mysql_query('SELECT team, adversaire, url_adv, pays_adv, date_jour, date_mois, date_an, type, style, tscore_team, tscore_adv, map, score_adv, score_team, report, auteur, url_league, etat FROM ' . WARS_TABLE . ' WHERE warid = \'' . $war_id . '\' ');
        if(mysql_num_rows($sql) <= 0){
            redirect('index.php?file=404', 0);
            exit();
        }
        
        list($team, $adv_name, $adv_url, $pays_adv, $jour, $mois, $an, $type, $style, $tscore_team, $tscore_adv, $map, $score_team, $score_adv, $report, $auteur, $url_league, $etat) = mysql_fetch_array($sql);
        list ($pays, $ext) = explode ('.', $pays_adv);       

        $adv_name = printSecuTags($adv_name);
        $type = printSecuTags($type);
        $style = printSecuTags($style);
        $score_adv = printSecuTags($score_adv);
        $score_team = printSecuTags($score_team);
        $map = explode('|', $map);;
        $score_team = explode('|', $score_team);;
        $score_adv = explode('|', $score_adv);;

        if ($language == 'french'){
            $date = $jour . '/' . $mois . '/' . $an;
        } 
        else{
            $date = $mois . '/' . $jour. '/' . $an;
        } 

        if ($team > 0){
            $sql_team = mysql_query('SELECT titre FROM ' . TEAM_TABLE . ' WHERE cid = \'' . $team . '\' ');
            list($team_name) = mysql_fetch_array($sql_team);
            $team_name = printSecuTags($team_name);
        } 
        else{
            $team_name = $nuked['name'];
        }

        if ($visiteur >= admin_mod('Wars')){
            ?>
            <script type="text/javascript">
            function delmatch(adversaire, id){
                if (confirm('<?php echo _DELETEMATCH; ?>'+adversaire+' ! <?php echo _CONFIRM; ?>')){
                    document.location.href = 'index.php?file=Wars&page=admin&op=del_war&war_id='+id;
                }
            }
            </script>
            <?php

            echo '<div style="text-align: right;"><a href="index.php?file=Wars&amp;page=admin&amp;op=match&amp;do=edit&amp;war_id=' . $war_id . '"><img style="border: 0;" src="images/edition.gif" alt="" title="' . _EDIT . '" /></a>
                    &nbsp;<a href="javascript:delmatch(\''. mysql_real_escape_string(stripslashes($adv_name)) . '\', \'' . $war_id . '\');"><img style="border: 0;" src="images/delete.gif" alt="" title="' . _DEL . '" /></a>&nbsp;</div>';
        } 

        echo '<br /><table style="margin-left: auto;margin-right: auto;text-align: left;background: ' . $bgcolor2 . ';" width="90%" border="0" cellpadding="3" cellspacing="3">
                <tr><td style="background: ' . $bgcolor2 . ';border: 1px solid ' . $bgcolor3 . ';text-align:center;" colspan="2"><big><b>' . $team_name . '</b> ' . _VS . ' <b>' . $adv_name . '</b></big></td></tr>
                <tr style="background: ' . $bgcolor1 . ';"><td style="border: 1px dashed ' . $bgcolor3 . ';"><b>' . _OPPONENT . '</b> :&nbsp;';

        if ($adv_url != ''){
            echo '<a href="' . $adv_url . '" onclick="window.open(this.href); return false;">' . $adv_name . '</a>';
        } 
        else{
            echo $adv_name;
        } 

        echo '&nbsp;<img src="images/flags/' . $pays_adv . '" alt="" title="' . $pays . '" /></td></tr>
                <tr style="background: ' . $bgcolor1 . ';"><td style="border: 1px dashed ' . $bgcolor3 . ';"><b>' . _DATE . '</b> : ' . $date . '</td></tr>
                <tr style="background: ' . $bgcolor1 . ';"><td style="border: 1px dashed ' . $bgcolor3 . ';"><b>' . _TYPE . '</b> : ' . $type . '</td></tr>
                <tr style="background: ' . $bgcolor1 . ';"><td style="border: 1px dashed ' . $bgcolor3 . ';"><b>' . _STYLE . '</b> : ' . $style . '</td></tr>
                <tr style="background: ' . $bgcolor1 . ';"><td style="border: 1px dashed ' . $bgcolor3 . ';"><b>' . _MAPS . '</b> :<br/><br/>';
        
        $size = count($map);
        
        for ($nbr=1; $nbr <= $size; $nbr++){
            echo '<br /><u>Map n&deg; ' . $nbr . ' :</u> ' . $map[$nbr-1];
            if ($etat != 0){
                echo '<br />' . _SCORE . ' : ';
                if ($score_team[$nbr-1] < $score_adv[$nbr-1]){
                    echo '&nbsp;<span style="color: #990000;"><b>' . $score_adv[$nbr-1] . '</b></span> - <span style="color: #009900;"><b>' . $score_team[$nbr-1] . '</b></span><br />';
                } 
                else if ($score_team[$nbr-1] > $score_adv[$nbr-1]){
                    echo '&nbsp;<span style="color: #009900;"><b>' . $score_adv[$nbr-1] . '</b></span> - <span style="color: #990000;"><b>' . $score_team[$nbr-1] . '</b></span><br />';
                } 
                else{
                    echo '&nbsp;<b>' . $score_team[$nbr-1] . ' - ' . $score_adv[$nbr-1] . '</b><br />';
                } 
            }
        }
        
        
        if($etat != 0){
            echo '</td></tr><tr style="background: ' . $bgcolor1 . ';"><td style="border: 1px dashed ' . $bgcolor3 . ';"><b>' . _RESULT . '</b> :';

            if ($tscore_team < $tscore_adv){
                echo '&nbsp;<span style="color: #990000;"><b>' . $tscore_team . '</b></span> - <span style="color: #009900;"><b>' . $tscore_adv . '</b></span></td></tr>';
            } 
            else if ($tscore_team > $tscore_adv){
                echo '&nbsp;<span style="color: #009900;"><b>' . $tscore_team . '</b></span> - <span style="color: #990000;"><b>' . $tscore_adv . '</b></span></td></tr>';
            } 
            else{
                echo '&nbsp;<b>' . $tscore_team . ' - ' . $tscore_adv . '</b></td></tr>';
            }
        }

        echo '<tr style="background: ' . $bgcolor2 . ';"><td>&nbsp;</td></tr>';        

        if ($report != ''){
            if ($etat == 0) $xtitle = _DETAILS2 . ' ' . _FROM; else $xtitle = _REPORTBY;
            
            echo '<tr><td style="background: ' . $bgcolor2 . ';border: 1px dashed ' . $bgcolor3 . ';"><b>' . $xtitle . ' <a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($auteur) . '">' . $auteur . '</a> : </b></td></tr>
                    <tr style="background: ' . $bgcolor1 . ';"><td style="border: 1px dashed ' . $bgcolor3 . ';">' . $report;
     
            if ($url_league != '' AND $etat != 0){
                echo '<br /><br /><a href="' . $url_league . '" onclick="window.open(this.href); return false;"><i>' . _OFFICIALREPORT . '</i></a>';
            } 

            echo '</td></tr><tr style="background: ' . $bgcolor2 . ';"><td>&nbsp;</td></tr>';
        }

        $sql_screen = mysql_query('SELECT url FROM ' . WARS_FILES_TABLE . ' WHERE module = \'Wars\' AND type = \'screen\' AND im_id = \'' . $war_id . '\' ');
        $nb_screen = mysql_num_rows($sql_screen);

        if ($nb_screen > 0){
            echo '<tr style="background: ' . $bgcolor1 . ';"><td style="border: 1px dashed ' . $bgcolor3 . ';">';

            while (list($url) = mysql_fetch_array($sql_screen)){
                echo '<a href="' . $url . '" rel="shadowbox"><img src="' . $url . '" alt="" style="max-width:150px;max-height:150px;margin:10px" /></a>';
            }
            
            echo '</td></tr><tr style="background: ' . $bgcolor2 . ';"><td>&nbsp;</td></tr>';
        } 

        $sql_demo = mysql_query('SELECT url FROM ' . WARS_FILES_TABLE . ' WHERE module = \'Wars\' AND type = \'demo\' AND im_id = \'' . $war_id . '\' ');
        $nb_demo = mysql_num_rows($sql_demo);

        if ($nb_demo > 0){
            $l = 1;
            echo '<tr style="background: ' . $bgcolor2 . ';"><td><table style="text-align:center;">';

            while (list($url) = mysql_fetch_array($sql_demo)){
                if ($nb_demo > 1){
                    $demos = $l . '/' . $nb_demo;
                } 
                else{
                    $demos = '';
                }
                
                $l++;
                echo '<tr><td><img src="modules/Wars/images/demo.png" alt="" /></td><td><a href="' . $url . '" onclick="window.open(this.href); return false;">' . _DOWNLOADDEMO . ' ' . $demos . '</a></td></tr>';
            }
            
            echo '</table></td></tr>';
        }
        
        echo '</table><br />';
        
        $sql = mysql_query('SELECT active FROM ' . $nuked['prefix'] . '_comment_mod WHERE module = \'wars\' ');
        list($active) = mysql_fetch_array($sql);
                
        if($active ==1 && $visiteur >= nivo_mod('Comment') && nivo_mod('Comment') > -1){
        echo '<table style="margin-left: auto;margin-right: auto;text-align: left;" width="80%" border="0" cellspacing="3" cellpadding="3"><tr style="background: ' . $bgcolor1 . ';"><td style="border: 1px dashed ' . $bgcolor3 . ';">';

            include ('modules/Comment/index.php');
            com_index('match', $war_id);

            echo '</td></tr></table>';
        }
        closetable();
    } 

    switch ($_REQUEST['op']){
        case 'index':
            index();
            break;

        case 'detail':
            detail($_REQUEST['war_id']);
            break;

        default:
            index();
            break;
    } 

} 
else if ($level_access == -1){
    opentable();
    echo '<br /><br /><div style="text-align: center;">' . _MODULEOFF . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a><br /><br /></div>';
    closetable();
} 
else if ($level_access == 1 && $visiteur == 0){
    opentable();
    echo '<br /><br /><div style="text-align: center;">' . _USERENTRANCE . '<br /><br /><b><a href="index.php?file=User&amp;op=login_screen">' . _LOGINUSER . '</a> | <a href="index.php?file=User&amp;op=reg_screen">' . _REGISTERUSER . '</a></b><br /><br /></div>';
    closetable();
} 
else{
    opentable();
    echo '<br /><br /><div style="text-align: center;">' . _NOENTRANCE . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a><br /><br /></div>';
    closetable();
} 
?>