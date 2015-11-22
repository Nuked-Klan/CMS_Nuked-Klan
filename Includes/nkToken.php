<?php
/**
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


/**
 * Genererate a admin token.
 *
 * @param string $name : Name of admin token.
 * @return string : The admin token generated.
 */
function nkToken_generate($tokenName = '') {
    $token = sha1(rand()) . sha1(uniqid(rand(), true));

    $_SESSION[$tokenName .'_token']         = $token;
    $_SESSION[$tokenName .'_token_time']    = time();
    $_SESSION[$tokenName .'_token_url']     = 'http://'. $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    return $token;
}

/**
 * Check a admin token.
 *
 * @param int $duration : Duration of admin token.
 * @param array $referer_data : Referer url to compare.
 * @param string $name : Name of admin token.
 * @return bool : The result of admin token validation
 */
function nkToken_valid($tokenName = '', $duration, $refererData) {
    if (isset(
            $_SESSION[$tokenName .'_token'],
            $_SESSION[$tokenName .'_token_time'],
            $_SESSION[$tokenName .'_token_url'],
            $_POST['token']
        )
        && $_SESSION[$tokenName .'_token'] == $_POST['token']
        && is_array($refererData)
    ) {
        foreach ($refererData as $referer) {
            if ($_SESSION[$tokenName .'_token_time'] >= time() - $duration
                && strpos($_SESSION[$tokenName .'_token_url'], $referer) === 0
            )
                return true;
        }
    }

    return false;
}

?>