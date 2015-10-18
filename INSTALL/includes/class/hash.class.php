<?php
/**
 * hash.class.php
 *
 * Manage hash
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

class hash {

    /*
     * Generate random hash key and return it
     */
    public static function generate() {
        $charPool = range(32, 255);
        unset($charPool[2], $charPool[7], $charPool[60]);

        $charPool   = array_values(array_map('chr', $charPool));
        $poolLength = count($charPool) - 1;
        $hashKey    = '';

        for ($n = 0; $n < 20; $n++)
            $hashKey .= $charPool[mt_rand(0, $poolLength)];

        return $hashKey;
    }

    /*
     * Return hashed password
     */
    public static function apply($hashKey, $password, $offset = null) {
        $builder    = '';
        $offset      = ($offset === null) ? rand(0, 15) : $offset;
        $password   = sha1($password);
        $passLength = strlen($password) * 2;

        for ($i = 0; $i < $passLength; $i++) {
            if ($i % 2 == 0)
                $builder .= $password[$i / 2];
            else
                $builder .= substr($hashKey, ($i / 2 + $offset) % 20, 1);
        }

        return '#'. dechex($offset) . md5($builder);
    }

}

?>