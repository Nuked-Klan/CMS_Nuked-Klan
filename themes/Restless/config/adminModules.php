<?php

$dbsModules = 'SELECT nom
               FROM '.MODULES_TABLE.'
               WHERE niveau >= 0';
$dbeModules = nkDB_execute($dbsModules);

$arrayTemp = array(
    'User' => array(
        'name' => defined('MODULE_'.strtoupper('User')) ? constant('MODULE_'.strtoupper('User')) : 'User',
        'fullPage' => in_array('User', explode(',', $this->get('cfg')->get('general.displayFullPage'))),
        'slider' => in_array('User', explode(',', $this->get('cfg')->get('general.displaySlider'))),
        'article' => in_array('User', explode(',', $this->get('cfg')->get('general.displayArticle')))

    )
);

while($dbrModules = nkDB_fetchAssoc($dbeModules)){
    if($dbrModules['nom'] != 'Comment' && $dbrModules['nom'] != 'Vote') {
        $name = $dbrModules['nom'];
        if (defined('MODULE_'.strtoupper($dbrModules['nom']))) {
            $name = constant('MODULE_'.strtoupper($dbrModules['nom']));
        }

        $arrayTemp[$dbrModules['nom']] = array(
            'name'     => $name,
            'fullPage' => in_array($dbrModules['nom'], explode(',', $this->get('cfg')->get('general.displayFullPage'))),
            'slider'   => in_array($dbrModules['nom'], explode(',', $this->get('cfg')->get('general.displaySlider'))),
            'article'   => in_array($dbrModules['nom'], explode(',', $this->get('cfg')->get('general.displayArticle')))
        );
    }
}

asort($arrayTemp);

$this->assign('arrayModules', $arrayTemp);