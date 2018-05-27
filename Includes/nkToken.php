<?php
/**
 * nkToken.php
 *
 * Protect form and url against CSRF exploit.
 *
 * @version     1.8
 * @link https://nuked-klan.fr Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * Genererate a token.
 *
 * @param string $name : Name of token.
 * @return string : A random token hash.
 */
function nkToken_generate($tokenName = '') {
    global $nuked;

    $token = sha1(rand()) . sha1(uniqid(rand(), true));

    $_SESSION[$tokenName .'_token']      = $token;
    $_SESSION[$tokenName .'_token_time'] = time();
    $_SESSION[$tokenName .'_token_url']  = $nuked['url'] .'/'. basename($_SERVER['REQUEST_URI']);

    return $token;
}

/**
 * Check a token.
 *
 * @param int $duration : Duration of token.
 * @param array $referer_data : Referer urls to compare.
 * @param string $name : Name of token.
 * @return bool : The result of token validation
 */
function nkToken_valid($tokenName = '', $duration, $refererData) {
    global $nuked;

    $request = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST : $_GET;

    $token     = $tokenName .'_token';
    $tokenTime = $tokenName .'_token_time';
    $tokenUrl  = $tokenName .'_token_url';

    if (isset(
            $_SESSION[$token],
            $_SESSION[$tokenTime],
            $_SESSION[$tokenUrl],
            $request['token']
        )
        && $_SESSION[$token] == $request['token']
        && is_array($refererData)
    ) {
        foreach ($refererData as $referer) {
            if ($_SESSION[$tokenTime] >= time() - $duration
                && strpos($_SESSION[$tokenUrl], $nuked['url'] .'/'. $referer) === 0
            )
                return true;
        }
    }

    return false;
}

?>
