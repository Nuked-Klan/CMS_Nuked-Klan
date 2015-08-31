<?php
$dbsModules = 'SELECT nom
           FROM '.MODULES_TABLE.'
           WHERE niveau >= 0';
$dbeModules = mysql_query($dbsModules);

$arrayFullPage = array();
$arraySlider = array();

while($dbrModules = mysql_fetch_assoc($dbeModules)){
    if(array_key_exists('module'.$dbrModules['nom'].'FullPage', $_REQUEST)){
        $arrayFullPage[] = $dbrModules['nom'];
    }

    if(array_key_exists('module'.$dbrModules['nom'].'Slider', $_REQUEST)){
        $arraySlider[] = $dbrModules['nom'];
    }

    if(array_key_exists('module'.$dbrModules['nom'].'Article', $_REQUEST)){
        $arrayArticle[] = $dbrModules['nom'];
    }
}

if(array_key_exists('moduleUserFullPage', $_REQUEST)){
    $arrayFullPage[] = 'User';
}

if(array_key_exists('moduleUserSlider', $_REQUEST)){
    $arraySlider[] = 'User';
}

if(array_key_exists('moduleUserArticle', $_REQUEST)){
    $arrayArticle[] = 'User';
}

$this->get('cfg')->set('general.displayFullPage', implode(',', $arrayFullPage));
$this->get('cfg')->set('general.displaySlider', implode(',', $arraySlider));
$this->get('cfg')->set('general.displayArticle', implode(',', $arrayArticle));

$this->get('cfg')->save();
