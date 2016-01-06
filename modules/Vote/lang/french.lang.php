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

return array(
    // vote_index - modules/Vote/index.php
    'VOTE_UNACTIVE' => 'Vote désactivé',
    // postVote / saveVote - modules/Vote/index.php
    'VOTE_FROM'     => 'Vote de',
    'ALREADY_VOTE'  => 'Vous avez déjà voté !',
    // saveVote - modules/Vote/index.php
    'VOTE_ADD'      => 'Votre vote a bien été enregistré',
    // editVoteModules - modules/Vote/admin.php
    'ADMIN_VOTE'    => 'Administration des Votes',
    // saveVoteModules - modules/Vote/admin.php
    'ACTION_MODIF_VOTE_MODULES' => 'a modifié la liste des modules votés.',
    'VOTE_MODULES_MODIFIED' => 'Liste des modules votés modifiés avec succès.',
    // modules/Vote/config/backend/voteModules.php
    'AUTHORIZED_VOTE_MODULES' => 'Liste des modules où les votes sont autorisées.',
    // views/frontend/modules/Vote/voteForm.php
    // views/frontend/modules/Vote/voteIndex.php
    'NOTE'          => 'Note',
    // views/frontend/modules/Vote/voteIndex.php
    'VOTES'         => 'votes',
    'NOT_EVAL'      => 'Non évalué',
    'RATE'          => 'Evaluer',
    // views/frontend/modules/Vote/voteForm.php
    'ONE_VOTE_ONLY' => 'Vous ne pouvez voter qu\'une seule fois',
);

?>