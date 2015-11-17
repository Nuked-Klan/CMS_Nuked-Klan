<?php
/**
 * blok.php
 *
 * Display block of Irc module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or exit('You can\'t run this file alone.');

global $nuked, $language;

translate('modules/Irc/lang/'. $language .'.lang.php');


$sql = mysql_query("SELECT date, text FROM " . IRC_AWARDS_TABLE . " ORDER BY id DESC LIMIT 0, 1");
list($date, $txt) = mysql_fetch_array($sql);
$date = nkDate($date);

echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\n"
		. "<tr><td align=\"left\">" . $txt . "</td></tr>\n"
		. "<tr><td align=\"center\"><hr style=\"width: 60%;height: 1px;\" />" . _JOINUS . " : <a href=\"irc://irc." . $nuked['irc_serv'] . "/" . $nuked['irc_chan'] . "\">#" . $nuked['irc_chan'] . "</a></td></tr>\n"
		. "<tr><td align=\"center\"><a href=\"index.php?file=Irc&amp;op=awards\">" . _MOREAWARDS . "</a></td></tr></table>\n";

?>