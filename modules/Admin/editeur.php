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
if ($visiteur == 9)
{
    function main()
    {
        global $user, $nuked, $language;
		
		$sql = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'couleur'");
		list($couleur) = mysql_fetch_array($sql);
		$sql2 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'bouton'");
		list($bouton) = mysql_fetch_array($sql2);
		$sql3 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'status'");
		list($status) = mysql_fetch_array($sql3);
		if($bouton == "top")
		{
			$checked11 = "selected=\"selected\"";
		}
		else
		{
			$checked12 = "selected=\"selected\"";
		}
		if($status == "top")
		{
			$checked21 = "selected=\"selected\"";
		}
		else if ($status == "bottom")
		{
			$checked22 = "selected=\"selected\"";
		}
		else
		{
			$checked23 = "selected=\"selected\"";
		}
		echo "<link rel=\"stylesheet\" href=\"modules/Admin/css/editeur.css\" type=\"text/css\"/><div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _ADMINEDITEUR . "</h3>\n"
		. "</div>\n"
		. "<div class=\"tab-content\" id=\"tab2\"><br/>\n"
		. "<form method=\"post\" action=\"index.php?file=Admin&amp;page=editeur&amp;op=editeur\">\n"
		. "<div style=\"width:96%\"><table style=\"margin-left: 2%;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\">\n"
		. "<tr><td colspan=\"2\"><big><b>" . _GENERAL . "</b></big></td></tr>\n"
		. "<tr><td>" . _COULEUR . " :</td><td><input type=\"text\" id=\"couleur\" name=\"couleur\" size=\"40\" value=\"".$couleur."\" /><a href=\"#\" onclick=\"javascript:window.open('index.php?file=Admin&amp;page=menu&amp;nuked_nude=menu&amp;op=code_color&amp;color=" . $color . "','" . _COLOR . "','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=330,height=330,top=30,left=0');return(false)\" title=\"" . _VIEWCOLOR . "\">["._COLOR."]</a></td></tr>\n"
		. "<tr><td>" . _BARREBOUTON . " :</td><td><select name=\"bouton\"><option value=\"top\" " . $checked11 . ">" . _TOP . "</option><option value=\"bottom\" " . $checked12 . ">" . _BOTTOM . "</option></select></td></tr>\n"
		. "<tr><td>" . _BARRESTATUS . " :</td><td><select name=\"status\"><option value=\"top\" " . $checked21 . ">" . _TOP . "</option><option value=\"bottom\" " . $checked22 . ">" . _BOTTOM . "</option><option value=\"none\" " . $checked23 . ">" . _NONE . "</option></select></td></tr>\n"
		. "<tr><td colspan=\"2\"><big><b>" . _BOUTON . "</b></big></td></tr>\n"
		. "<tr><td><table><tr><td width=\"25%\">" . _LIGNE1 . " :<br />\n";
		
		$sql3 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'ligne1b'");
		list($ligne1b) = mysql_fetch_array($sql3);
		$ligne1b = explode(",",$ligne1b);
		for($nbr =0; $nbr <=20; $nbr++)
		{
			if($ligne1b[$nbr] != "")
			{
				$ligne1[$nbr] = "checked=\"checked\"";
			}
		}
		$sql4 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'ligne2b'");
		list($ligne2b) = mysql_fetch_array($sql4);
		$ligne2b = explode(",",$ligne2b);
		for($nbr =0; $nbr <=12; $nbr++)
		{
			if($ligne2b[$nbr] != "")
			{
				$ligne2[$nbr] = "checked=\"checked\"";
			}
		}
		$sql5 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'ligne3b'");
		list($ligne3b) = mysql_fetch_array($sql5);
		$ligne3b = explode(",",$ligne3b);
		for($nbr =0; $nbr <=19; $nbr++)
		{
			if($ligne3b[$nbr] != "")
			{
				$ligne3[$nbr] = "checked=\"checked\"";
			}
		}
		$sql6 = mysql_query("SELECT value FROM " . $nuked['prefix'] . "_editeur WHERE name = 'ligne4b'");
		list($ligne4b) = mysql_fetch_array($sql6);
		$ligne4b = explode(",",$ligne4b);
		for($nbr =0; $nbr <=7; $nbr++)
		{
			if($ligne4b[$nbr] != "")
			{
				$ligne4[$nbr] = "checked=\"checked\"";
			}
		}
		?>
		<input type="button" class="mce_save"/><input type="checkbox" name="ligne1[0]" value="save" <?php echo $ligne1[0]; ?> /><br />
		<input type="button" class="mce_newdocument"/><input type="checkbox" name="ligne1[1]" value="newdocument" <?php echo $ligne1[1]; ?> /><br />
		<input type="button" class="mce_restoredraft"/><input type="checkbox" name="ligne1[2]" value="restoredraft" <?php echo $ligne1[2]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne1[3]" value="|" <?php echo $ligne1[3]; ?> /><br />
		<input type="button" class="mce_cut"/><input type="checkbox" name="ligne1[4]" value="cut" <?php echo $ligne1[4]; ?> /><br />
		<input type="button" class="mce_copy"/><input type="checkbox" name="ligne1[5]" value="copy" <?php echo $ligne1[5]; ?> /><br />
		<input type="button" class="mce_paste"/><input type="checkbox" name="ligne1[6]" value="paste" <?php echo $ligne1[6]; ?> /><br />
		<input type="button" class="mce_pastetext"/><input type="checkbox" name="ligne1[7]" value="pastetext" <?php echo $ligne1[7]; ?> /><br />
		<input type="button" class="mce_pasteword"/><input type="checkbox" name="ligne1[8]" value="pasteword" <?php echo $ligne1[8]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne1[9]" value="|" <?php echo $ligne1[9]; ?> /><br />
		<input type="button" class="mce_undo"/><input type="checkbox" name="ligne1[10]" value="undo" <?php echo $ligne1[10]; ?> /><br />
		<input type="button" class="mce_redo"/><input type="checkbox" name="ligne1[11]" value="redo" <?php echo $ligne1[11]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne1[12]" value="|" <?php echo $ligne1[12]; ?> /><br />
		<input type="button" class="mce_print"/><input type="checkbox" name="ligne1[13]" value="print" <?php echo $ligne1[13]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne1[14]" value="|" <?php echo $ligne1[14]; ?> /><br />
		<input type="button" class="mce_fullscreen"/><input type="checkbox" name="ligne1[15]" value="fullscreen" <?php echo $ligne1[15]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne1[16]" value="|" <?php echo $ligne1[16]; ?> /><br />
		<input type="button" class="mce_preview"/><input type="checkbox" name="ligne1[17]" value="preview" <?php echo $ligne1[17]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne1[18]" value="|" <?php echo $ligne1[18]; ?> /><br />
		<input type="button" class="mce_help"/><input type="checkbox" name="ligne1[19]" value="help" <?php echo $ligne1[19]; ?> /><br />
		<input type="button" class="mce_code"/><input type="checkbox" name="ligne1[20]" value="code" <?php echo $ligne1[20]; ?> /><br />
		</td><td width="25%">
		<?php echo _LIGNE2; ?><br />
		<span style="font-size:9px;margin-left:3px;">Style</span><input type="checkbox" name="ligne2[0]" value="styleselect" <?php echo $ligne2[0]; ?> /><br />
		<span style="font-size:9px;">Police</span><input type="checkbox" name="ligne2[1]" value="fontselect" <?php echo $ligne2[1]; ?> /><br />
		<span style="font-size:9px;margin-left:4px;">Taille</span><input type="checkbox" name="ligne2[2]" value="fontsizeselect" <?php echo $ligne2[2]; ?> /><br />
		<input style="margin-left:13px;" type="button" class="separator"/><input type="checkbox" name="ligne2[3]" value="|" <?php echo $ligne2[3]; ?> /><br />
		<input style="margin-left:5px;"  type="button" class="mce_link"/><input type="checkbox" name="ligne2[4]" value="link" <?php echo $ligne2[4]; ?> /><br />
		<input style="margin-left:5px;"  type="button" class="mce_unlink"/><input type="checkbox" name="ligne2[5]" value="unlink" <?php echo $ligne2[5]; ?> /><br />
		<input style="margin-left:5px;"  type="button" class="mce_anchor"/><input type="checkbox" name="ligne2[6]" value="anchor" <?php echo $ligne2[6]; ?> /><br />
		<input style="margin-left:13px;"  type="button" class="separator"/><input type="checkbox" name="ligne2[7]" value="|" <?php echo $ligne2[7]; ?> /><br />
		<input style="margin-left:5px;"  type="button" class="mce_emotions"/><input type="checkbox" name="ligne2[8]" value="emotions" <?php echo $ligne2[8]; ?> /><br />
		<input style="margin-left:5px;"  type="button" class="mce_image"/><input type="checkbox" name="ligne2[9]" value="image" <?php echo $ligne2[9]; ?> /><br />
		<input style="margin-left:5px;"  type="button" class="mce_forecolor"/><input type="checkbox" name="ligne2[11]" value="forecolor" <?php echo $ligne2[11]; ?> /><br />
		<input style="margin-left:5px;"  type="button" class="mce_backcolor"/><input type="checkbox" name="ligne2[12]" value="backcolor" <?php echo $ligne2[12]; ?> /><br />
		</td><td width="25%">
		<?php echo _LIGNE3; ?><br />
		<input type="button" class="mce_bold"/><input type="checkbox" name="ligne3[0]" value="bold" <?php echo $ligne3[0]; ?> /><br />
		<input type="button" class="mce_italic"/><input type="checkbox" name="ligne3[1]" value="italic" <?php echo $ligne3[1]; ?> /><br />
		<input type="button" class="mce_underline"/><input type="checkbox" name="ligne3[2]" value="underline" <?php echo $ligne3[2]; ?> /><br />
		<input type="button" class="mce_strikethrough"/><input type="checkbox" name="ligne3[3]" value="strikethrough" <?php echo $ligne3[3]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne3[4]" value="|" <?php echo $ligne3[4]; ?> /><br />
		<input type="button" class="mce_justifyleft"/><input type="checkbox" name="ligne3[5]" value="justifyleft" <?php echo $ligne3[5]; ?> /><br />
		<input type="button" class="mce_justifycenter"/><input type="checkbox" name="ligne3[6]" value="justifycenter" <?php echo $ligne3[6]; ?> /><br />
		<input type="button" class="mce_justifyright"/><input type="checkbox" name="ligne3[7]" value="justifyright" <?php echo $ligne3[7]; ?> /><br />
		<input type="button" class="mce_justifyfull"/><input type="checkbox" name="ligne3[8]" value="justifyfull" <?php echo $ligne3[8]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne3[9]" value="|" <?php echo $ligne3[9]; ?> /><br />
		<input type="button" class="mce_bullist"/><input type="checkbox" name="ligne3[10]" value="bullist" <?php echo $ligne3[10]; ?> /><br />
		<input type="button" class="mce_numlist"/><input type="checkbox" name="ligne3[11]" value="numlist" <?php echo $ligne3[11]; ?> /><br />
		<input type="button" class="mce_hr"/><input type="checkbox" name="ligne3[12]" value="hr" <?php echo $ligne3[12]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne3[13]" value="|" <?php echo $ligne3[13]; ?> /><br />
		<input type="button" class="mce_outdent"/><input type="checkbox" name="ligne3[14]" value="outdent" <?php echo $ligne3[14]; ?> /><br />
		<input type="button" class="mce_indent"/><input type="checkbox" name="ligne3[15]" value="indent" <?php echo $ligne3[15]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne3[16]" value="|" <?php echo $ligne3[16]; ?> /><br />
		<input type="button" class="mce_removeformat"/><input type="checkbox" name="ligne3[17]" value="removeformat" <?php echo $ligne3[17]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne3[18]" value="|" <?php echo $ligne3[18]; ?> /><br />
		<input type="button" class="mce_spellchecker"/><input type="checkbox" name="ligne3[19]" value="spellchecker" <?php echo $ligne3[19]; ?> /><br />
		
		</td><td width="25%">
		<?php echo _LIGNE4; ?><br />
		<input type="button" class="mce_table"/><input type="checkbox" name="ligne4[0]" value="tablecontrols" <?php echo $ligne4[0]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne4[1]" value="|" <?php echo $ligne4[1]; ?> /><br />
		<input type="button" class="mce_blockquote"/><input type="checkbox" name="ligne4[2]" value="blockquote" <?php echo $ligne4[2]; ?> /><br />
		<input type="button" class="mce_sub"/><input type="checkbox" name="ligne4[3]" value="sub" <?php echo $ligne4[3]; ?> /><br />
		<input type="button" class="mce_sup"/><input type="checkbox" name="ligne4[4]" value="sup" <?php echo $ligne4[4]; ?> /><br />
		<input type="button" class="separator"/><input type="checkbox" name="ligne4[5]" value="|" <?php echo $ligne4[5]; ?> /><br />
		<input type="button" class="mce_charmap"/><input type="checkbox" name="ligne4[6]" value="charmap" <?php echo $ligne4[6]; ?> /><br />
		<input type="button" class="mce_pagebreak"/><input type="checkbox" name="ligne4[7]" value="pagebreak" <?php echo $ligne4[7]; ?> /><br />
		
		<?php
		echo "</td></tr></table>\n"
		. "<tr><td>&nbsp;</td><td><input class=\"button\" type=\"submit\" value=\"Send\" /></td></tr></td></tr></table></form>\n"
		."<form method=\"post\" onsubmit=\"styling(this.nam.value,this.taille.value,this.couleur2.value,this.gras.value,this.italique.value,this.souligne.value);return false\" action=\"\"> <table style=\"margin-left: 2%;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr><td colspan=\"2\">\n"
		. "<tr><td colspan=\"2\"><big><b>" . _STYLE . "</b></big></td></tr>\n"
		. "<tr><td>" . _NAME . " :</td><td><input type=\"text\" name=\"nam\" size=\"40\" value=\"\" /></td></tr>\n"
		. "<tr><td>" . _Taille . " :</td><td><select name=\"taille\"><option value=\"rien\">Rien</option><option value=\"8\">8</option><option value=\"10\">10</option><option value=\"12\">12</option><option value=\"14\">14</option><option value=\"16\">16</option><option value=\"18\">18</option><option value=\"20\">20</option><option value=\"24\">24</option><option value=\"32\">32</option></select></td></tr>\n"
		. "<tr><td>" . _Couleur . " :</td><td><input type=\"text\" id=\"couleur2\" name=\"couleur2\" size=\"40\" value=\"\" /><a href=\"#\" onclick=\"javascript:window.open('index.php?file=Admin&amp;page=menu&amp;nuked_nude=menu&amp;op=code_color&amp;number=trues&amp;color=" . $color . "&amp;balise=true','" . _COLOR . "','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=330,height=330,top=30,left=0');return(false)\" title=\"" . _COLOR . "\">["._COLOR."]</a></td></tr>\n"
		. "<tr><td>" . _Gras . " :</td><td><input type=\"checkbox\" name=\"gras\" size=\"40\"/></td></tr>\n"
		. "<tr><td>" . _Italique . " :</td><td><input type=\"checkbox\" name=\"italique\" size=\"40\" /></td></tr>\n"
		. "<tr><td>" . _Souligner . " :</td><td><input type=\"checkbox\" name=\"souligne\" size=\"40\" /></td></tr>\n"
		. "<tr><td>&nbsp;</td><td><input class=\"button\" type=\"submit\" value=\"Send\" /></td></tr>\n"
		. "<tr><td><b>Les styles enregistrés:</b><br/><br />\n";
		$sql = mysql_query("SELECT id, texte  FROM " . $nuked['prefix'] . "_style ORDER BY id DESC LIMIT 0, 16");
		while (list($id, $texte) = mysql_fetch_array($sql))
		{
			echo "N°".$id.":&nbsp;&nbsp;".$texte." <a href=\"index.php?file=Admin&amp;page=editeur&amp;op=del&amp;id=".$id."\"><img src=\"images/del.gif\"/></a><br />\n";
		}
		echo "</td></tr>\n"
		. "</table></form>\n"
		. "<table style=\"margin-left: 2%;margin-right: auto;text-align: left;\" border=\"0\" cellspacing=\"1\" cellpadding=\"2\"><tr><td colspan=\"2\"><big><b>" . _APERCU . "</b></big></td></tr>\n"
		. "<tr><td><div style=\"width:550px;height:260px;margin:auto;\">\n"
		. "<textarea></textarea>\n"
		. "</div></td></tr>\n"
		. "</table></div></div>\n";
	}
	function editeur()
	{
		global $nuked, $user;
		
		$barre1 = "";
		$barre2 = "";
		$barre3 = "";
		$barre4 = "";
		
		for($nbr = 0; $nbr<sizeof($_REQUEST['ligne1']); $nbr++)
		{
			$barre1 .= $_REQUEST['ligne1'][$nbr];
			if($_REQUEST['ligne1'][$nbr] != "")
			{
				$barre1 .=",";
			}
		}
		$barre1 = substr($barre1, 0,count($barre1)-2);
		
		for($nbr = 0; $nbr<sizeof($_REQUEST['ligne2']); $nbr++)
		{
			$barre2 .= $_REQUEST['ligne2'][$nbr];
			if($_REQUEST['ligne2'][$nbr] != "")
			{
				$barre2 .=",";
			}
		}
		$barre2 = substr($barre2, 0,count($barre2)-2);
		
		for($nbr = 0; $nbr<sizeof($_REQUEST['ligne3']); $nbr++)
		{
			$barre3 .= $_REQUEST['ligne3'][$nbr];
			if($_REQUEST['ligne3'][$nbr] != "")
			{
				$barre3 .=",";
			}
		}
		$barre3 = substr($barre3, 0,count($barre2)-2);
		
		for($nbr = 0; $nbr<sizeof($_REQUEST['ligne4']); $nbr++)
		{
			$barre4 .= $_REQUEST['ligne4'][$nbr];
			if($_REQUEST['ligne4'][$nbr] != "")
			{
				$barre4 .=",";
			}
		}
		$barre4 = substr($barre4, 0,count($barre4)-2);
		
		for($nbr = 0; $nbr<21; $nbr++)
		{
			if($_REQUEST['ligne1'][$nbr] == "|")
			{
				$_REQUEST['ligne1'][$nbr] = "barre";
			}
			$ligne1b .= $_REQUEST['ligne1'][$nbr];
			$ligne1b .=",";
			
		}
		$ligne1b = substr($ligne1b, 0,count($ligne1b)-2);
		
		for($nbr = 0; $nbr<13; $nbr++)
		{
			if($_REQUEST['ligne2'][$nbr] == "|")
			{
				$_REQUEST['ligne2'][$nbr] = "barre";
			}
			$ligne2b .= $_REQUEST['ligne2'][$nbr];
			$ligne2b .=",";
		}
		$ligne2b = substr($ligne2b, 0,count($ligne2b)-2);
		
		for($nbr = 0; $nbr<20; $nbr++)
		{
			if($_REQUEST['ligne3'][$nbr] == "|")
			{
				$_REQUEST['ligne3'][$nbr] = "barre";
			}
			$ligne3b .= $_REQUEST['ligne3'][$nbr];
			$ligne3b .=",";
		}
		$ligne3b = substr($ligne3b, 0,count($ligne3b)-2);
		
		for($nbr = 0; $nbr<8; $nbr++)
		{
			if($_REQUEST['ligne4'][$nbr] == "|")
			{
				$_REQUEST['ligne4'][$nbr] = "barre";
			}
			$ligne4b .= $_REQUEST['ligne4'][$nbr];
			$ligne4b .=",";
		}
		$ligne4b = substr($ligne4b, 0,count($ligne4b)-2);
		
		$upd = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($_REQUEST['couleur']) . "' WHERE name = 'couleur'");
		$upd2 = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($_REQUEST['bouton']) . "' WHERE name = 'bouton'");
		$upd3 = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($_REQUEST['status']) . "' WHERE name = 'status'");
		$upd4 = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($barre1) . "' WHERE name = 'ligne1'");
		$upd5 = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($barre2) . "' WHERE name = 'ligne2'");
		$upd6 = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($barre3) . "' WHERE name = 'ligne3'");
		$upd7 = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($barre4) . "' WHERE name = 'ligne4'");
		$upd8 = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($ligne1b) . "' WHERE name = 'ligne1b'");
		$upd9 = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($ligne2b) . "' WHERE name = 'ligne2b'");
		$upd10 = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($ligne3b) . "' WHERE name = 'ligne3b'");
		$upd11 = mysql_query("UPDATE  ". $nuked['prefix'] ."_editeur  SET value = '" . mysql_real_escape_string($ligne4b) . "' WHERE name = 'ligne4b'");
		
		// Action
		$texteaction = "". _ACTIONCHANGEDIT .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _CHANGEEDIT . "\n"
		. "</div>\n"
		. "</div>\n";
        echo "<script>\n"
			."setTimeout('screen()','3000');\n"
			."function screen() { \n"
			."screenon('index.php?file=Suggest&module=Sections', 'index.php?file=Admin&page=editeur');\n"
			."}\n"
			."</script>\n";
	}
	function style()
	{
		global $nuked, $user;
		
		$impossible = true;
		
		if ($_REQUEST['souligne'] != "")
		{
			$souligne = "textDecoration:'underline'";
			$impossible = false;
		}
		else
		{
			$souligne = "";
		}
		if ($_REQUEST['italique'] != "")
		{
			if ($souligne != "")
			{
				$italique = "fontStyle: 'italic',";
			}
			else
			{
				$italique = "fontStyle:'italic'";
			}
			$impossible = false;
		}
		else
		{
			$italique = "";
		}
		if ($_REQUEST['gras'] != "")
		{
			if ($souligne == "" AND $italique == "")
			{
				$gras = "fontWeight:'bold'";
			}
			else
			{
				$gras = "fontWeight:'bold',";
			}
			$impossible = false;
		}
		else
		{
			$gras = "";
		}
		if ($_REQUEST['taille'] != "rien")
		{
			if ($souligne == "" AND $italique == "" AND $gras == "")
			{
				$taille = "fontSize: '".$_REQUEST['taille']."px'";
			}
			else
			{
				$taille = "fontSize: '".$_REQUEST['taille']."px',";
			}
			$impossible = false;
		}
		else
		{
			$taille ="";
		}
		if ($_REQUEST['couleur'] != "")
		{
			if ($souligne == "" AND $italique == "" AND $gras == "" AND $taille =="")
			{
				$couleur = "color : '".$_REQUEST['couleur']."'";
			}
			else
			{
				$couleur = "color : '".$_REQUEST['couleur']."',";
			}
			$impossible = false;
		}
		else
		{
			$couleur ="";
		}
		if ($_REQUEST['name'] != "")
		{
			$impossible = false;
		}
		if(!$impossible)
		{
			$texte = "{title : '".$_REQUEST['name']."', inline : 'span', styles : {".$couleur."".$taille."".$gras."".$italique."".$souligne."}}";
			
			$upd = mysql_query("INSERT INTO ". $nuked['prefix'] ."_style  (`texte`)  VALUES ('".mysql_real_escape_string($texte)."')");
			
			// Action
			$texteaction = "". _ACTIONADDSTYLE .".";
			$acdate = time();
			$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
			//Fin action
			
		}
	}
    function delete()
	{
		global $nuked, $user;
		
		$sql = mysql_query("DELETE FROM ". $nuked['prefix'] ."_style WHERE id = '" . $_REQUEST['id'] . "'");
		// Action
		$texteaction = "". _ACTIONDELSTYLE .".";
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		echo "<div class=\"notification success png_bg\">\n"
		. "<div>\n"
		. "" . _DELSTYLE . "\n"
		. "</div>\n"
		. "</div>\n";
        echo "<script>\n"
			."setTimeout('screen()','3000');\n"
			."function screen() { \n"
			."screenon('index.php?file=Suggest&module=Sections', 'index.php?file=Admin&page=editeur');\n"
			."}\n"
			."</script>\n";
	}
	switch ($_REQUEST['op'])
    {
        case "main":
		admintop();
        main();
		adminfoot();
        break;
		case "style":
        style();
        break;
		case "editeur":
		admintop();
        editeur();
		adminfoot();
        break;
		case "del":
		admintop();
        delete();
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
