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
require_once 'globals.php';

if (file_exists('conf.inc.php'))
    require_once 'conf.inc.php';

require_once 'nuked.php';


/**
 * Checks if site is closed or not installed
 */
nkHandle_siteInstalled();

if ($nuked['time_generate'] == 'on')
    $microTime = microtime(true);

// GESTION DES ERREURS SQL - SQL ERROR MANAGEMENT
if(ini_get('set_error_handler')) set_error_handler('erreursql');


if ($nuked['stats_share'] == 1 && isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 'sendNkStats') {
    require_once 'Includes/nkStats.php';

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
 * Choose which module file will be include
 */
//$page = nkHandle_page();
$_REQUEST['page'] = nkHandle_page();


// Hack for CSRF vulnerabilities
if ($_SESSION['admin'] == true &&
    $_REQUEST['file'] != 'Admin'
    && $_REQUEST['page'] != 'admin'
    && (! ($_REQUEST['file'] == 'Textbox' && $_REQUEST['page'] == 'index' && $_REQUEST['op'] == 'ajax'))
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
    nkTemplate_setInterface('frontend');
    nkTemplate_setPageDesign('none');
    echo applyTemplate('websiteClosed');
}
// Display admin login
else if (($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin')
    && nkSessions_adminCheck() == false
) {
    require_once 'modules/Admin/login.php';
}
// Run module
else {
    ob_start();

    require_once 'themes/'. $theme .'/colors.php';
    require_once 'themes/'. $theme .'/theme.php';

    if ($nuked['level_analys'] != -1)
        visits();

    if (is_file('modules/'. $_REQUEST['file'] .'/'. $_REQUEST['page'] .'.php'))
        require_once 'modules/'. $_REQUEST['file'] .'/'. $_REQUEST['page'] .'.php';
    else
        require_once 'modules/404/index.php';

    if ($_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin' && defined('EDITOR_CHECK')) {
        // choix de l'éditeur
        if ($nuked['editor_type'] == 'cke') // ckeditor
            loadCkeFiles();
        else if ($nuked['editor_type'] == 'tiny') // tinymce
            loadTinymceFiles();
    }

    $moduleContent = ob_get_clean();

    loadSyntaxhighlighterFiles();
    nkTemplate_addJSFile('media/js/infobulle.js');

    if (in_array(nkTemplate_getPageDesign(), array('fullPage', 'nudePage')) || isset($_REQUEST['nuked_nude'])) {
        if (! ($_REQUEST['file'] == 'Admin' || $_REQUEST['page'] == 'admin')
            || $_REQUEST['page'] == 'login'
        )
            nkTemplate_addJS('InitBulle(\''. $bgcolor2 .'\',\''. $bgcolor3 .'\', 2);');

        if (trim($moduleContent) != '') {// Hack for old module without content displayed
            if (nkTemplate_getPageDesign() == 'fullPage')
                    $moduleContent = nkHandle_alert() . $moduleContent;

            $html = nkTemplate_renderPage($moduleContent);

            if (isset($_REQUEST['nuked_nude']))
                header('Content-Type: text/html;charset=ISO-8859-1');

            if (! isset($_REQUEST['nuked_nude']) && defined('NK_GZIP') && ini_get('zlib_output'))
                ob_start('ob_gzhandler');

            echo $html;

            //if (nkTemplate_getPageDesign() == 'fullPage')
            //    nkBenchmark_display();

            if ($nuked['stats_share'] == 1) nkStats_cron();

            echo '</body></html>';
        }
    }
    else {
        header('Content-Type: text/html;charset=ISO-8859-1');
        echo $moduleContent;
    }
}

nkDB_disconnect();

/*
if (nkTemplate_getPageDesign() == 'fullPage') {
    echo '<!--', "\n";
    print_r($GLOBALS['nkDB']['querys']);
    echo '-->', "\n";
}*/

?>