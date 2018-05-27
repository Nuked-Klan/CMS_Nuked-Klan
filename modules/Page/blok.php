<?php
/**
 * blok.php
 *
 * Display block of Page module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or exit('You can\'t run this file alone.');

global $nuked;


$i = 0;
$sql = nkDB_execute("SELECT titre FROM " . PAGE_TABLE . " ORDER BY titre");
while (list($titre) = nkDB_fetchArray($sql))
{
    $titre = stripslashes($titre);
    $titre = htmlspecialchars($titre);
    $i++;

    echo "<div><b>" . $i . " . <a href=\"index.php?file=Page&amp;name=" . $titre . "\">" . $titre . "</a></b></div>\n";
}


?>
