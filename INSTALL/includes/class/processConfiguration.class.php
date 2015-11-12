<?php
/**
 * processConfiguration.class.php
 *
 * Manage process configuration
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

class processConfiguration {

    /*
     * Store process configuration
     */
    private $_configuration = array();

    /*
     * Constructor
     * - Check if configuration file exist
     * - Load it
     */
    public function __construct($session) {
        if (! is_file('config.php')) {
            $i18n = i18n::getInstance();
            throw new Exception(sprintf($i18n['MISSING_FILE'], 'INSTALL/config.php'));
        }

        $this->_load($session);
    }

    /*
     * Load configuration data
     */
    private function _load($session) {
        if (isset($session['configuration']) && is_array($session['configuration'])) {
            $this->_configuration = $session['configuration'];
        }
        else {
            $this->_configuration = include 'config.php';

            $this->_check();
        }
    }

    /*
     * Return configuration data
     */
    public function get() {
        return $this->_configuration;
    }

    /*
     * Check configuration data
     */
    private function _check() {
        $cfgStrKey      = array('nkVersion', 'nkMinimumVersion', 'minimalPhpVersion', 'partnersKey');
        $cfgArrayKey    = array('phpExtension', 'uploadDir', 'changelog', 'infoList', 'deprecatedFiles');
        $i18n           = i18n::getInstance();

        foreach (array_merge($cfgStrKey, $cfgArrayKey) as $cfgKey) {
            if (! array_key_exists($cfgKey, $this->_configuration)) {
                throw new Exception(sprintf($i18n['MISSING_CONFIG_KEY'], $cfgKey));
            }

            if (in_array($cfgKey, $cfgStrKey) && (! is_string($this->_configuration[$cfgKey]) || empty($this->_configuration[$cfgKey]))) {
                throw new Exception(sprintf($i18n['CONFIG_KEY_MUST_BE_STRING'], $cfgKey));
            } elseif (in_array($cfgKey, $cfgArrayKey) && (! is_array($this->_configuration[$cfgKey]) || empty($this->_configuration[$cfgKey]))) {
                throw new Exception(sprintf($i18n['CONFIG_KEY_MUST_BE_ARRAY'], $cfgKey));
            }
        }
    }
}

?>