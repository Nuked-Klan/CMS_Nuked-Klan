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

global $nuked, $language;
translate("modules/Search/lang/" . $language . ".lang.php");
translate("modules/404/lang/" . $language . ".lang.php");

opentable();

if($_REQUEST['op'] != "sql")
{
	echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"90%\" cellspacing=\"1\" cellpadding=\"1\" border=\"0\"><tr>\n"
	. "<td><br /><div style=\"text-align: center;\"><big><b>" . $nuked['name'] . "</b></big></div><br />\n"
	. _NOEXIST . "<br /><br /><div style=\"text-align: center;\"><a href=\"index.php\"><b>"._BACK."</b></a></div></td></tr></table><br />\n";
echo "<div style=\"text-align:center;\"><form method=\"post\" action=\"index.php?file=Search&amp;op=mod_search\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n"
    . "<tr><td align=\"center\"><input type=\"hidden\" name=\"module\" value=\"\" /><input type=\"text\" name=\"main\" size=\"25\" /></td></tr>\n"
    . "<tr><td align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit\" value=\"" . _SEARCHFOR . "\" /></td></tr>\n"
    . "<tr><td align=\"center\"><a href=\"index.php?file=Search\">" . _ADVANCEDSEARCH . "</a></td></tr></table></form></div>\n";
	}
else
{
echo "<br /><div style=\"text-align:center;\">Veulliez nous excuser, la page demandée est actuellement indisponible, l'administrateur a été averti! Merci<br />";
echo "<br /><form method=\"post\" action=\"index.php?file=Search&amp;op=mod_search\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n"
    . "<tr><td align=\"center\"><input type=\"hidden\" name=\"module\" value=\"\" /><input type=\"text\" name=\"main\" size=\"25\" /></td></tr>\n"
    . "<tr><td align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit\" value=\"" . _SEARCHFOR . "\" /></td></tr>\n"
    . "<tr><td align=\"center\"><a href=\"index.php?file=Search\">" . _ADVANCEDSEARCH . "</a></td></tr></table></form></div>\n";
}
closetable();

?>
