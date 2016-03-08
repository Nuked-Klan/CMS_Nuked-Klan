<?php
/**
 * sections_rss.php
 *
 * Display Sections Rss feed
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

if (nivo_mod('Sections') == -1) exit;

require_once '../Includes/nkRss.php';
require_once '../lang/'. $nuked['langue'] .'.lang.php';


$dbrSections = nkDB_selectMany(
    'SELECT artid, title, content, date
    FROM '. SECTIONS_TABLE,
    array('date'), 'DESC', 20
);

foreach ($dbrSections as $section) {
    nkRss_addItem(
        $section['title'],
        $nuked['url'] .'/index.php?file=Sections&amp;op=article&amp;artid='. $section['artid'],
        $section['date'],
        $section['content']
    );
}

header('Content-Type: text/xml');

echo nkRss_getFeedXmlStructure();

?>