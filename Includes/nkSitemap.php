<?php
/**
 * nkSitemap.php
 *
 * Manage sitemap.xml file.
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * Format url node of sitemap.
 *
 * @param string $module : The name of module.
 * @param float $priority : The
 * @param mixed $lastmod : The date of last modification if added in url node, false also.
 * @param mixed $changefreq : Value of update frequency of url node if added in url node, false also.
 * @return string XML code
 */
function nkSitemap_addUrlNode($module, $priority, $lastmod = false, $changefreq = false) {
    global $nuked;

    $xml = "\t" .'<url>' ."\r\n"
        . "\t\t" .'<loc>'. $nuked['url'] .'/index.php?file='. $module .'</loc>' ."\r\n"
        . "\t\t" .'<priority>'. $priority .'</priority>' ."\r\n";

    if ($lastmod !== false)
        $xml .= "\t\t" .'<lastmod>'. $lastmod .'</lastmod>' ."\r\n";

    if ($changefreq !== false)
        $xml .= "\t\t" .'<changefreq>daily</changefreq>' ."\r\n";

    return $xml . "\t" .'</url>' ."\r\n";
}

/**
 * Format sitemap content.
 *
 * @param void
 * @return string XML code of sitemap content.
 */
function nkSitemap_getContent() {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' ."\r\n"
        . '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' ."\r\n"
        . 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"' ."\r\n"
        . 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' ."\r\n";

    $dbrModules = nkDB_selectMany(
        'SELECT nom
        FROM '. MODULES_TABLE .'
        WHERE niveau = 0'
    );

    $moduleList = array_diff(
        array_values(array_column($dbrModules, 'nom')),
        array('Suggest', 'Comment', 'Vote', 'Textbox', 'Members')
    );

    foreach ($moduleList as $module) {
        $lastmod = date('Y-m-d');

        switch ($module) {
            case 'News':
                //$dbrNews = nkDB_execute('SELECT date FROM '. NEWS_TABLE, array('date'), 'DESC', 1);
                //$lastmod = $dbrNews['date'];
                $xml .= nkSitemap_addUrlNode($module, 0.8, $lastmod, 'daily');
                break;

            case 'Forum':
                $xml .= nkSitemap_addUrlNode($module, 0.4, $lastmod, 'always');
                break;

            case 'Download':
                //$dbrDownload = nkDB_execute('SELECT date FROM '. DOWNLOAD_TABLE, array('date'), 'DESC', 1);
                //$lastmod = $dbrDownload['date'];
                $xml .= nkSitemap_addUrlNode($module, 0.5, $lastmod, 'weekly');
                break;

            default:
                $xml .= nkSitemap_addUrlNode($module, 0.5);
        }
    }

    $xml .= '</urlset>' ."\r\n";

    return $xml;
}

/**
 * Write sitemap file.
 *
 * @param void
 * @return bool Result of writing file.
 */
function nkSitemap_write() {
    if (is_writable('sitemap.xml')) {
        $xml       = chr(0xEF) . chr(0xBB)  . chr(0xBF) . utf8_encode(nkSitemap_getContent());
        $xmlLength = strlen($xml);
        $result    = file_put_contents('sitemap.xml', $xml);

        if ($result !== false && $result === $xmlLength)
            return true;
    }

    return false;
}

?>
