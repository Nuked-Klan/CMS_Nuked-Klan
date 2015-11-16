<?php
/**
 * nkRss.php
 *
 * Librairy for generate RSS 2.0 feed
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * Initialisation of nkRss global vars
 */
$GLOBALS['nkRss'] = array(
    'itemsContent' => ''
);


/**
 * Return language used in Rss feed
 *
 * @param void
 * @return string : the language corresponding to the cms
 */
function nkRss_getLanguage() {
    global $nuked;

    $language = array(
        'french'    => 'fr',
        'english'   => 'en-us'
    );

    if (array_key_exists($nuked['langue'], $language))
        return $language[$nuked['langue']];

    return 'en-us';
}

/**
 * Protect node text against special characters
 *
 * @param string $str : The raw text
 * @return string : The node text protected
 */
function nkRss_formatNodeText($str) {
    $str = @nkHtmlEntityDecode($nuked['name']);
    $str = str_replace('&amp;', '&', $str);

    return nkHtmlSpecialChars($str);
}

/**
 * Format item description. Cut description test if too long and protect it
 *
 * @param string $description : The raw description
 * @return string : The description formated and protected
 */
function nkRss_formatDescription($description) {
    if ($description != '') {
        $description = strip_tags($description);

        if (strlen($description) > 300)
            $description = substr($description, 0, 300) .'...';

        $description = nkHtmlSpecialChars($description);
    }

    return $description;
}

/**
 * Format and return the Rss feed xml structure
 *
 * @param void
 * @return string : The Rss feed xml structure
 */
function nkRss_getFeedXmlStructure() {
    global $nuked;

    $sitename = nkRss_formatNodeText($nuked['name']);
    $sitedesc = nkRss_formatNodeText($nuked['slogan']);

    return '<?xml version="1.0" encoding="ISO-8859-1"?>' ."\n"
        . '<rss version="2.0">' ."\n"
        . '<channel>' ."\n"
        . '<title>'. $sitename .'</title>' ."\n"
        . '<link>'. $nuked['url'] .'</link>' ."\n"
        . '<image>' ."\n"
        . '<url>'. $nuked['url'] .'/images/ban.png</url>' ."\n"
        . '<title>'. $sitename .'</title>' ."\n"
        . '<link>'. $nuked['url'] .'</link>' ."\n"
        . '<width>96</width>' ."\n"
        . '<height>31</height>' ."\n"
        . '</image>' ."\n"
        . '<description>'. $sitedesc .'</description>' ."\n"
        . '<language>'. nkRss_getLanguage() .'</language>' ."\n"
        . '<webMaster>'. $nuked['mail'] .'</webMaster>' ."\n"
        . $GLOBALS['nkRss']['itemsContent']
        . '</channel>' ."\n"
        . '</rss>';
}

/**
 * Format and store a Rss feed item node
 *
 * @param string $title : The title node of item node
 * @param string $link : The link node of item node
 * @param int $pubDate : The timestamp of pubDate node
 * @param string $description : The description node of item node
 * @return void
 */
function nkRss_addItem($title, $link, $pubDate, $description) {
    $GLOBALS['nkRss']['itemsContent'] .= '<item>' ."\n"
        . '<title>'. nkHtmlSpecialChars($title) .'</title>' ."\n"
        . '<link>'. $link .'</link>' ."\n"
        . '<pubDate>'. date('r', $pubDate) .'</pubDate>' ."\n"
        . '<description>'. nkRss_formatDescription($description) .'</description>' ."\n"
        . '</item>' ."\n";
}

?>