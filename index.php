<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//

define ("INDEX_CHECK", 1);
$mtime = microtime();
include_once ('Includes/php51compatibility.php');
include_once ('Includes/is_image.php');
include ("globals.php");
@include ("conf.inc.php");
//For compatibility with all old module and theme
if (defined('COMPATIBILITY_MODE') && COMPATIBILITY_MODE == true)
	extract($_REQUEST);

if (!defined("NK_INSTALLED"))
{
	if (file_exists("install.php"))
	{
		header("location: install.php");
		exit(); //Run
	}
}
else
{
	if (file_exists("update.php")
		&&
		(
			(file_exists("install.php") && !unlink("install.php"))
			|| (file_exists("update.php") && !unlink("update.php"))
		))
	{
		echo "<br /><br /><br /><div style=\"text-align: center;\"><big>Warning ! <b>install.php</b> and <b>update.php</b> must be removed before continuing !</big></div>";
		exit();
	}
}
if (!defined("NK_OPEN"))
{
	echo "<br /><br /><br /><div style=\"text-align: center;\"><big>Sorry, this website is momently closed, Please try again later.</big></div>";
	exit();
}

date_default_timezone_set('Europe/Paris');
include ("nuked.php");
//error_reporting(E_ALL);
include_once ('Includes/hash.php');

//Gestion des erreus sql
set_error_handler("erreursql");
$session = session_check();
if ($session == 1) $user = secure();
else $user = array();

$session_admin = admin_check();
$check_ip = banip();

if ($check_ip != "")
{
	$url_ban = "ban.php?ip_ban=" . $check_ip;
	redirect($url_ban, 0);
	exit();
}

if (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] != "") $_REQUEST['im_file'] = $_REQUEST['nuked_nude'];
else if (isset($_REQUEST['page']) && $_REQUEST['page'] != "") $_REQUEST['im_file'] = $_REQUEST['page'];
else $_REQUEST['im_file'] = "index";

// Securite par phpSecure.info
if (preg_match("`\.\.`", $theme) || preg_match("`\.\.`", $language) || preg_match("`\.\.`", $_REQUEST['file']) || preg_match("`\.\.`", $_REQUEST['im_file']) || preg_match("`http\:\/\/`i", $_REQUEST['file']) || preg_match("`http\:\/\/`i", $_REQUEST['im_file'] || strpos( $_SERVER['QUERY_STRING'], ".." ) || strpos( $_SERVER['QUERY_STRING'], "http://" ) || strpos( $_SERVER['QUERY_STRING'], "%3C%3F" )))
{
	die("<br /><br /><br /><div style=\"text-align: center;\"><big>What are you trying to do ?</big></div>");
}

$_REQUEST['file'] = basename(trim($_REQUEST['file']));
$_REQUEST['im_file'] = basename(trim($_REQUEST['im_file']));
$_REQUEST['page'] = basename(trim($_REQUEST['im_file']));
$theme = trim($theme);
$language = trim($language);
// Fin
if (!$user)
{
	$visiteur = 0;
	$_SESSION['admin'] = false;
}
else
{
	$visiteur = $user[1];
}
if ($nuked['nk_status'] == "closed" && $user[1] < 9 && $_REQUEST['op'] != "login_screen" && $_REQUEST['op'] != "login_message" && $_REQUEST['op'] != "login")
{
	include ("themes/" . $theme . "/colors.php");
	translate("lang/" . $language . ".lang.php");

	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
	. "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
	. "<head><title>" . $nuked['name'] . " :: " . $nuked['slogan'] . " ::</title>\n"
	. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
	. "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
	. "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" />\n"
	. "<body style=\"background: " . $bgcolor2 . ";\"><div><br /><br /><br /><br /><br /><br /><br /><br /></div>\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor3 . ";\" cellspacing=\"1\" cellpadding=\"20\">\n"
	. "<tr><td style=\"background: " . $bgcolor1 . ";\" align=\"center\"><big><b>" . $nuked['name'] . " - " . $nuked['slogan'] . "</b></big><br /><br />\n"
	. _SITECLOSED . "</td></tr></table></body></html>";
}
else if (($_REQUEST['file'] == "Admin" || $_REQUEST['page'] == "admin" || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == "admin")) && $session_admin == '')
{
	include ("themes/" . $theme . "/colors.php");
	include ("themes/" . $theme . "/theme.php");
	translate("lang/" . $language . ".lang.php");

	if (!isset($_REQUEST['nuked_nude'])) top();
	include("modules/Admin/login.php");

	if (!isset($_REQUEST['nuked_nude']))
	{
		footer();
		include("Includes/copyleft.php");
	}
}
else if (($_REQUEST['file'] != "Admin" AND $_REQUEST['page'] != "admin") || ( nivo_mod($_REQUEST['file']) === false || (nivo_mod($_REQUEST['file']) > -1 && (nivo_mod($_REQUEST['file']) <= $visiteur))) )
{

	include ("themes/" . $theme . "/colors.php");
	include ("themes/" . $theme . "/theme.php");
	translate("lang/" . $language . ".lang.php");

	if ($nuked['level_analys'] != -1) visits();

	if (!isset($_REQUEST['nuked_nude']))
	{

		if (defined("NK_GZIP") && @extension_loaded('zlib') && !@ini_get('zlib.output_compression') && @phpversion() >= "4.0.4" && stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)
		{
			ob_start("ob_gzhandler");
			header('Content-Encoding: gzip');
		}
		else
		{
			ob_start();
		}

		if (!($_REQUEST['file'] == "Admin" || $_REQUEST['page'] == "admin" || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == "admin")) || $_REQUEST['page'] == "login")
		{
			top();
		}
		echo "<script type=\"text/javascript\" src=\"js/infobulle.js\"></script>"
		. "<script type=\"text/javascript\">InitBulle('" . $bgcolor2 . "', '" . $bgcolor3 . "', 2);</script>\n"
		."<link rel=\"stylesheet\" href=\"editeur/plugins/insertcode/insertcode.css\" type=\"text/css\" media=\"screen\" />\n";
		if (!($_REQUEST['file'] == "Admin" || $_REQUEST['page'] == "admin" || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == "admin")) || $_REQUEST['page'] == "login")
		{
		?>
		<script type="text/javascript" src="editeur/tiny_mce.js"></script>
		<script type="text/javascript">
			tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		<?php
		if($language == "french")
		{
		?>
		language : "fr",
		<?php
		}
		?>
		plugins : "pagebreak,layer,table,save,advimage,advlink,emotions,spellchecker,inlinepopups,preview,print,contextmenu,paste,directionality,fullscreen,wordcount,advlist,autosave",
		editor_selector : "editoradvanced",
		// Theme options
		<?php
		$sql1 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'couleur'");
		list($couleur) = mysql_fetch_array($sql1);
		$sql2 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'bouton'");
		list($bouton) = mysql_fetch_array($sql2);
		$sql3 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'status'");
		list($status) = mysql_fetch_array($sql3);
		$sql5 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'ligne1'");
		list($ligne1) = mysql_fetch_array($sql5);
		$sql6 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'ligne2'");
		list($ligne2) = mysql_fetch_array($sql6);
		$sql7 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'ligne3'");
		list($ligne3) = mysql_fetch_array($sql7);
		$sql8 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'ligne4'");
		list($ligne4) = mysql_fetch_array($sql8);
		?>
		theme_advanced_buttons1 : "<?php echo $ligne1; ?>",
		theme_advanced_buttons2 : "<?php echo $ligne2; ?>",
		theme_advanced_buttons3 : "<?php echo $ligne3; ?>",
		theme_advanced_buttons4 : "<?php echo $ligne4; ?>",
		theme_advanced_toolbar_location : "<?php echo $bouton; ?>",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "<?php echo $status; ?>",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "themes/<?php echo $theme; ?>/editeur.css",

		// Drop lists for link/image/media/template dialogs
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		// Style formats
		style_formats : [
			<?php
			$sql = mysql_query("SELECT texte FROM " . $nuked['prefix'] . "_style ORDER BY id DESC");
			$nbr = mysql_num_rows($sql);
			$compteur = 0;
			while (list($texte) = mysql_fetch_array($sql))
			{	$compteur++;
				if($compteur != $nbr)
				{
					echo "".$texte.",\n";
				}
				else
				{
					echo "".$texte."\n";
				}
			}
			?>
			]
		});
		tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		<?php
		if($language == "french")
		{
		?>
		language : "fr",
		<?php
		}
		?>
		plugins : "pagebreak,layer,table,save,advimage,advlink,emotions,spellchecker,inlinepopups,preview,print,contextmenu,paste,directionality,fullscreen,wordcount,advlist,autosave",
		editor_selector : "editorsimpla",
		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,|,justifycenter,|,emotions,forecolor,|,removeformat,|,spellchecker",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "<?php echo $bouton; ?>",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "none",
		theme_advanced_resizing : true,
		content_css : "themes/<?php echo $theme; ?>/editeur.css",
	});
		</script>
		<?php
		$sql = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name='couleur'");
		?>
		<style type="text/css">
		#forum_texte_toolbargroup {
			background-color: #<?php echo mysql_result($sql, 0); ?>;
		}
		</style>
		<?php
		if ($couleur != "")
		{
		?>
			<style>
			.defaultSkin table.mceLayout tr.mceFirst
			{
			font-size: 13px;
			background-color: #<?php echo $couleur; ?>;
			border: 1px solid #d5d5d5;
			}
			.defaultSkin table.mceLayout tr.mceLast {
			background-color: #<?php echo $couleur; ?>;
			}
			</style>
		<?php
		}
		else if($bgcolor4 !="")
		{
		?>
			<style>
			.defaultSkin table.mceLayout tr.mceFirst
			{
			font-size: 13px;
			background-color: <?php echo $bgcolor4; ?>;
			border: 1px solid #d5d5d5;
			}
			.defaultSkin table.mceLayout tr.mceLast {
			background-color: <?php echo $bgcolor4; ?>;
			}
			</style>
		<!-- /TinyMCE -->
		<?php
		}
		else
		{
		?>
			<style>
			.defaultSkin table.mceLayout tr.mceFirst
			{
			font-size: 13px;
			background: #fff url('modules/Admin/images/bg-form-field.gif') top left repeat-x;
			border: 1px solid #d5d5d5;
			}
			</style>
		<?php
		}
		}
		$sql = mysql_query('SELECT nom FROM ' . MODULES_TABLE);
		$signs = array();
		while($row = mysql_fetch_assoc($sql)){
			$signs[] = "'".Module_Hash($row['nom'])."'";
		}
		if (($_REQUEST['op'] == 'index' || $_REQUEST['op'] == '') && $_REQUEST['im_file'] == 'index' && $_REQUEST['file'] != 'Admin' && $visiteur == 9)
		echo "<script type=\"text/javascript\" src=\"js/update.js\"></script>\n"
			. "<script type=\"text/javascript\">\n"
			. "NKUpdate.lng = '$language';\n"
			. "NKUpdate.UpdateUrl = '" . UPDATE_URL . "';\n"
			. "</script>\n"
			. "<div style=\"background-color:$bgcolor1;padding:10px;border:double 4px $bgcolor3;width:auto;text-align:center;display:none;\" id=\"NKmess\"></div>\n"
			. "<script type=\"text/javascript\">\n"
			. "NKMessState = 0;NKMessUpdate = 0;\nNKUpdate.SetModCallback(function(tab){\n"
			. "if ((tab['State'] == 'pirate' || tab['State'] == 'faille') && NKMessState == 0) {\n"
			. "document.getElementById('NKmess').innerHTML = document.getElementById('NKmess').innerHTML + '<br />Un ou plusieurs de vos modules présentent des failles !';\n"
			. "NKMessState = 1;\n}\n if (tab['UpdateFile'] != null) {"
			. "document.getElementById('NKmess').innerHTML = document.getElementById('NKmess').innerHTML + '<br />Des mises à jour sont disponible pour un ou plusieurs de vos modules';\n"
			. "NKMessUpdate = 1;\n}\n if (((tab['State'] != 'pirate' || tab['State'] != 'faille') && NKMessState != 0 )||(tab['UpdateFile'] == null)) {\n document.getElementById('NKmess').style.display='none';\n}\n}\n, new Array(".implode(', ', $signs)."));\n"
			. "</script>\n";


		if ($nuked['nk_status'] == "closed" && $user[1] == 9)
		{
			echo "<table style=\"background: " . $bgcolor3 . ";\" width=\"100%\" cellspacing=\"1\" cellpadding=\"8\">\n"
			."<tr><td style=\"background: " . $bgcolor2 . ";\" ><big><b>" . _YOURSITEISCLOSED . " :<br /><br/ >" . $nuked['url'] . "/index.php?file=User&amp;op=login_screen</b></big></td></tr></table><br />\n";
		}
	}

	if (is_file("modules/" . $_REQUEST['file'] . "/" . $_REQUEST['im_file'] . ".php"))
	{
		include("modules/" . $_REQUEST['file'] . "/" . $_REQUEST['im_file'] . ".php");
	}
	else
	{
		include("modules/404/index.php");
	}

	if (!isset($_REQUEST['nuked_nude']))
	{
		if ($user[5] > 0 && !isset($_COOKIE['popup']) && $_REQUEST['file'] != "User" && $_REQUEST['file'] != "Userbox")
		{
			echo "<div id=\"popup_dhtml\" style=\"position:absolute;top:0;left:0;visibility:visible;z-index:10\"></div>\n"
			. "<script type=\"text/javascript\" src=\"js/popup.js\"></script>\n"
			. "<script type=\"text/javascript\">popup('" . $bgcolor2 . "', '" . $bgcolor3 . "', '" . _NEWMESSAGESTART . "&nbsp;" . $user[5] . "&nbsp;" . _NEWMESSAGEEND . "', '" . _CLOSEWINDOW . "', 'index.php?file=Userbox', 350, 100);</script>\n";
		}
		if (!($_REQUEST['file'] == "Admin" || $_REQUEST['page'] == "admin") || $_REQUEST['page'] == "login")
		{
			footer();
		}

		include("Includes/copyleft.php");
		$mtime = microtime() - $mtime;
		echo "<p style=\"color:#555555;text-align:center;width:100%;\">Generated in ${mtime}s</p></body></html>";
	}
}
else
{
	include ("themes/" . $theme . "/colors.php");
	include ("themes/" . $theme . "/theme.php");
	top();
	opentable();
	translate("lang/" . $language . ".lang.php");
	echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
	closetable();
	footer();
}
mysql_close($db);

?>