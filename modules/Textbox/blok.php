<?php
/**
 * blok.php
 *
 * Display block of Textbox module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or exit('You can\'t run this file alone.');

global $language;

translate('modules/Textbox/lang/'. $language .'.lang.php');

include 'modules/Textbox/config.php';


if ($active == 3 || $active == 4) {
    $width  = $mbox_width;
    $height = $mbox_height;
}
else {
    $width  = $box_width;
    $height = $box_height;
}

echo applyTemplate('modules/Textbox/block', array(
    'captcha' => initCaptcha(),
    'width'   => $width,
    'height'  => $height,
    'active'  => $active
));

?>
