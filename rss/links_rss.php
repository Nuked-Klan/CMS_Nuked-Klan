<?php
/**
 * links_rss.php
 *
 * Display Links Rss feed
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
define('INDEX_CHECK', 1);
ini_set('default_charset', 'ISO8859-1');

require_once '../Includes/fatal_errors.php';
require_once '../globals.php';
require_once '../conf.inc.php';
require_once '../nuked.php';

if (nivo_mod('Links') == -1) exit;

require_once '../Includes/nkRss.php';
require_once '../lang/'. $nuked['langue'] .'.lang.php';


$dbrLinks = nkDB_selectMany(
    'SELECT id, titre, description, date
    FROM '. LINKS_TABLE,
    array('date'), 'DESC', 20
);

foreach ($dbrLinks as $link) {
    nkRss_addItem(
        $link['titre'],
        $nuked['url'] .'/index.php?file=Links&amp;op=description&amp;link_id='. $link['id'],
        $link['date'],
        $link['description']
    );
}

header('Content-Type: text/xml');

echo nkRss_getFeedXmlStructure();

?>