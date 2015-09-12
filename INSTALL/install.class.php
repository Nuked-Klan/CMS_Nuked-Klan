<?php

    class install {

        /*
         * Sets Nuked-Klan version to install / update
         */
        const NK_VERSION = '1.7.13';

        /*
         * Sets language list
         */
        private $_languageList = array(
            'english'  => '_ENGLISH',
            'french'   => '_FRENCH'
        );

        /*
         * Sets config database data list
         */
        private $_configDbData = array('db_host', 'db_user', 'db_pass', 'db_name');

        /*
         * Sets navigation list display in left sidebar
         */
        private $_navigation = array(
            'language'      => '_SELECTLANG',
            'process'       => '_SELECTTYPE',
            'stats'         => '_SELECTSTATS',
            'db_save'       => '_SELECTSAVE',
            'assist'        => '_CHECKTYPEINSTALL',
            'user_admin'    => '_CHECKUSERADMIN'
        );

        /*
         * Sets info list display in footer
         */
        private $_infoList = array(
            '_DISCOVERY',
            '_NEWSADMIN',
            '_INSTALL_AND_UPDATE',
            '_COMMUNAUTY_NK'
        );

        /*
         * Sets changelog list
         */
        private $_changelog = array(
            '_SECURITE',
            '_OPTIMISATION',
            '_ADMINISTRATION',
            '_BANTEMP',
            '_SHOUTBOX',
            '_ERRORSQL',
            '_MULTIWARS',
            '_COMSYS',
            '_EDITWYS',
            '_CONT',
            '_ERREURPASS',
            '_DIFFMODIF'
        );

        /*
         * Sets partners key
         */
        const PARTNERS_KEY = 'iS5scBmNTNyE6M07Jna3';

        /*
         * Sets the minimum version of PHP used by this version of Nuked-Klan
         */
        const MINIMAL_PHP_VERSION = '5.1.0';

        /*
         * Sets PHP extension list used by this version of Nuked-Klan
         */
        private $_phpExtension = array(
            'mysql'     => 'required',
            'session'   => 'required',
            //'zip'       => 'required',
            'fileinfo'  => 'optional',
            //'hash'      => 'required',
            'gd'        => 'required'
        );

        /*
         * Sets database list
         */
        private $_databaseTypeList = array(
            'mysql'     => 'MySQL'
        );

        /*
         * Sets current database
         */
        private $_databaseType = 'mysql';

        /*
         * Sets view name
         */
        private $_view;

        /*
         * Sets view data
         */
        private $_viewData = array();

        /*
         * Sets data stored in PHP session
         */
        private $data;

        private $_language = 'english';

        private $_i18n = array();

        /*
         * Constructor
         * - Init PHP session and load data
         * - Load language file
         */
        function __construct() {
            $this->_initSession();
            $this->_loadLanguage();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Core methods, they allow the display of pages and execute action
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////

        /*
         * Select used language while install / update process
         */
        public function selectLanguage() {
            $this->_view = 'selectLanguage';

            $this->_viewData['languageList']    = $this->_languageList;
            $this->_viewData['language']        = $this->_language;
        }

        /*
         * Set used language while install / update process
         */
        public function setLanguage() {
            if (isset($_POST['language']) && array_key_exists($_POST['language'], $this->_languageList)) {
                $_SESSION['language'] = $_POST['language'];

                $this->_redirect('index.php?action=main');
            }

            $this->_redirect('index.php');
        }

        // TODO : Voir pour $versionStatus === -1
        /*
         * Detect process (install / update) and check Nuked-Klan version if already installed
         */
        public function main() {
            $this->_view = 'main';

            if (! is_file('../conf.inc.php')) {
                $this->_viewData['process'] = 'install';
            }
            else {
                define('INDEX_CHECK', 1);

                if (is_file('../Includes/version.php')) {
                    include '../Includes/version.php';
                    $version = $nk_version;
                }
                else {
                    include '../conf.inc.php';
                    $connect = $this->_dbConnect($global['db_host'], $global['db_user'], $global['db_pass'], $global['db_name']);

                    if ($connect == 'OK') {
                        $sql = 'SELECT value FROM `'. $db_prefix .'_config` WHERE name = \'version\'';
                        $req = @mysql_query($sql);

                        if ($req === false)
                            $this->_viewData['error'] = _DB_PREFIX_ERROR;
                        else
                            list($version) = mysql_fetch_array($req);
                    }
                    else {
                        $this->_viewData['error'] = $this->_translateDbConnectError($connect);
                    }
                }

                if (isset($version)) {
                    $_SESSION['version'] = $this->_viewData['version'] = $version;

                    $versionStatus = $this->_validVersion($_SESSION['version']);

                    if ($versionStatus === 0)
                        $this->_viewData['process'] = 'update';
                    elseif ($versionStatus === -1)
                        $this->_viewData['message'] = _BADVERSION;
                    else
                        $this->_viewData['message'] = _LASTVERSIONSET;
                }
            }
        }

        /*
         * Check and save process (install / update)
         */
        public function saveProcess() {
            if (isset($_GET['process']) && in_array($_GET['process'], array('install', 'update'))) {
                $_SESSION['process'] = $_GET['process'];

                $this->_redirect('index.php?action=checkCompatibility');
            }

            $this->_redirect('index.php?action=main');
        }

        /*
         * Check PHP requirements for execute process
         */
        public function checkCompatibility() {
            $this->_view = 'checkCompatibility';

            $this->_viewData['requirements'] = $this->_requirements();
        }

        /*
         * Choose to send stats to Nuked-Klan.org or not
         */
        public function chooseSendStats() {
            if (isset($this->data['stats']) && $this->data['stats'] === false)
                $stats = false;
            else
                $stats = true;

            $this->_view = 'chooseSendStats';

            $this->_viewData['stats'] = $stats;
        }

        /*
         * Set to send stats to Nuked-Klan.org or not
         */
        public function setSendStats() {
            if (isset($_POST['conf_stats']) && $_POST['conf_stats'] == 'on')
                $_SESSION['stats'] = 'yes';
            else
                $_SESSION['stats'] = 'no';

            if ($this->data['process'] == 'update')
                $this->_redirect('index.php?action=selectSaveBdd');
            else
                $this->_redirect('index.php?action=selectProcessType');
        }

        /*
         * Display link to save database
         */
        public function selectSaveBdd() {
            $_SESSION['db_save'] = 'no';

            $this->_view = 'selectSaveBdd';
        }

        /*
         * Generate backup of MySQL database and send file to web browser
         */
        public function createBackupDb() {
            $_SESSION['db_save'] = 'yes';

            header('Content-disposition:filename=save-'. time() .'.sql');
            header('Content-type:application/octetstream');

            include '../conf.inc.php';

            $connect = $this->_dbConnect($global['db_host'], $global['db_user'], $global['db_pass'], $global['db_name']);

            if ($connect != 'OK') {
                echo $connect;
                return;
            }

            echo '-- --------------------------------------------------------', "\n"
               , '-- Dump of Nuked-Klan database', "\n"
               , '-- Database: `', $global['db_name'], '`', "\n"
               , '-- Date: ', strftime('%c'), "\n"
               , '-- --------------------------------------------------------', "\n\n";

            $resultTables   = mysql_query('SHOW TABLES');
            $nbTable        = mysql_num_rows($resultTables);
            $t              = 0;

            while ($table = mysql_fetch_row($resultTables)) {
                $resultCreateTable = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '. $table[0]));

                echo '--', "\n"
                   , '-- Table structure for table `', $table[0], '`', "\n"
                   , '--', "\n\n"
                   , 'DROP TABLE IF EXISTS ', $table[0], ';', "\n\n"
                   , $resultCreateTable[1], ';', "\n\n"
                   , '-- --------------------------------------------------------', "\n\n";

                $resultData = mysql_query('SELECT * FROM '. $table[0]);
                $nbData     = mysql_num_rows($resultData);

                if ($nbData > 0) {
                    echo '--', "\n"
                       , '-- Dumping data for table `', $table[0], '`', "\n"
                       , '--', "\n\n";

                    $resultColumns = mysql_query('SHOW COLUMNS FROM '. $table[0]);
                    $fields = array();

                    while ($row = mysql_fetch_assoc($resultColumns))
                        $fields[] = $row['Field'];

                    echo 'INSERT INTO ', $table[0], ' (`', implode('`, `', $fields), '`) VALUES', "\n";

                    $nbFields = count($fields);
                    $d = 0;

                    for ($i = 0; $i < $nbFields; $i++) {
                        while ($row = mysql_fetch_row($resultData)) {
                            echo '(';

                            for ($j = 0; $j < $nbFields; $j++) {
                                $row[$j] = $this->_sqlAddSlashes($row[$j]);

                                if (isset($row[$j]))
                                    echo '\'', $row[$j], '\'' ;
                                else
                                    echo '\'\'';

                                if ($j < ($nbFields - 1))
                                    echo ', ';
                            }

                            $d++;

                            if ($d == $nbData)
                                echo ');', "\n";
                            else
                                echo '),', "\n";
                        }
                    }
                }

                $t++;

                if ($t != $nbTable && $nbData > 0)
                    echo "\n", '-- --------------------------------------------------------', "\n\n";

            }
        }

        /*
         * Select process type (assisted or not)
         */
        public function selectProcessType() {
            if ($this->data['process'] == 'update' && ! $this->_configIsUpdatable())
                $this->_redirect('index.php?action=saveConfig');

            $this->_view = 'selectProcessType';

            $this->_viewData['process'] = $this->data['process'];
        }

        /*
         * Check and save process type (assisted or not)
         */
        public function saveProcessType() {
            if (isset($_GET['assist']) && in_array($_GET['assist'], array('yes', 'no'))) {
                $_SESSION['assist'] = $_GET['assist'];

                if ($_GET['assist'] == 'yes')
                    $this->_redirect('index.php?action=changelog');
                else
                    $this->_redirect('index.php?action=setConfig');
            }

            $this->_redirect('index.php?action=selectProcessType');
        }

        /*
         * Display changelog
         */
        public function changelog() {
            $this->_view = 'changelog';

            $this->_viewData['changelog'] = $this->_changelog;
        }

        /*
         * Set config (assisted or not)
         */
        public function setConfig() {

            $this->_view = 'setConfig';

            $this->_viewData['process']             = $this->data['process'];
            $this->_viewData['assist']              = $this->data['assist'];
            //$this->_viewData['databaseTypeList']    = $this->_databaseTypeList;

            if ($this->data['process'] == 'update') {
                include '../conf.inc.php';

                $this->_viewData['host']            = $global['db_host'];
                $this->_viewData['user']            = $global['db_user'];
                $this->_viewData['name']            = $global['db_name'];
                //$this->_viewData['databaseType']    = $global['db_type'];
                //$this->_viewData['port']            = $global['db_port'];
                //$this->_viewData['persistent']      = $global['db_persistent'];
                $this->_viewData['prefix']          = $db_prefix;
            }
        }

        /*
         * Save config data in PHP session
         */
        public function saveConfig() {
            if ($this->data['process'] == 'install') {
                $_SESSION['host']           = $_POST['db_host'];
                $_SESSION['user']           = $_POST['db_user'];
                $_SESSION['pass']           = (isset($_POST['db_pass'])) ? $_POST['db_pass'] : '';
                $_SESSION['db_name']        = $_POST['db_name'];
                //$_SESSION['db_type']        = $_POST['db_type'];
                //$_SESSION['db_port']        = $_POST['db_port'];
                //$_SESSION['db_persistent']  = $_POST['db_persistent'];
                $_SESSION['db_prefix']      = $_POST['db_prefix'];

                $this->_redirect('index.php?action=installDB');
            }
            elseif ($this->data['process'] == 'update') {
                unset($_SESSION['hash']);
                include '../conf.inc.php';

                $_SESSION['host']           = $global['db_host'];
                $_SESSION['user']           = $global['db_user'];
                $_SESSION['pass']           = $global['db_pass'];
                $_SESSION['db_name']        = $global['db_name'];
                //$_SESSION['db_type']        = $global['db_type'];
                //$_SESSION['db_port']        = $global['db_port'];
                //$_SESSION['db_persistent']  = $global['db_persistent'];
                $_SESSION['db_prefix']      = $db_prefix;

                $this->_redirect('index.php?action=checkMaliciousScript');
            }
        }

        /*
         * Check if malicious sscipt exist (update only)
         */
        public function checkMaliciousScript() {
            $maliciousScript = false;
            $path = '../modules/404/lang/turkish.lang.php';

            if (is_file($path)) {
                if (is_writeable($path)) {
                    @chmod ($path, 0755);
                    @unlink($path);

                    if (is_file($path))
                        $maliciousScript = true;
                }
                else {
                    $maliciousScript = true;
                }
            }

            if ($maliciousScript)
                $this->_view = 'maliciousScript';
            else
                $this->_redirect('index.php?action=installDB');
        }

        /*
         * Check MySQL database connection and return result
         */
        public function dbConnectTest() {
            $connect = $this->_dbConnect($_POST['db_host'], $_POST['db_user'], utf8_decode($_POST['db_pass']), $_POST['db_name']);

            if (strpos($connect, 'Unknown MySQL server host') !== false) {# 2002
                echo 'HOST_ERROR';
            }
            else if (strpos($connect, 'Access denied for user') !== false) {# 1044 / 1045
                echo 'LOGIN_ERROR';
            }
            else if (strpos($connect, 'Unknown database') !== false) {# 1049
                echo 'DB_ERROR';
            }
            else if ($connect == 'OK') {
                if (isset($_POST['type'])) {
                    if ($_POST['type'] == 'update') {
                        $sql = 'SELECT name, value FROM '. $_POST['db_prefix'] .'_config ORDER BY RAND() LIMIT 1';
                        $req = @mysql_query($sql);

                        if ($req === false)
                            echo 'PREFIX_ERROR';
                        else
                            echo 'OK';
                    }
                    else {
                        echo 'OK';
                    }
                }
                else {
                    echo 'OK';
                }
            }
            else {
                echo $connect;
            }
        }

        /*
         * Create or update all database table
         */
        public function installDB() {
            $this->_view = 'installDB';

            $this->_viewData['process'] = $this->data['process'];

            $this->_viewData['processTableList']    = $this->_getProcessTableList();
            $this->_viewData['db_prefix']           = $this->data['db_prefix'];

            if ($this->data['process'] == 'install') {
                //$this->_viewData['array_text']              = array(_LOGITXTSUCCESS);
                $this->_viewData['error']                   = _LOGITXTERROR;
                $this->_viewData['complete']                = _LOGITXTENDSUCCESS;
                $this->_viewData['complete_error_start']    = _LOGITXTENDERRORSTART;
                $this->_viewData['complete_error_end']      = _LOGITXTENDERROREND;
            }
            elseif ($this->data['process'] == 'update') {
                unset($_SESSION['hash']);
                include '../conf.inc.php';

                /*$this->_viewData['array_text']              = array(
                                                                _LOGUTXTSUCCESS,
                                                                _LOGUTXTUPDATE,
                                                                _LOGUTXTUPDATE2,
                                                                _LOGUTXTREMOVE,
                                                                _LOGUTXTREMOVE2
                                                            );*/
                $this->_viewData['error']                   = _LOGUTXTERROR;
                $this->_viewData['complete']                = _LOGUTXTENDSUCCESS;
                $this->_viewData['complete_error_start']    = _LOGUTXTENDERRORSTART;
                $this->_viewData['complete_error_end']      = _LOGUTXTENDERROREND;
            }
        }

        /*
         * Create or update a database table
         */
        public function creatingDB() {
            $tableFile  = $_REQUEST['tableFile'];
            $db_prefix  = $this->data['db_prefix'];
            $charset    = 'latin1';
            $collate    = 'latin1_general_ci';

            $connect = $this->_dbConnect($this->data['host'], $this->data['user'], $this->data['pass'], $this->data['db_name']);

            include 'update.inc';

            if ($connect == 'OK') {
                $path = 'tables/'. $tableFile;

                if (is_file($path)) {
                    $result = include $path;
                    echo $result;
                }
                else {
                    echo 'File no found : '. $path .'<br />';
                }
            }
            else {
                echo $this->_translateDbConnectError($connect), '<br/>';
            }
        }

        /*
         * Display form for create user admin
         */
        public function setUserAdmin() {
            $_SESSION['user_admin'] = 'INPROGRESS';

            $this->_view = 'setUserAdmin';
        }

        /*
         * Check user admin data and save it, generate conf.inc file for install process
         */
        public function saveUserAdmin() {
            if (! isset($_POST['pseudo'], $_POST['pass'], $_POST['pass2'], $_POST['mail'])
                || strlen($_POST['pseudo']) < 3 || preg_match('`[\$\^\(\)\'"?%#<>,;:]`', $_POST['pseudo'])
                || $_POST['pass'] != $_POST['pass2']
                || ! preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/', $_POST['mail'])
            ) {
                $this->_view = 'userAdminError';

                $this->_viewData['error'] = 'fields';
            }
            else {
                $connect = $this->_dbConnect($this->data['host'], $this->data['user'], $this->data['pass'], $this->data['db_name']);

                if ($connect == 'OK') {
                    include 'user.inc';

                    $saveCfgResult = saveConfig('install');

                    if ($saveCfgResult == 'OK') {
                        $_SESSION['user_admin'] = 'FINISH';
                        $this->_redirect('index.php?action=installSuccess');
                    }
                    else {
                        $this->_redirect('index.php?action=installFailure&error='. $saveCfgResult);
                    }
                }
                else {
                    $this->_view = 'userAdminError';

                    $this->_viewData['error'] = $this->_translateDbConnectError($connect);
                }
            }
        }

        /*
         * Update conf.inc.php file
         */
        public function updateConfig() {
            include 'user.inc';

            $saveCfgResult = saveConfig('update');

            if ($saveCfgResult == 'OK')
                $this->_redirect('index.php?action=installSuccess');
            else
                $this->_redirect('index.php?action=installFailure&error='. $saveCfgResult);
        }

        /*
         * Display install failure message
         */
        public function installFailure() {
            $_SESSION['user_admin'] = 'FINISH';

            $this->_view = 'installFailure';

            $this->_viewData['error']       = (isset($_GET['error'])) ? $_GET['error'] : '';
            $this->_viewData['content_web'] = (isset($this->data['content_web'])) ? $this->data['content_web'] : '';
        }

        /*
         * Send conf.inc.php file to web browser
         */
        public function printConfig() {
            header('Content-disposition:filename=conf.inc.php');
            header('Content-type:application/octetstream');

            if (isset($_SESSION['content']))
                echo $_SESSION['content'];
        }

        /*
         * Display install success message
         */
        public function installSuccess() {
            $this->_view = 'installSuccess';
        }

        /*
         * Display partners logo & link
         */
        public function getPartners() {
            $content = @file_get_contents('http://www.nuked-klan.org/extra/partners.php?key='. self::PARTNERS_KEY);
            $content = @unserialize($content);
            $content = (! is_array($content)) ? array() : $content;

            echo $this->_applyView('getPartners', array(
                'content'   => $content
            ));
        }

        /*
         * Reset PHP session and restart process (install /update)
         */
        public function resetSession() {
            unset($_SESSION);
            session_destroy();
            $this->_redirect('index.php');
        }

        /*
         * Delete PHP session and redirect to website
         */
        public function deleteSession() {
            unset($_SESSION);
            session_destroy();
            $this->_redirect('../index.php');
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Service methods, called to perform a specific task
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////

        /*
         * Initialize PHP session and store data in $data attribute
         */
        private function _initSession() {
            session_start();

            if (isset($_SESSION['active']) && $_SESSION['active'] === true) {
                foreach ($_SESSION as $k => $v)
                    $this->data[$k] = $v;
            }

            $_SESSION['active'] = true;
        }

        /*
         * Detect language and return it
         */
        private function _detectLanguage() {
            if (isset($_GET['language']) && array_key_exists($_GET['language'], $this->_languageList)) {
                return $_GET['language'];
            }
            else {
                if (isset($this->data['language']) && array_key_exists($this->data['language'], $this->_languageList))
                    return $this->data['language'];
                else
                    return $this->_getWebBrowserLanguage();
            }
        }

        /*
         * Load language file
         */
        private function _loadLanguage() {
            $this->_language = $this->_detectLanguage();

            $this->_i18n = include 'lang/'. $this->_language .'.lang.php';
        }

        /*
         * Detect web browser used language and return it
         * see : http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
         */
        private function _getWebBrowserLanguage() {
            $preferredLanguage  = '';
            $highRange          = 0;

            if (! isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) || empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
                return $preferredLanguage;

            foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $rawLanguageData) {
                $languageData = explode(';', $rawLanguageData);
                $languageTags = array_shift($languageData);

                if (empty($languageData))
                    $languageRange = 1;
                else
                    $languageRange = array_shift($languageData);

                if ($languageRange > $highRange) {
                    $preferredLanguage  = substr($languageTags, 0, 2);
                    $highRange          = $languageRange;
                }
            }

            $navigatorLanguageList = array(
                'en' => 'english',
                'fr' => 'french'
            );

            if (array_key_exists($preferredLanguage, $navigatorLanguageList))
                return $navigatorLanguageList[$preferredLanguage];
            else
                return 'english';
        }

        /*
         * Connect to MySQL database and return result message
         */
        private function _dbConnect($host, $user, $password, $dbName) {
            if (($db = @mysql_connect($host, $user, $password)) === false)
               return mysql_error();
 
            if (! @mysql_select_db($dbName, $db))
               return mysql_error();

            // see http://php.net/manual/fr/mysqlinfo.concepts.charset.php
            if (! @mysql_set_charset('latin1', $db))
               return mysql_error();// Invalid characterset or character set not supported

            return 'OK';
        }

        /*
         * Translate and return MySQL database connection error
         */
        private function _translateDbConnectError($error) {
            if (strpos($error, 'Unknown MySQL server host') !== false) # 2002
                return _DB_HOST_ERROR;
            else if (strpos($error, 'Access denied for user') !== false) # 1044 / 1045
                return _DB_LOGIN_ERROR;
            else if (strpos($error, 'Unknown database') !== false) # 1049
                return _DB_NAME_ERROR;
            else
                return _DB_UNKNOW_ERROR .' : '. $error;
        }

        /*
         * Check PHP version, required and optional PHP extension and FTP file permissions (writable)
         */
        private function _requirements() {
            $requirements = array(
                '_PHPVERSION'   => (version_compare(PHP_VERSION, install::MINIMAL_PHP_VERSION)) ? 'enabled' : 'required-disabled'
            );

            foreach ($this->_phpExtension as $extensionName => $requirement) {
                if (extension_loaded($extensionName))
                    $requirements['_'. strtoupper($extensionName) .'EXT'] = 'enabled';
                else
                    $requirements['_'. strtoupper($extensionName) .'EXT'] = $requirement .'-disabled';
            }

            $requirements['_TESTCHMOD'] = (is_writable(dirname(dirname(__FILE__)).'/')) ? 'enabled' : 'optional-disabled';

            return $requirements;
        }

        /*
         * Check if Nuked-Klan version is updatable or not
         */
        # TODO : Voir pour 1.7.8 > nk > 1.7.9 RC3 (valeur de retour -1)
        private function _validVersion($version) {
            if (version_compare($version, self::NK_VERSION, '=')) { // last version already set
                return 1;
            }
            else if ((version_compare($version, '1.7.8', '>') && version_compare($version, '1.7.9 RC3', '<')) || version_compare($version, '1.7.7', '<')) { // cannot update
                return -1;
            }
            else {// can update, version == 1.7.7, 1.7.8 or greater than 1.7.9
                return 0;
            }
        }

        /*
         * Check if config data must be updated or not
         */
        private function _configIsUpdatable() {
            include '../conf.inc.php';

            foreach ($this->_configDbData as $key)
                if (! array_key_exists($key, $global)) return true;

            return false;
        }

        /*
         * Protect var for database backup (from phpMyAdmin)
         */
        private function _sqlAddSlashes($str) {
            $str = str_replace(array("\x00", "\x0a", "\x0d", "\x1a"), array('\0', '\n', '\r', '\Z'), $str);
            $str = str_replace('\\', '\\\\', $str);
            //if ($crlf) $str = strtr($str, array("\n" => '\n', "\r" => '\r', "\t" => '\t'));

            return str_replace('\'', '\'\'', $str);
        }

        /*
         * Generate and return table list for install / update
         */
        private function _getProcessTableList() {
            $processList = array();

            foreach (array_diff(scandir('tables'), array('.', '..')) as $fileTable) {
                $fileTableData = explode('.', $fileTable);

                if (in_array($this->data['process'], $fileTableData)
                    && $fileTableData[0] == 'table'
                    && array_pop($fileTableData) == 'inc'
                )
                    $processList[] = $fileTable;
            }

            return $processList;
        }

        /*
         * Check if table exist in database
         */
        public function tableExist($tableName) {
            $sql = 'SHOW TABLES FROM `'. $this->data['db_name'] .'` LIKE \''. $this->data['db_prefix'] .'_'. $tableName .'\'';
            $req = mysql_query($sql) or die(mysql_error());

            return (mysql_num_rows($req) == 0) ? false : true;
        }

        /*
         * Check if field exist in database table
         */
        public function fieldExist($tableName, $fieldname) {
            $sql = 'SHOW COLUMNS FROM `'. $this->data['db_prefix'] .'_'. $tableName .'` LIKE \''. $fieldname .'\'';
            $req = mysql_query($sql) or die(mysql_error());

            return (mysql_num_rows($req) == 0) ? false : true;
        }

        /*
        public function _convertDatabaseToUTF8() {
            include '../conf.inc.php';

            $this->bddConnect($global['db_host'], $global['db_user'], $global['db_pass'], $global['db_name']);

            $sql = 'ALTER DATABASE '. $global['db_name'] .' CHARACTER SET utf8 COLLATE utf8_unicode_ci';
            mysql_query($sql) or die(mysql_error());

            $result = mysql_query('SHOW TABLES');

            while ($row = mysql_fetch_row($result)) {
                $sql = 'ALTER TABLE '. $db_prefix .'_'. $row[0] .' CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci';
                mysql_query($sql) or die(mysql_error());
            }
        }
        */

        /*
         * Return a random info to display in footer
         */
        private function _getInfo() {
            $n = rand(0, count($this->_infoList) - 1);

            return array(
                'n'     => $n + 1,
                'name'  => $this->_infoList[$n]
            );
        }

        /*
         * Send header to execute a redirection
         */
        private function _redirect($url) {
            header('Location: '. $url);
            exit;
        }

        /*
         * Generate random hash key and return it
         */
        public static function generateHashKey() {
            $charPool = range(32, 255);
            unset($charPool[2], $charPool[7], $charPool[60]);

            $charPool   = array_values(array_map('chr', $charPool));
            $poolLength = count($charPool) - 1;
            $hashKey    = '';

            for ($n = 0; $n < 20; $n++)
                $hashKey .= $charPool[mt_rand(0, $poolLength)];

            return $hashKey;
        }

        /*
         * Return hashed password
         */
        public static function hashPassword($hashKey, $password, $offset = null) {
            $builder    = '';
            $offset      = ($offset === null) ? rand(0, 15) : $offset;
            $password   = sha1($password);
            $passLength = strlen($password) * 2;

            for ($i = 0; $i < $passLength; $i++) {
                if ($i % 2 == 0)
                    $builder .= $password[$i / 2];
                else
                    $builder .= substr($hashKey, ($i / 2 + $offset) % 20, 1);
            }

            return '#'. dechex($offset) . md5($builder);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Display methods known to perform layout
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////

        /*
         * Apply HTML view and return this content
         */
        private function _applyView($view, $data = array()) {
            ob_start();
            extract($data);

            include 'views/'. $view .'.php';

            return ob_get_clean();
        }

        /*
         * Run install / update process
         */
        public function run() {
            $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

            if (method_exists($this, $action) && (isset($_SESSION['language']) || isset($_REQUEST['language'])))
                $this->{$action}();
            else
                $this->selectLanguage();

            if (isset($this->_view)) {
                $content = $this->_applyView($this->_view, $this->_viewData);

                echo $this->_applyView('fullPage', array(
                    'navigationList'    => $this->_navigation,
                    'data'              => $this->data,
                    'action'            => $action,
                    'content'           => $content,
                    'info'              => $this->_getInfo()
                ));
            }

        }

    }

?>