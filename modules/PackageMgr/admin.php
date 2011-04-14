<?php
/***********************************************
 * PckageMgr - Gestionnaire de patch
 * ---------------------------------------------
 * Auteur : Bontiv <prog.bontiv@gmail.com>
 * Site web : http://remi.bonnetchangai.free.fr/
 * ---------------------------------------------
 * Ce fichier fait parti d'un module libre. Toutefois
 * je vous demanderez de respecter mon travail en ne
 * supprimant pas mon pseudo.
 ***********************************************/

if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $user, $language;
include("modules/Admin/design.php");
include('manager.php');

admintop();
$visiteur = !$user ? 0 : $user[1];

$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);

if ($visiteur >= $level_admin && $level_admin > -1)
{
	$page = new Manager(isset($_REQUEST['a'])?$_REQUEST['a']:'index');	
} 
else if ($level_admin == -1)
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">Ce module n'est pas activé<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}
else if ($visiteur > 1)
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">Vous n'avez pas le droit d'accéder à cette page.<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}
else
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}
echo '<center><p><i><span style="color:#AAAAAA">Package Manager - Cree par <a href="mailto:prog.bontiv@gmail.com">Bontiv</a></span></i></p></center>';
adminfoot();

?>