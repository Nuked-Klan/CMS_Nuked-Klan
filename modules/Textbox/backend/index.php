<?php
/**
 * index.php
 *
 * Backend of Shoutbox module - Manage Shoutbox messages
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Textbox'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'dataName'              => 'shoutboxMessage',
    'tableName'             => TEXTBOX_TABLE,
    'onlyEdit'              => true
));


/**
 * Callback function for nkAction_list & nkAction_edit functions.
 * Return page title of current action.
 *
 * @param void
 * @return string : The Shoutbox title for list or edit form.
 */
function getShoutboxMessageTitle($void = null) {
    global $op;

    if ($op == 'edit')
        return __('ADMIN_SHOUTBOX') .' - '. __('EDIT_THIS_SHOUTBOX_MESSAGE');

    return __('ADMIN_SHOUTBOX');
}

/* Shoutbox messages list function */

/**
 * Callback function for nkList.
 * Format Shoutbox message row.
 *
 * @param array $row : The Shoutbox message row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Shoutbox message row formated.
 */
function formatShoutboxMessageRow($row, $nbData, $r, $functionData) {
    $row['date'] = nkDate($row['date']);

    return $row;
}

/* Shoutbox message edit form function */

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Shoutbox message.
 *
 * @param array $form : The Shoutbox message form configuration.
 * @param array $forumCategory : The Shoutbox message data.
 * @param int $id : The Shoutbox message id.
 * @return array : The Shoutbox message form configuration prepared.
 */
function prepareFormForEditShoutboxMessage(&$form, $shoutboxMessage, $id) {
    $form['items']['nickname']['html'] = $shoutboxMessage['auteur'] .' ( '. $shoutboxMessage['ip'] .' )';
}


function deleteAllShoutboxMessage() {
    nkDb_delete(TEXTBOX_TABLE);

    saveUserAction(__('ACTION_DELETE_ALL_SHOUTBOX_MESSAGE'));

    printNotification(__('ALL_SHOUTBOX_MESSAGE_DELETED'), 'success');

    redirect('index.php?admin=Textbox', 2);
}


if (in_array($GLOBALS['op'], array('index', 'edit'))) {
    nkTemplate_addJS(
        "function deleteAllShoutboxMsg() {\n"
        . "if (confirm('". __('CONFIRM_TO_DELETE_ALL_SHOUTBOX_MESSAGE') ."')){\n"
        . "document.location.href = 'index.php?admin=Textbox&op=deleteAllMsg';}\n"
        . "}\n"
    );
}

// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Shoutbox message form for editing.
        nkAction_edit();
        break;

    case 'save' :
        // Modify Shoutbox message.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Shoutbox message.
        nkAction_delete();
        break;

    case 'deleteAllMsg' :
        deleteAllShoutboxMessage();
        break;

    default :
        // Display Soutbox messages list.
        nkAction_list();
        break;
}

?>
