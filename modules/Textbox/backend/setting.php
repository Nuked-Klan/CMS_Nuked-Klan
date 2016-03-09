<?php
/**
 * setting.php
 *
 * Backend of Textbox module - Manage Textbox setting
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Textbox'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'title' => __('ADMIN_SHOUTBOX') .' - '. __('PREFERENCES')
));

if ($GLOBALS['op'] != 'save') {
    nkTemplate_addJS(
        "function deleteAllShoutboxMsg() {\n"
        . "if (confirm('". __('CONFIRM_TO_DELETE_ALL_SHOUTBOX_MESSAGE') ."')){\n"
        . "document.location.href = 'index.php?admin=Textbox&op=deleteAllMsg';}\n"
        . "}\n"
    );
}

// Action handle
switch ($GLOBALS['op']) {
    // Save Textbox setting.
    case 'save' :
        nkAction_save();
        break;

    // Display Textbox setting form.
    default :
        nkAction_edit();
        break;
}

?>