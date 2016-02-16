<?php
/**
 * nkTemplate.php
 *
 * Manage HTML design (full or nude page)
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
    'interface'     => 'frontend', // frontend or backend
    'pageDesign'    => 'fullPage', // fullPage, nudePage or none
    'title'         => '',
    'defaultColor'  => array(
        'bgcolor1' => '#666',
        'bgcolor2' => '#777',
        'bgcolor3' => '#444',
        'bgcolor4' => '#999'
    ),
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
        'string'        => ''
    )
);

define('JQUERY_LIBRAIRY', 'media/js/jquery-min-1.8.3.js');
define('JQUERY_UI_LIBRAIRY', 'media/jquery-ui/jquery-ui.min.js');
define('JQUERY_UI_CSS', 'media/jquery-ui/jquery-ui.css');


/**
 * Initialize default data of nkTemplate librairy.
 *
 * @param string $module : The current module.
 * @return void
 */
function nkTemplate_init($module = null) {
    global $page, $admin;

    if ($module === null)
        $GLOBALS['nkTemplate']['pageDesign'] = 'none';
    else if ($module == 'Admin' || $page == 'admin' || $admin === true)
        $GLOBALS['nkTemplate']['interface'] = 'backend';

    if ($GLOBALS['nkTemplate']['interface'] == 'backend')
        nkTemplate_adminInit($module);
}

/**
 * Initialize default Js and Css data for module frontend.
 *
 * @param string $module : The current module.
 * @return void
 */
function nkTemplate_moduleInit($module) {
    global $theme;

    nkTemplate_addJSFile('media/js/nkDefault.js');
    nkTemplate_addCSSFile('media/css/nkDefault.css');

    if (is_string($module)) {
        $jsFiles = array(
            'modules/'. $module .'/js/frontend.js',
            'themes/'. $theme .'/js/modules/'. $module .'.js'
        );

        foreach ($jsFiles as $jsFile)
            if (is_file($jsFile)) nkTemplate_addJSFile($jsFile);

        $cssFiles = array(
            'modules/'. $module .'/'. $module .'.css',
            'themes/'. $theme .'/css/modules/'. $module .'.css'
        );

        foreach ($cssFiles as $cssFile)
            if (is_file($cssFile)) nkTemplate_addCSSFile($cssFile);
    }
}

/**
 * Initialize default Js and Css data for module backend.
 *
 * @param string $module : The current module.
 * @return void
 */
function nkTemplate_adminInit($module) {
    global $language, $nuked;

    nkTemplate_addCSSFile('modules/Admin/css/reset.css');
    nkTemplate_addCSSFile('modules/Admin/css/style.css');
    nkTemplate_addCSSFile('modules/Admin/css/invalid.css');

    // TODO :
    // Start - Load only for fullPage design
    nkTemplate_addJSFile('modules/Admin/scripts/facebox.js', 'librairyPlugin');
    nkTemplate_addJSFile('modules/Admin/scripts/simpla.jquery.configuration.js');

    nkTemplate_addJS(
        'var condition_js = "'. (($nuked['screen'] == 'off') ? 1 : 0) .'";
        var lang_nuked = "'. $language .'";',
        'beforeLibs'
    );

    nkTemplate_addJSFile('modules/Admin/scripts/config.js');
    // End - Load only for fullPage design

    if ($module == 'Admin') {
        nkTemplate_addCSSFile('modules/Admin/css/Admin.css');
    }
    else {
        if (is_file('modules/'. $module .'/backend/'. $module .'.js'))
            nkTemplate_addJSFile('modules/'. $module .'/backend/'. $module .'.js');

        if (is_file('modules/'. $module .'/backend/'. $module .'.css'))
            nkTemplate_addCSSFile('modules/'. $module .'/backend/'. $module .'.css');
    }
}

/**
 * Set default value of $bgcolor vars.
 *
 * @param void
 * @return void
 */
function nkTemplate_setBgColors() {
    global $theme, $bgcolor1, $bgcolor2, $bgcolor3, $bgcolor4;

    $bgcolor1 = $bgcolor2 = $bgcolor3 = $bgcolor4 = null;

    require_once 'themes/'. $theme .'/colors.php';

    foreach ($GLOBALS['nkTemplate']['defaultColor'] as $color => $value) {
        if (! isset($GLOBALS[$color]))
            $GLOBALS[$color] = $value;
    }
}

/**
 * Initialize Js and Css common librairies for module frontend.
 *
 * @param void
 * @return void
 */
function nkTemplate_loadModuleMedias() {
    global $nuked, $bgcolor2, $bgcolor3;

    for ($i = 1; $i <= 4; $i++) {
        $GLOBALS['nkTemplate']['CSS']['string'] .= '.nkColor'. $i .'{color:'. $GLOBALS['bgcolor'. $i] .';}' ."\n"
            . '.nkBgColor'. $i .'{background:'. $GLOBALS['bgcolor'. $i] .';}' ."\n"
            . '.nkBorderColor'. $i .'{border-color:'. $GLOBALS['bgcolor'. $i] .' !important;}' ."\n"; // TODO : Why !important ?
    }

    if ($GLOBALS['nkTemplate']['pageDesign'] == 'fullPage') {
        nkTemplate_addJSFile('media/js/infobulle.js');
        nkTemplate_addJS('InitBulle(\''. $bgcolor2 .'\',\''. $bgcolor3 .'\', 2);');
        loadSyntaxhighlighterFiles();

        if ($nuked['stats_share'] == 1)
            nkStats_cron();
    }

    if (defined('EDITOR_CHECK')) {
        if ($nuked['editor_type'] == 'cke')
            loadCkeFiles();
        else if ($nuked['editor_type'] == 'tiny')
            loadTinymceFiles();
    }
}

/**
 * Set title of page.
 *
 * @param string $title : Title of page to set.
 * @param bool $add : Append title content at default title. ( $nuked['name'] .' - '. $nuked['slogan'] )
 * @return void
 */
function nkTemplate_setTitle($title, $append = false) {
    if ($title != '' && is_string($title)) {
        if ($append)
            $GLOBALS['nkTemplate']['title'] .= $title;
        else
            $GLOBALS['nkTemplate']['title'] = $title;
    }
}

/**
 * Set the interface to use.
 *
 * @param string $interface : Set if frontend view is use or backend view.
 * @return void
 */
function nkTemplate_setInterface($interface) {
    if (in_array($interface, array('frontend', 'backend')))
        $GLOBALS['nkTemplate']['interface'] = $interface;
}

/**
 * Return the interface used.
 *
 * @param void
 * @return string : The interface name.
 */
function nkTemplate_getInterface() {
    return $GLOBALS['nkTemplate']['interface'];
}

/**
 * Sets the page design style.
 *
 * @param string $pageDesign : The page design style to set. ( fullPage, nudePage ou none )
 * @return void
 */
function nkTemplate_setPageDesign($pageDesign) {
    if (in_array($pageDesign, array('fullPage', 'nudePage', 'none')))
        $GLOBALS['nkTemplate']['pageDesign'] = $pageDesign;
}

/**
 * Return the page design style. ( fullPage, nudePage ou none )
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
        if ($GLOBALS['nkTemplate']['interface'] == 'backend') {
            return applyTemplate('design/topAdmin');
        }
        else {
            if (function_exists('head')) {
                // Si la function head est défini dans le theme.php (themes de la version 1.8)
                head();
                top();
            } else {
                // Sinon on conserve la compatibilité avec les anciens thèmes
                ob_start();
                top();
                return ob_get_clean() . nkHandle_alert();
            }
        }
    }
    elseif ($GLOBALS['nkTemplate']['pageDesign'] == 'nudePage')
        return applyTemplate('design/topNude');
}

/**
 * Return the HTML code of footer page.
 *
 * @param void
 * @return string HTML code.
 */
function nkTemplate_getFooterOfPage() {
    if ($GLOBALS['nkTemplate']['pageDesign'] == 'fullPage') {
        if ($GLOBALS['nkTemplate']['interface'] == 'backend') {
            return applyTemplate('design/footerAdmin');
        }
        else {
            ob_start();
            footer();
            require_once 'Includes/copyleft.php';
            nkBenchmark_display();
            echo "\n</body>\n</html>\n";
            return ob_get_clean();
        }
    }
    elseif ($GLOBALS['nkTemplate']['pageDesign'] == 'nudePage')
        return "\n</body>\n</html>\n";
}

/**
 * Apply template and returns results.
 *
 * @param string $path : The path of template file. ( without extention )
 * @param array $data : The data to transmit at template.
 * @return string HTML code.
 */
function applyTemplate($path, $data = array()) {
    ob_start();
    extract($data);

    include 'views/'. $GLOBALS['nkTemplate']['interface'] .'/'. $path .'.php';

    return ob_get_clean();
}

/**
 * Add a CSS stylesheet to a global list that will be include by nkTemplate_append.
 *
 * @param string $file : The name of the file to include.
 * @param string $media : The media this stylesheet applies to.
 * @param mixed $conditionalComment : The string into square bracket for your condition, false if none.
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
 * @return string : HTML code.
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
 * @param string $type : The type of file to include. ( librairy, librairyPlugin or normal )
 * @return void
 */
function nkTemplate_addJSFile($file, $type = 'normal') {
    if ($type == 'librairy') {
        if (! array_key_exists($file, $GLOBALS['nkTemplate']['JS']['file']['librairy']))
            $GLOBALS['nkTemplate']['JS']['file']['librairy'][$file] = true;
    }
    elseif ($type == 'librairyPlugin') {
        if (! array_key_exists($file, $GLOBALS['nkTemplate']['JS']['file']['librairyPlugin']))
            $GLOBALS['nkTemplate']['JS']['file']['librairyPlugin'][$file] = true;
    }
    elseif ($type == 'normal') {
        if (! array_key_exists($file, $GLOBALS['nkTemplate']['JS']['file']['normal']))
            $GLOBALS['nkTemplate']['JS']['file']['normal'][$file] = true;
    }
}

/**
 * Add a Javascript block to a global list that will be include by nkTemplate_append.
 *
 * @param string $script : A set of Javascript instructions.
 * @param bool $jsType : If jQuery DOM ready function is added to js string.
 * @return void
 */
function nkTemplate_addJS($script, $jsType = 'normal') {
    if ($jsType == 'jqueryDomReady')
        $GLOBALS['nkTemplate']['JS']['string']['jqueryDomReady'] .= $script ."\n";
    else if ($jsType == 'beforeLibs')
        $GLOBALS['nkTemplate']['JS']['string']['beforeLibs'] .= $script ."\n";
    else
        $GLOBALS['nkTemplate']['JS']['string']['normal'] .= $script ."\n";
}

/**
 * Get HTML code for added Javascript file. One type of file is included.
 *
 * @param string $fileType : The type of Javascript file to add.
 * @return string HTML code.
 */
function nkTemplate_getJSFiles($fileType) {
    $html = '';

    if (! empty( $GLOBALS['nkTemplate']['JS']['file'][$fileType])) {
        foreach ($GLOBALS['nkTemplate']['JS']['file'][$fileType] as $file => $void)
            $html .= '<script type="text/javascript" src="'. $file .'"></script>' ."\n";
    }

    return $html;
}

/**
 * Get HTML code for added Javascript libs and block.
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

    $html .= nkTemplate_getJSFiles('librairy')
        . '<script type="text/javascript" src="'. JQUERY_LIBRAIRY .'"></script>' ."\n"
        . nkTemplate_getJSFiles('librairyPlugin')
        . nkTemplate_getJSFiles('normal');

    if ($GLOBALS['nkTemplate']['JS']['string']['normal'] != '' || $GLOBALS['nkTemplate']['JS']['string']['jqueryDomReady'] != '') {
        $html .= '<script type="text/javascript">' ."\n"
            . '// <![CDATA[' ."\n";

        if ($GLOBALS['nkTemplate']['JS']['string']['normal'] != '')
            $html .= $GLOBALS['nkTemplate']['JS']['string']['normal'];

        if ($GLOBALS['nkTemplate']['JS']['string']['jqueryDomReady'] != '') {
            $html .= '$(document).ready(function() { ' ."\n"
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
        'string'          => array(
            'normal'            => '',
            'jqueryDomReady'    => '',
            'beforeLibs'        => ''
        ),
    );

    return $html;
}

/**
 * Append meta generator, OpenSearch link, title, links of Rss feed
 * and JS and CSS librairy into the HEAD section of HTML output.
 *
 * @param string $contentTop : Current result of top() function.
 * @return string : Modified result of top() function.
 */
function nkTemplate_append($contentTop) {
    global $nuked;

    $append = '<meta name="generator" content="Nuked Klan - v'. $nuked['version'] .'" />' ."\n"
        . nkTemplate_getCSS()
        . nkTemplate_getJS();

    if ($GLOBALS['nkTemplate']['interface'] == 'frontend') {
        $append .= '<link rel="search" type="application/opensearchdescription+xml" title="'. $nuked['name'] .'" href="'. $nuked['url'] .'/opensearch.php" />' ."\n";

        if ($GLOBALS['nkTemplate']['pageDesign'] == 'fullPage') {
            $titlePrefix = $nuked['name'] .' RSS : ';

            foreach (explode('|', $nuked['rssFeed']) as $rssFeed) {
                $rssTitleConst = strtoupper($rssFeed) .'_RSS_TITLE';

                if (translationExist($rssTitleConst))
                    $title = __($rssTitleConst);
                else
                    $title = $rssFeed;

                $append .= '<link rel="alternate" title="'. $titlePrefix . $title .'" href="'. $nuked['url'] .'/rss/'. $rssFeed .'_rss.php" type="application/rss+xml" />' ."\n";
            }
        }
    }

    /*
    if (function_exists('nkTheme_head')) {
        nkTheme_head();
    }
    else {

    }
    */

    if ($GLOBALS['nkTemplate']['interface'] == 'frontend' && $GLOBALS['nkTemplate']['pageDesign'] == 'fullPage')
        $contentTop = preg_replace('#<title>(.*?)</title>#i', '<title>'. $GLOBALS['nkTemplate']['title'] .'</title>', $contentTop);

    $contentTop = str_ireplace('<head>', '<head>'. $append, $contentTop);

    return $contentTop;
}

/**
 * Build HTML content to display.
 *
 * @param string $content : The HTML content of Module.
 * @return string HTML code.
 */
function nkTemplate_renderPage($content) {
    global $nuked;

    ob_start();

    if ($GLOBALS['nkTemplate']['interface'] == 'frontend')
        nkTemplate_loadModuleMedias();

    $moduleMedias = ob_get_clean();

    if ($GLOBALS['nkTemplate']['title'] != '')
        $GLOBALS['nkTemplate']['title'] = $nuked['name'] .' - '. $nuked['slogan'];

    $contentTop    = nkTemplate_getTopOfPage();
    $contentFooter = nkTemplate_getFooterOfPage();

    /*
    list($contentTop, $content, $contentFooter) = preg_replace(
        '#<script[\s]*[type="text/javascript"]*[\s]*src="[A-z0-9:./_-]*(jquery)+[A-z0-9.:/_-]*"[\s]*[type="text/javascript"]*[\s]*>#',
        '',
        array($contentTop, $content, $contentFooter)
    );
    */

    $contentTop = nkTemplate_append($contentTop);

    return $contentTop . $content . $moduleMedias . $contentFooter;
}


/**
 * Run block list of defined side and display result.
 *
 * @param string $side : Side of block to run.
 * @return void
 */
//function nkTemplate_getBlock($side) {
function get_blok($side) {
    global $file, $visiteur, $nuked;

    //if ($side == 'left') {
    if ($side == 'gauche') {
        $active = 1;
        $nuked['IsBlok'] = true;
    }
    //else if ($side == 'right') {
    else if ($side == 'droite') {
        $active = 2;
        $nuked['IsBlok'] = true;
    }
    //else if ($side == 'center') {
    else if ($side == 'centre') {
        $active = 3;
    }
    //else if ($side == 'bottom') {
    else if ($side == 'bas') {
        $active = 4;
    }

    $themeBlockFunction = 'block_'. $side;

    $dbsBlock = nkDB_selectMany(
        'SELECT *
        FROM '. BLOCK_TABLE .'
        WHERE active = '. $active,
        array('position')
    );

    foreach ($dbsBlock as $block) {
        $block['titre'] = printSecuTags($block['titre']);
        $block['page'] = explode('|', $block['page']);

        if ($visiteur >= $block['nivo'] && (in_array($file, $block['page']) || in_array('Tous', $block['page']))) {
            if (file_exists($blockFile = 'Includes/blocks/block_'. $block['type'] .'.php'))
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

            if (! empty($block['content'])) $themeBlockFunction($block);
        }
    }

    $nuked['IsBlok'] = false;
}

/*
function get_blok($side){
    trigger_error('get_blok function is deprecated. Please update your theme.', E_USER_DEPRECATED);

    $translatedSide = array(
        'gauche'    => 'left',
        'droite'    => 'right',
        'centre'    => 'center',
        'bas'       => 'bottom'
    );

    if (array_key_exists($side, $translatedSide))
        nkTemplate_getBlock($translatedSide[$side]);
}
*/

?>