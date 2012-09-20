<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

translate("modules/News/lang/" . $language . ".lang.php");

include 'modules/Admin/design.php';

$visiteur = $user ? $user[1] : 0;

$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1) {
	function main() {
		global $nuked, $language;

		$nb_news = 30;

		$sql = mysql_query("SELECT id FROM " . NEWS_TABLE);
		$count = mysql_num_rows($sql);

		if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
		$start = $_REQUEST['p'] * $nb_news - $nb_news;

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
		   . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _NAVNEWS . "<b> | "
		   . "<a href=\"index.php?file=News&amp;page=admin&amp;op=add\">" . _ADDNEWS . "</a> | "
		   . "<a href=\"index.php?file=News&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
		   . "<a href=\"index.php?file=News&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n";

		if ($_REQUEST['orderby'] == "date") {
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

		if ($_REQUEST['orderby'] == "date" || !$_REQUEST['orderby']) {
			echo "<b>" . _DATE . "</b> | ";
		} else {
			echo "<a href=\"index.php?file=News&amp;page=admin&amp;orderby=date\">" . _DATE . "</a> | ";
		}

		if ($_REQUEST['orderby'] == "title") {
			echo "<b>" . _TITLE . "</b> | ";
		} else {
			echo "<a href=\"index.php?file=News&amp;page=admin&amp;orderby=title\">" . _TITLE . "</a> | ";
		}

		if ($_REQUEST['orderby'] == "author") {
			echo "<b>" . _AUTHOR . "</b> | ";
		} else {
			echo "<a href=\"index.php?file=News&amp;page=admin&amp;orderby=author\">" . _AUTHOR . "</a> | ";
		}

		if ($_REQUEST['orderby'] == "cat") {
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

		echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
	}

	function add() {
		global $nuked, $language;

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINNEWS . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=News&amp;page=admin\">" . _NAVNEWS . "</a> | "
		   . "</b>" . _ADDNEWS . "<b> | "
		   . "<a href=\"index.php?file=News&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
		   . "<a href=\"index.php?file=News&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
		   . "<form method=\"post\" action=\"index.php?file=News&amp;page=admin&amp;op=do_add\" onsubmit=\"backslash('news_texte');backslash('news_suite');\">\n"
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

		$prevprevprevyear = date(Y) -3;
		$prevprevyear = date(Y) -2;
		$prevyear = date(Y) -1;
		$year = date(Y) ;
		$nextyear = date(Y) + 1;
		$nextnextyear = date(Y) + 2;
		$check = "selected=\"selected\"";

		echo "<option value=\"" . $prevprevprevyear . "\">" . $prevprevprevyear . "</option>\n"
		   . "<option value=\"" . $prevprevyear . "\">" . $prevprevyear . "</option>\n"
		   . "<option value=\"" . $prevyear . "\">" . $prevyear . "</option>\n"
		   . "<option value=\"" . $year . "\" " . $check . ">" . $year . "</option>\n";

		$heure = date("H:i");

		echo "<option value=\"" . $nextyear . "\">" . $nextyear . "</option>\n"
		   . "<option value=\"" . $nextnextyear . "\">" . $nextnextyear . "</option>\n"
		   . "</select>&nbsp;<b>" . _AT . " :</b>&nbsp;<input type=\"text\" id=\"news_heure\" name=\"heure\" size=\"5\" maxlength=\"5\" value=\"" . $heure . "\" /></td></tr>\n"
		   . "<tr><td align=\"center\"><b>" . _CAT . " :</b> <select id=\"news_cat\" name=\"cat\">\n";

		select_news_cat();

		echo "</select></td></tr><tr><td>&nbsp;</td></tr>\n"
		   . "<tr><td align=\"center\"><big><b>" . _TEXT . " :</b></big></td></tr>\n";


		echo "<tr><td align=\"center\"><textarea class=\"editor\" id=\"news_texte\" name=\"texte\" cols=\"70\" rows=\"15\"></textarea></td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><big><b>" . _MORE . " :</b></big></td></tr>\n";



		echo "<tr><td align=\"center\"><textarea class=\"editor\" id=\"news_suite\" name=\"suite\" cols=\"70\" rows=\"15\"></textarea></td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . _ADDNEWS . "\" />\n"
		   . "</td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\">[ <a href=\"index.php?file=News&amp;page=admin&amp;op=main\"><b>" . _BACK . "</b></a> ]</td></tr></table></form><br /></div></div>\n";
	}

	function do_add($titre, $texte, $suite, $cat, $jour, $mois, $annee, $heure) {
		global $nuked, $user;


		$table = explode(':', $heure, 2);

		$date = mktime ($table[0], $table[1], 0, $mois, $jour, $annee) ;

		$texte = html_entity_decode($texte);
		$suite = html_entity_decode($suite);

		$titre = mysql_real_escape_string(stripslashes($titre));
		$texte = mysql_real_escape_string(stripslashes($texte));
		$suite = mysql_real_escape_string(stripslashes($suite));
		$auteur = $user[2];
		$auteur_id = $user[0];

		$sql = mysql_query("INSERT INTO " . NEWS_TABLE . " ( `id` , `cat` , `titre` , `auteur` , `auteur_id` , `texte` , `suite` , `date`) VALUES ( '', '" . $cat ."' , '" . $titre . "' , '" . $auteur . "' , '" . $auteur_id . "' , '" . $texte . "' , '" . $suite . "' , '" . $date .  "')");
		// Action
		$texteaction = "". _ACTIONADDNEWS .": ".$titre.".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _NEWSADD . "\n"
		   . "</div>\n"
		   . "</div>\n";
		$sqls = mysql_query("SELECT id FROM " . NEWS_TABLE . " WHERE titre = '" . $titre . "' AND date = '".$date."'");
		list($news_id) = mysql_fetch_array($sqls);
		echo "<script>\n"
		   . "setTimeout('screen()','3000');\n"
		   . "function screen() { \n"
		   . "screenon('index.php?file=News&op=suite&news_id=".$news_id."', 'index.php?file=News&page=admin');\n"
		   . "}\n"
		   . "</script>\n";
	}

	function edit($news_id) {
		global $nuked, $language;

		$sql = mysql_query("SELECT titre, texte, suite, date, cat FROM " . NEWS_TABLE . " WHERE id = '" . $news_id . "'");
		list($titre, $texte, $suite, $date, $cat) = mysql_fetch_array($sql);

		$sql2 = mysql_query("SELECT nid, titre FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cat . "'");
		list($cid, $categorie) = mysql_fetch_array($sql2);

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINNEWS . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=News&amp;page=admin&amp;op=do_edit&amp;news_id=" . $news_id . "\" onsubmit=\"backslash('news_texte');backslash('news_suite');\">\n"
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
		   . "<tr><td align=\"center\"><b>" . _CAT . " :</b> <select id=\"news_cat\" name=\"cat\"><option value=\"" . $cid . "\">" . $categorie . "</option>\n";

		select_news_cat();

		echo "</select></td></tr><tr><td>&nbsp;</td></tr>\n"
		   . "<tr><td align=\"center\"><big><b>" . _TEXT . " :</b></big></td></tr>\n"
		   . "<tr><td align=\"center\"><textarea class=\"editor\" id=\"news_texte\" name=\"texte\" cols=\"70\" rows=\"15\">".$texte."</textarea></td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><big><b>" . _MORE . " :</b></big></td></tr><tr><td align=\"center\">\n";


		echo "</td></tr><tr><td align=\"center\"><textarea class=\"editor\" id=\"news_suite\" name=\"suite\" cols=\"70\" rows=\"15\">".$suite."</textarea></td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\"><input type=\"submit\" value=\"" . _MODIFTHISNEWS . "\" />\n"
		   . "</td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr><tr><td align=\"center\">[ <a href=\"index.php?file=News&amp;page=admin&amp;op=main\"><b>" . _BACK . "</b></a> ]</td></tr></table></form><br /></div></div>\n";
	}

	function do_edit($news_id, $titre, $texte, $suite, $cat, $jour, $mois, $annee, $heure) {
		global $nuked, $user;

		$table = explode(':', $heure, 2);
		$date = mktime ($table[0], $table[1], 0, $mois, $jour, $annee) ;

		$texte = html_entity_decode($texte);
		$titre = mysql_real_escape_string(stripslashes($titre));
		$texte = mysql_real_escape_string(stripslashes($texte));
		$suite = html_entity_decode($suite);
		$suite = mysql_real_escape_string(stripslashes($suite));

		$upd = mysql_query("UPDATE " . NEWS_TABLE . " SET cat = '" . $cat . "', titre = '" . $titre . "', texte = '" . $texte . "', suite = '" . $suite . "', date = '" . $date . "' WHERE id = '" . $news_id . "'");
		// Action
		$texteaction = "". _ACTIONMODIFNEWS .": ".$titre.".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _NEWSMODIF . "\n"
		   . "</div>\n"
		   . "</div>\n";
		echo "<script>\n"
		   . "setTimeout('screen()','3000');\n"
		   . "function screen() { \n"
		   . "screenon('index.php?file=News&op=suite&news_id=".$news_id."', 'index.php?file=News&page=admin');\n"
		   . "}\n"
		   . "</script>\n";
	}

	function do_del($news_id) {
		global $nuked, $user;

		$sqls = mysql_query("SELECT titre FROM " . NEWS_TABLE . " WHERE id = '" . $news_id . "'");
		list($titre) = mysql_fetch_array($sqls);
		$titre = mysql_real_escape_string(stripslashes($titre));
		$del = mysql_query("DELETE FROM " . NEWS_TABLE . " WHERE id = '" . $news_id . "'");
		$del_com = mysql_query("DELETE FROM " . COMMENT_TABLE . "  WHERE im_id = '" . $news_id . "' AND module = 'news'");
		// Action
		$texteaction = "". _ACTIONDELNEWS .": ".$titre.".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _NEWSDEL . "\n"
		   . "</div>\n"
		   . "</div>\n";
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
		   . "<div class=\"content-box-header\"><h3>" . _ADMINNEWS . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=News&amp;page=admin\">" . _NAVNEWS . "</a> | "
		   . "<a href=\"index.php?file=News&amp;page=admin&amp;op=add\">" . _ADDNEWS . "</a> | "
		   . "</b>" . _CATMANAGEMENT . "<b> | "
		   . "<a href=\"index.php?file=News&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
		   . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"70%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
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

		echo "</table><br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=News&amp;page=admin&amp;op=add_cat\"><b>" . _ADDCAT . "</b></a> ]</div>\n"
		   . "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=News&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
	}


	function add_cat() {
		global $language;

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINNEWS . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=News&amp;page=admin&amp;op=send_cat\" enctype=\"multipart/form-data\">\n"
		   . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
		   . "<tr><td><b>" . _TITLE . " : </b><input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _URLIMG . " : </b><input type=\"text\" name=\"image\" size=\"39\" /></td></tr>\n"
		   . "<tr><td><b>" . _UPIMG . " : </b><input type=\"file\" name=\"fichiernom\" /></td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _DESCR . " : </b><br /><textarea class=\"editor\" name=\"description\" cols=\"65\" rows=\"10\"></textarea></td></tr>\n"
		   . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _CREATECAT . "\" /></div>\n"
		   . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=News&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
	}

	function send_cat($titre, $description, $image, $fichiernom) {
		global $nuked, $user;
		
		$filename = $_FILES['fichiernom']['name'];

		if ($filename != "") {
			$ext = pathinfo($filename, PATHINFO_EXTENSION);

			if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
				$url_image = "upload/News/" . $filename;
				move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_image) or die ("<br /><br /><div style=\"text-align: center;\"><b>Upload file failed !!!</b></div><br /><br />");
				@chmod ($url_image, 0644);
			} else {
				echo "<div class=\"notification error png_bg\">\n"
				   . "<div>\n"
				   . "No image file !"
				   . "</div>\n"
				   . "</div>\n";
				redirect("index.php?file=News&page=admin&op=add_cat", 2);
				adminfoot();
				footer();
				die;
			}
		} else {
			$url_image = $image;
		}

		$titre = mysql_real_escape_string(stripslashes($titre));
		$description = html_entity_decode($description);
		$description = mysql_real_escape_string(stripslashes($description));

		$sql = mysql_query("INSERT INTO " . NEWS_CAT_TABLE . " ( `nid` , `titre` , `description` , `image` ) VALUES ( '' , '" . $titre . "' , '" . $description . "' , '" . $url_image . "' )");
		// Action
		$texteaction = "". _ACTIONADDCATNEWS .": ".$titre.".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _CATADD . "\n"
		   . "</div>\n"
		   . "</div>\n";
		redirect("index.php?file=News&page=admin&op=main_cat", 2);
	}

	function edit_cat($cid) {
		global $nuked, $language;

		$sql = mysql_query("SELECT titre, description, image FROM " . NEWS_CAT_TABLE . " WHERE nid = '" . $cid . "'");
		list($titre, $description, $image) = mysql_fetch_array($sql);

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINNEWS . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=News&amp;page=admin&amp;op=modif_cat\" enctype=\"multipart/form-data\">\n"
		   . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
		   . "<tr><td><b>" . _TITLE . " : </b><input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre . "\" /></td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _URLIMG . " : </b><input type=\"text\" name=\"image\" size=\"39\" value=\"" . $image . "\" /></td></tr>\n"
		   . "<tr><td><b>" . _UPIMG . " : </b><input type=\"file\" name=\"fichiernom\" /></td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr><tr><td><b>" . _DESCR . " : </b><br /><textarea class=\"editor\" name=\"description\" cols=\"65\" rows=\"10\">" . $description . "</textarea></td></tr>\n"
		   . "</table><div style=\"text-align: center;\"><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /><br /><input type=\"submit\" value=\"" . _MODIFTHISCAT . "\" /></div>\n"
		   . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=News&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div>\n";

	}

	function modif_cat($cid, $titre, $description, $image, $fichiernom) {
		global $nuked, $user;

		$filename = $_FILES['fichiernom']['name'];

		if ($filename != "") {
			$ext = pathinfo($filename, PATHINFO_EXTENSION);

			if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext) || preg_match("`gif`i", $ext) || preg_match("`png`i", $ext))) {
				$url_image = "upload/News/" . $filename;
				move_uploaded_file($_FILES['fichiernom']['tmp_name'], $url_image) or die ("<br /><br /><div style=\"text-align: center;\"><b>Upload file failed !!!</b></div><br /><br />");
				@chmod ($url_image, 0644);
			} else {
				echo "<div class=\"notification error png_bg\">\n"
				   . "<div>\n"
				   . "No image file !"
				   . "</div>\n"
				   . "</div>\n";
				redirect("index.php?file=News&page=admin&op=edit_cat&cid=" . $cid, 2);
				adminfoot();
				footer();
				die;
			}
		} else {
			$url_image = $image;
		}

		$titre = mysql_real_escape_string(stripslashes($titre));
		$description = html_entity_decode($description);
		$description = mysql_real_escape_string(stripslashes($description));

		$sql = mysql_query("UPDATE " . NEWS_CAT_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "', image = '" . $url_image . "' WHERE nid = '" . $cid . "'");
		// Action
		$texteaction = "". _ACTIONEDITCATNEWS .": ".$titre.".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _CATMODIF . "\n"
		   . "</div>\n"
		   . "</div>\n";
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
		// Action
		$texteaction = "". _ACTIONDELCATNEWS .": ".$titre.".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _CATDEL . "\n"
		   . "</div>\n"
		   . "</div>\n";
		redirect("index.php?file=News&page=admin&op=main_cat", 2);
	}

	function main_pref() {
		global $nuked, $language;

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINNEWS . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/News.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=News&amp;page=admin\">" . _NAVNEWS . "</a> | "
		   . "<a href=\"index.php?file=News&amp;page=admin&amp;op=add\">" . _ADDNEWS . "</a> | "
		   . "<a href=\"index.php?file=News&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a> | "
		   . "</b>" . _PREFS . "</div><br />\n"
		   . "<form method=\"post\" action=\"index.php?file=News&amp;page=admin&amp;op=change_pref\">\n"
		   . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
		   . "<tr><td align=\"center\"><big>" . _PREFS . "</big></td></tr>\n"
		   . "<tr><td>" . _NUMBERNEWS . " :</td><td> <input type=\"text\" name=\"max_news\" size=\"2\" value=\"" . $nuked['max_news'] . "\" /></td></tr>\n"
		   . "<tr><td>" . _NUMBERARCHIVE . " :</td><td> <input type=\"text\" name=\"max_archives\" size=\"2\" value=\"" . $nuked['max_archives'] . "\" /></td></tr>\n"
		   . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
		   . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=News&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
	}

	function change_pref($max_news, $max_archives) {
		global $nuked, $user;

		$upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_news . "' WHERE name = 'max_news'");
		$upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_archives . "' WHERE name = 'max_archives'");
		// Action
		$texteaction = "". _ACTIONPREFNEWS .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _PREFUPDATED . "\n"
		   . "</div>\n"
		   . "</div>\n";
		redirect("index.php?file=News&page=admin", 2);
	}

	switch ($_REQUEST['op']) {
		case "edit":
			admintop();
			edit($_REQUEST['news_id']);
			adminfoot();
			break;

		case "add":
			admintop();
			add();
			adminfoot();
			break;

		case "do_del":
			admintop();
			do_del($_REQUEST['news_id']);
			adminfoot();
			break;

		case "do_add":
			admintop();
			do_add($_REQUEST['titre'], $_REQUEST['texte'], $_REQUEST['suite'], $_REQUEST['cat'], $_REQUEST['jour'], $_REQUEST['mois'], $_REQUEST['annee'], $_REQUEST['heure']);
			UpdateSitmap();
			adminfoot();
			break;

		case "do_edit":
			admintop();
			do_edit($_REQUEST['news_id'], $_REQUEST['titre'], $_REQUEST['texte'], $_REQUEST['suite'], $_REQUEST['cat'], $_REQUEST['jour'], $_REQUEST['mois'], $_REQUEST['annee'], $_REQUEST['heure']);
			adminfoot();
			break;

		case "main":
			admintop();
			main();
			adminfoot();
			break;

		case "send_cat":
			admintop();
			send_cat($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['image'], $_REQUEST['fichiernom']);
			adminfoot();
			break;

		case "add_cat":
			admintop();
			add_cat();
			adminfoot();
			break;

		case "main_cat":
			admintop();
			main_cat();
			adminfoot();
			break;

		case "edit_cat":
			admintop();
			edit_cat($_REQUEST['cid']);
			adminfoot();
			break;

		case "modif_cat":
			admintop();
			modif_cat($_REQUEST['cid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['image'], $_REQUEST['fichiernom']);
			adminfoot();
			break;

		case "del_cat":
			admintop();
			del_cat($_REQUEST['cid']);
			adminfoot();
			break;

		case "main_pref":
			admintop();
			main_pref();
			adminfoot();
			break;

		case "change_pref":
			admintop();
			change_pref($_REQUEST['max_news'], $_REQUEST['max_archives']);
			adminfoot();
			break;

		default:
			admintop();
			main();
			adminfoot();
			break;
	}
} else if ($level_admin == -1) {
	echo "<div class=\"notification error png_bg\">\n"
	   . "<div>\n"
	   . "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	   . "</div>\n"
	   . "</div>\n";
} else if ($visiteur > 1) {
	echo "<div class=\"notification error png_bg\">\n"
	   . "<div>\n"
	   . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	   . "</div>\n"
	   . "</div>\n";
} else {
	echo "<div class=\"notification error png_bg\">\n"
	   . "<div>\n"
	   . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	   . "</div>\n"
	   . "</div>\n";
}

?>