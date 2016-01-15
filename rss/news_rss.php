<?php
/**
 * news_rss.php
 *
 * Display News Rss feed
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
define('INDEX_CHECK', 1);
ini_set('default_charset', 'ISO8859-1');

require_once '../Includes/fatal_errors.php';
require_once '../globals.php';
require_once '../conf.inc.php';
require_once '../nuked.php';

if (nivo_mod('News') == -1) exit;

require_once '../Includes/nkRss.php';
require_once '../lang/'. $nuked['langue'] .'.lang.php';


$dbrNews = nkDB_selectMany(
    'SELECT id, titre, texte, date
    FROM '. NEWS_TABLE,
    array('date'), 'DESC', 20
);

foreach ($dbrNews as $news) {
    nkRss_addItem(
        $news['titre'],
        $nuked['url'] .'/index.php?file=News&amp;op=index_comment&amp;news_id='. $news['id'],
        $news['date'],
        $news['texte']
    );
}

//print_r($dbsNews); die;

header('Content-Type: text/xml');

echo nkRss_getFeedXmlStructure();

?>