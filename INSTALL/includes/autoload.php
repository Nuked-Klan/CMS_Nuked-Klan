<?php
/**
 * autoload.php
 *
 * Autoloading class for install / update process
 *
 * @version 1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

define('CLASS_EXT', '.class.php');

/*
 * Autoloading function used by spl_autoload_register
 */
function processAutoload($className) {
    $classDir = 'includes/class/';

    if (strrpos($className, 'Exception') !== false)
        $classDir .= 'exception/';

    if (! is_file($classFile = $classDir . $className . CLASS_EXT)) {
        if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'fr')
            throw new Exception(__FUNCTION__ .' : Fichier '. $classFile .' de la classe '. $className .' n\'existe pas !');
        else
            throw new Exception(__FUNCTION__ .' : '. $classFile .' file of '. $className .' class don\'t exist !');
            
	}

    include_once $classFile;
}

spl_autoload_register('processAutoload');
spl_autoload_extensions(CLASS_EXT);

?>
