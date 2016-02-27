<?php
/**
 * french.lang.php
 *
 * French translation file of Server module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

define("_ON","sur");
define("_MOREINFOS","+ d'infos");
define("_SEVERDOWN","Serveur Down...");
define("_NAME","Nom");

define("_MAP","Map");
define("_PLAYER","Joueurs");
define("_NOSERVER","Aucun serveur pour cette catégorie");
define("_SERVERINFOS","Infos sur un serveur");
define("_SEARCH","Chercher");
define("_EXECTHISFILE","Cochez Exécutez ce programme à partir de son emplacement actuel et cliquez sur ok pour rejoindre le serveur");

define("_SERVERDETAIL","Serveur en détails");
define("_ADDRESS","Adresse");
define("_NBPLAYER","Nb de joueurs");
define("_GAME","Game");
define("_SYSTEMOS","Système Os");
define("_SERVERTYPE","Type de serveur");
define("_SERVERRULES","Serveur Rules");
define("_SERVERVERSION","Version");
define("_PLAYERID","Id");
define("_NICK","Pseudo");
define("_SCORE","Score");
define("_FRAG","Frags");
define("_HONOR","Honeur");
define("_DEATHS","Morts");
define("_PING","Ping");
define("_NOPLAYERS","Aucun joueur sur ce serveur");

return array(
    // modules/Server/backend/category.php
    // modules/Server/backend/index.php
    'ADMIN_SERVER'         => 'Administration Serveurs',
    // modules/Server/backend/index.php
    'ADD_SERVER'           => 'Ajouter un Serveur',
    'EDIT_THIS_SERVER'     => 'Editer ce Serveur',
    'DELETE_THIS_SERVER'   => 'Supprimer ce Serveur',
    'NO_SERVER_IN_DB'      => 'Aucune serveur dans la base de données',
    'ADD_THIS_SERVER'      => 'Créer un Serveur',
    'MODIFY_THIS_SERVER'   => 'Modifier ce Serveur',
    'SERVER_ADDED'         => 'Serveur ajoutée avec succès.',
    'SERVER_MODIFIED'      => 'Serveur modifiée avec succès.',
    'SERVER_DELETED'       => 'Serveur supprimée avec succès.',
    'ACTION_ADD_SERVER'    => 'a ajouté le serveur',
    'ACTION_EDIT_SERVER'   => 'a modifié le serveur',
    'ACTION_DELETE_SERVER' => 'a supprimé le serveur',
    // modules/Server/backend/category.php
    'ACTION_ADD_SERVER_CATEGORY' => 'a ajouté la catégorie serveur',
    'ACTION_EDIT_SERVER_CATEGORY' => 'a modifié la catégorie serveur',
    'ACTION_DELETE_SERVER_CATEGORY' => 'a supprimé la catégorie serveur',
    // modules/Server/backend/config/menu.php
    'SERVER'                => 'Serveurs',
    // modules/Server/backend/config/server.php
    'SERVER_IP'             => 'Adresse Ip',
    'SERVER_PORT'           => 'Port',
    'SERVER_GAME'           => 'Type de serveur',
    'SERVER_PASSWORD'       => 'Password'
);

?>