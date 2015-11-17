<?php
/**
 * maj.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

include 'modules/Admin/design.php';

if (! adminInit('Admin', SUPER_ADMINISTRATOR_ACCESS))
    return;


admintop();

echo "<div class=\"content-box\">\n" //<!-- Start Content Box -->
    . "<div class=\"content-box-header\"><h3>" . _MAJ . "</h3></div>\n"
. "<div class=\"tab-content\" id=\"tab2\"><div style=\"width:90%; margin-left:5%;\">\n";
?>
<br /><br /><div class="notification information png_bg">

            <div>
<?php echo _MAJEXPLI; ?>:<br /><br />
<a href="index.php?file=Admin&amp;page=modules"><?php echo _MAJMAIN; ?></a>
            </div>
</div>


<?php
echo "</div>\n"
. "<div style=\"text-align: center;\"><br />[ <a href=\"index.php?file=Admin\"><b>" . _BACK . "</b></a> ]</div></form><br /></div></div>\n";

adminfoot();

?>