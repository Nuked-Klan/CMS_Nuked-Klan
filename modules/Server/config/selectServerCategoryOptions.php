<?php
/**
 * selectServerCategoryOptions.php
 *
 * Configuration for generate Server category list of input select options.
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

return array(
    'defaultValue' => array('' => __('NONE')),
    'sql' => array(
        'query' => 'SELECT cid, titre FROM '. SERVER_CAT_TABLE,
        'order' => array('titre')
    ),
    'key' => 'cid',
    'value' => 'titre'
);

?>