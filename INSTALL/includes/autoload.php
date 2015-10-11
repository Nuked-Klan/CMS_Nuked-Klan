<?php

define('CLASS_EXT', '.class.php');

/*
 * Autoloading class for install / update process
 */
function processAutoload($className) {
    $classDir = 'includes/class/';

    if (strrpos($className, 'Exception') !== false)
        $classDir .= 'exception/';

    if (! is_file($classFile = $classDir . $className . CLASS_EXT))
        throw new Exception(__FUNCTION__ .' : '. $classFile .' file of '. $className .' don\'t exist !');

    include_once $classFile;
}

spl_autoload_register('processAutoload');
spl_autoload_extensions(CLASS_EXT);

?>