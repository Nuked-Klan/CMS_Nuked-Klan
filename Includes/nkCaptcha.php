<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//

if (!defined('INDEX_CHECK')) exit('You can\'t run this file alone.');


//réglage captcha (auto | on | off)
define('_NKCAPTCHA','auto');
// Définit la largeur du captcha / Set the captcha width
define('_CT_WIDTH', 150);
// Définit la hauteur du captcha / Set the captcha hauteur
define('_CT_HEIGHT', 50);
// Définit les couleur du fond du captcha / Set the captcha background color
// Syntaxe OBLIGATOIRE #XXXXXX / MUST BE #XXXXXX
define('_CT_BGCOLOR', '#333333');
// Définit les couleur du texte du captcha / Set the captcha text color
// Syntaxe OBLIGATOIRE #XXXXXX / MUST BE #XXXXXX
define('_CT_TEXTCOLOR', '#FFFFFF');
// Définit les couleur des lignes du captcha / Set the captcha lines color
// Syntaxe OBLIGATOIRE #XXXXXX / MUST BE #XXXXXX
define('_CT_LINECOLOR', '#bbbbbb');
// Définit les couleur des lignes du captcha / Set the captcha lines color
// Syntaxe OBLIGATOIRE #XXXXXX / MUST BE #XXXXXX
define('_CT_NOISECOLOR', '#FFFFFF');
// Définit la police du captcha / Set the captcha font
define('_CT_TTF_FILE', 'Includes/font/AHGBold.ttf');

// ----------------------------------
// NE RIEN MODIFIER APRES CETTE LIGNE
// DON'T EDIT AFTER THIS LINE
// ----------------------------------

// Include hash librairy to crypt captcha code
require_once ('conf.inc.php');
require_once (dirname(__FILE__) . '/hash.php');

/**
* Generate captcha code and save it in session
* @return string(5) captchaCode
**/
function captchaGenerator(){
    $code = '';
    $charset = 'ABCDEFGHKLMNPRSTUVWYZabcdefghklmnprstuvwyz23456789';

    if (function_exists('mb_strlen')) {
        for($i = 1, $cslen = mb_strlen($charset); $i <= 5; ++$i) {
            $code .= mb_substr($charset, mt_rand(0, $cslen - 1), 1, 'UTF-8');
        }
    } else {
        for($i = 1, $cslen = strlen($charset); $i <= 5; ++$i) {
            $code .= substr($charset, mt_rand(0, $cslen - 1), 1);
        }
    }

    saveCaptcha($code);

    return $code;
}

/**
* Save captcha code in session array
**/
function saveCaptcha($code){
    // Crypt captcha code before save it
    $code = nk_hash(strtolower($code));

    $_SESSION['CT_CODE'] = $code;
}

function validCaptchaCode($code){
    if(!isset($_SESSION['CT_CODE'])){
        $captchaSaved = '#0';
    }
    else{
        $captchaSaved = $_SESSION['CT_CODE'];
    }

    unset($_SESSION['CT_CODE']);

    if(Check_Hash(strtolower($code), $captchaSaved)){
        return true;
    }

    return false;
}

function createCaptcha($style){

    if(@extension_loaded('gd')){
        $code = '';
    }
    else{
        $code = captchaGenerator();
    }

    if($style == 1){
        ?>
        <tr>
            <td style="padding-top:10px;">
                <b><?php echo _SECURITYCODE; ?> :</b>
        <?php
            if (@extension_loaded('gd')) echo '&nbsp;<img src="captcha.php" alt="" title="'._SECURITYCODE.'" />';
            else echo '&nbsp;<big><i>'.$code.'</i></big>';
        ?>
            </td>
        </tr>
        <tr>
            <td style="padding-bottom:10px;">
                <b><?php echo _TYPESECCODE; ?> :</b>&nbsp;<input type="text" id="code" name="code_confirm" size="6" maxlength="5" />
            </td>
        </tr>
        <?php
    }
    else if($style == 2){
        ?>
        <tr>
            <td colspan="2" style="padding-top:10px;">
                <b><?php echo _SECURITYCODE; ?> :</b>
        <?php
            if (@extension_loaded('gd')) echo '&nbsp;<img src="captcha.php" alt="" title="'._SECURITYCODE.'" />';
            else echo '&nbsp;<big><i>'.$code.'</i></big>';
        ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom:10px;">
                <b><?php echo _TYPESECCODE; ?> :</b>&nbsp;<input type="text" id="code" name="code_confirm" size="6" maxlength="5" />
            </td>
        </tr>
        <?php
    }
    else{
        ?>
        <div style="margin:15px 0;">
        <?php
            if (@extension_loaded('gd')) echo '<img src="captcha.php" alt="" title="'._SECURITYCODE.'" />';
            else echo '&nbsp;<big><i>'.$code.'</i></big>';
        ?>
            <b><?php echo _TYPESECCODE; ?> :</b>&nbsp;<input type="text" id="code" name="code_confirm" size="6" maxlength="5" />
         </div>
        <?php
    }
}

/**
* Create a rgba value from a hex color
* @return array rgbValue
**/
function colorRGBOutput($color){
    // check if $color is a valid hex color
    if(!preg_match('#\#[a-fA-F0-9]{6}#', $color)){
        return array(0, 0, 0);
    }

    // Remove # character
    $color = substr($color, 1);

    // Convert hex value in numeric value for each colors
    $red = hexdec(substr($color,0,2));
    $green = hexdec(substr($color,2,2));
    $blue = hexdec(substr($color,4,2));

    return array($red, $green, $blue);
}

?>
