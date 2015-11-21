<?php
/**
 * deconnexion.php
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


function main()
{
    global $user, $nuked;
    
    $_SESSION['admin'] = false;
    
    ?>
    
        <!-- Page Head -->
        <h2><?php echo _BIENTOT; ?> <?php echo $user[2]; ?></h2>
        
        <?php 
        if ($_SESSION['admin'] == false)
        {
        // Action
        $texteaction = _ACTIONDECONNECT;
        $acdate = time();
        $sqlaction = mysql_query("INSERT INTO ". $nuked['prefix'] ."_action  (`date`, `pseudo`, `action`)  VALUES ('".$acdate."', '".$user[0]."', '".$texteaction."')");
        //Fin action
        ?>
        <div class="notification success png_bg">
            <div>
                <?php echo _OPEREUS; ?>
            </div>
        </div>
        <?php
        redirect("index.php", 1);
        }
        else
        {
        ?>
        <div class="notification error png_bg">
            <div>
                <?php echo _OPEECHE; ?>
            </div>
        </div>
            <?php
    redirect("index.php?file=Admin", 1);
        }
}


switch ($_REQUEST['op']) {
    case "main":
        main();
        break;
    default:
        main();
        break;
}

?>