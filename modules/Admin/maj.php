<?php
/**
 * maj.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', SUPER_ADMINISTRATOR_ACCESS))
    return;


echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _MAJ . "</h3></div>\n"
    . "<div class=\"tab-content\" id=\"tab2\"><div style=\"width:90%; margin-left:5%;\">\n"
    . "<br /><br />\n";

printNotification(_MAJEXPLI, 'information', array(
    'linkTxt' => _MAJMAIN,
    'linkUrl' => 'index.php?file=Admin&amp;page=modules'
));

echo "</div>\n"
    . "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . __('BACK') . "</b></a> ]</div></form><br /></div></div>\n";

?>
