<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined("INDEX_CHECK") or die ('You can\'t run this file alone.');

global $nuked, $language;
translate('modules/Calendar/lang/' . $language . '.lang.php');

function affich_block_event($blok){
    global $nuked, $bgcolor1, $bgcolor2, $bgcolor3, $file;

    define ('ADAY', (61 * 60 * 24));
    $datearray = getdate();

    if (empty($_REQUEST['mo']) && empty($_REQUEST['ye'])){
        $month = $datearray['mon'];
        $year = $datearray['year'];
        $nextmonth = $month + 1;
        $prevmonth = $month-1;

        if ($nextmonth > 12){
            $nextmonth = 1;
            $nextyear = $year + 1;
        }
        else $nextyear = $year;

        if ($prevmonth < 1){
            $prevmonth = 12;
            $prevyear = $year-1;
        }
        else $prevyear = $year;

    }
    else{
        $month = $_REQUEST['mo'];
        $year = $_REQUEST['ye'];
        $nextmonth = $_REQUEST['mo'] + 1;
        $prevmonth = $_REQUEST['mo']-1;
		
        if ($nextmonth > 12){
            $nextmonth = 1;
            $nextyear = $year + 1;
        }
        else $nextyear = $year;

        if ($prevmonth < 1){
            $prevmonth = 12;
            $prevyear = $year - 1;
        }
        else $prevyear = $year;

    }

    $start = mktime(0, 0, 0, $month, 1, $year);
    $firstdayarray = getdate($start);

    $months = Array(_JAN, _FEB, _MAR, _APR, _MAY, _JUN, _JUL, _AUG, _SEP, _OCT, _NOV, _DEC);
    $this_month = $month - 1;
    $days = Array(_SUN, _MON, _TUE, _WEN, _THR, _FRI, _SAT);

    $blok['content'] .= '<table style="margin:0 auto;text-align:left" cellpadding="0" cellspacing="0"><tr><td>'."\n"
					 . '<a href="index.php?file='.$file.'&amp;mo=' . $prevmonth . '&amp;ye='.$prevyear.'" title="'._PREVMONTH.'"><small>&lt;&lt;</small></a>&nbsp;<b>'.$months[$this_month].'&nbsp;'.$year.'</b>&nbsp;'."\n"
					 . '<a href="index.php?file='.$file.'&amp;mo='.$nextmonth.'&amp;ye='.$nextyear.'" title="'._NEXTMONTH.'"><small>&gt;&gt;</small></a></td></tr></table>'."\n"
					 . '<table style="margin:0 auto;text-align:left" cellpadding="2" cellspacing="1"><tr>'."\n";

	$size = count($days);
	for($i=0; $i<$size; $i++){
		$blok['content'] .= '<td style="text-align:center"><b>' . $days[$i] . '</b></td>';
	}

    for($count = 0;$count < (6 * 7);$count++){
        $dayarray = getdate($start);

        if ((($count) % 7) == 0){
            $blok['content'] .= '</tr><tr>';
        }

        if ($count < $firstdayarray['wday'] || $dayarray['mon'] != $month){
            $blok['content'] .= '<td>&nbsp;</td>';
        }
        else{
            if ($dayarray['mday'] == $datearray['mday'] && $dayarray['mon'] == $datearray['mon']){
                $bd = '<b>';
                $bf = '</b>';
            }
            else{
                $bd = '';
                $bf = '';
            }

            $event_date = $dayarray['mday'];
            $txt = '';
            $heure2 = '';

            $sql1 = mysql_query('SELECT titre, date_jour, date_mois, date_an, heure, auteur FROM ' . CALENDAR_TABLE . ' WHERE date_an = \'' . $year . '\' AND date_mois = \'' . $month . '\' AND date_jour = \'' . $event_date . '\' ORDER BY heure');
            $nb_event = mysql_num_rows($sql1);

            if (defined("WARS_TABLE")){
                $sql2 = mysql_query('SELECT * FROM ' . WARS_TABLE . ' WHERE date_an = \'' . $year . '\' AND date_mois = \'' . $month . '\' AND date_jour = \'' . $event_date . '\' ');
                $nb_match = mysql_num_rows($sql2);
            }
            else{
                $nb_match = 0;
            }

            $nb_birthday = 0;
            if ($nuked['birthday'] != 'off'){
                $sql3 = mysql_query('SELECT user_id, age FROM ' . USER_DETAIL_TABLE);
                while (list($tuid, $tage) = mysql_fetch_array($sql3)){
                    list ($tjour, $tmois, $tan) = explode ('/', $tage);

                    if ($nuked['birthday'] == 'team'){
                        $and = 'AND team > 0';
                    }
                    else if ($nuked['birthday'] == 'admin'){
                        $and = 'AND niveau > 1';
                    }
                    else{
                        $and = '';
                    }

                    $sql_test = mysql_query('SELECT pseudo FROM ' . USER_TABLE . ' WHERE id = \'' . $tuid . '\' '. $and);
                    $test = mysql_num_rows($sql_test);

                    if ($tmois == $month && $tjour == $event_date && $test > 0){
                        $nb_birthday++;
                    }
                }
            }

            if ($nb_match > 0 || $nb_event > 0 || $nb_birthday > 0){
                while (list($titre1, $jour1, $mois1, $an1, $heure1, $auteur1) = mysql_fetch_array($sql1)){
                        $titre1 = printSecuTags($titre1);

                    if (defined("WARS_TABLE")){
                        $sql = mysql_query('SELECT etat, adversaire, type, date_jour, date_mois, date_an, heure, style, tscore_team, tscore_adv FROM ' . WARS_TABLE . ' WHERE date_an = \'' . $year . '\' AND date_mois = \''. $month . '\' AND date_jour = \'' . $event_date . '\' AND heure >= \'' . $heure2 . '\' AND heure < \'' . $heure1 . '\' ORDER BY heure');
                        while (list($etat, $adv_name, $type_match, $jour, $mois, $an, $heure, $style, $score_team, $score_adv) = mysql_fetch_array($sql)){
                            if ($etat == 1){
                                if ($score_team < $score_adv){
                                    $scores = _RESULT . ' : <span style="color: #900"><b>' . $score_team . ' - ' . $score_adv . '</b></span>';
                                }
                                else if ($score_team > $score_adv){
                                    $scores = _RESULT . ' : <span style="color: #090"><b>' . $score_team . ' - ' . $score_adv . '</b></span>';
                                }
                                else{
                                    $scores = _RESULT . ' : <span style="color: #009"><b>' . $score_team . ' - ' . $score_adv . '</b></span>';
                                }
                            }
                            else{
                                $scores = "";
                            }

                            if ($heure) $txt .= '<b>' . $heure . '</b><br />';
                            $txt .= _MATCH . '&nbsp:' . $type_match;
                            if ($adv_name) $txt .= _VS . '&nbsp;' . $adv_name;
                            if ($scores)$txt .= '<br />' . $scores;
                            $txt .= '<br />';
                        }
                    }

                    if ($heure1) $txt .= '<b>' . $heure1 . '</b><br />';
                    $txt .= $titre1;
                    $txt .= '<br />';

                    $heure2 = $heure1;
                }

                if (defined("WARS_TABLE")){
                    $sql = mysql_query('SELECT etat, adversaire, type, date_jour, date_mois, date_an, heure, style, tscore_team, tscore_adv FROM ' . WARS_TABLE . ' WHERE date_an = \'' . $year . '\' AND date_mois = \'' . $month . '\' AND date_jour = \'' . $event_date . '\' AND heure >= \'' . $heure2 . '\' ORDER BY heure');
                    while (list($etat, $adv_name, $type_match, $jour, $mois, $an, $heure, $style, $score_team, $score_adv) = mysql_fetch_array($sql)){
                        if ($etat == 1 && $score_team != "" && $score_adv != ""){
                            if ($score_team < $score_adv){
                                $scores = _RESULT . ' : <span style="color: #900;"><b>' . $score_team . ' - ' . $score_adv . '</b></span>';
                            }
                            else if ($score_team > $score_adv){
                                $scores = _RESULT . ' : <span style="color: #090;"><b>' . $score_team . ' - ' . $score_adv . '</b></span>';
                            }
                            else{
                                $scores = _RESULT . " : <span style='color: #000099;'><b>" . $score_team . "&nbsp;-&nbsp;" . $score_adv . "</b></span>";
                            }
                        }
                        else{
                            $scores = '';
                        }

                        if ($heure) $txt .= '<b>' . $heure . '</b><br />';
                        $txt .= _MATCH . '&nbsp;' . $type_match;
                        if ($adv_name) $txt .= '&nbsp;' . _VS . '&nbsp;' . $adv_name;
                        if ($scores)$txt .= '<br />' . $scores;
                        $txt .= '<br /><br />';
                    }
                }

                if ($nb_birthday > 0){
                    $sql4 = mysql_query('SELECT user_id, prenom, age FROM ' . USER_DETAIL_TABLE);
                    while (list($id_user, $prenom, $birthday) = mysql_fetch_array($sql4)){

                        if ($birthday != ""){
                            list ($ajour, $amois, $aan) = explode ('/', $birthday);

                            if ($amois == $month && $ajour == $event_date){
                                $age = $year - $aan;
								
                                if ($month < $amois){
                                    $age = $age - 1;
                                }
								
                                if ($event_date < $ajour && $month == $amois){
                                    $age = $age-1;
                                }
								
                                $sql5 = mysql_query('SELECT pseudo FROM ' . USER_TABLE . ' WHERE id = \'' . $id_user . '\' ' . $and);
                                list($pseudo) = mysql_fetch_array($sql5);

                                if ($prenom != ""){
                                    $nom = $prenom;
                                }
                                else{
									$nom = $pseudo;
                                }

                                $txt .= '<b>' . _BIRTHDAY . ' : ' . $pseudo . '</b><br />' . _BIRTHDAYTEXT . '&nbsp;<b>' . $nom . '</b>&nbsp;' . _BIRTHDAYTEXTSUITE . '&nbsp;<b>' . $age . '</b>&nbsp;' . _YEARSOLD . '<br /><br />';
                            }
                        }
                    }
                }

                $blok['content'] .= '<td style="background: ' . $bgcolor1 . ';border: 1px solid ' . $bgcolor3 . ';text-align:center;" onmouseover="AffBulle(\'&nbsp;&nbsp;&nbsp;&nbsp;' . $event_date . '&nbsp;' . $months[$this_month] . '&nbsp;' . $year . '\', \'' . htmlentities(mysql_real_escape_string($txt), ENT_NOQUOTES) . '\', 200)" onmouseout="HideBulle()">'."\n"
                . '<a href="index.php?file=Calendar&amp;m=' . $month . '&amp;y=' . $year . '">'. $bd . $dayarray['mday'] . $bf . '</a></td>'."\n";
            }
            else{
                $blok['content'] .= '<td align="center"><span style="text-align: center;">' . $bd . $dayarray[mday] . $bf . '</span></td>'."\n";
            }

            $start += ADAY;
        }
    }

    $blok['content'] .= '</tr></table>'."\n";

    return $blok;
}

function edit_block_event($bid){
    global $nuked, $language;

    $sql = mysql_query('SELECT active, position, titre, module, content, type, nivo, page FROM ' . BLOCK_TABLE . ' WHERE bid = \'' . $bid . '\' ');
    list($active, $position, $titre, $modul, $content, $type, $nivo, $pages) = mysql_fetch_array($sql);
    $titre = printSecuTags($titre);

    if ($active == 1) $checked1 = 'selected="selected"';
    else if ($active == 2) $checked2 = 'selected="selected"';
    else $checked0 = 'selected="selected"';

    echo '<div class="content-box">'."\n" //<!-- Start Content Box -->
	   . '<div class="content-box-header"><h3>'._BLOCKADMIN.'</h3>'."\n"
	   . '<a href="help/'.$language.'/block.html" rel="modal">'."\n"
	   . '<img style="border: 0;" src="help/help.gif" alt="" title="'._HELP.'" /></a>'."\n"
	   . '</div>'."\n"
	   . '<div class="tab-content" id="tab2"><form method="post" action="index.php?file=Admin&amp;page=block&amp;op=modif_block">'."\n"
	   . '<table style="margin:0 auto;text-align: left;" cellspacing="0" cellpadding="2" border="0">'."\n"
	   . '<tr><td><b>'._TITLE.'</b></td><td><b>'._BLOCK.'</b></td><td><b>'._POSITION.'</b></td><td><b>'._LEVEL.'</b></td></tr>'."\n"
	   . '<tr><td style="text-align:center;" ><input type="text" name="titre" size="40" value="'.$titre.'" /></td>'."\n"
	   . '<td style="text-align:center;"><select name="active">'."\n"
	   . '<option value="1" '.$checked1.'>'._LEFT.'</option>'."\n"
	   . '<option value="2" '.$checked2.'>'._RIGHT.'</option>'."\n"
	   . '<option value="0" '.$checked0.'>'._OFF.'</option></select></td>'."\n"
	   . '<td style="text-align:center;" ><input type="text" name="position" size="2" value="'.$position.'" /></td>'."\n"
	   . '<td style="text-align:center;" ><select name="nivo"><option>'.$nivo.'</option>'."\n"
	   . '<option>0</option>'."\n"
	   . '<option>1</option>'."\n"
	   . '<option>2</option>'."\n"
	   . '<option>3</option>'."\n"
	   . '<option>4</option>'."\n"
	   . '<option>5</option>'."\n"
	   . '<option>6</option>'."\n"
	   . '<option>7</option>'."\n"
	   . '<option>8</option>'."\n"
	   . '<option>9</option></select></td></tr><tr><td colspan="4">&nbsp;</td></tr><tr><td colspan="4" style="text-align:center;" ><b>'._PAGESELECT.' :</b></td></tr><tr><td colspan="4">&nbsp;</td></tr>'."\n"
	   . '<tr><td colspan="4" align="center"><select name="pages[]" size="8" multiple="multiple">'."\n";

    select_mod2($pages);

    echo '</select></td></tr><tr><td colspan="4" style="text-align:center;" ><br />'."\n"
	   . '<input type="hidden" name="type" value="'.$type.'" />'."\n"
	   . '<input type="hidden" name="bid" value="'.$bid.'" />'."\n"
	   . '<input type="submit" name="send" value="'._MODIFBLOCK.'" />'."\n"
	   . '</td></tr></table>'
	   . '<div style="text-align: center;"><br />[ <a href="index.php?file=Admin&amp;page=block"><b>'._BACK.'</b></a> ]</div></form><br /></div></div>'."\n";

}
?>
