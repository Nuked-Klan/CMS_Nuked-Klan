<?php
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

global $nuked;

$i = 0;
$sql = mysql_query("SELECT titre FROM " . PAGE_TABLE . " ORDER BY titre");
while (list($titre) = mysql_fetch_array($sql))
{
    $titre = stripslashes($titre);
    $titre = htmlspecialchars($titre);
    $i++;

    echo "<div><b>" . $i . " . <a href=\"index.php?file=Page&amp;name=" . $titre . "\">" . $titre . "</a></b></div>\n";
} 


?>
