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
    // V�rification des variables de request
    if(array_key_exists('file', $_REQUEST)){
        $file = $_REQUEST['file'];
    }
    else{
        $file = '';
    }

    // V�rification de la pr�sence de marqueur de g�n�ration
    // Permet de ne pas recharger un css ou un js si un block le demande alors qu'ils ont d�j� �t� charg�s par le module
    if(array_key_exists('mediaPrinted', $GLOBALS['nuked'])){
        // Si le marqueur existe on le stocke temporairement
        $mediasPrinted = $GLOBALS['mediaPrinted'];
    }
    else{
        $mediasPrinted = array();
    }

    // D�finition du chemin vers les fichiers de modules
    $pathJsMods = 'modules/'.$file.'/'.$file.'.js';
    $pathCssMods = 'modules/'.$file.'/'.$file.'.css';

    // D�finition du chemin vers les fichiers du themes
    $pathJsTemplate = 'themes/'.$GLOBALS['theme'].'/js/modules/'.$file.'.js';
    $pathCssTemplate = 'themes/'.$GLOBALS['theme'].'/css/modules/'.$file.'.css';

    // D�finition des cheminds vers les fichiers par d�faut
    $pathJsDefault = 'media/js/nkDefault.js';
    $pathCssDefault = 'media/css/nkDefault.css';

    // On stocke les paths dans un ordre bien pr�cis Default -> Mods -> Templates afin de permettre la surcharge des propri�t�s css
    $arrayMedias = array(
                    'CSS' => array($pathCssDefault, $pathCssMods, $pathCssTemplate),
                    'JS'  => array($pathJsDefault, $pathJsMods, $pathJsTemplate)
                );

    // On initialise la sortie
    $output = null;

    // On ajout le chargement de jquery avant les autres scripts
    if($jQuery === false){
        $output = '<script type="text/javascript" src="media/js/jquery-min-1.8.3.js"></script>';
    }

    // On parcours le tableaux des paths et on g�n�re la sortie html
    foreach($arrayMedias as $language => $paths){
        foreach($paths as $path){
            if(file_exists($path)){
                if(!in_array($path, $mediasPrinted)){
                    if($language == 'CSS'){
                        $output .= '<link rel="stylesheet" type="text/css" href="'.$path.'" />';
                    }
                    else if($language == 'JS'){
                        $output .= '<script type="text/javascript" src="'.$path.'"></script>';
                    }
                    $GLOBALS['nuked']['mediasPrinted'][] = $path;
                }
            }
        }
    }

    // on ajoute les bgcolors
    $output .= setBgColors();

    // On retourne la sortie pour affichage
    return $output;
}

function setBgColors(){
    // On d�finit les bgcolor par d�faut s'il ne sont pas pr�sent dans le th�me
    $arrayDefaultColor = array(
        'bgcolor1' => '#666',
        'bgcolor2' => '#777',
        'bgcolor3' => '#444',
        'bgcolor4' => '#999'
    );

    // On check si les bgcolor on �t� d�fini sinon on les d�fini
    foreach ($arrayDefaultColor as $color => $value) {
        if(!isset($GLOBALS[$color])){
            $GLOBALS[$color] = $value;
        }
    }

    // On cr�er une balise style avec toutes les classes pour les bgcolors
    $output = '<style type="text/css">';

    for ($i = 1;$i <= 4;$i++){
        // On d�finit une classe pour une couleur de fond
        $output .= '.nkBgColor'.$i.'{background:'.$GLOBALS['bgcolor'.$i].';}'."\n";
        // On d�finit une class pour une couleur de bordure
        $output .= '.nkBorderColor'.$i.'{border-color:'.$GLOBALS['bgcolor'.$i].' !important;}'."\n";
    }

    $output .= '</style>';

    return $output;
}