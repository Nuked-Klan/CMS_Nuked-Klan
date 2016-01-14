<?php
/**
 * nkList.php
 *
 * Librairy to manage backend list
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * Initialisation of nkList global vars
 */
$GLOBALS['nkList'] = array(
    'inputMode'     => 'php',
    'p'             => 1,
    'sortUrl'       => '',
    'paginationUrl' => '',
    'sqlClause'     => array(
        'where'         => '',
        'order'         => false,
        'dir'           => 'asc',
        'limit'         => false,
        'offset'        => 0
    ),
    'sortby'        => '',
    'dir'           => 'asc',
    'callbackRowFunction' => false,
);

/**
 * Initialisation and checking of configuration list.
 *
 * @param array $config : The configuration list. (pass by reference)
 * @return void
 */
function nkList_init(&$config) {
    global $nkList, $file, $page;

    if (! is_array($config)) {
        trigger_error('$config must be a array', E_USER_NOTICE);
        return false;
    }

    if (array_key_exists('sqlQuery', $config) && $config['sqlQuery'] !== '')
        $nkList['inputMode'] = 'sql';
    else if (array_key_exists('dataList', $config) && function_exists($config['dataList']))
        $nkList['inputMode'] = 'php';
    else {
        trigger_error('Input data isn\'t defined for nkList', E_USER_NOTICE);
        return false;
    }

    if (! array_key_exists('rowId', $config))
        $config['rowId'] = 'id';

    if (! array_key_exists('css', $config))
        $config['css'] = array();

    if (! array_key_exists('css', $config))
        $config['css'] = array('tablePrefix' => 'nk', 'fieldsPrefix' => 'f');
    else {
        if (! (array_key_exists('tablePrefix', $config['css']) && $config['css']['tablePrefix'] !== ''))
            $config['css']['tablePrefix'] = 'nk';

        if (! (array_key_exists('fieldsPrefix', $config['css']) && $config['css']['fieldsPrefix'] !== ''))
            $config['css']['fieldsPrefix'] = 'f';
    }

    list($nkList['p']) = getRequestVars('p');

    $nkList['p'] = max(1, (int) $nkList['p']);

    // Prepare basic url for list
    $config['baseUrl'] = 'index.php?file='. $file .'&amp;page='. $page;

    if (array_key_exists('uriData', $config)
        && is_array($config['uriData'])
        && ! empty($config['uriData'])
    ) {
        foreach ($config['uriData'] as $uriKey => $uriValue)
            $config['baseUrl'] .= '&amp;'. $uriKey .'='. $uriValue;

        unset($config['uriData']);
    }

    nkList_prepareSortingData($config);

    $nkList['sortUrl'] = $nkList['paginationUrl'] = $config['baseUrl'];

    if ($_REQUEST['op'] != 'index') {
        $nkList['sortUrl']          .= '&amp;op='. $_REQUEST['op'];
        $nkList['paginationUrl']    .= '&amp;op='. $_REQUEST['op'];
    }

    if ($nkList['inputMode'] == 'sql'
        && array_key_exists('limit', $config)
        && is_int($config['limit'])
        && $config['limit'] > 0
    )
        $nkList['sqlClause']['limit'] = $config['limit'];


    if ($nkList['p'] > 1)
        $nkList['sortUrl'] .= '&amp;p='. $nkList['p'];

    // Set autocomplete vars
    if (array_key_exists('autocomplete', $config) && is_array($config['autocomplete']))
        nkList_checkAutocompleteData($config);
    else
        $config['autocomplete'] = false;

    if (! array_key_exists('fields', $config) || ! is_array($config['fields'])) {
        trigger_error('Bad $config[\'fields\'] value', E_USER_NOTICE);
        return false;
    }
    else
        nkList_checkFieldData($config);

    if ($nkList['inputMode'] == 'sql' && array_key_exists('defaultSortables', $config)) {
        if (array_key_exists('order', $config['defaultSortables']))
            $nkList['sqlClause']['order'] = $config['defaultSortables']['order'];

        if (array_key_exists('dir', $config['defaultSortables']))
            $nkList['sqlClause']['dir'] = $config['defaultSortables']['dir'];
    }

    if (array_key_exists('callbackRowFunction', $config) && is_array($config['callbackRowFunction']))
        nkList_checkCallbackRowFunctionData($config);

    // Prepare page link of list data
    if ($nkList['inputMode'] == 'sql' && $nkList['sqlClause']['limit'] !== false) {
        $nkList['sqlClause']['offset']   = ($nkList['p'] - 1) * $config['limit'];
        $nkList['sqlClause']['limit']    = $config['limit'];
    }

    return true;
}

/**
 * Prepare the following query sorting selected.
 *
 * @param array $config : The configuration list.
 * @return void
 */
function nkList_prepareSortingData($config) {
    global $nkList;

    // TODO REVOIR POUR TRI DE CHAMPS MULTIPLE
    if (array_key_exists('sortby', $_GET)) {
        if (array_key_exists($_GET['sortby'], $config['sortables'])) {
            if ($config['fields'][$_GET['sortby']]['sort'] == 'sql')
                $nkList['sqlClause']['order'] = $config['sortables'][$_GET['sortby']];

            $nkList['sortby']            = $_GET['sortby'];
            $nkList['paginationUrl']    .= '&amp;sortby='. $_GET['sortby'];

            if (array_key_exists('dir', $_GET)) {
                if (in_array($_GET['dir'], array('asc', 'desc'))) {
                    $nkList['dir']               = $_GET['dir'];
                    $nkList['paginationUrl']    .= '&amp;dir='. $_GET['dir'];

                    if ($nkList['inputMode'] == 'sql')
                        $nkList['sqlClause']['dir'] = $_GET['dir'];
                }
            }
        }
    }
}

/**
 * Check a string value of configuration key.
 *
 * @param array $config : The configuration list.
 * @param string $fieldName : The name of configuration key.
 * @return bool : Return true if configuration key exist, is a string and not empty, false also.
 */
function nkList_checkConfigStringValue($config, $keyName) {
    if (array_key_exists($keyName, $config)
        && is_string($config[$keyName])
        && $config[$keyName] != ''
    )
        return true;

    return false;
}

/**
 * Check data of editing link in configuration list.
 *
 * @param array $config : The configuration list. (pass by reference)
 * @return void
 */
function nkList_checkEditLinkData(&$config) {
    if (nkList_checkConfigStringValue($config['fields']['edit'], 'op')
        && nkList_checkConfigStringValue($config['fields']['edit'], 'imgTitle')
    )
        return true;

    unset($config['fields']['edit']);

    return false;
}

/**
 * Check data of deleting link in configuration list.
 *
 * @param array $config : The configuration list. (pass by reference)
 * @return void
 */
function nkList_checkDeleteLinkData(&$config) {
    if (nkList_checkConfigStringValue($config['delete'], 'op')
        && nkList_checkConfigStringValue($config['delete'], 'confirmField')
        && array_key_exists($config['delete']['confirmField'], $config['fields'])
        && nkList_checkConfigStringValue($config['delete'], 'imgTitle')
        && nkList_checkConfigStringValue($config['delete'], 'confirmTxt')
        //&& nkList_checkConfigStringValue($config['delete'], 'token')
    )
        return true;

    unset($config['delete']);

    return false;
}

/**
 * Check and prepare data of checkable collumn field in configuration list.
 *
 * @param array $config : The configuration list. (pass by reference)
 * @param string $fieldName : The name of collumn field.
 * @return void
 */
function nkList_checkCheckboxData(&$config, $fieldName) {
    if (nkList_checkConfigStringValue($config['fields'][$fieldName], 'formAction')
        && nkList_checkConfigStringValue($config['fields'][$fieldName], 'submitTxt')
        && nkList_checkConfigStringValue($config['fields'][$fieldName], 'confirmTxt')
    ) {
        if (! nkList_checkConfigStringValue($config['fields'][$fieldName], 'checkboxName'))
            $config['fields'][$fieldName]['checkboxName'] = $fieldName;

        if (! nkList_checkConfigStringValue($config['fields'][$fieldName], 'checkboxValue'))
            $config['fields'][$fieldName]['checkboxValue'] = 1;

        $config['checkbox'] = array(
            'formAction'    => $config['fields'][$fieldName]['formAction'],
            'submitTxt'     => $config['fields'][$fieldName]['submitTxt'],
            'confirmTxt'    => $config['fields'][$fieldName]['confirmTxt']
        );

        unset(
            $config['fields'][$fieldName]['formAction'],
            $config['fields'][$fieldName]['submitTxt'],
            $config['fields'][$fieldName]['confirmTxt']
        );

        return true;
    }

    unset($config['fields'][$fieldName]);

    return false;
}

/**
 * Check data of image to display in configuration list.
 *
 * @param array $config : The configuration list. (pass by reference)
 * @return void
 */
function nkList_checkImageData(&$config, $fieldName) {
    if (nkList_checkConfigStringValue($config['fields'][$fieldName], 'src')) {

        if (! nkList_checkConfigStringValue($config['fields'][$fieldName], 'title'))
            $config['fields'][$fieldName]['title'] = '';

        return true;
    }

    unset($config['fields'][$fieldName]);

    return false;
}

/**
 * Check data of links for modify position in configuration list.
 *
 * @param array $config : The configuration list. (pass by reference)
 * @return void
 */
function nkList_checkPositionLinkData(&$config, $fieldName) {
    if (! nkList_checkConfigStringValue($config['fields'][$fieldName], 'labelUp'))
        $config['fields'][$fieldName]['labelUp'] = '';

    if (! nkList_checkConfigStringValue($config['fields'][$fieldName], 'labelUp'))
        $config['fields'][$fieldName]['labelDown'] = '';
}

/**
 * Check and prepare data of collumn field list in configuration list.
 *
 * @param array $config : The configuration list. (pass by reference)
 * @return void
 */
function nkList_checkFieldData(&$config) {
    global $nkList;

    foreach ($config['fields'] as $fieldName => &$fieldData) {
        if (! is_array($fieldData)) {
            unset($config['fields'][$fieldName]);
            continue;
        }

        if ($fieldName == 'edit') {
            if (! nkList_checkEditLinkData($config))
                continue;
        }
        else if ($fieldName == 'delete') {
            if (! nkList_checkDeleteLinkData($config))
                continue;
        }
        else {
            if (! array_key_exists('type', $fieldData)) {
                $config['fields'][$fieldName]['type'] = 'string';
            }
            else if ($config['fields'][$fieldName]['type'] == 'checkbox') {
                if (! nkList_checkCheckboxData($config, $fieldName))
                    continue;
            }
            else if ($config['fields'][$fieldName]['type'] == 'image') {
                if (! nkList_checkImageData($config, $fieldName))
                    continue;
            }
            else if ($config['fields'][$fieldName]['type'] == 'positionLink') {
                nkList_checkPositionLinkData($config, $fieldName);
            }

            if (! array_key_exists('sort', $fieldData) && $nkList['inputMode'] == 'sql')
                $config['fields'][$fieldName]['sort'] = 'sql';

            if (! (array_key_exists('sort', $fieldData)
                && is_string($fieldData['sort'])
                && in_array($fieldData['sort'], array('sql', 'php'))
                && array_key_exists('sortables', $config)
                && array_key_exists($fieldName, $config['sortables'])
            )) {
                $config['fields'][$fieldName]['sort'] = null;
            }
            else {
                $config['fields'][$fieldName]['sortUrl'] = $nkList['sortUrl'] .'&amp;sortby='. $fieldName;

                if ($fieldName == $nkList['sortby']) {
                    $config['fields'][$fieldName]['sortUrl'] .= '&amp;dir='. (($nkList['dir'] == 'asc') ? 'desc' : 'asc');
                    $config['fields'][$fieldName]['sortTitle'] = ($nkList['dir'] == 'asc') ? _SORTDESC : _SORTASC;
                }
                else {
                    $config['fields'][$fieldName]['sortUrl'] .= '&amp;dir=desc';
                    $config['fields'][$fieldName]['sortTitle'] = _SORTDESC;
                }
            }
        }
    }
}

/**
 * Check and prepare jQuery autocomplete data.
 *
 * @param array $config : The configuration list. (pass by reference)
 * @return void
 */
function nkList_checkAutocompleteData(&$config) {
    global $nkList;

    if ($config['autocomplete']['remoteUrl'] != ''
        && $config['autocomplete']['field'] != ''
    ) {
        // Define the search value if submit or actual searching
        list($search) = getRequestVars('search');

        // If search value exist, prepare sql query
        if ($search != '' ) {
            if ($nkList['inputMode'] == 'sql') {
                $nkList['sqlClause']['where'] = ' AND ('. $config['autocomplete']['field'] .
                    ' LIKE "%'. nkDB_escape($search, true) .'%" )';
            }

            $nkList['sortUrl']          .= '&amp;search='. $search;
            $nkList['paginationUrl']    .= '&amp;search='. $search;
        }

        $config['autocomplete']['formUrl']  = $config['baseUrl'];
        $config['autocomplete']['value']    = $search;
    }
    else
        $config['autocomplete'] = false;
}

/**
 * Check and prepare callback row function data.
 *
 * @param array $config : The configuration list. (pass by reference)
 * @return void
 */
function nkList_checkCallbackRowFunctionData(&$config) {
    global $nkList;

    if (array_key_exists('functionName', $config['callbackRowFunction'])
        && function_exists($config['callbackRowFunction']['functionName'])
    ) {
        $nkList['callbackRowFunction'] = array(
            'functionName' => $config['callbackRowFunction']['functionName'],
            'functionData' => array()
        );

        if (array_key_exists('functionData', $config['callbackRowFunction'])
            && is_array($config['callbackRowFunction']['functionData'])
        )
            $nkList['callbackRowFunction']['functionData'] = $config['callbackRowFunction']['functionData'];

    }
}

/**
 * Sort and return a multidimensional associative array.
 *
 * Pass the array, followed by the column names and sort flags.
 * Ex : $sorted = nkList_arrayOrderBy($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
 */
function nkList_arrayOrderBy() {
    // http://php.net/manual/fr/function.array-multisort.php#100534
    // thx to jimpoz for this function
    $args = func_get_args();
    $data = array_shift($args);

    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();

            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];

            $args[$n] = $tmp;
        }
    }

    $args[] = &$data;
    call_user_func_array('array_multisort', $args);

    return array_pop($args);
}

/**
 * Generate and return a HTML list
 *
 * @param array $config : The configuration list
 * @return HTML code : The list generated
 */
function nkList_generate($config) {
    global $nkList;

    if (! nkList_init($config))
        return;

    //
    if (array_key_exists('sqlQuery', $config) && $config['sqlQuery'] !== '') {
        $config['dataList'] = nkDB_selectMany(
            $config['sqlQuery'],
            $nkList['sqlClause']['order'], $nkList['sqlClause']['dir'],
            $nkList['sqlClause']['limit'], $nkList['sqlClause']['offset']
        );

        // Prepare navigation link
        if ($nkList['sqlClause']['limit'] !== false) {
            $totalNbDataList = nkDB_totalNumRows();

            $config['pagination'] = number(
                $nkList['p'], $totalNbDataList, $config['limit'], $nkList['paginationUrl'], true
            );
        }
        else
            $config['pagination'] = '';
    }
    else if (array_key_exists('dataList', $config) && function_exists($config['dataList'])) {
        $config['pagination'] = '';
    }
    else {
        trigger_error('Aucune source de donnée déclaré', E_USER_NOTICE);
        return;
    }

    // Count data to display in list
    $config['nbData'] = count($config['dataList']);

    // Apply callback function to each row
    if ($nkList['callbackRowFunction']) {
        $r = 0;

        foreach ($config['dataList'] as &$row) {
            $row = $nkList['callbackRowFunction']['functionName'](
                $row, $config['nbData'], $r, $nkList['callbackRowFunction']['functionData']
            );

            $r++;
        }
    }

    // Sort a field collumn on data list
    if ($nkList['sortby'] != ''
        && $config['fields'][$nkList['sortby']]['sort'] == 'php'
    ) {
        $config['dataList'] = nkList_arrayOrderBy(
            $config['dataList'], $nkList['sortby'],
            ($nkList['dir'] == 'asc') ? SORT_ASC : SORT_DESC
        );
    }

    // Add Javascript confirm message to delete
    if (array_key_exists('delete', $config)) {
        nkTemplate_addJS(
            'function confirmToDeleteInList(title, id) {' ."\n"
            . "\t" .'var confirmMsg = \''. $config['delete']['confirmTxt'] .'\';' ."\n\n"
            . "\t" .'if (confirm(confirmMsg.replace(/%s/, title))) {' ."\n"
            . "\t\t" .'document.location.href = \''. str_replace('&amp;', '&', $config['baseUrl']) .'&op='. $config['delete']['op'] .'&id=\' + id;' ."\n"
            . "\t" .'}' ."\n"
            . '}' ."\n"
        );
    }

    // Add jQuery autocomplete files
    if ($config['autocomplete'] !== false) {
        nkTemplate_addCSSFile(JQUERY_UI_CSS);

        nkTemplate_addJSFile(JQUERY_UI_LIBRAIRY, 'librairyPlugin');
        nkTemplate_addJS(
            '$("input[name=searchedValue]").autocomplete({' ."\n"
            . "\t" .'source: "'. $config['autocomplete']['remoteUrl'] .'",' ."\n"
            . "\t" .'minLength: 2,' ."\n"
            . "\t" .'delay: 500' ."\n"
            . '});' ."\n",
            'jqueryDomReady'
        );

        unset($config['autocomplete']['remoteUrl'], $config['autocomplete']['field']);
    }

    // Add Javascript for checkable collumn
    if (array_key_exists('checkbox', $config)) {
        nkTemplate_addJS(
            '$("#checkboxListSelector").on("click", function() {' ."\n"
            . "\t" .'if ($(this).attr("data-click-state") == 1) {' ."\n"
            . "\t\t" .'$(this).attr("data-click-state", 0);' ."\n"
            . "\t\t" .'$(this).attr("title", "'. _UNCHECKALL .'");' ."\n"
            . "\t\t" .'$("#nkListCheckbox input[type=checkbox]").prop("checked", false);' ."\n"
            . "\t" .'} else {' ."\n"
            . "\t\t" .'$(this).attr("data-click-state", 1);' ."\n"
            . "\t\t" .'$(this).attr("title", "'. _CHECKALL .'");' ."\n"
            . "\t\t" .'$("#nkListCheckbox input[type=checkbox]").prop("checked", true);' ."\n"
            . "\t" .'}' ."\n\n"
            . "\t" .'return false;' ."\n"
            . '});' ."\n\n"
            . '$("form#nkListCheckbox").submit(function() {' ."\n"
            . "\t" .'if (confirm("'. $config['checkbox']['confirmTxt'] .'"))' ."\n"
            . "\t\t" .'return true;' ."\n"
            . "\t" .'else' ."\n"
            . "\t\t" .'return false;' ."\n"
            . '});' ."\n",
            'jqueryDomReady'
        );

        unset($config['checkbox']['confirmTxt']);
    }

    unset($config['limit']);

    // Return list generated
    return applyTemplate('nkList', $config);
}

?>