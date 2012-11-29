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

global $nuked, $user, $language, $theme;

translate("modules/Forum/lang/" . $language . ".lang.php");
include("modules/Forum/template.php");

opentable();

$visiteur = $user ? $user[1] : 0;

$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{
    $nb_mess_for_mess = $nuked['mess_forum_page'];

    $sql = mysql_query("SELECT nom, moderateurs, cat, level FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
    $level_ok = mysql_num_rows($sql);

    $sql2 = mysql_query("SELECT titre, view, closed, annonce, last_post, auteur_id, sondage FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND id = '" . $_REQUEST['thread_id'] . "'");
    $topic_ok = mysql_num_rows($sql2);

     // No user access
     if ($level_ok == 0) {
          echo "<br /><br /><div style=\"text-align: center;\">" . _NOACCESSFORUM . "</div><br /><br />";
     }
     // No topic exists
     else if ($topic_ok == 0) {
          echo "<br /><br /><div style=\"text-align: center;\">" . _NOTOPICEXIST . "</div><br /><br />";
     }
     // User access
     else {

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
                         if (strrpos(',' . $tid, ',' . $thread . ',') === false){
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

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        } 
        else
        {
            $administrator = 0;
        } 

        list($titre, $read, $closed, $annonce, $lastpost, $topic_aid, $sondage) = mysql_fetch_array($sql2);
        $titre = printSecuTags($titre);
        $titre = nk_CSS($titre);

        $upd = mysql_query("UPDATE " . FORUM_THREADS_TABLE . " SET view = view + 1 WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND id = '" . $_REQUEST['thread_id'] . "'");

        $sql_next = mysql_query("SELECT id FROM " . FORUM_THREADS_TABLE . " WHERE last_post > '" . $lastpost. "' AND forum_id = '" . $_REQUEST['forum_id'] . "' ORDER BY last_post LIMIT 0, 1");
        list($nextid) = mysql_fetch_array($sql_next);

        if ($nextid != "")
        {
            $next = "<small><a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $nextid . "\">" . _NEXTTHREAD . "</a> &gt;</small>";
        } 

        $sql_last = mysql_query("SELECT id FROM " . FORUM_THREADS_TABLE . " WHERE last_post < '" . $lastpost . "' AND forum_id = '" . $_REQUEST['forum_id'] . "' ORDER BY last_post DESC LIMIT 0, 1");
        list($lastid) = mysql_fetch_array($sql_last);

        if ($lastid != "")
        {
            $prev = "<small>&lt; <a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $lastid . "\">" . _LASTTHREAD . "</a>&nbsp;</small>";
        } 

        echo "<br /><a name=\"top\"></a><table width=\"100%\" id=\"Forum\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\">\n"
        . "<tr><td><big><b>" . $titre . "</b></big></td><td align=\"right\">" . $prev . "&nbsp;" . $next . "</td></tr>\n"
        . "<tr><td valign=\"bottom\"><a href=\"index.php?file=Forum\"><b>" . _INDEXFORUM . "</b></a> -&gt; <a href=\"index.php?file=Forum&amp;cat=" . $cat . "\"><b>" . $cat_name . "</b></a> -&gt; <a href=\"index.php?file=Forum&amp;page=viewforum&amp;forum_id=" . $_REQUEST['forum_id'] . "\"><b>" . $nom . "</b></a>\n";

        $sql3 = mysql_query("SELECT thread_id FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "'");
        $count = mysql_num_rows($sql3);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_mess_for_mess - $nb_mess_for_mess;

        if ($_REQUEST['highlight'] != "")
        {
            $url_page = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "&amp;highlight=" . urlencode($_REQUEST['highlight']);
        } 
        else
        {
            $url_page = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'];
        } 

        if ($count > $nb_mess_for_mess)
        {
            echo "<br /><br />\n";
            number($count, $nb_mess_for_mess, $url_page);
        } 

        echo "</td><td align=\"right\" valign=\"bottom\">";

        if ($level == 0 || $visiteur >= $level || $administrator == 1)
        {
            echo "<a href=\"index.php?file=Forum&amp;page=post&amp;forum_id=" . $_REQUEST['forum_id'] . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/newthread.gif\" alt=\"\" title=\"" . _NEWSTOPIC . "\" /></a>";

            if ($closed == 0 || $administrator == 1 || $visiteur >= admin_mod("Forum"))
            {
                echo "<a href=\"index.php?file=Forum&amp;page=post&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/reply.gif\" alt=\"\" title=\"" . _REPLY . "\" /></a>";
            } 
        } 

        echo "</td></tr></table>\n"
    . "<table style=\"background: " . $color3 . ";\" width=\"100%\" cellspacing=\"1\" cellpadding=\"4\" border=\"0\" id=\"img_resize_forum\">\n";

        if ($sondage == 1)
        {
            echo "<tr style=\"background: " . $color2 . ";\"><td colspan=\"2\" align=\"center\">";

            $sql_poll = mysql_query("SELECT id, titre FROM " . FORUM_POLL_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "'");
            list($poll_id, $question) = mysql_fetch_array($sql_poll);
            $question = printSecuTags($question);

            if ($user && $topic_aid == $user[0] && $closed == 0 || $visiteur >= admin_mod("Forum") || $administrator == 1)
            {
                echo "<div style=\"text-align: right;\"><a href=\"index.php?file=Forum&amp;op=edit_poll&amp;poll_id=" . $poll_id . "&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/edit.gif\" alt=\"\" title=\"" . _EDITPOLL . "\" /></a>&nbsp;<a href=\"index.php?file=Forum&amp;op=del_poll&amp;poll_id=" . $poll_id . "&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/delete.gif\" alt=\"\" title=\"" . _DELPOLL . "\" /></a>&nbsp;</div>\n";
            } 

            $check = mysql_query("SELECT auteur_ip FROM " . FORUM_VOTE_TABLE . " WHERE poll_id = '" . $poll_id . "' AND auteur_id = '" . $user[0] . "'");
            $test = mysql_num_rows($check);

            if ($user && $test > 0 || $_REQUEST['vote'] == "view")
            {
                echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"2\" cellpadding=\"4\" border=\"0\">\n"
                . "<tr><td colspan=\"2\" align=\"center\"><b>" . $question . "</b></td></tr>\n";

                $sql_options = mysql_query("SELECT option_vote FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "' ORDER BY id ASC");
                $nbcount = 0;

                while (list($option_vote) = mysql_fetch_array($sql_options))
                {
                    $nbcount = $nbcount + $option_vote;
                } 

                $sql_res = mysql_query("SELECT option_vote, option_text FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "' ORDER BY id ASC");
                while (list($optioncount, $option_text) = mysql_fetch_array($sql_res))
                {
                    $optiontext = printSecuTags($option_text);

                    if ($nbcount <> 0)
                    {
                        $etat = ($optioncount * 100) / $nbcount ;
                    } 
                    else
                    {
                        $etat = 0;
                    } 

                    $pourcent_arrondi = round($etat);

                    echo "<tr><td>" . $optiontext . "</td><td>";

                    if ($etat < 1)
                    {
                        $width = 2;
                    } 
                    else
                    {
                        $width = $etat * 2;
                    } 

                    if (is_file("themes/" . $theme . "/images/bar.gif"))
                    {
                        $img = "themes/" . $theme . "/images/bar.gif";
                    } 
                    else
                    {
                        $img = "modules/Forum/images/bar.gif";
                    } 

                    echo "<img src=\"" . $img . "\" width=\"" . $width . "\" height=\"10\" alt=\"\" title=\"" . $pourcent_arrondi . "%\" />&nbsp;" . $pourcent_arrondi . "% (" . $optioncount . ")</td></tr>\n";
                } 
                echo "<tr></tr><td align=\"center\" colspan=\"2\"><b>" . _TOTALVOTE . " : </b>" . $nbcount . "</td></tr></table>\n";
            } 
            else
            {
                echo "<form  method=\"post\" action=\"index.php?file=Forum&amp;op=vote&amp;poll_id=" . $poll_id . "\">\n"
                . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\">\n"
                . "<tr><td align=\"center\"><b>" . $question . "</b></td></tr>\n";

                $sql_options = mysql_query("SELECT id, option_text FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "' ORDER BY id ASC");
                while (list($voteid, $optiontext) = mysql_fetch_array($sql_options))
                {
                    $optiontext = printSecuTags($optiontext);

                    echo "<tr><td><input type=\"radio\" class=\"checkbox\" name=\"voteid\" value=\"" . $voteid . "\" />&nbsp;" . $optiontext . "</td></tr>\n";
                } 

                echo "<tr><td>&nbsp;<input type=\"hidden\" name=\"forum_id\" value=\"" . $_REQUEST['forum_id'] . "\" /><input type=\"hidden\" name=\"thread_id\" value=\"" . $_REQUEST['thread_id'] . "\" /></td></tr>\n"
                . "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _TOVOTE . "\" />&nbsp;<input type=\"button\" value=\"" . _RESULT . "\" onclick=\"document.location='index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "&amp;vote=view'\" /></td></tr></table></form>\n";
            } 

            echo "</td></tr>\n";
        } 

        echo "<tr " . $background . "><td style=\"width: 25%;\" align=\"center\"><b>" . _AUTHOR . "</b></td><td style=\"width: 75%;\" align=\"center\" id=\"forum-table\"><b>" . _MESSAGE . "</b></td></tr>\n";

        $sql4 = mysql_query("SELECT id, titre, auteur, auteur_id, auteur_ip, txt, date, edition, usersig, file  FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "' ORDER BY date ASC limit " . $start . ", " . $nb_mess_for_mess."");
        while (list($mess_id, $title, $auteur, $auteur_id, $auteur_ip, $txt, $date, $edition, $usersig, $fichier) = mysql_fetch_row($sql4))
        {

            $title = printSecuTags($title);            

            if ($_REQUEST['highlight'] != "")
            { 
                $string = trim($_REQUEST['highlight']);
                $string = printSecuTags($string);
                $title = str_replace($string, '<span style="color: #FF0000">' . $string . '</span>', $title);

                $search = explode(" ", $string);
                for($i = 0; $i < count($search); $i++)
                {
                    $tab = preg_split("`(<\w+.*?>)`", $txt, -1, PREG_SPLIT_DELIM_CAPTURE);

                    foreach ($tab as $key=>$val)
                    {
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

            echo "<tr style=\"background: " . $color . ";\"><td style=\"width: 25%;\" valign=\"top\"><a name=\"" . $mess_id . "\"></a>";

            if ($auteur_id != "")
            {
                $sq_user = mysql_query("SELECT pseudo, niveau, rang, avatar, signature, date, email, icq, msn, aim, yim, url, country, count FROM " . USER_TABLE . " WHERE id = '" . $auteur_id . "'");
                $test = mysql_num_rows($sq_user);
                list($autor, $user_level, $rang, $avatar, $signature, $date_member, $email, $icq, $msn, $aim, $yim, $homepage, $country, $nb_post) = mysql_fetch_array($sq_user);

                if ($test > 0 && $autor != "")
                {
                    // Valeur TRUE = Pas d'heure/minute.
                    $date_member = nkDate($date_member, TRUE);

                    if ($fichier != "" && is_file("upload/Forum/" . $fichier))
                    {
                        $url_file = "upload/Forum/" . $fichier;
                        $filesize = filesize($url_file) / 1024;
                        $arrondi_size = ceil($filesize);
                        $file = explode(".", $fichier);
                        $ext = count($file)-1;

                        if ($user && $auteur_id == $user[0] || $visiteur >= admin_mod("Forum") || $administrator == 1)
                        {
                            $del = "&nbsp;<a href=\"index.php?file=Forum&amp;op=del_file&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "&amp;mess_id=" . $mess_id . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/del.gif\" alt=\"\" title=\"" . _DELFILE . "\" /></a>";
                        } 
                        else
                        {
                            $del = "";
                        } 

                        $attach_file = "<br /><img src=\"modules/Forum/images/file.gif\" alt=\"\" title=\"" . _ATTACHFILE . "\" /><a href=\"" . $url_file . "\" onclick=\"window.open(this.href); return false;\" title=\"" . _DOWNLOADFILE . "\">" . $fichier . "</a> (" . $arrondi_size . " Ko)" . $del;
                    } 
                    else
                    {
                        $attach_file = "";
                    } 

                    if ($modos != "" && strpos($modos, $auteur_id) !== false)
                    {
                        $auteur_modo = 1;
                    } 
                    else
                    {
                        $auteur_modo = 0;
                    } 

                    if ($rang > 0 && $nuked['forum_rank_team'] == "on")
                    {
                        $sql_rank_team = mysql_query("SELECT titre FROM " . TEAM_RANK_TABLE . " WHERE id = '" . $rang . "'");
                        list($rank_name) = mysql_fetch_array($sql_rank_team);
                        $rank_name = printSecuTags($rank_name);
                        $rank_image = "";
                    } 
                    else
                    {
                        if ($user_level >= admin_mod("Forum"))
                        {
                            $user_rank = mysql_query("SELECT nom, image FROM " . FORUM_RANK_TABLE . " WHERE type = 2");
                        } 
                        else if ($auteur_modo == 1)
                        {
                            $user_rank = mysql_query("SELECT nom, image FROM " . FORUM_RANK_TABLE . " WHERE type = 1");
                        } 
                        else
                        {
                            $user_rank = mysql_query("SELECT nom, image FROM " . FORUM_RANK_TABLE . " WHERE '" . $nb_post . "' >= post AND type = 0 ORDER BY post DESC LIMIT 0, 1");
                        } 

                        list($rank_name, $rank_image) = mysql_fetch_array($user_rank);
                        $rank_name = printSecuTags($rank_name);
                    } 

                    echo "<img src=\"images/flags/". $country ."\" alt=\"" . $country ."\" />&nbsp;<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($autor) . "\"><b>" . $autor . "</b></a><br />\n";

                    if ($rank_name != "")
                    {
                        echo $rank_name . "<br />\n";
                    } 

                    if ($rank_image != "")
                    {
                        echo "<img src=\"" . $rank_image . "\" alt=\"\" /><br /><br />\n";
                    } 

                    if ($avatar != "")
                    {
                        if ($avatar_resize == "off") $ar_ok = 0;
                        else if (preg_match("`http://`i", $avatar) && $avatar_resize == "local") $ar_ok = 0;
                        else  $ar_ok = 1;    
                        
                        if ($ar_ok == 1) $style = "style=\"border: 0; overflow: auto; max-width: " . $avatar_width . "px;  width: expression(this.scrollWidth >= " . $avatar_width . "? '" . $avatar_width . "px' : 'auto');\"";
                        else $style = "style=\"boder:0;\"";
                        
                        echo "<img src=\"" . checkimg($avatar) . "\" " . $style . "alt=\"\" /><br />\n";
                    } 
                    else{
                        echo '<img src="modules/User/images/noavatar.png" alt="" /><br />'."\n";
                    }

                    echo _MESSAGES . " : " . $nb_post . "<br />" . _REGISTERED . ": " . $date_member . "<br />\n";

                    if ($visiteur >= admin_mod("Forum") || $administrator == 1)
                    {
                        echo _IP . " : " . $auteur_ip;
                    } 
                } 
                else
                {
                    echo "<b>" . $auteur . "</b><br />\n";

                    if ($visiteur >= admin_mod("Forum") || $administrator == 1)
                    {
                        echo _IP . " : " . $auteur_ip;
                    } 
                } 
            } 
            else
            {
                echo "<b>" . $auteur . "</b><br />\n";

                if ($visiteur >= admin_mod("Forum") || $administrator == 1)
                {
                    echo _IP . " : " . $auteur_ip;
                } 
            } 


            echo "</td><td style=\"width: 75%;\" valign=\"top\">\n"
            . "<table width=\"100%\" cellpadding=\"5\" cellspacing=\"1\" border=\"0\">\n"
            . "<tr><td><a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "&amp;p=" . $_REQUEST['p'] . "#" . $mess_id . "\" title=\"" . _PERMALINK_TITLE . "\"><img src=\"images/posticon.gif\" style=\"border:0px;\" alt=\"\" /></a>" . _POSTEDON . " " . $date . "&nbsp;&nbsp;" . $attach_file . "</td><td align=\"right\">";

            if ($closed == 0 && $administrator == 1 || $visiteur >= admin_mod("Forum") || $visiteur >= $level)
            {
                echo "<a href=\"index.php?file=Forum&amp;page=post&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "&amp;mess_id=" . $mess_id . "&amp;do=quote\"><img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/quote.gif\" alt=\"\" title=\"" . _REPLYQUOTE . "\" /></a>";
            } 

            if ($user && $auteur_id == $user[0] && $closed == 0 || $visiteur >= admin_mod("Forum") || $administrator == 1)
            {
                echo "&nbsp;<a href=\"index.php?file=Forum&amp;page=post&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;mess_id=" . $mess_id . "&amp;do=edit\"><img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/edit.gif\" title=\"" . _EDITMESSAGE . "\" alt=\"\" /></a>";
            } 

            if ($visiteur >= admin_mod("Forum") || $administrator == 1)
            {
                echo "&nbsp;<a href=\"index.php?file=Forum&amp;op=del&amp;mess_id=" . $mess_id . "&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/delete.gif\" alt=\"\" title=\"" . _DELMESSAGE . "\" /></a>";
            } 

            echo "</td></tr><tr style=\"background: " . $color . ";\"><td colspan=\"2\"><b>" . $title . "</b></td></tr>\n"
            . "<tr style=\"background: " . $color . ";\"><td colspan=\"2\">" . $txt . "<br /><br /></td></tr>\n";

            if ($edition != "")
            {
                echo "<tr style=\"background: " . $color . ";\"><td colspan=\"2\"><small><i>" . $edition . "</i></small></td></tr>\n";
            } 
        
            if ($auteur_id != "" && $signature != "" && $usersig == 1)
            {
                echo "<tr style=\"background: " . $color . ";\"><td style=\"border-top: 1px dashed " . $color3 . ";\" colspan=\"2\">" . $signature . "</td></tr>\n";
            } 

            echo "</table></td></tr>\n"
            . "<tr style=\"background: " . $color . ";\"><td style=\"width: 25%;\" valign=\"middle\"><a href=\"#top\" title=\"" . _BACKTOTOP . "\">" . _BACKTOTOP . "</a>&nbsp;|&nbsp;<a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "&amp;p=" . $_REQUEST['p'] . "#" . $mess_id . "\" title=\"" . _PERMALINK_TITLE . "\">" . _PERMALINK . "</a></td><td style=\"width: 75%;\" valign=\"bottom\">";

            if ($test > 0 && $auteur_id != "")
            {
                echo "<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($autor) . "\" title=\"" . _SEEPROFIL . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/profile.gif\" alt=\"\" /></a>";
            } 

            if ($test > 0 && $user && $auteur_id != "")
            {
                echo "<a href=\"index.php?file=Userbox&amp;op=post_message&amp;for=" . $auteur_id . "\" title=\"" . _SENDPM . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/message.gif\" alt=\"\" /></a>";
            } 

            if ($email != "" && $auteur_id != "")
            {
                echo "<a href=\"mailto:" . $email . "\" title=\"" . _SENDEMAIL . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/email.gif\" alt=\"\" /></a>";
            } 

            if ($homepage != "" && $auteur_id != "")
            {
                echo "<a href=\"" . $homepage . "\" onclick=\"window.open(this.href); return false;\" title=\"" . _SEEHOMEPAGE . "&nbsp;" . $autor . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/website.gif\" alt=\"\" /></a>";
            } 

            if ($icq != "" && $auteur_id != "")
            {
                echo "<a href=\"http://web.icq.com/whitepages/add_me?uin=" . $icq . "&amp;action=add\"><img style=\"border: 0;\" src=\"modules/Forum/images/icq.gif\" title=\"" . $icq . "\" alt=\"\" /></a>";
            } 

            if ($msn != "" && $auteur_id != "")
            {
                echo "<a href=\"mailto:" . $msn . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/msn.gif\" title=\"" . $msn . "\" alt=\"\" /></a>";
            } 

            if ($aim != "" && $auteur_id != "")
            {
                echo "<a href=\"aim:goim?screenname=" . $aim . "&amp;message=Hi+" . $aim . "+Are+you+there+?\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"modules/Forum/images/aol.gif\" title=\"" . $aim . "\" alt=\"\" /></a>";
            } 

            if ($yim != "" && $auteur_id != "")
            {
                echo "<a href=\"http://edit.yahoo.com/config/send_webmesg?target=" . $yim . "&amp;src=pg\"><img style=\"border: 0;\" src=\"modules/Forum/images/yim.gif\" title=\"" . $yim . "\" alt=\"\" /></a>";
            } 

            echo "</td></tr>\n";
        } 

        echo "</table><table width=\"100%\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\"><tr><td valign=\"top\">";

        if ($_REQUEST['highlight'] != "")
        {
            $url_page = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'] . "&amp;highlight=" . urlencode($_REQUEST['highlight']);
        } 
        else
        {
            $url_page = "index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
        } 

        if ($count > $nb_mess_for_mess)
        {
            number($count, $nb_mess_for_mess, $url_page);
        } 

        echo "</td><td align=\"right\" valign=\"top\">";
    
        if ($user[0] != "")
        {
            $sql_notify = mysql_query("SELECT emailnotify FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "' AND auteur_id = '" . $user[0] . "'");
            $user_notify = mysql_num_rows($sql_notify);
            
            if ($user_notify > 0)
            {
                $inotify = 0;
                while(list($notify) = mysql_fetch_array($sql_notify))
                {
                    if ($notify == 1)
                    {
                        $inotify++;
                    }

        }

        if ($inotify > 0)
        {
                    echo "<a href=\"index.php?file=Forum&amp;op=notify&amp;do=off&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "\">" . _NOTIFYOFF . "</a><br />\n";
        }
        else
        {
                    echo "<a href=\"index.php?file=Forum&amp;op=notify&amp;do=on&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "\">" . _NOTIFYON . "</a><br />\n";
        }

            }

    }

        if ($level == 0 || $visiteur >= $level || $administrator == 1)
        {
            echo "<a href=\"index.php?file=Forum&amp;page=post&amp;forum_id=" . $_REQUEST['forum_id'] . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/newthread.gif\" alt=\"\" title=\"" . _NEWSTOPIC . "\" /></a>";

            if ($closed == 0 || $administrator == 1 || $visiteur >= admin_mod("Forum"))
            {
                echo "<a href=\"index.php?file=Forum&amp;page=post&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/buttons/" . $language . "/reply.gif\" alt=\"\" title=\"" . _REPLY . "\" /></a>";
            } 

        } 

        echo "</td></tr></table>\n";

        if ($visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            echo "<br /><a href=\"index.php?file=Forum&amp;op=del_topic&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/topic_delete.gif\" alt=\"\" title=\"" . _TOPICDEL . "\" /></a>"
            . "&nbsp;<a href=\"index.php?file=Forum&amp;op=move&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "\"><img style=\"border: 0;\" src=\"modules/Forum/images/topic_move.gif\" alt=\"\" title=\"" . _TOPICMOVE . "\" /></a>";

            if ($closed == 1)
            {
                echo "&nbsp;<a href=\"index.php?file=Forum&amp;op=lock&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "&amp;do=open\"><img style=\"border: 0;\" src=\"modules/Forum/images/topic_unlock.gif\" alt=\"\" title=\"" . _TOPICUNLOCK . "\" /></a>";
            } 
            else
            {
                echo "&nbsp;<a href=\"index.php?file=Forum&amp;op=lock&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "&amp;do=close\"><img style=\"border: 0;\" src=\"modules/Forum/images/topic_lock.gif\" alt=\"\" title=\"" . _TOPICLOCK . "\" /></a>";
            } 

            if ($annonce == 1)
            {
                echo "&nbsp;<a href=\"index.php?file=Forum&amp;op=announce&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "&amp;do=down\"><img style=\"border: 0;\" src=\"modules/Forum/images/topic_down.gif\" alt=\"\" title=\"" . _TOPICDOWN . "\" /></a>";
            } 
            else
            {
                echo "&nbsp;<a href=\"index.php?file=Forum&amp;op=announce&amp;forum_id=" . $_REQUEST['forum_id'] . "&amp;thread_id=" . $_REQUEST['thread_id'] . "&amp;do=up\"><img style=\"border: 0;\" src=\"modules/Forum/images/topic_up.gif\" alt=\"\" title=\"" . _TOPICUP . "\" /></a>";
            } 
        }

        echo "<script type=\"text/javascript\">\nMaxWidth = document.getElementById('Forum').offsetWidth - 300;\n</script>\n";

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
    } 
} 
else if ($level_access == -1)
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
} 
else if ($level_access == 1 && $visiteur == 0)
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b></div><br /><br />";
} 
else
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
} 
closetable();

?>
