<?php
/**
 * nkDebug.php
 *
 * Write PHP errors log. Add sql errors in Javascript console of web browser.
 * Only for development.
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

register_shutdown_function('nkDebug_writeErrorLog', dirname(__FILE__) .'/../');
set_error_handler('nkDebug_errorHandler', E_ALL);

$GLOBALS['nk_error'] = array();


/**
 * Sets a user function (error_handler ) to handle errors in a script.
 * Save Error in a global var.
 *
 * @param int $no : Contains the level of the error raised.
 * @param string $str : Contains the error message.
 * @param string $file : Contains the filename that the error was raised in.
 * @param string $line : Contains the line number the error was raised at.
 * @return void
 */
function nkDebug_errorHandler($no, $str, $file, $line) {
    $GLOBALS['nk_error'][] = nkDebug_getErrorName($no) .': '. strip_tags($str) .' in '. $file .' on line '. $line;

    return false;
}

/**
 * Return the name of the error code.
 *
 * @param int $no : Contains the level of the error raised.
 * @return string : The name of the error code.
 */
function nkDebug_getErrorName($no) {
    $errorCode = array(
        1       => 'E_ERROR',
        2       => 'E_WARNING',
        4       => 'E_PARSE',
        8       => 'E_NOTICE',
        16      => 'E_CORE_ERROR',
        32      => 'E_CORE_WARNING',
        64      => 'E_COMPILE_ERROR',
        128     => 'E_COMPILE_WARNING',
        256     => 'E_USER_ERROR',
        512     => 'E_USER_WARNING',
        1024    => 'E_USER_NOTICE',
        2047    => 'E_ALL',
        2048    => 'E_STRICT',
        4096    => 'E_RECOVERABLE_ERROR',
        6143    => 'E_ALL',
        8192    => 'E_DEPRECATED',
        16384   => 'E_USER_DEPRECATED',
        30719   => 'E_ALL'
    );

    if (array_key_exists($no, $errorCode))
        return $errorCode[$no];

    return 'UNKNOW ERROR ('. $no .')';
}

/**
 * Write errors in log file.
 *
 * @param string $path : The root path of CMS directory.
 * @return void
 */
function nkDebug_writeErrorLog($path) {
    $content = '';

    if (is_file($path .'.git/refs/heads/develop_1.8'))
        $content .= 'Commit : '. file_get_contents($path .'.git/refs/heads/develop_1.8');

    $content .= utf8_decode(implode("\n", $GLOBALS['nk_error'])) ."\n\n";

    file_put_contents($path .'error.log', $content, FILE_APPEND);
}

/**
 * Write sql errors in Javascript console of web browser.
 *
 * @param void
 * @return void
 */
function nkDebug_sqlErrors() {
    foreach ($GLOBALS['nkDB']['status'] as $sqlQuery) {
        if ($sqlQuery[1] != 'ok') {
            echo '<script type="text/javascript">console.log(\''. addslashes('Query : '. str_replace(array("\n", "\r", "\t"), '', $sqlQuery[0])) .'\');</script>'
                . '<script type="text/javascript">console.log(\''. addslashes('Error : '. str_replace(array("\n", "\r", "\t"), '', $sqlQuery[1])) .'\');</script>'
                . '<script type="text/javascript">console.log("\n");</script>';
        }
    }
}

?>