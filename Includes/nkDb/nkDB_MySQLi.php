<?php
/**
 * nkDB_MySQLi.php
 *
 *
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * A global Array with info about database layer and querys
 */
$GLOBALS['nkDB'] = array(
    'params'            => array(),
    'database'          => 'MySQLi',
    'querys'            => array(),
    'connectionError'   => false,
    'queryError'        => false,
    'selects'           => array(),
    'latestRessource'   => false,
    'connection'        => false,
    'status'            => array(),
    'totalTime'         => 0
);


/**
 * Initialize nkDb layer
 *
 * @param array $data : An associative array with connection parameters
 * @return void
 */
function nkDB_init($data) {
    $GLOBALS['nkDB']['params'] = $data;
}

/**
 * Connection to database
 *
 * @param void
 * @return bool : Status of MySQLi database connection
 */
function nkDB_connect() {
    if ($GLOBALS['nkDB']['connectionError'])
        return false;

    // replace localhost by 127.0.0.1

    // Open a persistency or normal connection to a MySQLi Server
    if (isset($GLOBALS['nkDB']['params']['persistency']) && $GLOBALS['nkDB']['params']['persistency'])
        $host = 'p:'. $GLOBALS['nkDB']['params']['db_host'];
    else
        $host = $GLOBALS['nkDB']['params']['db_host'];

    $GLOBALS['nkDB']['connection'] = mysqli_connect(
        $host,
        $GLOBALS['nkDB']['params']['db_user'],
        $GLOBALS['nkDB']['params']['db_pass'],
        $GLOBALS['nkDB']['params']['db_name']
        // port 3306
    );

    // If the connection isn't etablished, add error and stop connect process.
    if ($GLOBALS['nkDB']['connection'] === false) {
        $GLOBALS['nkDB']['connectionError'] = true;

        return false;
    }

    nkDB_execute('SET NAMES \'latin1\'');

    return $GLOBALS['nkDB']['connection'];
}

/**
 * Get error of MySQLi database connection
 *
 * @param void
 * @return string : The language define used for error of MySQLi database connection
 * /
function nkDB_getConnectError() {
    $mysqlErrno = mysqli_errno();

    if ($mysqlErrno == 2002)
        return 'DB_HOST_ERROR';
    else if (in_array($mysqlErrno, array(1044, 1045)))
        return 'DB_LOGIN_ERROR';
    else if ($mysqlErrno == 1049)
        return 'DB_NAME_ERROR';
    else if ($mysqlErrno == 2019)
        return 'DB_CHARSET_ERROR';
    else
        return mysqli_error();
}*/

/**
 * Disconnect to MySQLi database
 *
 * @param void
 * @return void
 */
function nkDB_disconnect() {
    if ($GLOBALS['nkDB']['connection'])
        mysqli_close($GLOBALS['nkDB']['connection']);
}

/**
 * Show version of MySQLi  server
 *
 * @param void
 * @return string : Version of MySQLi server
 */
function nkDB_show_version() {
    if ($GLOBALS['nkDB']['connectionError'])
        return '';

    return mysqli_get_server_info();
}

/**
 * A simple layer to handle select querys.
 * Return only one row of result
 *
 * @param string $query : The SQL part, should be database independant
 * @param mixed $order : Array of sorting field
 * @param string $dir : Sorting direction
 * @param int $limit : Maximum result to fetch
 * @param int offset : Offset to start at (in case of use of the limit parameter)
 * @return array : Associative array of result row
 */
function nkDB_selectOne($query, $order = false, $dir = 'ASC', $limit = false, $offset = 0) {
    $result = array();

    // Build the query
    $sql = nkDB_formatSelectQuery($query, $order, $dir, $limit, $offset);

    $GLOBALS['nkDB']['selects'][] = $query;

    // Execute the query
    $ressource = nkDB_execute($sql);

    if ($ressource)
        $result = mysqli_fetch_assoc($ressource);

    return $result;
}

/**
 * A simple layer to handle select querys.
 * Return many row of result
 *
 * @param string $query : The SQL part, should be database independant
 * @param mixed $order : Array of sorting field
 * @param string $dir : Sorting direction
 * @param int $limit : Maximum result to fetch
 * @param int offset : Offset to start at (in case of use of the limit parameter)
 * @return array : Numeric indexed array of rows
 */
function nkDB_selectMany($query, $order = false, $dir = 'ASC', $limit = false, $offset = 0) {
    $result = array();

    // Build the query
    $sql = nkDB_formatSelectQuery($query, $order, $dir, $limit, $offset);

    $GLOBALS['nkDB']['selects'][] = $query;

    // Execute the query
    $ressource = nkDB_execute($sql);

    if ($ressource) {
        // Build the numeric indexed array of rows of query data
        while ($data = mysqli_fetch_assoc($ressource))
            $result[] = $data;
    }

    return $result;
}

/**
 * Format SQL string for select querys
 *
 * @param string $query : The SQL part, should be database independant
 * @param mixed $order : Array of sorting field
 * @param string $dir : Sorting direction
 * @param int $limit : Maximum result to fetch
 * @param int offset : Offset to start at (in case of use of the limit parameter)
 * @return string : The SQL string for select query.
 */
function nkDB_formatSelectQuery($query, $order, $dir, $limit, $offset) {
    $sql = $query;

    // Sort field order by mutiple directions
    if (is_array($order)) {
        $nbOrder = count($order);

        $sql .= ' ORDER BY ';

        if (is_array($dir)) {
            for ($i = 0; $i < $nbOrder; $i++) {
                if (! array_key_exists($i, $dir)) $dir[$i] = 'ASC';

                if ($i > 0) $sql .= ', ';

                $sql .= $order[$i] .' '. $dir[$i];
            }
        }
        else {
            for ($i = 0; $i < $nbOrder; $i++) {
                if ($i > 0) $sql .= ', ';

                $sql .= $order[$i] .' '. $dir;
            }
        }
    }

    if ($limit)
        $sql .= ' LIMIT '. $limit .' OFFSET '. $offset;

    return $sql;
}

/**
 * Get the row_count for a query.
 * By default, the latest select query is used
 *
 * @param mixed $ressource : The MySQLi Ressource pointer returned by a query. If false, the latest ressource is used
 *        So you don't need to specify this parameter if used immediatly after the select query
 * @return int : Number of rows returned by the query
 */
function nkDB_numRows($ressource = false) {
    if ($GLOBALS['nkDB']['connectionError'])
        return false;

    if (! $ressource)
        $ressource = $GLOBALS['nkDB']['latestRessource'];

    return (int) mysqli_num_rows($ressource);
}

/**
 * Get the total row_count for a select query.
 * By default, the latest select query is used
 * Note : The SELECT clause is not need if query is defined
 *
 * @param mixed $query : The MySQLi query. If false, the latest ressource is used
 *        So you don't need to specify this parameter if used immediatly after the select query
 * @return int : Total of rows returned by the query
 */
function nkDB_totalNumRows($query = false) {
    if (! $query)
        $query = $GLOBALS['nkDB']['selects'][count($GLOBALS['nkDB']['selects']) - 1];

    // TODO : Remove inner & outer join
    $fromOffset = strpos($query, 'FROM ');

    if ($fromOffset === false)
        return false;
    else if ($fromOffset === 0)
        $sql = 'SELECT COUNT(*) AS `recordcount` '. $query;
    else
        $sql = 'SELECT COUNT(*) AS `recordcount` '. substr($query, $fromOffset, (strlen($query) - $fromOffset));

    $query_data = nkDB_selectOne($sql);

    return (int) $query_data['recordcount'];
}

/**
 * A simple layer to handle insert querys
 *
 * @param string $table : Table name
 * @param array $data : An associative array with :
 *        - Keys : List of fields to insert
 *        - Values : List of values to insert
 *        Values are automaticly escaped
 *        You may disable escaping by placing value in a sub-array
 *        ex. :
 *          $data = array(
 *              'field_foo' => 'foo',                               // values will be escaped
 *              'field_bar' => array('field_bar + 1', 'no-escape')  // Second value won't be escaped
 *          )
 * @return bool : The result of insert query
 */
function nkDB_insert($table, $data) {
    $values = array_values($data);

    // Prepares data to insert
    foreach ($values as $i => $value) {
        if (is_array( $values[$i]) && count($values[$i]) > 1) {
            if ($values[$i][1] == 'no-escape')
                $values[$i] = $values[$i][0];
        }
        else {
            $values[$i] = nkDB_quote($values[$i]);
        }
    }

    // Build the query
    $sql = 'INSERT INTO `'. $table .'` (`'. implode('`, `', array_keys($data)) .'`) VALUES ('. implode(', ', $values) .')';

    return nkDB_execute($sql);
}

/**
 * Get last inserted id
 *
 * @param void
 * @return mixed : The value of auto-increment field if the query was successful, else returns false
 */
function nkDB_insertId() {
    if ($GLOBALS['nkDB']['latestRessource'])
        return mysqli_insert_id($GLOBALS['nkDB']['connection']);
    else
        return false;
}

/**
 * A simple layer to handle replace querys
 *
 * @param string $table : Table name
 * @param array $data : An associative array with :
 *        - Keys : List of fields to replace
 *        - Values : List of values to replace
 *        Values are automaticly escaped
 *        You may disable escaping by placing value in a sub-array
 *        ex. :
 *          $data = array(
 *              'field_foo' => 'foo',                               // values will be escaped
 *              'field_bar' => array('field_bar + 1', 'no-escape')  // Second value won't be escaped
 *          )
 * @return bool : The result of replace query
 */
function nkDB_replace($table, $data) {
    $values = array_values($data);

    // Prepares data to replace
    foreach ($values as $i => $value) {
        if (is_array( $values[$i]) && count($values[$i]) > 1) {
            if ($values[$i][1] == 'no-escape')
                $values[$i] = $values[$i][0];
        }
        else {
            $values[$i] = nkDB_quote($values[$i]);
        }
    }

    // Build the query
    $sql = 'REPLACE `'. $table .'` (`'. implode('`, `', array_keys($data)) .'`) VALUES ('. implode(', ', $values) .')';

    return nkDB_execute($sql);
}

/**
 * A simple layer to handle update querys
 *
 * @param string $table : Table name
 * @param array $data : An associative array with :
 *        - Keys : List of fields to insert
 *        - Values : List of values to insert
 *        Values are automaticly escaped
 *        You may disable escaping by placing value in a sub-array
 *        ex. :
 *          $data = array(
 *              'field_foo' => 'foo',                               // values will be escaped
 *              'field_bar' => array('field_bar + 1', 'no-escape')  // Second value won't be escaped
 *          )
 * @param string $where : SQL part to identify the row to update (ie. "id = 56")
 * @return bool : The result of insert query
 */
function nkDB_update($table, $data, $where) {
    $fields         = array_keys($data);
    $values         = array_values($data);
    $separator      = '';
    $fieldsLength   = count($fields) - 1;

    // Build the query
    $sql = 'UPDATE `'. $table .'` SET ';

    for ($i = 0; $i <= $fieldsLength; $i++) {
        $sql .= $separator .'`'. $fields[$i] .'` = ';

        if (is_array($values[$i]) && count($values[$i]) > 1) {
            if ($values[$i][1] == 'no-escape')
                $sql .= $values[$i][0];
        }
        else {
            $sql .= nkDB_quote($values[$i]);
        }

        $separator = ', ';
    }

    $sql .= ' WHERE '. $where;

    return nkDB_execute($sql);
}

/**
 * Get the number of affected rows by the last INSERT, UPDATE, REPLACE or DELETE query
 *
 * @param void
 * @return int : The number of affected rows if the query was successful, returns -1 if the last query failed.
 */
function nkDB_affectedRows() {
    if ($GLOBALS['nkDB']['latestRessource'])
        return mysqli_affected_rows($GLOBALS['nkDB']['connection']);
    else
        return false;
}

/**
 * A simple layer to handle delete querys
 *
 * @param string $table : Table name
 * @param string $where : SQL part to identify the row to delete (ie. "id = 56")
 *        if this parameter isn't defined, this requete delete all row in the table
 * @return mixed : The result of nkDB_execute call
 */
function nkDB_delete($table, $where = 'all') {
    $where = ($where != 'all') ? ' WHERE '. $where : '';

    return nkDB_execute('DELETE FROM `'. $table .'` '. $where);
}

/**
 * Exec querys...
 *
 * @param string $sql : The SQL query to execute
 * @return bool : The result of mysqli_query call
 */
function nkDB_execute($sql) {
    if ($GLOBALS['nkDB']['connectionError'])
        return false;

    $GLOBALS['nkDB']['queryError'] = false;

    // Save query for debug propose
    $GLOBALS['nkDB']['querys'][] = $sql;

    $sqlStart   = microtime(true);
    $ressource  = mysqli_query($GLOBALS['nkDB']['connection'], $sql);
    $sqlTime    = microtime(true) - $sqlStart;

    if ($ressource !== false) {
        $GLOBALS['nkDB']['status'][count($GLOBALS['nkDB']['querys']) - 1] = array($sql, 'ok', $sqlTime);
    }
    else {
        $error = mysqli_error($GLOBALS['nkDB']['connection']);
        $GLOBALS['nkDB']['status'][count($GLOBALS['nkDB']['querys']) - 1] = array($sql, $error, 0);
        $GLOBALS['nkDB']['queryError'] = true;

        $errline = 0;
        $errfile = '';

        foreach (debug_backtrace(false) as $k => $backtrace) {
            if (strpos($backtrace['function'], 'nkDB_') !== 0)
                break;

            $errfile = $backtrace['file'];
            $errline = $backtrace['line'];
        }

        nkDB_insert(SQL_ERROR_TABLE, array(
            'date'  => time(),
            'url'   => basename($_SERVER['REQUEST_URI']),
            'code'  => mysqli_errno(),
            'line'  => $errline,
            'file'  => $errfile,
            'error' => $error
        ));

        saveNotification(
            __('SQL_ERROR_DETECTED') .' : [<a href="index.php?file=Admin&page=erreursql">'. __('LINK') .'</a>].',
            NOTIFICATION_WARNING
        );
    }

    $GLOBALS['nkDB']['totalTime'] += $sqlTime;

    // Save result ressource in order to perform a row count via mysqli_num_rows
    $GLOBALS['nkDB']['latestRessource'] = $ressource;

    return $ressource;
}

/**
 * Return last execute query status
 *
 * @param void
 * @return bool : Return false if last execute query has error or true if nothing appears
 */
function nkDB_queryError() {
    return $GLOBALS['nkDB']['queryError'];
}

/**
 * Escape a string for insertion into a text field
 *
 * @param string $value : String to ptotect
 * @param bool $noQuote : If value is enclosed into single quote
 * @return string : The escaped string
 */
function nkDB_quote($value, $noQuote = false) {
    if ($GLOBALS['nkDB']['connectionError'])
        return '';

    if (is_array($value)) {
        foreach ($value as $key => $val)
            $value[$key] = nkDB_quote($val);
    }
    else {
        $value = mysqli_real_escape_string($GLOBALS['nkDB']['connection'], $value);
        $value = str_replace('`', '\`', $value);
    }

    if ($noQuote)
        return $value;
    else
        return '\''. $value .'\'';
}

/**
 * Get time for execute all querys
 *
 * @param void
 * @return int : Time for execute all querys (in ms)
 */
function nkDB_getTimeForExecuteAllQuery() {
    return round($GLOBALS['nkDB']['totalTime'] * 1000, 1);
}

/**
 * Get number of executed querys and return result
 *
 * @param void
 * @return int : Number of executed querys
 */
function nkDB_getNbExecutedQuery() {
    return count($GLOBALS['nkDB']['querys']);
}

/* Substitute function of mysql librairy */

function nkDB_fetchArray($ressource, $resultType = MYSQLI_BOTH) {
    return mysqli_fetch_array($ressource, $resultType);
}

function nkDB_fetchAssoc($ressource) {
    return mysqli_fetch_assoc($ressource);
}

function nkDB_fetchRow($ressource) {
    return mysqli_fetch_row($ressource);
}

function nkDB_dataSeek($ressource, $rowNumber) {
    return mysqli_data_seek($ressource, $rowNumber);
}

function nkDB_realEscapeString($str, $linkIdentifier = NULL) {
    if ($GLOBALS['nkDB']['connectionError'])
        return '';

    return mysqli_real_escape_string($GLOBALS['nkDB']['connection'], $str);
}

// mysqli_free_result($result);

?>
