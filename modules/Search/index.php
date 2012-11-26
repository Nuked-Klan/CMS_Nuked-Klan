<?php 
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")){
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
} 

global $nuked, $language;

translate("modules/Search/lang/" . $language . ".lang.php");
compteur("Search");
opentable();

function index(){
    global $nuked, $user, $bgcolor1, $bgcolor2, $bgcolor3;

    $_REQUEST['main'] = stripslashes($_REQUEST['main']);
    
    if ($_REQUEST['searchtype'] == "matchor") $checked1 = "checked=\"checked\"";
    else if ($_REQUEST['searchtype'] == "matchexact") $checked3 = "checked=\"checked\"";
    else $checked2 = "checked=\"checked\"";

    if ($_REQUEST['limit'] == 10) $checked4 = "checked=\"checked\"";
    else if ($_REQUEST['limit'] == 100) $checked6 = "checked=\"checked\"";
    else $checked5 = "checked=\"checked\"";

	echo "<br /><form method=\"post\" action=\"index.php?file=Search&amp;op=mod_search\">\n"
			. "<table style=\"background: " . $bgcolor3 . ";\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"3\">\n"
			. "<tr style=\"background: " . $bgcolor3 . ";\"><td colspan=\"2\" align=\"center\"><big><b>" . _SEARCHFOR . "</b></big></td></tr>\n"
			. "<tr style=\"background: " . $bgcolor2 . ";\"><td><b>" . _KEYWORDS . " :</b></td>\n"
			. "<td>&nbsp;<input type=\"text\" name=\"main\" size=\"30\" value=\"" . printSecuTags($_REQUEST['main']) . "\" /><br />\n"
			. "<input type=\"radio\" class=\"checkbox\" name=\"searchtype\" value=\"matchor\" " . $checked1 . " />" . _MATCHOR . "<br />\n"
			. "<input type=\"radio\" class=\"checkbox\" name=\"searchtype\" value=\"matchand\" " . $checked2 . " />" . _MATCHAND . "<br />\n"
			. "<input type=\"radio\" class=\"checkbox\" name=\"searchtype\" value=\"matchexact\" " . $checked3 . " />" . _MATCHEXACT . "</td></tr>\n"
			. "<tr style=\"background: " . $bgcolor2 . ";\"><td><b>" . _AUTHOR . " :</b></td><td>&nbsp;<input type=\"text\" size=\"30\" id=\"autor\" name=\"autor\"  value=\"" . printSecuTags($_REQUEST['autor']) . "\" /></td></tr>\n"
			. "<tr style=\"background: " . $bgcolor2 . ";\"><td><b>" . _COLUMN . " :</b> </td><td>&nbsp;<select name=\"module\"><option value=\"\">" . _SALL . "</option>\n";

    $path = "modules/Search/rubriques/";
    $modules = array();
    $handle = opendir($path);
	
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
		if ($temp[1] == $_REQUEST['module']) $selected = "selected=\"selected\"";
		else $selected = "";
		echo "<option value=\"" . $temp[1] . "\" " . $selected . ">" . $temp[0] . "</option>\n";
    }

    echo "</select></td></tr><tr style=\"background: " . $bgcolor2 . ";\"><td><b>" . _NBANSWERS . " :</b></td><td>\n"
			. "&nbsp;<input type=\"radio\" class=\"checkbox\" name=\"limit\" value=\"10\" " . $checked4 . " />10"
			. "&nbsp;<input type=\"radio\" name=\"limit\" class=\"checkbox\" value=\"50\" " . $checked5 . " />50"
			. "&nbsp;<input type=\"radio\" name=\"limit\" class=\"checkbox\" value=\"100\" " . $checked6 . " />100</td></tr>\n"
			. "<tr style=\"background: " . $bgcolor2 . ";\"><td align=\"center\" colspan=\"2\">\n"
			. "<input type=\"submit\" name=\"submit\" value=\"" . _SEARCHFOR . "\" /></td></tr></table></form><br />\n";
}

function mod_search($module, $main, $autor, $limit, $searchtype){
    global $nuked, $user, $bgcolor1, $bgcolor2, $bgcolor3;

    if (preg_match("`%20union%20`i", $main) ||preg_match("`union`i ", $main) || preg_match("`\*union\*`i", $main) || preg_match("`\+union\+`i", $main) || preg_match("`\*`i", $main)){
		echo "<br /><br /><div style=\"text-align: center;\"><big>What are you trying to do ?</big></div><br /><br />";
		redirect("index.php?file=Search", 2);
		closetable();
		footer();
		exit();
    }	

    if (!$limit) $limit = 50;
    if (!$searchtype) $searchtype = "matchand";

    index();

    echo "<div style=\"text-align: center;\"><br /><big><b>" . _SEARCHRESULT . "</b></big></div><br />\n";

    $main = trim($main);
    $autor = trim($autor);

    if ($main != "" || $autor!=""){
        if (strlen($main) < 3 && strlen($autor) < 3){
            echo "<div style=\"text-align: center;\"><br />" . _3CHARSMIN . "</div><br /><br />\n";
            closetable();
            footer();
            exit();
        } 

        $main = mysql_real_escape_string(stripslashes($main));
        $autor = htmlentities($autor, ENT_QUOTES);
	    $autor = nk_CSS($autor);
        $autor = mysql_real_escape_string(stripslashes($autor));
        $search = explode(" ", $main);
        $i = 0;

        $path = "modules/Search/rubriques/";
        $handle = opendir($path);
		
        while ($mod = readdir($handle)){
            if ($mod != "." && $mod != ".." && $mod != "index.html"){
                $i++;
                $mod = str_replace(".php", "", $mod);
                $perm = nivo_mod($mod);
                if (!$perm) $perm = 0;
				
                if ($user[1] >= $perm && $perm > -1 && ($module == $mod || $module == "")){
                    $umod = strtoupper($mod);
                    $modname = "_S" . $umod;
                    if (defined($modname)) $modname = constant($modname);
                    else $modname = $mod;
                    require("modules/Search/rubriques/" . $mod . ".php");
                } 
            } 
        } 
        $l = count($tab['module']);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $limit - $limit;
        $end = $start + $limit;
        if ($end > $l) $end = $l;

        echo "<div style=\"text-align: center;\"><b>" . _RETURN . "&nbsp;" . $l . "&nbsp;" . _RESULTS . "</b></div><br />\n";

        if ($l > $limit){ 
			$search_url = "index.php?file=Search&amp;op=mod_search&amp;main=" . urlencode($main) . "&amp;autor=" . $autor . "&amp;module=" . $module . "&amp;limit=" . $limit . "&amp;searchtype=" . $searchtype;
			number($l, $limit, $search_url);
		}

        echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellspacing=\"2\" cellpadding=\"3\" border=\"0\">\n";

        for($a = $start;$a < $end;$a++){
            if ($z == 0){
                $bg = $bgcolor2;
                $z++;
            } 
            else{
                $bg = $bgcolor1;
                $z = 0;
            }
			
            echo "<tr style=\"background: " . $bg . ";\"><td width=\"100%\"><a href=\"" . $tab['link'][$a] . "\">" . $tab['title'][$a] . "</a></td><td><big>" . $tab['module'][$a] . "</big></td></tr>\n";
        } 

        echo "</table>\n";

        if ($l > $limit){ 
			$search_url = "index.php?file=Search&amp;op=mod_search&amp;main=" . urlencode($main) . "&amp;autor=" . $autor . "&amp;module=" . $module . "&amp;limit=" . $limit . "&amp;searchtype=" . $searchtype;
			number($l, $limit, $search_url);
		}

		echo"<br />";
    } 
    else{
        echo "<div style=\"text-align: center;\"><br />" . _NOWORDS . "</div><br /><br />\n";
    } 
} 

switch ($_REQUEST['op']){
    case "mod_search":
    mod_search($_REQUEST['module'], $_REQUEST['main'], $_REQUEST['autor'], $_REQUEST['limit'], $_REQUEST['searchtype']);
    break;

    default:
    index();
     break;
}

closetable();
?>