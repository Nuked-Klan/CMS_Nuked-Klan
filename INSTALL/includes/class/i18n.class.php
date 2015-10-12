<?php
/**
 * i18n.class.php
 *
 * Manage internationalization
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

class i18n implements ArrayAccess {

    /*
     * Sets language used
     */
    private $_language = 'english';

    /*
     * Sets language translation data
     */
    private $_i18n = array();

    /*
     * Store instance of i18n class
     */
    static private $_instance;

    /*
     * Constructor
     * - Detect language
     * - Load language file
     */
    private function __construct() {
        $this->_language = $this->_detectLanguage();

        $this->_i18n = include 'lang/'. $this->_language .'.lang.php';

        $this->setLocale();
    }

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
     * Generate language list and return it.
     */
    public function getLanguageList() {
        $languageList = array();

        if (! is_dir('lang')) {
            if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'fr')
                throw new Exception('Dossier INSTALL/lang manquant');
            else
                throw new Exception('Missing INSTALL/lang directory');
        }

        foreach (array_diff(scandir('lang'), array('.', '..')) as $languageFile) {
            $languageFileData = explode('.', $languageFile);

            if (is_file('lang/'. $languageFile)
                && $languageFileData[1] == 'lang'
                && $languageFileData[2] == 'php'
            )
                $languageList[] = $languageFileData[0];
        }

        if (empty($languageList)) {
            if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'fr')
                throw new Exception('Dossier INSTALL/lang vide');
            else
                throw new Exception('Empty INSTALL/lang directory');
        }

        return $languageList;
    }

    /*
     * Detect language and return it
     */
    private function _detectLanguage() {
        if (isset($_GET['language']) && is_file('lang/'. $_GET['language'] .'.lang.php'))
            return $_GET['language'];

        $session = PHPSession::getInstance();

        if (isset($session['language']))
            return $session['language'];

        return $this->_getWebBrowserLanguage();
    }

    /*
     * Detect web browser used language and return it
     * see : http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     */
    private function _getWebBrowserLanguage() {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && ! empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $navigatorLanguageList = array_flip($this->getLanguageList());

            foreach ($navigatorLanguageList as $k => &$v)
                $v = substr($k, 0, 2);

            $navigatorLanguageList = array_flip($navigatorLanguageList);

            foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $rawLanguageData) {
                $languageData       = explode(';', $rawLanguageData);
                $preferredLanguage  = substr(array_shift($languageData), 0, 2);

                if (is_file('lang/'. $navigatorLanguageList[$preferredLanguage] .'.lang.php'))
                    return $navigatorLanguageList[$preferredLanguage];
            }
            die;
        }

        return 'english';
    }

    /*
     * Return language used
     */
    public function getLanguage() {
        return $this->_language;
    }

    /*
     * Generate Javasctipt internationalization file content
     */
    public function generateI18nJsContent() {
        $data = array();

        foreach ($this->_i18n as $k => $v)
            $data[] = strtolower($k) .':\''. str_replace('\'', '\\\'', $v) .'\'';

        return 'var language = \''. $this->_language .'\', i18n = {'. implode(', ', $data) .'};';
    }

    /**
     * Set locale following the Operating System.
     * @ see : https://msdn.microsoft.com/en-us/library/cdax410z%28v=vs.90%29.aspx
     *         https://msdn.microsoft.com/en-us/library/39cwe7zf%28v=vs.90%29.aspx
     */
    private function setLocale() {
        if ($this->_language == 'french')
            setlocale (LC_ALL, 'fr_FR.iso88591', 'fr_FR@euro', 'fr_FR', 'french', 'fra');
        else if ($this->_language == 'english')
            setlocale (LC_ALL, 'en_GB.iso88591', 'en_GB', 'english', 'eng');
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Methods of ArrayAccess interface
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function offsetSet($offset, $value) {
        $this->_i18n[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->_i18n[$offset]);
    }

    public function offsetUnset($offset) {
        throw new Exception('You cannot delete language data');
    }

    public function offsetGet($offset) {
        return isset($this->_i18n[$offset]) ? $this->_i18n[$offset] : $offset;
    }

}

?>