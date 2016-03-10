<?php
/**
 * index.php
 *
 * Frontend of Suggest module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Suggest'))
    return;


function index(){
    global $nuked, $visiteur;

    opentable();
    $autorized_modules = array();
    $handle = opendir('modules/Suggest/modules/');

    while ($mod = readdir($handle)){
        if ($mod != '.' && $mod != '..' && $mod != 'index.html'){
                $mod = str_replace('.php', '', $mod);
            $autorized_modules[] = $mod;
        }
    }
    // Securite par phpSecure.info
    if (isset($_REQUEST['module']) && is_file('modules/Suggest/modules/' . $_REQUEST['module'] . '.php')){
        if (false===array_search($_REQUEST['module'], $autorized_modules) || preg_match('`\.\.`', $_REQUEST['module'])){
            printNotification('What are you trying to do ?', 'error');
            return;
        }

        $_REQUEST['module'] = trim($_REQUEST['module']);
        // Fin

        $niveau = nivo_mod($_REQUEST['module']);

        if ($visiteur >= $niveau){
            define('EDITOR_CHECK', 1);
            include('modules/Suggest/modules/' . $_REQUEST['module'] . '.php');
            form(0, 0);
        }
        else if ($niveau == -1){
            echo applyTemplate('nkAlert/moduleOff');
        }
        else if ($niveau == 1 && $visiteur == 0){
            echo applyTemplate('nkAlert/userEntrance');
        }
        else{
            echo applyTemplate('nkAlert/noEntrance');
        }
    }
    else{
        echo '<br /><div style="text-align: center"><big><b>' . _SUGGEST . '</b></big></div><br />',"\n"
                . '<form method="post" action="index.php?file=Suggest">',"\n"
                . '<table style="margin: auto;text-align: left" width="90%">',"\n"
                . '<tr><td align="center">' . _SELECTMOD . ' : ',"\n"
                . '<select name="module" onchange="submit();"><option value="">-----------</option>',"\n";

        $modules = array();
        $path = 'modules/Suggest/modules/';
        $handle = opendir($path);
        while ($mod = readdir($handle)){
            if ($mod != '.' && $mod != '..' && $mod != 'index.html'){
                $mod = str_replace('.php', '', $mod);

                if ($mod == 'Gallery') $modname = _NAVGALLERY;
                else if ($mod == 'Download') $modname = _NAVDOWNLOAD;
                else if ($mod == 'Links') $modname = _NAVLINKS;
                else if ($mod == 'News') $modname = _NAVNEWS;
                else if ($mod == 'Sections') $modname = _NAVART;
                else $modname = $mod;

                array_push($modules, $modname . '|' . $mod);
            }
        }
        closedir($handle);
        natcasesort($modules);
        foreach($modules as $value){
            $temp = explode('|', $value);
            $niveau = nivo_mod($temp[0]);

            if ($visiteur >= $niveau){
                echo '<option value="' . $temp[1] . '">' . $temp[0] . '</option>',"\n";
            }
            }

        echo '</select></td></tr><tr><td>&nbsp;</td></tr></table></form>';
    }
    closetable();
}

function add_sug($data){
    global $user, $nuked, $user_ip;

    opentable();

    if (preg_match('#\.\.#', $_REQUEST['module']) || preg_match('#\\\#', $_REQUEST['module'])){
        printNotification('What are you trying to do ?', 'error');
        return;
    }
    else{
        include('modules/Suggest/modules/' . $_REQUEST['module'] . '.php');
    }

    if (($content = make_array($data)) === false)
        return;

    $content = mysql_real_escape_string(stripslashes($content));

    if(strlen($content) <= 30){
        printNotification(_NOCONTENT, 'error');
        closetable();
        return;
    }
    // Captcha check
    if (initCaptcha() && ! validCaptchaCode())
        return;

    $date = time();

    if ($user){
        $author = $user[0];
    }
    else{
        $author = $user_ip;
    }

    $sql = mysql_query("INSERT INTO " . SUGGEST_TABLE . " ( `id` , `module` , `user_id` , `proposition` , `date` ) VALUES ( '' , '" . $_REQUEST['module'] . "' , '" . $author . "' , '" . $content . "' , '" . $date . "' )");

    saveNotification(_NOTSUG .' : [<a href="index.php?file=Suggest&page=admin">'. _TLINK .'</a>].');

    printNotification(_YOURSUGGEST .'<br />'. _THXPART, 'success');

    if ($nuked['suggest_avert'] == 'on'){
        $date2 = nkDate($date);

        if (!empty($user[2])) $pseudo = $user[2];
        else $pseudo = __('VISITOR') . ' (' . $user_ip . ')';

        $subject = _NEWSUGGEST . ", " . $date2;
        $corps = $pseudo . " " . _NEWSUBMIT . "\r\n" . $nuked['url'] . "/index.php?file=Suggest&page=admin\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
        $from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $nuked['mail'];

        $subject = nkHtmlEntityDecode($subject);
        $corps = nkHtmlEntityDecode($corps);
        $from = nkHtmlEntityDecode($from);

        mail($nuked['mail'], $subject, $corps, $from);
    }

    redirect('index.php?file=' . $_REQUEST['module'], 2);
    closetable();
}

switch ($GLOBALS['op']){
    case'index':
    index();
    break;

    case'add_sug':
    add_sug($_REQUEST);
    break;

    default:
    index();
    break;
}

?>