<?php

/*
 * Manage database table update
 */
class updateDbTable {

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
     * List of update action list
     */
    private $_updateList = array();

    /*
     * List of external vars used in callback function for modify row in database table
     */
    private $_callbackFunctionVars = array();

    /*
     * Constructor.
     * - Reset session data if needed
     * - Set database table name
     * - Read info of database table
     */
    public function __construct($table) {
        if (isset($_SESSION['currentTable']) && $_SESSION['currentTable'] != $table)
            $this->_resetUpdateData();

        $this->_table = $_SESSION['currentTable'] = $table;
    }

    /*
     * Read fields info of database table
     */
    private function _readTableInfo() {
        $sql = 'SHOW COLUMNS
            FROM `'. $this->_table .'`';

        $dbsColumnsTable = mysql_query($sql) or die(__METHOD__ .' : '. mysql_error() .'<br />'. $sql);

        while ($row = mysql_fetch_assoc($dbsColumnsTable))
            $this->_setFieldTableInfo($row['Field'], $row);
    }

    /*
     * Set fields info of database table
     */
    private function _setFieldTableInfo($field, $row) {
        $this->_tableInfo[$field] = array(
            'type'      => $row['Type'],
            'null'      => $row['Null'],
            'key'       => $row['Key'],
            'default'   => $row['Default'],
            'extra'     => $row['Extra']
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

        die(__METHOD__ .' : Field don\'t exist ('. $field .')');
    }

    public function renameTable($newTable) {
        $sql = 'ALTER TABLE `'. $this->_table .'` RENAME AS `'. $newTable .'`';
        mysql_query($sql) or die(mysql_error());

        $this->_updateList[] = 'RENAME_TABLE '. $newTable;
        $this->_table = $_SESSION['currentTable'] = $newTable;
    }

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
            die(__METHOD__ .' : Field ('. $field .') type no found');

        if ($after != '') {
            if (! in_array($after, $this->_newFieldCreate) && ! $this->fieldExist($after))
                die(__METHOD__ .' : Field don\'t exist ('. $after .')');
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
        $this->_updateList[]        = 'ADD_FIELD '. $field;
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
            die(__METHOD__ .' : Field don\'t exist ('. $field .')');

        if (! isset($data['type']))
            die(__METHOD__ .' : Field ('. $field .') type no found');

        $sql = 'MODIFY `'. $field .'` '. $data['type'];

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

        //$data['key']
        //$data['extra']

        $this->_alterTable[] = $sql;
        $this->_updateList[] = 'MODIFY_FIELD '. $field;

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
            die(__METHOD__ .' : Field don\'t exist ('. $field .')');

        $this->_alterTable[]    = 'DROP `'. $field .'`';
        $this->_updateList[]    = 'DROP_FIELD '. $field;
        $this->_dropTableInfo[] = $field;

        return $this;
    }

    /*
     * Apply modification to database table
     */
    public function modifyTable() {
        if (! empty($this->_alterTable)) {
            $sql = 'ALTER TABLE `'. $this->_table .'` '
                . implode(', ', $this->_alterTable);

            mysql_query($sql) or die(mysql_error());
            $this->_tableInfo = array_merge($this->_tableInfo, $this->_updateTableInfo);

            foreach ($this->_dropTableInfo as $dropField)
                unset($this->_tableInfo[$dropField]);
        }

        $this->_alterTable = array();
    }

    /*
     * Set update action and field used in SELECT query
     */
    public function updateFieldData($update, $field) {
        $this->_updateList[] = $update;

        if (is_string($field))
            $this->_selectFields[] = $field;
        else if (is_array($field))
            $this->_selectFields = array_merge($this->_selectFields, $field);

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
        $dbsTable = mysql_query($sql) or die(mysql_error());
        $row = mysql_fetch_assoc($dbsTable);

        return $row['recordcount'];
    }

    /*
     * Excecute update action in database table.
     */
    public function execute($fieldId = 'id', $callbackUpdateFunction) {
        if (! empty($this->_updateList)) {
            if (! function_exists($callbackUpdateFunction))
                die(__METHOD__ .' : Callback update function don\'t exist ('. $callbackUpdateFunction .')');

            if (! isset($_SESSION['nbTableEntries']))
                $_SESSION['nbTableEntries'] = $this->_countTableEntries();

            $this->_selectFields[] = $fieldId;

            $sql = 'SELECT '. implode(', ', array_unique($this->_selectFields)) .'
                FROM `'. $this->_table .'`';

            if ($_SESSION['nbTableEntries'] > self::NB_ENTRIES_BY_STEP) {
                if (! isset($_SESSION['offset'])) 
                    $_SESSION['offset'] = 0;
                else
                    $_SESSION['offset'] = $_SESSION['offset'] + self::NB_ENTRIES_BY_STEP;

                $sql .= ' LIMIT '. self::NB_ENTRIES_BY_STEP .' OFFSET '. $_SESSION['offset'];
            }

            $dbsTable = mysql_query($sql) or die(mysql_error());

            while ($row = mysql_fetch_assoc($dbsTable)) {
                $setFields = $callbackUpdateFunction($this->_updateList, $row, $this->_callbackFunctionVars);

                $data = array();

                foreach ($setFields as $key => $value)
                    $data[] = $key .' = \''. mysql_real_escape_string($value) .'\'';

                $sql = 'UPDATE `'. $this->_table .'`
                    SET '. implode(', ', $data) .'
                    WHERE '. $fieldId .' = \''. $row[$fieldId] .'\'';
                mysql_query($sql) or die(mysql_error());
            }

            if (isset($_SESSION['offset']) && $_SESSION['offset'] + self::NB_ENTRIES_BY_STEP < $_SESSION['nbTableEntries']) {
                return 'STEP_'. ($_SESSION['offset'] + self::NB_ENTRIES_BY_STEP) .'_TOTAL_STEP_'. $_SESSION['nbTableEntries'];
            }
            else {
                $this->_resetUpdateData();

                return 'UPDATED';
            }
        }

        return 'NOTHING_TO_DO';
    }

    /*
     * Reset session of database table update
     */
    private function _resetUpdateData() {
        unset($_SESSION['nbTableEntries']);
        unset($_SESSION['offset']);
    }

    /*
     * Return result if database table is updated or not
     */
    public function isUpdated() {
        return ! empty($this->_updateList);
    }

}

?>