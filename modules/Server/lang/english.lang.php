<?php
/**
 * english.lang.php
 *
 * English translation file of Server module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

define("_ON","on");
define("_MOREINFOS","More info");
define("_SEVERDOWN","Server Down...");
define("_NAME","Name");

define("_MAP","Map");
define("_PLAYER","Players");
define("_NOSERVER","No Servers in this Category");
define("_SERVERINFOS","Server Search");
define("_SEARCH","Search");

define("_SERVERDETAIL","Server details");
define("_ADDRESS","Address");
define("_NBPLAYER","Players");
define("_GAME","Game");
define("_SYSTEMOS","OS System");
define("_SERVERTYPE","Server Type");
define("_SERVERRULES","Server Rules");
define("_SERVERVERSION","Version");
define("_PLAYERID","Id");
define("_NICK","Nick");
define("_FRAG","Frags");
define("_SCORE","Score");
define("_HONOR","Honour");
define("_DEATHS","Deaths");
define("_PING","Ping");
define("_NOPLAYERS","No players");

return array(
    // modules/Server/backend/category.php
    // modules/Server/backend/index.php
    'ADMIN_SERVER'         => 'Servers Administration',
    // modules/Server/backend/index.php
    'ADD_SERVER'           => 'Add Server',
    'EDIT_THIS_SERVER'     => 'Edit this Server',
    'DELETE_THIS_SERVER'   => 'Remove this Server',
    'NO_SERVER_IN_DB'      => 'No server in database',
    'ADD_THIS_SERVER'      => 'Create Server',
    'MODIFY_THIS_SERVER'   => 'Modify this Server',
    'SERVER_ADDED'         => 'Server was successfully added.',
    'SERVER_MODIFIED'      => 'Server was successfully modified.',
    'SERVER_DELETED'       => 'Server was successfully removed.',
    'ACTION_ADD_SERVER'    => 'have added a server',
    'ACTION_EDIT_SERVER'   => 'have modified a server',
    'ACTION_DELETE_SERVER' => 'have deleted a server',
    // modules/Server/backend/category.php
    'ACTION_ADD_SERVER_CATEGORY' => 'has added the server category',
    'ACTION_EDIT_SERVER_CATEGORY' => 'has modified the server category',
    'ACTION_DELETE_SERVER_CATEGORY' => 'has deleted the server category',
    // modules/Server/backend/config/menu.php
    'SERVER'                => 'Servers',
    // modules/Server/backend/config/server.php
    'SERVER_IP'             => 'Ip address',
    'SERVER_PORT'           => 'Port',
    'SERVER_GAME'           => 'Server type',
    'SERVER_PASSWORD'       => 'Password'
);

?>