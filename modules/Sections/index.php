<?php
/**
 * index.php
 *
 * Frontend of Sections module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Sections'))
    return;

compteur('Sections');

include 'modules/Vote/index.php';

function index(){
    global $nuked;

    opentable();

    echo "<br /><div style=\"text-align: center;\"><big><b>" . _SECTIONS . "</b></big></div>\n"
            . "<div style=\"text-align: center;\"><br />\n"
            . "[ " . _INDEXSECTIONS . " | "
            . "<a href=\"index.php?file=Sections&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSART . "</a> | "
            . "<a href=\"index.php?file=Sections&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _TOPART . "</a> | "
            . "<a href=\"index.php?file=Suggest&amp;module=Sections\" style=\"text-decoration: underline\">" . _SUGGESTART . "</a> ]</div>\n";

    $sql = nkDB_execute("SELECT artid FROM " . SECTIONS_TABLE);
    $nb_arts = nkDB_numRows($sql);

    $sql_nbcat = nkDB_execute("SELECT secid FROM " . SECTIONS_CAT_TABLE);
    $nb_cat = nkDB_numRows($sql_nbcat);

    if ($nb_cat > 0){
        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"15\" cellpadding=\"5\">\n";

        $sql_cat = nkDB_execute("SELECT secid, secname, description FROM " . SECTIONS_CAT_TABLE . "  WHERE parentid = 0 ORDER BY position, secname");
        $test = 0;
        $last_cid = '';
        while (list($secid, $secname, $description) = nkDB_fetchArray($sql_cat)){
            $secname = printSecuTags($secname);

            if ($secid != $last_cid){
                $test++;
                if ($test == 1){
                    echo "<tr>";
                }

                echo "<td valign=\"top\"><img src=\"modules/Sections/images/fleche.gif\" alt=\"\" /><a href=\"index.php?file=Sections&amp;op=categorie&amp;secid=" . $secid . "\"><b>" . $secname . "</b></a>";

                $sql2 = nkDB_execute("SELECT secid FROM " . SECTIONS_TABLE . "  WHERE secid = '" . $secid . "'");
                $nb_art = nkDB_numRows($sql2);

                if ($nb_art > 0){
                    echo "<small>&nbsp;(" . $nb_art . ")</small>\n";
                }

                if ($description != ""){
                    echo "<div style=\"width: 225px;\">" . $description . "</div>\n";
                }
                else{
                    echo "<br />";
                }

                $sql_subcat = nkDB_execute("SELECT secid, secname FROM " . SECTIONS_CAT_TABLE . "  WHERE parentid = '" . $secid . "' ORDER BY position, secname LIMIT 0, 4");
                $nb_subcat = nkDB_numRows($sql_subcat);

                if ($nb_subcat > 0){
                    $t = 0;
                    while (list($sub_cat_id, $sub_cat_titre) = nkDB_fetchArray($sql_subcat)){
                        $sub_cat_titre = printSecuTags($sub_cat_titre);
                        $t++;
                        if ($t <= 3) echo "<small><a href=\"index.php?file=Sections&amp;op=categorie&amp;secid=" . $sub_cat_id . "\">" . $sub_cat_titre . "</a></small>&nbsp;&nbsp;";
                        else echo "<small><a href=\"index.php?file=Sections&amp;op=categorie&amp;secid=" . $secid . "\">...</a></small>";
                    }
                }

                echo "</td>\n";

                if ($test == 2){
                    $test = 0;
                    echo "</tr>\n";
                }
                $last_secid = $secid;
            }
        }

        if ($test == 1) echo "</tr>\n";
        echo "</table>\n";
    }
    else{
        echo "<br />\n";
    }

    classe("0", "0");

    if ($nb_cat > 0 || $nb_arts > 0) echo "<div style=\"text-align: center;\"><br /><small><i>( " . _THEREIS . "&nbsp;" . $nb_arts . "&nbsp;" . _ARTDB . " &amp; " . $nb_cat . "&nbsp;" . _SECDB . "&nbsp;" . _INDATABASE . " )</i></small></div><br />\n";
    else echo "<div style=\"text-align: center;\"><br />" . _NOARTINDB . "</div><br /><br />\n";

    closetable();
}

function categorie($secid){
    global $nuked;

    opentable();

    $sql = nkDB_execute("SELECT secname, description, parentid FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $secid . "'");

    if(nkDB_numRows($sql) <= 0)
        redirect("index.php?file=404");

    list($secname, $description, $parentid) = nkDB_fetchRow($sql);

    $secname = printSecuTags($secname);

    $sql2 = nkDB_execute("SELECT * FROM " . SECTIONS_TABLE . " WHERE secid = '" . $secid . "'");
    $nb_art = nkDB_numRows($sql2);

    if ($parentid > 0){
        $sql_parent = nkDB_execute("SELECT secname FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $parentid . "'");
        list($parent_titre) = nkDB_fetchArray($sql_parent);
        $parent_titre = printSecuTags($parent_titre);

        echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Sections\" style=\"text-decoration:none\"><big><b>" . _SECTIONS . "</b></big></b></a> &gt; <a href=\"index.php?file=Sections&amp;op=categorie&amp;secid=" . $parentid . "\" style=\"text-decoration:none\"><big><b>" . $parent_titre . "</b></big></a> &gt; <big><b>" . $secname . "</b></big></div><br />\n";
    }
    else{
        echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Sections\" style=\"text-decoration:none\"><big><b>" . _SECTIONS . "</b></big></a> &gt; <big><b>" . $secname . "</b></big></div><br />\n";
    }

    $sql_subcat = nkDB_execute("SELECT secid, secname, description FROM " . SECTIONS_CAT_TABLE . "  WHERE parentid = '" . $secid . "' ORDER BY position, secname");
    $nb_subcat = nkDB_numRows($sql_subcat);
    $count = 0;

    if ($nb_subcat > 0){
        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"15\" cellpadding=\"5\">\n";

        while (list($catid, $parentcat, $parentdesc) = nkDB_fetchArray($sql_subcat)){

            $parentcat = printSecuTags($parentcat);

            $sql_nbcat = nkDB_execute("SELECT secid FROM " . SECTIONS_TABLE . " WHERE secid = '" . $catid . "'");
            $nb_artcat = nkDB_numRows($sql_nbcat);

            if ($catid != $last_catid){
                $count++;
                if ($count == 1){
                    echo "<tr>";
                }

                echo "<td style=\"width: 225px;\" valign=\"top\"><img src=\"modules/Sections/images/fleche.gif\" alt=\"\" /><a href=\"index.php?file=Sections&amp;op=categorie&amp;secid=" . $catid . "\"><b>" . $parentcat . "</b></a> <small>(" . $nb_artcat . ")</small><br />" . $parentdesc . "</td>";

                if ($count == 2){
                    $count = 0;
                    echo "</tr>\n";
                }
                $last_catid = $catid;
            }
        }

        if ($count == 1) echo "</tr>\n";
        echo "</table>\n";

    }
    else{
        echo "<div style=\"text-align: center;\">" . $description . "</div><br />\n";
    }

    classe($secid, $nb_subcat);

    echo "<br />\n";

    closetable();
}

function article($artid){
    global $nuked, $user, $visiteur, $bgcolor3, $bgcolor2, $bgcolor1;

    opentable();

    if ((array_key_exists('p', $_REQUEST) && $_REQUEST['p'] == 1) OR (array_key_exists('p', $_REQUEST) && $_REQUEST['p'] == "")){
        $upd = nkDB_execute("UPDATE " . SECTIONS_TABLE . "  SET counter = counter + 1 WHERE artid = '" . $artid . "'");
    }

    $sql = nkDB_execute("SELECT artid, secid, title, content, coverage, autor, autor_id, counter, date FROM " . SECTIONS_TABLE . "  WHERE artid = '" . $artid . "'");

    if(nkDB_numRows($sql) <= 0)
        redirect("index.php?file=404");

    list($artid, $secid, $title, $content, $coverage, $autor, $autor_id, $counter, $date) = nkDB_fetchRow($sql);

    $sql2 = nkDB_execute("SELECT secname, parentid FROM " . SECTIONS_CAT_TABLE . "  WHERE secid = '" . $secid . "'");
    list($secname, $parentid) = nkDB_fetchRow($sql2);
    $secname = printSecuTags($secname);

    if ($secid == 0){
        $category = _NONE;
    }
    else if ($parentid > 0)
    {
        $sql3 = nkDB_execute("SELECT secname FROM " . SECTIONS_CAT_TABLE . " WHERE secid = '" . $parentid . "'");
        list($parent_name) = nkDB_fetchArray($sql3);
        $parent_name = printSecuTags($parent_name);

        $category = "<a href=\"index.php?file=Sections&amp;op=categorie&amp;secid=" . $parentid . "\">" . $parent_name . "</a> -&gt; <a href=\"index.php?file=Sections&amp;op=categorie&amp;secid=" . $secid . "\">" . $secname . "</a>";
    }
    else{
        $category = "<a href=\"index.php?file=Sections&amp;op=categorie&amp;secid=" . $secid . "\">" . $secname . "</a>";
    }

    $title = printSecuTags($title);

    $words = sizeof(explode(" ", $content));

    if ($autor_id != '') {
        $auteur = "<a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($autor) . "\">" . nkHtmlEntities($autor) . "</a>";
    }
    else{
        $auteur = nkHtmlEntities($autor);
    }

    $content = preg_replace('#\r\n\t#', '', $content);
    // $contentpages = explode('<div style="page-break-after: always;"><span style="display: none;">&nbsp;</span></div>', $content);
    $contentpages = explode('<div style="page-break-after: always"><span style="display:none">&nbsp;</span></div>', $content);
    $pageno = count($contentpages);
    if(!array_key_exists('p', $_REQUEST)){
        $_REQUEST['p'] = '';
    }
    if ($_REQUEST['p'] == "" || $_REQUEST['p'] < 1) $_REQUEST['p'] = 1;
    if ($_REQUEST['p'] > $pageno) $_REQUEST['p'] = $pageno;
    $arrayelement = (int)$_REQUEST['p'];
    $arrayelement --;

    if ($date != "") $date = nkDate($date);

    if ($visiteur >= admin_mod("Sections")){
        echo "<script type=\"text/javascript\">\n"
                ."<!--\n"
                ."\n"
                . "function delart(titre, id)\n"
                . "{\n"
                . "if (confirm('" . _DELETEART . " '+titre+' ! " . _CONFIRM . "'))\n"
                . "{document.location.href = 'index.php?file=Sections&page=admin&op=del&artid='+id;}\n"
                . "}\n"
                . "\n"
                . "// -->\n"
                . "</script>\n";

        echo "<div style=\"text-align: right;\"><div class=\"nkButton-group\"><a class=\"nkButton icon alone edit\" href=\"index.php?file=Sections&amp;page=admin&amp;op=edit&amp;artid=" . $artid . "\" title=\"" . _EDIT . "\" ></a>"
                . "&nbsp;<a class=\"nkButton icon alone remove danger\" href=\"javascript:delart('" . addslashes($title) . "','" . $artid . "');\" title=\"" . _DEL . "\"></a></div></div>\n";
    }


    echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Sections\" style=\"text-decoration:none\"><big><b>" . _SECTIONS . "</b></big></a></div><br />\n"
            . "<table width=\"100%\" border=\"0\" cellspacing=\"3\" cellpadding=\"3\">\n"
            . "<tr><td style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" align=\"center\">\n"
            . "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n"
            . "<tr><td style=\"width: 5%;\">&nbsp;</td>\n"
            . "<td style=\"width: 90%;\" align=\"center\"><big><b>" . $title . "</b></big></td>\n"
            . "<td style=\"width: 5%;\" align=\"center\"><a href=\"#\" onclick=\"javascript:window.open('index.php?file=Sections&amp;op=pdf&amp;artid=" . $artid . "','projet','toolbar=yes,location=no,directories=no,scrollbars=yes,resizable=yes')\"><img style=\"border: 0;\" src=\"images/pdf.gif\" alt=\"\" title=\"" . _PDF . "\" /></a></td></tr></table></td></tr>\n"
            . "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _CAT . " :</b> " . $category . "</td></tr>\n"
            . "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _ADDTHE . " :</b> " . $date . "</td></tr>\n";

    if ($auteur != ""){
        echo"<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . __('AUTHOR') . " :</b> " . $auteur . "</td></tr>\n";
    }

    echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _READSART . " :</b> " . $counter . "</td></tr>\n";

    $sql = nkDB_execute(
        'SELECT active
        FROM '. VOTE_MODULES_TABLE .'
        WHERE module = \'sections\''
    );

    list($active) = nkDB_fetchArray($sql);

    if($active == 1 && $visiteur >= nivo_mod('Vote') && nivo_mod('Vote') > -1){
        echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">";
        vote_index("Sections", $artid);
        echo "</td></tr>";
    }

    echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">";

        if ($coverage != ""){
        echo "<div class=\"nkSectionsCoverage\" ><img src=\"" . $coverage . "\" title=\"" . $title . "\" /></div>";
        }

    if ($pageno > 1){
        echo _PAGE . " : " . $_REQUEST['p'] . "/" . $pageno . "<br /><br />";
    }
    else {
        echo "<br />";
    }

    echo $contentpages[$arrayelement];

    if ($_REQUEST['p'] >= $pageno){
        $next_page = "";
    }
    else{
        $next_pagenumber = $_REQUEST['p'] + 1;

        if ($_REQUEST['p'] != 1){
            $next_page .= "";
        }

        $next_page .= "<a href=\"index.php?file=Sections&amp;op=article&amp;artid=" . $artid . "&amp;p=" . $next_pagenumber . "\">" . _NEXTPAGE . " (" . $next_pagenumber . "/" . $pageno . ")</a>&nbsp;"
                            . "<a href=\"index.php?file=Sections&amp;op=article&amp;artid=" . $artid . "&amp;p=" . $next_pagenumber . "\"><img style=\"border: 0;\" src=\"modules/Sections/images/right.gif\" alt=\"\" title=\"" . _NEXTPAGE . "\" align=\"top\"></a>";
    }

    if ($_REQUEST['p'] <= 1){
        $previous_page = "";
    }
    else{
        $previous_pagenumber = $_REQUEST['p'] - 1;
        $previous_page = "<a href=\"index.php?file=Sections&amp;op=article&amp;artid=" . $artid . "&amp;p=" . $previous_pagenumber . "\"><img style=\"border: 0;\" src=\"modules/Sections/images/left.gif\" alt=\"\" title=\"" . _PREVIOUSPAGE . "\"></a>"
                                . "&nbsp;<a href=\"index.php?file=Sections&amp;op=article&amp;artid=" . $artid . "&amp;p=" . $previous_pagenumber . "\">" . _PREVIOUSPAGE . " (" . $previous_pagenumber . "/" . $pageno . ")</a>";
    }

    echo "<br /><div style=\"text-align: center;\">" . $previous_page. "&nbsp;&nbsp;" . $next_page . "</div></td></tr></table><br />\n";

    $sql = nkDB_execute(
        'SELECT active
        FROM '. COMMENT_MODULES_TABLE .'
        WHERE module = \'sections\''
    );

    list($active) = nkDB_fetchArray($sql);

    if($active == 1 && $visiteur >= nivo_mod('Comment') && nivo_mod('Comment') > -1){
        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"100%\" border=\"0\" cellspacing=\"3\" cellpadding=\"3\"><tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">";

        include 'modules/Comment/index.php';
        com_index('Sections', $artid);

        echo "</td></tr></table>\n";
    }
    closetable();
}

function classe(){
    global $op, $nuked, $visiteur, $theme, $bgcolor1, $bgcolor2, $bgcolor3;

    $arrayRequest = array('sid', 'nb_subcat');

    foreach ($arrayRequest as $key) {
        if(array_key_exists($key, $_REQUEST)){
            ${$key} = $_REQUEST[$key];
        }
        else{
            ${$key} = '';
        }
    }

    if ($op == "classe"){
        echo "<br /><div style=\"text-align: center;\"><big><b>" . _SECTIONS . "</b></big></div>\n"
                . "<div style=\"text-align: center;\"><br />\n"
                . "[ <a href=\"index.php?file=Sections\" style=\"text-decoration: underline\">" . _INDEXSECTIONS . "</a> | ";

        if ($_REQUEST['orderby'] == "news"){
            echo _NEWSART . " | ";
        }
        else{
            echo "<a href=\"index.php?file=Sections&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSART . "</a> | ";
        }

        if ($_REQUEST['orderby'] == "count"){
            echo _TOPART . " | ";
        }
        else{
            echo "<a href=\"index.php?file=Sections&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _TOPART . "</a> | ";
        }

        echo "<a href=\"index.php?file=Suggest&amp;module=Sections\" style=\"text-decoration: underline\">" . _SUGGESTART . "</a> ]</div><br />\n";
    }

    $nb_max = $nuked['max_sections'];
    if(array_key_exists('p', $_REQUEST)){
        $page = $_REQUEST['p'];
    }
    else{
        $page = 1;
    }
    $start = $page * $nb_max - $nb_max;

    if ($sid != "") $where = "WHERE S.secid = '" . $sid . "'";
    else $where = "";

    if(!array_key_exists('orderby', $_REQUEST)){
        $_REQUEST['orderby'] = '';
    }

    if ($_REQUEST['orderby'] == "name"){
        $order = "ORDER BY S.title";
    }
    else if ($_REQUEST['orderby'] == "count"){
        $order = "ORDER BY S.counter DESC";
    }
    else if ($_REQUEST['orderby'] == "note"){
        $order = "ORDER BY note DESC";
    }
    else{
        $_REQUEST['orderby'] = "news";
        $order = "ORDER BY S.artid DESC";
    }

    $sql = nkDB_execute("SELECT S.artid, S.title, S.date, S.counter, S.content, S.coverage, AVG(V.vote) AS note  FROM " . SECTIONS_TABLE . " AS S LEFT JOIN " . VOTE_TABLE . " AS V ON S.artid = V.vid AND V.module = 'Sections' " . $where . " GROUP BY S.artid " . $order);
    
    $nb_art = nkDB_numRows($sql);

    if ($nb_art > 1 && $sid != ""){
        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\">\n"
                . "<tr><td align=\"right\"><small>" . _ORDERBY . " : ";

        if ($_REQUEST['orderby'] == "news") echo "<b>" . _DATE . "</b> | ";
        else echo "<a href=\"index.php?file=Sections&amp;op=" . $op . "&amp;orderby=news&amp;secid=" . $sid . "\">" . _DATE . "</a> | ";

        if ($_REQUEST['orderby'] == "count") echo "<b>" . _TOPFILE . "</b> | ";
        else echo "<a href=\"index.php?file=Sections&amp;op=" . $op . "&amp;orderby=count&amp;secid=" . $sid . "\">" . _TOPFILE . "</a> | ";

        if ($_REQUEST['orderby'] == "name") echo "<b>" . _NAME . "</b> | ";
        else echo"    <a href=\"index.php?file=Sections&amp;op=" . $op . "&amp;orderby=name&amp;secid=" . $sid . "\">" . _NAME . "</a> | ";

        if ($_REQUEST['orderby'] == "note") echo"<b>" . _NOTE . "</b>";
        else echo"    <a href=\"index.php?file=Sections&amp;op=" . $op . "&amp;orderby=note&amp;secid=" . $sid . "\">" . _NOTE . "</a>";

        echo "</small></td></tr></table>\n";
    }

    if ($nb_art > 0){
        if ($nb_art > $nb_max){
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\"><tr><td>";
            $url = "index.php?file=Sections&amp;op=" . $op . "&amp;secid=" . $sid . "&amp;orderby=" . $_REQUEST['orderby'];
            number($nb_art, $nb_max, $url);
            echo "</td></tr></table>\n";
        }

        echo "<br />";

        $sqlhot = nkDB_execute("SELECT artid FROM " . SECTIONS_TABLE . " ORDER BY counter DESC LIMIT 0, 10");

        $seek = mysql_data_seek($sql, $start);
        for($i = 0;$i < $nb_max;$i++){
            if (list($artid, $title, $date, $counter, $content, $coverage) = nkDB_fetchRow($sql)){
                $title = printSecuTags($title);
                $newsdate = time() - 604800;
                $att = "";

                if ($date != "" && $date > $newsdate) $att = "&nbsp;&nbsp;" . _NEW;

                if ($content != ""){
                    $content = preg_replace('#\r\n#', '', $content);
                    $texte = strip_tags($content);

                    if (strlen($texte) > 150){
                        $texte = htmlspecialchars_decode($texte, ENT_NOQUOTES);
                        $texte = substr($texte, 0, 150) . "...";
                    }
                }
                else{
                    $texte = "";
                }

                mysql_data_seek($sqlhot, 0);
                while (list($id_hot) = nkDB_fetchArray($sqlhot)){
                    if ($artid == $id_hot && $nb_art > 1 && $counter > 9) $att .= "&nbsp;&nbsp;" . _HOT;
                }

                if ($date != "") $alt = "title=\"" . _ADDTHE . "&nbsp;" . nkDate($date) . "\"";
                else $alt = "";

                if (is_file("themes/" . $theme . "/images/articles.gif")){
                    $img = "<img src=\"themes/" . $theme . "/images/articles.gif\" alt=\"\" " . $alt . "/>";
                }
                else{
                    $img = "<img src=\"modules/Sections/images/articles.gif\" alt=\"\" " . $alt . "/>";
                }

                echo "<table style=\"background: " . $bgcolor3 . ";margin-left: auto;margin-right: auto;text-align: left;\" width=\"100%\" cellspacing=\"1\" cellpadding=\"0\">\n"
                        . "<tr><td><table style=\"background: " . $bgcolor2 . ";\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
                        . "<tr>\n";

                        if ($coverage != ""){
                        echo "<td rowspan=\"3\"><img src=\"" . $coverage . "\" style=\"width:80px; height:auto; border:0;\" /></td>";
                        }

                        echo "<td style=\"width: 100%;\">" .  $img . " <a href=\"index.php?file=Sections&amp;op=article&amp;artid=" . $artid . "\" style=\"text-decoration: none\"><big><b>" . $title . "</b></big></a>" . $att . "</td>\n"
                        . "<td><a href=\"#\" onclick=\"javascript:window.open('index.php?file=Sections&amp;op=pdf&amp;artid=" . $artid . "','projet','toolbar=yes,location=no,directories=no,scrollbars=yes,resizable=yes')\">"
                        . "<img style=\"border: 0;\" src=\"images/pdf.gif\" alt=\"\" title=\"" . _PDF . "\" /></a></td></tr>\n";

                if ($texte != ""){
                    echo "<tr><td colspan=\"2\">" . $texte . "</td></tr>\n";
                }

                echo "<tr style=\"background: " . $bgcolor1 . ";\"><td colspan=\"2\">&nbsp;<b>" . _READSART . " :</b> " . $counter . "&nbsp;";

                $sql = nkDB_execute(
                    'SELECT active
                    FROM '. VOTE_MODULES_TABLE .'
                    WHERE module = \'sections\''
                );

                list($active) = nkDB_fetchArray($sql);

                if($active == 1 && $visiteur >= nivo_mod('Vote') && nivo_mod('Vote') > -1)
                    vote_index("Sections", $artid);

                echo "</td></tr></table></td></tr></table><br />\n";
            }
        }

        if ($nb_art > $nb_max){
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\"><tr><td>";
            $url = "index.php?file=Sections&amp;op=" . $op . "&amp;secid=" . $sid . "&amp;orderby=" . $_REQUEST['orderby'];
            number($nb_art, $nb_max, $url);
            echo "</td></tr></table>\n";
        }

    }
    else{
        if ($nb_subcat == 0 && $sid > 0) echo "<div style=\"text-align: center;\"><br />" . _NOARTS . "</div><br /><br />\n";
        if ($op == "classe") echo "<div style=\"text-align: center;\"><br />" . _NOARTINDB . "</div><br /><br />\n";
    }
}

function pdf($artid) {
    global $nuked;

    nkTemplate_setPageDesign('none');

    $sql = nkDB_execute("SELECT title, content FROM " . SECTIONS_TABLE . "  WHERE artid = '" . $artid . "'");
    list($title, $text) = nkDB_fetchRow($sql);

    $text = "<br />" . $text;

    $text = str_replace("&quot;", "\"", $text);
    $text = str_replace("&#039;", "'", $text);
    $text = str_replace("&agrave;", "à", $text);
    $text = str_replace("&acirc;", "â", $text);
    $text = str_replace("&eacute;", "é", $text);
    $text = str_replace("&egrave;", "è", $text);
    $text = str_replace("&ecirc;", "ê", $text);
    $text = str_replace("&ucirc;", "û", $text);

    $text = preg_replace('#\r\n\t#', '', $text);
    // $text = str_replace('<div style="page-break-after: always;"><span style="display: none;">&nbsp;</span></div>', '</page><page>', $text);
    $text = str_replace('<div style="page-break-after: always"><span style="display:none">&nbsp;</span></div>', '</page><page>', $text);

    $articleurl = $nuked['url'] . "/index.php?file=Sections&op=article&artid=" . $artid;

    $sitename = $nuked['name'] . " - " . $nuked['slogan'];

    $texte = '<page><h1>'.$title.'</h1><hr />'.$text.'<hr />'.$sitename.'<br />'.$articleurl.'</page>';

    $file = $sitename .'_'. $title;
    $file = str_replace(' ', '_', $file);
    $file .= '.pdf';

    // convert in PDF
    require_once('Includes/html2pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'fr');
        $html2pdf->setDefaultFont('dejavusans');
        $html2pdf->writeHTML(utf8_encode($texte), isset($_GET['vuehtml']));
        $html2pdf->Output($title.'.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
}

switch ($GLOBALS['op']){
    case "article":
        article($_REQUEST['artid']);
        break;
    case "classe":
        opentable();
        classe();
        closetable();
        break;
    case "categorie":
        categorie($_REQUEST['secid']);
        break;
    case "pdf":
        pdf($_REQUEST['artid']);
        break;
    default:
        index();
        break;
}

?>