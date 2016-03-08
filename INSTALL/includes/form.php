<?php
/**
 * form.php
 *
 * Common form function
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */

/*
 * Display selected attribute of input
 */
function selected($str1, $str2) {
    if ($str1 == $str2)
        echo ' selected="selected"';
}

/*
 * Display checked attribute of input
 */
function checked($str1, $str2 = true) {
    if ($str1 == $str2)
        echo ' checked="checked"';
}

/*
 * Display disabled attribute of input
 */
function disabled($str1, $str2) {
    if ($str1 == $str2)
        echo ' disabled="disabled"';
}

?>