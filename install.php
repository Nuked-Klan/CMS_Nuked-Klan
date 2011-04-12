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
include ('Includes/hash.php');

function index()
{
    style(1,0);

    echo "<div style=\"text-align: center;\"><br /><br /><br /><br /><h3>Select your language : </h3></div><br />\n"
    . "<form method=\"post\" action=\"install.php?action=check\">\n"
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

    echo "</select>&nbsp;&nbsp;<input type=\"submit\" name=\"ok\" value=\"send\" /><br /><br /></div></form></div></div></body></html>";
}

function RequirementTrue($type)
{
	echo "<div style=\"float:right\">[&nbsp;&nbsp;<span style=\"color:#00FF00\">OK</span>&nbsp;&nbsp;]</div>";
	echo "<div style=\"margin-right: 40px;\">$type</div>\n";
}

function RequirementWarn($type)
{
	echo "<div style=\"float:right\">[&nbsp;<span style=\"color:#FF6600\">WARN</span>&nbsp;]</div>";
	echo "<div style=\"margin-right: 40px;\">$type</div>\n";
}

function RequirementFalse($type)
{
	global $Test;
	$Test = false;
	echo "<div style=\"float:right\">[&nbsp;<span style=\"color:#FF0000\">FAIL</span>&nbsp;]</div>";
	echo "<div style=\"margin-right: 40px; width: 100%\">$type</div>\n";
}

function RequirementTest($text, $condition, $langue, $errormsg = null)
{
	include("lang/" . $langue . ".lang.php");
	if ($condition > 0)
	{
		RequirementTrue($text);
	}
	else
	{
		RequirementFalse($text);
		if ($errormsg !== null)
		{
			echo "<p>$errormsg</p>\n";
		}
	}
}

function Requirement()
{
	Global $Test;
	include("lang/" . $_REQUEST['langue'] . ".lang.php");
	echo $_REQUEST['langue'];
	$Test = true;
	style(1,$_REQUEST['langue']);
	echo "<div style=\"font-family: Courier, 'Courier New';border:solid 1px black; width: 400px; margin:auto;padding:15px;\">\n";
	echo _CHECKCURRING;
	RequirementTest(_PHPVERSION, version_compare(phpversion(), '5.1'), $_REQUEST['langue'], _QUESPHPVERSION);
	RequirementTest(_MYSQLEXT, extension_loaded('mysql'), $_REQUEST['langue']);
	RequirementTest(_EXTENSIONLOAD, extension_loaded('session'), $_REQUEST['langue']);
	RequirementTest('extention zip', extension_loaded('zip'), $_REQUEST['langue']);
	RequirementTest('Check chmod', is_writable(dirname(__FILE__)), $_REQUEST['langue']);
	if (extension_loaded('fileinfo'))
		RequirementTrue('extension File Info');
	else
		RequirementWarn('extension File Info');
	RequirementTest('extention GD', extension_loaded('gd'), $_REQUEST['langue']);
	RequirementTest('extention Hash', function_exists('hash'), $_REQUEST['langue']);

	echo "</div>\n";
	echo "<div style=\"border:double 3px black;width:250px;margin:20px auto;padding: 10px;\">\n";
	if ($Test == true)
	{
		echo _SYSTEMINSTALL;
		echo "<center><form>
		<input type=\"hidden\" name=\"action\" value=\"install\"/>
		<input type=\"hidden\" name=\"langue\" value=\"".$_REQUEST['langue']."\"/>
		<input type=\"submit\" value=\""._NEXTSTEP."\"/></form></center>";
	}
	else
	{
		echo _FORCE;
		echo _NEXTLANG;
	}
	echo "</div></div></div></body></html>";
}

function install()
{

	if(!isset($_REQUEST['langue']))
	{
     echo 'Veuillez sélectionner une langue !<br />Please select a language!<br /><br /><a href="install.php">Retour / Back</a>';
     exit();
	}
	else
	{
    include("lang/" . $_REQUEST['langue'] . ".lang.php");
	}
    style(2,$_REQUEST['langue']);

    echo "<div style=\"text-align: center;\"><br /><br /><br /><br /><h3>" . _WELCOMEINSTALL . "</h3><br />" . _GUIDEINSTALL . "<br /><br /><br />\n"
    . "<input type=\"button\" name=\"install\" onclick=\"document.location='install.php?action=edit_config_assistant&amp;op=info&amp;langue=" . $_REQUEST['langue'] . "';\" value=\""._INSTALLPASPAS."\" />"
	. "&nbsp;<input type=\"button\" name=\"install\" onclick=\"document.location='install.php?action=edit_config&amp;op=save_config&amp;langue=" . $_REQUEST['langue'] . "';\" value=\""._INSTALL."\" />"
    . "&nbsp;<input type=\"button\" name=\"upgrade\" onclick=\"document.location='update.php?action=install&langue=" . $_REQUEST['langue'] . "';\" value=\"" . _UPGRADE . "\" /></div>";

	?>
		<script>
		interval = setInterval(suivant, 6000);
		compteur = 2;
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
	echo "</div></div></body></html>";
}

function style($etape, $langue)
{

	if($langue == "inconnu" && $etape !=1)
	{
		?>
		<script>
		window.location='install.php';
		</script>
		<?php
	}
	if($langue != 0 && $etape != 1)
	{
		include("lang/" . $langue . ".lang.php");
	}
    ?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
	<meta http-equiv="content-style-type" content="text/css" />
	<title>Installation Nuked-klan</title>
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
							<a href="install.php" class="current">
							<?php
							}
							else
							{
							?>
							<a href="install.php">
							<?php
							}
							?>
							Checking configuration &amp; select language
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
							<a href="install.php?action=install&langue=<?php echo $langue; ?>" class="current">
							<?php
							}
							else
							{
							?>
							<a href="install.php?action=install&langue=<?php echo $langue; ?>">
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
							<a href="install.php?action=edit_config&amp;op=save_config&amp;langue=<?php echo $langue; ?>" class="current">
							<?php
							}
							else
							{
							?>
							<a href="install.php?action=edit_config&amp;op=save_config&amp;langue=<?php echo $langue; ?>">
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
							<a href="install.php?action=edit_config_assistant&amp;op=info&amp;langue=<?php echo $langue; ?>" class="current">
							<?php
							}
							else
							{
							?>
							<a href="install.php?action=edit_config_assistant&amp;op=info&amp;langue=<?php echo $langue; ?>">
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
    . "<tr><td><div id=\"infos\">" . _INSTALLPROGRESS . "</div></td></tr>\n"
    . "</table></td></tr></table>\n";
}

if (isset($_GET['action']) && $_GET['action'] != "check" && $_GET['action'] != "save_config" && $_GET['action'] != "update_config"&& $_GET['action'] != "language" && $_GET['action'] != "edit_config" && $_GET['action'] != "edit_config_assistant" && $_GET['action'] != "install")
{
    @include ("conf.inc.php");
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
if (isset($global['db_host'], $global['db_user'], $global['db_pass']))
{
    $db = mysql_connect($global['db_host'], $global['db_user'], $global['db_pass']) or die ("<div style=\"text-align: center;\">Error ! Database connexion failed<br />Check your user's name/password</div>");
    $connect= mysql_select_db($global['db_name'], $db) or die ("<div style=\"text-align: center;\">Error ! Database connexion failed<br />Check your database's name</div>");
}
}

function create_db()
{
    global $nuked, $db_prefix;

    include ("lang/" . $_REQUEST['langue'] . ".lang.php");

    style(4,$_REQUEST['langue']);

    echo "<div style=\"text-align: center;\"><br /><br /><h3>" . _INSTALLNK . "</h3><br />\n";

    progress();
	// Config de God

echo "<script type=\"text/javascript\">\n"
."<!--\n"
."\n"
. "function verifchamps()\n"
. "{\n"
. "if (document.getElementById('install_pseudo').value.length < 3)\n"
. "{\n"
. "alert('" . _3TYPEMIN . "');\n"
. "return false;\n"
. "}\n"
. "\n"
. "if (document.getElementById('install_pass').value.length < 4)\n"
. "{\n"
. "alert('" . _4TYPEMIN . "');\n"
. "return false;\n"
. "}\n"
."\n"
."if (document.getElementById('install_pass').value != document.getElementById('install_passconf').value)\n"
."{\n"
."alert('" . _PASSFAILED . "');\n"
."return false;\n"
."}\n"
."\n"
. "return true;\n"
. "}\n"
."\n"
. "// -->\n"
. "</script>\n";
echo "<div style=\"text-align: center;\"><br /><br /><h3>" . _GODCONF . "</h3></div>\n"
. "<form method=\"post\" action=\"install.php?action=add_god\" onsubmit=\"return verifchamps();\">\n"
. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
. "<tr><td colspan=\"2\"><b>" . _CONFIG . "</b></td></tr>\n"
. "<tr><td colspan=\"2\">&nbsp;</td></tr>\n"
. "<tr><td>" . _GODNICK . " * :</td><td><input id=\"install_pseudo\" type=\"text\" name=\"pseudo\" size=\"30\" value=\"\" /></td></tr>\n"
. "<tr><td>" . _GODPASS . " * :</td><td><input id=\"install_pass\" type=\"password\" name=\"pass\" size=\"30\" value=\"\" /></td></tr>\n"
. "<tr><td>" . _GODPASS . " (" . _PASSCONFIRM . ") * :</td><td><input id=\"install_passconf\" type=\"password\" name=\"passconf\" size=\"30\" value=\"\" /></td></tr>\n"
. "<tr><td>" . _GODMAIL . " :</td><td><input type=\"text\" name=\"email\" size=\"40\" value=\"\" /></td></tr>\n"
. "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"langue\" value=\"" . $_REQUEST['langue'] . "\" /></td></tr>\n"
. "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"ok\" value=\"Install\" /></td></tr></table></form>\n";
    $time = time();

///////////////////////////////////////////////////////
//  Creation des Tables
///////////////////////////////////////////////////////

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

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_banned</b>" . _CREATES . "&nbsp;');</script>";

$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_banned";
$req = mysql_query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_tmpses` (
  `session_id` varchar(64) NOT NULL,
  `session_vars` text NOT NULL,
  `session_start` bigint(20) NOT NULL,
  PRIMARY KEY (`session_id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_banned (
  `id` int(11) NOT NULL auto_increment,
  `ip` varchar(50) NOT NULL default '',
  `pseudo` varchar(50) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `date` VARCHAR(20)  NULL,
  `dure` VARCHAR(20)  NULL,
  `texte` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_banned</b>" . _CREATES . "&nbsp;');</script>";

		$sql = "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_packages` (
		  `file` varchar(100) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `author` varchar(255) NOT NULL,
		  `link` varchar(255) NOT NULL,
		  `active` tinyint(1) NOT NULL,
		  PRIMARY KEY (`file`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
		$req = mysql_query($sql);


$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_block";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_block (
  `bid` int(10) NOT NULL auto_increment,
  `active` int(1) NOT NULL default '0',
  `position` int(2) NOT NULL default '0',
  `module` varchar(100) NOT NULL default '',
  `titre` text NOT NULL,
  `content` text NOT NULL,
  `type` varchar(30) NOT NULL default '0',
  `nivo` int(1) NOT NULL default '0',
  `page` text NOT NULL,
  PRIMARY KEY  (`bid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_block</b>" . _CREATES . "&nbsp;');</script>";

$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (1, 2, 1, '', '" . _BLOKLOGIN . "', '', 'login', 0, 'Tous');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (2, 1, 1, '', '" . _NAV . "', '[News]|" . _NAVNEWS . "||0|NEWLINE[Archives]|" . _NAVARCHIV . "||0|NEWLINE[Forum]|" . _NAVFORUM . "||0|NEWLINE[Download]|" . _NAVDOWNLOAD . "||0|NEWLINE[Members]|" . _NAVMEMBERS . "||0|NEWLINE[Team]|" . _NAVTEAM . "||0|NEWLINE[Defy]|" . _NAVDEFY . "||0|NEWLINE[Recruit]|" . _NAVRECRUIT . "||0|NEWLINE[Sections]|" . _NAVART . "||0|NEWLINE[Server]|" . _NAVSERVER . "||0|NEWLINE[Links]|" . _NAVLINKS . "||0|NEWLINE[Calendar]|" . _NAVCALENDAR . "||0|NEWLINE[Gallery]|" . _NAVGALLERY . "||0|NEWLINE[Wars]|" . _NAVMATCHS . "||0|NEWLINE[Irc]|" . _NAVIRC . "||0|NEWLINE[Guestbook]|" . _NAVGUESTBOOK . "||0|NEWLINE[Search]|" . _NAVSEARCH . "||0|NEWLINE|<b>" . _MEMBER . "</b>||1|NEWLINE[User]|" . _NAVACCOUNT . "||1|NEWLINE|<b>" . _ADMIN . "</b>||2|NEWLINE[Admin]|" . _NAVADMIN . "||2|', 'menu', 0, 'Tous');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (3, 1, 2, 'Search', '" . _BLOKSEARCH . "', '', 'module', 0, 'Tous');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (4, 2, 2, '', '" . _POLL . "', '', 'survey', 0, 'Tous');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (5, 2, 3, 'Wars', '" . _NAVMATCHS . "', '', 'module', 0, 'Tous');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (6, 1, 3, 'Stats', '" . _BLOKSTATS . "', '', 'module', 0, 'Tous');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (7, 0, 0, 'Irc', '" . _IRCAWARD . "', '', 'module', 0, 'Tous');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (8, 0, 0, 'Server', '" . _SERVERMONITOR . "', '', 'module', 0, 'Tous');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (9, 0, 0, '', '" . _SUGGEST . "', '', 'suggest', 1, 'Tous');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (10, 0, 0, 'Textbox', '" . _BLOKSHOUT . "', '', 'module', 0, 'Tous');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_block (bid, active, position, module, titre, content, type, nivo, page) VALUES (11, 1, 4, '', '" . _BLOKPARTNERS . "', '<div style=\"text-align: center;padding: 10px;\"><a href=\"http://www.nuked-klan.org\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"http://www.nuked-klan.org/ban.gif\" alt=\"\" title=\"Nuked-klaN CMS\" /></a></div><div style=\"text-align: center;padding: 10px;\"><a href=\"http://www.nitroserv.fr\" onclick=\"window.open(this.href); return false;\"><img style=\"border: 0;\" src=\"http://www.nitroserv.com/images/logo_88x31.jpg\" alt=\"\" title=\"Location de serveurs de jeux\" /></a></div>', 'html', 0, 'Tous');";
$req = mysql_query($sql);

$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_calendar";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_calendar (
  `id` int(11) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `date_jour` int(2) default NULL,
  `date_mois` int(2) default NULL,
  `date_an` int(4) default NULL,
  `heure` varchar(5) NOT NULL default '',
  `auteur` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_calendar</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_comment";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_comment (
  `id` int(10) NOT NULL auto_increment,
  `module` varchar(30) NOT NULL default '0',
  `im_id` int(100) default NULL,
  `autor` text,
  `autor_id` varchar(20) NOT NULL default '',
  `titre` text NOT NULL,
  `comment` text,
  `date` varchar(12) default NULL,
  `autor_ip` varchar(20) default NULL,
  PRIMARY KEY  (`id`),
  KEY `im_id` (`im_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_comment</b>" . _CREATES . "&nbsp;');</script>";


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

$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_config";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_config (
  `name` varchar(255) NOT NULL default '',
  `value` text NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_config</b>" . _CREATES . "&nbsp;');</script>";

$date = time();
$siteurl = "http://" . $_SERVER['SERVER_NAME'] . str_replace("install.php", "", $_SERVER['SCRIPT_NAME']);
if (substr($siteurl, -1) == "/") $siteurl = substr($siteurl, 0, -1);

$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('version', '1.7.9');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('date_install', '" . $date. "');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('langue', '" . $_REQUEST['langue'] . "');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('name', 'Nuked-klaN 1.7.9');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('slogan', 'PHP 4 Gamers');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('tag_pre', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('tag_suf', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('url', '" . $siteurl . "');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('mail', 'mail@hotmail.com');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('footmessage', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('nk_status', 'open');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('index_site', 'News');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('theme', 'Impact_Nk');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('keyword', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('description', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('inscription', 'on');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('inscription_mail', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('inscription_avert', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('inscription_charte', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('validation', 'auto');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('user_delete', 'on');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('suggest_avert', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('irc_chan', 'nuked-klan');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('irc_serv', 'quakenet.eu.org');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('server_ip', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('server_port', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('server_pass', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('server_game', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_title', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_desc', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_rank_team', 'off');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_field_max', '10');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_file', 'on');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_file_level', '1');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('forum_file_maxsize', '1000');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('thread_forum_page', '20');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('mess_forum_page', '10');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('hot_topic', '20');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('post_flood', '10');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('gallery_title', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_img_line', '2');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_img', '6');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_news', '5');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_download', '10');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('hide_download', 'on');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_liens', '10');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_sections', '10');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_wars', '30');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_archives', '30');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_members', '30');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('max_shout', '20');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('mess_guest_page', '10');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('sond_delay', '24');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('level_analys', '-1');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('visit_delay', '10');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('recrute', '1');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('recrute_charte', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('recrute_mail', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('recrute_inbox', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('defie_charte', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('defie_mail', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('defie_inbox', '');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('birthday', 'all');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('avatar_upload', 'on');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('avatar_url', 'on');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('cookiename', 'nuked');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('sess_inactivemins', '5');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('sess_days_limit', '365');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('nbc_timeout', '300');";
$req=mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_config (name, value) VALUES ('screen', 'on');";
$req = mysql_query($sql);
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

$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_defie";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_defie (
  `id` int(11) NOT NULL auto_increment,
  `send` varchar(12) NOT NULL default '',
  `pseudo` text NOT NULL,
  `clan` text NOT NULL,
  `mail` varchar(80) NOT NULL default '',
  `icq` varchar(50) NOT NULL default '',
  `irc` varchar(50) NOT NULL default '',
  `url` varchar(200) NOT NULL default '',
  `pays` text NOT NULL,
  `date` varchar(20) NOT NULL default '',
  `heure` varchar(10) NOT NULL default '',
  `serveur` text NOT NULL,
  `game` int(11) NOT NULL default '0',
  `type` text NOT NULL,
  `map` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_defie</b>" . _CREATES . "&nbsp;');</script>";

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

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_discussion</b>" . _CREATES . "&nbsp;');</script>";

$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_downloads";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_downloads (
  `id` int(11) NOT NULL auto_increment,
  `date` varchar(12) NOT NULL default '',
  `taille` varchar(6) NOT NULL default '0',
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `type` int(11) NOT NULL default '0',
  `count` int(10) NOT NULL default '0',
  `url` varchar(200) NOT NULL default '',
  `url2` varchar(200) NOT NULL default '',
  `broke` int(11) NOT NULL default '0',
  `url3` varchar(200) NOT NULL default '',
  `level` int(1) NOT NULL default '0',
  `hit` int(11) NOT NULL default '0',
  `edit` varchar(12) NOT NULL default '',
  `screen` varchar(200) NOT NULL default '',
  `autor` text NOT NULL,
  `url_autor` varchar(200) NOT NULL default '',
  `comp` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_downloads</b>" . _CREATES . "&nbsp;');</script>";



$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_downloads_cat";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_downloads_cat (
  `cid` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `titre` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `level` int(1) NOT NULL default '0',
  `position` int(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_downloads_cat</b>" . _CREATES . "&nbsp;');</script>";

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

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_ErreurSql</b>" . _CREATES . "&nbsp;');</script>";

$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_fichiers_joins";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_fichiers_joins (
  `id` int(10) NOT NULL auto_increment,
  `module` varchar(30) NOT NULL default '',
  `im_id` int(10) NOT NULL default '0',
  `type` varchar(30) NOT NULL default '',
  `url` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `im_id` (`im_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_fichiers_joins</b>" . _CREATES . "&nbsp;');</script>";



$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_forums";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_forums (
  `id` int(5) NOT NULL auto_increment,
  `cat` int(11) NOT NULL default '0',
  `nom` text NOT NULL,
  `comment` text NOT NULL,
  `moderateurs` text NOT NULL,
  `niveau` int(1) NOT NULL default '0',
  `level` int(1) NOT NULL default '0',
  `ordre` int(5) NOT NULL default '0',
  `level_poll` int(1) NOT NULL default '0',
  `level_vote` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

$sql = "INSERT INTO " . $db_prefix . "_forums (id, cat, nom, comment, moderateurs, niveau, level, ordre, level_poll, level_vote) VALUES (1, 1, 'Forum', 'Test Forum', '', 0, 0, 0, 1 ,1);";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_forums</b>" . _CREATES . "&nbsp;');</script>";



$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_forums_cat";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_forums_cat (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(100) default NULL,
  `ordre` int(5) NOT NULL default '0',
  `niveau` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

$sql="INSERT INTO " . $db_prefix . "_forums_cat (id, nom, ordre, niveau) VALUES (1, 'Categorie 1', 0, 0);";
$req=mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_forums_cat</b>" . _CREATES . "&nbsp;');</script>";



$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_forums_messages";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_forums_messages (
  `id` int(5) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `txt` text NOT NULL,
  `date` varchar(12) NOT NULL default '',
  `edition` text NOT NULL,
  `auteur` text NOT NULL,
  `auteur_id` varchar(20) NOT NULL default '',
  `auteur_ip` varchar(20) NOT NULL default '',
  `bbcodeoff` int(1) NOT NULL default '0',
  `smileyoff` int(1) NOT NULL default '0',
  `cssoff` int(1) NOT NULL default '0',
  `usersig` int(1) NOT NULL default '0',
  `emailnotify` int(1) NOT NULL default '0',
  `thread_id` int(5) NOT NULL default '0',
  `forum_id` mediumint(10) NOT NULL default '0',
  `file` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `auteur_id` (`auteur_id`),
  KEY `thread_id` (`thread_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_forums_messages</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_forums_threads";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_forums_threads (
  `id` int(5) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `date` varchar(10) default NULL,
  `closed` int(1) NOT NULL default '0',
  `auteur` text NOT NULL,
  `auteur_id` varchar(20) NOT NULL default '',
  `forum_id` int(5) NOT NULL default '0',
  `last_post` varchar(20) NOT NULL default '',
  `view` int(10) NOT NULL default '0',
  `annonce` int(1) NOT NULL default '0',
  `sondage` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `auteur_id` (`auteur_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_forums_threads</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_forums_rank";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_forums_rank (
  `id` int(10) NOT NULL auto_increment,
  `nom` varchar(100) NOT NULL default '',
  `type` int(1) NOT NULL default '0',
  `post` int(4) NOT NULL default '0',
  `image` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

$sql = "INSERT INTO " . $db_prefix . "_forums_rank (id, nom, type, post, image) VALUES (1, 'Newbie', 0, 0, 'modules/Forum/images/rank/star1.gif');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_forums_rank (id, nom, type, post, image) VALUES (2, 'Junior Member', 0, 10, 'modules/Forum/images/rank/star2.gif');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_forums_rank (id, nom, type, post, image) VALUES (3, 'Member', 0, 100, 'modules/Forum/images/rank/star3.gif');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_forums_rank (id, nom, type, post, image) VALUES (4, 'Senior Member', 0, 500, 'modules/Forum/images/rank/star4.gif');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_forums_rank (id, nom, type, post, image) VALUES (5, 'Posting Freak', 0, 1000, 'modules/Forum/images/rank/star5.gif');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_forums_rank (id, nom, type, post, image) VALUES (6, 'Moderator', 1, 0, 'modules/Forum/images/rank/mod.gif');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_forums_rank (id, nom, type, post, image) VALUES (7, 'Administrator', 2, 0, 'modules/Forum/images/rank/mod.gif');";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_forums_rank</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_forums_read";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_forums_read (
  `id` int(11) NOT NULL auto_increment,
  `user_id` varchar(20) NOT NULL default '',
  `thread_id` int(11) NOT NULL default '0',
  `forum_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  KEY `thread_id` (`thread_id`),
  KEY `forum_id` (`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_forums_read</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_forums_poll";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_forums_poll (
  `id` int(11) NOT NULL auto_increment,
  `thread_id` int(11) NOT NULL default '0',
  `titre` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `thread_id` (`thread_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_forums_poll</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_forums_options";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_forums_options (
  `id` int(11) NOT NULL default '0',
  `poll_id` int(11) NOT NULL default '0',
  `option_text` varchar(255) NOT NULL default '',
  `option_vote` int(11) NOT NULL default '0',
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_forums_options</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_forums_vote";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_forums_vote (
  `poll_id` int(11) NOT NULL default '0',
  `auteur_id` varchar(20) NOT NULL default '',
  `auteur_ip` varchar(20) NOT NULL default '',
  KEY `poll_id` (`poll_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_forums_vote</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_gallery";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_gallery (
  `sid` int(11) NOT NULL auto_increment,
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `url` varchar(200) NOT NULL default '',
  `url2` varchar(200) NOT NULL default '',
  `url_file` varchar(200) NOT NULL default '',
  `cat` int(11) NOT NULL default '0',
  `date` varchar(12) NOT NULL default '',
  `count` int(10) NOT NULL default '0',
  `autor` text NOT NULL,
  PRIMARY KEY  (`sid`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_gallery</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_gallery_cat";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_gallery_cat (
  `cid` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `titre` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `position` int(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_gallery_cat</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_games";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_games (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `titre` varchar(50) NOT NULL default '',
  `icon` varchar(150) NOT NULL default '',
  `pref_1` varchar(50) NOT NULL default '',
  `pref_2` varchar(50) NOT NULL default '',
  `pref_3` varchar(50) NOT NULL default '',
  `pref_4` varchar(50) NOT NULL default '',
  `pref_5` varchar(50) NOT NULL default '',
  `map` TEXT NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_games</b>" . _CREATES . "&nbsp;');</script>";

$sql = "INSERT INTO " . $db_prefix . "_games (id, name, titre, icon, pref_1, pref_2, pref_3, pref_4, pref_5) VALUES (1, 'Counter-Strike', '" . _PREFCS . "', 'images/games/cs.gif', '" . _OTHERNICK . "', '" . _FAVMAP . "', '" . _FAVWEAPON . "', '" . _SKINT . "', '" . _SKINCT . "');";
$req = mysql_query($sql);



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

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_games_prefs</b>" . _CREATES . "&nbsp;');</script>";



$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_guestbook";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_guestbook (
  `id` int(9) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `url` varchar(70) NOT NULL default '',
  `date` int(11) NOT NULL default '0',
  `host` varchar(60) NOT NULL default '',
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_guestbook</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_irc_awards";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_irc_awards (
  `id` int(20) NOT NULL auto_increment,
  `text` text NOT NULL,
  `date` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_irc_awards</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_liens";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_liens (
  `id` int(10) NOT NULL auto_increment,
  `date` varchar(12) NOT NULL default '',
  `titre` text NOT NULL,
  `description` text NOT NULL,
  `url` varchar(200) NOT NULL default '',
  `cat` int(11) NOT NULL default '0',
  `webmaster` text NOT NULL,
  `country` varchar(50) NOT NULL default '',
  `count` int(11) NOT NULL default '0',
  `broke` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_liens</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_liens_cat";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_liens_cat (
  `cid` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `titre` varchar(50) NOT NULL default '',
  `description` text NOT NULL,
  `position` int(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_liens_cat</b>" . _CREATES . "&nbsp;');</script>";




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

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_match</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_modules";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_modules (
  `id` int(2) NOT NULL auto_increment,
  `nom` varchar(50) NOT NULL default '',
  `niveau` int(1) NOT NULL default '0',
  `admin` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_modules</b>" . _CREATES . "&nbsp;');</script>";

$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (1, 'News', 0, 2);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (2, 'Forum', 0, 2);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (3, 'Wars', 0, 2);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (4, 'Irc', 0, 2);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (5, 'Survey', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (6, 'Links', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (7, 'Sections', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (8, 'Server', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (9, 'Download', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (10, 'Gallery', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (11, 'Guestbook', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (12, 'Suggest', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (13, 'Textbox', 0, 9);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (14, 'Calendar', 0, 2);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (15, 'Members', 0, 9);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (16, 'Team', 0, 9);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (17, 'Defy', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (18, 'Recruit', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (19, 'Comment', 0, 9);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (20, 'Vote', 0, 9);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (21, 'Stats', 0, 2);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_modules (id, nom, niveau, admin) VALUES (22, 'Contact', 0, 3);";
$req = mysql_query($sql);


$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_nbconnecte";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_nbconnecte (
  `IP` varchar(15) NOT NULL default '',
  `type` int(10) NOT NULL default '0',
  `date` int(14) NOT NULL default '0',
  `user_id` varchar(20) NOT NULL default '',
  `username` varchar(40) NOT NULL default '',
  PRIMARY KEY  ( `IP` , `user_id` )
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_nbconnecte</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_news";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_news (
  `id` int(11) NOT NULL auto_increment,
  `cat` varchar(30) NOT NULL default '',
  `titre` text,
  `auteur` text,
  `auteur_id` varchar(20) NOT NULL default '',
  `texte` text,
  `suite` text,
  `date` varchar(30) NOT NULL default '',
  `bbcodeoff` int(1) NOT NULL default '0',
  `smileyoff` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_news</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_news_cat";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_news_cat (
  `nid` int(11) NOT NULL auto_increment,
  `titre` text,
  `description` text,
  `image` text,
  PRIMARY KEY  (`nid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_news_cat</b>" . _CREATES . "&nbsp;');</script>";

$sql = "INSERT INTO " . $db_prefix . "_news_cat VALUES ('1', 'Counter-Strike', '" . _BESTMOD . "', 'modules/News/images/cs.gif');";
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

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_notification</b>" . _CREATES . "&nbsp;');</script>";

$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_recrute";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_recrute (
  `id` int(11) NOT NULL auto_increment,
  `date` varchar(12) NOT NULL default '',
  `pseudo` text NOT NULL,
  `prenom` text NOT NULL,
  `age` int(3) NOT NULL default '0',
  `mail` varchar(80) NOT NULL default '',
  `icq` varchar(50) NOT NULL default '',
  `country` text NOT NULL,
  `game` int(11) NOT NULL default '0',
  `connection` text NOT NULL,
  `experience` text NOT NULL,
  `dispo` text NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `game` (`game`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_recrute</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_sections";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_sections (
  `artid` int(11) NOT NULL auto_increment,
  `secid` int(11) NOT NULL default '0',
  `title` text NOT NULL,
  `content` text NOT NULL,
  `autor` text NOT NULL,
  `autor_id` varchar(20) NOT NULL default '',
  `counter` int(11) NOT NULL default '0',
  `bbcodeoff` int(1) NOT NULL default '0',
  `smileyoff` int(1) NOT NULL default '0',
  `date` varchar(12) NOT NULL default '',
  PRIMARY KEY  (`artid`),
  KEY `secid` (`secid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_sections</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_sections_cat";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_sections_cat (
  `secid` int(11) NOT NULL auto_increment,
  `parentid` int(11) NOT NULL default '0',
  `secname` varchar(40) NOT NULL default '',
  `description` text NOT NULL,
  `position` int(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`secid`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_sections_cat</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_serveur";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_serveur (
  `sid` int(30) NOT NULL auto_increment,
  `game` varchar(30) NOT NULL default '',
  `ip` varchar(30) NOT NULL default '',
  `port` varchar(10) NOT NULL default '',
  `pass` varchar(10) NOT NULL default '',
  `cat` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`sid`),
  KEY `game` (`game`),
  KEY `cat` (`cat`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_serveur</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_serveur_cat";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_serveur_cat (
  `cid` int(30) NOT NULL auto_increment,
  `titre` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_serveur_cat</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_sessions";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_sessions (
  `id` varchar(50) NOT NULL default '0',
  `user_id` varchar(20) NOT NULL default '0',
  `date` varchar(30) NOT NULL default '',
  `last_used` varchar(30) NOT NULL default '',
  `ip` varchar(50) NOT NULL default '',
  `vars` blob NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_sessions</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_smilies";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_smilies (
  `id` int(5) NOT NULL auto_increment,
  `code` varchar(50) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_smilies</b>" . _CREATES . "&nbsp;');</script>";

$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (1, ':D', 'biggrin.gif', 'Very Happy');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (2, ':)', 'smile.gif', 'Smile');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (3, ':(', 'frown.gif', 'Sad');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (4, ':o', 'eek.gif', 'Surprised');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (5, ':?', 'confused.gif', 'Confused');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (6, '8)', 'cool.gif', 'Cool');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (7, ':P', 'tongue.gif', 'Razz');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (8, ':x', 'mad.gif', 'Mad');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (9, ';)', 'wink.gif', 'Wink');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (10, ':red:', 'redface.gif', 'Embarassed');";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_smilies (id, code, url, name) VALUES (11, ':roll:', 'rolleyes.gif', 'Rolling Eyes');";
$req = mysql_query($sql);




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_sondage";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_sondage (
  `sid` int(11) NOT NULL auto_increment,
  `titre` varchar(100) NOT NULL default '',
  `date` varchar(15) NOT NULL default '0',
  `niveau` int(1) NOT NULL default '0',
  PRIMARY KEY  (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_sondage</b>" . _CREATES . "&nbsp;');</script>";

$sql = "INSERT INTO " . $db_prefix . "_sondage VALUES (1, '" . _LIKENK . "', '" . $time . "', 0);";
$req = mysql_query($sql);




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_sondage_check";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_sondage_check (
  `ip` varchar(20) NOT NULL default '',
  `pseudo` varchar(50) NOT NULL default '',
  `heurelimite` int(14) NOT NULL default '0',
  `sid` varchar(30) NOT NULL default '',
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_sondage_check</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_sondage_data";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_sondage_data (
  `sid` int(11) NOT NULL default '0',
  `optionText` char(50) NOT NULL default '',
  `optionCount` int(11) NOT NULL default '0',
  `voteID` int(11) NOT NULL default '0',
  KEY `sid` (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_sondage_data</b>" . _CREATES . "&nbsp;');</script>";

$sql = "INSERT INTO " . $db_prefix . "_sondage_data VALUES (1, '" . _ROXX . "', 0, 1);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_sondage_data VALUES (1, '" . _NOTBAD . "', 0, 2);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_sondage_data VALUES (1, '" . _SHIET . "', 0, 3);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_sondage_data VALUES (1, '" . _WHATSNK . "', 0, 4);";
$req = mysql_query($sql);





$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_stats";
$req = mysql_query($sql);

$sql="CREATE TABLE " . $db_prefix . "_stats (
  `nom` varchar(50) NOT NULL default '',
  `type` varchar(50) NOT NULL default '',
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`nom`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_stats</b>" . _CREATES . "&nbsp;');</script>";

$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Gallery', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Archives', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Calendar', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Defy', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Download', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Guestbook', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Irc', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Links', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Wars', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('News', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Search', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Recruit', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Sections', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Server', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Members', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Team', 'pages', 0);";
$req = mysql_query($sql);
$sql = "INSERT INTO " . $db_prefix . "_stats (nom, type, count) VALUES ('Forum', 'pages', 0);";
$req = mysql_query($sql);





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

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_stats_visitor</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_suggest";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_suggest (
  `id` int(11) NOT NULL auto_increment,
  `module` mediumtext NOT NULL,
  `user_id` varchar(20) NOT NULL default '',
  `proposition` longtext NOT NULL,
  `date` varchar(14) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_suggest</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_shoutbox";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_shoutbox (
  `id` int(11) NOT NULL auto_increment,
  `auteur` text,
  `ip` varchar(20) NOT NULL default '',
  `texte` text,
  `date` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo"<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_shoutbox</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_team";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_team (
  `cid` int(11) NOT NULL auto_increment,
  `titre` varchar(50) NOT NULL default '',
  `tag` text NOT NULL,
  `tag2` text NOT NULL,
  `ordre` int(5) NOT NULL default '0',
  `game` int(11) NOT NULL default '0',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_team</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_team_rank";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_team_rank (
  `id` int(11) NOT NULL auto_increment,
  `titre` varchar(80) NOT NULL default '',
  `ordre` int(5) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_userbox";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_userbox (
  `mid` int(50) NOT NULL auto_increment,
  `user_from` varchar(30) NOT NULL default '',
  `user_for` varchar(30) NOT NULL default '',
  `titre` varchar(50) NOT NULL default '',
  `message` text NOT NULL,
  `date` varchar(30) NOT NULL default '',
  `status` int(1) NOT NULL default '0',
  PRIMARY KEY  (`mid`),
  KEY `user_from` (`user_from`),
  KEY `user_for` (`user_for`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_userbox</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_users";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_users (
  `id` varchar(20) NOT NULL default '',
  `team` varchar(80) NOT NULL default '',
  `team2` varchar(80) NOT NULL default '',
  `team3` varchar(80) NOT NULL default '',
  `rang` int(11) NOT NULL default '0',
  `ordre` int(5) NOT NULL default '0',
  `pseudo` text NOT NULL,
  `mail` varchar(80) NOT NULL default '',
  `email` varchar(80) NOT NULL default '',
  `icq` varchar(50) NOT NULL default '',
  `msn` varchar(80) NOT NULL default '',
  `aim` varchar(50) NOT NULL default '',
  `yim` varchar(50) NOT NULL default '',
  `url` varchar(150) NOT NULL default '',
  `pass` varchar(80) NOT NULL default '',
  `niveau` int(1) NOT NULL default '0',
  `date` varchar(30) NOT NULL default '',
  `avatar` varchar(100) NOT NULL default '',
  `signature` text NOT NULL,
  `user_theme` varchar(30) NOT NULL default '',
  `user_langue` varchar(30) NOT NULL default '',
  `game` int(11) NOT NULL default '0',
  `country` varchar(50) NOT NULL default '',
  `count` int(10) NOT NULL default '0',
  `erreur` INT(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `team` (`team`),
  KEY `team2` (`team2`),
  KEY `team3` (`team3`),
  KEY `rang` (`rang`),
  KEY `game` (`game`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_users</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_users_detail";
$req = mysql_query($sql);

$sql = "CREATE TABLE " . $db_prefix . "_users_detail (
  `user_id` varchar(20) NOT NULL default '0',
  `prenom` text,
  `age` varchar(10) NOT NULL default '',
  `sexe` varchar(20) NOT NULL default '',
  `ville` text,
  `photo` varchar(150) NOT NULL default '',
  `motherboard` text,
  `cpu` varchar(50) default NULL,
  `ram` varchar(10) NOT NULL default '',
  `video` text,
  `resolution` text,
  `son` text,
  `ecran` text,
  `souris` text,
  `clavier` text,
  `connexion` text,
  `system` text,
  `pref_1` text NOT NULL,
  `pref_2` text NOT NULL,
  `pref_3` text NOT NULL,
  `pref_4` text NOT NULL,
  `pref_5` text NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_users_detail</b>" . _CREATES . "&nbsp;');</script>";




$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_vote";
$req = mysql_query($sql);

$sql="CREATE TABLE " . $db_prefix . "_vote (
  `id` int(11) NOT NULL auto_increment,
  `module` varchar(30) NOT NULL default '0',
  `vid` int(100) default NULL,
  `ip` varchar(20) NOT NULL default '',
  `vote` int(2) default NULL,
  PRIMARY KEY  (`id`),
  KEY `vid` (`vid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;";
$req = mysql_query($sql);

echo "<script>show_progress('&nbsp;&nbsp;','<b>" . $db_prefix . "_vote</b>" . _CREATES . "&nbsp;');</script>";


$sql = "DROP TABLE IF EXISTS " . $db_prefix . "_style";
	$req = mysql_query($sql);

	$sql = "CREATE TABLE IF NOT EXISTS `" . $db_prefix . "_style` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `texte` text COLLATE latin1_german2_ci NOT NULL,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci AUTO_INCREMENT=1;";
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
	('ligne1', 'save,newdocument,restoredraft,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,print,|,fullscreen,|,preview,|,help'),
	('ligne2', 'styleselect,fontselect,fontsizeselect,|,link,unlink,anchor,|,emotions,image,media,forecolor,backcolor'),
	('ligne3', 'bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,hr,|,outdent,indent,|,removeformat,|,spellchecker'),
	('ligne4', 'tablecontrols,|,blockquote,sub,sup,|,charmap,pagebreak'),
	('ligne2b', 'styleselect,fontselect,fontsizeselect,barre,link,unlink,anchor,barre,emotions,image,media,forecolor,backcolor'),
	('ligne3b', 'bold,italic,underline,strikethrough,barre,justifyleft,justifycenter,justifyright,justifyfull,barre,bullist,numlist,hr,barre,outdent,indent,barre,removeformat,barre,spellchecker'),
	('ligne4b', 'tablecontrols,barre,blockquote,sub,sup,barre,charmap,pagebreak'),
	('ligne1b', 'save,newdocument,restoredraft,barre,cut,copy,paste,pastetext,pasteword,barre,undo,redo,barre,print,barre,fullscreen,barre,preview,barre,help');";
	$req = mysql_query($sql);


echo "<script>show_progress('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;','<b>" . _INSERTFIELD . "</b>.....Ok');</script>";
echo "<script>show_progress('&nbsp;&nbsp;&nbsp;','<b>" . _INSERTFINISH . "</b><br />');</script></div>";


echo "</body></html>";
}

function add_god($data)
{
    global $db_prefix;

    include ("lang/" . $data['langue'] . ".lang.php");
	style(4, $data['langue']);
    if (!$data['pseudo'] || ($data['pseudo'] == "") || $data['pass'] != $data['passconf'] || (preg_match("`[\$\^\(\)'\"?%#<>,;:]`",$data['pseudo'])))
    {
	echo "<script type=\"text/javascript\">\n"
	."<!--\n"
	."\n"
	. "function verifchamps()\n"
	. "{\n"
	. "if (document.getElementById('install_pseudo').value.length < 3)\n"
	. "{\n"
	. "alert('" . _3TYPEMIN . "');\n"
	. "return false;\n"
	. "}\n"
	. "\n"
	. "if (document.getElementById('install_pass').value.length < 4)\n"
	. "{\n"
	. "alert('" . _4TYPEMIN . "');\n"
	. "return false;\n"
	. "}\n"
	."\n"
	."if (document.getElementById('install_pass').value != document.getElementById('install_passconf').value)\n"
	."{\n"
	."alert('" . _PASSFAILED . "');\n"
	."return false;\n"
	."}\n"
	."\n"
	. "return true;\n"
	. "}\n"
	."\n"
	. "// -->\n"
	. "</script>\n";

	echo "<div style=\"text-align: center;\"><br /><b>" . _GODFAILDED . " !!!</b><br /><br /><h3>" . _GODCONF . "</h3></div>\n"
	. "<form method=\"post\" action=\"install.php?action=add_god\" onsubmit=\"return verifchamps();\">\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
	. "<tr><td colspan=\"2\"><b>" . _CONFIG . "</b></td></tr>\n"
	. "<tr><td colspan=\"2\">&nbsp;</td></tr>\n"
	. "<tr><td>" . _GODNICK . " :</td><td><input id=\"install_pseudo\" type=\"text\" name=\"pseudo\" size=\"30\" value=\"\" /></td></tr>\n"
	. "<tr><td>" . _GODPASS . " :</td><td><input id=\"install_pass\" type=\"password\" name=\"pass\" size=\"30\" value=\"\" /></td></tr>\n"
	. "<tr><td>" . _GODPASS . " (" . _PASSCONFIRM . ") :</td><td><input id=\"install_passconf\" type=\"password\" name=\"passconf\" size=\"30\" value=\"\" /></td></tr>\n"
	. "<tr><td>" . _GODMAIL . " :</td><td><input type=\"text\" name=\"email\" size=\"40\" value=\"\" /></td></tr>\n"
	. "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"langue\" value=\"" . $_REQUEST['langue'] . "\" /></td></tr>\n"
	. "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" name=\"ok\" value=\"Install\" /></td></tr></table></form></body></html>";

    }
    else
    {
    $pass = nk_hash($data['pass']);
	$date = time();
	$ip = $_SERVER['REMOTE_ADDR'];

	$taille = 20;
	$lettres = "abCdefGhijklmNopqrstUvwXyz0123456789";
	srand(time());

	$pseudo = htmlentities($data['pseudo'], ENT_QUOTES);
	for ($i=0;$i<$taille;$i++)
	{
	    $user_id .= substr($lettres,(rand()%(strlen($lettres))), 1);
	}

	$sql = "INSERT INTO " . $db_prefix . "_news VALUES (1, 1, '" . _FIRSTNEWSTITLE . "', '" . $pseudo . "', '" . $user_id . "', '" . _FIRSTNEWSCONTENT . "', '', '" . $date . "', '', '');";
	$req = mysql_query($sql);

	$sql2 = "INSERT INTO " . $db_prefix . "_users VALUES ('" . $user_id . "', '', '', '', '', '', '" . $pseudo . "', '" . $data['email'] . "', '', '', '', '', '', '', '" . $pass . "', 9, '" . $date . "', '', '', '', '', 1, 'France.gif', '', '');";
	$req2 = mysql_query($sql2);

	$sql3 = "INSERT INTO " . $db_prefix . "_shoutbox VALUES (1, '" . $pseudo . "', '" . $ip . "', '" . _FIRSTNEWSTITLE . "', '" . $date . "');";
	$req3 = mysql_query($sql3);

	$error = 0;

	$path1 = "upload/Download/";
	if (is_dir($path1)) @chmod($path1, 0777);
	if (!is_writable($path1)) $error++;

	$path2 = "upload/Forum/";
	if (is_dir($path2)) @chmod($path2, 0777);
	if (!is_writable($path2)) $error++;

	$path3 = "upload/Gallery/";
	if (is_dir($path3)) @chmod($path3, 0777);
	if (!is_writable($path3)) $error++;

	$path4 = "upload/News/";
	if (is_dir($path4)) @chmod($path4, 0777);
	if (!is_writable($path4)) $error++;

	$path5 = "upload/PDF/";
	if (is_dir($path5)) @chmod($path5, 0777);
	if (!is_writable($path5)) $error++;

	$path6 = "upload/User/";
	if (is_dir($path6)) @chmod($path6, 0777);
	if (!is_writable($path6)) $error++;

	$path7 = "upload/Wars/";
	if (is_dir($path7)) @chmod($path7, 0777);
	if (!is_writable($path7)) $error++;

	$path8 = "images/icones/";
	if (is_dir($path8)) @chmod($path8, 0777);
	if (!is_writable($path8)) $error++;

	$path9 = "upload/Suggest/";
	if (is_dir($path9)) @chmod($path9, 0777);
	if (!is_writable($path9)) $error++;

	if (file_exists("update.php"))
	{
	    $path = "update.php";
	    $filesys = str_replace("/", "\\", $path);
	    @chmod($path, 0775);
	    @unlink($path);
	    @system("del $filesys");
	    if (file_exists($path)) $error++;
	}

	if (file_exists("install.php"))
	{
	    $path_2="install.php";
	    $filesys2 = str_replace("/", "\\", $path_2);
	    @chmod($path_2, 0775);
	    @unlink($path_2);
	    @system("del $filesys2");
	    if (file_exists($path_2)) $error++;
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
}

function edit_config($op)
{

    include ("lang/" . $_REQUEST['langue'] . ".lang.php");

    style(3,$_REQUEST['langue']);

    @include("conf.inc.php");

    echo "<div><a href=\"#\" onclick=\"javascript:window.open('help/" . $_REQUEST['langue'] . "/install.html','Help','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=300');return(false)\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a></div>\n"
    . "<div style=\"text-align: center;\"><br /><br /><h3>" . _INSTALLNK . "</h3></div>\n"
    . "<form method=\"post\" action=\"install.php?action=" . $op . "\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td colspan=\"2\" align=\"center\"><b>" . _CONFIG . "</b></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"langue\" value=\"" . $_REQUEST['langue'] . "\" /></td></tr>\n"
    . "<tr><td>" . _DBHOST . " :</td><td><input type=\"text\" name=\"db_host\" size=\"40\" /></td></tr>\n"
    . "<tr><td>" . _DBUSER . " : </td><td><input type=\"text\" name=\"db_user\" size=\"40\" /></td></tr>\n"
    . "<tr><td>" . _DBPASS . " :</td><td><input type=\"password\" name=\"db_pass\" size=\"10\" /></td></tr><tr><td>\n"
    . "<tr><td>" . _DBPREFIX . " :</td><td><input type=\"text\" name=\"prefix\" size=\"10\" /></td></tr>\n"
    . "<tr><td>" . _DBNAME . " :</td><td><input type=\"text\" name=\"db_name\" size=\"10\" /></td></tr></table>\n"
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
		echo "<div style=\"text-align:center;\"><input type=\"button\" name=\"install\" onclick=\"document.location='install.php?action=edit_config_assistant&amp;op=save_config&amp;langue=" . $_REQUEST['langue'] . "';\" value=\""._NEXT."\" /></div>\n";
	}
	else if($op == "save_config")
	{
    include ("lang/" . $_REQUEST['langue'] . ".lang.php");

    style(3,$_REQUEST['langue']);



    @include("conf.inc.php");

    echo "<div><a href=\"#\" onclick=\"javascript:window.open('help/" . $_REQUEST['langue'] . "/install.html','Help','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=300');return(false)\">\n"
    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a></div>\n"
    . "<div style=\"text-align: center;\"><br /><br /><h3>" . _INSTALLNK . "</h3></div>\n"
    . "<form method=\"post\" action=\"install.php?action=" . $op . "\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"2\" border=\"0\">\n"
    . "<tr><td colspan=\"2\" align=\"center\"><b>" . _CONFIG . "</b></td></tr>\n"
    . "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"langue\" /></td></tr>\n"
    . "<tr><td>" . _DBHOST . " :</td><td><input type=\"text\" name=\"db_host\" size=\"40\" /></td></tr>\n"
	. "<tr><td><img src=\"img/tuyau.png\"/></td><td>"._INSTALLHOST."</td></tr>\n"
    . "<tr><td>" . _DBUSER . " : </td><td><input type=\"text\" name=\"db_user\" size=\"40\" /></td></tr>\n"
	. "<tr><td><img src=\"img/tuyau.png\"/></td><td>"._INSTALLDBUSER."</td></tr>\n"
    . "<tr><td>" . _DBPASS . " :</td><td><input type=\"password\" name=\"db_pass\" size=\"10\" /></td></tr>\n"
	. "<tr><td><img src=\"img/tuyau.png\"/></td><td>"._INSTALLDBPASS."</td></tr>\n"
    . "<tr><td>" . _DBPREFIX . " :</td><td><input type=\"text\" name=\"prefix\" size=\"10\" /></td></tr>\n"
	. "<tr><td><img src=\"img/tuyau.png\"/></td><td>"._INSTALLDBPREFIX."</td></tr>\n"
    . "<tr><td>" . _DBNAME . " :</td><td><input type=\"text\" name=\"db_name\" size=\"10\" /></td></tr>\n"
	. "<tr><td><img src=\"img/tuyau.png\"/></td><td>"._INSTALLDBDBNAME."</td></tr></table>\n"
    . "<div style=\"text-align: center;\"><br />" . _CHMOD . "<br /><br /><input type=\"submit\" name=\"ok\" value=\"" . _NEXT . "\" /></div></form></body></html>";
	}
}

function save_config($vars)
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

	redirect("install.php?action=edit_config_assistant&op=save_config&langue=" . $vars['langue'], 5);
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
	. "define('HASHKEY', '".addslashes(@sha1(uniqid(''), true))."');\n"
	. "\n"
	. "?>";

	$path = "conf.inc.php";
	@chmod ($path, 0666);
	@chmod ('./', 0777);

	if (is_writable($path) || (!file_exists($path) && is_writable('./')))
	{
	    $fp = @fopen("conf.inc.php", w);
	    fwrite($fp, $content);
	    fclose($fp);
	    @chmod ($path, 0644);

		copy("conf.inc.php", "extra/conf" . date('%Y%m%d%H%i') . '.php');

	    style(3,$vars['langue']);

		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "<big>" . _CONFIGSAVE . "<br />" . _CLICNEXTTOINSTALL . "</big>\n"
		. "</div>\n"
		. "</div>\n";
	    echo "<form method=\"post\" action=\"install.php?action=create_db&amp;langue=" . $vars['langue'] . "\" onsubmit=\"this.goButton.disabled=true; return true\">\n"
	    . "<div style=\"text-align: center;\"><input type=\"submit\" name=\"goButton\" value=\"" . _NEXT . "\" /></div></form></body></html>";
   }
   else
   {
	    style(3,$vars['langue']);

	    echo "<div><a href=\"#\" onclick=\"javascript:window.open('help/" . $vars['langue'] . "/install.html','Help','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=350,height=300');return(false)\">\n"
	    . "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a></div>\n"
	    . "<div style=\"text-align: center;\"><br /><br /><h3>" . _INSTALLNK . "</h3></div>\n"
	    . "<form method=\"post\" action=\"install.php?action=save_config\">\n"
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
    case "save_config":
    save_config($_REQUEST);
    break;

    case "create_db":
    create_db();
    break;

    case "add_god":
    add_god($_REQUEST);
    break;

    case"edit_config":
    edit_config($_REQUEST['op']);
    break;

	case"edit_config_assistant":
    edit_config_assistant($_REQUEST['op']);
    break;

    case"install":
    install();
    break;

    case "check":
    	Requirement();
    	break;

    default:
		index();
		break;
}


?>
