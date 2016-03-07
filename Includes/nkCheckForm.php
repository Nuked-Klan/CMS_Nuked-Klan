<?php
/**
 * nkCheckForm.php
 *
 * Manage form validation
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');

// TODO : Translate

// TODO : Check while install / update if encoding is supported with mb_list_encodings()
//mb_internal_encoding('UTF-8');
mb_internal_encoding('ISO-8859-1');


/**
 * Check submited form.
 *
 * @param array $form : The form configuration.
 * @param array $fields : The fields list to check.
 * @param mixed $validData : The valid value of checked fields.
 * @return bool : The result of form validation.
 */
function nkCheckForm(&$form, $fields, &$validData = null) {
    if (! isset($form['dataName']) || $form['dataName'] == '')
        trigger_error('You must defined a data name for this form configuration !', E_USER_ERROR);;

    if (! is_array($validData)) $validData = null;

    // TODO : $_FILES ?
    $_SESSION['save_'. $form['dataName']] = $_POST;

    if (isset($form['captcha']) && $form['captcha'] && initCaptcha()) {
        if (! validCaptchaCode())
            return false;
    }

    if (isset($form['token'])
        && is_array($form['token'])
        && isset($form['token']['refererData'])
        && is_array($form['token']['refererData'])
    ) {
        if (! isset($form['token']['name']) || $form['token']['name'] == '')
            $form['token']['name'] = $form['dataName'] .'Form';

        include_once 'Includes/nkToken.php';

        if (! isset($form['token']['duration']) || ! ctype_digit($form['token']['duration']))
            $form['token']['duration'] = 300;

        if (! nkToken_valid($form['token']['name'], $form['token']['duration'], $form['token']['refererData'])) {
            printNotification(__('TOKEN_NO_VALID'), 'error');
            return false;
        }
    }
    else
        trigger_error('You must defined a token refererData for this form configuration !', E_USER_ERROR);

    foreach ($fields as $field) {
        if (isset($form['items'][$field]) && isset($form['items'][$field]['type'])) {
            if (! isset($form['items'][$field]['required']) || ! is_bool($form['items'][$field]['required']))
                $form['items'][$field]['required'] = false;

            if (array_key_exists('name', $form['items'][$field]))
                $fieldName = str_replace('[]', '', $form['items'][$field]['name']);
            else
                $fieldName = $field;

            if ($form['items'][$field]['type'] == 'checkbox') {
                nkCheckForm_checkbox($fieldName, $form['items'][$field], $validData);
            }
            else {
                if ($validData !== null && array_key_exists($fieldName, $validData))
                    continue;

                if (isset($form['items'][$field]['uploadField'])
                    && isset($form['items'][$form['items'][$field]['uploadField']])
                    && isset($form['items'][$form['items'][$field]['uploadField']]['type'])
                    && $form['items'][$form['items'][$field]['uploadField']]['type'] == 'file'
                )
                    $form['items'][$form['items'][$field]['uploadField']]['urlField'] = $field;

                if (isset($form['items'][$field]['uploadField']) && isset($form['items'][$form['items'][$field]['uploadField']])) {
                    if (! isset($form['items'][$form['items'][$field]['uploadField']]['required'])
                        || ! is_bool($form['items'][$form['items'][$field]['uploadField']]['required'])
                    )
                        $form['items'][$form['items'][$field]['uploadField']]['required'] = false;

                    if (! nkCheckForm_checkFormInput(
                        $form['items'][$field]['uploadField'],
                        $form['items'][$form['items'][$field]['uploadField']],
                        $form,
                        $validData
                    ))
                        return false;

                    //if (isset($form['items'][$field]['uploadValue']))
                    //    continue;

                    if ($validData !== null && array_key_exists($fieldName, $validData))
                        continue;
                }

                if (! nkCheckForm_checkFormInput($fieldName, $form['items'][$field], $form, $validData))
                    return false;
            }
        }
    }

    unset($_SESSION['save_'. $form['dataName']]);

    return true;
}

/**
 * Check field value of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @param mixed $validData : The valid value of checked fields.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkFormInput($field, &$fieldData, &$form, &$validData) {
    $multiple = false;

    if (isset($_POST[$field]) && is_array($_POST[$field]))
        $multiple = true;

    if (isset($fieldData['checkFieldFunction']) && function_exists($fieldData['checkFieldFunction'])) {
        if (! $fieldData['checkFieldFunction']($field, $fieldData))
            return false;
    }
    else if ($fieldData['type'] == 'file') {
        return nkCheckForm_checkFileHandle($field, $fieldData, $form, $validData);
    }
    else if (isset($fieldData['dataType'])) {
        switch ($fieldData['dataType']) {
            case 'alpha' :
            case 'alphanumeric' :
            case 'integer' :
                if ($fieldData['dataType'] == 'integer' && isset($fieldData['range'])) {
                    if (! is_array($fieldData['range']))
                        trigger_error('range field parameter must be a array !', E_USER_ERROR);
                }

                if (! nkCheckForm_checkMultipleHandle('nkCheckForm_checkValueType', $field, $fieldData, $validData, $multiple))
                    return false;
                break;

            case 'email' :
                if (! nkCheckForm_checkEmail($field, $fieldData))
                    return false;
                break;

            case 'username' :
                if (! nkCheckForm_checkNickname($field, $fieldData))
                    return false;
                break;

            case 'date' :
                if (! nkCheckForm_checkDate($field, $fieldData))
                    return false;
                break;

            case 'html' :
                if (! nkCheckForm_checkHtml($field, $fieldData))
                    return false;
                break;
        }

        return nkCheckForm_checkMultipleHandle('nkCheckForm_checkInputText', $field, $fieldData, $validData, $multiple);
    }

    if ($validData !== null)
        $validData[$field] = $_POST[$field];

    return true;
}

/**
 * Check a single value or multiple value with validation function.
 *
 * @param string $function : The validation function name to execute.
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @param array $fieldData : The field configuration.
 * @param bool $multiple : If it's a single value or multiple value.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkMultipleHandle($function, $field, $fieldData, &$validData, $multiple) {
    if ($multiple) {
        foreach ($_POST[$field] as $k => $value) {
            if (! $function($field, $fieldData, $validData, $value))
                return false;
        }
    }
    else {
        $value = (isset($_POST[$field])) ? $_POST[$field] : '';

        if (! $function($field, $fieldData, $validData, $value))
            return false;
    }

    return true;
}

// %s ne contient pas que des caractères alphabétique
// %s ne contient pas que des caractères alphanumérique
// %s ne contient pas que des entiers

/**
 * Check alphabetic, alphanumeric or integer field value of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @param string $value : The current value of field.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkValueType($field, $fieldData, &$validData, $value) {
    if ($fieldData['dataType'] == 'alpha') {
        $check = ctype_alpha($value);
    }
    else if ($fieldData['dataType'] == 'alphanumeric') {
        $check = ctype_alnum($value);
    }
    else if ($fieldData['dataType'] == 'integer') {
        $check = ctype_digit($value);
    }

    if (! $check) {
        if ($fieldData['required']) {
            printNotification(sprintf(__('NOT_'. strtoupper($fieldData['dataType']) .'_FIELD'), $fieldData['label']), 'error');
            return false;
        }
        //else
        //    $_POST[$field] = '';
    }

    if ($fieldData['dataType'] == 'integer' && isset($fieldData['range'])) {
        if (isset($fieldData['range']['min']) && $value < $fieldData['range']['min']) {
            //printNotification(sprintf(__('NOT_VALID_RANGE_FIELD'),
            //    $fieldData['label'], $fieldData['range']['min']), 'error');

            return false;
        }
        if (isset($fieldData['range']['max']) && $value > $fieldData['range']['max']) {
            //printNotification(sprintf(__('NOT_VALID_RANGE_FIELD'),
            //    $fieldData['label'], $fieldData['range']['max']), 'error');

            return false;
        }
    }

    return true;
}

/**
 * Check email field value of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkEmail($field, &$fieldData) {
    if (! isset($fieldData['checkRegistred']) || ! is_bool($fieldData['checkRegistred']))
        $fieldData['checkRegistred'] = false;

    $_POST[$field] = checkEmail($_POST[$field], $fieldData['checkRegistred']);

    if (($error = getCheckEmailError($_POST[$field])) !== false) {
        if ($fieldData['required']) {
            printNotification($error, 'error');
            return false;
        }
        else
            $_POST[$field] = '';
    }

    return true;
}

/**
 * Check nickname field value of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkNickname($field, $fieldData) {
    $_POST[$field] = checkNickname($_POST[$field]);

    if (($error = getCheckNicknameError($_POST[$field])) !== false) {
        if ($fieldData['required']) {
            printNotification($error, 'error');
            return false;
        }
        else
            $_POST[$field] = '';
    }

    return true;
}

/**
 * Check date field value of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkDate($field, &$fieldData) {
    if (! isset($fieldData['dateFormat']))
        $fieldData['dateFormat'] = 'Y-m-d';

    // http://php.net/manual/fr/function.checkdate.php#113205
    $d = date_create_from_format($fieldData['dateFormat'], $_POST[$field]);

    if ($d && date_format($d, $fieldData['dateFormat']) == $_POST[$field])
        return true;

    // date_get_last_errors()

    if ($fieldData['required']) {
        printNotification(sprintf(__('NOT_VALID_DATE_FIELD'), $fieldData['label']), 'error');
        return false;
    }

    $_POST[$field] = '';

    return true;
}

/**
 * Check html field value of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkHtml($field, $fieldData) {
    $_POST[$field] = secu_html(nkHtmlEntityDecode($_POST[$field]));

    if ($_POST[$field] === false) {
        if ($fieldData['required']) {
            printNotification(sprintf(__('NOT_HTML_FIELD'), $fieldData['label'], 'error'));
            return false;
        }
        else
            $_POST[$field] = '';
    }

    return true;
}

/**
 * Check file of submited form
 * for a single file or multiple file.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @param mixed $validData : The valid value of checked fields.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkFileHandle($field, &$fieldData, &$form, &$validData) {
    require_once 'Includes/nkUpload.php';

    if (! isset($fieldData['multiple']) || ! is_bool($fieldData['multiple']))
        $fieldData['multiple'] = false;

    if ($fieldData['multiple'] && is_array($_FILES[$field]['error'])) {
        $nbFile = count($_FILES[$field]['error']);

        for ($i = 0; $i < $nbFile; $i++) {
            if (! nkCheckForm_checkFile($field, $fieldData, $form, $validData, $_FILES[$field]['name'][$i], $i))
                return false;
        }
    }
    else {
        if (! nkCheckForm_checkFile($field, $fieldData, $form, $validData, $_FILES[$field]['name']))
            return false;
    }

    return true;
}

/**
 * Check file of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @param mixed $validData : The valid value of checked fields.
 * @param array $fileData : The current data of uploaded file.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkFile($field, &$fieldData, &$form, &$validData, $filename, $i = null) {
    if ($filename != '') {
        /*if (! isset($fieldData['urlField'])
            || ! isset($form['items'][$fieldData['urlField']])
        ) {
            printNotification(sprintf(__('NO_URL_FIELD'), $fieldData['label']), 'error');
            return false;
        }*/

        if (! isset($fieldData['uploadDir'])) {
            printNotification(sprintf(__('NO_UPLOAD_DIR_FIELD'), $fieldData['label']), 'error');
            return false;
        }

        if (isset($fieldData['overwriteField'], $form['items'][$fieldData['overwriteField']])
            && isset($_POST[$fieldData['overwriteField']], $form['items'][$fieldData['overwriteField']]['inputValue'])
            && $form['items'][$fieldData['overwriteField']]['inputValue'] != ''
            && $_POST[$fieldData['overwriteField']] == $form['items'][$fieldData['overwriteField']]['inputValue']
        ) {
            $fieldData['overwrite'] = true;
        }

        list($filename, $uploadError) = nkUpload_check($field, $fieldData, $i);

        if ($uploadError !== false) {

            if ($fieldData['required']) {
                printNotification($uploadError, 'error');
                return false;
            }
            else {
                return true;
            }
        }

        if ($validData !== null) {
            if ($fieldData['multiple']) {
                $validData[$field][] = $filename;
            }
            else {
                if (isset($fieldData['urlField']) && $fieldData['urlField'] != '')
                    $validData[$fieldData['urlField']] = $filename;
                else
                    $validData[$field] = $filename;
            }
        }

        //if (! $fieldData['multiple'] && isset($fieldData['urlField'], $form['items'][$fieldData['urlField']]))
        //    $form['items'][$fieldData['urlField']]['uploadValue'] = true;
    }

    return true;
}

/**
 * Check field textual value of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @param mixed $validData : The valid value of checked fields.
 * @param string $value : The current value of field.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkInputText($field, $fieldData, &$validData, $value) {
    $trimmedField = trim($value);
    $error        = null;

    // Check if required field is empty
    if ($fieldData['required'] && $trimmedField == '') {
        $error = sprintf(__('EMPTY_FIELD'), $fieldData['label']);
    }
    // Check minimum length
    else if (isset($fieldData['minlength'])
        && ctype_digit($fieldData['minlength'])
        && $fieldData['minlength'] > 0
        && mb_strlen($trimmedField) < $fieldData['minlength']
    ) {
        $error = sprintf(__('WRONG_FIELD_MINLENGTH'), $fieldData['label'], $fieldData['minlength']);
    }
    // Check maximum length
    else if (isset($fieldData['maxlength'])
        && ctype_digit($fieldData['maxlength'])
        && $fieldData['maxlength'] > 0
        && mb_strlen($trimmedField) > $fieldData['maxlength']
    ) {
        $error = sprintf(__('WRONG_FIELD_MAXLENGTH'), $fieldData['label'], $fieldData['maxlength']);
    }

    if ($error !== null) {
        if ($fieldData['required']) {
            printNotification($error, 'error');
            return false;
        }
    }
    else {
        if ($validData !== null)
            $validData[$field] = $value;
    }

    return true;
}

/**
 * Check checkbox value of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @param mixed $validData : The valid value of checked fields.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkbox($field, $fieldData, &$validData) {
    if (isset($_POST[$field], $fieldData['inputValue'])
        && $fieldData['inputValue'] != ''
        && $_POST[$field] == $fieldData['inputValue']
    ) {
        if ($validData !== null)
            $validData[$field] = $fieldData['inputValue'];
    }
    else if (isset($fieldData['defaultValue']) && $fieldData['defaultValue'] != '') {
        if ($validData !== null)
            $validData[$field] = $fieldData['defaultValue'];
    }
}

?>