<?php
/**
 * Media inclusions in Nuked-klan
 *
 * Include JS and CSS file from Mods and templates
 *
 * @version     1.8
 * @link http://www.nuked-klan.org Clan Management System for Gamers
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2001-2013 Nuked-Klan (Registred Trademark)
 */

function printMedias(){
    // Vérification des variables de request
    if(array_key_exists('file', $_REQUEST)){
        $file = $_REQUEST['file'];
    }
    else{
        $file = '';
    }

    // Vérification de la présence de marqueur de génération
    // Permet de ne pas recharger un css ou un js si un block le demande alors qu'ils ont déjà été chargés par le module
    if(array_key_exists('mediaPrinted', $GLOBALS['nuked'])){
        // Si le marqueur existe on le stocke temporairement
        $mediasPrinted = $GLOBALS['mediaPrinted'];
    }
    else{
        $mediasPrinted = array();
    }

    // Définition du chemin vers les fichiers de modules
    $pathJsMods = 'modules/'.$file.'/'.$file.'.js';
    $pathCssMods = 'modules/'.$file.'/'.$file.'.css';

    // Définition du chemin vers les fichiers du themes
    $pathJsTemplate = 'themes/'.$GLOBALS['theme'].'/js/modules/'.$file.'.js';
    $pathCssTemplate = 'themes/'.$GLOBALS['theme'].'/css/modules/'.$file.'.css';

    // Définition des cheminds vers les fichiers par défaut
    $pathJsDefault = 'media/js/nkDefault.js';
    $pathCssDefault = 'media/css/nkDefault.css';

    // On stocke les paths dans un ordre bien précis Default -> Mods -> Templates afin de permettre la surcharge des propriétés css
    $arrayMedias = array(
                    'CSS' => array($pathCssDefault, $pathCssMods, $pathCssTemplate),
                    'JS'  => array($pathJsDefault, $pathJsMods, $pathJsTemplate)
                );

    // On initialise la sortie
    $output = '';

    // On ajout le chargement de jquery avant les autres scripts
    $output = getJquery();

    // On parcours le tableaux des paths et on génère la sortie html
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

    // On retourne la sortie pour affichage
    return $output;
}

function displayMedias(){
    if(function_exists('head')){
        // Si la function head est défini dans le theme.php (themes de la version 1.8)
        head();

        echo printMedias();

        top();
    }
    else{
        // Sinon on conserve la compatibilité avec les anciens thèmes
        top();

        echo printMedias();
    }
}

function getJquery(){
?>
    <script type="text/javascript">
        if(typeof jQuery == 'undefined'){
            document.write('\x3Cscript type="text/javascript" src="media/js/jquery-min-1.8.3.js">\x3C/script>');
        }
    </script>

<?php
}

?>
