<?php
/**
 * index.php
 *
 * Index of CMS Nuked-Klan
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

// Permet de s'assurer que tous les scripts passe bien par l'index du CMS
define('INDEX_CHECK', 1);
ini_set('default_charset', 'ISO8859-1');

require_once 'Includes/fatal_errors.php';
require_once 'Includes/php51compatibility.php';
require_once 'globals.php';

if (file_exists('conf.inc.php'))
    require_once 'conf.inc.php';

// POUR LA COMPATIBILITE DES ANCIENS THEMES ET MODULES - FOR COMPATIBITY WITH ALL OLD MODULE AND THEME
if (defined('COMPATIBILITY_MODE') && COMPATIBILITY_MODE == true)
    extract($_REQUEST);


require_once 'nuked.php';
require_once 'Includes/hash.php';

/**
 * Checks if site is closed or not installed
 */
nkHandle_siteInstalled();

// Ouverture du buffer PHP
$bufferMedias = ob_start();

if ($nuked['time_generate'] == 'on')
    $microTime = microtime(true);

// GESTION DES ERREURS SQL - SQL ERROR MANAGEMENT
if(ini_get('set_error_handler')) set_error_handler('erreursql');


if ($nuked['stats_share'] == 1) {
    require_once 'Includes/nkStats.php';

    if (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'ajax')
        nkStats_send();
}


/**
 * Get user ( $user var is affected to $GLOBALS['user'] )
 */
nkSessions_getUser();


/**
 * Checks if current user is banned or not
 */
nkHandle_bannedUser();


/**
 * Choose which file willl be include
 */
//$page = nkHandle_file();
$_REQUEST['page'] = nkHandle_file();


/**
 * Checks for forbidden characters in request parameters
 */
nkHandle_URIInjections();


if ( $_REQUEST['file'] !== 'Admin'
    && $_REQUEST['page'] != 'admin'
    && (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] != 'admin')
    && (! ($_REQUEST['file'] == 'Textbox' && $_REQUEST['op'] == 'ajax' && $_REQUEST['nuked_nude'] == 'index'))
    && $_SESSION['admin'] == true
) {
    $_SESSION['admin'] = false;
}


/**
 * Init translation
 */
translate('lang/'. $language .'.lang.php');

// If website is closed
if ($nuked['nk_status'] == 'closed' && $visiteur < 9
    && ! in_array($_REQUEST['op'], array('login_screen', 'login_message', 'login'))
) {
    require_once 'themes/'. $theme .'/colors.php';

    echo applyTemplate('websiteClosed');
}
// Display admin login
else if (($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin'
    || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin'))
    && nkSessions_adminCheck() == false
) {
    require_once 'modules/Admin/login.php';
}
// Run module
else if (($_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin')
    || (nivo_mod($_REQUEST['file']) === false || (nivo_mod($_REQUEST['file']) > -1
    && (nivo_mod($_REQUEST['file']) <= $visiteur)))
) {
    require_once 'themes/'. $theme .'/colors.php';
    require_once 'themes/'. $theme .'/theme.php';

    if ($nuked['level_analys'] != -1)
        visits();

    //if (nkTemplate_getPageDesign() == 'nudePage') {
    if (! isset($_REQUEST['nuked_nude'])){
        if (defined('NK_GZIP') && ini_get('zlib_output'))
            ob_start('ob_gzhandler');

        if (! ($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin' || (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] == 'admin'))
            || $_REQUEST['page'] == 'login') {

            top();

            nkGetMedias();
            nkTemplate_addJS('InitBulle(\''. $bgcolor2 .'\',\''. $bgcolor3 .'\', 2);');
        }

        if ($visiteur == 9 && $_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin') {
            if ($nuked['nk_status'] == 'closed')
                echo applyTemplate('nkAlert/nkSiteClosedLogged');

            if (is_dir('INSTALL/'))
                echo applyTemplate('nkAlert/nkInstallDirTrue');

            if (file_exists('install.php') || file_exists('update.php'))
                echo applyTemplate('nkAlert/nkInstallFileTrue');
        }

        if ($user && $user[5] > 0 && ! isset($_COOKIE['popup'])
            && ! in_array($_REQUEST['file'], array('User', 'Userbox', 'Admin'))
            && $_REQUEST['page'] != 'admin'
        )
            echo applyTemplate('nkAlert/nkNewPrivateMsg');
    }
    else {
        header('Content-Type: text/html;charset=ISO-8859-1');
    }

    if (is_file('modules/'. $_REQUEST['file'] .'/'. $_REQUEST['page'] .'.php'))
        require_once 'modules/'. $_REQUEST['file'] .'/'. $_REQUEST['page'] .'.php';
    else
        require_once 'modules/404/index.php';

    if ($_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin' && defined('EDITOR_CHECK')) {
        // choix de l'éditeur
        if ($nuked['editor_type'] == 'cke') { // ckeditor
            loadCkeFiles();
        }
        else if ($nuked['editor_type'] == 'tiny') { // tinymce
            nkTemplate_addJSFile('media/tinymce/tinymce.min.js');
            nkTemplate_addJSFile('media/tinymce/nkConfig.js');
        }
    }

    //if (nkTemplate_getPageDesign() == 'nudePage') {
    if (! isset($_REQUEST['nuked_nude'])) {
        if (! ($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin') || $_REQUEST['page'] == 'login') {
            footer();
            require_once 'Includes/copyleft.php';
        }

        if ($nuked['time_generate'] == 'on') {
            $microTime = round((microtime(true) - $microTime) * 1000, 1);
            echo '<p class="nkGenerated">Generated in '. $microTime .'ms</p>';
        }

        // TODO : Create a $nuked vars to display it
        echo '<p class="nkGenerated">', nkDB_getNbExecutedQuery(), ' requêtes sql (', nkDB_getTimeForExecuteAllQuery(), 'ms)</p>';

        if ($nuked['stats_share'] == 1) nkStats_cron();

        echo '</body></html>';

        echo '<!--', "\n";
        print_r($GLOBALS['nkDB']['querys']);
        echo '-->', "\n";
    }
}
// User unauthorized to module access
else {
    require_once 'themes/'. $theme .'/colors.php';
    require_once 'themes/'. $theme .'/theme.php';
    top();
    opentable();
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

// echo nkTemplate_renderPage($moduleContent);

nkDB_disconnect();

?>