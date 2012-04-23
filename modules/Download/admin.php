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

include 'modules/Admin/design.php';
admintop();

$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1) {
	function add_file() {
		global $nuked, $language;

		$upload_max_filesize = @ini_get('upload_max_filesize');
		$file_uploads = @ini_get('file_uploads');

		if ($file_uploads == 1 && $upload_max_filesize != "") {
			list($maxfilesize) = explode('M', $upload_max_filesize);
			$upload_status = "(" . _MAX . " : " . $maxfilesize . "&nbsp;" . _MO . ")";
		} else {
			$upload_status = "";
		}

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINDOWN . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Download&amp;page=admin\">" . _DOWNLOAD . "</a> | "
		   . "</b>" . _ADDFILE . "<b> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_broken\">" . _BROKENLINKS . "</a> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
		   . "<form method=\"post\" action=\"index.php?file=Download&amp;page=admin&amp;op=send_file\" enctype=\"multipart/form-data\" onsubmit=\"backslash('dl_texte');\">\n"
		   . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
		   . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" /></td></tr>\n"
		   . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\">\n";

		select_cat();

		echo "</select></td></tr>\n"
		   . "<tr><td align=\"left\" colspan=\"2\"><b>" . _AUTOR . " :</b> <input type=\"text\" name=\"autor\" size=\"40\" /></td></tr>\n"
		   . "<tr><td align=\"left\"><b>" . _SITE . " :</b> <input type=\"text\" name=\"site\" size=\"55\" value=\"http://\" /></td></tr>\n"
		   . "<tr><td align=\"center\">\n";

		echo"</td></tr><tr><td align=\"center\">\n";


		echo "</td></tr><tr><td><b>" . _DESCR . " : </b><br />\n"
		   . "<textarea class=\"editor\" id=\"dl_texte\" name=\"description\" rows=\"10\" cols=\"65\" onselect=\"storeCaret('dl_texte');\" onclick=\"storeCaret('dl_texte');\" onkeyup=\"storeCaret('dl_texte');\"></textarea></td></tr>\n"
		   . "<tr><td align=\"left\"><b>" . _SIZE . " :  </b><input type=\"text\" name=\"size\" size=\"5\" /> (" . _KO . ")"
		   . "&nbsp;<b>" . _LEVEL . " :</b> <select name=\"level\">\n"
		   . "<option>0</option>\n"
		   . "<option>1</option>\n"
		   . "<option>2</option>\n"
		   . "<option>3</option>\n"
		   . "<option>4</option>\n"
		   . "<option>5</option>\n"
		   . "<option>6</option>\n"
		   . "<option>7</option>\n"
		   . "<option>8</option>\n"
		   . "<option>9</option></select></td></tr>\n"
		   . "<tr><td><b>" . _COMPATIBLE . " :</b> <input type=\"text\" name=\"comp\" size=\"45\" /></td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr>\n"
		   . "<tr><td><b>" . _URLFILE . " :</b> <input type=\"text\" name=\"url\" size=\"55\" value=\"http://\" /></td></tr>\n"
		   . "<tr><td><b>" . _URL2 . " :</b> <input type=\"text\" name=\"url2\" size=\"55\" value=\"http://\" /></td></tr>\n"
		   . "<tr><td><b>" . _URL3 . " :</b> <input type=\"text\" name=\"url3\" size=\"55\" value=\"http://\" /></td></tr>\n"
		   . "<tr><td><b>" . _UPFILE . " :</b>&nbsp;" . $upload_status . " <br /><input type=\"file\" name=\"copy\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_file\" value=\"1\" /> " . _REPLACE . "</td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr>\n"
		   . "<tr><td><b>" . _CAPTURE . " :</b> <input type=\"text\" name=\"screen\" size=\"42\" value=\"http://\" /></td></tr>\n"
		   . "<tr><td><b>" . _UPIMG . " :</b> <br /><input type=\"file\" name=\"screen2\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" /> " . _REPLACE . "</td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr>\n"
		   . "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _ADDTHISFILE . "\" /></td></tr>\n"
		   . "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Download&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
	}

	function send_file($date, $size, $titre, $description, $cat, $url, $url2, $url3, $level, $autor, $site, $comp, $screen, $screen2, $copy, $ecrase_file, $ecrase_screen) {
		global $nuked;

		$description = html_entity_decode($description);
		$description = mysql_real_escape_string(stripslashes($description));
		$titre = mysql_real_escape_string(stripslashes($titre));
		$autor = mysql_real_escape_string(stripslashes($autor));
		$comp = mysql_real_escape_string(stripslashes($comp));

		$date = time();
		$taille = str_replace(",", ".", $size);

		if ($site == "http://") $site = "";
		if ($url == "http://") $url = "";
		if ($url2 == "http://") $url2 = "";
		if ($url3 == "http://") $url3 = "";
		if ($screen == "http://") $screen = "";

		if ($site != "" && !preg_match("`http://`i", $site)) {
			$site = "http://" . $site;
		}

		$racine_up = "upload/Download/";
		$racine_down = "";

		if ($_FILES['copy']['name'] != "") {
			$filename = $_FILES['copy']['name'];
			$filesize = $_FILES['copy']['size'];
			$taille = $filesize / 1024;
			$taille = (round($taille * 100)) / 100;
			$url_file = $racine_up . $filename;

			if (!is_file($url_file) || $ecrase_file == 1) {
				if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && $filename != ".htaccess") {
					  move_uploaded_file($_FILES['copy']['tmp_name'], $url_file) or die ("Upload file failed !!!");
					@chmod ($url_file, 0644);
				} else {
					echo "<br /><br /><div style=\"text-align: center;\">Unauthorized file !!!</div><br /><br />";
					redirect("index.php?file=Download&page=admin&op=add_file", 2);
					adminfoot();
					footer();
					die;
				}
			} else {
				$deja_file = 1;
			}

			$url_full = $racine_down . $url_file;
			$url_full = $url_file;

			if ($url == "") $url = $url_full;
			else if ($url2 == "") $url2 = $url_full;
			else if ($url3 == "") $url3 = $url_full;
			else $url = $url_full;
		}

		if ($_FILES['screen2']['name'] != "") {
			$screenname = $_FILES['screen2']['name'];
			$ext = pathinfo($_FILES['screen2']['name'], PATHINFO_EXTENSION);
			$filename2 = str_replace($ext, "", $screenname);
			$url_screen = $racine_up . $filename2 . $ext;

			if (!is_file($url_screen) || $ecrase_screen == 1) {
				if ($ext == "jpg" || $ext == "jpeg" || $ext == "JPG" || $ext == "JPEG" || $ext == "gif" || $ext == "GIF" || $ext == "png" || $ext == "PNG") {
					move_uploaded_file($_FILES['screen2']['tmp_name'], $url_screen) or die ("Upload screen failed !!!");
					@chmod ($url_screen, 0644);
				} else {
					echo "<div class=\"notification error png_bg\">\n"
					   . "<div>\n"
					   . "<div style=\"text-align: center;\">No image file !!!</div><br />\n"
					   . "</div></div>\n";
					redirect("index.php?file=Download&page=admin&op=add_file", 2);
					adminfoot();
					footer();
					die;
				}
			} else {
				$deja_screen = 1;
			}

			$url_full_screen = $racine_down . $url_screen;
			$screen = $url_full_screen;
		}

		if ($deja_file == 1 || $deja_screen == 1) {
			echo "<div class=\"notification error png_bg\">\n"
			   . "<div>\n";


			if ($deja_file == 1) echo _DEJAFILE;
			if ($deja_screen == 1) echo "&nbsp;" . _DEJASCREEN;

			echo "<br />" . _REPLACEIT . "<br /><br /><a href=\"javascript:history.back();\"><b>" . _BACK . "</b></a></div><br /><br />";
			echo "</div>\n"
			   . "</div>\n";
		} else if ($url != "" && $titre != "") {
			$sql = mysql_query("INSERT INTO " . DOWNLOAD_TABLE . " ( `date` , `taille` , `titre` , `description` , `type` , `url` , `url2`  , `url3` , `level`, `autor` , `url_autor`  , `comp` , `screen` )  VALUES ( '" . $date . "' , '" . $taille . "' , '" . $titre . "' , '" . $description . "' , '" . $cat . "' , '" . $url . "' , '" . $url2 . "' , '" . $url3 . "' , '" . $level ."' , '" . $autor . "' , '" . $site . "' , '" . $comp . "' , '" . $screen . "' )");

			// Action
			$texteaction = "". _ACTIONADDDL .": ".$titre."";
			$acdate = time();
			$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
			//Fin action
			echo "<div class=\"notification success png_bg\">\n"
			   . "<div>\n"
			   . _FILEADD . "\n"
			   . "</div>\n"
			   . "</div>\n";
			$sql = mysql_query("SELECT id FROM " . DOWNLOAD_TABLE . " WHERE date = '" . $date . "' AND titre = '" . $titre . "'");
			list($id) = mysql_fetch_array($sql);
			echo "<script>\n"
			   . "setTimeout('screen()','3000');\n"
			   . "function screen() { \n"
			   . "screenon('index.php?file=Download&op=description&dl_id=".$id."', 'index.php?file=Download&page=admin');\n"
			   . "}\n"
			   . "</script>\n";
		} else {
			echo "<div class=\"notification error png_bg\">\n"
			   . "<div>\n"
			   . "<div style=\"text-align: center;\">" . _URLORTITLEFAILDED . "<br /><br /><a href=\"javascript:history.back();\"><b>" . _BACK . "</b></a></div>"
			   . "</div>\n"
			   . "</div>\n";
		}
	}

	function del_file($did) {
		global $nuked, $user;

		$sql = mysql_query("SELECT titre FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $did . "'");
		list($titre) = mysql_fetch_array($sql);
		$titre = mysql_real_escape_string($titre);
		$sql = mysql_query("DELETE FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $did . "'");
		$del_com = mysql_query("DELETE FROM " . COMMENT_TABLE . " WHERE im_id = '" . $did . "' AND module = 'Download'");
		$del_vote = mysql_query("DELETE FROM " . VOTE_TABLE . " WHERE vid = '" . $did . "' AND module = 'Download'");
		// Action
		$texteaction = "". _ACTIONDELDL .": ".$titre."";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _FILEDEL . "\n"
		   . "</div>\n"
		   . "</div>\n";
		redirect("index.php?file=Download&page=admin", 2);
	}

	function edit_file($did) {
		global $nuked, $language;

		$sql = mysql_query("SELECT titre, description, type, taille, url, url2, url3, count, level, screen, autor, url_autor, comp  FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $did . "'");
		list($titre, $description, $cat, $taille, $url, $url2, $url3, $count, $level, $screen, $autor, $url_autor, $comp) = mysql_fetch_array($sql);

		$upload_max_filesize = @ini_get('upload_max_filesize');
		$file_uploads = @ini_get('file_uploads');

		if ($file_uploads == 1 && $upload_max_filesize != "") {
			list($maxfilesize) = explode('M', $upload_max_filesize);
			$upload_status = "(" . _MAX . " : " . $maxfilesize . "&nbsp;" . _MO . ")";
		} else {
			$upload_status = "";
		}

		if ($cat == 0 || !$cat) {
			$cid = 0;
			$cat_name = _NONE;
		} else {
			$cid = $cat;
			$sql2 = mysql_query("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cat . "'");
			list($cat_name) = mysql_fetch_array($sql2);
			$cat_name = printSecuTags($cat_name);
		}

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINDOWN . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Download&amp;page=admin&amp;op=modif_file\" enctype=\"multipart/form-data\" onsubmit=\"backslash('dl_texte');\">\n"
		   . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
		   . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"40\" value=\"" . $titre . "\" /></td></tr>\n"
		   . "<tr><td><b>" . _CAT . " :</b> <select name=\"cat\"><option value=\"" . $cid . "\">" . $cat_name . "</option>\n";

		select_cat();

		echo "</select></td></tr>\n"
		   . "<tr><td align=\"left\" colspan=\"2\"><b>" . _AUTOR . " :</b> <input type=\"text\" name=\"autor\" size=\"40\" value=\"" . $autor . "\" /></td></tr>\n"
		   . "<tr><td align=\"left\"><b>" . _SITE . " :</b> <input type=\"text\" name=\"site\" size=\"55\" value=\"" . $url_autor . "\" /></td></tr>\n"
		   . "<tr><td align=\"center\">\n";

		echo"</td></tr><tr><td align=\"center\">\n";


		echo "</td></tr><tr><td><b>" . _DESCR . " : </b><br />\n"
		   . "<textarea class=\"editor\" id=\"dl_texte\" name=\"description\" rows=\"10\" cols=\"65\" onselect=\"storeCaret('dl_texte');\" onclick=\"storeCaret('dl_texte');\" onkeyup=\"storeCaret('dl_texte');\">" . $description . "</textarea></td></tr>\n"
		   . "<tr><td><b>" . _DOWNLOADED . "</b> : <input type=\"text\" name=\"count\" size=\"7\" value=\"" . $count . "\" />&nbsp;<b>" . _SIZE . " :  </b><input type=\"text\" name=\"taille\" size=\"5\" value=\"" . $taille . "\" /> (" . _KO . ")"
		   . "&nbsp;<b>" . _LEVEL . " :</b> <select name=\"level\"><option>" . $level . "</option>\n"
		   . "<option>0</option>\n"
		   . "<option>1</option>\n"
		   . "<option>2</option>\n"
		   . "<option>3</option>\n"
		   . "<option>4</option>\n"
		   . "<option>5</option>\n"
		   . "<option>6</option>\n"
		   . "<option>7</option>\n"
		   . "<option>8</option>\n"
		   . "<option>9</option></select></td></tr>\n"
		   . "<tr><td><b>" . _COMPATIBLE . " :</b> <input type=\"text\" name=\"comp\" size=\"45\" value=\"" . $comp . "\" /></td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr>\n"
		   . "<tr><td><b>" . _URLFILE . " :</b> <input type=\"text\" name=\"url\" size=\"55\" value=\"" . $url . "\" /></td></tr>\n"
		   . "<tr><td><b>" . _URL2 . " :</b> <input type=\"text\" name=\"url2\" size=\"55\" value=\"" . $url2 . "\" /></td></tr>\n"
		   . "<tr><td><b>" . _URL3 . " :</b> <input type=\"text\" name=\"url3\" size=\"55\" value=\"" . $url3 . "\" /></td></tr>\n"
		   . "<tr><td><b>" . _UPFILE . " :</b>&nbsp;" . $upload_status . " <br /><input type=\"file\" name=\"copy\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_file\" value=\"1\" /> " . _REPLACE . "</td></tr>\n"
		   . "<tr><td>&nbsp;</td></tr>\n"
		   . "<tr><td><b>" . _CAPTURE . " :</b> <input type=\"text\" name=\"screen\" size=\"42\" value=\"" . $screen . "\" /></td></tr>\n"
		   . "<tr><td><b>" . _UPIMG . " :</b> <br /><input type=\"file\" name=\"screen2\" />&nbsp;<input class=\"checkbox\" type=\"checkbox\" name=\"ecrase_screen\" value=\"1\" /> " . _REPLACE . "</td></tr>\n"
		   . "<tr><td>&nbsp;<input type=\"hidden\" name=\"did\" value=\"" . $did . "\" /></td></tr>\n"
		   . "<tr><td align=\"center\"><input type=\"submit\" value=\"" . _MODIFFILE . "\" /></td></tr>\n"
		   . "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Download&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";

	}

	function modif_file($did, $date, $taille, $titre, $description, $cat, $count, $url, $url2, $url3, $level, $autor, $site, $comp, $screen, $screen2, $copy, $ecrase_file, $ecrase_screen) {
		global $nuked, $user;

		$description = html_entity_decode($description);
		$description = mysql_real_escape_string(stripslashes($description));
		$titre = mysql_real_escape_string(stripslashes($titre));
		$autor = mysql_real_escape_string(stripslashes($autor));
		$comp = mysql_real_escape_string(stripslashes($comp));

		$day = time();
		$taille = str_replace(",", ".", $taille);

		if ($site == "http://") $site = "";
		if ($url == "http://") $url = "";
		if ($url2 == "http://") $url2 = "";
		if ($url3 == "http://") $url3 = "";
		if ($screen == "http://") $screen = "";

		if ($site != "" && !preg_match("`http://`i", $site)) {
			$site = "http://" . $site;
		}

		$racine_up = "upload/Download/";
		$racine_down = "";

		if ($_FILES['copy']['name'] != "") {
			$filename = $_FILES['copy']['name'];
			$filesize = $_FILES['copy']['size'];
			$taille = $filesize / 1024;
			$taille = (round($taille * 100)) / 100;
			$url_file = $racine_up . $filename;

			if (!is_file($url_file) || $ecrase_file == 1) {
				if (!preg_match("`\.php`i", $filename) && !preg_match("`\.htm`i", $filename) && !preg_match("`\.[a-z]htm`i", $filename) && $filename != ".htaccess") {
					move_uploaded_file($_FILES['copy']['tmp_name'], $url_file) or die ("Upload file failed !!!");
					@chmod ($url_file, 0644);
				} else {
					echo "<br /><br /><div style=\"text-align: center;\">Unauthorized file !!!</div><br /><br />";
					redirect("index.php?file=Download&page=admin&op=edit_file&did=" . $did, 2);
					adminfoot();
					footer();
					die;
				}
			} else {
				$deja_file = 1;
			}

			$url_full = $racine_down . $url_file;
			$url_full = $url_file;

			if ($url == "") $url = $url_full;
			else if ($url2 == "") $url2 = $url_full;
			else if ($url3 == "") $url3 = $url_full;
			else $url = $url_full;
		}

		if ($_FILES['screen2']['name'] && $url) {

			$screenname = $_FILES['screen2']['name'];
			$ext = strrchr($screenname, ".");
			$ext = substr($screenname, 1);
			$filename2 = str_replace($ext, "", $screenname);
			$url_screen = $racine_up . $filename2 . $ext;

			if (!is_file($url_screen) || $ecrase_screen == 1) {
				if (!preg_match("`\.php`i", $screenname) && !preg_match("`\.htm`i", $screenname) && !preg_match("`\.[a-z]htm`i", $screenname) && (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext) || preg_match("`gif`i", $ext) || preg_match("`png`i", $ext))) {
					move_uploaded_file($_FILES['screen2']['tmp_name'], $url_screen) or die ("Upload screen failed !!!");
					@chmod ($url_screen, 0644);
				} else {
					echo "<br /><br /><div style=\"text-align: center;\">No image file !!!</div><br /><br />";
					redirect("index.php?file=Download&page=admin&op=edit_file&did=" . $did, 2);
					adminfoot();
					footer();
					die;
				}
			} else {
				$deja_screen = 1;
			}

			$url_full_screen = $racine_down . $url_screen;
			$screen = $url_full_screen;
		}

		if ($deja_file == 1 || $deja_screen == 1) {
			echo "<br /><br /><div style=\"text-align: center;\">";

			if ($deja_file == 1) echo _DEJAFILE;
			if ($deja_screen == 1) echo "&nbsp;" . _DEJASCREEN;

			echo "<br />" . _REPLACEIT . "<br /><br /><a href=\"javascript:history.back();\"><b>" . _BACK . "</b></a></div><br /><br />";
		} else if ($url != "" && $titre != "") {
			$sql = mysql_query("UPDATE " . DOWNLOAD_TABLE . " SET titre = '" . $titre . "', description = '" . $description . "', type = '" . $cat . "', count = '" . $count . "', url = '" . $url . "', url2 = '" . $url2 . "', url3 = '" . $url3 . "', taille = '" . $taille . "', level = '" . $level . "', edit = '" . $day . "', autor = '" . $autor . "', url_autor = '" . $site . "', comp = '" . $comp . "', screen = '" . $screen . "' WHERE id = '" . $did . "'");
			// Action
			$texteaction = "". _ACTIONMODIFDL .": ".$titre."";
			$acdate = time();
			$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
			//Fin action
			echo "<div class=\"notification success png_bg\">\n"
			   . "<div>\n"
			   . _FILEEDIT . "\n"
			   . "</div>\n"
			   . "</div>\n";
			echo "<script>\n"
			   . "	setTimeout('screen()','3000');\n"
			   . "	function screen() {\n"
			   . "		screenon('index.php?file=Download&op=description&dl_id=".$did."', 'index.php?file=Download&page=admin');\n"
			   . "	}\n"
			   . "</script>\n";
		} else {
			echo "<br /><br /><div style=\"text-align: center;\">" . _URLORTITLEFAILDED . "<br /><br /><a href=\"javascript:history.back();\"><b>" . _BACK . "</b></a></div><br /><br />";
		}
	}

	function main_broken() {
		global $nuked, $language;

		echo"<script type=\"text/javascript\">\n"
		   . "<!--\n"
		   . "\n"
		   . "	function delfile(titre, id) {\n"
		   . "		if (confirm('" . _DELETEFILE . " '+titre+' ! " . _CONFIRM . "')) {\n"
		   . "			document.location.href = 'index.php?file=Download&page=admin&op=del_file&did='+id;\n"
		   . "		}\n"
		   . "	}\n"
		   . "\n"
		   . "	function delbroke() {\n"
		   . "		if (confirm('" . _ERASEALLLIST . "')) {\n"
		   . "			document.location.href = 'index.php?file=Download&page=admin&op=del_broken';\n"
		   . "		}\n"
		   . "	}\n"
		   . "\n"
		   . "// -->\n"
		   . "</script>\n";

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINDOWN . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Download&amp;page=admin\">" . _DOWNLOAD . "</a> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=add_file\">" . _ADDFILE . "</a> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
		   . "</b>" . _BROKENLINKS . "<b> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
		   . "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
		   . "<tr>\n"
		   . "<td style=\"width: 10%;\" align=\"center\"><b>#</b></td>\n"
		   . "<td colspan=\"2\" style=\"width: 35%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
		   . "<td style=\"width: 10%;\" align=\"center\"><b>X</b></td>\n"
		   . "<td style=\"width: 15%;\" align=\"center\"><b>" . _ERASE . "</b></td>\n"
		   . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
		   . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

		$i = 0;
		$l = 0;
		$sql = mysql_query("SELECT id, titre, url, broke FROM " . DOWNLOAD_TABLE . " WHERE broke > 0 ORDER BY broke DESC, type");
		$nb_broke = mysql_num_rows($sql);

		if ($nb_broke > 0) {
			while (list($did, $titre, $url, $broke) = mysql_fetch_array($sql)) {
				$titre = printSecuTags($titre);
				$l++;

				echo "<tr>\n"
				   . "<td style=\"width: 10%;\" align=\"center\">" . $l . "</td>\n"
				   . "<td style=\"width: 30%;\"><b>" . $titre . "</b></td><td style=\"width: 5%;\" align=\"center\"><a href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"modules/Download/images/download.gif\" alt=\"\" title=\"" . $url . "\" /></a></td>\n"
				   . "<td style=\"width: 10%;\" align=\"center\">" . $broke . "</td>\n"
				   . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=del_broke&amp;did=" . $did. "\"><img style=\"border: 0;\" src=\"modules/Download/images/del.gif\" alt=\"\" title=\"" . _ERASEFROMLIST . "\" /></a></td>\n"
				   . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=edit_file&amp;did=" . $did . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISFILE . "\" /></a></td>\n"
				   . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:delfile('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $did . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISFILE . "\" /></a></td></tr>\n";
			}
		} else {
			echo "<tr><td align=\"center\" colspan=\"6\">" . _NODOWNLOADINDB . "</td></tr>\n";
		}

		echo "</table><br /><div style=\"text-align: center;\">[ <a href=\"javascript:delbroke();\"><b>" . _ERASELIST . "</b></a> ]</div>\n"
		   . "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Download&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
	}

	function del_broke($did) {
		global $nuked, $user;

		$sql2 = mysql_query("SELECT titre FROM " . DOWNLOAD_TABLE . " WHERE id = '" . $did . "'");
		list($titre) = mysql_fetch_array($sql2);
		$titre = mysql_real_escape_string($titre);
		$sql = mysql_query("UPDATE " . DOWNLOAD_TABLE . " SET broke = 0 WHERE id = '" . $did . "'");
		// Action
		$texteaction = "". _ACTION1BROKEDL .": ".$titre."";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _FILEERASED . "\n"
		   . "</div>\n"
		   . "</div>\n";
		redirect("index.php?file=Download&page=admin&op=main_broken", 2);
	}

	function del_broken() {
		global $nuked, $user;
		$sql = mysql_query("UPDATE " . DOWNLOAD_TABLE . " SET broke = 0");
		// Action
		$texteaction = "". _ACTIONALLBROKEDL .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _LISTERASED . "\n"
		   . "</div>\n"
		   . "</div>\n";
		redirect("index.php?file=Download&page=admin&op=main_broken", 2);
	}

	function main() {
		global $nuked, $language;

		$nb_download = 30;

		$sql3 = mysql_query("SELECT id FROM " . DOWNLOAD_TABLE);
		$nb_dl = mysql_num_rows($sql3);

		if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
		$start = $_REQUEST['p'] * $nb_download - $nb_download;

		echo"<script type=\"text/javascript\">\n"
		   . "<!--\n"
		   . "\n"
		   . "	function delfile(titre, id) {\n"
		   . "		if (confirm('" . _DELETEFILE . " '+titre+' ! " . _CONFIRM . "')) {\n"
		   . "			document.location.href = 'index.php?file=Download&page=admin&op=del_file&did='+id;\n"
		   . "		}\n"
		   . "	}\n"
		   . "\n"
		   . "// -->\n"
		   . "</script>\n";

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINDOWN . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _DOWNLOAD . "<b> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=add_file\">" . _ADDFILE . "</a> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_broken\">" . _BROKENLINKS . "</a> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n";

		if ($_REQUEST['orderby'] == "date") {
			$order_by = "D.id DESC";
		} else if ($_REQUEST['orderby'] == "name") {
			$order_by = "D.titre";
		} else if ($_REQUEST['orderby'] == "cat") {
			$order_by = "DC.titre, DC.parentid";
		} else {
			$order_by = "D.id DESC";
		}

		echo "<table width=\"100%\" cellpadding=\"2\" cellspacing=\"0\" border=\"0\">\n"
		   . "<tr><td align=\"right\">" . _ORDERBY . " : ";

		if ($_REQUEST['orderby'] == "date" || !$_REQUEST['orderby']) {
			echo "<b>" . _DATE . "</b> | ";
		} else {
			echo "<a href=\"index.php?file=Download&amp;page=admin&amp;orderby=date\">" . _DATE . "</a> | ";
		}

		if ($_REQUEST['orderby'] == "name") {
			echo "<b>" . _TITLE . "</b> | ";
		} else {
			echo "<a href=\"index.php?file=Download&amp;page=admin&amp;orderby=name\">" . _TITLE . "</a> | ";
		}

		if ($_REQUEST['orderby'] == "cat") {
			echo "<b>" . _CAT . "</b>";
		} else {
			echo "<a href=\"index.php?file=Download&amp;page=admin&amp;orderby=cat\">" . _CAT . "</a>";
		}

		echo "&nbsp;</td></tr></table>\n";

		if ($nb_dl > $nb_download) {
			echo "<div>";
			$url_page = "index.php?file=Download&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
			number($nb_dl, $nb_download, $url_page);
			echo "</div>\n";
		}

		echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
		   . "<tr>\n"
		   . "<td style=\"width: 30%;\" align=\"center\" colspan=\"2\"><b>" . _TITLE . "</b></td>\n"
		   . "<td style=\"width: 20%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
		   . "<td style=\"width: 20%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
		   . "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
		   . "<td style=\"width: 15%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

		$i = 0;
		$sql = mysql_query("SELECT D.id, D.type, D.titre, D.url, D.date, DC.parentid, DC.titre  FROM " . DOWNLOAD_TABLE . " AS D LEFT JOIN " . DOWNLOAD_CAT_TABLE . " AS DC ON DC.cid = D.type ORDER BY " . $order_by . " LIMIT " . $start . ", " . $nb_download);
		while (list($did, $cat, $titre, $url, $date, $parentid, $namecat) = mysql_fetch_array($sql)) {
			$titre = printSecuTags($titre);

			$date = nkDate($date);

			if ($cat == 0) {
				$categorie = _NONE;
			} else if ($parentid == 0) {
				$categorie = $namecat;
			} else {
				$sql3 = mysql_query("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
				list($parentcat) = mysql_fetch_array($sql3);
				$categorie = $parentcat . " -> " . $namecat;
				$categorie = printSecuTags($categorie);
			}

			echo "<tr style=\"background: " . $bg . ";\">\n"
			   . "<td style=\"width: 25%;\">" . $titre . "</td><td style=\"width: 5%;\" align=\"center\"><a href=\"" . $url . "\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"modules/Download/images/download.gif\" alt=\"\" title=\"" . $url . "\" /></a></td>\n"
			   . "<td style=\"width: 20%;\" align=\"center\">" . $date . "</td>\n"
			   . "<td style=\"width: 20%;\" align=\"center\">" . $categorie . "</td>\n"
			   . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=edit_file&amp;did=" . $did . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISFILE . "\" /></a></td>\n"
			   . "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:delfile('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $did . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISFILE . "\" /></a></td></tr>\n";
		}

		if ($nb_dl == 0) {
			echo "<tr><td align=\"center\" colspan=\"6\">" . _NODOWNLOADINDB . "</td></tr>\n";
		}

		echo "</table>\n";

		if ($nb_dl > $nb_download) {
			echo "<div>";
			$url_page = "index.php?file=Download&amp;page=admin&amp;orderby=" . $_REQUEST['orderby'];
			number($nb_dl, $nb_download, $url_page);
			echo "</div>\n";
		}

		echo "<br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
	}

	function main_cat() {
		global $nuked, $language;

		echo "<script type=\"text/javascript\">\n"
		   . "<!--\n"
		   . "\n"
		   . "	function delcat(titre, id) {\n"
		   . "		if (confirm('" . _DELETEFILE . " '+titre+' ! " . _CONFIRM . "')) {\n"
		   . "			document.location.href = 'index.php?file=Download&page=admin&op=del_cat&cid='+id;\n"
		   . "		}\n"
		   . "	}\n"
		   . "\n"
		   . "// -->\n"
		   . "</script>\n";

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINDOWN . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Download&amp;page=admin\">" . _DOWNLOAD . "</a> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=add_file\">" . _ADDFILE . "</a> | "
		   . "</b>" . _CATMANAGEMENT . "<b><br />"
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_broken\">" . _BROKENLINKS . "</a> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_pref\">" . _PREFS . "</a></b></div><br />\n"
		   . "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
		   . "<tr>\n"
		   . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CAT . "</b></td>\n"
		   . "<td style=\"width: 35%;\" align=\"center\"><b>" . _CATPARENT . "</b></td>\n"
		   . "<td style=\"width: 10%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
		   . "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
		   . "<td style=\"width: 10%;\" align=\"center\"><b>" . _DELETE . "</b></td></tr>\n";

		$i = 0;
		$sql = mysql_query("SELECT cid, titre, parentid, position FROM " . DOWNLOAD_CAT_TABLE . " ORDER BY parentid, position");
		$nbcat = mysql_num_rows($sql);

		if ($nbcat > 0) {
			while (list($cid, $titre, $parentid, $position) = mysql_fetch_array($sql)) {
				$titre = printSecuTags($titre);

				echo "<tr>\n"
				. "<td style=\"width: 35%;\" align=\"center\">" . $titre . "</td>\n"
				. "<td style=\"width: 35%;\" align=\"center\">\n";

				if ($parentid > 0) {
					$sql2 = mysql_query("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
					list($pnomcat) = mysql_fetch_array($sql2);
					$pnomcat = printSecuTags($pnomcat);

					echo "<i>" . $pnomcat . "</i>";
				} else {
					echo _NONE;
				}

				echo "</td><td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=down\" title=\"" . _MOVEDOWN . "\">&lt;</a>"
				   . "&nbsp;" . $position . "&nbsp;<a href=\"index.php?file=Download&amp;page=admin&amp;op=modif_position&amp;cid=" . $cid . "&amp;method=up\" title=\"" . _MOVEUP . "\">&gt;</a></td>\n"
				   . "<td align=\"center\"><a href=\"index.php?file=Download&amp;page=admin&amp;op=edit_cat&amp;cid=" . $cid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDITTHISCAT . "\" /></a></td>\n"
				   . "<td align=\"center\"><a href=\"javascript:delcat('" . mysql_real_escape_string(stripslashes($titre)) . "', '" . $cid . "');\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" title=\"" . _DELTHISCAT . "\" /></a></td></tr>\n";
			}
		}else{
			echo "<tr><td align=\"center\" colspan=\"5\">" . _NONE . "&nbsp;" . _CAT . "&nbsp;" . _INDATABASE . "</td></tr>\n";
		}

		echo "</table><br /><div style=\"text-align: center;\">[ <a href=\"index.php?file=Download&amp;page=admin&amp;op=add_cat\"><b>" . _ADDCAT . "</b></a> ]</div>\n"
		   . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Download&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
	}

	function add_cat() {
		global $language, $nuked;

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINDOWN . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Download&amp;page=admin&amp;op=send_cat\">\n"
		   . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
		   . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" /></td></tr>\n"
		   . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\"><option value=\"0\">" . _NONE . "</option>\n";

		$sql = mysql_query("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
		while (list($cid, $nomcat) = mysql_fetch_array($sql)) {
			$nomcat = printSecuTags($nomcat);

			echo "<option value=\"" . $cid . "\">" . $nomcat . "</option>\n";
		}

		echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"0\" />\n"
		   . "&nbsp;<b>" . _LEVEL . " :</b> <select name=\"level\">\n"
		   . "<option>0</option>\n"
		   . "<option>1</option>\n"
		   . "<option>2</option>\n"
		   . "<option>3</option>\n"
		   . "<option>4</option>\n"
		   . "<option>5</option>\n"
		   . "<option>6</option>\n"
		   . "<option>7</option>\n"
		   . "<option>8</option>\n"
		   . "<option>9</option></select></td></tr>\n"
		   . "<tr><td><b>" . _DESCR . " :</b></td></tr>\n"
		   . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\"></textarea></td></tr></table>\n"
		   . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _CREATECAT . "\" /></div>\n"
		   . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Download&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
	}

	function send_cat($titre, $description, $parentid, $level, $position) {
		global $nuked, $user;

		$description = html_entity_decode($description);
		$titre = mysql_real_escape_string(stripslashes($titre));
		$description = mysql_real_escape_string(stripslashes($description));

		$sql = mysql_query("INSERT INTO " . DOWNLOAD_CAT_TABLE . " ( `parentid` , `titre` , `description` , `level` , `position` ) VALUES ( '" . $parentid . "' , '" . $titre . "' , '" . $description . "' , '" . $level . "' , '" . $position . "' )");
		// Action
		$texteaction = "". _ACTIONADDCATDL .": ".$titre."";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _CATADD . "\n"
		   . "</div>\n"
		   . "</div>\n";
		$sql2 = mysql_query("SELECT cid FROM " . DOWNLOAD_CAT_TABLE . " WHERE titre = '" . $titre . "' AND parentid = '" . $parentid . "'");
		list($did) = mysql_fetch_array($sql2);
		echo "<script>\n"
		   . "	setTimeout('screen()','3000');\n"
		   . "	function screen() { \n"
		   . "		screenon('index.php?file=Download&op=categorie&cat=".$did."', 'index.php?file=Download&page=admin&op=main_cat');\n"
		   . "	}\n"
		   . "</script>\n";
	}

	function edit_cat($cid) {
		global $nuked, $language;

		$sql = mysql_query("SELECT titre, description, parentid, level, position FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cid . "'");
		list($titre, $description, $parentid, $level, $position) = mysql_fetch_array($sql);

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINDOWN . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Download&amp;page=admin&amp;op=modif_cat\">\n"
		   . "<table  style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
		   . "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $titre . "\" /></td></tr>\n"
		   . "<tr><td><b>" . _CATPARENT . " :</b> <select name=\"parentid\">\n";

		if ($parentid > 0) {
			$sql2 = mysql_query("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $parentid . "'");
			list($pcid, $pnomcat) = mysql_fetch_array($sql2);

			$pnomcat = printSecuTags($pnomcat);

			echo "<option value=\"" . $pcid . "\">" . $pnomcat . "</option>\n";
		}

		echo "<option value=\"0\">" . _NONE . "</option>\n";

		$sql3 = mysql_query("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
		while (list($catid, $nomcat) = mysql_fetch_array($sql3)) {
			$nomcat = printSecuTags($nomcat);

			if ($nomcat != $titre) {
				echo "<option value=\"" . $catid . "\">" . $nomcat . "</option>\n";
			}
		}

		echo "</select></td></tr><tr><td><b>" . _POSITION . " : </b><input type=\"text\" name=\"position\" size=\"2\" value=\"" . $position . "\" />\n"
		   . "&nbsp;<b>" . _LEVEL . " :</b> <select name=\"level\"><option>" . $level . "</option>\n"
		   . "<option>0</option>\n"
		   . "<option>1</option>\n"
		   . "<option>2</option>\n"
		   . "<option>3</option>\n"
		   . "<option>4</option>\n"
		   . "<option>5</option>\n"
		   . "<option>6</option>\n"
		   . "<option>7</option>\n"
		   . "<option>8</option>\n"
		   . "<option>9</option></select></td></tr>\n"
		   . "<tr><td><b>" . _DESCR . " :</b> <input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" /></td></tr>\n"
		   . "<tr><td align=\"center\"><textarea class=\"editor\" name=\"description\" cols=\"60\" rows=\"10\">" . $description . "</textarea></td></tr></table>\n"
		   . "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _MODIFTHISCAT . "\" /></div>\n"
		   . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Download&amp;page=admin&amp;op=main_cat\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
	}

	function modif_cat($cid, $titre, $description, $parentid, $level, $position) {
		global $nuked, $user;

		$description = html_entity_decode($description);
		$titre = mysql_real_escape_string(stripslashes($titre));
		$description = mysql_real_escape_string(stripslashes($description));

		$sql = mysql_query("UPDATE " . DOWNLOAD_CAT_TABLE . " SET parentid = '" . $parentid . "', titre = '" . $titre . "', description = '" . $description . "', level = '" . $level . "', position = '" . $position . "' WHERE cid = '" . $cid . "'");
		$sql_file = mysql_query("UPDATE " . DOWNLOAD_TABLE . " SET level = '" . $level . "' WHERE type = '" . $cid . "'");
		$sql_cat = mysql_query("UPDATE " . DOWNLOAD_CAT_TABLE . " SET level = '" . $level . "' WHERE parentid = '" . $cid . "'");

		$sql_cat = mysql_query("SELECT cid FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = '" . $cid . "'");
		while (list($cat_id) = mysql_fetch_array($sql_cat)) {
			$sql_file2 = mysql_query("UPDATE " . DOWNLOAD_TABLE . " SET level = '" . $level . "' WHERE type = '" . $cat_id . "'");
		}
		// Action
		$texteaction = "". _ACTIONMODIFCATDL .": ".$titre."";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _CATMODIF . "\n"
		   . "</div>\n"
		   . "</div>\n";
		echo "<script>\n"
		   . "	setTimeout('screen()','3000');\n"
		   . "	function screen() { \n"
		   . "		screenon('index.php?file=Download&op=categorie&cat=".$cid."', 'index.php?file=Download&page=admin&op=main_cat');\n"
		   . "	}\n"
		   . "</script>\n";
	}

	function select_cat() {
		global $nuked;

		$sql = mysql_query("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = 0 ORDER BY position, titre");
		while (list($cid, $titre) = mysql_fetch_array($sql)) {
			$titre = printSecuTags($titre);

			echo "<option value=\"" . $cid . "\">* " . $titre . "</option>\n";

			$sql2 = mysql_query("SELECT cid, titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE parentid = '" . $cid . "' ORDER BY position, titre");
			while (list($s_cid, $s_titre) = mysql_fetch_array($sql2)) {
				$s_titre = printSecuTags($s_titre);

				echo "<option value=\"" . $s_cid . "\">&nbsp;&nbsp;&nbsp;" . $s_titre . "</option>\n";
			}
		}
		echo "<option value=\"0\">* " . _NONE . "</option>\n";
	}

	function del_cat($cid) {
		global $nuked, $user;
		$sql2 = mysql_query("SELECT titre FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cid . "'");
		list($titre) = mysql_fetch_array($sql2);
		$sql = mysql_query("DELETE FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cid . "'");
		$sql = mysql_query("UPDATE " . DOWNLOAD_CAT_TABLE . " SET parentid = 0 WHERE parentid = '" . $cid . "'");
		$sql = mysql_query("UPDATE " . DOWNLOAD_TABLE . " SET type = 0 WHERE type = '" . $cid . "'");
		// Action
		$texteaction = "". _ACTIONDELCATDL .": ".$titre."";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _CATDEL . "\n"
		   . "</div>\n"
		   . "</div>\n";
		redirect("index.php?file=Download&page=admin&op=main_cat", 2);
	}

	function main_pref() {
		global $nuked, $language;

		if ($nuked['hide_download'] == "on") $checked = "checked=\"checked\"";

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		   . "<div class=\"content-box-header\"><h3>" . _ADMINDOWN . "</h3>\n"
		   . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Download.php\" rel=\"modal\">\n"
		   . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		   . "</div></div>\n"
		   . "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"index.php?file=Download&amp;page=admin\">" . _DOWNLOAD . "</a> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=add_file\">" . _ADDFILE . "</a> | "
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_cat\">" . _CATMANAGEMENT . "</a><br />"
		   . "<a href=\"index.php?file=Download&amp;page=admin&amp;op=main_broken\">" . _BROKENLINKS . "</a> | </b>"
		   . _PREFS . "</div><br />\n"
		   . "<form method=\"post\" action=\"index.php?file=Download&amp;page=admin&amp;op=change_pref\">\n"
		   . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"0\" cellpadding=\"3\">\n"
		   . "<tr><td align=\"center\" colspan=\"2\"><big>" . _PREFS . "</big></td></tr>\n"
		   . "<tr><td>" . _NUMBERFILE . " :</td><td><input type=\"text\" name=\"max_download\" size=\"2\" value=\"" . $nuked['max_download'] . "\" /></td></tr>\n"
		   . "<tr><td>" . _HIDEDESC . " :</td><td><input class=\"checkbox\" type=\"checkbox\" name=\"hide_download\" value=\"on\" " . $checked . " /></td></tr>\n"
		   . "</table><div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
		   . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Download&amp;page=admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
	}

	function change_pref($max_download, $hide_download) {
		global $nuked, $user;

		if ($hide_download != "on") $hide_download = "off";

		$upd1 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $max_download . "' WHERE name = 'max_download'");
		$upd2 = mysql_query("UPDATE " . CONFIG_TABLE . " SET value = '" . $hide_download . "' WHERE name = 'hide_download'");
		// Action
		$texteaction = _ACTIONMODIFPREFDL .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _PREFUPDATED . "\n"
		   . "</div>\n"
		   . "</div>\n";

		redirect("index.php?file=Download&page=admin", 2);
	}

	function modif_position($cid, $method) {
		global $nuked, $user;

		$sql2 = mysql_query("SELECT titre, position FROM " . DOWNLOAD_CAT_TABLE . " WHERE cid = '" . $cid . "'");
		list($titre, $position) = mysql_fetch_array($sql2);
		if ($position <=0 AND $method == "up") {
			echo "<div class=\"notification error png_bg\">\n"
			   . "<div>\n"
			   . _CATERRORPOS . "\n"
			   . "</div>\n"
			   . "</div>\n";
			redirect("index.php?file=Download&page=admin&op=main_cat", 2);
			die;
		}

		if ($method == "up") $upd = mysql_query("UPDATE " . DOWNLOAD_CAT_TABLE . " SET position = position - 1 WHERE cid = '" . $cid . "'");
		else if ($method == "down") $upd = mysql_query("UPDATE " . DOWNLOAD_CAT_TABLE . " SET position = position + 1 WHERE cid = '" . $cid . "'");

		// Action
		$texteaction = "". _ACTIONPOSMODIFCATDL .": ".$titre."";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '". mysql_real_escape_string($texteaction) ."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		   . "<div>\n"
		   . _CATMODIF . "\n"
		   . "</div>\n"
		   . "</div>\n";
		redirect("index.php?file=Download&page=admin&op=main_cat", 2);
	}

	switch ($_REQUEST['op']) {
		case "edit_file":
			edit_file($_REQUEST['did']);
			break;

		case "add_file":
			add_file();
			break;

		case "del_file":
			del_file($_REQUEST['did']);
			break;

		case "send_file":
			send_file($_REQUEST['date'], $_REQUEST['size'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['cat'], $_REQUEST['url'], $_REQUEST['url2'], $_REQUEST['url3'], $_REQUEST['level'], $_REQUEST['autor'], $_REQUEST['site'], $_REQUEST['comp'], $_REQUEST['screen'], $_REQUEST['screen2'], $_REQUEST['copy'], $_REQUEST['ecrase_file'], $_REQUEST['ecrase_screen']);
			UpdateSitmap();
			break;

		case "modif_file":
			modif_file($_REQUEST['did'], $_REQUEST['date'], $_REQUEST['taille'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['cat'], $_REQUEST['count'], $_REQUEST['url'], $_REQUEST['url2'], $_REQUEST['url3'], $_REQUEST['level'], $_REQUEST['autor'], $_REQUEST['site'], $_REQUEST['comp'], $_REQUEST['screen'], $_REQUEST['screen2'], $_REQUEST['copy'], $_REQUEST['ecrase_file'], $_REQUEST['ecrase_screen']);
			break;

		case "main":
			main();
			break;

		case "send_cat":
			send_cat($_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['parentid'], $_REQUEST['level'], $_REQUEST['position']);
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
			modif_cat($_REQUEST['cid'], $_REQUEST['titre'], $_REQUEST['description'], $_REQUEST['parentid'], $_REQUEST['level'], $_REQUEST['position']);
			break;

		case "del_cat":
			del_cat($_REQUEST['cid']);
			break;

		case "main_broken":
			main_broken();
			break;

		case "del_broke":
			del_broke($_REQUEST['did']);
			break;

		case "del_broken":
			del_broken();
			break;

		case "main_pref":
			main_pref();
			break;

		case "change_pref":
			change_pref($_REQUEST['max_download'], $_REQUEST['hide_download']);
			break;

		case "modif_position":
			modif_position($_REQUEST['cid'], $_REQUEST['method']);
			break;

		default:
			main();
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

adminfoot();

?>