<?php
/**
 * english.lang.php
 *
 * English translation file of Vote module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

return array(
    // vote_index - modules/Vote/index.php
    'VOTE_UNACTIVE' => 'Vote unactive',
    // postVote / saveVote - modules/Vote/index.php
    'VOTE_FROM'     => 'Vote from',
    'ALREADY_VOTE'  => 'You already voted !',
    // saveVote - modules/Vote/index.php
    'VOTE_ADD'      => 'Rating was successfully registered',
    // editVoteModules - modules/Vote/admin.php
    'ADMIN_VOTE'    => 'Votes Administration',
    // saveVoteModules - modules/Vote/admin.php
    'ACTION_MODIF_VOTE_MODULES' => 'has modified the list of vote module.',
    'VOTE_MODULES_MODIFIED' => 'List of vote module has been succesfully modified.',
    // modules/Vote/config/backend/voteModules.php
    'AUTHORIZED_VOTE_MODULES' => 'List of modules where the votes are authorised.',
    // views/frontend/modules/Vote/voteForm.php
    // views/frontend/modules/Vote/voteIndex.php
    'NOTE'          => 'Rating',
    // views/frontend/modules/Vote/voteIndex.php
    'VOTES'         => 'votes',
    'NOT_EVAL'      => 'Not evaluated',
    'RATE'          => 'Rate',
    // views/frontend/modules/Vote/voteForm.php
    'ONE_VOTE_ONLY' => 'You can vote only once',
);

?>