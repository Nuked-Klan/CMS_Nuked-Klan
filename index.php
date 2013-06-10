<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN - PHP Portal                                                  //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//

define('INDEX_CHECK', 1);

include_once('Includes/php51compatibility.php');
include('globals.php');
if(file_exists('conf.inc.php')) include('conf.inc.php');

// INCLUDE FATAL ERROR LANG
include('Includes/fatal_errors.php');

// POUR LA COMPATIBILITE DES ANCIENS THEMES ET MODULES - FOR COMPATIBITY WITH ALL OLD MODULE AND THEME
if (defined('COMPATIBILITY_MODE') && COMPATIBILITY_MODE == TRUE) extract($_REQUEST);

# Redirect to INSTALL
if (!defined('NK_INSTALLED')){
    if (file_exists('INSTALL/index.php')){
        header('location: INSTALL/index.php');
        exit();
    }
}

if (!defined('NK_OPEN')){
    echo WBSITE_CLOSED;
    exit();
}

include('nuked.php');
include_once('Includes/hash.php');

if ($nuked['time_generate'] == 'on'){
    $mtime = microtime();
}

// GESTION DES ERREURS SQL - SQL ERROR MANAGEMENT
if(ini_get('set_error_handler')) set_error_handler('erreursql');

$session = session_check();
$user = ($session == 1) ? secure() : array();
$session_admin = admin_check();

if(isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'ajax') {
    if($nuked['stats_share'] == 1) {
        $timediff = (time() - $nuked['stats_timestamp'])/60/60/24/60; // 60 Days
        if($timediff >= 60) {
            include('Includes/nkStats.php');
            $data = getStats($nuked);

            $string = serialize($data);

            $opts = array(
                'http' => array(
                    'method' => "POST",
                    'content' => 'data=' . $string
                )
            );

            $context = stream_context_create($opts);

            $daurl = 'http://stats.nuked-klan.org/';
            $retour = file_get_contents($daurl, false, $context);

            $value_sql = ($retour == 'YES') ? mysql_real_escape_string(time()) : 'value + 86400';
            $sql = mysql_query('UPDATE ' . CONFIG_TABLE . ' SET value = ' . mysql_real_escape_string($value_sql) . ' WHERE name = "stats_timestamp"');

        }
    }
    die();
}

if (isset($_REQUEST['nuked_nude']) && !empty($_REQUEST['nuked_nude'])) $_REQUEST['im_file'] = $_REQUEST['nuked_nude'];
else if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) $_REQUEST['im_file'] = $_REQUEST['page'];
else $_REQUEST['im_file'] = 'index';

if (preg_match('`\.\.`', $theme) || preg_match('`\.\.`', $language) || preg_match('`\.\.`', $_REQUEST['file']) || preg_match('`\.\.`', $_REQUEST['im_file']) || preg_match('`http\:\/\/`i', $_REQUEST['file']) || preg_match('`http\:\/\/`i', $_REQUEST['im_file']) || is_int(strpos( $_SERVER['QUERY_STRING'], '..' )) || is_int(strpos( $_SERVER['QUERY_STRING'], 'http://' )) || is_int(strpos( $_SERVER['QUERY_STRING'], '%3C%3F' ))){
    die(WAYTODO);
}

$_REQUEST['file'] = basename(trim($_REQUEST['file']));
$_REQUEST['im_file'] = basename(trim($_REQUEST['im_file']));
$_REQUEST['page'] = basename(trim($_REQUEST['im_file']));
$theme = trim($theme);
$language = trim($language);

// Check Ban
$check_ip = banip();

if (!$user){
    $visiteur = 0;
    $_SESSION['admin'] = false;
}
else $visiteur = $user[1];

include ('themes/' . $theme . '/colors.php');
translate('lang/' . $language . '.lang.php');

if ($nuked['nk_status'] == 'closed' && $user[1] < 9 && $_REQUEST['op'] != 'login_screen' && $_REQUEST['op'] != 'login_message' && $_REQUEST['op'] != 'login'){
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
    <head><title>' , $nuked['name'] , ' - ' , $nuked['slogan'] , '</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <link title="style" type="text/css" rel="stylesheet" href="themes/' , $theme , '/style.css" />
    <body style="background: ' , $bgcolor2 , '">
    <div style="width: 600px; padding: 25px; margin: 200px auto; border: 1px solid ' , $bgcolor3 , '; background: ' , $bgcolor1 , '; text-align: center">
    <h2 style="margin: 0">' , $nuked['name'] , ' - ' , $nuked['slogan'] , '</h2>
    ' , _SITECLOSED , '<br/><br /><a href="index.php?file=User&amp;op=login_screen"><b>' . _LOGINUSER . '</b></a></div></body></html>';
}
else if (($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin' || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin')) && $_SESSION['admin'] == 0){
    include('modules/Admin/login.php');
}
else if (($_REQUEST['file'] != 'Admin' AND $_REQUEST['page'] != 'admin') || ( nivo_mod($_REQUEST['file']) === false || (nivo_mod($_REQUEST['file']) > -1 && (nivo_mod($_REQUEST['file']) <= $visiteur))) ){
    include ('themes/' . $theme . '/theme.php');

    if ($nuked['level_analys'] != -1) visits();

    if (!isset($_REQUEST['nuked_nude'])){
        if (defined('NK_GZIP') && ini_get('zlib_output')){
            ob_start('ob_gzhandler');
        }

        if (!($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin' || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin')) || $_REQUEST['page'] == 'login') {

            if(function_exists('head')){
                // Si la function head est défini dans le theme.php (themes de la version 1.8)
                head();

                // Si le module requis contient un fichier css on l'inclus
                $urlCssMod = 'modules/'.$_REQUEST['file'].'/'.$_REQUEST['file'].'.css';
                if(file_exists($urlCssMod)){
                    echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$urlCssMod.'" />';
                }

                // Si le thème utilisé contient un fichier css pour le module requis on l'inclut (Version 1.8)
                $urlThemeCssMod = 'themes/'.$theme.'/css/modules/'.$_REQUEST['file'].'/'.$_REQUEST['file'].'.css';
                if(file_exists($urlThemeCssMod)){
                    echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$urlThemeCssMod.'" />';
                }

                top();
            }
            else{
                // Sinon on conserve la compatibilité avec les anciens thèmes
                top();

                // Si le module requis contient un fichier css on l'inclus
                $urlCssMod = 'modules/'.$_REQUEST['file'].'/'.$_REQUEST['file'].'.css';
                if(file_exists($urlCssMod)){
                    echo '<link rel="stylesheet" type="text/css" media="screen" href="'.$urlCssMod.'" />';
                }
            }

            ?>
            <script type="text/javascript" src="media/js/infobulle.js"></script>
            <script type="text/javascript">
                InitBulle('<?= $bgcolor2; ?>','<?= $bgcolor3; ?>', 2);
            </script>
            <script type="text/javascript">
                if(typeof jQuery == 'undefined'){
                    document.write('\x3Cscript type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js">\x3C/script>');
                }
            </script>
            <script type="text/javascript" src="media/js/syntaxhighlighter/shCore.js"></script>
            <script type="text/javascript" src="media/js/syntaxhighlighter/shAutoloader.js"></script>
            <script type="text/javascript" src="media/js/syntaxhighlighter.autoloader.js"></script>
            <link type="text/css" rel="stylesheet" href="media/css/syntaxhighlighter/shCoreMonokai.css"/>
            <link type="text/css" rel="stylesheet" href="media/css/syntaxhighlighter/shThemeMonokai.css"/>
            <?php
        }

        if($user[1] == 9 && $_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin'){
            if ($nuked['nk_status'] == 'closed'){
                echo '<div style="border: 1px solid ' , $bgcolor3 , '; background: ' , $bgcolor2 , '; margin: 10px; padding: 10px"><b>' , _YOURSITEISCLOSED , ' :<br /><br/ >' , $nuked['url'] , '/index.php?file=User&amp;op=login_screen</b></div>',"\n";
            }
            if (is_dir('INSTALL/')){
                echo '<div style="border: 1px solid ' , $bgcolor3 , '; background: ' , $bgcolor2 , '; margin: 10px; padding: 10px;text-align:center;font-size:18px;"><b>' , REMOVEDIRINST , '</b></div>',"\n";
            }
            if (file_exists('install.php') || file_exists('update.php')){
                echo '<div style="border: 1px solid ' , $bgcolor3 , '; background: ' , $bgcolor2 , '; margin: 10px; padding: 10px;text-align:center;font-size:18px;"><b>' , REMOVEINST , '</b></div>',"\n";
            }
        }
    }
    else
        header('Content-Type: text/html;charset=ISO-8859-1');

    if (is_file('modules/' . $_REQUEST['file'] . '/' . $_REQUEST['im_file'] . '.php')){
        include('modules/' . $_REQUEST['file'] . '/' . $_REQUEST['im_file'] . '.php');
    }
    else include('modules/404/index.php');

    if ($_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin' && defined('EDITOR_CHECK')) {

        ?>
            <script type="text/javascript" src="media/ckeditor/ckeditor.js"></script>
            <script type="text/javascript" src="media/ckeditor/config.js"></script>
            <script type="text/javascript">
                //<![CDATA[
                if(document.getElementById('e_basic')){
                    CKEDITOR.config.scayt_sLang = "<?php echo (($language == 'french') ? 'fr_FR' : 'en_US'); ?>";
                    CKEDITOR.config.scayt_autoStartup = "true";
                    CKEDITOR.replace('e_basic',{
                        toolbar : 'Basic',
                        language : '<?php echo substr($language, 0,2) ?>',
                        <?php echo !empty($bgcolor4) ? 'uiColor : \''.$bgcolor4.'\'' : ''; ?>
                    });
                    <?php echo ConfigSmileyCkeditor(); ?>
                }

                if(document.getElementById('e_advanced')){
                    <?php echo ($nuked['video_editeur'] == 'on') ? 'CKEDITOR.config.extraPlugins = \'Video\';' : ''; ?>
                    CKEDITOR.config.scayt_sLang = "<?php echo (($language == 'french') ? 'fr_FR' : 'en_US'); ?>";
                    <?php echo ($nuked['scayt_editeur'] == 'on') ? 'CKEDITOR.config.scayt_autoStartup = "true";' : ''; ?>
                    CKEDITOR.replace('e_advanced',{
                        toolbar : 'Full',
                        language : '<?php echo substr($language, 0,2) ?>',
                        <?php echo !empty($bgcolor4) ? 'uiColor : \''.$bgcolor4.'\',' : ''; ?>
                        allowedContent:
                            'p h1 h2 h3 h4 h5 h6 blockquote tr td div a span{text-align,font-size,font-family,font-style,color,background-color,display};' +
                            'img[!src,alt,width,height,class,id,style,title,border];' +
                            'strong s em u strike sub sup ol ul li br caption thead  hr big small tt code del ins cite q address section aside header;' +
                            'div[class,id,style,title,align]{page-break-after,width,height,background};' +
                            'a[!href,accesskey,class,id,name,rel,style,tabindex,target,title];' +
                            'table[align,border,cellpadding,cellspacing,class,id,style];' +
                            'td[colspan, rowspan];' +
                            'th[scope];' +
                            'pre(*);' +
                            'span[id, style];'
                            <?php if($nuked['video_editeur'] == 'on'){ ?>
                                + 'object[width,height,data,type];'
                                + 'param[name,value];'
                                + 'embed[width,height,src,type,allowfullscreen,allowscriptaccess];'
                            <?php } ?>
                    });
                    <?php echo ConfigSmileyCkeditor(); ?>
                }
                //]]>
            </script>
        <?php
    }

    if (!isset($_REQUEST['nuked_nude'])){
        if ($user[5] > 0 && !isset($_COOKIE['popup']) && $_REQUEST['file'] != 'User' && $_REQUEST['file'] != 'Userbox'){
            echo '<div id="popup_dhtml" style="position:absolute;top:0;left:0;visibility:visible;z-index:10"></div>',"\n"
            , '<script type="text/javascript" src="media/js/popup.js"></script>',"\n"
            , '<script type="text/javascript">popup("' , $bgcolor2 , '", "' , $bgcolor3 , '", "' , _NEWMESSAGESTART , '&nbsp;' , $user[5] , '&nbsp;' , _NEWMESSAGEEND , '", "' , _CLOSEWINDOW , '", "index.php?file=Userbox", 350, 100);</script>',"\n";
        }

        if (!($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin') || $_REQUEST['page'] == 'login'){
            footer();
        }

        include('Includes/copyleft.php');

        if ($nuked['time_generate'] == 'on'){
            $mtime = microtime() - $mtime;
            echo '<p style="color:#555555;text-align:center;width:100%;">Generated in ',${mtime},'s</p>';
        }

        send_stats_nk();

        echo '</body></html>';
    }
}
else{
    include ('themes/' . $theme . '/colors.php');
    include ('themes/' . $theme . '/theme.php');
    top();
    opentable();
    translate('lang/' . $language . '.lang.php');
    echo '<br /><br /><div style="text-align: center;">' , _NOENTRANCE , '<br /><br /><a href="javascript:history.back()"><b>' , _BACK , '</b></a></div><br /><br />';
    closetable();
    footer();
}

mysql_close($db);
?>
