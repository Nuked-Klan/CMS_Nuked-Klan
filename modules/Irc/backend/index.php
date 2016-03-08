<?php
/**
 * index.php
 *
 * Backend of Irc module - Irc awards management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Irc'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'dataName'              => 'award',
    'tableName'             => IRC_AWARDS_TABLE,
    //'titleField_dbTable'    => 'nom',
    'previewUrl'            => 'index.php?file=Irc&op=awards'
));


/* Irc awards list function */

/**
 * Callback function for nkList.
 * Format Irc award row.
 *
 * @param array $row : The Irc award row.
 * @param int $nbData : The list count.
 * @param int $r : The number of row.
 * @param array $functionData : The external data of list passed to this function.
 * @return array : The Irc award row formated.
 */
function formatAwardRow($row, $nbData, $r, $functionData) {
    $row['date'] = nkDate($row['date']);
    $row['text'] = strip_tags($row['text']);

    if (mb_strlen($row['text']) > 50) {
        $row['text'] = mb_substr($row['text'], 0, 50) .'...';
        $row['text'] = nkHtmlEntities($row['text']);
    }

    return $row;
}

/* Irc award save form function */

/**
 * Callback function for nkAction_save.
 * Prepare data to save Irc award.
 *
 * @param int $id : The Team member id.
 * @param array $map : The valid Irc award data.
 * @return void
 */
function preSaveAwardData($id, &$data) {
    if ($id === null)
        $data['date'] = time();
}


// Action handle
switch ($GLOBALS['op']) {
    case 'edit' :
        // Display Irc awards form for addition / editing.
        nkAction_edit();
        break;

    case 'save' :
        // Save / modify Irc award.
        nkAction_save();
        break;

    case 'delete' :
        // Delete Irc award.
        nkAction_delete();
        break;

    default:
        // Display Irc award list.
        nkAction_list();
        break;
}

?>