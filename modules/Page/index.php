<?php
/**
 * index.php
 *
 * Frontend of Page module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! moduleInit('Page'))
    return;


function index($name) {
global $nuked, $visiteur, $user;

opentable();

if ($name != "") {
    $sql = mysql_query("SELECT id, titre, niveau, content, url, type, show_title, members FROM " . PAGE_TABLE . " WHERE titre = '" . $name . "'");
    $count = mysql_num_rows($sql);
}
else if ($nuked['index_page'] != "") {
    $sql = mysql_query("SELECT id, titre, niveau, content, url, type, show_title, members FROM " . PAGE_TABLE . " WHERE titre = '" . $nuked['index_page'] . "'");
    $count = mysql_num_rows($sql);
}
else {
    $count = 0;	
}

if ($count > 0) {
    list($pid, $titre, $niveau, $content, $url, $type, $show_title, $members) = mysql_fetch_array($sql);
    $content = stripslashes($content);
    $titre = stripslashes($titre);
    
    
    if ($visiteur >= admin_mod("Page")) echo '<div style="text-align:right; margin:10px;"><a class="nkButton icon alone edit" href="index.php?file=Page&amp;page=admin&amp;op=edit&amp;page_id=' . $pid . '" ></a></div>';	

    if ($visiteur >= $niveau) {
        if (!empty($members)) $users_list = explode('|', $members);
        
        if ((isset($users_list) AND in_array($user[0], $users_list)) OR $visiteur >= admin_mod("Page") OR !isset($users_list)) {
            $titleshow = ( $show_title == 1 ) ? '<h2 style="text-transform:capitalize;">' . $titre . '</h2>' : '';
        
            if ($content != "") {
                if ($type == "php")
                {
                    ob_start();
                    $content_php = eval($content);
                    $content_php = ob_get_contents();
                    ob_end_clean();
                    echo $titleshow;
                    echo $content_php;
                }
                else {
                    $content = str_replace("&lt;", "<", $content);
                    $content = str_replace("&gt;", ">", $content);
                    echo $titleshow;
                    echo $content;
                }
            }	            

            if ($url != "") {
                if ($type == "php" && is_file("modules/Page/php/" . $url)) {
                    ob_start();
                    $page_php =  eval(' include ("modules/Page/php/" . $url); ');
                    $page_php = ob_get_contents();
                    ob_end_clean();
                    echo $titleshow;
                    echo $page_php;
                }
                else if (is_file("modules/Page/html/" . $url)) {
                    ob_start();
                    $html = eval(' include ("modules/Page/html/" . $url); ');
                    $html = ob_get_contents(); 
                    ob_end_clean();

                    if (stripos($html, '<body') !== false && stripos($html, '</body>') !== false) {
                        preg_match_all("=<body[^>]*>(.*)</body>=siU", $html, $a); 
                        $html_page = $a[1][0];
                        echo $titleshow;
                        echo $html_page;
                    }
                    else {
                        echo $titleshow;
                        echo $html;
                    }	
                }
            }	
        }
        else {
            echo '<div id="nkAlertError" class="nkAlert">
                    <strong>'._NOENTRANCE.'</strong>
                    <a href="javascript:history.back()"><span>'._BACK.'</span></a>
                </div>';
        }
    }
    else if ($niveau == 1 && $visiteur == 0) {
        echo '<div id="nkAlertError" class="nkAlert">
                <strong>'._USERENTRANCE.'</strong>
                <a href="index.php?file=User&amp;op=login_screen"><span>'._LOGINUSER.'</span></a>
                &nbsp;|&nbsp;
                <a href="index.php?file=User&amp;op=reg_screen"><span>'._REGISTERUSER.'</span></a>
            </div>';
    }
    else {
        echo '<div id="nkAlertError" class="nkAlert">
                <strong>'._NOENTRANCE.'</strong>
                <a href="javascript:history.back()"><span>'._BACK.'</span></a>
            </div>';
    }

}
else {
    echo '<div id="nkAlertWarning" class="nkAlert">
            <strong>'._NOEXIST.'</strong>
            <a href="javascript:history.back()"><span>'._BACK.'</span></a>
        </div>';
}

closetable();
}

switch($_REQUEST['op']) {
    case "index":
    index($_REQUEST['name']);
    break;

    default:
    index();
    break;
}


?>