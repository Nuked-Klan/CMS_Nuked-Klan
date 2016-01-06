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

// admin - module_vote
define("_ADMINVOTE","Votes Administration");
define("_VOTEMOD","Vote module list");
define("_LISTI","List of modules where the votes are authorised.<br /><br />");
define("_DESACTIVER","Unactive");
define("_ACTIVER","Active");
define("_MODIF","Modify");
define("_BACK","Back");
// admin - modify_module_vote
define("_ACTIONMODIFVOTEMOD","has modified the list of vote module");
define("_VOTEMODIFMOD","List of vote module has been succesfully modified.");

return array(
    // vote_index - modules/Vote/index.php
    'VOTE_UNACTIVE' => 'Vote unactive',
    // postVote / saveVote - modules/Vote/index.php
    'VOTE_FROM'     => 'Vote from',
    'ALREADY_VOTE'  => 'You already voted !',
    // saveVote - modules/Vote/index.php
    'VOTE_ADD'      => 'Rating was successfully registered',
    // views/frontend/modules/Vote/voteIndex.php / views/frontend/modules/Vote/voteForm.php
    'NOTE'          => 'Rating',
    // views/frontend/modules/Vote/voteIndex.php
    'VOTES'         => 'votes',
    'NOT_EVAL'      => 'Not evaluated',
    'RATE'          => 'Rate',
    // views/frontend/modules/Vote/voteForm.php
    'ONE_VOTE_ONLY' => 'You can vote only once',
);

?>