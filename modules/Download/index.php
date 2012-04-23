<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined("INDEX_CHECK") or die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");

translate("modules/Download/lang/" . $language . ".lang.php");

$visiteur = $user ? $user[1] : 0;

$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1) {
    compteur("Download");
    include 'modules/Vote/index.php';

    function index()  {

        global $nuked, $visiteur;

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _DOWNLOAD . "</b></big></div>\n"
           . "<div style=\"text-align: center;\"><br />\n"
           . "[ " . _INDEXDOWNLOAD . " | "
           . "<a href=\"index.php?file=Download&amp;op=classe&amp;orderby=news\" style=\"text-decoration: underline\">" . _NEWSFILE . "</a> | "
           . "<a href=\"index.php?file=Download&amp;op=classe&amp;orderby=count\" style=\"text-decoration: underline\">" . _POPULAR . "</a> | "
           . "<a href=\"index.php?file=Suggest&amp;module=Download\" style=\"text-decoration: underline\">" . _SUGGESTFILE . "</a> ]</div>\n";

        $sql_nbcat = mysql_query("SELECT cid FROM " . DOWNLOAD_CAT_TABLE);
        $nb_cat = mysql_num_rows($sql_nbcat);

        $sql = mysql_query("SELECT id FROM " . DOWNLOAD_TABLE);
        $nb_download = mysql_num_rows($sql);

        if ($nb_cat > 0) {
            echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"15\" cellpadding=\"5\">\n";

            $sql_cat = mysql_query("SELECT cid, titre, description FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = 0 AND " . $visiteur . " >= level ORDER BY position, titre");
            $nb_subcat = mysql_num_rows($sql_cat);

            $test = 0;
            while (list($cid, $titre, $description) = mysql_fetch_array($sql_cat)) {
                $titre = printSecuTags($titre);

                $description = icon($description);

                if ($cid != $last_cid) {
                    $test++;

                    if ($test == 1) {
                        echo "<tr>";
                    }

                    echo "<td valign=\"top\"><img src=\"modules/Download/images/fleche.gif\" alt=\"\" /><a href=\"index.php?file=Download&amp;op=categorie&amp;cat=" . $cid . "\"><b>" . $titre . "</b></a>";

                    $sql2 = mysql_query("SELECT type FROM " . DOWNLOAD_TABLE . " WHERE type = '" . $cid . "'");
                    $nb_dl = mysql_num_rows($sql2);

                    if ($nb_dl > 0) {
                        echo "<small>&nbsp;(" . $nb_dl . ")</small>";
                    }

                    if ($description != "") {
                        echo "<div style=\"width: 225px;\">" . $description . "</div>\n";
                    } else {
                        echo "<br />";
                    }

                    $t = 0;
                    $sql_subcat = mysql_query("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = '" . $cid . "' AND " . $visiteur . " >= level ORDER BY position, titre LIMIT 0, 4");

                    while (list($sub_cat_id, $sub_cat_titre) = mysql_fetch_array($sql_subcat)) {
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

        $sql = mysql_query("SELECT titre, description, parentid, level FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cat . "'");

        if(mysql_num_rows($sql) <= 0) {
            redirect("index.php?file=404", 0);
            die;
        }

        list($cat_titre, $cat_desc, $parentid, $level) = mysql_fetch_array($sql);
        $cat_titre = printSecuTags($cat_titre);

        $cat_desc = icon($cat_desc);

        if ($visiteur >= $level) {
            if ($parentid > 0) {
                $sql_parent = mysql_query("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
                list($parent_titre) = mysql_fetch_array($sql_parent);
                $parent_titre = printSecuTags($parent_titre);

                echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <a href=\"index.php?file=Download&amp;op=categorie&amp;cat=" . $parentid . "\" style=\"text-decoration:none\"><big><b>" . $parent_titre . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n";
            } else {
                echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n";
            }

            $sql_subcat = mysql_query("SELECT cid, titre, description FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = '" . $cat . "' AND " . $visiteur . " >= level ORDER BY position, titre");
            $nb_subcat = mysql_num_rows($sql_subcat);
            $count = 0;

            if ($nb_subcat > 0) {
                echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"15\" cellpadding=\"5\">\n";

                while (list($catid, $parentcat, $parentdesc) = mysql_fetch_array($sql_subcat)) {

                    $parentcat = printSecuTags($parentcat);

                    $parentdesc = icon($parentdesc);

                    $sql_nbcat = mysql_query("SELECT id FROM " . DOWNLOAD_TABLE . " WHERE type = '" . $catid . "'");
                    $nb_dlcat = mysql_num_rows($sql_nbcat);

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
               . "<br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>\n";
        } else {
            echo"<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n"
              . "<br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>\n";
        }
    }

    function popup($dl_id) {
        global $nuked, $bgcolor1, $bgcolor2, $bgcolor3, $theme;

        $sql = mysql_query("SELECT titre, url, url2, url3 FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $dl_id . "'");
        if(mysql_num_rows($sql) <= 0) {
            redirect("index.php?file=404", 0);
            die;
        }
        list($titre, $dl_url, $dl_url2, $dl_url3) = mysql_fetch_array($sql);
        $titre = printSecuTags($titre);

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
           . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
           . "<head><title>" . _DOWNLOAD . " - " . $titre . "</title>\n"
           . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
           . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
           . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
           . "<body style=\"background: " . $bgcolor2 . ";\">" . _PLEASEWAIT . "<br />\n"
           . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"95%\" cellspacing=\"3\" cellpadding=\"3\">\n"
           . "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b><big>·</big></b>&nbsp;<a href=\"index.php?file=Download&amp;nuked_nude=index&amp;op=do_dl&amp;dl_id=" . $dl_id . "\"><b>" . _LIEN1 . "</b></a></td></tr>\n";

        if ($dl_url2 != "") {
            echo"<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b><big>·</big></b>&nbsp;<a href=\"index.php?file=Download&amp;nuked_nude=index&amp;op=do_dl&amp;dl_id=" . $dl_id . "&amp;nb=2\"><b>" . _LIEN2 . "</b></a></td></tr>\n";
        }

        if ($dl_url3 != "") {
            echo"<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><b><big>·</big></b>&nbsp;<a href=\"index.php?file=Download&amp;nuked_nude=index&amp;op=do_dl&amp;dl_id=" . $dl_id . "&amp;nb=3\"><b>" . _LIEN3 . "</b></a></td></tr>\n";
        }

        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"#\" onclick=\"javascript:window.close();\">" . _CLOSEWINDOW . "</a> ]<br /></div>";

        redirect("index.php?file=Download&nuked_nude=index&op=do_dl&dl_id=" . $dl_id, 3);

        echo "</body></html>";
    }

    function do_dl($dl_id, $nb) {
        global $nuked, $visiteur;

        $sql = mysql_query("SELECT url, url2, url3, count, level FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $dl_id . "'");
        if(mysql_num_rows($sql) <= 0) {
            redirect("index.php?file=404", 0);
            die;
        }
        list($dl_url1, $dl_url2, $dl_url3, $count, $level) = mysql_fetch_array($sql);

        if ($visiteur >= $level) {
            $new_count = $count + 1;
            $upd = mysql_query("UPDATE " . DOWNLOAD_TABLE . " SET count = '" . $new_count . "' WHERE id = '" . $dl_id . "'");

            if ($nb == 2) {
                $dl_url = $dl_url2;
            } else if ($nb == 3) {
                $dl_url = $dl_url3;
            } else {
                $dl_url = $dl_url1;
            }

            header("location: " . $dl_url);
        } else {
            echo "<div style=\"text-align: center;\">" . _NOENTRANCE . "</div>";
            redirect("index.php", 2);
        }
    }

    function broken($dl_id) {
        global $nuked;

        $sql = mysql_query("UPDATE " . DOWNLOAD_TABLE . " SET broke = broke + 1 WHERE id = '" . $dl_id . "'");
        echo "<br /><br /><div style=\"text-align: center;\">" . _THXBROKENLINK . "</div><br /><br />";
        redirect("index.php?file=Download", 2);
    }

    function description($dl_id) {
        global $nuked, $user, $visiteur, $bgcolor1, $bgcolor2, $bgcolor3;

        # include css and js library shadowbox
        echo '<script type="text/javascript"><!--'."\n"
        . 'document.write(\'<link rel="stylesheet" type="text/css" href="media/shadowbox/shadowbox.css">\');'."\n"
        . '--></script>'."\n"
        . '<script type="text/javascript" src="media/shadowbox/shadowbox.js"></script>'."\n"
        . '<script type="text/javascript">'."\n"
        . 'Shadowbox.init();'."\n"
        . '</script>'."\n";

        $upd = mysql_query("UPDATE " . DOWNLOAD_TABLE . " SET hit = hit + 1 WHERE id = '" . $dl_id . "'");

        $sql = mysql_query("SELECT id, titre, date, taille, description, type, count, level, hit, edit, screen, autor, url_autor, comp, url FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $dl_id . "'");
        if(mysql_num_rows($sql) <= 0) {
            redirect("index.php?file=404", 0);
            die;
        }
        list($dl_id, $titre, $date, $taille, $comment, $cat, $count, $level, $hit, $edit, $screen, $autor, $url_autor, $comp, $url) = mysql_fetch_array($sql);
 
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
            $sql2 = mysql_query("SELECT titre, parentid FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cat . "'");
            list($cat_name, $parentid) = mysql_fetch_array($sql2);
            $cat_name = printSecuTags($cat_name);

            if ($cat == 0) {
                $category = "N/A";
            } else if ($parentid > 0) {
                $sql3 = mysql_query("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
                list($parent_name) = mysql_fetch_array($sql3);
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
                   . "&nbsp;<a href=\"javascript:deldown('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $dl_id . "');\"><img style=\"border: 0;\" src=\"images/delete.gif\" alt=\"\" title=\"" . _DEL . "\" /></a></div>\n";
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

            if($visiteur >= nivo_mod('Vote') && nivo_mod('Vote') > -1){
                echo "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">";
                vote_index("Download", $dl_id);
                echo "</td></tr>\n";
            }

            if ($visiteur > 0) {
                echo "<tr style=\"background: " . $bgcolor2 . ";\"><td>&nbsp;</td></tr>\n"
                   . "<tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\"><img src=\"modules/Download/images/warning.gif\" alt=\"\" /> [ <a href=\"index.php?file=Download&amp;op=broken&amp;dl_id=" . $dl_id . "\">" . _INDICATELINK . "</a> ]</td></tr>\n";
            }

            echo "</table>\n"
               . "<br /><div style=\"text-align: center;\"><input type=\"button\" value=\"" . _DOWNFILE . "\" onclick=\"javascript:window.open('index.php?file=Download&amp;nuked_nude=index&amp;op=popup&amp;dl_id=" . $dl_id . "','download','toolbar=0,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=360,height=200,top=30,left=0')\" /></div><br />\n";
            
            $sql = mysql_query("SELECT active FROM " . $nuked['prefix'] . "_comment_mod WHERE module = 'download'");
            list($active) = mysql_fetch_array($sql);
            
            if($active == 1  && $visiteur >= nivo_mod('Comment') && nivo_mod('Comment') > -1) {
                echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"3\" cellpadding=\"3\"><tr style=\"background: " . $bgcolor1 . ";\"><td style=\"border: 1px dashed " . $bgcolor3 . ";\">";

                include 'modules/Comment/index.php';
                com_index("Download", $dl_id);

                echo "</td></tr></table>\n";
            }
        } else if ($level == 1) {
            echo "<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n"
               . "<br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>\n";
        } else {
            echo"<br /><div style=\"text-align: center;\"><a href=\"index.php?file=Download\" style=\"text-decoration:none\"><big><b>" . _DOWNLOAD . "</b></big></a> &gt; <big><b>" . $cat_titre . "</b></big></div><br />\n"
              . "<br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>\n";
        }
    }

    function classe($cat, $nb_subcat)
    {
        global $nuked, $theme, $visiteur, $bgcolor1, $bgcolor2, $bgcolor3;

        if ($_REQUEST['op'] == "classe") {

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
        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_download - $nb_download;

        if ($cat != "") {
            $and = "AND type = '" . $cat . "'";
        } else {
            $and = '';
        }


        if ($_REQUEST['orderby'] == "name") {
            $order = "ORDER BY D.titre";
        } else if ($_REQUEST['orderby'] == "count") {
            $order = "ORDER BY D.count DESC";
        } else if ($_REQUEST['orderby'] == "note") {
            $order = "ORDER BY note DESC";
        } else {
            $_REQUEST['orderby'] = "news";
            $order = "ORDER BY D.date DESC";
        }

        $sql = mysql_query("SELECT DISTINCT D.id, D.titre, D.description, D.taille, D.type, D.count, D.date, D.url, D.screen, avg(V.vote) AS note FROM " . DOWNLOAD_TABLE . " AS D LEFT JOIN " . VOTE_TABLE . " AS V ON D.id = V.vid AND V.module = 'Download' WHERE " . $visiteur . " >= D.level " . $and . " GROUP BY D.id " . $order);
        $nb_dl = mysql_num_rows($sql);

        if ($nb_dl > 0) {
            if ($nb_dl > 1 && $cat != "") {
                echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\">\n"
                   . "<tr><td align=\"right\"><small>" . _ORDERBY . " : ";

                if ($_REQUEST['orderby'] == "news") echo "<b>" . _DATE . "</b> | ";
                else echo "<a href=\"index.php?file=Download&amp;op=" . $_REQUEST['op'] . "&amp;orderby=news&amp;cat=" . $cat . "\">" . _DATE . "</a> | ";

                if ($_REQUEST['orderby'] == "count") echo "<b>" . _TOPFILE . "</b> | ";
                else echo"<a href=\"index.php?file=Download&amp;op=" . $_REQUEST['op'] . "&amp;orderby=count&amp;cat=" . $cat . "\">" . _TOPFILE . "</a> | ";

                if ($_REQUEST['orderby'] == "name") echo "<b>" . _NAME . "</b> | ";
                else echo "<a href=\"index.php?file=Download&amp;op=" . $_REQUEST['op'] . "&amp;orderby=name&amp;cat=" . $cat . "\">" . _NAME . "</a> | ";

                if ($_REQUEST['orderby'] == "note") echo "<b>" . _NOTE . "</b>";
                else echo "<a href=\"index.php?file=Download&amp;op=" . $_REQUEST['op'] . "&amp;orderby=note&amp;cat=" . $cat . "\">" . _NOTE . "</a>";

                echo "</small></td></tr></table>\n";
            }

            if ($nb_dl > $nb_download) {
                echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\"><tr><td>";
                $url_page = "index.php?file=Download&amp;op=". $_REQUEST['op'] . "&amp;cat=" . $cat . "&amp;orderby=" . $_REQUEST['orderby'];
                number($nb_dl, $nb_download, $url_page);
                echo "</td></tr></table>\n";
            }

            echo "<br />";

            $sqlhot = mysql_query("SELECT id FROM " . DOWNLOAD_TABLE . " ORDER BY count DESC LIMIT 0, 10");

            $seek = mysql_data_seek($sql, $start);
            for ($i = 0;$i < $nb_download;$i++) {
                if (list($dl_id, $titre, $description, $taille, $type, $count, $date, $url, $screen) = mysql_fetch_array($sql)) {
                    $newsdate = time() - 604800;
                    $att = "";

                    if ($date!="" && $date > $newsdate) $att = "&nbsp;&nbsp;" . _NEW;

                    mysql_data_seek($sqlhot, 0);
                    while (list($id_hot) = mysql_fetch_array($sqlhot)) {
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
                    $sql_com_dl = mysql_query("SELECT id FROM " . COMMENT_TABLE . " WHERE im_id = '" . $dl_id . "' AND module = 'Download'");
                    $nb_comment = mysql_num_rows($sql_com_dl);
                    
                    $sql_cat = mysql_query("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $type . "'");
                    list($name_cat_dl) = mysql_fetch_array($sql_cat);
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
                       . "<tr style=\"background: " . $bgcolor1 . ";\"><td>&nbsp;&nbsp;»&nbsp;<b>" . _DOWNLOADED . " :</b> " . $count . "&nbsp;" . _TIMES . "</td></tr>\n"
                       . "<tr style=\"background: " . $bgcolor1 . ";\"><td>&nbsp;&nbsp;»\n";
                    
                    vote_index("Download", $dl_id);
                    
                    echo "</td></tr>\n"
                       . "</td></tr></table>\n"
                       . "</td></tr></table><br />\n";
                }

            }

            if ($nb_dl > $nb_download) {
                echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\"><tr><td>";
                $url_page = "index.php?file=Download&amp;op=". $_REQUEST['op'] . "&amp;cat=" . $cat . "&amp;orderby=" . $_REQUEST['orderby'];
                number($nb_dl, $nb_download, $url_page);
                echo "</td></tr></table>\n";
            }
        } else {
            if ($nb_subcat == 0 && $cat > 0) echo "<div style=\"text-align: center;\"><br />" . _NODOWNLOADS . "</div><br /><br />\n";
            if ($_REQUEST['op'] == "classe") echo "<div style=\"text-align: center;\"><br />" . _NODOWNLOADINDB . "</div><br /><br />\n";
        }

    }

    switch ($_REQUEST['op']) {
        case "categorie":
            opentable();
            categorie($_REQUEST['cat']);
            closetable();
            break;

        case "classe":
            opentable();
            classe($_REQUEST['cat'], $_REQUEST['nb_subcat']);
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

} else if ($level_access == -1) {
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
} else if ($level_access == 1 && $visiteur == 0) {
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>";
    closetable();
} else {
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
    closetable();
}

?>