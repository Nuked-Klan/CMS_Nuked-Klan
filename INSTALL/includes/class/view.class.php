<?php
/**
 * view.class.php
 *
 * Manage view
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

class view {

    /*
     * Store view filename
     */
    private $_viewFile;

    /*
     * Store vars used in view
     */
    private $_vars = array();

    /*
     * Constructor
     * - Check if view file exist
     * - Set view filename
     */
    public function __construct($view) {
        if (! is_file($viewFile = 'views/'. $view .'.php')) {
            $i18n = i18n::getInstance();
            throw new Exception(sprintf($i18n['VIEW_NO_FOUND'], $viewFile));
        }

        $this->_viewFile = $viewFile;
    }

    /*
     * Set view vars
     */
    public function __set($key, $value) {
        $this->_vars[$key] = $value;
    }

    /*
     * Apply HTML view and return this content
     */
    public function __toString() {
        ob_start();
        extract($this->_vars);

        include $this->_viewFile;

        return ob_get_clean();
    }

}

?>