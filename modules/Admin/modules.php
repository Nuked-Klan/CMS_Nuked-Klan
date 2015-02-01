<?php
/**
 * @version     1.7.10
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
if (!defined("INDEX_CHECK"))
{
    die ("<div style=\"text-align: center;\">You cannot open this page directly</div>");
}

global $user, $language;
translate("modules/Admin/lang/" . $language . ".lang.php");
include_once ('Includes/hash.php');
include("modules/Admin/design.php");
if (!$user)
{
    $visiteur = 0;
}
else
{
    $visiteur = $user[1];
}

admintop();

if ($visiteur == 9)
{
    function edit_module($mid)
    {
        global $nuked, $language;

        $sql = mysql_query("SELECT id, nom, niveau, admin FROM " . MODULES_TABLE . " WHERE id = '" . $mid . "'");
        list($mid, $nom, $niveau, $level) = mysql_fetch_array($sql);

        if ($niveau > -1 && $level > -1)
        {
            $button = "&nbsp;&nbsp;&nbsp;<input class=\"button\" type=\"button\" value=\"" . _OFFMODULE . "\" onclick=\"document.location='index.php?file=Admin&amp;page=modules&amp;op=desactive&amp;mid=" . $mid . "'\" />";
            $read = "";
        }
        else
        {
            $button = "&nbsp;&nbsp;&nbsp;<input class=\"button\" type=\"button\" value=\"" . _ONMODULE . "\" onclick=\"document.location='index.php?file=Admin&amp;page=modules&amp;op=active&amp;mid=" . $mid . "'\" />"
            . "<input type=\"hidden\" name=\"niveau\" value=\"-1\" /><input type=\"hidden\" name=\"level\" value=\"-1\" />";
            $read = "disabled";
        }
		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _MODULE . "&nbsp;" . $nom . "</h3>\n";

        echo "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/modules.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=modules&amp;op=update_module\">\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"1\" border=\"0\">\n"
	. "<tr><td><b>" . _LEVELACCES . " :</b></td><td><select name=\"niveau\" " . $read . "><option>" . $niveau . "</option>\n"
	. "<option>0</option>\n"
	. "<option>1</option>\n"
	. "<option>2</option>\n"
	. "<option>3</option>\n"
	. "<option>4</option>\n"
	. "<option>5</option>\n"
	. "<option>6</option>\n"
	. "<option>7</option>\n"
	. "<option>8</option>\n"
	. "<option>9</option></select></td></tr>\n";

        if ($nom == "Team" || $nom == "Members" || $nom == "Vote")
        {
            echo "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"level\" value=\"" . $level . "\" /></td></tr>";
        }
        else
        {
            echo "<tr><td><b>" . _LEVELADMIN . " :</b></td><td><select name=\"level\" " . $read . "><option>" . $level . "</option>\n"
            . "<option>1</option>\n"
            . "<option>2</option>\n"
            . "<option>3</option>\n"
            . "<option>4</option>\n"
            . "<option>5</option>\n"
            . "<option>6</option>\n"
            . "<option>7</option>\n"
            . "<option>8</option>\n"
            . "<option>9</option></select></td></tr><tr><td colspan=\"2\">&nbsp;</td></tr>\n";
        }

        echo "<tr><td colspan=\"2\"><small><b>" . _MEMBERS . " :</b> " . _LEVEL1 . "<br /><b> " . _ADMINFIRST . " :</b> " . _LEVEL2 . " <br /><b> " . _ADMINSUP . " :</b> " . _LEVEL9 . "</small></td></tr>\n"
	. "<tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"mid\" value=\"" . $mid . "\" /></td></tr>\n"
	. "<tr><td colspan=\"2\" align=\"center\"></td></tr></table>\n"
	. "<div style=\"text-align: center;\"><br /><input class=\"button\" type=\"submit\" value=\"" . _EDITMODULE . "\" />" . $button . "<a class=\"buttonLink\" href=\"index.php?file=Admin&amp;page=modules\">" . _BACK . "</a></div></form><br /></div></div>\n";
    }

    function desactive($mid)
    {
        global $nuked, $user;

		$mid = mysql_real_escape_string(stripslashes($mid));

		$sql2 = mysql_query("SELECT nom FROM " . MODULES_TABLE . " WHERE id = '" . $mid . "'");
        list($nom) = mysql_fetch_array($sql2);
        $sql = mysql_query("UPDATE " . MODULES_TABLE . " SET niveau = -1, admin = -1 WHERE id = '" . $mid . "'");
		// Action
		$texteaction = "". _ACTIONDESMOD .": ".$nom."";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _MODULEDISABLED . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Admin&page=modules", 2);
    }

    function active($mid)
    {
        global $nuked, $user;

		$mid = mysql_real_escape_string(stripslashes($mid));
		$sql2 = mysql_query("SELECT nom FROM " . MODULES_TABLE . " WHERE id = '" . $mid . "'");
        list($nom) = mysql_fetch_array($sql2);
        $sql = mysql_query("UPDATE " . MODULES_TABLE . " SET niveau = 0, admin = 2 WHERE id = '" . $mid . "'");
		// Action
		$texteaction = "". _ACTIONACTMOD .": ".$nom."";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _MODULEENABLED . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Admin&page=modules", 2);
    }

    function update_module($mid, $niveau, $level)
    {
        global $nuked, $user;

		$mid = mysql_real_escape_string(stripslashes($mid));

		$sql2 = mysql_query("SELECT nom FROM " . MODULES_TABLE . " WHERE id = '" . $mid . "'");
        list($nom) = mysql_fetch_array($sql2);
		$niveau = mysql_real_escape_string(stripslashes($niveau));
		$level = mysql_real_escape_string(stripslashes($level));
        $sql = mysql_query("UPDATE " . MODULES_TABLE . " SET niveau = '" . $niveau . "', admin = '" . $level . "' WHERE id = '" . $mid . "'");
		// Action
		$texteaction = "". _ACTIONMODIFMOD .": ".$nom."";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _MODULEMODIF . "\n"
		. "</div>\n"
		. "</div>\n";
        redirect("index.php?file=Admin&page=modules", 2);
    }

    function main()
    {
        global $nuked, $language;

       echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _MODULE . "&nbsp;" . $nom . "</h3>\n";

        echo "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/modules.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
	. "<tr>\n"
	. "<td style=\"width: 30%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _STATUS . "</b></td>\n"
	. "<td style=\"width: 15%;\" align=\"center\"><b>" . _LEVELACCES . "</b></td>\n"
	. "<td style=\"width: 15%;\" align=\"center\"><b>" . _LEVELADMIN . "</b></td>\n"
	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _EDIT . "</b></td></tr>\n";

        $mod = array();
        $sql = mysql_query("SELECT id, nom, niveau, admin FROM " . MODULES_TABLE . " ORDER BY nom");
        while (list($mid, $nom, $niveau, $admin) = mysql_fetch_array($sql))
        {
            if ($nom == "Gallery")
            {
                $name = _NAMEGALLERY;
            }
            else if ($nom == "Calendar")
            {
                $name = _NAMECALANDAR;
            }
            else if ($nom == "Defy")
            {
                $name = _NAMEDEFY;
            }
            else if ($nom == "Download")
            {
                $name = _NAMEDOWNLOAD;
            }
            else if ($nom == "Guestbook")
            {
                $name = _NAMEGUESTBOOK;
            }
            else if ($nom == "Irc")
            {
                $name = _NAMEIRC;
            }
            else if ($nom == "Links")
            {
                $name = _NAMELINKS;
            }
            else if ($nom == "Wars")
            {
                $name = _NAMEMATCHES;
            }
            else if ($nom == "News")
            {
                $name = _NAMENEWS;
            }
            else if ($nom == "Recruit")
            {
                $name = _NAMERECRUIT;
            }
            else if ($nom == "Sections")
            {
                $name = _NAMESECTIONS;
            }
            else if ($nom == "Server")
            {
                $name = _NAMESERVER;
            }
            else if ($nom == "Suggest")
            {
                $name = _NAMESUGGEST;
            }
            else if ($nom == "Survey")
            {
                $name = _NAMESURVEY;
            }
            else if ($nom == "Forum")
            {
                $name = _NAMEFORUM;
            }
            else if ($nom == "Comment")
            {
                $name = _NAMECOMMENT;
            }
            else if ($nom == "Members")
            {
                $name = _NAMEMEMBERS;
            }
            else if ($nom == "Team")
            {
                $name = _NAMETEAM;
            }
            else if ($nom == "Textbox")
            {
                $name = _NAMESHOUTBOX;
            }
            else if ($nom == "Vote")
            {
                $name = _NAMEVOTE;
            }
            else
            {
                $name = $nom;
            }

            if ($niveau > -1 && $admin > -1)
            {
                $status = _ENABLED;
            }
            else
            {
                $status = _DISABLED;
            }

            array_push($mod, $name . "|" . $mid . "|" . $niveau . "|" . $admin . "|" . $status);
        }
        natcasesort($mod);
        foreach($mod as $value)
        {
            $temp = explode("|", $value);


            echo "<tr>\n"
            . "<td style=\"width: 30%;\">&nbsp;" . $temp[0] . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $temp[4] . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $temp[2] . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $temp[3] . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=modules&amp;op=edit_module&amp;mid=" . $temp[1] . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _MODULEEDIT . "\" /></a></td></tr>\n";
        }
        echo "</table><div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . _BACK . "</a></div><br />\n";
    }

    switch ($_REQUEST['op'])
    {
        case "update_module":
            update_module($_REQUEST['mid'], $_REQUEST['niveau'], $_REQUEST['level']);
            UpdateSitmap();
        	break;

        case "desactive":
            desactive($_REQUEST['mid']);
        	UpdateSitmap();
            break;

        case "active":
            active($_REQUEST['mid']);
        	UpdateSitmap();
            break;

        case "edit_module":
            edit_module($_REQUEST['mid']);
            break;

        case "main":
            main();
            break;

        default:
            main();
            break;
    }

}
else if ($visiteur > 1)
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a class=\"buttonLink\" href=\"javascript:history.back()\">" . _BACK . "</a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}
else
{
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a class=\"buttonLink\" href=\"javascript:history.back()\">" . _BACK . "</a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
}
adminfoot();

?>
