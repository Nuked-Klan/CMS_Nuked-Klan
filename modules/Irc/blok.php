<?php
/**
 * blok.php
 *
 * Display block of Irc module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or exit('You can\'t run this file alone.');

global $language;

translate('modules/Irc/lang/'. $language .'.lang.php');
nkTemplate_addCSSFile('modules/Irc/Irc.css');

// Get last Irc awards
$dbrIrcAwards = nkDB_selectOne(
    'SELECT text
    FROM '. IRC_AWARDS_TABLE,
    array('id'), 'DESC', 1
);

// Display block
echo applyTemplate('modules/Irc/block', array(
    'ircAward' => $dbrIrcAwards['text']
));

?>
