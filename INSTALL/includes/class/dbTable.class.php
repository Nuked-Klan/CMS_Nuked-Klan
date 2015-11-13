<?php
/**
 * dbTable.class.php
 *
 * Manage database table for install / update process
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

class dbTable {

    /*
     * Maximal recording by step
     */
    const NB_ENTRIES_BY_STEP = 400;

    /*
     * Set table name
     */
    private $_table;

    /*
     * Set fields info of database table
     */
    private $_tableInfo = array();

    /*
     * Set fields info to update after alter database table
     */
    private $_updateTableInfo = array();

    /*
     * Set fields info to drop after alter database table
     */
    private $_dropTableInfo = array();

    /*
     * List of modification list of database table
     */
    private $_alterTable = array();

    /*
     * List of new field create after alter table
     */
    private $_newFieldCreate = array();

    /*
     * List of field list for retrieve data in database table
     */
    private $_selectFields = array();

    /*
     * List of process action list
     */
    private $_actionList = array();

    /*
     * List of update field action list
     */
    private $_updateFieldsList = array();

    /*
     * List of external vars used in callback function for modify row in database table
     */
    private $_callbackFunctionVars = array();

    /*
     * Set the response code for jQuery ajax request
     */
    private $_jqueryAjaxResponse = 'NOTHING_TO_DO';

    /*
     * Store PHPsession instance
     */
    private $_session;

    /*
     * Store db instance
     */
    private $_db;

    /*
     * Store i18n instance
     */
    private $_i18n;

    /*
     * Constructor.
     * - Reset session data if needed
     * - Set database table name
     * - Read info of database table
     */
    public function __construct($db, $session, $i18n) {
        $this->_db      = $db;
        $this->_session = $session;
        $this->_i18n    = $i18n;
    }

    /*
     * Initialize session data of dbTable class
     */
    private function _init() {
        if (isset($this->_session['offset']))
            unset($this->_session['offset']);

        if (isset($this->_session['nbTableEntries']))
            unset($this->_session['nbTableEntries']);
    }

    /*
     * Set current table of database used
     */
    public function setTable($table) {
        if (isset($this->_session['currentTable']) && $this->_session['currentTable'] != $table)
            $this->_init();

        $this->_table = $this->_session['currentTable'] = $table;
    }

    /*
     * Return current table of database used
     */
    public function getTableName() {
        return $this->_table;
    }

    /*
     * Read fields info of database table
     */
    private function _readTableInfo() {
        foreach ($this->_db->getTableInfo($this->_table) as $tableInfo)
            $this->_setFieldTableInfo($tableInfo['Field'], $tableInfo);
    }

    /*
     * Set fields info of database table
     */
    private function _setFieldTableInfo($field, $tableInfo) {
        $this->_tableInfo[$field] = array(
            'type'      => $tableInfo['Type'],
            'null'      => $tableInfo['Null'],
            'key'       => $tableInfo['Key'],
            'default'   => $tableInfo['Default'],
            'extra'     => $tableInfo['Extra']
        );
    }

    /*
     * Return field type of database table
     */
    public function getFieldType($field) {
        if (empty($this->_tableInfo))
            $this->_readTableInfo();

        if (array_key_exists($field, $this->_tableInfo))
            return $this->_tableInfo[$field]['type'];

        throw new dbTableException(sprintf($this->_i18n['FIELD_DONT_EXIST'], $field));
    }

    /*
     * Check integrity of database table
     * - Check if database table exist
     * - Check if fields exist
     *   Fields is method argument list. Use array for multiple field name and
     *   null when field don't exist
     */
    public function checkIntegrity() {
        if (! $this->tableExist()) {
            $this->_actionList[] = sprintf($this->_i18n['MISSING_TABLE'], $this->_table);
        }
        else {
            foreach (func_get_args() as $field) {
                // Multiple field name (rename)
                if (is_array($field)) {
                    $check = false;

                    foreach ($field as $_field) {
                        if ($_field !== null && $this->fieldExist($_field))
                            $check = $check || true;
                    }

                    // unexisting field is null
                    if (in_array(null, $field))
                        $check = $check || true;

                    if (! $check)
                        $this->_actionList[] = sprintf($this->_i18n['MISSING_FIELD'], $this->_table);
                }
                else {
                    if (! $this->fieldExist($field))
                        $this->_actionList[] = sprintf($this->_i18n['MISSING_FIELD'], $this->_table);
                }
            }
        }

        if (empty($this->_actionList))
            $this->_jqueryAjaxResponse = 'INTEGRITY_ACCEPTED';
        else
            $this->_jqueryAjaxResponse = 'INTEGRITY_FAIL';
    }

    /*
     * Check and convert charset and collation of database table
     */
    public function checkAndConvertCharsetAndCollation() {
        if (! $this->tableExist()) return;

        $dbTableData = $this->_db->getTableCharsetAndCollation($this->_table);

        if ($dbTableData['charset'] != db::CHARSET
            || $dbTableData['collation'] != db::COLLATION
        ) {
            $this->_db->convertTableCharsetAndCollation($this->_table);

            $this->_actionList[]        = sprintf($this->_i18n['CONVERT_CHARSET_AND_COLLATION'], $this->_table, db::CHARSET, db::COLLATION);
            $this->_jqueryAjaxResponse  = 'TABLE_CONVERTED';
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Manage database table
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Check if table exist in database
     */
    public function tableExist($table = '') {
        if ($table == '')
            $table = $this->_table;

        return $this->_db->tableExist($table);
    }

    /*
     * Create a database table
     */
    public function createTable($sql) {
        $this->_db->execute($sql);

        $this->_actionList[]        = sprintf($this->_i18n['CREATE_TABLE'], $this->_table);
        $this->_jqueryAjaxResponse  = 'CREATED';

        return $this;
    }

    /*
     * Rename a database table
     */
    public function renameTable($newTable) {
        // mysql - postgresql
        $sql = 'ALTER TABLE `'. $this->_table .'` RENAME TO `'. $newTable .'`';

        $this->_db->execute($sql);

        $this->_actionList[]    = sprintf($this->_i18n['RENAME_TABLE'], $this->_table, $newTable);
        $this->_table           = $this->_session['currentTable'] = $newTable;

        return $this;
    }

    /*
     * Drop a database table
     */
    public function dropTable($table = '') {
        if ($table == '')
            $table = $this->_table;

        // mysql - postgresql
        $sql = 'DROP TABLE IF EXISTS `'. $table .'`';

        $this->_db->execute($sql);

        $this->_actionList[] = sprintf($this->_i18n['DROP_TABLE'], $table);

        return $this;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Manage field of database table
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Check if field exist in database table
     */
    public function fieldExist($field) {
        if (empty($this->_tableInfo))
            $this->_readTableInfo();

        return array_key_exists($field, $this->_tableInfo);
    }

    /*
     * Prepare data to add a field in database table
     */
    public function addField($field, $data = array(), $after = '') {
        if (! isset($data['type']))
            throw new dbTableException(sprintf($this->_i18n['FIELD_TYPE_NO_FOUND'], $field));

        if ($after != '') {
            if (! in_array($after, $this->_newFieldCreate) && ! $this->fieldExist($after))
                throw new dbTableException(sprintf($this->_i18n['FIELD_DONT_EXIST'], $after));
        }

        $sql = 'ADD `'. $field .'` '. $data['type'];

        $null = 'YES';

        if (isset($data['null'])) {
            if ($data['null']) {
                $sql .= ' NULL';
            }
            else {
                $sql .= ' NOT NULL';
                $null = 'NO';
            }
        }

        if (isset($data['default']) && $data['default'] != '')
            $sql .= ' DEFAULT '. $data['default'];

        // PRI MUL
        //$data['key']

        // auto_increment
        //$data['extra']

        if ($after != '')
            $sql .= ' AFTER `'. $after .'`';

        $this->_alterTable[]        = $sql;
        $this->_actionList[]        = sprintf($this->_i18n['ADD_FIELD'], $field, $this->_table);
        $this->_newFieldCreate[]    = $field;

        $this->_updateTableInfo[$field] = array(
            'type'      => $data['type'],
            'null'      => $null,
            'key'       => (isset($data['Key'])) ? $data['Key'] : '',
            'default'   => (isset($data['Default'])) ? $data['Default'] : '',
            'extra'     => (isset($data['Extra'])) ? $data['Extra'] : ''
        );

        return $this;
    }

    /*
     * Prepare data to modify a field in database table
     */
    public function modifyField($field, $data = array()) {
        if (! $this->fieldExist($field))
            throw new dbTableException(sprintf($this->_i18n['FIELD_DONT_EXIST'], $field));

        if (! isset($data['type']))
            throw new dbTableException(sprintf($this->_i18n['FIELD_TYPE_NO_FOUND'], $field));

        $sql = 'CHANGE `'. $field .'` `'. $field .'` '. $data['type'];

        $null = 'YES';

        if (isset($data['null'])) {
            if ($data['null']) {
                $sql .= ' NULL';
            }
            else {
                $sql .= ' NOT NULL';
                $null = 'NO';
            }
        }

        if (isset($data['default']) && $data['default'] != '')
            $sql .= ' DEFAULT '. $data['default'];

        // PRI MUL
        //$data['key']

        // auto_increment
        //$data['extra']

        $this->_alterTable[] = $sql;
        $this->_actionList[] = sprintf($this->_i18n['MODIFY_FIELD'], $field, $this->_table);

        $this->_updateTableInfo[$field] = array(
            'type'      => $data['type'],
            'null'      => $null,
            'key'       => (isset($data['Key'])) ? $data['Key'] : '',
            'default'   => (isset($data['Default'])) ? $data['Default'] : '',
            'extra'     => (isset($data['Extra'])) ? $data['Extra'] : ''
        );

        return $this;
    }

    /*
     * Prepare data to delete a field in database table
     */
    public function dropField($field) {
        if (! $this->fieldExist($field))
            throw new dbTableException(sprintf($this->_i18n['FIELD_DONT_EXIST'], $field));

        $this->_alterTable[]    = 'DROP `'. $field .'`';
        $this->_actionList[]    = sprintf($this->_i18n['DROP_FIELD'], $field, $this->_table);
        $this->_dropTableInfo[] = $field;

        return $this;
    }

    /*
     * Apply modification to database table
     */
    public function alterTable() {
        if (! empty($this->_alterTable)) {
            $sql = 'ALTER TABLE `'. $this->_table .'` '
                . implode(', ', $this->_alterTable);

            $this->_db->execute($sql);

            $this->_tableInfo = array_merge($this->_tableInfo, $this->_updateTableInfo);

            foreach ($this->_dropTableInfo as $dropField)
                unset($this->_tableInfo[$dropField]);

            $this->_alterTable          = array();
            $this->_jqueryAjaxResponse  = 'UPDATED';
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Manage field data of database table
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Insert data in current database table
     */
    public function insertData($type, $sql) {
        $this->_db->execute($sql);

        if (is_string($type))
            $this->_actionList[] = sprintf($this->_i18n[$type], $this->_table);
        else if (is_array($type))
            $this->_actionList[] = vsprintf($this->_i18n[array_shift($type)], array_merge($type, array($this->_table)));
    }

    /*
     * Update data in current database table
     */
    public function updateData($type, $sql) {
        $this->_db->execute($sql);

        if (is_string($type))
            $this->_actionList[] = sprintf($this->_i18n[$type], $this->_table);
        else if (is_array($type))
            $this->_actionList[] = vsprintf($this->_i18n[array_shift($type)], array_merge($type, array($this->_table)));
    }

    /*
     * Delete data in current database table
     */
    public function deleteData($type, $sql) {
        $this->_db->execute($sql);

        if (is_string($type))
            $this->_actionList[] = sprintf($this->_i18n[$type], $this->_table);
        else if (is_array($type))
            $this->_actionList[] = vsprintf($this->_i18n[array_shift($type)], array_merge($type, array($this->_table)));
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Manage field data of database table (by list)
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Set update action and field used in SELECT query
     */
    public function setUpdateFieldData($update, $field) {
        $this->_updateFieldsList[] = $update;

        $updateData = explode(' ', $update);
        $action     = array_shift($updateData);

        if (is_string($field)) {
            $this->_selectFields[] = $field;
            $this->_actionList[] = sprintf($this->_i18n[$action], $field, $this->_table);
        }
        else if (is_array($field)) {
            $this->_selectFields = array_merge($this->_selectFields, $field);
            $this->_actionList[] = sprintf($this->_i18n[$action], implode(', ', $field), $this->_table);
        }

        return $this;
    }

    /*
     * Set external vars used in callback function for modify row in database table
     */
    public function setCallbackFunctionVars($vars) {
        $this->_callbackFunctionVars = array_merge($this->_callbackFunctionVars, $vars);

        return $this;
    }

    /*
     * Count all row in database table
     */
    private function _countTableEntries() {
        $sql = 'SELECT COUNT(*) AS `recordcount`
            FROM `'. $this->_table .'`';

        $dbsTable = $this->_db->selectOne($sql);

        return $dbsTable['recordcount'];
    }

    /*
     * Excecute update action in database table.
     */
    public function applyUpdateFieldListToData($fieldId = 'id', $callbackUpdateFunction) {
        if (! empty($this->_updateFieldsList)) {
            if (! function_exists($callbackUpdateFunction))
                throw new dbTableException(sprintf($this->_i18n['CALLBACK_UPDATE_FUNCTION_DONT_EXIST'], $callbackUpdateFunction));

            if (! isset($this->_session['nbTableEntries']))
                $this->_session['nbTableEntries'] = $this->_countTableEntries();

            $this->_selectFields[] = $fieldId;

            $sql = 'SELECT '. implode(', ', array_unique($this->_selectFields)) .'
                FROM `'. $this->_table .'`';

            if ($this->_session['nbTableEntries'] > self::NB_ENTRIES_BY_STEP) {
                if (! isset($this->_session['offset'])) 
                    $this->_session['offset'] = 0;
                else
                    $this->_session['offset'] = $this->_session['offset'] + self::NB_ENTRIES_BY_STEP;

                $sql .= ' LIMIT '. self::NB_ENTRIES_BY_STEP .' OFFSET '. $this->_session['offset'];
            }

            $dbsTable = $this->_db->selectMany($sql);

            foreach ($dbsTable as $row) {
                $setFields = $callbackUpdateFunction($this->_updateFieldsList, $row, $this->_callbackFunctionVars);

                $data = array();

                foreach ($setFields as $key => $value)
                    $data[] = $key .' = \''. $this->_db->quote($value) .'\'';

                $sql = 'UPDATE `'. $this->_table .'`
                    SET '. implode(', ', $data) .'
                    WHERE '. $fieldId .' = \''. $row[$fieldId] .'\'';

                $this->_db->execute($sql);
            }

            if (isset($this->_session['offset'])
                && $this->_session['offset'] + self::NB_ENTRIES_BY_STEP < $this->_session['nbTableEntries']
            ) {
                $this->_jqueryAjaxResponse = 'STEP_'. ($this->_session['offset'] + self::NB_ENTRIES_BY_STEP) .'_TOTAL_STEP_'. $this->_session['nbTableEntries'];
            }
            else {
                $this->_jqueryAjaxResponse = 'UPDATED';

                $this->_init();
            }
        }
    }

    /*
     * Return jQuery ajax response to displayed
     */
    public function getJqueryAjaxResponse() {
        if ($this->_jqueryAjaxResponse != 'NOTHING_TO_DO')
            return $this->_jqueryAjaxResponse;

        if (! empty($this->_actionList))
            return 'UPDATED';

        return $this->_jqueryAjaxResponse;
    }

    /*
     * Set jQuery ajax response
     */
    public function setJqueryAjaxResponse($response) {
        $this->_jqueryAjaxResponse = $response;
    }

    /*
     * Return action list
     */
    public function getActionList() {
        return $this->_actionList;
    }

}

?>