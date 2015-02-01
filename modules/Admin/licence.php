<?php
/**
 * @version     1.7.10
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
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

if ($visiteur >= 2)
{
    admintop();
	echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
		. "<div class=\"content-box-header\"><h3>" . _LICENCES . "</h3></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"width:96%; margin-left:2%;\">\n";
	echo _LICENCETXT;
	echo "</div>\n"
    . "<div style=\"text-align: center;\"><br /><a class=\"buttonLink\" href=\"index.php?file=Admin\">" . _BACK . "</a></div></form><br /></div></div>\n";

    adminfoot();

}
else if ($visiteur > 1)
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a class=\"buttonLink\" href=\"javascript:history.back()\">" . _BACK . "</a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
    adminfoot();
}
else
{
    admintop();
    echo "<div class=\"notification error png_bg\">\n"
	. "<div>\n"
	. "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a class=\"buttonLink\" href=\"javascript:history.back()\">" . _BACK . "</a></div><br /><br />"
	. "</div>\n"
	. "</div>\n";
    adminfoot();
}
?>
