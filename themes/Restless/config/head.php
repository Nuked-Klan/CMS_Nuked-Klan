<?php

$this->assign('siteTitle', $GLOBALS['nuked']['name'].' - '.$GLOBALS['nuked']['slogan']);

$this->assign('siteKeywords', $GLOBALS['nuked']['keyword']);

$this->assign('siteDescription', $GLOBALS['nuked']['description']);

$this->assign('backgroundPosition', $this->get('cfg')->get('general.backgroundPosition'));

$this->assign('backgroundImage', $this->get('cfg')->get('general.backgroundImage'));

$this->assign('mainLogoPosition', $this->get('cfg')->get('general.mainLogoPosition'));

$this->assign('mainLogoMargin', $this->get('cfg')->get('general.mainLogoMargin'));

$this->assign('styleColor', 'orange');

$temp = $this->get('cfg')->get('general.color');

if(in_array($temp, array('orange', 'red', 'blue', 'purple', 'green', 'gold'))){
    $this->assign('styleColor', $temp);
}
