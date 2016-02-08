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
        if (isset($form['items'][$field])) {
            if (! isset($form['items'][$field]['required']) || ! is_bool($form['items'][$field]['required']))
                $form['items'][$field]['required'] = false;

            if (array_key_exists('name', $form['items'][$field]))
                $fieldName = $form['items'][$field]['name'];
            else
                $fieldName = $field;

            if ($form['items'][$field]['type'] == 'checkbox') {
                nkCheckForm_checkbox($fieldName, $form['items'][$field], $validData);
            }
            else {
                if (isset($form['items'][$field]['uploadField'])
                    && isset($form['items'][$form['items'][$field]['uploadField']])
                    && isset($form['items'][$form['items'][$field]['uploadField']]['type'])
                    && $form['items'][$form['items'][$field]['uploadField']]['type'] == 'file'
                )
                    $form['items'][$form['items'][$field]['uploadField']]['urlField'] = $field;

                if (isset($form['items'][$field]['uploadField']) && isset($form['items'][$form['items'][$field]['uploadField']])) {
                    if (! nkCheckForm_checkFormInput(
                        $form['items'][$field]['uploadField'],
                        $form['items'][$form['items'][$field]['uploadField']],
                        $form,
                        $validData
                    ))
                        return false;

                    if (isset($form['items'][$field]['uploadValue']))
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
    if ($fieldData['type'] != 'file') {
        if (isset($_POST[$field]))
            $fieldData['trimmedField'] = trim($_POST[$field]);
        else
            $fieldData['trimmedField'] = $_POST[$field] = '';
    }
    else
        $fieldData['dataType'] = 'file';

    if (isset($fieldData['checkFieldFunction']) && function_exists($fieldData['checkFieldFunction'])) {
        if (! $fieldData['checkFieldFunction']($field, $fieldData))
            return false;
    }
    else if (isset($fieldData['dataType'])) {
        switch ($fieldData['dataType']) {
            case 'alpha' :
            case 'alphanumeric' :
            case 'numeric' :
                if (! nkCheckForm_checkValueType($field, $fieldData))
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

            case 'file' :
                return nkCheckForm_checkFile($field, $fieldData, $form, $validData);
                break;
        }

        return nkCheckForm_checkInputText($field, $fieldData, $validData);
    }

    if ($validData !== null)
        $validData[$field] = $_POST[$field];

    return true;
}

// %s ne contient pas que des caractères alphabétique
// %s ne contient pas que des caractères alphanumérique
// %s ne contient pas que des entiers

/**
 * Check alphabetic, alphanumeric or numeric field value of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkValueType($field, $fieldData) {
    if ($fieldData['dataType'] == 'alpha') {
        $check = ctype_alpha($_POST[$field]);
    }
    else if ($fieldData['dataType'] == 'alphanumeric') {
        $check = ctype_alnum($_POST[$field]);
    }
    else if ($fieldData['dataType'] == 'numeric') {
        $check = ctype_digit($_POST[$field]);
    }

    if (! $check) {
        if ($fieldData['required']) {
            printNotification(sprintf(__('NOT_'. strtoupper($fieldData['dataType']) .'_FIELD'), $fieldData['label']), 'error');
            return false;
        }
        else
            $_POST[$field] = '';
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
 * Check file of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @param mixed $validData : The valid value of checked fields.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkFile($field, &$fieldData, &$form, &$validData) {
    require_once 'Includes/nkUpload.php';

    if ($_FILES[$field]['name'] != '') {
        if (! isset($fieldData['urlField'])
            || ! isset($form['items'][$fieldData['urlField']])
        ) {
            printNotification(sprintf(__('NO_URL_FIELD'), $fieldData['label']), 'error');
            return false;
        }

        if (! isset($fieldData['fileType']))
            $fieldData['fileType'] = 'no-html-php';

        if (! isset($fieldData['uploadDir'])) {
            printNotification(sprintf(__('NO_UPLOAD_DIR_FIELD'), $fieldData['label']), 'error');
            return false;
        }

        if (! array_key_exists('fileSize', $fieldData))
            $fieldData['fileSize'] = null;

        if (! isset($fieldData['fileRename']))
            $fieldData['fileRename'] = false;

        list($filename, $uploadError) = nkUpload_check(
            $field,
            $fieldData['fileType'],
            $fieldData['uploadDir'],
            $fieldData['fileSize'],
            $fieldData['fileRename']
        );

        if ($uploadError !== false) {
            if ($fieldData['required']) {
                printNotification($uploadError, 'error');
                return false;
            }
            else {
                return true;
            }
        }

        if ($validData !== null)
            $validData[$fieldData['urlField']] = $filename;

        $form['items'][$fieldData['urlField']]['uploadValue'] = true;
    }

    return true;
}

/**
 * Check field textual value of submited form.
 *
 * @param string $field : The field key in form configuration.
 * @param array $fieldData : The field configuration.
 * @param mixed $validData : The valid value of checked fields.
 * @return bool : The result of field validation.
 */
function nkCheckForm_checkInputText($field, $fieldData, &$validData) {
    $error = null;

    // Check if not empty
    if (isset($fieldData['noempty'])
        && $fieldData['noempty']
        && $fieldData['trimmedField'] == ''
    ) {
        $error = sprintf(__('EMPTY_FIELD'), $fieldData['label']);
    }
    // Check minimum length
    else if (isset($fieldData['minlength'])
        && ctype_digit($fieldData['minlength'])
        && $fieldData['minlength'] > 0
        && mb_strlen($fieldData['trimmedField']) < $fieldData['minlength']
    ) {
        $error = sprintf(__('WRONG_FIELD_MINLENGTH'), $fieldData['label'], $fieldData['minlength']);
    }
    // Check maximum length
    else if (isset($fieldData['maxlength'])
        && ctype_digit($fieldData['maxlength'])
        && $fieldData['maxlength'] > 0
        && mb_strlen($fieldData['trimmedField']) > $fieldData['maxlength']
    ) {
        $error = sprintf(__('WRONG_FIELD_MAXLENGTH'), $fieldData['label'], $fieldData['maxlength']);
    }

    if ($error !== null) {
        if ($fieldData['required']) {
            printNotification($error, 'error');
            return false;
        }
        else
            $_POST[$field] = '';
    }

    if ($validData !== null)
        $validData[$field] = $_POST[$field];

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