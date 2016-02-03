<?php


/**
 * Initialisation of nkAction global vars
 */
$GLOBALS['nkAction'] = array(
    'dataName'              => '',
    'ucf_dataName'          => '',
    'tableName'             => '',
    'tableId'               => 'id',
    'titleField_dataTable'  => null,
    'uriId'                 => 'id',
    'uriIdType'             => 'INT',
    'moduleUriKey'          => 'file',
    'cfgFile'               => '',
    'getFieldsFunction'     => '',
    'getFormFunction'       => '',
    'getListFunction'       => ''
);


/**
 * Initialize nkAction configuration and check it.
 *
 * @param string $actionType : The type of action. (edit, save, delete or list)
 * @return bool : The result of initialisation.
 */
function nkAction_init($actionType) {
    global $page;

    $editing = in_array($actionType, array('edit', 'save'));

    if ((! $editing || ($editing && $page != 'setting')) && ! nkAction_checkConstant($actionType))
        return false;

    nkAction_setParams();

    if ($actionType != 'delete' && ! nkAction_checkConfigurationFile($actionType))
        return false;

    return true;
}

/**
 * Check constant and set them in nkAction configuration.
 *
 * @param string $actionType : The type of action. (edit, save, delete or list)
 * @return bool : The result of checking.
 */
function nkAction_checkConstant($actionType) {
    global $nkAction;

    if (! defined('CURRENT_DATA_NAME')) {
        printNotification(sprintf(__('MISSING_CONSTANT'), 'CURRENT_DATA_NAME'), 'error');
        return false;
    }

    $nkAction['dataName']     = CURRENT_DATA_NAME;
    $nkAction['ucf_dataName'] = ucfirst(CURRENT_DATA_NAME);

    if (in_array($actionType, array('edit', 'save', 'delete'))) {
        if (! defined('CURRENT_TABLE_NAME')) {
            printNotification(sprintf(__('MISSING_CONSTANT'), 'CURRENT_TABLE_NAME'), 'error');
            return false;
        }

        $nkAction['tableName'] = CURRENT_TABLE_NAME;
    }

    if (defined('CURRENT_TABLE_ID'))
        $nkAction['tableId'] = CURRENT_TABLE_ID;

    if (defined('CURRENT_URI_ID'))
        $nkAction['uriId'] = CURRENT_URI_ID;

    if (defined('CURRENT_URI_ID_TYPE'))
        $nkAction['uriIdType'] = CURRENT_URI_ID_TYPE;

    if (defined('CURRENT_TITLE_FIELD_DATA_TABLE'))
        $nkAction['titleField_dataTable'] = CURRENT_TITLE_FIELD_DATA_TABLE;

    return true;
}

/**
 * Set parameters of nkAction configuration.
 *
 * @param void
 * @return void
 */
function nkAction_setParams() {
    global $nkAction, $nkTemplate, $file, $page;

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
        if (! function_exists($nkAction['getFieldsFunction'])) {
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

    if ($nkTemplate['interface'] == 'backend'
        && $nkAction['titleField_dataTable'] !== null
        && translationExist($tsAction)
        && is_array($data)
        && array_key_exists($nkAction['titleField_dataTable'], $data)
    )
        saveUserAction(__($tsAction) .': '. $data[$nkAction['titleField_dataTable']]);
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
 * @param mixed $id : The uri ID value if exist, null also.
 * @return string : The success notification message.
 */
function nkAction_getSuccessMsg($actionType, $tsKeyDataName, $id = null) {
    global $page;

    if (in_array($page, array('category', 'rank')))
        $tsKeyDataName = strtoupper($page);

    if ($actionType == 'delete') {
        return nkAction_getActionTranslation($tsKeyDataName, '%s_DELETED');
    }
    else {
        if ($id === null)
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

/**
 * Display a form for addition / editing item of module.
 *
 * @param void
 * @return void
 */
function nkAction_edit() {
    global $nkAction, $nkTemplate, $file, $page, $nuked;

    if (! nkAction_init('edit'))
        return;

    require_once 'Includes/nkForm.php';

    $form = $nkAction['getFormFunction']();

    if ($page == 'setting')
        $form['dataName'] = lcfirst($file) .'Setting';
    else
        $form['dataName'] = $nkAction['dataName'];

    $form['method'] = 'post';
    $form['action'] = nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'save', array(), true);

    $form['labelFormat'] = '<b>%s :</b>&nbsp;';

    if ($nkTemplate['interface'] == 'backend' && $page == 'setting') {
        $fields = $nkAction['getFieldsFunction']();

        foreach ($fields as $field)
            $form['items'][$field]['value'] = $nuked[$field];

        $form['itemsFooter']['backlink'] = array(
            'html' => '<a class="buttonLink" href="index.php?'. $nkAction['moduleUriKey'] .'='. $file .'">'. __('BACK') .'</a>'
        );
    }
    else {
        $id = nkAction_getID();

        if ($id === null) {
            if (function_exists($callbackAddFormFunct = 'prepareFormForAdd'. $nkAction['ucf_dataName']))
                $callbackAddFormFunct($form);
        }
        else {
            $form['action'] .= '&amp;id='. $id;

            $fields = $nkAction['getFieldsFunction']();

            $dbrTable = nkDB_selectOne(
                'SELECT *
                FROM '. $nkAction['tableName'] .'
                WHERE '. $nkAction['tableId'] .' = '. nkDB_escape($id)
            );

            foreach ($fields as $field)
                $form['items'][$field]['value'] = $dbrTable[$field];

            if (function_exists($callbackEditFormFunct = 'prepareFormForEdit'. $nkAction['ucf_dataName']))
                $callbackEditFormFunct($form, $dbrTable, $id);
        }

        if (isset($form['itemsFooter']) && isset($form['itemsFooter']['submit'])) {
            if ($id > 0 && isset($form['itemsFooter']['submit']['value'][1]))
                $form['itemsFooter']['submit']['value'] = __($form['itemsFooter']['submit']['value'][1]);
            else if (isset($form['itemsFooter']['submit']['value'][0]))
                $form['itemsFooter']['submit']['value'] = __($form['itemsFooter']['submit']['value'][0]);
            else
                $form['itemsFooter']['submit']['value'] = __('SEND');
        }

        $form['itemsFooter']['backlink'] = array(
            'html' => '<a class="buttonLink" href="index.php?'. $nkAction['moduleUriKey'] .'='. $file .'">'. __('BACK') .'</a>'
        );
    }

    $title = '';

    if ($page == 'setting') {
        if (defined('CURRENT_SETTING_TITLE'))
            $title = CURRENT_SETTING_TITLE;
        else
            $title = __('PREFERENCES');
    }
    else {
        if (function_exists($getTitleFunct = 'get'. $nkAction['ucf_dataName'] .'Title'))
            $title = $getTitleFunct($id);
    }

    $content = '';

    if ($nkTemplate['interface'] == 'backend' && $page == 'setting')
        $content .= getMenuOfModuleAdmin();

    if (isset($form['infoNotification']) && $form['infoNotification'] != '')
        $content .= printNotification($form['infoNotification'], 'information', $optionsData = array(), $return = true);

    if ($nkTemplate['interface'] == 'backend') {
        echo applyTemplate('contentBox', array(
            'title'     => $title,
            'helpFile'  => $file,
            'content'   => $content . nkForm_generate($form)
        ));
    }
    else {
        echo nkForm_generate($form);
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
        $form['id'] = lcfirst($file) .'SettingForm';
    else
        $form['id'] = $nkAction['dataName'] .'Form';

    if (! isset($form['token']))
        $form['token'] = array();

    if (! isset($form['token']['name']) || $form['token']['name'] == '')
        $form['token'] = array('name' => $form['id']);

    $_POST = array_map('stripslashes', $_POST);

    if ($nkTemplate['interface'] == 'backend' && $page == 'setting') {
        if (is_array($form['token']) && ! isset($form['token']['refererData'])) {
            $form['token']['refererData'] = array(
                nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'edit')
            );
        }

        if (! nkCheckForm($form, $fields)) {
            redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'edit'), 2);
            return;
        }

        foreach ($fields as $field) {
            if (isset($nuked[$field], $_POST[$field]) && $nuked[$field] != $_POST[$field])
                nkDB_update(CONFIG_TABLE, array('value' => $_POST[$field]), 'name = '. nkDB_escape($field));
        }

        saveUserAction(sprintf(__('ACTION_MODULE_SETTING_UPDATED') .'.', $file));

        printNotification(__('PREFERENCES_UPDATED'), 'success');
        redirect(nkUrl_format($nkAction['moduleUriKey'], $file, 'setting'), 2);
    }
    else {
        $id = nkAction_getID();

        if (is_array($form['token']) && ! isset($form['token']['refererData'])) {
            $form['token']['refererData'] = array(
                nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'edit', array($nkAction['uriId'] => $id))
            );
        }

        if (! nkCheckForm($form, $fields)) {
            redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'edit', array($nkAction['uriId'] => $id)), 2);
            return;
        }

        if (function_exists($callbackPostSaveFunct = 'postSave'. $nkAction['ucf_dataName'] .'Data'))
            $callbackPostSaveFunct($id, $data);

        foreach ($fields as $field) {
            if (array_key_exists('name', $form['items'][$field]))
                $data[$field] = $_POST[$form['items'][$field]['name']];
            else
                $data[$field] = $_POST[$field];
        }

        $tsKeyDataName = nkAction_getDataNameTranslationKey();

        if ($id === null) {
            if (function_exists($callbackPostAddFunct = 'postAdd'. $nkAction['ucf_dataName'] .'Data'))
                $callbackPostAddFunct($data);

            nkDB_insert($nkAction['tableName'], $data);
            nkAction_saveUserAction('ADD_'. $tsKeyDataName, $data);
        }
        else {
            if (function_exists($callbackPostUpdateFunct = 'postUpdate'. $nkAction['ucf_dataName'] .'Data'))
                $callbackPostUpdateFunct($id, $data);

            nkDB_update($nkAction['tableName'], $data, $nkAction['tableId'] .' = '. nkDB_escape($id));
            nkAction_saveUserAction('EDIT_'. $tsKeyDataName, $data);
        }

        printNotification(nkAction_getSuccessMsg('save', $tsKeyDataName, $id), 'success');

        if ($nkTemplate['interface'] == 'backend' && defined('PREVIEW_DATA_URL'))
            setPreview(PREVIEW_DATA_URL, nkUrl_format($nkAction['moduleUriKey'], $file, $page));
        else
            redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page), 2);
    }
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

    $id = nkAction_getID();

    if ($id === null) {
        printNotification(sprintf(__('MISSING_ID_URI'), $nkAction['uriId']), 'error');
        return;
    }

    $tsKeyDataName = nkAction_getDataNameTranslationKey();

    if ($nkTemplate['interface'] == 'backend'
        && $nkAction['titleField_dataTable'] !== null
        && translationExist('ACTION_DELETE_'. $tsKeyDataName)
    ) {
        $dbrTable = nkDB_selectOne(
            'SELECT '. $nkAction['titleField_dataTable'] .'
            FROM '. $nkAction['tableName'] .'
            WHERE '. $nkAction['tableId'] .' = '. nkDB_escape($id)
        );
    }

    if (function_exists($callbackPostDeleteFunct = 'postDelete'. $nkAction['ucf_dataName'] .'Data'))
        $callbackPostDeleteFunct($id);

    nkDB_delete($nkAction['tableName'], $nkAction['tableId'] .' = '. nkDB_escape($id));
    nkAction_saveUserAction('DELETE_'. $tsKeyDataName, $dbrTable);

    printNotification(nkAction_getSuccessMsg('delete', $tsKeyDataName), 'success');

    if ($nkTemplate['interface'] == 'backend' && defined('PREVIEW_DATA_URL'))
        setPreview(PREVIEW_DATA_URL, nkUrl_format($nkAction['moduleUriKey'], $file, $page));
    else
        redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page), 2);
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

    if (! isset($listCfg['css']))
        $listCfg['css'] = array();

    if (! isset($listCfg['css']['tablePrefix']))
        $listCfg['css']['tablePrefix'] = $nkAction['dataName'];

    if (! isset($listCfg['css']['fieldsPrefix']))
        $listCfg['css']['fieldsPrefix'] = nkAction_getFieldsPrefix();

    if (isset($listCfg['edit']) && is_array($listCfg['edit'])) {
        if (! isset($listCfg['edit']['op']))
            $listCfg['edit']['op'] = 'edit';

        if (! isset($listCfg['edit']['imgTitle']))
            $listCfg['edit']['imgTitle'] = nkAction_getActionTranslation($tsKeyDataName, 'EDIT_THIS_%s');
    }

    if (isset($listCfg['delete']) && is_array($listCfg['delete'])) {
        if (! isset($listCfg['delete']['op']))
            $listCfg['delete']['op'] = 'delete';

        if (! isset($listCfg['delete']['confirmTxt']))
            $listCfg['delete']['confirmTxt'] = __('CONFIRM_TO_DELETE');

        if (! isset($listCfg['delete']['confirmField']))
            $listCfg['delete']['confirmField'] = $nkAction['titleField_dataTable'];

        if (! isset($listCfg['delete']['imgTitle']))
            $listCfg['delete']['imgTitle'] = nkAction_getActionTranslation($tsKeyDataName, 'DELETE_THIS_%s');
    }

    if (! isset($listCfg['emptytable']))
        $listCfg['emptytable'] = nkAction_getActionTranslation($tsKeyDataName, 'NO_%s_IN_DB');

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

        $tsAddEntrie = nkAction_getActionTranslation($tsKeyDataName, 'ADD_%s');

        $listCfg['footerLinks'][$tsAddEntrie] = nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'edit', array(), true);

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