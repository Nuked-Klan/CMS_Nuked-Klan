<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $user, $nuked, $language;
translate("modules/Admin/lang/" . $language . ".lang.php");
include("modules/Admin/design.php");

$visiteur = $user ? $user[1] : 0;

if ($visiteur >= 2)
{
    function main()
    {
        global $user, $nuked, $language;

		echo"<script type=\"text/javascript\">\n"
		."<!--\n"
		."\n"
		. "function delfile()\n"
		. "{\n"
		. "if (confirm('" . _DELETEFILE . " " . _VIDERSQL . " ! " . _CONFIRM . "'))\n"
		. "{document.location.href = 'index.php?file=Admin&page=erreursql&op=delete';}\n"
		. "}\n"
    	. "\n"
		. "// -->\n"
		. "</script>\n";

		echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _ADMINSQLERROR . "</h3>\n"
        . "<div style=\"text-align:right;\"><a href=\"help/" . $language . "/Erreursql.php\" rel=\"modal\">\n"
		. "<img style=\"border: 0;\" src=\"help/help.gif\" alt=\"\" title=\"" . _HELP . "\" /></a>\n"
		. "</div></div>\n"
		. "<div class=\"tab-content\" id=\"tab2\"><div style=\"text-align: center;\"><b><a href=\"javascript:delfile();\">" . _VIDERSQL . "</a></b></div><br />\n";

		echo "<table><tr><td><b>" . _DATE . "</b>\n"
		."</td><td><b>" . _URL . "</b>\n"
		."</td><td><b>" . _INFORMATION . "</b>\n"
		."</td></tr>\n";

		$sql = mysql_query("SELECT date, lien, texte  FROM " . $nuked['prefix'] . "_erreursql ORDER BY date DESC");
		while (list($date, $lien, $texte) = mysql_fetch_array($sql))
		{
			$date = nkDate($date);

			echo "<tr><td>" . $date . "</td>\n"
			. "<td><a href=\"" . $nuked['url'] . $lien . "\">" . $lien . "</a></td>\n"
			. "<td>" . $texte . "</td></tr>\n";
		}
		echo "</table><div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";
	}

	function delete()
	{
		global $user, $nuked, $visiteur;
		
		if ($visiteur == '9')
		$sql3 = mysql_query("DELETE FROM ". $nuked['prefix'] ."_erreursql");
		
		// Action
		$texteaction = _ACTIONVIDERSQL;
		$acdate = time();
		$sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
		//Fin action
		?>
		<div class="notification success png_bg">
			<div>
				<?php echo _SQLERRORDELETED; ?>
			</div>
		</div>
		<?php
		redirect('index.php?file=Admin&page=erreursql', 1);
	}
    switch ($_REQUEST['op'])
    {
        case 'main':
		admintop();
        main();
		adminfoot();
        break;
		case 'delete':
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
