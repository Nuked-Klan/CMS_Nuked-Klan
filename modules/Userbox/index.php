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

global $language, $user;
translate("modules/Userbox/lang/" . $language . ".lang.php");

if ($user)
{
    function select_user()
    {
        global $nuked;

        $sql = mysql_query("SELECT id, pseudo, niveau FROM " . USER_TABLE . " WHERE niveau > 0 ORDER BY niveau DESC, pseudo");
        while (list($id_user, $pseudo, $niveau) = mysql_fetch_array($sql))
        {
            
  	    if ($niveau == 9)
            {
                $nivo = "****";
            } 
            else if ($niveau > 2)
            {
                $nivo = "**";
            } 
            else if ($niveau == 2)
            {
                $nivo = "*";
            } 
            else
            {
                $nivo = "";
            }

            echo "<option value=\"" . $id_user . "\">" . $pseudo . $nivo . "</option>\n";
        } 
    } 

    function post_message()
    {
        global $user;
	
	if ($_REQUEST['for'] != "" && preg_match("`^[a-zA-Z0-9]+$`", $_REQUEST['for']))
	{
            $sql = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $_REQUEST['for'] . "'");
            list($pseudo) = mysql_fetch_array($sql);
	}

        if ($_REQUEST['titre'] != "")
        {
            $_REQUEST['titre'] = stripslashes($_REQUEST['titre']);
            $title = "RE:" . htmlentities($_REQUEST['titre']);
        }

        if ($_REQUEST['message'] != "")
        {
			$_REQUEST['message'] = secu_html(html_entity_decode($_REQUEST['message']));
            $_REQUEST['message'] = stripslashes($_REQUEST['message']);
            $reply = "<br /><table style=\"background: " . $bgcolor3 . ";\" cellpadding=\"3\" cellspacing=\"1\" width=\"100%\" border=\"0\"><tr><td style=\"background: #FFFFFF;color: #000000\"><b>" . $pseudo . " :</b><br />" .  $_REQUEST['message'] . "</td></tr></table></br>";
        } 

        opentable();

        echo "<br /><form method=\"post\" action=\"index.php?file=Userbox&amp;op=send_message\" onsubmit=\"backslash('mess_pv');\">\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\">\n"
	. "<tr><td align=\"center\"><big><b>" . _POSTMESS . "</b></big><br /><br /></td></tr>\n"
	. "<tr><td><b>" . _AUTHOR . " :</b> " . $user[2] . "</td></tr>\n"
	. "<tr><td><b>" . _USERFOR . " :</b> ";

	if ($_REQUEST['for'] != "" && $pseudo != "") 
	{
            echo "<i>" . $pseudo . "</i><input type=\"hidden\" name=\"user_for\" value=\"" . $_REQUEST['for'] . "\" />";
	}
	else
	{
            echo "<select name=\"user_for\" >\n";
            select_user();
            echo "</select>\n";
	}

	echo "</td></tr><tr><td><b>" . _SUBJECT . " :</b> <input type=\"text\" name=\"titre\" size=\"30\" value=\"" . $title . "\" /></td></tr>\n"
	. "<tr><td><b>" . _USERMESS . " :</b><br /><textarea class=\"editoradvanced\" id=\"mess_pv\" name=\"message\" cols=\"65\" rows=\"10\">" . $reply . "</textarea></td></tr>\n"
	. "<tr><td align=\"center\"><br /><input type=\"submit\" name=\"send\" value=\"" . _SEND . "\" />&nbsp;<input type=\"button\" value=\"" . _CANCEL . "\" onclick=\"javascript:history.back()\" /></td></tr></table></form><br />\n";
	
        closetable();
    } 

    function send_message($titre, $user_for, $message)
    {
        global $user, $nuked;

        if ($titre == "" || $user_for == "" || $message == "")
        {
            opentable();
            echo "<br /><br /><div style=\"text-align: center;\">" . _EMPTYFIELD . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
            closetable();
        } 
        else
        {
            if ($user_for != "" && preg_match("`[a-zA-Z0-9]+$`", $user_for))
            {
				$sql2 = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $user_for . "'");
				$nb = mysql_num_rows($sql2);
            }
            else
            {
				$nb = 0;
            }	

            if ($nb == 0)
            {
                opentable();
                echo "<br /><br /><div style=\"text-align: center;\">" . _UNKNOWMEMBER . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />";
                closetable();
            } 
            else
            {
				$flood = mysql_query("SELECT date FROM " . USERBOX_TABLE . " WHERE user_from = '" . $user[0] . "' ORDER BY date DESC LIMIT 0, 1");
				list($flood_date) = mysql_fetch_row($flood);
				$anti_flood = $flood_date + $nuked['post_flood'];

				$date = time();

				if ($date < $anti_flood)
				{
					echo "<br /><br /><div style=\"text-align: center;\">" . _NOFLOOD . "</div><br /><br />";
					$url = "index.php?file=Userbox";
					redirect($url, 2);
					closetable();
					footer();
					exit();
				} 
				
				$message = secu_html(html_entity_decode($message));
                $titre = mysql_real_escape_string(stripslashes($titre));
                $message = mysql_real_escape_string(stripslashes($message));
                $user_for = mysql_real_escape_string(stripslashes($user_for));

                $titre = htmlentities($titre);

                $sql = mysql_query("INSERT INTO " . USERBOX_TABLE . " ( `mid` , `user_from` , `user_for` , `titre` , `message` , `date` , `status` ) VALUES ( '' , '" . $user[0] . "' , '" . $user_for . "' , '" . $titre . "' , '" . $message . "' , '" . $date . "' , '0' )");
				opentable();
                echo "<br /><br /><div style=\"text-align: center;\">" . _MESSSEND . "</div><br /><br />";
                closetable();
                redirect("index.php?file=Userbox", 2);
            } 
        } 
    } 

    function show_message($mid)
    {
        global $user, $nuked, $bgcolor2, $bgcolor3;

        echo "<script type=\"text/javascript\">\n"
		."<!--\n"
		."\n"
		. "function del_mess(pseudo, id)\n"
		. "{\n"
		. "if (confirm('" . _DELETEMESS . " '+pseudo+' ! " . _CONFIRM . "'))\n"
		. "{document.location.href = 'index.php?file=Userbox&op=del_message&mid='+id;}\n"
		. "}\n"
		."\n"
		. "// -->\n"
		. "</script>\n";

        $sql = mysql_query("UPDATE " . USERBOX_TABLE . " SET status = 1 WHERE mid = '" . $mid . "' AND user_for = '" . $user[0] . "'");

        opentable();

        echo "<br /><div style=\"text-align: center;\"><big><b>" . _PRIVATEMESS . "</b></big></div><br /><br />\n";

        $sql2 = mysql_query("SELECT titre, message, user_from, date FROM " . USERBOX_TABLE . " WHERE mid = '" . $mid . "' AND user_for = '" . $user[0] . "'");
        list($titre, $message, $user_from, $date) = mysql_fetch_array($sql2);

        $date = strftime("%x %H:%M", $date);
        
        if(strlen($titre) >= 50){
           $titre = substr($titre, 0, 47)."...";
        }

		$sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $user_from . "'");
        list($pseudo) = mysql_fetch_array($sql_member);

        echo "<table style=\"margin-left: auto;margin-right: auto;text-align: left;background: " . $bgcolor3 . ";\" width=\"90%\" cellspacing=\"1\" cellpadding=\"4\">\n"
		. "<tr style=\"background: " . $bgcolor3 . ";\"><td>" . _OF . "  <a href=\"index.php?file=Members&amp;op=detail&amp;autor=" . urlencode($pseudo) . "\"><b>" . $pseudo . "</b></a> " . _THE . "&nbsp;" . $date. "</td></tr>\n"
		. "<tr style=\"background: " . $bgcolor2 . ";\"><td><b>" . _SUBJECT . " :</b> " . $titre . "</td></tr>\n"
		. "<tr style=\"background: " . $bgcolor2 . ";\"><td>" . $message . "</td></tr></table>\n"
		. "<br /><form method=\"post\" action=\"index.php?file=Userbox&amp;op=post_message&amp;for=" . $user_from . "\">\n"
		. "<div style=\"text-align: center;\">\n"
		. "<input type=\"hidden\" name=\"message\" value=\"" . htmlentities($message) . "\" />\n"
		. "<input type=\"hidden\" name=\"titre\" value=\"" . htmlentities($titre) . "\" />\n"
		. "<input type=\"submit\" value=\"" . _REPLY . "\" />&nbsp;"
		. " <input type=\"button\" value=\"" . _DEL . "\" onclick=\"javascript:del_mess('" . mysql_real_escape_string(stripslashes($pseudo)) . "', '" . $mid . "');\" />\n"
		. "<br /><br />[ <a href=\"index.php?file=Userbox\"><b>" . _BACK . "</b></a> ]</div></form><br />\n";

        closetable();
    } 

    function del_message($mid)
    {
        global $user, $nuked;

        opentable();
		$sql = mysql_query("SELECT mid FROM " . USERBOX_TABLE . " WHERE  mid = '" . $mid . "' AND user_for = '" . $user[0] . "'");
		$nbr = mysql_num_rows($sql);
		if($nbr > 0)
		{
			$sql = mysql_query("DELETE FROM " . USERBOX_TABLE . " WHERE mid = '" . $mid . "' AND user_for = '" . $user[0] . "'");
			echo "<br /><br /><div style=\"text-align: center;\">" . _MESSDEL . "</div><br /><br />";
		}
		else
		{
			echo "<br /><br /><div style=\"text-align: center;\">Failed...</div><br /><br />";
		}
        redirect("index.php?file=Userbox", 2);

        closetable();
    } 

    function del_message_form($mid, $del_oui)
    {
        global $user, $nuked;

        opentable();

        if ($del_oui)
        {
            $sql = mysql_query("SELECT mid FROM " . USERBOX_TABLE . " WHERE user_for = '" . $user[0] . "' ORDER BY mid");
            $nb_mess = mysql_num_rows($sql);
            $get_mid = 0;

            while ($nb_mess > $get_mid && $nb_mess <> "")
            {
                $titi = $mid[$get_mid];
                $get_mid++;

                if ($titi)
                {
                    $sql = mysql_query("DELETE FROM " . USERBOX_TABLE . " WHERE mid = '" . $titi . "'");
                } 
            } 

            echo "<br /><br /><div style=\"text-align: center;\">" . _MESSAGESDEL . "</div><br /><br />";
            redirect("index.php?file=Userbox", 2);
        } 
        else
        {
            if (!$mid)
            {
                echo "<br /><br /><div style=\"text-align: center;\">" . _NOSELECTMESS . "</div><br /><br />";
                redirect("index.php?file=Userbox", 2);
                closetable();
                footer();
                exit();
            } 

            $sql = mysql_query("SELECT mid FROM " . USERBOX_TABLE . " WHERE user_for = '" . $user[0] . "' ORDER BY mid");
            $nb_mess = mysql_num_rows($sql);

            echo "<form method=\"post\" action=\"index.php?file=Userbox&amp;op=del_message_form&amp;del_oui=ok\">\n"
            . "<div style=\"text-align: center;\"><br /><br /><b>" . _DELETEMESSAGES . " :</b><br /><br />\n";

            $get_mid = 0;
            while ($nb_mess > $get_mid && $nb_mess <> "")
            {
                $titi = $mid[$get_mid];
                $get_mid++;

                if ($titi)
                {
			$sql_mess = mysql_query("SELECT user_from, date FROM " . USERBOX_TABLE . " WHERE user_for = '" . $user[0] . "' AND mid = '" . $titi . "'");
			list($user_from, $date) = mysql_fetch_array($sql_mess);
			$date = strftime("%x %H:%M", $date);

			$sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $user_from . "'");
        		list($pseudo) = mysql_fetch_array($sql_member);

                    echo "<b><big>·</big></b>&nbsp;" . _OF ."&nbsp;" . $pseudo. " ( " . $date . " )<br />\n"
                    . "<input type=\"hidden\" name=\"mid[]\" value=\"" . $titi . "\" />\n";
                } 
            } 

            echo "<br /><br /><input type=\"submit\" value=\"" . _DELCONFIRM . "\" />"
            . "&nbsp;<input type=\"button\" value=\"" . _CANCEL . "\" onclick=\"document.location='index.php?file=Userbox'\" /></div></form><br />\n";
        } 
        closetable();
    } 

    function index()
    {
        global $user, $nuked, $bgcolor1, $bgcolor2, $bgcolor3;

        opentable();

        echo "<script type=\"text/javascript\">\n"
		."<!--\n"
		."\n"
		."function setCheckboxes(checkbox, nbcheck, do_check)\n"
		."{\n"
		."for (var i = 0; i < nbcheck; i++)\n"
		."{\n"
		."cbox = checkbox + i;\n"
		."document.getElementById(cbox).checked = do_check;\n"
		."}\n"
		."return true;\n"
		."}\n"
		."\n"
		. "// -->\n"
		. "</script>\n";

        echo "<form method=\"post\" action=\"index.php?file=Userbox&amp;op=del_message_form\">\n"
		. "<div style=\"text-align: center;\"><br /><big><b>" . _PRIVATEMESS . "</b></big><br /></div>\n"
		. "<table style=\"background: " . $bgcolor2 . ";border: 1px solid " . $bgcolor3 . ";\" width=\"100%\" cellpadding=\"2\" cellspacing=\"1\">\n"
		. "<tr style=\"background: " . $bgcolor3 . ";\">\n"
		. "<td style=\"width: 3%;\" align=\"center\">&nbsp;<b>" . _DELBOX . "</b></td>\n"
		. "<td align=\"center\"><b>" . _FROM . "</b></td>\n"
		. "<td align=\"center\"><b>" . _SUBJECT . "</b></td>\n"
		. "<td align=\"center\"><b>" . _DATE . "</b></td>\n"
		. "<td align=\"center\"><b>" . _STATUS . "</b></td>\n"
		. "<td align=\"center\"><b>" . _READMESS . "</b></td></tr>\n";

        $sql = mysql_query("SELECT mid, titre, user_from, date, status FROM " . USERBOX_TABLE . " WHERE user_for = '" . $user[0] . "' ORDER BY date DESC");
        $nb_mess = mysql_num_rows($sql);
		$i = 0;
        while (list($mid, $titre, $user_from, $date, $status) = mysql_fetch_array($sql))
        {
            $date = strftime("%x %H:%M", $date);

            $sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $user_from . "'");
            list($pseudo) = mysql_fetch_array($sql_member);

            if ($status == 0)
            {
                $etat = _NOTREAD;
            } 
            else
            {
                $etat = _READ;
            } 

            if ($j == 0)
            {
                $bg = $bgcolor2;
                $j++;
            } 
            else
            {
                $bg = $bgcolor1;
                $j = 0;
            } 
            
            if(strlen($titre) >= 50){
              $titre = substr($titre, 0, 47)."...";
            }

		 	echo "<tr style=\"background: " . $bg . ";\">\n"
			. "<td><input id=\"box" . $i . "\" type=\"checkbox\" class=\"checkbox\" name=\"mid[]\" value=\"" . $mid . "\" /></td>\n"
			. "<td align=\"center\">" . $pseudo . "</td>\n"
			. "<td align=\"center\">" . $titre . "</td>\n"
			. "<td align=\"center\">" . $date . "</td>\n"
			. "<td align=\"center\">" . $etat . "</td>\n"
			. "<td align=\"center\"><a href=\"index.php?file=Userbox&amp;op=show_message&amp;mid=" . $mid . "\"><img style=\"border: 0;\" src=\"modules/Userbox/images/read.gif\" alt=\"\" /></a></td></tr>\n";

			$i++;
        } 

        if ($nb_mess == 0)
        {
            echo "<tr style=\"background: " . $bgcolor2 . ";\"><td colspan=\"6\" align=\"center\">" . _NOMESSPV . "</td></tr>\n";
        } 

        echo"</table>";

        if ($nb_mess > 1)
        {
            echo "<div style=\"text-align: left;\">&nbsp;<img src=\"images/flech_coch.gif\" alt=\"\" />\n"
            . "<a href=\"#\" onclick=\"setCheckboxes('box', '" . $nb_mess . "', true);\">" . _CHECKALL . "</a> / " 
            . "<a href=\"#\" onclick=\"setCheckboxes('box', '" . $nb_mess . "', false);\">" . _UNCHECKALL . "</a><br /></div>\n";
        } 

		echo "<div style=\"text-align: center;\"><br />\n";

        if ($nb_mess > 0)
        {
            $button = _SENDNEWMESS;	            	
            echo "<input type=\"submit\" value=\"" . _DEL . "\" />&nbsp;";
        } 
        else
		{
            $button = _POSTMESS;	
		}
            echo "<input type=\"button\" value=\"" . $button . "\" onclick=\"document.location='index.php?file=Userbox&amp;op=post_message'\" />\n"
				. "<br /><br />[ <a href=\"index.php?file=User\"><b>" . _BACK . "</b></a> ]</div></form><br />\n";
        

        closetable();
    } 

 if (isset($_REQUEST['op'])){
		switch ($_REQUEST['op'])
	    {
	        case "post_message":
	            post_message();
	            break;

	        case "send_message":
	            send_message($_REQUEST['titre'], $_REQUEST['user_for'], $_REQUEST['message']);
	            break;

	        case "show_message":
	            show_message($_REQUEST['mid']);
	            break;

	        case "del_message":
	            del_message($_REQUEST['mid']);
	            break;

	        case "del_message_form":
	            del_message_form($_REQUEST['mid'], $_REQUEST['del_oui']);
	            break;

	        default:
	            index();
	            break;
	    } 
	}
} 
else
{
    opentable();
    echo "<br /><br /><div style=\"text-align: center;\">" . _USERENTRANCE . "<br /><br /><a href=\"index.php?file=User&amp;op=login_screen\"><b>" . _LOGINUSER . "</b></a> | " 
    . "<a href=\"index.php?file=User&amp;op=reg_screen\"><b>" . _REGISTERUSER . "</b></a></div><br /><br />";
    closetable();
} 

?>
