<?php
// PHP ERROR NK
if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0,2) == 'fr'){
    define('ERROR_SESSION', 'Erreur dans la création de la session anonyme');
    define('THEME_NOTFOUND','Erreur fatale : Impossible de trouver le thème');
    define('ERROR_QUERY','Veuillez nous excuser, le site web est actuellement indisponible !<br />Information :<br />Connexion SQL impossible.');
    define('ERROR_QUERYDB','Veuillez nous excuser, le site web est actuellement indisponible !<br />Information :<br />Nom de base de données sql incorrect.');
	define('ERROR_SQL', '<b>Une erreur SQL a été détectée.<br /><br />Information :<br /><br />Mon ERREUR</b> [' . $errno . '] ' . $errstr . '<br />Erreur fatale sur la ligne ' . $errline . ' dans le fichier ' . $errfile . ', PHP ' . PHP_VERSION . ' (' . PHP_OS . ')<br />Arrêt...<br />');
}
else{
    define('ERROR_SESSION', 'Error in creating the anonymous session');
    define('THEME_NOTFOUND','Fatal error: No theme found');
    define('ERROR_QUERY','Sorry but the website is not available !<br />Information :<br />SQL connection impossible.');
    define('ERROR_QUERYDB','Sorry but the website is not available !<br />Information :<br />Database SQL name incorrect.');
	define('ERROR_SQL', '<b>A SQL error has been detected.<br /><br />Information:<br /><br />My ERREUR</b> [' . $errno . '] ' . $errstr . '<br />Fatal error on the line ' . $errline . ', file ' . $errfile . ', PHP ' . PHP_VERSION . ' (' . PHP_OS . ')<br />Stop ...<br />');

}
?>