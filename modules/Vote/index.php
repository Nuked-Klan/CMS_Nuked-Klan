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
 * @param int $id : The vote ID.
 * @param string $module : The name of module.
 * @return bool
 */
function checkAlreadyVote($id, $module) {
    global $user_ip;

    $nbVote = nkDB_totalNumRows(
        'FROM '. VOTE_TABLE .'
        WHERE vid = '. $id .'
        AND module = '. nkDB_escape($module) .'
        AND ip = '. nkDB_escape($user_ip)
    );

    return ($nbVote > 0);
}

function vote_index($module, $vid) {
    global $visiteur;

    $module = stripslashes($module);

    if (! checkVoteStatus($module, $vid)) {
        echo '<b>'. _VOTE_UNACTIVE . '</b>';
        return;
    }

    $level_access = nivo_mod('Vote');

    echo '<b>' . _NOTE . ' :</b>&nbsp;';

    $sql = mysql_query("SELECT id, ip, vote FROM " . VOTE_TABLE . " WHERE vid = '" . $vid . "' AND module = '" . mysql_real_escape_string($module) . "'");
    $count = mysql_num_rows($sql);

    $total = 0;
    $n = 0;
    if ($count > 0) {
        while (list($id, $ip, $vote) = mysql_fetch_array($sql)) {
            $total = $total + $vote / $count;
            $pourcent_arrondi = ceil($total);
        }
        $note = $pourcent_arrondi;

        for ($i = 2;$i <= $note;$i += 2) {
            echo '<img style="border: 0;" src="modules/Vote/images/z1.png" alt="" title="' . $note . '/10 (' . $count . '&nbsp;' . _VOTES . ')" />';
            $n++;
        }

        if (($note - $i) != -2) {
            echo '<img style="border: 0;" src="modules/Vote/images/z2.png" alt="" title="' . $note . '/10 (' . $count . '&nbsp;' . _VOTES . ')" />';
            $n++;
        }

        for ($z = $n;$z < 5;$z++) {
            echo '<img style="border: 0;" src="modules/Vote/images/z3.png" alt="" title="' . $note . '/10 (' . $count . '&nbsp;' . _VOTES . ')" />';
        }
    } else {
        echo _NOTEVAL;
    }

    if ($visiteur >= $level_access && $level_access > -1) {
        echo '&nbsp;<small>[ <a href="#" onclick="javascript:window.open(\'index.php?file=Vote&amp;op=postVote&amp;vid=' . $vid . '&amp;module=' . $module . '\',\'screen\',\'toolbar=0,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=350,height=150,top=30,left=0\');return(false)">' . _RATE . '</a> ]</small>'."\n";
    }
}

// Display Vote form
function postVote() {
    global $user, $visiteur;

    $id     = (isset($_GET['id'])) ? (int) $_GET['id'] : 0;
    $module = (isset($_GET['module'])) ? stripslashes($_GET['module']) : '';
    $author = ($user) ? $user['name'] : _VISITOR;

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_VOTEFROM .'&nbsp;'. $author);

    if ($visiteur >= nivo_mod('Vote')) {
        if (checkAlreadyVote($id, $module)) {
            printNotification(_ALREADYVOTE, 'error', array('closeLink' => true));
        }
        else {
            echo applyTemplate('modules/Vote/voteForm', array(
                'id'        => $id,
                'module'    => $module
            ));
        }
    }
    else {
        echo applyTemplate('nkAlert/noEntrance');
        //printNotification(_NOENTRANCE, 'error', array('closeLink' => true));
    }
}

// Save Vote result
function saveVote() {
    global $user, $visiteur;

    $id     = (isset($_POST['id'])) ? (int) $_POST['id'] : 0;
    $module = (isset($_POST['module'])) ? stripslashes($_POST['module']) : '';
    $vote   = (isset($_POST['vote'])) ? $_POST['vote'] : '';
    $author = ($user) ? $user['name'] : _VISITOR;

    if (! checkVoteStatus($module, $id)) return;

    nkTemplate_setPageDesign('nudePage');
    nkTemplate_setTitle(_VOTEFROM .'&nbsp;'. $author);

    if ($visiteur >= nivo_mod('Vote') && ctype_digit($vote) && $vote <= 10 && $vote >= 0) {
        if (checkAlreadyVote($id, $module)) {
            printNotification(_ALREADYVOTE, 'error', array('closeLink' => true));
        }
        else {
            nkDB_insert(VOTE_TABLE, array(
                'module'    => $module,
                'vid'       => $id,
                'ip'        => $user_ip,
                'vote'      => $vote
            ));

            printNotification(_VOTEADD, 'success', array('closeLink' => true, 'reloadOnClose' => true));
        }
    }
    else {
        echo applyTemplate('nkAlert/noEntrance');
        //printNotification(_NOENTRANCE, 'error', array('closeLink' => true));
    }
}

switch ($_REQUEST['op']) {
    case 'vote_index':
        vote_index($_REQUEST['module'], $_REQUEST['vid']);
        break;

    case 'postVote' :
        postVote();
        break;

    case 'saveVote' :
        saveVote();
        break;

    default :
        break;
}

?>