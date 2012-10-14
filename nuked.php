<?php
// -------------------------------------------------------------------------//
// Nuked-KlaN 1.7 - PHP Portal                                              //
// http://www.nuked-klan.org                                                //
// -------------------------------------------------------------------------//
// This program is free software. you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License.           //
// -------------------------------------------------------------------------//
defined('INDEX_CHECK') or die ('You can\'t run this file alone.');

// CONNECT TO DB.
connect();

// QUERY NUKED CONFIG_TABLE.
$nuked = array();
$sql_conf = initializeControlDB($db_prefix);
while ($row = mysql_fetch_array($sql_conf)) $nuked[$row['name']] = htmlentities($row['value'], ENT_NOQUOTES);
unset($sql_conf, $row);

// CONVERT ALL HTML ENTITIES TO THEIR APPLICABLE CHARACTERS
$nuked['prefix'] = $db_prefix;

// FUNCTION TO FIX PRINTING TAGS
function printSecuTags($value){
    $value = htmlentities(html_entity_decode(html_entity_decode($value)));
    return $value;
}
// FIX TAGS IN NUKED ARRAY
foreach($nuked as $k => $v){
    $nuked[$k] = printSecuTags($v);
}

// INCLUDE CONSTANT TABLE
include('Includes/constants.php');

// $_REQUEST['file'] & $_REQUEST['op'] DEFAULT VALUE.
if (empty($_REQUEST['file'])) $_REQUEST['file'] = $nuked['index_site'];
if (empty($_REQUEST['op'])) $_REQUEST['op'] = 'index';


// SELECT THEME, USER THEME OR NOT FOUND THEME : ERROR
$nuked['user_theme'] = $_REQUEST[$nuked['cookiename'] . '_user_theme'];
if ($nuked['user_theme'] && is_file(dirname(__FILE__) . '/themes/' . $nuked['user_theme'] . '/theme.php')) $theme = $nuked['user_theme'];
elseif (is_file(dirname(__FILE__) . '/themes/' . $nuked['theme'] . '/theme.php')) $theme = $nuked['theme'];
else exit(THEME_NOTFOUND);

// SELECT LANGUAGE AND USER LANGUAGE
$nuked['user_lang'] = $_REQUEST[$nuked['cookiename'] . '_user_langue'];
$language = ($nuked['user_lang'] && is_file(dirname(__FILE__) . '/lang/' . $nuked['user_lang'] . '.lang.php')) ? $nuked['user_lang'] : $nuked['langue'];

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
    
function nkDate($timestamp, $blok = FALSE) {
    global $nuked, $language;
    $format = ((($blok === FALSE) ? $nuked['IsBlok'] : $blok) === TRUE) ? ($language == 'french') ? '%d/%m/%Y' : '%m/%d/%Y' : $nuked['dateformat'];
    // iconv pour éviter les caractères spéciaux dans la date
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
function session_open($path, $name){
    return true;
}

// CLOSE PHP SESSION
function session_close(){
    return true;
}

// READ PHP SESSION
function session_read($id){
    connect();

    $sql = mysql_query('SELECT session_vars FROM ' . TMPSES_TABLE . ' WHERE session_id = "' . $id . '"');
    if(mysql_num_rows($sql) > 0){
        return ($sql === false) ? '' : mysql_result($sql, 0);
    }
}

// WRITE PHP SESSION
function session_write($id, $data){
    $id = mysql_escape_string($id);
    $data = mysql_escape_string($data);

    connect();

    $sql = mysql_query('INSERT INTO ' . TMPSES_TABLE . ' (session_id, session_start, session_vars) VALUES ("' . $id . '", ' . time() . ', \'' . $data . '\')');

    if ($sql === false || mysql_affected_rows() == 0) $sql = mysql_query('UPDATE ' . TMPSES_TABLE . ' SET session_vars = \'' . $data . '\' WHERE session_id = "' . $id . '"');

    return $sql !== false;
}

// DELETE PHP SESSION
function session_delete($id){
    connect();

    $sql = mysql_query('DELETE FROM ' . TMPSES_TABLE . ' WHERE session_id = "' . mysql_escape_string($id) . '"');

    return $sql;
}

// KILL DEAD SESSION
function session_gc($maxlife){
    $time = time() - $maxlife;

    connect();

    mysql_query('DELETE FROM ' . TMPSES_TABLE . ' WHERE session_start < ' . $time);

    return true;
}

// CONNECT TO DB.
function connect(){
    global $global, $db, $language;

    $db = mysql_connect($global['db_host'], $global['db_user'], $global['db_pass']);

    if (!$db){
        echo '<div style="text-align: center;">' . ERROR_QUERY . '</div>';
        exit();
    }

    $connect = mysql_select_db($global['db_name'], $db);
    mysql_query('SET NAMES "latin1"');
        
    if (!$connect){
        echo '<div style="text-align: center;">' . ERROR_QUERYDB . '</div>';
        exit();
    }
}

// CONFIG PHP SESSION
if(ini_get('session.save_handler') == 'files') session_set_save_handler('session_open', 'session_close', 'session_read', 'session_write', 'session_delete', 'session_gc');

if(ini_get('suhosin.session.encrypt') == '1'){
    @ini_set('session.gc_probability', 100);
    @ini_set('session.gc_divisor', 100);
    @ini_set('session.gc_maxlifetime', (1440));
}

session_name('nuked');
session_start();
if (session_id() == '') exit(ERROR_SESSION);
include ('Includes/nkSessions.php');

// QUERY BAN FOR USER / VISITOR
function banip() {
    global $user_ip, $user, $language;

    // On supprime le dernier chiffre pour les IP's dynamiques
    $ip_dyn = substr($user_ip, 0, -1);

    // Condition SQL : IP dynamique ou compte
    $where_query = ' WHERE (ip LIKE "%' . $ip_dyn . '%") OR pseudo = "' . $user[2] . '"';

    // Recherche d'un banissement
    $query_ban = mysql_query('SELECT `id`, `pseudo`, `date`, `dure` FROM ' . BANNED_TABLE . $where_query);
    $ban = mysql_fetch_assoc($query_ban);

    // Si résultat positif à la recherche d'un bannissement
    if(mysql_num_rows($query_ban) > 0) {
        // Nouvelle adresse IP
        $banned_ip = $user_ip;
    }
    // Recherche d'un cookie de banissement
    else if(isset($_COOKIE['ip_ban']) && !empty($_COOKIE['ip_ban'])) {
        // On supprime le dernier chiffre de l'adresse IP contenu dans le cookie
        $ip_dyn2 = substr($_COOKIE['ip_ban'], 0, -1);

        // On vérifie l'adresse IP du cookie et l'adresse IP actuelle
        if($ip_dyn2 == $ip_dyn) {
            // On vérifie l'existance du bannissement
            $query_ban2 = mysql_query('SELECT `id` FROM ' . BANNED_TABLE . ' WHERE (ip LIKE "%' . $ip_dyn2 . '%")');
            // Si résultat positif, on fait un nouveau ban
            if(mysql_num_rows($query_ban2) > 0)
                $banned_ip = $user_ip;
        }
    }
    else{
        $banned_ip = '';
    }

    // Suppression des banissements dépassés ou mise à jour de l'IP
    if(!empty($banned_ip)) {
        // Recherche banissement dépassé
        if($ban['dure'] != 0 && ($ban['date'] + $ban['dure']) < time()) {
            // Suppression bannissement
            $del_ban = mysql_query('DELETE FROM ' . BANNED_TABLE . $where_query);
            // Notification dans l'administration
            $notify = mysql_query("INSERT INTO " . NOTIFICATIONS_TABLE . " (`date` , `type` , `texte`)  VALUES ('" . time() . "', 4, '" . mysql_real_escape_string($pseudo) . mysql_real_escape_string(_BANFINISHED) . "')");
        }
        // Sinon on met à jour l'IP
        else {
            $where_user = $user ? ', pseudo = "' . $user[2] . '"' : '';
            $upd_ban = mysql_query('UPDATE ' . BANNED_TABLE . ' SET ip = "' . $user_ip . '"' . $where_user . ' ' . $where_query);
            // Redirection vers la page de banissement
            $url_ban = 'ban.php?ip_ban=' . $banned_ip;
            if(!empty($user)){
                $url_ban .= '&user=' . urlencode($user[2]);
            }
            redirect($url_ban, 0);
        }
    }
}

// DISPLAY ALL BLOCKS
function get_blok($side){
    global $user, $nuked;
    
    if ($side == 'gauche') {
        $active = 1;
        $nuked['IsBlok'] = TRUE;
    } else if ($side == 'droite') {
        $active = 2;
        $nuked['IsBlok'] = TRUE;
    } else if ($side == 'centre') {
        $active = 3;
    } else if ($side == 'bas') {
        $active = 4;
    }
    
    $aff_good_bl = 'block_' . $side;

    $sql = mysql_query('SELECT bid, active, position, module, titre, content, type, nivo, page FROM ' . BLOCK_TABLE . ' WHERE active = ' . $active . ' ORDER BY position');
    while ($blok = mysql_fetch_array($sql)){
        $blok['titre'] = printSecuTags($blok['titre']);
        $test_page = '';
        $blok['page'] = explode('|', $blok['page']);
        $size = count($blok['page']);
        for($i=0; $i<$size; $i++){
            if (isset($_REQUEST['file']) && $_REQUEST['file'] == $blok['page'][$i] || $blok['page'][$i] == 'Tous') $test_page = 'ok';
        }

        $visiteur = $user ? $user[1] : 0;

        if ($visiteur >= $blok['nivo'] && $test_page == 'ok'){
            if(file_exists('Includes/blocks/block_' . $blok['type'] . '.php'))
                include_once('Includes/blocks/block_' . $blok['type'] . '.php');
            $function = 'affich_block_' . $blok['type'];
            $blok = $function($blok);

            if (!empty($blok['content'])) $aff_good_bl($blok);
        }
    }
    $nuked['IsBlok'] = FALSE;
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

/**
 * Replace smilies in text
 * @param array $matches : text to parse
 * @return string : parsing text
 */
function replace_smilies($matches)
{
  $matches[0] = preg_replace('#<img src=\"(.*)\" alt=\"(.*)\" title=\"(.*)\" />#Usi', '$2', $matches[0]);
  return $matches[0];
}

// DISPLAYS SMILEYS
function icon($texte){
    global $nuked;
    
    $texte = str_replace('mailto:', 'mailto!', $texte);
    $texte = str_replace('http://', '_http_', $texte);
    $texte = str_replace('https://', '_https_', $texte);
    $texte = str_replace('&quot;', '_QUOT_', $texte);
    $texte = str_replace('&#039;', '_SQUOT_', $texte);
    

    $sql = mysql_query("SELECT code, url, name FROM " . SMILIES_TABLE . " ORDER BY id");
    while (list($code, $url, $name) = mysql_fetch_array($sql)){
        $texte = str_replace($code, '<img src="images/icones/' . $url . '" alt="" title="' . htmlentities($name) . '" />', $texte);
    }

    $texte = str_replace('mailto!', 'mailto:', $texte);
    $texte = str_replace('_http_', 'http://', $texte);
    $texte = str_replace('_https_', 'https://', $texte);
    $texte = str_replace('_QUOT_', '&quot;', $texte);
    $texte = str_replace('_SQUOT_', '&#039;', $texte);
    
    // Light calculation if <pre> tag is not present in text
    if (strpos($texte, '<pre') !== false)
    {
        $texte = preg_replace_callback('#<pre(.*)>(.*)<\/pre>#Uis','replace_smilies', $texte);
    }

    return $texte;
}

// SEARCH SMILIES FOR CKEDITOR.
function ConfigSmileyCkeditor(){

    $donnee = 'CKEDITOR.config.smiley_path=\'images/icones/\';';

    $sql = mysql_query('SELECT code, url, name FROM ' . SMILIES_TABLE . ' ORDER BY id');
    while($row = mysql_fetch_assoc($sql)){
        $TabCode[] = addslashes($row['code']);
        $TabUrl[] = $row['url'];
        $TabName[] = htmlentities($row['name']);
    }

    $IUrl = 0;
    $CompteurUrl = count($TabUrl);
    $donnee .= 'CKEDITOR.config.smiley_images=[';
    foreach( $TabUrl as $VUrl ){
        $IUrl++;
        $VirguleUrl = ($IUrl == $CompteurUrl) ? '' : ', ';
        $donnee .= "'$VUrl'$VirguleUrl";
    }
    $donnee .= '];';

    $ICode = 0;
    $CompteurCode = count($TabCode);
    $donnee .= 'CKEDITOR.config.smiley_descriptions=[';
    foreach( $TabCode as $VCode ){
        $ICode++;
        $VirguleCode = ($ICode == $CompteurCode) ? '' : ', ';
        $donnee .= "'$VCode'$VirguleCode";
    }
    $donnee .= '];';
    
    $IName = 0;
    $CompteurName = count($TabName);
    $donnee .= 'CKEDITOR.config.smiley_titles=[';
    foreach( $TabName as $VName ){
        $IName++;
        $VirguleName = ($IName == $CompteurName) ? '' : ', ';
        $donnee .= "'$VName'$VirguleName";
    }
    $donnee .= '];';

    return $donnee;
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
        'font-family'
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
            'dir',
        ),
        'h1' => array(
            'style',
        ),
        'h2' => array(
            'style',
        ),
        'h3' => array(
            'style',
        ),
        'h4' => array(
            'style',
        ),
        'h5' => array(
            'style',
        ),
        'h6' => array(
            'style',
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
            'border',
        ),
        'strong' => array(),
        'em' => array(),
        'u' => array(),
        'strike' => array(),
        'sub' => array(),
        'sup' => array(),
        'ol' => array(),
        'ul' => array(),
        'li' => array(),
        'blockquote' => array(
            'style',
        ),
        'div' => array(
            'class',
            'id',
            'lang',
            'style',
            'title',
            'align',
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
            'type',
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
            'summary',
        ),
        'caption' => array(),
        'thead' => array(),
        'tr' => array(
            'style',
        ),
        'td' => array(
            'style',
            'colspan',
            'rowspan'
        ),
        'th' => array(
            'scope',
        ),
        'tbody' => array(),
        'hr' => array(),
        'span' => array(
            'id',
            'style',
            'dir',
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
        'address' => array(),
    );

        // FOR VIDEO PLUGIN -- POUR PLUGIN VIDEO
    $TabVideo = array(
        'object' => array(
            'width',
            'height',
            'data',
            'type',
        ),
        'param' => array (
            'name',
            'value',
        ),
        'embed' => array (
            'allowfullscreen',
            'allowscriptaccess',
            'height',
            'src',
            'type',
            'width',
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
            $args[2][$id] = trim(html_entity_decode($val), " \t\n\r\0\"");
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
        if ($matches[3] == '/'){
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
    $texte = htmlspecialchars($texte);
    $texte = str_replace('&amp;', '&', $texte);
    
    // Balise autorisée
    $texte = preg_replace_callback('/&lt;([^ &]+)[[:blank:]]?((.(?<!&gt;))*)&gt;/', 'secu_args', $texte);

    preg_match_all('`<(/?)([^/ >]+)(| [^>]*([^/]))>`', $texte, $Tags, PREG_SET_ORDER);

    $TagList = array();
    $bad = false;
    $size = count($Tags);
    for($i=0; $i<$size; $i++){
        $TagName = $Tags[$i][3] == ''?$Tags[$i][2].$Tags[$i][4]:$Tags[$i][2];
        if ($Tags[$i][1] == '/'){
            $bad = $bad | array_pop($TagList) != $TagName;
        }
        else{
            array_push($TagList, $TagName);
        }
    }

    $bad = $bad | count($TagList) > 0;

    if ($bad){
        return(_HTMLNOCORRECT);
    }
    else{
        return $texte;
    }
}

// REDIRECT AFTER ($tps) SECONDS TO ($url)
function redirect($url, $tps){
    $temps = $tps * 1000;

    echo '<script type="text/javascript">',"\n"
    , '<!--',"\n"
    , "\n"
    , 'function redirect() {',"\n"
    , 'window.location=\'' , $url , '\'',"\n"
    , "}\n"
    , 'setTimeout(\'redirect()\',\'' , $temps ,'\');',"\n"
    , "\n"
    , '// -->',"\n"
    , '</script>',"\n";
}

// DISPLAYS THE NUMBER OF PAGES
function number($count, $each, $link){

    $current = $_REQUEST['p'];

    if ($each > 0){
        if ($count <= 0)     $count   = 1;
        if (empty($current)) $current = 1; // On renormalise la page courante...
        // Calcul du nombre de pages
        $n = ceil($count / intval($each)); // on arrondit à  l'entier sup.
        // Début de la chaine d'affichage
        $output = '<b class="pgtitle">' . _PAGE . ' :</b> ';
        
        for ($i = 1; $i <= $n; $i++){
            if ($i == $current){
                $output .= sprintf('<b class="pgactuel">[%d]</b> ',$i    );
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
                // Après la page courante
                elseif (!isset($last_done) && $i > $current){
                    $output .= sprintf('<a href="' . $link . '&amp;p=%d" title="' . _NEXTPAGE . '" class="pgnext">&raquo;</a>... ',$current+1);
                    $last_done = true;
                }
                // On a dépassé les cas qui nous intéressent : inutile de continuer
                elseif ($i > $current)
                    break;
            }
        }
        $output .= '<br />';
        echo $output;
    }
}

function nbvisiteur(){
    global $user, $nuked, $user_ip;

    $limite = time() + $nuked['nbc_timeout'];
    $time = time();

    $req = mysql_query("DELETE FROM " . NBCONNECTE_TABLE . " WHERE date < '" . $time."'");

    if (isset($user_ip)){
        if (isset($user[0])){
            $where = "WHERE user_id='" . $user[0] . "'";
        }
        else{
            $where = "WHERE IP='" . $user_ip . "'";
        }
        $req = mysql_query("SELECT IP FROM " . NBCONNECTE_TABLE . " " . $where);
        $query = mysql_num_rows($req);

        if ($query > 0){
            if (isset($user[0])){
                $req = mysql_query("UPDATE " . NBCONNECTE_TABLE . " SET date = '" . $limite . "', type = '" . $user[1] . "', IP = '" . $user_ip . "', username = '" . $user[2] . "' WHERE user_id = '" . $user[0] . "'");
            }
            else{
                $req = mysql_query("UPDATE " . NBCONNECTE_TABLE . " SET date = '" . $limite . "', type = '" . $user[1] . "', user_id = '" . $user[0] . "', username = '" . $user[2] . "' WHERE IP = '" . $user_ip . "'");
            }
        }
        else{
            $del = mysql_query("DELETE FROM " . NBCONNECTE_TABLE . " WHERE IP = '" . $user_ip . "'");
            $req = mysql_query("INSERT INTO " . NBCONNECTE_TABLE . " ( `IP` , `type` , `date` , `user_id` , `username` ) VALUES ( '" . $user_ip . "' , '" . $user[1] . "' , '" . $limite . "' , '" . $user[0] . "' , '" . $user[2] . "' )");
        }
    }

    $res = mysql_query("SELECT type FROM " . NBCONNECTE_TABLE . " WHERE type = 0");
    $count[0] = mysql_num_rows($res);
    $res = mysql_query("SELECT type FROM " . NBCONNECTE_TABLE . " WHERE type BETWEEN 1 AND 2");
    $count[1] = mysql_num_rows($res);
    $res = mysql_query("SELECT type FROM " . NBCONNECTE_TABLE . " WHERE type > 2");
    $count[2] = mysql_num_rows($res);
    $count[3] = $count[1] + $count[2];
    $count[4] = $count[0] + $count[3];
    return $count;
}

function nivo_mod($mod){
    $sql = mysql_query("SELECT niveau FROM " . MODULES_TABLE . " WHERE nom = '" . $mod . "'");
    if (mysql_num_rows($sql) == 0){
        return false;
    }
    else{
        list($niveau) = mysql_fetch_array($sql);
        return $niveau;
    }
}

function admin_mod($mod){
    $sql = mysql_query("SELECT admin FROM " . MODULES_TABLE . " WHERE nom = '" . $mod . "'");
    list($admin) = mysql_fetch_array($sql);
    return $admin;
}

function translate($file_lang){
    global $nuked;

    ob_start();
    print eval(" include ('$file_lang'); ");
    $lang_define = ob_get_contents();
    $lang_define = htmlentities($lang_define, ENT_NOQUOTES);
    $lang_define = str_replace('&lt;', '<', $lang_define);
    $lang_define = str_replace('&gt;', '>', $lang_define);
    ob_end_clean();
    return $lang_define;
}

function compteur($file){
    $upd = mysql_query('UPDATE ' . STATS_TABLE . ' SET count = count + 1 WHERE type = "pages" AND nom = "' . $_GET['file'] . '"');
}

function nk_CSS($str){
    if ($str != ""){
        $str = str_replace('content-disposition:','&#99;&#111;&#110;&#116;&#101;&#110;&#116;&#45;&#100;&#105;&#115;&#112;&#111;&#115;&#105;&#116;&#105;&#111;&#110;&#58;',$str);
        $str = str_replace('content-type:','&#99;&#111;&#110;&#116;&#101;&#110;&#116;&#45;&#116;&#121;&#112;&#101;&#58;',$str);
        $str = str_replace('content-transfer-encoding:','&#99;&#111;&#110;&#116;&#101;&#110;&#116;&#45;&#116;&#114;&#97;&#110;&#115;&#102;&#101;&#114;&#45;&#101;&#110;&#99;&#111;&#100;&#105;&#110;&#103;&#58;',$str);
        $str = str_replace('include','&#105;&#110;&#99;&#108;&#117;&#100;&#101;',$str);
        $str = str_replace('script','&#115;&#99;&#114;&#105;&#112;&#116;',$str);
        $str = str_replace('eval','&#101;&#118;&#97;&#108;',$str);
        $str = str_replace('javascript','&#106;&#97;&#118;&#97;&#115;&#99;&#114;&#105;&#112;&#116;',$str);
        $str = str_replace('embed','&#101;&#109;&#98;&#101;&#100;',$str);
        $str = str_replace('iframe','&#105;&#102;&#114;&#97;&#109;&#101;',$str);
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
    return($str);
}

function visits(){
    global $nuked, $user_ip, $user;

    $time = time();
    $timevisit = $nuked['visit_delay'] * 60;
    $limite = $time + $timevisit;

    $sql_where = ($user) ? 'user_id = "' . $user[0] : 'ip = "' . $user_ip;
    $sql = mysql_query('SELECT id, date FROM ' . STATS_VISITOR_TABLE . ' WHERE ' . $sql_where . '" ORDER by date DESC LIMIT 0, 1');

    list($id, $date) = mysql_fetch_array($sql);

    if (isset($id) && $date > $time){
        $upd = mysql_query("UPDATE " . STATS_VISITOR_TABLE . " SET  date = '" . $limite . "' WHERE id = '" . $id . "'");
    }
    else{
        $month = strftime('%m', $time);
        $year = strftime('%Y', $time);
        $day = strftime('%d', $time);
        $hour = strftime('%H', $time);
        $user_referer = mysql_escape_string($_SERVER['HTTP_REFERER']);
        $user_host = strtolower(@gethostbyaddr($user_ip));
        $user_agent = mysql_escape_string($_SERVER['HTTP_USER_AGENT']);

        if ($user_host == $user_ip) $host = '';
        else{
            if (preg_match('`([^.]{1,})((\.(co|com|net|org|edu|gov|mil))|())((\.(ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|fi|fj|fk|fm|fo|fr|fx|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zr|zw))|())$`', $user_host, $res))
                $host = $res[0];
        }

        $browser = getBrowser();
        $os = getOS();
        $sql2 = mysql_query("INSERT INTO " . STATS_VISITOR_TABLE . " ( `id` , `user_id` , `ip` , `host` , `browser` , `os` , `referer` , `day` , `month` , `year` , `hour` , `date` ) VALUES ( '' , '" . $user[0] . "' , '" . $user_ip . "' , '" . $host . "' , '" . $browser . "' , '" . $os . "' , '" . $user_referer . "' , '" . $day . "' , '" . $month . "' , '" . $year . "' , '" . $hour . "' , '" . $limite . "' )");
    }
}

function verif_pseudo($string = null, $old_string = null) {
    global $nuked;

    $string = trim($string);

    if (empty($string) || preg_match("`[\$\^\(\)'\"?%#<>,;:]`", $string)) {
        return 'error1';
    }

    if($string != $old_string) {
        $sql = mysql_query('SELECT pseudo FROM ' . USER_TABLE . ' WHERE pseudo = "' . $string . '"');
        $is_reg = mysql_num_rows($sql);
        if ($is_reg > 0) {
            return 'error2';
        }
    }

    $sql2 = mysql_query('SELECT pseudo FROM ' . BANNED_TABLE . ' WHERE pseudo = "' . $string . '"');
    $is_reg2 = mysql_num_rows($sql2);
    if ($is_reg2 > 0) {
        return 'error3';
    }

    return $string;
}

function UpdateSitmap(){
    global $nuked;
    $Disable = array('Suggest', 'Comment', 'Vote', 'Textbox', 'Members');

    $fp = fopen(dirname(__FILE__).'/sitemap.xml', 'wb');
    if ($fp !== false){
        $Sitemap = "<?xml version='1.0' encoding='UTF-8'?>\r\n";
        $Sitemap .= "<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\r\n";
        $Sitemap .= "xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\"\r\n";
        $Sitemap .= "xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n";

        $sql = 'SELECT nom FROM ' . MODULES_TABLE . ' WHERE niveau = 0';
        $mods = mysql_query($sql);

        while(list($mod) = mysql_fetch_row($mods)){
            if (!in_array($mod, $Disable)){
                $Sitemap .= "\t<url>\r\n";
                $Sitemap .= "\t\t<loc>$nuked[url]/index.php?file=$mod</loc>\r\n";
                switch($mod){
                    case 'News':
                        $Last = mysql_query('SELECT date FROM ' . NEWS_TABLE . 'ORDER BY date DESC LIMIT 1');
                        $Last = date('Y-m-d');
                        $Sitemap .= "\t\t<priority>0.8</priority>\r\n";
                        $Sitemap .= "\t\t<lastmod>$Last</lastmod>\r\n";
                        $Sitemap .= "\t\t<changefreq>daily</changefreq>\r\n";
                        break;
                    case 'Forum':
                        $Sitemap .= "\t\t<priority>0.4</priority>\r\n";
                        $Sitemap .= "\t\t<lastmod>$Last</lastmod>\r\n";
                        $Sitemap .= "\t\t<changefreq>always</changefreq>\r\n";
                        break;
                    case 'Download':
                        $Last = mysql_query('SELECT date FROM ' . DOWNLOAD_TABLE . 'ORDER BY date DESC LIMIT 1');
                        $Last = date('Y-m-d');
                        $Sitemap .= "\t\t<priority>0.5</priority>\r\n";
                        $Sitemap .= "\t\t<lastmod>$Last</lastmod>\r\n";
                        $Sitemap .= "\t\t<changefreq>weekly</changefreq>\r\n";
                        break;

                    default:
                        $Sitemap .= "\t\t<priority>0.5</priority>\r\n";
                } // switch
                $Sitemap .= "\t</url>\r\n";
            }
        }

        $Sitemap .= "</urlset>\r\n";
        fwrite($fp, chr(0xEF) . chr(0xBB)  . chr(0xBF) . utf8_encode($Sitemap)); //Ajout de la marque d'Octet
        fclose($fp);
    }
}

function getOS(){

    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $os = 'Autre';

    $list_os = array(
        // Windows
        'Windows NT 6.1'       => 'Windows 7',
        'Windows NT 6.0'       => 'Windows Vista',
        'Windows NT 5.2'       => 'Windows Server 2003',
        'Windows NT 5.1'       => 'Windows XP',
        'Windows NT 5.0'       => 'Windows 2000',
        'Windows 2000'         => 'Windows 2000',
        'Windows CE'           => 'Windows Mobile',
        'Win 9x 4.90'          => 'Windows Me.',
        'Windows 98'           => 'Windows 98',
        'Windows 95'           => 'Windows 95',
        'Win95'                => 'Windows 95',
        'Windows NT'           => 'Windows NT',

        // Linux
        'Ubuntu'               => 'Linux Ubuntu',
        'Fedora'               => 'Linux Fedora',
        'Linux'                => 'Linux',

        // Mac
        'Macintosh'            => 'Mac',
        'Mac OS X'             => 'Mac OS X',
        'Mac_PowerPC'          => 'Mac OS X',

         // Autres
        'FreeBSD'              => 'FreeBSD',
        'Unix'                 => 'Unix',
        'Playstation portable' => 'PSP',
        'OpenSolaris'          => 'SunOS',
        'SunOS'                => 'SunOS',
        'Nintendo Wii'         => 'Nintendo Wii',
        'Mac'                  => 'Mac',

        // Search Engines
        'msnbot'               => 'Microsoft Bing',
        'googlebot'            => 'Google Bot',
        'yahoo'                => 'Yahoo Bot'
    );

    $user_agent = strtolower( $user_agent );

    foreach( $list_os as $k => $v ){
        if (preg_match("#".strtolower($k)."#", strtolower($user_agent))){
            $os = $v;
            break;
        }
    }
    return $os;
}

function getBrowser(){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser = 'Autre';

    $list_browser = array(
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

    foreach( $list_browser as $k => $v ){
        if (preg_match("#".$k."#i", $user_agent)){
            $browser = $v;
            break;
        }
    }
    return $browser;

}
function erreursql($errno, $errstr, $errfile, $errline, $errcontext){
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
            $upd = mysql_query("INSERT INTO " . $nuked['prefix'] . "_erreursql  (`date` , `lien` , `texte`)  VALUES ('" . $date . "', '" . mysql_escape_string($_SERVER["REQUEST_URI"]) . "', '" . $texte . "')");
            $upd2 = mysql_query("INSERT INTO " . $nuked['prefix'] . "_notification  (`date` , `type` , `texte`)  VALUES ('".$date."', '4', '" . _ERRORSQLDEDECTED . " : [<a href=\"index.php?file=Admin&page=erreursql\">" . _TLINK . "</a>].')");
            exit();
            break;
    }
    /* Ne pas exécuter le gestionnaire interne de PHP */
    return true;
}

function send_stats_nk() {
	global $nuked;
	
	if($nuked['stats_share'] == "1")
	{
		$timediff = (time() - $nuked['stats_timestamp'])/60/60/24/60; // Tous les 60 jours
		if($timediff >= 60) 
		{
			
			?>
            <script type="text/javascript" src="modules/Admin/scripts/jquery-1.6.1.min.js"></script>
            <script type="text/javascript">
			$(document).ready(function() {
				data="nuked_nude=ajax";
				$.ajax({url:'index.php', data:data, type: "GET", success: function(html) { 
				 }});
			});
			</script>
            <?php
		}
	}
}

// Control valid DB prefix
function initializeControlDB($prefixDB) {
    if (!isset($prefixDB)) {
        exit(DBPREFIX_ERROR);
    } else {
        $result = mysql_query('SELECT name, value FROM ' . $prefixDB . '_config');
        if ($result == false) {
            exit(DBPREFIX_ERROR);
        } else {
            return $result;
        }
    }
}
?>
