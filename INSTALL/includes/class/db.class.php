<?php

/*
 * Database provider
 */
class db {

    /*
     * Sets used database charset
     */
    const CHARSET = 'latin1';

    /*
     * Sets used database collation
     */
    const COLLATION = 'latin1_general_ci';

    /*
     * Store instance of db class (provider)
     */
    static private $_instance;

    /*
     * Store instance of db class
     */
    private $_db;

    private function __construct() { }

    private function __clone() { }

    /*
     * Singleton mechanism
     */
    public static function getInstance() {
        if (self::$_instance === null)
            self::$_instance = new self;

        return self::$_instance;
    }

    /*
     * Check and return database type instance
     */
    public function load($databaseData = array()) {
        if ($this->_db !== null)
            return $this->_db;

        $databaseType = (isset($databaseData['db_type'])) ? $databaseData['db_type'] : 'MySQL';

        if (! is_file($classFile = 'includes/class/db/db'. $databaseType .'.class.php'))
            throw new dbException(sprintf(i18n::getInstance()['UNKNOW_DATABASE_TYPE'], $databaseType));

        include_once $classFile;

        $dbClass = 'db'. $databaseType;
        $this->_db = new $dbClass($databaseData);

        return $this->_db;
    }

    /*
     * Get list of database type
     */
    static public function getDatabaseTypeList() {
        $result = array();

        foreach (array_diff(scandir('includes/class/db'), array('.', '..')) as $dbFile) {
            $dbFileData = explode('.', $dbFile);

            if (is_file('includes/class/db/'. $dbFile)
                && strpos($dbFileData[0], 'db') === 0
                && array_pop($dbFileData) == 'php'
                && array_pop($dbFileData) == 'class'
            ) {
                $result[] = str_replace('db', '', $dbFileData[0]);
            }
        }

        return $result;
    }

}

?>