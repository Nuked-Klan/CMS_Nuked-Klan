<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $nuked, $language, $user, $cookie_captcha;
translate("modules/Forum/lang/" . $language . ".lang.php");

// Inclusion système Captcha
include_once("Includes/nkCaptcha.php");

// On determine si le captcha est actif ou non
if (_NKCAPTCHA == "off") $captcha = 0;
else if ((_NKCAPTCHA == 'auto' OR _NKCAPTCHA == 'on') && $user[1] > 0)  $captcha = 0;
else $captcha = 1;


if (!$user)
{
    $visiteur = 0;
}
else
{
    $visiteur = $user[1];
}
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{
    compteur("Forum");

    function index()
    {
        opentable();
        include("modules/Forum/main.php");
        closetable();
    }

    function edit($mess_id)
    {
        global $visiteur, $user, $nuked;

        opentable();

        if ($_REQUEST['titre'] == "" || $_REQUEST['texte'] == "" || @ctype_space($_REQUEST['titre']) || @ctype_space($_REQUEST['texte']))
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _FIELDEMPTY . "</div><br /><br />";
            $url = "index.php?file=Forum&page=post&forum_id=" . $_REQUEST['forum_id'] . "&mess_id=" . $_REQUEST['mess_id'] . "&do=edit";
            redirect($url, 2);
            closetable();
            footer();
            exit();
        }

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= level AND id = '" . $_REQUEST['forum_id'] . "'");
        list($modos) = mysql_fetch_array($result);

        $administrator = ($user && $modos != "" && strpos($modos, $user[0]) !== false) ? 1 : 0;

        if ($_REQUEST['author'] == $user[2] || $visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            $date = nkDate(time());

            if ($_REQUEST['edit_text'] == 1)
            {
                $texte_edit = _EDITBY . "&nbsp;" . $user[2] . "&nbsp;" . _THE . "&nbsp;" . $date;
                $edition = ", edition = '" . $texte_edit ."'";
            }
            else
            {
                $edition = "";
            }

            $_REQUEST['texte'] = secu_html(html_entity_decode($_REQUEST['texte']));
            $_REQUEST['texte'] = icon($_REQUEST['texte']);
            $_REQUEST['titre'] = mysql_real_escape_string(stripslashes($_REQUEST['titre']));
            $_REQUEST['texte'] = mysql_real_escape_string(stripslashes($_REQUEST['texte']));

            if (!is_numeric($_REQUEST['usersig'])) $_REQUEST['usersig'] = 0;
            if (!is_numeric($_REQUEST['emailnotify'])) $_REQUEST['emailnotify'] = 0;

            $sql2 = mysql_query("SELECT thread_id FROM " . FORUM_MESSAGES_TABLE . " WHERE id = '" . $mess_id . "'");
            list($thread_id) = mysql_fetch_row($sql2);

            $sql3 = mysql_query("SELECT id FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "' ORDER BY id LIMIT 0, 1");
            list($mid) = mysql_fetch_row($sql3);

            $sql = mysql_query("UPDATE " . FORUM_MESSAGES_TABLE . " SET titre = '" . $_REQUEST['titre'] . "', txt = '" . $_REQUEST['texte'] . "'" . $edition . ", usersig = '" . $_REQUEST['usersig'] . "', emailnotify = '" . $_REQUEST['emailnotify'] . "' WHERE id = '" . $mess_id . "'");

            if ($mid == $mess_id)
            {
                $upd = mysql_query("UPDATE " . FORUM_THREADS_TABLE . " SET titre = '" . $_REQUEST['titre'] . "' WHERE id = '" . $thread_id . "'");
            }

            $sql_page = mysql_query("SELECT id FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "'");
            $nb_rep = mysql_num_rows($sql_page);

            if ($nb_rep > $nuked['mess_forum_page'])
            {
                $topicpages = $nb_rep / $nuked['mess_forum_page'];
                $topicpages = ceil($topicpages);

                $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $thread_id . "&p=" . $topicpages . "#" . $mess_id;
            }
            else
            {
                $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $thread_id . "#" . $mess_id;
            }

            echo "<br /><br /><div style=\"text-align: center;\">" . _MESSMODIF . "</div><br /><br />";
        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
            $url = 'index.php?file=Forum';
        }
        redirect($url, 2);
        closetable();
    }

    function del($mess_id)
    {
        global $visiteur, $user, $nuked;

        opentable();

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
        list($modos) = mysql_fetch_array($result);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        if ($visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            if ($_REQUEST['confirm'] == _YES)
            {
                $sql2 = mysql_query("SELECT id, file FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "' ORDER BY id LIMIT 0, 1");
                list($mid, $filename) = mysql_fetch_row($sql2);

                if ($filename != "")
                {
                    $path = "upload/Forum/" . $filename;

                    if (is_file($path))
                    {
                        $filesys = str_replace("/", "\\", $path);
                        @chmod ($path, 0775);
                        @unlink($path);
                        @system("del $filesys");
                    }
                }

                if ($mid == $mess_id)
                {
                    $sql_survey = mysql_query("SELECT sondage FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $_REQUEST['thread_id'] . "'");
                    list($sondage) = mysql_fetch_row($sql_survey);

                    if ($sondage == 1)
                    {
                        $sql_poll = mysql_query("SELECT id FROM " . FORUM_POLL_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "'");
                        list($poll_id) = mysql_fetch_row($sql_poll);

                        $sup1 = mysql_query("DELETE FROM " . FORUM_POLL_TABLE . " WHERE id = '" . $poll_id . "'");
                        $sup2 = mysql_query("DELETE FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "'");
                        $sup3 = mysql_query("DELETE FROM " . FORUM_VOTE_TABLE . " WHERE poll_id = '" . $poll_id . "'");
                    }

                         mysql_query("DELETE FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . (int) $_REQUEST['thread_id'] . "'");
                         mysql_query("DELETE FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . (int) $_REQUEST['thread_id'] . "'");

                         $url = "index.php?file=Forum&page=viewforum&forum_id=" . (int) $_REQUEST['forum_id'];
                    } else {
                         $url = "index.php?file=Forum&page=viewtopic&forum_id=" . (int) $_REQUEST['forum_id'] . "&thread_id=" . (int) $_REQUEST['thread_id'];
                }

                $sql = mysql_query("DELETE FROM " . FORUM_MESSAGES_TABLE . " WHERE id = '" . $mess_id . "'");

                echo "<br /><br /><div style=\"text-align: center;\">" . _MESSDELETED . "</div><br /><br />";
                redirect($url, 2);
            }

            else if ($_REQUEST['confirm'] == _NO)
            {
                $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
                echo "<br /><br /><div style=\"text-align: center;\">" . _DELCANCEL . "</div><br /><br />";
                redirect($url, 2);
            }

            else
            {
                echo "<form method=\"post\" action=\"index.php?file=Forum&amp;op=del\">\n"
                . "<div style=\"text-align: center;\"><br /><br />" . _CONFIRMDELMESS . "<br />\n"
                . "<input type=\"hidden\" name=\"forum_id\" value=\"" . $_REQUEST['forum_id'] . "\" />\n"
                . "<input type=\"hidden\" name=\"thread_id\" value=\"" . $_REQUEST['thread_id'] . "\" />\n"
                . "<input type=\"hidden\" name=\"mess_id\" value=\"" . $mess_id . "\" />\n"
                . "<input type=\"submit\" name=\"confirm\" value=\"" . _YES . "\" />"
                . "&nbsp;<input type=\"submit\" name=\"confirm\" value=\"" . _NO . "\" /></div></form><br />\n";
            }
        }
        else
        {
            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
            redirect($url, 2);
        }

        closetable();
    }

    function del_topic($thread_id)
    {
        global $visiteur, $user, $nuked;

        opentable();

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
        list($modos) = mysql_fetch_array($result);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        if ($visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            if ($_REQUEST['confirm'] == _YES)
            {
                $sql = mysql_query("SELECT sondage FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $thread_id . "'");
                list($sondage) = mysql_fetch_row($sql);

                if ($sondage == 1)
                {
                    $sql_poll = mysql_query("SELECT id FROM " . FORUM_POLL_TABLE . " WHERE thread_id = '" . $thread_id . "'");
                    list($poll_id) = mysql_fetch_row($sql_poll);

                    $sup1 = mysql_query("DELETE FROM " . FORUM_POLL_TABLE . " WHERE id = '" . $poll_id . "'");
                    $sup2 = mysql_query("DELETE FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "'");
                    $sup3 = mysql_query("DELETE FROM " . FORUM_VOTE_TABLE . " WHERE poll_id = '" . $poll_id . "'");
                }

                $sql2 = mysql_query("SELECT file FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "'");
                while (list($filename) = mysql_fetch_row($sql2))
                {
                    if ($filename != "")
                    {
                        $path = "upload/Forum/" . $filename;
                        if (is_file($path))
                        {
                            $filesys = str_replace("/", "\\", $path);
                            @chmod ($path, 0775);
                            @unlink($path);
                            @system("del $filesys");
                        }
                    }
                }

                    mysql_query("DELETE FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "' AND forum_id = '" . (int) $_REQUEST['forum_id'] . "'");
                    mysql_query("DELETE FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $thread_id . "' AND forum_id = '" . (int) $_REQUEST['forum_id'] . "'");

                $url = "index.php?file=Forum&page=viewforum&forum_id=" . $_REQUEST['forum_id'];
                echo "<br /><br /><div style=\"text-align: center;\">" . _TOPICDELETED . "</div><br /><br />";
                redirect($url, 2);
            }

            else if ($_REQUEST['confirm'] == _NO)
            {
                $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $thread_id;
                echo "<br /><br /><div style=\"text-align: center;\">" . _DELCANCEL . "</div><br /><br />";
                redirect($url, 2);
            }

            else
            {
                echo "<form method=\"post\" action=\"index.php?file=Forum&amp;op=del_topic\">\n"
                . "<div style=\"text-align: center;\"><br /><br />" . _CONFIRMDELTOPIC . "<br />\n"
                . "<input type=\"hidden\" name=\"forum_id\" value=\"" . $_REQUEST['forum_id'] . "\" />\n"
                . "<input type=\"hidden\" name=\"thread_id\" value=\"" . $thread_id . "\" />\n"
                . "<input type=\"submit\" name=\"confirm\" value=\"" . _YES . "\" />"
                . "&nbsp;<input type=\"submit\" name=\"confirm\" value=\"" . _NO . "\" /></div></form><br />\n";
            }

        }
        else
        {
            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $thread_id;
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
            redirect($url, 2);
        }

        closetable();
    }

    function move()
    {
        global $visiteur, $user, $nuked;

        opentable();

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
        list($modos) = mysql_fetch_array($result);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        if ($visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            if ($_REQUEST['confirm'] == _YES && $_REQUEST['newforum'] != "")
            {
                echo"<br /><br /><div style=\"text-align: center;\">" . _TOPICMOVED . "</div><br /><br />";

                    mysql_query("UPDATE " . FORUM_THREADS_TABLE . " SET forum_id = '" . $_REQUEST['newforum'] . "' WHERE id = '" . (int) $_REQUEST['thread_id'] . "'");
                    mysql_query("UPDATE " . FORUM_MESSAGES_TABLE . " SET forum_id = '" . $_REQUEST['newforum'] . "' WHERE thread_id = '" . (int) $_REQUEST['thread_id'] . "'");
                    $SQL = "SELECT thread_id, forum_id, user_id FROM " . FORUM_READ_TABLE . " WHERE forum_id LIKE '%," . $_REQUEST['forum_id'] . ",%' OR  forum_id LIKE '%," . $_REQUEST['newforum'] . ",%' ";
                    $req = mysql_query($SQL);
                    $update = '';
                    // Liste des utilisateurs
                    $userTMP = array();
                    while ($data = mysql_fetch_assoc($req)) {
                         $userTMP[$data['user_id']] = array('forum_id' => $data['forum_id'], 'thread_id' => $data['thread_id']);
                    }
                    // Vieux forum
                    $oldTMP = array();
                    // Liste des threads de l'ancien forum
                    $SQL = "SELECT id FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = " . (int) $_REQUEST['forum_id'] . " ";
                    $req = mysql_query($SQL);
                    // On vérifie que tous les threads sont lus
                    while ($data = mysql_fetch_assoc($req)) {
                         $oldTMP[$data['id']] = $data['id'];
                    }
                    // Nouveau forum
                    $newTMP = array();
                    // Liste des threads du nouveau forum
                    $SQL = "SELECT id FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = " . (int) $_REQUEST['newforum'] . " ";
                    $req = mysql_query($SQL);
                    // On vérifie que tous les threads sont lus
                    while ($data = mysql_fetch_assoc($req)) {
                         $newTMP[$data['id']] = $data['id'];
                    }

                    // On boucle les users
                    foreach ($userTMP as $key => $member) {
                         // On part du fait que tout les posts sont lu
                         $read = true;
                         foreach ($oldTMP as $old) {
                              // Si au moins un post n'est pas lu
                              if (strrpos($member['thread_id'], ',' . $old . ',') === false)
                                   $read = false;
                         }
                         
                         // Si ils sont tous lu, et que le forum est pas dans la liste on le rajoute
                         if ($read === true && strrpos($member['forum_id'], ',' . $_REQUEST['forum_id'] . ',') === false) {
                              // Nouvelle liste des forums
                              $fid = $member['forum_id'] . $_REQUEST['forum_id'] . ',';
                              // Si aucun update n'a eu lieu avant
                              $update .= (!empty($update) ? ', ':'');
                              $update .= "('" . $fid . "', '" . $key . "')";
                              }

                         // On part du fait que tout les posts sont lu
                         $read = true;
                         foreach($newTMP as $new){
                              // Si au moins un post n'est pas lu
                              if (strrpos($member['thread_id'], ',' . $new . ',') === false)
                                   $read = false;
                         }
                         
                         // Si tout n'est pas lu, et que le forum est présent dans la liste on le retire
                         if ($read === false && strrpos($fid, ',' . $_REQUEST['newforum'] . ',') !== false) {
                              // Nouvelle liste des forums
                              $fid = preg_replace("#," . $_REQUEST['newforum'] . ",#is", ",", $fid);
                              // Si aucun n'update n'a eu lieu avant
                              $update .= (!empty($update) ? ', ':'');
                              $update .= "('" . $fid . "', '" . $key . "')";
                         }
                         
                    }
                    
                    if(!empty($update)){
                         $update = "INSERT INTO `" . FORUM_READ_TABLE . "` (forum_id, user_id) VALUES $update ON DUPLICATE KEY UPDATE forum_id=VALUES(forum_id);";
                         mysql_query($update) or die(mysql_error());
                    }

                    $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['newforum'] . "&thread_id=" . (int) $_REQUEST['thread_id'];
                    redirect($url, 2);
               } else if ($_REQUEST['confirm'] == _NO) {
                echo "<br /><br /><div style=\"text-align: center;\">" . _DELCANCEL . "</div><br /><br />";

                $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
                redirect($url, 2);
            }

            else
            {
                echo "<form action=\"index.php?file=Forum&amp;op=move\" method=\"post\">\n"
                . "<div style=\"text-align: center;\"><br /><br />" . _MOVETOPIC . " : <select name=\"newforum\">\n";

                $sql_cat = mysql_query("SELECT id, nom FROM " . FORUM_CAT_TABLE . " WHERE '" . $visiteur . "' >= niveau ORDER BY ordre, nom");
                while (list($cat, $cat_name) = mysql_fetch_row($sql_cat))
                {
                    $cat_name = printSecuTags($cat_name);

                    echo "<option value=\"\">* " . $cat_name . "</option>\n";

                    $sql_forum = mysql_query("SELECT nom, id FROM " . FORUM_TABLE . " WHERE cat = '" . $cat . "' AND '" . $visiteur . "' >= niveau ORDER BY ordre, nom");
                    while (list($forum_name, $fid) = mysql_fetch_row($sql_forum))
                    {
                        $forum_name = printSecuTags($forum_name);

                        echo "<option value=\"" . $fid . "\">&nbsp;&nbsp;&nbsp;" . $forum_name . "</option>\n";
                    }
                }

                echo "</select><br /><br /><input type=\"submit\" name=\"confirm\" value=\"" . _YES . "\" />"
                . "&nbsp;<input type=\"submit\" name=\"confirm\" value=\"" . _NO . "\" />\n"
                . "<input type=\"hidden\" name=\"forum_id\" value=\"".$_REQUEST['forum_id']."\" />\n"
                . "<input type=\"hidden\" name=\"thread_id\" value=\"".$_REQUEST['thread_id']."\" /></div></form><br />\n";
            }
        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            redirect($url, 2);
        }

        closetable();
    }

    function lock()
    {
        global $visiteur, $user, $nuked;

        opentable();

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
        list($modos) = mysql_fetch_array($result);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        if ($_REQUEST['do'] == "close")
        {
            $lock_text = _TOPICLOCKED;
            $lock_type = 1;
        }

        else if ($_REQUEST['do'] == "open")
        {
            $lock_text = _TOPICUNLOCKED;
            $lock_type = 0;
        }

        if ($visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . $lock_text . "</div><br /><br />";

            $sql = mysql_query("UPDATE " . FORUM_THREADS_TABLE . " SET closed = '" . $lock_type . "' WHERE id = '" . $_REQUEST['thread_id'] . "'");

            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            redirect($url, 2);
        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";

            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            redirect($url, 2);
        }

        closetable();
    }

    function announce()
    {
        global $visiteur, $user, $nuked;

        opentable();

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
        list($modos) = mysql_fetch_array($result);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        if ($_REQUEST['do'] == "up")
        {
            $announce = 1;
        }
        else if ($_REQUEST['do'] == "down")
        {
            $announce = 0;
        }

        if ($visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _TOPICMODIFIED . "</div><br /><br />";

            $sql = mysql_query("UPDATE " . FORUM_THREADS_TABLE . " SET annonce = '" . $announce . "' WHERE id = '" . $_REQUEST['thread_id'] . "'");

            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            redirect($url, 2);
        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";

            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            redirect($url, 2);
        }

        closetable();
    }

    function reply()
    {
        global $user, $nuked, $captcha,$visiteur,$user_ip, $bgcolor3;

        opentable();

        if ($captcha == 1 && !ValidCaptchaCode($_REQUEST['code_confirm']))
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _BADCODECONFIRM . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div><br /><br />";
            closetable();
            footer();
            exit();
        }

        if ($_REQUEST['auteur'] == "" || $_REQUEST['titre'] == "" || $_REQUEST['texte'] == "" || @ctype_space($_REQUEST['titre']) || @ctype_space($_REQUEST['texte']))
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _FIELDEMPTY . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div><br /><br />";
            closetable();
            footer();
            exit();
        }

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
        list($modos) = mysql_fetch_array($result);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        $lock = mysql_query("SELECT closed FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND id = '" . $_REQUEST['thread_id'] . "'");
        list($closed) = mysql_fetch_array($lock);

        $forum = mysql_query("SELECT FT.level FROM " . FORUM_TABLE . " AS FT INNER JOIN " . FORUM_THREADS_TABLE . " AS FTT ON FT.id = FTT.forum_id WHERE FTT.id = '" . $_REQUEST['thread_id'] . "'");
        list($level) = mysql_fetch_array($forum);

        if ($visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            $auth = 1;
        }
        else if ($closed > 0 || $level > $visiteur)
        {
            $auth = 0;
        }

        if ($auth == "0")
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";

            $url = "index.php?file=Forum&page=post&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            redirect($url, 2);
            closetable();
            footer();
            exit();
        }

        if ($user[2] != "")
        {
            $autor = $user[2];
            $auteur_id = $user[0];
        }
        else
        {
            $_REQUEST['auteur'] = htmlentities($_REQUEST['auteur'], ENT_QUOTES);
            $_REQUEST['auteur'] = verif_pseudo($_REQUEST['auteur']);

            if ($_REQUEST['auteur'] == "error1")
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _PSEUDOFAILDED . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div><br /><br />";
                closetable();
                footer();
                exit();
            }
            else if ($_REQUEST['auteur'] == "error2")
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _RESERVNICK . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div><br /><br />";
                closetable();
                footer();
                exit();
            }
            else if ($_REQUEST['auteur'] == "error3")
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _BANNEDNICK . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div><br /><br />";
                closetable();
                footer();
                exit();
            }
            else
            {
                $autor = $_REQUEST['auteur'];
            }

        }

        $flood = mysql_query("SELECT date FROM " . FORUM_MESSAGES_TABLE . " WHERE auteur = '" . $autor . "' OR auteur_ip = '" . $user_ip . "' ORDER BY date DESC LIMIT 0, 1");
        list($flood_date) = mysql_fetch_row($flood);
        $anti_flood = $flood_date + $nuked['post_flood'];

        $date = time();

        if ($date < $anti_flood && $visiteur < admin_mod("Forum"))
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _NOFLOOD . "</div><br /><br />";
            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            redirect($url, 2);
            closetable();
            footer();
            exit();
        }

        $_REQUEST['texte'] = secu_html(html_entity_decode($_REQUEST['texte']));
        $_REQUEST['texte'] = icon($_REQUEST['texte']);
        $_REQUEST['titre'] = mysql_real_escape_string(stripslashes($_REQUEST['titre']));
        $_REQUEST['texte'] = mysql_real_escape_string(stripslashes($_REQUEST['texte']));
        $_REQUEST['texte'] = str_replace('<blockquote>', '<blockquote style="border: 1px dashed ' . $bgcolor3 . '; background: #FFF; color: #000; padding: 5px"><strong>' . _QUOTE . ' :</strong><br />', $_REQUEST['texte']);

        $autor = mysql_real_escape_string(stripslashes($autor));

        if (!is_numeric($_REQUEST['usersig'])) $_REQUEST['usersig'] = 0;
        if (!is_numeric($_REQUEST['emailnotify'])) $_REQUEST['emailnotify'] = 0;

        $filename = $_FILES['fichiernom']['name'];
        $filesize = $_FILES['fichiernom']['size'] / 1000;

        if ($visiteur >= $nuked['forum_file_level'] && $filename != "" && $nuked['forum_file'] == "on" && $nuked['forum_file_maxsize'] >= $filesize)
        {
            if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && $filename != ".htaccess")
            {
                $url_file = "upload/Forum/" . $filename;
                move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_file) or die ("<br /><br /><div style=\"text-align: center;\"><big><b>" . _UPLOADFAILED . "</b></big></div><br /><br />");
                @chmod ($url_file, 0644);
            }
        }
        else
        {
            $url_file = "";
        }


          mysql_query("UPDATE " . FORUM_THREADS_TABLE . " SET last_post = '" . $date . "' WHERE id = '" . (int) $_REQUEST['thread_id'] . "'");
          $SQL = "SELECT thread_id, forum_id, user_id FROM " . FORUM_READ_TABLE . "  WHERE thread_id LIKE '%," . (int) $_REQUEST['thread_id'] . ",%' OR forum_id LIKE '%," . (int) $_REQUEST['forum_id'] . ",%' ";
          $req = mysql_query($SQL);
          $update = "";
          while ($results = mysql_fetch_assoc($req)) {
               $tid = $results['thread_id'];
               $fid = $results['forum_id'];
               if (strrpos($fid, ',' . $_REQUEST['forum_id'] . ',') !== false) {
                    $fid = preg_replace("#," . $_REQUEST['forum_id'] . ",#is", ",", $fid);
               }
               if (strrpos($tid, ',' . $_REQUEST['thread_id'] . ',') !== false) {
                    $tid = preg_replace("#," . $_REQUEST['thread_id'] . ",#is", ",", $tid);
               }
                              $update .= (!empty($update) ? ', ':'');
                              $update .= "('" . $fid . "', '" . $tid ."', '" . $results['user_id'] . "')";
          }
          if(!empty($update)){
               $update = "INSERT INTO `" . FORUM_READ_TABLE . "` (forum_id, thread_id, user_id) VALUES $update ON DUPLICATE KEY UPDATE forum_id=VALUES(forum_id), thread_id=VALUES(thread_id);";
               mysql_query($update) or die(mysql_error());
          }

          mysql_query("INSERT INTO " . FORUM_MESSAGES_TABLE . " ( `id` , `titre` , `txt` , `date` , `edition` , `auteur` , `auteur_id` , `auteur_ip` , `usersig` , `emailnotify` , `thread_id` , `forum_id` , `file` ) VALUES ( '' , '" . $_REQUEST['titre'] . "' , '" . $_REQUEST['texte'] . "' , '" . $date . "' , '' , '" . $autor . "' , '" . $auteur_id . "' , '" . $user_ip . "' , '" . $_REQUEST['usersig'] . "' , '" . $_REQUEST['emailnotify'] . "' , '" . (int) $_REQUEST['thread_id'] . "' , '" . (int) $_REQUEST['forum_id'] . "' , '" . $filename . "' )");

          $notify = mysql_query("SELECT auteur_id FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . (int) $_REQUEST['thread_id'] . "' AND emailnotify = 1 GROUP BY auteur_id");
		$nbusers = mysql_num_rows($notify);

		if ($nbusers > 0)
		{
			while (list($usermail) = mysql_fetch_row($notify))
			{
				if($usermail != $auteur_id)
				{
							$getmail = mysql_query("SELECT mail FROM " . USER_TABLE . " WHERE id = '" . $usermail . "'");
							list($email) = mysql_fetch_row($getmail);
							$subject = _MESSAGE . " : " . $_REQUEST['titre'];
							$corps = _EMAILNOTIFYMAIL . "\r\n" . $nuked['url'] . "/index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'] . "\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
							$from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $nuked['mail'];

							$subject = @html_entity_decode($subject);
							$corps = @html_entity_decode($corps);
							$from = @html_entity_decode($from);

							mail($email, $subject, $corps, $from);
				}
			}
		}

		if ($user)
		{
			$sql_count = mysql_query("SELECT count FROM " . USER_TABLE . " WHERE id = '" . $user[0] . "'");
			list($count) = mysql_fetch_row($sql_count);
			$newcount = $count + 1;
			$upd = mysql_query("UPDATE " . USER_TABLE . " SET count = '" . $newcount . "' WHERE id = '" . $user[0] . "'");
		}

		$sql_page = mysql_query("SELECT id FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $_REQUEST['thread_id'] . "'");
		list($mess_id) = mysql_fetch_row($sql_page);
		$nb_rep = mysql_num_rows($sql_page);

		if ($nb_rep > $nuked['mess_forum_page'])
		{
			$topicpages = $nb_rep / $nuked['mess_forum_page'];
			$topicpages = ceil($topicpages);
			$link_post = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'] . "&p=" . $topicpages . "#" . $mess_id;
		}
		else
		{
			$link_post = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'] . "#" . $mess_id;
		}

		echo "<br /><br /><div style=\"text-align: center;\">" . _MESSAGESEND . "</div><br /><br />";
		redirect($link_post, 2);
        closetable();
    }

    function post()
    {
        global $user, $nuked,$captcha,$user_ip, $visiteur, $bgcolor3;

        opentable();

        if ($captcha == 1 && !ValidCaptchaCode($_REQUEST['code_confirm']))
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _BADCODECONFIRM . "<br /><br /><a href=\"javascript:history.back()\">[ <b>" . _BACK . "</b> ]</a></div><br /><br />";
            closetable();
            footer();
            exit();
        }

        if ($_REQUEST['auteur'] == "" || $_REQUEST['titre'] == "" || $_REQUEST['texte'] == "" || @ctype_space($_REQUEST['titre']) || @ctype_space($_REQUEST['texte']))
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _FIELDEMPTY . "</div><br /><br />";
            $url = "index.php?file=Forum&page=post&forum_id=" . $_REQUEST['forum_id'];
            redirect($url, 2);
            closetable();
            footer();
            exit();
        }

        $forum = mysql_query("SELECT level, level_poll FROM " . FORUM_TABLE . " WHERE id = '" . $_REQUEST['forum_id'] . "'");
        list($level, $level_poll) = mysql_fetch_array($forum);

        if ($level > $visiteur)
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
            $url = "index.php?file=Forum&page=post&forum_id=" . $_REQUEST['forum_id'];
            redirect($url, 2);
            closetable();
            footer();
            exit();
        }

        if ($user[2] != "")
        {
            $autor = $user[2];
            $auteur_id = $user[0];
        }
        else
        {
            $_REQUEST['auteur'] = htmlentities($_REQUEST['auteur'], ENT_QUOTES);
            $_REQUEST['auteur'] = verif_pseudo($_REQUEST['auteur']);

            if ($_REQUEST['auteur'] == "error1")
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _PSEUDOFAILDED . "</div><br /><br />";
                $url = "index.php?file=Forum&page=post&forum_id=" . $_REQUEST['forum_id'];
                redirect($url, 2);
                closetable();
                footer();
                exit();
            }
            else if ($_REQUEST['auteur'] == "error2")
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _RESERVNICK . "</div><br /><br />";
                $url = "index.php?file=Forum&page=post&forum_id=" . $_REQUEST['forum_id'];
                redirect($url, 2);
                closetable();
                footer();
                exit();
            }
            else if ($_REQUEST['auteur'] == "error3")
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _BANNEDNICK . "</div><br /><br />";
                $url = "index.php?file=Forum&page=post&forum_id=" . $_REQUEST['forum_id'];
                redirect($url, 2);
                closetable();
                footer();
                exit();
            }
            else
            {
                $autor = $_REQUEST['auteur'];
            }
        }

        $flood = mysql_query("SELECT date FROM " . FORUM_MESSAGES_TABLE . " WHERE auteur = '" . $autor . "' OR auteur_ip = '" . $user_ip . "' ORDER BY date DESC LIMIT 0, 1");
        list($flood_date) = mysql_fetch_row($flood);
        $anti_flood = $flood_date + $nuked['post_flood'];

        $date = time();

        if ($date < $anti_flood && $user[1] < admin_mod("Forum"))
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _NOFLOOD . "</div><br /><br />";
            $url = "index.php?file=Forum&page=viewforum&forum_id=" . $_REQUEST['forum_id'];
            redirect($url, 2);
            closetable();
            footer();
            exit();
        }
        $_REQUEST['texte'] = secu_html(html_entity_decode($_REQUEST['texte']));
        $_REQUEST['texte'] = icon($_REQUEST['texte']);
        $_REQUEST['titre'] = mysql_real_escape_string(stripslashes($_REQUEST['titre']));
        $_REQUEST['texte'] = mysql_real_escape_string(stripslashes($_REQUEST['texte']));
        $_REQUEST['texte'] = str_replace('<blockquote>', '<blockquote style="border: 1px dashed ' . $bgcolor3 . '; background: #FFF; color: #000; padding: 5px"><strong>' . _QUOTE . ' :</strong><br />', $_REQUEST['texte']);

        $autor = mysql_real_escape_string(stripslashes($autor));

        if (!is_numeric($_REQUEST['usersig'])) $_REQUEST['usersig'] = 0;
        if (!is_numeric($_REQUEST['emailnotify'])) $_REQUEST['emailnotify'] = 0;
        if (($visiteur < admin_mod("Forum") && $administrator == 0) || !is_numeric($_REQUEST['annonce'])) $_REQUEST['annonce'] = 0;

        if ($_REQUEST['survey'] == 1 && $_REQUEST['survey_field'] > 0 && $visiteur >= $level_poll)
        {
            $sondage = 1;
        }
        else
        {
            $sondage = 0;
        }

        $sql = mysql_query("INSERT INTO " . FORUM_THREADS_TABLE . " ( `id` , `titre` , `date` , `closed` , `auteur` , `auteur_id` , `forum_id` , `last_post` , `view` , `annonce` , `sondage` ) VALUES ( '' , '" . $_REQUEST['titre'] . "' , '" . $date . "' , '' , '" . $autor . "' , '" . $auteur_id . "' , '" . $_REQUEST['forum_id'] . "' , '" . $date . "' , '' , '" . $_REQUEST['annonce'] . "' , '" . $sondage . "' )");
        $req4 = mysql_query("SELECT MAX(id) FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND titre = '" . $_REQUEST['titre'] . "' AND date = '" . $date . "' AND auteur = '" . $_REQUEST['auteur'] . "'");
        $idmax = mysql_result($req4, 0, "MAX(id)");

        $_REQUEST['thread_id'] = $idmax;

        $filename = $_FILES['fichiernom']['name'];
        $filesize = $_FILES['fichiernom']['size'] / 1000;

        if ($visiteur >= $nuked['forum_file_level'] && $filename != "" && $nuked['forum_file'] == "on" && $nuked['forum_file_maxsize'] >= $filesize)
        {
            if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && $filename != ".htaccess")
            {
                $url_file = "upload/Forum/" . $filename;
                move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_file) or die ("<br /><br /><div style=\"text-align: center;\"><big><b>" . _UPLOADFAILED . "</b></big></div><br /><br />");
                @chmod ($url_file, 0644);
            }
        }
        else
        {
            $url_file = "";
        }

        $sql2 = mysql_query("INSERT INTO " . FORUM_MESSAGES_TABLE . " ( `id` , `titre` , `txt` , `date` , `edition` , `auteur` , `auteur_id` , `auteur_ip` , `usersig` , `emailnotify` , `thread_id` , `forum_id` , `file` ) VALUES ( '' , '" . $_REQUEST['titre'] . "' , '" . $_REQUEST['texte'] . "' , '" . $date . "' , '' , '" . $autor . "' , '" . $auteur_id . "' , '" . $user_ip . "' , '" . $_REQUEST['usersig'] . "' , '" . $_REQUEST['emailnotify'] . "' , '" . $_REQUEST['thread_id'] . "' , '" . $_REQUEST['forum_id'] . "' , '" . $filename . "' )");
          $SQL = "SELECT thread_id, forum_id, user_id FROM " . FORUM_READ_TABLE . "  WHERE thread_id LIKE '%," . (int) $_REQUEST['thread_id'] . ",%' OR forum_id LIKE '%," . (int) $_REQUEST['forum_id'] . ",%' ";
          $req = mysql_query($SQL);
          $update = "";
          while ($results = mysql_fetch_assoc($req)) {
               $tid = $results['thread_id'];
               $fid = $results['forum_id'];
               if (strrpos($fid, ',' . $_REQUEST['forum_id'] . ',') !== false) {
                    $fid = preg_replace("#," . $_REQUEST['forum_id'] . ",#is", ",", $fid);
               }
               if (strrpos($tid, ',' . $_REQUEST['thread_id'] . ',') !== false) {
                    $tid = preg_replace("#," . $_REQUEST['thread_id'] . ",#is", ",", $tid);
               }
               $update .= (!empty($update) ? ', ' : '');
               $update .= "('" . $fid . "', '" . $tid . "', '" . $results['user_id'] . "')";
          }
          if (!empty($update)) {
               $update = "INSERT INTO `" . FORUM_READ_TABLE . "` (forum_id, thread_id, user_id) VALUES $update ON DUPLICATE KEY UPDATE forum_id=VALUES(forum_id), thread_id=VALUES(thread_id);";
               mysql_query($update) or die(mysql_error());
          }
        if ($user)
        {
            $sql_count = mysql_query("SELECT count FROM " . USER_TABLE . " WHERE id = '" . $user[0] . "'");
            list($count) = mysql_fetch_row($sql_count);
            $newcount = $count + 1;
            $upd = mysql_query("UPDATE " . USER_TABLE . " SET count = '" . $newcount . "' WHERE id = '" . $user[0] . "'");
        }

        if ($_REQUEST['survey'] == 1 && $_REQUEST['survey_field'] > 0 && $visiteur >= $level_poll)
        {
            $url = "index.php?file=Forum&op=add_poll&survey_field=" . $_REQUEST['survey_field'] . "&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
        }
        else
        {
            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
        }

        echo "<br /><br /><div style=\"text-align: center;\">" . _MESSAGESEND . "</div><br /><br />";
        redirect($url, 2);
        closetable();
    }

    function mark()
    {
        global $user, $nuked, $cookie_forum;
        
        if ($user)
        {
            if ($_REQUEST['forum_id'] > 0)
            {
                $new_id = '';
                $table_read_forum = array();
                $id_read_forum = '';

                if (isset($_COOKIE[$cookie_forum]) && $_COOKIE[$cookie_forum] != "")
                {
                    $id_read_forum = $_COOKIE[$cookie_forum];
                    if (preg_match("`[^0-9,]`i", $id_read_forum)) $id_read_forum = "";
                    $table_read_forum = explode(',',$id_read_forum);
                }

                $req = "SELECT MAX(id) FROM " . FORUM_MESSAGES_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND date > '" . $user[4] . "' GROUP BY thread_id";
                $sql = mysql_query($req);
                while (list($max_id) = mysql_fetch_array($sql))
                {
                    if (!in_array($max_id,$table_read_forum))
                    {
                        if ($new_id != '')  $new_id .= ',';
                        $new_id .= $max_id;
                    }
                }

                if ($id_read_forum != '' && $new_id != '') $id_read_forum .= ',';
                $_COOKIE['cookie_forum'] = $id_read_forum . $new_id;
            }
            else
            {
                $_COOKIE['cookie_forum'] = '';
                $req = "UPDATE " . SESSIONS_TABLE . " SET last_used = date WHERE user_id = '" . $user[0] . "'";
                $sql = mysql_query($req);
            }
               if ($user) {
                    if ((int) $_REQUEST['forum_id'] != "") {
                         $where = "WHERE forum_id = '" . (int) $_REQUEST['forum_id'] . "'";
                    } else {
                    $where = "";
                } 
                    // On veut modifier la chaine thread_id et forum_id
                    $req = mysql_query("SELECT thread_id, forum_id FROM " . FORUM_READ_TABLE . " WHERE user_id = '" . $user[0] . "'");

                $result = mysql_query("SELECT id, forum_id FROM " . FORUM_THREADS_TABLE . " " . $where);
                $nbtopics = mysql_num_rows($result);

                    if ($nbtopics > 0) {
                         $res = mysql_fetch_assoc($req);
                         $tid = ',' . substr($res['thread_id'], 1);
                         $fid = ',' . substr($res['forum_id'], 1);
                         ;
                         while (list($thread_id, $forum_id) = mysql_fetch_row($result)) {
                              if (strrpos($tid, ',' . $thread_id . ',') === false)
                                   $tid .= $thread_id . ',';
                              if (strrpos($fid, ',' . $forum_id . ',') === false)
                                   $fid .= $forum_id . ',';
                    } 
                         $sql = mysql_query("REPLACE " . FORUM_READ_TABLE . " (`user_id` , `thread_id` , `forum_id` ) VALUES ('" . $user[0] . "' , '" . $tid . "' , '" . $fid . "' )");
                } 
            } 
        }
        opentable();
        echo "<br /><br /><div style=\"text-align: center;\">" . _MESSAGESMARK . "</div><br /><br />";
        redirect("index.php?file=Forum", 2);
        closetable();
    }

    function del_file()
    {
        global $visiteur, $user, $nuked;

        opentable();

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
        list($modos) = mysql_fetch_array($result);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        $sql = mysql_query("SELECT file, auteur_id FROM " . FORUM_MESSAGES_TABLE . " WHERE id = '" . $_REQUEST['mess_id'] . "'");
        list($filename, $auteur_id) = mysql_fetch_array($sql);

        if ($user && $auteur_id == $user[0] || $visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            $path = "upload/Forum/" . $filename;
            if (is_file($path))
            {
                $filesys = str_replace("/", "\\", $path);
                @chmod ($path, 0775);
                @unlink($path);
                @system("del $filesys");

                $upd = mysql_query("UPDATE " . FORUM_MESSAGES_TABLE . " SET file = '' WHERE id = '" . $_REQUEST['mess_id'] . "'");
                echo "<br /><br /><div style=\"text-align: center;\">" . _FILEDELETED . "</div><br /><br />";
                $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
                redirect($url, 2);
            }
        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            redirect($url, 2);
        }

        closetable();
    }

    function add_poll()
    {
        global $visiteur, $user, $nuked;

        opentable();

        $sql = mysql_query("SELECT auteur_id, sondage FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $_REQUEST['thread_id'] . "'");
        list($auteur_id, $sondage) = mysql_fetch_array($sql);

        $sql_poll = mysql_query("SELECT level_poll FROM " . FORUM_TABLE . " WHERE id = '" . $_REQUEST['forum_id'] . "'");
        list($level_poll) = mysql_fetch_array($sql_poll);

        if ($user && $user[0] == $auteur_id && $sondage == 1 && $visiteur >= $level_poll)
        {
            if ($_REQUEST['survey_field'] > $nuked['forum_field_max'])
            {
                $max = $nuked['forum_field_max'];
            }
            else
            {
                $max = $_REQUEST['survey_field'];
            }

            echo "<br /><div style=\"text-align: center;\"><big><b>" . _POSTSURVEY . "</b></big></div><br />\n"
            . "<form method=\"post\" action=\"index.php?file=Forum&amp;op=send_poll\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
            . "<tr><td align=\"right\"><b>" . _QUESTION . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" /></td></tr>\n"
            . "<tr><td>&nbsp;</td></tr>\n";

            $r = 0;
            while ($r < $max)
            {
                $r++;
                echo "<tr><td align=\"right\">" . _OPTION . "&nbsp;" . $r . " : <input type=\"text\" name=\"option[]\" size=\"40\" /></td></tr>\n";

            }

            echo "<tr><td>&nbsp;<input type=\"hidden\" name=\"thread_id\" value=\"" . $_REQUEST['thread_id'] . "\" />\n"
            . "<input type=\"hidden\" name=\"forum_id\" value=\"" . $_REQUEST['forum_id'] . "\" />\n"
            . "<input type=\"hidden\" name=\"max_option\" value=\"" . $max . "\" /></td></tr>\n"
            . "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _ADDTHISPOLL . "\" /></td></tr></table></form><br />\n";
        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            redirect($url, 2);
        }

        closetable();
    }

    function send_poll($titre, $option, $thread_id, $forum_id, $max_option)
    {
        global $visiteur, $user, $nuked;

        opentable();

        $sql = mysql_query("SELECT auteur_id, sondage FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $thread_id . "'");
        list($auteur_id, $sondage) = mysql_fetch_array($sql);

        $sql_poll = mysql_query("SELECT level_poll FROM " . FORUM_TABLE . " WHERE id = '" . $forum_id . "'");
        list($level_poll) = mysql_fetch_array($sql_poll);

        if ($user && $user[0] == $auteur_id && $sondage == 1 && $visiteur >= $level_poll)
        {
            if ($option[1] != "")
            {
                $titre = mysql_real_escape_string(stripslashes($titre));

                $add = mysql_query("INSERT INTO " . FORUM_POLL_TABLE . " ( `id` , `thread_id` , `titre` ) VALUES ( '' , '" . $thread_id . "' , '" . $titre . "' )");

                $sql2 = mysql_query("SELECT id FROM " . FORUM_POLL_TABLE . " WHERE thread_id = '" . $thread_id . "'");
                list($poll_id) = mysql_fetch_array($sql2);

                if ($max_option > $nuked['forum_field_max'])
                {
                    $max = $nuked['forum_field_max'];
                }
                else
                {
                    $max = $max_option;
                }

                $r = 0;
                while ($r < $max)
                {
                    $vid = $r + 1;
                    $options = $option[$r];
                    $options = mysql_real_escape_string(stripslashes($options));

                    if ($options != "")
                    {
                        $sql3 = mysql_query("INSERT INTO " . FORUM_OPTIONS_TABLE . " ( `id` , `poll_id` , `option_text` , `option_vote` ) VALUES ( '" . $vid . "' , '" . $poll_id . "' , '" . $options . "' , '' )");
                    }
                    $r++;
                }

                echo "<br /><br /><div style=\"text-align: center;\">" . _POLLADD . "</div><br /><br />";
                $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $forum_id . "&thread_id=" . $thread_id;
                redirect($url, 2);
            }
            else
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _2OPTIONMIN . "</div><br /><br />";
                $url = "index.php?file=Forum&op=add_poll&survey_field=" . $max_option . "&forum_id=" . $forum_id . "&thread_id=" . $thread_id;
                redirect($url, 2);
            }

        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $forum_id . "&thread_id=" . $thread_id;
            redirect($url, 2);
        }

        closetable();
    }

    function vote($poll_id)
    {
        global $visiteur, $user, $nuked,$user_ip;

        opentable();

        if ($_REQUEST['voteid'] != "")
        {
            if ($visiteur > 0)
            {
                $sql_poll = mysql_query("SELECT level_vote FROM " . FORUM_TABLE . " WHERE id = '" . $_REQUEST['forum_id'] . "'");
                list($level_vote) = mysql_fetch_array($sql_poll);

                if ($visiteur >= $level_vote)
                {
                    $sql = mysql_query("SELECT auteur_ip FROM " . FORUM_VOTE_TABLE . " WHERE auteur_id = '" . $user[0] . "' AND poll_id = '" . $poll_id . "'");
                    $check = mysql_num_rows($sql);

                    if ($check == 0)
                    {
                        $upd = mysql_query("UPDATE " . FORUM_OPTIONS_TABLE . " SET option_vote = option_vote + 1 WHERE id = '" . $_REQUEST['voteid'] . "' AND poll_id = '" . $poll_id . "'");
                        $insert = mysql_query("INSERT INTO " . FORUM_VOTE_TABLE . " ( `poll_id` , `auteur_id` , `auteur_ip` ) VALUES ( '" . $poll_id . "' , '" . $user[0] . "' , '" . $user_ip . "' )");

                        echo  "<br /><br /><div style=\"text-align: center;\">" . _VOTESUCCES . "</div><br /><br />";
                    }
                    else
                    {
                        echo "<br /><br /><div style=\"text-align: center;\">" . _ALREADYVOTE . "</div><br /><br />";
                    }

                }
                else
                {
                    echo "<br /><br /><div style=\"text-align: center;\">" . _BADLEVEL . "</div><br /><br />";
                }

            }
            else
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _ONLYMEMBERSVOTE . "</div><br /><br />";
            }

        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _NOOPTION . "</div><br /><br />";
        }

        $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
        redirect($url, 2);

        closetable();
    }

    function del_poll($poll_id, $thread_id, $forum_id)
    {
        global $visiteur, $user, $nuked;

        opentable();

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $forum_id . "'");
        list($modos) = mysql_fetch_array($result);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        $sql = mysql_query("SELECT auteur_id FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $thread_id . "'");
        list($auteur_id) = mysql_fetch_array($sql);

        if ($user && $user[0] == $auteur_id || $visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            if ($_REQUEST['confirm'] == _YES)
            {
                $del1 = mysql_query("DELETE FROM " . FORUM_POLL_TABLE . " WHERE id = '" . $poll_id . "'");
                $del2 = mysql_query("DELETE FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "'");
                $del2 = mysql_query("DELETE FROM " . FORUM_VOTE_TABLE . " WHERE poll_id = '" . $poll_id . "'");
                $upd = mysql_query("UPDATE " . FORUM_THREADS_TABLE . " SET sondage = 0 WHERE id = '" . $thread_id . "'");

                echo "<br /><br /><div style=\"text-align: center;\">" . _POLLDELETE . "</div><br /><br />";
                $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $forum_id . "&thread_id=" . $thread_id;
                redirect($url, 2);
            }
            else if ($_REQUEST['confirm'] == _NO)
            {
                echo "<br /><br /><br><center>" . _CONFIRMDELPOLL . "" . _DELCANCEL . "</div><br /><br />";
                $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $forum_id . "&thread_id=" . $thread_id;
                redirect($url, 2);
            }
            else
            {
                echo "<form method=\"post\" action=\"index.php?file=Forum&amp;op=del_poll\">\n"
                 . "<div style=\"text-align: center;\"><br />" . _CONFIRMDELPOLL . "<br /><br />\n"
                 . "<input type=\"hidden\" name=\"poll_id\" value=\"" . $poll_id . "\" />\n"
                 . "<input type=\"hidden\" name=\"thread_id\" value=\"" . $thread_id . "\" />\n"
                 . "<input type=\"hidden\" name=\"forum_id\" value=\"" . $forum_id . "\" />\n"
                 . "<input type=\"submit\" name=\"confirm\" value=\"" . _YES . "\" />\n"
                 . "&nbsp;<input type=\"submit\" name=\"confirm\" value=\"" . _NO . "\" /><br /></div></form>\n";
            }

        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $forum_id . "&thread_id=" . $thread_id;
            redirect($url, 2);
        }

        closetable();
    }

    function edit_poll($poll_id)
    {
        global $visiteur, $user, $nuked;

        opentable();

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $_REQUEST['forum_id'] . "'");
        list($modos) = mysql_fetch_array($result);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        $sql = mysql_query("SELECT auteur_id FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $_REQUEST['thread_id'] . "'");
        list($auteur_id) = mysql_fetch_array($sql);

        if ($user && $user[0] == $auteur_id || $visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            $sql1 = mysql_query("SELECT titre FROM " . FORUM_POLL_TABLE . " WHERE id = '" . $poll_id . "'");
            list($titre) = mysql_fetch_array($sql1);

            echo "<br /><div style=\"text-align: center;\"><big><b>" . _POSTSURVEY . "</b></big></div><br />\n"
            . "<form method=\"post\" action=\"index.php?file=Forum&amp;op=modif_poll\">\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
            . "<tr><td align=\"right\"><b>" . _QUESTION . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" value=\"" . $titre . "\" /></td></tr>\n"
            . "<tr><td>&nbsp;</td></tr>\n";

            $sql2 = mysql_query("SELECT id, option_text FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "' ORDER BY id ASC");
            $r = 0;
            while (list($option_id, $option_text) = mysql_fetch_array($sql2))
            {
                $r++;
                echo "<tr><td align=\"right\">" . _OPTION . "&nbsp;" . $r . " : <input type=\"text\" name=\"option[" . $r . "]\" size=\"40\" value=\"" . $option_text . "\" /></td></tr>\n";
            }

            $r++;

            echo "<tr><td align=\"right\">" . _OPTION . "&nbsp;" . $r . " : <input type=\"text\" name=\"newoption\" size=\"40\" /></td></tr>\n"
            . "<tr><td>&nbsp;<input type=\"hidden\" name=\"poll_id\" value=\"" . $poll_id . "\" />\n"
            . "<input type=\"hidden\" name=\"thread_id\" value=\"" . $_REQUEST['thread_id'] . "\" />\n"
            . "<input type=\"hidden\" name=\"forum_id\" value=\"" . $_REQUEST['forum_id'] . "\" /></td></tr>\n"
            . "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _MODIFTHISPOLL . "\" /></td></tr></table></form><br />\n";

        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
            $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
            redirect($url, 2);
        }

        closetable();
    }

    function modif_poll($poll_id, $titre, $option, $newoption, $thread_id, $forum_id)
    {
        global $visiteur, $user, $nuked;

        opentable();

        $result = mysql_query("SELECT moderateurs FROM " . FORUM_TABLE . " WHERE '" . $visiteur . "' >= niveau AND id = '" . $forum_id . "'");
        list($modos) = mysql_fetch_array($result);

        if ($user && $modos != "" && strpos($modos, $user[0]) !== false)
        {
            $administrator = 1;
        }
        else
        {
            $administrator = 0;
        }

        $sql = mysql_query("SELECT auteur_id FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $thread_id . "'");
        list($auteur_id) = mysql_fetch_array($sql);

        if ($user && $user[0] == $auteur_id || $visiteur >= admin_mod("Forum") || $administrator == 1)
        {
            $titre = mysql_real_escape_string(stripslashes($titre));

            $upd1 = mysql_query("UPDATE " . FORUM_POLL_TABLE . " SET titre = '" . $titre . "' WHERE id = '" . $poll_id . "'");

            $r = 0;
            while ($r < $nuked['forum_field_max'])
            {
                $r++;
                $options = $option[$r];
                $options = mysql_real_escape_string(stripslashes($options));

                if ($options != "")
                {
                    $upd2 = mysql_query("UPDATE " . FORUM_OPTIONS_TABLE . " SET option_text = '" . $options . "' WHERE poll_id = '" . $poll_id . "' AND id = '" . $r . "'");
                }
                else
                {
                    $del = mysql_query("DELETE FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "' AND id = '" . $r . "'");
                }

            }

            if ($newoption != "")
            {
                $newoption = mysql_real_escape_string(stripslashes($newoption));
                $sql2 = mysql_query("SELECT id FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "' ORDER BY id DESC LIMIT 0, 1");
                list($option_id) = mysql_fetch_array($sql2);
                $s = $option_id + 1;

                $sql3 = mysql_query("INSERT INTO " . FORUM_OPTIONS_TABLE . " ( `id` , `poll_id` , `option_text` , `option_vote` ) VALUES ( '" . $s . "' , '" . $poll_id . "' , '" . $newoption . "', '0')");
            }

            echo "<br /><br /><div style=\"text-align: center;\">" . _POLLMODIF . "</div><br /><br />";
        }
        else
        {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
        }

        $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $forum_id . "&thread_id=" . $thread_id;
        redirect($url, 2);
        closetable();
    }


    function notify()
    {
        global $user, $nuked;

        opentable();

    if ($user[0] != "")
    {

            if ($_REQUEST['do'] == "on")
            {
            $notify = 1;
            $notify_texte = _NOTIFYISON;
            }
            else
            {
            $notify = 0;
            $notify_texte = _NOTIFYISOFF;
            }

        $upd = mysql_query("UPDATE " . FORUM_MESSAGES_TABLE . " SET emailnotify = '" . $notify . "' WHERE thread_id = '" . $_REQUEST['thread_id'] . "' AND auteur_id = '" . $user[0] . "'");

        echo "<br /><br /><div style=\"text-align: center;\">" . $notify_texte . "</div><br /><br />";

    }
    else
    {
            echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "</div><br /><br />";
    }

        $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
        redirect($url, 2);
        closetable();
    }


    switch ($_REQUEST['op'])
    {
        case"index":
            index();
            break;

        case"post":
            post();
            break;

        case"reply":
            reply();
            break;

        case"edit":
            edit($_REQUEST['mess_id']);
            break;

        case"del":
            del($_REQUEST['mess_id']);
            break;

        case"del_topic":
            del_topic($_REQUEST['thread_id']);
            break;

        case"move":
            move();
            break;

        case"lock":
            lock();
            break;

        case"announce":
            announce();
            break;

        case"mark":
            mark();
            break;

        case"del_file":
            del_file();
            break;

        case"add_poll":
            add_poll();
            break;

        case"send_poll":
            send_poll($_REQUEST['titre'], $_REQUEST['option'], $_REQUEST['thread_id'], $_REQUEST['forum_id'], $_REQUEST['max_option']);
            break;

        case"vote":
            vote($_REQUEST['poll_id']);
            break;

        case"del_poll":
            del_poll($_REQUEST['poll_id'], $_REQUEST['thread_id'], $_REQUEST['forum_id']);
            break;

        case"edit_poll":
            edit_poll($_REQUEST['poll_id']);
            break;

        case"modif_poll":
            modif_poll($_REQUEST['poll_id'], $_REQUEST['titre'], $_REQUEST['option'], $_REQUEST['newoption'], $_REQUEST['thread_id'], $_REQUEST['forum_id']);
            break;

        case"notify":
            notify();
            break;

        default:
            index();
            break;
    }

}
else if ($level_access == -1)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
}
else if ($level_access == 1 && $visiteur == 0)
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>";
    closetable();
}
else
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
}

?>
