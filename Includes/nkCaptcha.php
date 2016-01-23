<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//

defined('INDEX_CHECK') or die('You can\'t run this file alone.');

//réglage captcha (auto | on | off)
define('_NKCAPTCHA', 'auto');

/**
* Check if the code is the good captcha code
* @param void
* @return bool
**/
function ValidCaptchaCode() {
    $message = null;

    // Check valid token code
    if (! isset($_REQUEST['ct_token'])) {
        $message = _CTNOTOKEN;
    }
    else if($_REQUEST['ct_token'] != $_SESSION['CT_TOKEN']) {
        $message = _CTBADTOKEN;
    }
    else {
        // If is valid token we delete it for no re-use
        unset($_SESSION['CT_TOKEN']);

        // Check valid ct_script field edited via JS
        if (! isset($_REQUEST['ct_script']) || $_REQUEST['ct_script'] != 'klan')
            $message = _CTBADJS;

        // Check no-data in ct_email field
        if (isset($_REQUEST['ct_email']) && $_REQUEST['ct_email'] != '')
            $message = _CTBADFIELD;
    }

    if ($message != null) {
        captchaNotification($message);
        return false;
    }

    return true;
}

/**
 * Create hidden input using captcha system.
 */
function create_captcha() {
    // Save token in session
    if (! array_key_exists('CT_TOKEN', $_SESSION) || empty($_SESSION['CT_TOKEN'])) {
        // Generate token code
        $token = md5(uniqid(microtime(), true));
        $_SESSION['CT_TOKEN'] = $token;
    }
    else {
        $token = $_SESSION['CT_TOKEN'];
    }

    $contentCaptcha = '<input type="hidden" name="ct_token" value="'.$token.'" />
                       <input type="hidden" class="ct_script" name="ct_script" value="nuked" />
                       <input type="hidden" name="ct_email" value="" />';

    static $js = false;

    if ($js === false) {
        $js = true;
        $contentCaptcha .= '<script type="text/javascript">
                               if (typeof jQuery == \'undefined\') {
                                   document.write(\'\x3Cscript type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js">\x3C/script>\');
                               }
                           </script>
                           <script type="text/javascript" src="media/js/captcha.js"></script>';
    }

    return $contentCaptcha;
}

/**
 * Display notification if captcha failed
 * @param $data : message to display
 */
function captchaNotification($data) {
    if (isset($_REQUEST['ajax']))
        $id = ' id="ajax_message"';
    else
        $id = '';

    echo '<br /><br /><div', $id, ' style="text-align: center;">', $data
        , '<br /><br /><a href="javascript:history.back()">[ <b>', _BACK, '</b> ]</a></div><br /><br />';

    if ($data != _CTNOTOKEN && ! isset($_REQUEST['ajax']))
        redirect('index.php?file=User&op=login_screen', 2);

    closetable();
    footer();
    exit();
}

?>
