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
translate("modules/Contact/lang/" . $language . ".lang.php");

opentable();

if (!$user)
{
    $visiteur = 0;
} 
else
{
    $visiteur = $user[1];
} 

$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1)
{
    function index()
    {
	echo "<script type=\"text/javascript\">\n"
	."<!--\n"
	."\n"
	. "function verifchamps()\n"
	. "{\n"
	. "if (document.getElementById('ns_pseudo').value.length == 0)\n"
	. "{\n"
	. "alert('" . _NONICK . "');\n"
	. "return false;\n"
	. "}\n"
	. "if (document.getElementById('ns_email').value.indexOf('@') == -1)\n"
	. "{\n"
	. "alert('" . _BADMAIL . "');\n"
	. "return false;\n"
	. "}\n"
	. "if (document.getElementById('ns_sujet').value.length == 0)\n"
	. "{\n"
	. "alert('" . _NOSUBJECT . "');\n"
	. "return false;\n"
	. "}\n"
	. "if (document.getElementById('ns_corps').value.length == 0)\n"
	. "{\n"
	. "alert('" . _NOTEXTMAIL . "');\n"
	. "return false;\n"
	. "}\n"
	. "return true;\n"
	. "}\n"
	. "\n"
	. "// -->\n"
	."</script>\n";

	echo "<br /><form method=\"post\" action=\"index.php?file=Contact&amp;op=sendmail\" onsubmit=\"return verifchamps()\">\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"1\" cellpadding=\"3\" border=\"0\">\n"
	. "<tr><td align=\"center\"><big><b>" . _CONTACT . "</b></big><br /><br />" . _CONTACTFORM . "</td></tr>\n"
	. "<tr><td>&nbsp;</td></tr><tr><td><b>" . _YNICK . " : </b>&nbsp;<input id=\"ns_pseudo\" type=\"text\" name=\"nom\" size=\"26\" value=\"" . $user[2]. "\" /></td></tr>\n"
	. "<tr><td><b>" . _YMAIL . " : </b>&nbsp;<input id=\"ns_email\" type=\"text\" name=\"mail\" value=\"\" size=\"30\" /></td></tr>\n"
	. "<tr><td><b>" . _YSUBJECT . " : </b>&nbsp;<input id=\"ns_sujet\" type=\"text\" name=\"sujet\" value=\"\" size=\"36\" /></td></tr>\n"
	. "<tr><td>&nbsp;</td></tr><tr><td><b>" . _YCOMMENT . " : </b><br /><textarea class=\"editorsimpla\" id=\"ns_corps\" name=\"corps\" cols=\"60\" rows=\"12\"></textarea></td></tr>\n"
	. "<tr><td align=\"center\"><br /><input type=\"submit\" class=\"bouton\" value=\"" . _SEND . "\" /></td></tr></table></form><br />\n";
    }



    function sendmail($nom, $mail, $sujet, $corps)
    {
	global $nuked, $user_ip, $nuked;

    	$time = time();
    	$date = strftime("%x %H:%M", $time);
    	$contact_flood = $nuked['contact_flood'] * 60;

    	$sql = mysql_query("SELECT date FROM " . CONTACT_TABLE . " WHERE ip = '" . $user_ip . "' ORDER BY date DESC LIMIT 0, 1");
    	$count = mysql_num_rows($sql);
    	list($flood_date) = mysql_fetch_array($sql);
    	$anti_flood = $flood_date + $contact_flood;

    	if ($count > 0 && $time < $anti_flood)
    	{
	    echo "<br /><br /><div style=\"text-align: center;\">" . _FLOODCMAIL . "</big></div><br /><br />";
	    redirect("index.php", 3);
    	}
    	else
    	{
	    $nom = trim($nom);
	    $mail = trim($mail);
	    $sujet = trim($sujet);

	    $subjet = $sujet . ", " . $date;
	    $corp = $corps . "\r\n\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
	    $from = "From: " . $nom . " <" . $mail . ">\r\nReply-To: " . $mail;

	    if ($nuked['contact_mail'] != "") $email = $nuked['contact_mail'];
	    else $email = $nuked['mail'];	
		$corp = secu_html(html_entity_decode($corp));
		
	    mail($email, $subjet, $corp, $from);

	    $name = htmlentities($nom, ENT_QUOTES);
	    $email = htmlentities($mail, ENT_QUOTES);
	    $subject = htmlentities($sujet, ENT_QUOTES);
	    $text = secu_html(html_entity_decode($corps, ENT_QUOTES));

	    $add = mysql_query("INSERT INTO " . CONTACT_TABLE . " ( `id` , `titre` , `message` , `email` , `nom` , `ip` , `date` ) VALUES ( '' , '" . $subject . "' , '" . $text . "' , '" . $email . "' , '" . $name . "' , '" . $user_ip . "' , '" . $time . "' )");
		$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('".$time."', '1', '"._NOTCON.": [<a href=\"index.php?file=Contact&page=admin\">lien</a>].')");
	    echo "<br /><br /><div style=\"text-align: center;\">" . _SENDCMAIL . "</div><br /><br />";
	    redirect("index.php", 3);
    	}
    }

    switch($_REQUEST['op']){

	case"sendmail":
	sendmail($_REQUEST['nom'], $_REQUEST['mail'], $_REQUEST['sujet'], $_REQUEST['corps']);
	break;

	case"index":
	index();
	break;

	default:
	index();
	break;
    }

} 
else if ($level_access == -1)
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
} 
else if ($level_access == 1 && $visiteur == 0)
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><b><a href=\"index.php?file=User&amp;op=login_screen\">" . _LOGINUSER . "</a> | <a href=\"index.php?file=User&amp;op=reg_screen\">" . _REGISTERUSER . "</a></b><br /><br /></div>";
} 
else
{
    echo "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a><br /><br /></div>";
} 

closetable();

?>