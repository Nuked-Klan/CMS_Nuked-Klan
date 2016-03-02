<?php 
/**
 * index.php
 *
 * Frontend of Irc module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Irc'))
    return;

compteur('Irc');


/**
 * Display Irc awards list.
 *
 * @param void
 * @return void
 */
function displayIrcAwards() {
    $dbrIrcAwards = nkDB_selectMany(
        'SELECT date, text
        FROM '. IRC_AWARDS_TABLE,
        array('id'), 'DESC'
    );

    $nbIrcAwards = nkDB_numRows();

    echo applyTemplate('modules/Irc/awards', array(
        'ircAwardsList' => $dbrIrcAwards,
        'nbIrcAwards'   => $nbIrcAwards
    ));
}

opentable();

// Action handle
switch ($GLOBALS['op']) {
    case 'awards' :
        displayIrcAwards();
        break;

    default :
        // Display main Irc page
        echo applyTemplate('modules/Irc/main');
        break;
}

closetable();

?>