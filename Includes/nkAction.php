<?php


/**
 * Initialisation of nkAction global vars
 */
$GLOBALS['nkAction'] = array(
    'itemName'          => '',
    'ucf_itemName'      => '',
    'tableName'         => '',
    'tableId'           => 'id',
    'uriId'             => 'id',
    'uriIdType'         => 'INT',
    'moduleUriKey'      => 'file',
    'cfgFile'           => '',
    'getFieldsFunction' => '',
    'getFormFunction'   => '',
);


function nkAction_checkConstant() {
    global $nkAction;

    if (! defined('CURRENT_ITEM_NAME')) {
        printNotification(__('MISSING_CURRENT_ITEM_NAME'), 'error');
        return false;
    }

    $nkAction['itemName']     = CURRENT_ITEM_NAME;
    $nkAction['ucf_itemName'] = ucfirst(CURRENT_ITEM_NAME);

    if (! defined('CURRENT_TABLE_NAME')) {
        printNotification(__('MISSING_CURRENT_TABLE_NAME'), 'error');
        return false;
    }

    $nkAction['tableName'] = CURRENT_TABLE_NAME;

    if (defined('CURRENT_TABLE_ID'))
        $nkAction['tableId'] = CURRENT_TABLE_ID;

    if (defined('CURRENT_URI_ID'))
        $nkAction['uriId'] = CURRENT_URI_ID;

    if (defined('CURRENT_URI_ID_TYPE'))
        $nkAction['uriIdType'] = CURRENT_URI_ID_TYPE;

    //CURRENT_FIELD_NAME_TABLE

    return true;
}

function nkAction_setParams($interface) {
    global $nkAction, $file, $page;

    if ($interface == 'backend') {
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
        $nkAction['cfgFile']           = 'modules/'. $file .'/'. $cfgDir .'config/'. $nkAction['itemName'] .'.php';
        $nkAction['getFieldsFunction'] = 'get'. $nkAction['ucf_itemName'] .'Fields';
        $nkAction['getFormFunction']   = 'get'. $nkAction['ucf_itemName'] .'FormCfg';
    }
}

function nkAction_checkConfiguration() {
    global $nkAction;

    if (! is_file($nkAction['cfgFile'])) {
        printNotification(sprintf(__('MISSING_FORM_CFG_FILE'), $nkAction['cfgFile']), 'error');
        return false;
    }

    require_once $nkAction['cfgFile'];

    if (! function_exists($nkAction['getFieldsFunction'])) {
        printNotification(sprintf(__('MISSING_GET_FIELDS_FUNCTION'), $nkAction['getFieldsFunction']), 'error');
        return false;
    }

    if (! function_exists($nkAction['getFormFunction'])) {
        printNotification(sprintf(__('MISSING_GET_FORM_FUNCTION'), $nkAction['getFormFunction']), 'error');
        return false;
    }

    return true;
}

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

// Display admin form for addition / editing.
// Display admin setting form of module.
function nkAction_edit() {
    global $nkAction, $file, $page, $nuked;

    if ($page != 'setting' && ! nkAction_checkConstant())
        return;

    $interface = nkTemplate_getInterface();

    nkAction_setParams($interface);

    if (! nkAction_checkConfiguration())
        return;

    require_once 'Includes/nkForm.php';

    $form = $nkAction['getFormFunction']();

    if ($page == 'setting')
        $form['id'] = lcfirst($file) .'SettingForm';
    else
        $form['id'] = $nkAction['itemName'] .'Form';

    $form['method'] = 'post';
    $form['action'] = nkUrl_format($nkAction['moduleUriKey'], $file, $page, 'save', array(), true);

    if ($interface == 'backend' && $page == 'setting') {
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
            if (function_exists($callbackAddFormFunct = 'prepareFormForAdd'. $nkAction['ucf_itemName']))
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

            if (function_exists($callbackEditFormFunct = 'prepareFormForEdit'. $nkAction['ucf_itemName']))
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
        if (function_exists($getTitleFunct = 'get'. $nkAction['ucf_itemName'] .'Title'))
            $title = $getTitleFunct($id);
    }

    $content = '';

    if ($interface == 'backend' && $page == 'setting')
        $content .= getMenuOfModuleAdmin();

    if (isset($form['infoNotification']) && $form['infoNotification'] != '')
        $content .= printNotification($form['infoNotification'], 'information', $optionsData = array(), $return = true);

    if ($interface == 'backend') {
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

// Save / modify submited admin form.
// Save module setting.
function nkAction_save() {
    global $nkAction, $file, $page, $nuked;

    if ($page != 'setting' && ! nkAction_checkConstant())
        return;

    $interface = nkTemplate_getInterface();

    nkAction_setParams($interface);

    if (! nkAction_checkConfiguration())
        return;

    require_once 'Includes/nkCheckForm.php';

    $form   = $nkAction['getFormFunction']();
    $fields = $nkAction['getFieldsFunction']();

    if ($page == 'setting')
        $form['id'] = lcfirst($file) .'SettingForm';
    else
        $form['id'] = $nkAction['itemName'] .'Form';

    if (! isset($form['token']))
        $form['token'] = array();

    if (! isset($form['token']['name']) || $form['token']['name'] == '')
        $form['token'] = array('name' => $form['id']);

    $_POST = array_map('stripslashes', $_POST);

    if ($interface == 'backend' && $page == 'setting') {
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

        saveUserAction(sprintf(_ACTIONPREFFO .'.', $file));

        printNotification(_PREFUPDATED, 'success');
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

        if (function_exists($callbackPostSaveFunct = 'postSave'. $nkAction['ucf_itemName'] .'Data'))
            $callbackPostSaveFunct($id, $data);

        foreach ($fields as $field) {
            if (array_key_exists('name', $form['items'][$field]))
                $data[$field] = $_POST[$form['items'][$field]['name']];
            else
                $data[$field] = $_POST[$field];
        }

        $tsItemName = strtoupper(implode('_', preg_split('/(?=[A-Z])/', $nkAction['itemName'])));

        if ($id === null) {
            if (function_exists($callbackPostAddFunct = 'postAdd'. $nkAction['ucf_itemName'] .'Data'))
                $callbackPostAddFunct($data);

            nkDB_insert($nkAction['tableName'], $data);

            if ($interface == 'backend' && translationExist($tsAction = 'ACTION_ADD_'. $tsItemName))
                saveUserAction(__($tsAction) .': '. $data[CURRENT_FIELD_NAME_TABLE]);
        }
        else {
            if (function_exists($callbackPostUpdateFunct = 'postUpdate'. $nkAction['ucf_itemName'] .'Data'))
                $callbackPostUpdateFunct($id, $data);

            nkDB_update($nkAction['tableName'], $data, $nkAction['tableId'] .' = '. nkDB_escape($id));

            if ($interface == 'backend' && translationExist($tsAction = 'ACTION_EDIT_'. $tsItemName))
                saveUserAction(__($tsAction) .': '. $data[CURRENT_FIELD_NAME_TABLE]);
        }

        printNotification(nkAction_getSaveSuccessMsg($id, $tsItemName), 'success');

        if (defined('PREVIEW_ITEM_URL'))
            setPreview(PREVIEW_ITEM_URL, nkUrl_format($nkAction['moduleUriKey'], $file, $page));
        else
            redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page), 2);
    }
}

function nkAction_getSaveSuccessMsg($id, $tsItemName) {
    global $page;

    if (in_array($page, array('category', 'rank')))
        $tsItemName = strtoupper($page);

    if ($id === null) {
        if (translationExist($tsItemName .'_ADDED'))
            return __($tsItemName .'_ADDED');
        //else
        //    return __('_ADDED');
    }
    else {
        if (translationExist($tsItemName .'_MODIFIED'))
            return __($tsItemName .'_MODIFIED');
        //else
        //    return __('_MODIFIED');
    }
}

function nkAction_delete() {
    global $nkAction, $file, $page;

    if (! nkAction_checkConstant())
        return;

    $interface = nkTemplate_getInterface();

    nkAction_setParams($interface);

    $id = nkAction_getID();

    // TODO : Check id is not null

    $tsItemName = strtoupper(implode('_', preg_split('/(?=[A-Z])/', $nkAction['itemName'])));

    if (defined('CURRENT_FIELD_NAME_TABLE') && translationExist($tsAction = 'ACTION_DELETE_'. $tsItemName)) {
        $dbrTable = nkDB_selectOne(
            'SELECT '. CURRENT_FIELD_NAME_TABLE .'
            FROM '. $nkAction['tableName'] .'
            WHERE '. $nkAction['tableId'] .' = '. nkDB_escape($id)
        );
    }

    if (function_exists($callbackPostDeleteFunct = 'postDelete'. $nkAction['ucf_itemName'] .'Data'))
        $callbackPostDeleteFunct($id);

    nkDB_delete($nkAction['tableName'], $nkAction['tableId'] .' = '. nkDB_escape($id));

    if (defined('CURRENT_FIELD_NAME_TABLE') && translationExist($tsAction = 'ACTION_DELETE_'. $tsItemName))
        saveUserAction(__($tsAction) .': '. $dbrTable[CURRENT_FIELD_NAME_TABLE]);

    if (in_array($page, array('category', 'rank')))
        printNotification(__(strtoupper($page) .'_DELETED'), 'success');
    else if (translationExist($tsItemName .'_DELETED'))
        printNotification(__($tsItemName .'_DELETED'), 'success');
    //else
    //    printNotification(__('_DELETED'), 'success');

    if (defined('PREVIEW_ITEM_URL'))
        setPreview(PREVIEW_ITEM_URL, nkUrl_format($nkAction['moduleUriKey'], $file, $page));
    else
        redirect(nkUrl_format($nkAction['moduleUriKey'], $file, $page), 2);
}

function nkAction_getFieldsPrefix() {
    $fieldsPrefix = substr(CURRENT_ITEM_NAME, 0, 1);

    if (preg_match_all('#([A-Z]+)#', CURRENT_ITEM_NAME, $matches))
        $fieldsPrefix .= strtolower(implode($matches[1]));

    return $fieldsPrefix;
}

// Display admin list.
function nkAction_list() {
    global $file, $page;

    if (! defined('CURRENT_ITEM_NAME')) {
        printNotification(__('MISSING_CURRENT_ITEM_NAME'), 'error');
        return;
    }

    require_once 'Includes/nkList.php';

    if (! is_file($cfgFile = 'modules/'. $file .'/backend/config/'. CURRENT_ITEM_NAME .'.php')) {
        printNotification(sprintf(__('MISSING_LIST_CFG_FILE'), $cfgFile), 'error');
        return;
    }

    require_once $cfgFile;

    if (! function_exists($getListFunct = 'get'. ucfirst(CURRENT_ITEM_NAME) .'ListCfg')) {
        printNotification(sprintf(__('MISSING_GET_LIST_FUNCTION'), $getListFunct), 'error');
        return;
    }

    $listCfg = $getListFunct();

    if (in_array($page, array('category', 'rank')))
        $tsItemName = strtoupper($page);
    else
        $tsItemName = strtoupper(implode('_', preg_split('/(?=[A-Z])/', CURRENT_ITEM_NAME)));

    if (! isset($listCfg['css']))
        $listCfg['css'] = array();

    if (! isset($listCfg['css']['tablePrefix']))
        $listCfg['css']['tablePrefix'] = CURRENT_ITEM_NAME;

    if (! isset($listCfg['css']['fieldsPrefix']))
        $listCfg['css']['fieldsPrefix'] = nkAction_getFieldsPrefix();

    if (isset($listCfg['edit']) && is_array($listCfg['edit'])) {
        if (! isset($listCfg['edit']['op']))
            $listCfg['edit']['op'] = 'edit';

        if (! isset($listCfg['edit']['imgTitle'])) {
            if (translationExist('EDIT_THIS_'. $tsItemName))
                $listCfg['edit']['imgTitle'] = __('EDIT_THIS_'. $tsItemName);
            else
                $listCfg['edit']['imgTitle'] = __('EDIT_THIS_ENTRIE');
        }
    }

    if (isset($listCfg['delete']) && is_array($listCfg['delete'])) {
        if (! isset($listCfg['delete']['op']))
            $listCfg['delete']['op'] = 'delete';

        if (! isset($listCfg['delete']['confirmTxt']))
            $listCfg['delete']['confirmTxt'] = _DELETE_CONFIRM .' %s ! '. _CONFIRM;

        if (! isset($listCfg['delete']['confirmField']))
            $listCfg['delete']['confirmField'] = CURRENT_FIELD_NAME_TABLE;

        if (! isset($listCfg['delete']['imgTitle'])) {
            if (translationExist('DELETE_THIS_'. $tsItemName))
                $listCfg['delete']['imgTitle'] = __('DELETE_THIS_'. $tsItemName);
            else
                $listCfg['delete']['imgTitle'] = __('DELETE_THIS_ENTRIE');
        }
    }

    if (! isset($listCfg['emptytable'])) {
        if (translationExist('DELETE_THIS_'. $tsItemName))
            $listCfg['emptytable'] = __('NO_'. $tsItemName .'_IN_DB');
        else
            $listCfg['emptytable'] = __('NO_ENTRIE_IN_DB');
    }

    if (function_exists($callbackRowFunct = 'format'. ucfirst(CURRENT_ITEM_NAME) .'Row')) {
        $listCfg['callbackRowFunction'] = array(
            'functionName' => 'format'. ucfirst(CURRENT_ITEM_NAME) .'Row'
        );
    }

    $title = '';

    if (function_exists($getTitleFunct = 'get'. ucfirst(CURRENT_ITEM_NAME) .'Title'))
        $title = $getTitleFunct();

    if (! isset($listCfg['footerLinks'])) {
        $listCfg['footerLinks'] = array();

        if (translationExist('ADD_'. $tsItemName))
            $tsAddEntrie = __('ADD_'. $tsItemName);
        else
            $tsAddEntrie = __('ADD_ENTRIE');

        $listCfg['footerLinks'][$tsAddEntrie] = nkUrl_format('admin', $file, $page, 'edit', array(), true);

        if ($page == 'index')
            $listCfg['footerLinks'][__('BACK')] = 'index.php?file=Admin';
        else
            $listCfg['footerLinks'][__('BACK')] = 'index.php?admin='. $file .'&amp;page=admin';
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