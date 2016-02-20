<?php
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * Initialisation of nkAction global vars
 */
$GLOBALS['nkForm'] = array(
    'allowedCheckformType' => array(
        'text',
        'alpha',
        'alphanumeric',
        'numeric',
        'email',
        'date',
        'password',
        'passwordConfirm',
        'oldPassword',
        'username'
    ),
    'allowedInputType' => array(
        'button',
        'checkbox',
        'color',
        'date',
        'file',
        'password',
        'radio',
        'select',
        'submit',
        'text',
        'textarea',
        'time'
    )
);


/**
 * Initialize the form
 *
 * @param array $form : The array of form to initialize
 * @return void
 */
function nkForm_init(&$form) {
    global $nkTemplate;

    if (! isset($form['dataName']) || $form['dataName'] == '')
        trigger_error('You must defined a data name for this form configuration !', E_USER_ERROR);

    if (! isset($form['formStyle']) || ! in_array($form['formStyle'], array('inline', 'table')))
        $form['formStyle'] = 'inline';

    $form['id'] = nkForm_formatHtmlSelector($form['dataName'] .'Form');

    nkForm_setFieldsPrefix($form);

    if (in_array('file', array_column($form['items'], 'type')))
        $form['enctype'] = ' enctype="multipart/form-data"';
    else
        $form['enctype'] = '';

    nkTemplate_addCSSFile('media/css/nkForm.css');

    if (! array_key_exists('hiddenField', $form))
        $form['hiddenField'] = array();

    if (array_key_exists('captcha', $form) && $form['captcha'] && initCaptcha())
        $form['captchaField'] = create_captcha();
    else
        $form['captchaField'] = '';

    if (! isset($form['token'])
        || ! is_array($form['token'])
        || ! isset($form['token']['name'])
        || $form['token']['name'] == ''
    ) {
        $form['token'] = array('name' => $form['dataName'] .'Form');
    }

    include_once 'Includes/nkToken.php';

    $form['hiddenField']['token'] = array(
        'name'  => 'token',
        'value' => nkToken_generate($form['token']['name'])
    );

    if (! array_key_exists('labelFormat', $form))
        $form['labelFormat'] = '%s :&nbsp;';
}

/**
 * Return field prefix used for create field class of nkForm.
 *
 * @param
 * @return string
 */
function nkForm_setFieldsPrefix(&$form) {
    $form['fieldsPrefix'] = substr($form['dataName'], 0, 1);

    if (preg_match_all('#([A-Z]+)#', $form['dataName'], $matches))
        $form['fieldsPrefix'] .= strtolower(implode($matches[1]));
}

function nkForm_formatHtmlSelector($selectorName) {
    global $nkTemplate;

    if ($nkTemplate['interface'] == 'frontend')
        return 'nk'. ucfirst($selectorName);
    else
        return $selectorName;
}

function nkForm_getCheckFormField($itemData) {
    $js = $itemData['id'] .': { type: "'. $itemData['dataType'] .'"';

    // TODO : Rewrite nkCheckform.js
    $js .= ', optional: '. (($itemData['required']) ? 'false' : 'true');

    foreach (array('noempty', 'oldUsername', 'passwordCheck') as $setting)
        if (array_key_exists($setting, $itemData))
            $js .= ', '. $setting .': '. (($itemData[$setting]) ? 'true' : 'false');

    foreach (array('passwordConfirmId', 'oldPasswordId') as $setting)
        if (array_key_exists($setting, $itemData) && $itemData[$setting] != '')
            $js .= ', '. $setting .': "'. $itemData[$setting] .'"';

    if (array_key_exists('minlength', $itemData))
        $js .= ', minlength: '. $itemData['minlength'];

    return $js .' }';
}

function nkForm_initCheckForm($formId, $fieldsData) {
    global $nkForm, $language;

    nkTemplate_addCSSFile('media/nkCheckForm/nkCheckForm.css');

    nkTemplate_addJSFile('media/nkCheckForm/nkCheckForm.js', 'librairyPlugin');
    nkTemplate_addJS(
        '$("#'. $formId .'").nkCheckForm({ input: {' ."\n"
        . implode(",\n", $fieldsData) ."\n"
        . '}});' ."\n",
        'jqueryDomReady'
    );

    if ($language != 'english')
        nkTemplate_addJSFile('media/nkCheckForm/i18n/nkCheckForm-'. $language .'.js', 'librairyPlugin');
}

/**
 *  Format attribute of input
 * @param string $name : The name of attribute
 * @param array $params : The array of input parameter
 * @param string $comparaison_value : Value of comparaison for checked and selected attribute
 * @return string HTML code
 */
function nkForm_formatAttribute($params, $attributes, $selectedValue = '') {
    $str = '';

    foreach ($attributes as $attribute) {
        // Format disabled attribute
        if ($attribute == 'disabled') {
                if (array_key_exists('disabled', $params) && $params['disabled'])
                $str .= ' disabled="disabled"';
        }
        // Format checked attribute...
        elseif ($attribute == 'checked') {
            // ...for input type checkbox...
            if ($params['type'] == 'checkbox' && $params['value'] == $params['inputValue'])
                $str .= ' checked="checked"';

            // ...or or input type radio
            elseif ($params['type'] == 'radio' && $params['value'] == $selectedValue)
                $str .= ' checked="checked"';
        }
        // Format for attribute
        elseif ($attribute == 'for') {
            if (array_key_exists('id', $params) && $params['id'] != '')
                $str .= ' for="'. $params['id'] .'"';
        }
        // Format class attribute
        elseif (in_array($attribute, array('class', 'labelClass', 'fakeLabelClass', 'inputClass', 'fieldsetClass', 'legendClass'))) {
            if (array_key_exists($attribute, $params) && is_array($params[$attribute]) && ! empty($params[$attribute]))
                $str .= ' class="'. implode(' ', $params[$attribute]) .'"';
        }
        // Format name attribute
        else if ($attribute == 'name' && array_key_exists('type', $params) && $params['type'] == 'select') {
            if (array_key_exists('multiple', $params) && $params['multiple'] == 'multiple')
                $str .= ' '. $attribute .'="'. $params[$attribute] .'[]"';
            else
                $str .= ' '. $attribute .'="'. $params[$attribute] .'"';
        }
        // Otherwise format other attributes
        else if (array_key_exists($attribute, $params) && $params[$attribute] != '') {
            $str .= ' '. $attribute .'="'. $params[$attribute] .'"';
        }
    }

    return $str;
}


/**
 * Generate a form
 * @param array $form : The array of form to generate
 * @return string HTML code
 */
function nkForm_generate($form) {
    global $nkForm;

    nkForm_init($form);

    $html = "\n". '<form class="nkForm" id="'. $form['id'] .'" action="'. $form['action'] .'" method="'. strtoupper($form['method']) .'"'. $form['enctype'] .'>' ."\n";
    $r = 0;
    $jsFieldsData = array();

    if ($form['formStyle'] == 'table')
        $html .= '<div class="'. nkForm_formatHtmlSelector($form['dataName'] .'IniTable') .'">';

    foreach ($form['items'] as $itemName => $itemData) {
        if (is_array($itemData)) {
            nkForm_initInput($itemName, $itemData, $form);

            if (array_key_exists('dataType', $itemData) && in_array($itemData['dataType'], $nkForm['allowedCheckformType']))
                $jsFieldsData[] = nkForm_getCheckFormField($itemData);
        }

        if (strpos($itemName, 'fieldsetStart') === 0) {
            $html .= '<fieldset id="'. $itemData['id'] .'">' ."\n";

            if (array_key_exists('legend', $itemData) && $itemData['legend'] != '')
                $html .= '<legend>'. $itemData['legend'] .'</legend>' ."\n";
        }
        else if (strpos($itemName, 'fieldsetEnd') === 0) {
            $html .= '</fieldset>' ."\n";
        }
        else if (strpos($itemName, 'html') === 0) {
            $html .= $itemData;
        }
        else {
            if (array_key_exists('type', $itemData) && $itemData['type'] == 'hidden') {
                $form['hiddenField'][] = $itemData;

                continue;
            }

            $html .= '<div id="'. nkForm_formatHtmlSelector($form['dataName'] . $itemData['camelCaseName']) .'" class="nkFormRow">';

            $label = $input = '';

            if (array_key_exists('label', $itemData))
                $label = nkForm_formatLabel($form['labelFormat'], $itemData);
            else if (array_key_exists('fakeLabel', $itemData))
                $label = nkForm_formatFakeLabel($form['labelFormat'], $itemData);

            if ($label != '') {
                if ($form['formStyle'] == 'table')
                    $html .= '<div>'. $label .'</div>';
                else
                    $html .= $label;
            }

            if (array_key_exists('type', $itemData) && in_array($itemData['type'], $nkForm['allowedInputType'])) {
                $fieldFonction = 'nkForm_input'. ucfirst($itemData['type']);
                $input .= $fieldFonction($itemName, $itemData, $form['id']);
            }

            //if ($itemData['required'])
            //    $input .= '&nbsp;<b class="required">*</b>';

            if (array_key_exists('html', $itemData) && $itemData['html'] != '')
                $input .= $itemData['html'];

            if ($input != '') {
                if ($form['formStyle'] == 'table')
                    $html .= '<div>'. $input .'</div>';
                else
                    $html .= $input;
            }

            $html .= '</div>' ."\n";
        }

        $r++;
    }

    if ($form['formStyle'] == 'table')
        $html .= '</div>';

    $html .= '<div id="'. nkForm_formatHtmlSelector($form['dataName'] .'ActionLinks') .'" class="nkFormActionLinks">';

    foreach ($form['itemsFooter'] as $itemName => $itemData) {
        if (array_key_exists('type', $itemData) && in_array($itemData['type'], $nkForm['allowedInputType'])) {
            $currentClass = 'nkForm_input'. ucfirst($itemData['type']);

            if (! array_key_exists('name', $itemData))
                $itemData['name'] = $itemName;

            if (in_array($itemData['type'], $nkForm['allowedInputType']))
                $html .= $currentClass($itemName, $itemData, $form['id']);
        }

        if (array_key_exists('html', $itemData) && $itemData['html'] != '')
            $html .= $itemData['html'];
    }

    foreach ($form['hiddenField'] as $params)
        $html .= '<input type="hidden"'. nkForm_formatAttribute($params, array('id', 'name', 'value')) .' />';// 'class', 

    if ($form['captchaField'] != '')
        $html .= $form['captchaField'];

    if ($jsFieldsData)
        nkForm_initCheckForm($form['id'], $jsFieldsData);

    return $html .'</div>' ."\n" .'</form>' ."\n";
}

function nkForm_underscore2camelcase($str) {
    $tmp = explode('_', $str);
    $tmp = array_map('ucfirst', $tmp);
    return implode($tmp);
}




/**
 * Initialize the input
 * @param array $input : The array of input to initialize
 * @return array $input : The array of input modified
 */
function nkForm_initInput($fieldName, &$params, $form) {
    //if (! array_key_exists('inputClass', $params))
    //    $params['inputClass'] = array();

    //if (array_key_exists('itemClass', $params)) {
    //    $params['inputClass']     = array_unique(array_merge($params['inputClass'], $params['itemClass']));
    //    $params['labelClass']     = array_unique(array_merge($params['labelClass'], $params['itemClass']));
    //    $params['fakeLabelClass'] = array_unique(array_merge($params['fakeLabelClass'], $params['itemClass']));
    //}

    if (! array_key_exists('name', $params))
        $params['name'] = $fieldName;

    if (! array_key_exists('value', $params))
        $params['value'] = '';

    if (! array_key_exists('required', $params))
        $params['required'] = false;

    $params['camelCaseName'] = nkForm_underscore2camelcase($fieldName);

    if (! array_key_exists('id', $params))
        $params['id'] = $form['fieldsPrefix'] . $params['camelCaseName'];

    if (! array_key_exists('htmlspecialchars', $params))
        $params['htmlspecialchars'] = false;

    /*if (array_key_exists('init', $params) && $params['init']) {
        $initClassName = 'nkFormInit'. ucfirst($fieldName) .'Field';
        $initField = new $initClassName;
        $initField->apply($params, $formId);
    }*/
}


function nkForm_formatLabel($labelFormat, $params) {
    //if (! array_key_exists('labelClass', $params))
    //    $params['labelClass'] = array();

    return '<label for="'. $params['id'] .'">'. sprintf($labelFormat, $params['label']) .'</label>';
}


function nkForm_formatFakeLabel($labelFormat, $params) {
    //if (! array_key_exists('fakeLabelClass', $params))
    //    $params['fakeLabelClass'] = array();

    return '<span>'. sprintf($labelFormat, $params['fakeLabel']) .'</span>';
}


/**
 *  Generate a button
 */
function nkForm_inputButton($fieldName, $params, $formId) {
    $attributes = array('type', 'id', 'inputClass', 'name', 'value', 'disabled');

    return '<input'. nkForm_formatAttribute($params, $attributes) .' />';
}


/**
 *  Generate a checkable field
 */
function nkForm_inputCheckbox($fieldName, $params, $formId) {
    if (nkTemplate_getInterface() == 'frontend') {
        $attributes = array('type', 'id', 'inputClass', 'name', 'value', 'checked', 'disabled');

        $params['inputClass'][] = 'checkbox';

        return '<input '. nkForm_formatAttribute($params, $attributes) .' value="'. $params['inputValue'] .'" />';
    }
    else {
        $check = $classInline = $dataCheck = '';

        if ($params['inputValue'] == $params['value']) {
            $check = 'checked="checked"';
            $dataCheck = 'data-check="checked"';
        }

        if (array_key_exists('inline', $params) && $params['inline'] === true)
            $classInline = ' inline ';

        return applyTemplate('nkForm/checkbox', array(
            'classInline'   => $classInline,
            'id'            => $params['name'],
            'name'          => $params['name'],
            'check'         => $check,
            'dataCheck'     => $dataCheck
        ));
    }
}


// TODO REVOIR POUR CHAMPS MULTIPLE (CONFLIT D'ID)
/**
 *  Generate a field for hex color data with colorpicker selection
 */
function nkForm_inputColor($fieldName, $params, $formId) {
    $attributes = array('id', 'name', 'size', 'value', 'maxlength', 'disabled', 'inputClass');

    $params['size'] = $params['maxlength'] = 6;
    $params['inputClass'][] = 'color';

    // TODO : Move jscolor librairy to media directory
    nkTemplate_addJSFile('modules/Admin/jscolor/jscolor.js');

    return '<input type="text"'. nkForm_formatAttribute($params, $attributes) .' />';
}


/**
 *  Generate a field for date data
 */
function nkForm_InputDate($fieldName, $params, $formId) {
    $attributes = array('id', 'inputClass', 'name', 'value', 'size', 'maxlength');

    $params['maxlength']  = 10;
    $params['class'][]    = 'datepicker';


    nkTemplate_addJSFile(JQUERY_LIBRAIRY, 'librairy');
    nkTemplate_addJSFile(JQUERY_UI_LIBRAIRY, 'librairyPlugin');

    if ($params['locale'] != 'en_GB') {
        $datepickerLocale = substr($params['locale'], 0, 2);

        nkTemplate_addJSFile(
            'web/js/jquery-ui-1.11.4/datepicker-i18n/datepicker-'. $datepickerLocale .'.js', 'librairyPlugin'
        );
    }

    nkTemplate_addCSSFile(JQUERY_UI_CSS);

    $options = array();
    $options[] = 'dateFormat: \''. (($params['locale'] == 'fr_FR') ? 'dd/mm/yy' : 'mm/dd/yy') .'\'';

    if (array_key_exists('options', $params)) {
        if (array_key_exists('changeMonth', $params['options']) && $params['options']['changeMonth'])
            $options[] = 'changeMonth: true';

        if (array_key_exists('changeYear', $params['options']) && $params['options']['changeYear'])
            $options[] = 'changeYear: true';

        if (array_key_exists('yearRange', $params['options']) && $params['options']['yearRange'] != '')
            $options[] = 'yearRange: \''. $params['options']['yearRange'] .'\'';

        if (array_key_exists('buttonText', $params['options']) && $params['options']['buttonText'] != '')
            $options[] = 'buttonText: \''. $params['options']['buttonText'] .'\'';
    }

    nkTemplate_addJS(
'$(\'#'. $params['id'] .'\').datepicker({
    showOn: \'button\',
    showButtonPanel: true,
    buttonImage: \'web/js/jquery-ui-1.11.4/images/calendar.gif\',
    buttonImageOnly: true,'. implode(',', $options) .'
});',
        'jqueryDomReady'
    );

    return '<input type="text"'. nkForm_formatAttribute($params, $attributes) .' />';
}


/**
 *  Generate a file field
 */
function nkForm_InputFile($fieldName, $params, $formId) {
    $attributes = array('type', 'id', 'inputClass', 'name', 'disabled');

    $html = '<input'. nkForm_formatAttribute($params, $attributes) .' />';

    /*if (array_key_exists('showMaxFilesize', $params) && $params['showMaxFilesize']) {
        if (! array_key_exists('filesize', $params))
            $params['filesize'] = false;

        $html .= '&nbsp;'. _MAX .' : '. getUploadMaxFilesize($params['filesize']);
    }*/

    return $html;
}


/**
 * Get upload_max_filesize of php.ini
 *
 * @access private
 * @param int $filesize : The maximum filesize authorized for upload or 0 for used PHP setting
 * @return int : The maximum filesize value in bytes
 * /
function getUploadMaxFilesize($filesize = 0) {
    if ($filesize == 0) {
        if (defined('NK_UPLOAD_MAX_FILESIZE'))
            return NK_UPLOAD_MAX_FILESIZE;

        $value  = trim(ini_get('upload_max_filesize'));

        switch (strtolower($value[strlen($value) - 1])) {
            case 'g' :
                $value *= 1073741824;
                break;

            case 'm' :
                $value *= 1048576;
                break;

            case 'k' :
                $value *=  1024;
                break;
        }

        $value = formatBytesString($value);

        define('NK_UPLOAD_MAX_FILESIZE', $value);

        return NK_UPLOAD_MAX_FILESIZE;

    }
    else {
        return formatBytesString($filesize);
    }
}


/ **
 * Format a bytes value and return it with his unit
 *
 * @access private
 * @param int $value : The value in bytes to convert
 * @return string : The converted value with his units
 * /
function formatBytesString($value) {
    $unit = ' '. '_B';

    if ($value >= 1024) {
        $value /= 1024;
        $unit   = ' '. '_KB';
    }

    if ($value >= 1024) {
        $value /= 1024;
        $unit   = ' '. '_MB';
    }

    if ($value >= 1024) {
        $value /= 1024;
        $unit   = ' '. '_GB';
    }

    return round($value, 2) . $unit;
}


/**
 *  Generate a password field
 */
function nkForm_inputPassword($fieldName, $params, $formId) {
    $attributes = array('id', 'name');

    return '<input type="password"'. nkForm_formatAttribute($params, $attributes) .' value="" />';
}


/**
 *  Generate a multiple radio tag
 */
function nkFormInputRadio($fieldName, $params, $formId) {
    $name = nkForm_formatAttribute($params, array('name'));
    //$html = '<div class="items-align">';
    $html = '';

    foreach ($params['data'] as $value => $label) {
        //$r++;
        //$pClass = ($r % 2 == 0) ? ' class="alt-row"' : '';

        // Format the checked attribute
        $checked = nkForm_formatAttribute($params, array('checked'), $value);

        // Format id attribute
        $radioId = $params['name'] .'_'. $value;

        $html .= '<input class="vertical-align" type="radio"'. $name .' id="'. $radioId .'" value="'. $value .'"'. $checked .' />'
        . '<label class="vertical-align text-right" for="'. $radioId .'">'. $label .'</label>';//<br />';
    }

    //$html .= '</div>';

    return $html;
}


/**
 *  Generate a field selection
 */
function nkForm_inputSelect($fieldName, $params, $formId) {
    $attributes = array('id', 'name', 'disabled', 'multiple');

    if (array_key_exists('multiple', $params) && $params['multiple'])
        $params['multiple'] = 'multiple';

    // Generate select tag start
    $html = '<select'. nkForm_formatAttribute($params, $attributes) .'>';

    foreach ($params['options'] as $key => $value) {
        if (strpos($key, 'start-optgroup') === 0)
            $html .= '<optgroup label="'. $value .'">';

        else if (strpos($key, 'end-optgroup') === 0)
            $html .= '</optgroup>';

        else {
            if ($params['htmlspecialchars'])
                $value = nkHtmlSpecialChars($value);

            $html .= '<option value="'. $key .'"'. (($params['value'] == $key) ? ' selected="selected"' : '') .'>'. $value .'</option>';
        }
    }

    $html .= '</select>';

    return $html;
}


/**
 *  Generate a field for text data
 */
function nkForm_inputText($fieldName, $params, $formId) {
    if ($params['htmlspecialchars'])
        $params['value'] = nkHtmlSpecialChars($params['value']);

    return '<input type="text"'. nkForm_formatAttribute($params, array('id', 'name', 'size', 'value', 'maxlength', 'disabled')) .' />';
}


/**
 *  Generate a textarea field
 */
function nkForm_inputTextarea($fieldName, $params, $formId) {
    $attributes = array('id', 'inputClass', 'name', 'cols', 'rows');

    // Set cols and rows attribute of nk textarea
    if (! array_key_exists('cols', $params)) $params['cols'] = 70;
    if (! array_key_exists('rows', $params)) $params['rows'] = 15;

    if ($params['htmlspecialchars'])
        $params['value'] = nkHtmlSpecialChars($params['value']);

    // Generate a input textaera with markitup style
    return '<textarea'. nkForm_formatAttribute($params, $attributes) .'>'. $params['value'] .'</textarea>';
}


/**
 *  Generate a field for time data
 */
function nkForm_inputTime($fieldName, $params, $formId) {
    $attributes = array('id', 'inputClass', 'name', 'value', 'maxlength');

    $params['class'][]    = 'time';
    $params['maxlength']  = 5;

    return '<input type="text"'. nkForm_formatAttribute($params, $attributes) .' />';
}


/**
 *  Generate a submit button
 */
function nkForm_inputSubmit($fieldName, $params, $formId) {
    return '<input type="submit" '. nkForm_formatAttribute($params, array('id', 'name', 'inputClass', 'value', 'disabled')) .' />';
}

?>