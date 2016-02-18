<?php


/**
 * Initialisation of nkAction global vars
 */
$GLOBALS['nkAction'] = array(
    'dataName'                  => null,
    'ucf_dataName'              => '',
    'tableName'                 => null,
    'tableId'                   => 'id',
    'titleField_dbTable'        => null,
    'uriId'                     => 'id',
    'uriIdType'                 => 'INT',
    'uriData'                   => array(),
    'id'                        => null,
    'moduleUriKey'              => 'file',
    'cfgFile'                   => null,
    'getFieldsFunction'         => null,
    'getFormFunction'           => null,
    'getListFunction'           => null,
    'title'                     => null,
    'previewUrl'                => null,
    'deleteConfirmation'        => null,
    'onlyEditDbTable'           => false,
    'onlyEdit'                  => false,
    'onlyAdd'                   => false,
    'editOp'                    => 'edit'
);


/**
 * Initialize nkAction configuration and check it.
 *
 * @param string $actionType : The type of action. (edit, save, delete or list)
 * @return bool : The result of initialisation.
 */
function nkAction_init($actionType) {
    global $nkAction, $nkTemplate, $file, $page;

    $nkAction['actionType'] = $actionType;

    if (! ($nkTemplate['interface'] == 'backend' && $page == 'setting'))
        $nkAction['id'] = nkAction_getID();

    if ($nkTemplate['interface'] == 'frontend'
        && function_exists($checkAccessFunct = 'check'. $nkAction['ucf_dataName'] .'Access')
    ) {
        if (! $checkAccessFunct())
            return false;
    }

    $editing = in_array($actionType, array('edit', 'save'));

    if ((! $editing || ($editing && $page != 'setting')) && ! nkAction_checkRequiredParams($actionType))
        return false;

    if ($nkTemplate['interface'] == 'backend') {
        $cfgDir                   = 'backend/';
        $nkAction['moduleUriKey'] = 'admin';
    }
    else {
        $cfgDir = '';
    }

    if ($page == 'setting') {
        $nkAction['cfgFile']           = 'modules/'. $file .'/'. $cfgDir .'config/setting.php';
        $nkAction['getFieldsFunction'] = 'get'. $file .'SettingFields';
        $nkAction['getFormFunction']   = 'get'. $file .'SettingFormCfg';
    }
    else {
        $nkAction['cfgFile']           = 'modules/'. $file .'/'. $cfgDir .'config/'. $nkAction['dataName'] .'.php';
        $nkAction['getFieldsFunction'] = 'get'. $nkAction['ucf_dataName'] .'Fields';
        $nkAction['getFormFunction']   = 'get'. $nkAction['ucf_dataName'] .'FormCfg';
        $nkAction['getListFunction']   = 'get'. $nkAction['ucf_dataName'] .'ListCfg';
    }

    if ($actionType != 'delete' && ! nkAction_checkConfigurationFile($actionType))
        return false;

    return true;
}

/**
 * Check required parameters of nkAction configuration.
 *
 * @param string $actionType : The type of action. (edit, save, delete or list)
 * @return bool : The result of checking.
 */
function nkAction_checkRequiredParams($actionType) {
    global $nkAction;

    if ($nkAction['dataName'] === null) {
        printNotification(sprintf(__('MISSING_NKACTION_PARAMETERS'), 'dataName'), 'error');
        return false;
    }

    $nkAction['ucf_dataName'] = ucfirst($nkAction['dataName']);

    if (in_array($actionType, array('edit', 'save', 'delete'))) {
        if (function_exists('get'. $nkAction['ucf_dataName'] .'Data'))
            $nkAction['getData'] = 'get'. $nkAction['ucf_dataName'] .'Data';

        if ($nkAction['tableName'] === null && ! isset($nkAction['getData'])) {
            printNotification(sprintf(__('MISSING_NKACTION_PARAMETERS'), 'tableName'), 'error');
            return false;
        }
    }

    return true;
}

/**
 * Set parameters of nkAction configuration.
 *
 * @param void
 * @return void
 */
function nkAction_setParams($params) {
    global $nkAction;

    $nkAction = array_merge($nkAction, $params);
}

/**
 * Check and load configuration file.
 *
 * @param string $actionType : The type of action. (edit, save, delete or list)
 * @return bool : The result of checking.
 */
function nkAction_checkConfigurationFile($actionType) {
    global $nkAction;

    if (! is_file($nkAction['cfgFile'])) {
        printNotification(sprintf(__('MISSING_CFG_FILE'), $cfgFile), 'error');
        return false;
    }

    require_once $nkAction['cfgFile'];

    if ($actionType == 'list') {
        if (! function_exists($nkAction['getListFunction'])) {
            printNotification(sprintf(__('MISSING_FUNCTION'), $nkAction['getListFunction']), 'error');
            return false;
        }
    }
    else {
        if (! isset($nkAction['getData']) && ! function_exists($nkAction['getFieldsFunction'])) {
            printNotification(sprintf(__('MISSING_FUNCTION'), $nkAction['getFieldsFunction']), 'error');
            return false;
        }

        if (! function_exists($nkAction['getFormFunction'])) {
            printNotification(sprintf(__('MISSING_FUNCTION'), $nkAction['getFormFunction']), 'error');
            return false;
        }
    }

    return true;
}

/**
 * Return uri ID value.
 *
 * @param void
 * @return mixed : The uri ID value if exist, null also.
 */
function nkAction_getID() {
    global $nkAction;

    if (isset($_GET[$nkAction['uriId']])) {
        if ($nkAction['uriIdType'] == 'INT')
            return (int) $_GET[$nkAction['uriId']];
        else
            return $_GET[$nkAction['uriId']];
    }

    return null;
}

/**
 * Check and save user action.
 *
 * @param string $actionName : The translation name (without ACTION_ prefix) of executed user action.
 * @param array $data : The submited form data.
 * @return void
 */
function nkAction_saveUserAction($actionName, $data) {
    global $nkAction, $nkTemplate;

    $tsAction = 'ACTION_'. $actionName;

    if (translationExist($tsAction)) {
        if ($nkAction['titleField_dbTable'] !== null
            && is_array($data)
            && array_key_exists($nkAction['titleField_dbTable'], $data)
        )
            saveUserAction(__($tsAction) .': '. $data[$nkAction['titleField_dbTable']]);
        else
            saveUserAction(__($tsAction));
    }
}

/**
 * Return translation of current data action or default data action.
 *
 * @param string $tsKeyDataName : The translation name of current data.
 * @param string $format : The translation key format.
 * @return string : The translation of data action.
 */
function nkAction_getActionTranslation($tsKeyDataName, $format) {
    $tsActionKey = sprintf($format, $tsKeyDataName);

    if (translationExist($tsActionKey))
        return __($tsActionKey);
    else
        return __(sprintf($format, 'DATA'));
}

/**
 * Return success notification message of current action.
 *
 * @param string $actionType : The type of action. (edit, save, delete or list)
 * @param string $tsKeyDataName : The translation name of current data.
 * @return string : The success notification message.
 */
function nkAction_getSuccessMsg($actionType, $tsKeyDataName) {
    global $nkAction, $page;

    if (in_array($page, array('category', 'rank')))
        $tsKeyDataName = strtoupper($page);

    if ($actionType == 'delete') {
        return nkAction_getActionTranslation($tsKeyDataName, '%s_DELETED');
    }
    else {
        if ($nkAction['saveAction'] == 'insert')
            return nkAction_getActionTranslation($tsKeyDataName, '%s_ADDED');
        else
            return nkAction_getActionTranslation($tsKeyDataName, '%s_MODIFIED');
    }
}

/**
 * Return translation key part of current data.
 *
 * @param void
 * @return string : The translation key part.
 */
function nkAction_getDataNameTranslationKey() {
    global $nkAction;

    return strtoupper(implode('_', preg_split('/(?=[A-Z])/', $nkAction['dataName'])));
}

function nkAction_editBackendSetting(&$form) {
    global $nkAction, $file, $nuked;

    $fields = $nkAction['getFieldsFunction']();

    foreach ($fields as $field)
        $form['items'][$field]['value'] = $nuked[$field];

    if (function_exists($prepareFormForEditFunct = 'prepareFormForEdit'. ucfirst($file) .'Setting'))
        $prepareFormForEditFunct($form, $data);

    $form['itemsFooter']['backlink'] = array(
        'html' => '<a class="buttonLink" href="index.php?'. $nkAction['moduleUriKey'] .'='. $file .'">'. __('BACK') .'</a>'
    );
}

function nkAction_editBackendDbTableList(&$form) {
    global $nkAction;

    //$fields = $nkAction['getFieldsFunction']();

    // TODO nkAction_init !
    $data = nkDB_selectMany(
        'SELECT *
        FROM '. $nkAction['tableName'] .'
        WHERE '. $nkAction['tableId']
    );

    if (! $data) {
        $tsKeyDataName = nkAction_getDataNameTranslationKey();
        printNotification(nkAction_getActionTranslation($tsKeyDataName, '%s_NO_EXIST'), 'error');
        return;
    }

    if (function_exists($prepareFormForEditFunct = 'prepareFormForEdit'. $nkAction['ucf_dataName']))
        $prepareFormForEditFunct($form, $data);

    $rawForm = $form;

    foreach ($rawForm['items'] as &$item) {
        if (is_array($item) && array_key_exists('value', $item))
            unset($item['value']);
    }

    unset($item);

    $_SESSION[$nkAction['dataName'] .'RawForm'] = $rawForm;
}

function nkAction_commonEdit(&$form) {
    global $nkAction, $page;

    if (! $nkAction['onlyEdit'] && $nkAction['id'] === null) {
        if (function_exists($addFormFunct = 'prepareFormForAdd'. $nkAction['ucf_dataName']))
            $addFormFunct($form);
    }
    else if (! $nkAction['onlyAdd']) {
        $form['action'] .= '&amp;id='. $nkAction['id'];

        $fields = $nkAction['getFieldsFunction']();

        if (isset($nkAction['getData'])) {
            $data = $getDataFunct($nkAction['id']);
        }
        else {
            $data = nkDB_selectOne(
                'SELECT *
                FROM '. $nkAction['tableName'] .'
                WHERE '. $nkAction['tableId'] .' = '. nkDB_escape($nkAction['id'])
            );

            if (! $data) {
                $tsKeyDataName = nkAction_getDataNameTranslationKey();

                if (in_array($page, array('category', 'rank')))
                    $tsKeyDataName = strtoupper($page);

                printNotification(nkAction_getActionTranslation($tsKeyDataName, '%s_NO_EXIST'), 'error');
                return;
            }
        }

        foreach ($fields as $field)
            $form['items'][$field]['value'] = $data[$field];

        if (function_exists($editFormFunct = 'prepareFormForEdit'. $nkAction['ucf_dataName']))
            $editFormFunct($form, $data, $nkAction['id']);
    }
}

/**
 * Display a form for addition / editing item of module.
 *
 * @param void
 * @return void
 */
function nkAction_edit() {
    global $nkAction, $nkTemplate, $file, $page;

    if (! nkAction_init('edit'))
        return;

    require_once 'Includes/nkForm.php';

    $form = $nkAction['getFormFunction']();

    if ($page == 'setting')
        $form['dataName'] = lcfirst($file) .'Setting';
    else
        $form['dataName'] = $nkAction['dataName'];

    $form['method'] = 'post';
    $form['action'] = nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'save', $nkAction['uriData'], true);

    if ($nkTemplate['interface'] == 'backend')
        $form['labelFormat'] = '<b>%s :</b>&nbsp;';

    if ($nkTemplate['interface'] == 'backend' && $page == 'setting')
        nkAction_editBackendSetting($form);
    else if ($nkTemplate['interface'] == 'backend' && $nkAction['onlyEditDbTable'])
        nkAction_editBackendDbTableList($form);
    else
        nkAction_commonEdit($form);

    if (isset($form['itemsFooter']) && isset($form['itemsFooter']['submit'])) {
        if (isset($form['itemsFooter']['submit']['value'])
            && is_array($form['itemsFooter']['submit']['value'])
        ) {
            if ($nkAction['id'] !== null && isset($form['itemsFooter']['submit']['value'][1]))
                $form['itemsFooter']['submit']['value'] = __($form['itemsFooter']['submit']['value'][1]);
            else if (isset($form['itemsFooter']['submit']['value'][0]))
                $form['itemsFooter']['submit']['value'] = __($form['itemsFooter']['submit']['value'][0]);
        }
        else
            $form['itemsFooter']['submit']['value'] = __('SEND');
    }

    if ($nkTemplate['interface'] == 'backend') {
        $form['itemsFooter']['backlink'] = array(
            'html' => '<a class="buttonLink" href="index.php?'. $nkAction['moduleUriKey'] .'='. $file .'">'. __('BACK') .'</a>'
        );
    }

    $title = '';

    if ($page == 'setting') {
        if ($nkAction['title'] !== null)
            $title = $nkAction['title'];
        else
            $title = __('PREFERENCES');
    }
    else {
        if (function_exists($getTitleFunct = 'get'. $nkAction['ucf_dataName'] .'Title'))
            $title = $getTitleFunct($nkAction['id']);
        else
            $title = $nkAction['title'];
    }

    $content = '';

    if ($nkTemplate['interface'] == 'backend' && $page == 'setting')
        $content .= getMenuOfModuleAdmin();

    if (isset($form['infoNotification']) && $form['infoNotification'] != '')
        $content .= printNotification($form['infoNotification'], 'information', $optionsData = array(), $return = true);

    $generatedForm = nkForm_generate($form);

    if ($nkTemplate['interface'] == 'backend') {
        echo applyTemplate('contentBox', array(
            'title'     => $title,
            'helpFile'  => $file,
            'content'   => $content . $generatedForm
        ));
    }
    else {
        if (function_exists($generateFormViewFunct = 'generate'. $nkAction['ucf_dataName'] .'EditView'))
            echo $generateFormViewFunct($generatedForm);
        else
            echo $generatedForm;
    }
}

function nkAction_saveBackendSetting($form, $fields) {
    global $nkAction, $nuked, $file, $page;

    if (is_array($form['token']) && ! isset($form['token']['refererData'])) {
        $form['token']['refererData'] = array(
            nkUrl_format($nkAction['moduleUriKey'], $file, $page, $nkAction['editOp'])
        );
    }

    if (! nkCheckForm($form, $fields)) {
        redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page, $nkAction['editOp']), 2);
        return;
    }

    if (function_exists($postCheckformValidationFunct = 'postCheckform'. $nkAction['ucf_dataName'] .'Validation')) {
        if (! $postCheckformValidationFunct()) {
            redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page, $nkAction['editOp']), 2);
            return;
        }
    }

    foreach ($fields as $field) {
        if (isset($nuked[$field], $_POST[$field]) && $nuked[$field] != $_POST[$field])
            nkDB_update(CONFIG_TABLE, array('value' => $_POST[$field]), 'name = '. nkDB_escape($field));
    }

    $moduleNameConst = strtoupper($file) .'_MODNAME';

    if (translationExist($moduleNameConst))
        $moduleName = __($moduleNameConst);
    else
        $moduleName = $file;

    saveUserAction(sprintf(__('ACTION_MODULE_SETTING_UPDATED') .'.', $moduleName));

    printNotification(__('PREFERENCES_UPDATED'), 'success');
    redirect(nkUrl_format($nkAction['moduleUriKey'], $file, 'setting'), 2);
}

function nkAction_saveBackendDbTableList($form, $fields) {
    global $nkAction, $file, $page;

    if (isset($_SESSION[$nkAction['dataName'] .'RawForm'])) {
        $form = array_merge($form, $_SESSION[$nkAction['dataName'] .'RawForm']);
        unset($_SESSION[$nkAction['dataName'] .'RawForm']);
    }

    if (is_array($form['token']) && ! isset($form['token']['refererData'])) {
        $form['token']['refererData'] = array(
            nkUrl_format($nkAction['moduleUriKey'], $file, $page)
        );
    }

    if (! nkCheckForm($form, $fields)) {
        redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page), 2);
        return;
    }

    if (function_exists($postCheckformValidationFunct = 'postCheckform'. $nkAction['ucf_dataName'] .'Validation')) {
        if (! $postCheckformValidationFunct()) {
            redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page), 2);
            return;
        }
    }

    if (function_exists($updateDataFunct = 'update'. $nkAction['ucf_dataName'] .'Data'))
        $updateDataFunct();

    $tsKeyDataName = nkAction_getDataNameTranslationKey();

    saveUserAction(__('ACTION_MODIF_'. $tsKeyDataName));

    printNotification(__($tsKeyDataName .'_MODIFIED'), 'success');
    redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page), 2);
}

function nkAction_commonSave($form, $fields) {
    global $nkAction, $nkTemplate, $file, $page;

    $uriData = $nkAction['uriData'];
    $uriData[$nkAction['uriId']] = $nkAction['id'];

    if (is_array($form['token']) && ! isset($form['token']['refererData'])) {
        $form['token']['refererData'] = array(
            nkUrl_format($nkAction['moduleUriKey'], $file, $page, $nkAction['editOp'], $uriData)
        );
    }

    if (function_exists($preCheckformProcessFunct = 'preCheckform'. $nkAction['ucf_dataName'] .'Process')) {
        $preCheckformProcessFunct($nkAction['id'], $form);
    }

    $data = array();

    if (! nkCheckForm($form, $fields, $data)) {
        redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page, $nkAction['editOp'], $uriData), 2);
        return;
    }

    if (function_exists($postCheckformValidationFunct = 'postCheckform'. $nkAction['ucf_dataName'] .'Validation')) {
        if (! $postCheckformValidationFunct($nkAction['id'], $data)) {
            redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page, $nkAction['editOp'], $uriData), 2);
            return;
        }
    }

    $tsKeyDataName = nkAction_getDataNameTranslationKey();

    if (function_exists($preSaveDataFunct = 'preSave'. $nkAction['ucf_dataName'] .'Data'))
        $preSaveDataFunct($nkAction['id'], $data);

    if (! $nkAction['onlyEdit'] && $nkAction['id'] === null) {
        if (function_exists($insertDataFunct = 'insert'. $nkAction['ucf_dataName'] .'Data')) {
            $insertDataFunct($data);
        }
        else {
            nkDB_insert($nkAction['tableName'], $data);
            $nkAction['id'] = nkDB_insertId();
        }

        $nkAction['saveAction'] = 'insert';

        if ($nkTemplate['interface'] == 'backend')
            nkAction_saveUserAction('ADD_'. $tsKeyDataName, $data);
    }
    else if (! $nkAction['onlyAdd']) {
        if (function_exists($updateDataFunct = 'update'. $nkAction['ucf_dataName'] .'Data')) {
            $updateDataFunct($data);
        }
        else {
            nkDB_update($nkAction['tableName'], $data, $nkAction['tableId'] .' = '. nkDB_escape($nkAction['id']));
        }

        $nkAction['saveAction'] = 'update';

        if ($nkTemplate['interface'] == 'backend')
            nkAction_saveUserAction('EDIT_'. $tsKeyDataName, $data);
    }

    if (function_exists($postSaveDataFunct = 'postSave'. $nkAction['ucf_dataName'] .'Data'))
        $postSaveDataFunct($nkAction['id'], $data);

    if ($nkTemplate['pageDesign'] == 'nudePage')
        $notificationOptions = array('closeLink' => true, 'reloadOnClose' => true);
    else
        $notificationOptions = array();

    printNotification(nkAction_getSuccessMsg('save', $tsKeyDataName), 'success', $notificationOptions);

    if ($nkTemplate['pageDesign'] == 'fullPage') {
        if ($nkTemplate['interface'] == 'backend' && $nkAction['previewUrl'] !== null) {
            setPreview($nkAction['previewUrl'], nkUrl_format($nkAction['moduleUriKey'], $file, $page));
        }
        else {
            if (function_exists($getRedirectUrlFunct = 'get'. $nkAction['ucf_dataName'] .'RedirectUrl'))
                $redirectUrl = $getRedirectUrlFunct();
            else
                $redirectUrl = nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'index', $nkAction['uriData']);

            redirect($redirectUrl, 2);
        }
    }
}

/**
 * Check item submited form and insert / update item of module.
 *
 * @param void
 * @return void
 */
function nkAction_save() {
    global $nkAction, $nkTemplate, $file, $page, $nuked;

    if (! nkAction_init('save'))
        return;

    require_once 'Includes/nkCheckForm.php';

    $form   = $nkAction['getFormFunction']();
    $fields = $nkAction['getFieldsFunction']();

    if ($page == 'setting')
        $form['dataName'] = lcfirst($file) .'Setting';
    else
        $form['dataName'] = $nkAction['dataName'];

    if (! isset($form['token']))
        $form['token'] = array();

    if (! isset($form['token']['name']) || $form['token']['name'] == '')
        $form['token'] = array('name' => $form['dataName'] .'Form');

    $_POST = array_map_recursive('stripslashes', $_POST);

    if ($nkTemplate['interface'] == 'backend' && $page == 'setting')
        nkAction_saveBackendSetting($form, $fields);
    else if ($nkTemplate['interface'] == 'backend' && $nkAction['onlyEditDbTable'])
        nkAction_saveBackendDbTableList($form, $fields);
    else
        nkAction_commonSave($form, $fields);
}


function nkAction_deleteConfirmation() {
    global $nkAction, $file, $page;

    $uriData = $nkAction['uriData'];
    $uriData[$nkAction['uriId']] = $nkAction['id'];

    // Display confirmation form
    if (! isset($_POST['confirm'])) {
        echo applyTemplate('confirm', array(
            'url'       => nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'delete', $uriData, true),
            'message'   => $nkAction['deleteConfirmation'],
            'fields'    => array(
                'token'     => nkToken_generate('delete'. $nkAction['ucf_dataName'] . $nkAction['id'])
            ),
        ));
    }
    else if ($_POST['confirm'] == __('YES')) {
        // Check delete token
        $tokenValid = nkToken_valid('delete'. $nkAction['ucf_dataName'] . $nkAction['id'],
            300, array(nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'delete', $uriData))
        );

        if ($tokenValid) return true;

        printNotification(__('TOKEN_NO_VALID'), 'error');
        redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'delete', $uriData), 2);
    }
    else if ($_POST['confirm'] == __('NO')) {
        printNotification(__('OPERATION_CANCELED'), 'warning');

        if (function_exists($getRedirectUrlFunct = 'get'. $nkAction['ucf_dataName'] .'RedirectUrl')) {
            $redirectUrl = $getRedirectUrlFunct();
            redirect($redirectUrl, 2);
        }
    }

    return false;
}

/**
 * Delete item of module.
 *
 * @param void
 * @return void
 */
function nkAction_delete() {
    global $nkAction, $nkTemplate, $file, $page;

    if (! nkAction_init('delete'))
        return;

    if ($nkAction['id'] === null) {
        printNotification(sprintf(__('MISSING_ID_URI'), $nkAction['uriId']), 'error');
        return;
    }

    if ($nkAction['deleteConfirmation'] !== null && ! nkAction_deleteConfirmation())
        return;

    $tsKeyDataName = nkAction_getDataNameTranslationKey();

    if ($nkTemplate['interface'] == 'backend'
        && $nkAction['titleField_dbTable'] !== null
        && translationExist('ACTION_DELETE_'. $tsKeyDataName)
    ) {
        $dbrTable = nkDB_selectOne(
            'SELECT '. $nkAction['titleField_dbTable'] .'
            FROM '. $nkAction['tableName'] .'
            WHERE '. $nkAction['tableId'] .' = '. nkDB_escape($nkAction['id'])
        );
    }

    if (function_exists($preDeleteFunct = 'preDelete'. $nkAction['ucf_dataName'] .'Data'))
        $preDeleteFunct($nkAction['id']);

    if (function_exists($deleteDataFunct = 'delete'. $nkAction['ucf_dataName'] .'Data')) {
        $deleteDataFunct($nkAction['id']);
    }
    else {
        nkDB_delete($nkAction['tableName'], $nkAction['tableId'] .' = '. nkDB_escape($nkAction['id']));
    }

    if (function_exists($postDeleteFunct = 'postDelete'. $nkAction['ucf_dataName'] .'Data'))
        $postDeleteFunct($nkAction['id']);

    if ($nkTemplate['interface'] == 'backend')
        nkAction_saveUserAction('DELETE_'. $tsKeyDataName, $dbrTable);

    printNotification(nkAction_getSuccessMsg('delete', $tsKeyDataName), 'success');

    if ($nkTemplate['interface'] == 'backend' && $nkAction['previewUrl'] !== null) {
        setPreview($nkAction['previewUrl'], nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'index', $nkAction['uriData']));
    }
    else {
        if (function_exists($getRedirectUrlFunct = 'get'. $nkAction['ucf_dataName'] .'RedirectUrl'))
            $redirectUrl = $getRedirectUrlFunct();
        else
            $redirectUrl = nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'index', $nkAction['uriData']);

        redirect($redirectUrl, 2);
    }
}

/**
 * Return field prefix used for create field class of nkList.
 *
 * @param void
 * @return string
 */
function nkAction_getFieldsPrefix() {
    global $nkAction;

    $fieldsPrefix = substr($nkAction['dataName'], 0, 1);

    if (preg_match_all('#([A-Z]+)#', $nkAction['dataName'], $matches))
        $fieldsPrefix .= strtolower(implode($matches[1]));

    return $fieldsPrefix;
}

/**
 * Display items list of module.
 *
 * @param void
 * @return void
 */
function nkAction_list() {
    global $nkAction, $file, $page;

    if (! nkAction_init('list'))
        return;

    require_once 'Includes/nkList.php';

    $listCfg = $nkAction['getListFunction']();

    if (in_array($page, array('category', 'rank')))
        $tsKeyDataName = strtoupper($page);
    else
        $tsKeyDataName = nkAction_getDataNameTranslationKey();

    $listCfg['limit'] = 30;

    if (! isset($listCfg['css']))
        $listCfg['css'] = array();

    if (! isset($listCfg['css']['tablePrefix']))
        $listCfg['css']['tablePrefix'] = $nkAction['dataName'];

    if (! isset($listCfg['css']['fieldsPrefix']))
        $listCfg['css']['fieldsPrefix'] = nkAction_getFieldsPrefix();

    if (isset($listCfg['edit']) && is_array($listCfg['edit'])) {
        if (! isset($listCfg['edit']['op']))
            $listCfg['edit']['op'] = $nkAction['editOp'];

        if (! isset($listCfg['edit']['imgTitle']))
            $listCfg['edit']['imgTitle'] = nkAction_getActionTranslation($tsKeyDataName, 'EDIT_THIS_%s');
    }

    if (isset($listCfg['delete']) && is_array($listCfg['delete'])) {
        if (! isset($listCfg['delete']['op']))
            $listCfg['delete']['op'] = 'delete';

        if (! isset($listCfg['delete']['confirmTxt']))
            $listCfg['delete']['confirmTxt'] = nkAction_getActionTranslation($tsKeyDataName, 'CONFIRM_TO_DELETE_%s');

        // TODO : A revoir...
        if (! isset($listCfg['delete']['confirmField']))
            $listCfg['delete']['confirmField'] = $nkAction['titleField_dbTable'];

        if (! isset($listCfg['delete']['imgTitle']))
            $listCfg['delete']['imgTitle'] = nkAction_getActionTranslation($tsKeyDataName, 'DELETE_THIS_%s');
    }

    if (! isset($listCfg['noDataText']))
        $listCfg['noDataText'] = nkAction_getActionTranslation($tsKeyDataName, 'NO_%s_IN_DB');

    if (function_exists($callbackRowFunct = 'format'. $nkAction['ucf_dataName'] .'Row')) {
        $listCfg['callbackRowFunction'] = array(
            'functionName' => 'format'. $nkAction['ucf_dataName'] .'Row'
        );
    }

    $title = '';

    if (function_exists($getTitleFunct = 'get'. $nkAction['ucf_dataName'] .'Title'))
        $title = $getTitleFunct();

    if (! isset($listCfg['footerLinks'])) {
        $listCfg['footerLinks'] = array();

        if (! $nkAction['onlyEdit']) {
            $tsAddEntrie = nkAction_getActionTranslation($tsKeyDataName, 'ADD_%s');

            $listCfg['footerLinks'][$tsAddEntrie] = nkUrl_format($nkAction['moduleUriKey'], $file, $page, $nkAction['editOp'], array(), true);
        }

        if ($page == 'index')
            $listCfg['footerLinks'][__('BACK')] = 'index.php?file=Admin';
        else
            $listCfg['footerLinks'][__('BACK')] = 'index.php?'. $nkAction['moduleUriKey'] .'='. $file .'&amp;page=admin';
    }

    $footerLink = applyTemplate('footerLink', array(
        'links' => $listCfg['footerLinks']
    ));

    echo applyTemplate('contentBox', array(
        'title'     => $title,
        'helpFile'  => $file,
        'content'   => getMenuOfModuleAdmin() . nkList_generate($listCfg) . $footerLink
    ));
}

?>