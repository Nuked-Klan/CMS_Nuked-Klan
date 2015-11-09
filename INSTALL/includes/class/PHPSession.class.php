<?php
/**
 * PHPSession.class.php
 *
 * Manage PHP sessions
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

class PHPSession implements ArrayAccess, IteratorAggregate {

    /*
     * Store instance of PHPSession class
     */
    static private $_instance;

    /*
     * Constructor - Start PHP session, check it and store $_SESSION var
     */
    private function __construct() {
        if (session_id() == '') {
            session_name('installUpdateProcess');
            session_start();
        }

        if (session_id() == '') {
            if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'fr')
                throw new Exception('La session PHP ne peut être demarrée');
            else
                throw new Exception('PHP session cannot be start');
        }

        $this->_session = & $_SESSION;
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
     * Stop and destruct PHP session
     */
    public function stop() {
        $_SESSION = array();
        session_unset();
        session_destroy();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Methods of IteratorAggregate interface
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function getIterator() {
        return new ArrayIterator($this->_session);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Methods of ArrayAccess interface
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function offsetExists($offset) {
        return isset($this->_session[$offset]);
    }

    public function offsetGet($offset) {
        if ($this->offsetExists($offset))
            return $this->_session[$offset];

        return null;
    }

    public function offsetSet($offset, $value) {
        $this->_session[$offset] = $value;
    }

    public function offsetUnset($offset) {
        if ($this->offsetExists($offset))
            unset($this->_session[$offset]);
    }

}
