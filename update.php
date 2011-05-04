<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN 1.7 - Portal PHP                                            //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//

define ("INDEX_CHECK", 1);

include ("globals.php");
@include ("conf.inc.php");

function UpdatePassCrypt($pass){
	$bulder = '';
	$decal = $decal === null?rand(0, 15):$decal;
	$pass = sha1($pass);
	for ($i = 0; $i < strlen($pass) * 2; $i++)
	{
		if ($i % 2 == 0) {
			$builder .= $pass[$i / 2];
		}
		else
		{
			$builder .= substr(HASHKEY, ($i / 2 + $decal) % 20, 1);
		}
	}
	return '%'.dechex($decal).md5($builder);
}

function checkimg($url)
{
	$url = rtrim($url);
	$ext = strrchr($url, ".");
	$ext = substr($ext, 1);

	if (!preg_match("`\.php`i", $url) && !preg_match("`\.htm`i", $url) && !preg_match("`\.[a-z]htm`i", $url) && substr($url, -1) != "/" && (preg_match("`jpg`i", $ext) || preg_match("`jpeg`i", $ext) || preg_match("`gif`", $ext) || preg_match("`png`i", $ext) || preg_match("`bmp`i", $ext))) $img = $url;
	else $img = "images/noimagefile.gif";

	return($img);
}

function BBcode($texte, $imgMaxWidth = 0, $imgClic = FALSE)
{
	global $bgcolor3, $bgcolor1;

	if ($texte != "")
	{
		//$texte = nl2br($texte);

		$texte = " " . $texte;
		$texte = preg_replace("#([\t\r\n ])([a-z0-9]+?){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="\2://\3"  onclick="window.open(this.href); return false;">\2://\3</a>', $texte);
		$texte = preg_replace("#([\t\r\n ])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^ \"\n\r\t<]*)?)#i", '\1<a href="http://\2.\3"  onclick="window.open(this.href); return false;">\2.\3</a>', $texte);
		$texte = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $texte);

		$texte = str_replace("\r", "", $texte);
		$texte = str_replace("\n", "<br />", $texte);

		$texte = preg_replace("/\[color=(.*?)\](.*?)\[\/color\]/i", "<span style=\"color: \\1;\">\\2</span>", $texte);
		$texte = preg_replace("/\[size=(.*?)\](.*?)\[\/size\]/i", "<span style=\"font-size: \\1px;\">\\2</span>", $texte);
		$texte = preg_replace("/\[font=(.*?)\](.*?)\[\/font\]/i", "<span style=\"font-family: \\1;\">\\2</span>", $texte);
		$texte = preg_replace("/\[align=(.*?)\](.*?)\[\/align\]/i", "<div style=\"text-align: \\1;\">\\2</div>", $texte);
		$texte = str_replace("[b]", "<b>", $texte);
		$texte = str_replace("[/b]", "</b>", $texte);
		$texte = str_replace("[i]", "<i>", $texte);
		$texte = str_replace("[/i]", "</i>", $texte);
		$texte = str_replace("[li]", "<ul><li>", $texte);
		$texte = str_replace("[/li]", "</li></ul>", $texte);
		$texte = str_replace("[u]", "<span style=\"text-decoration: underline;\">", $texte);
		$texte = str_replace("[/u]", "</span>", $texte);
		$texte = str_replace("[center]", "<div style=\"text-align: center;\">", $texte);
		$texte = str_replace("[/center]", "</div>", $texte);
		$texte = str_replace("[strike]", "<span style=\"text-decoration: line-through;\">", $texte);
		$texte = str_replace("[/strike]", "</span>", $texte);
		$texte = str_replace("[blink]", "<span style=\"text-decoration: blink;\">", $texte);
		$texte = str_replace("[/blink]", "</span>", $texte);
		$texte = preg_replace("/\[flip\](.*?)\[\/flip\]/i", "<div style=\"width: 100%;filter: FlipV;\">\\1</div>", $texte);
		$texte = preg_replace("/\[blur\](.*?)\[\/blur\]/i", "<div style=\"width: 100%;filter: blur();\">\\1</div>", $texte);
		$texte = preg_replace("/\[glow\](.*?)\[\/glow\]/i", "<div style=\"width: 100%;filter: glow(color=red);\">\\1</div>", $texte);
		$texte = preg_replace("/\[glow=(.*?)\](.*?)\[\/glow\]/i", "<div style=\"width: 100%;filter: glow(color=\\1);\">\\2</div>", $texte);
		$texte = preg_replace("/\[shadow\](.*?)\[\/shadow\]/i", "<div style=\"width: 100%;filter: shadow(color=red);\">\\1</div>", $texte);
		$texte = preg_replace("/\[shadow=(.*?)\](.*?)\[\/shadow\]/i", "<div style=\"width: 100%;filter: shadow(color=\\1);\">\\2</div>", $texte);
		$texte = preg_replace("/\[email\](.*?)\[\/email\]/i", "<a href=\"mailto:\\1\">\\1</a>", $texte);
		$texte = preg_replace("/\[email=(.*?)\](.*?)\[\/email\]/i", "<a href=\"mailto:\\1\">\\2</a>", $texte);
		$texte = str_replace("[quote]", "<br /><table style=\"background: " . $bgcolor3 . ";\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\" border=\"0\"><tr><td style=\"background: #FFFFFF;color: #000000\"><div id=\"quote\" style=\"border: 0; overflow: auto;\"><b>" . _QUOTE . " :</b><br />", $texte);
		$texte = preg_replace("/\[quote=(.*?)\]/i", "<br /><table style=\"background: " . $bgcolor3 . ";\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\" border=\"0\"><tr><td style=\"background: #FFFFFF;color: #000000\"><div id=\"quote\" style=\"border: 0; overflow: auto;\"><b>\\1 " . _HASWROTE . " :</b></div>", $texte);
		$texte = str_replace("[/quote]", "</div></td></tr></table><br />", $texte);
		$texte = str_replace("[code]", "<br /><table style=\"background: " . $bgcolor3 . ";\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\" border=\"0\"><tr><td style=\"background: #FFFFFF;color: #000000\"><div id=\"code\" style=\"border: 0; overflow: auto;\"><b>" . _CODE . " :</b><pre>", $texte);
		$texte = str_replace("[/code]", "</pre></div></td></tr></table>", $texte);
		if ($imgMaxWidth>0)
		{
			if ($imgClic == TRUE) $texte = preg_replace_callback('/\[img\](.*?)\[\/img\]/i', create_function('$var', '$img = "<a href=\"" . checkimg($var[1]) . "\" class=\"thickbox\" alt=\"\"><img style=\"border: 0; overflow: auto; max-width: ' . $imgMaxWidth . 'px;  width: expression(this.scrollWidth >= ' . $imgMaxWidth . '? \'' . $imgMaxWidth . 'px\' : \'auto\');\" src=\"" . checkimg($var[1]) . "\" alt=\"\" /></a>";return $img;'), $texte);
			else $texte = preg_replace_callback('/\[img\](.*?)\[\/img\]/i', create_function('$var', '$img = "<img style=\"border: 0; overflow: auto; max-width: ' . $imgMaxWidth . 'px;  width: expression(this.scrollWidth >= ' . $imgMaxWidth . '? \'' . $imgMaxWidth . 'px\' : \'auto\');\" src=\"" . checkimg($var[1]) . "\" alt=\"\" />";return $img;'), $texte);
		}
		else
		{
			$texte = preg_replace_callback('/\[img\](.*?)\[\/img\]/i', create_function('$var', '$img = "<img style=\"border: 0;\" src=\"" . checkimg($var[1]) . "\" alt=\"\" />";return $img;'), $texte);
		}
		$texte = preg_replace_callback('/\[img=(.*?)x(.*?)\](.*?)\[\/img\]/i', create_function('$var', '$img = "<a href=\"" . checkimg($var[3]) . "\" class=\"thickbox\" alt=\"\"><img style=\"border: 0;\" width=\"" . $var[1] . "\" height=\"" . $var[2] . "\" src=\"" . checkimg($var[3]) . "\" alt=\"\" /></a>";return $img;'), $texte);
		$texte = preg_replace("/\[flash\](.*?)\[\/flash\]/i", "<object type=\"application/x-shockwave-flash\" data=\"\\1\"><param name=\"movie\" value=\"\\1\" /><param name=\"pluginurl\" value=\"http://www.macromedia.com/go/getflashplayer\" /></object>", $texte);
		$texte = preg_replace("/\[flash=(.*?)x(.*?)\](.*?)\[\/flash\]/i", "<object type=\"application/x-shockwave-flash\" data=\"\\3\" width=\"\\1\" height=\"\\2\"><param name=\"movie\" value=\"\\3\" /><param name=\"pluginurl\" value=\"http://www.macromedia.com/go/getflashplayer\" /></object>", $texte);
		$texte = preg_replace("/\[url\]www.(.*?)\[\/url\]/i", "<a href=\"http://www.\\1\" onclick=\"window.open(this.href); return false;\">\\1</a>", $texte);
		$texte = preg_replace("/\[url\](.*?)\[\/url\]/i", "<a href=\"\\1\" onclick=\"window.open(this.href); return false;\">\\1</a>", $texte);
		$texte = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/i", "<a href=\"\\1\" onclick=\"window.open(this.href); return false;\">\\2</a>", $texte);
		$texte = preg_replace("#\[s\](http://)?(.*?)\[/s\]#si", "<img style=\"border: 0;\" src=\"images/icones/\\2\" alt=\"\" />", $texte);

		$texte = ltrim($texte);
	}
	return($texte);
}

$bbUpdate = array(
	'_block' => 'content',
	'_calendar' => 'description',
	'_comment' => 'comment',
	'_contact' => 'message',
	'_defie' => 'comment',
	'_discussion' => 'texte',
	'_downloads' => 'description',
	'_downloads' => 'comp',
	'_downloads_cat' => 'description',
	'_forums_messages' => 'txt',
	'_gallery' => 'description',
	'_gallery_cat' => 'description',
	'_guestbook' => 'comment',
	'_irc_awards' => 'text',
	'_liens' => 'description',
	'_liens_cat' => 'description',
	'_news' => 'texte',
	'_recrute' => 'connection',
	'_recrute' => 'experience',
	'_recrute' => 'dispo',
	'_recrute' => 'comment',
	'_sections' => 'content',
	'_sections_cat' => 'description',
	'_serveur_cat' => 'description',
	'_shoutbox' => 'texte',
	'_userbox' => 'message',
	'_users' => 'signature',
	);

function index()
{
    style(1,0);

    echo "<div style=\"text-align: center;\"><br /><br /><br /><br /><h3>Select your language : </h3></div><br />\n"
    . "<form method=\"post\" action=\"update.php?action=install\">\n"
    . "<div style=\"text-align: center;\"><select name=\"langue\">\n";

    if ($handle = opendir("lang/"))
    {
		while ($f = readdir($handle))
		{
			if ($f != ".." && $f != "." && $f != "index.html")
			{
				list ($langfile, ,) = explode ('.', $f);
				echo "<option value=\"" . $langfile . "\">" . $langfile . "</option>\n";
			}
		}

		closedir($handle);
    }

    echo "</select>&nbsp;&nbsp;<input type=\"submit\" name=\"ok\" value=\"send\" /><br /><br /></div></form></body></html>";
}

function install()
{
	if (isset($_REQUEST['langue'])) include("lang/" . $_REQUEST['langue'] . ".lang.php");
	else
	{
		echo 'Veuillez sélectionner une langue !<br />Please select a language!<br /><br /><a href="install.php">Retour / Back</a>';
		exit();
	}

    style(2,$_REQUEST['langue']);

    echo "<div style=\"text-align: center;\"><br /><br /><br /><br /><h3>" . _WELCOMEINSTALL . "</h3><br />" . _GUIDEINSTALL . "<br /><br />\n"
	. "<input type=\"button\" name=\"upgrade\" onclick=\"document.location='update.php?action=edit_config&amp;op=update_config&amp;langue=" . $_REQUEST['langue'] . "';\" value=\"" . _UPGRADESPEED . "\" />\n"
    . "<input type=\"button\" name=\"upgrade\" onclick=\"document.location='update.php?action=edit_config_assistant&amp;op=info&amp;langue=" . $_REQUEST['langue'] . "';\" value=\"" . _UPGRADE . "\" /></div>\n";
	?>
		<script>
		interval = setInterval(suivant, 6000);
		compteur = 1;
		function suivant()
		{
			document.getElementById("slide1").style.display="none";
			document.getElementById("slide2").style.display="none";
			document.getElementById("slide3").style.display="none";
			document.getElementById("slide4").style.display="none";

			document.getElementById("slide"+compteur+"").style.display="block";

			if(compteur == 4)
			{
				compteur = 1;
			}
			else
			{
				compteur++;
			}
		}
		</script>
	<!-- Slideshow HTML -->
  <br /><hr /><br />
  <div style="width:560px;height:263px;overflow:hidden;margin:auto;">
      <div id="slide1" style="display:block;width:560px;height:263px;">
		<h2><?php echo _DECOUVERTE; ?></h2>
        <p>
			<img src="img/img_slide_01.jpg" alt="Page d'accueil de Snoupix" style=" float:right;" width="215" height="145" />
			<?php echo _DECOUVERTE1; ?>
		</p>
      </div>
      <div id="slide2" style="display:none;">
         <h2><?php echo _NEWSADMIN; ?></h2>
        <p>
			<img src="img/img_slide_02.jpg" alt="Page d'accueil de Snoupix" style=" float:left;margin-right:5px;" width="215" height="145" />
			<?php echo _NEWSADMIN1; ?>
		</p>
      </div>
      <div id="slide3" style="display:none;">
         <h2><?php echo _PROCHE; ?></h2>
        <p>
			<img src="img/img_slide_03.jpg" alt="Page d'accueil de Snoupix" style="float:right;" width="215" height="145" />
			<?php echo _PROCHE1; ?>
		</p>

      </div>
      <div id="slide4" style="display:none;">
        <h2><?php echo _SIMPLIFIE; ?></h2>
        <p>
			<img src="img/img_slide_04.jpg" alt="Page d'accueil de Snoupix" style=" float:left;margin-right:5px;" width="215" height="145" />
			<?php echo _SIMPLIFIE1; ?>
		</p>
    </div>
  </div>
  <!-- Slideshow HTML -->
	<?php
	echo "</body></html>";
}

function style($etape, $langue)
{

	if($langue == "inconnu" && $etape !=1)
	{
		?>
		<script>
		window.location='update.php'
		</script>
		<?php
	}
	if($langue != 0 && $etape != 1)
	{
		echo "test";
		include("lang/" . $langue . ".lang.php");
	}
    ?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
	<meta http-equiv="content-style-type" content="text/css" />
	<title>Installation de Nuked-klan</title>
	<link rel="shortcut icon"  href="images/favicon.ico" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="content-style-type" content="text/css" />
	<link rel="shortcut icon"  type="image/x-icon" href="/images/favicon.ico" />
		<!--                       CSS                       -->
		<!-- Reset Stylesheet -->
		<link rel="stylesheet" href="modules/Admin/css/reset.css" type="text/css" media="screen" />
		<!-- Main Stylesheet -->
		<link rel="stylesheet" href="modules/Admin/css/style.css" type="text/css" media="screen" />
		<!-- Invalid Stylesheet. This makes stuff look pretty. Remove it if you want the CSS completely valid -->
		<link rel="stylesheet" href="modules/Admin/css/invalid.css" type="text/css" media="screen" />
		<!-- Colour Schemes

		Default colour scheme is green. Uncomment prefered stylesheet to use it.

		<link rel="stylesheet" href="modules/Admin/css/blue.css" type="text/css" media="screen" />

		<link rel="stylesheet" href="modules/Admin/css/red.css" type="text/css" media="screen" />

		-->

		<!-- Internet Explorer Fixes Stylesheet -->

		<!--[if lte IE 7]>
			<link rel="stylesheet" href="modules/Admin/css/ie.css" type="text/css" media="screen" />
		<![endif]-->

		<!--                       Javascripts                       -->

		<!-- jQuery -->
		<script type="text/javascript" src="modules/Admin/scripts/jquery-1.3.2.min.js"></script>

		<!-- jQuery Configuration -->
		<script type="text/javascript" src="modules/Admin/scripts/simpla.jquery.configuration.js"></script>

		<!-- Facebox jQuery Plugin -->
		<script type="text/javascript" src="modules/Admin/scripts/facebox.js"></script>


		<!--[if IE]><script type="text/javascript" src="modules/Admin/scripts/jquery.bgiframe.js"></script><![endif]-->

		<!-- Internet Explorer .png-fix -->

		<!--[if IE 6]>
			<script type="text/javascript" src="modules/Admin/scripts/DD_belatedPNG_0.0.7a.js"></script>
			<script type="text/javascript">
				DD_belatedPNG.fix('.png_bg, img, li');
			</script>
		<![endif]-->
		<script type="text/javascript">
			<!--
			function show_progress(val, msg){
			document.getElementById('infos').innerHTML = msg;
			}
			//-->
		</script>
	</head>
	<body><div id="body-wrapper"> <!-- Wrapper for the radial gradient background -->
		<div id="sidebar"><div id="sidebar-wrapper"> <!-- Sidebar with logo and menu -->
			<!-- Logo (221px wide) -->
			<a href="http://www.nuked-klan.org"><img id="logo" src="modules/Admin/images/logo.png" alt="Simpla Admin logo" /></a>

			<ul id="main-nav">  <!-- Accordion Menu -->
				<li>
					<?php
					if ($_REQUEST['action'] == "")
					{
					?>
					<a href="#" class="nav-top-item no-submenu current">
					<?php
					}
					else
					{
					?>
					<a href="#" class="nav-top-item no-submenu">
					<?php
					}
					?>
					Etape 1
					</a>
					<ul>
						<li>
							<?php
							if ($_REQUEST['action'] == "")
							{
							?>
							<a href="update.php" class="current">
							<?php
							}
							else
							{
							?>
							<a href="update.php">
							<?php
							}
							?>
							Select a language
							</a>
						</li>
					</ul>
				</li>
				<?php
				if($etape > 1)
				{
				?>
				<li>
					<?php
					if ($_REQUEST['action'] == "install")
					{
					?>
					<a href="#" class="nav-top-item no-submenu current">
					<?php
					}
					else
					{
					?>
					<a href="#" class="nav-top-item no-submenu">
					<?php
					}
					echo _ETAPE2;
					?>
					</a>
					<ul>
						<li>
							<?php
							if ($_REQUEST['action'] == "install")
							{
							?>
							<a href="update.php?action=install&langue=<?php echo $langue; ?>" class="current">
							<?php
							}
							else
							{
							?>
							<a href="update.php?action=install&langue=<?php echo $langue; ?>">
							<?php
							}
							?>
							<?php echo _CHOIX; ?>
							</a>
						</li>
					</ul>
				</li>
				<?php
				}
				?>
				<?php
				if($etape > 2)
				{
				?>
				<li>
					<?php
					if ($etape == 3)
					{
					?>
					<a href="#" class="nav-top-item no-submenu current">
					<?php
					}
					else
					{
					?>
					<a href="#" class="nav-top-item no-submenu">
					<?php
					}
					echo _ETAPE3;
					?>
					</a>
					<ul>
						<li>
							<?php
							if ($_REQUEST['action'] == "edit_config")
							{
							?>
							<a href="update.php?action=edit_config&amp;op=save_config&amp;langue=<?php echo $langue; ?>" class="current">
							<?php
							}
							else
							{
							?>
							<a href="update.php?action=edit_config&amp;op=save_config&amp;langue=<?php echo $langue; ?>">
							<?php
							}
							?>
							<?php echo _CONFIGSQL; ?>
							</a>
						</li>
						<li>
							<?php
							if ($_REQUEST['action'] == "edit_config_assistant")
							{
							?>
							<a href="update.php?action=edit_config_assistant&amp;op=info&amp;langue=<?php echo $langue; ?>" class="current">
							<?php
							}
							else
							{
							?>
							<a href="update.php?action=edit_config_assistant&amp;op=info&amp;langue=<?php echo $langue; ?>">
							<?php
							}
							?>
							<?php echo _CONFIGSQLASSIS; ?>
							</a>
						</li>
					</ul>
				</li>
				<?php
				}
				?>
				<?php
				if($etape > 3)
				{
				?>
				<li>
					<?php
					if ($etape == 4)
					{
					?>
					<a href="#" class="nav-top-item no-submenu current">
					<?php
					}
					else
					{
					?>
					<a href="#" class="nav-top-item no-submenu">
					<?php
					}
					echo _ETAPE4;
					?>
					</a>
				</li>
				<?php
				}
				?>
			</ul>
			</div></div>
		<div id="main-content"> <!-- Main Content Section with everything -->

	<?php
}

function progress()
{
    echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"350\" border=\"0\"><tr><td>\n"
    . "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n"
    . "<tr><td colspan=\"2\"><div id=\"infos\">" . _INSTALLPROGRESS . "</div></td></tr>\n"
    . "</table></td></tr></table>\n";
}

if (isset($_GET['action']) && $_GET['action'] != "save_config" && $_GET['action'] != "update_config" && $_GET['action'] != "edit_config" && $_GET['action'] != "install")
{
    connect();
}

function redirect($url, $tps)
{
    $temps = $tps * 1000;

    echo "<script type=\"text/javascript\">\n"
    . "<!--\n"
    . "\n"
    . "function redirect() {\n"
    . "window.location='" . $url . "'\n"
    . "}\n"
    . "setTimeout('redirect()','" . $temps ."');\n"
    . "\n"
    . "// -->\n"
    . "</script>\n";
}

function connect()
{
    global $global;

    $db = mysql_connect($global['db_host'], $global['db_user'], $global['db_pass']) or die ("<div style=\"text-align: center;\">Error ! Database connexion failed<br />Check your user's name/password</div>");
    $connect= mysql_select_db($global['db_name'], $db) or die ("<div style=\"text-align: center;\">Error ! Database connexion failed<br />Check your database's name</div>");
}

function upgrade_db()
{
    global $db_prefix, $bbUpdate;

    include ("lang/" . $_REQUEST['langue'] . ".lang.php");

    $date = time();

    style(4,$_REQUEST['langue']);

    echo "<div style=\"text-align: center;\"><br /><br /><h3>" . _NKUPGRADE . "</h3><br />\n";

   $test_version=mysql_query("SELECT value FROM " . $db_prefix . "_config WHERE name = 'version'");
    list($nk_version) = mysql_fetch_array($test_version);
    $v = explode(".", $nk_version);

	$recup_mail = mysql_query("SELECT value FROM " . $db_prefix . "_config WHERE name = 'mail'");
    list($mail_admin) = mysql_fetch_array($recup_mail);

    if ($v[2] == 9)
     {
		$sql = mysql_query("SELECT value FROM " . $db_prefix . "_config WHERE name='screen'");
		$number = mysql_num_rows($sql);
		if ($number == 1)
		{
			$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('screen', 'on');";
			$req = mysql_query($sql);
			$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('contact_mail', '" . $mail_admin . "');";
			$req = mysql_query($sql);
			$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('contact_flood', '60');";
			$req = mysql_query($sql);
			mysql_query('ALTER TABLE ' . $db_prefix . '_users ADD  erreur INT(10) NOT NULL default \'0\'');
			$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_contact";
			$req = mysql_query($sql);
		}
		$sql = mysql_query("ALTER TABLE  " . $db_prefix . "_games ADD `map` TEXT NOT NULL;");
		
		if ($number == 1)
		{
		$sql = "CREATE TABLE " . $db_prefix . "_contact (
		  `id` int(11) NOT NULL auto_increment,
		  `titre` varchar(200) NOT NULL default '',
		  `message` text NOT NULL,
		  `email` varchar(80) NOT NULL default '',
		  `nom` varchar(200) NOT NULL default '',
		  `ip` varchar(50) NOT NULL default '',
		  `date` varchar(30) NOT NULL default '',
		  PRIMARY KEY  (`id`),
		  KEY `titre` (`titre`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
		$req = mysql_query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_packages` (
		  `file` varchar(100) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `author` varchar(255) NOT NULL,
		  `link` varchar(255) NOT NULL,
		  `active` tinyint(1) NOT NULL,
		  PRIMARY KEY (`file`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
		$req = mysql_query($sql);


		$sql = "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_tmpses` (
		  `session_id` varchar(64) NOT NULL,
		  `session_vars` text NOT NULL,
		  `session_start` bigint(20) NOT NULL,
		  PRIMARY KEY (`session_id`)
		  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
		$req = mysql_query($sql);
		}
	// FIN DE L'UPDATE
	$error = 0;

	mysql_query('INSERT INTO ' . $db_prefix . '_modules (`nom`, `niveau`, `admin`) VALUES (\'PackageMgr\', 9, 9)');

	if (is_file("modules/404/lang/turskish.lang.php"))
	{
	    $path_3 = "modules/404/lang/turskish.lang.php";
	    $filesys3 = str_replace("/", "\\", $path_3);
	    @chmod ($path_3, 0775);
	    @unlink($path_3);
	    @system("del $filesys3");
	}
	if (is_file("update.php"))
	{
	    $path_1 = "update.php";
	    $filesys1 = str_replace("/", "\\", $path_1);
	    @chmod ($path_1, 0775);
	    @unlink($path_1);
	    @system("del $filesys1");
	    if (is_file($path_1)) $error++;
	}

	if (is_file("install.php"))
	{
	    $path_2="install.php";
	    $filesys2 = str_replace("/", "\\", $path_2);
	    @chmod ($path_2, 0775);
	    @unlink($path_2);
	    @system("del $filesys2");
	    if (is_file($path_2)) $error++;
	}

	if ($error > 0)
	{
	    echo "<div class=\"notification attention png_bg\">\n"
		. "<div>\n"
		. "" . _CONGRATULATION . "<br />" . _ERRORCHMOD . "<br /><br /><a href=\"index.php\">" . _GOHOME . "</a>\n"
		. "</div>\n"
		. "</div>\n";
	    echo "</body></html>";
	}
	else
	{
	    echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _CONGRATULATION . "<br />" . _REDIRECT . "<br /><br /><a href=\"index.php\">" . _CLICIFNO . "</a>\n"
		. "</div>\n"
		. "</div>\n";
	    echo "</body></html>";
		redirect("index.php", 5);
	}
     }
     else if ($v[1] == 6 || $v[1] == 7)
    {
	progress();

	echo "<script>show_progress('&nbsp;&nbsp;&nbsp;','upgrade');</script>";

	if ($v[2] != 9)
	{
		$sql_user = mysql_query("SELECT id, pass FROM " . $db_prefix . "_users");
		while (list($userid, $userpass) = mysql_fetch_row($sql_user))
		{
			mysql_query('UPDATE ' . $db_prefix . '_users SET pass=\'' . UpdatePassCrypt($userpass) . '\' WHERE id = \'' . $userid . '\'');
		}
	}
     foreach ($bbUpdate as $table => $field){
     	$sql = "SELECT DISTINCT $field FROM {$db_prefix}{$table}";
     	$result = mysql_query($sql);
     	while($row = mysql_fetch_row($result)){
     		$row[1] = BBcode($row[0]);
     		$row = array_map('mysql_escape_string', $row);
     		$sql = "UPDATE {$db_prefix}{$table} SET $field = '$row[1]' WHERE $field = '$row[0]'";
     		mysql_query($sql);
     	}
     }

	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('screen', 'on');";
	$req = mysql_query($sql);

	mysql_query('UPDATE ' . $db_prefix . '_config SET value = \'1.7.9\' WHERE name = \'version\'');
	mysql_query('UPDATE ' . $db_prefix . '_config SET value = \'Impact_Nk\' WHERE name = \'theme\'');
	mysql_query('UPDATE ' . $db_prefix . '_config SET value = \'quakenet.org\' WHERE name = \'irc_serv\'');

	mysql_query('ALTER TABLE ' . $db_prefix . '_users ADD  erreur INT(10) NOT NULL default \'0\'');

	mysql_query('INSERT INTO ' . $db_prefix . '_modules (`nom`, `niveau`, `admin`) VALUES (\'Stats\', 0, 2)');
	mysql_query('INSERT INTO ' . $db_prefix . '_modules (`nom`, `niveau`, `admin`) VALUES (\'Contact\', 0, 3)');
	mysql_query('INSERT INTO ' . $db_prefix . '_modules (`nom`, `niveau`, `admin`) VALUES (\'PackageMgr\', 9, 9)');

	mysql_query('ALTER TABLE ' . $db_prefix . '_banned ADD `date` VARCHAR(20)  NULL AFTER `email`');
	mysql_query('ALTER TABLE ' . $db_prefix . '_banned ADD `dure` VARCHAR(20)  NULL AFTER `date`');

	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('contact_mail', '');";
	$req = mysql_query($sql);
	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('contact_flood', '60');";
	$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_contact";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_contact (
	  `id` int(11) NOT NULL auto_increment,
	  `titre` varchar(200) NOT NULL default '',
	  `message` text NOT NULL,
	  `email` varchar(80) NOT NULL default '',
	  `nom` varchar(200) NOT NULL default '',
	  `ip` varchar(50) NOT NULL default '',
	  `date` varchar(30) NOT NULL default '',
	  PRIMARY KEY  (`id`),
	  KEY `titre` (`titre`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_tmpses` (
		  `session_id` varchar(64) NOT NULL,
		  `session_vars` text NOT NULL,
		  `session_start` bigint(20) NOT NULL,
		  PRIMARY KEY (`session_id`)
		  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
		$req = mysql_query($sql);

		$sql = "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_packages` (
		  `file` varchar(100) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `author` varchar(255) NOT NULL,
		  `link` varchar(255) NOT NULL,
		  `active` tinyint(1) NOT NULL,
		  PRIMARY KEY (`file`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
		$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_action";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_action (
	  `id` int(11) NOT NULL auto_increment,
	  `date` varchar(30) NOT NULL default '0',
	  `pseudo`  text NOT NULL,
	  `action`  text NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_notification";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_notification (
	  `id` int(11) NOT NULL auto_increment,
	  `date` varchar(30) NOT NULL default '0',
	  `type`  text NOT NULL,
	  `texte`  text NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_erreursql";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_erreursql (
	  `id` int(11) NOT NULL auto_increment,
	  `date` varchar(30) NOT NULL default '0',
	  `lien`  text NOT NULL,
	  `texte`  text NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_discussion";
	$req= mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_discussion (
	  `id` int(11) NOT NULL auto_increment,
	  `date` varchar(30) NOT NULL default '0',
	  `pseudo`  text NOT NULL,
	  `texte`  text NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_comment_mod";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_comment_mod (
	  `id` int(11) NOT NULL auto_increment,
	  `module` text NOT NULL,
	  `active` int(1) NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

	echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_comment_mod</b>" . _CREATES . "&nbsp;');</script>";

	$sql = "INSERT INTO " . $db_prefix . "_comment_mod (`id`, `module`, `active`) VALUES
	(1, 'news', 1),
	(2, 'download', 1),
	(3, 'links', 1),
	(4, 'survey', 1),
	(5, 'wars', 1),
	(6, 'gallery', 1),
	(7, 'sections', 1);";
	$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_style";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_style` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `texte` text COLLATE latin1_german2_ci NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci AUTO_INCREMENT=1 ;";
	$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_editeur";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_editeur` (
	  `name` varchar(255) COLLATE latin1_german2_ci NOT NULL DEFAULT '',
	  `value` text COLLATE latin1_german2_ci NOT NULL,
	  PRIMARY KEY (`name`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

	$sql = "INSERT INTO `" . $db_prefix . "_editeur` (`name`, `value`) VALUES
	('couleur', ''),
	('bouton', 'top'),
	('status', 'bottom'),
	('ligne1', 'save,newdocument,restoredraft,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,print,|,fullscreen,|,preview,|,help,code'),
	('ligne2', 'styleselect,fontselect,fontsizeselect,|,link,unlink,anchor,|,emotions,image,forecolor,backcolor'),
	('ligne3', 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,hr,|,outdent,indent,|,removeformat,|,spellchecker'),
	('ligne4', 'tablecontrols,|,blockquote,sub,sup,|,charmap,pagebreak'),
	('ligne2b', 'styleselect,fontselect,fontsizeselect,barre,link,unlink,anchor,barre,emotions,image,forecolor,backcolor'),
	('ligne3b', 'bold,italic,underline,strikethrough,barre,justifyleft,justifycenter,justifyright,justifyfull,barre,bullist,numlist,hr,barre,outdent,indent,barre,removeformat,barre,spellchecker'),
	('ligne4b', 'tablecontrols,barre,blockquote,sub,sup,barre,charmap,pagebreak'),
	('ligne1b', 'save,newdocument,restoredraft,barre,cut,copy,paste,pastetext,pasteword,barre,undo,redo,barre,print,barre,fullscreen,barre,preview,barre,help,code');";
	$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_match";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_match (
	  `warid` int(10) NOT NULL auto_increment,
	  `etat` int(1) NOT NULL default '0',
	  `team` int(11) NOT NULL default '0',
	  `game` int(11) NOT NULL default '0',
	  `adversaire` text,
	  `url_adv` varchar(60) default NULL,
	  `pays_adv` varchar(50) NOT NULL default '',
	  `type` varchar(100) default NULL,
	  `style` varchar(100) NOT NULL default '',
	  `date_jour` int(2) default NULL,
	  `date_mois` int(2) default NULL,
	  `date_an` int(4) default NULL,
	  `heure` varchar(10) NOT NULL default '',
	  `map` text,
	  `tscore_team` float default NULL,
	  `tscore_adv` float default NULL,
	  `score_team` text NOT NULL,
	  `score_adv` text NOT NULL,
	  `report` text,
	  `auteur` varchar(50) default NULL,
	  `url_league` varchar(100) default NULL,
	  `dispo` text,
	  `pas_dispo` text,
	  PRIMARY KEY  (`warid`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req  =mysql_query($sql);

	//SPECIAL 1.7.6 => 1.7.9

	if ($v[1] == 6)
	{
		//MODULE USER, COMMENT & USERBOX
	$sql = mysql_query("ALTER TABLE " . $db_prefix . "_news ADD `auteur_id` varchar (20) not null AFTER `auteur`;");
	$sql = mysql_query("ALTER TABLE " . $db_prefix . "_comment ADD `autor_id` varchar (20) not null AFTER `autor`;");

	$sql_user = mysql_query("SELECT pseudo, id FROM " . $db_prefix . "_users");
	while (list($pseudo, $auteur_id) = mysql_fetch_row($sql_user))
	{
	    $upd_news = mysql_query("UPDATE " . $db_prefix . "_news SET auteur_id = '" . $auteur_id . "' WHERE auteur = '" . $pseudo . "'");
	    $upd_comment = mysql_query("UPDATE " . $db_prefix . "_comment SET autor_id = '" . $auteur_id . "' WHERE autor = '" . $pseudo . "'");
	    $upd_mess_for = mysql_query("UPDATE " . $db_prefix . "_userbox SET user_from = '" . $auteur_id . "' WHERE user_from = '" . $pseudo . "'");
	    $upd_mess_from = mysql_query("UPDATE " . $db_prefix . "_userbox SET user_for = '" . $auteur_id . "' WHERE user_for = '" . $pseudo . "'");
	}



	//MODULE SURVEY
	$sql = mysql_query("ALTER TABLE " . $db_prefix . "_sondage ADD `niveau` int (1) not null AFTER `date`;");
	$sql = mysql_query("ALTER TABLE " . $db_prefix . "_sondage_check ADD `pseudo` varchar (50) not null AFTER `ip`;");


	//PREFERENCE
	$sql = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'validation'");
	$test=mysql_num_rows($sql);
	if ($test == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('validation', 'auto');");

	$sql2 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'forum_field_max'");
	$test1 = mysql_num_rows($sql2);
	if ($test1 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_field_max', '10');");

	$sql3 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'forum_file'");
	$test2 = mysql_num_rows($sql3);
	if ($test2 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_file', 'on');");

	$sql4 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'forum_file_level'");
	$test3 = mysql_num_rows($sql4);
	if ($test3 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_file_level', '1');");

	$sql5 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'forum_file_maxsize'");
	$test4 = mysql_num_rows($sql5);
	if ($test4 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_file_maxsize', '1000');");

	$sql6 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'forum_rank_team'");
	$test5 = mysql_num_rows($sql6);
	if ($test5 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_rank_team', 'off');");

	$sql7 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'level_analys'");
	$test6 = mysql_num_rows($sql7);
	if ($test6 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('level_analys', '-1');");

	$sql8 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'visit_delay'");
	$test7 = mysql_num_rows($sql8);
	if ($test7 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('visit_delay', '10');");

	$sql9 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'max_sections'");
	$test8 = mysql_num_rows($sql9);
	if ($test8 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_sections', '10');");

	$sql10 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'max_wars'");
	$test9 = mysql_num_rows($sql10);
	if ($test9 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_wars', '30');");

	$sql11 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'birthday'");
	$test10 = mysql_num_rows($sql11);
	if ($test10 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('birthday', 'all');");

	$sql12 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'avatar_upload'");
	$test11 = mysql_num_rows($sql12);
	if ($test11 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('avatar_upload', 'on');");

	$sql13 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'avatar_url'");
	$test12 = mysql_num_rows($sql13);
	if ($test12 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('avatar_url', 'on');");

	$sql14=mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'user_delete'");
	$test13=mysql_num_rows($sql14);
	if ($test13 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('user_delete', 'on');");

	$sql15 = mysql_query("SELECT name FROM " . $db_prefix . "_config WHERE name = 'nk_status'");
	$test14 = mysql_num_rows($sql15);
	if ($test14 == 0) $sql = mysql_query("INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('nk_status', 'open');");

	$upd = mysql_query("UPDATE " . $db_prefix . "_config SET value = '1.7' WHERE name = 'version'");


	//MODULE FORUM
	$sql = "CREATE TABLE " . $db_prefix . "_forums_poll (
	  `id` int(11) NOT NULL auto_increment,
	  `thread_id` int(11) NOT NULL default '0',
	  `titre` varchar(255) NOT NULL default '',
	  PRIMARY KEY  (`id`),
	  KEY `thread_id` (`thread_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_forums_options (
	  `id` int(11) NOT NULL default '0',
	  `poll_id` int(11) NOT NULL default '0',
	  `option_text` varchar(255) NOT NULL default '',
	  `option_vote` int(11) NOT NULL default '0',
	  KEY `poll_id` (`poll_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_forums_vote (
	  `poll_id` int(11) NOT NULL default '0',
	  `auteur_id` varchar(20) NOT NULL default '',
	  `auteur_ip` varchar(20) NOT NULL default '',
	  KEY `poll_id` (`poll_id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_forums ADD `level_poll` INT(1) DEFAULT '0' NOT NULL AFTER `ordre`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_forums ADD `level_vote` INT(1) DEFAULT '0' NOT NULL AFTER `level_poll`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_forums_threads ADD `sondage` INT(1) DEFAULT '0' NOT NULL AFTER `annonce`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_forums_messages ADD `file` VARCHAR (200) NOT NULL AFTER `forum_id`;";
	$req = mysql_query($sql);

	$upd = mysql_query("UPDATE " . $db_prefix . "_forums SET level_poll = 1, level_vote = 1");


	// MODULE STATS
	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_stats_visitor";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_stats_visitor (
	  `id` int(11) NOT NULL auto_increment,
	  `user_id` varchar(20) NOT NULL default '',
	  `ip` varchar(15) NOT NULL default '',
	  `host` varchar(100) NOT NULL default '',
	  `browser` varchar(50) NOT NULL default '',
	  `os` varchar(50) NOT NULL default '',
	  `referer` varchar(200) NOT NULL default '',
	  `day` int(2) NOT NULL default '0',
	  `month` int(2) NOT NULL default '0',
	  `year` int(4) NOT NULL default '0',
	  `hour` int(2) NOT NULL default '0',
	  `date` varchar(30) NOT NULL default '',
	  PRIMARY KEY  (`id`),
	  KEY `user_id` (`user_id`),
	  KEY `host` (`host`),
	  KEY `browser` (`browser`),
	  KEY `os` (`os`),
	  KEY `referer` (`referer`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);



	//MODULE GALLERY
	$sql = "ALTER TABLE " . $db_prefix . "_gallery ADD `date` varchar(12) default '' NOT NULL;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_gallery ADD `count` varchar(10) default '0' NOT NULL;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_gallery ADD `autor` TEXT NOT NULL AFTER `count`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_gallery_cat ADD `position` INT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ;";
	$req = mysql_query($sql);

	$upd = mysql_query("UPDATE " . $db_prefix . "_gallery SET date = '" . $date . "'");



	//MODULE DOWNLOAD
	$sql = "ALTER TABLE " . $db_prefix . "_downloads ADD `url2` varchar(200) default '' NOT NULL;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_downloads ADD `broke` int(11) NOT NULL default '0';";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_downloads ADD `url3` varchar(200) NOT NULL default '';";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_downloads ADD  `level` int(1) NOT NULL default '0';";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_downloads ADD  `hit` int(11) NOT NULL default '0';";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_downloads ADD `edit` varchar(12) NOT NULL default '';";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_downloads ADD `screen` varchar(200) NOT NULL default '';";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_downloads ADD  `autor` text NOT NULL;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_downloads ADD  `url_autor` varchar(200) NOT NULL default '';";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_downloads ADD `comp` text NOT NULL;";
	$req = mysql_query($sql);

	$upd = mysql_query("UPDATE " . $db_prefix . "_config SET value = '10' WHERE name = 'max_download';");

	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('hide_download', 'on');";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_downloads_cat ADD `position` INT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ;";
	$req = mysql_query($sql);

	$upd = "UPDATE " . $db_prefix . "_downloads SET taille = taille * 1000;";
	$req = mysql_query($upd);


	//MODULE SECTIONS
	$sql = "ALTER TABLE " . $db_prefix . "_sections RENAME TO " . $db_prefix . "_sections_cat;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_seccont RENAME TO " . $db_prefix . "_sections;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_sections ADD `date` VARCHAR( 12 ) NOT NULL ;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_sections ADD `autor` TEXT NOT NULL AFTER `content`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_sections ADD `autor_id` VARCHAR( 20 ) NOT NULL AFTER `autor`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_sections_cat ADD `position` INT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ;";
	$req = mysql_query($sql);

	$upd = mysql_query("UPDATE " . $db_prefix . "_sections SET date = '" . $date . "'");



	// BAN
	$sql = "ALTER TABLE " . $db_prefix . "_banned ADD `pseudo` VARCHAR (50) NOT NULL AFTER `ip`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_banned ADD `email` VARCHAR (80) NOT NULL AFTER `pseudo`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_banned ADD `texte` text NOT NULL AFTER `email`;";
	$req = mysql_query($sql);


	// MODULE TEAM
	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_games_prefs";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE " . $db_prefix . "_games_prefs (
	  `id` int(11) NOT NULL auto_increment,
	  `game` int(11) NOT NULL default '0',
	  `user_id` varchar(20) NOT NULL default '',
	  `pref_1` text NOT NULL,
	  `pref_2` text NOT NULL,
	  `pref_3` text NOT NULL,
	  `pref_4` text NOT NULL,
	  `pref_5` text NOT NULL,
	  PRIMARY KEY  (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
	$req = mysql_query($sql);

	$sql="ALTER TABLE " . $db_prefix . "_team CHANGE `tag` `tag` TEXT NOT NULL";
	$req=mysql_query($sql);

	$sql="ALTER TABLE " . $db_prefix . "_team ADD `tag2` TEXT NOT NULL AFTER `tag`";
	$req=mysql_query($sql);

	$sql="ALTER TABLE " . $db_prefix . "_team ADD `game` INT(11) NOT NULL";
	$req=mysql_query($sql);



	// MODULE USER
	$sql = "ALTER TABLE " . $db_prefix . "_users_detail ADD `sexe` VARCHAR (20) NOT NULL AFTER `age`";
	$req = mysql_query($sql);

	$upd = mysql_query("UPDATE " . $db_prefix . "_users_detail SET age = ''");



	// MODULE LIENS
	$sql = "ALTER TABLE " . $db_prefix . "_liens ADD `webmaster` TEXT NOT NULL AFTER `cat`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_liens ADD `country` VARCHAR (50) NOT NULL AFTER `webmaster`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_liens ADD `broke` INT(11) DEFAULT '0' NOT NULL AFTER `count`;";
	$req = mysql_query($sql);

	$sql = "ALTER TABLE " . $db_prefix . "_liens_cat ADD `position` INT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ;";
	$req = mysql_query($sql);



	// MODULE RECRUIT & DEFY
	$sql_defie=mysql_query("SELECT charte, mail FROM " . $db_prefix . "_defie_pref");
	list($defie_charte, $defie_email) = mysql_fetch_array($sql_defie);

	$sql_recrute = mysql_query("SELECT recrute, charte, mail FROM " . $db_prefix . "_recrute_pref");
	list($recrute, $recrute_charte, $recrute_email) = mysql_fetch_array($sql_recrute);

	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('recrute', '" . $recrute . "');";
	$req = mysql_query($sql);

	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('recrute_charte', '" . $recrute_charte . "');";
	$req = mysql_query($sql);

	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('recrute_mail', '" . $recrute_email . "');";
	$req = mysql_query($sql);

	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('recrute_inbox', '');";
	$req = mysql_query($sql);

	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('defie_charte', '" . $defie_charte . "');";
	$req = mysql_query($sql);

	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('defie_mail', '" . $defie_email . "');";
	$req = mysql_query($sql);

	$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('defie_inbox', '');";
	$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_defie_pref";
	$req = mysql_query($sql);

	$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_recrute_pref";
	$req = mysql_query($sql);




	// MODULE WARS
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_match CHANGE `score_team` `score_team` INT( 10 ) NOT NULL");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_match CHANGE `score_adv` `score_adv` INT( 10 ) NOT NULL");




	// OPTIMISATION MySQL
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_banned DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_banned ADD PRIMARY KEY ( `id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_block DROP INDEX `bid` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_block ADD PRIMARY KEY ( `bid` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_calendar DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_calendar ADD PRIMARY KEY ( `id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_comment DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_comment ADD PRIMARY KEY ( `id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_comment ADD INDEX ( `im_id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_comment CHANGE `autor_ip` `autor_ip` VARCHAR( 20 ) DEFAULT NULL ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_config ADD PRIMARY KEY ( `name` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_downloads DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_downloads ADD PRIMARY KEY ( `id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_downloads ADD INDEX ( `type` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_downloads_cat ADD INDEX ( `parentid` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_fichiers_joins DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_fichiers_joins PRIMARY KEY ( `id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_fichiers_joins ADD INDEX ( `im_id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums ADD INDEX ( `cat` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_messages DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_messages ADD INDEX ( `auteur_id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_messages ADD INDEX ( `thread_id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_messages ADD INDEX ( `forum_id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_rank DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_rank ADD PRIMARY KEY ( `id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_read ADD INDEX ( `user_id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_read ADD INDEX ( `thread_id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_read ADD INDEX ( `forum_id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_threads DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_threads ADD INDEX ( `auteur_id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_forums_threads ADD INDEX ( `forum_id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_gallery ADD INDEX ( `cat` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_gallery_cat ADD INDEX ( `parentid` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_irc_awards DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_irc_awards ADD PRIMARY KEY ( `id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_liens DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_liens ADD PRIMARY KEY ( `id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_liens ADD INDEX ( `cat` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_liens_cat ADD INDEX ( `parentid` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_modules DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_modules ADD PRIMARY KEY ( `id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_nbconnecte DROP PRIMARY KEY ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_nbconnecte ADD PRIMARY KEY ( `IP` , `user_id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_news DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_news ADD PRIMARY KEY ( `id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_news ADD INDEX ( `cat` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_news_cat DROP INDEX `nid` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_news_cat ADD PRIMARY KEY ( `nid` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_recrute ADD INDEX ( `game` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_sections ADD INDEX ( `secid` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_sections_cat ADD INDEX ( `parentid` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_serveur ADD INDEX ( `game` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_serveur ADD INDEX ( `cat` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_serveur_cat DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_serveur_cat ADD PRIMARY KEY ( `cid` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_sessions DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_sessions ADD PRIMARY KEY ( `id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_sessions ADD INDEX ( `user_id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_shoutbox DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_shoutbox ADD PRIMARY KEY ( `id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_sondage_check ADD INDEX ( `sid` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_sondage_data ADD INDEX ( `sid` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_stats CHANGE `nom` `nom` VARCHAR( 50 ) NOT NULL ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_stats CHANGE `type` `type` VARCHAR( 50 ) NOT NULL ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_stats ADD PRIMARY KEY ( `nom` , `type` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_suggest DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_suggest ADD PRIMARY KEY ( `id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_suggest ADD INDEX ( `user_id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_userbox ADD INDEX ( `user_from` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_userbox ADD INDEX ( `user_for` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_users DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_users ADD PRIMARY KEY ( `id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_users ADD INDEX ( `team` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_users ADD INDEX ( `team2` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_users ADD INDEX ( `team3` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_users ADD INDEX ( `rang` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_users ADD INDEX ( `game` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_users_detail ADD PRIMARY KEY ( `user_id` ) ");

	$req = mysql_query("ALTER TABLE " . $db_prefix . "_vote DROP INDEX `id` ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_vote ADD PRIMARY KEY ( `id` ) ");
	$req = mysql_query("ALTER TABLE " . $db_prefix . "_vote ADD INDEX ( `vid` ) ");

	$req = mysql_query("ALTER TABLE  " . $db_prefix . "_games ADD `map` TEXT NOT NULL;");
	// BLOCKS
	$del = mysql_query("DELETE FROM " . $db_prefix . "_block WHERE type = 'who_on_line'");
	$del = mysql_query("DELETE FROM " . $db_prefix . "_block WHERE module = 'Calendar'");
	$del = mysql_query("DELETE FROM " . $db_prefix . "_block WHERE module = 'User'");

	$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES ('', 1, 4, '', '" . _BLOKPARTNERS . "', '<div style=\"text-align: center;padding: 10px;\"><a href=\"http://www.nuked-klan.org\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"images/ban.png\" alt=\"\" title=\"Nuked-klaN CMS\" /></a></div><div style=\"text-align: center;padding: 10px;\"><a href=\"http://www.nitroserv.fr\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"http://www.nitroserv.com/images/logo_88x31.jpg\" alt=\"\" title=\"Location de serveurs de jeux\" /></a></div>', 'html', 0, 'Tous');";
	$req = mysql_query($sql);

		if (is_file("modules/Search/rubriques/Articles.php"))
		{
			$path = "modules/Search/rubriques/Articles.php";
			$filesys = str_replace("/", "\\", $path);
			@chmod ($path, 0775);
			@unlink($path);
			@system("del $filesys");
		}
	}
	// FIN DE L'UPDATE
	$error = 0;

	if (is_file("modules/404/lang/turskish.lang.php"))
	{
	    $path_3 = "modules/404/lang/turskish.lang.php";
	    $filesys3 = str_replace("/", "\\", $path_3);
	    @chmod ($path_3, 0775);
	    @unlink($path_3);
	    @system("del $filesys3");
	}
	if (is_file("update.php"))
	{
	    $path_1 = "update.php";
	    $filesys1 = str_replace("/", "\\", $path_1);
	    @chmod ($path_1, 0775);
	    @unlink($path_1);
	    @system("del $filesys1");
	    if (is_file($path_1)) $error++;
	}

	if (is_file("install.php"))
	{
	    $path_2="install.php";
	    $filesys2 = str_replace("/", "\\", $path_2);
	    @chmod ($path_2, 0775);
	    @unlink($path_2);
	    @system("del $filesys2");
	    if (is_file($path_2)) $error++;
	}

	if ($error > 0)
	{
	    echo "<div class=\"notification attention png_bg\">\n"
		. "<div>\n"
		. "" . _CONGRATULATION . "<br />" . _ERRORCHMOD . "<br /><br /><a href=\"index.php\">" . _GOHOME . "</a>\n"
		. "</div>\n"
		. "</div>\n";
	    echo "</body></html>";
	}
	else
	{
	    echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _CONGRATULATION . "<br />" . _REDIRECT . "<br /><br /><a href=\"index.php\">" . _CLICIFNO . "</a>\n"
		. "</div>\n"
		. "</div>\n";
	    echo "</body></html>";
		redirect("index.php", 5);
	}

    }
    else
    {
		echo "<div class=\"notification error png_bg\">\n"
		. "<div>\n"
		. "" . _BADVERSION . ""
		. "</div>\n"
		. "</div>\n";
		echo "</body></html>";
		redirect("index.php", 5);
    }
}

function edit_config($op)
{

    include ("lang/" . $_REQUEST['langue'] . ".lang.php");

    style(3,$_REQUEST['langue']);

    include("conf.inc.php");

    echo "<div><a href=\"#\" onclick=\"javascript:window.open('help/" . $_REQUEST['langue'] . "/install.html','Help','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=300');return(false)\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a></div>\n"
    . "<div style=\"text-align: center;\"><br /><br /><h3>" . _UPGRADE . "</h3></div>\n"
    . "<form method=\"post\" action=\"update.php?action=" . $op . "\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td colspan=\"2\" align=\"center\"><b>" . _CONFIG . "</b></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"langue\" value=\"" . $_REQUEST['langue'] . "\" /></td></tr>\n"
    . "<tr><td>" . _DBHOST . " :</td><td><input type=\"text\" name=\"db_host\" size=\"40\" value=\""  . $global['db_host'] . "\" /></td></tr>\n"
    . "<tr><td>" . _DBUSER . " : </td><td><input type=\"text\" name=\"db_user\" size=\"40\" value=\"" . $global['db_user'] . "\" /></td></tr>\n"
    . "<tr><td>" . _DBPASS . " :</td><td><input type=\"password\" name=\"db_pass\" size=\"10\" /></td></tr><tr><td>\n"
    . "<tr><td>" . _DBPREFIX . " :</td><td><input type=\"text\" name=\"prefix\" size=\"10\" value=\"" . $db_prefix . "\" /></td></tr>\n"
    . "<tr><td>" . _DBNAME . " :</td><td><input type=\"text\" name=\"db_name\" size=\"10\" value=\"" . $global['db_name'] . "\" /></td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br />" . _CHMOD . "<br /><br /><input type=\"submit\" name=\"ok\" value=\"" . _NEXT . "\" /></div></form></body></html>";
}

function edit_config_assistant($op)
{

	if($op == "info")
	{
		include ("lang/" . $_REQUEST['langue'] . ".lang.php");
		style(3,$_REQUEST['langue']);

	?>
		<div style="text-align:center;"><img src="img/nk.png"/><h2><b><?php echo _NEWNK179; ?></b></h2></div>
		<br />
		<p><b><?php echo _SECURITE; ?>:</b><br />
		<?php echo _SECURITE1; ?>
		<br /><br /></p>
		<p><b><?php echo _OPIMISATION; ?>:</b><br />
		<?php echo _OPIMISATION1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _ADMINNISTRATION; ?>:</b><br />
		<?php echo _ADMINNISTRATION1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _BANTEMP; ?>:</b><br />
		<?php echo _BANTEMP1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _SHOUTBOX; ?>:</b><br />
		<?php echo _SHOUTBOX1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _ERORSQL; ?>:</b><br />
		<?php echo _ERORSQL1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _MULTIWARS; ?>:</b><br />
		<?php echo _MULTIWARS1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _COMSYS; ?>:</b><br />
		<?php echo _COMSYS1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _EDITWYS; ?>:</b><br />
		<?php echo _EDITWYS1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _MISAJ; ?>:</b><br />
		<?php echo _MISAJ1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _CONT; ?>:</b><br />
		<?php echo _CONT1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _ERREURPASS; ?>:</b><br />
		<?php echo _ERREURPASS1; ?>
		<br />
		<br /></p><p>
		<b><?php echo _DIFFMODIF; ?>:</b><br />
		<?php echo _DIFFMODIF1; ?>
		<br /></p><br />
	<?php
		echo "<div style=\"text-align: center;\"><input type=\"button\" onclick=\"document.location='update.php?action=edit_config_assistant&amp;op=update_config&amp;langue=" . $_REQUEST['langue'] . "';\" name=\"ok\" value=\"" . _NEXT . "\" /></div></body></html>";
	}
	else if($op == "update_config")
	{
    include ("lang/" . $_REQUEST['langue'] . ".lang.php");

    style(3,$_REQUEST['langue']);

    include("conf.inc.php");

    echo "<div><a href=\"#\" onclick=\"javascript:window.open('help/" . $_REQUEST['langue'] . "/install.html','Help','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=300');return(false)\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a></div>\n"
    . "<div style=\"text-align: center;\"><br /><br /><h3>" . _UPGRADE . "</h3></div>\n"
    . "<form method=\"post\" action=\"update.php?action=" . $op . "\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td colspan=\"2\" align=\"center\"><b>" . _CONFIG . "</b></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"langue\" value=\"" . $_REQUEST['langue'] . "\" /></td></tr>\n"
    . "<tr><td>" . _DBHOST . " :</td><td><input type=\"text\" name=\"db_host\" size=\"40\" value=\""  . $global['db_host'] . "\" /></td></tr>\n"
	. "<tr><td><img src=\"img/tuyau.png\"/></td><td>L'host mysql correspond à l'url du serveur msql, du genre nuked-klan.org. Souvent les hébergeurs utilisent comme adresse msql localhost.</td></tr>\n"
    . "<tr><td>" . _DBUSER . " : </td><td><input type=\"text\" name=\"db_user\" size=\"40\" value=\"" . $global['db_user'] . "\" /></td></tr>\n"
	. "<tr><td><img src=\"img/tuyau.png\"/></td><td>L'host mysql correspond à l'url du serveur msql, du genre nuked-klan.org. Souvent les hébergeurs utilisent comme adresse msql localhost.</td></tr>\n"
    . "<tr><td>" . _DBPASS . " :</td><td><input type=\"password\" name=\"db_pass\" size=\"10\" /></td></tr>\n"
	. "<tr><td><img src=\"img/tuyau.png\"/></td><td>L'host mysql correspond à l'url du serveur msql, du genre nuked-klan.org. Souvent les hébergeurs utilisent comme adresse msql localhost.</td></tr>\n"
    . "<tr><td>" . _DBPREFIX . " :</td><td><input type=\"text\" name=\"prefix\" size=\"10\" value=\"" . $db_prefix . "\" /></td></tr>\n"
	. "<tr><td><img src=\"img/tuyau.png\"/></td><td>L'host mysql correspond à l'url du serveur msql, du genre nuked-klan.org. Souvent les hébergeurs utilisent comme adresse msql localhost.</td></tr>\n"
    . "<tr><td>" . _DBNAME . " :</td><td><input type=\"text\" name=\"db_name\" size=\"10\" value=\"" . $global['db_name'] . "\" /></td></tr>\n"
	. "<tr><td><img src=\"img/tuyau.png\"/></td><td>L'host mysql correspond à l'url du serveur msql, du genre nuked-klan.org. Souvent les hébergeurs utilisent comme adresse msql localhost.</td></tr>\n"
	. "</table>\n"
    . "<div style=\"text-align: center;\"><br />" . _CHMOD . "<br /><br /><input type=\"submit\" name=\"ok\" value=\"" . _NEXT . "\" /></div></form></body></html>";
	}
}

function update_config($vars)
{
    global $nuked;
    include ("lang/" . $vars['langue'] . ".lang.php");


    $db = @mysql_connect($vars['db_host'], $vars['db_user'], $vars['db_pass']);
    $connect= @mysql_select_db($vars['db_name'], $db);

    if(!$db || !$connect)
    {
	style(3,$vars['langue']);
	echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "" . _ERRORCONNECTDB . ""
	. "</div>\n"
	. "</div>\n";
	echo "</body></html>";
	redirect("update.php?action=edit_config_assistant&op=update_config&langue=" . $vars['langue'], 5);
    }
    else
    {

	$content="<?php\n"
	. "//-------------------------------------------------------------------------//\n"
	. "//  Nuked-KlaN - PHP Portal                                                //\n"
	. "//  http://www.nuked-klan.org                                              //\n"
	. "//-------------------------------------------------------------------------//\n"
	. "//  This program is free software. you can redistribute it and/or modify   //\n"
	. "//  it under the terms of the GNU General Public License as published by   //\n"
	. "//  the Free Software Foundation; either version 2 of the License.         //\n"
	. "//-------------------------------------------------------------------------//\n"
	. "\n"
	. "\$global['db_host']  = '" . $vars['db_host'] . "';\n"
	. "\$global['db_user']  = '" . $vars['db_user'] . "';\n"
	. "\$global['db_pass']  = '" . $vars['db_pass'] . "';\n"
	. "\$global['db_name'] = '" . $vars['db_name'] . "';\n"
	. "\$db_prefix = '" . $vars['prefix'] . "';\n"
	. "\n"
	. "define('NK_INSTALLED', true);\n"
	. "define('NK_OPEN', true);\n"
	. "define('NK_GZIP', true);\n"
	. "// NE PAS SUPPRIMER! / DO NOT DELETE\n"
	. "define('HASHKEY', '".addslashes(sha1(uniqid(), true))."');\n"
	. "\n"
	. "?>";

	$path = "conf.inc.php";
	@chmod ($path, 0666);

	if (is_writable($path))
	{
	    if(!defined("HASHKEY"))
	    {
		    $fp = @fopen("conf.inc.php", w);
		    fwrite($fp, $content);
		    fclose($fp);
		    @chmod ($path, 0444);
	    }

		copy("conf.inc.php", "extra/conf" . date('%Y%m%d%H%i') . '.php');

	    style(3,$vars['langue']);
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<big>" . _CONFIGSAVE . "<br />" . _CLICNEXTTOUPGRADE . "</big>\n"
		. "</div>\n"
		. "</div>\n";
	    echo "<form method=\"post\" action=\"update.php?action=upgrade_db&amp;langue=" . $vars['langue'] . "\" onsubmit=\"this.goButton.disabled=true; return true\">\n"
	    . "<div style=\"text-align: center;\"><input type=\"submit\" name=\"goButton\" value=\"" . _NEXT . "\" /></div></form></body></html>";
        }
        else
        {
	    style(3,$vars['langue']);

	    echo "<div><a href=\"#\" onclick=\"javascript:window.open('help/" . $vars['langue'] . "/install.html','Help','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=300');return(false)\">\n"
	    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a></div>\n"
	    . "<div style=\"text-align: center;\"><br /><br /><h3>" . _UPGRADE . "</h3></div>\n"
	    . "<form method=\"post\" action=\"update.php?action=update_config\">\n"
	    . "<div style=\"text-align: center;\">\n"
	    . "<input type=\"hidden\" name=\"langue\" value=\"" . $vars['langue'] . "\" />\n"
	    . "<input type=\"hidden\" name=\"db_host\" value=\""  . $vars['db_host'] . "\" />\n"
	    . "<input type=\"hidden\" name=\"db_user\" value=\"" . $vars['db_user'] . "\" />\n"
	    . "<input type=\"hidden\" name=\"db_pass\" value=\"" . $vars['db_pass'] . "\" />\n"
	    . "<input type=\"hidden\" name=\"prefix\" value=\"" . $vars['prefix'] . "\" />\n"
	    . "<input type=\"hidden\" name=\"db_name\" value=\"" . $vars['db_name'] . "\" />\n"
	    . "<br />" . _BADCHMOD . "<br /><br /><input type=\"submit\" name=\"ok\" value=\"" . _RETRY . "\" /></div></form></body></html>";
        }
    }
}


switch ($_REQUEST['action'])
{

    case "upgrade_db":
    upgrade_db();
    break;

    case"edit_config":
    edit_config($_REQUEST['op']);
    break;

	case"edit_config_assistant":
    edit_config_assistant($_REQUEST['op']);
    break;

    case"update_config":
    update_config($_REQUEST);
    break;

    case"install":
    install();
    break;

    default:
    index();
    break;
}


?>
