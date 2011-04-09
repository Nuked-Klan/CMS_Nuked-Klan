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
include ("globals.php");
include ("conf.inc.php");
include("nuked.php");
include ("Includes/constants.php");

global $nuked, $language, $theme, $bgcolor1, $bgcolor2, $bgcolor3;

if (preg_match("`\.\.`", $theme) || preg_match("`\.\.`", $language) || preg_match("`[A-Za-z]`", $_GET['ip_ban']))
{
    die("<br /><br /><br /><div style=\"text-align: center;\"><big>What are you trying to do ?</big></div>");
}

$theme = trim($theme);
$language = trim($language);

include ("themes/" . $theme . "/colors.php");
translate("lang/" . $language . ".lang.php");

$ip_ban = $_GET['ip_ban'];

$sql = mysql_query("SELECT texte, dure FROM " . BANNED_TABLE . " WHERE ip = '" . $ip_ban . "'");
$count = mysql_num_rows($sql);

if ($count > 0)
{
    list($texte_ban, $dure) = mysql_fetch_array($sql);
    $texte_ban = htmlentities($texte_ban);
    $texte_ban = BBcode($texte_ban);

    setcookie("ip_ban", "$ip_ban", time() + 9999999, "", "", "");

    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
    . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
    . "<head><title>" . $nuked['name'] . " :: " . $nuked['slogan'] . " ::</title>\n"
    . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
    . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
    . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
    . "<body style=\"background : " . $bgcolor2 . ";\"><div><br /><br /><br /><br /><br /><br /><br /><br /></div>\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor3 . ";\" cellspacing=\"0\" cellpadding=\"20\">\n"
    . "<tr><td style=\"background: " . $bgcolor1 . ";\" align=\"center\"><big><b>" . $nuked['name'] . " - " . $nuked['slogan'] . "</b><br /><br />\n"
    . _IPBANNED . "</big>\n";

    if ($texte_ban != "")
    {
	echo "<br /><table width=\"100%\"><tr><td align=\"left\"><hr style=\"color: " . $bgcolor3 . ";height: 1px;\" />\n"
	. "<big><b>" . _REASON . "</b><br>" . $texte_ban . "</big></td></tr></table>\n";
    }
	
	if($dure == 0)
	{
		$temps = _AVIE;
	}
	else if ($dure == 86400)
	{
		$temps = _1JOUR;
	}
	else if ($dure == 604800)
	{
		$temps = _7JOUR;
	}
	else if ($dure == 2678400)
	{
		$temps = _1MOIS;
	}
	else if ($dure == 31708800)
	{
		$temps = _1AN;
	}
    echo "</td></tr>\n"
	. "<tr><td>"._DURE."</td><td>\n"
	
	."".$temps."</td></tr>\n"
	."</table><div style=\"text-align: center;\"><br />" . _CONTACTWEBMASTER . " : <a href=\"mailto:" . $nuked['mail'] . "\">" . $nuked['mail'] . "</a></div></body></html>";
}
?>