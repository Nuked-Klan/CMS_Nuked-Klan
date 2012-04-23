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

global $nuked, $language, $bgcolor3, $theme;
translate('modules/Wars/lang/' . $language . '.lang.php');

$sql2 = mysql_query('SELECT active FROM ' . BLOCK_TABLE . ' WHERE bid = \'' . $bid . '\' ');
list($active) = mysql_fetch_array($sql2);
if ($active == 3 || $active == 4){
    echo '<table style="margin-left: auto;margin-right: auto;text-align: left;" width="90%">
			<tr><td style="width: 45%;vertical-align:top;"><a href="index.php?file=Wars"><b><big>' . _LATESTWAR . '</big></b></a>
			<br /><br /><table width="180" cellspacing="1" cellpadding="0">';

    $sql = mysql_query('SELECT warid, pays_adv, adversaire, tscore_team, tscore_adv FROM ' . WARS_TABLE . ' WHERE etat = 1 ORDER BY date_an DESC, date_mois DESC, date_jour DESC LIMIT 0, 10');
    $nbwar = mysql_num_rows($sql);

    while (list($war_id, $pays_adv, $adv_name, $score_team, $score_adv) = mysql_fetch_array($sql)){
        $adv_name = printSecuTags($adv_name);
        list ($pays, $ext) = explode ('.', $pays_adv);

        if ($score_team > $score_adv){
            $color = '#009900';
        }
        else if ($score_team < $score_adv){
            $color = '#990000';
        }
        else{
            $color = '#3333FF';
        }

        echo '<tr><td style="width: 60%;"><img src="images/flags/' . $pays_adv . '" alt="" title="' . $pays . '" />&nbsp;&nbsp;<a href="index.php?file=Wars&amp;op=detail&amp;war_id=' . $war_id . '"><b>' . $adv_name . '</b></a></td>
				<td style="width: 40%;background: $color;color: #FFFFFF;text-align:center;"><b>' . $score_team . '/' . $score_adv . '</b></td></tr>';
    }

	if (mysql_num_rows($sql) == NULL) echo '<tr><td colspan="2" style="text-align:center;">' . _NOMATCH . '</td></tr>';

    echo '</table></td><td style="width: 10%;">&nbsp;</td><td style="width: 45%;vertical-align:top;"><a href="index.php?file=Calendar"><b><big>' . _NEXTWAR . '</big></b></a>
			<br /><br /><table width="180" cellspacing="1" cellpadding="0">';

    $sql2 = mysql_query('SELECT warid, pays_adv, adversaire, date_jour, date_mois, date_an FROM ' . WARS_TABLE . ' WHERE etat = 0 ORDER BY date_an, date_mois, date_jour LIMIT 0, 10');
    $nbwar2 = mysql_num_rows($sql2);

    $d = date('d');
    $m = date('m');
    $y = date('Y');

    while (list($war_id2, $pays_adv2, $adv_name2, $d2, $m2, $y2) = mysql_fetch_array($sql2)){
		$adv_name2 = printSecuTags($adv_name2);
		list ($pays2, $ext2) = explode ('.', $pays_adv2);

		if ($m2 < 10){
		    $m2 = "0" . $m2;
		}

		if ($language == 'french'){
			$date = $d2 . '/' . $m2 . '/' . $y2;
		}
		else{
			$date = $m2 . '/' . $d2 . '/' . $y2;
		}

		echo '<tr><td style="width: 60%;"><img src="images/flags/' . $pays_adv2 . '" alt="" title="' . $pays2 . '" />&nbsp;&nbsp;<a href="index.php?file=Calendar&amp;m=' . $m2 . '&amp;y=' . $y2 . '"><b>' . $adv_name2 . '</b></a></td>
				<td style="width: 40%;text-align:center;">' . $date . '</td></tr>';
    }

	if (mysql_num_rows($sql2) == NULL) echo '<tr><td colspan="2" style="text-align:center;">' . _NOMATCH . '</td></tr>';

    echo '</table></td></tr><tr><td style="width: 45%;text-align:right;"><a href="index.php?file=Wars"><small>+ ' . _GOWARS . '</small></a></td>
			<td style="width: 10%;"></td><td style="width: 45%;text-align:right;"><a href="index.php?file=Calendar"><small>+ ' . _GOCALENDAR . '</small></a></td></tr></table><br />';

}
else{

    echo '<table width="100%" border="0" cellspacing="1" cellpadding="0">
			<tr><td colspan="2"><span style="text-decoration: underline"><b>'._LATESTWAR.' :</b></span></td></tr><tr><td colspan="2">&nbsp;</td></tr>';

    $sql = mysql_query('SELECT warid, pays_adv, adversaire, tscore_team, tscore_adv FROM ' . WARS_TABLE . ' WHERE etat = 1 ORDER BY date_an DESC, date_mois DESC, date_jour DESC LIMIT 0, 5');
    while (list($war_id, $pays_adv, $adv_name, $score_team, $score_adv) = mysql_fetch_array($sql)){
        $adv_name = printSecuTags($adv_name);
        list ($pays, $ext) = explode ('.', $pays_adv);

        if ($score_team > $score_adv){
            $color = '#009900';
        }
        else if ($score_team < $score_adv){
            $color = '#990000';
        }
        else{
            $color = '#3333FF';
        }

        echo '<tr><td style="width: 60%"><img src="images/flags/' . $pays_adv . '" alt="" title="' . $pays. '" />&nbsp;&nbsp;<a href="index.php?file=Wars&amp;op=detail&amp;war_id=' . $war_id . '"><b>' . $adv_name . '</b></a></td>
				<td style="width: 100px;background: ' . $color . ';text-align:center;"><b>' . $score_team . '/' . $score_adv . '</b></td></tr>';
    }

	if (mysql_num_rows($sql) == NULL) echo '<tr><td colspan="2" style="text-align:center;"><em>' . _NOMATCH . '</em></td></tr>';

    $sql2 = mysql_query('SELECT warid, pays_adv, adversaire, date_jour, date_mois, date_an FROM ' . WARS_TABLE . ' WHERE etat = 0 ORDER BY date_an, date_mois, date_jour LIMIT 0, 5');
    $do_affich_bl = mysql_num_rows($sql2);

    if ($do_affich_bl > 0){
		$d = date('d');
		$m = date('m');
		$y = date('Y');

		echo '<tr><td colspan="2">&nbsp;</td></tr><tr><td colspan="2"><span style="text-decoration: underline"><b>'._NEXTWAR.' :</b></span></td></tr><tr><td colspan="2">&nbsp;</td></tr>';

        while (list($war_id2, $pays_adv2, $adv_name2, $d2, $m2, $y2) = mysql_fetch_array($sql2)){
            $adv_name2 = printSecuTags($adv_name2);
            list ($pays2, $ext2) = explode ('.', $pays_adv2);

            if ($m2 < 10){
                $m2 = '0' .$m2;
            }

            if ($language == 'french'){
                $date = $d2 . '/' .$m2;
            }
            else{
                $date = $m2. '/' .$d2;
            }

            echo '<tr><td style="width: 60%"><img src="images/flags/' . $pays_adv2 . '" alt="" title="' . $pays2 . '" />
					&nbsp;&nbsp;<a href="index.php?file=Calendar&amp;m=' . $m2 . '&amp;y=' . $y2 . '"><b>' . $adv_name2 . '</b></a></td>
					<td style="width: 40%text-align:center;">' . $date . '</td></tr>';
        }
    }
	
    echo '</table>';
}
?>