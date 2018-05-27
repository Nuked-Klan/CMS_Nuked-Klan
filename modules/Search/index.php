<?php
/**
 * index.php
 *
 * Frontend of Search module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

global $nuked, $language;

translate('modules/Search/lang/'. $language .'.lang.php');
compteur('Search');
opentable();

function index(){
    global $nuked, $user, $bgcolor1, $bgcolor2, $bgcolor3;

    $arrayRequest = array('module', 'main', 'autor', 'limit', 'searchtype');

    foreach($arrayRequest as $key){
        if(!array_key_exists($key, $_REQUEST)){
            $_REQUEST[$key] = '';
        }
    }

    $_REQUEST['main'] = stripslashes($_REQUEST['main']);

    $checked1 = $checked2 = $checked3 = $checked4 = $checked5 = $checked6 = '';

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
			. "<tr style=\"background: " . $bgcolor2 . ";\"><td><b>" . __('AUTHOR') . " :</b></td><td>&nbsp;<input type=\"text\" size=\"30\" id=\"autor\" name=\"autor\"  value=\"" . printSecuTags($_REQUEST['autor']) . "\" /></td></tr>\n"
			. "<tr style=\"background: " . $bgcolor2 . ";\"><td><b>" . _COLUMN . " :</b> </td><td>&nbsp;<select name=\"module\"><option value=\"\">" . _SALL . "</option>\n";

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

            if (($user && $user[1] >= $perm) && $perm > -1){
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

function mod_search(){
    global $nuked, $user, $bgcolor1, $bgcolor2, $bgcolor3;

    $arrayRequest = array('module', 'main', 'autor', 'limit', 'searchtype');

    foreach($arrayRequest as $key){
        if(array_key_exists($key, $_REQUEST)){
            ${$key} = $_REQUEST[$key];
        }
        else{
            ${$key} = '';
        }
    }

    if (preg_match("`%20union%20`i", $main) ||preg_match("`union`i ", $main) || preg_match("`\*union\*`i", $main) || preg_match("`\+union\+`i", $main) || preg_match("`\*`i", $main)){
		printNotification('What are you trying to do ?', 'error');
		redirect("index.php?file=Search", 2);
		closetable();
		return;
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
            return;
        }

        $main = nkDB_realEscapeString(stripslashes($main));
        $autor = nkHtmlEntities($autor, ENT_QUOTES);
	    $autor = nk_CSS($autor);
        $autor = nkDB_realEscapeString(stripslashes($autor));
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

                if (($user && $user[1] >= $perm) && $perm > -1 && ($module == $mod || $module == "")){
                    $umod = strtoupper($mod);
                    $modname = "_S" . $umod;
                    if (defined($modname)) $modname = constant($modname);
                    else $modname = $mod;
                    require("modules/Search/rubriques/" . $mod . ".php");
                }
                else{
                    $tab = array('module' => array(), 'title' => array(), 'link' => array());
                }
            }
        }
        $l = count($tab['module']);

        if(array_key_exists('p', $_REQUEST)){
            $page = $_REQUEST['p'];
        }
        else{
            $page = 1;
        }
        $start = $page * $limit - $limit;
        $end = $start + $limit;
        if ($end > $l) $end = $l;

        echo "<div style=\"text-align: center;\"><b>" . _RETURN . "&nbsp;" . $l . "&nbsp;" . _RESULTS . "</b></div><br />\n";

        if ($l > $limit){
			$search_url = "index.php?file=Search&amp;op=mod_search&amp;main=" . urlencode($main) . "&amp;autor=" . $autor . "&amp;module=" . $module . "&amp;limit=" . $limit . "&amp;searchtype=" . $searchtype;
			number($l, $limit, $search_url);
		}

        echo "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellspacing=\"2\" cellpadding=\"3\" border=\"0\">\n";

        $z = 0;
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
        printNotification(_NOWORDS, 'error');
    }
}

switch ($GLOBALS['op']){
    case "mod_search":
    mod_search();
    break;

    default:
    index();
     break;
}

closetable();

?>
