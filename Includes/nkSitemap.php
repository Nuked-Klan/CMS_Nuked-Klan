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


function UpdateSitmap() {
    global $nuked;

    if (($fp = fopen(dirname(__FILE__) .'/sitemap.xml', 'wb')) !== false) {
        $Sitemap = "<?xml version='1.0' encoding='UTF-8'?>\r\n";
        $Sitemap .= "<urlset xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"\r\n";
        $Sitemap .= "xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\"\r\n";
        $Sitemap .= "xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n";

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
            $Sitemap .= "\t<url>\r\n";
            $Sitemap .= "\t\t<loc>$nuked[url]/index.php?file=". $module['nom'] ."</loc>\r\n";

            $Last = date('Y-m-d');

            switch ($module['nom']) {
                case 'News':
                    //$Last = nkDB_execute('SELECT date FROM ' . NEWS_TABLE . 'ORDER BY date DESC LIMIT 1');
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
                    //$Last = nkDB_execute('SELECT date FROM ' . DOWNLOAD_TABLE . 'ORDER BY date DESC LIMIT 1');
                    $Sitemap .= "\t\t<priority>0.5</priority>\r\n";
                    $Sitemap .= "\t\t<lastmod>$Last</lastmod>\r\n";
                    $Sitemap .= "\t\t<changefreq>weekly</changefreq>\r\n";
                    break;

                default:
                    $Sitemap .= "\t\t<priority>0.5</priority>\r\n";
            } // switch

            $Sitemap .= "\t</url>\r\n";
        }

        $Sitemap .= "</urlset>\r\n";

        fwrite($fp, chr(0xEF) . chr(0xBB)  . chr(0xBF) . utf8_encode($Sitemap)); //Ajout de la marque d'Octet
        fclose($fp);
    }
}

?>