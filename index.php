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

define('DEVELOPMENT', true);


// Permet de s'assurer que tous les scripts passe bien par l'index du CMS
define('INDEX_CHECK', 1);
ini_set('default_charset', 'ISO8859-1');

if (defined('DEVELOPMENT') && DEVELOPMENT)
    require_once 'Includes/nkDebug.php';

require_once 'Includes/fatal_errors.php';
require_once 'globals.php';

if (file_exists('conf.inc.php'))
    require_once 'conf.inc.php';

require_once 'nuked.php';


if ($nuked['time_generate'] == 'on')
    $microTime = microtime(true);

// GESTION DES ERREURS SQL - SQL ERROR MANAGEMENT
if(ini_get('set_error_handler')) set_error_handler('erreursql');


if ($nuked['stats_share'] == 1) {
    require_once 'Includes/nkStats.php';

    if (isset($_GET['ajax']) && $_GET['ajax'] == 'sendNkStats')
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
$GLOBALS['page'] = nkHandle_page();

$inBackend = $GLOBALS['file'] == 'Admin' || $GLOBALS['page'] == 'admin' || $GLOBALS['admin'] === true;

// Hack for CSRF vulnerabilities
if ($_SESSION['admin'] == true
    && ! $inBackend
    && (! ($GLOBALS['file'] == 'Stats' && $GLOBALS['page'] == 'admin' && $GLOBALS['op'] == 'statsPopup'))
    && (! ($GLOBALS['file'] == 'Textbox' && $GLOBALS['page'] == 'index' && $GLOBALS['op'] == 'ajax'))
) {
    if (! (isset($_GET['previewToken'], $_SESSION['previewToken']) && $_GET['previewToken'] == $_SESSION['previewToken'])
        || ! isset($_GET['previewToken'])
    ) {
        $_SESSION['admin'] = false;
    }
}

unset($_SESSION['previewToken']);


/**
 * Init translation
 */
translate('lang/'. $language .'.lang.php');


// If website is closed
if ($nuked['nk_status'] == 'closed'
    && $visiteur < 9
    && ! in_array($GLOBALS['op'], array('login_screen', 'login_message', 'login'))
) {
    nkTemplate_setBgColors();
    nkTemplate_init();
    echo nkTemplate_renderPage(applyTemplate('websiteClosed'));
}
// Display admin login
else if ($inBackend && nkSessions_adminCheck() == false) {
    nkTemplate_init($GLOBALS['file']);
    require_once 'modules/Admin/login.php';
}
// Run module
else {
    ob_start();

    nkTemplate_init($GLOBALS['file']);
    nkTemplate_setBgColors();

    if (nkTemplate_getInterface() == 'frontend')
        require_once 'themes/'. $theme .'/theme.php';

    if ($nuked['level_analys'] != -1)
        visits();

    if ($GLOBALS['admin'] === true)
        $moduleFile = 'modules/'. $GLOBALS['file'] .'/backend/'. $GLOBALS['page'] .'.php';
    else
        $moduleFile = 'modules/'. $GLOBALS['file'] .'/'. $GLOBALS['page'] .'.php';

    if (is_file($moduleFile))
        require_once $moduleFile;
    else
        require_once 'modules/404/index.php';

    $content = ob_get_clean();

    if (in_array(nkTemplate_getPageDesign(), array('fullPage', 'nudePage'))) {
        $content = nkTemplate_renderPage($content);

        if (defined('NK_GZIP') && ini_get('zlib_output'))
            ob_start('ob_gzhandler');
    }
    else
        header('Content-Type: text/html;charset=ISO-8859-1');

    echo $content;
}

nkDB_disconnect();

?>