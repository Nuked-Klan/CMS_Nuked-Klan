<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

global $nuked, $language, $user, $cookie_captcha;
translate('modules/Suggest/lang/' . $language . '.lang.php');

// Inclusion système Captcha
include_once('Includes/nkCaptcha.php');

// On determine si le captcha est actif ou non
if (_NKCAPTCHA == 'off') $captcha = 0;
else if ((_NKCAPTCHA == 'auto' OR _NKCAPTCHA == 'on') && $user[1] > 0)  $captcha = 0;
else $captcha = 1;

$visiteur = !$user ? 0 : $user[1];
$ModName = basename(dirname(__FILE__));
$level_access = nivo_mod($ModName);
if ($visiteur >= $level_access && $level_access > -1){
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
                die('<br /><br /><div style="text-align: center"><big>What are you trying to do ?</big></div><br /><br />');
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
                echo '<br /><br /><div style="text-align: center">' . _MODULEOFF . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a><br /><br /></div>';
            }
            else if ($niveau == 1 && $visiteur == 0){
                echo '<br /><br /><div style="text-align: center">' . _USERENTRANCE . '<br /><br /><b><a href="index.php?file=User&amp;op=login_screen">' . _LOGINUSER . '</a> | <a href="index.php?file=User&amp;op=reg_screen">' . _REGISTERUSER . '</a></b><br /><br /></div>';
            }
            else{
                echo '<br /><br /><div style="text-align: center">' . _NOENTRANCE . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a><br /><br /></div>';
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
        global $user, $nuked, $captcha,$user_ip;

        opentable();

        if (preg_match('#\.\.#', $_REQUEST['module']) || preg_match('#\\\#', $_REQUEST['module'])){
            die('<br /><br /><div style="text-align: center"><big>What are you trying to do ?</big></div><br /><br />');
        }
        else{
            include('modules/Suggest/modules/' . $_REQUEST['module'] . '.php');
        }

        $content = make_array($data);
        $content = mysql_real_escape_string(stripslashes($content));

        if(strlen($content) <= 30){
            echo '<br /><br /><div style="text-align: center">' . _NOCONTENT . '</div><br /><br />';
            closetable();
            footer();
            die();
        }

        // Verification code captcha
        if ($captcha == 1 && !ValidCaptchaCode($_REQUEST['code_confirm'])){
            echo '<br /><br /><div style="text-align: center">' . _BADCODECONFIRM . '<br /><br /><a href="javascript:history.back()">[ <b>' . _BACK . '</b> ]</a></div><br /><br />';
            closetable();
            footer();
            exit();
        }

        $date = time();

        if ($user){
            $author = $user[0];
        }
        else{
            $author = $user_ip;
        }

        $sql = mysql_query("INSERT INTO " . SUGGEST_TABLE . " ( `id` , `module` , `user_id` , `proposition` , `date` ) VALUES ( '' , '" . $_REQUEST['module'] . "' , '" . $author . "' , '" . $content . "' , '" . $date . "' )");
        $upd2 = mysql_query("INSERT INTO ". $nuked['prefix'] ."_notification  (`date` , `type` , `texte`)  VALUES ('" . $date . "', '1', '" . _NOTSUG . " : [<a href=\"index.php?file=Suggest&page=admin\">lien</a>].')");
        echo '<br /><br /><div style="text-align: center">' . _YOURSUGGEST . '<br />' . _THXPART . '</div><br /><br />';

        if ($nuked['suggest_avert'] == 'on'){
            $date2 = nkDate($date);

            if (!empty($user[2])) $pseudo = $user[2];
            else $pseudo = _VISITOR . ' (' . $user_ip . ')';

            $subject = _NEWSUGGEST . ", " . $date2;
            $corps = $pseudo . " " . _NEWSUBMIT . "\r\n" . $nuked['url'] . "/index.php?file=Suggest&page=admin\r\n\r\n" . $nuked['name'] . " - " . $nuked['slogan'];
            $from = "From: " . $nuked['name'] . " <" . $nuked['mail'] . ">\r\nReply-To: " . $nuked['mail'];

            $subject = html_entity_decode($subject);
            $corps = html_entity_decode($corps);
            $from = html_entity_decode($from);

            mail($nuked['mail'], $subject, $corps, $from);
        }

        redirect('index.php?file=' . $_REQUEST['module'], 2);
        closetable();
    }

    switch ($_REQUEST['op']){
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
}
else if ($level_access == -1){
    opentable();
    echo '<br /><br /><div style="text-align: center">' . _MODULEOFF . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a><br /><br /></div>';
    closetable();
}
else if ($level_access == 1 && $visiteur == 0){
    opentable();
    echo '<br /><br /><div style="text-align: center">' . _USERENTRANCE . '<br /><br /><b><a href="index.php?file=User&amp;op=login_screen">' . _LOGINUSER . '</a> | <a href="index.php?file=User&amp;op=reg_screen">' . _REGISTERUSER . '</a></b><br /><br /></div>';
    closetable();
}
else{
    opentable();
    echo '<br /><br /><div style="text-align: center">' . _NOENTRANCE . '<br /><br /><a href="javascript:history.back()"><b>' . _BACK . '</b></a><br /><br /></div>';
    closetable();
}
?>