<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
if (!defined("INDEX_CHECK")) die ('<div style="text-align: center;">You cannot open this page directly</div>');

global $nuked, $language;
translate("modules/Irc/lang/" . $language . ".lang.php");

$sql = mysql_query("SELECT date, text FROM " . IRC_AWARDS_TABLE . " ORDER BY id DESC LIMIT 0, 1");
list($date, $txt) = mysql_fetch_array($sql);
$date = nkDate($date);

echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
		. "<tr><td align=\"left\">" . $txt . "</td></tr>\n"
		. "<tr><td align=\"center\"><hr style=\"width: 60%;height: 1px;\" />" . _JOINUS . " : <a href=\"irc://irc." . $nuked['irc_serv'] . "/" . $nuked['irc_chan'] . "\">#" . $nuked['irc_chan'] . "</a></td></tr>\n"
		. "<tr><td align=\"center\"><a href=\"index.php?file=Irc&amp;op=awards\">" . _MOREAWARDS . "</a></td></tr></table>\n";

?>