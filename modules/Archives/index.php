<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")){
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $language;

translate("modules/Archives/lang/".$language.".lang.php");

compteur("Archives");

opentable();

$visiteur = (!$user) ? 0 : $user[1];
$level_access = nivo_mod("News");

if ($visiteur >= $level_access && $level_access > -1) {



    function index() {

        $nb_news = $GLOBALS["nuked"]['max_archives'];
        $day = time();

        $sql =  mysql_query("
                    SELECT date
                    FROM ".NEWS_TABLE."
                    ORDER BY date DESC
                ");
        $count = mysql_num_rows($sql);

        if (!$_REQUEST['p']) { $_REQUEST['p'] = 1; }

        $start = $_REQUEST['p'] * $nb_news - $nb_news;
        ?>
        <div style="text-align:center;margin: 10px auto;">
            <big><strong><?php echo _ARCHIVE; ?></strong></big>
        </div>
        <table width="100%">
            <tr><td align="right"><?php echo _ORDERBY; ?> :
        <?php

        if (!$_REQUEST['orderby']) {
            $_REQUEST['orderby'] = "date";
        }

        if ($_REQUEST['orderby'] == "date"):
            ?><strong><?php echo _DATE; ?></strong><?php
        else:
            ?> | <a href="index.php?file=Archives&amp;orderby=date"><?php echo _DATE; ?></a><?php
        endif;

        if ($_REQUEST['orderby'] == "titre"):
            ?> | <strong><?php echo _TITLE; ?></strong><?php
        else:
            ?> | <a href="index.php?file=Archives&amp;orderby=titre"><?php echo _TITLE; ?></a><?php
        endif;

        if ($_REQUEST['orderby'] == "sujet"):
            ?> | <strong><?php echo _SUBJET; ?></strong><?php
        else:
            ?> | <a href="index.php?file=Archives&amp;orderby=sujet"><?php echo _SUBJET; ?></a><?php
        endif;

        if ($_REQUEST['orderby'] == "auteur"):
            ?> | <strong><?php echo _AUTHOR; ?></strong><?php
        else:
            ?> | <a href="index.php?file=Archives&amp;orderby=auteur"><?php echo _AUTHOR; ?></a><?php
        endif;

        echo "</td></tr></table>\n";

        if ($count > $nb_news) {
            $url_page = "index.php?file=Archives&amp;orderby=".$_REQUEST['orderby'];
            number($count, $nb_news, $url_page);
        }
        ?>
        <table
            style="margin-left:auto;margin-right:auto;text-align:left;background:<?php echo $GLOBALS["bgcolor2"]; ?>;border: 1px solid <?php echo $GLOBALS["bgcolor3"]; ?>;"
            width="100%" cellpadding="2" cellspacing="1">
                <tr style="background:<?php echo $GLOBALS["bgcolor3"]; ?>">
                <td style="width: 30%;" align="center"><strong><?php echo _TITLE; ?></strong></td>
                <td style="width: 20%;" align="center"><strong><?php echo _SUBJET; ?></strong></td>
                <td style="width: 25%;" align="center"><strong><?php echo _DATE; ?></strong></td>
                <td style="width: 15%;" align="center"><strong><?php echo _AUTHOR; ?></strong></td>
                <td style="width: 10%;" align="center"><strong><?php echo _OPTION; ?>&nbsp;</strong></td></tr>
        <?php

        $sql_nb = mysql_query("
                    SELECT nid
                    FROM ".NEWS_CAT_TABLE
                );
        $nbsujet = mysql_num_rows($sql_nb);

        if ($_REQUEST['orderby'] == "titre") {
            $sql2 = mysql_query("
                SELECT id, titre, auteur, auteur_id, date, cat FROM ".NEWS_TABLE."
                WHERE '".$day."' = date
                ORDER BY titre
                LIMIT ".$start.", ".$nb_news."");
        } else if ($_REQUEST['orderby'] == "date") {
            $sql2 = mysql_query("
                SELECT id, titre, auteur, auteur_id, date, cat
                FROM ".NEWS_TABLE."
                WHERE '".$day."' >= date
                ORDER BY date DESC
                LIMIT ".$start.", ".$nb_news."");
        } else if ($_REQUEST['orderby'] == "auteur") {
            $sql2 = mysql_query("
                SELECT id, titre, auteur, auteur_id, date, cat
                FROM ".NEWS_TABLE."
                WHERE ".$day." >= date
                ORDER BY auteur
                LIMIT ".$start.", ".$nb_news."");
        } else if ($_REQUEST['orderby'] == "sujet") {
            $sql2 = mysql_query("
                SELECT id, titre, auteur, auteur_id, date, cat
                FROM ".NEWS_TABLE."
                WHERE ".$day." >= date
                ORDER BY cat
                LIMIT ".$start.", " . $nb_news."");
        } else {
            $sql2 = mysql_query("
                SELECT id, titre, auteur, auteur_id, date, cat
                FROM ".NEWS_TABLE."
                WHERE ".$day." >= date
                ORDER BY id DESC
                LIMIT ".$start.", " . $nb_news."");
        }

        while (list($news_id, $titre, $autor, $autor_id, $date, $cat) = mysql_fetch_array($sql2)):

            $date = nkDate($date);

            if (strlen($titre) > 25):
                $title = '  <a href="index.php?file=News&amp;op=index_comment&amp;news_id='.$news_id.'" title="'.$titre.'">'.nkHtmlEntities(substr($titre, 0, 25)).'...</a>';
            else:
                $title = '<a href="index.php?file=News&amp;op=index_comment&amp;news_id='.$news_id.'">'.nkHtmlEntities($titre).'</a>';
            endif;

            if (!empty($autor_id)) {
                $sql4 = mysql_query("
                    SELECT pseudo
                    FROM ".USER_TABLE."
                    WHERE id = '".$autor_id."'
                ");
                $test = mysql_num_rows($sql4);
            }

            if ($autor_id != "" && $test > 0) {
                list($auteur) = mysql_fetch_array($sql4);
            }
            else {
                $auteur = $autor;
            }

            if ($j == 0) {
                $bg = $GLOBALS["bgcolor2"];
                $j++;
            }
            else {
                $bg = $GLOBALS["bgcolor1"];
                $j = 0;
            }
            ?>
            <tr style="background:<?php echo $bg; ?>"><td style="width: 30%;"><?php echo $title; ?></td>
            <?php
            if (!empty($cat)):
                $sql3 = mysql_query("
                    SELECT titre
                    FROM ".NEWS_CAT_TABLE."
                    WHERE nid = '".$cat."'
                ");
                list($categorie) = mysql_fetch_array($sql3);

                $categorie = nkHtmlEntities($categorie);
                ?>
                <td style="width: 20%;" align="center">
                    <a href="index.php?file=Archives&amp;op=sujet&amp;cat_id=" . $cat; ?>" title="<?php echo _SEENEWS; ?>&nbsp;<?php echo $categorie; ?>
                        <?php echo $categorie; ?>
                    </a>
                </td>
                <?php
            else:
                ?>
                <td style="width: 20%;" align="center">N/A</td>
                <?php
            endif;
            ?>
                <td style="width: 25%;" align="center"><?php echo $date; ?></td>
                <td style="width: 15%;" align="center">
                    <a href="index.php?file=Members&amp;op=detail&amp;autor=<?php echo urlencode($auteur); ?>" title="<?php echo _DETAILAUTHOR; ?>&nbsp;<?php echo $auteur; ?>">
                        <?php echo $auteur; ?>
                    </a>
                </td>
                <td style="width: 10%;" align="center">
                    <a href="index.php?file=News&amp;nuked_nude=index&amp;op=pdf&amp;news_id=<?php echo $news_id; ?>" onclick="window.open(this.href); return false;">
                        <img style="border: 0;" src="images/pdf.gif" alt="" title="<?php echo _PDF; ?>">
                    </a>
                    &nbsp;
                    <a href="index.php?file=News&amp;op=sendfriend&amp;news_id=<?php echo $news_id; ?>">
                        <img style="border: 0;" src="images/friend.gif" alt="" title="<?php echo _FSEND; ?>">
                    </a>
                </td>
            </tr>
        <?php
        endwhile;

        if ($count == 0):
        ?>
            <tr><td colspan="5" align="center"><?php echo _NONEWS; ?></td></tr>
        <?php
        endif;
        ?>
        </table>
        <?php
        if ($count > $nb_news) {
            $url_page = "index.php?file=Archives&amp;orderby=".$_REQUEST['orderby'];
            number($count, $nb_news, $url_page);
        }
        ?>
        <div style="text-align: center;margin:10px auto;">
            <small>
                <i>( <?php echo _THEREIS; ?> <?php echo $count; ?>&nbsp;<?php echo _NEWS; ?> <?php echo $nbsujet; ?> <?php echo _SUBNEWS; ?> <?php echo _INDATABASE; ?>)
                </i>
            </small>
        </div>
        <?php
    }

    function sujet($cat_id)
    {

        $nb_news = $GLOBALS["nuked"]['max_archives'];
        $day = time();

        $sql = mysql_query("
            SELECT cat FROM ".NEWS_TABLE."
            WHERE cat = '".$cat_id."'
            ORDER BY date DESC"
        );
        $count = mysql_num_rows($sql);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_news - $nb_news;
        ?>
        <div style="text-align: center;margin:10px auto;">
            <big><strong><?php echo _ARCHIVE; ?></strong></big>
        </div>
        <table width="100%">
            <tr>
                <td align="right"><?php echo _ORDERBY; ?> :
        <?php

        if (!$_REQUEST['orderby']) {
            $_REQUEST['orderby'] = "date";
        }

        if ($_REQUEST['orderby'] == "date"):
            echo "<strong><?php echo _DATE; ?></strong> | ";
        else:
            ?>  <a href="index.php?file=Archives&amp;op=sujet&amp;cat_id=<?php echo $cat_id; ?>&amp;orderby=date">
                    <?php echo _DATE; ?>
                </a> |
            <?php
        endif;

        if ($_REQUEST['orderby'] == "titre"):
            ?>  <strong><?php echo _TITLE; ?></strong> | <?php
        else:
            ?>  <a href="index.php?file=Archives&amp;op=sujet&amp;cat_id=<?php echo $cat_id; ?>&amp;orderby=titre">
                    <?php echo _TITLE; ?>
                </a> |
            <?php
        endif;

        if ($_REQUEST['orderby'] == "auteur"):
            ?>
                <strong><?php echo _AUTHOR; ?></strong>
            <?php
        else:
            ?>  <a href="index.php?file=Archives&amp;op=sujet&amp;cat_id=<?php echo $cat_id; ?>&amp;orderby=auteur">
                    <?php echo _AUTHOR; ?>
                </a>
            <?php
        endif;
            ?>
                </td>
            </tr>
        </table>
        <?php

        if ($count > $nb_news) {
            $url_page = "index.php?file=Archives&amp;op=sujet&amp;cat_id=<?php echo $cat_id; ?>&amp;orderby=<?php echo $_REQUEST['orderby']; ?>"
            number($count, $nb_news, $url_page);
        }
        ?>
        <table
            style=" margin-left: auto; margin-right: auto;
                    text-align: left;background:<?php echo $GLOBALS["bgcolor2"]; ?>;
                    border: 1px solid <?php echo $GLOBALS["bgcolor3"]; ?>"
                    width="100%" cellpadding="2" cellspacing="1">
            <tr style="background:<?php echo $GLOBALS["bgcolor3"]; ?>">
                <td style="width: 30%;" align="center"><strong><?php echo _TITLE; ?></strong></td>
                <td style="width: 20%;" align="center"><strong><?php echo _SUBJET; ?></strong></td>
                <td style="width: 25%;" align="center"><strong><?php echo _DATE; ?></strong></td>
                <td style="width: 15%;" align="center"><strong><?php echo _AUTHOR; ?></strong></td>
                <td style="width: 10%;" align="center"><strong><?php echo _OPTION; ?>&nbsp;</strong></td>
                </tr>
        <?php
        if ($_REQUEST['orderby'] == "titre") {
            $sql2 = mysql_query("
                SELECT id, titre, auteur, auteur_id, date, cat
                FROM ".NEWS_TABLE."
                WHERE cat = '".$cat_id."'
                    AND '".$day."' >= date
                ORDER BY titre
                LIMIT ".$start.", ".$nb_news."
            ");
        }
        else if ($_REQUEST['orderby'] == "date") {
            $sql2 = mysql_query("
                SELECT id, titre, auteur, auteur_id, date, cat
                FROM ".NEWS_TABLE."
                WHERE cat = '" . $cat_id."'
                    AND '" . $day."' >= date
                ORDER BY date DESC
                LIMIT ".$start.", ".$nb_news."
            ");
        } else if ($_REQUEST['orderby'] == "auteur") {
            $sql2 = mysql_query("
                SELECT id, titre, auteur, auteur_id, date, cat
                FROM ".NEWS_TABLE."
                WHERE cat = '" . $cat_id."'
                    AND '" . $day."' >= date
                ORDER BY auteur
                LIMIT " . $start.", ".$nb_news."
            ");
        } else {
            $sql2 = mysql_query("
                SELECT id, titre, auteur, auteur_id, date, cat
                FROM ".NEWS_TABLE."
                WHERE cat = '" . $cat_id."'
                    AND '" . $day."' >= date
                ORDER BY id DESC
                LIMIT " . $start.", ".$nb_news."");
        }

        while (list($news_id, $titre, $autor, $autor_id, $date, $cat) = mysql_fetch_array($sql2)):

            $date = nkDate($date);

            if (strlen($titre) > 25):
                $title = '  <a href="index.php?file=News&amp;op=index_comment&amp;news_id='.$news_id.'" title="'$titre.'">
                                '.nkHtmlEntities(substr($titre, 0, 25)).'...</a>';
            else:
                $title =  ' <a href="index.php?file=News&amp;op=index_comment&amp;news_id='.$news_id.'">
                                '.nkHtmlEntities($titre).'</a>';
            endif;

            if ($autor_id != "") {
                $sql4 = mysql_query("
                    SELECT pseudo
                    FROM ".USER_TABLE."
                    WHERE id = '".$autor_id."'
                ");
                $test = mysql_num_rows($sql4);
            }

            if ($autor_id != "" && $test > 0) {
                list($auteur) = mysql_fetch_array($sql4);
            } else {
                $auteur = $autor;
            }

            if ($j == 0) {
                $bg = $GLOBALS["bgcolor2"];
                $j++;
            } else {
                $bg = $GLOBALS["bgcolor1"];
                $j = 0;
            }
            ?>
            <tr style="background:<?php echo $bg; ?>">
                <td style="width: 30%;">
                    <?php echo $title; ?>
                </td>
            <?php

            $sql3 = mysql_query("
                SELECT titre
                FROM ".NEWS_CAT_TABLE."
                WHERE nid = '".$cat."'
            ");

            list($categorie) = mysql_fetch_array($sql3);
            $categorie = nkHtmlEntities($categorie);
            ?>
                <td style="width: 20%;" align="center">
                    <i><?php echo $categorie; ?></i>
                </td>
                <td style="width: 25%;" align="center">
                    <?php echo $date; ?></td>
                <td style="width: 15%;" align="center">
                    <a href="index.php?file=Members&amp;op=detail&amp;autor=<?php echo urlencode($auteur); ?>" title="<?php echo _DETAILAUTHOR; ?> <?php echo $auteur; ?>">
                        <?php echo $auteur; ?>
                    </a>
                </td>
                <td style="width: 10%;" align=\"center">
                    <a href="index.php?file=News&amp;nuked_nude=index&amp;op=pdf&amp;news_id=<?php echo $news_id; ?>" onclick="window.open(this.href); return false;">
                        <img style="border: 0;" src="images/pdf.gif" alt="" title="<?php echo _PDF; ?>">
                    </a>
                    <a href="index.php?file=News&amp;op=sendfriend&amp;news_id=<?php echo $news_id; ?>">
                        <img style="border: 0;" src="images/friend.gif" alt="" title="<?php echo _FSEND; ?>">
                    </a>
                </td>
            </tr>
            <?php
        endwhile;

        if ($count == 0):
            ?>
            <tr><td colspan="5" align="center"><?php echo _NONEWS; ?></td></tr>
            <?php
        endif;
        ?>
        </table>
        <?php
        if ($count > $nb_news) {
            $url_page = "index.php?file=Archives&amp;op=sujet&amp;cat_id=<?php echo $cat_id; ?>&amp;orderby=".$_REQUEST['orderby'];
            number($count, $nb_news, $url_page);
        }
        ?>
        <div style="text-align: center;margin:10px auto;">
            <small>
                <i>( <?php echo _THEREIS; ?> <?php echo $count; ?> <?php echo _NEWS; ?> <?php echo _INDATABASE; ?>)</i>
            </small>
        </div>
        <?php
    }

    switch ($_REQUEST['op'])
    {
        case"index":
            index();
            break;

        case"sujet":
            sujet($_REQUEST['cat_id']);
            break;

        default:
            index();
            break;
    }
}

else if ($level_access == -1):
    ?>
    <div style="text-align: center;margin:20px auto;">
        <p><?php echo _MODULEOFF; ?></p>
        <p>
            <a href="javascript:history.back()">
                <strong><?php echo _BACK; ?></strong>
            </a>
        </p>
    </div>
    <?php
else if ($level_access == 1 && $visiteur == 0):
    ?>
    <div style="text-align: center;margin:20px auto;">
        <p><?php echo _USERENTRANCE; ?></p>
        <p> <strong>
            <a href="index.php?file=User&amp;op=login_screen">
                <?php echo _LOGINUSER; ?>
            </a> |
            <a href="index.php?file=User&amp;op=reg_screen">
                <?php echo _REGISTERUSER; ?></a>
            </strong>
        </p>
    </div>
    <?php
} else:
    ?>
    <div style="text-align: center;margin:20px auto;">
        <p><?php echo _NOENTRANCE; ?></p>
        <p>
            <a href="javascript:history.back()">
                <strong><?php echo _BACK; ?></strong>
            </a>
        </p>
    </div>
    <?php
endif;

CloseTable();

?>
