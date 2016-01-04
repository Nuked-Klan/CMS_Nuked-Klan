<?php
/**
 * admin.php
 *
 * Backend of News module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('News'))
    return;


function main() {
    global $nuked, $language;

    $nb_news = 30;

    $sql = mysql_query("SELECT id FROM " . NEWS_TABLE);
    $count = mysql_num_rows($sql);

    if(array_key_exists('p', $_REQUEST)){
        $page = $_REQUEST['p'];
    }
    else{
        $page = 1;
    }
    $start = $page * $nb_news - $nb_news;

    echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function del_news(titre, id)\n"
        . "{\n"
        . "if (confirm('" . _DELETENEWS . " '+titre+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=News&page=admin&op=do_del&news_id='+id;}\n"
        . "}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADMINNEWS . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        nkAdminMenu(1);

    if(!array_key_exists('ordreby', $_REQUEST)){
        $order_by = 'date DESC';
    } else if ($_REQUEST['orderby'] == "date") {
        $order_by = "date DESC";
    } else if ($_REQUEST['orderby'] == "title") {
        $order_by = "titre";
    } else if ($_REQUEST['orderby'] == "cat") {
        $order_by = "cat";
    } else if ($_REQUEST['orderby'] == "author") {
        $order_by = "auteur";
    } else {
        $order_by = "date DESC";
    }

    echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">\n"
        . "<tr><td align=\"right\">" . _ORDERBY . " : ";

    if ((array_key_exists('ordreby', $_REQUEST) && $_REQUEST['orderby'] == "date") || !array_key_exists('ordreby', $_REQUEST)) {
        echo "<b>" . _DATE . "</b> | ";
    } else {
        echo "<a href=\"index.php?file=News&amp;page=admin&amp;orderby=date\">" . _DATE . "</a> | ";
    }

    if (array_key_exists('ordreby', $_REQUEST) && $_REQUEST['orderby'] == "title") {
        echo "<b>" . _TITLE . "</b> | ";
    } else {
        echo "<a href=\"index.php?file=News&amp;page=admin&amp;orderby=title\">" . _TITLE . "</a> | ";
    }

    if (array_key_exists('ordreby', $_REQUEST) && $_REQUEST['orderby'] == "author") {
        echo "<b>" . _AUTHOR . "</b> | ";
    } else {
        echo "<a href=\"index.php?file=News&amp;page=admin&amp;orderby=author\">" . _AUTHOR . "</a> | ";
    }

    if (array_key_exists('ordreby', $_REQUEST) && $_REQUEST['orderby'] == "cat") {
        echo "<b>" . _CAT . "</b>";
    } else {
        echo "<a href=\"index.php?file=News&amp;page=admin&amp;orderby=cat\">" . _CAT . "</a>";
    }

    echo "&nbsp;</td></tr></table>\n";


    if ($count > $nb_news) {
        echo "<div>";
        $url = "index.php?file=News&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
        number($count, $nb_news, $url);
        echo "</div>\n";
    }

    echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 25%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
        . "<td style=\"width: 15%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _AUTHOR . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql2 = mysql_query("SELECT id, titre, auteur, auteur_id, cat, date FROM " . NEWS_TABLE . " ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_news);
    while (list($news_id, $titre, $autor, $autor_id, $cat, $date) = mysql_fetch_array($sql2)) {
        $date = nkDate($date);

        $sql3 = mysql_query("SELECT titre FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cat. "'");
        list($categorie) = mysql_fetch_array($sql3);
        $categorie = printSecuTags($categorie);

        if ($autor_id != "") {
            $sql4 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
            $test = mysql_num_rows($sql4);
        }

        if ($autor_id != "" && $test > 0) {
            list($_REQUEST['auteur']) = mysql_fetch_array($sql4);
        } else {
            $_REQUEST['auteur'] = $autor;
        }

        if (strlen($titre) > 25) {
            $title = "<span style=\"cursor: hand\" title=\"" . printSecuTags($titre) . "\">" . printSecuTags(substr($titre, 0, 25)) . "...</span>";
        } else {
            $title = printSecuTags($titre);
        }

        echo "<tr>\n"
            . "<td style=\"width: 25%;\">" . $title . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $categorie . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $_REQUEST['auteur'] . "</td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=News&amp;page=admin&amp;op=edit&amp;news_id=" . $news_id . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISNEWS . "\" /></a></td>\n"
            . "<td style=\"width: 10%;\" align=\"center\"><a href=\"javascript:del_news('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $news_id . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISNEWS . "\" /></a></td></tr>\n";
    }

    if ($count == 0) {
        echo "<tr><td align=\"center\" colspan=\"6\">" . _NONEWSINDB . "</td></tr>\n";
    }

    echo" </table>\n";

    if ($count > $nb_news) {
        echo "<div>";
        $url = "index.php?file=News&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
        number($count, $nb_news, $url);
        echo "</div>\n";
    }

    echo "<br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . _BACK . "</a></div><br /></div></div>\n";
}

function add() {
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADDNEWS . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        nkAdminMenu(2);

    echo "<form method=\"post\" action=\"index.php?file=News&amp;page=admin&amp;op=do_add\" onsubmit=\"backslash('news_texte');backslash('news_suite');\" enctype=\"multipart/form-data\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
        . "<tr><td align=\"center\"><b>" . _TITLE . " :</b>&nbsp;<input type=\"text\" id=\"news_titre\" name=\"titre\" maxlength=\"100\" size=\"45\" /></td></tr>\n"
        . "<tr><td align=\"center\"><b>" . _PUBLISH . "&nbsp;" . _THE ." :</b>&nbsp;<select id=\"news_jour\" name=\"jour\">\n";

    $day = 1;
    while ($day < 32) {
        if ($day == date("d")) {
            echo "<option value=\"" . $day . "\" selected=\"selected\">" . $day . "</option>\n";
        } else {
            echo "<option value=\"" . $day . "\">" . $day . "</option>\n";
        }
        $day++;
    }

    echo "</select>&nbsp;<select id=\"news_mois\" name=\"mois\">\n";

    $month = 1;
    while ($month < 13) {
        if ($month == date("m")) {
            echo "<option value=\"" . $month . "\" selected=\"selected\">" . $month . "</option>\n";
        } else {
            echo "<option value=\"" . $month . "\">" . $month . "</option>\n";
        }
        $month++;
    }

    echo "</select>&nbsp;<select id=\"news_annee\" name=\"annee\">\n";

    $prevprevprevyear = date("Y") -3;
    $prevprevyear = date("Y") -2;
    $prevyear = date("Y") -1;
    $year = date("Y") ;
    $nextyear = date("Y") + 1;
    $nextnextyear = date("Y") + 2;
    $check = "selected=\"selected\"";

    echo "<option value=\"" . $prevprevprevyear . "\">" . $prevprevprevyear . "</option>\n"
        . "<option value=\"" . $prevprevyear . "\">" . $prevprevyear . "</option>\n"
        . "<option value=\"" . $prevyear . "\">" . $prevyear . "</option>\n"
        . "<option value=\"" . $year . "\" " . $check . ">" . $year . "</option>\n";

    $heure = date("H:i");

    echo "<option value=\"" . $nextyear . "\">" . $nextyear . "</option>\n"
        . "<option value=\"" . $nextnextyear . "\">" . $nextnextyear . "</option>\n"
        . "</select>&nbsp;<b>" . _AT . " :</b>&nbsp;<input type=\"text\" id=\"news_heure\" name=\"heure\" size=\"5\" maxlength=\"5\" value=\"" . $heure . "\" /></td></tr>\n"
        . "<tr><td><b>" . _IMAGE . " :</b> <input type=\"text\" name=\"urlImage\" size=\"42\" /></td></tr>\n"
        . "<tr><td><b>" . _UPLOADIMAGE . " :</b> <input type=\"file\" name=\"upImage\" /></td></tr>\n"
        . "<tr><td align=\"center\"><b>" . _CAT . " :</b> <select id=\"news_cat\" name=\"cat\">\n";

    select_news_cat();

    echo "</select></td></tr><tr><td>&nbsp;</td></tr>\n"
        . "<tr><td align=\"center\"><big><b>" . _TEXT . " :</b></big></td></tr>\n";


    echo "<tr><td align=\"center\"><textarea class=\"editor\" id=\"news_texte\" name=\"texte\" cols=\"70\" rows=\"15\"></textarea></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><big><b>" . _MORE . " :</b></big></td></tr>\n";



    echo "<tr><td align=\"center\"><textarea class=\"editor\" id=\"news_suite\" name=\"suite\" cols=\"70\" rows=\"15\"></textarea></td></tr>\n"
        . "</table><br /><div style=\"text-align: center;\"><input class=\"button\" type=\"submit\" value=\"" . _ADDNEWS . "\" /><a class=\"buttonLink\" href=\"index.php?file=News&amp;page=admin&amp;op=main\">" . _BACK . "</a></div>\n"
        . "</form><br /></div></div>\n";
}

function do_add($titre, $texte, $suite, $cat, $jour, $mois, $annee, $heure, $urlImage, $upImage) {
    global $nuked, $user;


    $table = explode(':', $heure, 2);

    $date = mktime ($table[0], $table[1], 0, $mois, $jour, $annee) ;

    $texte = secu_html(nkHtmlEntityDecode($texte));
    $suite = secu_html(nkHtmlEntityDecode($suite));

    $titre = mysql_real_escape_string(stripslashes($titre));
    $texte = mysql_real_escape_string(stripslashes($texte));
    $suite = mysql_real_escape_string(stripslashes($suite));
    $auteur = $user[2];
    $auteur_id = $user[0];

    //Upload du fichier
    $filename = $_FILES['upImage']['name'];
    if ($filename != "") {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
            $url_image = "upload/News/" . $filename;
            if (! move_uploaded_file($_FILES['upImage']['tmp_name'], $url_image)) {
                printNotification(_UPLOADFILEFAILED, 'error');
                redirect('index.php?file=News&page=admin&op=add', 2);
                return;
            }
            @chmod ($url_image, 0644);
        }
        else {
            printNotification(_NOIMAGEFILE, 'error');
            redirect('index.php?file=News&page=admin&op=add', 2);
            return;
        }
    }
    else {
        $url_image = $urlImage;
    }

    $sql = mysql_query("INSERT INTO " . NEWS_TABLE . " ( `id` , `cat` , `titre` , `coverage` , `auteur` , `auteur_id` , `texte` , `suite` , `date`) VALUES ( '', '" . $cat ."' , '" . $titre . "' , '" . $url_image . "' , '" . $auteur . "' , '" . $auteur_id . "' , '" . $texte . "' , '" . $suite . "' , '" . $date .  "')");

    $id = nkDB_insertId();

    saveUserAction(_ACTIONADDNEWS .': '. $titre .'.');

    printNotification(_NEWSADD, 'success');

    require_once 'Includes/nkSitemap.php';

    if (! nkSitemap_write())
        return;

    setPreview('index.php?file=News&op=suite&news_id='. $id, 'index.php?file=News&page=admin');
}

function edit($news_id) {
    global $nuked, $language;

    $sql = mysql_query("SELECT titre, coverage, texte, suite, date, cat FROM " . NEWS_TABLE . " WHERE id = '" . $news_id . "'");
    list($titre, $coverage, $texte, $suite, $date, $cat) = mysql_fetch_array($sql);

    $sql2 = mysql_query("SELECT nid, titre FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cat . "'");
    list($cid, $categorie) = mysql_fetch_array($sql2);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _EDITTHISNEWS . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n";
        printNotification(_NOTIFIMAGECOVERAGE);
    echo "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=News&amp;page=admin&amp;op=do_edit&amp;news_id=" . $news_id . "\" onsubmit=\"backslash('news_texte');backslash('news_suite');\" enctype=\"multipart/form-data\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
        . "<tr><td align=\"center\"><b>" . _TITLE . " :</b>&nbsp;<input type=\"text\" id=\"news_titre\" name=\"titre\" maxlength=\"100\" size=\"45\" value=\"" . printSecuTags($titre) . "\" /></td></tr>\n"
        . "<tr><td align=\"center\"><b>" . _PUBLISH . "&nbsp;" . _THE ." :</b>&nbsp;<select id=\"news_jour\" name=\"jour\">\n";

    $day = 1;
    while ($day < 32) {
        if ($day == date("d", $date)) {
            echo "<option value=\"" . $day . "\" selected=\"selected\">" . $day . "</option>\n";
        } else {
            echo "<option value=\"" . $day . "\">" . $day . "</option>\n";
        }
        $day++;
    }

    echo "</select>&nbsp;<select id=\"news_mois\" name=\"mois\">\n";

    $month = 1;
    while ($month < 13) {
        if ($month == date("m", $date)) {
            echo "<option value=\"" . $month . "\" selected=\"selected\">" . $month . "</option>\n";
        } else {
            echo "<option value=\"" . $month . "\">" . $month . "</option>\n";
        }
        $month++;
    }

    echo "</select>&nbsp;<select id=\"news_annee\" name=\"annee\">\n";

    $prevprevprevyear = date("Y", $date) -3;
    $prevprevyear = date("Y", $date) -2;
    $prevyear = date("Y", $date) -1;
    $year = date("Y", $date) ;
    $nextyear = date("Y", $date) + 1;
    $nextnextyear = date("Y", $date) + 2;
    $check = "selected=\"selected\"";

    echo "<option value=\"" . $prevprevprevyear . "\">" . $prevprevprevyear . "</option>\n"
        . "<option value=\"" . $prevprevyear . "\">" . $prevprevyear . "</option>\n"
        . "<option value=\"" . $prevyear . "\">" . $prevyear . "</option>\n"
        . "<option value=\"" . $year . "\" " . $check . ">" . $year . "</option>\n";

    $heure = date("H:i", $date);

    echo "<option value=\"" . $nextyear . "\">" . $nextyear . "</option>\n"
        . "<option value=\"" . $nextnextyear . "\">" . $nextnextyear . "</option>\n"
        . "</select>&nbsp;<b>" . _AT . " :</b>&nbsp;<input type=\"text\" id=\"news_heure\" name=\"heure\" size=\"5\" maxlength=\"5\" value=\"" . $heure . "\" /></td></tr>\n"
        . "<tr><td><b>" . _IMAGE . " :</b> <input type=\"text\" name=\"urlImage\" value=\"" . $coverage . "\" size=\"42\" />\n";

        if ($coverage != ""){
            echo "<img src=\"" . $coverage . "\" title=\"" . printSecuTags($titre) . "\" style=\"margin-left:20px; width:300px; height:auto; vertical-align:middle;\" />\n";
        }

        echo "</td></tr>\n"
        . "<tr><td><b>" . _UPLOADIMAGE . " :</b> <input type=\"file\" name=\"upImage\" /></td></tr>\n"
        . "<tr><td align=\"center\"><b>" . _CAT . " :</b> <select id=\"news_cat\" name=\"cat\"><option value=\"" . $cid . "\">" . $categorie . "</option>\n";

    select_news_cat();

    $texte = editPhpCkeditor($texte);

    echo "</select></td></tr><tr><td>&nbsp;</td></tr>\n"
        . "<tr><td align=\"center\"><big><b>" . _TEXT . " :</b></big></td></tr>\n"
        . "<tr><td align=\"center\"><textarea class=\"editor\" id=\"news_texte\" name=\"texte\" cols=\"70\" rows=\"15\">".$texte."</textarea></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><big><b>" . _MORE . " :</b></big></td></tr><tr><td align=\"center\">\n";


    echo "</td></tr><tr><td align=\"center\"><textarea class=\"editor\" id=\"news_suite\" name=\"suite\" cols=\"70\" rows=\"15\">".$suite."</textarea></td></tr>\n"
        . "</table><br /><div style=\"text-align: center;\"><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISNEWS . "\" /><a class=\"buttonLink\" href=\"index.php?file=News&amp;page=admin&amp;op=main\">" . _BACK . "</a></div>\n"
        . "</form><br /></div></div>\n";
}

function do_edit($news_id, $titre, $texte, $suite, $cat, $jour, $mois, $annee, $heure, $urlImage, $upImage) {
    global $nuked, $user;

    $table = explode(':', $heure, 2);
    $date = mktime ($table[0], $table[1], 0, $mois, $jour, $annee) ;

    $texte = secu_html(nkHtmlEntityDecode($texte));
    $titre = mysql_real_escape_string(stripslashes($titre));
    $texte = mysql_real_escape_string(stripslashes($texte));
    $suite = secu_html(nkHtmlEntityDecode($suite));
    $suite = mysql_real_escape_string(stripslashes($suite));

    //Upload du fichier
    $filename = $_FILES['upImage']['name'];
    if ($filename != "") {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
            $url_image = "upload/News/" . $filename;
            if (! move_uploaded_file($_FILES['upImage']['tmp_name'], $url_image)) {
                printNotification(_UPLOADFILEFAILED, 'error');
                redirect('index.php?file=News&page=admin&op=edit&news_id='. $news_id, 2);
                return;
            }
            @chmod ($url_image, 0644);
        }
        else {
            printNotification(_NOIMAGEFILE, 'error');
            redirect('index.php?file=News&page=admin&op=edit&news_id='. $news_id, 2);
            return;
        }
    }
    else {
        $url_image = $urlImage;
    }

    $upd = mysql_query("UPDATE " . NEWS_TABLE . " SET cat = '" . $cat . "', titre = '" . $titre . "', coverage = '" . $url_image . "', texte = '" . $texte . "', suite = '" . $suite . "', date = '" . $date . "' WHERE id = '" . $news_id . "'");

    saveUserAction(_ACTIONMODIFNEWS .': '. $titre .'.');

    printNotification(_NEWSMODIF, 'success');
    setPreview('index.php?file=News&op=suite&news_id='. $news_id, 'index.php?file=News&page=admin');
}

function do_del($news_id) {
    global $nuked, $user;

    $sqls = mysql_query("SELECT titre FROM " . NEWS_TABLE . " WHERE id = '" . $news_id . "'");
    list($titre) = mysql_fetch_array($sqls);
    $titre = mysql_real_escape_string(stripslashes($titre));
    $del = mysql_query("DELETE FROM " . NEWS_TABLE . " WHERE id = '" . $news_id . "'");
    $del_com = mysql_query("DELETE FROM " . COMMENT_TABLE . "  WHERE im_id = '" . $news_id . "' AND module = 'news'");

    saveUserAction(_ACTIONDELNEWS .': '. $titre .'.');

    printNotification(_NEWSDEL, 'success');
    redirect("index.php?file=News&page=admin", 2);
}

function main_cat() {
    global $nuked, $language;

    echo "<script type=\"text/javascript\">\n"
        . "<!--\n"
        . "\n"
        . "function del_cat(titre, id)\n"
        . "{\n"
        . "if (confirm('" . _DELETENEWS . " '+titre+' ! " . _CONFIRM . "'))\n"
        . "{document.location.href = 'index.php?file=News&page=admin&op=del_cat&cid='+id;}\n"
        . "}\n"
        . "\n"
        . "// -->\n"
        . "</script>\n";

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _CATMANAGEMENT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        nkAdminMenu(3);

    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
        . "<tr>\n"
        . "<td style=\"width: 60%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

    $sql = mysql_query("SELECT nid, titre FROM " . NEWS_CAT_TABLE . " ORDER BY titre");
    while (list($cid, $titre) = mysql_fetch_array($sql)) {
        $titre = printSecuTags($titre);

    echo "<tr>\n"
        . "<td style=\"width: 60%;\" align=\"center\">" . $titre . "</td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><a href=\"index.php?file=News&amp;page=admin&amp;op=edit_cat&amp;cid=" . $cid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
        . "<td style=\"width: 20%;\" align=\"center\"><a href=\"javascript:del_cat('" . mysql_real_escape_string(stripslashes($titre)) . "','" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
    }

    echo "</table><br /><div style=\"text-align: center;\"><a class=\"buttonLink\" href=\"index.php?file=News&amp;page=admin&amp;op=add_cat\">" . _ADDCAT . "</a><a class=\"buttonLink\" href=\"index.php?file=News&amp;page=admin\">" . _BACK . "</a></div>\n"
        . "<br /></div></div>\n";
}

function add_cat() {
    global $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _ADDCAT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=News&amp;page=admin&amp;op=send_cat\" enctype=\"multipart/form-data\">\n"
        . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
        . "<tr><td><b>" . _TITLE . " : </b><input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _URLIMG . " : </b><input type=\"text\" name=\"image\" size=\"39\" /></td></tr>\n"
        . "<tr><td><b>" . _UPIMG . " : </b><input type=\"file\" name=\"fichiernom\" /></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _DESCR . " : </b><br /><textarea class=\"editor\" name=\"description\" cols=\"65\" rows=\"10\"></textarea></td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _CREATECAT . "\" /><a class=\"buttonLink\" href=\"index.php?file=News&amp;page=admin&amp;op=main_cat\">" . _BACK . "</a></div>\n"
        . "</form><br /></div></div>\n";
}

function send_cat($titre, $description, $image, $fichiernom) {
    global $nuked, $user;

    $filename = $_FILES['fichiernom']['name'];

    if ($filename != "") {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
            $url_image = "upload/News/" . $filename;
            if (! move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_image)) {
                printNotification('Upload file failed !!!', 'error');
                return;
            }
            @chmod ($url_image, 0644);
        } else {
            printNotification('No image file !!!', 'error');
            redirect("index.php?file=News&page=admin&op=add_cat", 2);
            return;
        }
    } else {
        $url_image = $image;
    }

    $titre = mysql_real_escape_string(stripslashes($titre));
    $description = secu_html(nkHtmlEntityDecode($description));
    $description = mysql_real_escape_string(stripslashes($description));

    $sql = mysql_query("INSERT INTO " . NEWS_CAT_TABLE . " ( `nid` , `titre` , `description` , `image` ) VALUES ( '' , '" . $titre . "' , '" . $description . "' , '" . $url_image . "' )");

    saveUserAction(_ACTIONADDCATNEWS .': '. $titre .'.');

    printNotification(_CATADD, 'success');
    redirect("index.php?file=News&page=admin&op=main_cat", 2);
}

function edit_cat($cid) {
    global $nuked, $language;

    $sql = mysql_query("SELECT titre, description, image FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cid . "'");
    list($titre, $description, $image) = mysql_fetch_array($sql);

    $description = editPhpCkeditor($description);

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _EDITTHISCAT . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=News&amp;page=admin&amp;op=modif_cat\" enctype=\"multipart/form-data\">\n"
        . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
        . "<tr><td><b>" . _TITLE . " : </b><input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre . "\" /></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _URLIMG . " : </b><input type=\"text\" name=\"image\" size=\"39\" value=\"" . $image . "\" />\n";

    if ($image != ""){
    echo "<img src=\"" . $image . "\" title=\"" . $titre . "\" style=\"margin-left:20px; width:50px; height:50px; vertical-align:middle;\" />\n";
    }

    echo "</td></tr>\n"
        . "<tr><td><b>" . _UPIMG . " : </b><input type=\"file\" name=\"fichiernom\" /></td></tr>\n"
        . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _DESCR . " : </b><br /><textarea class=\"editor\" name=\"description\" cols=\"65\" rows=\"10\">" . $description . "</textarea></td></tr>\n"
        . "</table><div style=\"text-align: center;\"><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /><br /><input class=\"button\" type=\"submit\" value=\"" . _MODIFTHISCAT . "\" /><a class=\"buttonLink\" href=\"index.php?file=News&amp;page=admin&amp;op=main_cat\">" . _BACK . "</a></div>\n"
        . "</form><br /></div>\n";

}

function modif_cat($cid, $titre, $description, $image, $fichiernom) {
    global $nuked, $user;

    $filename = $_FILES['fichiernom']['name'];

    if ($filename != "") {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext) || preg_match("`gif`i", $ext) || preg_match("`png`i", $ext))) {
            $url_image = "upload/News/" . $filename;
            if (! move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_image)) {
                printNotification('Upload file failed !!!', 'error');
                return;
            }
            @chmod ($url_image, 0644);
        } else {
            printNotification('No image file !!!', 'error');
            redirect("index.php?file=News&page=admin&op=edit_cat&cid=" . $cid, 2);
            return;
        }
    } else {
        $url_image = $image;
    }

    $titre = mysql_real_escape_string(stripslashes($titre));
    $description = secu_html(nkHtmlEntityDecode($description));
    $description = mysql_real_escape_string(stripslashes($description));

    $sql = mysql_query("UPDATE " . NEWS_CAT_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "', image = '" . $url_image . "' WHERE nid = '" . $cid . "'");

    saveUserAction(_ACTIONEDITCATNEWS .': '. $titre .'.');

    printNotification(_CATMODIF, 'success');
    redirect("index.php?file=News&page=admin&op=main_cat", 2);
}

function select_news_cat() {
    global $nuked;

    $sql = mysql_query("SELECT nid, titre FROM " . NEWS_CAT_TABLE);
    while (list($cid, $titre) = mysql_fetch_array($sql)) {
        $titre = printSecuTags($titre);
        echo "<option value=\"" . $cid . "\">" . $titre . "</option>\n";
    }
}

function del_cat($cid) {
    global $nuked, $user;

    $sqlq = mysql_query("SELECT titre FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cid . "'");
    list($titre) = mysql_fetch_array($sqlq);
    $titre = mysql_real_escape_string(stripslashes($titre));
    $sql = mysql_query("DELETE FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cid . "'");

    saveUserAction(_ACTIONDELCATNEWS .': '. $titre .'.');

    printNotification(_CATDEL, 'success');
    redirect("index.php?file=News&page=admin&op=main_cat", 2);
}

function main_pref() {
    global $nuked, $language;

    echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _PREFS . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
        . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
        . "</div></div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        nkAdminMenu(4);

    echo "<form method=\"post\" action=\"index.php?file=News&amp;page=admin&amp;op=change_pref\">\n"
        . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
        . "<tr><td>" . _NUMBERNEWS . " :</td><td> <input type=\"text\" name=\"max_news\" size=\"2\" value=\"" . $nuked['max_news'] . "\" /></td></tr>\n"
        . "<tr><td>" . _NUMBERARCHIVE . " :</td><td> <input type=\"text\" name=\"max_archives\" size=\"2\" value=\"" . $nuked['max_archives'] . "\" /></td></tr>\n"
        . "</table><div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _SEND . "\" /><a class=\"buttonLink\" href=\"index.php?file=News&amp;page=admin\">" . _BACK . "</a></div>\n"
        . "</form><br /></div></div>\n";
}

function change_pref($max_news, $max_archives) {
    global $nuked, $user;

    $upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_news . "' WHERE name = 'max_news'");
    $upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_archives . "' WHERE name = 'max_archives'");

    saveUserAction(_ACTIONPREFNEWS .'.');

    printNotification(_PREFUPDATED, 'success');
    redirect("index.php?file=News&page=admin", 2);
}

function nkAdminMenu($tab = 1) {
    global $language, $user, $nuked;

    $class = ' class="nkClassActive" ';
?>
    <div class= "nkAdminMenu">
        <ul class="shortcut-buttons-set" id="1">
            <li <?php echo ($tab == 1 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=News&amp;page=admin">
                    <img src="modules/Admin/images/icons/speedometer.png" alt="icon" />
                    <span><?php echo _NAVNEWS; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 2 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=News&amp;page=admin&amp;op=add">
                    <img src="modules/Admin/images/icons/add_page.png" alt="icon" />
                    <span><?php echo _ADDNEWS; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 3 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=News&amp;page=admin&amp;op=main_cat">
                    <img src="modules/Admin/images/icons/folder_full.png" alt="icon" />
                    <span><?php echo _CATMANAGEMENT; ?></span>
                </a>
            </li>
            <li <?php echo ($tab == 4 ? $class : ''); ?>>
                <a class="shortcut-button" href="index.php?file=News&amp;page=admin&amp;op=main_pref">
                    <img src="modules/Admin/images/icons/process.png" alt="icon" />
                    <span><?php echo _PREFS; ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
<?php
}


switch ($_REQUEST['op']) {
    case "edit":
        edit($_REQUEST['news_id']);
        break;

    case "add":
        add();
        break;

    case "do_del":
        do_del($_REQUEST['news_id']);
        break;

    case "do_add":
        do_add($_REQUEST['titre'], $_REQUEST['texte'], $_REQUEST['suite'], $_REQUEST['cat'], $_REQUEST['jour'], $_REQUEST['mois'], $_REQUEST['annee'], $_REQUEST['heure'], $_REQUEST['urlImage'], $_REQUEST['upImage']);
        break;

    case "do_edit":
        do_edit($_REQUEST['news_id'], $_REQUEST['titre'], $_REQUEST['texte'], $_REQUEST['suite'], $_REQUEST['cat'], $_REQUEST['jour'], $_REQUEST['mois'], $_REQUEST['annee'], $_REQUEST['heure'], $_REQUEST['urlImage'], $_REQUEST['upImage']);
        break;

    case "main":
        main();
        break;

    case "send_cat":
        send_cat($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['image'], $_FILES['fichiernom']);
        break;

    case "add_cat":
        add_cat();
        break;

    case "main_cat":
        main_cat();
        break;

    case "edit_cat":
        edit_cat($_REQUEST['cid']);
        break;

    case "modif_cat":
        modif_cat($_REQUEST['cid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['image'], $_FILES['fichiernom']);
        break;

    case "del_cat":
        del_cat($_REQUEST['cid']);
        break;

    case "main_pref":
        main_pref();
        break;

    case "change_pref":
        change_pref($_REQUEST['max_news'], $_REQUEST['max_archives']);
        break;

    default:
        main();
        break;
}

?>