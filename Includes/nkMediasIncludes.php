<?php
/**
 * Media inclusions in Nuked-klan
 *
 * Include JS and CSS file from Mods and templates
 *
 * @version 1.7.10
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2015 Nuked-Klan (Registred Trademark)
 */

if (!defined("INDEX_CHECK")) exit('You can\'t run this file alone.');

function printMedias($jQuery = false){
    // Vérification des variables de request
    if(array_key_exists('file', $_REQUEST)){
        $file = $_REQUEST['file'];
    }
    else{
        $file = '';
    }

    // Vérification de la présence de marqueur de génération
    // Permet de ne pas recharger un css ou un js si un block le demande alors qu'ils ont déjà été chargés par le module
    if(!array_key_exists('mediaPrinted', $GLOBALS['nuked'])){
        // Si le marqueur existe on le stocke temporairement
        $GLOBALS['nuked']['mediaPrinted'] = array();
    }

    // Définition du chemin vers les fichiers de modules
    $pathJsMods = 'modules/'.$file.'/'.$file.'.js';
    $pathCssMods = 'modules/'.$file.'/'.$file.'.css';

    // Définition du chemin vers les fichiers du themes
    $pathJsTemplate = 'themes/'.$GLOBALS['theme'].'/js/modules/'.$file.'.js';
    $pathCssTemplate = 'themes/'.$GLOBALS['theme'].'/css/modules/'.$file.'.css';

    // Définition des chemins vers les fichiers par défaut
    $pathJsDefault = 'media/js/nkDefault.js';
    $pathCssDefault = 'media/css/nkDefault.css';

    // Définition des chemins vers les scripts JS plugins
    $arrayPathsPluginsJs = array(
                            'media/js/infobulle.js',
                            'media/js/syntaxhighlighter/shCore.js',
                            'media/js/syntaxhighlighter/shAutoloader.js',
                            'media/js/syntaxhighlighter.autoloader.js'
                            );

    // Définition des chemins vers les CSS plugins
    $arrayPathsPluginsCss = array(
                                'media/css/syntaxhighlighter/shCoreMonokai.css',
                                'media/css/syntaxhighlighter/shThemeMonokai.css'
                            );

    // On stocke les paths dans un ordre bien précis Plugins -> Default -> Mods -> Templates afin de permettre la surcharge des propriétés css
    $arrayMedias = array(
                    'CSS' => array($arrayPathsPluginsCss, $pathCssDefault, $pathCssMods, $pathCssTemplate),
                    'JS'  => array($arrayPathsPluginsJs, $pathJsDefault, $pathJsMods, $pathJsTemplate)
                );

    // On initialise la sortie
    $output = setBgColors();

    // On ajout le chargement de jquery avant les autres scripts
    if($jQuery === false){
        $output .= '<script type="text/javascript" src="media/js/jquery-min-1.8.3.js"></script>';
    }

    // On parcours le tableaux des paths et on génère la sortie html
    foreach($arrayMedias as $language => $paths){
        foreach($paths as $path){
            if(is_array($path)){
                foreach($path as $rowPath){
                    $output .= setPath($rowPath, $language);
                }
            }
            else{
                $output .= setPath($path, $language);
            }

        }
    }

    // On retourne la sortie pour affichage
    return $output;
}

function setBgColors(){
    // On définit les bgcolor par défaut s'il ne sont pas présent dans le thème
    $arrayDefaultColor = array(
        'bgcolor1' => '#666',
        'bgcolor2' => '#777',
        'bgcolor3' => '#444',
        'bgcolor4' => '#999'
    );

    // On check si les bgcolor on été défini sinon on les défini
    foreach ($arrayDefaultColor as $color => $value) {
        if(!isset($GLOBALS[$color])){
            $GLOBALS[$color] = $value;
        }
    }

    // On créer une balise style avec toutes les classes pour les bgcolors
    $output = '<style type="text/css">';

    for ($i = 1;$i <= 4;$i++){
        // On définit une classe pour une couleur de fond
        $output .= '.nkBgColor'.$i.'{background:'.$GLOBALS['bgcolor'.$i].';}'."\n";
        // On définit une class pour une couleur de bordure
        $output .= '.nkBorderColor'.$i.'{border-color:'.$GLOBALS['bgcolor'.$i].' !important;}'."\n";
    }

    $output .= '</style>';

    return $output;
}

function setPath($path, $language){
    if(file_exists($path)){
        if(!is_array($GLOBALS['nuked']['mediasPrinted']) || !in_array($path, $GLOBALS['nuked']['mediasPrinted'])){
            if($language == 'CSS'){
                return '<link rel="stylesheet" type="text/css" href="'.$path.'" />';
            }
            else if($language == 'JS'){
                return '<script type="text/javascript" src="'.$path.'"></script>';
            }
            $GLOBALS['nuked']['mediasPrinted'][] = $path;
        }
    }
    else{
        return null;
    }
}