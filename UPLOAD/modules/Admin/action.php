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
translate("modules/Admin/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");

if (!$user)
{
    $visiteur = 0;
}
else
{
    $visiteur = $user[1];
}
if ($visiteur >= 2)
{
    function main()
    {
        global $user, $nuked, $language;
	
		
		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _ADMINACTION . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Action.html\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><br />\n"
	. "<div class=\"notification information png_bg\">\n"
	. "<div>"._INFOACTION."</div></div>\n"
		. "<br /><table><tr><td><b>". _DATE ."</b>\n"
		."</td><td><b>". _INFORMATION ."</b>\n"
		."</td></tr>\n";
		$sql = mysql_query("SELECT date, pseudo, action  FROM " . $nuked['prefix'] . "_action ORDER BY date DESC");
		while (list($date, $users, $texte) = mysql_fetch_array($sql))
		{
			if($users != "")
			{
			$users = mysql_real_escape_string($users);
			
			$sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $users . "'");
			list($pseudo) = mysql_fetch_array($sql2);
			}
			else
			{
				$pseudo = "N/A";
			}
			$date = strftime("%x", $date)." "._A." ".strftime("%H", $date).":".strftime("%M", $date);
			$texte = "".$pseudo." ".$texte ."";
			
			echo "<tr><td>".$date."</td>\n"
			. "<td>".$texte."</td></tr>\n";
			
		}
		echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
		$theday = time();
		$compteur = 0;
		$delete = mysql_query("SELECT id, date  FROM " . $nuked['prefix'] . "_action ORDER BY date DESC");
		while (list($id, $date) = mysql_fetch_array($delete))
		{
			$limit_time = $date + 1209600;
            
            if ($limit_time < $theday)
            {
                $del = mysql_query("DELETE FROM " . $nuked['prefix'] . "_action WHERE id = '" . $id . "'");
				$compteur++;
            }
		}
		if ($compteur > 0)
		{
			if($compteur ==1)
			{
				$text = "".$compteur." "._1NBRNOTACTION."";
			}
			else
			{
				$text = "".$compteur." "._NBRNOTACTION."";
			}
			$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$theday."', '3', '".$text."')");
		}
	}
    switch ($_REQUEST['op'])
    {
        case "main":
		admintop();
        main();
		adminfoot();
        break;
        default:
		admintop();
        main();
		adminfoot();
        break;
    }

}
else if ($visiteur > 1)
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
    adminfoot();
}
else
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
    adminfoot();
}
?>