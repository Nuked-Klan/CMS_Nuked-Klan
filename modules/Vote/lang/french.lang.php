<?php
/**
 * french.lang.php
 *
 * French translation file of Vote module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

// admin - module_vote
define("_ADMINVOTE","Administration des Votes");
define("_VOTEMOD","Liste des modules votés");
define("_LISTI","Liste des modules où les votes sont autorisées.<br /><br />");
define("_DESACTIVER","Désactiver");
define("_ACTIVER","Activer");
define("_MODIF","Modifier");
define("_BACK","Retour");
// admin - modify_module_vote
define("_ACTIONMODIFVOTEMOD","a modifié la liste des modules votés");
define("_VOTEMODIFMOD","Liste des modules votés modifiés avec succès.");


return array(
    // vote_index - modules/Vote/index.php
    'VOTE_UNACTIVE' => 'Vote désactivé',
    // postVote / saveVote - modules/Vote/index.php
    'VOTE_FROM'     => 'Vote de',
    'ALREADY_VOTE'  => 'Vous avez déjà voté !',
    // saveVote - modules/Vote/index.php
    'VOTE_ADD'      => 'Votre vote a bien été enregistré',
    // views/frontend/modules/Vote/voteIndex.php / views/frontend/modules/Vote/voteForm.php
    'NOTE'          => 'Note',
    // views/frontend/modules/Vote/voteIndex.php
    'VOTES'         => 'votes',
    'NOT_EVAL'      => 'Non évalué',
    'RATE'          => 'Evaluer',
    // views/frontend/modules/Vote/voteForm.php
    'ONE_VOTE_ONLY' => 'Vous ne pouvez voter qu\'une seule fois',
);

?>