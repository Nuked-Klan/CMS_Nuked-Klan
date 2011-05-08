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

global $user, $nuked, $language, $theme, $bgcolor1, $bgcolor2, $bgcolor3;
translate("modules/Admin/lang/" . $language . ".lang.php");
translate("modules/User/lang/" . $language . ".lang.php");
include_once(dirname(__FILE__) . '/../../Includes/hash.php');

$url = "index.php";
$message = "";

if ($user && isset($_POST['admin_password']) && $_POST['admin_password'] != "")
{
    $cookie_admin = $nuked['cookiename'] . "_admin_session";

    $sql = mysql_query("SELECT pseudo, pass FROM " . USER_TABLE . " WHERE id = '" . $user[0] . "'");
    list($pseudo, $hash) = mysql_fetch_array($sql);
    $check = mysql_num_rows($sql);

    if ($check == 1 && Check_Hash($_POST['admin_password'], $hash))
    {
		list($pseudo) = mysql_fetch_array($sql);

		if ($_POST['formulaire'] == 0 && $_SERVER['HTTP_REFERER'] != "")
		{
		    list($url_ref, $redirect) = explode('?', $_SERVER['HTTP_REFERER']);
		    if ($redirect == "file=Admin&page=login") $redirect = "file=Admin";
	        $url = "index.php?" . $redirect;
		}
		else
		{
	            $url = "index.php?file=Admin";
		}

		$_SESSION['admin'] = true;
		// Action
		$texteaction = "". _ACTIONCONNECT ."";
		$acdate = time();
		$action = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date` , `pseudo` , `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action

		if (!isset($_COOKIE[$cookie_session]))
		{
			$message = _ERRORCOOKIE;
		}
		else $message = _ADMINPROGRESS;

    }
    else
    {
		$message = _BADLOGADMIN;
	    $url = "index.php?file=Admin";
    }

	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
		. "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
		. "<head><title>" . $nuked['name'] . " :: " . $nuked['slogan'] . " ::</title>\n"
		. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
		. "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
		. "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
		. "<body style=\"background: " . $bgcolor2 . ";\"><div><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /></div>\n"
		. "<table width=\"600\" style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor3 . ";\" cellspacing=\"1\" cellpadding=\"20\">\n"
		. "<tr><td style=\"background: " . $bgcolor1 . ";\" align=\"center\"><big><b>" . $message . "</b></big></td></tr></table></body></html>";

	    redirect($url, 2);
}
else
{
    if (!$user) redirect("index.php?file=User&op=login_screen", 0);
	else if ($_REQUEST['nuked_nude'])
    {
		redirect("index.php?file=Admin", 0);
    }
    else
    {
		if ($_POST) $check = 1;
		else $check = 0;

		opentable();

		echo "<br /><div style=\"text-align: center;\"><big><b>" . _ADMINSESSION . "</b></big></div><br />\n"
		. "<form action=\"index.php?file=Admin&amp;nuked_nude=login\" method=\"post\">\n"
		. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
		. "<tr><td><b>" . _NICK . " :</b></td><td><input type=\"text\" name=\"admin_pseudo\" value=\"" . $user[2] . "\" size=\"15\" maxlength=\"180\" /></td></tr>\n"
		. "<tr><td><b>" . _PASSWORD . " :</b></td><td><input type=\"password\" name=\"admin_password\" size=\"15\" maxlength=\"15\" /></td></tr>\n"
		. "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"" . _TOLOG . "\" /></td></tr><tr><td colspan=\"2\">&nbsp;<input type=\"hidden\" name=\"formulaire\" value=\"" . $check  . "\" /></td></tr>\n"
		. "<tr><td colspan=\"2\"><b><a href=\"index.php?file=User&amp;op=oubli_pass\">" . _LOSTPASS . "</a></b></td></tr></table></form><br />\n";

		closetable();
    }
}

?>