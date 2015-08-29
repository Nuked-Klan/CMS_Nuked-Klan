<?php

$this->assign('mainTitle', $this->get('cfg')->get('general.mainTitle'));

$this->assign('mainLogo', $this->get('cfg')->get('general.mainLogo'));

$this->assign('mainLogoMargin', $this->get('cfg')->get('general.mainLogoMargin'));

$this->assign('backgroundImage', $this->get('cfg')->get('general.backgroundImage'));

$this->assign('styleColor', $this->get('cfg')->get('general.color'));

$arrayPosition = array('left', 'center', 'right');

$arrayTemp = array();

$i = 0;
foreach($arrayPosition as $position){
    $selected = null;
    if($position == $this->get('cfg')->get('general.backgroundPosition')){
        $selected = ' selected="selected" ';
    }

    $arrayTemp[$i]['text'] = constant(strtoupper($position));
    $arrayTemp[$i]['value'] = $position;
    $arrayTemp[$i]['selected'] = $selected;

    $i++;
}

$this->assign('backgroundPosition', $arrayTemp);

$arrayTemp = array();

$i = 0;
foreach($arrayPosition as $position){
    $selected = null;
    if($position == $this->get('cfg')->get('general.mainLogoPosition')){
        $selected = ' selected="selected" ';
    }

    $arrayTemp[$i]['text'] = constant(strtoupper($position));
    $arrayTemp[$i]['value'] = $position;
    $arrayTemp[$i]['selected'] = $selected;

    $i++;
}

$this->assign('mainLogoPosition', $arrayTemp);

$arrayColor = array('blue', 'green', 'orange', 'purple', 'red', 'gold');

$arrayTemp = array();

$i = 0;

foreach($arrayColor as $color){
    $selected = null;
    if($color == $this->get('cfg')->get('general.color')){
        $selected = ' selected="selected" ';
    }

    $arrayTemp[$i]['value'] = $color;
    $arrayTemp[$i]['text'] = constant(strtoupper($color));
    $arrayTemp[$i]['selected'] = $selected;
    $i++;
}

$this->assign('selectColor', $arrayTemp);
$this->assign('currentColor', ' RL_select_'.$this->get('cfg')->get('general.color'));