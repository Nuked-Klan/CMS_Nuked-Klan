<?php
/**
 * globals.php
 *
 *
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

/**
 * Delete all global vars
 *
 * @return void
 */
function DeleteGlobalVars(){
    //Vars witch shouldn't be delete
    $NoDelete = array('GLOBALS', '_POST', '_GET', '_COOKIE', '_FILES', '_SERVER', '_ENV', '_REQUEST', '_SESSION');

    foreach ($GLOBALS as $k => $v) {
        if (in_array($k, $NoDelete) === false) {
            $GLOBALS[$k] = NULL;
            unset($GLOBALS[$k]);
        }
    }
}


/**
 * Secure a var
 *
 * @param mixed var
 * @return void
 */
function secureVar($value) {
    if (is_array($value)) {
        foreach ($value as $k => $v)
            $value[$k] = SecureVar($value[$k]);

        return $value;
    }
    else {
        return str_replace(array('&', '<', '>', '0x'), array('&amp;', '&lt;', '&gt;', '0&#120;'), addslashes($value));
    }
}

// Suppression de l'affichage des erreurs PHP
if (defined('DEVELOPMENT') && DEVELOPMENT) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
else
    error_reporting (E_ERROR | E_WARNING | E_PARSE);

// ANTI INJECTION SQL (UNION) et XSS/CSS
$queryString = strtolower(rawurldecode($_SERVER['QUERY_STRING']));

$badString = array(
    '%20union%20', '/*', '*/union/*', '+union+', 'load_file', 'outfile', // ANTI INJECTION SQL (UNION)
    'document.cookie', 'onmouse', '<script', '<iframe', '<applet', '<meta', '<style', '<form', '<img', '<body', '<link', // XSS/CSS
    '..', 'http://', '%3C%3F'
);

$size = count($badString);

for ($i = 0; $i < $size; $i++) {
    if (strpos($queryString, $badString[$i]))
        die(WAYTODO);
}

unset($queryString, $badString);


// INTEGER ID CHECK
// TODO : Use a unique ID
$getId = array(
    'cat_id', 'cat', 'forum_id', 'thread_id', 'game',// 'id',
    'p', 'm', 'y', 'mo', 'ye', 'oday', 'omonth', 'oyear',
    // replace by id
    'news_id', 'dl_id', 'link_id', 'cid', 'secid', 'artid', 'poll_id',
    'sid', 'vid', 'im_id', 'tid', 'war_id', 'server_id', 'mid',
);

$size = count($getId);

for ($i = 0; $i < $size; $i++) {
    if (isset($_GET[$getId[$i]]) && ! empty($_GET[$getId[$i]]) && ! ctype_digit($_GET[$getId[$i]]))
        die(sprintf(ID_MUST_INTEGER, $getId[$i]));
}

unset($getId, $size);


// FONCTION DE SUBSTITUTION POUR MAGIC_QUOTE_GPC
DeleteGlobalVars();
$_GET       = array_map('secureVar', $_GET);
$_POST      = array_map('secureVar', $_POST);
$_COOKIE    = array_map('secureVar', $_COOKIE);
$_REQUEST   = array_merge($_COOKIE, $_POST, $_GET);

// POUR LA COMPATIBILITE DES ANCIENS THEMES ET MODULES - FOR COMPATIBITY WITH ALL OLD MODULE AND THEME
if (defined('COMPATIBILITY_MODE') && COMPATIBILITY_MODE == true) {
    extract($_REQUEST);

    $_REQUEST['file'] = & $GLOBALS['file'];
    $_REQUEST['page'] = & $GLOBALS['page'];
    $_REQUEST['op']   = & $GLOBALS['op'];

    if (isset($_REQUEST['nuked_nude']))
        $_REQUEST['nuked_nude'] = & $GLOBALS['nuked_nude'];
}

?>