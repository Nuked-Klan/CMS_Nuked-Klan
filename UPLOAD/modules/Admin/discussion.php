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
if (!$user)
{
    $visiteur = 0;
}
else
{
    $visiteur = $user[1];
}

if ($visiteur  >= 2)
{
    function main()
    {
        global $user, $nuked;
		$date = time();
		if ($_REQUEST['texte'] != "")
		{
			$_REQUEST['texte'] = utf8_decode($_REQUEST['texte']);
			$_REQUEST['texte'] = htmlentities($_REQUEST['texte']);
			$texte = mysql_real_escape_string(stripslashes($_REQUEST['texte']));
			$upd = mysql_query("INSERT INTO ".$nuked['prefix']."_discussion  (`date` , `pseudo` , `texte`)  VALUES ('".$date."', '".$user[0]."', '".$texte."')");
		}
    }
    switch ($_REQUEST['op'])
    {
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
    admintop();
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    adminfoot();
}
else
{
    admintop();
    echo "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
    adminfoot();
}
?>