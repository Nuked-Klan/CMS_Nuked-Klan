<?php
//-------------------------------------------------------------------------//
//  Nuked-KlaN - PHP Portal                                                //
//  http://www.nuked-klan.org                                              //
//-------------------------------------------------------------------------//
//  This program is free software. you can redistribute it and/or modify   //
//  it under the terms of the GNU General Public License as published by   //
//  the Free Software Foundation; either version 2 of the License.         //
//-------------------------------------------------------------------------//

if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $user, $language;
translate("modules/Stats/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
admintop();

if (!$user)
{
    $visiteur = 0;
} 
else
{
    $visiteur = $user[1];
} 
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);
if ($visiteur >= $level_admin && $level_admin > -1)
{
    function main()
    {
	global $nuked, $language;

	echo "<script type=\"text/javascript\">\n"
	. "<!--\n"
	. "\n"
	. "function del()\n"
	. "{\n"
	. "if (confirm('" . _DELETE . "'))\n"
	. "{document.location.href = 'index.php?file=Stats&page=admin&op=del';}\n"
	. "}\n"
	. "\n"
	. "// -->\n"
	. "</script>\n";

	echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _ADMINSTATS . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Stats.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><br /><br /><div style=\"text-align: center;\"><b><a href=\"javascript:del();\">"._VIDERSTATS."</a></b><br />\n"
	. "<br /><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";

    }
    function del()
    {
	global $nuked, $user;
	
	$sql = mysql_query("DELETE FROM ".$nuked['prefix']."_stats_visitor");
	// Action
		$texteaction = "". _ACTIONDELSTATS .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
	//Fin action
	echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _VIDER . "\n"
		. "</div>\n"
		. "</div>\n";
		
	redirect("index.php?file=Stats&page=admin", 2);
    }
	
    switch($_REQUEST['op'])
    {
	case "del":
		del();
	break;
	default:
        main();
	break;
    }

} 
else if ($level_admin == -1)
{
echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
} 
else if ($visiteur > 1)
{
       echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
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

adminfoot();

?>