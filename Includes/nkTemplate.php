<?php
/**
 * nkTemplate.php
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
  * Initialisation of nkTemplate global vars
  */
$GLOBALS['nkTemplate'] = array(
    'pageDesign'    => 'fullPage', // fullPage, nudePage or none
    'title'         => '',
    'JS'            => array(
        'file'          => array(
            'librairy'          => array(),
            'librairyPlugin'    => array(),
            'normal'            => array()
        ),
        'string'        => array(
            'normal'            => '',
            'jqueryDomReady'    => '',
            'beforeLibs'        => ''
        ),
    ),
    'CSS'            => array(
        'file'          => array(),
        'data'          => ''
    )
);

define('JQUERY_LIBRAIRY', 'media/js/jquery/jquery-1.11.3.min.js');
define('JQUERY_UI_LIBRAIRY', '');
define('JQUERY_UI_CSS', '');


function nkTemplate_init($theme) {
    
}

/**
  * Set title of page.
  *
  * @param string $title : Title of page to set.
  * @param bool $add : Add title content at defaut title ( $GLOBALS['nuked']['name'] .' - '. $GLOBALS['nuked']['slogan'] )
  * @return void
  */
function nkTemplate_setTitle($title, $add = false) {
    if ($title != '' && is_string($title)) {
        if ($add)
            $GLOBALS['nkTemplate']['title'] .= $title;
        else
            $GLOBALS['nkTemplate']['title'] = $title;
    }
}


/**
  * Sets the page design style.
  *
  * @param string $pageDesign : The page design style to set ( fullPage, nudePage ou none )
  * @return void
  */
function nkTemplate_setPageDesign($pageDesign) {
    if (in_array($pageDesign, array('fullPage', 'nudePage', 'none')))
        $GLOBALS['nkTemplate']['pageDesign'] = $pageDesign;
}


/**
  * Return the page design style ( fullPage, nudePage ou none ).
  *
  * @param void
  * @return string
  */
function nkTemplate_getPageDesign() {
    return $GLOBALS['nkTemplate']['pageDesign'];
}


/**
  * Return the HTML code of top page.
  *
  * @param void
  * @return string HTML code.
  */
function nkTemplate_getTopOfPage() {
    if ($GLOBALS['nkTemplate']['pageDesign'] == 'fullPage') {
        ob_start();
        top();
        return ob_get_clean();
    }
    elseif ($GLOBALS['nkTemplate']['pageDesign'] == 'nudePage')
        return applyTemplate('nudePage/header', array());
}


/**
  * Return the HTML code of footer page.
  *
  * @param void
  * @return string HTML code.
  */
function nkTemplate_getFooterOfPage() {
    if ($GLOBALS['nkTemplate']['pageDesign'] == 'fullPage') {
        ob_start();
        footer();
        return ob_get_clean();
    }
    elseif ($GLOBALS['nkTemplate']['pageDesign'] == 'nudePage')
        return "\n</body>\n</html>\n";
}


/**
  * Apply template and returns results.
  *
  * @param string $path : The path of template file. ( without extention )
  * @param array $data : The data to transmit at template.
  * @return string HTML code
  */
function applyTemplate($path, $data = array(), $interface = 'frontend') {
    ob_start();
    extract($data);

    include 'views/'. $interface .'/'. $path .'.php';

    return ob_get_clean();
}


/**
  * Add a CSS stylesheet to a global list that will be include by nkTemplate_append.
  *
  * @param string $file : The name of the file to include.
  * @param string $media : The media this stylesheet applies to.
  * @return void
  */
function nkTemplate_addCSSFile($file, $media = 'screen', $conditionalComment = false) {
    if (! array_key_exists($file, $GLOBALS['nkTemplate']['CSS']['file'])) {
        $GLOBALS['nkTemplate']['CSS']['file'][$file] = array(
            'media'                 => $media,
            'conditionalComment'    => $conditionalComment
        );
    }
}


/**
  * Add a CSS block to a global list that will be include by nkTemplate_append.
  *
  * @param string $style : A set of css instruction.
  * @return void
  */
function nkTemplate_addCSS($style) {
    $GLOBALS['nkTemplate']['CSS']['string'] .= $style ."\n";
}


/**
  * Get HTML code for added stylesheets.
  *
  * @param void
  * @return string HTML code.
  */
function nkTemplate_getCSS() {
    $html = '';

    if (! empty($GLOBALS['nkTemplate']['CSS']['file'])) {
        foreach ($GLOBALS['nkTemplate']['CSS']['file'] as $file => $data) {
            if ($data['conditionalComment'] !== false)
                $html .= '<!--['. $data['conditionalComment'] .']>' ."\n"
                    . '<link rel="stylesheet" type="text/css" href="'. $file .'" media="'. $data['media'] .'"/>' ."\n"
                    . '<![endif]-->' ."\n";
            else
                $html .= '<link rel="stylesheet" type="text/css" href="'. $file .'" media="'. $data['media'] .'"/>' ."\n";
        }
    }

    if ($GLOBALS['nkTemplate']['CSS']['string'] != '') {
        $html .= '<style type="text/css">' ."\n"
            . $GLOBALS['nkTemplate']['CSS']['string']
            . '</style>' ."\n";
    }

    $GLOBALS['nkTemplate']['CSS'] = array(
        'file'      => array(),
        'string'    => ''
    );

    return $html;
}


/**
  * Add a Javascript librairy to a global list that will be include by nkTemplate_append.
  *
  * @param string $file : The name of the file to include.
  * @return void
  */
function nkTemplate_addJSFile($file, $type = 'normal', $conditionalComment = false) {
    if ($type == 'librairy') {
        if (! array_key_exists($file, $GLOBALS['nkTemplate']['JS']['file']['librairy']))
            $GLOBALS['nkTemplate']['JS']['file']['librairy'][$file] = $conditionalComment;

    }
    elseif ($type == 'librairyPlugin') {
        if (! array_key_exists($file, $GLOBALS['nkTemplate']['JS']['file']['librairyPlugin']))
            $GLOBALS['nkTemplate']['JS']['file']['librairyPlugin'][$file] = $conditionalComment;

    }
    elseif ($type == 'normal') {
        if (! array_key_exists($file, $GLOBALS['nkTemplate']['JS']['file']['normal']))
            $GLOBALS['nkTemplate']['JS']['file']['normal'][$file] = $conditionalComment;
    }
}

/**
  * Add a Javascript block to a global list that will be include by nkTemplate_append.
  *
  * @param string $script : A set of javascript instructions.
  * @param bool $jsType : If jQuery DOM ready function is added to js string.
  * @return void
  */
function nkTemplate_addJS($script, $jsType = 'normal') {
    if ($jsType == 'jqueryDomReady')
        $GLOBALS['nkTemplate']['JS']['string']['jqueryDomReady'] .= $script;
    if ($jsType == 'beforeLibs')
        $GLOBALS['nkTemplate']['JS']['string']['beforeLibs'] .= $script;
    else
        $GLOBALS['nkTemplate']['JS']['string']['normal'] .= $script;
}


function nkTemplate_getJSFile($file, $conditionalComment) {
    if ($conditionalComment !== false) {
        return '<!--['. $conditionalComment .']>' ."\n"
        . '<script type="text/javascript" src="'. $file .'"></script>' ."\n"
        . '<![endif]-->' ."\n";
    }
    else {
        return '<script type="text/javascript" src="'. $file .'"></script>' ."\n";
    }
}


/**
  * Get HTML code for added javascript libs.
  *
  * @param void
  * @return string HTML code.
  */
function nkTemplate_getJS() {
    $html = '';

    if ($GLOBALS['nkTemplate']['JS']['string']['beforeLibs'] != '') {
        $html .= '<script type="text/javascript">' ."\n"
            . '// <![CDATA[' ."\n"
            . $GLOBALS['nkTemplate']['JS']['string']['beforeLibs']
            . '// ]]>' ."\n"
            . '</script>' ."\n";
    }

    if ( ! empty( $GLOBALS['nkTemplate']['JS']['file']['librairy'] ) ) {
        $jQueryLib = '';

        foreach ($GLOBALS['nkTemplate']['JS']['file']['librairy'] as $file => $conditionalComment) {
            if ($file == JQUERY_LIBRAIRY) {
                $jQueryLib = '<script type="text/javascript" src="'. JQUERY_LIBRAIRY .'"></script>' ."\n"
                    . '<script type="text/javascript">jQuery.noConflict();</script>' ."\n";
            } else {
                $html .= nkTemplate_getJSFile($file, $conditionalComment);
            }
        }

        $html .= $jQueryLib;
    }

    if (! empty($GLOBALS['nkTemplate']['JS']['file']['librairyPlugin'])) {
        foreach ($GLOBALS['nkTemplate']['JS']['file']['librairyPlugin'] as $file => $conditionalComment)
            $html .= nkTemplate_getJSFile($file, $conditionalComment);
    }

    if (! empty($GLOBALS['nkTemplate']['JS']['file']['normal'])) {
        foreach ($GLOBALS['nkTemplate']['JS']['file']['normal'] as $file => $conditionalComment)
            $html .= nkTemplate_getJSFile($file, $conditionalComment);
    }

    if ($GLOBALS['nkTemplate']['JS']['string']['normal'] != '' || $GLOBALS['nkTemplate']['JS']['string']['jqueryDomReady'] != '') {
        $html .= '<script type="text/javascript">' ."\n"
            . '// <![CDATA[' ."\n";

        if ($GLOBALS['nkTemplate']['JS']['string']['normal'] != '')
            $html .= $GLOBALS['nkTemplate']['JS']['string']['normal'];

        if ($GLOBALS['nkTemplate']['JS']['string']['jqueryDomReady'] != '') {
            $html .= 'jQuery(document).ready(function($) { ' ."\n"
                . $GLOBALS['nkTemplate']['JS']['string']['jqueryDomReady'] ."\n"
                . "});\n";
        }

        $html .= '// ]]>' ."\n"
            . '</script>' ."\n";
    }

    $GLOBALS['nkTemplate']['JS'] = array(
        'file'          => array(
            'librairy'          => array(),
            'librairyPlugin'    => array(),
            'normal'            => array()
        ),
        'data'          => array(
            'normal'            => '',
            'jqueryDomReady'    => '',
        ),
    );

    return $html;
}


/**
  * Append meta generator, title, JS and CSS librairy into the HEAD section of HTML output.
  *
  * @param string $contentTop : Current result of top() function.
  * @return string : Modified result of top() function.
  */
function nkTemplate_append($contentTop) {
    global $nuked;

    $append  = '<meta name="generator" content="Nuked Klan - v'. $nuked['version'] .'" />' ."\n"
        . '<link rel="search" type="application/opensearchdescription+xml" title="'. $nuked['name'] .'" href="'. $nuked['url'] .'/openSearch.php" />'
        // rajouter les flux rss
        . nkTemplate_getCSS()
        . nkTemplate_getJS();

    /*
    foreach (explode('|', $nuked['rssFeed']) as rssFeed)
        $append .= '<link rel="alternate" title="'. $nuked['name'] .' : '. constant(strtoupper($rssFeed) .'_RSS_TITLE').'" href="'. $nuked['url'] .'/rss/'. $rssFeed .'_rss.php" type="application/rss+xml" />' ."\n";

    . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniéres news" href="'. $nuked['url'] .'/rss/news_rss.php" type="application/rss+xml" />' ."\n"
    . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniers articles" href="'. $nuked['url'] .'/rss/sections_rss.php" type="application/rss+xml" />' ."\n"
    . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniers téléchargements" href="'. $nuked['url'] .'/rss/download_rss.php" type="application/rss+xml" />' ."\n"
    . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniers liens" href="'. $nuked['url'] .'/rss/links_rss.php" type="application/rss+xml" />' ."\n"
    . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniéres images" href="'. $nuked['url'] .'/rss/gallery_rss.php" type="application/rss+xml" />' ."\n"
    . '<link rel="alternate" title="Nuked-Klan RSS : Les 20 derniers sujets" href="'. $nuked['url'] .'/rss/forum_rss.php" type="application/rss+xml" />' ."\n"
    */

    if ($GLOBALS['nkTemplate']['title'] != '')
        $contentTop = preg_replace( '#<title>(.*?)</title>#i', '<title>'. $GLOBALS['nkTemplate']['title'] .'</title>', $contentTop );

    $contentTop = str_ireplace('</head>', $append .'</head>', $contentTop);

    return $contentTop;
}


function nkTemplate_renderPage($content) {
    $contentTop     = nkTemplate_getTopOfPage();
    $contentFooter  = nkTemplate_getFooterOfPage();
    $contentTop     = nkTemplate_append($contentTop);

    return $contentTop . $content . $contentFooter;
}


/**
  * Run block and send output html
  *
  * @param string $side : Side of block to run
  * @return string HTML code
  * /
function get_blok($side) {
    global $visiteur, $nuked;

    if ($side == 'gauche') {
        $active = 1;
        $nuked['IsBlok'] = true;
    }
    else if ($side == 'droite') {
        $active = 2;
        $nuked['IsBlok'] = true;
    }
    else if ($side == 'centre') {
        $active = 3;
    }
    else if ($side == 'bas') {
        $active = 4;
    }

    $aff_good_bl = 'block_' . $side;

    $dbsBlock = nkDB_select(
        'SELECT * 
        FROM '. BLOCK_TABLE .' 
        WHERE active = '. $active,
        array('position')
    );

    foreach ($dbsBlock as $block) {
        $block['titre'] = printSecuTags($block['titre']);
        $block['page'] = explode('|', $block['page']);

        if ($visiteur >= $block['nivo'] && (in_array($GLOBALS['file'], $block['page']) || in_array('Tous', $block['page']))) {
            if (file_exists($blockFile = 'Includes/blocks/block_' . $block['type'] . '.php'))
                include_once $blockFile;
            else {
                trigger_error('Block file '. $blockFile .' no found', E_USER_WARNING);
                continue;
            }

            if (! function_exists($blockFunction = 'affich_block_'. $block['type'])) {
                trigger_error('Block function '. $blockFunction .' no found', E_USER_WARNING);
                continue;
            }

            $block = $blockFunction($block);

            if (! empty($block['content'])) $aff_good_bl($block);
        }
    }

    $nuked['IsBlok'] = false;
}


/**
  * Add Infobulle at ID of HTML element
  *
  * @param string $id : ID of HTML element
  * @param string $title : The title of info-bulle
  * @param string $text : The text of info-bulle
  * @param string $width : The width of info-bulle
  * @return void
  */
function addInfobulle($id, $title, $text, $width = 200) {
    static $infobulleInit = false;

    if (! $infobulleInit) {
        nkTemplate_addJSFile('js/infobulle/infobulle.js');
        nkTemplate_addJS('infobulle_init();');

        $infobulleInit = true;
    }

    nkTemplate_addJS('infobulle_set("'. $id .'","'. $title .'", "'. $text .'", '. $width .');' ."\n");
}

?>