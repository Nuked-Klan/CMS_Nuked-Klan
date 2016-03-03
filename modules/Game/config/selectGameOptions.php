<?php
/**
 * selectGameOptions.php
 *
 * Configuration for generate Game list of input select options.
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

return array(
    'sql' => array(
        'query' => 'SELECT id, name FROM '. GAMES_TABLE,
        'order' => array('name')
    ),
    'key' => 'id',
    'value' => 'name'
);

?>