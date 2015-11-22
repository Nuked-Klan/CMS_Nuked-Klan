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

    echo "<script>\n"
        ."setTimeout('screen()','3000');\n"
        ."function screen() { \n"
        ."screenon('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main_cat');\n"
        ."}\n"
        ."</script>\n";
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

    echo "<script>\n"
        ."setTimeout('screen()','3000');\n"
        ."function screen() { \n"
        ."screenon('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main_cat');\n"
        ."}\n"
        ."</script>\n";
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

    $id         = (isset($_GET['id'])) ? $_GET['id'] : 0;
    $content    = '';

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

    echo "<script>\n"
        ."setTimeout('screen()','3000');\n"
        ."function screen() { \n"
        ."screenon('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main');\n"
        ."}\n"
        ."</script>\n";
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

    echo "<script>\n"
        ."setTimeout('screen()','3000');\n"
        ."function screen() { \n"
        ."screenon('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main');\n"
        ."}\n"
        ."</script>\n";
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

    $id         = (isset($_GET['rid'])) ? $_GET['rid'] : 0;
    $content    = '';

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

function send_rank($nom, $type, $post, $image, $upImageRank){
    global $nuked, $user;

    $nom = mysql_real_escape_string(stripslashes($nom));

    //Upload du fichier
    $filename = $_FILES['upImageRank']['name'];
    if ($filename != "") {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
            $url_image = "upload/Forum/rank/" . $filename;
            if (! move_uploaded_file($_FILES['upImageRank']['tmp_name'], $url_image)) {
                printNotification(_UPLOADFILEFAILED, 'index.php?file=Forum&page=admin&op=editRank', $type = 'error', $back = false, $redirect = true);
                return;
            }
            @chmod ($url_image, 0644);
        }
        else {
            printNotification(_NOIMAGEFILE, 'index.php?file=Forum&page=admin&op=editRank', $type = 'error', $back = false, $redirect = true);
            return;
        }
    }
    else {
        $url_image = $image;
    }


    $sql = mysql_query("INSERT INTO " . FORUM_RANK_TABLE . " ( `id` , `nom` , `type` , `post` , `image` ) VALUES ( '' , '" . $nom . "' , '" . $type . "' , '" . $post . "' , '" . $url_image . "' )");

    saveUserAction(_ACTIONADDRANKFO .': '. $nom);

    echo "<div class=\"notification success png_bg\">\n"
    . "<div>\n"
    . "" . _RANKADD . "\n"
    . "</div>\n"
    . "</div>\n";
    redirect("index.php?file=Forum&page=admin&op=main_rank", 2);
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

function modif_rank($rid, $nom, $type, $post, $image, $upImageRank){
    global $nuked, $user;

    $nom = mysql_real_escape_string(stripslashes($nom));

    //Upload du fichier
    $filename = $_FILES['upImageRank']['name'];
    if ($filename != "") {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
            $url_image = "upload/Forum/rank/" . $filename;
            if (! move_uploaded_file($_FILES['upImageRank']['tmp_name'], $url_image)) {
                printNotification(_UPLOADFILEFAILED, 'index.php?file=Forum&page=admin&op=editRank', $type = 'error', $back = false, $redirect = true);
                return;
            }
            @chmod ($url_image, 0644);
        }
        else {
            printNotification(_NOIMAGEFILE, 'index.php?file=Forum&page=admin&op=editRank', $type = 'error', $back = false, $redirect = true);
            return;
        }
    }
    else {
        $url_image = $image;
    }

    $sql = mysql_query("UPDATE " . FORUM_RANK_TABLE . " SET nom = '" . $nom . "', type = '" . $type . "', post = '" . $post . "', image = '" . $url_image . "' WHERE id = '" . $rid . "'");

    saveUserAction(_ACTIONMODIFRANKFO .': '. $nom);

    echo "<div class=\"notification success png_bg\">\n"
    . "<div>\n"
    . "" . _RANKMODIF . "\n"
    . "</div>\n"
    . "</div>\n";
    redirect("index.php?file=Forum&page=admin&op=main_rank", 2);
}

function prune(){
    global $adminMenu, $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
    ."<!--\n"
    ."\n"
    . "function verifchamps()\n"
    . "{\n"
    . "if (document.getElementById('prune_day').value.length == 0)\n"
    . "{\n"
    . "alert('" . _NODAY . "');\n"
    . "return false;\n"
    . "}\n"
    . "return true;\n"
    . "}\n"
        . "\n"
    . "// -->\n"
    . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . " - " . _PRUNE . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    echo applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    echo "<form method=\"post\" action=\"index.php?file=Forum&amp;page=admin&amp;op=do_prune\" onsubmit=\"return verifchamps();\">\n"
    . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\"  border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr><td>" . _DELOLDMESSAGES . "</td></tr>\n"
    . "<tr><td><b>" . _NUMBEROFDAY . " :</b> <input id=\"prune_day\" type=\"text\" name=\"day\" size=\"3\" maxlength=\"3\" /></td></tr>\n"
    . "<tr><td><b>" . _FORUM . " :</b> <select name=\"forum_id\"><option value=\"\">" . _ALL . "</option>\n";

    $sql_cat = mysql_query("SELECT id, nom FROM " . FORUM_CAT_TABLE . " ORDER BY ordre, nom");
    while (list($cat, $cat_name) = mysql_fetch_row($sql_cat)){
        $cat_name = printSecuTags($cat_name);

        echo "<option value=\"cat_" . $cat . "\">* " . $cat_name . "</option>\n";

        $sql_forum = mysql_query("SELECT nom, id FROM " . FORUM_TABLE . " WHERE cat = '" . $cat . "' ORDER BY ordre, nom");
        while (list($forum_name, $fid) = mysql_fetch_row($sql_forum)){
            $forum_name = printSecuTags($forum_name);

            echo "<option value=\"" . $fid . "\">&nbsp;&nbsp;&nbsp;" . $forum_name . "</option>\n";
        }
    }

    echo "</select></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _SEND . "\" /><a class=\"buttonLink\" href=\"index.php?file=Forum&amp;page=admin\">" . _BACK . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function do_prune($day, $forum_id){
    global $nuked, $user;
    
    $sql_forum = mysql_query("SELECT nom FROM " . FORUM_TABLE . " WHERE id = '" . $forum_id . "'");
    list($nom) = mysql_fetch_array($sql_forum);
    
    $prunedate = time() - (86400 * $day);
    
    if (is_int(strpos($forum_id, "cat_"))){
        $cat = preg_replace("`cat_`i", "", $forum_id);
        $and = "AND cat = '" . $cat . "'";
    }
    else if ($forum_id != ""){
        $and = "AND forum_id = '" . $forum_id . "'";
    }
    else{
        $and = "";
    }
    
    $sql = mysql_query("SELECT id, sondage FROM " . FORUM_THREADS_TABLE . " WHERE " . $prunedate . " >= last_post AND annonce = 0 " . $and);
    while (list($thread_id, $sondage) = mysql_fetch_row($sql)){
        if ($sondage == 1){
            $sql_poll = mysql_query("SELECT id FROM " . FORUM_POLL_TABLE . " WHERE thread_id = '" . $thread_id . "'");
            list($poll_id) = mysql_fetch_row($sql_poll);
            $del1 = mysql_query("DELETE FROM " . FORUM_POLL_TABLE . " WHERE id = '" . $poll_id . "'");
            $del2 = mysql_query("DELETE FROM " . FORUM_OPTIONS_TABLE . " WHERE poll_id = '" . $poll_id . "'");
            $del3 = mysql_query("DELETE FROM " . FORUM_VOTE_TABLE . " WHERE poll_id = '" . $poll_id . "'");
        }

        mysql_query("DELETE FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "'");
        mysql_query("DELETE FROM " . FORUM_THREADS_TABLE . " WHERE id = '" . $thread_id . "'");
    }

    saveUserAction(_ACTIONPRUNEFO .': '. $nom);

    echo "<div class=\"notification success png_bg\">\n"
    . "<div>\n"
    . "" .  _FORUMPRUNE . "\n"
    . "</div>\n"
    . "</div>\n";
    redirect("index.php?file=Forum&page=admin", 2);
}

function main_pref(){
    global $adminMenu, $nuked, $language;

    $checked1 = $checked2 = $checked3 = $checked4 = $checked5 = $checked6 = $checked7 = $checked8 = $checked9 = false;

    if ($nuked['forum_file'] == "on") $checked1 = true;
    if ($nuked['forum_rank_team'] == "on") $checked2 = true;
    if ($nuked['forum_image'] == "on") $checked3 = true;
    if ($nuked['forum_cat_image'] == "on") $checked4 = true;
    if ($nuked['forum_birthday'] == "on") $checked5 = true;
    if ($nuked['forum_gamer_details'] == "on") $checked6 = true;
    if ($nuked['forum_user_details'] == "on") $checked7 = true;
    if ($nuked['forum_labels_active'] == "on") $checked8 = true;
    if ($nuked['forum_display_modos'] == "on") $checked9 = true;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _ADMINFORUM . " - " . _PREFS . "</h3>\n"
    . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Forum.php\" rel=\"modal\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
    . "</div></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\">\n";

    echo applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    echo "<form method=\"post\" action=\"index.php?file=Forum&amp;page=admin&amp;op=change_pref\">\n"
    . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\"  border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
    . "<tr><td align=\"center\" colspan=\"2\">&nbsp;</td></tr>\n"
    . "<tr><td colspan=\"2\"><b>" . _FORUMTITLE . " :</b> <input type=\"text\" name=\"forum_title\" size=\"40\" value=\"" . $nuked['forum_title'] . "\" /></td></tr>\n"
    . "<tr><td colspan=\"2\"><b>" . _FORUMDESC . " :</b><br /><textarea name=\"forum_desc\" cols=\"55\" rows=\"5\">" . $nuked['forum_desc'] . "</textarea></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;</td></tr>\n"
    . "<tr><td>" . _USERANKTEAM . " :</td><td>";

    checkboxButton('forum_rank_team', 'forum_rank_team', $checked2, false);

    echo "</td></tr>\n"
    . "<tr><td>" . _DISPLAYFORUMIMAGE . " :</td><td>";

    checkboxButton('forum_image', 'forum_image', $checked3, false);
    echo "</td></tr>\n"
    . "<tr><td>" . _DISPLAYCATIMAGE . " :</td><td>";

    checkboxButton('forum_cat_image', 'forum_cat_image', $checked4, false);
    echo "</td></tr>\n"
    . "<tr><td>" . _DISPLAYBIRTHDAY . " :</td><td>";

    checkboxButton('forum_birthday', 'forum_birthday', $checked5, false);
    echo "</td></tr>\n"
    . "<tr><td>" . _DISPLAYGAMERDETAILS . " :</td><td>";

    checkboxButton('forum_gamer_details', 'forum_gamer_details', $checked6, false);
    echo "</td></tr>\n"
    . "<tr><td>" . _DISPLAYUSERDETAILS . " :</td><td>";

    checkboxButton('forum_user_details', 'forum_user_details', $checked7, false);
    echo "</td></tr>\n"
    . "<tr><td>" . _DISPLAYLABELS . " :</td><td>";

    checkboxButton('forum_labels_active', 'forum_labels_active', $checked8, false);
    echo "</td></tr>\n"
    . "<tr><td>" . _DISPLAYMODOS . " :</td><td>";

    checkboxButton('forum_display_modos', 'forum_display_modos', $checked9, false);
    echo "</td></tr>\n"
    . "<tr><td>" . _NUMBERTHREAD . " :</td><td><input type=\"text\" name=\"thread_forum_page\" size=\"2\" value=\"" . $nuked['thread_forum_page'] . "\" /></td></tr>\n"
    . "<tr><td>" . _NUMBERPOST . " :</td><td><input type=\"text\" name=\"mess_forum_page\" size=\"2\" value=\"" . $nuked['mess_forum_page'] . "\" /></td></tr>\n"
    . "<tr><td>" . _TOPICHOT . " :</td><td><input type=\"text\" name=\"hot_topic\" size=\"2\" value=\"" . $nuked['hot_topic'] . "\" /></td></tr>\n"
    . "<tr><td>" . _POSTFLOOD . " :</td><td><input type=\"text\" name=\"post_flood\" size=\"2\" value=\"" . $nuked['post_flood'] . "\" /></td></tr>\n"
    . "<tr><td>" . _MAXFIELD . " :</td><td><input type=\"text\" name=\"forum_field_max\" size=\"2\" value=\"" . $nuked['forum_field_max'] . "\" /></td></tr>\n"
    . "<tr><td>" . _ATTACHFILES . " :</td><td>";

    checkboxButton('forum_file', 'forum_file', $checked1, false);

    echo "</td></tr>\n"
    . "<tr><td>" . _FILELEVEL . " :</td><td><select name=\"forum_file_level\"><option>" . $nuked['forum_file_level'] . "</option>\n"
    . "<option>0</option>\n"
    . "<option>1</option>\n"
    . "<option>2</option>\n"
    . "<option>3</option>\n"
    . "<option>4</option>\n"
    . "<option>5</option>\n"
    . "<option>6</option>\n"
    . "<option>7</option>\n"
    ." <option>8</option>\n"
    . "<option>9</option></select></td></tr>"
    . "<tr><td>" . _MAXSIZEFILE . " :</td><td><input type=\"text\" name=\"forum_file_maxsize\" size=\"6\" value=\"" . $nuked['forum_file_maxsize'] . "\" /></td></tr>\n"
    . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _SEND . "\" /><a class=\"buttonLink\" href=\"index.php?file=Forum&amp;page=admin\">" . _BACK . "</a></div>\n"
    . "</form><br /></div></div>\n";
}

function change_pref($forum_title, $forum_desc, $forum_rank_team, $thread_forum_page, $mess_forum_page, $hot_topic, $post_flood, $forum_field_max, $forum_file, $forum_file_level, $forum_file_maxsize, $forum_image, $forum_cat_image, $forum_birthday, $forum_gamer_details, $forum_user_details, $forum_labels_active, $forum_display_modos){
    global $nuked, $user;

    if ($forum_file != "on") {
        $forum_file = "off";
    }

    if ($forum_rank_team != "on") {
        $forum_rank_team = "off";
    }

    if ($forum_image != "on") {
        $forum_image = "off";
    }

    if ($forum_cat_image != "on") {
        $forum_cat_image = "off";
    }

    if ($forum_birthday != "on") {
        $forum_birthday = "off";
    }

    if ($forum_gamer_details != "on") {
        $forum_gamer_details = "off";
    }

    if ($forum_user_details != "on") {
        $forum_user_details = "off";
    }

    if ($forum_labels_active != "on") {
        $forum_labels_active = "off";
    }

    if ($forum_display_modos != "on") {
        $forum_display_modos = "off";
    }

    $forum_title = mysql_real_escape_string(stripslashes($forum_title));
    $forum_desc = mysql_real_escape_string(stripslashes($forum_desc));

    $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_title . "' WHERE name = 'forum_title'");
    $upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_desc . "' WHERE name = 'forum_desc'");
    $upd3 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_rank_team . "' WHERE name = 'forum_rank_team'");
    $upd4 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $thread_forum_page . "' WHERE name = 'thread_forum_page'");
    $upd5 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $mess_forum_page . "' WHERE name = 'mess_forum_page'");
    $upd6 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $hot_topic . "' WHERE name = 'hot_topic'");
    $upd7 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $post_flood . "' WHERE name = 'post_flood'");
    $upd8 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_field_max . "' WHERE name = 'forum_field_max'");
    $upd9 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_file . "' WHERE name = 'forum_file'");
    $upd10 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_file_level . "' WHERE name = 'forum_file_level'");
    $upd11 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_file_maxsize . "' WHERE name = 'forum_file_maxsize'");
    $upd12 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_image . "' WHERE name = 'forum_image'");
    $upd13 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_cat_image . "' WHERE name = 'forum_cat_image'");
    $upd14 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_birthday . "' WHERE name = 'forum_birthday'");
    $upd15 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_gamer_details . "' WHERE name = 'forum_gamer_details'");
    $upd16 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_user_details . "' WHERE name = 'forum_user_details'");
    $upd17 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_labels_active . "' WHERE name = 'forum_labels_active'");
    $upd18 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $forum_display_modos . "' WHERE name = 'forum_display_modos'");

    saveUserAction(_ACTIONPREFFO .'.');

    echo "<div class=\"notification success png_bg\">\n"
    . "<div>\n"
    . "" . _PREFUPDATED . "\n"
    . "</div>\n"
    . "</div>\n";
    redirect("index.php?file=Forum&page=admin", 2);
}


$adminMenu = array(
    _FORUM => array(
        'img' => 'modules/Admin/images/icons/speedometer.png'
    ),
    _ADDFORUM => array(
        'img' => 'modules/Admin/images/icons/add_page.png',
        'op' => 'editForum'
    ),
    _CATMANAGEMENT => array(
        'img' => 'modules/Admin/images/icons/folder_full.png',
        'op' => 'main_cat'
    ),
    _RANKMANAGEMENT => array(
        'img' => 'modules/Admin/images/icons/ranks.png',
        'op' => 'main_rank'
    ),
    _PRUNE => array(
        'img' => 'modules/Admin/images/icons/remove_from_database.png',
        'op' => 'prune'
    ),
    _PREFS => array(
        'img' => 'modules/Admin/images/icons/process.png',
        'op' => 'main_pref'
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

    case "send_rank":
        send_rank($_REQUEST['nom'], $_REQUEST['type'], $_REQUEST['post'], $_REQUEST['image'], $_REQUEST['upImageRank']);
        break;

    case 'deleteRank' :
        deleteRank();
        break;

    case "modif_rank":
        modif_rank($_REQUEST['rid'], $_REQUEST['nom'], $_REQUEST['type'], $_REQUEST['post'], $_REQUEST['image'], $_REQUEST['upImageRank']);
        break;

    case "prune":
        prune();
        break;

    case "do_prune":
        do_prune($_REQUEST['day'], $_REQUEST['forum_id']);
        break;

    case "main_pref":
        main_pref();
        break;

    case "change_pref":
        change_pref($_REQUEST['forum_title'], $_REQUEST['forum_desc'], $_REQUEST['forum_rank_team'], $_REQUEST['thread_forum_page'], $_REQUEST['mess_forum_page'], $_REQUEST['hot_topic'], $_REQUEST['post_flood'], $_REQUEST['forum_field_max'], $_REQUEST['forum_file'], $_REQUEST['forum_file_level'], $_REQUEST['forum_file_maxsize'], $_REQUEST['forum_image'], $_REQUEST['forum_cat_image'], $_REQUEST['forum_birthday'], $_REQUEST['forum_gamer_details'], $_REQUEST['forum_user_details'], $_REQUEST['forum_labels_active'], $_REQUEST['forum_display_modos']);
        break;

    default:
        main();
        break;
}

?>