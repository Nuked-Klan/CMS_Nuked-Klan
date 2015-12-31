<?php
/**
 * notification.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

global $user, $visiteur, $nuked, $language;

translate('modules/Admin/lang/'. $language .'.lang.php');


if ($visiteur >= 2) {
    function main() {
        global $user, $nuked;

        $date = time();
        if ($_REQUEST['texte'] != "" AND $_REQUEST['type'] != "" AND $_REQUEST['type'] != "0") {
            $_REQUEST['texte'] = utf8_decode($_REQUEST['texte']);

            saveNotification(stripslashes($_REQUEST['texte']), (int) $_REQUEST['type']);

            echo _THANKSPARTICIPATION;
        }
        else {
            echo _NOTIFICATIONNOTRECEIVED;
        }

        exit();
    }

    function delete() {
        global $nuked, $visiteur, $user;

        if ($visiteur == "9" AND $_REQUEST['id'] != "") {
            nkDB_delete(NOTIFICATIONS_TABLE, 'id = '. (int) $_REQUEST['id']);

            saveUserAction(_ACTIONDELNOT .'.');
        }
    }

    switch ($_REQUEST['op']) {
        case "main":
            main();
            break;
        case "delete":
            delete();
            break;
        default:
            main();
            break;
    }

}
else
{
    echo _NOTIFICATIONNOTRECEIVED;
}

?>