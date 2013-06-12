<?php
/**
 * Index of CMS Nuked-Klan
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

// Permet de s'assurer que tous les scripts passe bien par l'index du CMS
define('INDEX_CHECK', 1);

require_once 'Includes/php51compatibility.php';
require_once 'globals.php';

if (file_exists('conf.inc.php')) {
    require_once 'conf.inc.php';
}

require_once 'Includes/fatal_errors.php';

// POUR LA COMPATIBILITE DES ANCIENS THEMES ET MODULES - FOR COMPATIBITY WITH ALL OLD MODULE AND THEME
if (defined('COMPATIBILITY_MODE') && COMPATIBILITY_MODE == TRUE){
    extract($_REQUEST);
}

// Redirection vers l'installation si NK n'est pas installé
if (!defined('NK_INSTALLED')) {
    if (file_exists('INSTALL/index.php')) {
        header('location: INSTALL/index.php');
        exit();
    }
}

// Si le site est fermé on affiche le message de fermeture
if (!defined('NK_OPEN')) {
    echo WBSITE_CLOSED;
    exit();
}

require_once 'nuked.php';
require_once 'Includes/hash.php';

if ($nuked['time_generate'] == 'on') {
    $microTime = microtime();
}

// GESTION DES ERREURS SQL - SQL ERROR MANAGEMENT
if(ini_get('set_error_handler')) set_error_handler('erreursql');

$session       = session_check();
$user          = ($session == 1) ? secure() : array();
$session_admin = admin_check();

if (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'ajax') {
    if ($nuked['stats_share'] == 1) {
        $timediff = (time() - $nuked['stats_timestamp'])/60/60/24/60; // 60 Days
        if($timediff >= 60) {
            require_once 'Includes/nkStats.php';
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

            $sql = mysql_query('UPDATE '.CONFIG_TABLE.' SET value = '.mysql_real_escape_string($value_sql).' WHERE name = "stats_timestamp"');
        }
    }
    exit();
}

// Définition du type de page à afficher
if (isset($_REQUEST['nuked_nude']) && !empty($_REQUEST['nuked_nude'])) {
    $_REQUEST['im_file'] = $_REQUEST['nuked_nude'];
}
else if (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) {
    $_REQUEST['im_file'] = $_REQUEST['page'];
}
else {
    $_REQUEST['im_file'] = 'index';
}

// Securisation des variables utilisateurs
if (preg_match('`\.\.`', $theme) || preg_match('`\.\.`', $language) || preg_match('`\.\.`', $_REQUEST['file']) ||
    preg_match('`\.\.`', $_REQUEST['im_file']) || preg_match('`http\:\/\/`i', $_REQUEST['file']) ||
    preg_match('`http\:\/\/`i', $_REQUEST['im_file']) || is_int(strpos( $_SERVER['QUERY_STRING'], '..' )) ||
    is_int(strpos( $_SERVER['QUERY_STRING'], 'http://' )) || is_int(strpos( $_SERVER['QUERY_STRING'], '%3C%3F' ))){
    exit(WAYTODO);
}

$_REQUEST['file']    = basename(trim($_REQUEST['file']));
$_REQUEST['im_file'] = basename(trim($_REQUEST['im_file']));
$_REQUEST['page']    = basename(trim($_REQUEST['im_file']));
$theme               = trim($theme);
$language            = trim($language);

// Check Ban
$check_ip = banip();

if (!$user) {
    $visiteur          = 0;
    $_SESSION['admin'] = false;
}
else {
    $visiteur          = $user[1];
}

// Inclusion du fichier des couleurs
require_once 'themes/'.$theme.'/colors.php';

// Inclusion du fichier de langue général
translate('lang/'.$language.'.lang.php');

// Si le site est fermé
if ($nuked['nk_status'] == 'closed'
    && (!$user || ($user && $user[1] < 9))
    && $_REQUEST['op'] != 'login_screen'
    && $_REQUEST['op'] != 'login_message'
    && $_REQUEST['op'] != 'login') {
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
        <head>
            <title><?php echo $nuked['name']; ?> - <?php echo $nuked['slogan']; ?></title>
            <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
            <link type="text/css" rel="stylesheet" media="screen" href="media/css/nkDefault.css" />
            <link type="text/css" rel="stylesheet" media="screen" href="themes/<?php echo $theme; ?>/style.css" />
        </head>
        <body style="background:<?php echo $bgcolor2; ?>;">
            <div id="nkSiteClosedWrapper" style=" border: 1px solid <?php echo $bgcolor3; ?>; background:<?php echo $bgcolor2; ?>;">
                <h1><?php echo $nuked['name']; ?> - <?php echo $nuked['slogan']; ?></h1>
                <p><?php echo _SITECLOSED; ?></p>
                <a href="index.php?file=User&amp;op=login_screen"><strong><?php echo _LOGINUSER; ?></strong></a>
            </div>
        </body>
    </html>
<?php
}
else if ( ($_REQUEST['file'] == 'Admin'
            || $_REQUEST['page'] == 'admin'
            || ( isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin' )
          )
            && $_SESSION['admin'] == 0) {
    require_once 'modules/Admin/login.php';
}
else if ( ( $_REQUEST['file'] != 'Admin' AND $_REQUEST['page'] != 'admin' )
            || ( nivo_mod($_REQUEST['file']) === false || ( nivo_mod($_REQUEST['file']) > -1 && nivo_mod($_REQUEST['file']) <= $visiteur ) ) ) {


    require_once 'themes/'.$theme.'/theme.php';

    if ($nuked['level_analys'] != -1) {
        visits();
    }

    if (!isset($_REQUEST['nuked_nude'])){
        if (defined('NK_GZIP') && ini_get('zlib_output')) {
            ob_start('ob_gzhandler');
        }

        if ( !($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin' || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin'))
            || $_REQUEST['page'] == 'login') {

            require_once 'Includes/nkMediasIncludes.php';
            // Les appels de top(); et head(); se font dans la fonction suivante
            displayMedias();
?>
            <script type="text/javascript" src="media/js/infobulle.js"></script>
            <script type="text/javascript">
                InitBulle('<?php echo $bgcolor2; ?>','<?php echo $bgcolor3; ?>', 2);
            </script>
            <script type="text/javascript" src="media/js/syntaxhighlighter/shCore.js"></script>
            <script type="text/javascript" src="media/js/syntaxhighlighter/shAutoloader.js"></script>
            <script type="text/javascript" src="media/js/syntaxhighlighter.autoloader.js"></script>
            <link type="text/css" rel="stylesheet" href="media/css/syntaxhighlighter/shCoreMonokai.css"/>
            <link type="text/css" rel="stylesheet" href="media/css/syntaxhighlighter/shThemeMonokai.css"/>
<?php
        }

        if((array_key_exists(1, $user) && $user[1] == 9) && $_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin') {
            if ($nuked['nk_status'] == 'closed') {
?>
                <div id="nkSiteClosedLogged" class="nkAlert">
                    <strong><?php echo _YOURSITEISCLOSED; ?></strong>
                    <p><?php echo $nuked['url']; ?>/index.php?file=User&amp;op=login_screen</p>
                </div>
<?php
            }
            if (is_dir('INSTALL/')){
?>
                <div id="nkInstallDirTrue" class="nkAlert">
                    <strong><?php echo REMOVEDIRINST; ?></strong>
                </div>
<?php
            }
            if (file_exists('install.php') || file_exists('update.php')){
?>
                <div id="nkInstallFileTrue" class="nkAlert">
                    <strong><?php echo REMOVEINST; ?></strong>
                </div>
<?php
            }
        }

        if (($user && $user[5] > 0) && !isset($_COOKIE['popup']) && $_REQUEST['file'] != 'User' && $_REQUEST['file'] != 'Userbox' && $_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin'){
?>
                <div id="nkNewPrivateMsg" class="nkAlert">
                    <strong><?php echo _NEWMESSAGESTART; ?>&nbsp;<?php echo $user[5]; ?>&nbsp;<?php echo _NEWMESSAGEEND; ?></strong>
                    <a href="index.php?file=Userbox"><?php echo _GOTO_PRIVATE_MESSAGES; ?></a>
                    <a id="nkNewPrivateMsgClose" href="#" title="<?php echo _CLICK_TO_CLOSE; ?>"><span><?php echo _CLICK_TO_CLOSE; ?></span></a>
                </div>
<?php
        }
    }
    else {
        header('Content-Type: text/html;charset=ISO-8859-1');
    }

    if (is_file('modules/' . $_REQUEST['file'] . '/' . $_REQUEST['im_file'] . '.php')) {
        require_once 'modules/' . $_REQUEST['file'] . '/' . $_REQUEST['im_file'] . '.php';
    }
    else {
        require_once 'modules/404/index.php';
    }

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
                            'img[!src,alt,width,height,class,id,style,title,border]{*}(*);' +
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

    if (!isset($_REQUEST['nuked_nude'])) {
        if (!($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin') || $_REQUEST['page'] == 'login') {
            footer();
        }

        require_once 'Includes/copyleft.php';

        if ($nuked['time_generate'] == 'on') {
            $microTime = microtime() - $microTime;
            echo '<p class="nkGenerated">Generated in '.$microTime.'s</p>';
        }

        send_stats_nk();

        echo '</body></html>';
    }
}
else {
    require_once 'themes/'.$theme.'/colors.php';
    require_once 'themes/'.$theme.'/theme.php';
    top();
    opentable();
    translate('lang/'.$language.'.lang.php');
?>
    <link type="text/css" rel="stylesheet" href="media/css/nkDefault.css" />
    <div class="nkErrorMod">
        <p><?php echo _NOENTRANCE; ?></p>
        <a href="javascript:history.back()"><b><?php echo _BACK; ?></b></a>
    </div>

<?php
    closetable();
    footer();
}

mysql_close($db);
?>
