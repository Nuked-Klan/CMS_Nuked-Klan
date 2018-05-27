<?php
/**
 * selectForumCategoryOptions.php
 *
 * Configuration for generate Forum category list of input select options.
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

return array(
    'sql' => array(
        'query' => 'SELECT id, nom FROM '. FORUM_CAT_TABLE,
        'order' => array('ordre', 'nom')
    ),
    'key' => 'id',
    'value' => 'nom'
);

?>
