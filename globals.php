<?php
/**
 * globals.php
 *
 *
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
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
function SecureVar($value) {
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
// error_reporting (E_ERROR | E_WARNING | E_PARSE);

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


// INTEGRER ID CHECK
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
        die(sprintf(ID_MUST_INTEGRER, $getId[$i]));
}

unset($getId, $size);


// FONCTION DE SUBSTITUTION POUR MAGIC_QUOTE_GPC
DeleteGlobalVars();
$_GET       = array_map('SecureVar', $_GET);
$_POST      = array_map('SecureVar', $_POST);
$_COOKIE    = array_map('SecureVar', $_COOKIE);
$_REQUEST   = array_merge($_COOKIE, $_POST, $_GET);

// POUR LA COMPATIBILITE DES ANCIENS THEMES ET MODULES - FOR COMPATIBITY WITH ALL OLD MODULE AND THEME
if (defined('COMPATIBILITY_MODE') && COMPATIBILITY_MODE == true) {
    extract($_REQUEST);

    $_REQUEST['file'] = & $GLOBALS['file'];
    //$_REQUEST['page'] = & $GLOBALS['page'];
    //$_REQUEST['op']   = & $GLOBALS['op'];
}


// UPLOAD PROTECTION
/*
// TODO : Update module with nkUpload librairy
if (! empty($_FILES)) {
    $nkModules = array(
        'Admin', 'Forum', 'Suggest', 'User', 'Gallery', 'News', 'Download',
        'Sections', 'Wars'
    );

    // TODO : Get cleaned $_REQUEST['file'] value !!!
    if (! in_array($_REQUEST['file'], $nkModules)) {
        foreach ($_FILES as $k => $v) {
            if ($_FILES[$k]['error'] !== UPLOAD_ERR_OK)
                continue;

            $ext = strrchr($_FILES[$k]['name'], '.');

            $_FILES[$k]['name'] = substr(md5(uniqid()), rand(0, 20), 10) . $ext;

            $sfile = new finfo(FILEINFO_MIME);
            $mime  = $sfile->file($_FILES[$k]['tmp_name']);

            if (stripos($ext, 'php') !== false || stripos($mime, 'php') !== false) {
                //@unlink($_FILES[$k]['tmp_name']);
                die(NO_UPLOAD_PHP_FILE);
            }
            else if (stripos($ext, 'htm') !== false || stripos($mime, 'htm') !== false) {
                //@unlink($_FILES[$k]['tmp_name']);
                die(NO_UPLOAD_HTML_FILE);
            }
            else if (stripos($ext, 'htaccess') !== false || stripos($mime, 'htaccess') !== false) {
                //@unlink($_FILES[$k]['tmp_name']);
                die(NO_UPLOAD_HTACCESS_FILE);
            }

            unset($ext, $sfile, $mime);
        }
    }

    unset($nkModules);
}

*/

//register_shutdown_function(create_function('', 'var_dump($_GET, $_POST, $_REQUEST);return false;'));


?>