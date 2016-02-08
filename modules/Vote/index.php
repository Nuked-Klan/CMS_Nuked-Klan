<?php
/**
 * index.php
 *
 * Frontend of Vote module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

translate('modules/Vote/lang/'. $language .'.lang.php');


/**
 * Check if vote is disabled for his module and if module data exist.
 *
 * @param string $module : The name of module.
 * @param int $imId : The module data ID.
 * @return bool
 */
function checkVoteStatus($module, $imId) {
    if (! empty($module) && preg_match('/^[A-Za-z_]+$/', $module)) {
        $dbrVoteModules = nkDB_selectOne(
            'SELECT active
            FROM '. VOTE_MODULES_TABLE .'
            WHERE module = '. nkDB_escape(strtolower($module))
        );

        if ($dbrVoteModules && $dbrVoteModules['active'] == 1) {
            $tableConstName = strtoupper($module) .'_TABLE';

            if (defined($tableConstName .'_ID'))
                $tableIdName = constant($tableConstName .'_ID');
            else
                $tableIdName = 'id';

            $nbVoteModuleData = nkDB_totalNumRows(
                'FROM '. nkDB_escape(constant($tableConstName), true) .'
                WHERE '. nkDB_escape($tableIdName, true) .' = '. intval($imId)
            );

            return ($nbVoteModuleData > 0);
        }
    }

    return false;
}

/**
 * Check if user have already voted.
 *
 * @param string $module : The name of module.
 * @param int $id : The vote ID.
 * @return bool
 */
function checkAlreadyVote($module, $id) {
    global $user_ip;

    $nbVote = nkDB_totalNumRows(
        'FROM '. VOTE_TABLE .'
        WHERE vid = '. $id .'
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
 * Display vote result / status.
 * Included by module.
 *
 * @param string $module : The name of module.
 * @param int $id : The vote ID.
 * @return void
 */
function vote_index($module, $id) {
    global $visiteur;

    $id     = (int) $id;
    $module = stripslashes($module);

    if (! checkVoteStatus($module, $id)) {
        echo '<b>'. __('VOTE_UNACTIVE') . '</b>';
        return;
    }

    $dbrVote = nkDB_selectMany(
        'SELECT vote FROM '. VOTE_TABLE .'
        WHERE vid = '. $id .' AND module = '. nkDB_escape($module)
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
        'module'        => $module
    ));
}

// Display Vote form
function postVote() {
    global $visiteur;

    $id     = (isset($_GET['id'])) ? (int) $_GET['id'] : 0;
    $module = (isset($_GET['module'])) ? stripslashes($_GET['module']) : '';

    if (! checkVoteStatus($module, $id)) return;

    $levelAccess = nivo_mod('Vote');

    if ($visiteur >= $levelAccess && $levelAccess > -1) {
        if (checkAlreadyVote($module, $id)) {
            printNotification(__('ALREADY_VOTE'), 'error', array('closeLink' => true));
        }
        else {
            echo applyTemplate('modules/Vote/voteForm', array(
                'id'        => $id,
                'module'    => $module
            ));
        }
    }
    else {
        echo applyTemplate('nkAlert/noEntrance', array('closeLink' => true));
    }
}

// Save Vote result
function saveVote() {
    global $visiteur, $user_ip;

    $id     = (isset($_POST['id'])) ? (int) $_POST['id'] : 0;
    $module = (isset($_POST['module'])) ? stripslashes($_POST['module']) : '';
    $vote   = (isset($_POST['vote'])) ? $_POST['vote'] : '';

    if (! checkVoteStatus($module, $id)) return;

    $levelAccess = nivo_mod('Vote');

    if ($visiteur >= $levelAccess && $levelAccess > -1 && ctype_digit($vote) && $vote <= 10 && $vote >= 0) {
        if (checkAlreadyVote($module, $id)) {
            printNotification(__('ALREADY_VOTE'), 'error', array('closeLink' => true));
        }
        else {
            nkDB_insert(VOTE_TABLE, array(
                'module'    => $module,
                'vid'       => $id,
                'ip'        => $user_ip,
                'vote'      => $vote
            ));

            printNotification(__('VOTE_ADD'), 'success', array('closeLink' => true, 'reloadOnClose' => true));
        }
    }
    else {
        echo applyTemplate('nkAlert/noEntrance', array('closeLink' => true));
    }
}

// Action handle
switch ($GLOBALS['op']) {
    case 'post' :
        votePopUpInit();
        postVote();
        break;

    case 'save' :
        votePopUpInit();
        saveVote();
        break;

    default :
        break;
}

?>