<?php

/*
 * Generate or update config file (conf.inc.php)
 */
function saveConfig($process) {
    if (@extension_loaded('zlib')
        && ! @ini_get('zlib.output_compression')
        && stripos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
    )
        define('GZIP_COMPRESS', 'true');
    else
        define('GZIP_COMPRESS', 'false');

    $content = '<?php' ."\n"
                . '//-------------------------------------------------------------------------//' ."\n"
                . '//  Nuked-KlaN - PHP Portal                                                //' ."\n"
                . '//  http://www.nuked-klan.org                                              //' ."\n"
                . '//-------------------------------------------------------------------------//' ."\n"
                . '//  This program is free software. you can redistribute it and/or modify   //' ."\n"
                . '//  it under the terms of the GNU General Public License as published by   //' ."\n"
                . '//  the Free Software Foundation; either version 2 of the License.         //' ."\n"
                . '//-------------------------------------------------------------------------//' ."\n"
                . '$nk_version = \''. install::NK_VERSION .'\';' ."\n\n"
                . '$global[\'db_host\'] = \''. $_SESSION['host'] .'\';' ."\n"
                . '$global[\'db_user\'] = \''. $_SESSION['user'] .'\';' ."\n"
                . '$global[\'db_pass\'] = \''. $_SESSION['pass'] .'\';' ."\n"
                . '$global[\'db_name\'] = \''. $_SESSION['db_name'] .'\';' ."\n"
                . '$db_prefix = \''. $_SESSION['db_prefix'] .'\';' ."\n\n"
                . 'define(\'NK_INSTALLED\', true);' ."\n"
                . 'define(\'NK_OPEN\', true);' ."\n"
                . 'define(\'NK_GZIP\', '. GZIP_COMPRESS .');' ."\n"
                . '// NE PAS SUPPRIMER! / DO NOT DELETE' ."\n"
                . 'define(\'HASHKEY\', \''. $_SESSION['hash'] .'\');' ."\n\n"
                . '?>';

    $_SESSION['content'] = $content;

    $path = dirname(dirname(__FILE__)) .'/';
    @chmod($path.'conf.inc.php', 0666);
    @chmod($path, 0755);

    try {
        if (! (is_writable($path .'conf.inc.php') || (! is_file($path .'conf.inc.php') && is_writable($path))))
            throw new Exception('CHMOD');

        if (! @file_put_contents($path.'conf.inc.php', $content)) throw new Exception('CONF_INC');
        if (! @chmod($path.'conf.inc.php', 0644)) throw new Exception('CHMOD');
        if (! @copy($path.'conf.inc.php', $path .'config_save_'. date('Y-m-d-H-i') .'.php')) throw new Exception('COPY');

        return 'OK';
    }
    catch (exception $e) {
        $_SESSION['content_web'] = nl2br(htmlentities($content, ENT_COMPAT | ENT_HTML401, 'ISO-8859-1'));

        return $e->getMessage();
    }
}

?>