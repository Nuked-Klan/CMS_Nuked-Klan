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

nkTemplate_addCSSFile('modules/Forum/css/backend.css');

/* Forum category management */

function formatForumCatRow($row, $nbData, $r, $functionData) {
    $row['nom'] = printSecuTags(stripslashes($row['nom']));

    return $row;
}

function main_cat() {
    global $adminMenu;

    require_once 'Includes/nkList.php';
    require_once 'modules/Forum/config/backend/category.php';

    $adminMenu = applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    $footerLink = applyTemplate('footerLink', array(
        'links' => array(
            _ADDCAT => 'index.php?file=Forum&amp;page=admin&amp;op=editCat',
            _BACK   => 'index.php?file=Forum&amp;page=admin'
        )
    ));

    echo applyTemplate('contentBox', array(
        'title'     => _ADMINFORUM .' - '. _CATMANAGEMENT,
        'helpFile'  => 'Forum',
        'content'   => $adminMenu . nkList_generate($forumCatList) . $footerLink
    ));
}

function editForumCat() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/config/backend/category.php';

    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

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

        $forumCatForm['action'] .= '&amp;id='. $id;

        if ($dbrForumCat['image'] !='')
            $forumCatForm['items']['htmlCategoryImage'] = '<img id="catImgPreview" src="'. $dbrForumCat['image'] .'" alt="" />';

        $forumCatForm['itemsFooter']['submit']['value'] = _MODIFTHISCAT;
    }

    $info = printNotification(_NOTIFIMAGESIZE, 'information', $backLinkUrl = false, $return = true);

    echo applyTemplate('contentBox', array(
        'title'     => ($id == 0) ? _ADMINFORUM .' - '. _ADDCAT : _CATMANAGEMENT .' - '. _EDITTHISCAT,
        'helpFile'  => 'Forum',
        'content'   => $info . nkForm_generate($forumCatForm)
    ));
}

function saveForumCat() {
    require_once 'Includes/nkUpload.php';

    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    $data = array(
        'nom'       => $_POST['nom'],
        'niveau'    => $_POST['niveau'],
        'ordre'     => $_POST['ordre']
    );

    if ($_FILES['upImageCat']['name'] != '') {
        list($data['image'], $uploadError) = nkUpload_check('upImageCat', 'image', 'upload/Forum/cat');

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            redirect('index.php?file=Forum&page=admin&op=editCat'. (($id > 0) ? '&id='. $id : ''), 2);
            return;
        }
    }
    else {
        $data['image'] = $_POST['urlImageCat'];
    }

    if ($id == 0) {
        nkDB_insert(FORUM_CAT_TABLE, array_keys($data), array_values($data));
        saveUserAction(_ACTIONADDCATFO .': '. $data['nom']);

        printNotification(_CATADD, 'success');
    }
    else {
        nkDB_update(FORUM_CAT_TABLE, array_keys($data), array_values($data), 'id = '. nkDB_escape($id));
        nkDB_update(FORUM_TABLE, array('niveau'), array($data['niveau']), 'cat = '. nkDB_escape($id));
        saveUserAction(_ACTIONMODIFCATFO .': '. $data['nom']);

        printNotification(_CATMODIF, 'success');
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

    printNotification(_CATDEL, 'success');
    setPreview('index.php?file=Forum', 'index.php?file=Forum&page=admin&op=main_cat');
}

/* Forum management */

function formatForumRow($row, $nbData, $r, $functionData) {
    $row['nom']         = printSecuTags(stripslashes($row['nom']));
    $row['category']    = printSecuTags(stripslashes($row['category']));

    return $row;
}

function main() {
    global $adminMenu;

    require_once 'Includes/nkList.php';
    require_once 'modules/Forum/config/backend/forum.php';

    $adminMenu = applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    $footerLink = applyTemplate('footerLink', array(
        'links' => array(
            _BACK   => 'index.php?file=Forum&amp;page=admin'
        )
    ));

    echo applyTemplate('contentBox', array(
        'title'     => _ADMINFORUM,
        'helpFile'  => 'Forum',
        'content'   => $adminMenu . nkList_generate($forumList) . $footerLink
    ));
}

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
    require_once 'modules/Forum/config/backend/forum.php';

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

        $forumForm['action'] .= '&amp;id='. $id;

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
            $forumForm['items']['image']['html'] = '<img id="forumImgPreview" src="'. $dbrForum['image'] .'" title="'. $dbrForum['nom'] .'" alt="" />';

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
    require_once 'Includes/nkUpload.php';

    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    $data = array(
        'nom'           => stripslashes($_POST['titre']),
        'comment'       => stripslashes(secu_html(nkHtmlEntityDecode($_POST['description']))),
        'cat'           => $_POST['cat'],
        'moderateurs'   => isset($_POST['modo']) ? $_POST['modo'] : '',
        'niveau'        => $_POST['niveau'],
        'level'         => $_POST['level'],
        'ordre'         => $_POST['ordre'],
        'level_poll'    => $_POST['level_poll'],
        'level_vote'    => $_POST['level_vote']
    );

    if ($_FILES['upImageForum']['name'] != '') {
        list($data['image'], $uploadError) = nkUpload_check('upImageForum', 'image', 'upload/Forum/cat');

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            redirect('index.php?file=Forum&page=admin&op=editForum'. (($id > 0) ? '&id='. $id : ''), 2);
            return;
        }
    }
    else {
        $data['image'] = $_POST['urlImageForum'];
    }

    if ($id == 0) {
        nkDB_insert(FORUM_TABLE, array_keys($data), array_values($data));
        saveUserAction(_ACTIONADDFO .': '. $data['nom']);

        printNotification(_FORUMADD, 'success');
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

        printNotification(_FORUMMODIF, 'success');
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

    printNotification(_FORUMDEL, 'success');
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

    printNotification(_MODODEL, 'success');
    redirect('index.php?file=Forum&page=admin&op=editForum&id='. $_GET['forum_id'], 2);
}

/* Forum rank management */

function formatForumRankRow($row, $nbData, $r, $functionData) {
    $row['nom'] = printSecuTags(stripslashes($row['nom']));

    if ($row['type'] == 1) {
        $row['nom'] = '<b>'. $row['nom'] .'</b>';
        $row['type'] = _MODERATEUR;
        $row['post'] = '-';
        $row['noDeleteRow'] = true;
    }
    else if ($row['type'] == 2) {
        $row['nom'] = '<b>'. $row['nom'] .'</b>';
        $row['type'] = _ADMINISTRATOR;
        $row['post'] = '-';
        $row['noDeleteRow'] = true;
    }
    else {
        $row['type'] = _MEMBER;
    }

    return $row;
}

function main_rank() {
    global $adminMenu;

    require_once 'Includes/nkList.php';
    require_once 'modules/Forum/config/backend/rank.php';

    $adminMenu = applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    $footerLink = applyTemplate('footerLink', array(
        'links' => array(
            _ADDRANK    => 'index.php?file=Forum&amp;page=admin&amp;op=editRank',
            _BACK       => 'index.php?file=Forum&amp;page=admin'
        )
    ));

    echo applyTemplate('contentBox', array(
        'title'     => _ADMINFORUM .' - '. _RANKMANAGEMENT,
        'helpFile'  => 'Forum',
        'content'   => $adminMenu . nkList_generate($forumRankList) . $footerLink
    ));
}

function editRank() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/config/backend/rank.php';

    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    if ($id > 0) {
        $dbrForumRank = nkDB_selectOne(
            'SELECT *
            FROM '. FORUM_RANK_TABLE .'
            WHERE id = '. nkDB_escape($id)
        );

        foreach ($forumRankField as $field)
            $forumRankForm['items'][$field]['value'] = $dbrForumRank[$field];

        $forumRankForm['action'] .= '&amp;id='. $id;

        if ($dbrForumRank['type'] != 0)
            $forumRankForm['items']['post']['type'] = 'hidden';

        if ($dbrForumRank['image'] !='')
            $forumRankForm['items']['image']['html'] = '<img id="rankImgPreview" src="'. $dbrForumRank['image'] .'" title="'. $dbrForumRank['nom'] .'" alt="" />';

        $forumRankForm['itemsFooter']['submit']['value'] = _MODIFTHISRANK;
    }

    echo applyTemplate('contentBox', array(
        'title'     => ($id == 0) ? _ADMINFORUM .' - '. _ADDRANK : _ADMINFORUM .' - '. _EDITTHISRANK,
        'helpFile'  => 'Forum',
        'content'   => nkForm_generate($forumRankForm)
    ));
}

function saveRank() {
    require_once 'Includes/nkUpload.php';

    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    $data = array(
        'nom'   => $_POST['nom'],
        'type'  => $_POST['type'],
        'post'  => $_POST['post']
    );

    if ($_FILES['upImageRank']['name'] != '') {
        list($data['image'], $uploadError) = nkUpload_check('upImageRank', 'image', 'upload/Forum/rank');

        if ($uploadError !== false) {
            printNotification($uploadError, 'error');
            redirect('index.php?file=Forum&page=admin&op=editRank'. (($id > 0) ? '&id='. $id : ''), 2);
            return;
        }
    }
    else {
        $data['image'] = $_POST['image'];
    }

    if ($id == 0) {
        nkDB_insert(FORUM_RANK_TABLE, array_keys($data), array_values($data));
        saveUserAction(_ACTIONADDRANKFO .': '. $data['nom']);

        printNotification(_RANKADD, 'success');
    }
    else {
        nkDB_update(FORUM_RANK_TABLE, array_keys($data), array_values($data), 'id = '. nkDB_escape($id));
        saveUserAction(_ACTIONMODIFRANKFO .': '. $data['nom']);

        printNotification(_RANKMODIF, 'success');
    }

    redirect('index.php?file=Forum&page=admin&op=main_rank', 2);
}

function deleteRank() {
    $id = (isset($_GET['id'])) ? $_GET['id'] : 0;

    $dbrForumRank = nkDB_selectOne(
        'SELECT nom
        FROM '. FORUM_RANK_TABLE .'
        WHERE id = '. nkDB_escape($id)
    );

    nkDB_delete(FORUM_RANK_TABLE, 'id = '. nkDB_escape($id));
    saveUserAction(_ACTIONDELRANKFO .': '. $dbrForumRank['nom']);

    printNotification(_RANKDEL, 'success');

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
    require_once 'modules/Forum/config/backend/prune.php';

    nkTemplate_addJS(
'$("#pruneForumForm").submit(function() {
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

    printNotification(_FORUMPRUNE, 'success');
    redirect('index.php?file=Forum&page=admin', 2);
}

/* Forum settings management */

function editSetting() {
    global $adminMenu, $nuked;

    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/config/backend/setting.php';

    foreach ($forumSettingField as $field)
        $forumSettingForm['items'][$field]['value'] = $nuked[$field];

    $adminMenu = applyTemplate('share/adminMenu', array('menu' => $adminMenu));

    echo applyTemplate('contentBox', array(
        'title'     => _ADMINFORUM .' - '. _PREFS,
        'helpFile'  => 'Forum',
        'content'   => $adminMenu . nkForm_generate($forumSettingForm)
    ));
}

function saveSetting() {
    global $nuked;

    require_once 'Includes/nkForm.php';
    require_once 'modules/Forum/config/backend/setting.php';

    $_POST['forum_title']   = stripslashes($_POST['forum_title']);
    $_POST['forum_desc']    = stripslashes($_POST['forum_desc']);

    foreach ($forumSettingField as $field) {
        if ($forumSettingForm['items'][$field]['type'] == 'checkbox' && $_POST[$field] != 'on')
            $_POST[$field] = 'off';

        if ($nuked[$field] != $_POST[$field])
            nkDB_update(CONFIG_TABLE, array('value'), array($_POST[$field]), 'name = '. nkDB_escape($field));
    }

    saveUserAction(_ACTIONPREFFO .'.');

    printNotification(_PREFUPDATED, 'success');
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

    case 'main_cat' :
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

    case 'main_rank' :
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