<?php
/**
 * forum_rss.php
 *
 * Display Forum Rss feed
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
require_once '../Includes/nkRss.php';
require_once '../lang/'. $nuked['langue'] .'.lang.php';


$dbrForum = nkDB_selectMany(
    'SELECT T.id, T.forum_id
    FROM '. FORUM_THREADS_TABLE .' AS T
    LEFT JOIN '. FORUM_TABLE .' AS F ON T.forum_id = F.id
    WHERE F.niveau = 0',
    array('last_post'), 'DESC', 20
);

foreach ($dbrForum as $forum) {
    $dbrForumMessage = nkDB_selectMany(
        'SELECT auteur, titre, txt, date
        FROM '. FORUM_MESSAGES_TABLE .'
        WHERE thread_id = \'' . $forum['id'] . '\'',
        array('id'), 'DESC', 1
    );

    nkRss_addItem(
        $dbrForumMessage['titre'],
        $nuked['url'] .'/index.php?file=Forum&amp;page=viewtopic&amp;forum_id='. $forum['forum_id'] .'&amp;thread_id='. $forum['id'],
        $dbrForumMessage['date'],
        $dbrForumMessage['auteur'] .' : '. $dbrForumMessage['txt']
    );
}

header('Content-Type: text/xml');

echo nkRss_getFeedXmlStructure();

?>