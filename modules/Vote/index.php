<?php
/**
 * index.php
 *
 * Frontend of Vote module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

translate('modules/Vote/lang/'. $language .'.lang.php');
nkTemplate_addCSSFile('modules/Vote/backend/Vote.css');


/**
 * Display vote result / status.
 * Included by module.
 *
 * @param string $module : The name of module.
 * @param int $imId : The ID of module data join to vote data.
 * @return void
 */
function vote_index($module, $imId) {
    global $visiteur;

    $imId   = (int) $imId;
    $module = stripslashes($module);

    if (! checkVoteStatus($module, $imId)) {
        echo '<b>'. __('VOTE_UNACTIVE') . '</b>';
        return;
    }

    include_once 'modules/Vote/config/config.php';

    $dbrVote = nkDB_selectMany(
        'SELECT vote FROM '. VOTE_TABLE .'
        WHERE vid = '. $imId .' AND module = '. nkDB_escape($module)
    );

    $nbVote = nkDB_numRows();
    $note   = 0;

    if ($nbVote > 0) {
        foreach ($dbrVote as $vote)
            $note += $vote['vote'] / $nbVote;

        $note = ceil($note);
    }

    echo applyTemplate('modules/Vote/voteIndex', array(
        'note'          => $note,
        'nbVote'        => $nbVote,
        'userLevel'     => $visiteur,
        'levelAccess'   => nivo_mod('Vote'),
        'module'        => $module,
        'imId'          => $imId,
        'voteCfg'       => $voteCfg
    ));
}

/**
 * Callback function for nkAction_init.
 * Check if the user has the right to access Vote.
 *
 * @param void
 * @return bool
 */
function checkVoteAccess() {
    global $nkAction, $visiteur, $imId, $module;

    if (checkVoteStatus($module, $imId)) {
        $levelAccess = nivo_mod('Vote');

        if ($visiteur >= $levelAccess && $levelAccess > -1) {
            if (checkAlreadyVote($module, $imId))
                printNotification(__('ALREADY_VOTE'), 'error', array('closeLink' => true));
            else
                return true;
        }
        else
            echo applyTemplate('nkAlert/noEntrance', array('closeLink' => true));
    }

    return false;
}

/**
 * Check if module data exist.
 *
 * @param string $module : The name of module.
 * @param int $imId : The ID of module data join to vote data.
 * @return bool
 */
function checkVoteStatus($module, $imId) {
    if (! empty($module) && preg_match('/^[A-Za-z_]+$/', $module)) {
        $tableConstName = strtoupper($module) .'_TABLE';

        if (defined($tableConstName .'_ID'))
            $tableIdName = constant($tableConstName .'_ID');
        else
            $tableIdName = 'id';

        $nbVoteModuleData = nkDB_totalNumRows(
            'FROM '. nkDB_escape(constant($tableConstName), true) .'
            WHERE '. nkDB_escape($tableIdName, true) .' = '. intval($imId)
        );

        if ($nbVoteModuleData > 0) {
            return true;
        }
        else {
            // vote don't exist
        }
    }
    else {
        // bad module name
    }

    return false;
}

/**
 * Check if user have already voted.
 *
 * @param string $module : The name of module.
 * @param int $imId : The ID of module data join to vote data.
 * @return bool
 */
function checkAlreadyVote($module, $imId) {
    global $user_ip;

    $nbVote = nkDB_totalNumRows(
        'FROM '. VOTE_TABLE .'
        WHERE vid = '. $imId .'
        AND module = '. nkDB_escape($module) .'
        AND ip = '. nkDB_escape($user_ip)
    );

    return ($nbVote > 0);
}

/**
 * Set nkTemplate parameters to create Vote pop-up.
 *
 * @param void
 * @return void
 */
function votePopUpInit() {
    global $user;

    $author = ($user) ? $user['name'] : __('VISITOR');

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(__('VOTE_FROM') .'&nbsp;'. $author);
}

/**
 * Load nkAction librairy & Vote nkAction parameters.
 *
 * @param void
 * @return void
 */
function loadVoteAction() {
    global $module, $imId;

    require_once 'Includes/nkAction.php';

    $module = (isset($_GET['module'])) ? stripslashes($_GET['module']) : '';
    $imId   = (isset($_GET['im_id'])) ? (int) $_GET['im_id'] : 0;

    nkAction_setParams(array(
        'dataName'  => 'vote',
        'tableName' => VOTE_TABLE,
        'uriData'   => array('im_id' => $imId, 'module' => $module),
        'onlyAdd'   => true,
        'editOp'    => 'post'
    ));
}

/* Vote save function */

/**
 * Callback function for nkAction_save.
 * Additional process before save Vote.
 *
 * @param null $void : Unused argument.
 * @param array $vote : The valid Vote data.
 * @return void
 */
function preSaveVoteData($void, &$vote) {
    global $user_ip, $imId, $module;

    $vote['module'] = $module;
    $vote['vid']    = $imId;
    $vote['ip']     = $user_ip;
}


// Action handle
switch ($GLOBALS['op']) {
    case 'post' :
        // Display Vote form
        votePopUpInit();
        loadVoteAction();
        nkAction_edit();
        break;

    case 'save' :
        // Save Vote result
        votePopUpInit();
        loadVoteAction();
        nkAction_save();
        break;

    default :
        break;
}

?>