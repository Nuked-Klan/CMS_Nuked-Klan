<?php
/**
 * setting.php
 *
 * Backend of Forum module - Manage Forum setting
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Forum'))
    return;

define('CURRENT_SETTING_TITLE', __('ADMIN_FORUM') .' - '. __('PREFERENCES'));

require_once 'Includes/nkAction.php';


// Action handle
switch ($GLOBALS['op']) {
    // Save Forum setting.
    case 'save' :
        nkAction_save();
        break;

    // Display Forum setting form.
    default :
        nkAction_edit();
        break;
}

?>