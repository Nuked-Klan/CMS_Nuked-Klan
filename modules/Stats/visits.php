<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die;

global $nuked, $user, $language, $bgcolor3, $bgcolor2, $bgcolor1;
translate('modules/Stats/lang/' . $language . '.lang.php');

$visiteur = ($user) ? $user[1] : 0;

if ($visiteur >= $nuked['level_analys'] && $nuked['level_analys']!= -1) {
    if ($_REQUEST['op'] == 'view_all') {
        view_all();
    }
    else if ($_REQUEST['op'] == 'view_referer') {
        view_referer();
    }
    else if ($_REQUEST['op'] == 'view_host') {
        view_host();
    }
    else {
        opentable();

        $time = time();
        $month = strftime('%m', $time);
        $year = strftime('%Y', $time);
        $day = strftime('%d', $time);

        echo '<br /><div style="text-align: center"><h2><b>' . _ANALYS . '</b></h2>'."\n"
        . '[ <a href="index.php?file=Stats&amp;page=visits&amp;oday=' . $day . '&amp;omonth=' . $month . '&amp;oyear=' . $year . '">' . _ODAY . '</a> | '
        . '<a href="index.php?file=Stats&amp;page=visits&amp;omonth=' . $month . '&amp;oyear=' . $year . '">' . _OMONTH . '</a> | '
        . '<a href="index.php?file=Stats&amp;page=visits&amp;oyear=' . $year . '">' . _OYEAR . '</a> | '
        . '<a href="index.php?file=Stats&amp;page=visits">' . _ALL . '</a> ]</div><br />'."\n";

        if (!empty($_REQUEST['oday']) && !empty($_REQUEST['omonth']) && !empty($_REQUEST['oyear'])) {
            $where = "WHERE day = '" . $_REQUEST['oday'] . "' AND month = '" . $_REQUEST['omonth'] . "' AND year = '" . $_REQUEST['oyear'] . "'";
            $where2 = $where . "AND referer NOT LIKE '" . $nuked['url'] . "%' AND referer != ''";

            $otext = _VISITORS . '&nbsp;';
            $otext .= ($_REQUEST['oday'] == $day) ? _TODAY : _ON . '&nbsp;' . $_REQUEST['oday'] . '/' . $_REQUEST['omonth'] . '/' . $_REQUEST['oyear'];
        }
        else if (!empty($_REQUEST['omonth']) && !empty($_REQUEST['oyear'])) {
            $where = "WHERE month = '" . $_REQUEST['omonth'] . "' AND year = '" . $_REQUEST['oyear'] . "'";
            $where2 = $where . "AND referer NOT LIKE '" . $nuked['url'] . "%' AND referer != ''";

            $otext = _VISITORS . '&nbsp;';
            $otext .= ($_REQUEST['omonth'] == $month) ? _THISMONTH : _ON . '&nbsp;' . $_REQUEST['omonth'] . '/' . $_REQUEST['oyear'];
        }
        else if (!empty($_REQUEST['oyear'])) {
            $where = "WHERE year='" . $_REQUEST['oyear'] . "'";
            $where2 = $where . "AND referer NOT LIKE '" . $nuked['url'] . "%' AND referer != ''";
            $otext = _VISITORS . '&nbsp;';
            $otext .= ($_REQUEST['oyear'] == $year) ? _THISYEAR : _EN . '&nbsp;' . $_REQUEST['oyear'];
        }
        else {
            $where = '';
            $where2 = "WHERE referer NOT LIKE '" . $nuked['url'] . "%' AND referer != ''";
            $odate = nkDate($nuked['date_install']);
            $otext = _VISITORS . '&nbsp;' . _SINCE . ' ' . $odate;
        }

        $sql = mysql_query("SELECT id FROM " . STATS_VISITOR_TABLE . " " . $where);
        $visites = mysql_num_rows($sql);

        $sql1 = mysql_query("SELECT id FROM " . STATS_VISITOR_TABLE . " " . $where2);
        $visites2 = mysql_num_rows($sql1);

        if ($visites > 0) {
            echo '<div style="text-align: center">' . _WERECEICED . ' <b>' . $visites . '</b> ' . $otext . '</div><br />'."\n";

            if (!empty($_REQUEST['oday']) && !empty($_REQUEST['omonth']) && !empty($_REQUEST['oyear'])) {
                echo '<h3 style="text-align: center; margin-top: 20px">' . _LASTVISITORS . '</h3>'."\n"
                . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . '" width="80%" cellpadding="2" cellspacing="1">'."\n"
                . '<tr style="background: ' . $bgcolor3 . '">'."\n"
                . '<td style="width: 5%" align="center"><b>#</b></td>'."\n"
                . '<td align="center"><b>' . _VPSEUDO . '</b></td>'."\n"
                . '<td align="center"><b>' . _VIP . '</b></td>'."\n"
                . '<td align="center"><b>' . _VBROWSER . '</b></td>'."\n"
                . '<td align="center"><b>' . _VOS . '</b></td>'."\n"
                . '<td align="center"><b>' . _HOURS . '</b></td></tr>'."\n";

                $imembers = 0;
                $sql_last = mysql_query("SELECT ip, user_id, browser, os, date FROM " . STATS_VISITOR_TABLE . " " . $where . " ORDER BY date DESC LIMIT 0, 10");
                while (list($v_ip, $v_user_id, $v_browser, $v_os, $v_date) = mysql_fetch_array($sql_last)) {
                    $imembers++;

                    $v_hours = strftime('%H:%M', $v_date);
                    $v_osname = ($v_os == 'Autres') ? _OTHERS : $v_os;
                    $v_browsername = ($v_browser == 'Autres') ? _OTHERS : ($v_browser == 'Moteurs de recherche') ? _SEARCHENGINE : $v_browser;

                    if ($j == 0) {
                        $bg = $bgcolor2;
                        $j++;
                    }
                    else {
                        $bg = $bgcolor1;
                        $j = 0;
                    }

                    if (!empty($v_user_id)) {
                        $sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE  id = '" . $v_user_id . "'");
                        list($pseudo) = mysql_fetch_array($sql_member);
                        $v_pseudo = '<a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($pseudo) . '">' . $pseudo . '</a>';
                    }
                    else {
                        $v_pseudo = _VISITOR;
                    }

                    echo '<tr style="background: ' . $bg . '">'."\n"
                    . '<td style="width: 5%" align="center">' . $imembers . '</td>'."\n"
                    . '<td align="center">' . $v_pseudo . '</td>'."\n"
                    . '<td align="center">' . $v_ip . '</td>'."\n"
                    . '<td align="center">' . $v_browsername . '</td>'."\n"
                    . '<td align="center">' . $v_osname . '</td>'."\n"
                    . '<td align="center">' . $v_hours . '</td></tr>'."\n";
                }

                echo '</table>'."\n";
                if ($imembers >= 10) {
                    echo '<table style="margin: auto" width="80%" cellpadding="0" cellspacing="0"><tr>'."\n"
                    . '<td align="right"><a href="#" onclick="javascript:window.open(\'index.php?file=Stats&amp;nuked_nude=visits&amp;op=view_all&amp;oday=' . $_REQUEST['oday'] . '&amp;omonth=' . $_REQUEST['omonth'] . '&amp;oyear=' . $_REQUEST['oyear'] . '\',\'visitors\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=1000,height=700,top=30,left=0\')">' . _VIEWALL . '</a></td></tr></table>'."\n";
                }
            }
            // End last visitors

            echo '<br /><h3 style="text-align: center; margin-bottom: 10px">' . _BROWSER . '</h3>'."\n"
            . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . '" width="80%" cellpadding="2" cellspacing="1">'."\n"
            . '<tr style="background: ' . $bgcolor3 . '">'."\n"
            . '<td style="width: 5%" align="center"><b>#</b></td>'."\n"
            . '<td style="width: 25%" align="center"><b>' . _NOM . '</b></td>'."\n"
            . '<td style="width: 20%" align="center"><b>' . _VISITCOUNT . '</b></td>'."\n"
            . '<td style="width: 50%; font-weight: bold; text-align: center">'."\n"
            . '<div style="width: 10%; display: inline-block">0%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">25%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">50%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">75%</div>'."\n"
            . '<div style="width: 10%; display: inline-block">100%</div>'."\n"
            . '</td></tr>'."\n";

            $inav = 0;
            $sql2 = mysql_query("SELECT browser, COUNT(*) AS total FROM " . STATS_VISITOR_TABLE . " " . $where . " GROUP BY browser ORDER BY total DESC");
            while (list($browser) = mysql_fetch_array($sql2)) {
                $inav++;

                $and = empty($where) ? "WHERE browser = '" . $browser . "'" : $where . " AND browser = '" . $browser . "'";

                $browsername = ($browser == 'Autres') ? _OTHERS : ($browser == 'Moteurs de recherche') ? _SEARCHENGINE : $browser;

                $sql3 = mysql_query("SELECT id FROM " . STATS_VISITOR_TABLE . " " . $and);
                $bcount = mysql_num_rows($sql3);

                $etat = round(($bcount * 100) / $visites);

                if ($j0 == 0) {
                    $bg0 = $bgcolor2;
                    $j0++;
                }
                else {
                    $bg0 = $bgcolor1;
                    $j0 = 0;
                }

                echo '<tr style="background: ' . $bg0 . '">'."\n"
                . '<td style="width: 5%" align="center">' . $inav . '</td>'."\n"
                . '<td style="width: 25%">' . $browsername . '</td>'."\n"
                . '<td style="width: 20%" align="center">' . $bcount . ' (' . $etat . '%)</td>'."\n"
                . '<td style="width: 50%" align="left">'."\n";

                show_etat($etat);

                echo '</td></tr>'."\n";
            }
            // End browsers

            echo '</table><br /><h3 style="text-align: center; margin-bottom: 10px">' . _SYSTEMOS . '</h3>'."\n"
            . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . '" width="80%" cellpadding="2" cellspacing="1">'."\n"
            . '<tr style="background: ' . $bgcolor3 . '">'."\n"
            . '<td style="width: 5%" align="center"><b>#</b></td>'."\n"
            . '<td style="width: 25%" align="center"><b>' . _NOM . '</b></td>'."\n"
            . '<td style="width: 20%" align="center"><b>' . _VISITCOUNT . '</b></td>'."\n"
            . '<td style="width: 50%; font-weight: bold; text-align: center">'."\n"
            . '<div style="width: 10%; display: inline-block">0%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">25%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">50%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">75%</div>'."\n"
            . '<div style="width: 10%; display: inline-block">100%</div>'."\n"
            . '</td></tr>'."\n";

            $ios = 0;
            $sql4 = mysql_query("SELECT os, COUNT(*) AS total FROM " . STATS_VISITOR_TABLE . " " . $where . " GROUP BY os ORDER BY total DESC");
            while (list($os) = mysql_fetch_array($sql4)) {
                $ios++;

                $and1 = (empty($where)) ? "WHERE os = '" . $os . "'" : $where . " AND os = '" . $os . "'";

                $sql5 = mysql_query("SELECT id FROM " . STATS_VISITOR_TABLE . " " . $and1);
                $oscount = mysql_num_rows($sql5);

                $etat1 = round(($oscount * 100) / $visites);

                $osname = ($os == 'Autres') ? _OTHERS : $os;

                if ($j1 == 0) {
                    $bg1 = $bgcolor2;
                    $j1++;
                }
                else {
                    $bg1 = $bgcolor1;
                    $j1 = 0;
                }

                echo '<tr style="background: ' . $bg1 . '">'."\n"
                . '<td style="width: 5%" align="center">' . $ios . '</td>'."\n"
                . '<td style="width: 25%">' . $osname . '</td>'."\n"
                . '<td style="width: 20%" align="center">' . $oscount . ' (' . $etat1 . '%)</td>'."\n"
                . '<td style="width: 50%" align="left">'."\n";

                show_etat($etat1);

                echo '</td></tr>'."\n";
            }
            // End OS

            echo '</table><br /><h3 style="text-align: center; margin-bottom: 10px">' . _HOST . '</h3>'."\n"
            . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . '" width="80%" cellpadding="2" cellspacing="1">'."\n"
            . '<tr style="background: ' . $bgcolor3 . '">'."\n"
            . '<td style="width: 5%" align="center"><b>#</b></td>'."\n"
            . '<td style="width: 25%" align="center"><b>' . _NOM . '</b></td>'."\n"
            . '<td style="width: 20%" align="center"><b>' . _VISITCOUNT . '</b></td>'."\n"
            . '<td style="width: 50%; font-weight: bold; text-align: center">'."\n"
            . '<div style="width: 10%; display: inline-block">0%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">25%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">50%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">75%</div>'."\n"
            . '<div style="width: 10%; display: inline-block">100%</div>'."\n"
            . '</td></tr>'."\n";

            $ihost = 0;
            $sql6 = mysql_query("SELECT host, COUNT(*) AS total FROM " . STATS_VISITOR_TABLE . " " . $where . " GROUP BY host ORDER BY total DESC LIMIT 0, 10");
            while (list($host) = mysql_fetch_array($sql6)) {
                $ihost++;

                $and2 = empty($where) ? "WHERE host = '" . $host . "'" : $where . " AND host = '" . $host . "'";

                $sql7 = mysql_query("SELECT id FROM " . STATS_VISITOR_TABLE . " " . $and2);
                $hostcount = mysql_num_rows($sql7);

                $etat2 = round(($hostcount * 100) / $visites);

                if ($j2 == 0) {
                    $bg2 = $bgcolor2;
                    $j2++;
                }
                else {
                    $bg2 = $bgcolor1;
                    $j2 = 0;
                }
                $hostname = (strlen($host) > 15) ? substr($host, 0, 15) . '...' : $host;
                $host = empty($host) ? _UNKNOWN : '<a href="' . $host . '" onclick="window.open(this.href); return false;">' . htmlentities($hostname) . '</a>';

                echo '<tr style="background: ' . $bg2 . '">'."\n"
                . '<td style="width: 5%" align="center">' . $ihost . '</td>'."\n"
                . '<td style="width: 25%">' . $host . '</td>'."\n"
                . '<td style="width: 20%" align="center">' . $hostcount . ' (' . $etat2 . '%)</td>'."\n"
                . '<td style="width: 50%" align="left">'."\n";

                show_etat($etat2);

                echo '</td></tr>'."\n";
            }
            // End host

            echo '</table>'."\n";

            if ($ihost == 10) {
                echo '<table style="margin: auto" width="80%" cellpadding="2" cellspacing="1"><tr><td align="right">'."\n"
                . '<a href="#" onclick="javascript:window.open(\'index.php?file=Stats&amp;nuked_nude=visits&amp;op=view_host&amp;oday=' . $_REQUEST['oday'] . '&amp;omonth=' . $_REQUEST['omonth'] . '&amp;oyear=' . $_REQUEST['oyear'] . '\',\'Host\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600,top=30,left=0\')">' . _VIEWALL . '</a></td></tr></table>'."\n";
            }


            echo '<br /><h3 style="text-align: center; margin-bottom: 10px">' . _REFERER . ' (' . $visites2 . ')</h3>'."\n"
            . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . '" width="80%" cellpadding="2" cellspacing="1">'."\n"
            . '<tr style="background: ' . $bgcolor3 . '">'."\n"
            . '<td style="width: 5%" align="center"><b>#</b></td>'."\n"
            . '<td style="width: 25%" align="center"><b>' . _NOM . '</b></td>'."\n"
            . '<td style="width: 20%" align="center"><b>' . _VISITCOUNT . '</b></td>'."\n"
            . '<td style="width: 50%; font-weight: bold; text-align: center">'."\n"
            . '<div style="width: 10%; display: inline-block">0%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">25%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">50%</div>'."\n"
            . '<div style="width: 20%; display: inline-block">75%</div>'."\n"
            . '<div style="width: 10%; display: inline-block">100%</div>'."\n"
            . '</td></tr>'."\n";

            $iref = 0;
            $sql8 = mysql_query("SELECT referer, COUNT(*) AS total FROM " . STATS_VISITOR_TABLE . " " . $where2 . " GROUP BY referer ORDER BY total DESC LIMIT 0, 10");
            while (list($referer) = mysql_fetch_array($sql8)) {
                $iref++;

                $and3 = empty($where) ? "WHERE referer = '" . $referer . "'" : $where . " AND referer = '" . $referer . "'";

                $sql9 = mysql_query("SELECT id FROM " . STATS_VISITOR_TABLE . " " . $and3);
                $refcount = mysql_num_rows($sql9);

                $etat3 = round(($refcount * 100) / $visites2);

                if ($j3 == 0) {
                    $bg3 = $bgcolor2;
                    $j3++;
                }
                else {
                    $bg3 = $bgcolor1;
                    $j3 = 0;
                }

                $referant = preg_replace('`http://`i', '', $referer);

                $ref = (strlen($referant) > 15) ? htmlentities(substr($referant, 0, 15)) . '...' : htmlentities($referant);
                $user_ref = (!empty($referer) && !is_int(strpos($referer, 'login'))) ? '<a href="' . $referer . '" onclick="window.open(this.href); return false;" title="' . $referant .'">' . $ref . '</a>' : _UNKNOWN;

                echo '<tr style="background: ' . $bg3 . '">'."\n"
                . '<td style="width: 5%" align="center">' . $iref . '</td>'."\n"
                . '<td style="width: 25%">' . $user_ref . '</td>'."\n"
                . '<td style="width: 20%" align="center">' . $refcount . ' (' . $etat3 . '%)</td>'."\n"
                . '<td style="width: 50%" align="left">'."\n";

                    show_etat($etat3);

                    echo '</td></tr>';
            }
            // End referer

            if ($iref == 0) {
                echo '<tr><td colspan="4" align="center">' . _NOREF . '</td></tr>'."\n";
            }

            echo'</table>'."\n";

            if ($iref == 10) {
                echo '<table style="margin: auto" width="80%" cellpadding="2" cellspacing="1"><tr><td align="right">'."\n"
                . '<a href="#" onclick="javascript:window.open(\'index.php?file=Stats&amp;nuked_nude=visits&amp;op=view_referer&amp;oday=' . $_REQUEST['oday'] . '&amp;omonth=' . $_REQUEST['omonth'] . '&amp;oyear=' . $_REQUEST['oyear'] . '\',\'Referers\',\'toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=600,top=30,left=0\')">' . _VIEWALL . '</a></td></tr></table>'."\n";
            }

            echo '<br />'."\n";

            if (!empty($_REQUEST['oyear'])) {
                echo '<br /><h3 style="text-align: center; margin-bottom: 10px">' . _CHANGEDAY . '</h3><form method="post" action="index.php?file=Stats&amp;page=visits"><div style="text-align: center">'."\n";

                if (!empty($_REQUEST['oday']) && !empty($_REQUEST['omonth'])) {
                    echo '<select name="oday">';
                    $sql10 = mysql_query("SELECT day FROM " . STATS_VISITOR_TABLE . " WHERE month = '" . $_REQUEST['omonth'] . "' AND year = '" . $_REQUEST['oyear'] . "' GROUP BY day ORDER BY day");
                    while (list($newday) = mysql_fetch_array($sql10)) {
                        $selected = ($_REQUEST['oday'] == $newday) ? 'selected="selected"' : '';

                        echo '<option value="' . $newday . '" ' . $selected . '>' . $newday . '</option>'."\n";
                    }
                    echo '</select> /&nbsp;';
                }

                if (!empty($_REQUEST['omonth'])) {
                    echo '<select name="omonth">';
                    $sql10 = mysql_query("SELECT month FROM " . STATS_VISITOR_TABLE . " WHERE year = '" . $_REQUEST['oyear'] . "' GROUP BY month ORDER BY month");
                    while (list($newmonth) = mysql_fetch_array($sql10)) {
                        $selected1 = ($_REQUEST['omonth'] == $newmonth) ? 'selected="selected"' : '';

                        echo '<option value="' . $newmonth . '" ' . $selected1 . '>' . $newmonth . '</option>'."\n";
                    }
                    echo '</select> /&nbsp;';
                }

                echo '<select name="oyear">';
                $sql10 = mysql_query("SELECT year FROM " . STATS_VISITOR_TABLE . " GROUP BY year ORDER BY year");
                while (list($newyear) = mysql_fetch_array($sql10)) {
                    $selected2 = ($_REQUEST['oyear'] == $newyear) ? 'selected="selected"' : '';

                    echo '<option value="' . $newyear . '" ' . $selected2 . '>' . $newyear . '</option>'."\n";
                }
                echo '</select>&nbsp;&nbsp;<input type="submit" value="' . _SUBMIT . '" /></div></form>'."\n";
            }
        }
        else {
            echo '<br /><br /><div style="text-align: center"><b>' . _NOVISITS . '</b></div><br /><br />'."\n";
        }

        echo '<div style="text-align: center; margin-top: 20px">[ <a href="index.php?file=Stats">' . _STATISTICS . '</a> ]</div><br />'."\n";

        closetable();
    }
}
else if ($nuked['level_analys'] == 1 && $visiteur == 0) {
    opentable();
    echo '<br /><br /><div style="text-align: center">' . _USERENTRANCE . '<br /><br /><b><a href="index.php?file=User&amp;op=login_screen">' . _LOGINUSER . '</a> | '
    . '<a href="index.php?file=User&amp;op=reg_screen">' . _REGISTERUSER . '</a></b></div><br /><br />';
    closetable();
}
else {
    opentable();
    echo '<br /><br /><div style="text-align: center">' . _NOENTRANCE . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a></div><br /><br />';
    closetable();
}

function show_etat($etat){
    global $theme;

    $width = ($etat < 1) ? 1 : $etat;

    $img = (is_file('themes/' . $theme . '/images/bar.gif')) ? 'themes/' . $theme . '/images/bar.gif' : 'modules/Stats/images/bar.gif';

    echo '<div style="width: ' . $width . '%; height: 15px; background: url(' . $img . ') repeat-x center left"></div>';
}

function view_all() {
    global $nuked, $theme, $bgcolor3, $bgcolor2, $bgcolor1;


    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n"
    . '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">'."\n"
    . '<head><title>' . _VISITORS . ' : ' . $_REQUEST['oday'] . '/' . $_REQUEST['omonth'] . '/' . $_REQUEST['oyear'] . '</title>'."\n"
    . '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'."\n"
    . '<meta http-equiv="content-style-type" content="text/css" />'."\n"
    . '<link title="style" type="text/css" rel="stylesheet" href="themes/' . $theme . '/style.css" /></head>'."\n"
    . '<body style="background: ' . $bgcolor2 . '">'."\n"
    . '<table style="border: 1px solid ' . $bgcolor3 . '" width="100%" cellpadding="2" cellspacing="1">'."\n"
    . '<tr style="background: ' . $bgcolor3 . '">'."\n"
    . '<td style="width: 5%" align="center"><b>#</b></td>'."\n"
    . '<td align="center"><b>' . _VPSEUDO . '</b></td>'."\n"
    . '<td align="center"><b>' . _VIP . '</b></td>'."\n"
    . '<td align="center"><b>' . _VHOST . '</b></td>'."\n"
    . '<td align="center"><b>' . _REFERER . '</b></td>'."\n"
    . '<td align="center"><b>' . _VBROWSER . '</b></td>'."\n"
    . '<td align="center"><b>' . _VOS . '</b></td>'."\n"
    . '<td align="center"><b>' . _HOURS . '</b></td></tr>'."\n";

    $i = 0;
    $sql = mysql_query("SELECT ip, user_id, browser, host, referer, os, date FROM " . STATS_VISITOR_TABLE . " WHERE day = '" . $_REQUEST['oday'] . "' AND month = '" . $_REQUEST['omonth'] . "' AND year = '" . $_REQUEST['oyear'] . "' ORDER BY date");
    while (list($ip, $user_id, $browser, $host, $referer, $os, $date) = mysql_fetch_array($sql)) {

        $host = htmlentities($host);
        $i++;
        $hours = strftime("%H:%M", $date);

        $osname = ($os == 'Autres') ? _OTHERS : $os;
        $browsername = ($browser == 'Autres') ? _OTHERS : ($browser == 'Moteurs de recherche') ? _SEARCHENGINE : $browser;

        $referant = preg_replace("`http://`i", "", $referer);
        $ref = (strlen($referant) > 20) ? htmlentities(substr($referant, 0, 20)) . '...' : htmlentities($referant);
        $uref_ref = (!empty($referer) && !is_int(strpos($referer, 'login'))) ? '<a href="' . $referer . '" onclick="window.open(this.href); return false;" title="' . $referer .'">' . $ref . '</a>' : _UNKNOWN;

        if ($j == 0) {
            $bg = $bgcolor2;
            $j++;
        }
        else {
            $bg = $bgcolor1;
            $j = 0;
        }

        if (!empty($user_id)) {
            $sql_member = mysql_query('SELECT pseudo FROM ' . USER_TABLE . ' WHERE  id = "' . $user_id . '"');
            list($pseudo) = mysql_fetch_array($sql_member);
            $v_pseudo = '<a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($pseudo) .'" onclick="window.open(this.href); return false;">' . $pseudo . '</a>';
        }
        else {
            $v_pseudo = _VISITOR;
        }


        $hostname = (!empty($host)) ? '<a href="http://www.' . $host . '" onclick="window.open(this.href); return false;">' . $host . '</a>' : _UNKNOWN;

        echo '<tr style="background: ' . $bg . '">'."\n"
        . '<td style="width: 5%" align="center">' . $i . '</td>'."\n"
        . '<td align="center">' . $v_pseudo . '</td>'."\n"
        . '<td align="center">' . $ip . '</td>'."\n"
        . '<td align="center">' . $hostname . '</td>'."\n"
        . '<td align="center">' . $user_ref . '</td>'."\n"
        . '<td align="center">' . $browsername . '</td>'."\n"
        . '<td align="center">' . $osname . '</td>'."\n"
        . '<td align="center">' . $hours . '</td></tr>'."\n";
    }

    echo '</table><div style="text-align: center"><br /><a href="javascript: self.close()"><b>' . _CLOSEWINDOW . '</b></a></div></body></html>';
}


function view_referer()
{
    global $nuked, $theme, $bgcolor3, $bgcolor2, $bgcolor1;

    if (!empty($_REQUEST['oday']) && !empty($_REQUEST['omonth']) && !empty($_REQUEST['oyear'])) {
        $where = "WHERE day = '" . $_REQUEST['oday'] . "' AND month = '" . $_REQUEST['omonth'] . "' AND year = '" . $_REQUEST['oyear'] . "'";
        $where2 = $where . "AND referer NOT LIKE '" . $nuked['url'] . "%' AND referer != ''";
        $date_title = ' : ' . $_REQUEST['oday'] . '/' . $_REQUEST['omonth'] . '/' . $_REQUEST['oyear'];
    }
    else if (!empty($_REQUEST['omonth']) && !empty($_REQUEST['oyear'])) {
        $where = "WHERE month = '" . $_REQUEST['omonth'] . "' AND year = '" . $_REQUEST['oyear'] . "'";
        $where2 = $where . "AND referer NOT LIKE '" . $nuked['url'] . "%' AND referer != ''";
        $date_title = ' : ' . $_REQUEST['omonth'] . '/' . $_REQUEST['oyear'];
    }
    else if (!empty($_REQUEST['oyear'])) {
        $where = "WHERE year = '" . $_REQUEST['oyear']. "'";
        $where2 = $where . "AND referer NOT LIKE '" . $nuked['url'] . "%' AND referer != ''";
        $date_title = ' : ' . $_REQUEST['oyear'];
    }
    else {
        $where = '';
        $where2 = "WHERE referer NOT LIKE '" . $nuked['url'] . "%' AND referer != ''";
        $date_title = '';
    }

    $sql_v = mysql_query("SELECT id FROM " . STATS_VISITOR_TABLE . " " . $where2);
    $visites = mysql_num_rows($sql_v);

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n"
    . '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">'."\n"
    . '<head><title>' . _REFERER . $date_title . '</title>'."\n"
    . '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'."\n"
    . '<meta http-equiv="content-style-type" content="text/css" />'."\n"
    . '<link title="style" type="text/css" rel="stylesheet" href="themes/' . $theme . '/style.css" /></head>'."\n"
    . '<body style="background: ' . $bgcolor2 . '">'."\n"
    . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . '" width="100%" cellpadding="2" cellspacing="1">'."\n"
    . '<tr style="background: ' . $bgcolor3 . '">'."\n"
    . '<td style="width: 5%" align="center"><b>#</b></td>'."\n"
    . '<td style="width: 25%" align="center"><b>' . _NOM . '</b></td>'."\n"
    . '<td style="width: 20%" align="center"><b>' . _VISITCOUNT . '</b></td>'."\n"
    . '<td style="width: 50%"><table width="100%" cellpadding="0" cellspacing="0">'."\n"
    . '<tr><td style="width: 25%">&nbsp;<b>0%</b></td><td style="width: 25%"><b>25%</b></td><td style="width: 25%;"><b>50%</b></td><td style="width: 25%;"><b>75%</b></td><td style="width: 25%;"><b>100%</b>&nbsp;</td></tr></table></td></tr>'."\n";

    $iref = 0;
    $sql8 = mysql_query("SELECT referer, COUNT(*) AS total FROM " . STATS_VISITOR_TABLE . " " . $where2 . " GROUP BY referer ORDER BY total DESC");
    while (list($referer) = mysql_fetch_array($sql8)) {
        $iref++;

        $and = (empty($where)) ? "WHERE referer = '" . $referer . "'" : $where . " AND referer = '" . $referer . "'";

        $sql = mysql_query("SELECT id FROM " . STATS_VISITOR_TABLE . " " . $and);
        $refcount = mysql_num_rows($sql);

        $etat = round(($refcount * 100) / $visites);

        if ($j == 0) {
            $bg = $bgcolor2;
            $j++;
        }
        else {
            $bg = $bgcolor1;
            $j = 0;
        }

        $referant = preg_replace('`http://`i', '', $referer);
        $ref = (strlen($referant) > 40) ? substr($referant, 0, 40) : $referant;
        $user_ref = (empty($referer) && is_int(strpos($referer, 'login'))) ? _UNKNOWN : '<a href="' . $referer . '" onclick="window.open(this.href); return false;" title="' . $referer .'">' . htmlentities($ref) . '</a>';

        echo '<tr style="background: ' . $bg . '">'."\n"
        . '<td style="width: 5%" align="center">' . $iref . '</td>'."\n"
        . '<td style="width: 25%">' . $user_ref . '</td>'."\n"
        . '<td style="width: 20%" align="center">' . $refcount . ' (' . $etat . '%)</td>'."\n"
        . '<td style="width: 50%" align="left">'."\n";

        show_etat($etat);

        echo '</td></tr>';
    }

    if ($iref == 0) echo '<tr><td colspan="4" align="center">' . _NOREF . '</td></tr>'."\n";

    echo '</table><div style="text-align: center"><br /><a href="javascript: self.close()"><b>' . _CLOSEWINDOW . '</b></a></div></body></html>';
}


function view_host()
{
    global $nuked, $theme, $bgcolor3, $bgcolor2, $bgcolor1;

    if (!empty($_REQUEST['oday']) && !empty($_REQUEST['omonth']) && !empty($_REQUEST['oyear'])) {
        $where = "WHERE day = '" . $_REQUEST['oday'] . "' AND month = '" . $_REQUEST['omonth'] . "' AND year = '" . $_REQUEST['oyear'] . "'";
        $date_title = ' : ' . $_REQUEST['oday'] . '/' . $_REQUEST['omonth'] . '/' . $_REQUEST['oyear'];
    }
    else if (!empty($_REQUEST['omonth']) && !empty($_REQUEST['oyear'])) {
        $where = "WHERE month = '" . $_REQUEST['omonth'] . "' AND year = '" . $_REQUEST['oyear'] . "'";
        $date_title = ' : ' . $_REQUEST['omonth'] . '/' . $_REQUEST['oyear'];
    }
    else if (!empty($_REQUEST['oyear'])) {
        $where = "WHERE year = '" . $_REQUEST['oyear']. "'";
        $date_title = ' : ' . $_REQUEST['oyear'];
    }
    else {
        $where = '';
        $date_title = '';
    }

    $sql_v = mysql_query("SELECT id FROM " . STATS_VISITOR_TABLE . " " . $where);
    $visites = mysql_num_rows($sql_v);

    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'."\n"
    . '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">'."\n"
    . '<head><title>' . _HOST . $date_title . '</title>'."\n"
    . '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />'."\n"
    . '<meta http-equiv="content-style-type" content="text/css" />'."\n"
    . '<link title="style" type="text/css" rel="stylesheet" href="themes/' . $theme . '/style.css" /></head>'."\n"
    . '<body style="background: ' . $bgcolor2 . '">'."\n"
    . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . '" width="100%" cellpadding="2" cellspacing="1">'."\n"
    . '<tr style="background: ' . $bgcolor3 . '">'."\n"
    . '<td style="width: 5%" align="center"><b>#</b></td>'."\n"
    . '<td style="width: 25%" align="center"><b>' . _NOM . '</b></td>'."\n"
    . '<td style="width: 20%" align="center"><b>' . _VISITCOUNT . '</b></td>'."\n"
    . '<td style="width: 50%"><table width="100%" cellpadding="0" cellspacing="0">'."\n"
    . '<tr><td style="width: 25%">&nbsp;<b>0%</b></td><td style="width: 25%"><b>25%</b></td><td style="width: 25%;"><b>50%</b></td><td style="width: 25%;"><b>75%</b></td><td style="width: 25%;"><b>100%</b>&nbsp;</td></tr></table></td></tr>'."\n";

    $ihost = 0;
    $sql = mysql_query("SELECT host, COUNT(*) AS total FROM " . STATS_VISITOR_TABLE . " " . $where . " GROUP BY host ORDER BY total DESC");
    while (list($host) = mysql_fetch_array($sql))
    {
        $ihost++;
        $and = (empty($where)) ? "WHERE host = '" . $host . "'" : $where . " AND host = '" . $host . "'";

        $sql2 = mysql_query("SELECT id FROM " . STATS_VISITOR_TABLE . " " . $and);
        $hostcount = mysql_num_rows($sql2);

        $etat = round(($hostcount * 100) / $visites);

        if ($j == 0) {
            $bg = $bgcolor2;
            $j++;
        }
        else {
            $bg = $bgcolor1;
            $j = 0;
        }

        $hostname = (empty($host)) ? _UNKNOWN : '<a href="http://www.' . urlencode($host) . '" onclick="window.open(this.href); return false;">' . htmlentities($host) . '</a>';

        echo '<tr style="background: ' . $bg . '">'."\n"
        . '<td style="width: 5%" align="center">' . $ihost . '</td>'."\n"
        . '<td style="width: 25%">' . $hostname . '</td>'."\n"
        . '<td style="width: 20%" align="center">' . $hostcount . ' (' . $etat . '%)</td>'."\n"
        . '<td style="width: 50%" align="left">'."\n";

        show_etat($etat);

        echo '</td></tr>'."\n";
    }

    echo '</table><div style="text-align: center"><br /><a href="javascript: self.close()"><b>' . _CLOSEWINDOW . '</b></a></div></body></html>';
}
?>