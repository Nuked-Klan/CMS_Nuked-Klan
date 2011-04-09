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
	exit('You can\'t run this file alone.');
}

if (!isset($nk_version)) include("Includes/version.php");

echo "<div id=\"copyleft\" style=\"text-align: center; width: 100%\">\n"
. "<a href=\"http://www.nuked-klan.org\" onclick=\"window.open(this.href); return false;\"><img style=\"border:0;\" src=\"images/nk_powered.gif\" width=\"80\" height=\"15\" alt=\"\" title=\"Powered by Nuked-Klan " . $nk_version . " © 2002, 2008\" /></a>"
. "&nbsp;<a href=\"http://validator.w3.org/check?uri=referer\"><img style=\"border :0;\" src=\"images/w3c_xhtml.gif\" width=\"80\" height=\"15\" alt=\"\" title=\"Valid XHTML 1.0!\" /></a>"
. "&nbsp;<a href=\"http://jigsaw.w3.org/css-validator/check/referer\"><img style=\"border:0;\" src=\"images/w3c_css.gif\" width=\"80\" height=\"15\" alt=\"\" title=\"Valid CSS!\" /></a></div>";

?>