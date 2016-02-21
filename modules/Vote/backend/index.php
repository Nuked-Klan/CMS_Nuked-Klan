<?php
/**
 * index.php
 *
 * Backend of Vote module - Vote modules management
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

if (! adminInit('Vote'))
    return;

require_once 'Includes/nkAction.php';

nkAction_setParams(array(
    'dataName'              => 'voteModules',
    'tableName'             => VOTE_MODULES_TABLE,
    'onlyEditDbTable'       => true,
    'title'                 => __('ADMIN_VOTE') // TODO : A finir
));

// remplacer ACTION_MODIF_VOTE_MODULES par ACTION_EDIT_VOTE_MODULES


/**
 * Get Vote modules list.
 *
 * @param void
 * @return array
 */
function getVoteModulesList() {
    static $voteModulesList;

    if (! $voteModulesList) {
        $voteModulesList = nkDB_selectMany(
            'SELECT module, active
            FROM '. VOTE_MODULES_TABLE
        );
    }

    return $voteModulesList;
}

/**
 * Callback function for nkAction_edit.
 * Prepare form configuration to edit Vote modules list.
 *
 * @param array $form : The Vote modules list form configuration.
 * @param array $forumCategory : The Vote modules data.
 * @return array : The Vote modules list form configuration prepared.
 */
function prepareFormForEditVoteModules(&$form, $voteModulesList) {
    foreach ($voteModulesList as $voteModule) {
        if ($voteModule['active'] == 1)
            $value = 'on';
        else
            $value = 'off';

        $moduleNameConst = strtoupper($voteModule['module']) .'_MODNAME';

        if (translationExist($moduleNameConst))
            $moduleName = __($moduleNameConst);
        else
            $moduleName = $voteModule['module'];

        $form['items'][$voteModule['module']] = array(
            'label'             => $moduleName,
            'type'              => 'checkbox',
            'inputValue'        => 'on',
            'defaultValue'      => 'off',
            'value'             => $value
        );
    }
}

/**
 * Callback function for nkAction_save.
 * Save Vote modules list configuration.
 *
 * @param void
 * @return void
 */
function updateVoteModulesData() {
    foreach (getVoteModulesList() as $voteModule) {
        $value = 0;

        if (isset($_POST[$voteModule['module']]) && $_POST[$voteModule['module']] == 'on')
            $value = 1;

        if ($value != $voteModule['active']) {
            nkDB_update(VOTE_MODULES_TABLE, array(
                    'active' => $value
                ),
                'module = \''. $voteModule['module'] .'\''
            );
        }
    }
}


// Action handle
switch ($GLOBALS['op']) {
    case 'save' :
        // Save vote modules settings
        nkAction_save();
        break;

    default :
        // Display editing vote modules form
        nkAction_edit();
        break;
}

?>