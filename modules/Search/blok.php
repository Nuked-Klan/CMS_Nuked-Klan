<?php
/**
 * blok.php
 *
 * Display block of Search module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or exit('You can\'t run this file alone.');

global $nuked, $language, $user;

translate('modules/Search/lang/'. $language .'.lang.php');


if ($active == 3 || $active == 4){
    echo "<form method=\"post\" action=\"index.php?file=Search&amp;op=mod_search\">\n"
			. "<table width=\"100%\"cellspacing=\"0\" cellpadding=\"3\"><tr><td align=\"center\">\n"
			. "<input type=\"text\" name=\"main\" size=\"30\" />&nbsp;"
			. "<input type=\"submit\" name=\"submit\" value=\"" . _SEARCHFOR . "\" /></td></tr>\n"
			. "<tr><td align=\"center\">" . _COLUMN . " : <select name=\"module\">\n"
			. "<option value=\"\">" . _SALL . "</option>\n";

    $path = "modules/Search/rubriques/";
    $modules = array();
    $handle = opendir($path);

    $i = 0;
    while ($mod = readdir($handle)){
        if ($mod != "." && $mod != ".." && $mod != "index.html"){
            $i++;
            $mod = str_replace(".php", "", $mod);
            $perm = nivo_mod($mod);
            if (!$perm) $perm = 0;

            if ($user[1] >= $perm && $perm > -1){
                $umod = strtoupper($mod);
                $modname = "_S" . $umod;
                if (defined($modname)) $modname = constant($modname);
                else $modname = $mod;
				array_push($modules, $modname . "|" . $mod);
            }
        }
    }

    natcasesort($modules);

    foreach($modules as $value){
		$temp = explode("|", $value);
		if ($temp[1] == $_REQUEST['file']) $selected = "selected=\"selected\"";
		else $selected = "";
		echo "<option value=\"" . $temp[1] . "\" " . $selected . ">" . $temp[0] . "</option>\n";
    }

    echo "</select></td></tr></table></form>\n";
}
else{
    echo "<form method=\"post\" action=\"index.php?file=Search&amp;op=mod_search\">\n"
    . "<p style=\"text-align: center\"><input type=\"hidden\" name=\"module\" value=\"\" /><input type=\"text\" name=\"main\" style=\"width:90%;\" /><br />\n"
    . "<input type=\"submit\" class=\"button\" name=\"submit\" value=\"" . _SEARCHFOR . "\" /><br />\n"
    . "<a href=\"index.php?file=Search\">" . _ADVANCEDSEARCH . "</a></p></form>\n";
}
?>
