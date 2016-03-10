<?php
/**
 * index.php
 *
 * Frontend of Download module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Download'))
    return;

compteur('Download');

include_once 'modules/Vote/index.php';


function index()  {

    global $nuked, $visiteur;

    echo "<br /><div style=\"text-align: center;\"><big><b>" . _DOWNLOAD . "</b></big></div>\n"
        . "<div style=\"text-align: center;\"><br />\n"
        . "[ " . _INDEXDOWNLOAD . " | "
        . "<a href=\"index.php?file=Download&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSFILE . "</a> | "
        . "<a href=\"index.php?file=Download&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _POPULAR . "</a> | "
        . "<a href=\"index.php?file=Suggest&amp;module=Download\" style=\"text-decoration: underline\">" . _SUGGESTFILE . "</a> ]</div>\n";

    $sql_nbcat = nkDB_execute("SELECT cid FROM " . DOWNLOAD_CAT_TABLE);
    $nb_cat = nkDB_numRows($sql_nbcat);

    $sql = nkDB_execute("SELECT id FROM " . DOWNLOAD_TABLE);
    $nb_download = nkDB_numRows($sql);

    if ($nb_cat > 0) {
        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"15\" cellpadding=\"5\">\n";

        $sql_cat = nkDB_execute("SELECT cid, titre, description FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = 0 AND " . $visiteur . " >= level ORDER BY position, titre");
        $nb_subcat = nkDB_numRows($sql_cat);

        $test = 0;
        $last_cid = '';
        while (list($cid, $titre, $description) = nkDB_fetchArray($sql_cat)) {
            $titre = printSecuTags($titre);

            $description = icon($description);

            if ($cid != $last_cid) {
                $test++;

                if ($test == 1) {
                    echo "<tr>";
                }

                echo "<td valign=\"top\"><img src=\"modules/Download/images/fleche.gif\" alt=\"\" /><a href=\"index.php?file=Download&amp;op=categorie&amp;cat=" . $cid . "\"><b>" . $titre . "</b></a>";

                $sql2 = nkDB_execute("SELECT type FROM " . DOWNLOAD_TABLE . " WHERE type = '" . $cid . "'");
                $nb_dl = nkDB_numRows($sql2);

                if ($nb_dl > 0) {
                    echo "<small>&nbsp;(" . $nb_dl . ")</small>";
                }

                if ($description != "") {
                    echo "<div style=\"width: 225px;\">" . $description . "</div>\n";
                } else {
                    echo "<br />";
                }

                $t = 0;
                $sql_subcat = nkDB_execute("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = '" . $cid . "' AND " . $visiteur . " >= level ORDER BY position, titre LIMIT 0, 4");

                while (list($sub_cat_id, $sub_cat_titre) = nkDB_fetchArray($sql_subcat)) {
                    $sub_cat_titre = printSecuTags($sub_cat_titre);
                    $t++;
                    if ($t <= 3) echo "<small><a href=\"index.php?file=Download&amp;op=categorie&amp;cat=" . $sub_cat_id . "\">" . $sub_cat_titre . "</a></small>&nbsp;&nbsp;";
                    else echo "<a href=\"index.php?file=Download&amp;op=categorie&amp;cat=" . $cid . "\"><small>...</small></a>";
                }

                echo "</td>\n";

                if ($test == 2) {
                    $test = 0;
                    echo "</tr>\n";
                }

                $last_cid = $cid;
            }
        }

        if ($test == 1) echo "</tr>\n";

        echo "</table>\n";
    } else {
        echo "<br />\n";
    }

    classe("0", "0");

    if ($nb_cat > 0 || $nb_download > 0) {
        echo "<div style=\"text-align: center;\"><br /><small><i> ( " . _THEREIS . "&nbsp;" . $nb_download. "&nbsp;" . _FILES . " &amp; " . $nb_cat. "&nbsp;" . _NBCAT . "&nbsp;" . _INDATABASE . " ) </i></small></div><br /><br />\n";
    } else {
        echo "<div style=\"text-align: center;\"><br />" . _NODOWNLOADINDB . "</div><br /><br />\n";
    }
}

function categorie($cat) {

    global $bgcolor3, $nuked, $visiteur;

    $nb_download = $nuked['max_download'];

    $sql = nkDB_execute("SELECT titre, description, parentid, level FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cat . "'");

    if(nkDB_numRows($sql) <= 0)
        redirect("index.php?file=404");

    list($cat_titre, $cat_desc, $parentid, $level) = nkDB_fetchArray($sql);
    $cat_titre = printSecuTags($cat_titre);

    $cat_desc = icon($cat_desc);

    if ($visiteur >= $level) {
        if ($parentid > 0) {
            $sql_parent = nkDB_execute("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
            list($parent_titre) = nkDB_fetchArray($sql_parent);
            $parent_titre = printSecuTags($parent_titre);

            echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <a href=\"index.php?file=Download&amp;op=categorie&amp;cat=" . $parentid . "\" style=\"text-decoration:none\"><big><b>" . $parent_titre . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n";
        } else {
            echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n";
        }

        $sql_subcat = nkDB_execute("SELECT cid, titre, description FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = '" . $cat . "' AND " . $visiteur . " >= level ORDER BY position, titre");
        $nb_subcat = nkDB_numRows($sql_subcat);
        $count = 0;

        if ($nb_subcat > 0) {
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"15\" cellpadding=\"5\">\n";

            while (list($catid, $parentcat, $parentdesc) = nkDB_fetchArray($sql_subcat)) {

                $parentcat = printSecuTags($parentcat);

                $parentdesc = icon($parentdesc);

                $sql_nbcat = nkDB_execute("SELECT id FROM " . DOWNLOAD_TABLE . " WHERE type = '" . $catid . "'");
                $nb_dlcat = nkDB_numRows($sql_nbcat);

                if ($catid != $last_catid) {
                    $count++;
                    if ($count == 1) echo "<tr>";

                    echo "<td style=\"width: 225px;\" valign=\"top\"><img src=\"modules/Download/images/fleche.gif\" alt=\"\" /><a href=\"index.php?file=Download&amp;op=categorie&amp;cat=" . $catid . "\"><b>" . $parentcat . "</b></a> <small>(" . $nb_dlcat . ")</small><br />" . $parentdesc . "</td>";

                    if ($count == 2) {
                        $count = 0;
                        echo "</tr>\n";
                    }

                    $last_catid = $catid;
                }
            }

            if ($count == 1) echo "</tr>\n";
            echo "</table>\n";
        } else {
            echo "<div style=\"text-align: center;\">" . $cat_desc . "</div><br />\n";
        }

        classe($cat, $nb_subcat);

    } else if ($level == 1) {
        echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n"
            . "<br /><div style=\"text-align: center;\">" . __('USER_ENTRANCE') . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . __('LOGIN_USER') . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . __('REGISTER_USER') . "</a></b><br /><br /></div>\n";
    } else {
        echo"<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n"
            . "<br /><div style=\"text-align: center;\">" . __('NO_ENTRANCE') . "<br /><br /><a href=\"javascript:history.back()\"><b>" . __('BACK') . "</b></a><br /><br /></div>\n";
    }
}

function popup($dl_id) {
    global $bgcolor1, $bgcolor3;

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_DOWNLOAD .' - '. $titre);

    $sql = nkDB_execute("SELECT titre, url, url2, url3 FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $dl_id . "'");

    if(nkDB_numRows($sql) <= 0)
        redirect("index.php?file=404");

    list($titre, $dl_url, $dl_url2, $dl_url3) = nkDB_fetchArray($sql);
    $titre = printSecuTags($titre);

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"95%\" cellspacing=\"3\" cellpadding=\"3\">\n"
        . "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b><big>·</big></b>&nbsp;<a href=\"index.php?file=Download&amp;op=do_dl&amp;dl_id=" . $dl_id . "\"><b>" . _LIEN1 . "</b></a></td></tr>\n";

    if ($dl_url2 != "") {
        echo"<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b><big>·</big></b>&nbsp;<a href=\"index.php?file=Download&amp;op=do_dl&amp;dl_id=" . $dl_id . "&amp;nb=2\"><b>" . _LIEN2 . "</b></a></td></tr>\n";
    }

    if ($dl_url3 != "") {
        echo"<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b><big>·</big></b>&nbsp;<a href=\"index.php?file=Download&amp;op=do_dl&amp;dl_id=" . $dl_id . "&amp;nb=3\"><b>" . _LIEN3 . "</b></a></td></tr>\n";
    }

    echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"#\" onclick=\"javascript:window.close();\">" . __('CLOSE_WINDOW') . "</a> ]<br /></div>";

    redirect("index.php?file=Download&op=do_dl&dl_id=" . $dl_id, 3);
}

function do_dl($dl_id, $nb) {
    global $visiteur;

    $sql = nkDB_execute("SELECT url, url2, url3, count, level FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $dl_id . "'");

    if(nkDB_numRows($sql) <= 0)
        redirect("index.php?file=404");

    list($dl_url1, $dl_url2, $dl_url3, $count, $level) = nkDB_fetchArray($sql);

    if ($visiteur >= $level) {
        nkTemplate_setPageDesign('none');

        $new_count = $count + 1;
        $upd = nkDB_execute("UPDATE " . DOWNLOAD_TABLE . " SET count = '" . $new_count . "' WHERE id = '" . $dl_id . "'");

        if ($nb == 2) {
            $dl_url = $dl_url2;
        } else if ($nb == 3) {
            $dl_url = $dl_url3;
        } else {
            $dl_url = $dl_url1;
        }

        header("location: " . $dl_url);
    } else {
        nkTemplate_setPageDesign('nudePage');

        echo applyTemplate('nkAlert/noEntrance');
        redirect("index.php", 2);
    }
}

function broken($dl_id) {
    global $nuked;

    $sql = nkDB_execute("UPDATE " . DOWNLOAD_TABLE . " SET broke = broke + 1 WHERE id = '" . $dl_id . "'");

    printNotification(_THXBROKENLINK, 'success');
    redirect("index.php?file=Download", 2);
}

function description($dl_id) {
    global $nuked, $user, $visiteur, $bgcolor1, $bgcolor2, $bgcolor3;

    # include css and js library shadowbox
    nkTemplate_addCSSFile('media/shadowbox/shadowbox.css');
    nkTemplate_addJSFile('media/shadowbox/shadowbox.js');
    nkTemplate_addJS('Shadowbox.init();');

    $upd = nkDB_execute("UPDATE " . DOWNLOAD_TABLE . " SET hit = hit + 1 WHERE id = '" . $dl_id . "'");

    $sql = nkDB_execute("SELECT id, titre, date, taille, description, type, count, level, hit, edit, screen, autor, url_autor, comp, url FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $dl_id . "'");

    if(nkDB_numRows($sql) <= 0)
        redirect("index.php?file=404");

    list($dl_id, $titre, $date, $taille, $comment, $cat, $count, $level, $hit, $edit, $screen, $autor, $url_autor, $comp, $url) = nkDB_fetchArray($sql);

    $titre = printSecuTags($titre);
    $autor = printSecuTags($autor);
    $comp = printSecuTags($comp);

    $comment = icon($comment);

    $date = nkDate($date);

    if ($edit != "") $edition = nkDate($edit);
    else $edition = "N/A";

    if ($screen != "") $capture = "<a href=\"" . $screen . "\" rel=\"shadowbox\" title=\"" . $titre . "\">" . _CLICHERE . "</a>";
    else $capture = "N/A";

    if ($autor != "") $author = $autor;
    else $author = "N/A";

    if ($url_autor != "") $home_autor = "<a href=\"" . $url_autor . "\" onclick=\"window.open(this.href); return false;\">" . $url_autor . "</a>";
    else $home_autor = "N/A";

    if ($comp != "") $compatible = $comp;
    else $compatible = "N/A";

    if ($comment != "") $description = $comment;
    else $description = "N/A";

    if ($taille != "" && $taille < 1000) {
        $size = $taille . "&nbsp;" . _KO;
    } else if ($taille != "" && $taille >= 1000) {
        $taille = $taille / 1000;
        $taille = (round($taille * 100)) / 100;
        $size = $taille. "&nbsp;" . _MO;
    } else {
        $size = "N/A";
    }

    $ext = strrchr($url, '.');
    $ext = substr($ext, 1);
    if ($ext != "" && !preg_match("`\?`i", $url) && !preg_match("`.html`i", $url) && !preg_match("`.htm`i", $url)) $extension = $ext;
    else $extension = "N/A";

    $name = strrchr($url, '/');
    $name = substr($name, 1);
    if ($name != "" && !preg_match("`\?`i", $url) && !preg_match("`.html`i", $url) && !preg_match("`.htm`i", $url)) $filename = $name;
    else $filename = "N/A";

    if ($visiteur >= $level) {
        $sql2 = nkDB_execute("SELECT titre, parentid FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cat . "'");
        list($cat_name, $parentid) = nkDB_fetchArray($sql2);
        $cat_name = printSecuTags($cat_name);

        if ($cat == 0) {
            $category = "N/A";
        } else if ($parentid > 0) {
            $sql3 = nkDB_execute("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
            list($parent_name) = nkDB_fetchArray($sql3);
            $parent_name = printSecuTags($parent_name);

            $category = "<a href=\"index.php?file=Download&amp;op=categorie&amp;cat=" . $parentid . "\">" . $parent_name . "</a> -&gt; <a href=\"index.php?file=Download&amp;op=categorie&amp;cat=" . $cat . "\">" . $cat_name . "</a>";
        } else {
            $category = "<a href=\"index.php?file=Download&amp;op=categorie&amp;cat=" . $cat . "\">" . $cat_name . "</a>";
        }


        if ($visiteur >= admin_mod("Download")) {
            echo "<script type=\"text/javascript\">\n"
                . "<!--\n"
                . "\n"
                . "function deldown(titre, id)\n"
                . "{\n"
                . "if (confirm('" . _DELETEFILE . " '+titre+' ! " . _CONFIRM . "'))\n"
                . "{document.location.href = 'index.php?file=Download&page=admin&op=del_file&did='+id;}\n"
                . "}\n"
                . "\n"
                . "// -->\n"
                . "</script>\n";

            echo "<div style=\"text-align: right;\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=edit_file&amp;did=" . $dl_id . "\"><img style=\"border: 0;\" src=\"images/edition.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a>"
                . "&nbsp;<a href=\"javascript:deldown('" . addslashes($titre) . "', '" . $dl_id . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DELETE . "\" /></a></div>\n";
        }

        echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b> " . _DOWNLOAD . " </b></big></a></div><br />\n"
            . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"3\" cellpadding=\"3\">\n"
            . "<tr><td style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" align=\"center\"><big><b>" . $titre . "</b></big></td></tr>\n";

        if ($comment != "" || $nuked['hide_download'] == "off") {
            echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">" . $description . "</td></tr>\n"
                . "<tr style=\"background: " . $bgcolor2 . ";\"><td>&nbsp;</td></tr>\n";
        }

        if ($cat > 0 || $nuked['hide_download'] == "off") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _CAT . " :</b> " . $category . "</td></tr>\n";

        if ($autor != "" || $nuked['hide_download'] == "off") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _AUTOR . " :</b> " . $author . "</td></tr>\n";

        if ($url_autor != "" || $nuked['hide_download'] == "off") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _SITE . " :</b> " . $home_autor . "</td></tr>\n";

        echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _ADDTHE . " :</b> " . $date . "</td></tr>\n";

        if ($edit != "" || $nuked['hide_download'] == "off") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _EDITTHE . " :</b> " . $edition . "</td></tr>\n";

        echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _DOWNLOADED . " :</b> " . $count . "&nbsp;" . _TIMES . "</td></tr>\n"
        . "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _SEEN . " :</b> " . $hit . "&nbsp;" . _TIMES . "</td></tr>\n";

        if ($taille != "" || $nuked['hide_download'] == "off") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _SIZE . " :</b> " . $size . "</td></tr>\n";

        if ($ext != "" || $nuked['hide_download'] == "off") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _EXT . " :</b> " . $extension . "</td></tr>\n";

        if ($name != "" || $nuked['hide_download'] == "off") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _FILENAME . " :</b> " . $filename . "</td></tr>\n";

        if ($comp != "" || $nuked['hide_download'] == "off") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _COMPATIBLE . " :</b> " . $compatible . "</td></tr>\n";

        if ($screen != "" || $nuked['hide_download'] == "off") echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b>" . _CAPTURE . " :</b> " . $capture . "</td></tr>\n";

        echo "<tr style=\"background: " . $bgcolor2 . ";\"><td>&nbsp;</td></tr>";

        $sql = nkDB_execute(
            'SELECT active
            FROM '. VOTE_MODULES_TABLE .'
            WHERE module = \'download\''
        );

        list($active) = nkDB_fetchArray($sql);

        if ($active == 1  && $visiteur >= nivo_mod('Vote') && nivo_mod('Vote') > -1) {
            echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">";
            vote_index("Download", $dl_id);
            echo "</td></tr>\n";
        }

        if ($visiteur > 0) {
            echo "<tr style=\"background: " . $bgcolor2 . ";\"><td>&nbsp;</td></tr>\n"
                . "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><img src=\"modules/Download/images/warning.gif\" alt=\"\" /> [ <a href=\"index.php?file=Download&amp;op=broken&amp;dl_id=" . $dl_id . "\">" . _INDICATELINK . "</a> ]</td></tr>\n";
        }

        echo "</table>\n"
            . "<br /><div style=\"text-align: center;\"><input type=\"button\" value=\"" . _DOWNFILE . "\" onclick=\"javascript:window.open('index.php?file=Download&amp;op=popup&amp;dl_id=" . $dl_id . "','download','toolbar=0,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=360,height=200,top=30,left=0')\" /></div><br />\n";

        $sql = nkDB_execute(
            'SELECT active
            FROM '. COMMENT_MODULES_TABLE .'
            WHERE module = \'download\''
        );

        list($active) = nkDB_fetchArray($sql);

        if($active == 1  && $visiteur >= nivo_mod('Comment') && nivo_mod('Comment') > -1) {
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"3\" cellpadding=\"3\"><tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">";

            include_once 'modules/Comment/index.php';
            com_index('Download', $dl_id);

            echo "</td></tr></table>\n";
        }
    } else if ($level == 1) {
        echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n"
            . "<br /><div style=\"text-align: center;\">" . __('USER_ENTRANCE') . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . __('LOGIN_USER') . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . __('REGISTER_USER') . "</a></b><br /><br /></div>\n";
    } else {
        echo"<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n"
            . "<br /><div style=\"text-align: center;\">" . __('NO_ENTRANCE') . "<br /><br /><a href=\"javascript:history.back()\"><b>" . __('BACK') . "</b></a><br /><br /></div>\n";
    }
}

function classe()
{
    global $op, $nuked, $theme, $visiteur, $bgcolor1, $bgcolor2, $bgcolor3;

    $arrayRequest = array('cat', 'nb_subcat');

    foreach($arrayRequest as $key){
        if(array_key_exists($key, $_REQUEST)){
            ${$key} = $_REQUEST[$key];
        }
        else{
            ${$key} = '';
        }
    }

    if ($op == "classe") {

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _DOWNLOAD . "</b></big></div>\n"
            . "<div style=\"text-align: center;\"><br />\n"
            . "[ <a href=\"index.php?file=Download\" style=\"text-decoration: underline\">" . _INDEXDOWNLOAD . "</a> | ";

        if ($_REQUEST['orderby'] == "news") {
            echo _NEWSFILE . " | ";
        } else {
            echo "<a href=\"index.php?file=Download&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSFILE . "</a> | ";
        }

        if ($_REQUEST['orderby'] == "count") {
            echo _POPULAR . " | ";
        } else {
            echo "<a href=\"index.php?file=Download&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _POPULAR . "</a> | ";
        }

        echo "<a href=\"index.php?file=Suggest&amp;module=Download\" style=\"text-decoration: underline\">" . _SUGGESTFILE . "</a> ]</div><br />\n";
    }

    $nb_download = $nuked['max_download'];

    if(array_key_exists('p', $_REQUEST)){
        $page = $_REQUEST['p'];
    }
    else{
        $page = 1;
    }
    $start = $page * $nb_download - $nb_download;

    if ($cat != "") {
        $and = "AND type = '" . $cat . "'";
    } else {
        $and = '';
    }

    if(array_key_exists('orderby', $_REQUEST)){
        if ($_REQUEST['orderby'] == "name") {
            $order = "ORDER BY D.titre";
        } else if ($_REQUEST['orderby'] == "count") {
            $order = "ORDER BY D.count DESC";
        } else if ($_REQUEST['orderby'] == "note") {
            $order = "ORDER BY note DESC";
        }
        else {
            $_REQUEST['orderby'] = "news";
            $order = "ORDER BY D.date DESC";
        }
    }
    else{
        $_REQUEST['orderby'] = "news";
        $order = "ORDER BY D.date DESC";
    }

    $sql = nkDB_execute("SELECT DISTINCT D.id, D.titre, D.description, D.taille, D.type, D.count, D.date, D.url, D.screen, avg(V.vote) AS note FROM " . DOWNLOAD_TABLE . " AS D LEFT JOIN " . VOTE_TABLE . " AS V ON D.id = V.vid AND V.module = 'Download' WHERE " . $visiteur . " >= D.level " . $and . " GROUP BY D.id " . $order);
    $nb_dl = nkDB_numRows($sql);

    if ($nb_dl > 0) {
        if ($nb_dl > 1 && $cat != "") {
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\">\n"
                . "<tr><td align=\"right\"><small>" . _ORDERBY . " : ";

            if ($_REQUEST['orderby'] == "news") echo "<b>" . _DATE . "</b> | ";
            else echo "<a href=\"index.php?file=Download&amp;op=" . $op . "&amp;orderby=news&amp;cat=" . $cat . "\">" . _DATE . "</a> | ";

            if ($_REQUEST['orderby'] == "count") echo "<b>" . _TOPFILE . "</b> | ";
            else echo"<a href=\"index.php?file=Download&amp;op=" . $op . "&amp;orderby=count&amp;cat=" . $cat . "\">" . _TOPFILE . "</a> | ";

            if ($_REQUEST['orderby'] == "name") echo "<b>" . _NAME . "</b> | ";
            else echo "<a href=\"index.php?file=Download&amp;op=" . $op . "&amp;orderby=name&amp;cat=" . $cat . "\">" . _NAME . "</a> | ";

            if ($_REQUEST['orderby'] == "note") echo "<b>" . _NOTE . "</b>";
            else echo "<a href=\"index.php?file=Download&amp;op=" . $op . "&amp;orderby=note&amp;cat=" . $cat . "\">" . _NOTE . "</a>";

            echo "</small></td></tr></table>\n";
        }

        if ($nb_dl > $nb_download) {
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\"><tr><td>";
            $url_page = "index.php?file=Download&amp;op=". $op . "&amp;cat=" . $cat . "&amp;orderby=" . $_REQUEST['orderby'];
            number($nb_dl, $nb_download, $url_page);
            echo "</td></tr></table>\n";
        }

        echo "<br />";

        $sqlhot = nkDB_execute("SELECT id FROM " . DOWNLOAD_TABLE . " ORDER BY count DESC LIMIT 0, 10");

        $seek = nkDB_dataSeek($sql, $start);
        for ($i = 0;$i < $nb_download;$i++) {
            if (list($dl_id, $titre, $description, $taille, $type, $count, $date, $url, $screen) = nkDB_fetchArray($sql)) {
                $newsdate = time() - 604800;
                $att = "";

                if ($date!="" && $date > $newsdate) $att = "&nbsp;&nbsp;" . _NEW;

                nkDB_dataSeek($sqlhot, 0);
                while (list($id_hot) = nkDB_fetchArray($sqlhot)) {
                    if ($dl_id == $id_hot && $nb_dl > 1 && $count > 9) $att .= "&nbsp;&nbsp;" . _HOT;
                }

                $extension = strrchr($url, '.');
                $extension = substr($extension, 1);

                if (is_file("modules/Download/images/" . $extension . ".gif")) {
                    $img = "<a href=\"index.php?file=Download&amp;op=description&amp;dl_id=" . $dl_id . "\"><img style=\"border: 0;\" src=\"modules/Download/images/" . $extension . ".gif\" title=\"" . $extension . "\" alt=\"\" /></a>";
                } else if (is_file("themes/" . $theme . "/images/files.gif")) {
                    $img = "<a href=\"index.php?file=Download&amp;op=description&amp;dl_id=" . $dl_id . "\"><img style=\"border: 0;\" src=\"themes/" . $theme . "/images/files.gif\" title=\"" . $extension . "\" alt=\"\" /></a>";
                } else {
                    $img = "<a href=\"index.php?file=Download&amp;op=description&amp;dl_id=" . $dl_id . "\"><img style=\"border: 0;\" src=\"modules/Download/images/files.gif\" title=\"" . $extension . "\" alt=\"\" /></a>";
                }

                if ($description != "") {
                    $description = str_replace("\r", "", $description);
                    $description = str_replace("\n", " ", $description);
                    $texte = strip_tags($description);

                    if (strlen($texte) > 150) {
                        $texte = substr($texte, 0, 150) . "...";
                    }

                    $texte = printSecuTags($texte);

                } else {
                    $texte = "";
                }


                if ($taille != "" && $taille < 1000) {
                    $taille = $taille . "&nbsp;" . _KO;
                } else if ($taille != "" && $taille >= 1000) {
                    $taille = $taille / 1000;
                    $taille = $taille. "&nbsp;" . _MO;
                } else {
                    $taille = "N/A";
                }

                // ----- Affiche le nombre de commentaires -----
                $sql_com_dl = nkDB_execute("SELECT id FROM " . COMMENT_TABLE . " WHERE im_id = '" . $dl_id . "' AND module = 'Download'");
                $nb_comment = nkDB_numRows($sql_com_dl);

                $sql_cat = nkDB_execute("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $type . "'");
                list($name_cat_dl) = nkDB_fetchArray($sql_cat);
                $name_cat_dl = stripslashes($name_cat_dl);
                $name_cat_dl = printSecuTags($name_cat_dl);
                $category = "" . $name_cat_dl . "";
                if($category == "") $category = "N/A";

                if ($screen != "") {
                    $box = "<img style=\"cursor: pointer; overflow: auto; max-width: 160px; max-height: 120px; width: expression(this.scrollWidth >= 160? '160px' : 'auto'); height: expression(this.scrollHeight >= 120? '120px' : 'auto');\" src=\"" . checkimg($screen) . "\" onclick=\"document.location='index.php?file=Download&op=description&dl_id=" . $dl_id . "'\" border=\"0\" title=\"" . $titre . "\" alt=\"" . $titre . "\" />";
                } else {
                    $box = "<img style=\"cursor: pointer; overflow: auto; max-width: 160px; max-height: 120px; width: expression(this.scrollWidth >= 160? '160px' : 'auto'); height: expression(this.scrollHeight >= 120? '120px' : 'auto');\" src=\"" . checkimg('images/noimagefile.gif') . "\" onclick=\"document.location='index.php?file=Download&op=description&dl_id=" . $dl_id . "'\" border=\"0\" title=\"" . $titre . "\" alt=\"" . $titre . "\" />";
                }

                echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
                    . "<tr style=\"background: " . $bgcolor3 . ";\"><td colspan=\"2\">" . $img . "&nbsp;<a href=\"index.php?file=Download&amp;op=description&amp;dl_id=" . $dl_id . "\"><big><b>" . $titre . "</b></big></a>" . $att . "</td></tr>\n"
                    . "<tr style=\"background: " . $bgcolor1 . ";height: 140px;text-align: center;\"><td style=\"width: 170px;vertical-align: middle;\">" . $box . "</td><td style=\"vertical-align: top;\">\n"
                    . "<table style=\"text-align: left;\" width=\"100%\" cellspacing=\"1\" cellpadding=\"1\">\n"
                    . "<tr style=\"background: " . $bgcolor1 . ";\"><td>&nbsp;</td></tr>\n"
                    . "<tr style=\"background: " . $bgcolor1 . ";\"><td>&nbsp;&nbsp;»&nbsp;<b>" . _ADDTHE . " :</b> " . nkDate($date) . "</td></tr>\n"
                    . "<tr style=\"background: " . $bgcolor1 . ";\"><td>&nbsp;&nbsp;»&nbsp;<b>" . _CAT . " :</b> " . $category . "</td></tr>\n"
                    . "<tr style=\"background: " . $bgcolor1 . ";\"><td>&nbsp;&nbsp;»&nbsp;<b>" . _SIZE . " :</b> " . $taille . "</td></tr>\n"
                    . "<tr style=\"background: " . $bgcolor1 . ";\"><td>&nbsp;&nbsp;»&nbsp;<b>" . _FILECOMMENT . " :</b> " . $nb_comment . "</td></tr>\n"
                    . "<tr style=\"background: " . $bgcolor1 . ";\"><td>&nbsp;&nbsp;»&nbsp;<b>" . _DOWNLOADED . " :</b> " . $count . "&nbsp;" . _TIMES . "</td></tr>\n";

                    $sql = nkDB_execute(
                        'SELECT active
                        FROM '. VOTE_MODULES_TABLE .'
                        WHERE module = \'download\''
                    );

                    list($active) = nkDB_fetchArray($sql);

                    if ($active == 1  && $visiteur >= nivo_mod('Vote') && nivo_mod('Vote') > -1) {
                        echo "<tr style=\"background: " . $bgcolor1 . ";\"><td>&nbsp;&nbsp;»\n";
                        vote_index("Download", $dl_id);
                        echo "</td></tr>\n";
                    }

                echo "</td></tr></table>\n"
                    . "</td></tr></table><br />\n";
            }

        }

        if ($nb_dl > $nb_download) {
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\"><tr><td>";
            $url_page = "index.php?file=Download&amp;op=". $op . "&amp;cat=" . $cat . "&amp;orderby=" . $_REQUEST['orderby'];
            number($nb_dl, $nb_download, $url_page);
            echo "</td></tr></table>\n";
        }
    } else {
        if ($nb_subcat == 0 && $cat > 0) echo "<div style=\"text-align: center;\"><br />" . _NODOWNLOADS . "</div><br /><br />\n";
        if ($op == "classe") echo "<div style=\"text-align: center;\"><br />" . _NODOWNLOADINDB . "</div><br /><br />\n";
    }

}

switch ($GLOBALS['op']) {
    case "categorie":
        opentable();
        categorie($_REQUEST['cat']);
        closetable();
        break;

    case "classe":
        opentable();
        classe();
        closetable();
        break;

    case "popup":
        popup($_REQUEST['dl_id']);
        break;

    case "do_dl":
        do_dl($_REQUEST['dl_id'], $_REQUEST['nb']);
        break;

    case "broken":
        opentable();
        broken($_REQUEST['dl_id']);
        closetable();
        break;

    case "description":
        opentable();
        description($_REQUEST['dl_id']);
        closetable();
        break;

    default:
        opentable();
        index();
        closetable();
        break;
}

?>