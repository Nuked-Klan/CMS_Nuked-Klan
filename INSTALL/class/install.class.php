<?php

/*
 * Manage install / update process
 */
    class install {

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Configuration data (config.inc)
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////

        /*
         * Sets Nuked-Klan version to install / update
         */
        private $_nkVersion;

        /*
         * Sets Nuked-Klan minimum version for install / update
         */
        private $_nkMinimumVersion;

        /*
         * Sets the minimum version of PHP used by this version of Nuked-Klan
         */
        private $_minimalPhpVersion;

        /*
         * Sets PHP extension list used by this version of Nuked-Klan
         */
        private $_phpExtension = array();

        /*
         * Sets upload directory list
         */
        private $_uploadDir = array();

        /*
         * Sets changelog list
         */
        private $_changelog = array();

        /*
         * Sets info list display in footer
         */
        private $_infoList = array();

        /*
         * Sets partners key
         */
        private $_partnersKey;

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Core data
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////

        /*
         * Sets config database data list
         */
        private $_configDbData = array('db_host', 'db_user', 'db_pass', 'db_name');

        /*
         * Sets navigation list display in left sidebar
         */
        private $_navigation = array(
            'language'      => 'SELECT_LANGUAGE',
            'process'       => 'SELECT_TYPE',
            'stats'         => 'SELECT_STATS',
            'db_save'       => 'SELECT_SAVE',
            'assist'        => 'CHECK_TYPE_INSTALL',
            'user_admin'    => 'CREATE_USER_ADMIN'
        );

        /*
         * Sets database list
         */
        /*private $_databaseTypeList = array(
            'mysql'     => 'MySQL'
        );*/

        /*
         * Sets current database
         */
        //private $_databaseType = 'mysql';

        /*
         * Sets used database charset 
         */
        private $_charset = 'latin1';

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

        /*
         * Sets language used
         */
        private $_language = 'english';

        /*
         * Sets language translation
         */
        private $_i18n = array();

        /*
         * Constructor
         * - Init PHP session and load data
         * - Load configuration file
         * - Load language file
         */
        function __construct() {
            $this->_initSession();
            $this->_loadConfiguration();
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

            $this->_viewData['i18n']            = $this->_i18n;
            $this->_viewData['languageList']    = $this->_getLanguageList();
            $this->_viewData['language']        = $this->_language;
        }

        /*
         * Set used language while install / update process
         */
        public function setLanguage() {
            if (isset($_POST['language']) && is_file('lang/'. $_POST['language'] .'.lang.php')) {
                $_SESSION['language'] = $_POST['language'];

                $this->_redirect('index.php?action=main');
            }

            $this->_redirect('index.php');
        }

        /*
         * Detect process (install / update) and check Nuked-Klan version if already installed
         */
        public function main() {
            if (! is_file('../conf.inc.php')) {
                $this->_viewData['process'] = 'install';
            }
            else {
                define('INDEX_CHECK', 1);

                // TODO : Tester si un ancien fichier est encore présent
                if (is_file('../Includes/version.php')) {
                    include '../Includes/version.php';
                }
                else {
                    include '../conf.inc.php';

                    try {
                        if (! isset($global, $db_prefix) || ! is_array($global) || ! is_string($db_prefix))
                            throw new Exception($this->_i18n['CORRUPTED_CONF_INC']);

                        $connect = $this->_dbConnect($global);

                        if ($connect != 'OK')
                            throw new Exception($this->_translateDbConnectError($connect));

                        $sql = 'SELECT value FROM `'. $db_prefix .'_config` WHERE name = \'version\'';
                        $dbsConfig = @mysql_query($sql);

                        if ($dbsConfig === false)
                            throw new Exception($this->_i18n['DB_CONNECT_FAIL'] .'<br/>'. $this->_i18n['DB_PREFIX_ERROR']);

                        list($nk_version) = mysql_fetch_array($dbsConfig);
                    }
                    catch (exception $e) {
                        $this->_view = 'fatalError';

                        $this->_viewData['i18n']        = $this->_i18n;
                        $this->_viewData['error']       = $e->getMessage();
                        $this->_viewData['refreshLink'] = 'index.php?action=main';
                        return;
                    }
                }

                if (isset($nk_version)) {
                    $_SESSION['version'] = $this->_viewData['currentVersion'] = $nk_version;

                    if ($_SESSION['version'] == $this->_nkVersion) {
                        $this->_viewData['message'] = sprintf($this->_i18n['LAST_VERSION_SET'], $this->_nkVersion);
                        $this->_viewData['alreadyUpdated'] = true;
                    }
                    else if (version_compare($this->_nkMinimumVersion, $_SESSION['version'], '<')) {
                        $this->_viewData['message'] = sprintf($this->_i18n['BAD_VERSION'], $this->_nkMinimumVersion);
                    }
                    else {
                        $this->_viewData['process'] = 'update';
                    }
                }
            }

            $this->_view = 'main';

            $this->_viewData['i18n']            = $this->_i18n;
            $this->_viewData['processVersion']  = $this->_nkVersion;
        }

        /*
         * Check and save process (install / update)
         */
        public function saveProcess() {
            if (isset($_GET['process']) && in_array($_GET['process'], array('install', 'update'))) {
                $_SESSION['process'] = $_GET['process'];

                if ($_GET['process'] == 'update') {
                    require_once 'class/confInc.class.php';

                    $cfg            = new confInc;
                    $saveCfgResult  = $cfg->closeWebsite();

                    if ($saveCfgResult != 'OK') {
                        $this->_view = 'confIncFailure';

                        $this->_viewData['i18n']        = $this->_i18n;
                        $this->_viewData['error']       = $saveCfgResult;
                        $this->_viewData['retryUrl']    = 'index.php?action=saveProcess&amp;process='. $_GET['process'];
                        $this->_viewData['nextUrl']     = 'index.php?action=checkCompatibility';
                        return;
                    }
                }

                $this->_redirect('index.php?action=checkCompatibility');
            }

            $this->_redirect('index.php?action=main');
        }

        /*
         * Check PHP requirements for execute process
         */
        public function checkCompatibility() {
            $this->_i18n['PHP_VERSION'] = sprintf($this->_i18n['PHP_VERSION'], $this->_minimalPhpVersion);

            $this->_view = 'checkCompatibility';

            $this->_viewData['i18n']                = $this->_i18n;
            $this->_viewData['requirements']        = $this->_requirements();
            $this->_viewData['nbChmodDirectory']    = count($this->_uploadDir) + 1;
        }

        /*
         * Choose to send stats to Nuked-Klan.org or not
         */
        public function chooseSendStats() {
            if (isset($this->data['stats']) && $this->data['stats'] == 'off')
                $stats = false;
            else
                $stats = true;

            $this->_view = 'chooseSendStats';

            $this->_viewData['i18n']    = $this->_i18n;
            $this->_viewData['stats']   = $stats;
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

            $this->_viewData['i18n'] = $this->_i18n;
        }

        /*
         * Generate backup of MySQL database and send file to web browser
         */
        public function createBackupDb() {
            include '../conf.inc.php';

            if (! isset($global, $db_prefix) || ! is_array($global) || ! is_string($db_prefix)) {
                $error = $this->_i18n['CORRUPTED_CONF_INC'];
            }
            else {
                $connect = $this->_dbConnect($global);

                if ($connect != 'OK') $error = $connect;
            }

            if (isset($error)) {
                $this->_view = 'fatalError';

                $this->_viewData['i18n']    = $this->_i18n;
                $this->_viewData['error']   = $error;
                return;
            }

            $_SESSION['db_save'] = 'yes';

            header('Content-disposition:filename=save-'. time() .'.sql');
            header('Content-type:application/octetstream');

            echo '-- --------------------------------------------------------', "\n"
               , '-- Dump of Nuked-Klan database', "\n"
               , '-- Database: `', $global['db_name'], '`', "\n"
               , '-- Date: ', strftime('%c'), "\n"
               , '-- --------------------------------------------------------', "\n\n";

            $dbsTables  = mysql_query('SHOW TABLES');
            $nbTable    = mysql_num_rows($dbsTables);
            $t          = 0;

            while ($table = mysql_fetch_row($dbsTables)) {
                $resultCreateTable = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '. $table[0]));

                echo '--', "\n"
                   , '-- Table structure for table `', $table[0], '`', "\n"
                   , '--', "\n\n"
                   , 'DROP TABLE IF EXISTS ', $table[0], ';', "\n\n"
                   , $resultCreateTable[1], ';', "\n\n"
                   , '-- --------------------------------------------------------', "\n\n";

                $dbsTable   = mysql_query('SELECT * FROM '. $table[0]);
                $nbData     = mysql_num_rows($dbsTable);

                if ($nbData > 0) {
                    echo '--', "\n"
                       , '-- Dumping data for table `', $table[0], '`', "\n"
                       , '--', "\n\n";

                    $dbsColumnsTable    = mysql_query('SHOW COLUMNS FROM '. $table[0]);
                    $fields             = array();

                    while ($row = mysql_fetch_assoc($dbsColumnsTable))
                        $fields[] = $row['Field'];

                    echo 'INSERT INTO ', $table[0], ' (`', implode('`, `', $fields), '`) VALUES', "\n";

                    $nbFields = count($fields);
                    $d = 0;

                    while ($row = mysql_fetch_row($dbsTable)) {
                        echo '(';

                        for ($f = 0; $f < $nbFields; $f++) {
                            $row[$f] = $this->_sqlAddSlashes($row[$f]);

                            if (isset($row[$f]))
                                echo '\'', $row[$f], '\'' ;
                            else
                                echo '\'\'';

                            if ($f < ($nbFields - 1))
                                echo ', ';
                        }

                        $d++;

                        if ($d == $nbData)
                            echo ');', "\n";
                        else
                            echo '),', "\n";
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

            $this->_viewData['i18n']    = $this->_i18n;
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

            $this->_viewData['i18n']            = $this->_i18n;
            $this->_viewData['processVersion']  = $this->_nkVersion;
            $this->_viewData['changelog']       = $this->_changelog;
        }

        /*
         * Set config (assisted or not)
         */
        public function setConfig() {
            $this->_view = 'setConfig';

            $this->_viewData['i18n']                = $this->_i18n;
            $this->_viewData['process']             = $this->data['process'];
            $this->_viewData['assist']              = $this->data['assist'];
            //$this->_viewData['databaseTypeList']    = $this->_databaseTypeList;

            if ($this->data['process'] == 'update') {
                include '../conf.inc.php';

                $this->_viewData['host']            = $global['db_host'];
                $this->_viewData['user']            = $global['db_user'];
                $this->_viewData['name']            = $global['db_name'];
                //$this->_viewData['databaseType']    = isset($global['db_type']) ? $global['db_type'] : $this->_databaseType;
                //$this->_viewData['port']            = isset($global['db_port']) ? $global['db_port'] : $this->_getDbDefaultPort();
                //$this->_viewData['persistent']      = isset($global['db_persistent']) ? $global['db_persistent'] : false;
                $this->_viewData['prefix']          = $db_prefix;
            }
        }

        /*
         * Save config data in PHP session
         */
        public function saveConfig() {
            if ($this->data['process'] == 'install') {
                $_SESSION['db_host']        = $_POST['db_host'];
                $_SESSION['db_user']        = $_POST['db_user'];
                $_SESSION['db_pass']        = (isset($_POST['db_pass'])) ? $_POST['db_pass'] : '';
                $_SESSION['db_name']        = $_POST['db_name'];
                //$_SESSION['db_type']        = $_POST['db_type'];
                //$_SESSION['db_port']        = $_POST['db_port'];
                //$_SESSION['db_persistent']  = $_POST['db_persistent'];
                $_SESSION['db_prefix']      = $_POST['db_prefix'];
                $_SESSION['HASHKEY']        = self::generateHashKey();

                $this->_redirect('index.php?action=installDB');
            }
            elseif ($this->data['process'] == 'update') {
                include '../conf.inc.php';

                $_SESSION['db_host']        = $global['db_host'];
                $_SESSION['db_user']        = $global['db_user'];
                $_SESSION['db_pass']        = $global['db_pass'];
                $_SESSION['db_name']        = $global['db_name'];
                //$_SESSION['db_type']        = $global['db_type'];
                //$_SESSION['db_port']        = $global['db_port'];
                //$_SESSION['db_persistent']  = $global['db_persistent'];
                $_SESSION['db_prefix']      = $db_prefix;
                $_SESSION['HASHKEY']        = defined('HASHKEY') ? HASHKEY : self::generateHashKey();

                $this->_redirect('index.php?action=checkMaliciousScript');
            }
        }

        /*
         * Check if malicious sscipt exist (update only)
         */
        public function checkMaliciousScript() {
            $maliciousScript    = false;
            $path               = '../modules/404/lang/turkish.lang.php';

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

            if ($maliciousScript) {
                $this->_view = 'maliciousScript';

                $this->_viewData['i18n'] = $this->_i18n;
            }
            else {
                $this->_redirect('index.php?action=installDB');
            }
        }

        /*
         * Check MySQL database connection and return result
         */
        public function dbConnectTest() {
            $_POST      = array_map('utf8_decode', $_POST);
            $connect    = $this->_dbConnect($_POST);

            if (strpos($connect, 'Unknown MySQL server host') !== false) {# 2002
                echo 'DB_HOST_ERROR';
            }
            else if (strpos($connect, 'Access denied for user') !== false) {# 1044 / 1045
                echo 'DB_LOGIN_ERROR';
            }
            else if (strpos($connect, 'Unknown database') !== false) {# 1049
                echo 'DB_NAME_ERROR';
            }
            else if (strpos($connect, 'Invalid characterset or character') !== false) {# 2019
                echo 'DB_CHARSET_ERROR';
            }
            else if ($connect == 'OK') {
                if (isset($_POST['type']) && $_POST['type'] == 'update') {
                    $sql = 'SELECT name, value FROM '. $_POST['db_prefix'] .'_config ORDER BY RAND() LIMIT 1';
                    $dbsConfig = @mysql_query($sql);

                    if ($dbsConfig === false)
                        echo 'DB_PREFIX_ERROR';
                    else
                        echo 'OK';
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

            $this->_viewData['i18n']                = $this->_i18n;
            $this->_viewData['process']             = $this->data['process'];
            $this->_viewData['db_prefix']           = $this->data['db_prefix'];
            $this->_viewData['processTableList']    = $this->_getProcessTableList();
        }

        /*
         * Create or update a database table
         */
        public function creatingDB() {
            $charset    = $this->_charset;
            $collate    = $this->_charset .'_general_ci';

            $connect = $this->_dbConnect($this->data);

            if ($connect == 'OK') {
                $path = 'tables/'. $_POST['tableFile'];

                if (is_file($path)) {
                    $result = include $path;
                    echo $result;
                }
                else {
                    echo $this->_i18n['MISSING_FILE'], $path, '<br />';
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
            $_SESSION['user_admin'] = $this->data['user_admin'] = 'IN_PROGRESS';

            $this->_view = 'setUserAdmin';

            $this->_viewData['i18n'] = $this->_i18n;
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

                $this->_viewData['i18n'] = $this->_i18n;
            }
            else {
                if (! isset($this->data['defaultContent'])) {
                    $connect = $this->_dbConnect($this->data);

                    if ($connect == 'OK') {
                        $result = $this->_writeDefaultContent($_POST['pseudo'], $_POST['pass'], $_POST['mail']);

                        if ($result === true)
                            $_SESSION['defaultContent'] = true;
                        else
                            $error = $result;
                    }
                    else {
                        $error = $this->_translateDbConnectError($connect);
                    }

                    if (isset($error)) {
                        $this->_view = 'fatalError';

                        $this->_viewData['i18n']        = $this->_i18n;
                        $this->_viewData['error']       = $error;
                        $this->_viewData['backLink']    = 'index.php?action=setUserAdmin';
                        return;
                    }
                }

                require_once 'class/confInc.class.php';

                $cfg = new confInc;

                $global = array();

                foreach ($this->_configDbData as $k) {
                    if (array_key_exists($k, $this->data))
                        $global[$k] = $this->data[$k];
                }

                $cfg->setData(array(
                    'nk_version'    => $this->_nkVersion,
                    'global'        => $global,
                    'db_prefix'     => $this->data['db_prefix'],
                    'HASHKEY'       => $_SESSION['HASHKEY']
                ));

                $saveCfgResult = $cfg->save();

                if ($saveCfgResult != 'OK') {
                    $this->_view = 'confIncFailure';

                    $this->_viewData['i18n']        = $this->_i18n;
                    $this->_viewData['error']       = $saveCfgResult;
                    $this->_viewData['retryUrl']    = 'index.php?action=saveUserAdmin';
                    $this->_viewData['nextUrl']     = 'index.php?action=installSuccess';
                    return;
                }
                else {
                    $_SESSION['user_admin'] = 'FINISH';
                    $this->_redirect('index.php?action=installSuccess');
                }
            }
        }

        /*
         * Update conf.inc.php file
         */
        public function updateConfig() {
            $connect = $this->_dbConnect($this->data);

            if ($connect == 'OK') {
                $sql = 'UPDATE `'. $this->data['db_prefix'] .'_config`
                    SET value = \''. $this->_nkVersion .'\' WHERE name = \'version\'';

                if (mysql_query($sql) === false)
                    $error = $this->_i18n['DB_CONNECT_FAIL'] .'<br/>'. $this->_i18n['DB_PREFIX_ERROR'];
            }
            else {
                $error = $this->_translateDbConnectError($connect);
            }

            if (isset($error)) {
                $this->_view = 'fatalError';

                $this->_viewData['i18n']    = $this->_i18n;
                $this->_viewData['error']   = $error;
            }
            else {
                require_once 'class/confInc.class.php';

                $cfg = new confInc;

                $global = array();

                foreach ($this->_configDbData as $k) {
                    if (array_key_exists($k, $this->data))
                        $global[$k] = $this->data[$k];
                }

                $cfg->setData(array(
                    'nk_version'    => $this->_nkVersion,
                    'global'        => $global,
                    'db_prefix'     => $this->data['db_prefix'],
                    'HASHKEY'       => $this->data['HASHKEY']
                ));

                $saveCfgResult = $cfg->save();

                if ($saveCfgResult == 'OK')
                    $this->_redirect('index.php?action=installSuccess');
                else
                    $this->_redirect('index.php?action=installFailure&error='. $saveCfgResult);
            }
        }

        /*
         * Display install failure message
         * /
        public function installFailure() {
            $_SESSION['user_admin'] = 'FINISH';

            $this->_view = 'installFailure';

            $this->_viewData['i18n']        = $this->_i18n;
            $this->_viewData['error']       = (isset($_GET['error'])) ? $_GET['error'] : '';
            $this->_viewData['content_web'] = (isset($this->data['content_web'])) ? $this->data['content_web'] : '';
        }

        /*
         * Send conf.inc.php file to web browser
         */
        public function printConfig() {
            header('Content-disposition:filename=conf.inc.php');
            header('Content-type:application/octetstream');

            if (isset($_SESSION['confIncContent']))
                echo $_SESSION['confIncContent'];
        }

        /*
         * Display install success message
         */
        public function installSuccess() {
            $_SESSION['user_admin'] = $this->data['user_admin'] = 'FINISH';

            $this->_view = 'installSuccess';

            $this->_viewData['i18n'] = $this->_i18n;
        }

        /*
         * Display partners logo & link
         */
        public function getPartners() {
            $content = @file_get_contents('http://www.nuked-klan.org/extra/partners.php?key='. $this->_partnersKey);
            $content = @unserialize($content);
            $content = (! is_array($content)) ? array() : $content;

            echo $this->_applyView('getPartners', array(
                'i18n'      => $this->_i18n,
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
                $data = $_SESSION;
                unset($data['configuration'], $data['active']);

                foreach ($data as $k => $v)
                    $this->data[$k] = $v;
            }

            $_SESSION['active'] = true;
        }

        /*
         * Load editable configuration, check it and store in PHP session
         */
        private function _loadConfiguration() {
            if (isset($_SESSION['configuration']) && is_array($_SESSION['configuration'])) {
                foreach ($_SESSION['configuration'] as $k => $v)
                    $this->{'_'. $k} = $v;
            }
            else {
                $cfgStrKey      = array('nkVersion', 'nkMinimumVersion', 'minimalPhpVersion', 'partnersKey');
                $cfgArrayKey    = array('phpExtension', 'uploadDir', 'changelog', 'infoList');

                try {
                    $cfg = include 'config.inc';

                    if (count(array_diff(array_keys($cfg), array_merge($cfgStrKey, $cfgArrayKey))) != 0)
                        throw new Exception('Bad configuration array');

                    foreach ($cfgStrKey as $strKey) {
                        if (! is_string($cfg[$strKey]) || empty($cfg[$strKey]))
                            throw new Exception('Bad configuration key (must be a string) : '. $strKey);
                    }

                    foreach ($cfgArrayKey as $arrayKey) {
                        if (! is_array($cfg[$arrayKey]) || empty($cfg[$arrayKey]))
                            throw new Exception('Bad configuration key (must be a array) : '. $arrayKey);
                    }

                    $_SESSION['configuration'] = array();

                    foreach ($cfg as $k => $v)
                        $_SESSION['configuration'][$k] = $this->{'_'. $k} = $v;
                }
                catch (exception $e) {
                    die($e->getMessage());
                }
            }
        }

        /*
         * Generate language list and return it.
         */
        private function _getLanguageList() {
            $languageList = array();

            foreach (array_diff(scandir('lang'), array('.', '..')) as $languageFile) {
                $languageFileData = explode('.', $languageFile);

                if (is_file('lang/'. $languageFile)
                    && $languageFileData[1] == 'lang'
                    && $languageFileData[2] == 'php'
                )
                    $languageList[] = $languageFileData[0];
            }

            return $languageList;
        }

        /*
         * Detect language and return it
         */
        private function _detectLanguage() {
            if (isset($_GET['language']) && is_file('lang/'. $_GET['language'] .'.lang.php')) {
                return $_GET['language'];
            }
            else {
                if (isset($this->data['language']))
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
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && ! empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                $navigatorLanguageList = array(
                    'en' => 'english',
                    'fr' => 'french'
                );

                foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $rawLanguageData) {
                    $languageData       = explode(';', $rawLanguageData);
                    $preferredLanguage  = substr(array_shift($languageData), 0, 2);

                    if (is_file('lang/'. $navigatorLanguageList[$preferredLanguage] .'.lang.php'))
                        return $navigatorLanguageList[$preferredLanguage];
                }
            }

            return 'english';
        }

        /*
         * Connect to MySQL database and return result message
         */
        private function _dbConnect($data) {
            if (! array_key_exists('db_host', $data)) $data['db_host'] = '';
            if (! array_key_exists('db_user', $data)) $data['db_user'] = '';
            if (! array_key_exists('db_pass', $data)) $data['db_pass'] = '';
            if (! array_key_exists('db_name', $data)) $data['db_name'] = '';

            if (($db = @mysql_connect($data['db_host'], $data['db_user'], $data['db_pass'])) === false)
               return mysql_error();
 
            if (! @mysql_select_db($data['db_name'], $db))
               return mysql_error();

            // see http://php.net/manual/fr/mysqlinfo.concepts.charset.php
            if (! @mysql_set_charset($this->_charset, $db))
               return mysql_error();

            return 'OK';
        }

        /*private function _getDbDefaultPort() {
            switch ($this->_databaseType) {
                case 'mysql' :      return 3306;
                case 'postgresql' : return 5432;
            }
        }*/

        /*
         * Translate and return MySQL database connection error
         */
        private function _translateDbConnectError($error) {
            if (strpos($error, 'Unknown MySQL server host') !== false) # 2002
                return $i18n['DB_CONNECT_FAIL'] .'<br/>'. $i18n['DB_HOST_ERROR'];
            else if (strpos($error, 'Access denied for user') !== false) # 1044 / 1045
                return $i18n['DB_CONNECT_FAIL'] .'<br/>'. $i18n['DB_USER_ERROR'];
            else if (strpos($error, 'Unknown database') !== false) # 1049
                return $i18n['DB_CONNECT_FAIL'] .'<br/>'. $i18n['DB_NAME_ERROR'];
            else if (strpos($error, 'Invalid characterset or character') !== false) # 2019
                return $i18n['DB_CONNECT_FAIL'] .'<br/>'. sprintf($i18n['DB_CHARSET_ERROR'], $this->_charset);
            else
                return $i18n['DB_CONNECT_FAIL'] .'<br/>'. $i18n['DB_UNKNOW_ERROR'] .' :<br/>'. $error;
        }

        /*
         * Check PHP version, required and optional PHP extension and FTP file permissions (writable)
         */
        private function _requirements() {
            $requirements = array(
                'PHP_VERSION'   => (version_compare(PHP_VERSION, $this->_minimalPhpVersion)) ? 'enabled' : 'required-disabled'
            );

            foreach ($this->_phpExtension as $extensionName => $requirement) {
                if (extension_loaded($extensionName))
                    $requirements[strtoupper($extensionName) .'_EXT'] = 'enabled';
                else
                    $requirements[strtoupper($extensionName) .'_EXT'] = $requirement .'-disabled';
            }

            @chmod('../', 0755);
            $requirements['CHMOD_TEST_WEBSITE_DIRECTORY'] = (is_writable('../')) ? 'enabled' : 'optional-disabled';

            @chmod('../upload', 0755);

            foreach ($this->_uploadDir as $uploadDir) {
                @chmod('../'. $uploadDir, 0755);
                $requirements['CHMOD_TEST_'. $uploadDir] = (is_writable('../')) ? 'enabled' : 'optional-disabled';
            }

            return $requirements;
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

            return str_replace('\'', '\'\'', $str);
        }

        /*
         * Generate and return table list for install / update
         */
        private function _getProcessTableList() {
            $processList = array();

            foreach (array_diff(scandir('tables'), array('.', '..')) as $tableFile) {
                $tableFileData = explode('.', $tableFile);

                if (is_file('tables/'. $tableFile)
                    && in_array($this->data['process'], $tableFileData)
                    && $tableFileData[0] == 'table'
                    && array_pop($tableFileData) == 'inc'
                )
                    $processList[] = $tableFile;
            }

            return $processList;
        }

        /*
         * Check if table exist in database
         */
        public function tableExist($tableName) {
            $sql = 'SHOW TABLES FROM `'. $this->data['db_name'] .'` LIKE \''. $this->data['db_prefix'] .'_'. $tableName .'\'';
            $dbsTable = mysql_query($sql) or die(mysql_error());

            return (mysql_num_rows($dbsTable) == 0) ? false : true;
        }

        /*
         * Generate and return a random user id 
         */
        private function _generateUserId() {
            $charPool   = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
            $poolLength = count($charPool) - 1;
            $userId     = '';

            shuffle($charPool);

            for ($i = 0; $i < 20; $i++)
                $userId .= $charPool{mt_rand(0, $poolLength)};

            return $userId;
        }

        /*
         * Write demo content in database
         */
        private function _writeDefaultContent($nickname, $password, $email) {
            $nickname   = mysql_real_escape_string($nickname);
            $password   = mysql_real_escape_string(self::hashPassword($this->data['HASHKEY'] , $password));
            $email      = mysql_real_escape_string($email);
            $date       = time();
            $userId     = $this->_generateUserId();
            $ip         = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);

            $sql = 'TRUNCATE TABLE `'. $this->data['db_prefix'] .'_users`';

            if (@mysql_query($sql) === false)
                return mysql_error();

            $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_users` VALUES 
                (\''. $userId .'\', \'\', \'\', \'\', \'\', \'\', \''. $nickname .'\', \''. $email .'\', \'\', \'\', \'\', \'\', \'\', \'\', \''. $password .'\', 9, \''. $date .'\', \'\', \'\', \'\', \'\', 1, \'France.gif\', \'\', \'\', \'\', \'0\')';

            if (@mysql_query($sql) === false)
                return mysql_error();

            $firstNewsTitle = mysql_real_escape_string(sprintf($this->_i18n['FIRST_NEWS_TITLE'], $this->_nkVersion));

            $sql = 'TRUNCATE TABLE `'. $this->data['db_prefix'] .'_news`';

            if (@mysql_query($sql) === false)
                return mysql_error();

            $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_news` VALUES
                (1, 1, \''. $firstNewsTitle .'\', \''. $nickname .'\', \''. $userId .'\', \''. mysql_real_escape_string($this->_i18n['FIRST_NEWS_CONTENT']) .'\', \'\', \''. $date .'\', \'\', \'\')';

            if (@mysql_query($sql) === false)
                return mysql_error();

            $sql = 'TRUNCATE TABLE `'. $this->data['db_prefix'] .'_shoutbox`';

            if (@mysql_query($sql) === false)
                return mysql_error();

            $sql = 'INSERT INTO `'. $this->data['db_prefix'] .'_shoutbox` VALUES
                (1, \''. $nickname .'\', \''. $ip .'\', \''. $firstNewsTitle .'\', \''. $date .'\')';

            if (@mysql_query($sql) === false)
                return mysql_error();

            $sql = 'UPDATE `'. $this->data['db_prefix'] .'_config` SET value = \''. $email .'\'
                WHERE name = \'contact_mail\' OR name = \'mail\'';

            if (@mysql_query($sql) === false)
                return mysql_error();

            return true;
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
         * Load smilies data and store it in PHP session
         */
        private function _loadBBcode() {
            require_once 'class/bbcode.class.php';

            $bbcode = new BBcode(
                $this->_loadSmilies(),
                array(
                    'QUOTE'     => $this->_i18n['QUOTE'],
                    'HAS_WROTE' => $this->_i18n['HAS_WROTE'],
                    'CODE'      => $this->_i18n['CODE']
                )
            );

            return $bbcode;
        }

        /*
         * Load smilies data and store it in PHP session
         */
        private function _loadSmilies() {
            if (isset($this->data['smiliesList']))
                return $this->data['smiliesList'];

            $sql = 'SELECT code, url, name FROM `'. $this->data['db_prefix'] .'_smilies`';
            $dbsSmilies = mysql_query($sql) or die (mysql_error());

            $smilies = array();

            while ($row = mysql_fetch_assoc($dbsSmilies))
                $smilies[] = $row;

            $_SESSION['smiliesList'] = $this->data['smiliesList'] = $smilies;

            return $smilies;
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
                    'language'          => $this->_language,
                    'i18n'              => $this->_i18n,
                    'processVersion'    => $this->_nkVersion,
                    'navigation'        => $this->_navigation,
                    'data'              => $this->data,
                    'action'            => $action,
                    'content'           => $content,
                    'info'              => $this->_getInfo()
                ));
            }

        }

    }

?>