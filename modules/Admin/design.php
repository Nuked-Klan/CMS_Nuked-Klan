<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $user, $nuked, $language;


function admintop()
{
global $user, $nuked, $language;
translate("modules/Admin/lang/" . $language . ".lang.php");

if (!$user)
{
    $visiteur = 0;
}
else
{
    $visiteur = $user[1];
}
if($visiteur < 2)
{
redirect("index.php?file=User",0);
}
?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head>
    <meta name="keywords" content="<?php echo $nuked['keyword'] ?>" />
    <meta name="Description" content="<?php echo $nuked['description'] ?>" />
	<meta http-equiv="content-style-type" content="text/css" />
	<title><?php echo $nuked['name'] ?> - <?php echo $nuked['slogan'] ?></title>
	<link rel="shortcut icon"  href="images/favicon.ico" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="content-style-type" content="text/css" />
		<!--                       CSS                       -->
		<!-- Reset Stylesheet -->
		<link rel="stylesheet" href="modules/Admin/css/reset.css" type="text/css" media="screen" />
		<!-- Main Stylesheet -->
		<link rel="stylesheet" href="modules/Admin/css/style.css" type="text/css" media="screen" />
		<!-- Invalid Stylesheet. This makes stuff look pretty. Remove it if you want the CSS completely valid -->
		<link rel="stylesheet" href="modules/Admin/css/invalid.css" type="text/css" media="screen" />	
		
		
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
			function maFonctionAjax(texte)
			{
			  var OAjax;
			  if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
			  else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP'); 
			  OAjax.open('POST',"index.php?file=Admin&page=discussion",true);
			  OAjax.onreadystatechange = function()
			  {
				  if (OAjax.readyState == 4 && OAjax.status==200)
				  {
					  if (document.getElementById) 
					  {    
						 document.getElementById("affichefichier").innerHTML = OAjax.responseText;
						 document.getElementById("texte").value = "";
					  }     
				  }
			  }
			  OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			  OAjax.send('texte='+texte+'');
			  $(document).trigger('close.facebox')
			}
			var xtralink = "non";
			function screenon(lien,lien2)
			{
				xtralink = lien2;
				document.getElementById("iframe").innerHTML = "<iframe style=\"border:0px;\" width=\"100%\" height=\"80%\" src=\""+lien+"\"></iframe>";
				<?php
					if ($nuked['screen'] == "off")
					{
				?>
						screenoff();
				<?php
					}
					else
					{
				?>
						document.getElementById("screen").style.display="block";
				<?php
					}
				?>
			}
			function screenoff()
			{
				document.getElementById("screen").style.display="none";
				if (xtralink != "non" )
				{
					window.location = xtralink;
				}
			}
			function maFonctionAjax2(texte,type)
			{
			  var OAjax;
			  if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
			  else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP'); 
			  OAjax.open('POST',"index.php?file=Admin&page=notification",true);
			  OAjax.onreadystatechange = function()
			  {
				  if (OAjax.readyState == 4 && OAjax.status==200)
				  {
					  if (document.getElementById) 
					  {  
						 document.getElementById("texte").value = "";
						 document.getElementById("type").value = "";
					  }     
				  }
			  }
			  OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			  OAjax.send('texte='+texte+'&type='+type+'');
			  $(document).trigger('close.facebox')
			}
			function styling(name,taille,couleur,gras,italique,souligne)
			{
			  var OAjax;
			  if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
			  else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP'); 
			  OAjax.open('POST',"index.php?file=Admin&page=editeur&op=style",true);
			  OAjax.onreadystatechange = function()
			  {
				  if (OAjax.readyState == 4 && OAjax.status==200)
				  {
					  if (document.getElementById) 
					  {  
						document.getElementById("couleur2").value = "";
					  }     
				  }
			  }
			  OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			  OAjax.send('name='+name+'&taille='+taille+'&couleur='+couleur+'&gras='+gras+'&italique='+italique+'&souligne='+souligne+'');
			  document.location = document.location;
			}
			function maFonctionAjax3(texte)
			{
			  var OAjax;
			  if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
			  else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP'); 
			  OAjax.open('POST',"modules/"+texte+"/menu/<?php echo $language; ?>/menu.php",true);
			  OAjax.onreadystatechange = function()
			  {
				  if (OAjax.readyState == 4 && OAjax.status==200)
				  {
					  if (document.getElementById) 
					  {  
					  document.getElementById("1").innerHTML = OAjax.responseText;
					  }     
				  }
			  }
			  OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			  OAjax.send();
			}
		function del(id)
			{
			  var OAjax;
			  if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
			  else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP'); 
			  OAjax.open('POST',"index.php?file=Admin&page=notification&op=delete",true);
			  OAjax.onreadystatechange = function()
			  {
				  if (OAjax.readyState == 4 && OAjax.status==200)
				  {
					  if (document.getElementById) 
					  {  
						   
					  }     
				  }
			  }
			  OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
			  OAjax.send('id='+id+'');
			} 
		</script>
		<!-- TinyMCE -->
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
		plugins : "pagebreak,layer,table,insertcode,save,advimage,advlink,emotions,spellchecker,inlinepopups,preview,print,contextmenu,paste,directionality,fullscreen,wordcount,advlist,autosave",
		editor_deselector : "noediteur",
		// Theme options
		theme_advanced_buttons1 : "save,newdocument,restoredraft,|,cut,copy,paste,pastetext,pasteword,|,undo,redo,|,print,|,fullscreen,|,preview,|,help",
		theme_advanced_buttons2 : "styleselect,fontselect,fontsizeselect,|,link,unlink,anchor,|,emotions,image,media,forecolor,backcolor",
		theme_advanced_buttons3 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,hr,|,outdent,indent,|,removeformat,|,spellchecker",
		theme_advanced_buttons4 : "tablecontrols,|cite,abbr|,blockquote,insertcode,sub,sup,|,charmap,pagebreak",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
		content_css : "editeur/plugins/insertcode/insertcode.css",
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
		</script>
		<style>
		.defaultSkin table.mceLayout tr.mceFirst
		{
		font-size: 13px;
		background: #fff  url('modules/Admin/images/bg-form-field.gif') top left repeat-x;
		border: 1px solid #d5d5d5;
		}
		</style>
		<!-- /TinyMCE -->
		
	</head>
	<body>
	<div id="screen" onclick="screenoff()" style="display:none;position:absolute;width:100%;height:100%;background:  url(modules/Admin/images/bg.png) repeat;z-index:10000;">
	<div id="iframe" style="margin-left:5%;margin-top:5%; width:90%;height:90%;">
		
	</div>
	<div style="display:block;width:295px;height:25px;background:url(images/croix.png) no-repeat;position:absolute;right:3%;bottom:3%;z-index:20000;">&nbsp;</div>
	</div>
	<div id="body-wrapper"> <!-- Wrapper for the radial gradient background -->
		<div id="sidebar"><div id="sidebar-wrapper"> <!-- Sidebar with logo and menu -->
			<!-- Logo (221px wide) -->
			<a href="http://www.nuked-klan.org" target="_blanck"><img id="logo" src="modules/Admin/images/logo.png" alt="Simpla Admin logo" /></a>
			<!-- Sidebar Profile links -->
			<div id="profile-links">
				<?php echo _BONJOUR; ?> <a href="index.php?file=User" title="Editer son profil"><?php echo $user[2];?></a>, <?php echo _VOIR; ?> <a href="#messages" rel="modal" title="messages"><?php echo _MESSAGES; ?></a><br />
				<br />
				<a onclick="javascript:screenon('index.php', 'non');return false" href=""  title="Voir le Site"><?php echo _VOIRSITE; ?></a> | <a href="index.php?file=Admin&amp;page=deconnexion" title="Sign Out"><?php echo _DECONNEXION; ?></a>
				<br /><a href="index.php"  title="retourner sur le site"><?php echo _RETOURNER; ?></a>
			</div>
			<ul id="main-nav">  <!-- Accordion Menu -->
				<li>
					<?php
					if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "")
					{
					?>
					<a href="index.php?file=Admin" class="nav-top-item no-submenu current">
					<?php
					}
					else
					{
					?>
					<a href="index.php?file=Admin" class="nav-top-item no-submenu">
					<?php
					}
					 echo _PANNEAU;
					 ?>
					</a>       
				</li>
				<li>
					<?php
					if($_REQUEST['file'] == "Admin" && ($_REQUEST['page'] == "setting" || $_REQUEST['page'] == "maj" || $_REQUEST['page'] == "phpinfo" || 
					$_REQUEST['page'] == "mysql" || $_REQUEST['page'] == "action" || $_REQUEST['page'] == "erreursql"))
					{
					?>
					<a href="#" class="nav-top-item current">
					<?php
					}
					else
					{
					?>
					<a href="#" class="nav-top-item">
					<?php
					}
					echo _PARAMETRE; ?>
					</a>
					<ul>
						<li>
							<?php
								if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "setting")
								{
							?>
								<a class="current" href="index.php?file=Admin&amp;page=setting">
							<?php
								}
								else
								{
							?>
								<a href="index.php?file=Admin&amp;page=setting">
							<?php
								} 
								echo _PREFGEN; 
							?>
							</a>
						</li>
						<li>
							<?php
							if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "mysql")
							{
							?>
								<a class="current" href="index.php?file=Admin&amp;page=mysql">
							<?php
							}
							else
							{
							?>
								<a href="index.php?file=Admin&amp;page=mysql">
							<?php
							} 
							echo _GMYSQL ; 
							?>
							</a>
						</li> <!-- Add class "current" to sub menu items also -->
						<li>
							<?php
							if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "phpinfo")
							{
							?>
								<a class="current" href="index.php?file=Admin&amp;page=phpinfo">
							<?php
							}
							else
							{
							?>
								<a href="index.php?file=Admin&amp;page=phpinfo">
							<?php
							}
							echo _ADMINPHPINFO;
							?>
							</a>
						</li>
						<li>
							<?php
							if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "maj")
							{
							?>
							<a class="current" href="index.php?file=Admin&amp;page=maj">
							<?php
							}
							else
							{
							?>
							<a href="index.php?file=Admin&amp;page=maj">
							<?php
							} 
							echo _CHECKUPDATE;
							?>
							</a>
						</li>
						<li>
							<?php
							if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "action")
							{
							?>
							<a class="current" href="index.php?file=Admin&amp;page=action">
							<?php
							}
							else
							{
							?>
							<a href="index.php?file=Admin&amp;page=action">
							<?php
							} 
							echo _ACTIONM; 
							?>
							</a>
						</li>
						<li>
							<?php
							if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "erreursql")
							{
							?>
							<a class="current" href="index.php?file=Admin&amp;page=erreursql">
							<?php
							}
							else
							{
							?>
							<a href="index.php?file=Admin&amp;page=erreursql">
							<?php
							} 
							echo _ERRORBDD;
							?>
							</a>
						</li>
					</ul>
				</li>
				<li>
					<?php
					if($_REQUEST['file'] == "Admin" && ($_REQUEST['page'] == "user" || $_REQUEST['page'] == "theme" || $_REQUEST['page'] == "modules" || 
					 $_REQUEST['page'] == "block" || $_REQUEST['page'] == "menu" || $_REQUEST['page'] == "smilies" || $_REQUEST['page'] == "games" || $_REQUEST['page'] == "editeur"))
					{
					?>
					<a href="#" class="nav-top-item current">
					<?php
					}
					else
					{
					?>
					<a href="#" class="nav-top-item">
					<?php
					} echo _GESTIONS; ?>
					</a>
						<ul>
							<li>
								<?php
								if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "user")
								{
								?>
									<a class="current" href="index.php?file=Admin&amp;page=user">
								<?php
								}
								else
								{
								?>
									<a href="index.php?file=Admin&amp;page=user">
								<?php
								} echo _UTILISATEURS; ?>
								</a>
							</li>
							<?php if(file_exists("themes/".$nuked['theme']."/admin.php"))
							{
								echo "<li>";
									if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "theme")
									{
									?>
										<a class="current" href="index.php?file=Admin&amp;page=theme">
									<?php
									}
									else
									{
									?>
										<a href="index.php?file=Admin&amp;page=theme">
									<?php
									}
								echo "". _THEMIS ."</a></li>";
							}
							?>
							<li>
								<?php
								if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "modules")
								{
								?>
									<a class="current" href="index.php?file=Admin&amp;page=modules">
								<?php
								}
								else
								{
								?>
									<a href="index.php?file=Admin&amp;page=modules">
								<?php
								} echo _INFOMODULES; ?></a>
							</li>
							<li>
								<?php
								if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "block")
								{
								?>
									<a class="current" href="index.php?file=Admin&amp;page=block">
								<?php
								}
								else
								{
								?>
									<a href="index.php?file=Admin&amp;page=block">
								<?php
								} 
								echo _BLOCK; ?>
								</a>
							</li>
							<li>
								<?php
								if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "menu")
								{
								?>
									<a class="current" href="index.php?file=Admin&amp;page=menu">
								<?php
								}
								else
								{
								?>
									<a href="index.php?file=Admin&amp;page=menu">
								<?php
								} 
								echo _MENU; ?>
								</a>
							</li>
							<li>
								<?php
								if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "smilies")
								{
								?>
									<a class="current" href="index.php?file=Admin&amp;page=smilies">
								<?php
								}
								else
								{
								?>
									<a href="index.php?file=Admin&amp;page=smilies">
								<?php
								} 
								echo _SMILEY; ?>
								</a>
							</li>
							<li>
								<?php
								if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "games")
								{
								?>
									<a class="current" href="index.php?file=Admin&amp;page=games">
								<?php
								}
								else
								{
								?>
									<a href="index.php?file=Admin&amp;page=games">
								<?php
								} 
								echo _JEUX; ?>
								</a>
							</li>
							<li>
								<?php
								if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "editeur")
								{
								?>
									<a class="current" href="index.php?file=Admin&amp;page=editeur">
								<?php
								}
								elseif ($user[1] == 9)
								{
								?>
									<a href="index.php?file=Admin&amp;page=editeur">
								<?php
								} 
								echo _EDITEUR; 
								?>
								</a>
							</li>
						</ul>
					</li>
					<li>
						<?php
							$modules = array();
							$sql = mysql_query("SELECT nom FROM " . MODULES_TABLE . " WHERE '" . $visiteur . "' >= admin AND niveau > -1 AND admin > -1 ORDER BY nom");
							while (list($mod) = mysql_fetch_array($sql))
							{
								if ($mod == "Gallery")
								{
									$modname = _NAMEGALLERY;
								}
								else if ($mod == "Calendar")
								{
									$modname = _NAMECALANDAR;
								}
								else if ($mod == "Defy")
								{
									$modname = _NAMEDEFY;
								}
								else if ($mod == "Download")
								{
									$modname = _NAMEDOWNLOAD;
								}
								else if ($mod == "Guestbook")
								{
									$modname = _NAMEGUESTBOOK;
								}
								else if ($mod == "Irc")
								{
									$modname = _NAMEIRC;
								}
								else if ($mod == "Links")
								{
									$modname = _NAMELINKS;
								}
								else if ($mod == "Wars")
								{
									$modname = _NAMEMATCHES;
								}
								else if ($mod == "News")
								{
									$modname = _NAMENEWS;
								}
								else if ($mod == "Recruit")
								{
									$modname = _NAMERECRUIT;
								}
								else if ($mod == "Sections")
								{
									$modname = _NAMESECTIONS;
								}
								else if ($mod == "Server")
								{
									$modname = _NAMESERVER;
								}
								else if ($mod == "Suggest")
								{
									$modname = _NAMESUGGEST;
								}
								else if ($mod == "Survey")
								{
									$modname = _NAMESURVEY;
								}
								else if ($mod == "Forum")
								{
									$modname = _NAMEFORUM;
								}
								else if ($mod == "Textbox")
								{
									$modname = _NAMESHOUTBOX;
								}
								else if ($mod == "Comment")
								{
									$modname = _NAMECOMMENT;
								}
								else
								{
									$modname = $mod;
								}

								array_push($modules, $modname . "|" . $mod);
							}
							natcasesort($modules);
							foreach($modules as $value)
							{
								$temp = explode("|", $value);
							
								if (is_file("modules/" . $temp[1] . "/admin.php"))
								{
									if ($_REQUEST['file'] == $temp[1] && $_REQUEST['page'] == "admin")
									{
									$modulecur = true;
									}
								}
							}
						if($modulecur == true)
						{
						echo "<a href=\"#\" class=\"nav-top-item current\">\n";
						
						}
						else
						{
						echo "<a href=\"#\" class=\"nav-top-item\">\n";
							 }
							echo _CONTENU."</a><ul>";
							foreach($modules as $value)
							{
								$temp = explode("|", $value);
							
								if (is_file("modules/" . $temp[1] . "/admin.php"))
								{
									if ($_REQUEST['file'] == $temp[1] && $_REQUEST['page'] == "admin")
									{
									echo "<li><a class=\"current\" href=\"index.php?file=" . $temp[1] . "&amp;page=admin\">" . $temp[0] . "</a><li>";
									$modulecur = true;
									}
									else
									{
									echo "<li><a href=\"index.php?file=" . $temp[1] . "&amp;page=admin\">" . $temp[0] . "</a><li>";
									}
								}
							}
					?>
				</ul>
			</li>
			<li>
				<?php
				if($_REQUEST['file'] == "Admin" && ($_REQUEST['page'] == "propos" || $_REQUEST['page'] == "licence"))
				{
				?>
					<a href="#" class="nav-top-item current">
				<?php
				}
				else
				{
				?>
					<a href="#" class="nav-top-item">
				<?php
				}
				echo _DIVERS;
				?>
				</a>
				<ul>
					<li><a href="http://www.nuked-klan.org/index.php?file=Forum" target="_blanck"><?php echo _OFFICIEL; ?></a></li>
					<li>
						<?php
						if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "licence")
						{
						?>
							<a class="current" href="index.php?file=Admin&amp;page=licence">
						<?php
						}
						else
						{
						?>
							<a href="index.php?file=Admin&amp;page=licence">
						<?php
						}  
						echo _LICENCE;
						?>
						</a>
					</li>
					<li>
					<?php
					if($_REQUEST['file'] == "Admin" && $_REQUEST['page'] == "propos")
					{
					?>
						<a class="current" href="index.php?file=Admin&amp;page=propos">
					<?php
					}
					else
					{
					?>
						<a href="index.php?file=Admin&amp;page=propos">
					<?php
					} 
					echo _PROPOS; 
					?>
					</a>
					</li>
				</ul>
			</li>
		</ul> <!-- End #main-nav -->
		<div id="messages" style="display: none"> <!-- Messages are shown when a link with these attributes are clicked: href="#messages" rel="modal"  -->
			<h3><?php echo _DISCUADMIN; ?>:</h3>			
			<div style="height:200px; overflow:auto;">	
				<?php
				$sql = mysql_query("SELECT date, pseudo, texte  FROM " . $nuked['prefix'] . "_discussion ORDER BY date DESC LIMIT 0, 16");
				while (list($date, $users, $texte) = mysql_fetch_array($sql))
				{
				$users = mysql_real_escape_string($users);
				$sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $users . "'");
				list($pseudo) = mysql_fetch_array($sql2);
				$date = strftime("%x", $date);
				?>
				<p>
					<strong><?php echo $date; ?></strong> <?php echo _BY; ?> <?php echo $pseudo; ?><br />
					<?php echo $texte; ?>
				</p>
				<?php
				}
				?>
			</div>	
			<form method="post" onsubmit="maFonctionAjax(this.texte.value);return false" action="">
				<h4><?php echo _NEWMSG; ?>:</h4>
				<fieldset>
					<textarea class="noediteur" name="texte" cols="79" rows="5"></textarea>
				</fieldset>
				<fieldset>
					<input class="button" type="submit" value="Send" />
				</fieldset>
			</form> 
			<div id="affichefichier"></div>
		</div> <!-- End #messages -->
	</div>
</div> <!-- End #sidebar -->
<div id="main-content"> <!-- Main Content Section with everything -->
	<div style="width:100%;">
		<noscript> <!-- Show a notification if the user has disabled javascript -->
			<div class="notification error png_bg">
				<div>
					<?php echo _JAVA; ?>
				</div>
			</div>
		</noscript>
<?php
}
function adminfoot()
{
?>
</div></div></body>
</html>
<?php
}
?>