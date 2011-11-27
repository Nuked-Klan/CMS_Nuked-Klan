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

global $user, $language;
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

if ($visiteur == 9)
{
    function index()
    {
        global $nuked, $language;
		
		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _MENUADMIN . "</h3>\n"
		. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/menu.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
	. "<tr>\n"
	. "<td style=\"width: 35%;\" align=\"center\"><b>" . _NAME . "</b></td>\n"
	. "<td style=\"width: 20%;\" align=\"center\"><b>" . _BLOCK . "</b></td>\n"
	. "<td style=\"width: 15%;\" align=\"center\"><b>" . _POSITION . "</b></td>\n"
	. "<td style=\"width: 15%;\" align=\"center\"><b>" . _LEVEL . "</b></td>\n"
	. "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td></tr>\n";

        $sql = mysql_query("SELECT  bid, active, position, titre, nivo FROM " . BLOCK_TABLE . " WHERE type = 'menu'");
        while (list($bid, $activ, $position, $titre, $nivo) = mysql_fetch_array($sql))
        {
            $titre = htmlentities($titre);

            if ($activ == 1) $active = _LEFT;
            else if ($activ == 2) $active = _RIGHT;
            else $active = _OFF;


            echo "<tr>\n"
            . "<td style=\"width: 35%;\">" . $titre . "</td>\n"
            . "<td style=\"width: 20%;\" align=\"center\">" . $active . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $position . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\">" . $nivo . "</td>\n"
            . "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=menu&amp;op=edit_menu&amp;bid=" . $bid . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a></td></tr>\n";

        }
        echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }

    function edit_menu($bid)
    {
        global $nuked, $user, $language;

        $sql = mysql_query("SELECT titre, content FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
        list($titre, $content) = mysql_fetch_array($sql);
        $titre = htmlentities($titre);

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

        $link = explode('NEWLINE', $content);
        $count = count($link);
        $r = 0;

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _MENUADMIN . " : " . $titre . "</h3>\n"
		. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/menu.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=menu&amp;op=send_line\">\n"
	. "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
	. "<tr>\n"
	. "<td style=\"width: 5%;\" align=\"center\"><b>&lt; # &gt;</b></td>\n"
	. "<td style=\"width: 5%;\" align=\"center\"><b>" . _DELBOX . "</b></td>\n"
	. "<td style=\"width: 25%;\" align=\"center\"><b>" . _TITLE . "</b></td>\n"
	. "<td style=\"width: 25%;\" align=\"center\"><b>" . _URL . "</b></td>\n"
	. "<td style=\"width: 10%;\" align=\"center\"><b>" . _COMMENT . "</b></td>\n"
	. "<td style=\"width: 10%;\" align=\"center\"><b>" . _NEWPAGE . "</b></td>\n"
	. "<td style=\"width: 10%;\" align=\"center\"><b>" . _LEVEL . "</b></td>\n"
	. "<td style=\"width: 10%;\" align=\"center\"><b>" . _EDIT . "</b></td></tr>\n";

        if (!empty($content))
        {
            foreach ($link as $link)
            {
                list($url, $title, $comment, $niveau, $blank) = explode('|', $link);
                $title = strip_tags($title);
                $title = htmlentities($title);

                if ($comment !="" && strlen($comment) > 15)
                {
                    $comment = htmlentities(substr($comment, 0, 15)) . "...";
                }
		else if ($comment != "")
		{
                    $comment = htmlentities($comment);
		}
		else
		{
                    $comment = _NOCOMLINK;
		}

                if ($blank == 1)
                {
                    $checked = _YES;
                }
                else
                {
                    $checked = _NO;
                }

                if ($url != "" && substr($url, 0, 1) == "[")
                {
                    $url = str_replace("[", "", $url);
                    $url = str_replace("]", "", $url);
                    $type = _MODULE . " : <i>" . $url ."</i>";
                }
                else if ($url != "")
		{
                    $type = _LINK;
		}
                else
		{
                    $type = _TITLE;
		}


                echo "<tr><td style=\"width: 5%;\" align=\"center\">";

                if ($r < ($count - 1))
                {
                    echo "<a href=\"index.php?file=Admin&amp;page=menu&amp;op=send_line&amp;bid=" . $bid . "&amp;lid=" . $r . "&amp;line=" . ($r + 2) . "\" title=\"" . _DOWN . "\">&lt;</a>";
                }
		else
		{
                    echo "&nbsp;&nbsp;";
		}

                echo "&nbsp;" . $r . "&nbsp;";

                if ($r > 0)
                {
                    echo "<a href=\"index.php?file=Admin&amp;page=menu&amp;op=send_line&amp;bid=" . $bid . "&amp;lid=" . $r . "&amp;line=" . ($r - 1) . "\"  title=\"" . _UP . "\">&gt;</a>";
                }
		else
		{
                    echo "&nbsp;&nbsp;";
		}

                echo "</td><td style=\"width: 5%;\" align=\"center\"><input id=\"box" . $r . "\" class=\"checkbox\" type=\"checkbox\" name=\"cid[" . $r . "]\" value=\"1\" /></td>\n"
                . "<td style=\"width: 25%;\">&nbsp;" . $title . "</td>\n"
                . "<td style=\"width: 20%;\">&nbsp;" . $type . "</td>\n"
                . "<td style=\"width: 15%;\" align=\"center\">" . $comment . "</td>\n"
                . "<td style=\"width: 10%;\" align=\"center\">" . $checked . "</td>\n"
                . "<td style=\"width: 10%;\" align=\"center\">" . $niveau . "</td>\n"
                . "<td style=\"width: 10%;\" align=\"center\"><a href=\"index.php?file=Admin&amp;page=menu&amp;op=edit_line&amp;bid=" . $bid . "&amp;lid=" . $r . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" title=\"" . _EDIT . "\" /></a></td></tr>\n";

                $r++;
            }

            if ($r > 0)
            {

                echo "</table><table width=\"100%\"><tr><td style=\"width: 7%;\">&nbsp;</td><td><img src=\"images/flech_coch.gif\" alt=\"\" />\n"
                . "<a href=\"#\" onclick=\"setCheckboxes('box', '" . $r . "', true);\">" . _CHECKALL . "</a> / "
                . "<a href=\"#\" onclick=\"setCheckboxes('box', '" . $r . "', false);\">" . _UNCHECKALL . "</a></td></tr>\n";
            }
        }

        echo "</table><div style=\"text-align: center;\"><br /><input type=\"hidden\" name=\"bid\" value=\"" . $bid . "\" />\n"
	. "<input type=\"button\" value=\"" . _DEL . "\" onclick=\"if (confirm('" . _SURDELLINE . "')) submit();\" />\n"
        . "&nbsp;<input type=\"button\" value=\"" . _ADD . "\" onclick=\"document.location='index.php?file=Admin&amp;page=menu&amp;op=edit_line&amp;bid=" . $bid . "'\" /></div>\n"
        . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin&amp;page=menu\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>";
    }

    function edit_line($bid, $lid)
    {
        global $nuked, $user, $language;

        $sql = mysql_query("SELECT titre, content FROM " . BLOCK_TABLE . " WHERE bid = '" . $bid . "'");
        list($titre, $content) = mysql_fetch_array($sql);
        $titre = strip_tags($titre);
        $titre = htmlentities($titre);

        if ($content) $link = explode('NEWLINE', $content);
        list($module, $title, $comment, $niveau, $blank) = explode('|', $link[$lid]);

	if ($lid != "") $selected0 = "";
	else $selected0 = "selected=\"selected\"";

        if ($blank == 1) $checked = "checked=\"checked\"";
	else  $checked = "";

        if (preg_match("`<b>`i", $title)) $chk1 = "checked=\"checked\""; else $chk1 = "";
        if (preg_match("`<i>`i", $title)) $chk2 = "checked=\"checked\""; else $chk2 = "";
        if (preg_match("`underline`i", $title)) $chk3 = "checked=\"checked\""; else $chk3 = "";

        if (preg_match("`<img src=`i", $title))
        {
            preg_match("/^(<img src=\")?([^\"]+)/i", $title, $matches);
            $img = $matches[0];
            $puce = strrchr($img, '/');
            $puce = substr($puce, 1);
            $puce = htmlentities($puce);
        }

        if (preg_match("`<span style=`i", $title) && preg_match("`color:`i", $title))
        {
            $test = strstr($title, '<span');
            preg_match("/^(<span style=\")?([^;\"]+)/i", $test, $matches);
            $font = $matches[0];
            $color = strrchr($font, '"');
            $color = substr($color, 1);
            $color= str_replace("color: #", "", $color);
            $color = htmlentities($color);
        }

        $title = strip_tags($title);
        $title = htmlentities($title);

        if (substr($module, 0, 1) == "[" || $module == "") $url = "http://";
        else $url = $module;

        echo "<script type=\"text/javascript\">\n"
	."<!--\n"
	."\n"
	. "function update_img(newimage)\n"
	. "{\n"
	. "document.getElementById('img_puce').src = 'images/puces/' + newimage;\n"
	. "}\n"
	."\n"
	. "// -->\n"
	. "</script>\n";


        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _EDITLINE . "</h3>\n"
		. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/menu.php\" rel=\"modal\">\n"
	. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
	. "</div></div>\n"
	. "<div class=\"tab-content\" id=\"tab2\"><form method=\"post\" action=\"index.php?file=Admin&amp;page=menu&amp;op=send_line&amp;bid=" . $bid . "&amp;lid=" . $lid . "\">\n"
	. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" width=\"80%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
	. "<tr>\n"
	. "<td colspan=\"4\" align=\"center\"><big><b>" .$titre . " : " . $title . "</b></big></td></tr>\n"
	. "<tr><td colspan=\"2\">" . _PUCE . " : </td>\n"
	. "<td><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td><select name=\"puce\" onchange=\"update_img(this.options[selectedIndex].value);\">\n";

        list_puce($puce);

        if ($puce == "") $puce = "none.gif";

        echo "</select></td><td>&nbsp;</td>\n"
	. "<td><table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">\n"
	. "<tr><td><img id=\"img_puce\" src=\"images/puces/" . $puce . "\" alt=\"\" /></td></tr></table>\n"
	. "</td></td></tr></table>\n"
	. "<td style=\"width: 25%;\" rowspan=\"4\" align=\"right\">" . _COLOR . " : <a href=\"#\" onclick=\"javascript:window.open('index.php?file=Admin&amp;page=menu&amp;nuked_nude=menu&amp;op=code_color&amp;color=" . $color . "','" . _COLOR . "','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=330,height=330,top=30,left=0');return(false)\" title=\"" . _VIEWCOLOR . "\"><b>#</b></a><input id=\"couleur\" type=\"text\" name=\"color\" value=\"" . $color . "\" size=\"7\" maxlength=\"6\" />\n"
	. "<br />" . _BOLD . " : <input class=\"checkbox\" type=\"checkbox\" name=\"b\" value=\"1\" " . $chk1 . " />&nbsp;&nbsp;\n"
	. "<br />" . _ITAL . " : <input class=\"checkbox\" type=\"checkbox\" name=\"i\" value=\"1\" " . $chk2 . " />&nbsp;&nbsp;\n"
	. "<br />" . _UNDERLINE . " : <input class=\"checkbox\" type=\"checkbox\" name=\"u\" value=\"1\" " . $chk3 . " />&nbsp;&nbsp;</td></tr>\n"
	. "<tr><td  colspan=\"2\">" . _TITLE . " :</td><td><input type=\"text\" name=\"title\" value=\"" . $title . "\" size=\"30\" /></td></tr>\n"
	. "<tr><td rowspan=\"2\" valign=\"top\">" . _URL . " : </td><td>" . _LINK . " :</td><td><input type=\"text\" name=\"url\" value=\"" . $url . "\" size=\"30\" /></td></tr>\n"
	. "<tr><td>" . _MODULE . " : </td><td><select name=\"module\">\n";

        list_mod($module);

        echo"</select></td></tr>\n"
	. "<tr><td colspan=\"2\">" . _COMMENT . " : </td><td colspan=\"2\"><input type=\"text\" name=\"comment\" value=\"" . $comment . "\" size=\"30\" /></td></tr>\n"
	. "<tr><td colspan=\"2\">" . _POSITION . " : </td><td colspan=\"2\"><select name=\"line\">\n";

        for($z = 0;$z < count($link);$z++)
        {
            if ($lid == $z) $selected = "selected=\"selected\"";
            else $selected = "";

            list($url2, $title2, $comment2, $niveau2, $blank2) = explode('|', $link[$z]);
            $title2 = strip_tags($title2);
            $title2 = htmlentities($title2);

            echo "<option value=\"" . $z . "\" " . $selected . ">" . $z . "&nbsp;&nbsp;(" . $title2 . ")</option>\n";
        }

        echo "<option value=\"" . $z . "\"  " . $selected0 . ">" . $z . "&nbsp;&nbsp;" . _LAST . " ...</option>\n"
        . "</select></td></tr>\n"
	. "<tr><td colspan=\"2\">" . _LEVEL . " : </td><td colspan=\"2\"><select name=\"niveau\">\n";

        for($j = 0;$j < 10;$j++)
        {
            if ($niveau == $j) $selected1 = "selected=\"selected\"";
            else $selected1 = "";

            echo "<option value=\"" . $j . "\" " . $selected1 . ">" . $j . "</option>\n";
        }

        echo "</select></td></tr>\n"
	. "<tr><td colspan=\"4\">" . _NEWPAGE . " : <input class=\"checkbox\" type=\"checkbox\" name=\"blank\" value=\"1\" " . $checked . " /></td></tr></table>\n"
	. "<div style=\"text-align: center;\"><br /><input type=\"submit\" value=\"" . _SEND . "\" /></div>\n"
	. "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin&amp;page=menu&amp;op=edit_menu&amp;bid=" . $bid . "\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function send_line($bid, $lid)
    {
        global $nuked, $user, $b, $i, $u, $puce, $cid;

        $sql = mysql_query("SELECT titre, content FROM " . BLOCK_TABLE . " WHERE bid = '" . $_REQUEST['bid'] . "'");
        list($titre, $content) = mysql_fetch_array($sql);

        if ($_REQUEST['niveau'] != "")
        {
            if ($_REQUEST['b'] == 1) $_REQUEST['title'] = "<b>" . $_REQUEST['title'] . "</b>";
            if ($_REQUEST['i'] == 1) $_REQUEST['title'] = "<i>" . $_REQUEST['title'] . "</i>";
            if ($_REQUEST['u'] == 1) $_REQUEST['title'] = "<span style=\"text-decoration: underline;\">" . $_REQUEST['title'] . "</span>";
            if ($_REQUEST['color'] != "") $_REQUEST['title'] = "<span style=\"color: #" . $_REQUEST['color']. ";\">" . $_REQUEST['title'] . "</span>";

            if ($_REQUEST['puce'] != "" && $puce != "none.gif") $_REQUEST['title'] = "<img src=\"images/puces/" . $_REQUEST['puce'] . "\" style=\"border: 0;\" alt=\"\" />" . $_REQUEST['title'];

            if ($content) $link = explode('NEWLINE', $content);
            if ($_REQUEST['url'] == "http://" || $_REQUEST['url'] == "") $_REQUEST['url'] = $_REQUEST['module'];

            $new_line = $_REQUEST['url'] . "|" . $_REQUEST['title'] . "|" . $_REQUEST['comment'] . "|" . $_REQUEST['niveau'] . "|" . $_REQUEST['blank'] . "|";
            $count = count($link);
            if ($lid == "") $lid = $count;
            $link[$lid] = $new_line;
            if ($link) $content = implode('NEWLINE', $link);
        }

    	if ($_REQUEST['cid'])
        {
            $link = explode('NEWLINE', $content);
            $i = 0;
            foreach($link AS $link)
            {
                if (!$_REQUEST['cid'][$i]) $link2[] = $link;
                $i++;
            }
            if ($link2) $content = implode('NEWLINE', $link2);
            else $content = "";
        }

        if ($_REQUEST['line'] != $lid) $content = move($lid, $_REQUEST['line'], $content);

        $content = mysql_real_escape_string(stripslashes($content));

        $sql = mysql_query("UPDATE " . BLOCK_TABLE . " SET content = '" . $content . "' WHERE bid = '" . $_REQUEST['bid'] . "'");
		// Action
		$texteaction = "". _ACTIONMODIFMENU .": ".$titre."";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
        if ($_REQUEST['cid'])
        {
			echo "<div class=\"notification success png_bg\">\n"
			. "<div>\n"
			. "" . _LINEDELETED . "\n"
			. "</div>\n"
			. "</div>\n";
        }
        else
        {
			echo "<div class=\"notification success png_bg\">\n"
			. "<div>\n"
			. "" . _LINEMODIFIED . "\n"
			. "</div>\n"
			. "</div>\n";
        }
		echo "<script>\n"
			."setTimeout('screen()','3000');\n"
			."function screen() { \n"
			."screenon('index.php', 'index.php?file=Admin&page=menu&op=edit_menu&bid=" . $_REQUEST['bid']."');\n"
			."}\n"
			."</script>\n";
    }

    function move($start, $end, $content)
    {
        $link = explode('NEWLINE', $content);
        $save_line = $link[$start];

        if ($start > $end)
        {
            for($i = $start;$i > $end;$i--)
            {
                $link[$i] = $link[$i-1];
            }
            $link[$end] = $save_line;
        }

        else if ($start < $end)
        {
            for($i = $start;$i < $end-1;$i++)
            {
                $link[$i] = $link[$i + 1];
            }
            $link[$end-1] = $save_line;
        }

        $content = implode('NEWLINE', $link);
        return $content;
    }

    function list_mod($module)
    {
        global $nuked, $user, $language;

        echo "<option value=\"\">-- " . _NONE . " --</option>\n";

        $modules = array();
        $handle = opendir("modules");
        while (false !== ($mod = readdir($handle)))
        {
            if ($mod != "." && $mod != ".." && $mod != "404" && $mod != "index.html" && $mod != "Comment" && $mod != "Vote")
            {
                $rep = $mod;
                $umod = strtoupper($mod);
                $modname = _NAME . $umod;

                if (defined($modname)) $modname = constant($modname);
                else $modname = $mod;


                array_push($modules, $modname . "|" . $mod);
            }
        }

        closedir($handle);
        natcasesort($modules);

	foreach($modules as $value)
	{
            $temp = explode("|", $value);
            $tmp = "[" . $temp[1] . "]";

            if ($module == $tmp)
            {
		$checked = "selected=\"selected\"";
            }
            else
            {
		$checked = "";
            }

            $sql = mysql_query("SELECT nom FROM " . MODULES_TABLE . " WHERE nom = '" . $temp[1] . "' AND admin = -1 AND niveau = -1");
            $count = mysql_num_rows($sql);

            if (is_file("modules/" . $temp[1] . "/index.php") && $count == 0)
            {
		echo "<option value=\"" . $tmp . "\" " . $checked . ">" . $temp[0] . "</option>\n";
            }
	}
    }

    function list_puce($spuce)
    {
        echo "<option value=\"none.gif\">-- " . _NONE . " --</option>\n";

        $path = "images/puces/";
        $handle = opendir($path);
        while (false !== ($puce = readdir($handle)))
        {
            if ($puce != "." && $puce != ".." && $puce != "Thumbs.db" && $puce != "index.html" && $puce != "none.gif")
            {
                if (is_file($path . $puce))
                {
                    if ($puce == $spuce) $selected = "selected=\"selected\"";
                    else $selected = "";

                    echo "<option value=\"" . $puce . "\" " . $selected . ">" . $puce . "</option>\n";
                }
            }
        }
    }

    function code_color()
    {
        global $theme, $bgcolor2;

        if (!$_REQUEST['color']) $_REQUEST['color'] ="FFFFFF";

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n"
        . "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\">\n"
        . "<head><title>" . _COLORCODE . "</title>\n"
        . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />\n"
        . "<meta http-equiv=\"content-style-type\" content=\"text/css\" />\n"
        . "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"themes/" . $theme . "/style.css\" /></head>\n"
        . "<body style=\"background: " . $bgcolor2 . ";\">";

        echo "<script type=\"text/javascript\">\n"
        ."<!--\n"
        ."\n"
        ."function shouldset(passon){\n"
        ."if(document.getElementById('hex').value.length == 7){setcolor(passon)}\n"
        ."}\n"
        ."\n"
        ."function setcolor(elem){\n"
        ."document.getElementById('hex').value=elem;\n"
        ."document.getElementById('sel').style.backgroundColor=elem;\n"
        ."}\n"
        ."\n";
		if ($_GET['balise'] == "true")
		{
			echo "function addcolor(elem){\n"
			."elem=document.getElementById('hex').value;\n"
			."elem = elem.substr(1,6);\n"
			."opener.document.getElementById('couleur2').value=elem;\n"
			."}\n"
			."\n"
			. "// -->\n"
			. "</script>\n";
		}
		else
		{
			echo "function addcolor(elem){\n"
			."elem=document.getElementById('hex').value;\n"
			."elem = elem.substr(1,6);\n"
			."opener.document.getElementById('couleur').value=elem;\n"
			."}\n"
			."\n"
			. "// -->\n"
			. "</script>\n";
		}
        echo "<table style=\"background: #000000;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"270\">\n"
        . "<tr><td style=\"width: 100%;\">\n"
        . "<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\">\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #00ff00;\"><a href=\"javascript:setcolor('#00FF00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00ff33;\"><a href=\"javascript:setcolor('#00FF33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00ff66;\"><a href=\"javascript:setcolor('#00FF66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00ff99;\"><a href=\"javascript:setcolor('#00FF99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00ffcc;\"><a href=\"javascript:setcolor('#00FFCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00ffff;\"><a href=\"javascript:setcolor('#00FFFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00cc00;\"><a href=\"javascript:setcolor('#00CC00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00cc66;\"><a href=\"javascript:setcolor('#00CC66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00cc66;\"><a href=\"javascript:setcolor('#00CC66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00cc99;\"><a href=\"javascript:setcolor('#00CC99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00cccc;\"><a href=\"javascript:setcolor('#00CCCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #00ccff;\"><a href=\"javascript:setcolor('#00CCFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #009900;\"><a href=\"javascript:setcolor('#009900')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #009933;\"><a href=\"javascript:setcolor('#009933')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #009966;\"><a href=\"javascript:setcolor('#009966')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #009999;\"><a href=\"javascript:setcolor('#009999')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #0099cc;\"><a href=\"javascript:setcolor('#0099CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #0099ff;\"><a href=\"javascript:setcolor('#0099FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #33ff00;\"><a href=\"javascript:setcolor('#33FF00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33ff33;\"><a href=\"javascript:setcolor('#33FF33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33ff66;\"><a href=\"javascript:setcolor('#33FF66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33ff99;\"><a href=\"javascript:setcolor('#33FF99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33ffcc;\"><a href=\"javascript:setcolor('#33FFCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33ffff;\"><a href=\"javascript:setcolor('#33FFFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33cc00;\"><a href=\"javascript:setcolor('#33CC00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33cc33;\"><a href=\"javascript:setcolor('#33CC33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33cc66;\"><a href=\"javascript:setcolor('#33CC66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33cc99;\"><a href=\"javascript:setcolor('#33CC99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33cccc;\"><a href=\"javascript:setcolor('#33CCCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #33ccff;\"><a href=\"javascript:setcolor('#33CCFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #339900;\"><a href=\"javascript:setcolor('#339900')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #339933;\"><a href=\"javascript:setcolor('#339933')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #339966;\"><a href=\"javascript:setcolor('#339966')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #339999;\"><a href=\"javascript:setcolor('#339999')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #3399cc;\"><a href=\"javascript:setcolor('#3399CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #3399ff;\"><a href=\"javascript:setcolor('#3399FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #66ff00;\"><a href=\"javascript:setcolor('#66FF00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66ff33;\"><a href=\"javascript:setcolor('#66FF33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66ff66;\"><a href=\"javascript:setcolor('#66FF66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66ff99;\"><a href=\"javascript:setcolor('#66FF99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66ffcc;\"><a href=\"javascript:setcolor('#66FFCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66ffff;\"><a href=\"javascript:setcolor('#66FFFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66cc00;\"><a href=\"javascript:setcolor('#66CC00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66cc33;\"><a href=\"javascript:setcolor('#66CC33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66cc66;\"><a href=\"javascript:setcolor('#66CC66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66cc99;\"><a href=\"javascript:setcolor('#66CC99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66cccc;\"><a href=\"javascript:setcolor('#66CCCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #66ccff;\"><a href=\"javascript:setcolor('#66CCFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #669900;\"><a href=\"javascript:setcolor('#669900')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #669933;\"><a href=\"javascript:setcolor('#669933')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #669966;\"><a href=\"javascript:setcolor('#669966')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #669999;\"><a href=\"javascript:setcolor('#669999')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #6699cc;\"><a href=\"javascript:setcolor('#6699CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #6699ff;\"><a href=\"javascript:setcolor('#6699FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #99ff00;\"><a href=\"javascript:setcolor('#99FF00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99ff33;\"><a href=\"javascript:setcolor('#99FF33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99ff66;\"><a href=\"javascript:setcolor('#99FF66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99ff99;\"><a href=\"javascript:setcolor('#99FF99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99ffcc;\"><a href=\"javascript:setcolor('#99FFCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99ffff;\"><a href=\"javascript:setcolor('#99FFFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99cc00;\"><a href=\"javascript:setcolor('#99CC00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99cc33;\"><a href=\"javascript:setcolor('#99CC33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99cc66;\"><a href=\"javascript:setcolor('#99CC66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99cc99;\"><a href=\"javascript:setcolor('#99CC99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99cccc;\"><a href=\"javascript:setcolor('#99CCCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #99ccff;\"><a href=\"javascript:setcolor('#99CCFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #999900;\"><a href=\"javascript:setcolor('#999900')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #999933;\"><a href=\"javascript:setcolor('#999933')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #999966;\"><a href=\"javascript:setcolor('#999966')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #999999;\"><a href=\"javascript:setcolor('#999999')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #9999cc;\"><a href=\"javascript:setcolor('#9999CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #9999ff;\"><a href=\"javascript:setcolor('#9999FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #ccff00;\"><a href=\"javascript:setcolor('#CCFF00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ccff33;\"><a href=\"javascript:setcolor('#CCFF33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ccff66;\"><a href=\"javascript:setcolor('#CCFF66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ccff99;\"><a href=\"javascript:setcolor('#CCFF99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ccffcc;\"><a href=\"javascript:setcolor('#CCFFCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ccffff;\"><a href=\"javascript:setcolor('#CCFFFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cccc00;\"><a href=\"javascript:setcolor('#CCCC00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cccc33;\"><a href=\"javascript:setcolor('#CCCC33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cccc66;\"><a href=\"javascript:setcolor('#CCCC66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cccc99;\"><a href=\"javascript:setcolor('#CCCC99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cccccc;\"><a href=\"javascript:setcolor('#CCCCCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ccccff;\"><a href=\"javascript:setcolor('#CCCCFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc9900;\"><a href=\"javascript:setcolor('#CC9900')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc9933;\"><a href=\"javascript:setcolor('#CC9933')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc9966;\"><a href=\"javascript:setcolor('#CC9966')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc9999;\"><a href=\"javascript:setcolor('#CC9999')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc99cc;\"><a href=\"javascript:setcolor('#CC99CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc99ff;\"><a href=\"javascript:setcolor('#CC99FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffff00;\"><a href=\"javascript:setcolor('#FFFF00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffff33;\"><a href=\"javascript:setcolor('#FFFF33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffff66;\"><a href=\"javascript:setcolor('#FFFF66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffff99;\"><a href=\"javascript:setcolor('#FFFF99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffffcc;\"><a href=\"javascript:setcolor('#FFFFCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffffff;\"><a href=\"javascript:setcolor('#FFFFFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffcc00;\"><a href=\"javascript:setcolor('#FFCC00')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffcc33;\"><a href=\"javascript:setcolor('#FFCC33')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffcc66;\"><a href=\"javascript:setcolor('#FFCC66')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffcc99;\"><a href=\"javascript:setcolor('#FFCC99')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffcccc;\"><a href=\"javascript:setcolor('#FFCCCC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffccff;\"><a href=\"javascript:setcolor('#FFCCFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff9900;\"><a href=\"javascript:setcolor('#FF9900')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff9933;\"><a href=\"javascript:setcolor('#FF9933')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff9966;\"><a href=\"javascript:setcolor('#FF9966')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff9999;\"><a href=\"javascript:setcolor('#FF9999')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff99cc;\"><a href=\"javascript:setcolor('#FF99CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff99ff;\"><a href=\"javascript:setcolor('#FF99FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #006600;\"><a href=\"javascript:setcolor('#006600')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #006633;\"><a href=\"javascript:setcolor('#006633')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #006666;\"><a href=\"javascript:setcolor('#006666')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #006699;\"><a href=\"javascript:setcolor('#006699')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #0066cc;\"><a href=\"javascript:setcolor('#0066CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #0066ff;\"><a href=\"javascript:setcolor('#0066FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #003300;\"><a href=\"javascript:setcolor('#003300')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #003333;\"><a href=\"javascript:setcolor('#003333')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #003366;\"><a href=\"javascript:setcolor('#003366')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #003399;\"><a href=\"javascript:setcolor('#003399')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #0033cc;\"><a href=\"javascript:setcolor('#0033CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #0033ff;\"><a href=\"javascript:setcolor('#0033FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #000000;\"><a href=\"javascript:setcolor('#000000')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #000033;\"><a href=\"javascript:setcolor('#000033')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #000066;\"><a href=\"javascript:setcolor('#000066')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #000099;\"><a href=\"javascript:setcolor('#000099')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #0000cc;\"><a href=\"javascript:setcolor('#0000CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #0000ff;\"><a href=\"javascript:setcolor('#0000FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #336600;\"><a href=\"javascript:setcolor('#336600')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #336633;\"><a href=\"javascript:setcolor('#336633')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #336666;\"><a href=\"javascript:setcolor('#336666')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #336699;\"><a href=\"javascript:setcolor('#336699')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #3366cc;\"><a href=\"javascript:setcolor('#3366CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #3366ff;\"><a href=\"javascript:setcolor('#3366FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #333300;\"><a href=\"javascript:setcolor('#333300')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #333333;\"><a href=\"javascript:setcolor('#333333')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #333366;\"><a href=\"javascript:setcolor('#333366')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #333399;\"><a href=\"javascript:setcolor('#333399')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #3333cc;\"><a href=\"javascript:setcolor('#3333CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #3333ff;\"><a href=\"javascript:setcolor('#3333FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #330000;\"><a href=\"javascript:setcolor('#330000')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #330033;\"><a href=\"javascript:setcolor('#330033')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #330066;\"><a href=\"javascript:setcolor('#330066')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #330099;\"><a href=\"javascript:setcolor('#330099')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #3300cc;\"><a href=\"javascript:setcolor('#3300CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #3300ff;\"><a href=\"javascript:setcolor('#3300FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #666600;\"><a href=\"javascript:setcolor('#666600')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #666633;\"><a href=\"javascript:setcolor('#666633')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #666666;\"><a href=\"javascript:setcolor('#666666')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #666699;\"><a href=\"javascript:setcolor('#666699')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #6666cc;\"><a href=\"javascript:setcolor('#6666CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #6666ff;\"><a href=\"javascript:setcolor('#6666FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #663300;\"><a href=\"javascript:setcolor('#663300')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #663333;\"><a href=\"javascript:setcolor('#663333')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #663366;\"><a href=\"javascript:setcolor('#663366')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #663399;\"><a href=\"javascript:setcolor('#663399')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #6633cc;\"><a href=\"javascript:setcolor('#6633CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #6633ff;\"><a href=\"javascript:setcolor('#6633FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #660000;\"><a href=\"javascript:setcolor('#660000')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #660033;\"><a href=\"javascript:setcolor('#660033')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #660066;\"><a href=\"javascript:setcolor('#660066')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #660099;\"><a href=\"javascript:setcolor('#660099')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #6600cc;\"><a href=\"javascript:setcolor('#6600CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #6600ff;\"><a href=\"javascript:setcolor('#6600FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #996600;\"><a href=\"javascript:setcolor('#996600')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #996633;\"><a href=\"javascript:setcolor('#996633')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #996666;\"><a href=\"javascript:setcolor('#996666')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #996699;\"><a href=\"javascript:setcolor('#996699')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #9966cc;\"><a href=\"javascript:setcolor('#9966CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #9966ff;\"><a href=\"javascript:setcolor('#9966FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #993300;\"><a href=\"javascript:setcolor('#993300')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #993333;\"><a href=\"javascript:setcolor('#993333')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #993366;\"><a href=\"javascript:setcolor('#993366')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #993399;\"><a href=\"javascript:setcolor('#993399')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #9933cc;\"><a href=\"javascript:setcolor('#9933CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #9933ff;\"><a href=\"javascript:setcolor('#9933FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #990000;\"><a href=\"javascript:setcolor('#990000')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #990033;\"><a href=\"javascript:setcolor('#990033')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #990066;\"><a href=\"javascript:setcolor('#990066')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #990099;\"><a href=\"javascript:setcolor('#990099')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #9900cc;\"><a href=\"javascript:setcolor('#9900CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #9900ff;\"><a href=\"javascript:setcolor('#9900FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc6600;\"><a href=\"javascript:setcolor('#CC6600')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc6633;\"><a href=\"javascript:setcolor('#CC6633')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc6666;\"><a href=\"javascript:setcolor('#CC6666')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc6699;\"><a href=\"javascript:setcolor('#CC6699')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc66cc;\"><a href=\"javascript:setcolor('#CC66CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc66ff;\"><a href=\"javascript:setcolor('#CC66FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc3300;\"><a href=\"javascript:setcolor('#CC3300')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc3333;\"><a href=\"javascript:setcolor('#CC3333')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc3366;\"><a href=\"javascript:setcolor('#CC3366')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc3399;\"><a href=\"javascript:setcolor('#CC3399')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc33cc;\"><a href=\"javascript:setcolor('#CC33CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc33ff;\"><a href=\"javascript:setcolor('#CC33FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc0000;\"><a href=\"javascript:setcolor('#CC0000')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc0033;\"><a href=\"javascript:setcolor('#CC0033')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc0066;\"><a href=\"javascript:setcolor('#CC0066')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc0099;\"><a href=\"javascript:setcolor('#CC0099')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc00cc;\"><a href=\"javascript:setcolor('#CC00CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #cc00ff;\"><a href=\"javascript:setcolor('#CC00FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff6600;\"><a href=\"javascript:setcolor('#FF6600')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff6633;\"><a href=\"javascript:setcolor('#FF6633')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff6666;\"><a href=\"javascript:setcolor('#FF6666')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff6699;\"><a href=\"javascript:setcolor('#FF6699')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff66cc;\"><a href=\"javascript:setcolor('#FF66CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff66ff;\"><a href=\"javascript:setcolor('#FF66FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff3300;\"><a href=\"javascript:setcolor('#FF3300')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff3333;\"><a href=\"javascript:setcolor('#FF3333')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff3366;\"><a href=\"javascript:setcolor('#FF3366')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff3399;\"><a href=\"javascript:setcolor('#FF3399')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff33cc;\"><a href=\"javascript:setcolor('#FF33CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff33ff;\"><a href=\"javascript:setcolor('#FF33FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff0000;\"><a href=\"javascript:setcolor('#FF0000')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff0033;\"><a href=\"javascript:setcolor('#FF0033')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff0066;\"><a href=\"javascript:setcolor('#FF0066')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff0099;\"><a href=\"javascript:setcolor('#FF0099')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff00cc;\"><a href=\"javascript:setcolor('#FF00CC')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #ff00ff;\"><a href=\"javascript:setcolor('#FF00FF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr>\n"
        . "</table>\n"
        . "</td></tr>\n"
        . "</table>\n"
        . "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"290\">\n"
        . "<tr><td style=\"width: 100%;\">&nbsp;</td></tr>\n"
        . "<tr><td style=\"width: 100%;\">\n"
        . "<table style=\"background: #000000;\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"135\">\n"
        . "<tr><td style=\"width: 100%;\">\n"
        . "<table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\">\n"
        . "<tr>\n"
        . "<td style=\"width: 15px;height:15px;background: #ffffff;\"><a href=\"javascript:setcolor('#FFFFFF')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #dddddd;\"><a href=\"javascript:setcolor('#DDDDDD')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #c0c0c0;\"><a href=\"javascript:setcolor('#C0C0C0')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #969696;\"><a href=\"javascript:setcolor('#969696')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #808080;\"><a href=\"javascript:setcolor('#808080')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #646464;\"><a href=\"javascript:setcolor('#646464')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #4b4b4b;\"><a href=\"javascript:setcolor('#4B4B4B')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #242424;\"><a href=\"javascript:setcolor('#242424')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "<td style=\"width: 15px;height:15px;background: #000000;\"><a href=\"javascript:setcolor('#000000')\"><img style=\"border: 0;\" src=\"images/pixel.gif\" alt=\"\" height=\"15\" width=\"15\" /></a></td>\n"
        . "</tr></table>\n"
        . "</td></tr></table>\n"
        . "</td></tr></table>\n"
        . "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n"
        . "<tr><td style=\"width: 100%;\">&nbsp;</td></tr>\n"
        . "<tr><td style=\"width: 100%;\">\n"
        . "<input id=\"hex\" name=\"hexvalue\" value=\"#" . $_REQUEST['color'] . "\" size=\"10\" class=\"hexfield\" onchange=\"shouldset(this.value)\" type=\"text\" />"
        . "&nbsp;<input id=\"sel\" style=\"background-color: " . $_REQUEST['color'] . ";\" name=\"selcolor\" size=\"24\" type=\"text\" />"
        . "&nbsp;<input type=\"button\" value=\"" . _OK . "\" onclick=\"addcolor()\" /></td></tr></table>\n"
        . "<div style=\"text-align: center;\"><br /><br /><a href=\"#\" onclick=\"javascript:window.close()\"><b>" . _CLOSEWINDOW . "</b></a></div></body></html>";

    }

    switch ($_REQUEST['op'])
    {
        case "index":
            admintop();
            index();
            adminfoot();
            break;

        case "edit_menu":
            admintop();
            edit_menu($_REQUEST['bid']);
            adminfoot();
            break;

        case "edit_line":
            admintop();
            edit_line($_REQUEST['bid'], $_REQUEST['lid']);
            adminfoot();
            break;

        case "send_line":
            admintop();
            send_line($_REQUEST['bid'], $_REQUEST['lid']);
            adminfoot();
            break;

        case "move":
            admintop();
            move($_REQUEST['start'], $_REQUEST['end'], $_REQUEST['content']);
            adminfoot();
            break;

        case "code_color":
            code_color();
            break;

        default:
            admintop();
            index();
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
