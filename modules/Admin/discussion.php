<?php
/**
 * discussion.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', ADMINISTRATOR_ACCESS))
    return;


function main() {
    global $user;

    if ($_REQUEST['texte'] != '') {
        $_REQUEST['texte'] = utf8_decode($_REQUEST['texte']);
        $_REQUEST['texte'] = nkHtmlEntities($_REQUEST['texte']);
        $_REQUEST['texte'] = stripslashes($_REQUEST['texte']);

        nkDB_update(DISCUSSION_TABLE, array(
            'date'      => time(),
            'authorId'  => $user['id'],
            'author'    => $user['name'],
            'texte'     => $_REQUEST['texte']
        ));
    }
}

switch ($GLOBALS['op']) {
    case "main":
    main();
    break;
    default:
    main();
    break;
}

?>