<?php
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $nuked, $user, $language, $cookie_forum;

translate("modules/Forum/lang/" . $language . ".lang.php");

opentable();

if (!$user) {
    $visiteur = 0;
}
else {
    $visiteur = $user[1];
}
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1) {
    $nb_mess_for = $nuked['thread_forum_page'];

    if ($_REQUEST['date_max'] != "") {
        $date_jour = time();
        $date_select = $date_jour - $_REQUEST['date_max'];
    }

    if ($_REQUEST['date_max'] != "") {
        $sql2 = mysql_query("SELECT forum_id FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND date > '" . $date_select . "' ORDER BY last_post DESC");
    }
    else {
        $sql2 = mysql_query("SELECT forum_id FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' ORDER BY last_post DESC");
    }

    $count = mysql_num_rows($sql2);

    $p = !$_GET['p']?1:$_GET['p'];
    $start = $p * $nb_mess_for - $nb_mess_for;

    $sql = mysql_query("SELECT nom, comment, moderateurs, image, cat, level FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
    $level_ok = mysql_num_rows($sql);

    if ($level_ok == 0) {
        echo '<div id="nkAlertError" class="nkAlert">' . _NOACCESSFORUM . '</div>';
    }
    else {
        list($nom, $comment, $modos, $image, $cat, $level) = mysql_fetch_array($sql);
        $nom = printSecuTags($nom);

        $sql_cat = mysql_query("SELECT nom FROM " . FORUM_CAT_TABLE . " WHERE id = '" . $cat . "'");
        list($cat_name) = mysql_fetch_array($sql_cat);
        $cat_name = printSecuTags($cat_name);

        if ($modos != "") {
            $moderateurs = explode('|', $modos);
            for ($i = 0;$i < count($moderateurs);$i++) {
                if ($i > 0) $sep = ",&nbsp;";
                $sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $moderateurs[$i] . "'");
                list($modo_pseudo) = mysql_fetch_row($sql2);
                $modo .= $sep . $modo_pseudo;
            }
        }
        else {
            $modo = _NONE;
        }

        if ($user && $modos != "" && strpos($user[0], $modos)) {
            $administrator = 1;
        }
        else {
            $administrator = 0;
        }
    
    $category = '-> <a href="index.php?file=Forum&amp;cat='.$cat.'"><strong>'.$cat_name.'</strong></a>&nbsp;';
    $forum = '-> <strong>'.$nom.'</strong>&nbsp;';
    $nav = $category.$forum;

    //Initialisation de la couleur des catégories en fonction du bgcolor
?>
    <div id="nkForumWrapper">
        <div id="nkForumInfos">
<?php
            if($nuked['forum_image'] == "on" && $image != "") {
                echo '<img src="'.$image.'" alt="" />';
            }
?>
            <div>
                <h2><?php echo $nom; ?></h2>
                <p><?php echo $comment; ?></p>
                <div class="nkForumModos"><small><?php echo _MODO; ?> : <?php echo $modo; ?></small></div>
            </div>
        </div>
        <div id="nkForumBreadcrumb">
            <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM; ?></strong></a>&nbsp;<?php echo $nav; ?>
        </div>

<?php
        if ($level == 0 || $user[1] >= $level || $administrator == 1) {
?>
        <div id="nkForumPostNewTopic">
            <a class="nkButton icon add" href="index.php?file=Forum&amp;page=post&amp;forum_id=<?php echo $_REQUEST['forum_id']; ?>"><?php echo _NEWTOPIC; ?></a>
        </div>
<?php
        }
?>
        <div class="nkForumNavPage">
<?php
        if ($count > $nb_mess_for) {
            $url_page = "index.php?file=Forum&amp;page=viewforum&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;date_max=" . $_REQUEST['date_max'];
            number($count, $nb_mess_for, $url_page);
        }
?>
        </div><!-- @whitespace
     --><div id="nkForumUserActionLink">
<?php
            if($user){
?>
                <a id="nkForumMarkRead" href="index.php?file=Forum&amp;op=mark&amp;forum_id=<?php echo $_REQUEST['forum_id']; ?>"><?php echo _MARKSUBJECTREAD; ?></a>
<?php
            }
?>
        </div>
        <div class="nkForumCat">
            <div class="nkForumCatWrapper">
                <div class="nkForumCatHead nkBgColor3">
                    <div>
                        <div class="nkForumBlankCell"></div>
                        <div class="nkForumForumCell"><?php echo _SUBJECTS; ?></div>
                        <div class="nkForumStatsCell"><?php echo _STATS; ?></div>
                        <div class="nkForumDateCell"><?php echo _LASTPOST; ?></div>
                    </div>
                </div>
                <div class="nkForumCatContent nkBgColor2">
<?php
                if ($count == 0){
?>
                    <div>
                        <div class="nkForumIconCell nkBorderColor1"></div>
                        <div class="nkForumForumCell nkBorderColor1"><?php echo _NOPOSTFORUM; ?></div>
                        <div class="nkForumStatsCell nkBorderColor1"></div>
                        <div class="nkForumDateCell nkBorderColor1"></div>
                    </div>
<?php
                }

        if ($_REQUEST['date_max'] != "") {
            $sql3 = mysql_query("SELECT id, titre, auteur, view, closed, annonce, sondage FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND date > '" . $date_select . "' ORDER BY annonce DESC, last_post DESC LIMIT " . $start . ", " . $nb_mess_for."");
        }
        else {
            $sql3 = mysql_query("SELECT id, titre, date, auteur, auteur_id, view, closed, annonce, sondage FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' ORDER BY annonce DESC, last_post DESC LIMIT " . $start . ", " . $nb_mess_for."");
        }

        while (list($thread_id, $titre, $threadDate, $auteur, $auteur_id, $nb_read, $closed, $annonce, $sondage) = mysql_fetch_row($sql3)) {
            $sql8 = mysql_query("SELECT txt FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "' ORDER BY id LIMIT 0, 1");
            list($txt) = mysql_fetch_array($sql8);

            $auteur = nk_CSS($auteur);

            $txt = str_replace("\r", "", $txt);
            $txt = str_replace("\n", " ", $txt);

            $texte = strip_tags($txt);

            if (!preg_match("`[a-zA-Z0-9\?\.]`i", $texte)) {
                $texte = _NOTEXTRESUME;
            }

            if (strlen($texte) > 150) {
                $texte = substr($texte, 0, 150) . "...";
            }

            $texte = nkHtmlEntities($texte);
            $texte = nk_CSS($texte);

            $title = nkHtmlEntities(printSecuTags($titre));

            if (strlen($titre) > 30) {
                $titre_topic = '<a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $thread_id . '" title="' . printSecuTags($titre) . '">' . printSecuTags(substr($titre, 0, 30)) . '...</a>';
            }
            else {
                $titre_topic = '<a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $thread_id . '" title="' . printSecuTags($titre) . '">' . printSecuTags($titre) . '</a>';
            }

            $sql4 = mysql_query("SELECT file FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "'");
            $nb_rep = mysql_num_rows($sql4) - 1;

            $fichier_joint = 0;
            while (list($url_file) = mysql_fetch_row($sql4)) {
                if ($url_file != "") $fichier_joint++;
            }

            $sql6 = mysql_query("SELECT MAX(id) FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "'");
            $idmax = mysql_result($sql6, 0, "MAX(id)");

            $sql7 = mysql_query("SELECT id, date, auteur, auteur_id FROM " . FORUM_MESSAGES_TABLE . " WHERE id = '" . $idmax . "'");
            list($mess_id, $last_date, $last_auteur, $last_auteur_id) = mysql_fetch_array($sql7);
            $last_auteur = nk_CSS($last_auteur);

            if (strftime("%d %m %Y", time()) ==  strftime("%d %m %Y", $last_date)) $last_date = _FTODAY . "&nbsp;" . strftime("%H:%M", $last_date);
            else if (strftime("%d", $last_date) == (strftime("%d", time()) - 1) && strftime("%m %Y", time()) == strftime("%m %Y", $last_date)) $last_date = _FYESTERDAY . "&nbsp;" . strftime("%H:%M", $last_date);
            else $last_date = nkDate($last_date);
            
               if ($user) {
                    $visitx = mysql_query("SELECT user_id FROM " . FORUM_READ_TABLE . " WHERE user_id = '" . $user[0] . "' AND `thread_id` LIKE '%" . ',' . $thread_id . ',' . "%' ");
                    $results = mysql_num_rows($visitx);
                         $user_visitx = $results;
               } else {
                $user_visitx = 0;
            }
            if ($user && $closed == 1 && ($user_visitx == 0)) {
                $img = 'nkForumNewTopicLock';
            }
            else if ($closed == 1) {
                $img = 'nkForumTopicLock';
            }
            else if ($user && $nb_rep >= $nuked['hot_topic'] && ($user_visitx == 0)) {
                $img = 'nkForumNewTopicPopular';
            }
            else if ($user && ($user_visitx >= 0) && $nb_rep >= $nuked['hot_topic']) {
                $img = 'nkForumTopicPopular';
                $labelHot = '<span class="nkForumLabels nkForumOrangeColor">' . _HOT . '</span>';
            }
            else if ($user && ($user_visitx == 0) && $nb_rep < $nuked['hot_topic']) {
                $img = 'nkForumNewTopic';
            }
            else {
                $img = '';
            }


            if ($annonce == 1) {
                if ($nuked['forum_labels_active'] == 'on') {
                    $a_img = '<span class="nkForumLabels nkForumOrangeColor">' . _ANNOUNCE . '</span>&nbsp;';    
                }
                else {
                    $a_img = '<img src="modules/Forum/images/announce.png" class="nkForumAlignImg" alt="" title="' . _ANNOUNCE . '" />&nbsp;';   
                }
                
            }
            else {
                $a_img = "";
            }

            if ($sondage == 1) {
                if ($nuked['forum_labels_active'] == 'on') {
                    $s_img = '<span class="nkForumLabels nkForumGreenColor">' . _SURVEY . '</span>&nbsp;';
                }
                else {
                    $s_img = '<img src="modules/Forum/images/poll.png" class="nkForumAlignImg" alt="" title="' . _SURVEY . '" />&nbsp;';
                }
            }
            else {
                $s_img = "";
            }

            if ($fichier_joint > 0) {
                if ($nuked['forum_labels_active'] == 'on') {
                    $f_img = '<span class="nkForumLabels nkForumGreyColor">' . _ATTACHFILE . '</span>&nbsp;';
                }
                else {
                    $f_img = '<img src="modules/Forum/images/clip.png" class="nkForumAlignImg" alt="" title=' . _ATTACHFILE . ' (' . $fichier_joint . ')" />&nbsp;';
                }
            }
            else {
                $f_img = "";
            }

            $title = $a_img . $s_img . $f_img . $titre_topic;

            $posts = $nb_rep + 1;
            if ($posts > $nuked['mess_forum_page']) {
                $topicpages = $posts / $nuked['mess_forum_page'];
                $topicpages = ceil($topicpages);

                $link_post = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $thread_id . "&amp;p=" . $topicpages . "#" . $mess_id;

                for ($l = 1; $l <= $topicpages; $l++) {
                    $pagelinks .= " <a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $thread_id . "&amp;p=" . $l . "\" class=\"nkForumLinkMultipage2\">" . $l . "</a>";
                }

                $multipage2 = "<small>" . $pagelinks . "</small>";
                $pagelinks = "";
            }
            else {
                $multipage2 = "";
                $link_post = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $thread_id . "#" . $mess_id;
            }

            //Lien en image vers le message
            $postIconLink = '&nbsp;<a href="' . $link_post . '"><img style="border: 0;" src="modules/Forum/images/icon_latest_reply.png" class="nkForumAlignImg" alt="" title="' . _SEELASTPOST . '" /></a>';

            //On identifie l'auteur du message original
            if ($auteur_id != "") {
                $sql5 = mysql_query("SELECT pseudo, country FROM " . USER_TABLE . " WHERE id = '" . $auteur_id . "'");
                $test = mysql_num_rows($sql5);
                list($autor, $country) = mysql_fetch_array($sql5);

                if ($test > 0 && $autor != "") {
                    $initiat = "<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($autor) . "\"><b>" . $autor . "</b></a>";
                }
                else {
                    $initiat = "<strong>" . $auteur . "</strong>";
                }
            }
            else {
                $initiat = "<strong>" . $auteur . "</strong>";
            }

            //On identifie le dernier posteur
            if ($last_auteur_id != "") {
                $sql8 = mysql_query("SELECT pseudo, country, avatar, rang FROM " . USER_TABLE . " WHERE id = '" . $last_auteur_id . "'");
                $test1 = mysql_num_rows($sql8);
                list($last_autor, $last_country, $avatar, $autorRank) = mysql_fetch_array($sql8);

                //Rank color dernier posteur
                if ($nuked['forum_user_details'] == "on") { 
                    $sqlRankTeamAutor = mysql_query("SELECT couleur FROM " . TEAM_RANK_TABLE . " WHERE id = '" . $autorRank . "'");
                    list($colorRankTeamAutor) = mysql_fetch_array($sqlRankTeamAutor);
                    $rankColorAutor = 'style="color: #' . $colorRankTeamAutor . ';"'; 
                } 
                else {
                    $rankColorAutor = "";
                }

                if ($test1 > 0 && $last_autor != "") {
                   $threadLastMsgAuthor = '<a href="index.php?file=Members&amp;op=detail&amp;autor=' . $last_autor . '" ' . $rankColorAutor . '>' . $last_autor . '</a>';
                }
                else {
                    $threadLastMsgAuthor = $last_auteur;
                }
            }
            else {
                $threadLastMsgAuthor = $last_auteur;
            }

            //On remplace l'avatar vide par le noAvatar
            if ($avatar != "") {
                $lastAuthorAvatar = $avatar;
            }
            else {
                $lastAuthorAvatar = 'modules/Forum/images/noAvatar.png';
            }

//Affichage des forums
?>
                    <div>
                        <div class="nkForumIconCell nkBorderColor1">
                            <span class="nkForumTopicIcon <?php echo $img; ?>"></span>
                        </div>
                        <div class="nkForumForumCell nkBorderColor1">
                                <h3><?php echo $title; ?></h3>
                            <div>
                                <span>
                                    <?php echo _CREATEDBY; ?>
                                    <?php echo $initiat; ?>
                                    <?php echo _THE.'&nbsp;'.nkdate($threadDate) . $multipage2; ?>
                                </span>
                            </div>
                        </div>
                        <div class="nkForumStatsCell nkBorderColor1">
                            <strong><?php echo $nb_rep; ?></strong>&nbsp;<?php echo strtolower(_ANSWERS); ?>
                            <br/>
                            <strong><?php echo $nb_read; ?></strong>&nbsp;<?php echo strtolower(_VIEWS); ?>
                        </div>
                        <div class="nkForumDateCell nkBorderColor1">
                            <div class="nkForumAuthorAvatar">
                                <img src="<?php echo $lastAuthorAvatar; ?>" alt="" />
                            </div>
                            <div>
                                <p>
                                    <span><?php echo _BY; ?></span>
                                    <strong><?php echo $threadLastMsgAuthor . $postIconLink; ?></strong>
                                </p>
                                <p><?php echo $last_date; ?></p>
                            </div>
                        </div>
                    </div>
<?php
        }
?>
                </div>
            </div>
        </div>
        <div class="nkForumNavPage">
<?php
        if ($count > $nb_mess_for) {
            $url_page = "index.php?file=Forum&amp;page=viewforum&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;date_max=" . $_REQUEST['date_max'];
            number($count, $nb_mess_for, $url_page);
        }
?>
        </div><!-- @whitespace
     --><div id="nkForumUserActionLink">
<?php
            if($user) {
?>
                <a id="nkForumMarkRead" href="index.php?file=Forum&amp;op=mark&amp;forum_id=<?php echo $_REQUEST['forum_id']; ?>"><?php echo _MARKSUBJECTREAD; ?></a>
<?php
            }
?>
        </div>
<?php
        if ($level == 0 || $user[1] >= $level || $administrator == 1) {
?>
        <div id="nkForumPostNewTopic">
            <a class="nkButton icon add" href="index.php?file=Forum&amp;page=post&amp;forum_id=<?php echo $_REQUEST['forum_id']; ?>"><?php echo _NEWTOPIC; ?></a>
        </div>
<?php
        }
?>
        <div class="nkForumViewLegend">
            <div class="nkForumNewTopicLegend"><?php echo _POSTNEW; ?></div>
            <div class="nkForumNoNewTopicLegend"><?php echo _NOPOSTNEW; ?></div>
            <div class="nkForumNewTopicLockLegend"><?php echo _POSTNEWCLOSE; ?></div>
        </div>
        <div class="nkForumViewLegend">
            <div class="nkForumTopicLockLegend"><?php echo _SUBJECTCLOSE; ?></div>
            <div class="nkForumNewTopicPopularLegend"><?php echo _POSTNEWHOT; ?></div>
            <div class="nkForumTopicPopularLegend"><?php echo _NOPOSTNEWHOT; ?></div>
        </div>
        <div class="nkForumQuickShotcuts">
            <form method="post" action="index.php?file=Forum&amp;page=viewforum">
                <div class="nkForumSelectTopics">
                    <?php echo _JUMPTO; ?> : 
                    <select name="forum_id" onchange="submit();">
                        <option value=""><?php echo _SELECTFORUM; ?></option>
<?php
                        $sql_forum = mysql_query("SELECT nom, id FROM " . FORUM_TABLE . " WHERE cat = '" . $cat . "' ORDER BY ordre, nom");
                        while (list($forum_name, $fid) = mysql_fetch_row($sql_forum)) {
                            $forum_name = printSecuTags($forum_name);

                            echo "<option value=\"" . $fid . "\">" . $forum_name . "</option>\n";
                        }
?>
                    </select>
                </div>
            </form>
            <form method="post" action="index.php?file=Forum&amp;page=viewforum&amp;forum_id=<?php echo  $_REQUEST['forum_id']; ?>">
                <div class="nkForumSelectDate">
                    <?php echo _SEETHETOPIC; ?> : 
                    <select name="date_max" onchange="submit();">
                        <option><?php echo _THEFIRST; ?></option>
                        <option value="86400"><?php echo _ONEDAY; ?></option>
                        <option value="604800"><?php echo _ONEWEEK; ?></option>
                        <option value="2592000"><?php echo _ONEMONTH; ?></option>
                        <option value="15552000"><?php echo _SIXMONTH; ?></option>
                        <option value="31104000"><?php echo _ONEYEAR; ?></option>
                    </select>
                </div>
            </form>
        </div>
<?php
    }
?>
    </div>
<?php
}
else if ($level_access == -1) {
    // On affiche le message qui previent l'utilisateur que le module est désactivé
    echo '<div id="nkAlertError" class="nkAlert">
            <strong>'._MODULEOFF.'</strong>
            <a href="javascript:history.back()"><span>'._BACK.'</span></a>
        </div>';
}
else if ($level_access == 1 && $visiteur == 0) {
    // On affiche le message qui previent l'utilisateur qu'il n'as pas accès à ce module
    echo '<div id="nkAlertError" class="nkAlert">
            <strong>'._USERENTRANCE.'</strong>
            <a href="index.php?file=User&amp;op=login_screen"><span>'._LOGINUSER.'</span></a>
            &nbsp;|&nbsp;
            <a href="index.php?file=User&amp;op=reg_screen"><span>'._REGISTERUSER.'</span></a>
        </div>';
}
else {
    // On affiche le message qui previent l'utilisateur que le module est désactivé
    echo '<div id="nkAlertError" class="nkAlert">
            <strong>'._NOENTRANCE.'</strong>
            <a href="javascript:history.back()"><span>'._BACK.'</span></a>
        </div>';
}

// Fermeture du conteneur de module
closetable();

?>
