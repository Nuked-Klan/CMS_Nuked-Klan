<?php
/**
 * admin.php
 *
 * Backend of Vote module
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Vote'))
    return;


/* Vote modules management */

// Display editing vote modules form
function editVoteModules() {
    require_once 'Includes/nkForm.php';
    require_once 'modules/Vote/config/backend/voteModules.php';

    $dbrVoteModules = nkDB_selectMany(
        'SELECT module, active
        FROM '. VOTE_MODULES_TABLE
    );

    foreach ($dbrVoteModules as $voteModule) {
        if ($voteModule['active'] == 1)
            $value = 'on';
        else
            $value = 'off';

        $moduleNameConst = strtoupper($voteModule['module']) .'_MODNAME';

        if (translationExist($moduleNameConst))
            $moduleName = __($moduleNameConst);
        else
            $moduleName = $voteModule['module'];

        $voteModulesForm['items'][$voteModule['module']] = array(
            'label'             => $moduleName,
            'type'              => 'checkbox',
            'name'              => $voteModule['module'],
            'inputValue'        => 'on',
            'value'             => $value
        );
    }

    echo applyTemplate('contentBox', array(
        'title'     => __('ADMIN_VOTE'),
        'helpFile'  => 'Vote',
        'content'   => nkForm_generate($voteModulesForm)
    ));
}

// Save vote modules settings
function saveVoteModules() {
    global $nuked, $user;

    $dbrVoteModules = nkDB_selectMany(
        'SELECT module, active
        FROM '. VOTE_MODULES_TABLE
    );

    foreach ($dbrVoteModules as $voteModule) {
        if (isset($_POST[$voteModule['module']]) && $_POST[$voteModule['module']] != $voteModule['active']) {
            nkDB_update(VOTE_MODULES_TABLE, array(
                    'active' => $_POST[$voteModule['module']]
                ),
                'module = \''. $voteModule['module'] .'\''
            );
        }
    }

    saveUserAction(__('ACTION_MODIF_VOTE_MODULES'));

    printNotification(__('VOTE_MODULES_MODIFIED'), 'success');
    redirect('index.php?file=Vote&page=admin&op=module_vote', 2);
}

// Action handle
switch ($GLOBALS['op']) {
    case 'saveVoteModules' :
        saveVoteModules();
        break;

    default :
        editVoteModules();
        break;
}

?>