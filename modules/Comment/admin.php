<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined('INDEX_CHECK')) die('<div style="text-align:center;">You cannot open this page directly</div>');

global $user, $language;
translate("modules/Comment/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");
admintop();
$visiteur = (!$user) ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_admin = admin_mod($ModName);

if ($visiteur >= $level_admin && $level_admin > -1){
    function edit_com($cid){
        global $nuked, $language;

        $sql = mysql_query("SELECT autor, autor_id, titre, comment, autor_ip FROM " . COMMENT_TABLE . " WHERE id = '" . $cid . "'");
        list($auteur, $autor_id, $titre, $texte, $ip) = mysql_fetch_array($sql);
        $auteur = htmlspecialchars($auteur);
		
        $titre = htmlentities($titre);

        if($autor_id != ""){
     	    $sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
            $test = mysql_num_rows($sql_member);
        }

        if($autor_id != "" && $test > 0){
            list($autor) = mysql_fetch_array($sql_member);
        }
        else{
            $autor = $auteur;
        }
		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
				. "<div class=\"content-box-header\"><h3>" . _ADMINCOMMENT . "</h3>\n"
				. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Comment.php\" rel=\"modal\">\n"
				. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
				. "</div></div>\n"
				. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><a href=\"index.php?file=Comment&amp;page=admin\">" . _COMMENTINDEX . "</a><b> | "
				. "<a href=\"index.php?file=Comment&amp;page=admin&amp;op=module_com\">" . _COMMENTMOD . "</a></b></div><br />\n"
				. "<form method=\"post\" action=\"index.php?file=Comment&amp;page=admin&amp;op=modif_com\">\n"
				. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">\n"
				. "<tr><td><b>" . _NICK . " :</b> " . $autor . " ( " . $ip . " )</td></tr>\n"
				. "<tr><td><b>" . _TITLE . " :</b> <input type=\"text\" name=\"titre\" value=\"" . $titre . "\" size=\"40\" /></td></tr>\n"
				. "<tr><td><br /><b>" . _MESSAGE . " :</b></td></tr>\n"
				. "<tr><td><textarea class=\"editor\" name=\"texte\" cols=\"65\" rows=\"10\">" . $texte . "</textarea></td></tr>\n"
				. "<tr><td align=\"center\"><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" />\n"
				. "<br /><input type=\"submit\" name=\"send\" value=\"" . _MODIF . "\" /></td></tr></table>\n"
				. "<div style=\"text-align: center;\"><br />[ <a href=\"javascript:history.back();\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }

    function modif_com($cid, $titre, $texte){
        global $nuked, $user;
		
		$texte = secu_html(html_entity_decode($texte));
        $texte = mysql_real_escape_string(stripslashes($texte));
        $titre = mysql_real_escape_string(stripslashes($titre));

        $sql = mysql_query("UPDATE " . COMMENT_TABLE . " SET titre = '" . $titre . "', comment = '" . $texte . "' WHERE id = '" . $cid . "'");
		// Action
		$texteaction = "". _ACTIONMODIFCOM .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
				. "<div>\n"
				. "" . _COMMENTMODIF . "\n"
				. "</div>\n"
				. "</div>\n";
				
        redirect("index.php?file=Comment&page=admin", 2);
    }

    function del_com($cid){
        global $nuked, $user;

        $sql = mysql_query("DELETE FROM " . COMMENT_TABLE . " WHERE id = '" . $cid . "'");
		// Action
		$texteaction = "". _ACTIONDELCOM .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
				. "<div>\n"
				. "" . _COMMENTDEL . "\n"
				. "</div>\n"
				. "</div>\n";
				
        redirect("index.php?file=Comment&page=admin", 2);
    }

    function main(){
        global $nuked, $language;

        $nb_com = 30;

        $sql2 = mysql_query("SELECT id FROM " . COMMENT_TABLE);
        $count = mysql_num_rows($sql2);

        if (!$_REQUEST['p']) $_REQUEST['p'] = 1;
        $start = $_REQUEST['p'] * $nb_com - $nb_com;

        echo "<script type=\"text/javascript\">\n"
				. "<!--\n"
				. "\n"
				. "function del_mess(pseudo, id)\n"
				. "{\n"
				. "if (confirm('" . _DELCOMMENT . " '+pseudo+' ! " . _CONFIRM . "'))\n"
				. "{document.location.href = 'index.php?file=Comment&page=admin&op=del_com&cid='+id;}\n"
				. "}\n"
				. "\n"
				. "// -->\n"
				. "</script>\n";

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
				. "<div class=\"content-box-header\"><h3>" . _ADMINCOMMENT . "</h3>\n"
				. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Comment.php\" rel=\"modal\">\n"
				. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
				. "</div></div>\n"
				. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\">" . _COMMENTINDEX . "<b> | "
				. "<a href=\"index.php?file=Comment&amp;page=admin&amp;op=module_com\">" . _COMMENTMOD . "</a></b></div><br />\n";

        if ($count > $nb_com){
            echo "<table width=\"100%\"><tr><td>";
            number($count, $nb_com, "index.php?file=Comment&amp;page=admin");
            echo"</td></tr></table>\n";
        }

        echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
				. "<tr>\n"
				. "<td style=\"width: 25%;\" align=\"center\"><b>" . _DATE . "</b></td>\n"
				. "<td style=\"width: 25%;\" align=\"center\"><b>" . _NICK . "</b></td>\n"
				. "<td style=\"width: 20%;\" align=\"center\"><b>" . _MODULE . "</b></td>\n"
				. "<td style=\"width: 15%;\" align=\"center\"><b>" . _EDIT . "</b></td>\n"
				. "<td style=\"width: 15%;\" align=\"center\"><b>" . _DEL . "</b></td></tr>\n";

        $sql = mysql_query("SELECT id, im_id, date, autor, autor_id, module FROM " . COMMENT_TABLE . " ORDER BY id DESC LIMIT " . $start . ", " . $nb_com);
        while (list($id, $im_id, $date, $auteur, $autor_id, $module) = mysql_fetch_array($sql)){
            $date = nkDate($date);
            $auteur = htmlspecialchars($auteur);

            if($autor_id != ""){
                $sql_member = mysql_query("SELECT pseudo FROM " . USER_TABLE . " WHERE id = '" . $autor_id . "'");
                $test = mysql_num_rows($sql_member);
            }

            if($autor_id != "" && $test > 0){
                list($autor) = mysql_fetch_array($sql_member);
            }
            else{
                $autor = $auteur;
            }

            echo "<tr><td style=\"width: 25%;\" align=\"center\">" . $date . "</td><td style=\"width: 25%;\" align=\"center\">" . $autor . "</td><td style=\"width: 20%;\"align=\"center\">" . $module . "</td>\n"
					. "<td style=\"width: 15%;\" align=\"center\"><a href=\"index.php?file=Comment&amp;page=admin&amp;op=edit_com&amp;cid=" . $id . "\" title=\"" . _EDITTHISCOM . "\"><img style=\"border: 0;\" src=\"images/edit.gif\" alt=\"\" /></a></td>\n"
					. "<td style=\"width: 15%;\" align=\"center\"><a href=\"javascript:del_mess('" . $autor . "', '" . $id . "');\" title=\"" . _DELTHISCOM . "\"><img style=\"border: 0;\" src=\"images/del.gif\" alt=\"\" /></a></td></tr>\n";
        }

        if ($count == "0"){
            echo "<tr><td colspan=\"5\" align=\"center\">" . _NOCOMMENT . "</td></tr>\n";
        }

        echo "</table>";

        if ($count > $nb_com){
            echo "<table width=\"100%\"><tr><td>";
            number($count, $nb_com, "index.php?file=Comment&amp;page=admin");
            echo "</td></tr></table>";
        }
        echo "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div><br /></div></div>\n";
    }
	
	function module_send_com($news, $download, $sections, $links, $wars, $gallery, $survey){
        global $nuked, $user;

        $sql1 = mysql_query("UPDATE " . $nuked['prefix'] . "_comment_mod SET active = '" . $news . "' WHERE module = 'news'");
		$sql2 = mysql_query("UPDATE " . $nuked['prefix'] . "_comment_mod SET active = '" . $download . "' WHERE module = 'download'");
		$sql3 = mysql_query("UPDATE " . $nuked['prefix'] . "_comment_mod SET active = '" . $sections . "' WHERE module = 'sections'");
		$sql4 = mysql_query("UPDATE " . $nuked['prefix'] . "_comment_mod SET active = '" . $links . "' WHERE module = 'links'");
		$sql5 = mysql_query("UPDATE " . $nuked['prefix'] . "_comment_mod SET active = '" . $wars . "' WHERE module = 'wars'");
		$sql6 = mysql_query("UPDATE " . $nuked['prefix'] . "_comment_mod SET active = '" . $gallery . "' WHERE module = 'gallery'");
		$sql7 = mysql_query("UPDATE " . $nuked['prefix'] . "_comment_mod SET active = '" . $survey . "' WHERE module = 'survey'");
		// Action
		$texteaction = "". _ACTIONMODIFCOMMOD .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
				. "<div>\n"
				. "" . _COMMENTMODIFMOD . "\n"
				. "</div>\n"
				. "</div>\n";
				
        redirect("index.php?file=Comment&page=admin&op=module_com", 2);
    }
	
	function module_com(){
        global $nuked, $language;
        
		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
				. "<div class=\"content-box-header\"><h3>" . _ADMINCOMMENT . "</h3>\n"
				. "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Comment.php\" rel=\"modal\">\n"
				. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
				. "</div></div>\n"
				. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><a href=\"index.php?file=Comment&amp;page=admin\">" . _COMMENTINDEX . "</a><b> | "
				. "" . _COMMENTMOD . "</b></div><br />\n"
				. "<form method=\"post\" action=\"index.php?file=Comment&amp;page=admin&amp;op=module_send_com\">\n"
				. "<table style=\"margin-left: auto;margin-right: auto;text-align: left;\" cellspacing=\"0\" cellpadding=\"3\" border=\"0\">"._LISTI."\n";
				
		$sql = mysql_query("SELECT module, active FROM " . $nuked['prefix'] . "_comment_mod");
		while(list($module, $active) = mysql_fetch_array($sql)){
		?>
			<tr><td><b><?php echo $module; ?>:</b></td><td>
		<?php if( $active == 0){
		?>
				<select name="<?php echo $module; ?>">
					<option value="0"><?php echo _DESACTIVER; ?></option>
					<option value="1"><?php echo _ACTIVER; ?></option>
				</select></td></tr>
		<?php
			}
			else{
		?>
				<select name="<?php echo $module; ?>">
					<option value="1"><?php echo _ACTIVER; ?></option>
					<option value="0"><?php echo _DESACTIVER; ?></option>
				</select></td></tr>
		<?php
			}
		}	
		echo "<tr><td align=\"center\"><input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" />\n"
				. "<br /><input type=\"submit\" name=\"send\" value=\"" . _MODIF . "\" /></td></tr></table>\n"
				. "<div style=\"text-align: center;\"><br />[ <a href=\"javascript:history.back();\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
    }
    switch ($_REQUEST['op']){
        case "edit_com":
            edit_com($_REQUEST['cid']);
            break;

        case "modif_com":
            modif_com($_REQUEST['cid'], $_REQUEST['titre'], $_REQUEST['texte']);
            break;

        case "del_com":
            del_com($_REQUEST['cid']);
            break;
			
		case "module_com":
            module_com();
            break;
		
		case "module_send_com":
            module_send_com($_REQUEST['news'],$_REQUEST['download'],$_REQUEST['sections'],$_REQUEST['links'],$_REQUEST['wars'],$_REQUEST['gallery'],$_REQUEST['survey']);
            break;
		
        default:
            main();
            break;
    }
}
else if ($level_admin == -1){
    echo "<div class=\"notification error png_bg\">\n"
			. "<div>\n"
			. "<br /><br /><div style=\"text-align: center;\">" . _MODULEOFF . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
			. "</div>\n"
			. "</div>\n";
}
else if ($visiteur > 1){
    echo "<div class=\"notification error png_bg\">\n"
			. "<div>\n"
			. "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
			. "</div>\n"
			. "</div>\n";
}
else{
    echo "<div class=\"notification error png_bg\">\n"
			. "<div>\n"
			. "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a href=\"javascript:history.back()\"><b>" . _BACK . "</b></a></div><br /><br />"
			. "</div>\n"
			. "</div>\n";
}
adminfoot();
?>