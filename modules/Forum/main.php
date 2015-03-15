<?php 
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $user, $nuked, $language, $cookie_forum;


// On définit le niveau du visiteur
$visiteur = $user ? $user[1] : 0;
$user_last_visit = (empty($user[4])) ? time() : $user[4];

$date_jour = nkDate(time());
$your_last_visite = nkDate($user_last_visit);

    //On vérifie si le titre du forum
    if ($nuked['forum_title'] != "") {
        $titleForum = $nuked['forum_title'];
        $descForum = $nuked['forum_desc'];
    } 
    else {
        $titleForum = $nuked['name'];
        $descForum = $nuked['slogan'];
    }

    //Recherche des catégories du forum
    if ($_REQUEST['cat'] != "") {
        $sql_cat = mysql_query("SELECT nom FROM " . FORUM_CAT_TABLE . " WHERE id = '" . $_REQUEST['cat'] . "'");
        list($cat_name) = mysql_fetch_row($sql_cat);
        $cat_name = printSecuTags($cat_name);
        $nav = '-> <strong>' . $cat_name . '</strong>';    
    }

    //Date de la dernière visite
    if ($user && $user[4] != "") {
        $textLastVisit = _LASTVISIT.' : '.$your_last_visite;
    } 

    // ------------------------------
    // ENTETE DU MAIN
    // ------------------------------
?>
        <div id="nkForumWrapper">
            <div id="nkForumHeader">
                <h1>Forums <?php echo $titleForum; ?></h1>
                <p><?php echo $descForum; ?></p>
            </div><!-- Hack inline-block
            --><div id="nkForumMainSearch">
                <form method="get" action="index.php" >
                    <label for="forumSearch"><?php echo _SEARCH; ?> :</label>
                    <input id="forumSearch" type="text" name="query" size="25" />
                    <p>
                        [ <a href="index.php?file=Forum&amp;page=search"><?php echo _ADVANCEDSEARCH; ?></a> ]
                    </p>
                    <input type="hidden" name="file" value="Forum" />
                    <input type="hidden" name="page" value="search" />
                    <input type="hidden" name="do" value="search" />
                    <input type="hidden" name="into" value="all" />
                </form>
            </div>
            <div class="nkForumMainBreadcrumb">
                <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM; ?></strong></a>&nbsp;<?php echo $nav; ?>
            </div><!-- Hack inline-block
            --><div id="nkForumMainDates">
                <span><?php echo _DAYIS; ?> : <?php echo $date_jour; ?></span>&nbsp;<span><?php echo $textLastVisit; ?></span>
            </div>

<?php
    if ($_REQUEST['cat'] != "") {
        $main = mysql_query("SELECT nom, id, image FROM " . FORUM_CAT_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['cat'] . "'");
    } 
    else {
        $main = mysql_query("SELECT nom, id, image FROM " . FORUM_CAT_TABLE . " WHERE " . $visiteur . " >= niveau ORDER BY ordre, nom");
    } 

    while (list($nom_cat, $cid, $catImage) = mysql_fetch_row($main)) {
        $nom_cat = printSecuTags($nom_cat);
        $catLink = 'index.php?file=Forum&amp;cat='.$cid;

    // ------------------------------
    // CONTENU DES FORUMS
    // ------------------------------
?>
                <div class="nkForumCat">
                    <div class="nkForumCatNameCell">
<?php
                    if ($nuked['forum_cat_image'] == "on" && $catImage != "") {
?>
                        <a href="<?php echo $catLink; ?>">
                            <img src="<?php echo $catImage; ?>" title="<?php echo $nom_cat; ?>" class="nkForumCatImage"/>
                        </a>
<?php
                    }
                    else {
?>
                        <h2><a href="<?php echo $catLink; ?>"><?php echo $nom_cat; ?></a></h2>
<?php                       
                    }
?>
                    </div>
                    <div class="nkForumCatWrapper">
                        <div class="nkForumCatHead nkBgColor3">
                            <div>
                                <div class="nkForumBlankCell"></div>
                                <div class="nkForumForumCell"><?php echo _FORUM; ?></div>
                                <div class="nkForumStatsCell"><?php echo _STATS; ?></div>
                                <div class="nkForumDateCell"><?php echo _LASTPOST; ?></div>
                            </div>
                        </div>
                        <div class="nkForumCatContent nkBgColor2">

<?php

        $sql = mysql_query("SELECT nom, comment, image, id, moderateurs from " . FORUM_TABLE . " WHERE cat = '" . $cid . "' AND '" . $visiteur . "' >= niveau ORDER BY ordre, nom");
        while (list($nom, $comment, $forumImage, $forum_id, $modos) = mysql_fetch_row($sql)) {

            $nom = printSecuTags($nom);

            //Modérateurs
            if ($modos != "" )
            {
                $moderateurs = explode('|', $modos);
                $lienmodo = null;
                $sep = "";
                for ($i = 0;$i < count($moderateurs) ;$i++)
                {
                    if ($i > 0) $sep = ",&nbsp;";
                    $typoModo = _MODOS;
                    $sqlModoName = mysql_query("SELECT pseudo, rang FROM " . USER_TABLE . " WHERE id = '" . $moderateurs[$i] . "'");
                    list($modo_pseudo, $modoRank) = mysql_fetch_row($sqlModoName);
                    

                    if ($nuked['forum_user_details'] == "on")
                    { 
                    $sql_rank_team_modo = mysql_query("SELECT couleur FROM " . TEAM_RANK_TABLE . " WHERE id = '" . $modoRank . "'");
                    list($theModoColor) = mysql_fetch_array($sql_rank_team_modo);
                    $rank_color_modo = "style=\"color: #" . $theModoColor . ";\"";
                    } else {$rank_color_modo = "";}


                    $modo_link= "<a href=\"index.php?file=Members&op=detail&autor=" . $modo_pseudo . "\" alt=\"" . _SEEMODO . "" . $modo_pseudo . "\" title=\"" . _SEEMODO . "" . $modo_pseudo . "\" " . $rank_color_modo . "><b>" . $modo_pseudo . "</b></a>";
                    $lienmodo .= $sep . $modo_link;
                }
            }
            else
            {
                $typoModo = _MODO;
                $lienmodo = _NONE;
            }
            //Fin modérateurs

            $req2 = mysql_query("SELECT forum_id from " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $forum_id . "'");
            $num_post = mysql_num_rows($req2);

            $req3 = mysql_query("SELECT forum_id from " . FORUM_MESSAGES_TABLE . " WHERE forum_id = '" . $forum_id . "'");
            $num_mess = mysql_num_rows($req3);

            $req4 = mysql_query("SELECT MAX(id) from " . FORUM_MESSAGES_TABLE . " WHERE forum_id = '" . $forum_id . "'");
            $idmax = mysql_result($req4, 0, "MAX(id)");

            //Vériofication si le message est lu/non lu
            $req5 = mysql_query("SELECT id, titre, thread_id, date, auteur, auteur_id FROM " . FORUM_MESSAGES_TABLE . " WHERE id = '" . $idmax . "'");
            list($mess_id, $topicTitle, $thid, $date, $auteur, $auteur_id) = mysql_fetch_array($req5);
            $auteur = nk_CSS($auteur);

            if ($user) {
                $visits = mysql_query("SELECT user_id, forum_id FROM " . FORUM_READ_TABLE . " WHERE user_id = '" . $user[0] . "' AND forum_id LIKE '%" . ',' . $forum_id . ',' . "%' ");
                $results = mysql_fetch_assoc($visits);
                
                if ($num_post > 0 && strrpos($results['forum_id'], ',' . $forum_id . ',') === false) {
                    $img = 'modules/Forum/images/forum_new.png';
                } 
                else {
                    $img = 'modules/Forum/images/forum.png';
                } 
            } 
            else {
                $img = 'modules/Forum/images/forum.png';
            }

            //Detection image forum
            if($nuked['forum_image'] == "on" && $forumImage != '') {
                $classImage = '<img src="' .$forumImage. '" class="nkForumNameCellImage" alt="" title="' .$nom. '" />';
            }
            else {
                $classImage = null;
            }

            //Lien vers le Forum
            $linkForum = 'index.php?file=Forum&amp;page=viewforum&amp;forum_id=' . $forum_id . '';

            //Construction du lien vers le post
            $sql_page = mysql_query("SELECT thread_id FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thid . "'");
            $nb_rep = mysql_num_rows($sql_page);

            if ($nb_rep > $nuked['mess_forum_page']) {
                $topicpages = $nb_rep / $nuked['mess_forum_page'];
                $topicpages = ceil($topicpages);
                $link_post = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $forum_id . "&amp;thread_id=" . $thid . "&amp;p=" . $topicpages . "#" . $mess_id;
            } 
            else {
                $link_post = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $forum_id . "&amp;thread_id=" . $thid . "#" . $mess_id;
            }

?>
                            <div>
                                <div class="nkForumIconCell nkBorderColor1">
                                    <img src="<?php echo $img; ?>" alt="" />
                                </div>
                                <div id="nkForumNameCell_" class="nkForumNameCell  nkBorderColor1"><?php echo $classImage; ?>
                                    <h3><a href="<?php echo $linkForum; ?>"><?php echo $nom; ?></a></h3>
                                    <p><?php echo $comment; ?></p>
<?php
                                if ($nuked['forum_display_modos'] == "on") {
?>
                                    <p><small><?php echo $typoModo; ?>:&nbsp;<?php echo $lienmodo; ?></small></p>
<?php
                                }
?>
                                </div>
                                <div class="nkForumStatsCell nkBorderColor1">
                                    <strong><?php echo $num_post; ?></strong>&nbsp;<?php echo strtolower(_TOPICS); ?>
                                    <br/>
                                    <strong><?php echo $num_mess; ?></strong>&nbsp;<?php echo strtolower(_MESSAGES); ?>
                                </div>
<?php
            if ($num_mess > 0) {
                if ($auteur_id != "") {
                    $sq_user = mysql_query("SELECT pseudo, avatar, country, rang FROM " . USER_TABLE . " WHERE id = '" . $auteur_id . "'");
                    $test = mysql_num_rows($sq_user);
                    list($author, $avatar, $country, $autorRank) = mysql_fetch_array($sq_user);

                    //Rank color dernier posteur
                    if ($nuked['forum_user_details'] == "on") { 
                        $sqlRankTeamAutor = mysql_query("SELECT couleur FROM " . TEAM_RANK_TABLE . " WHERE id = '" . $autorRank . "'");
                        list($colorRankTeamAutor) = mysql_fetch_array($sqlRankTeamAutor);
                        $rankColorAutor = 'style="color: #' . $colorRankTeamAutor . ';"'; 
                    } 
                    else {
                        $rankColorAutor = "";
                    }                    

                    if ($test > 0 && $author != "") {
                        $autor = $author;
                    } 
                    else {
                        $autor = $auteur;
                    }

                    //On remplace l'avatar vide par le noAvatar
                    if ($avatar != "") {
                        $lastAuthorAvatar = $avatar;
                    }
                    else {
                        $lastAuthorAvatar = 'modules/Forum/images/noAvatar.png';
                    }
                } 
                else {
                    $autor = $auteur;
                    $lastAuthorAvatar = 'modules/Forum/images/noAvatar.png';
                } 

                //Formatagee de la date
                if (strftime("%d %m %Y", time()) ==  strftime("%d %m %Y", $date)) $date = _FTODAY . "&nbsp;" . strftime("%H:%M", $date);
                else if (strftime("%d", $date) == (strftime("%d", time()) - 1) && strftime("%m %Y", time()) == strftime("%m %Y", $date)) $date = _FYESTERDAY . "&nbsp;" . strftime("%H:%M", $date);    
                else $date = nkDate($date);

                if ($auteur_id != "") {
                    $lastMsgAuthor = '<a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($autor) . '" ' . $rankColorAutor . '>' . $autor . '</a>';
                } 
                else {
                    $lastMsgAuthor = $autor;
                } 

                //Lien en image vers le message
                $cleanTopicTitle = str_replace('RE : ', '', $topicTitle);

                if (strlen($cleanTopicTitle) > 20) {
                    $cleanTopicTitle = substr($cleanTopicTitle, 0, 20) . "...";
                }

                $lastPostTopicTitle = '<a href="' . $link_post . '" title="' . $topicTitle . '"><img style="border: 0;" src="modules/Forum/images/icon_latest_reply.png" class="nkForumAlignImg" alt="" title="' . _SEELASTPOST . '" />&nbsp;' . $cleanTopicTitle . '</a>';

?>
                                <div class="nkForumDateCell nkBorderColor1">
                                    <div class="nkForumAuthorAvatar">
                                        <img src="<?php echo $lastAuthorAvatar; ?>" alt="IMG" />
                                    </div>
                                    <div>
                                        <p>
                                            <?php echo $lastPostTopicTitle; ?>
                                        </p>
                                        <p>
                                            <span><?php echo _BY; ?></span>
                                            <strong><?php echo $lastMsgAuthor; ?></strong>
                                        </p>
                                        <p><?php echo $date; ?></p>
                                    </div>
                                </div>
<?php
            } 
            else {
?>
                                <div class="nkForumDateCell  nkBorderColor1">
                                    <?php echo _NOPOST; ?>
                                </div>
<?php
            } 
?>
                            </div>
<?php
        }
?>
                        </div>
                    </div>
                </div>
<?php
    }
    // ------------------------------
    // LEGENDE DU MAIN
    // ------------------------------
 
    $nb = nbvisiteur();

    $sqlTotalMessages = mysql_query("SELECT id FROM " . FORUM_MESSAGES_TABLE . " ");
    $nbTotalMessages = mysql_num_rows($sqlTotalMessages);
    
    $sqlTotalUsers = mysql_query("SELECT id FROM " . USER_TABLE . " ");
    $nbTotalUsers = mysql_num_rows($sqlTotalUsers);
    
    $sqlLastUser = mysql_query("SELECT pseudo FROM " . USER_TABLE . " ORDER BY date DESC LIMIT 1");
    list($lastUser) = mysql_fetch_array($sqlLastUser);

    $nbTotalUsersOnline = _THEREARE."&nbsp;".$nb[0]."&nbsp;"._FVISITORS.", ".$nb[1]."&nbsp;"._FMEMBERS."&nbsp;"._AND."&nbsp;".$nb[2]."&nbsp;"._FADMINISTRATORS."&nbsp;"._ONLINE."<br />"._MEMBERSONLINE." : ";

?>
                <div id="nkForumWhoIsOnline" class="nkBgColor2">
                    <div class="nkForumWhoIsOnlineTitle nkBgColor3">
                        <h3><?php echo _FWHOISONLINE; ?></h3>
                    </div>
                    <div id="nkForumWhoIsOnlineIcon" class="nkBorderColor1"></div>
                    <div id="nkForumWhoIsOnlineContent" class="nkBorderColor1">
                        <p><?php echo _TOTAL_MEMBERS_POSTS.'<strong>'.$nbTotalMessages.'</strong>&nbsp;'.strtolower(_MESSAGES).'.'; ?></p>
                        <p><?php echo _WE_HAVE.'<strong>'.$nbTotalUsers.'</strong>'._REGISTERED_MEMBERS; ?></p>
                        <p><?php echo _LAST_USER_IS.'<a href="index.php?file=Members&op=detail&autor='.$lastUser.'">'.$lastUser.'</a>'; ?></p>
                        <p><?php echo $nbTotalUsersOnline; ?>
<?php
                        //Membres en Ligne
                        $i = 0;
                        $usersOnline = mysql_query("SELECT username FROM " . NBCONNECTE_TABLE . " WHERE type > 0 ORDER BY date");
                        while (list($userName) = mysql_fetch_row($usersOnline)) {
                            $i++;
                            if ($i == $nb[3]) {
                                $sep = "";
                            } 
                            else {
                                $sep = ", ";
                            } 

                        $sqlUserDetails = mysql_query("SELECT pseudo, country FROM " . USER_TABLE . " WHERE pseudo = '" . $userName . "'");
                        while (list($userPseudo, $userCountry) = mysql_fetch_array($sqlUserDetails)) {
                            echo '<img src="images/flags/' . $userCountry .'" alt="' . $userCountry . '" class="nkForumOnlineFlag" />';
                        }
                            if ($nuked['forum_user_details'] == "on") {   
                                $sqlUser = mysql_query("SELECT rang FROM " . USER_TABLE . " WHERE pseudo = '" . $userName . "'");
                                list($userRank) = mysql_fetch_array($sqlUser);

                                $sqlRankTeamOnline = mysql_query("SELECT couleur FROM " . TEAM_RANK_TABLE . " WHERE id = '" . $userRank . "'");
                                list($rankColorOnline) = mysql_fetch_array($sqlRankTeamOnline);
                                $rankColorStyleOnline = 'style="color: #' . $rankColorOnline . ';"';
                            }
                            else {
                                $rankColorStyleOnline = '';
                            }

                            echo '<a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($userName) . '" ' . $rankColorStyleOnline . '>' . $userName . '</a>' . $sep;
                        }

                        if (mysql_num_rows($usersOnline) == NULL) echo '<em>' . _NONE . '</em>';

?>                      </p>
<?php
                        //Légende des rangs
                        if ($nuked['forum_user_details'] == "on") {
                            echo '<br /><p>' . _RANKLEGEND . '&nbsp;:&nbsp;';
                        
                        $sqlRankTeamLegend = mysql_query("SELECT titre, couleur FROM " . TEAM_RANK_TABLE . " WHERE id > 0 ORDER BY ordre LIMIT 0, 20");
                        $nbRank=mysql_num_rows($sqlRankTeamLegend);
                        $i = -$nbRank;
                        while (list($rankTitleLegend, $rankColorLegend) = mysql_fetch_array($sqlRankTeamLegend)) {
                            $i++;
                            if ($i == 0) {
                                $sep = "";
                            } 
                            else {
                                $sep = " | ";
                            }                          
                              $rankColorStyleLegend = 'style="color: #' . $rankColorLegend . ';"';
                              echo '<span ' . $rankColorStyleLegend . '><strong>' . $rankTitleLegend . '</strong></span>' . $sep;
                            }
                            echo '</p>';
                        }

                        //Les anniversaires
                        if ($nuked['forum_birthday'] == "on"){
                            $currentYear = date("Y");
                            $currentMonth = date("m");
                            $currentDay = date("d");
                              
                            if ($currentDay < 10){$currentDay = str_replace("0", "", $currentDay);}
                            if ($currentMonth < 10){$currentMonth = str_replace("0", "", $currentMonth);}
                              
                            $sqlBirthdayAge = mysql_query("SELECT age FROM " . USER_DETAIL_TABLE . " WHERE age LIKE '%$d/$m%'");
                            $nbBirthday = mysql_num_rows($sqlBirthdayAge);
                              
                                while (list($birthdayDate) = mysql_fetch_array($sqlBirthdayAge)) {
                                    list ($bDDay, $bDMonth, $bDDay) =  split ('[/]', $birthdayDate);
                                    
                                    if ($currentDay != $bDDay || $currentMonth != $bDMonth) {       
                                    $nbBirthday = $nbBirthday - 1;
                                    }
                                }     
                                echo '<p>' . _TODAY . ', ';
                                if ($nbBirthday == 0)
                                echo _NOBIRTHDAY;
                                elseif ($nbBirthday == 1)
                                echo _ONEBIRTHDAY . '&nbsp;';
                                else
                                echo _THEREARE2 .'&nbsp;'. $nbBirthday .'&nbsp;'. _MANYBIRTHDAY . '&nbsp;';

  
                            $a = 0;
                            $sqlBirthdayUser = mysql_query("SELECT user_id, age, pseudo, rang FROM " . USER_DETAIL_TABLE . " INNER JOIN " . USER_TABLE . " ON user_id = id WHERE niveau > 0 ");
                            while (list($anivid, $birthDay, $userBirthday, $userBirthdayRank) = mysql_fetch_array($sqlBirthdayUser)) {

                            list ($bDay, $bMonth, $bYear) = split ('[/]', $birthDay);
                                $age = $currentYear - $bYear;
                                if ($currentMonth < $bMonth) {
                                    $age = $age - 1;
                                } 
                                if ($currentDay < $bDay && $currentMonth == $bMonth) {
                                    $age = $age - 1;
                                } 

                                if ($currentDay == $bDay && $currentMonth == $bMonth) {

                                    $userBirthday = stripslashes($userBirthday);

                                    $a++;
                                    if ($a != $nbBirthday) {
                                        $virg = ", ";
                                    }
                                    else {
                                        $virg = " ";
                                    }

                                    if ($nuked['forum_user_details'] == "on") {

                                        $sqlRankTeamBirthday = mysql_query("SELECT couleur FROM " . TEAM_RANK_TABLE . " WHERE id = '" . $userBirthdayRank . "'");

                                        list($userRankColorBirthday) = mysql_fetch_array($sqlRankTeamBirthday);
                                        $userRankColorBirthStyle = 'style="color: #' . $userRankColorBirthday . ';"';
                                    }
                                    else {
                                        $userRankColorBirthStyle = "";
                                    }
                                    echo '<a href="index.php?file=Members&amp;op=detail&amp;autor=' . $userBirthday . '" ' . $userRankColorBirthStyle . '><strong>' . $userBirthday . '</strong></a> (' . $age . ' ' . _ANS . ')' . $virg;
                                }
                            }
                            echo '</p>';
                        }       

?>
                    </div>
                </div>
                <div class="nkForumNavPage"></div><!-- @whitespace
             --><div id="nkForumUserActionLink">
<?php
                if($user){
?>
                    <a id="nkForumMarkRead" href="index.php?file=Forum&amp;op=mark"><?php echo _MARKREAD; ?></a>
<?php
                    if ($user && $user[4] != "") {
?>
                    <a id="nkForumViewUnread" href="index.php?file=Forum&amp;page=search&amp;do=search&amp;date_max=<?php echo $user[4]; ?>"><?php echo _VIEWLASTVISITMESS; ?></a>
<?php
                    }
                }
?>
                </div>
                <div id="nkForumReadLegend">
                    <div>
                        <img src="modules/Forum/images/forum_new.png" alt="NEW" />
                        <span><?php echo _NEWSPOSTLASTVISIT; ?></span>
                    </div>
                    <div>
                        <img src="modules/Forum/images/forum.png" alt="" />
                        <span><?php echo _NOPOSTLASTVISIT; ?></span>
                    </div>
                </div>
        </div>
<?php
    // ------------------------------
    // FIN DU MAIN
    // ------------------------------
?>