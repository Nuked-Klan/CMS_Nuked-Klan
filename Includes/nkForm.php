<?php
/**
 * nkForm.php
 *
 * Librairy to generate HTML form.
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

/**
 * Initialisation of nkForm global vars
 */
$GLOBALS['nkForm'] = array(
    'allowedCheckformType' => array(
        'text',
        'alpha',
        'alphanumeric',
        'integer',
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
    ),
    'defaultForm' => array(
        'enctype'      => '',
        'captchaField' => '',
        'formStyle'    => 'inline',
        'hiddenField'  => array(),
        'labelFormat'  => '%s :&nbsp;'
    ),
    'defaultInput' => array(
        'value'            => '',
        'required'         => false,
        'htmlspecialchars' => false
    )
);


/**
 * Initialize the form.
 *
 * @param array $form : The array of form to initialize.
 * @return void
 */
function nkForm_init(&$form) {
    global $nkForm, $nkTemplate;

    if (! isset($form['dataName']) || $form['dataName'] == '')
        trigger_error('You must defined a data name for this form configuration !', E_USER_ERROR);

    $form = $form + $nkForm['defaultForm'];

    $nkForm['config'] = & $form;

    if (! in_array($form['formStyle'], array('inline', 'table')))
        $form['formStyle'] = 'inline';

    $form['id'] = nkForm_formatHtmlSelector($form['dataName'] .'Form');

    nkForm_setFieldsPrefix($form);

    nkTemplate_addCSSFile('media/css/nkForm.css');

    if ($nkTemplate['interface'] == 'frontend'
        && array_key_exists('captcha', $form)
        && $form['captcha'] && initCaptcha()
    )
        $form['captchaField'] = create_captcha();

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
}

/**
 * Return field prefix used for create field class of nkForm.
 *
 * @param array $form : The form configuration.
 * @return string
 */
function nkForm_setFieldsPrefix(&$form) {
    $form['fieldsPrefix'] = substr($form['dataName'], 0, 1);

    if (preg_match_all('#([A-Z]+)#', $form['dataName'], $matches))
        $form['fieldsPrefix'] .= strtolower(implode($matches[1]));
}

/**
 * Return a formated field Id or class for HTML element.
 *
 * @param string $selectorName : The base of Id or class.
 * @return string
 */
function nkForm_formatHtmlSelector($selectorName) {
    global $nkTemplate;

    if ($nkTemplate['interface'] == 'frontend')
        return 'nk'. ucfirst($selectorName);
    else
        return $selectorName;
}

/**
 * Return a formated Javascript parameters list of field for nkCheckform.
 *
 * @param array $itemData : The input field configuration.
 * @return string
 */
function nkForm_getCheckFormField($itemData) {
    $js = $itemData['id'] .': { type: "'. $itemData['dataType'] .'"';

    foreach (array('required', 'oldUsername', 'passwordCheck') as $setting)
        if (array_key_exists($setting, $itemData))
            $js .= ', '. $setting .': '. (($itemData[$setting]) ? 'true' : 'false');

    foreach (array('passwordConfirmId', 'oldPasswordId') as $setting)
        if (array_key_exists($setting, $itemData) && $itemData[$setting] != '')
            $js .= ', '. $setting .': "'. $itemData[$setting] .'"';

    foreach (array('minlength', 'maxlength') as $setting)
        if (array_key_exists($setting, $itemData))
            $js .= ', '. $setting .': '. $itemData[$setting];

    if (array_key_exists('range', $itemData)) {
        if (array_key_exists('min', $itemData['range']))
            $js .= ', minrange: '. $itemData['range']['min'];

        if (array_key_exists('max', $itemData['range']))
            $js .= ', maxrange: '. $itemData['range']['max'];
    }

    return $js .' }';
}

/**
 * Initialize nkCheckform librairy and set form checking.
 *
 * @param string $formId : The form css id.
 * @param array $fieldsData : The list of formated Javascript parameters list of fields.
 * @return void
 */
function nkForm_initCheckForm($formId, $fieldsData) {
    global $language;

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
 *  Format attribute of input.
 *
 * @param array $params : The array of input parameter.
 * @param array $attributes : The input parameter list.
 * @param string $checkBoxValue : Value of input checkbox for checked attribute.
 * @return string HTML code
 */
function nkForm_formatAttribute($params, $attributes, $checkBoxValue = '') {
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
            elseif ($params['type'] == 'radio' && $params['value'] == $checkBoxValue)
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
 * Generate a form.
 *
 * @param array $form : The array of form to generate.
 * @return string HTML code
 */
function nkForm_generate($form) {
    global $nkForm;

    nkForm_init($form);

    $html = '';
    $r = 0;
    $jsFieldsData = array();

    if ($form['formStyle'] == 'table')
        $html .= '<div class="'. nkForm_formatHtmlSelector($form['dataName'] .'IniTable') .'">';

    foreach ($form['items'] as $itemName => $itemData) {
        if (is_array($itemData)) {
            nkFormInput_init($itemName, $itemData, $form);

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
                $label = nkForm_formatLabel($itemData);
            else if (array_key_exists('fakeLabel', $itemData))
                $label = nkForm_formatFakeLabel($itemData);

            if ($label != '') {
                if ($form['formStyle'] == 'table')
                    $html .= '<div>'. $label .'</div>';
                else
                    $html .= $label;
            }

            if (array_key_exists('type', $itemData) && in_array($itemData['type'], $nkForm['allowedInputType'])) {
                $fieldFonction = 'nkFormInput_'. $itemData['type'];
                $input .= $fieldFonction($itemName, $itemData, $form['id']);
            }

            //if ($itemData['required'])
            //    $input .= '&nbsp;<b class="required">*</b>';

            if (array_key_exists('html', $itemData) && $itemData['html'] != '')
                $input .= $itemData['html'];

            $html .= '<div class="nkFormInputCell">'. $input .'</div>';
            //if ($input != '') {
            //    if ($form['formStyle'] == 'table')
            //        $html .= '<div>'. $input .'</div>';
            //    else
            //        $html .= $input;
            //}

            $html .= '</div>' ."\n";
        }

        $r++;
    }

    if ($form['formStyle'] == 'table')
        $html .= '</div>';

    $html .= '<div id="'. nkForm_formatHtmlSelector($form['dataName'] .'ActionLinks') .'" class="nkFormActionLinks">';

    foreach ($form['itemsFooter'] as $itemName => $itemData) {
        if (array_key_exists('type', $itemData) && in_array($itemData['type'], $nkForm['allowedInputType'])) {
            $currentClass = 'nkFormInput_'. $itemData['type'];

            if (! array_key_exists('name', $itemData))
                $itemData['name'] = $itemName;

            if (in_array($itemData['type'], $nkForm['allowedInputType']))
                $html .= $currentClass($itemName, $itemData, $form['id']);
        }

        if (array_key_exists('html', $itemData) && $itemData['html'] != '')
            $html .= $itemData['html'];
    }

    foreach ($form['hiddenField'] as $params)
        $html .= '<input type="hidden"'. nkForm_formatAttribute($params, array('id', 'name', 'value')) .' />';

    if ($form['captchaField'] != '')
        $html .= $form['captchaField'];

    if ($jsFieldsData)
        nkForm_initCheckForm($form['id'], $jsFieldsData);

    unset($nkForm['config']);

    return "\n". '<form class="nkForm" id="'. $form['id'] .'" action="'. $form['action'] .'" method="'. strtoupper($form['method']) .'"'. $form['enctype'] .'>' ."\n"
     . $html .'</div>' ."\n" .'</form>' ."\n";
}

/**
 * Remove underscore character, apply camelcase formating and return the formated string.
 *
 * @param string $str : The raw string.
 * @return string
 */
function nkForm_underscore2camelcase($str) {
    $tmp = explode('_', $str);
    $tmp = array_map('ucfirst', $tmp);

    return implode($tmp);
}

/**
 * Get formated label tag.
 *
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkForm_formatLabel($params) {
    global $nkForm;

    if (array_key_exists('labelFormat', $params))
        $format = $params['labelFormat'];
    else
        $format = $nkForm['config']['labelFormat'];

    //if (! array_key_exists('labelClass', $params))
    //    $params['labelClass'] = array();

    return '<label for="'. $params['id'] .'">'. sprintf($format, $params['label']) .'</label>';
}

/**
 * Get formated fake label tag. (span tag is used for imitate label tag)
 *
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkForm_formatFakeLabel($params) {
    global $nkForm;

    if (array_key_exists('labelFormat', $params))
        $format = $params['labelFormat'];
    else
        $format = $nkForm['config']['labelFormat'];

    //if (! array_key_exists('fakeLabelClass', $params))
    //    $params['fakeLabelClass'] = array();

    return '<span class="fakeLabel">'. sprintf($format, $params['fakeLabel']) .'</span>';
}

/**
 * Initialize input data.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @param array $form : The form configuration.
 * @return array $input : The input data initialized.
 */
function nkFormInput_init($fieldName, &$params, $form) {
    global $nkForm;

    $params = $params + $nkForm['defaultInput'];

    //if (! array_key_exists('inputClass', $params))
    //    $params['inputClass'] = array();

    //if (array_key_exists('itemClass', $params)) {
    //    $params['inputClass']     = array_unique(array_merge($params['inputClass'], $params['itemClass']));
    //    $params['labelClass']     = array_unique(array_merge($params['labelClass'], $params['itemClass']));
    //    $params['fakeLabelClass'] = array_unique(array_merge($params['fakeLabelClass'], $params['itemClass']));
    //}

    if (! array_key_exists('name', $params))
        $params['name'] = $fieldName;

    $params['camelCaseName'] = nkForm_underscore2camelcase($fieldName);

    if (! array_key_exists('id', $params))
        $params['id'] = $form['fieldsPrefix'] . $params['camelCaseName'];

    if (isset($params['dataType']) && $params['dataType'] == 'integer' && isset($params['range']) && ! is_array($params['range']))
        trigger_error('range field parameter must be a array !', E_USER_ERROR);
}

/**
 *  Generate a button.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_button($fieldName, $params) {
    $attributes = array('type', 'id', 'inputClass', 'name', 'value', 'disabled');

    return '<input'. nkForm_formatAttribute($params, $attributes) .' />';
}

/**
 *  Generate a checkable field.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_checkbox($fieldName, $params) {
    global $nkTemplate;

    if ($nkTemplate['interface'] == 'frontend') {
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

/**
 *  Generate a field for hex color data with colorpicker selection.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_color($fieldName, $params) {
    $attributes = array('id', 'name', 'size', 'value', 'maxlength', 'disabled', 'inputClass');

    $params['size'] = $params['maxlength'] = 6;
    $params['inputClass'][] = 'color';

    // TODO : Move jscolor librairy to media directory
    nkTemplate_addJSFile('modules/Admin/jscolor/jscolor.js');

    return '<input type="text"'. nkForm_formatAttribute($params, $attributes) .' />';
}

/**
 *  Generate a field for date data.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_date($fieldName, $params) {
    global $language;

    $attributes = array('id', 'inputClass', 'name', 'value', 'size', 'maxlength');

    $params['maxlength']  = 10;
    $params['class'][]    = 'datepicker';


    nkTemplate_addJSFile(JQUERY_LIBRAIRY, 'librairy');
    nkTemplate_addJSFile(JQUERY_UI_LIBRAIRY, 'librairyPlugin');

    if ($language == 'english')
        $datepickerLocale = 'en-GB';
    else if ($language == 'french')
        $datepickerLocale = 'fr';

    nkTemplate_addJSFile(
        'media/jquery-ui/datepicker-i18n/datepicker-'. $datepickerLocale .'.js', 'librairyPlugin'
    );

    nkTemplate_addCSSFile(JQUERY_UI_CSS);

    $options = array();
    $options[] = 'dateFormat: \''. (($language == 'french') ? 'dd/mm/yy' : 'mm/dd/yy') .'\'';

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
    buttonImage: \'media/jquery-ui/images/calendar.gif\',
    buttonImageOnly: true,'. implode(',', $options) .'
});',
        'jqueryDomReady'
    );

    return '<input type="text"'. nkForm_formatAttribute($params, $attributes) .' />';
}

/**
 *  Generate a file field.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_file($fieldName, $params) {
    global $nkForm;

    $nkForm['config']['enctype'] = ' enctype="multipart/form-data"';

    $attributes = array('type', 'id', 'inputClass', 'name', 'disabled', 'multiple');

    /*
    if (! array_key_exists('filesize', $params)) {
        // http://stackoverflow.com/questions/13076480/php-get-actual-maximum-upload-size
        $maxsize = ini_get('upload_max_filesize');

        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);

        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            $maxsize = round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            $maxsize = round($size);
        }
    }
    else
        $maxsize = $params['filesize'];

    // 1 seul, avant input de type file
    $html = '<input type="hidden" name="MAX_FILE_SIZE" value="'. $maxsize .'" />';

    */

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
*/

/**
 *  Generate a password field.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_password($fieldName, $params) {
    $attributes = array('id', 'name');

    return '<input type="password"'. nkForm_formatAttribute($params, $attributes) .' value="" />';
}

/**
 *  Generate a multiple radio tag.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_radio($fieldName, $params) {
    if (! array_key_exists('subType', $params)) $params['subType'] = 'inline';

    $name = nkForm_formatAttribute($params, array('name'));

    $html = '<div class="nkInputRadio-'. $params['subType'] .'">';

    foreach ($params['data'] as $value => $label) {
        // Format the checked attribute
        $checked = nkForm_formatAttribute($params, array('checked'), $value);

        // Format id attribute
        $radioId = $params['name'] .'_'. $value;

        $html .= '<div><input type="radio"'. $name .' id="'. $radioId .'" value="'. $value .'"'. $checked .' />'
        . '<label for="'. $radioId .'">'. $label .'</label></div>';
    }

    $html .= '</div>';

    return $html;
}

/**
 *  Load select options configuration and return options array for input select function.
 *
 * @param array $params : The input data.
 * @return array
 */
function nkForm_loadSelectOptions($params) {
    static $cache = array();

    if (is_array($params['optionsName'])) {
        if (count($params['optionsName']) >= 2) {
            if (isset($params['optionsName'][2])
                && ! $params['optionsName'][2]
                && isset($cache[$params['optionsName'][1]])
            )
                return $cache[$params['optionsName'][1]];

            $optionsFile = 'modules/'. $params['optionsName'][0] .'/config/select'. ucfirst($params['optionsName'][1]) .'Options.php';
        }
        else {
            trigger_error('You must defined a module and options name for this optionsName array configuration !', E_USER_WARNING);
            return $options;
        }
    }

    $options = array();

    if (is_file($optionsFile)) {
        $cfg = include $optionsFile;

        if (array_key_exists('functionName', $cfg) && function_exists($cfg['functionName']))
            return $cfg['functionName']($params);

        if (array_key_exists('defaultValue', $cfg) && is_array($cfg['defaultValue']))
            $options = $cfg['defaultValue'];

        if (isset($cfg['sql']) && isset($cfg['sql']['query'])) {
            if (! array_key_exists('order', $cfg['sql']))
                $cfg['sql']['order'] = false;

            if (! array_key_exists('dir', $cfg['sql']))
                $cfg['sql']['dir'] = 'ASC';

            $dbrData = nkDB_selectMany(
                $cfg['sql']['query'],
                $cfg['sql']['order'],
                $cfg['sql']['dir']
            );

            if ($dbrData) {
                foreach ($dbrData as $data)
                    $options[$data[$cfg['key']]] = printSecuTags($data[$cfg['value']]);

                $cache[$params['optionsName'][1]] = $options;
            }
        }
    }

    return $options;
}

/**
 *  Generate a field selection.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_select($fieldName, $params) {
    $attributes = array('id', 'name', 'disabled', 'multiple', 'size');

    if (array_key_exists('multiple', $params) && $params['multiple'])
        $params['multiple'] = 'multiple';

    if (array_key_exists('optionsName', $params))
        $params['options'] = nkForm_loadSelectOptions($params);

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

            $selected = ($params['value'] == $key) ? ' selected="selected"' : '';

            $html .= '<option value="'. $key .'"'. $selected .'>'. $value .'</option>';
        }
    }

    $html .= '</select>';

    return $html;
}

/**
 *  Generate a field for text data.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_text($fieldName, $params) {
    $attributes = array('id', 'name', 'size', 'value', 'maxlength', 'disabled');

    if ($params['htmlspecialchars'])
        $params['value'] = nkHtmlSpecialChars($params['value']);

    return '<input type="text"'. nkForm_formatAttribute($params, $attributes) .' />';
}

/**
 *  Generate a textarea field.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_textarea($fieldName, $params) {
    global $nkTemplate;

    $attributes = array('id', 'inputClass', 'name', 'cols', 'rows', 'maxlength');

    if (array_key_exists('subType', $params) && $params['subType'] != 'normal') {
        if (! defined('EDITOR_CHECK')) define('EDITOR_CHECK', 1);

        if ($nkTemplate['interface'] == 'frontend') {
            if ($params['subType'] == 'advanced')
                $params['id'] = 'e_advanced';
            else
                $params['id'] = 'e_basic';
        }
        else {
            $params['inputClass'][] = 'editor';
        }
    }

    // Set cols and rows attribute of nk textarea
    if (! array_key_exists('cols', $params)) $params['cols'] = 70;
    if (! array_key_exists('rows', $params)) $params['rows'] = 15;

    if ($params['htmlspecialchars'])
        $params['value'] = nkHtmlSpecialChars($params['value']);

    // Generate a input textaera with markitup style
    return '<textarea'. nkForm_formatAttribute($params, $attributes) .'>'. $params['value'] .'</textarea>';
}

/**
 *  Generate a field for time data.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_time($fieldName, $params) {
    $attributes = array('id', 'inputClass', 'name', 'value', 'maxlength');

    $params['class'][]    = 'time';
    $params['maxlength']  = 5;

    return '<input type="text"'. nkForm_formatAttribute($params, $attributes) .' />';
}

/**
 *  Generate a submit button.
 *
 * @param string $fieldName : The key of input data in form configuration.
 * @param array $params : The input data.
 * @return string HTML code
 */
function nkFormInput_submit($fieldName, $params) {
    $attributes = array('id', 'name', 'inputClass', 'value', 'disabled');

    return '<input type="submit" '. nkForm_formatAttribute($params, $attributes) .' />';
}

?>