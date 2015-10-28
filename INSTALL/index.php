<?php
/**
 * index.php
 *
 * Main script to launch process execution
 *
 * @version 1.7
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

ini_set('default_charset', 'ISO8859-1');

//error_reporting(E_ALL & ~E_DEPRECATED);
error_reporting (E_ERROR | E_WARNING | E_PARSE);

if (strpos($_SERVER['HTTP_HOST'], 'free.fr') !== false && ! is_dir($_SERVER['DOCUMENT_ROOT'] .'/sessions'))
    mkdir($_SERVER['DOCUMENT_ROOT'].'/sessions', 0700);

require_once 'includes/autoload.php';

$install = new process();
$install->run();

?>
