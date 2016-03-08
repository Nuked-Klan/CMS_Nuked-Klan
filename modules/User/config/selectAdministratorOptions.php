<?php
/**
 * selectAdministratorOptions.php
 *
 * Configuration for generate Administrator list of input select options.
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

return array(
    'sql' => array(
        'query' => 'SELECT id, pseudo FROM '. USER_TABLE .' WHERE niveau >= 2',
        'order' => array('pseudo')
    ),
    'key' => 'id',
    'value' => 'pseudo'
);

?>