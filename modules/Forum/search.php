<?php
/**
 * search.php
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

require_once 'modules/Forum/core.php';
//require_once 'Includes/nkToken.php';
include 'modules/Forum/template.php';


function prepareForumSearchResultRow($forumMsg) {
    $forumMsg['date']      = nkDate($forumMsg['date']);
    $forumMsg['forumName'] = nkHtmlEntities($forumMsg['forumName']);
    $forumMsg['author']    = nkNickname($forumMsg, true, false);

    $forumMsg['cleanedText'] = strip_tags($forumMsg['txt']);

    if (! preg_match('`[a-zA-Z0-9\?\.]`', $forumMsg['cleanedText'])) {
        $forumMsg['cleanedText'] = _NOTEXTRESUME;
    }
    else {
        if (strlen($forumMsg['cleanedText']) > 150)
            $forumMsg['cleanedText'] = substr($forumMsg['cleanedText'], 0, 150) .'...';

        $forumMsg['cleanedText'] = nk_CSS($forumMsg['cleanedText']);
        $forumMsg['cleanedText'] = nkHtmlEntities($forumMsg['cleanedText']);
    }

    list($forumMsg['url'], $nbTopicPage) = getForumMessageUrl(
        //$forumMsg['forum_id'], $forumMsg['thread_id'], $forumMsg['id'], $forumTopic['nbReplies'] + 1
        $forumMsg['forum_id'], $forumMsg['thread_id'], $forumMsg['id'], false, '&highlight='. urlencode($query)
    );

    $forumMsg['url'] = str_replace('&', '&amp;', $forumMsg['url']);

    if (strlen($forumMsg['titre']) > 30)
        $forumMsg['cleanedTitle'] = printSecuTags(substr($forumMsg['titre'], 0, 30)) .'...';
    else
        $forumMsg['cleanedTitle'] = printSecuTags($forumMsg['titre']);

    $forumMsg['titre'] = printSecuTags($forumMsg['titre']);

    return $forumMsg;
}

// Display Forum search result
function displayForumSearchResult() {
    global $visiteur;

    list($query, $forumId, $limit, $p, $author, $dateMax, $searchType, $into) =
        getRequestVars('query', 'id_forum', 'limit', 'p', 'author', 'date_max', 'searchtype', 'into');

    // Check captcha
    if (initCaptcha() && ! validCaptchaCode())
        return;

    /* DEPRECATED
    if (stripos($query, '%20union%20') !== false
        || stripos($query, ' union ') !== false
        || stripos($query, '\*union\*') !== false
        || stripos($query, '\+union\+') !== false
        || stripos($query, '\*') !== false
    ) {
        printNotification(_NOENTRANCE, 'error');
        redirect('index.php?file=Forum&page=search', 2);
        return;
    }*/

    // Prepare where SQL clause
    $where = 'WHERE';

    // Research in Forum category
    if (strpos($forumId, 'cat_') === 0) {
        $cat = (int) str_replace('cat_', '', $forumId);

        if ($cat > 0) $where .= ' F.cat = '. $cat .' AND';
    }
    // Research in Forum
    else if ($forumId != '') {
        $cat = (int) $forumId;

        if ($cat > 0) $where .= ' M.forum_id = '. $cat .' AND';
    }

    $where .= ' '. $visiteur .' >= C.niveau AND '. $visiteur .' >= F.niveau AND';

    // Prepare research data
    $dateMax = (int) $dateMax;
    $limit   = (int) $limit;

    if (! in_array($limit, array(10, 50, 100))) $limit = 50;

    $start = (max((int) $p, 1) - 1) * $limit;

    $author = trim($author);

    $query = mysql_real_escape_string(stripslashes($query));
    $query = trim($query);

    // Get unread last Forum post since last connection
    if ($dateMax > 0) {
        $sql = 'SELECT M.id, M.auteur, M.auteur_id, M.titre, M.txt, M.thread_id, M.forum_id, M.date,
            F.nom AS forumName
            FROM '. FORUM_MESSAGES_TABLE .'AS M
            INNER JOIN '. FORUM_TABLE .' AS F
            ON F.id = M.forum_id
            INNER JOIN '. FORUM_CAT_TABLE .' AS C
            ON C.id = F.cat
            '. $where . ' M.date > '. $dateMax .'
            ORDER BY M.date DESC';

        $result   = mysql_query($sql);
        $nbResult = mysql_num_rows($result);
    }
    // TODO : Check pattern character
    // Check author and sought words length
    else if (($query != '' && strlen($query) < 3)
        || ($author != '' && strlen($author) < 3)
    ) {
        printNotification(_3CHARSMIN, 'warning');
        redirect('index.php?file=Forum&page=search', 2);
        return;
    }
    else if ($query != '' || $author != '') {
        $and = '';

        if ($author != '') {
            $author = nk_CSS($author);
            $author = nkHtmlEntities($author, ENT_QUOTES);
            $and .= '(M.auteur LIKE \'%'. mysql_real_escape_string($author) .'%\')';

            if ($query != '') $and .= ' AND ';
        }

        if ($searchType == 'matchexact' && $query != '') {
            if ($into == 'message') {
                $and .= '(M.txt LIKE \'%'. $query .'%\')';
            }
            else if ($into == 'subject') {
                $and .= '(M.titre LIKE \'%'. $query .'%\')';
            }
            else {
                $and .= '(M.txt LIKE \'%'. $query .'%\' OR M.titre LIKE \'%'. $query .'%\')';
            }
        }
        else if ($query != '') {
            $search = explode(' ', $query);
            $sep = '';
            $and .= '(';
            $nbSearch = count($search);

            for ($i = 0; $i < $nbSearch; $i++) {
                if ($into == 'message') {
                    $and .= $sep .'M.txt LIKE \'%'. $search[$i] .'%\'';
                }
                else if ($into == 'subject') {
                    $and .= $sep .'M.titre LIKE \'%'. $search[$i] .'%\'';
                }
                else {
                    $and .= $sep .'(M.txt LIKE \'%'. $search[$i] .'%\' OR M.titre LIKE \'%'. $search[$i] .'%\')';
                }

                if ($searchType == 'matchor')
                    $sep = ' OR ';
                else
                    $sep = ' AND ';
            }

            $and .= ')';
        }

        $sql = 'SELECT M.id, M.auteur, M.auteur_id, M.titre, M.txt, M.thread_id, M.forum_id, M.date,
            F.nom AS forumName
            FROM '. FORUM_MESSAGES_TABLE .'AS M
            INNER JOIN '. FORUM_TABLE .' AS F
            ON F.id = M.forum_id
            INNER JOIN '. FORUM_CAT_TABLE .' AS C
            ON C.id = F.cat
            '. $where .' '. $and .'
            ORDER BY M.date DESC';

        $result   = mysql_query($sql);
        $nbResult = mysql_num_rows($result);
    }
    // Empty author and sought words length
    else {
        printNotification(_NOWORDSTOSEARCH, 'warning');
        redirect('index.php?file=Forum&page=search', 2);
        return;
    }

    // Prepare pagination
    $pagination = '';

    if ($nbResult > $limit) {
        $url = 'index.php?file=Forum&amp;page=search&amp;do=search';

        if ($query != '')
            $url .= '&amp;query='. urlencode($query);

        if ($author != '')
            $url .= '&amp;autor='. urlencode($author);

        $url .= '&amp;into='. $into .'&amp;searchtype='. $searchType;

        if ($forumId != '')
            $url .= '&amp;id_forum='. $forumId;

        $url .= '&amp;limit='. $limit;

        if ($dateMax > 0)
            $url .= '&amp;date_max='. $dateMax;

        $pagination = number($nbResult, $limit, $url, true);
    }

    // Clean sought words
    $cleanedQuery = printSecutags($query);
    $cleanedQuery = nk_CSS($cleanedQuery);
    $cleanedQuery = stripslashes($cleanedQuery);

    // Display Forum search result
    echo applyTemplate('modules/Forum/searchResult', array(
        'query'         => $query,
        'cleanedQuery'  => $cleanedQuery,
        'limit'         => $limit,
        'authorSought'  => $author,
        'dateMax'       => $dateMax,
        'start'         => $start,
        'nbResult'      => $nbResult,
        'result'        => $result,
        'pagination'    => $pagination
    ));
}

// Display Forum search form
function displayForumSearchForm() {
    global $visiteur;

    /* TODO :
    - jQuery autocomplete missing
    - list function of Members module missing

    nkTemplate_addJS(
        '$("#author").autocomplete("index.php?file=Members&op=list", {
            minChars:2,
            max:200
        });', 'jqueryDomReady'
    );
    */

    // Get Forum list (sort by Forum category)
    $dbrForum = nkDB_selectMany(
        'SELECT FC.id AS catId, FC.nom AS catName, F.id AS forumId, F.nom AS forumName
        FROM '. FORUM_CAT_TABLE .' AS FC
        INNER JOIN '. FORUM_TABLE .' AS F
        ON F.cat = FC.id
        WHERE '. $visiteur .' >= FC.niveau AND '. $visiteur .' >= F.niveau',
        array('FC.ordre', 'FC.nom', 'F.ordre', 'F.nom')
    );

    // Display Forum search form
    echo applyTemplate('modules/Forum/searchForm', array(
        'forumList' => $dbrForum
    ));
}


list($do) = getRequestVars('do');

opentable();

switch ($do) {
    case 'search' :
        displayForumSearchResult();
        break;

    default :
        displayForumSearchForm();
        break;
}

closetable();

?>