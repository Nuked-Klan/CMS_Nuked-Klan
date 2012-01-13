<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $nuked, $user, $language, $bgcolor3, $bgcolor2, $bgcolor1;
translate('modules/Stats/lang/' . $language . '.lang.php');

$level_access = nivo_mod('Stats');
$visiteur = ($user) ? $user[1] : 0;

if (!isset($_REQUEST['nuked_nude'])) {
    opentable();
}

if ($visiteur >= $level_access && $level_access > -1) {

    function show_etat($etat) {
        global $theme;

        $width = ($etat < 1) ? 1 : $etat;

        $img = (is_file('themes/' . $theme . '/images/bar.gif')) ? 'themes/' . $theme . '/images/bar.gif' : 'modules/Stats/images/bar.gif';

        echo '<div style="width: ' . $width . '%; height: 10px; background: url(' . $img . ')"></div>';
    }

    if (!isset($_REQUEST['nuked_nude'])) {
        echo '<div style="text-align: center"><br /><h2><b>' . _STATSSITE . '&nbsp;' . $nuked['name'] . '</b></h2>'."\n";
    }

    if ($visiteur >= $nuked['level_analys'] && $nuked['level_analys'] != -1) {
        echo '[ <a href="index.php?file=Stats&amp;page=visits">' . _ANALYS . '</a> ] - ';
    }

    echo '[ <a href="index.php?file=Stats&amp;page=top">' . _TOP . '</a> ]</div>'."\n";

    $sql = mysql_query('SELECT
        (SELECT COUNT(id) FROM ' . USER_TABLE . ') AS nb_us,
        (SELECT COUNT(id) FROM ' . NEWS_TABLE . ') AS nb_nw,
        (SELECT COUNT(artid) FROM ' . SECTIONS_TABLE . ') AS nb_sc,
        (SELECT COUNT(id) FROM ' . COMMENT_TABLE . ') AS nb_cm,
        (SELECT COUNT(id) FROM ' . GUESTBOOK_TABLE . ') AS nb_gt,
        (SELECT COUNT(id) FROM ' . DOWNLOAD_TABLE . ') AS nb_dl,
        (SELECT COUNT(id) FROM ' . LINKS_TABLE . ') AS nb_lk,
        (SELECT COUNT(sid) FROM ' . GALLERY_TABLE . ') AS nb_gl,
        (SELECT COUNT(warid) FROM ' . WARS_TABLE . ') AS nb_wr,
        (SELECT SUM(count) FROM ' . STATS_TABLE . ') AS count');
    list($nb_us, $nb_nw, $nb_sc, $nb_cm, $nb_gt, $nb_dl, $nb_lk, $nb_gl, $nb_wr, $counter) = mysql_fetch_array($sql);

    echo '<div style="text-align: center"><br />' . _WERECEICED . '&nbsp;' . $counter . '&nbsp;' . _PAGESEE . '&nbsp;' . nkDate($nuked['date_install'], TRUE) . '.<br /><br /><h3>' . _PAGEVIEWS . '</h3></div>'."\n"
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

    $nb = 0;
    $sql2 = mysql_query('SELECT nom, count FROM ' . STATS_TABLE . ' ORDER BY count DESC');
    while (list($page, $count) = mysql_fetch_array($sql2)) {
        if (nivo_mod($page) != -1) {
            $nb++;

            if ($page == 'Archives') {
                $pagename = _NAMEARCHIVES;
            }
            else if ($page == 'Calendar') {
                $pagename = _NAMECALANDAR;
            }
            else if ($page == "Defy") {
                $pagename = _NAMEDEFY;
            }
            else if ($page == 'Download') {
                $pagename = _NAMEDOWNLOAD;
            }
            else if ($page == 'Forum') {
                $pagename = _NAMEFORUM;
            }
            else if ($page == 'Gallery') {
                $pagename = _NAMEGALLERY;
            }
            else if ($page == 'Guestbook') {
                $pagename = _NAMEGUESTBOOK;
            }
            else if ($page == "Irc") {
                $pagename = _NAMEIRC;
            }
            else if ($page == 'Links') {
                $pagename = _NAMELINKS;
            }
            else if ($page == 'Members') {
                $pagename = _NAMEMEMBERS;
            }
            else if ($page == 'News') {
                $pagename = _NAMENEWS;
            }
            else if ($page == "Recruit") {
                $pagename = _NAMERECRUIT;
            }
            else if ($page == 'Search') {
                $pagename = _NAMESEARCH;
            }
            else if ($page == 'Sections') {
                $pagename = _NAMESECTIONS;
            }
            else if ($page == "Server") {
                $pagename = _NAMESERVER;
            }
            else if ($page == "Team") {
                $pagename = _NAMETEAM;
            }
            else if ($page == "Wars") {
                $pagename = _NAMEMATCHES;
            }
            else {
                $pagename = $page;
            }

            $etat = round(($count * 100) / $counter);

            if ($j == 0) {
                $bg = $bgcolor2;
                $j++;
            } 
            else {
                $bg = $bgcolor1;
                $j = 0;
            }

            echo '<tr style="background: ' . $bg . '">'."\n"
            . '<td style="width : 5%" align="center">' . $nb . '</td>'."\n"
            . '<td style="width : 25%">' . $pagename . '</td>'."\n"
            . '<td style="width : 20%" align="center">' . $count . ' (' . $etat . '%)</td>'."\n"
            . '<td style="width : 50%">'."\n";

            show_etat($etat);

            echo '</td></tr>'."\n";
        }
    }

    echo '</table>'."\n"
    . '<h3 style="text-align: center; margin-top: 20px">' . _OTHERSTATS . '</h3>'."\n"
    . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . '" width="80%" cellpadding="2" cellspacing="1">'."\n"
    . '<tr style="background: ' . $bgcolor3 . '">'."\n"
    . '<td align="center"><b>' . _NOM . '</b></td>'."\n"
    . '<td align="center"><b>' . _COUNT . '</b></td></tr>'."\n";

    echo '<tr style="background: ' . $bgcolor2 . '"><td>&nbsp;' . _MEMBERSRECORD . '</td><td align="center">' . $nb_us . '</td></tr>'."\n";

    if (nivo_mod('News') != -1) {
        echo '<tr style="background: ' . $bgcolor1 . '"><td>&nbsp;' . _NEWSINDB . '</td><td align="center">' . $nb_nw . '</td></tr>'."\n";
    }
    if (nivo_mod('Sections') != -1) {
        echo '<tr style="background: ' . $bgcolor2 . '"><td>&nbsp;' . _ARTSINDB . '</td><td align="center">' . $nb_sc . '</td></tr>'."\n";
    }
    if (nivo_mod('Comment') != -1) {
        echo '<tr style="background: ' . $bgcolor1 . '"><td>&nbsp;' . _COMMENTINDB . '</td><td align="center">' . $nb_cm . '</td></tr>'."\n";
    }
    if (nivo_mod('Guestbook') != -1) {
        echo '<tr style="background: ' . $bgcolor2 . '"><td>&nbsp;' . _SIGNINGUESTBOOK . '</td><td align="center">' . $nb_gt . '</td></tr>'."\n";
    }
    if (nivo_mod('Download') != -1) {
        echo '<tr style="background: ' . $bgcolor1 . '"><td>&nbsp;' . _FILESINDB . '</td><td align="center">' . $nb_dl . '</td></tr>'."\n";
    }
    if (nivo_mod('Links') != -1) {
        echo '<tr style="background: ' . $bgcolor2 . '"><td>&nbsp;' . _LINKSINDB . '</td><td align="center">' . $nb_lk . '</td></tr>'."\n";
    }
    if (nivo_mod('Gallery') != -1) {
        echo '<tr style="background: ' . $bgcolor1 . '"><td>&nbsp;' . _SCREENINGALLERY . '</td><td align="center">' . $nb_gl . '</td></tr>'."\n";
    }
    if (nivo_mod('Wars') != -1) {
        echo '<tr style="background: ' . $bgcolor2 . '"><td>&nbsp;' . _MATCHSINDB . '</td><td align="center">' . $nb_wr . '</td></tr>'."\n";
    }

    echo '</table><br />'."\n";

} else if ($level_access == -1) {
    echo '<br /><br /><div style="text-align: center;">' . _MODULEOFF . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a></div><br /><br />';
} else if ($level_access == 1 && $visiteur == 0) {
    echo '<br /><br /><div style="text-align: center;">' . _USERENTRANCE . '<br /><br /><b><a href="index.php?file=User&amp;op=login_screen">' . _LOGINUSER . '</a> | <a href="index.php?file=User&amp;op=reg_screen">' . _REGISTERUSER . '</a></b></div><br /><br />';
} else {
    echo '<br /><br /><div style="text-align: center;">' . _NOENTRANCE . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a></div><br /><br />';
}
if (!isset($_REQUEST['nuked_nude'])) {
    closetable();
}
?>
