<?php
/**
 * download_rss.php
 *
 * Display Download Rss feed
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
define('INDEX_CHECK', 1);
ini_set('default_charset', 'ISO8859-1');

require_once 'Includes/fatal_errors.php';
require_once '../globals.php';
require_once '../conf.inc.php';
require_once '../nuked.php';
require_once '../Includes/nkRss.php';
require_once '../lang/'. $nuked['langue'] .'.lang.php';


$dbrDownload = nkDB_selectMany(
    'SELECT id, titre, description, date
    FROM '. DOWNLOAD_TABLE .'
    WHERE level = 0',
    array('date'), 'DESC', 20
);

foreach ($dbrDownload as $download) {
    nkRss_addItem(
        $download['titre'],
        $nuked['url'] .'/index.php?file=Download&amp;op=description&amp;dl_id='. $download['id'],
        $download['date'],
        $download['description']
    );
}

header('Content-Type: text/xml');

echo nkRss_getFeedXmlStructure();

?>