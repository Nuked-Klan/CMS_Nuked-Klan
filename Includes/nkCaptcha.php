<?php
/**
 * Captcha management in Nuked-klan
 *
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

//r�glage captcha (auto | on | off)
define("_NKCAPTCHA","auto");


function ValidCaptchaCode($code = null){
    $message = null;
    // Check valid token code
    if(!isset($_REQUEST['ct_token'])){
        $message = _CTNOTOKEN;
    }
    else if($_REQUEST['ct_token'] != $_SESSION['CT_TOKEN']){
        $message = _CTBADTOKEN;
    }
    else{
        // If is valid token we delete it for no re-use
        unset($_SESSION['CT_TOKEN']);
    }

    // Check valid ct_script field edited via JS
    if(!isset($_REQUEST['ct_script']) || $_REQUEST['ct_script'] != 'klan'){
        $message = _CTBADJS;
    }

    // Check no-data in ct_email field
    if(isset($_REQUEST['ct_email']) && $_REQUEST['ct_email'] != ''){
        $message = _CTBADFIELD;
    }

    if($message != null){
        ?>
        <div style="text-align:center;margin:15px 0;">
            <?php echo $message; ?>
        </div>
        <div style="text-align:center;margin:15px 0;">
            <a href="javascript:history.back();"><?php echo _BACK; ?></a>
        </div>
        <?php
        closetable();
        footer();
        exit();

    }

    return true;
}

function create_captcha($style){

    // Generate token code
    $token = md5(uniqid(microtime(), true));

    // Save token in session
    $_SESSION['CT_TOKEN'] = $token;

    if($style == 1 || $style == 2){
    ?>
        <tr>
            <td>
                <input type="hidden" name="ct_token" value="<?php echo $token; ?>" />
                <input type="hidden" id="ct_script" name="ct_script" value="nuked" />
                <input type="hidden" name="ct_email" value="" />
            </td>
        </tr>
    <?php
    }
    else{
    ?>
        <input type="hidden" name="ct_token" value="<?php echo $token; ?>" />
        <input type="hidden" id="ct_script" name="ct_script" value="nuked" />
        <input type="hidden" name="ct_email" value="" />
    <?php
    }

    if(!array_key_exists('captchaJS', $GLOBALS['nuked']) || empty($GLOBALS['nuked']['captchaJS'])){
        echo '<script type="text/javascript" src="media/js/captcha.js"></script>';
        $GLOBALS['nuked']['captchaJS'] = 1;
    }
}

?>
