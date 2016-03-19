<?php
/**
 * blok.php
 *
 * Display block of Forum module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or exit('You can\'t run this file alone.');

global $language, $visiteur, $bgcolor3, $bgcolor2;

translate('modules/Forum/lang/'. $language .'.lang.php');

require_once 'modules/Forum/core.php';

// Prepare fields list for SQL query
$fields = 'FT.id, FT.titre, FT.last_post, FT.forum_id, FT.nbReplies';

if ($active == 3 || $active == 4)
    $fields = ', FT.auteur, FT.auteur_id, FT.view';

// Get Forum topic list
$dbrForumThread = nkDB_selectMany(
    'SELECT '. $fields .'
    FROM '. FORUM_THREADS_TABLE .' AS FT
    INNER JOIN '. FORUM_TABLE .' AS F
    ON F.id = FT.forum_id
    WHERE F.niveau <= '. $visiteur,
    array('FT.last_post'), 'DESC', 10
);

echo applyTemplate('modules/Forum/block', array(
    'bgcolor2'          => $bgcolor2,
    'bgcolor3'          => $bgcolor3,
    'active'            => $active,
    'forumThreadList'   => $dbrForumThread
));

?>
