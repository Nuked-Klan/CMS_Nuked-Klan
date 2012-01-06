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

opentable();

global $user, $nuked, $language, $bgcolor3, $bgcolor2, $bgcolor1;
translate("modules/Stats/lang/" . $language . ".lang.php");

$level_access = nivo_mod('Stats');
$visiteur = ($user) ? $user[1] : 0;

if ($visiteur >= $level_access && $level_access > -1) {

    echo '<h2 style="text-align: center; margin-top: 10px">' . _TOP10 . '&nbsp;' . $nuked['name'] . '</h2>'."\n";

    if (nivo_mod('Download') != -1) {
        echo '<h3 style="text-align: center; margin-top: 30px">' . _TOPDOWNLOAD . '</h3>'."\n"
        . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . ';" width="80%" cellpadding="2" cellspacing="1">'."\n"
        . '<tr style="background: ' . $bgcolor3 . '">'."\n"
        . '<td style="width: 10%" align="center"><b>#</b></td>'."\n"
        . '<td style="width: 60%" align="center"><b>' . _NOM . '</b></td>'."\n"
        . '<td style="width: 30%" align="center"><b>' . _DOWNLOADCOUNT . '</b></td></tr>'."\n";

        $sql = mysql_query("SELECT id, titre, count FROM " . DOWNLOAD_TABLE . " ORDER BY count DESC LIMIT 0, 10");
        $nb_dl = mysql_num_rows($sql);
        if ($nb_dl > 0) {
            $idl = 0;
            while (list($dl_id, $dl_titre, $dl_count) = mysql_fetch_array($sql)) {
                $idl++;

                if ($j == 0) {
                    $bg = $bgcolor2;
                    $j++;
                }
                else {
                    $bg = $bgcolor1;
                    $j = 0;
                }

                $dl_titre = str_replace(array('&lt;','&gt;','&amp;'), array('<','>','&'), $dl_titre);
                $dl_titre = (strlen($dl_titre) > 40) ? substr($dl_titre, 0, 40) . '...' : $dl_titre;

                echo '<tr style="background: ' . $bg . '">'."\n"
                . '<td style="width: 10%" align="center">' . $idl . '</td>'."\n"
                . '<td style="width: 60%"><a href="index.php?file=Download&amp;op=description&amp;dl_id=' . $dl_id . '">' . htmlentities($dl_titre) . '</a></td>'."\n"
                . '<td style="width: 30%" align="center">' . $dl_count . '</td></tr>'."\n";
            }
        }
        else {
            echo '<tr><td align="center" colspan="3">' . _NODOWNLOAD . '</td></tr>'."\n";
        }

        echo '</table>'."\n";
    }


    if (nivo_mod('Links') != -1) {
        echo '<h3 style="text-align: center; margin-top: 30px">' . _TOPLINK . '</h3>'."\n"
        . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . ';" width="80%" cellpadding="2" cellspacing="1">'."\n"
        . '<tr style="background: ' . $bgcolor3 . '">'."\n"
        . '<td style="width: 10%" align="center"><b>#</b></td>'."\n"
        . '<td style="width: 60%" align="center"><b>' . _NOM . '</b></td>'."\n"
        . '<td style="width: 30%" align="center"><b>' . _VISITCOUNT . '</b></td></tr>'."\n";

        $sql2 = mysql_query("SELECT id, titre, count FROM " . LINKS_TABLE . " ORDER BY count DESC LIMIT 0, 10");
        $nb_link = mysql_num_rows($sql2);
        if ($nb_link > 0) {
            $ilink = 0;
            while (list($link_id, $link_titre, $link_count) = mysql_fetch_array($sql2)) {
                $ilink++;

                if ($j1 == 0) {
                    $bg1 = $bgcolor2;
                    $j1++;
                }
                else {
                    $bg1 = $bgcolor1;
                    $j1 = 0;
                }

                $link_titre = str_replace(array('&lt;','&gt;','&amp;'), array('<','>','&'), $link_titre);
                $link_titre = (strlen($link_titre) > 40) ? substr($link_titre, 0, 40) . '...' : $link_titre;

                echo '<tr style="background: ' . $bg1 . '">'."\n"
                . '<td style="width: 10%" align="center">' . $ilink . '</td>'."\n"
                . '<td style="width: 60%"><a href="index.php?file=Links&amp;op=description&amp;link_id=' . $link_id . '">' . htmlentities($link_titre) . '</a></td>'."\n"
                . '<td style="width: 30%" align="center">' . $link_count . '</td></tr>'."\n";
            } 
        }
        else {
            echo '<tr><td align="center" colspan="3">' . _NOLINK . '</td></tr>'."\n";
        }

        echo '</table>'."\n";
    }


    if (nivo_mod('Sections') != -1) {
        echo '<h3 style="text-align: center; margin-top: 30px">' . _TOPARTICLE . '</h3>'."\n"
        . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . ';" width="80%" cellpadding="2" cellspacing="1">'."\n"
        . '<tr style="background: ' . $bgcolor3 . '">'."\n"
        . '<td style="width: 10%" align="center"><b>#</b></td>'."\n"
        . '<td style="width: 60%" align="center"><b>' . _NOM . '</b></td>'."\n"
        . '<td style="width: 30%" align="center"><b>' . _READCOUNT . '</b></td></tr>'."\n";

        $sql3 = mysql_query("SELECT artid, title, counter FROM " . SECTIONS_TABLE . " ORDER BY counter DESC LIMIT 0, 10");
        $nb_art = mysql_num_rows($sql3);
        if ($nb_art > 0) {
            $iart = 0;
            while (list($art_id, $art_titre, $art_count) = mysql_fetch_array($sql3)) {
                $iart++;

                if ($j2 == 0) {
                    $bg2 = $bgcolor2;
                    $j2++;
                }
                else {
                    $bg2 = $bgcolor1;
                    $j2 = 0;
                }

                $art_titre = str_replace(array('&lt;','&gt;','&amp;'), array('<','>','&'), $art_titre);
                $art_titre = (strlen($art_titre) > 40) ? substr($art_titre, 0, 40) . '...' : $art_titre;

                echo '<tr style="background: ' . $bg2 . '">'."\n"
                . '<td style="width: 10%" align="center">' . $iart . '</td>'."\n"
                . '<td style="width: 60%"><a href="index.php?file=Sections&amp;op=article&amp;artid=' . $art_id . '">' . htmlentities($art_titre) . '</a></td>'."\n"
                . '<td style="width: 30%" align="center">' . $art_count . '</td></tr>'."\n";
            } 
        }
        else {
            echo '<tr><td align="center" colspan="3">' . _NOART . '</td></tr>'."\n";
        }

        echo '</table>'."\n";
    }


    if (nivo_mod('Forum') != -1) {
        echo '<h3 style="text-align: center; margin-top: 30px">' . _TOPTHREADS . '</h3>'."\n"
        . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . ';" width="80%" cellpadding="2" cellspacing="1">'."\n"
        . '<tr style="background: ' . $bgcolor3 . '">'."\n"
        . '<td style="width: 10%" align="center"><b>#</b></td>'."\n"
        . '<td style="width: 60%" align="center"><b>' . _NOM . '</b></td>'."\n"
        . '<td style="width: 30%" align="center"><b>' . _READCOUNT . '</b></td></tr>'."\n";

        $sql4 = mysql_query("SELECT id, forum_id, titre, view FROM " . FORUM_THREADS_TABLE . " ORDER BY view DESC LIMIT 0, 10");
        $nb_topic = mysql_num_rows($sql4);
        if ($nb_topic > 0) {
            $itopic = 0;
            while (list($tid, $fid, $topic_titre, $views) = mysql_fetch_array($sql4)) {
                $itopic++;

                if ($j3 == 0) {
                    $bg3 = $bgcolor2;
                    $j3++;
                }
                else {
                    $bg3 = $bgcolor1;
                    $j3 = 0;
                }

                $topic_titre = str_replace(array('&lt;','&gt;','&amp;'), array('<','>','&'), $topic_titre);
                $topic_titre = (strlen($topic_titre) > 40) ? substr($topic_titre, 0, 40) . '...' : $topic_titre;

                echo '<tr style="background: ' . $bg3 . '">'."\n"
                . '<td style="width: 10%" align="center">' . $itopic . '</td>'."\n"
                . '<td style="width: 60%"><a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=' . $fid . '&amp;thread_id=' . $tid . '">' . htmlentities($topic_titre) . '</a></td>'."\n"
                . '<td style="width: 30%" align="center">' . $views . '</td></tr>'."\n";
            }
        }
        else {
            echo '<tr><td align="center" colspan="3">' . _NOTOPIC . '</td></tr>'."\n";
        }
        echo '</table><h3 style="text-align: center; margin-top: 30px">' . _TOPUSERFORUM . '</h3>'."\n"
        . '<table style="margin: auto; background: ' . $bgcolor2 . '; border: 1px solid ' . $bgcolor3 . ';" width="80%" cellpadding="2" cellspacing="1">'."\n"
        . '<tr style="background: ' . $bgcolor3 . '">'."\n"
        . '<td style="width: 10%" align="center"><b>#</b></td>'."\n"
        . '<td style="width: 60%" align="center"><b>' . _PSEUDO . '</b></td>'."\n"
        . '<td style="width: 30%" align="center"><b>' . _POSTCOUNT . '</b></td></tr>'."\n";

        $sql5 = mysql_query("SELECT pseudo, count FROM " . USER_TABLE . " ORDER BY count DESC LIMIT 0, 10");

        $iuserf = 0;
        while (list($pseudof, $userfcount) = mysql_fetch_array($sql5)) {
            $iuserf++;

            if ($j4 == 0) {
                $bg4 = $bgcolor2;
                $j4++;
            } 
            else {
                $bg4 = $bgcolor1;
                $j4 = 0;
            }

            $pseudof = str_replace(array('&lt;','&gt;','&amp;'), array('<','>','&'), $pseudof);

            echo '<tr style="background: ' . $bg4 . '">'."\n"
            . '<td style="width: 10%" align="center">' . $iuserf . '</td>'."\n"
            . '<td style="width: 60%"><a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($pseudof) . '">' . htmlentities($pseudof) . '</a></td>'."\n"
            . '<td style="width: 30%" align="center">' . $userfcount . '</td></tr>'."\n";
        }

        echo '</table>'."\n";
    }

    echo '<div style="text-align: center"><br /><br />[ <a href="index.php?file=Stats">' . _STATISTICS . '</a> ]<br /><br /></div>'."\n";
} else if ($level_access == -1) {
    echo '<br /><br /><div style="text-align: center;">' . _MODULEOFF . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a></div><br /><br />';
} else if ($level_access == 1 && $visiteur == 0) {
    echo '<br /><br /><div style="text-align: center;">' . _USERENTRANCE . '<br /><br /><b><a href="index.php?file=User&amp;op=login_screen">' . _LOGINUSER . '</a> | <a href="index.php?file=User&amp;op=reg_screen">' . _REGISTERUSER . '</a></b></div><br /><br />';
} else {
    echo '<br /><br /><div style="text-align: center;">' . _NOENTRANCE . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a></div><br /><br />';
}

closetable();
?>