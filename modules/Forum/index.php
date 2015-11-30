<?php
/**
 * index.php
 *
 * Frontend of Forum module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Forum'))
    return;

compteur('Forum');

$captcha = initCaptcha();


function index() {
    opentable();
    require 'modules/Forum/main.php';
    closetable();
}

function isForumAdministrator($forumId) {
    global $user, $visiteur;

    $dbrForum = nkDB_selectOne(
        'SELECT moderateurs
        FROM '. FORUM_TABLE .'
        WHERE '. $visiteur .' >= level AND id = '. nkDB_escape($forumId)
    );

    return $visiteur >= admin_mod('Forum') ||
        ($user && $dbrForum['moderateurs'] != '' && strpos($dbrForum['moderateurs'], $user['id']) !== false);
}

function edit($mess_id) {
    global $user, $nuked;

    opentable();

    if ($_REQUEST['titre'] == '' || $_REQUEST['texte'] == '' || @ctype_space($_REQUEST['titre']) || @ctype_space($_REQUEST['texte'])) {
        printNotification(_FIELDEMPTY, 'warning');
        redirect('index.php?file=Forum&page=post&forum_id='. $_REQUEST['forum_id'] .'&mess_id='. $_REQUEST['mess_id'] .'&do=edit', 2);
        closetable();
        return;
    }

    if ($_REQUEST['author'] == $user['name'] || isForumAdministrator($_REQUEST['forum_id'])) {
        $data = array('titre' => stripslashes($_REQUEST['titre']));

        $data['txt'] = secu_html(nkHtmlEntityDecode($_REQUEST['texte']));
        $data['txt'] = icon($data['txt']);
        $data['txt'] = stripslashes($data['txt']);

        $data['usersig']        = (! is_numeric($_REQUEST['usersig'])) ? 0 : $_REQUEST['usersig'];
        $data['emailnotify']    = (! is_numeric($_REQUEST['emailnotify'])) ? 0 : $_REQUEST['emailnotify'];

        if ($_REQUEST['edit_text'] == 1)
            $data['edition'] = _EDITBY .'&nbsp;'. $user['name'] .'&nbsp;'. _THE .'&nbsp;'. nkDate(time());

        $dbrForumMessage = nkDB_selectOne(
            'SELECT thread_id
            FROM '. FORUM_MESSAGES_TABLE .'
            WHERE id = '. nkDB_escape($mess_id)
        );

        $thread_id = $dbrForumMessage['thread_id'];

        $dbrForumMessage = nkDB_selectOne(
            'SELECT id
            FROM '. FORUM_MESSAGES_TABLE .'
            WHERE thread_id = '. nkDB_escape($thread_id),
            array('id'), 'ASC', 1
        );

        $mid = $dbrForumMessage['id'];

        nkDB_update(FORUM_MESSAGES_TABLE, $data, 'id = '. nkDB_escape($mess_id));

        if ($mid == $mess_id)
            nkDB_update(FORUM_THREADS_TABLE, array('titre' => $data['titre']), 'id = '. nkDB_escape($thread_id));

        $nb_rep = nkDB_totalNumRows(
            'FROM '. FORUM_MESSAGES_TABLE .'
            WHERE thread_id = '. nkDB_escape($thread_id)
        );

        $url = 'index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $thread_id;

        if ($nb_rep > $nuked['mess_forum_page']) {
            $topicpages = $nb_rep / $nuked['mess_forum_page'];
            $topicpages = ceil($topicpages);

            $url .= '&p='. $topicpages .'#'. $mess_id;
        }
        else {
            $url .= '#'. $mess_id;
        }

        printNotification(_MESSMODIF, 'success');
    }
    else
    {
        printNotification(_ZONEADMIN, 'error');
        $url = 'index.php?file=Forum';
    }

    redirect($url, 2);
    closetable();
}

function del($mess_id) {
    opentable();

    if (isForumAdministrator($_REQUEST['forum_id'])) {
        if ($_REQUEST['confirm'] == _YES) {
            $dbrForumMessage = nkDB_selectOne(
                'SELECT id, file
                FROM '. FORUM_MESSAGES_TABLE .'
                WHERE thread_id = '. nkDB_escape($_REQUEST['thread_id']),
                array('id'), 'ASC', 1
            );

            $mid = $dbrForumMessage['id'];
            $filename = $dbrForumMessage['file'];

            if ($filename != '') {
                $path = 'upload/Forum/'. $filename;

                if (is_file($path)) {
                    @chmod($path, 0775);
                    @unlink($path);
                }
            }

            $url = 'index.php?file=Forum&page=viewforum&forum_id='. (int) $_REQUEST['forum_id'];

            if ($mid == $mess_id) {
                $dbrForumThread = nkDB_selectOne(
                    'SELECT sondage
                    FROM '. FORUM_THREADS_TABLE .'
                    WHERE id = '. nkDB_escape($_REQUEST['thread_id'])
                );

                $sondage = $dbrForumThread['sondage'];

                if ($sondage == 1) {
                    $dbrForumPoll = nkDB_selectOne(
                        'SELECT id
                        FROM '. FORUM_POLL_TABLE .'
                        WHERE thread_id = '. nkDB_escape($_REQUEST['thread_id'])
                    );

                    $poll_id = $dbrForumPoll['id'];

                    nkDB_delete(FORUM_POLL_TABLE, 'id = '. nkDB_escape($poll_id));
                    nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. nkDB_escape($poll_id));
                    nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. nkDB_escape($poll_id));
                }

                nkDB_delete(FORUM_THREADS_TABLE, 'id = '. nkDB_escape((int) $_REQUEST['thread_id']));
                nkDB_delete(FORUM_MESSAGES_TABLE, 'thread_id = '. nkDB_escape((int) $_REQUEST['thread_id']));
            }
            else {
                $url .= '&thread_id='. (int) $_REQUEST['thread_id'];
            }

            nkDB_delete(FORUM_MESSAGES_TABLE, 'id = '. nkDB_escape($mess_id));

            printNotification(_MESSDELETED, 'success');
            redirect($url, 2);
        }
        else if ($_REQUEST['confirm'] == _NO) {
            printNotification(_DELCANCEL, 'warning');
            redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);
        }
        else {
            echo applyTemplate('confirm', array(
                'url'       => 'index.php?file=Forum&amp;op=del',
                'message'   => _CONFIRMDELMESS,
                'fields'    => array(
                    'mess_id'   => $mess_id,
                    'thread_id' => $thread_id,
                    'forum_id'  => $forum_id
                ),
            ));
        }
    }
    else {
        printNotification(_ZONEADMIN, 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);
    }

    closetable();
}

function del_topic($thread_id) {
    opentable();

    if (isForumAdministrator($_REQUEST['forum_id'])) {
        if ($_REQUEST['confirm'] == _YES) {
            $dbrForumThread = nkDB_selectOne(
                'SELECT sondage
                FROM '. FORUM_THREADS_TABLE .'
                WHERE id = '. nkDB_escape($thread_id)
            );

            $sondage = $dbrForumThread['sondage'];

            if ($sondage == 1) {
                $dbrForumPoll = nkDB_selectOne(
                    'SELECT id
                    FROM '. FORUM_POLL_TABLE .'
                    WHERE thread_id = '. nkDB_escape($thread_id)
                );

                $poll_id = $dbrForumPoll['id'];

                nkDB_delete(FORUM_POLL_TABLE, 'id = '. nkDB_escape($poll_id));
                nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. nkDB_escape($poll_id));
                nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. nkDB_escape($poll_id));
            }

            $dbrForumMessage = nkDB_selectMany(
                'SELECT file
                FROM '. FORUM_MESSAGES_TABLE .'
                WHERE thread_id = '. nkDB_escape($thread_id)
            );

            foreach ($dbrForumMessage as $forumMessage) {
                if ($forumMessage['file'] != '') {
                    $path = 'upload/Forum/'. $forumMessage['file'];

                    if (is_file($path)) {
                        @chmod($path, 0775);
                        @unlink($path);
                    }
                }
            }

            nkDB_delete(FORUM_MESSAGES_TABLE, 'thread_id = '. nkDB_escape($thread_id) .' AND forum_id = '. nkDB_escape((int) $_REQUEST['forum_id']));
            nkDB_delete(FORUM_THREADS_TABLE, 'id = '. nkDB_escape($thread_id) .' AND forum_id = '. nkDB_escape((int) $_REQUEST['forum_id']));

            printNotification(_TOPICDELETED, 'success');
            redirect('index.php?file=Forum&page=viewforum&forum_id='. $_REQUEST['forum_id'], 2);
        }
        else if ($_REQUEST['confirm'] == _NO) {
            printNotification(_DELCANCEL, 'warning');
            redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $thread_id, 2);
        }
        else {
            echo applyTemplate('confirm', array(
                'url'       => 'index.php?file=Forum&amp;op=del_topic',
                'message'   => _CONFIRMDELTOPIC,
                'fields'    => array(
                    'thread_id' => $thread_id,
                    'forum_id'  => $forum_id
                ),
            ));
        }
    }
    else {
        printNotification(_ZONEADMIN, 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $thread_id, 2);
    }

    closetable();
}

function move() {
    global $visiteur;

    opentable();

    if (isForumAdministrator($_REQUEST['forum_id'])) {
        if ($_REQUEST['confirm'] == _YES && $_REQUEST['newforum'] != '') {
            printNotification(_TOPICMOVED, 'success');

            nkDB_update(FORUM_THREADS_TABLE,
                array('forum_id' => $_REQUEST['newforum']),
                'id = '. nkDB_escape((int) $_REQUEST['thread_id'])
            );

            nkDB_update(FORUM_MESSAGES_TABLE,
                array('forum_id' => $_REQUEST['newforum']),
                'thread_id = '. nkDB_escape((int) $_REQUEST['thread_id'])
            );

            $dbrForumRead = nkDB_selectMany(
                'SELECT thread_id, forum_id, user_id
                FROM '. FORUM_READ_TABLE .'
                WHERE forum_id LIKE \'%,'. nkDB_escape($_REQUEST['forum_id'], 'no-quote') .',%\'
                OR forum_id LIKE \'%,'. nkDB_escape($_REQUEST['newforum'], 'no-quote') .',%\''
            );

            // Liste des utilisateurs
            $userTMP = array();

            foreach ($dbrForumRead as $forumRead) {
                $userTMP[$forumRead['user_id']] = array(
                    'forum_id'  => $forumRead['forum_id'],
                    'thread_id' => $forumRead['thread_id']
                );
            }

            // Vieux forum
            $oldTMP = array();

            // Liste des threads de l'ancien forum
            $dbrForumThread = nkDB_selectMany(
                'SELECT id
                FROM '. FORUM_THREADS_TABLE .'
                WHERE forum_id = '. nkDB_escape((int) $_REQUEST['forum_id'])
            );

            // On vérifie que tous les threads sont lus
            foreach ($dbrForumThread as $forumThread)
                $oldTMP[$forumThread['id']] = $forumThread['id'];

            // Nouveau forum
            $newTMP = array();

            // Liste des threads du nouveau forum
            $dbrForumThread = nkDB_selectMany(
                'SELECT id
                FROM '. FORUM_THREADS_TABLE .'
                WHERE forum_id = '. nkDB_escape((int) $_REQUEST['newforum'])
            );

            // On vérifie que tous les threads sont lus
            foreach ($dbrForumThread as $forumThread)
                $newTMP[$forumThread['id']] = $forumThread['id'];

            $update = '';
            //$update = array();

            // On boucle les users
            foreach ($userTMP as $key => $member) {
                // On part du fait que tout les posts sont lu
                $read = true;

                foreach ($oldTMP as $old) {
                    // Si au moins un post n'est pas lu
                    if (strrpos($member['thread_id'], ','. $old .',') === false)
                        $read = false;
                }

                // Si ils sont tous lu, et que le forum est pas dans la liste on le rajoute
                if ($read === true && strrpos($member['forum_id'], ','. $_REQUEST['forum_id'] .',') === false) {
                    // Nouvelle liste des forums
                    $fid = $member['forum_id'] . $_REQUEST['forum_id'] .',';
                    // Si aucun update n'a eu lieu avant
                    $update .= (! empty($update)) ? ', ' : '';
                    $update .= "('". $fid ."', '". $key ."')";
                    //$update[] = 
                }

                // On part du fait que tout les posts sont lu
                $read = true;

                foreach($newTMP as $new){
                    // Si au moins un post n'est pas lu
                    if (strrpos($member['thread_id'], ','. $new .',') === false)
                        $read = false;
                }

                // Si tout n'est pas lu, et que le forum est présent dans la liste on le retire
                if ($read === false && strrpos($fid, ','. $_REQUEST['newforum'] .',') !== false) {
                    // Nouvelle liste des forums
                    $fid = preg_replace("#," . $_REQUEST['newforum'] . ",#is", ",", $fid);

                    // Si aucun n'update n'a eu lieu avant
                    $update .= (!empty($update) ? ', ':'');
                    $update .= "('" . $fid . "', '" . $key . "')";
                }

            }

            if(!empty($update)){
                $update = "INSERT INTO `" . FORUM_READ_TABLE . "`
                    (forum_id, user_id)
                    VALUES $update
                    ON DUPLICATE KEY UPDATE forum_id = VALUES(forum_id);";
                nkDB_execute($update);
            }

            redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['newforum'] .'&thread_id='. (int) $_REQUEST['thread_id'], 2);
        }
        else if ($_REQUEST['confirm'] == _NO) {
            printNotification(_DELCANCEL, 'warning');

            redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);
        }
        else {
            echo "<form action=\"index.php?file=Forum&amp;op=move\" method=\"post\">\n"
            . "<div id=\"nkAlertWarning\" class=\"nkAlert\"><span class=\"nkAlertSubTitle\">" . _MOVETOPIC . " : </span><select name=\"newforum\">\n";

            $sql_cat = mysql_query("SELECT id, nom FROM " . FORUM_CAT_TABLE . " WHERE '" . $visiteur . "' >= niveau ORDER BY ordre, nom");

            while (list($cat, $cat_name) = mysql_fetch_row($sql_cat)) {
                $cat_name = printSecuTags($cat_name);

                echo "<option value=\"\">* " . $cat_name . "</option>\n";

                $sql_forum = mysql_query("SELECT nom, id FROM " . FORUM_TABLE . " WHERE cat = '" . $cat . "' AND '" . $visiteur . "' >= niveau ORDER BY ordre, nom");

                while (list($forum_name, $fid) = mysql_fetch_row($sql_forum)) {
                    $forum_name = printSecuTags($forum_name);

                    echo "<option value=\"" . $fid . "\">&nbsp;&nbsp;&nbsp;" . $forum_name . "</option>\n";
                }
            }

            echo "</select><br /><br /><input type=\"submit\" name=\"confirm\" value=\"" . _YES . "\" class=\"nkButton\" />"
            . "&nbsp;<input type=\"submit\" name=\"confirm\" value=\"" . _NO . "\" class=\"nkButton\" />\n"
            . "<input type=\"hidden\" name=\"forum_id\" value=\"".$_REQUEST['forum_id']."\" />\n"
            . "<input type=\"hidden\" name=\"thread_id\" value=\"".$_REQUEST['thread_id']."\" /></div></form><br />\n";
        }
    }
    else {
        printNotification(_ZONEADMIN, 'error');
        $url = "index.php?file=Forum&page=viewtopic&forum_id=" . $_REQUEST['forum_id'] . "&thread_id=" . $_REQUEST['thread_id'];
        redirect($url, 2);
    }

    closetable();
}

function lock() {
    opentable();

    if (isForumAdministrator($_REQUEST['forum_id'])) {
        if ($_REQUEST['do'] == 'close') {
            printNotification(_TOPICLOCKED, 'success');
            $closed = 1;
        }
        else if ($_REQUEST['do'] == 'open') {
            printNotification(_TOPICUNLOCKED, 'success');
            $closed = 0;
        }

        nkDB_update(FORUM_THREADS_TABLE, array('closed' => $closed), 'id = '. nkDB_escape($_REQUEST['thread_id']));
    }
    else
        printNotification(_ZONEADMIN, 'error');

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);

    closetable();
}

function announce() {
    opentable();

    if ($_REQUEST['do'] == 'up')
        $announce = 1;
    else if ($_REQUEST['do'] == 'down')
        $announce = 0;

    if (isForumAdministrator($_REQUEST['forum_id'])) {
        printNotification(_TOPICMODIFIED, 'success');

        nkDB_update(FORUM_THREADS_TABLE, array('annonce' => $announce), 'id = '. nkDB_escape($_REQUEST['thread_id']));
    }
    else
        printNotification(_ZONEADMIN, 'error');

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);
    closetable();
}

function reply() {
    global $user, $nuked, $visiteur,$user_ip, $bgcolor3;

    opentable();

    if ($GLOBALS['captcha'] === true)
        ValidCaptchaCode();

    if ($_REQUEST['auteur'] == '' || $_REQUEST['titre'] == '' || $_REQUEST['texte'] == '' || @ctype_space($_REQUEST['titre']) || @ctype_space($_REQUEST['texte'])) {
        printNotification(_FIELDEMPTY, 'warning', 'javascript:history.back()');
        closetable();
        return;
    }

    $dbrForumThread = nkDB_selectOne(
        'SELECT closed
        FROM '. FORUM_THREADS_TABLE .'
        WHERE forum_id = '. nkDB_escape((int) $_REQUEST['forum_id']) .' AND id = '. nkDB_escape($_REQUEST['thread_id'])
    );

    $dbrForum = nkDB_selectOne(
        'SELECT FT.level
        FROM '. FORUM_TABLE .' AS FT
        INNER JOIN '. FORUM_THREADS_TABLE .' AS FTT
        ON FT.id = FTT.forum_id
        WHERE FTT.id = '. nkDB_escape($_REQUEST['thread_id'])
    );

    if (isForumAdministrator($_REQUEST['forum_id']))
        $auth = 1;
    else if ($dbrForumThread['closed'] > 0 || $dbrForum['level'] > $visiteur)
        $auth = 0;

    if ($auth == 0) {
        printNotification(_ZONEADMIN, 'error');
        redirect('index.php?file=Forum&page=post&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);
        closetable();
        return;
    }

    if ($user['name'] != '') {
        $autor = $user['name'];
        $auteur_id = $user['id'];
    }
    else {
        $_REQUEST['auteur'] = nkHtmlEntities($_REQUEST['auteur'], ENT_QUOTES);
        $_REQUEST['auteur'] = verif_pseudo($_REQUEST['auteur']);

        if ($_REQUEST['auteur'] == 'error1') {
            printNotification(_PSEUDOFAILDED, 'error', 'javascript:history.back()');
            closetable();
            return;
        }
        else if ($_REQUEST['auteur'] == 'error2') {
            printNotification(_RESERVNICK, 'error', 'javascript:history.back()');
            closetable();
            return;
        }
        else if ($_REQUEST['auteur'] == 'error3') {
            printNotification(_BANNEDNICK, 'error', 'javascript:history.back()');
            closetable();
            return;
        }
        else {
            $autor = $_REQUEST['auteur'];
        }
    }

    $dbrForumMessage = nkDB_selectOne(
        'SELECT date
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE auteur = '. nkDB_escape($autor) .' OR auteur_ip = '. nkDB_escape($user_ip),
        array('date'), 'DESC', 1
    );

    $anti_flood = $dbrForumMessage['date'] + $nuked['post_flood'];

    $date = time();

    if ($date < $anti_flood && $visiteur < admin_mod('Forum')) {
        printNotification(_NOFLOOD, 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);
        closetable();
        return;
    }

    $_REQUEST['titre'] = mysql_real_escape_string(stripslashes($_REQUEST['titre']));

    $_REQUEST['texte'] = secu_html(nkHtmlEntityDecode($_REQUEST['texte']));
    $_REQUEST['texte'] = icon($_REQUEST['texte']);
    $_REQUEST['texte'] = mysql_real_escape_string(stripslashes($_REQUEST['texte']));
    $_REQUEST['texte'] = str_replace('<blockquote>', '<blockquote class="nkForumBlockQuote">', $_REQUEST['texte']);

    $autor = mysql_real_escape_string(stripslashes($autor));

    if (! is_numeric($_REQUEST['usersig'])) $_REQUEST['usersig'] = 0;
    if (! is_numeric($_REQUEST['emailnotify'])) $_REQUEST['emailnotify'] = 0;

    if ($visiteur >= $nuked['forum_file_level']
        && $nuked['forum_file'] == 'on'
        && $_FILES['fichiernom']['name'] != ''
    ) {
        list($url_file, $uploadError) = nkUpload_check(
            'fichiernom', 'no-html-php', 'upload/Forum', $nuked['forum_file_maxsize'], true
        );

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            //redirect('index.php?file=Forum&page=admin&op=editForum'. (($id > 0) ? '&id='. $id : ''), 2);
            return;
        }
    }
    else {
        $url_file = '';
    }

    nkDB_update(FORUM_THREADS_TABLE, array('last_post' => $date), 'id = '. nkDB_escape((int) $_REQUEST['thread_id']));

    $dbrForumRead = nkDB_selectMany(
        'SELECT thread_id, forum_id, user_id
        FROM '. FORUM_READ_TABLE .'
        WHERE thread_id LIKE \'%,'. nkDB_escape((int) $_REQUEST['thread_id'], 'no-quote') .',%\'
        OR forum_id LIKE \'%,'. nkDB_escape((int) $_REQUEST['forum_id'], 'no-quote') .',%\''
    );

    $update = '';

    foreach ($dbrForumRead as $forumRead) {
        $tid = $forumRead['thread_id'];
        $fid = $forumRead['forum_id'];

        if (strrpos($fid, ',' . $_REQUEST['forum_id'] . ',') !== false)
            $fid = preg_replace('#,'. $_REQUEST['forum_id'] .',#is', ',', $fid);


        if (strrpos($tid, ',' . $_REQUEST['thread_id'] . ',') !== false)
            $tid = preg_replace('#,'. $_REQUEST['thread_id'] .',#is', ',', $tid);

        $update .= (! empty($update)) ? ', ' : '';
        $update .= "('" . $fid . "', '" . $tid ."', '" . $forumRead['user_id'] . "')";
    }

    if (! empty($update)) {
        $update = "INSERT INTO `" . FORUM_READ_TABLE . "`
            (forum_id, thread_id, user_id) VALUES $update
            ON DUPLICATE KEY UPDATE forum_id=VALUES(forum_id), thread_id=VALUES(thread_id);";
        nkDB_execute($update);
    }

    nkDB_insert(FORUM_MESSAGES_TABLE, array(
        'titre'         => $_REQUEST['titre'],
        'txt'           => $_REQUEST['texte'],
        'date'          => $date,
        'edition'       => '',
        'auteur'        => $autor,
        'auteur_id'     => $auteur_id,
        'auteur_ip'     => $user_ip,
        'usersig'       => $_REQUEST['usersig'],
        'emailnotify'   => $_REQUEST['emailnotify'],
        'thread_id'     => (int) $_REQUEST['thread_id'],
        'forum_id'      => (int) $_REQUEST['forum_id'],
        'file'          => $filename
    ));

    $dbrForumMessage = nkDB_selectMany(
        'SELECT auteur_id
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE thread_id = '. nkDB_escape((int) $_REQUEST['thread_id']) .' AND emailnotify = 1
        GROUP BY auteur_id'
    );

    if (nkDB_numRows() > 0) {
        foreach ($dbrForumMessage as $forumMessage) {
            if ($forumMessage['auteur_id'] != $auteur_id) {
                $dbrUser = nkDB_selectMany(
                    'SELECT mail
                    FROM '. USER_TABLE .'
                    WHERE id = '. nkDB_escape($forumMessage['auteur_id'])
                );

                $subject    = _MESSAGE .' : '. $_REQUEST['titre'];
                $corps      = _EMAILNOTIFYMAIL ."\r\n"
                            . $nuked['url'] .'/index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'] ."\r\n\r\n\r\n"
                            . $nuked['name'] .' - '. $nuked['slogan'];
                $from       = 'From: '. $nuked['name'] .' <'. $nuked['mail'] .'>' ."\r\n"
                            . 'Reply-To: '. $nuked['mail'];

                $subject    = @nkHtmlEntityDecode($subject);
                $corps      = @nkHtmlEntityDecode($corps);
                $from       = @nkHtmlEntityDecode($from);

                mail($dbrUser['mail'], $subject, $corps, $from);
            }
        }
    }

    if ($user)
        nkDB_update(USER_TABLE, array('count' =>array('count + 1', 'no-escape')), 'id = '. nkDB_escape($user['id']));

    $dbrForumMessage = nkDB_selectOne(
        'SELECT id
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE thread_id = '. nkDB_escape($_REQUEST['thread_id'])
    );

    $nb_rep = nkDB_numRows();

    $link_post = 'index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'];

    if ($nb_rep > $nuked['mess_forum_page']) {
        $topicpages = $nb_rep / $nuked['mess_forum_page'];
        $topicpages = ceil($topicpages);
        $link_post .= '&p='. $topicpages .'#'. $dbrForumMessage['id'];
    }
    else {
        $link_post .= '#'. $dbrForumMessage['id'];
    }

    printNotification(_MESSAGESEND, 'success');
    redirect($link_post, 2);
    closetable();
}

function post() {
    global $user, $nuked, $user_ip, $visiteur, $bgcolor3;

    opentable();

    if ($GLOBALS['captcha'] === true)
        ValidCaptchaCode();

    if ($_REQUEST['auteur'] == '' || $_REQUEST['titre'] == '' || $_REQUEST['texte'] == '' || @ctype_space($_REQUEST['titre']) || @ctype_space($_REQUEST['texte'])) {
        printNotification(_FIELDEMPTY, 'warning');
        redirect('index.php?file=Forum&page=post&forum_id='. $_REQUEST['forum_id'], 2);
        closetable();
        return;
    }

    $dbrForum = nkDB_selectOne(
        'SELECT level, level_poll
        FROM '. FORUM_TABLE .'
        WHERE id = '. nkDB_escape($_REQUEST['forum_id'])
    );

    if ($dbrForum['level'] > $visiteur) {
        printNotification(_ZONEADMIN, 'error');
        redirect('index.php?file=Forum&page=post&forum_id='. $_REQUEST['forum_id'], 2);
        closetable();
        return;
    }

    if ($user['name'] != '') {
        $autor = $user['name'];
        $auteur_id = $user['id'];
    }
    else {
        $_REQUEST['auteur'] = nkHtmlEntities($_REQUEST['auteur'], ENT_QUOTES);
        $_REQUEST['auteur'] = verif_pseudo($_REQUEST['auteur']);

        if ($_REQUEST['auteur'] == 'error1') {
            printNotification(_PSEUDOFAILDED, 'error');
            redirect('index.php?file=Forum&page=post&forum_id='. $_REQUEST['forum_id'], 2);
            closetable();
            return;
        }
        else if ($_REQUEST['auteur'] == 'error2') {
            printNotification(_RESERVNICK, 'error');
            redirect('index.php?file=Forum&page=post&forum_id='. $_REQUEST['forum_id'], 2);
            closetable();
            return;
        }
        else if ($_REQUEST['auteur'] == 'error3') {
            printNotification(_BANNEDNICK, 'error');
            redirect('index.php?file=Forum&page=post&forum_id='. $_REQUEST['forum_id'], 2);
            closetable();
            return;
        }
        else {
            $autor = $_REQUEST['auteur'];
        }
    }

    $dbrForumMessage = nkDB_selectOne(
        'SELECT date
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE auteur = '. nkDB_escape($autor) .' OR auteur_ip = '. nkDB_escape($user_ip),
        array('date'), 'DESC', 1
    );

    $anti_flood = $dbrForumMessage['date'] + $nuked['post_flood'];

    $date = time();

    if ($date < $anti_flood && $user[1] < admin_mod('Forum')) {
        printNotification(_NOFLOOD, 'error');
        redirect('index.php?file=Forum&page=viewforum&forum_id='. $_REQUEST['forum_id'], 2);
        closetable();
        return;
    }

    $_REQUEST['titre'] = mysql_real_escape_string(stripslashes($_REQUEST['titre']));

    $_REQUEST['texte'] = secu_html(nkHtmlEntityDecode($_REQUEST['texte']));
    $_REQUEST['texte'] = icon($_REQUEST['texte']);
    $_REQUEST['texte'] = mysql_real_escape_string(stripslashes($_REQUEST['texte']));
    $_REQUEST['texte'] = str_replace('<blockquote>', '<blockquote class="nkForumBlockQuote">', $_REQUEST['texte']);

    $autor = mysql_real_escape_string(stripslashes($autor));

    if (!is_numeric($_REQUEST['usersig'])) $_REQUEST['usersig'] = 0;
    if (!is_numeric($_REQUEST['emailnotify'])) $_REQUEST['emailnotify'] = 0;
    if (($visiteur < admin_mod('Forum') && $administrator == 0) || !is_numeric($_REQUEST['annonce'])) $_REQUEST['annonce'] = 0;

    if ($_REQUEST['survey'] == 1 && $_REQUEST['survey_field'] > 0 && $visiteur >= $dbrForum['level_poll'])
        $sondage = 1;
    else
        $sondage = 0;

    nkDB_insert(FORUM_THREADS_TABLE, array(
        'titre'     => $_REQUEST['titre'],
        'date'      => $date,
        'closed'    => '',
        'auteur'    => $autor,
        'auteur_id' => $auteur_id,
        'forum_id'  => $_REQUEST['forum_id'],
        'last_post' => $date,
        'view'      => '',
        'annonce'   => $_REQUEST['annonce'],
        'sondage'   => $sondage
    ));

    $req4 = mysql_query("SELECT MAX(id) FROM " . FORUM_THREADS_TABLE . " WHERE forum_id = '" . $_REQUEST['forum_id'] . "' AND titre = '" . $_REQUEST['titre'] . "' AND date = '" . $date . "' AND auteur = '" . $_REQUEST['auteur'] . "'");
    $idmax = mysql_result($req4, 0, "MAX(id)");
    $_REQUEST['thread_id'] = $idmax;

    if ($visiteur >= $nuked['forum_file_level']
        && $nuked['forum_file'] == 'on'
        && $_FILES['fichiernom']['name'] != ''
    ) {
        list($url_file, $uploadError) = nkUpload_check(
            'fichiernom', 'no-html-php', 'upload/Forum', $nuked['forum_file_maxsize'], true
        );

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            //redirect('index.php?file=Forum&page=admin&op=editForum'. (($id > 0) ? '&id='. $id : ''), 2);
            return;
        }
    }
    else {
        $url_file = '';
    }

    nkDB_insert(FORUM_MESSAGES_TABLE, array(
        'titre'         => $_REQUEST['titre'],
        'txt'           => $_REQUEST['texte'],
        'date'          => $date,
        'edition'       => '',
        'auteur'        => $autor,
        'auteur_id'     => $auteur_id,
        'auteur_ip'     => $user_ip,
        'usersig'       => $_REQUEST['usersig'],
        'emailnotify'   => $_REQUEST['emailnotify'],
        'thread_id'     => $_REQUEST['thread_id'],
        'forum_id'      => $_REQUEST['forum_id'],
        'file'          => $filename
    ));

    $dbrForumRead = nkDB_selectOne(
        'SELECT thread_id, forum_id, user_id
        FROM '. FORUM_READ_TABLE .'
        WHERE thread_id LIKE \'%,'. nkDB_escape((int) $_REQUEST['thread_id'], 'no-quote') .',%\'
        OR forum_id LIKE \'%,'. nkDB_escape((int) $_REQUEST['forum_id'], 'no-quote') .',%\''
    );

    $update = '';

    foreach ($dbrForumRead as $forumRead) {
        $tid = $forumRead['thread_id'];
        $fid = $forumRead['forum_id'];

        if (strrpos($fid, ','. $_REQUEST['forum_id'] .',') !== false)
            $fid = preg_replace('#,'. $_REQUEST['forum_id'] .',#is', ',', $fid);

        if (strrpos($tid, ','. $_REQUEST['thread_id'] .',') !== false)
            $tid = preg_replace('#,'. $_REQUEST['thread_id'] .',#is', ',', $tid);

        $update .= (!empty($update) ? ', ' : '');
        $update .= "('" . $fid . "', '" . $tid . "', '" . $forumRead['user_id'] . "')";
    }

    if (!empty($update)) {
        $update = "INSERT INTO `" . FORUM_READ_TABLE . "`
            (forum_id, thread_id, user_id) VALUES $update
            ON DUPLICATE KEY UPDATE forum_id=VALUES(forum_id), thread_id=VALUES(thread_id);";
        nkDB_execute($update);
    }

    if ($user)
        nkDB_update(USER_TABLE, array('count' => array('count + 1', 'no-escape')), 'id = '. nkDB_escape($user['id']));

    if ($_REQUEST['survey'] == 1 && $_REQUEST['survey_field'] > 0 && $visiteur >= $dbrForum['level_poll'])
        $url = 'index.php?file=Forum&op=add_poll&survey_field='. $_REQUEST['survey_field'] .'&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'];
    else
        $url = 'index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'];

    printNotification(_MESSAGESEND, 'success');
    redirect($url, 2);
    closetable();
}

function mark() {
    global $user, $cookie_forum;

    if ($user) {
        if ($_REQUEST['forum_id'] > 0) {
            $new_id = '';
            $table_read_forum = array();
            $id_read_forum = '';

            if (isset($_COOKIE[$cookie_forum]) && $_COOKIE[$cookie_forum] != '') {
                $id_read_forum = $_COOKIE[$cookie_forum];
                if (preg_match('`[^0-9,]`i', $id_read_forum)) $id_read_forum = '';
                $table_read_forum = explode(',',$id_read_forum);
            }

            $dbrForumMessage = nkDB_selectMany(
                'SELECT MAX(id)
                FROM '. FORUM_MESSAGES_TABLE .'
                WHERE forum_id = '. nkDB_escape($_REQUEST['forum_id']) .' AND date > '. nkDB_escape($user[4]) .'
                GROUP BY thread_id'
            );

            foreach ($dbrForumMessage as $forumMessage) {
                if (! in_array($forumMessage['id'],$table_read_forum)) {
                    if ($new_id != '')  $new_id .= ',';
                    $new_id .= $forumMessage['id'];
                }
            }

            if ($id_read_forum != '' && $new_id != '') $id_read_forum .= ',';
            $_COOKIE['cookie_forum'] = $id_read_forum . $new_id;
        }
        else {
            $_COOKIE['cookie_forum'] = '';

            nkDB_update(SESSIONS_TABLE, array('last_used' => array('date', 'no-escape')), 'user_id = '. nkDB_escape($user['id']));
        }

        if ($user) {
            if ((int) $_REQUEST['forum_id'] != '')
                $where = 'WHERE forum_id = '. nkDB_escape((int) $_REQUEST['forum_id']);
            else
                $where = '';

            // On veut modifier la chaine thread_id et forum_id
            $dbrForumRead = nkDB_selectOne(
                'SELECT thread_id, forum_id
                FROM '. FORUM_READ_TABLE .'
                WHERE user_id = '. nkDB_escape($user['id'])
            );

            $dbrForumThread = nkDB_selectMany(
                'SELECT id, forum_id
                FROM '. FORUM_THREADS_TABLE .'
                '. $where
            );

            if (nkDB_numRows() > 0) {
                $tid = ','. substr($dbrForumRead['thread_id'], 1);
                $fid = ','. substr($dbrForumRead['forum_id'], 1);

                foreach ($dbrForumThread as $forumThread) {
                    if (strrpos($tid, ','. $forumThread['id'] .',') === false)
                        $tid .= $forumThread['id'] .',';

                    if (strrpos($fid, ','. $forumThread['forum_id'] .',') === false)
                        $fid .= $forumThread['forum_id'] .',';
                }

                nkDB_replace(FORUM_READ_TABLE, array(
                    'user_id'   => $user['id'],
                    'thread_id' => $tid,
                    'forum_id'  => $fid
                ));
            }
        }
    }

    opentable();
    printNotification(_MESSAGESMARK, 'success');
    closetable();
    redirect('index.php?file=Forum', 2);
}

function del_file() {
    global $user;

    opentable();

    $dbrForumMessage = nkDB_selectOne(
        'SELECT file, auteur_id
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE id = '. nkDB_escape($_REQUEST['mess_id'])
    );

    $filename = $dbrForumMessage['file'];
    $auteur_id = $dbrForumMessage['auteur_id'];

    if ($user && $auteur_id == $user['id'] || isForumAdministrator($_REQUEST['forum_id'])) {
        $path = 'upload/Forum/'. $filename;

        if (is_file($path)) {
            @chmod($path, 0775);
            @unlink($path);

            nkDB_update(FORUM_MESSAGES_TABLE, array('file' => ''), 'id = '. nkDB_escape($_REQUEST['mess_id']));
            printNotification(_FILEDELETED, 'success');
        }
        // TODO If no file to delete, none notification
        //else
        //    printNotification(_NOFILETODELETE, 'info');
    }
    else
        printNotification(_ZONEADMIN, 'error');

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);

    closetable();
}

function add_poll() {
    global $visiteur, $user, $nuked;

    opentable();

    $dbrForumThread = nkDB_selectOne(
        'SELECT auteur_id, sondage
        FROM '. FORUM_THREADS_TABLE .'
        WHERE id = '. nkDB_escape($_REQUEST['thread_id'])
    );

    $dbrForum = nkDB_selectOne(
        'SELECT level_poll
        FROM '. FORUM_TABLE .'
        WHERE id = '. nkDB_escape($_REQUEST['forum_id'])
    );

    if ($user && $user['id'] == $dbrForumThread['auteur_id'] && $dbrForumThread['sondage'] == 1 && $visiteur >= $dbrForum['level_poll']) {
        if ($_REQUEST['survey_field'] > $nuked['forum_field_max'])
            $max = $nuked['forum_field_max'];
        else
            $max = $_REQUEST['survey_field'];

        $pollOptions = array();

        $r = 0;
        while ($r < $max) {
            $r++;
            $pollOptions[] = array('option_text' => '');
        }

        echo applyTemplate('editPoll', array(
            'title'         => '',
            'action'        => 'index.php?file=Forum&amp;op=send_poll',
            'pollOptions'   => $pollOptions,
            'max_option'    => $max,
            'thread_id'     => $_REQUEST['thread_id'],
            'forum_id'      => $_REQUEST['forum_id']
        ));
    }
    else {
        printNotification(_ZONEADMIN, 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);
    }

    closetable();
}

function send_poll($titre, $option, $thread_id, $forum_id, $max_option) {
    global $visiteur, $user, $nuked;

    opentable();

    $dbrForumThread = nkDB_selectOne(
        'SELECT auteur_id, sondage
        FROM '. FORUM_THREADS_TABLE .'
        WHERE id = '. nkDB_escape($_REQUEST['thread_id'])
    );

    $dbrForum = nkDB_selectOne(
        'SELECT level_poll
        FROM '. FORUM_TABLE .'
        WHERE id = '. nkDB_escape($forum_id)
    );

    if ($user && $user['id'] == $dbrForumThread['auteur_id'] && $dbrForumThread['sondage'] == 1 && $visiteur >= $dbrForum['level_poll']) {
        if ($option[1] != '') {
            nkDB_insert(FORUM_POLL_TABLE, array(
                'thread_id' => $thread_id,
                'titre'     => stripslashes($titre)
            ));

            $dbrForumPoll = nkDB_selectOne(
                'SELECT id
                FROM '. FORUM_POLL_TABLE .'
                WHERE thread_id = '. nkDB_escape($thread_id)
            );

            if ($max_option > $nuked['forum_field_max'])
                $max = $nuked['forum_field_max'];
            else
                $max = $max_option;

            $r = 0;

            while ($r < $max) {
                if ($option[$r] != '') {
                    nkDB_insert(FORUM_OPTIONS_TABLE, array(
                        'id'            => ($r + 1),
                        'poll_id'       => $dbrForumPoll['id'],
                        'option_text'   => stripslashes($option[$r]),
                        'option_vote'   => ''
                    ));
                }

                $r++;
            }

            printNotification(_POLLADD, 'success');
            redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forum_id .'&thread_id='. $thread_id, 2);
        }
        else {
            printNotification(_2OPTIONMIN, 'warning');
            redirect('index.php?file=Forum&op=add_poll&survey_field='. $max_option .'&forum_id='. $forum_id .'&thread_id='. $thread_id, 2);
        }
    }
    else {
        printNotification(_ZONEADMIN, 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forum_id .'&thread_id='. $thread_id, 2);
    }

    closetable();
}

function vote($poll_id) {
    global $visiteur, $user, $user_ip;

    opentable();

    if ($_REQUEST['voteid'] != '') {
        if ($visiteur > 0) {
            $dbrForum = nkDB_selectOne(
                'SELECT level_vote
                FROM '. FORUM_TABLE .'
                WHERE id = '. nkDB_escape($_REQUEST['forum_id'])
            );

            if ($visiteur >= $dbrForum['level_vote']) {
                $check = nkDB_totalNumRows(
                    'FROM '. FORUM_VOTE_TABLE .'
                    WHERE auteur_id = '. nkDB_escape($user['id']) .' AND poll_id = '. nkDB_escape($poll_id)
                );

                if ($check == 0) {
                    nkDB_update(FORUM_OPTIONS_TABLE,
                        array('option_vote' => array('option_vote + 1', 'no-escape')),
                        'id = '. nkDB_escape($_REQUEST['voteid']) .' AND poll_id = '. nkDB_escape($poll_id)
                    );

                    nkDB_insert(FORUM_VOTE_TABLE, array(
                        'poll_id'   => $poll_id,
                        'auteur_id' => $user['id'],
                        'auteur_ip' => $user_ip
                    ));

                    printNotification(_VOTESUCCES, 'success');
                }
                else {
                    printNotification(_ALREADYVOTE, 'warning');
                }
            }
            else {
                printNotification(_BADLEVEL, 'error');
            }
        }
        else {
            printNotification(_ONLYMEMBERSVOTE, 'error');
        }
    }
    else {
        printNotification(_NOOPTION, 'warning');
    }

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);

    closetable();
}

function del_poll($poll_id, $thread_id, $forum_id) {
    global $user;

    opentable();

    $dbrForumThread = nkDB_selectOne(
        'SELECT auteur_id
        FROM '. FORUM_THREADS_TABLE .'
        WHERE id = '. nkDB_escape($thread_id)
    );

    if ($user && $user['id'] == $dbrForumThread['auteur_id'] || isForumAdministrator($forum_id)) {
        if ($_REQUEST['confirm'] == _YES) {
            nkDB_delete(FORUM_POLL_TABLE, 'id = '. nkDB_escape($poll_id));
            nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. nkDB_escape($poll_id));
            nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. nkDB_escape($poll_id));
            nkDB_update(FORUM_THREADS_TABLE, array('sondage' => 0), 'id = '. nkDB_escape($thread_id));

            printNotification(_POLLDELETE, 'success');
            redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forum_id .'&thread_id='. $thread_id, 2);
        }
        else if ($_REQUEST['confirm'] == _NO) {
            printNotification(_DELCANCEL, 'warning');
            redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forum_id .'&thread_id='. $thread_id, 2);
        }
        else {
            echo applyTemplate('confirm', array(
                'url'       => 'index.php?file=Forum&amp;op=del_poll',
                'message'   => _CONFIRMDELPOLL,
                'fields'    => array(
                    'poll_id'   => $poll_id,
                    'thread_id' => $thread_id,
                    'forum_id'  => $forum_id
                ),
            ));
        }
    }
    else {
        printNotification(_ZONEADMIN, 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forum_id .'&thread_id='. $thread_id, 2);
    }

    closetable();
}

function edit_poll($poll_id) {
    global $user;

    opentable();

    $dbrForumThread = nkDB_selectOne(
        'SELECT auteur_id
        FROM '. FORUM_THREADS_TABLE .'
        WHERE id = '. nkDB_escape($_REQUEST['thread_id'])
    );

    if ($user && $user['id'] == $dbrForumThread['auteur_id'] || isForumAdministrator($_REQUEST['forum_id'])) {
        $dbrForumPoll = nkDB_selectOne(
            'SELECT titre
            FROM '. FORUM_POLL_TABLE .'
            WHERE id = '. nkDB_escape($poll_id)
        );

        $dbrForumPollOptions = nkDB_selectMany(
            'SELECT id, option_text
            FROM '. FORUM_OPTIONS_TABLE .'
            WHERE poll_id = '. nkDB_escape($poll_id),
            array('id')
        );

        echo applyTemplate('editPoll', array(
            'title'         => $dbrForumPoll['titre'],
            'action'        => 'index.php?file=Forum&amp;op=modif_poll',
            'pollOptions'   => $dbrForumPollOptions,
            'poll_id'       => $poll_id,
            'thread_id'     => $_REQUEST['thread_id'],
            'forum_id'      => $_REQUEST['forum_id']
        ));
    }
    else {
        printNotification(_ZONEADMIN, 'error');
        redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);
    }

    closetable();
}

function modif_poll($poll_id, $titre, $option, $newoption, $thread_id, $forum_id) {
    global $user, $nuked;

    opentable();

    $dbrForumThread = nkDB_selectOne(
        'SELECT auteur_id
        FROM '. FORUM_THREADS_TABLE .'
        WHERE id = '. nkDB_escape($thread_id)
    );

    if ($user && $user['id'] == $dbrForumThread['auteur_id'] || isForumAdministrator($forum_id)) {
        nkDB_update(FORUM_POLL_TABLE, array('titre' => stripslashes($titre)), 'id = '. nkDB_escape($poll_id));

        $r = 0;

        while ($r < $nuked['forum_field_max']) {
            $r++;

            if ($option[$r] != '') {
                nkDB_update(FORUM_OPTIONS_TABLE,
                    array('option_text' => stripslashes($option[$r])),
                    'poll_id = '. nkDB_escape($poll_id) .' AND id = '. $r
                );
            }
            else {
                nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. nkDB_escape($poll_id) .' AND id = '. $r);
            }
        }

        if ($newoption != '') {
            $dbrForumPollOptions = nkDB_selectOne(
                'SELECT id
                FROM '. FORUM_OPTIONS_TABLE .'
                poll_id = '. nkDB_escape($poll_id),
                array('id'), 'DESC', 1
            );

            nkDB_insert(FORUM_OPTIONS_TABLE, array(
                'id'            => ($dbrForumPollOptions['id'] + 1),
                'poll_id'       => $poll_id,
                'option_text'   => stripslashes($newoption),
                'option_vote'   => 0
            ));
        }

        printNotification(_POLLMODIF, 'success');
    }
    else {
        printNotification(_ZONEADMIN, 'error');
    }

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $forum_id .'&thread_id='. $thread_id, 2);
    closetable();
}

function notify() {
    global $user;

    opentable();

    if ($user['id'] != '') {
        if ($_REQUEST['do'] == 'on') {
            $notify = 1;
            printNotification(_NOTIFYISON, 'info');
        }
        else {
            $notify = 0;
            printNotification(_NOTIFYISOFF, 'info');
        }

        nkDB_update(FORUM_MESSAGES_TABLE,
            array('emailnotify' => $notify),
            'thread_id = '. nkDB_escape($_REQUEST['thread_id']) .' AND auteur_id = '. nkDB_escape($user['id'])
        );
    }
    else
        printNotification(_ZONEADMIN, 'error');

    redirect('index.php?file=Forum&page=viewtopic&forum_id='. $_REQUEST['forum_id'] .'&thread_id='. $_REQUEST['thread_id'], 2);
    closetable();
}


switch ($_REQUEST['op']) {
    case 'index' :
        index();
        break;

    case 'post' :
        post();
        break;

    case 'reply' :
        reply();
        break;

    case 'edit' :
        edit($_REQUEST['mess_id']);
        break;

    case 'del' :
        del($_REQUEST['mess_id']);
        break;

    case 'del_topic' :
        del_topic($_REQUEST['thread_id']);
        break;

    case 'move' :
        move();
        break;

    case 'lock' :
        lock();
        break;

    case 'announce' :
        announce();
        break;

    case 'mark' :
        mark();
        break;

    case 'del_file' :
        del_file();
        break;

    case 'add_poll' :
        add_poll();
        break;

    case 'send_poll' :
        send_poll($_REQUEST['titre'], $_REQUEST['option'], $_REQUEST['thread_id'], $_REQUEST['forum_id'], $_REQUEST['max_option']);
        break;

    case 'vote' :
        vote($_REQUEST['poll_id']);
        break;

    case 'del_poll' :
        del_poll($_REQUEST['poll_id'], $_REQUEST['thread_id'], $_REQUEST['forum_id']);
        break;

    case 'edit_poll' :
        edit_poll($_REQUEST['poll_id']);
        break;

    case 'modif_poll' :
        modif_poll($_REQUEST['poll_id'], $_REQUEST['titre'], $_REQUEST['option'], $_REQUEST['newoption'], $_REQUEST['thread_id'], $_REQUEST['forum_id']);
        break;

    case 'notify' :
        notify();
        break;

    default :
        index();
        break;
}

?>