<?php
/**
 * nkSitemap.php
 *
 *
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


function nkSitemap_addUrlNode($module, $priority, $lastmod = false, $changefreq = false) {
    $xml = "\t" .'<url>' ."\r\n"
        . "\t\t" .'<loc>'. $nuked['url'] .'/index.php?file='. $module .'</loc>' ."\r\n"
        . "\t\t" .'<priority>'. $priority .'</priority>' ."\r\n";

    if ($lastmod !== false)
        $xml .= "\t\t" .'<lastmod>'. $Last .'</lastmod>' ."\r\n";

    if ($changefreq !== false)
        $xml .= "\t\t" .'<changefreq>daily</changefreq>' ."\r\n";

    return $xml . "\t" .'</url>' ."\r\n";
}

function nkSitemap_write() {
    global $nuked;

    //if (is_writable($file = dirname(__FILE__) .'/sitemap.xml')) {
        //
        //file_put_contents($file, $Sitemap);
        //return true;
    //}

    //return false;

    if (($fp = fopen(dirname(__FILE__) .'/sitemap.xml', 'wb')) !== false) {
        $Sitemap = '<?xml version="1.0" encoding="UTF-8"?>' ."\r\n"
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
            $Last = date('Y-m-d');

            switch ($module['nom']) {
                case 'News':
                    //$dbrNews = nkDB_execute('SELECT date FROM '. NEWS_TABLE, array('date'), 'DESC', 1);
                    //$lastmod = $dbrNews['date'];
                    $Sitemap .= nkSitemap_addUrlNode($module['nom'], 0.8, $lastmod, 'daily');
                    break;

                case 'Forum':
                    $Sitemap .= nkSitemap_addUrlNode($module['nom'], 0.4, $lastmod, 'always');
                    break;

                case 'Download':
                    //$dbrDownload = nkDB_execute('SELECT date FROM '. DOWNLOAD_TABLE, array('date'), 'DESC', 1);
                    //$lastmod = $dbrDownload['date'];
                    $Sitemap .= nkSitemap_addUrlNode($module['nom'], 0.5, $lastmod, 'weekly');
                    break;

                default:
                    $Sitemap .= nkSitemap_addUrlNode($module['nom'], 0.5);
            }
        }

        $Sitemap .= '</urlset>' ."\r\n";

        fwrite($fp, chr(0xEF) . chr(0xBB)  . chr(0xBF) . utf8_encode($Sitemap)); //Ajout de la marque d'Octet
        fclose($fp);
    }
}

?>