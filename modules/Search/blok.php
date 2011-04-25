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

global $nuked, $language, $user;
translate("modules/Search/lang/" . $language . ".lang.php");

$sql2 = mysql_query("SELECT active FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
list($active) = mysql_fetch_array($sql2);
if ($active == 3 || $active == 4)
{
    echo "<form method=\"post\" action=\"index.php?file=Search&amp;op=mod_search\">\n"
    . "<table width=\"100%\"cellspacing=\"0\" cellpadding=\"3\"><tr><td align=\"center\">\n"
    . "<input type=\"text\" name=\"main\" size=\"30\" />&nbsp;"
    . "<input type=\"submit\" name=\"submit\" value=\"" . _SEARCHFOR . "\" /></td></tr>\n"
    . "<tr><td align=\"center\">" . _COLUMN . " : <select name=\"module\">\n"
    . "<option value=\"\">" . _SALL . "</option>\n";

    $path = "modules/Search/rubriques/";
    $modules = array();
    $handle = opendir($path);
    while ($mod = readdir($handle))
    {
        if ($mod != "." && $mod != ".." && $mod != "index.html")
        {
            $i++;
            $mod = str_replace(".php", "", $mod);
            $perm = nivo_mod($mod);
            if (!$perm) $perm = 0;
            if ($user[1] >= $perm && $perm > -1)
            {
                $umod = strtoupper($mod);
                $modname = "_S" . $umod;
                if (defined($modname)) $modname = constant($modname);
                else $modname = $mod;
        	array_push($modules, $modname . "|" . $mod);
            }
        }
    }
    natcasesort($modules);
    foreach($modules as $value)
    {
	$temp = explode("|", $value);
	if ($temp[1] == $_REQUEST['file']) $selected = "selected=\"selected\"";
	else $selected = "";
	echo "<option value=\"" . $temp[1] . "\" " . $selected . ">" . $temp[0] . "</option>\n";
    }
    echo "</select></td></tr></table></form>\n";
}
else
{
    echo "<form method=\"post\" action=\"index.php?file=Search&amp;op=mod_search\">\n"
    . "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n"
    . "<tr><td align=\"center\"><input type=\"hidden\" name=\"module\" value=\"\" /><input type=\"text\" name=\"main\" size=\"20\" /></td></tr>\n"
    . "<tr><td align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit\" value=\"" . _SEARCHFOR . "\" /></td></tr>\n"
    . "<tr><td align=\"center\"><a href=\"index.php?file=Search\">" . _ADVANCEDSEARCH . "</a></td></tr></table></form>\n";
}

?>
