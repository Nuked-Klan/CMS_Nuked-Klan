<?php
/**
 * confInc.class.php
 *
 * Manage conf.inc.php file
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

class confInc {

    /*
     * Sets config database data list
     */
    static private $_dbDataConfig = array('db_host', 'db_user', 'db_pass', 'db_name');

    /*
     * Sets to create copy of conf.inc or not
     */
    private $_copy = false;

    /*
     * Sets conf.inc data
     */
    private $_data = array();

    /*
     * Check if config data must be updated or not
     */
    static public function isUpdatable() {
        include '../conf.inc.php';

        foreach (self::$_dbDataConfig as $key)
            if (! array_key_exists($key, $global))
                return true;

        return false;
    }

    /*
     * Return config database data list
     */
    static public function getDbDataConfig() {
        return self::$_dbDataConfig;
    }

    /*
     * Sets conf.inc data
     */
    public function setData($data) {
        $this->_data = $data;
    }

    /*
     * Edit conf.inc file to close website
     */
    public function closeWebsite() {
        $session = PHPSession::getInstance();

        include_once '../conf.inc.php';

        if (isset($nk_version))
            $this->_data['nk_version'] = $nk_version;

        $this->_data['global']              = $global;
        $this->_data['db_prefix']           = $db_prefix;
        $this->_data['NK_INSTALLED']        = 'true';
        $this->_data['HASHKEY']             = HASHKEY;
        $this->_data['NK_OPEN']             = 'false';

        $session['confIncContent'] = $this->_getContent();

        $this->_write($session['confIncContent']);
    }

    /*
     * Create or update conf.inc file
     */
    public function save() {
        $session = PHPSession::getInstance();

        $this->_data['NK_OPEN'] = $this->_data['NK_INSTALLED'] = 'true';
        $session['confIncContent'] = $this->_getContent();
        $this->_copy = true;

        $this->_write($session['confIncContent']);
    }

    /*
     * Generate content of conf.inc file
     */
    private function _getContent() {
        if (@extension_loaded('zlib')
            && ! @ini_get('zlib.output_compression')
            && stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
        )
            $gzipCompress = 'true';
        else
            $gzipCompress = 'false';

        $content = '<?php' ."\n"
            . '//-------------------------------------------------------------------------//' ."\n"
            . '//  Nuked-KlaN - PHP Portal                                                //' ."\n"
            . '//  http://www.nuked-klan.org                                              //' ."\n"
            . '//-------------------------------------------------------------------------//' ."\n"
            . '//  This program is free software. you can redistribute it and/or modify   //' ."\n"
            . '//  it under the terms of the GNU General Public License as published by   //' ."\n"
            . '//  the Free Software Foundation; either version 2 of the License.         //' ."\n"
            . '//-------------------------------------------------------------------------//' ."\n\n";

        if (array_key_exists('nk_version', $this->_data))
            $content .= '$nk_version = \''. $this->_data['nk_version']  .'\';' ."\n\n";

        foreach ($this->_data['global'] as $k => $v)
            $content .= '$global[\''. $k .'\'] = \''. $v .'\';' ."\n";

        $content .= '$db_prefix = \''. $this->_data['db_prefix'] .'\';' ."\n\n"
            . 'define(\'NK_INSTALLED\', '. $this->_data['NK_INSTALLED'] .');' ."\n"
            . 'define(\'NK_OPEN\', '. $this->_data['NK_OPEN'] .');' ."\n"
            . 'define(\'NK_GZIP\', '. $gzipCompress .');' ."\n"
            . '// NE PAS SUPPRIMER! / DO NOT DELETE' ."\n"
            . 'define(\'HASHKEY\', \''. $this->_data['HASHKEY'] .'\');' ."\n\n"
            . '?>';

        return $content;
    }

    /*
     * Write conf.inc file
     */
    private function _write($content) {
        @chmod('../', 0755);

        if (is_file('../conf.inc.php')) {
            @chmod('../conf.inc.php', 0666);

            if (! is_writable('../conf.inc.php'))
                throw new confIncException('CONF_INC_CHMOD_0666');
        }
        else {
            if (! is_writable('../'))
                throw new confIncException('WEBSITE_DIRECTORY_CHMOD');
        }

        if (false === file_put_contents('../conf.inc.php', $content))
            throw new confIncException('WRITE_CONF_INC_ERROR');

        if (! @chmod('../conf.inc.php', 0644))
            throw new confIncException('CONF_INC_CHMOD_0644');

        if ($this->_copy && ! @copy('../conf.inc.php', '../config_save_'. date('Y-m-d-H-i') .'.php'))
            throw new confIncException('COPY_CONF_INC_ERROR');
    }

}

?>