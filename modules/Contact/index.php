<?php
/**
 * index.php
 *
 * Frontend of Contact module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Contact'))
    return;


function index(){
    global $user;

    define('EDITOR_CHECK', 1);

    echo '<script type="text/javascript">
    <!--
    function checkContactForm(){
        if (document.getElementById(\'ns_pseudo\') && document.getElementById(\'ns_pseudo\').value.length == 0){
            alert(\'' . addslashes(_CNONAME) . '\');
            return false;
        }
        if (! isEmail(\'ns_email\')){
            alert(\'' . addslashes(_BADMAIL) . '\');
            return false;
        }
        if (document.getElementById(\'ns_sujet\').value.length == 0){
            alert(\'' . addslashes(_NOSUBJECT) . '\');
            return false;
        }

        return true;
    }
    -->
    </script>';

    $input_user = ($user) ? '<input id="ns_pseudo" type="text" name="nom" value="' . $user[2] . '" style="width: 50%" />' : '';

    echo '<div style="width: 80%; margin: auto">
    <form method="post" action="index.php?file=Contact&amp;op=sendmail" onsubmit="return checkContactForm()">
    <p style="text-align: center; margin-bottom: 20px"><big><b>' . _CONTACT . '</b></big><br /><em>' . _CONTACTFORM . '</em></p>
    <p><label for="ns_pseudo" style="float: left; width: 20%; font-weight: bold">' . _CYNICK . ' : </label>&nbsp;' . $input_user . '</p>
    <p><label for="ns_email" style="float: left; width: 20%; font-weight: bold">' . _YMAIL . ' : </label>&nbsp;<input id="ns_email" type="text" name="mail" value="" style="width: 50%" /></p>
    <p><label for="ns_sujet" style="float: left; width: 20%; font-weight: bold">' . _YSUBJECT . ' : </label>&nbsp;<input id="ns_sujet" type="text" name="sujet" value="" style="width: 50%" /></p>
    <p style="font-weight: bold; margin-top: 10px">' . _CYCOMMENT . ' : <br /><textarea id="e_basic" name="corps" cols="60" rows="12"></textarea></p>';

    // Affichage du Captcha.
    echo '<div style="text-align: center">',"\n";
    if (initCaptcha()) echo create_captcha();
    echo '</div>',"\n";

    echo '<p style="text-align: center; clear: left"><br /><input type="submit" class="bouton" value="' . __('SEND') . '" /></p></form><br /></div>';
}

function sendmail(){
    global $nuked, $user_ip, $user;

    // Verification code captcha
    if (initCaptcha() && ! validCaptchaCode())
        return;

    if (! $user && (! isset($_REQUEST['nom']) || $_REQUEST['nom'] == '' || ctype_space($_REQUEST['nom']))) {
        printNotification(stripslashes(_CNONAME), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    if ($_REQUEST['sujet'] == '' || ctype_space($_REQUEST['sujet'])) {
        printNotification(stripslashes(_NOSUBJECT), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    $_REQUEST['mail'] = stripslashes($_REQUEST['mail']);

    if (($mail = checkEmail($_REQUEST['mail'], false, false)) === false) {
        printNotification(getCheckEmailError($mail), 'error', array('backLinkUrl' => 'javascript:history.back()'));
        return;
    }

    $time = time();
    $date = nkDate($time);
    $contact_flood = $nuked['contact_flood'] * 60;
    $escapeUserIp = nkDB_realEscapeString($user_ip);

    $sql = nkDB_execute("SELECT date FROM " . CONTACT_TABLE . " WHERE ip = '" . $escapeUserIp . "' ORDER BY date DESC LIMIT 0, 1");
    $count = nkDB_numRows($sql);
    list($flood_date) = nkDB_fetchArray($sql);
    $anti_flood = $flood_date + $contact_flood;

    if ($count > 0 && $time < $anti_flood){
        printNotification(_FLOODCMAIL, 'error');// TODO : Backlink ?
        redirect("index.php", 3);
    }
    else{
        $nom = trim(stripslashes($_REQUEST['nom']));
        $sujet = trim(stripslashes($_REQUEST['sujet']));
        $corps = stripslashes($_REQUEST['corps']);

        if($user) $nom = $user[2];

        $subjet = $sujet . ", " . $date;
        $corp = $corps . "<p><em>IP : " . $user_ip . "</em><br />" . $nuked['name'] . " - " . $nuked['slogan'] . "</p>";
        $from = "From: " . $nom . " <" . $mail . ">\r\nReply-To: " . $mail . "\r\n";
        $from .= "Content-Type: text/html\r\n\r\n";

        if ($nuked['contact_mail'] != "") $email = $nuked['contact_mail'];
        else $email = $nuked['mail'];
        $corp = secu_html(nkHtmlEntityDecode($corp));

        mail($email, $subjet, $corp, $from);

        $nom = nkHtmlEntities($nom, ENT_QUOTES);
        $email = nkHtmlEntities($mail, ENT_QUOTES);
        $subject = nkHtmlEntities($sujet, ENT_QUOTES);
        $text = secu_html(nkHtmlEntityDecode($corps, ENT_QUOTES));

        $nom     = nkDB_realEscapeString($nom);
        $email   = nkDB_realEscapeString($email);
        $subject = nkDB_realEscapeString($subject);
        $text    = nkDB_realEscapeString($text);

        nkDB_execute(
            "INSERT INTO ". CONTACT_TABLE ."
            (`titre`, `message`, `email`, `nom`, `ip`, `date`)
            VALUES
            ('". $subject ."', '". $text ."', '". $email ."', '". $nom ."', '". $escapeUserIp ."' , '". $time ."')"
        );

        saveNotification(_NOTCON .': [<a href="index.php?file=Contact&page=admin">'. _TLINK .'</a>].');

        printNotification(_SENDCMAIL, 'success');
        redirect("index.php", 3);
    }
}

opentable();

switch($GLOBALS['op']){
    case 'sendmail':
    sendmail();
    break;

    case 'index':
    index();
    break;

    default:
    index();
    break;
}

closetable();

?>
