<?php 
/**
 * theme.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', SUPER_ADMINISTRATOR_ACCESS))
    return;



function main() {
    global $user, $nuked;

    if(file_exists("themes/".$nuked['theme']."/admin.php")) {

        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _GESTEMPLATE . "</h3>\n"
        . "</div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        require 'themes/'. $nuked['theme'] .'/colors.php';
        include("themes/".$nuked['theme']."/admin.php");
        echo "</div>";
    }
    else
    {
        echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
        . "<div class=\"content-box-header\"><h3>" . _GESTEMPLATE . "</h3>\n"
        . "</div>\n"
        . "<div class=\"tab-content\" id=\"tab2\">\n";

        printNotification(_NOADMININTERNE, 'error');
    ?>
        </div>
    <?php
    }
}


switch ($GLOBALS['op']) {
    case "main":
        main();
        break;
    default:
        main();
        break;
}

?>