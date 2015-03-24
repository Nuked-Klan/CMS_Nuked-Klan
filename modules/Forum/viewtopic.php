<?php 
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die;

global $nuked, $user, $language, $theme;

translate("modules/Forum/lang/" . $language . ".lang.php");
include("modules/Forum/template.php");

opentable();

$visiteur = $user ? $user[1] : 0;

$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1) {
    $nb_mess_for_mess = $nuked['mess_forum_page'];

    $sql = mysql_query("SELECT nom, moderateurs, cat, level FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
    $level_ok = mysql_num_rows($sql);

    $sql2 = mysql_query("SELECT titre, view, closed, annonce, last_post, auteur_id, sondage FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND id = '" . $_REQUEST['thread_id'] . "'");
    $topic_ok = mysql_num_rows($sql2);

        // No user access
        if ($level_ok == 0) {
          echo '<div id="nkAlertError" class="nkAlert"><strong>' . _NOACCESSFORUM . '</strong></div>';
        }
        // No topic exists
        else if ($topic_ok == 0) {
          echo '<div id="nkAlertError" class="nkAlert"><strong>' . _NOTOPICEXIST . '</strong></div>';
        }
        // User access
        else {

        //Fonction message lu/non lu
        if ($user) {

                $SQL = "SELECT id FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = " . (int) $_GET['forum_id'] . " ";
                $req = mysql_query($SQL) or die(mysql_error());
                $thread_table = array();
                while ($res = mysql_fetch_assoc($req)) {
                    $thread_table[] = $res['id'];
            } 

                $visit = mysql_query("SELECT user_id, thread_id, forum_id FROM " . FORUM_READ_TABLE . " WHERE user_id = '" . $user[0] . "'") or die(mysql_error());
                $user_visit = mysql_fetch_assoc($visit);
                $tid = substr($user_visit['thread_id'], 1); // Thread ID
                $fid = substr($user_visit['forum_id'], 1); // Forum ID
                if (!$user_visit || strrpos($user_visit['thread_id'], ',' . $_GET['thread_id'] . ',') === false || strrpos($user_visit['forum_id'], ',' . $_GET['forum_id'] . ',') === false) {

                    if (strrpos($user_visit['thread_id'], ',' . $_GET['thread_id'] . ',') === false)
                        $tid .= $_GET['thread_id'] . ',';

                    $read = false;
                    foreach ($thread_table as $thread) {
                        if (strrpos(',' . $tid, ',' . $thread . ',') === false) {
                            $read = true;
                        }
                    } 

                    if (strrpos($user_visit['forum_id'], ',' . $_GET['forum_id'] . ',') === false && $read === false)
                         $fid .= $_GET['forum_id'] . ',';

                    // Insertion SQL du read
                    mysql_query("REPLACE INTO " . FORUM_READ_TABLE . " ( `user_id` , `thread_id` , `forum_id` ) VALUES ( '" . $user[0] . "' , '," . $tid . "' , '," . $fid . "' )") or die(mysql_error());
            } 
        }

        list($nom, $modos, $cat, $level) = mysql_fetch_array($sql);
        $nom = printSecuTags($nom);

        $sql_cat = mysql_query("SELECT nom FROM " . FORUM_CAT_TABLE . " WHERE id = '" . $cat . "'");
        list($cat_name) = mysql_fetch_array($sql_cat);
        $cat_name = printSecuTags($cat_name);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false) {
            $administrator = 1;
        } 
        else {
            $administrator = 0;
        } 

        list($titre, $read, $closed, $annonce, $lastpost, $topic_aid, $sondage) = mysql_fetch_array($sql2);
        $titre = printSecuTags($titre);
        $titre = nk_CSS($titre);

        $upd = mysql_query("UPDATE " . FORUM_THREADS_TABLE . " SET view = view + 1 WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND id = '" . $_REQUEST['thread_id'] . "'");

        $sql_next = mysql_query("SELECT id FROM " . FORUM_THREADS_TABLE . " WHERE last_post > '" . $lastpost. "' AND forum_id = '" . $_REQUEST['forum_id'] . "' ORDER BY last_post LIMIT 0, 1");
        list($nextid) = mysql_fetch_array($sql_next);

        if ($nextid != "") {
            $next = '<a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $nextid . '" class="nkButton icon arrowright">' . _NEXTTHREAD . '</a>';
        } 

        $sql_last = mysql_query("SELECT id FROM " . FORUM_THREADS_TABLE . " WHERE last_post < '" . $lastpost . "' AND forum_id = '" . $_REQUEST['forum_id'] . "' ORDER BY last_post DESC LIMIT 0, 1");
        list($lastid) = mysql_fetch_array($sql_last);

        if ($lastid != "") {
            $prev = '<a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $lastid . '" class="nkButton icon arrowleft">' . _LASTTHREAD . '</a>';
        }

        //Construction du Breadcrump
        $category = '-> <a href="index.php?file=Forum&amp;cat='.$cat.'"><strong>'.$cat_name.'</strong></a>&nbsp;';
        $topic = '-> <a href="index.php?file=Forum&amp;page=viewforum&amp;forum_id=' . $_REQUEST['forum_id'] . '"><strong>'.$nom.'</strong></a>&nbsp;';
        $nav = $category.$topic;


        //Détection du nombre de pages
        $sql3 = mysql_query("SELECT thread_id FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "'");
        $count = mysql_num_rows($sql3);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_mess_for_mess - $nb_mess_for_mess;

        if ($_REQUEST['highlight'] != "") {
            $url_page = 'index.php?file=Forum&amp;page=viewtopic&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '&amp;highlight=' . urlencode($_REQUEST['highlight']);
        } 
        else {
            $url_page = 'index.php?file=Forum&amp;page=viewtopic&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'];
        }

        //Boutons d'action utilisateur, remplacement automatique du bouton CSS par une image PNG si elle éxiste.
        if ((is_file('themes/' . $theme . '/images/newthread.png'))) {
            $postNewTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id=' . $_REQUEST['forum_id'] . '"><img style="border: 0;" src="themes/' . $theme . '/images/newthread.png" alt="" title="' . _NEWSTOPIC . '" /></a>';
        }
        else {
            $postNewTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id=' . $_REQUEST['forum_id'] . '" class="nkButton icon add">' . _NEWTOPIC . '</a>';
        }
        if ((is_file('themes/' . $theme . '/images/reply.png'))) {
            $replyToTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '"><img style="border: 0;" src="themes/' . $theme . '/images/reply.png" alt="" title="' . _REPLY . '" /></a>';
        }
        else {
            $replyToTopic = '<a href="index.php?file=Forum&amp;page=post&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '" class="nkButton icon chat">' . _REPLY . '</a>';
        }

?>

    <div id="nkForumWrapper">
        <div id="nkForumViewInfos">
            <div class="nkForumNavTopics"><?php echo $prev; ?><?php echo $next; ?></div>
            <a name="top"></a>
            <div>
                <h2><?php echo $titre; ?></h2>            
            </div>
        </div>
        <div id="nkForumBreadcrumb">
            <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM; ?></strong></a>&nbsp;<?php echo $nav; ?>
        </div>
        <div class="nkForumNavPage">
<?php
        if ($count > $nb_mess_for_mess) {
            number($count, $nb_mess_for_mess, $url_page);
        } 
?>
        </div><!-- @whitespace
     --><div id="nkForumPostOrReply">
            <div>
<?php
        if ($level == 0 || $visiteur >= $level || $administrator == 1) {
            echo $postNewTopic;

            if ($closed == 0 || $administrator == 1 || $visiteur >= admin_mod("Forum")) {
                echo $replyToTopic;
            } 
        } 
?>
            </div>
        </div>

        <div id="img_resize_forum" class="nkForumViewTopic">
            <!-- SONDAGE -->
<?php
        //Poll
        if ($sondage == 1) {

            $sql_poll = mysql_query("SELECT id, titre FROM " . FORUM_POLL_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "'");
            list($poll_id, $question) = mysql_fetch_array($sql_poll);
            $question = printSecuTags($question);
?>
            <div id="nkForumViewMainPoll" class="nkBorderColor1">
<?php

            $check = mysql_query("SELECT auteur_ip FROM " . FORUM_VOTE_TABLE . " WHERE poll_id = '" . $poll_id . "' AND auteur_id = '" . $user[0] . "'");
            $test = mysql_num_rows($check);

            if ($user && $test > 0 || $_REQUEST['vote'] == "view") {

?>
                <div class="nkForumViewPollBg"></div><!-- @whitespace
             --><div class="nkForumViewPoll">
                    <div class="nkForumPollTitle">
                        <h3><?php echo $question; ?></h3>
                    </div>
<?php
                    if ($user && $topic_aid == $user[0] && $closed == 0 || $visiteur >= admin_mod("Forum") || $administrator == 1) {
?>
                    <div class="nkForumViewActionLinks">
                        <div class="nkButton-group">
                            <a href="index.php?file=Forum&amp;op=edit_poll&amp;poll_id=<?php echo $poll_id; ?>&amp;forum_id=<?php echo $_REQUEST['forum_id']; ?>&amp;thread_id=<?php echo $_REQUEST['thread_id']; ?>" title="<?php echo _EDITPOLL; ?>" class="nkButton icon alone edit"></a>
                            <a href="index.php?file=Forum&amp;op=del_poll&amp;poll_id=<?php echo $poll_id; ?>&amp;forum_id=<?php echo $_REQUEST['forum_id']; ?>&amp;thread_id=<?php echo $_REQUEST['thread_id']; ?>" title="<?php echo _DELPOLL; ?>" class="nkButton icon alone remove danger"></a>
                        </div>
                    </div>
<?php
                    }

                $sql_options = mysql_query("SELECT option_vote FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "' ORDER BY id ASC");
                $nbcount = 0;

                while (list($option_vote) = mysql_fetch_array($sql_options)) {
                    $nbcount = $nbcount + $option_vote;
                } 

                $sql_res = mysql_query("SELECT option_vote, option_text FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "' ORDER BY id ASC");
                while (list($optioncount, $option_text) = mysql_fetch_array($sql_res)) {
                    $optiontext = printSecuTags($option_text);

                    if ($nbcount <> 0) {
                        $etat = ($optioncount * 100) / $nbcount ;
                    } 
                    else {
                        $etat = 0;
                    } 

                    $pourcent_arrondi = round($etat);

                    if ($etat < 1) {
                        $width = 2;
                    } 
                    else {
                        $width = $etat * 2;
                    } 

                    if (is_file("themes/" . $theme . "/images/bar.gif")) {
                        $img = "themes/" . $theme . "/images/bar.gif";
                    } 
                    else {
                        $img = "modules/Forum/images/bar.gif";
                    } 

                    $pollBarImg = '<img src="' . $img . '" width="' . $width . '" height="10" alt="" title="' . $pourcent_arrondi . '%" />&nbsp;' . $pourcent_arrondi . '% (' . $optioncount . ')';
?>
                    <div class="nkForumPollOptionsTxt"><?php echo $optiontext; ?></div>
                    <div class="nkForumPollImage"><?php echo $pollBarImg; ?></div>
<?php                
                } 
?>
                    <div class="nkForumPollStats">
                        <strong><?php echo _TOTALVOTE; ?>&nbsp;:</strong><?php echo $nbcount; ?>
                    </div>
                </div>
<?php
            } 
            else {
?>
                <div class="nkForumViewPollBg"></div><!-- @whitespace
             --><div class="nkForumViewPoll">
                    <form method="post" action="index.php?file=Forum&amp;op=vote&amp;poll_id=<?php echo $poll_id; ?>">
                        <div class="nkForumPollTitle">
                            <h3><?php echo $question; ?></h3>
                        </div>
<?php
                        if ($user && $topic_aid == $user[0] && $closed == 0 || $visiteur >= admin_mod("Forum") || $administrator == 1) {
?>
                        <div class="nkForumViewActionLinks">
                            <div class="nkButton-group">
                                <a href="index.php?file=Forum&amp;op=edit_poll&amp;poll_id=<?php echo $poll_id; ?>&amp;forum_id=<?php echo $_REQUEST['forum_id']; ?>&amp;thread_id=<?php echo $_REQUEST['thread_id']; ?>" title="<?php echo _EDITPOLL; ?>" class="nkButton icon alone edit"></a>
                                <a href="index.php?file=Forum&amp;op=del_poll&amp;poll_id=<?php echo $poll_id; ?>&amp;forum_id=<?php echo $_REQUEST['forum_id']; ?>&amp;thread_id=<?php echo $_REQUEST['thread_id']; ?>" title="<?php echo _DELPOLL; ?>" class="nkButton icon alone remove danger"></a>
                            </div>
                        </div>
<?php
                        }

                $sql_options = mysql_query("SELECT id, option_text FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "' ORDER BY id ASC");
                while (list($voteid, $optiontext) = mysql_fetch_array($sql_options)) {
                    $optiontext = printSecuTags($optiontext);
?>
                        <div class="nkForumPollOptions">
                            <input type="radio" class="checkbox" name="voteid" value="<?php echo $voteid; ?>" />&nbsp;
                            <span><?php echo $optiontext; ?></span>
                        </div>
<?php
                } 
?>
                        <div>
                            <input type="hidden" name="forum_id" value="<?php echo $_REQUEST['forum_id']; ?>" />
                            <input type="hidden" name="thread_id" value="<?php echo $_REQUEST['thread_id']; ?>" />
                        </div>
                        <div id="nkForumPollActionLinks">
                            <input type="submit" class="nkButton" value="<?php echo _TOVOTE; ?>" />
                            <input type="button" class="nkButton" value="<?php echo _RESULT; ?>" onclick="document.location='index.php?file=Forum&amp;page=viewtopic&amp;forum_id=<?php echo $_REQUEST['forum_id']; ?>&amp;thread_id=<?php echo $_REQUEST['thread_id']; ?>&amp;vote=view'" />
                        </div>
                    </form>
                </div>
<?php            
            } 
?>
            </div>
<?php
        }
        //Fin Poll

?>
            <div class="nkForumCatWrapper">
                <div id="forum-table" class="nkForumViewContent nkBorderColor1">
<?php

        $sql4 = mysql_query("SELECT id, titre, auteur, auteur_id, auteur_ip, txt, date, edition, usersig, file  FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "' ORDER BY date ASC limit " . $start . ", " . $nb_mess_for_mess."");
        while (list($mess_id, $title, $auteur, $auteur_id, $auteur_ip, $txt, $date, $edition, $usersig, $fichier) = mysql_fetch_row($sql4)) {

            $title = printSecuTags($title);            

            if ($_REQUEST['highlight'] != "") { 
                $string = trim($_REQUEST['highlight']);
                $string = printSecuTags($string);
                $title = str_replace($string, '<span style="color: #FF0000">' . $string . '</span>', $title);

                $search = explode(" ", $string);
                for($i = 0; $i < count($search); $i++) {
                    $tab = preg_split("`(<\w+.*?>)`", $txt, -1, PREG_SPLIT_DELIM_CAPTURE);

                    foreach ($tab as $key=>$val) {
                        if (preg_match("`^<\w+`", $val)) $tab[$key] = $val;
                        else $tab[$key] = preg_replace("/$search[$i]/","<span style=\"color: #FF0000;\"><b>$0</b></span>", $val);
                    }

                    $txt = implode($tab);
                } 
            }

            if (strftime("%d %m %Y", time()) ==  strftime("%d %m %Y", $date)) $date = _FTODAY . "&nbsp;" . strftime("%H:%M", $date);
            else if (strftime("%d", $date) == (strftime("%d", time()) - 1) && strftime("%m %Y", time()) == strftime("%m %Y", $date)) $date = _FYESTERDAY . "&nbsp;" . strftime("%H:%M", $date);    
            else $date = _THE . ' ' . nkDate($date);

            $tmpcnt++ % 2 == 1 ? $color = $color1 : $color = $color2;

            //Liens interface utilisateur/administrateurs
            $quoteLink  = 'index.php?file=Forum&amp;page=post&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '&amp;mess_id=' . $mess_id . '&amp;do=quote';
            $editLink   = 'index.php?file=Forum&amp;page=post&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;mess_id=' . $mess_id . '&amp;do=edit';
            $deleteLink = 'index.php?file=Forum&amp;op=del&amp;mess_id=' . $mess_id . '&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '';


            //On détermine si le visiteur est un administrateur et on lui affiche l'IP du posteur
            if ($visiteur >= admin_mod("Forum") || $administrator == 1) {
                $displayUserIp = _IP . ' : ' . $auteur_ip;
            }
            else {
                $displayUserIp = '';
            }
           

            if ($auteur_id != "") {

                $sq_user = mysql_query("SELECT pseudo, niveau, rang, avatar, signature, date, email, icq, msn, aim, yim, xfire, facebook, origin, steam, twitter, skype, url, country, count, game FROM " . USER_TABLE . " WHERE id = '" . $auteur_id . "'");
                $test = mysql_num_rows($sq_user);
                list($autor, $user_level, $rang, $avatar, $signature, $date_member, $email, $icq, $msn, $aim, $yim, $xfire, $facebook ,$origin, $steam, $twitter, $skype, $homepage, $country, $nb_post, $userGame) = mysql_fetch_array($sq_user);

                $sqlOnlineConnect = mysql_query("SELECT user_id FROM " . NBCONNECTE_TABLE . " WHERE user_id = '" . $auteur_id . "'");
                list($connect_id) = mysql_fetch_array($sqlOnlineConnect);

                $sqlGame = mysql_query("SELECT name, icon, pref_1, pref_2, pref_3, pref_4, pref_5 FROM " . GAMES_TABLE . " WHERE id = '" . $userGame . "'" );
                list($gameName, $gameIcon, $gamePref1, $gamePref2, $gamePref3, $gamePref4, $gamePref5) = mysql_fetch_array($sqlGame);
                
                    $gameName = nkHtmlEntities($gameName);
                    
                    if ($gameIcon != '' && is_file($gameIcon)) {
                        $gameIconDisplayed = $gameIcon;
                    } 
                    else {
                        $gameIconDisplayed = 'images/games/icon_nk.png';
                    }

                $sqlGameUserDetails = mysql_query("SELECT  pref_1, pref_2, pref_3, pref_4, pref_5 FROM " . USER_DETAIL_TABLE . " WHERE user_id = '" . $auteur_id . "'");
                list($gameUserPref1, $gameUserPref2, $gameUserPref3, $gameUserPref4, $gameUserPref5) = mysql_fetch_array($sqlGameUserDetails);

                    $gameUserPref1 = nkHtmlEntities($gameUserPref1);
                    $gameUserPref2 = nkHtmlEntities($gameUserPref2);
                    $gameUserPref3 = nkHtmlEntities($gameUserPref3);
                    $gameUserPref4 = nkHtmlEntities($gameUserPref4);
                    $gameUserPref5 = nkHtmlEntities($gameUserPref5);



                if ($test > 0 && $autor != "") {
                    
                    //Icone en ligne
                    if($auteur_id == $connect_id) {
                        $onlineIcon = '<div class="nkOnlineIcon" title="' . _ISONLINE . '"></div>';
                    }
                    else {
                        $onlineIcon = '';
                    }

                    // Valeur TRUE = Pas d'heure/minute.
                    $date_member = nkDate($date_member, TRUE);

                    if ($fichier != "" && is_file("upload/Forum/" . $fichier)) {
                        
                        $url_file = "upload/Forum/" . $fichier;
                        $filesize = filesize($url_file) / 1024;
                        $arrondi_size = ceil($filesize);
                        $file = explode(".", $fichier);
                        $ext = count($file)-1;

                        if ($user && $auteur_id == $user[0] || $visiteur >= admin_mod("Forum") || $administrator == 1) {
                            $del = '&nbsp;<a href="index.php?file=Forum&amp;op=del_file&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '&amp;mess_id=' . $mess_id . '" class="nkButton icon trash danger">' . _DELFILE . '</a>';
                        } 
                        else {
                            $del = '';
                        } 

                        $attach_file = '<div class="nkForumViewAttachedFile"><strong><a href="' . $url_file . '" onclick="window.open(this.href); return false;" title="' . _DOWNLOADFILE . '">' . $fichier . '</a> (' . $arrondi_size . ' Ko)' . $del . '</strong></div>';

                    } 
                    else {
                        $attach_file = '';
                    } 

                    if ($modos != "" && strpos($modos, $auteur_id) !== false) {
                        $auteur_modo = 1;
                    } 
                    else {
                        $auteur_modo = 0;
                    } 

                    if ($rang > 0 && $nuked['forum_rank_team'] == "on") {
                        
                        $sql_rank_team = mysql_query("SELECT titre, image, couleur  FROM " . TEAM_RANK_TABLE . " WHERE id = '" . $rang . "'");
                        list($rank_name, $team_rank_image, $rank_color) = mysql_fetch_array($sql_rank_team);
                        $rank_name = printSecuTags($rank_name);
                        $rank_image = $team_rank_image;
                    } 
                    else {

                        if ($user_level >= admin_mod("Forum")) {
                            $user_rank = mysql_query("SELECT nom, image FROM " . FORUM_RANK_TABLE . " WHERE type = 2");
                        } 
                        else if ($auteur_modo == 1) {
                            $user_rank = mysql_query("SELECT nom, image FROM " . FORUM_RANK_TABLE . " WHERE type = 1");
                        } 
                        else {
                            $user_rank = mysql_query("SELECT nom, image FROM " . FORUM_RANK_TABLE . " WHERE '" . $nb_post . "' >= post AND type = 0 ORDER BY post DESC LIMIT 0, 1");
                        }

                        list($rank_name, $rank_image) = mysql_fetch_array($user_rank);
                        $rank_name = printSecuTags($rank_name);
                    } 
                    
                    if ($rang > 0 && $nuked['forum_user_details'] == "on") {
                        $style_rank = 'style="color:#' . $rank_color . '"';
                    }
                    else {
                        $style_rank = '';
                    }

                    $postAutor = '<img src="images/flags/'. $country .'" alt="' . $country .'" class="nkForumOnlineFlag" /><a href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($autor) . '">' . $autor . '</a>' . $onlineIcon . '';

                    if ($rank_name != "") {
                        $userRankName = $rank_name;
                    }
                    else {
                         $userRankName = '';
                    }

                    if ($rank_image != "") {
                        $forumRankImage = '<img src="' . $rank_image . '" alt="" />';
                    } 

                    if ($avatar != "") {

                        if ($avatar_resize == "off") $ar_ok = 0;
                        else if (preg_match("`http://`i", $avatar) && $avatar_resize == "local") $ar_ok = 0;
                        else  $ar_ok = 1;    
                        
                        if ($ar_ok == 1) $style = "style=\"border: 0; overflow: auto; max-width: " . $avatar_width . "px;  width: expression(this.scrollWidth >= " . $avatar_width . "? '" . $avatar_width . "px' : 'auto');\"";
                        else $style = 'style="boder:0;"';
                        
                        $userAvatar = '<img src="' . checkimg($avatar) . '" ' . $style . 'alt="" />';
                    } 
                    else {
                        $userAvatar = '<img src="modules/Forum/images/noAvatar.png" alt="" />';
                    }

                    $totalUserPost = _MESSAGES . ' : ' . $nb_post . '<br />' . _REGISTERED . ': ' . $date_member . '';

                } 
                else {

                    $postAutor = $auteur;
                    $attach_file = '';
                    $userRankName = '';
                    $forumRankImage = '';
                    $userAvatar = '<img src="modules/Forum/images/noAvatar.png" alt="" />';
                    $totalUserPost = '';
                } 
            } 
            else {

                $postAutor = $auteur;
                $attach_file = '';
                $userRankName = '';
                $forumRankImage = '';
                $userAvatar = '<img src="modules/Forum/images/noAvatar.png" alt="" />';
                $totalUserPost = '';

            } 

?>
                    <div>
                        <div class="nkForumViewUserPseudo nkBgColor3"><h3><?php echo $postAutor; ?></h3></div>
                        <div class="nkForumViewPostHead nkBgColor3">
                            <a href="#top" title="<?php echo _BACKTOTOP; ?>">
                                <img src="modules/Forum/images/interface/top_24.png" class="nkUserButton small" />
                            </a>
                            <a href="index.php?file=Forum&amp;page=viewtopic&amp;forum_id=<?php echo $_REQUEST['forum_id']; ?>&amp;thread_id=<?php echo $_REQUEST['thread_id']; ?>&amp;p=<?php echo $_REQUEST['p']; ?>#<?php echo $mess_id; ?> " title="<?php echo _PERMALINK_TITLE; ?>">
                                <img src="modules/Forum/images/interface/permalink_24.png" class="nkUserButton small" />

                            </a>
                        </div>
                    </div>
                    <div>
                        <div class="nkForumViewAuthor nkBgColor2 nkBorderColor1">
                            <a name="<?php echo $mess_id; ?>"></a>
                            <div class="nkForumUserRankName" <?php echo $style_rank; ?>><?php echo $userRankName; ?></div>
                            <div class="nkForumForumRankImage"><?php echo $forumRankImage; ?></div>
                            <div class="nkForumUserAvatar"><?php echo $userAvatar; ?></div>
                            <div class="nkForumTotalUserPost"><?php echo $totalUserPost; ?></div>
                            <div class="nkForumDisplayUserIp"><?php echo $displayUserIp; ?></div>
<?php
                        //User Game details
                        if ($nuked['forum_gamer_details'] == "on") {
?>
                            <div class="nkForumGamerDetails">
                                <div class="nkForumUserGameIcon">
                                    <?php echo _FAVORITEGAME; ?>&nbsp;:&nbsp;
                                    <img src="<?php echo $gameIconDisplayed; ?>" alt="" title="<?php echo $gameName; ?>" />
                                </div>
<?php
                            if ($gameUserPref1) echo '<div>' . $gamePref1 . '&nbsp;:&nbsp;' . $gameUserPref1 . '</div>';
                            if ($gameUserPref2) echo '<div>' . $gamePref2 . '&nbsp;:&nbsp;' . $gameUserPref2 . '</div>';
                            if ($gameUserPref3) echo '<div>' . $gamePref3 . '&nbsp;:&nbsp;' . $gameUserPref3 . '</div>';
                            if ($gameUserPref4) echo '<div>' . $gamePref4 . '&nbsp;:&nbsp;' . $gameUserPref4 . '</div>';
                            if ($gameUserPref5) echo '<div>' . $gamePref5 . '&nbsp;:&nbsp;' . $gameUserPref5 . '</div>';
?>
                            </div>
<?php
                        }
                        //Fin User Game details
?>
                        </div><!-- fin colonne auteur -->
                        <div class="nkForumViewMessage nkBgColor2 nkBorderColor1">
                            <div class="nkForumViewTitle">
                                <h3><?php echo $title; ?></h3>
                            </div><!-- @whitespace
                         --><div class="nkForumViewActionLinks">
                                <div class="nkButton-group">
<?php
                                if ($closed == 0 && $administrator == 1 || $visiteur >= admin_mod("Forum") || $visiteur >= $level) {
?>
                                    <a href="<?php echo $quoteLink; ?>" title="<?php echo _REPLYQUOTE; ?>" class="nkButton icon alone chat"></a>
<?php
                                } 

                                if ($user && $auteur_id == $user[0] && $closed == 0 || $visiteur >= admin_mod("Forum") || $administrator == 1) {
?>
                                    <a href="<?php echo $editLink; ?>" title="<?php echo _EDITMESSAGE; ?>" class="nkButton icon alone edit"></a>
<?php
                                } 

                                if ($visiteur >= admin_mod("Forum") || $administrator == 1) {
?>                                    
                                    <a href="<?php echo $deleteLink; ?>" title="<?php echo _DELMESSAGE; ?>" class="nkButton icon alone remove danger"></a>
<?php
                                } 

?>
                                </div>
                            </div>
                            <div class="nkForumViewTxt">
                                <?php echo $txt; ?>
                            </div>
                            <?php echo $attach_file; ?>
<?php
                        if ($edition != "") {
?>
                            <div class="nkForumEditMessage">
                                <small><?php echo $edition; ?></small>
                            </div>
<?php
                        }
                        if ($auteur_id != "" && $signature != "" && $usersig == 1) {
?>
                            <div class="nkForumViewSig nkBorderColor1">
                                <?php echo $signature; ?>
                            </div>
<?php
                        }
?>
                        </div>
                    </div>
                    <div class="nkForumViewPostLegend nkBgColor2">
                        <div class="nkForumUserSocialLinks nkBorderColor1">
                            <div class="nkButton-group">
<?php
                            if ($test > 0 && $auteur_id != "") {
                                echo '<a class="nkButton icon user small alone" href="index.php?file=Members&amp;op=detail&amp;autor=' . urlencode($autor) . '" title="' . _SEEPROFIL . '"></a>';
                            } 

                            if ($test > 0 && $user && $auteur_id != "") {
                                echo '<a class="nkButton icon pm small alone" href="index.php?file=Userbox&amp;op=post_message&amp;for=' . $auteur_id . '" title="' . _SENDPM . '"></a>';
                            } 

                            if ($nuked['user_email'] == "on" && $email != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon email small alone" href="mailto:' . $email . '" title="' . _SENDEMAIL . '"></a>';
                            } 

                            if ($nuked['user_website'] == "on" && $homepage != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon website small alone" href="' . $homepage . '" onclick="window.open(this.href); return false;" title="' . _SEEHOMEPAGE . '&nbsp;' . $autor . '"></a>';
                            } 

                            if ($nuked['user_icq'] == "on" && $icq != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon icq small alone" href="http://web.icq.com/whitepages/add_me?uin=' . $icq . '&amp;action=add" title="' . $icq . '"></a>';
                            } 

                            if ($nuked['user_msn'] == "on" && $msn != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon msn small alone" href="mailto:' . $msn . '" title="' . $msn . '"></a>';
                            } 

                            if ($nuked['user_aim'] == "on" && $aim != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon aim small alone" href="aim:goim?screenname=' . $aim . '&amp;message=Hi+' . $aim . '+Are+you+there+?" onclick="window.open(this.href); return false;" title="' . $aim . '"></a>';
                            } 

                            if ($nuked['user_yim'] == "on" && $yim != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon yim small alone" href="http://edit.yahoo.com/config/send_webmesg?target=' . $yim . '&amp;src=pg" title="' . $yim . '"></a>';
                            }

                            if ($nuked['user_xfire'] == "on" && $xfire != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon xfire small alone" href="xfire:add_friend?user=' . $xfire . '" title="' . $xfire . '"></a>';
                            }

                            if ($nuked['user_facebook'] == "on" && $facebook != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon facebook small alone" href="http://www.facebook.com/' . $facebook . '" title="' . $facebook . '"></a>';
                            }

                            if ($nuked['user_origin'] == "on" && $origin != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon origin small alone" href="#" title="' . $origin . '"></a>';
                            }

                            if ($nuked['user_steam'] == "on" && $steam != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon steam small alone" href="http://steamcommunity.com/actions/AddFriend/' . $steam . '" title="' . $steam . '"></a>';
                            }

                            if ($nuked['user_twitter'] == "on" && $twitter != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon twitter small alone" href="http://twitter.com/#!/' . $twitter . '" title="' . $twitter . '"></a>';
                            }

                            if ($nuked['user_skype'] == "on" && $skype != "" && $auteur_id != "" && $user[1] >= $nuked['user_social_level']) {
                                echo '<a class="nkButton icon skype small alone" href="skype:' . $skype . '?call" title="' . $skype . '"></a>';
                            }
?>
                            </div>
                        </div>
                        <div class="nkForumPostInfos nkBorderColor1">
                            <div class="nkForumPostDate">
                                <?php echo _POSTEDON; ?>&nbsp;<?php echo $date; ?>                                
                            </div><!-- @whitespace
                         --><div class="nkForumPermalinks">
                            </div>
                        </div>
                    </div>
<?php
        } 
?>
               </div>
            </div>
        </div>
<?php
    
        if ($user[0] != "") {
            $sql_notify = mysql_query("SELECT emailnotify FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "' AND auteur_id = '" . $user[0] . "'");
            $user_notify = mysql_num_rows($sql_notify);
            
            if ($user_notify > 0) {
                $inotify = 0;
                while(list($notify) = mysql_fetch_array($sql_notify)) {
                    if ($notify == 1) {
                        $inotify++;
                    }
                }

                if ($inotify > 0) {
                    $mailNotification = '<a href="index.php?file=Forum&amp;op=notify&amp;do=off&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '">' . _NOTIFYOFF . '</a>';
                }
                else {
                    $mailNotification = '<a href="index.php?file=Forum&amp;op=notify&amp;do=on&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '">' . _NOTIFYON . '</a>';
                }

            }

        }
?>
        <div class="nkForumNavPage">
<?php
        if ($count > $nb_mess_for_mess) {
            number($count, $nb_mess_for_mess, $url_page);
        } 
?>
        </div><!-- @whitespace
     --><div id="nkForumUserActionLink"><?php echo $mailNotification; ?></div>
        <div id="nkForumPostNewTopic">
            <div>
<?php
        if ($level == 0 || $visiteur >= $level || $administrator == 1) {
            echo $postNewTopic;

            if ($closed == 0 || $administrator == 1 || $visiteur >= admin_mod("Forum")) {
                echo $replyToTopic;
            } 
        } 
?>
            </div>
        </div>

<?php
        //Liens d'administration
        if ($visiteur >= admin_mod("Forum") || $administrator == 1) {

            $delLink    = 'index.php?file=Forum&amp;op=del_topic&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '" title="' . _TOPICDEL . '';
            $moveLink   = 'index.php?file=Forum&amp;op=move&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '" title="' . _TOPICMOVE . '';

            if ($closed == 1) {
                $closeTitle = _TOPICUNLOCK;
                $closeClass = 'unlock';
                $closeLink = 'index.php?file=Forum&amp;op=lock&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '&amp;do=open" title="' . _TOPICUNLOCK . '';
            } 
            else {
                $closeTitle = _TOPICLOCK;
                $closeClass = 'lock';
                $closeLink = 'index.php?file=Forum&amp;op=lock&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '&amp;do=close" title="' . _TOPICLOCK . '';
            } 

            if ($annonce == 1) {
                $pinedTitle = _TOPICDOWN;
                $pinedClass = 'arrowdown';
                $pinedLink = 'index.php?file=Forum&amp;op=announce&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '&amp;do=down" title="' . _TOPICDOWN . '';
            } 
            else {
                $pinedTitle = _TOPICUP;
                $pinedClass = 'arrowup';
                $pinedLink = 'index.php?file=Forum&amp;op=announce&amp;forum_id=' . $_REQUEST['forum_id'] . '&amp;thread_id=' . $_REQUEST['thread_id'] . '&amp;do=up" title="' . _TOPICUP . '';
            } 

?>
        <div id="nkForumAdminLinks">
            <div class="nkButton-group">
                <a href="<?php echo $delLink; ?>" class="nkButton icon alone remove danger"></a>
                <a href="<?php echo $moveLink; ?>" class="nkButton icon alone move"></a>
                <a href="<?php echo $closeLink; ?>" class="nkButton icon alone <?php echo $closeClass; ?>"></a>
                <a href="<?php echo $pinedLink; ?>" class="nkButton icon alone <?php echo $pinedClass; ?>"></a>
            </div>
        </div>
<?php
        }

        //Redimensionnement automatique des images du Forum
        echo "<script type=\"text/javascript\">\nMaxWidth = document.getElementById('nkForumWrapper').offsetWidth - 300;\n</script>\n";

        echo '<script type="text/javascript">
            <!--
                var Img = document.getElementById("img_resize_forum").getElementsByTagName("img");
                var NbrImg = Img.length;
                for(var i = 0; i < NbrImg; i++){
                    if (Img[i].width > MaxWidth){
                        Img[i].style.height = Img[i].height * MaxWidth / Img[i].width+"px";
                        Img[i].style.width = MaxWidth+"px";
                    }
                }
            -->
        </script>';
    //Fin de l'affichage du viewtopic
?>
    </div>
<?php
    } 
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
