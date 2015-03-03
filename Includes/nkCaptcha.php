<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

//réglage captcha (auto | on | off)
define("_NKCAPTCHA","auto");

function ValidCaptchaCode($code = null){
    $message = null;
    // Check valid token code
    if(!isset($_REQUEST['ct_token'])){
        $message = _CTNOTOKEN;
        nkNotification($message);
        login_screen();
        exit();
    }
    else if($_REQUEST['ct_token'] != $_SESSION['CT_TOKEN']){
        $message = _CTBADTOKEN;
    }
    else{
        // If is valid token we delete it for no re-use
        unset($_SESSION['CT_TOKEN']);
    }

    // Check valid ct_script field edited via JS
    if((!isset($_REQUEST['ct_script']) || $_REQUEST['ct_script'] != 'klan') && $message == null){
        $message = _CTBADJS;
    }

    // Check no-data in ct_email field
    if(isset($_REQUEST['ct_email']) && $_REQUEST['ct_email'] != ''){
        $message = _CTBADFIELD;
    }

    if($message != null){
        nkNotification($message, 'index.php?file=User&op=login_screen', 2);
        exit();
    }

    return true;
}

function create_captcha(){
    // Save token in session
    if(!array_key_exists('CT_TOKEN', $_SESSION) || empty($_SESSION['CT_TOKEN'])){
        // Generate token code
        $token = md5(uniqid(microtime(), true));
        $_SESSION['CT_TOKEN'] = $token;
    }
    else{
        $token = $_SESSION['CT_TOKEN'];
    }

    $contentCaptcha = ' <input type="hidden" name="ct_token" value="'.$token.'" />
                        <input type="hidden" id="ct_script" name="ct_script" value="nuked" />
                        <input type="hidden" name="ct_email" value="" />
                        <script type="text/javascript">
                                    if(typeof jQuery == \'undefined\'){
                                        document.write(\'\x3Cscript type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js">\x3C/script>\');
                                    }
                                </script>
                        <script type="text/javascript" src="media/js/captcha.js"></script>';

    return $contentCaptcha;
}