<?php 
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
if (!defined("INDEX_CHECK")) {
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $nuked, $language;

translate("modules/Forum/lang/" . $language . ".lang.php");
include("modules/Forum/template.php");

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
    if ($_REQUEST['do'] == "search") {
        $where = "AS M , " . FORUM_TABLE . " AS F , " . FORUM_CAT_TABLE . " AS C WHERE";

    if (is_int(strpos($_REQUEST['id_forum'], 'cat_'))) {
            $cat = preg_replace("`cat_`i", "", $_REQUEST['id_forum']);
            $where .= " F.cat = '" . $cat . "' AND";
    }
        else if ($_REQUEST['id_forum'] != "") {
            $cat = $_REQUEST['id_forum'];
            $where .= " M.forum_id = '" . $cat . "' AND";
        }
    else {
            $cat = 0;
    }

        $where .= " F.cat = C.id AND '" . $visiteur . "' >= C.niveau AND M.forum_id = F.id AND '" . $visiteur . "' >= F.niveau AND";

        if ($_REQUEST['limit'] == "" || !is_numeric($_REQUEST['limit'])) $_REQUEST['limit'] = 50;
        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $_REQUEST['limit'] - $_REQUEST['limit'];

        $_REQUEST['autor'] = trim($_REQUEST['autor']);

        if (preg_match("`%20union%20`i", $_REQUEST['query']) ||preg_match("` union `i", $_REQUEST['query']) || preg_match("`\*union\*`i", $_REQUEST['query']) || preg_match("`\+union\+`i", $_REQUEST['query']) || preg_match("`\*`i", $_REQUEST['query']) || !is_numeric($cat)) {
            echo '<div id="nkAlertError" class="nkAlert"><strong>' . _NOENTRANCE . '</strong></div>';
            redirect("index.php?file=Forum&page=search", 2);
            closetable();
            footer();
            exit();
        }

        $_REQUEST['query'] = mysql_real_escape_string(stripslashes($_REQUEST['query']));
        $_REQUEST['query'] = trim($_REQUEST['query']);

        if ($_REQUEST['date_max'] != "" && !preg_match("`[^0-9]`i", $_REQUEST['date_max'])) {
            $req = "SELECT M.id, M.auteur, M.auteur_id, M.titre, M.txt, M.thread_id, M.forum_id, M.date FROM " . FORUM_MESSAGES_TABLE . " " . $where . " M.date > '" . $_REQUEST['date_max'] . "' ORDER BY M.date DESC";
            $result = mysql_query($req);
        } 
        else if (($_REQUEST['query'] != "" && strlen($_REQUEST['query']) < 3) || ($_REQUEST['autor'] != "" && strlen($_REQUEST['autor']) < 3)) {
            echo '<div id="nkAlertWarning" class="nkAlert"><strong>' . _3CHARSMIN . '</strong></div>';
            redirect("index.php?file=Forum&page=search", 2);
            closetable();
            footer();
            exit();
        } 

        else if ($_REQUEST['query'] != "" || $_REQUEST['autor'] != "") {
            $and = "";

            if ($_REQUEST['autor'] != "" && $_REQUEST['query'] != "") { 
                $_REQUEST['autor'] = nk_CSS($_REQUEST['autor']);
                $_REQUEST['autor'] = htmlentities($_REQUEST['autor'], ENT_QUOTES, 'ISO-8859-1');
                $and .= "(M.auteur LIKE '%" . $_REQUEST['autor'] . "%') AND ";
            }
            else if ($_REQUEST['autor'] != "") { 
                $_REQUEST['autor'] = nk_CSS($_REQUEST['autor']);
                $_REQUEST['autor'] = htmlentities($_REQUEST['autor'], ENT_QUOTES, 'ISO-8859-1');
                $and .= "(M.auteur LIKE '%" . $_REQUEST['autor'] . "%')";
            }

            if ($_REQUEST['searchtype'] == "matchexact" && $_REQUEST['query'] != "") {
                if ($_REQUEST['into'] == "message") {
                    $and .= "(M.txt LIKE '%" . $_REQUEST['query'] . "%')";
                } 
                else if ($_REQUEST['into'] == "subject") {
                    $and .= "(M.titre LIKE '%" . $_REQUEST['query'] . "%')";
                } 
                else {
                    $and .= "(M.txt LIKE '%" . $_REQUEST['query'] . "%' OR M.titre LIKE '%" . $_REQUEST['query'] . "%')";
                } 
            } 
            else if ($_REQUEST['query'] != "") {
                $search = explode(" ", $_REQUEST['query']);
                $sep = "";
                $and .= "(";
                for($i = 0; $i < count($search); $i++) {
                    if ($_REQUEST['into'] == "message") {
                        $and .= $sep . "M.txt LIKE '%" . $search[$i] . "%'";
                    } 
                    else if ($_REQUEST['into'] == "subject") {
                        $and .= $sep . "M.titre LIKE '%" . $search[$i] . "%'";
                    } 
                    else {
                        $and .= $sep . "(M.txt LIKE '%" . $search[$i] . "%' OR M.titre LIKE '%" . $search[$i] . "%')";
                    } 
                    if ($_REQUEST['searchtype'] == "matchor") $sep = " OR ";
                    else $sep = " AND ";
                } 
                $and .= ")";
            } 

            $req = "SELECT M.id, M.auteur, M.auteur_id, M.titre, M.txt, M.thread_id, M.forum_id, M.date FROM " . FORUM_MESSAGES_TABLE . " " . $where . " " . $and . " ORDER BY M.date DESC";
            $result = mysql_query($req);
        } 
        else {
            echo '<div id="nkAlertWarning" class="nkAlert"><strong>' . _NOWORDSTOSEARCH . '</strong></div>';
            redirect("index.php?file=Forum&page=search", 2);
            closetable();
            footer();
            exit();
        } 

        $nb_result = mysql_num_rows($result);

        $url = "index.php?file=Forum&amp;page=search&amp;op=" . $op . "&amp;query=" . urlencode($_REQUEST['query']) . "&amp;autor=" . urlencode($_REQUEST['autor']) . "&amp;do=" . $_REQUEST['do'] . "&amp;into=" . $_REQUEST['into'] . "&amp;searchtype=" . $_REQUEST['searchtype'] . "&amp;id_forum=" . $_REQUEST['id_forum'] . "&amp;limit=" . $_REQUEST['limit'] . "&amp;date_max=" . $_REQUEST['date_max'];
            if ($nb_result > $_REQUEST['limit']) number($nb_result, $_REQUEST['limit'], $url);
?>

    <div id="nkForumWrapper">
        <div id="nkForumInfos">
            <div>
                <h2><?php echo _FSEARCHRESULT; ?></h2>
                <p><?php echo $nb_result; ?>&nbsp;<?php echo _FSEARCHFOUND; ?>&nbsp;<strong><?php echo $_REQUEST['query']; ?></strong></p>
            </div>
        </div>
        <div id="nkForumBreadcrumb">
            <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM; ?></strong></a>&nbsp;->&nbsp;<a href="index.php?file=Forum&amp;page=search"><strong><?php echo _SEARCH; ?></strong></a>
        </div>
        <div class="nkForumCat">
            <div class="nkForumCatWrapper">
                <div class="nkForumCatHead nkBgColor3">
                    <div>
                        <div class="nkForumSearchCell"><?php echo _FORUMS; ?></div>
                        <div class="nkForumSearchCell"><?php echo _SUBJECTS; ?></div>
                        <div class="nkForumSearchCell"><?php echo _AUTHOR; ?></div>
                        <div class="nkForumSearchCell"><?php echo _DATE; ?></div>
                    </div>
                </div>
                <div class="nkForumCatContent nkBgColor2">


<?php
        if ($nb_result > 0) {
            mysql_data_seek($result, $start);
            for($i = 0;$i < $_REQUEST['limit'];$i++) {
                if (list($mess_id, $auteur, $auteur_id, $titre, $txt, $thread_id, $forum_id, $date) = mysql_fetch_row($result)) {
                    $sql_forum = mysql_query("SELECT nom, niveau FROM " . FORUM_TABLE . " WHERE id = '" . $forum_id . "'");
                    list($forum_name, $forum_level) = mysql_fetch_row($sql_forum);
                    $date = nkDate($date);
                    $forum_name = nkHtmlEntities($forum_name);

                    $auteur = nk_CSS($auteur);

                    $texte = strip_tags($txt);
                    $title = nkHtmlEntities($titre);
                    $title = nk_CSS($title);

                    if (!preg_match("`[a-zA-Z0-9\?\.]`", $texte)) {
                        $texte = _NOTEXTRESUME;
                    } 

                    if (strlen($texte) > 150) {
                        $texte = substr($texte, 0, 150) . "...";
                    } 

                    $texte = nk_CSS($texte);
                    $texte = nkHtmlEntities($texte);
                    
                    $sql_page = mysql_query("SELECT thread_id FROM " . FORUM_MESSAGES_TABLE . " WHERE thread_id = '" . $thread_id . "'");
                    $nb_rep = mysql_num_rows($sql_page);

                    if ($nb_rep > $nuked['mess_forum_page']) {
                        $topicpages = $nb_rep / $nuked['mess_forum_page'];
                        $topicpages = ceil($topicpages);
                        $page_num = "&amp;p=" . $topicpages . "#" . $mess_id;
                    } 
                    else {
                        $page_num = "#" . $mess_id;
                    } 

                    if (strlen($titre) > 30) {
                        $titre_topic = "<a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $forum_id . "&amp;thread_id=" . $thread_id . "&amp;highlight=" . urlencode($_REQUEST['query']) . $page_num . "\" onmouseover=\"AffBulle('" . $title . "', '" . $texte . "', 320)\" onmouseout=\"HideBulle()\"><b>" . nkHtmlEntities(substr($titre, 0, 30)) . "...</b></a>";
                    } 
                    else {
                        $titre_topic = "<a href=\"index.php?file=Forum&amp;page=viewtopic&amp;forum_id=" . $forum_id . "&amp;thread_id=" . $thread_id . "&amp;highlight=" . urlencode($_REQUEST['query']) . $page_num . "\" onmouseover=\"AffBulle('" . $title . "', '" . $texte . "', 320)\" onmouseout=\"HideBulle()\"><b>" . $title . "</b></a>";
                    } 

                    if ($auteur_id != "") {
                        $sql_user = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $auteur_id . "'");
                        $test = mysql_num_rows($sql_user);
                        list($autors) = mysql_fetch_row($sql_user);

                        if ($test > 0 && $autors != "") {
                            $author = "<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($autors) . "\">" . $autors . "</a>";
                        } 
                        else {
                            $author = $auteur;
                        } 
                    } 
                    else {
                        $author = $auteur;
                    } 


                    if ($visiteur >= $forum_level) {
?>
                    <div>
                        <div class="nkForumSearchForumCell nkBorderColor1">
                            <a href="index.php?file=Forum&amp;page=viewforum&amp;forum_id=<?php echo $forum_id; ?>">
                                <strong><?php echo $forum_name; ?></strong>
                            </a>
                        </div>
                        <div class="nkForumSearchTopicCell nkBorderColor1"><?php echo $titre_topic; ?></div>
                        <div class="nkForumSearchAuthorCell nkBorderColor1"><?php echo $author; ?></div>
                        <div class="nkForumSearchDateCell nkBorderColor1"><?php echo $date; ?></div>
                    </div>
<?php
                    } 
                } 
            } 
        } 
        else {
            $rquery = printSecutags($_REQUEST['query']);
            $rquery = nk_CSS($rquery);
            $rquery = stripslashes($rquery);

            if ($_REQUEST['query'] != "") {
                $result = _FNOSEARCHFOUND . " <strong><i>" . $rquery . "</i></strong>";
            } 
            else if ($_REQUEST['autor'] != "") {
                $result = _FNOSEARCHFOUND . " <strong><i>" . printSecutags($_REQUEST['autor']) . "</i></strong>";
            } 
            else if ($_REQUEST['date_max'] != "" && !preg_match("`[^0-9]`i", $_REQUEST['date_max'])) {
                $result = _FNOLASTVISITMESS;
            } 
            else {
                $result = _FNOSEARCHRESULT;
            } 

?>
                    <div>
                        <div class="nkForumSearchForumCell nkBorderColor1"></div>
                        <div class="nkForumSearchTopicCell nkBorderColor1"><?php echo $result; ?></div>
                        <div class="nkForumSearchAuthorCell nkBorderColor1"></div>
                        <div class="nkForumSearchDateCell nkBorderColor1"></div>
                    </div>
<?php
        } 
?>
                </div>
            </div>
        </div>
<?php

    $url = "index.php?file=Forum&amp;page=search&amp;op=" . $op . "&amp;query=" . urlencode($_REQUEST['query']) . "&amp;autor=" . urlencode($_REQUEST['autor']) . "&amp;do=" . $_REQUEST['do'] . "&amp;into=" . $_REQUEST['into'] . "&amp;searchtype=" . $_REQUEST['searchtype'] . "&amp;id_forum=" . $_REQUEST['id_forum'] . "&amp;limit=" . $_REQUEST['limit'] . "&amp;date_max=" . $_REQUEST['date_max'];
        if ($nb_result > $_REQUEST['limit']) number($nb_result, $_REQUEST['limit'], $url);
?>
        <div class="nkForumSearch">
            <form method="get" action="index.php" >
                <label for="forumSearch"><?php echo _SEARCH; ?> :</label>
                <input id="forumSearch" type="text" name="query" size="25" />
                <input type="hidden" name="file" value="Forum" />
                <input type="hidden" name="page" value="search" />
                <input type="hidden" name="do" value="search" />
                <input type="hidden" name="into" value="all" />
                <input type="submit" value="<?php echo _SEND; ?>" class="nkButton"/>
            </form>
        </div>
    </div>
<?php

    } 
    else
    {
        echo "<script type=\"text/javascript\">\n"
            . "    $(document).ready(function() {\n"
            . "        $(\"#autor\").autocomplete(\"index.php?file=Members&op=list&nuked_nude=index\",{
                minChars:2,
                max:200
            });
                });\n"
            . "</script>\n"

?>
        <div id="nkForumBreadcrumb">
            <a href="index.php?file=Forum"><strong><?php echo _INDEXFORUM; ?></strong></a>&nbsp;->&nbsp;<strong><?php echo _SEARCH; ?></strong>
        </div>
        <div class ="nkForumPostHead nkForumSearchCell nkBgColor3">
            <h3><?php echo _SEARCHING; ?></h3>
        </div>
            <form method="post" action="index.php?file=Forum&amp;page=search&amp;do=search">
                <div class="nkForumSearchTable">
                    <div class="nkForumCatWrapper nkBgColor2">
                        <div class="nkForumSearchTableContent">
                            <div>
                                <div class="nkForumSearchCat nkBorderColor1">
                                    <strong><?php echo _KEYWORDS; ?></strong>
                                </div>
                                <div class="nkForumSearchContent nkBorderColor1">
                                    <div><input type="text" name="query" size="30" /></div>
                                    <div><input type="radio" class="checkbox" name="searchtype" value="matchor" /><?php echo _MATCHOR; ?></div>
                                    <div><input type="radio" class="checkbox" name="searchtype" value="matchand" checked="checked" /><?php echo _MATCHAND; ?></div>
                                    <div><input type="radio" class="checkbox" name="searchtype" value="matchexact" /><?php echo _MATCHEXACT; ?></div>
                                </div>
                            </div>
                            <div>
                                <div class="nkForumSearchCat nkBorderColor1">
                                    <strong><?php echo _AUTHOR; ?></strong>
                                </div>
                                <div class="nkForumSearchContent nkBorderColor1">
                                    <div><input type="text" name="autor" id="autor" size="30" /></div>
                                </div>                               
                            </div>
                            <div>
                                <div class="nkForumSearchCat nkBorderColor1">
                                    <strong><?php echo _FORUM; ?></strong>
                                </div>
                                <div class="nkForumSearchContent nkBorderColor1">
                                    <div>
                                        <select name="id_forum"><option value=""><?php echo _ALL; ?></option>

<?php
                                        $sql_cat = mysql_query("SELECT id, nom FROM " . FORUM_CAT_TABLE . " WHERE '" . $visiteur . "' >= niveau ORDER BY ordre, nom");
                                        while (list($cat, $cat_name) = mysql_fetch_row($sql_cat)) {
                                            $cat_name = nkHtmlEntities($cat_name);

                                            echo "<option value=\"cat_" . $cat . "\">* " . $cat_name . "</option>\n";

                                            $sql_forum = mysql_query("SELECT nom, id FROM " . FORUM_TABLE . " WHERE cat = '" . $cat . "' AND '" . $visiteur . "' >= niveau ORDER BY ordre, nom");
                                            while (list($forum_name, $fid) = mysql_fetch_row($sql_forum)) {
                                                $forum_name = nkHtmlEntities($forum_name);

                                                echo "<option value=\"" . $fid . "\">&nbsp;&nbsp;&nbsp;" . $forum_name . "</option>\n";
                                            } 
                                        } 
?>
                                        </select>
                                    </div>
                                </div>                               
                            </div>
                            <div>
                                <div class="nkForumSearchCat nkBorderColor1">
                                    <strong><?php echo _SEARCHINTO; ?></strong>
                                </div>
                                <div class="nkForumSearchContent nkBorderColor1">
                                    <div>
                                        <input type="radio" class="checkbox" name="into" value="subject" /><?php echo _SUBJECTS; ?>&nbsp;
                                        <input type="radio" class="checkbox" name="into" value="message" /><?php echo _MESSAGES; ?>&nbsp;
                                        <input type="radio" class="checkbox" name="into" value="all" checked="checked" /><?php echo _BOTH; ?>
                                    </div>
                                </div>                               
                            </div>
                            <div>
                                <div class="nkForumSearchCat nkBorderColor1">
                                    <strong><?php echo _NBANSWERS; ?></strong>
                                </div>
                                <div class="nkForumSearchContent nkBorderColor1">
                                    <div>
                                        <input type="radio" class="checkbox" name="limit" value="10" />10 &nbsp;
                                        <input type="radio" name="limit" class="checkbox" value="50" checked="checked" />50&nbsp;
                                        <input type="radio" name="limit" class="checkbox" value="100" />100
                                    </div>
                                </div>                               
                            </div>
                        </div>
                    </div>
                </div>
                <div class ="nkForumPostbutton">
                    <input type="submit" value="<?php echo _SEARCHING; ?>" class="nkButton" />
                </div>                
            </form>
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
