<?php
/**
 * admin.php
 *
 * Backend of Forum module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Forum'))
    return;


/* Forum category management */

function main_cat(){
    global $adminMenu, $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
    ."<!--\n"
    ."\n"
    . "function delcat(titre, id)\n"
    . "{\n"
    . "if (confirm('" . _DELETEFORUM . " '+titre+' ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Forum&page=admin&op=deleteCat&id='+id;}\n"
    . "}\n"
    . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . " - " . _CATMANAGEMENT . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    echo applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 50%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _ORDER . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = mysql_query("SELECT id, nom, ordre FROM " . FORUM_CAT_TABLE . " ORDER BY ordre, nom");
    while (list($cid, $nom, $ordre) = mysql_fetch_row($sql)){
        $nom = printSecuTags($nom);

        echo "<tr>\n"
        . "<td align=\"center\">" . $nom . "</td>\n"
        . "<td align=\"center\">" . $ordre . "</td>\n"
        . "<td align=\"center\"><a href=\"index.php?file=Forum&amp;page=admin&amp;op=editCat&amp;id=" . $cid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
        . "<td align=\"center\"><a href=\"javascript:delcat('" . mysql_real_escape_string(stripslashes($nom)) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
    }

    echo "</table><br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Forum&amp;page=admin&amp;op=editCat\">" . _ADDCAT . "</a><a class=\"buttonLink\" href=\"index.php?file=Forum&amp;page=admin\">" . _BACK . "</a></div>\n"
    . "<br /></div></div>\n";
}

function editForumCat() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/config/category.php';

    $id         = (isset($_GET['id'])) ? $_GET['id'] : 0;
    $content    = '';

    if ($id == 0) {
        unset($forumCatForm['items']['htmlCategoryImage']);
    }
    else {
        $dbrForumCat = nkDB_selectOne(
            'SELECT *
            FROM '. FORUM_CAT_TABLE .'
            WHERE id = '. nkDB_escape($id)
        );

        foreach ($forumCatField as $field)
            $forumCatForm['items'][$field]['value'] = $dbrForumCat[$field];

        if ($dbrForumCat['image'] !='') {
            $content .= printNotification('information', _NOTIFIMAGESIZE, $backLink = false, $return = true);

            $forumCatForm['items']['htmlCategoryImage'] = '<img src="'. $dbrForumCat['image'] .'" style="max-width:100%;height:auto;"/>';
        }

        $forumCatForm['itemsFooter']['submit']['value'] = _MODIFTHISCAT;
    }

    echo applyTemplate('contentBox', array(
        'title'     => ($id == 0) ? _CATMANAGEMENT .' - '. _ADDCAT : _CATMANAGEMENT .' - '. _EDITTHISCAT,
        'helpFile'  => 'Forum',
        'content'   => $content . nkForm_generate($forumCatForm)
    ));
}

function saveForumCat() {
    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    $data = array(
        'nom'       => $_POST['nom'],
        'niveau'    => $_POST['niveau'],
        'ordre'     => $_POST['ordre']
    );

    // Upload du fichier
    $filename = $_FILES['upImageCat']['name'];

    if ($filename != '') {
        $imgInfo = getimagesize($filename);

        if ($imgInfo !== false && in_array($imgInfo[2], array(IMG_JPEG, IMG_GIF, IMG_PNG))) {
            $data['image'] = 'upload/Forum/cat/'. $filename;

            if (! move_uploaded_file($_FILES['upImageCat']['tmp_name'], $data['image'])) {
                printNotification('error', _UPLOADFILEFAILED);
                redirect('index.php?file=Forum&page=admin&op=editCat'. ($id > 0) ? '&cid='. $id : '', 2);
                return;
            }

            @chmod($data['image'], 0644);
        }
        else {
            printNotification('error', _NOIMAGEFILE);
            redirect('index.php?file=Forum&page=admin&op=editCat'. ($id > 0) ? '&cid='. $id : '', 2);
            return;
        }
    }
    else {
        $data['image'] = $_POST['urlImageCat'];
    }

    if ($id == 0) {
        nkDB_insert(FORUM_CAT_TABLE, array_keys($data), array_values($data));
        saveUserAction(_ACTIONADDCATFO .': '. $data['nom']);

        printNotification('success', _CATADD);
    }
    else {
        nkDB_update(FORUM_CAT_TABLE, array_keys($data), array_values($data), 'id = '. nkDB_escape($id));
        nkDB_update(FORUM_TABLE, array('niveau'), array($data['niveau']), 'cat = '. nkDB_escape($id));
        saveUserAction(_ACTIONMODIFCATFO .': '. $data['nom']);

        printNotification('success', _CATMODIF);
    }

    setPreview('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main_cat');
}

function deleteForumCat() {
    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    $dbrForumCat = nkDB_selectOne(
        'SELECT nom
        FROM '. FORUM_CAT_TABLE .'
        WHERE id = '. nkDB_escape($id)
    );

    nkDB_delete(FORUM_CAT_TABLE, 'id = '. nkDB_escape($id));
    saveUserAction(_ACTIONDELCATFO .': '. $dbrForumCat['nom']);

    printNotification('success', _CATDEL);
    setPreview('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main_cat');
}

/* Forum management */

function getModeratorOptions($moderatorList) {
    $options = array('' => _NONE);

    $dbrUser = nkDB_selectMany(
        'SELECT id, pseudo
        FROM '. USER_TABLE .'
        WHERE niveau > 0',
        array('niveau', 'pseudo'), array('DESC', 'ASC')
    );

    foreach ($dbrUser as $_user) {
        if (! in_array($_user['id'], $moderatorList))
            $options[$_user['id']] = $_user['pseudo'];
    }

    return $options;
}

function editForum() {
    global $adminMenu;

    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/config/forum.php';

    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    $dbrForumCat = nkDB_selectMany(
        'SELECT id, nom
        FROM '. FORUM_CAT_TABLE,
        array('ordre', 'nom')
    );

    foreach ($dbrForumCat as $forumCat)
        $forumForm['items']['cat']['options'][$forumCat['id']] = printSecuTags($forumCat['nom']);

    $moderatorList = array();

    if ($id == 0) {
        unset($forumForm['items']['moderatorList']);
    }
    else {
        $dbrForum = nkDB_selectOne(
            'SELECT *
            FROM '. FORUM_TABLE .'
            WHERE id = '. nkDB_escape($id)
        );

        foreach ($forumField as $field)
            $forumForm['items'][$field]['value'] = $dbrForum[$field];

        if ($dbrForum['moderateurs'] != '') {
            $moderateurs = explode('|', $dbrForum['moderateurs']);
            $nbModerator = count($moderateurs);

            for ($i = 0; $i < $nbModerator; $i++) {
                $sep = ($i == 0) ? '' : ', ';

                $dbrUser = nkDB_selectOne(
                    'SELECT id, pseudo
                    FROM '. USER_TABLE .'
                    WHERE id = '. nkDB_escape($moderateurs[$i])
                );

                $forumForm['items']['moderatorList']['html'] .= $sep . $dbrUser['pseudo'] .'&nbsp;(<a href="index.php?file=Forum&amp;page=admin&amp;op=deleteModerator&amp;user_id='. $dbrUser['id'] .'&amp;forum_id='. $id .'"><img style="border: 0;vertical-align:bottom;" src="modules/Admin/images/icons/cross.png" alt="" title="'. _DELTHISMODO .'" /></a>)';
                $moderatorList[] = $dbrUser['id'];
            }
        }
        else{
            $forumForm['items']['moderatorList']['html'] = _NONE;
        }

        if ($dbrForum['image'] !='')
            $forumForm['items']['image']['html'] = '<img src="'. $dbrForum['image'] .'" title="'. $dbrForum['nom'] .'" style="margin-left:20px; width:50px; height:50px; vertical-align:middle;" />';

        $forumForm['itemsFooter']['submit']['value'] = _MODIFTHISCAT;
    }

    $forumForm['items']['modo']['options'] = getModeratorOptions($moderatorList);

    $adminMenu = applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    echo applyTemplate('contentBox', array(
        'title'     => ($id == 0) ? _ADMINFORUM .' - '. _ADDFORUM : _ADMINFORUM .' - '. _EDITTHISFORUM,
        'helpFile'  => 'Forum',
        'content'   => $adminMenu . nkForm_generate($forumForm)
    ));
}

function saveForum() {
    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    $data = array(
        'nom'           => stripslashes($_POST['titre']),
        'comment'       => stripslashes(secu_html(nkHtmlEntityDecode($_POST['description']))),
        'cat'           => $_POST['cat'],
        'moderateurs'   => $_POST['modo'],
        'niveau'        => $_POST['niveau'],
        'level'         => $_POST['level'],
        'ordre'         => $_POST['ordre'],
        'level_poll'    => $_POST['level_poll'],
        'level_vote'    => $_POST['level_vote']
    );

    // Upload du fichier
    $filename = $_FILES['upImageForum']['name'];

    if ($filename != '') {
        $imgInfo = getimagesize($filename);

        if ($imgInfo !== false && in_array($imgInfo[2], array(IMG_JPEG, IMG_GIF, IMG_PNG))) {
            $data['image'] = 'upload/Forum/cat/'. $filename;

            if (! move_uploaded_file($_FILES['upImageForum']['tmp_name'], $data['image'])) {
                printNotification('error', _UPLOADFILEFAILED);
                redirect('index.php?file=Forum&page=admin&op=editForum'. ($id > 0) ? '&id='. $id : '', 2);
                return;
            }

            @chmod($data['image'], 0644);
        }
        else {
            printNotification('error', _NOIMAGEFILE);
            redirect('index.php?file=Forum&page=admin&op=editForum'. ($id > 0) ? '&id='. $id : '', 2);
            return;
        }
    }
    else {
        $data['image'] = $_POST['urlImageForum'];
    }

    if ($id == 0) {
        nkDB_insert(FORUM_TABLE, array_keys($data), array_values($data));
        saveUserAction(_ACTIONADDFO .': '. $data['nom']);

        printNotification('success', _FORUMADD);
    }
    else {
        if ($data['moderateurs'] != '') {
            $dbrForum = nkDB_selectOne(
                'SELECT moderateurs
                FROM '. FORUM_TABLE .'
                WHERE id = '. nkDB_escape($id)
            );

            if ($dbrForum['moderateurs'] != '')
                $modos = $dbrForum['moderateurs'] .'|'. $data['moderateurs'];
            else
                $modos = $data['moderateurs'];

            nkDB_update(FORUM_TABLE, array_keys('moderateurs'), array_values($modos), 'id = '. nkDB_escape($id));
        }

        nkDB_update(FORUM_TABLE, array_keys($data), array_values($data), 'id = '. nkDB_escape($id));
        saveUserAction(_ACTIONMODIFFO .': '. $data['nom']);

        printNotification('success', _FORUMMODIF);
    }

    setPreview('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main');
}

function deleteForum() {
    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    $dbrForum = nkDB_selectOne(
        'SELECT nom
        FROM '. FORUM_TABLE .'
        WHERE id = '. nkDB_escape($id)
    );

    $dbrForumThreads = nkDB_selectMany(
        'SELECT id, sondage
        FROM '. FORUM_THREADS_TABLE .'
        WHERE forum_id = '. nkDB_escape($id)
    );

    foreach ($dbrForumThreads as $forumThreads) {
        if ($forumThreads['sondage'] == 1) {
            $dbrForumPoll = nkDB_selectOne(
                'SELECT id
                FROM '. FORUM_POLL_TABLE .'
                WHERE thread_id = '. nkDB_escape($forumThreads['id'])
            );

            nkDB_delete(FORUM_POLL_TABLE, 'id = '. nkDB_escape($dbrForumPoll['id']));
            nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. nkDB_escape($dbrForumPoll['id']));
            nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. nkDB_escape($dbrForumPoll['id']));
        }
    }

    nkDB_delete(FORUM_TABLE, 'id = '. nkDB_escape($id));
    nkDB_delete(FORUM_THREADS_TABLE, 'forum_id = '. nkDB_escape($id));
    nkDB_delete(FORUM_MESSAGES_TABLE, 'forum_id = '. nkDB_escape($id));

    saveUserAction(_ACTIONDELFO .': '. $dbrForum['nom']);

    printNotification('success', _FORUMDEL);
    setPreview('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main');
}

function deleteModerator() {
    $dbrForum = nkDB_selectOne(
        'SELECT moderateurs
        FROM '. FORUM_TABLE .'
        WHERE id = '. nkDB_escape($_GET['forum_id'])
    );

    $list   = explode('|', $dbrForum['moderateurs']);
    $end    = count($list) - 1;

    for ($i = 0; $i <= $end; $i++) {
        if ($i == 0 || ($i == 1 && $list[0] == $_GET['user_id']))
            $sep = '';
        else
            $sep = '|';

        if ($list[$i] != $_GET['user_id'])
            $modos .= $sep . $list[$i];
    }

    nkDB_update(FORUM_TABLE, array_keys('moderateurs'), array_values($modos), 'id = '. nkDB_escape($_GET['forum_id']));

    $dbrUser = nkDB_selectOne(
        'SELECT pseudo
        FROM '. USER_TABLE .'
        WHERE id = '. nkDB_escape($_GET['user_id'])
    );

    saveUserAction(_ACTIONDELMODOFO .': '. $dbrUser['pseudo']);

    printNotification('success', _MODODEL);
    redirect('index.php?file=Forum&page=admin&op=editForum&id='. $_GET['forum_id'], 2);
}

function main() {
    global $adminMenu, $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
    ."<!--\n"
    ."\n"
    . "function delforum(nom, id)\n"
    . "{\n"
    . "if (confirm('" . _DELETEFORUM . " '+nom+' ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Forum&page=admin&op=deleteForum&id='+id;}\n"
    . "}\n"
        . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    echo applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _LEVELACCES . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _LEVELPOST . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = mysql_query("SELECT A.id, A.nom, A.niveau, A.level, A.cat, B.nom FROM " . FORUM_TABLE . " AS A LEFT JOIN " . FORUM_CAT_TABLE . " AS B ON B.id = A.cat ORDER BY B.ordre, B.nom, A.ordre, A.nom");
    while (list($id, $titre, $niveau, $level, $cat, $cat_name) = mysql_fetch_row($sql)){

        $titre = printSecuTags($titre);
        $cat_name = printSecuTags($cat_name);

        echo "<tr>\n"
        . "<td style=\"width: 20%;\">" . $titre . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $cat_name . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $niveau . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $level . "</td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Forum&amp;page=admin&amp;op=editForum&amp;id=" . $id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISFORUM . "\" /></a></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><a href=\"javascript:delforum('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISFORUM . "\" /></a></td></tr>\n";
    }
    echo "</table><div style=\"text-align: center;\"><br \><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . _BACK . "</a></div><br /></div></div>\n";
}

/* Forum rank management */

function main_rank(){
    global $adminMenu, $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
    ."<!--\n"
    ."\n"
    . "function delrank(titre, id)\n"
    . "{\n"
    . "if (confirm('" . _DELETEFORUM . " '+titre+' ! " . _CONFIRM . "'))\n"
    . "{document.location.href = 'index.php?file=Forum&page=admin&op=deleteRank&rid='+id;}\n"
    . "}\n"
        . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . " - " . _RANKMANAGEMENT . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    echo applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" cellpadding=\"2\" cellspacing=\"1\">\n"
    . "<tr>\n"
    . "<td style=\"width: 25%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
    . "<td style=\"width: 25%;\"align=\"center\"><b>" . _TYPE . "</b></td>\n"
    . "<td style=\"width: 20%;\" align=\"center\"><b>" . _MESSAGES . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
    . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = mysql_query("SELECT id, nom, type, post FROM " . FORUM_RANK_TABLE . " ORDER by type DESC, post");
    while (list($rid, $nom, $type, $nbpost) = mysql_fetch_row($sql)){
        $nom = printSecuTags($nom);

        if ($type == 1){
            $name = "<b>" . $nom . "</b>";
            $type_name = _MODERATEUR;
            $nb_post = "-";
            $del = "-";
        }
        else if ($type == 2){
            $name = "<b>" . $nom . "</b>";
            $type_name = _ADMINISTRATOR;
            $nb_post = "-";
            $del = "-";
        }
        else{
            $name = $nom;
            $type_name = _MEMBER;
            $nb_post = $nbpost;
            $del = "<a href=\"javascript:delrank('" . mysql_real_escape_string(stripslashes($nom)) . "', '" . $rid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISRANK . "\" /></a>";
        }

        echo "<tr>\n"
        . "<td style=\"width: 25%;\" align=\"center\">" . $name . "</td>\n"
        . "<td style=\"width: 25%;\" align=\"center\">" . $type_name . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\">" . $nb_post . "</td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Forum&amp;page=admin&amp;op=editRank&amp;id=" . $rid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISRANK . "\" /></a></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\">" . $del . "</td></tr>\n";
    }

    echo "</table><br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Forum&amp;page=admin&amp;op=editRank\">" . _ADDRANK . "</a><a class=\"buttonLink\" href=\"index.php?file=Forum&amp;page=admin\">" . _BACK . "</a></div>\n"
    . "<br /></div></div>\n";
}

function editRank() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/config/rank.php';

    $id = (isset($_GET['rid'])) ? $_GET['rid'] : 0;

    if ($id > 0) {
        $dbrForumRank = nkDB_selectOne(
            'SELECT *
            FROM '. FORUM_RANK_TABLE .'
            WHERE id = '. nkDB_escape($id)
        );

        foreach ($forumRankField as $field)
            $forumRankForm['items'][$field]['value'] = $dbrForumRank[$field];

        if ($dbrForumRank['type'] != 0){
            $forumRankForm['items']['type']['type'] = 'hidden';
        }

        $forumRankForm['itemsFooter']['submit']['value'] = _MODIFTHISRANK;
    }

    echo applyTemplate('contentBox', array(
        'title'     => ($id == 0) ? _ADMINFORUM .' - '. _ADDRANK : _ADMINFORUM .' - '. _EDITTHISRANK,
        'helpFile'  => 'Forum',
        'content'   => nkForm_generate($forumRankForm)
    ));
}

function saveRank() {
    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    $data = array(
        'nom'   => $_POST['nom'],
        'type'  => $_POST['type'],
        'post'  => $_POST['post']
    );

    // Upload du fichier
    $filename = $_FILES['upImageRank']['name'];

    if ($filename != '') {
        $imgInfo = getimagesize($filename);

        if ($imgInfo !== false && in_array($imgInfo[2], array(IMG_JPEG, IMG_GIF, IMG_PNG))) {
            $data['image'] = 'upload/Forum/rank/'. $filename;

            if (! move_uploaded_file($_FILES['upImageRank']['tmp_name'], $data['image'])) {
                printNotification('error', _UPLOADFILEFAILED);
                redirect('index.php?file=Forum&page=admin&op=editRank'. ($id > 0) ? '&cid='. $id : '', 2);
                return;
            }

            @chmod($data['image'], 0644);
        }
        else {
            printNotification('error', _NOIMAGEFILE);
            redirect('index.php?file=Forum&page=admin&op=editRank'. ($id > 0) ? '&cid='. $id : '', 2);
            return;
        }
    }
    else {
        $data['image'] = $_POST['image'];
    }

    if ($id == 0) {
        nkDB_insert(FORUM_RANK_TABLE, array_keys($data), array_values($data));
        saveUserAction(_ACTIONADDRANKFO .': '. $data['nom']);

        printNotification('success', _RANKADD);
    }
    else {
        nkDB_update(FORUM_RANK_TABLE, array_keys($data), array_values($data), 'id = '. nkDB_escape($id));
        saveUserAction(_ACTIONMODIFRANKFO .': '. $data['nom']);

        printNotification('success', _RANKMODIF);
    }

    redirect('index.php?file=Forum&page=admin&op=main_rank', 2);
}

function deleteRank() {
    $id = (isset($_GET['rid'])) ? $_GET['rid'] : 0;

    $dbrForumRank = nkDB_selectOne(
        'SELECT nom
        FROM '. FORUM_RANK_TABLE .'
        WHERE id = '. nkDB_escape($id)
    );

    nkDB_delete(FORUM_RANK_TABLE, 'id = '. nkDB_escape($id));
    saveUserAction(_ACTIONDELRANKFO .': '. $dbrForumRank['nom']);

    printNotification('success', _RANKDEL);

    redirect('index.php?file=Forum&page=admin&op=main_rank', 2);
}

/* Forum prune management */

function getPruneList() {
    $options = array('' => _ALL);

    $dbrForumCat = nkDB_selectMany(
        'SELECT id, nom
        FROM '. FORUM_CAT_TABLE,
        array('ordre', 'nom')
    );

    foreach ($dbrForumCat as $forumCat) {
        $options['cat_'. $forumCat['id']] = '* '. printSecuTags($forumCat['nom']);

        $dbrForum = nkDB_selectMany(
            'SELECT id, nom
            FROM '. FORUM_TABLE .'
            WHERE cat = '. nkDB_escape($forumCat['id']),
            array('ordre', 'nom')
        );

        foreach ($dbrForum as $forum)
            $options['cat_'. $forum['id']] = '&nbsp;&nbsp;&nbsp;'. printSecuTags($forum['nom']);
    }

    return $options;
}

function prune() {
    global $adminMenu;

    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/config/prune.php';

    nkTemplate_addJs(
'$("#pruneForumForm").submit(function(event) {
    if (document.getElementById("prune_day").value.length == 0) {
        alert("'. _NODAY .'");
        return false;
    }
    return true;
});', 'jqueryDomReady');

    $pruneForumForm['items']['prune_id']['options'] = getPruneList();

    $adminMenu = applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    echo applyTemplate('contentBox', array(
        'title'     => _ADMINFORUM .' - '. _PRUNE,
        'helpFile'  => 'Forum',
        'content'   => $adminMenu . nkForm_generate($pruneForumForm)
    ));
}

function doPrune() {
    $prunedate = time() - (86400 * $_POST['day']);

    if (strpos($_POST['prune_id'], 'cat_') === 0) {
        $cat = str_replace('cat_', '', $_POST['prune_id']);
        $and = 'AND cat = '. nkDB_escape($cat);

        $dbrForumCat = nkDB_selectMany(
            'SELECT nom
            FROM '. FORUM_CAT_TABLE .'
            WHERE id = '. nkDB_escape($cat)
        );

        $name = $dbrForumCat['nom'];
    }
    else if ($_POST['prune_id'] != '') {
        $and = 'AND forum_id = '. nkDB_escape($_POST['prune_id']);

        $dbrForum = nkDB_selectMany(
            'SELECT nom
            FROM '. FORUM_TABLE .'
            WHERE id = '. nkDB_escape($_POST['prune_id'])
        );

        $name = $dbrForum['nom'];
    }
    else {
        $and    = '';
        $name   = _ALL;
    }

    $dbrForumThreads = nkDB_selectMany(
        'SELECT id, sondage
        FROM '. FORUM_THREADS_TABLE .'
        WHERE '. $prunedate .' >= last_post AND annonce = 0 '. $and
    );

    foreach ($dbrForumThreads as $forumThreads) {
        if ($forumThreads['sondage'] == 1) {
            $dbrForumPoll = nkDB_selectMany(
                'SELECT id
                FROM '. FORUM_POLL_TABLE .'
                WHERE thread_id = '. nkDB_escape($forumThreads['id'])
            );

            nkDB_delete(FORUM_POLL_TABLE, 'id = '. nkDB_escape($dbrForumPoll['id']));
            nkDB_delete(FORUM_OPTIONS_TABLE, 'poll_id = '. nkDB_escape($dbrForumPoll['id']));
            nkDB_delete(FORUM_VOTE_TABLE, 'poll_id = '. nkDB_escape($dbrForumPoll['id']));
        }

        nkDB_delete(FORUM_MESSAGES_TABLE, 'thread_id = '. nkDB_escape($forumThreads['id']));
        nkDB_delete(FORUM_THREADS_TABLE, 'id = '. nkDB_escape($forumThreads['id']));
    }

    saveUserAction(_ACTIONPRUNEFO .': '. $name);

    printNotification('success', _FORUMPRUNE);
    redirect('index.php?file=Forum&page=admin', 2);
}

/* Forum settings management */

function editSetting() {
    global $adminMenu, $nuked;

    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/config/setting.php';

    foreach ($forumSettingField as $field)
        $forumSettingForm['items'][$field]['value'] = $nuked[$field];

    $adminMenu = applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    nkTemplate_addCSS('form.nkForm>div.nkForm_container>label{width:86%;}');

    echo applyTemplate('contentBox', array(
        'title'     => _ADMINFORUM .' - '. _PREFS,
        'helpFile'  => 'Forum',
        'content'   => $adminMenu . nkForm_generate($forumSettingForm)
    ));
}

function saveSetting() {
    global $nuked;

    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/config/setting.php';

    $_POST['forum_title']   = stripslashes($_POST['forum_title']);
    $_POST['forum_desc']    = stripslashes($_POST['forum_desc']);

    foreach ($forumSettingField as $field) {
        if ($forumSettingForm['items'][$field]['type'] == 'checkbox' && $_POST[$field] != 'on')
            $_POST[$field] = 'off';

        if ($nuked[$field] != $_POST[$field])
            nkDB_update(CONFIG_TABLE, array('value'), array($_POST[$field]), 'name = '. nkDB_escape($field));
    }

    saveUserAction(_ACTIONPREFFO .'.');

    printNotification('success', _PREFUPDATED);
    redirect('index.php?file=Forum&page=admin', 2);
}


$adminMenu = array(
    _FORUM => array(
        'img'   => 'modules/Admin/images/icons/speedometer.png'
    ),
    _ADDFORUM => array(
        'img'   => 'modules/Admin/images/icons/add_page.png',
        'op'    => 'editForum'
    ),
    _CATMANAGEMENT => array(
        'img'   => 'modules/Admin/images/icons/folder_full.png',
        'op'    => 'main_cat'
    ),
    _RANKMANAGEMENT => array(
        'img'   => 'modules/Admin/images/icons/ranks.png',
        'op'    => 'main_rank'
    ),
    _PRUNE => array(
        'img'   => 'modules/Admin/images/icons/remove_from_database.png',
        'op'    => 'prune'
    ),
    _PREFS => array(
        'img'   => 'modules/Admin/images/icons/process.png',
        'op'    => 'editSetting'
    )
);


switch ($_REQUEST['op']) {
    case 'editForum' :
        editForum();
        break;

    case 'saveForum' :
        saveForum();
        break;

    case 'deleteModerator' :
        deleteModerator();
        break;

    case "main_cat":
        main_cat();
        break;

    case 'editCat' :
        editForumCat();
        break;

    case 'saveCat' :
        saveForumCat();
        break;

    case 'deleteCat' :
        deleteForumCat();
        break;

    case 'deleteForum' :
        deleteForum();
        break;

    case "main_rank":
        main_rank();
        break;

    case 'editRank' :
        editRank();
        break;

    case 'saveRank' :
        saveRank();
        break;

    case 'deleteRank' :
        deleteRank();
        break;

    case 'prune' :
        prune();
        break;

    case 'doPrune' :
        doPrune();
        break;

    case 'editSetting' :
        editSetting();
        break;

    case 'saveSetting' :
        saveSetting();
        break;

    default:
        main();
        break;
}

?>