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

if ($visiteur >= 2)
{
    function main()
    {
        global $user, $nuked;
		$date = time();
		if ($_REQUEST['texte'] != "" AND $_REQUEST['type'] != "" AND $_REQUEST['type'] != "0")
		{
			$_REQUEST['texte'] = utf8_decode($_REQUEST['texte']);
			
			$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$date."', '".mysql_real_escape_string(stripslashes($_REQUEST['type']))."', '".mysql_real_escape_string(stripslashes($_REQUEST['texte']))."')");
			echo _THANKSPARTICIPATION;
		}
		else
		{
		echo _NOTIFICATIONNOTRECEIVED;
		}
		exit();
    }
	function delete()
	{
		global $nuked, $visiteur, $user;
		
		if ($visiteur == "9" AND $_REQUEST['id'] != "")
		{
			$_REQUEST['id'] = mysql_real_escape_string(stripslashes($_REQUEST['id']));
			$sql3 = mysql_query("DELETE FROM ". $nuked['prefix'] ."_notification WHERE id = '" . $_REQUEST['id'] . "'");
			// Action
			$texteaction = "". _ACTIONDELNOT .".";
			$acdate = time();
			$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
			//Fin action
		}
	}
    switch ($_REQUEST['op'])
    {
        case "main":
        main();
        break;
		case "delete":
        delete();
        break;
        default:
        main();
        break;
    }

}
else
{
    echo _NOTIFICATIONNOTRECEIVED;
}
?>