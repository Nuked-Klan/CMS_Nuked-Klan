<?php
/**
 * gallery_rss.php
 *
 * Display Gallery Rss feed
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


$dbrGallery = nkDB_selectMany(
    'SELECT sid, titre, description, date
    FROM '. GALLERY_TABLE,
    array('date'), 'DESC', 20
);

foreach ($dbrGallery as $gallery) {
    nkRss_addItem(
        $gallery['titre'],
        $nuked['url'] .'/index.php?file=Gallery&amp;op=description&amp;sid='. $gallery['sid'],
        $gallery['date'],
        $gallery['description']
    );
}

header('Content-Type: text/xml');

echo nkRss_getFeedXmlStructure();

?>