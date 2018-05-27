<?php
/**
 * selectTeamStatusOptions.php
 *
 * Configuration for generate Team status list of input select options.
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

return array(
    'defaultValue' => array('' => __('NONE')),
    'sql' => array(
        'query' => 'SELECT id, name FROM '. TEAM_STATUS_TABLE,
        'order' => array('name')
    ),
    'key' => 'id',
    'value' => 'name'
);

?>
