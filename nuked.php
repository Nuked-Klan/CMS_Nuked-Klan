<?php
/**
 * nuked.php
 *
 *
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


require_once 'Includes/nkSessions.php';
require_once 'Includes/nkTemplate.php';

// Set default flags used by nkHtmlEntityDecode, nkHtmlSpecialChars & nkHtmlEntities
define('NK_HTML_DEFAULT_FLAGS', (ENT_COMPAT | ENT_HTML401));

// Initialize main language array
$arrayModLang = array();

// CONNECT TO DB.
connect();

// QUERY NUKED CONFIG_TABLE.
$nuked = nkLoadConfiguration($db_prefix);

// INCLUDE CONSTANT TABLE
require_once 'Includes/constants.php';

// $_REQUEST['file'] & $_REQUEST['op'] DEFAULT VALUE.
if (empty($_REQUEST['file'])) $_REQUEST['file'] = $nuked['index_site'];
if (empty($_REQUEST['op'])) $_REQUEST['op'] = 'index';


// SELECT THEME, USER THEME OR NOT FOUND THEME : ERROR
if (array_key_exists($nuked['cookiename'] .'_user_theme', $_REQUEST))
    $nuked['user_theme'] = $_REQUEST[$nuked['cookiename'] .'_user_theme'];
else
    $nuked['user_theme'] = false;

if ($nuked['user_theme'] && is_file(dirname(__FILE__) .'/themes/'. $nuked['user_theme'] .'/theme.php'))
    $theme = $nuked['user_theme'];
elseif (is_file(dirname(__FILE__) .'/themes/'. $nuked['theme'] .'/theme.php'))
    $theme = $nuked['theme'];
else
    exit(THEME_NOTFOUND);

// SELECT LANGUAGE AND USER LANGUAGE
if (array_key_exists($nuked['cookiename'] .'_user_langue', $_REQUEST))
    $nuked['user_lang'] = $_REQUEST[$nuked['cookiename'] .'_user_langue'];
else
    $nuked['user_lang'] = false;

if ($nuked['user_lang'] && is_file(dirname(__FILE__) .'/lang/'. $nuked['user_lang'] .'.lang.php'))
    $language = $nuked['user_lang'];
else
    $language = $nuked['langue'];


// FORMAT DATE FR/EN
if($language == 'french') {
    // On verifie l'os du serveur pour savoir si on est en windows (setlocale : ISO) ou en unix (setlocale : UTF8)
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') setlocale (LC_ALL, 'fr_FR','fra');
    else setlocale(LC_ALL, 'fr_FR.UTF8','fra');
}
elseif($language == 'english') setlocale(LC_ALL, 'en_US');

// DATE FUNCTION WITH FORMAT AND ZONE FOR DATE
$dateZone = getTimeZoneDateTime($nuked['datezone']);
date_default_timezone_set($dateZone);

// CONFIG PHP SESSION
if (ini_get('session.save_handler') == 'files')
    session_set_save_handler('session_open', 'session_close', 'session_read', 'session_write', 'session_delete', 'session_gc');

if (ini_get('suhosin.session.encrypt') == '1') {
    @ini_set('session.gc_probability', 100);
    @ini_set('session.gc_divisor', 100);
    @ini_set('session.gc_maxlifetime', (1440));
}

nkSessions_init();


/**
 * Checks if site is closed or not installed
 *
 * @param void
 * @return void
 */
function nkHandle_siteInstalled() {
    if (! defined('NK_OPEN')) {
        echo WEBSITE_CLOSED;
        exit;
    }

    if (! defined('NK_INSTALLED') && file_exists('INSTALL/index.php')) {
        header('location: INSTALL/index.php');
        exit;
    }
}

/**
 * Checks for forbidden characters in request parameters
 *
 * @param void
 * @return void
 */
function nkHandle_URIInjections() {
    // On the recommendations of phpSecure.info
    if (stripos($GLOBALS['theme'], '..') !== false
        || stripos($GLOBALS['language'], '..') !== false
        || stripos($_REQUEST['file'], '..') !== false
        || stripos($_REQUEST['file'], 'http://') !== false
        || stripos($_SERVER['QUERY_STRING'], '..') !== false
        || stripos($_SERVER['QUERY_STRING'], 'http://') !== false
        || stripos($_SERVER['QUERY_STRING'], '%3C%3F') !== false
    ) {
        die(WAYTODO);
    }

    $_REQUEST['file']       = basename(trim($_REQUEST['file']));
    $GLOBALS['theme']       = trim($GLOBALS['theme']);
    $GLOBALS['language']    = trim($GLOBALS['language']);
}

/**
 * Choose which file will be include
 *
 * @param void
 * @return string : File name to include
 */
function nkHandle_file() {
    if (isset($_REQUEST['nuked_nude']))
        $_REQUEST['nuked_nude'] = basename(trim($_REQUEST['nuked_nude']));

    if (isset($_REQUEST['page']))
        $_REQUEST['page'] = basename(trim($_REQUEST['page']));

    if (isset($_REQUEST['nuked_nude']) && $_REQUEST['nuked_nude'] != '') {
        trigger_error('Superglobal $nuked_nude is deprecated. Please update your module.', E_USER_DEPRECATED);
        nkTemplate_setPageDesign('none');

        return $_REQUEST['nuked_nude'];
    }
    elseif (isset($_REQUEST['page']) && $_REQUEST['page'] != '') {
        return $_REQUEST['page'];
    }

    return 'index';
}

/**
 * Check and return nkAlert for Website closed, INSTALL directory,
 * install.php and update.php file and  new private message
 *
 * @param void
 * @return string : HTML code.
 */
function nkHandle_alert() {
    global $nuked, $user, $visiteur;

    $html = '';

    if ($visiteur == 9 && $_REQUEST['file'] != 'Admin' && $_REQUEST['page'] != 'admin') {
        if ($nuked['nk_status'] == 'closed')
            $html .= applyTemplate('nkAlert/nkSiteClosedLogged');

        if (is_dir('INSTALL/'))
            $html .= applyTemplate('nkAlert/nkInstallDirTrue');

        if (file_exists('install.php') || file_exists('update.php'))
            $html .= applyTemplate('nkAlert/nkInstallFileTrue');
    }

    if ($user && $user['nbNewPM'] > 0 && ! isset($_COOKIE['popup'])
        && ! in_array($_REQUEST['file'], array('User', 'Userbox', 'Admin'))
        && $_REQUEST['page'] != 'admin'
    )
        $html .= applyTemplate('nkAlert/nkNewPrivateMsg');

    return $html;
}

function getRequestVars() {
    $vars = array();

    $request = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST : $_GET;

    foreach (func_get_args() as $varName)
        $vars[] = (array_key_exists($varName, $request)) ? trim($request[$varName]) : '';

    return $vars;
}

// FUNCTIONS TO FIX COMPATIBILITY WITH PHP5.4

function nkHtmlEntityDecode($string, $flags = NK_HTML_DEFAULT_FLAGS) {
    return html_entity_decode($string, $flags, 'ISO-8859-1');
}

function nkHtmlSpecialChars($string, $flags = NK_HTML_DEFAULT_FLAGS) {
    return htmlspecialchars($string, $flags, 'ISO-8859-1');
}

function nkHtmlEntities($string, $flags = NK_HTML_DEFAULT_FLAGS) {
    return htmlentities($string, $flags, 'ISO-8859-1');
}

// FUNCTION TO FIX PRINTING TAGS
function printSecuTags($value) {
    return nkHtmlEntities(nkHtmlEntityDecode(nkHtmlEntityDecode($value)));
}

function nkDate($timestamp, $block = false) {
    global $nuked, $language;

    if(array_key_exists('IsBlok', $nuked)){
        $isBlock = $nuked['IsBlok'];
    }
    else{
        $isBlock = false;
    }

    $format = ((($block === false) ? $isBlock : $block) === true) ? ($language == 'french') ? '%d/%m/%Y' : '%m/%d/%Y' : $nuked['dateformat'];
    // iconv pour eviter les caracteres speciaux dans la date
    return iconv('UTF-8','ISO-8859-1',strftime($format, $timestamp));
    //return iconv('UTF-8','ISO-8859-1',utf8_encode(strftime($format, $timestamp))); // For Windows servers

}

// CURRENT ANNUAL DATEZONE TIME TABLE
function getTimeZoneDateTime($GMT) {
    $timezones = array(
        '-1200'=>'Pacific/Kwajalein',
        '-1100'=>'Pacific/Samoa',
        '-1000'=>'Pacific/Honolulu',
        '-0900'=>'America/Juneau',
        '-0800'=>'America/Los_Angeles',
        '-0700'=>'America/Denver',
        '-0600'=>'America/Mexico_City',
        '-0500'=>'America/New_York',
        '-0400'=>'America/Caracas',
        '-0330'=>'America/St_Johns',
        '-0300'=>'America/Argentina/Buenos_Aires',
        '-0200'=>'Atlantic/Azores',
        '-0100'=>'Atlantic/Azores',
        '+0000'=>'Europe/London',
        '+0100'=>'Europe/Paris',
        '+0200'=>'Europe/Helsinki',
        '+0300'=>'Europe/Moscow',
        '+0330'=>'Asia/Tehran',
        '+0400'=>'Asia/Baku',
        '+0430'=>'Asia/Kabul',
        '+0500'=>'Asia/Karachi',
        '+0530'=>'Asia/Calcutta',
        '+0600'=>'Asia/Colombo',
        '+0700'=>'Asia/Bangkok',
        '+0800'=>'Asia/Singapore',
        '+0900'=>'Asia/Tokyo',
        '+0930'=>'Australia/Darwin',
        '+1000'=>'Pacific/Guam',
        '+1100'=>'Asia/Magadan',
        '+1200'=>'Asia/Kamchatka'
    );
    return $timezones[$GMT];
}

// OPEN PHP SESSION
function session_open($path, $name) {
    return true;
}

// CLOSE PHP SESSION
function session_close() {
    return true;
}

// READ PHP SESSION
function session_read($id) {
    connect();

    $dbsSession = nkDB_selectOne(
        'SELECT session_vars
        FROM '. TMPSES_TABLE .'
        WHERE session_id = '. nkDB_escape($id)
    );

    if ($dbsSession && array_key_exists('session_vars', $dbsSession))
        return $dbsSession['session_vars'];

    return '';

}

// WRITE PHP SESSION
function session_write($id, $data) {
    connect();

    $dbiSession = nkDB_insert(TMPSES_TABLE, array(
        'session_id'    => $id,
        'session_start' => time(),
        'session_vars'  => $data
    ));

    $dbuSession = false;

    if ($dbiSession === false || nkDB_insert_id() == 0) {
        $dbuSession = nkDB_update(TMPSES_TABLE, array(
                'session_vars' => $data
            ),
            'session_id = '. nkDB_escape($id)
        );
    }

    return ($dbiSession !== false || $dbuSession !== false);
}

// DELETE PHP SESSION
function session_delete($id) {
    connect();

    return nkDB_delete(TMPSES_TABLE, 'session_id = '. nkDB_escape($id));
}

// KILL DEAD SESSION
function session_gc($maxlife) {
    connect();

    nkDB_delete(TMPSES_TABLE, 'session_start < '. time() - $maxlife);

    return true;
}

/**
 * Open a connection to a database server
 *
 * @param void
 * @return void
 */
function connect() {
    global $global;

    require_once 'Includes/nkDb/nkDB_MySQL.php';

    nkDB_init($global);

    if (($db = nkDB_connect()) === false) {
        $error = nkDB_getConnectError();

        // TODO : More error message ? ( DB_HOST_ERROR, DB_LOGIN_ERROR & DB_CHARSET_ERROR )
        if ($error == 'DB_NAME_ERROR')
            exit(ERROR_QUERYDB);
        else
            exit(ERROR_QUERY);
    }
}

/**
 * Redirect if current user / visitor is banned
 */
function nkHandle_bannedUser() {
    global $user_ip, $user;

    $userName = ($user) ? $user['name'] : '';

    // On supprime le dernier chiffre pour les IP's dynamiques
    $ip_dyn = substr($user_ip, 0, -1);

    // Condition SQL : IP dynamique ou compte
    $where_query = '`ip` LIKE \'%'. nkDB_escape($ip_dyn, true) .'%\' OR `pseudo` = '. nkDB_escape($userName);

    // Recherche d'un banissement
    $ban = nkDB_selectMany(
        'SELECT `id`, `pseudo`, `date`, `dure`
        FROM '. BANNED_TABLE .' 
        WHERE '. $where_query
    );

    // Si resultat positif a la recherche d'un bannissement
    if (nkDB_numRows() > 0) {
        // Nouvelle adresse IP
        $banned_ip = $user_ip;
    }
    // Recherche d'un cookie de banissement
    else if (isset($_COOKIE['ip_ban']) && !empty($_COOKIE['ip_ban'])) {
        // On supprime le dernier chiffre de l'adresse IP contenu dans le cookie
        $ip_dyn2 = substr($_COOKIE['ip_ban'], 0, -1);

        // On verifie l'adresse IP du cookie et l'adresse IP actuelle
        if($ip_dyn2 == $ip_dyn) {
            // On verifie l'existance du bannissement, si resultat positif, on fait un nouveau ban
            if (nkDB_totalNumRows('FROM '. BANNED_TABLE .' WHERE `ip` LIKE \'%'. $ip_dyn2 .'%\'') > 0)
                $banned_ip = $user_ip;
        }
    }
    else {
        $banned_ip = '';
    }

    // Suppression des banissements depasses ou mise a jour de l'IP
    if (! empty($banned_ip)) {
        // Recherche banissement depasse
        if ($ban['dure'] != 0 && ($ban['date'] + $ban['dure']) < time()) {
            // Suppression bannissement
            nkDB_delete(BANNED_TABLE, '`ip` LIKE \'%'. nkDB_escape($ip_dyn, true) .'%\' OR `pseudo` = '. nkDB_escape($userName));

            // Notification dans l'administration
            nkDB_insert(NOTIFICATIONS_TABLE, array(
                'date'  => time(),
                'type'  => 4,
                'texte' => $pseudo . _BANFINISHED
            ));
        }
        // Sinon on met a jour l'IP
        else {
            $data = array('ip' => $user_ip);

            if ($user) $data['pseudo'] = $user['name'];

            nkDB_update(BANNED_TABLE, $data, $where_query);

            // Redirection vers la page de banissement
            $url_ban = 'ban.php?ip_ban='. $banned_ip;

            if (! empty($user))
                $url_ban .= '&user='. urlencode($user['name']);

            redirect($url_ban, 0);
        }
    }
}

// QUERY IMAGE, BLOCK ALL IMAGE FILE (PHP, HTML ..)
function checkimg($url){
    $url = rtrim($url);
    $ext = strrchr($url, '.');
    $ext = substr($ext, 1);

    if (!preg_match('#\.(([a-z]?)htm|php)#i', $url) && substr($url, -1) != '/' && preg_match('#jpg|jpeg|gif|png|bmp#i', $ext) )
        return $url;
    else
        return 'images/noimagefile.gif';
}

function getSmiliesList() {
    static $smiliesList;

    if ($smiliesList) return $smiliesList;

    $smiliesList = nkDB_selectMany(
        'SELECT code, url, name
        FROM '. SMILIES_TABLE,
        array('id')
    );

    return $smiliesList;
}

/**
 * Replace smilies in text
 * @param array $matches : text to parse
 * @return string : parsing text
 */
function replaceSmilies($matches) {
    return preg_replace('#<img class="nkSmilie" src=\"(.*)\" alt=\"(.*)\" title=\"(.*)\" />#Usi', '$2', $matches[0]);
}

// DISPLAYS SMILEYS
function icon($text) {
    $text = str_replace('mailto:', 'mailto!', $text);
    $text = str_replace('http://', '_http_', $text);
    $text = str_replace('https://', '_https_', $text);
    $text = str_replace('&quot;', '_QUOT_', $text);
    $text = str_replace('&#039;', '_SQUOT_', $text);

    foreach (getSmiliesList() as $smilie) {
        $text = str_replace($smilie['code'],
            '<img class="nkSmilie" src="images/icones/'. $smilie['url'] .'" alt="'
            . $smilie['code'] .'" title="'. nkHtmlEntities($smilie['name']) .'" />',
            $text
        );
    }

    $text = str_replace('mailto!', 'mailto:', $text);
    $text = str_replace('_http_', 'http://', $text);
    $text = str_replace('_https_', 'https://', $text);
    $text = str_replace('_QUOT_', '&quot;', $text);
    $text = str_replace('_SQUOT_', '&#039;', $text);

    // Light calculation if <pre> tag is not present in text
    if (strpos($text, '<pre') !== false)
        $text = preg_replace_callback('#<pre(.*)>(.*)<\/pre>#Uis', 'replaceSmilies', $text);

    return $text;
}

// SEARCH SMILIES FOR CKEDITOR.
function ConfigSmileyCkeditor() {
    $smiliesList = getSmiliesList();

    $smiliesCodeList    = addslashes(implode('\', \'', array_values(array_column($smiliesList, 'code'))));
    $smiliesUrlList     = implode('\', \'', array_values(array_column($smiliesList, 'url')));
    $smiliesNameList    = nkHtmlEntities(implode('\', \'', array_values(array_column($smiliesList, 'name'))));

    return 'CKEDITOR.config.smiley_path=\'images/icones/\';'
        . 'CKEDITOR.config.smiley_images=[\''. $smiliesUrlList .'\'];'
        . 'CKEDITOR.config.smiley_descriptions=[\''. $smiliesCodeList .'\'];'
        . 'CKEDITOR.config.smiley_titles=[\''. $smiliesNameList .'\'];';
}

// SECURITY FOR HTTP LINKS.
function secu_url($url){
    $info = parse_url(strtolower($url));
    if ($info !== false){
        return strrchr($info['path'], '.') != '.php'
            && (!isset($info['query']) || $info['query'] == '');
    } else{
        return false;
    }
}

// CSS FILTER
function secu_css($Style){
    $AllowedProprieties = array(
        'display',
        'margin-left',
        'margin-right',
        'float',
        'padding',
        'text-decoration',
        'text-align',
        'color',
        'align',
        'vertical-align',
        'margin',
        'border',
        'background-color',
        'background',
        'width',
        'height',
        'border-color',
        'background-image',
        'border-width',
        'border-style',
        'padding-left',
        'padding-right',
        'font-size',
        'font-family',
        'page-break-after'
    );
    $Style = explode(';', $Style);
    $Style = array_map('trim', $Style);

    foreach ($Style as $id=>$Element){
        preg_match('/ *([^ :]+) *: *(( |.)*)/', $Element, $Phased);
        if (!in_array($Phased[1], $AllowedProprieties)){
            unset($Style[$id]);
        } elseif (preg_match('/url *\\( *\'?"? *([^ \'"]+) *"?\'?\\)/', $Element, $Phased) > 0){
            if (!secu_url($Phased[1])){
                unset($Style[$id]);
            }
        }
    }
    return implode(';', $Style);
}

// HTML FILTER
function secu_args($matches){
    global $nuked;

    $allowedTags = array(
        'p' => array(
            'style',
            'dir'
        ),
        'h1' => array(
            'style'
        ),
        'h2' => array(
            'style'
        ),
        'h3' => array(
            'style'
        ),
        'h4' => array(
            'style'
        ),
        'h5' => array(
            'style'
        ),
        'h6' => array(
            'style'
        ),
        'img' => array(
            'alt',
            'class',
            'dir',
            'id',
            'lang',
            'longdesc',
            'src',
            'style',
            'title',
            'width',
            'height',
            'border'
        ),
        'strong' => array(),
        's' => array(),
        'em' => array(),
        'u' => array(),
        'strike' => array(),
        'sub' => array(),
        'sup' => array(),
        'ol' => array(),
        'ul' => array(),
        'li' => array(),
        'blockquote' => array(
            'style'
        ),
        'div' => array(
            'class',
            'id',
            'lang',
            'style',
            'title',
            'align'
        ),
        'br' => array(),
        'a' => array(
            'accesskey',
            'charset',
            'class',
            'dir',
            'href',
            'id',
            'lang',
            'name',
            'rel',
            'style',
            'tabindex',
            'target',
            'title',
            'type'
        ),
        'table' => array(
            'align',
            'border',
            'cellpadding',
            'cellspacing',
            'class',
            'dir',
            'id',
            'style',
            'summary'
        ),
        'caption' => array(),
        'thead' => array(),
        'tr' => array(
            'style'
        ),
        'td' => array(
            'style',
            'colspan',
            'rowspan'
        ),
        'th' => array(
            'scope'
        ),
        'tbody' => array(),
        'hr' => array(),
        'span' => array(
            'id',
            'style',
            'dir'
        ),
        'big' => array(),
        'small' => array(),
        'tt' => array(),
        'code' => array(),
        'kbd' => array(),
        'samp' => array(),
        'var' => array(),
        'del' => array(),
        'ins' => array(),
        'cite' => array(),
        'q' => array(),
        'pre' => array(
            'class'
        ),
        'address' => array()
    );

        // FOR VIDEO PLUGIN -- POUR PLUGIN VIDEO
    $TabVideo = array(
        'object' => array(
            'width',
            'height',
            'data',
            'type'
        ),
        'param' => array(
            'name',
            'value'
        ),
        'embed' => array(
            'allowfullscreen',
            'allowscriptaccess',
            'height',
            'src',
            'type',
            'width'
        ),
        /*'iframe' => array (
            'src',
            'width',
            'height',
            'frameborder',
        ),*/

    );

    $allowedTags = ($nuked['video_editeur'] == 'on') ? array_merge($allowedTags, $TabVideo) : $allowedTags;

    if (in_array(strtolower($matches[1]), array_keys($allowedTags))) {
        preg_match_all('/([^ =]+)=(&quot;((.(?<!&quot;))*)|[^ ]+)/', $matches[2], $args);

        //Supprime les attributs interdit
        foreach ($args[1] as $id=>$attribute){
            if (!in_array($attribute, $allowedTags[$matches[1]]))
                foreach ($args as $part=>$g)
                    unset($args[$part][$id]);
        }

        //Met en forme les attributs restants
        foreach ($args[2] as $id=>$val){
            $args[1][$id] = trim(strtolower($args[1][$id]));
            $val = trim($val);
            if (preg_match('/^&quot;/', $val, $g))
                $val .= ';';
            $args[2][$id] = trim(nkHtmlEntityDecode($val), " \t\n\r\0\"");
            if ($args[1][$id] == 'style'){
                $args[2][$id] = secu_css($args[2][$id]);
            }
            elseif ($matches[1] == 'img' && $args[1][$id] == 'src'){
                if(!secu_url($args[2][$id]))
                    $args[2][$id] = 'images/noimagefile.gif';
            }
        }

        $RetStr = '<' . $matches[1];
        foreach ($args[1] as $id=>$attribute){
            $RetStr .= ' ' . $attribute . '="' . $args[2][$id] . '"';
        }
        if (array_key_exists(3,$matches) && $matches[3] == '/'){
            $RetStr .= ' />';
        }
        else{
            $RetStr .= '>';
        }
        return $RetStr;

    // Balise de fermeture
    }
    else if (substr($matches[1], 0, 1) == '/' && in_array(strtolower(substr($matches[1], 1)), array_keys($allowedTags))){
        return '<' . $matches[1] . '>';
    // Balises interdites
    }
    else{
        return $matches[0];
    }
}

// DISPLAY CONTENT WITH SECURITY CSS AND HTML ($texte)
function secu_html($texte){
    global $bgcolor3, $nuked, $language;

    // Balise HTML interdite
    $texte = str_replace(array('&lt;', '&gt;', '&quot;'), array('<', '>', '"'), $texte);
    $texte = stripslashes($texte);
    $texte = nkHtmlSpecialChars($texte);
    $texte = str_replace('&amp;', '&', $texte);
    $texte = str_replace('&lt;3', nkHtmlEntities('&lt;3'), $texte);

    // Balise autorisee
    $texte = preg_replace_callback('/&lt;([^ &]+)[[:blank:]]?((.(?<!&gt;))*)&gt;/', 'secu_args', $texte);
    $texte = str_replace('&amp;lt;3', '&lt;3', $texte);
    $arrayOnly1Tag = array('img', 'br', 'hr');

    if($nuked['video_editeur'] == 'on'){
        $arrayOnly1Tag[] = 'param';
    }

    $allowedTags = array(
        'p','h1','h2','h3','h4','h5','h6','img','strong','s','em','u','strike','sub','sup','ol','ul','li','blockquote','div','br','a','table','caption','thead','tr','td','th','tbody','hr','span','big','small','tt','code','kbd','samp','var','del','ins','cite','q','pre','address', 'object', 'param', 'embed'
        );

    if($nuked['video_editeur'] == 'on'){
        $allowedTags[] = 'object';
        $allowedTags[] = 'param';
        $allowedTags[] = 'embed';
    }

    preg_match_all('`<(/?)([^/ >]+)(| [^>]*([^/]))>`', $texte, $Tags, PREG_SET_ORDER);

    $TagList = array();
    $bad = false;
    $size = count($Tags);
    for($i=0; $i<$size; $i++) {
        $Tags[$i][4] = (isset($Tags[$i][4])) ? $Tags[$i][4] : '';
        $TagName = ($Tags[$i][3] == '') ? $Tags[$i][2] . $Tags[$i][4] : $Tags[$i][2];

        if(!in_array($TagName, $arrayOnly1Tag) && in_array($TagName, $allowedTags)){
            if ($Tags[$i][1] == '/'){
                $bad = $bad | array_pop($TagList) != $TagName;
            }
            else{
                array_push($TagList, $TagName);
            }
        }
    }
    $bad = $bad | count($TagList) > 1;
    if ($bad){
        return(nkHtmlSpecialChars($texte));
    }
    else{
        return $texte;
    }
}

function editPhpCkeditor($text){
    return str_replace('&lt;?php', nkHtmlEntities('&lt;?php'), $text);
}

// REDIRECT AFTER ($delay) SECONDS TO ($url)
function redirect($url, $delay = 0) {
    if ($delay == 0) {
        nkDB_disconnect();
        header('location:'. $url);
        exit;
    }

    echo '<script type="text/javascript">',"\n"
    , '<!--',"\n"
    , "\n"
    , 'function redirect() {',"\n"
    , 'window.location=\'' , $url , '\'',"\n"
    , "}\n"
    , 'setTimeout(\'redirect()\',\'' , ($delay * 1000) ,'\');',"\n"
    , "\n"
    , '// -->',"\n"
    , '</script>',"\n";
}

// DISPLAYS THE NUMBER OF PAGES
function number($count, $each, $link, $return = false) {

    if(array_key_exists('p', $_REQUEST)){
        $current = $_REQUEST['p'];
    }
    else{
        $current = '';
    }


    if ($each > 0){
        if ($count <= 0)     $count   = 1;
        if (empty($current)) $current = 1; // On renormalise la page courante...
        // Calcul du nombre de pages
        $n = ceil($count / intval($each)); // on arrondit a  l'entier sup.
        // Debut de la chaine d'affichage
        $output = '<b class="pgtitle">' . _PAGE . ' :</b> ';

        for ($i = 1; $i <= $n; $i++){
            if ($i == $current){
                $output .= sprintf('<b class="pgactuel">%d</b> ',$i    );
            }
            // On est autour de la page actuelle : on affiche
            elseif (abs($i - $current) <= 4){
                $output .= sprintf('<a href="' . $link . '&amp;p=%d" class="pgnumber">%d</a> ',$i, $i);
            }
            // On affiche quelque chose avant d'omettre les pages inutiles
            else{
                // On est avant la page courante
                if (!isset($first_done) && $i < $current){
                    $output .= sprintf('...<a href="' . $link . '&amp;p=%d" title="' . _PREVIOUSPAGE . '" class="pgback">&laquo;</a> ',$current-1);
                    $first_done = true;
                }
                // Apres la page courante
                elseif (!isset($last_done) && $i > $current){
                    $output .= sprintf('<a href="' . $link . '&amp;p=%d" title="' . _NEXTPAGE . '" class="pgnext">&raquo;</a>... ',$current+1);
                    $last_done = true;
                }
                // On a depasse les cas qui nous interessent : inutile de continuer
                elseif ($i > $current)
                    break;
            }
        }
        $output .= '<br />';

        if ($return)
            return $output;
        else
            echo $output;
    }
}

/**
 * Count the number of visitors present
 *
 * @param void
 * @return array with the number of visitors
 *  [0] = visitors
 *  [1] = members
 *  [2] = admin
 *  [3] = members + admin;
 *  [4] = visitors + members + admin
 */
function nbvisiteur() {
    global $user, $visiteur, $nuked, $user_ip;

    static $visitorStats = array();

    if ($visitorStats) return $visitorStats;

    $time   = time();
    $limit  = $time + $nuked['nbc_timeout'];

    nkDB_delete(NBCONNECTE_TABLE, 'date < '. $time);
 
    if ($user_ip != '') {
        if ($user)
            $whereClause = 'user_id = '. nkDB_escape($user['id']);
        else
            $whereClause = 'IP = '. nkDB_escape($user_ip);

        $connectData = array(
            'date'      => $limit,
            'type'      => $visiteur,
            'username'  => (($user) ? $user['name'] : 'visitor')
        );

        if (nkDB_totalNumRows('FROM '. NBCONNECTE_TABLE .' WHERE '. $whereClause) > 0) {
            if ($user) {
                $connectData['IP'] = $user_ip;
                nkDB_update(NBCONNECTE_TABLE, $connectData, 'user_id = '. nkDB_escape($user['id']));
            }
            else {
                $connectData['user_id'] = '';
                nkDB_update(NBCONNECTE_TABLE, $connectData, 'IP = '. nkDB_escape($user_ip));
            }
        }
        else {
            nkDB_delete(NBCONNECTE_TABLE, 'IP = '. nkDB_escape($user_ip));

            $connectData['IP'] = $user_ip;
            $connectData['user_id'] = ($user) ? $user['id'] : '';
            nkDB_insert(NBCONNECTE_TABLE, $connectData);
        }
    }

    $visitorStats[0] = nkDB_totalNumRows('FROM '. NBCONNECTE_TABLE .' WHERE type = 0');
    $visitorStats[1] = nkDB_totalNumRows('FROM '. NBCONNECTE_TABLE .' WHERE type BETWEEN 1 AND 2');
    $visitorStats[2] = nkDB_totalNumRows('FROM '. NBCONNECTE_TABLE .' WHERE type > 2');
    $visitorStats[3] = $visitorStats[1] + $visitorStats[2];
    $visitorStats[4] = $visitorStats[0] + $visitorStats[3];

    return $visitorStats;
}

/**
 * Return user level of module
 *
 * @param string $module : Module to check
 * @return mixed : Numeric user level of module (0 to 9),
 *         -1 if module is disabled and false if module isn't registred in modules table
 */
function nivo_mod($moduleName) {
    static $moduleUserLevelList = array();

    if (array_key_exists($moduleName, $moduleUserLevelList))
        return $moduleUserLevelList[$moduleName];

    $dbsModules = nkDB_selectOne(
        'SELECT niveau
        FROM '. MODULES_TABLE .'
        WHERE nom = '. nkDB_escape($moduleName)
    );

    $moduleUserLevelList[$moduleName] = (isset($dbsModules['niveau'])) ? $dbsModules['niveau'] : false;

    return $moduleUserLevelList[$moduleName];
}

/**
 * Return admin level of module
 *
 * @param string $module : Module to check
 * @return mixed : Numeric admin level of module (0 to 9),
 *         -1 if module is disabled and false if module isn't registred in modules table
 */
function admin_mod($moduleName) {
    static $moduleAdminLevelList = array();

    if (array_key_exists($moduleName, $moduleAdminLevelList))
        return $moduleAdminLevelList[$moduleName];

    $dbsModules = nkDB_selectOne(
        'SELECT admin
        FROM '. MODULES_TABLE .'
        WHERE nom = '. nkDB_escape($moduleName)
    );

    $moduleAdminLevelList[$moduleName] = (isset($dbsModules['admin'])) ? $dbsModules['admin'] : false;

    return $moduleAdminLevelList[$moduleName];
}

/**
 * Initializes admin of module
 *
 * @param string $module : Module to initialize
 * @return bool : Admin of module initialization result
 */
function adminInit($module, $adminPageLevel = false) {
    global $language, $visiteur;

    require_once 'modules/Admin/design.php';
    nkTemplate_setInterface('backend');
    adminDesignInit();
    translate('modules/Admin/lang/'. $language .'.lang.php');
    translate('modules/'. $module .'/lang/'. $language .'.lang.php');

    // Get admin level of module
    if ($module == 'Admin') {
        if (is_int($adminPageLevel))
            $adminLevel = $adminPageLevel;
        else
            die('You must defined access level for this page of Admin module !');
    }
    else
        $adminLevel = admin_mod($module);

    // User has the required level
    if ($visiteur >= $adminLevel && $adminLevel > -1) {
        return true;
    }
    // Module disabled
    elseif ($adminLevel == -1) {
        printNotification(_MODULEOFF, 'error', 'javascript:history.back()');
    }
    // User logged in, but not the rights
    elseif ($visiteur > 1) {
        printNotification(_NOENTRANCE, 'error', 'javascript:history.back()');
    }
    // User not logged
    else {
        printNotification(_ZONEADMIN, 'error', 'javascript:history.back()');
    }

    return false;
}

/**
 * Initializes module
 *
 * @param string $module : Module to initialize
 * @return bool : Module initialization result
 */
function moduleInit($module) {
    global $language, $file, $visiteur;

    nkTemplate_setInterface('frontend');
    nkTemplate_init($module);
    translate('modules/'. $module .'/lang/'. $language .'.lang.php');

    $moduleLevel = nivo_mod($module);

    // User has the required level
    if ($moduleLevel === false || ($visiteur >= $moduleLevel && $moduleLevel > -1)) {
        return true;
    }
    // Module disabled
    elseif ($moduleLevel == -1) {
        opentable();
        echo applyTemplate('nkAlert/moduleOff');
        closetable();
    }
    // No access for visitors
    elseif ($moduleLevel == 1 && $visiteur == 0) {
        opentable();
        echo applyTemplate('nkAlert/userEntrance');
        closetable();
    }
    // User not logged in
    else {
        opentable();
        echo applyTemplate('nkAlert/noEntrance');
        closetable();
    }

    return false;
}

/**
 * Including langage file
 *
 * @param string $languageFile : Langage file to load
 * @return void
 */
function translate($languageFile) {
    global $nuked, $arrayModLang;

    ob_start();
    $newArrayModLang = include_once $languageFile;
    ob_end_clean();

    if (is_array($newArrayModLang))
        $arrayModLang = array_merge($arrayModLang, $newArrayModLang);
}

/**
 * Get translation of string
 *
 * @param string $str : The string to translate
 * @return string : Translation if it exists or if an empty string
 */
function __($str, $n = 1) {
    global $arrayModLang;

    if (array_key_exists($str, $arrayModLang))
        return $arrayModLang[$str];

    return $str;
}

/**
 * Get translation of string
 *
 * @param string $str : The string to translate
 * @param int $n : The 
 * @return string : Translation if it exists or if an empty string
 */
function _n($str, $n = 1) {
    global $arrayModLang;

    if (array_key_exists($str, $arrayModLang) && is_array($arrayModLang[$str])) {
        if ($n > 0 && array_key_exists($n, $arrayModLang[$str]))
            return $arrayModLang[$str][$n];
    }

    return $str;
}

/**
 * Count the number of page views module for stats
 *
 * @param string $module : Module to update page view stats
 * @return void
 */
function compteur($module) {
    nkDB_update(STATS_TABLE,
        array('count' => array('count + 1', 'no-escape')),
        'type = \'pages\' AND nom = '. nkDB_escape($module)
    );
}

function nk_CSS($str) {
    if ($str != '') {
        $str = str_replace('content-disposition:','&#99;&#111;&#110;&#116;&#101;&#110;&#116;&#45;&#100;&#105;&#115;&#112;&#111;&#115;&#105;&#116;&#105;&#111;&#110;&#58;', $str);
        $str = str_replace('content-type:','&#99;&#111;&#110;&#116;&#101;&#110;&#116;&#45;&#116;&#121;&#112;&#101;&#58;', $str);
        $str = str_replace('content-transfer-encoding:','&#99;&#111;&#110;&#116;&#101;&#110;&#116;&#45;&#116;&#114;&#97;&#110;&#115;&#102;&#101;&#114;&#45;&#101;&#110;&#99;&#111;&#100;&#105;&#110;&#103;&#58;', $str);
        $str = str_replace('include','&#105;&#110;&#99;&#108;&#117;&#100;&#101;', $str);
        $str = str_replace('script','&#115;&#99;&#114;&#105;&#112;&#116;', $str);
        $str = str_replace('eval','&#101;&#118;&#97;&#108;', $str);
        $str = str_replace('javascript','&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;', $str);
        $str = str_replace('embed','&#101;&#109;&#98;&#101;&#100;', $str);
        $str = str_replace('iframe','&#105;&#102;&#114;&#97;&#109;&#101;', $str);
        $str = str_replace('refresh', '&#114;&#101;&#102;&#114;&#101;&#115;&#104;', $str);
        $str = str_replace('onload', '&#111;&#110;&#108;&#111;&#97;&#100;', $str);
        $str = str_replace('onstart', '&#111;&#110;&#115;&#116;&#97;&#114;&#116;', $str);
        $str = str_replace('onerror', '&#111;&#110;&#101;&#114;&#114;&#111;&#114;', $str);
        $str = str_replace('onabort', '&#111;&#110;&#97;&#98;&#111;&#114;&#116;', $str);
        $str = str_replace('onblur', '&#111;&#110;&#98;&#108;&#117;&#114;', $str);
        $str = str_replace('onchange', '&#111;&#110;&#99;&#104;&#97;&#110;&#103;&#101;', $str);
        $str = str_replace('onclick', '&#111;&#110;&#99;&#108;&#105;&#99;&#107;', $str);
        $str = str_replace('ondblclick', '&#111;&#110;&#100;&#98;&#108;&#99;&#108;&#105;&#99;&#107;', $str);
        $str = str_replace('onfocus', '&#111;&#110;&#102;&#111;&#99;&#117;&#115;', $str);
        $str = str_replace('onkeydown', '&#111;&#110;&#107;&#101;&#121;&#100;&#111;&#119;&#110;', $str);
        $str = str_replace('onkeypress', '&#111;&#110;&#107;&#101;&#121;&#112;&#114;&#101;&#115;&#115;', $str);
        $str = str_replace('onkeyup', '&#111;&#110;&#107;&#101;&#121;&#117;&#112;', $str);
        $str = str_replace('onmousedown', '&#111;&#110;&#109;&#111;&#117;&#115;&#101;&#100;&#111;&#119;&#110;', $str);
        $str = str_replace('onmousemove', '&#111;&#110;&#109;&#111;&#117;&#115;&#101;&#109;&#111;&#118;&#101;', $str);
        $str = str_replace('onmouseover', '&#111;&#110;&#109;&#111;&#117;&#115;&#101;&#111;&#118;&#101;&#114;', $str);
        $str = str_replace('onmouseout', '&#111;&#110;&#109;&#111;&#117;&#115;&#101;&#111;&#117;&#116;', $str);
        $str = str_replace('onmouseup', '&#111;&#110;&#109;&#111;&#117;&#115;&#101;&#117;&#112;', $str);
        $str = str_replace('onreset', '&#111;&#110;&#114;&#101;&#115;&#101;&#116;', $str);
        $str = str_replace('onselect', '&#111;&#110;&#115;&#101;&#108;&#101;&#99;&#116;', $str);
        $str = str_replace('onsubmit', '&#111;&#110;&#115;&#117;&#98;&#109;&#105;&#116;', $str);
        $str = str_replace('onunload', '&#111;&#110;&#117;&#110;&#108;&#111;&#97;&#100;', $str);
        $str = str_replace('document', '&#100;&#111;&#99;&#117;&#109;&#101;&#110;&#116;', $str);
        $str = str_replace('cookie', '&#99;&#111;&#111;&#107;&#105;&#101;', $str);
        $str = str_replace('vbscript', '&#118;&#98;&#115;&#99;&#114;&#105;&#112;&#116;', $str);
        $str = str_replace('location', '&#108;&#111;&#99;&#97;&#116;&#105;&#111;&#110;', $str);
        $str = str_replace('object', '&#111;&#98;&#106;&#101;&#99;&#116;', $str);
        $str = str_replace('vbs', '&#118;&#98;&#115;', $str);
        $str = str_replace('href', '&#104;&#114;&#101;&#102;', $str);
        $str = str_replace('src', '&#115;&#114;&#99;', $str);
        $str = str_replace('expression', '&#101;&#120;&#112;&#114;&#101;&#115;&#115;&#105;&#111;&#110;', $str);
        $str = str_replace('alert', '&#97;&#108;&#101;&#114;&#116;', $str);
    }

    return $str;
}

/**
 * Manage visitor stats
 *
 * @param void
 * @return void
 */
function visits() {
    global $nuked, $user_ip, $user;

    $time       = time();
    $timeVisit  = $nuked['visit_delay'] * 60;
    $visitLimit = $time + $timeVisit;

    $whereClause = ($user) ? 'user_id = '. nkDB_escape($user['id']) : 'ip = '. nkDB_escape($user_ip);

    $dbsVisitorStats = nkDB_selectOne(
        'SELECT id, date
        FROM '. STATS_VISITOR_TABLE .'
        WHERE ' . $whereClause,
        array('date'), 'DESC', 1
    );

    if (! empty($dbsVisitorStats) && $dbsVisitorStats['id'] && $dbsVisitorStats['date'] > $time) {
        nkDB_update(STATS_VISITOR_TABLE,
            array('date' => $visitLimit),
            'id = '. nkDB_escape($dbsVisitorStats['id'])
        );
    }
    else {
        $month      = strftime('%m', $time);
        $year       = strftime('%Y', $time);
        $day        = strftime('%d', $time);
        $hour       = strftime('%H', $time);
        $browser    = getBrowser();
        $os         = getOS();
        $user_host  = strtolower(@gethostbyaddr($user_ip));

        if ($user_host == $user_ip)
            $host = '';
        else {
            if (preg_match('`([^.]{1,})((\.(co|com|net|org|edu|gov|mil))|())((\.(ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|fi|fj|fk|fm|fo|fr|fx|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zr|zw))|())$`', $user_host, $res))
                $host = $res[0];
        }

        nkDB_insert(STATS_VISITOR_TABLE, array(
            'user_id'   => $user['id'],
            'ip'        => $user_ip,
            'host'      => $host,
            'browser'   => $browser,
            'os'        => $os,
            'referer'   => $_SERVER['HTTP_REFERER'],
            'day'       => $day,
            'month'     => $month,
            'year'      => $year,
            'hour'      => $hour,
            'date'      => $visitLimit
        ));
    }
}

function nkNickname($data, $link = true, $rankColor = true, $author = 'auteur', $pseudo = 'pseudo', $rank = 'rang') {
    global $nuked;

    if (is_array($data)) {
        if (isset($data[$pseudo]) && $data[$pseudo] != '') {
            if (! $link) return $data[$pseudo];

            $style = '';

            // TODO : Use CSS class instead
            if ($rankColor && $nuked['forum_user_details'] == 'on') {
                $teamRank = getTeamRank();

                if (array_key_exists($data[$rank], $teamRank))
                    $style = ' style="color: #'. $teamRank[$data[$rank]]['color'] .';"';
            }

            return '<a href="index.php?file=Members&amp;op=detail&amp;autor='. urlencode($data[$pseudo]) .'"'. $style .'>'
                . $data[$pseudo] .'</a>';
        }

        if (isset($data[$author]) && $data[$author] != '')
            return nk_CSS($data[$author]);
    }

    return '';
}

/**
 * Check if pseudo is conform (no empty & no special characters), not used and not banned
 *
 * @param string $pseudo : The pseudo to check
 * @return string : Pseudo string trimmed
 */
function verif_pseudo($string = null, $oldString = null, $maxLength = 30) {
    $string = trim($string);

    if (empty($string) || preg_match('`[\$\^\(\)\'"?%#<>,;:]`', $string)) {
        return 'error1';
    }

    $escapeString = nkDB_escape($string);

    if ($string != $oldString) {
        $isUsed = nkDB_totalNumRows('FROM '. USER_TABLE .' WHERE pseudo = '. $escapeString);

        if ($isUsed > 0)
            return 'error2';
    }

    $isBanned = nkDB_totalNumRows('FROM '. BANNED_TABLE .' WHERE pseudo = '. $escapeString);

    if ($isBanned > 0)
        return 'error3';

    if (strlen($string) > $maxLength) return 'error4';

    return $string;
}

/**
 * Return the error of verif pseudo
 *
 * @param string $error : Error alias returned by check pseudo
 * @return string : Error message
 */
function getCheckPseudoError($error) {
    switch ($error) {
        case 'error1' :
            return _PSEUDOFAILDED;
            break;

        case 'error2' :
            return _RESERVNICK;
            break;

        case 'error3' :
            return _BANNEDNICK;
            break;

        case 'error4' :
            return _NICKTOLONG;
            break;
    }

    return false;
}

/**
 * Get the Operating System of user
 *
 * @param void
 * @return string : The Operating System detected
 */
function getOS() {
    $userAgent  = strtolower($_SERVER['HTTP_USER_AGENT']);
    $os         = 'Autre';

    $osList = array(
        // Windows
        'windows nt 6.1'       => 'Windows 7',
        'windows nt 6.0'       => 'Windows Vista',
        'windows nt 5.2'       => 'Windows Server 2003',
        'windows nt 5.1'       => 'Windows XP',
        'windows nt 5.0'       => 'Windows 2000',
        'windows 2000'         => 'Windows 2000',
        'windows ce'           => 'Windows Mobile',
        'win 9x 4.90'          => 'Windows Me.',
        'windows 98'           => 'Windows 98',
        'windows 95'           => 'Windows 95',
        'win95'                => 'Windows 95',
        'windows nt'           => 'Windows NT',

        // Linux
        'ubuntu'               => 'Linux Ubuntu',
        'fedora'               => 'Linux Fedora',
        'linux'                => 'Linux',

        // Mac
        'macintosh'            => 'Mac',
        'mac os x'             => 'Mac OS X',
        'mac_powerpc'          => 'Mac OS X',

         // Autres
        'freebsd'              => 'FreeBSD',
        'unix'                 => 'Unix',
        'playstation portable' => 'PSP',
        'opensolaris'          => 'SunOS',
        'sunos'                => 'SunOS',
        'nintendo wii'         => 'Nintendo Wii',
        'mac'                  => 'Mac',

        // Search Engines
        'msnbot'               => 'Microsoft Bing',
        'googlebot'            => 'Google Bot',
        'yahoo'                => 'Yahoo Bot'
    );

    foreach ($osList as $k => $v) {
        if (strpos($userAgent, strtolower($k)) !== false) {
            $os = $v;
            break;
        }
    }

    return $os;
}

/**
 * Get the web browser of user
 *
 * @param void
 * @return string : The web browser detected
 */
function getBrowser(){
    $userAgent  = $_SERVER['HTTP_USER_AGENT'];
    $browser    = 'Autre';

    $browserList = array(
        'Firefox'   => 'Firefox',
        'Lynx'      => 'Lynx',
        'Konqueror' => 'Konqueror',
        'Netscape'  => 'Netscape',
        'Opera'     => 'Opera',
        'MSIE'      => 'Internet Explorer',
        'Chrome'    => 'Google Chrome',
        'Safari'    => 'Apple Safari',
        'Mozilla'   => 'Mozilla',

        // Search Engines
        'msnbot'    => 'Microsoft Bing',
        'googlebot' => 'Google Bot',
        'yahoo'     => 'Yahoo Bot'
    );

    foreach ($browserList as $k => $v) {
        if (stripos($userAgent, $k) !== false) {
            $browser = $v;
            break;
        }
    }

    return $browser;
}

// TODO : Bug #82 Sql error handler don't work
function erreursql($errno, $errstr, $errfile, $errline, $errcontext) {
    global $user, $nuked, $language;

    switch ($errno){
        case E_WARNING:
            break;
        case 8192:
            break;
        case 8:
            break;
        default:
            $content = ob_get_clean();
            // CONNECT TO DB AND OPEN SESSION PHP
            if(file_exists('conf.inc.php')) include ('conf.inc.php');
            connect();
            session_name('nuked');
            session_start();
            if (session_id() == '') exit(ERROR_SESSION);
            $date = time();
            echo ERROR_SQL;
            $texte = _TYPE . ': ' . $errno . _SQLFILE . $errfile . _SQLLINE . $errline;
            $upd = nkDB_execute("INSERT INTO " . $nuked['prefix'] . "_erreursql  (`date` , `lien` , `texte`)  VALUES ('" . $date . "', '" . mysql_escape_string($_SERVER["REQUEST_URI"]) . "', '" . $texte . "')");
            $upd2 = nkDB_execute("INSERT INTO " . $nuked['prefix'] . "_notification  (`date` , `type` , `texte`)  VALUES ('".$date."', '4', '" . _ERRORSQLDEDECTED . " : [<a href=\"index.php?file=Admin&page=erreursql\">" . _TLINK . "</a>].')");
            exit();
            break;
    }
    /* Ne pas exécuter le gestionnaire interne de PHP */
    return true;
}

/**
 * Load config vars from database
 *
 * @param string $dbPrefix : Prefix of database tables names
 * @return array $nuked : Associative array of all params fetched from database
 */
function nkLoadConfiguration($dbPrefix) {
    if (! isset($dbPrefix))
        exit(DBPREFIX_ERROR);

    $dbsConfig = nkDB_selectMany('SELECT name, value FROM '. $dbPrefix .'_config');

    if (nkDB_queryError())
        exit(DBPREFIX_ERROR);

    $nuked = array();

    foreach ($dbsConfig as $row)
        $nuked[$row['name']] = nkHtmlEntities($row['value'], ENT_NOQUOTES);

    $nuked['prefix'] = $dbPrefix;

    // FIX TAGS IN NUKED ARRAY
    foreach($nuked as $k => $v)
        $nuked[$k] = printSecuTags($v);

    return $nuked;
}

function nkGetMedias(){
    require_once 'Includes/nkMediasIncludes.php';

    $bufferEdited = ob_get_contents();

    $findJquery = (boolean)preg_match('#<script[\s]*[type="text/javascript"]*[\s]*src="[A-z0-9:./_-]*(jquery)+[A-z0-9.:/_-]*"[\s]*[type="text/javascript"]*[\s]*>#', $bufferEdited);
    $mediasToInclude = printMedias($findJquery);

    if($findJquery === true){
        $bufferEdited = preg_replace('#<script[\s]*[type="text/javascript"]*[\s]*src="[A-z0-9:./_-]*(jquery)+[A-z0-9.:/_-]*"[\s]*[type="text/javascript"]*[\s]*>#',
            '<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js">',
            $bufferEdited);
    }

    $bufferEdited = preg_replace('#<head>#', '<head>'.$mediasToInclude, $bufferEdited);

    ob_end_clean();

    echo $bufferEdited;
}

// information, success, error, info, warning
function printNotification($message, $type = 'information', $backLinkUrl = false, $return = false) {
    $html = applyTemplate('notification', array(
        'type'          => $type,
        'message'       => $message,
        'backLinkUrl'   => $backLinkUrl,
    ));

    if ($return)
        return $html;
    else
        echo $html;
}

function nkNotification($data, $redirectUrl = null, $redirectDelay = 0){
    if(function_exists('setNotification')){
        setNotification($data, $redirectUrl, $redirectDelay);
    }
    else{
        defaultNotification($data, $redirectUrl, $redirectDelay);
    }
}

function defaultNotification($data, $redirectUrl, $redirectDelay) {
    echo applyTemplate('notification', array('data' => $data));

    if (! empty($redirectUrl))
        redirect($redirectUrl, $redirectDelay);
}

/**
 * Initialization captcha system
 */
function initCaptcha(){
    // Inclusion système Captcha
    require_once('Includes/nkCaptcha.php');

    // On determine si le captcha est actif ou non
    if (_NKCAPTCHA == 'off' || (_NKCAPTCHA == 'auto' && !empty($GLOBALS['user']) && $GLOBALS['user'][1] > 0)) {
        $captcha = false;
    } else if((_NKCAPTCHA == 'auto' && $GLOBALS['user'] && $GLOBALS['user'][1] == 0) || _NKCAPTCHA == 'on') {
        $captcha = true;
    } else {
        $captcha = true;
    }

    return $captcha;
}


function loadSyntaxhighlighterFiles() {
    static $loaded = false;

    if ($loaded) return;

    nkTemplate_addJSFile('media/js/syntaxhighlighter/shCore.js');
    nkTemplate_addJSFile('media/js/syntaxhighlighter/shAutoloader.js');
    nkTemplate_addJSFile('media/js/syntaxhighlighter.autoloader.js');

    if (nkTemplate_getInterface() == 'frontend') {
        nkTemplate_addCSSFile('media/css/syntaxhighlighter/shCoreMonokai.css');
        nkTemplate_addCSSFile('media/css/syntaxhighlighter/shThemeMonokai.css');
    }
    else {
        nkTemplate_addCSSFile('media/css/syntaxhighlighter/shCore.css');
        nkTemplate_addCSSFile('media/css/syntaxhighlighter/shThemeDefault.css');
    }

    $loaded = true;
}

function loadCkeFiles() {
    global $language, $nuked, $bgcolor4;

    nkTemplate_addJSFile('media/ckeditor/ckeditor.js');
    nkTemplate_addJSFile('media/ckeditor/config.js');

    //nkTemplate_addJS(
        ?>
            <script type="text/javascript">
                //<![CDATA[
                if(document.getElementById('e_basic')){
                    CKEDITOR.config.scayt_sLang = "<?php echo (($language == 'french') ? 'fr_FR' : 'en_US'); ?>";
                    CKEDITOR.config.scayt_autoStartup = "true";
                    CKEDITOR.replace('e_basic',{
                        toolbar : 'Basic',
                        autoGrow_onStartup : true,
                        autoGrow_maxHeight : 200,
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
                        autoGrow_onStartup : true,
                        autoGrow_maxHeight : 200,
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
    //);
}

function loadTinymceFiles() {
    nkTemplate_addJSFile('media/tinymce/tinymce.min.js');

    //nkTemplate_addJS(
        ?>
            <script type="text/javascript">
            //<![CDATA[
            // for frontend
            if(document.getElementById('e_basic')){
                tinymce.init({
                    selector: "textarea#e_basic",
                    language : 'fr_FR',
                    plugins: [
                    "autolink lists preview",
                    "fullscreen",
                    "table contextmenu directionality",
                    "emoticons textcolor"
                    ],
                    toolbar1: "undo redo | styleselect | bold italic | bullist numlist outdent indent | link | emoticons "
                });
            }
            // for forum
            if(document.getElementById('e_advanced')){
                tinymce.init({
                    selector: "textarea#e_advanced",
                    language : 'fr_FR',
                    plugins: [
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime nonbreaking save table contextmenu directionality",
                    "emoticons paste textcolor youtube codemagic"
                    ],
                    toolbar1: "insertfile undo redo | styleselect | bold italic forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image youtube emoticons codemagic | preview",
                    /* toolbar2: "print preview media | forecolor backcolor emoticons | link image", */
                    image_advtab: true
                });
            }
            //]]>
            </script>
        <?php
    //);
}


// TODO : Translate message
function nkBenchmark_display() {
    global $nuked, $microTime;

    // TODO : Create a $nuked vars to display it
    $nuked['sql_benchmark']= 'on';

    $line = array();

    if ($nuked['time_generate'] == 'on')
        $line[] = 'Generated in '. (round((microtime(true) - $microTime) * 1000, 1)) .'ms';

    if ($nuked['sql_benchmark'] == 'on')
        $line[] = nkDB_getNbExecutedQuery() .' requêtes sql ('. nkDB_getTimeForExecuteAllQuery() .'ms)';

    if (! empty($line))
        echo '<p class="nkGenerated">'. implode('<br />', $line) .'</p>';
}

?>