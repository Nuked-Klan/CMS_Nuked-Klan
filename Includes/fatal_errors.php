<?php
/**
 * fatal_errors.php
 *
 * Define fatal errors translation
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2016 Nuked-Klan (Registred Trademark)
 */
defined('INDEX_CHECK') or die('You can\'t run this file alone.');


if (substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'fr') {
    // globals.php
    define('WAYTODO', 'Qu\'essayez vous de faire ?');
    define('ID_MUST_INTEGRER', 'Erreur : %s doit être un nombre !');
    define('NO_UPLOAD_PHP_FILE', 'Uploader un fichier PHP n\'est pas autorisé !!');
    define('NO_UPLOAD_HTACCESS_FILE', 'Uploader un fichier HTACCESS n\'est pas autorisé !!');
    // nuked.php
    define('THEME_NOTFOUND','Erreur fatale : Impossible de trouver le thème');
    define('WEBSITE_CLOSED','Ce site est momentanément fermé, merci de réessayer plus tard');
    define('ERROR_QUERYDB','Veuillez nous excuser, le site web est actuellement indisponible !<br />Information :<br />Nom de base de données sql incorrect.');
    define('ERROR_QUERY','Veuillez nous excuser, le site web est actuellement indisponible !<br />Information :<br />Connexion SQL impossible.');
    define('DBPREFIX_ERROR', 'Impossible de se connecter à la base de données ! Vérifier que la variable $db_prefix du fichier conf.inc.php correspond au préfixe de vos tables.');
    // nkSessions.php
    define('ERROR_SESSION', 'Erreur dans la création de la session anonyme');

    //define('REMOVEINST', 'Veuillez supprimer vos fichiers d\'installation de modules ou de patchs (install.php ou update.php)');
}
else {
    // globals.php
    define('WAYTODO', 'What are you trying to do ?');
    define('ID_MUST_INTEGRER', 'Error : %s must be a number !');
    define('NO_UPLOAD_PHP_FILE', 'Upload a PHP file isn\'t autorised !!');
    define('NO_UPLOAD_HTACCESS_FILE', 'Upload a HTACCESS file isn\'t autorised !!');
    // nuked.php
    define('THEME_NOTFOUND','Fatal error : No theme found');
    define('WEBSITE_CLOSED', 'Sorry, this website is momently closed, Please try again later.');
    define('ERROR_QUERYDB','Sorry but the website is not available !<br />Information :<br />Database SQL name incorrect.');
    define('ERROR_QUERY','Sorry but the website is not available !<br />Information :<br />SQL connection impossible.');
    define('DBPREFIX_ERROR', 'Can\'t connect to the database ! Check that $db_prefix variable on conf.inc.php file match with your prefix tables.');
    // nkSessions.php
    define('ERROR_SESSION', 'Error in creating the anonymous session');

    //define('REMOVEINST', 'Please delete your installation files for modules or patches (install.php or update.php)');
}

?>