<?php
/**
 * @version     1.7.10
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die;

global $user, $language;
translate('modules/Admin/lang/' . $language . '.lang.php');
include('modules/Admin/design.php');

$visiteur = $user ? $user[1] : 0;

admintop();

if ($visiteur >= 2) {
    ?>
    <div class="content-box"><!-- Start Content Box -->
        <div class="content-box-header"><h3><?php echo _PROPOS; ?></h3></div>
        <div class="tab-content" id="tab2">
            <div style="margin:20px">
                <?php echo _INFOSPROPOS; ?>
            </div>
            <div style="text-align: center"><br /><a class="buttonLink" href="index.php?file=Admin"><?php echo _BACK; ?></a><br /><br /><br /></div>
        </div>
    </div>
    <?php
}
else if ($visiteur > 1) {
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _NOENTRANCE . "<br /><br /><a class=\"buttonLink\" href=\"javascript:history.back()\">" . _BACK . "</a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
}
else {
    echo "<div class=\"notification error png_bg\">\n"
    . "<div>\n"
    . "<br /><br /><div style=\"text-align: center;\">" . _ZONEADMIN . "<br /><br /><a class=\"buttonLink\" href=\"javascript:history.back()\">" . _BACK . "</a></div><br /><br />"
    . "</div>\n"
    . "</div>\n";
}
?>