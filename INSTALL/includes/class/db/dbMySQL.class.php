<?php
/**
 * dbMySQL.class.php
 *
 * Manage MySQL database
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

// Fetch mode constant
define('DB_ASSOC', 1);
define('DB_NUM', 2);
define('DB_BOTH', 3);

class dbMySQL {

    /*
     * Store MySQL server
     */
    private $_host = '';

    /*
     * Store MySQL username
     */
    private $_user = '';

    /*
     * Store MySQL password
     */
    private $_pass = '';

    /*
     * Store MySQL database name
     */
    private $_dbName = '';

    /*
     * Store MySQL link identifier
     */
    private $_db;

    /*
     * Constructor
     * - Check and store MySQL connection data
     * - Connect to MySQL server
     */
    public function __construct($databaseData) {
        if (isset($databaseData['db_host']))
            $this->_host = $databaseData['db_host'];

        if (isset($databaseData['db_user']))
            $this->_user = $databaseData['db_user'];

        if (isset($databaseData['db_pass']))
            $this->_pass = $databaseData['db_pass'];

        if (isset($databaseData['db_name']))
            $this->_dbName = $databaseData['db_name'];

        $this->_connect();
    }

    /*
     * Destructor
     * - Disconnect to MySQL server
     */
    public function __destruct() {
        if ($this->_db !== null)
            mysql_close($this->_db);
    }

    /*
     * Connect to MySQL server
     */
    private function _connect() {
        if (($this->_db = @mysql_connect($this->_host, $this->_user, $this->_pass)) !== false) {
            if (@mysql_select_db($this->_dbName, $this->_db)) {
                @mysql_query('SET NAMES '. db::CHARSET);

                // see http://php.net/manual/fr/mysqlinfo.concepts.charset.php
                @mysql_set_charset(db::CHARSET, $this->_db);
            }
        }

        if (($error = mysql_error()) != '')
            throw new dbException($this->_getDbConnectError($error));
    }

    /*
     * Check MySQL database connection error and return i18n key
     */
    public function _getDbConnectError($error) {
        if (strpos($error, 'Unknown MySQL server host') !== false) # 2002
            return 'DB_HOST_ERROR';
        else if (strpos($error, 'Access denied for user') !== false) # 1044 / 1045
            return 'DB_LOGIN_ERROR';
        else if (strpos($error, 'Unknown database') !== false) # 1049
            return 'DB_NAME_ERROR';
        else if (strpos($error, 'Invalid characterset or character') !== false) # 2019
            return 'DB_CHARSET_ERROR';
        else
            return $error;
    }

    /*
     * Return MySQL default port
     */
    public function getDefaultPort() {
        return 3306;
    }

    /*
     * Return MySQL server version
     */
    public function getVersion() {
        return mysql_get_server_info();
    }

    /*
     * Escapes special characters with MySQL function
     */
    public function quote($str) {
        return @mysql_real_escape_string($str);
    }

    /*
     * Protect var for database backup (from phpMyAdmin)
     */
    private function _backupQuote($str) {
        $str = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $str);
        $str = str_replace('\\', '\\\\', $str);

        return str_replace('\'', '\'\'', $str);
    }

    /*
     * Generate backup of MySQL database and send file to web browser
     */
    public function createBackup($type = 'all', $options = array()) {
        $backup = '-- --------------------------------------------------------' ."\n"
            . '-- Dump of Nuked-Klan database' ."\n"
            . '-- Database: `'. $this->_dbName .'`' ."\n"
            . '-- Date: '. strftime('%c') ."\n"
            . '-- --------------------------------------------------------' ."\n\n";

        $tableList  = $this->getTableList();
        $nbTable    = count($tableList);
        $t          = 0;

        foreach ($tableList as $table) {
            $backup .= '--' ."\n"
                . '-- Table structure for table `'. $table .'`' ."\n"
                . '--' ."\n\n"
                . 'DROP TABLE IF EXISTS '. $table .';' ."\n\n"
                . $this->_getTableStructure($table) .';' ."\n\n"
                . '-- --------------------------------------------------------' ."\n\n";

            if ($type == 'all') {
                $tableDataList  = $this->selectMany('SELECT * FROM '. $table, array(DB_NUM));
                $nbData         = count($tableDataList);

                if ($nbData > 0) {
                    $backup .= '--' ."\n"
                        . '-- Dumping data for table `'. $table .'`' ."\n"
                        . '--' ."\n\n";

                    $tableInfo  = $this->getTableInfo($table);
                    $fields     = array();

                    foreach ($tableInfo as $row)
                        $fields[] = $row['Field'];

                    $insertQuery = 'INSERT INTO '. $table .' (`'. implode('`, `', $fields) .'`) VALUES' ."\n";
                    $backup .= $insertQuery;

                    $nbFields = count($fields);
                    $d = $e = 0;

                    foreach ($tableDataList as $tableData) {
                        $backup .= '(';

                        for ($f = 0; $f < $nbFields; $f++) {
                            $tableData[$f] = $this->_backupQuote($tableData[$f]);

                            if (isset($tableData[$f]))
                                $backup .= '\''. $tableData[$f] .'\'' ;
                            else
                                $backup .= '\'\'';

                            if ($f < ($nbFields - 1))
                                $backup .= ', ';
                        }

                        $d++;
                        $e++;

                        if ($d == $nbData) {
                            $backup .= ');' ."\n";
                        }
                        else {
                            if ($e == 20) {
                                $backup .= ');' ."\n";
                                $backup .= $insertQuery;
                                $e = 0;
                            }
                            else {
                                $backup .= '),' ."\n";
                            }
                        }
                    }
                }
            }

            $t++;

            if ($t != $nbTable && $nbData > 0)
                $backup .= "\n" .'-- --------------------------------------------------------' ."\n\n";

        }

        return $backup;
    }

    /*
     * Note : Use options array to set exception message and / or
     *        set used method to fetch data (Ex : DB_ASSOC, DB_NUM or DB_BOTH)
     */

    /*
     * Execute MySQL query
     */
    public function execute($sql, $options = array()) {
        if (($result = @mysql_query($sql)) === false) {
            if (array_key_exists('exception', $options) && $options['exception'] != '')
                throw new dbException($options['exception']);
            else
                throw new dbException(mysql_error());
        }

        return $result;
    }

    /*
     * Return one row from a MySQL query
     */
    public function selectOne($sql, $options = array()) {
        if (($req = $this->execute($sql, $options)) === false)
            return false;

        if (in_array(DB_NUM, $options))
            $mysqlFetchCmd = 'mysql_fetch_row';
        else
            $mysqlFetchCmd = 'mysql_fetch_assoc';

        return $mysqlFetchCmd($req);
    }

    /*
     * Return many row from a MySQL query
     */
    public function selectMany($sql, $options = array()) {
        if (($req = $this->execute($sql, $options)) === false)
            return false;

        $data = array();

        if (in_array(DB_NUM, $options))
            $mysqlFetchCmd = 'mysql_fetch_row';
        else
            $mysqlFetchCmd = 'mysql_fetch_assoc';

        while ($row = $mysqlFetchCmd($req))
            $data[] = $row;

        return $data;
    }

    /*
     * Return table list of database
     */
    public function getTableList($options = array()) {
        if (($req = $this->execute('SHOW TABLES', $options)) === false)
            return false;

        $data = array();

        while ($row = mysql_fetch_row($req))
            $data[] = $row[0];

        return $data;
    }

    /*
     * Return structure used to create MySQL table
     */
    private function _getTableStructure($table, $options = array()) {
        if (($req = $this->execute('SHOW CREATE TABLE '. $table, $options)) === false)
            return false;

        $row = mysql_fetch_row($req);

        return $row[1];
    }

    /*
     * Return info of MySQL table
     */
    public function getTableInfo($table, $options = array()) {
        if (($req = $this->execute('SHOW COLUMNS FROM '. $table, $options)) === false)
            return false;

        $data = array();

        while ($row = mysql_fetch_assoc($req))
            $data[] = $row;

        return $data;
    }

    /*
     * Check if table exist in database and return result
     */
    public function tableExist($table, $options = array()) {
        $sql = 'SHOW TABLES
            FROM `'. $this->_dbName .'`
            LIKE \''. $table .'\'';

        $dbsTable = $this->execute($sql, $options);

        return (mysql_num_rows($dbsTable) == 0) ? false : true;
    }

    /*
     * Get database charset and collation
     */
    public function getDatabaseCharsetAndCollation($options = array()) {
        $sql = 'SELECT default_character_set_name AS charset, default_collation_name AS collation
            FROM information_schema.SCHEMATA
            WHERE schema_name = \''. $this->_dbName .'\'';

        return $this->selectOne($sql, $options);
    }

    /*
     * Set database charset and collation
     */
    public function setDatabaseCharsetAndCollation($options = array()) {
        $sql = 'ALTER DATABASE `'. $this->_dbName .'`
            CHARACTER SET '. db::CHARSET .'
            COLLATE '. db::COLLATION;

        return $this->execute($sql, $options);
    }

    /*
     * Get table charset and collation
     */
    public function getTableCharsetAndCollation($table, $options = array()) {
        $sql = 'SELECT CCSA.character_set_name AS charset, CCSA.collation_name AS collation
            FROM information_schema.`TABLES` T, information_schema.`COLLATION_CHARACTER_SET_APPLICABILITY` CCSA
            WHERE CCSA.collation_name = T.table_collation
            AND T.table_schema = \''. $this->_dbName .'\' AND T.table_name = \''. $table .'\'';

        return $this->selectOne($sql, $options);
    }

    /*
     * Set table charset and collation
     */
    public function convertTableCharsetAndCollation($table, $options = array()) {
        // mysql - postgresql
        $sql = 'ALTER TABLE `'. $table .'`
            CONVERT TO CHARACTER SET '. db::CHARSET .'
            COLLATE '. db::COLLATION;

        return $this->execute($sql, $options);
    }

}

?>