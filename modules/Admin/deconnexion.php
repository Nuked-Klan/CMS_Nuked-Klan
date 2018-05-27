<?php
/**
 * deconnexion.php
 *
 * Backend of Admin module
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Admin', ADMINISTRATOR_ACCESS))
    return;


function main() {
    global $user, $nuked;

    $_SESSION['admin'] = false;

    ?>

        <!-- Page Head -->
        <h2><?php echo _BIENTOT; ?> <?php echo $user[2]; ?></h2>

<?php
    if ($_SESSION['admin'] == false) {
        saveUserAction(_ACTIONDECONNECT);

        printNotification(_OPEREUS, 'success');
        redirect('index.php', 1);
    }
    else {
        printNotification(_OPEECHE, 'error');
        redirect("index.php?file=Admin", 1);
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
