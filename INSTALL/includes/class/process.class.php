<?php
/**
 * process.class.php
 *
 * Manage install / update process
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

class process {

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

    /*
     * Sets deprecated files list
     */
    private $_deprecatedFiles = array();

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Core data
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Sets navigation list display in left sidebar
     */
    private $_navigation = array(
        'language'      => 'SELECT_LANGUAGE',
        'process'       => 'SELECT_TYPE',
        'stats'         => 'SELECT_STATS',
        'backupDb'      => 'SELECT_SAVE',
        'assist'        => 'CHECK_TYPE_INSTALL',
        'administrator' => 'CREATE_ADMINISTRATOR'
    );

    /*
     * Sets view name
     */
    private $_view;

    /*
     * Store i18n instance
     */
    private $_i18n;

    /*
     * Store PHPsession instance
     */
    private $_session;

    /*
     * Store db instance
     */
    private $_db;

    /*
     * Constructor
     * - Init PHP session and load data
     * - Load configuration file
     * - Create i18n instance
     */
    function __construct() {
        try {
            $this->_session = PHPSession::getInstance();
            $this->_i18n    = i18n::getInstance();

            $this->_loadConfiguration();
        }
        catch (Exception $e) {
            echo '<html><body style="margin-top:50px;text-align:center;"><h3>'
                , $e->getMessage()
                , '</h3></body></html>';
        }
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Core methods, they allow the display of pages and execute action
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Select used language while install / update process
     */
    public function selectLanguage() {
        $this->_view = new view('selectLanguage');

        $this->_view->languageList  = $this->_i18n->getLanguageList();
        $this->_view->language      = $this->_i18n->getLanguage();
    }

    /*
     * Set used language while install / update process
     */
    public function setLanguage() {
        if (isset($_POST['language']) && is_file('lang/'. $_POST['language'] .'.lang.php')) {
            $this->_session['language'] = $_POST['language'];

            $this->_redirect('index.php?action=main');
        }

        $this->_redirect('index.php');
    }

    /*
     * Detect process (install / update) and check Nuked-Klan version if already installed
     */
    public function main() {
        $this->_view = new view('main');

        $this->_view->processVersion = $this->_nkVersion;

        if (! is_file('../conf.inc.php')) {
            $this->_view->process = 'install';
        }
        else {
            define('INDEX_CHECK', 1);

            include '../conf.inc.php';
            unset($nk_version);

            if (is_file('../Includes/version.php'))
                include '../Includes/version.php';

            if (! isset($global, $db_prefix) || ! is_array($global) || ! is_string($db_prefix))
                throw new fatalErrorException($this->_i18n['CORRUPTED_CONF_INC']);

            $this->_db = db::getInstance()->load($global);

            $sql = 'SELECT value AS version
                FROM `'. $db_prefix .'_config`
                WHERE name = \'version\'';

            $dbsConfig = $this->_db->selectOne($sql, array('exception' => 'DB_PREFIX_ERROR'));

            if (isset($nk_version) && version_compare($nk_version, $dbsConfig['version'], '>'))
                $nkVersion = $nk_version;
            else
                $nkVersion = $dbsConfig['version'];

            $this->_session['version'] = $this->_view->currentVersion = $nkVersion;

            if ($this->_session['version'] == $this->_nkVersion) {
                $this->_view->message = sprintf($this->_i18n['LAST_VERSION_SET'], $this->_nkVersion);
                $this->_view->alreadyUpdated = true;
            }
            else if (version_compare($this->_nkMinimumVersion, $this->_session['version'], '>')) {
                $this->_view->message = sprintf($this->_i18n['BAD_VERSION'], $this->_nkMinimumVersion);
            }
            else {
                $this->_view->process = 'update';
            }
        }
    }

    /*
     * Check and save process (install / update)
     */
    public function saveProcess() {
        if (isset($_GET['process']) && in_array($_GET['process'], array('install', 'update'))) {
            $this->_session['process'] = $_GET['process'];

            if ($_GET['process'] == 'update') {
                $cfg = new confInc;
                $cfg->closeWebsite();
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

        $this->_view = new view('checkCompatibility');

        $this->_view->requirements = $this->_requirements();
    }

    /*
     * Choose to send stats to Nuked-Klan.org or not
     */
    public function chooseSendStats() {
        $this->_view = new view('chooseSendStats');

        if (isset($this->_session['stats']) && $this->_session['stats'] == 'off')
            $this->_view->stats = false;
        else
            $this->_view->stats = true;
    }

    /*
     * Set to send stats to Nuked-Klan.org or not
     */
    public function setSendStats() {
        if (isset($_POST['stats']) && $_POST['stats'] == 'on')
            $this->_session['stats'] = 'yes';
        else
            $this->_session['stats'] = 'no';

        if ($this->_session['process'] == 'update')
            $this->_redirect('index.php?action=selectSaveDb');
        else
            $this->_redirect('index.php?action=selectProcessType');
    }

    /*
     * Display link to save database
     */
    public function selectSaveDb() {
        $this->_session['backupDb'] = 'no';

        $this->_view = new view('selectSaveDb');
    }

    /*
     * Generate backup of MySQL database and send file to web browser
     */
    public function createBackupDb() {
        include '../conf.inc.php';

        if (! isset($global, $db_prefix) || ! is_array($global) || ! is_string($db_prefix))
            throw new fatalErrorException($this->_i18n['CORRUPTED_CONF_INC']);

        $this->_db = db::getInstance()->load($global);

        $this->_session['backupDb'] = 'yes';

        header('Content-disposition:filename=save-'. time() .'.sql');
        header('Content-type:application/octetstream');

        echo $this->_db->createBackup();
    }

    /*
     * Select process type (assisted or not)
     */
    public function selectProcessType() {
        if ($this->_session['process'] == 'update' && ! confInc::isUpdatable())
            $this->_redirect('index.php?action=saveDbConfiguration');

        $this->_view = new view('selectProcessType');

        $this->_view->process = $this->_session['process'];
    }

    /*
     * Check and save process type (assisted or not)
     */
    public function saveProcessType() {
        if (isset($_GET['assist']) && in_array($_GET['assist'], array('yes', 'no'))) {
            $this->_session['assist'] = $_GET['assist'];

            if ($_GET['assist'] == 'yes')
                $this->_redirect('index.php?action=changelog');
            else
                $this->_redirect('index.php?action=setDbConfiguration');
        }

        $this->_redirect('index.php?action=selectProcessType');
    }

    /*
     * Display changelog
     */
    public function changelog() {
        $this->_view = new view('changelog');

        $this->_view->processVersion    = $this->_nkVersion;
        $this->_view->changelog         = $this->_changelog;
    }

    /*
     * Set database configuration (assisted or not)
     */
    public function setDbConfiguration() {
        $this->_session['db_type'] = 'MySQL';

        $this->_view = new view('setDbConfiguration');

        $this->_view->process           = $this->_session['process'];
        $this->_view->assist            = $this->_session['assist'];
        //$this->_view->databaseTypeList  = db::getDatabaseTypeList();

        $dbHost = $dbUser = $dbName = '';
        //$dbType       = 'MySQL';
        //$dbPersistent = false;

        if ($this->_session['process'] == 'update') {
            include '../conf.inc.php';

            if (! isset($global, $db_prefix) || ! is_array($global) || ! is_string($db_prefix))
                throw new fatalErrorException($this->_i18n['CORRUPTED_CONF_INC']);

            $dbHost = $global['db_host'];
            $dbUser = $global['db_user'];
            $dbName = $global['db_name'];

            //if (isset($global['db_type'])) $dbType = $global['db_type'];
            //if (isset($global['db_port'])) $dbPort = $global['db_port'];
            //if (isset($global['db_persistent'])) $dbPersistent = $global['db_persistent'];
        }

        $this->_view->dbHost        = $dbHost;
        $this->_view->dbUser        = $dbUser;
        $this->_view->dbName        = $dbName;
        $this->_view->dbPrefix      = isset($db_prefix) ? $db_prefix : 'nuked';
        //$this->_view->databaseType  = $dbType;
        //$this->_view->persistent    = $dbPersistent;

        //if (! isset($dbPort))
        //    $this->_view->port = $this->getDbDefaultPort(true);
    }

    /*
     * Get database connection port
     */
    public function getDbDefaultPort($return = false) {
        if ($return)
            $_POST['db_type'] = 'MySQL';
        else
            $_POST = array_map('utf8_decode', $_POST);

        $port = '';

        try {
            $this->_db = db::getInstance()->load($_POST);

            if(method_exists($this->_db, 'getDefaultPort'))
                $port = $this->_db->getDefaultPort();
        }
        catch (dbException $e) {
            $result = $e->getMessage();

            if (isset($this->_i18n[$result]))
                $result = $this->_i18n[$result];
        }

        if ($return)
            return $result;

        echo $result;
    }

    /*
     * Check MySQL database connection and return result
     */
    public function dbConnectTest($return = false) {
        if (! $return)
            $_POST = array_map('utf8_decode', $_POST);

        $result = 'OK';

        try {
            $this->_db = db::getInstance()->load($_POST);

            $this->_db->connect();

            if ($this->_session['process'] == 'update') {
                $sql = 'SELECT name, value
                    FROM '. $_POST['db_prefix'] .'_config
                    ORDER BY RAND()
                    LIMIT 1';

                $this->_db->execute($sql, array('exception' => 'DB_PREFIX_ERROR'));
            }
        }
        catch (dbException $e) {
            $result = $e->getMessage();

            if (isset($this->_i18n[$result]))
                $result = $this->_i18n[$result];
        }

        if ($return)
            return $result;

        echo $result;
    }

    /*
     * Save database configuration data in PHP session
     */
    public function saveDbConfiguration() {
        if (! empty($_POST)) {
            if (! $this->_checkDbConfigurationForm())
                return;

            $this->_session['db_host']          = $_POST['db_host'];
            $this->_session['db_user']          = $_POST['db_user'];
            $this->_session['db_pass']          = $_POST['db_pass'];
            $this->_session['db_name']          = $_POST['db_name'];
            //$this->_session['db_type']          = $_POST['db_type'];
            //$this->_session['db_port']          = ($_POST['db_port'] != '') ? $_POST['db_port'] : '';
            //$this->_session['db_persistent']    = $_POST['db_persistent'];
            $this->_session['db_prefix']        = $_POST['db_prefix'];
            $this->_session['HASHKEY']          = hash::generate();

            $this->_redirect('index.php?action=runProcess');
        }
        else {
            include '../conf.inc.php';

            if (! isset($global, $db_prefix) || ! is_array($global) || ! is_string($db_prefix))
                throw new fatalErrorException($this->_i18n['CORRUPTED_CONF_INC']);

            $this->_session['db_host']          = $global['db_host'];
            $this->_session['db_user']          = $global['db_user'];
            $this->_session['db_pass']          = $global['db_pass'];
            $this->_session['db_name']          = $global['db_name'];
            //$this->_session['db_type']          = $global['db_type'];
            //$this->_session['db_port']          = $global['db_port'];
            //$this->_session['db_persistent']    = $global['db_persistent'];
            $this->_session['db_prefix']        = $db_prefix;
            $this->_session['HASHKEY']          = (defined('HASHKEY')) ? HASHKEY : hash::generate();

            $this->_redirect('index.php?action=checkMaliciousScript');
        }
    }

    /*
     * Check if malicious script exist (update only)
     */
    public function checkMaliciousScript() {
        $maliciousScript    = false;
        $path               = '../modules/404/lang/turkish.lang.php';

        if (is_file($path)) {
            if (is_writeable($path)) {
                @chmod($path, 0755);
                @unlink($path);

                if (is_file($path))
                    $maliciousScript = true;
            }
            else {
                $maliciousScript = true;
            }
        }

        if ($maliciousScript)
            $this->_view = new view('maliciousScript');
        else
            $this->_redirect('index.php?action=runProcess');
    }

    /*
     * Create or update all database table
     */
    public function runProcess() {
        $this->_view = new view('runProcess');

        $this->_view->process           = $this->_session['process'];
        $this->_view->processDataList   = $this->_getProcessDataList();
    }

    /*
     * Create or update a database table
     */
    public function runTableProcessAction() {
        $this->_session['db_type'] = 'MySQL';

        $this->_db = db::getInstance()->load($this->_session);

        $result = $error = '';

        try {
            if (! is_file($path = 'tables/'. $_POST['tableFile']))
                throw new fatalErrorException($this->_i18n['MISSING_FILE'] . $path);

            if (isset($_POST['dropTable']) && $_POST['dropTable'] == 'true')
                $process = 'drop';
            else if (isset($_POST['addForeignKeyOfTable']) && $_POST['addForeignKeyOfTable'] == 'true')
                $process = 'addForeignKey';
            else if (isset($_POST['checkIntegrity']) && $_POST['checkIntegrity'] == 'true')
                $process = 'checkIntegrity';
            else if (isset($_POST['checkAndConvertCharsetAndCollation'])
                && $_POST['checkAndConvertCharsetAndCollation'] == 'true')
                $process = 'checkAndConvertCharsetAndCollation';
            else if (isset($_POST['createTable']) && $_POST['createTable'] == 'true')
                $process = 'createTable';
            else
                $process = $this->_session['process'];

            global $dbTable, $db_prefix, $db;

            define('INDEX_CHECK', true);

            $db_prefix = $this->_session['db_prefix'];
            $db        = $this->_db;
            $dbTable   = new dbTable($this->_db, $this->_session, $this->_i18n);

            $dbTable->setDataName($_POST['tableFile']);

            require_once '../Includes/constants.php';
            include $path;

            $result = $dbTable->getJqueryAjaxResponse();
        }
        catch (processException $e) {
            if (get_class($e) == 'dbException')
                $error = $this->_formatSqlError($e->getMessage());
            else
                $error = $e->getMessage();
        }

        echo '#', $dbTable->getTableName(), '#';

        if ($result != '')
            echo $result, '#';

        if ($error != '')
            echo $error, '<br />';

        if ($process == 'update' && $result != 'CREATED' && isset($dbTable)) {
            $actionList = $dbTable->getActionList();

            if (! empty($actionList)) {
                foreach ($actionList as $k => $i18n)
                    echo $i18n, '<br />';
            }
        }
    }

    /*
     * Display form for create administrator
     */
    public function setAdministrator() {
        $this->_session['administrator'] = 'IN_PROGRESS';

        $this->_view = new view('setAdministrator');
    }

    /*
     * Check administrator data and save it, generate conf.inc file for install process
     */
    public function saveAdministrator() {
        if (isset($this->_session['_POST']))
            $_POST = $this->_session['_POST'];

        if ($this->_checkAdministratorForm()) {
            if (! isset($this->_session['defaultContent'])) {
                $this->_writeDefaultContent($_POST['nickname'], $_POST['password'], $_POST['email']);

                $this->_session['defaultContent'] = true;
            }

            $this->_saveConfInc();

            $this->_session['administrator'] = 'FINISH';

            if (isset($this->_session['_POST']))
                unset($this->_session['_POST']);

            $this->_redirect('index.php?action=cleaningFiles');
        }
    }

    /*
     * Update conf.inc.php file
     */
    public function updateDbConfiguration() {
        $this->_db = db::getInstance()->load($this->_session);

        $sql = 'UPDATE `'. $this->_session['db_prefix'] .'_config`
            SET value = \''. $this->_nkVersion .'\'
            WHERE name = \'version\'';

        $this->_db->execute($sql, array('exception' => 'DB_PREFIX_ERROR'));

        $this->_saveConfInc();

        $this->_redirect('index.php?action=cleaningFiles');
    }

    /*
     * Check to cleaning deprecated file in Nuked-Klan directory
     */
    public function cleaningFiles() {
        $deprecatedFiles = array();

        foreach ($this->_deprecatedFiles as $k => $file) {
            if (is_file($file)) {
                @unlink('../'. $file);
                clearstatcache();

                if (is_file('../'. $file))
                    $deprecatedFiles[] = $file;
            }
            else if (is_dir($file)) {
                $this->_deleteDirectory('../'. $file);
                clearstatcache();

                if (is_dir('../'. $file))
                    $deprecatedFiles[] = $file;
            }
        }

        if (! empty($deprecatedFiles)) {
            $this->_view = new view('cleaningFiles');

            $this->_view->deprecatedFiles = $this->_deprecatedFiles;
        }
        else
            $this->_redirect('index.php?action=processSuccess');
    }

    /*
     * Send conf.inc.php file to web browser
     */
    public function printConfig() {
        header('Content-disposition:filename=conf.inc.php');
        header('Content-type:application/octetstream');

        if (isset($this->_session['confIncContent']))
            echo $this->_session['confIncContent'];
    }

    /*
     * Display install / update success message
     */
    public function processSuccess() {
        $this->_session['administrator'] = 'FINISH';

        $this->_view = new view('processSuccess');
    }

    /*
     * Display partners logo & link
     */
    public function getPartners() {
        $content = @file_get_contents('http://www.nuked-klan.org/extra/partners.php?key='. $this->_partnersKey);
        $content = @unserialize($content);

        $view = new view('getPartners');

        $view->i18n     = $this->_i18n;
        $view->content  = (! is_array($content)) ? array() : $content;

        echo $view;
    }

    /*
     * Reset PHP session and restart process (install /update)
     */
    public function resetSession() {
        $this->_session->stop();
        $this->_redirect('index.php');
    }

    /*
     * Delete PHP session and redirect to website
     */
    public function deleteSession() {
        $this->_session->stop();

        if (! DEVEL_MODE)
            $this->_deleteDirectory('../INSTALL');

        $this->_redirect('../index.php');
    }

    /*
     * Display javascript i18n file
     */
    public function printJsI18nFile() {
        $language = $this->_i18n->getLanguage();

        header('Content-Type:text/html;charset=ISO-8859-1');
        header('Content-type:application/javascript', false);

        if (isset($this->_session['jsI18n_'. $language]))
            echo $this->_session['jsI18n_'. $language];
        else
            echo ($this->_session['jsI18n_'. $language] = $this->_i18n->generateI18nJsContent());
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Service methods, called to perform a specific task
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Load editable configuration
     */
    private function _loadConfiguration() {
        $configuration = new processConfiguration($this->_session);

        foreach ($configuration->get() as $k => $v)
            $this->{'_'. $k} = $v;
    }

    /*
     * Translate and return Sql error
     */
    private function _formatSqlError($error) {
        $sqlErrorlist = array(
            'DB_HOST_CONNECT_ERROR',
            'DB_USER_CONNECT_ERROR',
            'DB_NAME_CONNECT_ERROR',
            'DB_CHARSET_CONNECT_ERROR',
            'DB_PREFIX_CONNECT_ERROR'
        );

        if (in_array($error, $sqlErrorlist)) {
            if ($error == 'DB_HOST_CONNECT_ERROR')
                $errorMsg = sprintf($this->_i18n['DB_HOST_CONNECT_ERROR'], $this->_session['db_type']);
            else if ($error == 'DB_CHARSET_CONNECT_ERROR')
                $errorMsg = sprintf($this->_i18n['DB_CHARSET_CONNECT_ERROR'], $this->_db->getCharset());
            else
                $errorMsg = $this->_i18n[$error];

            return $this->_i18n['DB_CONNECT_FAIL'] .'<br/>'. $errorMsg;
        }
        else
            return sprintf($this->_i18n['FATAL_SQL_ERROR'], $error);
    }

    /*
     * Check PHP version, required and optional PHP extension and FTP file permissions (writable)
     */
    private function _requirements() {
        $requirements = array(
            'PHP_VERSION' => (version_compare(PHP_VERSION, $this->_minimalPhpVersion)) ? 'enabled' : 'required-disabled'
        );

        foreach ($this->_phpExtension as $extensionName => $requirement) {
            if (extension_loaded($extensionName))
                $requirements[strtoupper($extensionName) .'_EXT'] = 'enabled';
            else
                $requirements[strtoupper($extensionName) .'_EXT'] = $requirement .'-disabled';
        }

        $requirements['CHMOD_TEST'] = array();

        $path = realpath('../');
        @chmod($path, 0755);

        if (! is_writable($path))
            $requirements['CHMOD_TEST']['WEBSITE_DIRECTORY'] = 'optional-disabled';

        @chmod(realpath('../upload'), 0755);

        foreach ($this->_uploadDir as $uploadDir) {
            $path = realpath('../'. $uploadDir);
            @chmod($path, 0755);

            if (! is_writable($path))
                $requirements['CHMOD_TEST'][$uploadDir] = 'optional-disabled';
        }

        return $requirements;
    }

    /*
     * Check database configuration form values.
     */
    private function _checkDbConfigurationForm() {
        $errors = array();

        if (! isset($_POST['db_host']) || trim($_POST['db_host']) == '')
            $errors[] = sprintf($this->_i18n['DB_HOST_ERROR'], $this->_session['db_type']);

        if (! isset($_POST['db_user']) || trim($_POST['db_user']) == '')
            $errors[] = $this->_i18n['DB_USER_ERROR'];

        if (! isset($_POST['db_pass']) || ($_POST['db_user'] != 'root' && trim($_POST['db_pass']) == ''))
            $errors[] = $this->_i18n['DB_PASSWORD_ERROR'];

        if (! isset($_POST['db_name']) || trim($_POST['db_name']) == '')
            $errors[] = $this->_i18n['DB_NAME_ERROR'];

        //if (! isset($_POST['db_type']) || ! in_array($_POST['db_type'], db::getDatabaseTypeList()))
        //    $errors[] = sprintf($i18n['UNKNOW_DATABASE_TYPE'], $_POST['db_type']);

        //if (isset($_POST['db_port']) $_POST['db_type'] = trim($_POST['db_type']);

        //if (! isset($_POST['db_port'])
        //    || ($_POST['db_type'] != '' && ! ctype_digit($_POST['db_port']) || $_POST['db_port'] > 65535)
        //)
        //    $errors[] = $this->_i18n['DB_PORT_ERROR'];

        //if (isset($_POST['db_persistent']) $_POST['db_persistent']) == 'true')
        //    $_POST['db_persistent'] = true;
        //else
        //    $_POST['db_persistent'] = false;

        if (! isset($_POST['db_prefix']) || trim($_POST['db_prefix']) == '')
            $errors[] = $this->_i18n['DB_PREFIX_ERROR'];

        if (($connectError = $this->dbConnectTest(true)) != 'OK')
            $errors[] = $connectError;

        if ($errors) {
            $this->_view = new view('formError');

            $type = ($this->_session['assist'] == 'yes') ? 'ASSIST' : 'SPEED';

            if ($this->_session['process'] == 'install')
                $this->_view->title = $this->_i18n['INSTALL_'. $type];
            else
                $this->_view->title = $this->_i18n['UPDATE_'. $type];

            $this->_view->errors   = $errors;
            $this->_view->backLink = 'index.php?action=setDbConfiguration';

            return false;
        }

        return true;
    }

    /*
     * Generate and return table data list for install / update
     */
    private function _getProcessDataList() {
        $result = array(
            'processList'         => array(),
            'tableWithForeignKey' => array()
        );

        if ($this->_session['process'] == 'update')
            $result['checkIntegrity'] = array();

        $processFirstChr = substr($this->_session['process'], 0, 1);

        foreach (array_diff(scandir('tables'), array('.', '..')) as $tableFile) {
            $tableFileData = explode('.', $tableFile);

            if (is_file('tables/'. $tableFile)
                && $tableFileData[0] == 'table'
                && array_pop($tableFileData) == 'php'
            ) {
                if (in_array($processFirstChr, $tableFileData))
                    $result['processList'][] = $tableFile;

                if (in_array('fk', $tableFileData))
                    $result['tableWithForeignKey'][] = $tableFile;

                if ($this->_session['process'] == 'update' ) {
                    if (in_array('c', $tableFileData))
                        $result['checkIntegrity'][] = $tableFile;

                    if (in_array('i', $tableFileData))
                        $result['checkAndConvertCharsetAndCollation'][] = $tableFile;
                }
            }
        }

        return $result;
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
     * Check administrator form values.
     */
    private function _checkAdministratorForm() {
        $errors = array();

        if (! isset($_POST['nickname'])
            || strlen($_POST['nickname']) < 3
            || preg_match('`[\$\^\(\)\'"?%#<>,;:]`', $_POST['nickname'])
        )
            $errors[] = $this->_i18n['ERROR_NICKNAME'];

        if (! isset($_POST['password']) || trim($_POST['password']) == '')
            $errors[] = $this->_i18n['ERROR_PASSWORD'];

        if (! isset($_POST['passwordConfirm']) || $_POST['password'] != $_POST['passwordConfirm'])
            $errors[] = $this->_i18n['ERROR_PASSWORD_CONFIRM'];

        if (! isset($_POST['email'])
            || ! preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/', $_POST['email'])
        )
            $errors[] = $this->_i18n['ERROR_EMAIL'];

        if ($errors) {
            $this->_view = new view('formError');

            $this->_view->title    = $this->_i18n['CREATE_ADMINISTRATOR'];
            $this->_view->errors   = $errors;
            $this->_view->backLink = 'index.php?action=setAdministrator';

            return false;
        }

        return true;
    }

    /*
     * Write demo content in database
     */
    private function _writeDefaultContent($nickname, $password, $email) {
        $this->_db = db::getInstance()->load($this->_session);

        $nickname   = $this->_db->quote($nickname);
        $password   = $this->_db->quote(hash::apply($this->_session['HASHKEY'] , $password));
        $email      = $this->_db->quote($email);
        $date       = time();
        $userId     = $this->_generateUserId();
        $ip         = $this->_db->quote($_SERVER['REMOTE_ADDR']);

        $this->_db->truncateTableWithForeignKeyReferences($this->_session['db_prefix'] .'_users');

        $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_users`
            (`id`, `pseudo`, `mail`, `pass`, `niveau`, `date`, `game`, `country`, `token_time`) VALUES
            (\''. $userId .'\', \''. $nickname .'\', \''. $email .'\', \''. $password .'\', 9, \''. $date .'\', 1, \'France.gif\', \'0\')';

        $this->_db->execute($sql);

        $firstNewsTitle = $this->_db->quote(sprintf($this->_i18n['FIRST_NEWS_TITLE'], $this->_nkVersion));

        $sql = 'TRUNCATE TABLE `'. $this->_session['db_prefix'] .'_news`';

        $this->_db->execute($sql);

        $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_news`
            (`cat`, `titre`, `auteur`, `auteur_id`, `texte`, `coverage`, `date`) VALUES
            (1, \''. $firstNewsTitle .'\', \''. $nickname .'\', \''. $userId .'\', \''. $this->_db->quote($this->_i18n['FIRST_NEWS_CONTENT']) .'\', \'images/coverage.jpg\', \''. $date .'\')';

        $this->_db->execute($sql);

        $sql = 'TRUNCATE TABLE `'. $this->_session['db_prefix'] .'_shoutbox`';

        $this->_db->execute($sql);

        $sql = 'INSERT INTO `'. $this->_session['db_prefix'] .'_shoutbox`
            (`auteur`, `ip`, `texte`, `date`) VALUES
            (\''. $nickname .'\', \''. $ip .'\', \''. $firstNewsTitle .'\', \''. $date .'\')';

        $this->_db->execute($sql);

        $sql = 'UPDATE `'. $this->_session['db_prefix'] .'_config`
            SET value = \''. $email .'\'
            WHERE name = \'contact_mail\' OR name = \'mail\'';

        $this->_db->execute($sql);
    }

    /*
     * Create or update conf.inc file
     */
    private function _saveConfInc() {
        $cfg = new confInc;

        $global = array();

        foreach (confInc::getDbDataConfig() as $k) {
            if (isset($this->_session[$k]))
                $global[$k] = $this->_session[$k];
        }

        $cfg->setData(array(
            'nk_version'    => $this->_nkVersion,
            'global'        => $global,
            'db_prefix'     => $this->_session['db_prefix'],
            'HASHKEY'       => $this->_session['HASHKEY']
        ));

        return $cfg->save();
    }

    /*
     * Delete a directory
     */
    private function _deleteDirectory($path) {
        foreach (array_diff(scandir($path), array('.', '..')) as $deletedFile) {
            if (is_dir($path .'/'. $deletedFile))
                $this->_deleteDirectory($path .'/'. $deletedFile);
            else if (is_file($path .'/'. $deletedFile))
                @unlink($path .'/'. $deletedFile);
        }

        @unlink($path);
    }

    /*
     * Send header to execute a redirection
     */
    private function _redirect($url) {
        header('Location: '. $url);
        exit;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Display methods known to perform layout
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    /*
     * Set old and current action
     */
    private function _setAction($action) {
        if (in_array($_GET['action'],
            array('createBackupDb', 'dbConnectTest', 'runTableProcessAction', 'printConfig', 'getPartners', 'printJsI18nFile'))
        )
            return;

        if (! isset($this->_session['oldAction']))
            $this->_session['oldAction'] = '';
        else {
            if ($this->_session['currentAction'] != $action)
                $this->_session['oldAction'] = $this->_session['currentAction'];
        }

        $this->_session['currentAction'] = $action;
    }

    /*
     * Run install / update process
     */
    public function run() {
        try {
            $action = isset($_GET['action']) ? $_GET['action'] : '';

            if (method_exists($this, $action) && (isset($_REQUEST['language']) || isset($this->_session['language']))) {
                $this->_setAction($action);
                $this->{$action}();
            }
            else if ($action != 'printJsI18nFile') {
                $action = 'selectLanguage';
                $this->selectLanguage();
            }
        }
        catch (processException $e) {
            $exceptionName = get_class($e);

            if ($exceptionName == 'confIncException')
                $this->_view = new view('confIncFailure');
            else
                $this->_view = new view('fatalError');

            $this->_view->oldAction     = isset($this->_session['oldAction']) ? $this->_session['oldAction'] : '';
            $this->_view->currentAction = isset($this->_session['currentAction']) ? $this->_session['currentAction'] : '';

            if (in_array($exceptionName, array('fatalErrorException', 'confIncException')))
                $this->_view->error = $e->getMessage();
            else if ($exceptionName == 'dbException')
                $this->_view->error = $this->_formatSqlError($e->getMessage());

            if (! empty($_POST))
                $this->_session['_POST'] = $_POST;
        }

        if (get_class($this->_view) == 'view') {
            $view = new view('fullPage');

            $view->i18n = $this->_view->i18n = $this->_i18n;
            $view->language = $this->_view->language = $this->_i18n->getLanguage();
            $view->processVersion   = $this->_nkVersion;
            $view->navigation       = $this->_navigation;
            $view->session          = $this->_session;
            $view->action           = $action;
            $view->content          = $this->_view;
            $view->infoList         = $this->_infoList;

            echo $view;
        }
    }

}

?>