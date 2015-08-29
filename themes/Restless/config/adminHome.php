<?php

$arrayPresets =  array_diff(scandir('themes/Restless/presets'), array('..', '.', 'default.ini'));

$arrayTemp = array();

$i = 0;
foreach($arrayPresets as $preset){
    $arrayTemp[$i]['value'] = substr($preset, 0, -4);
    $arrayTemp[$i]['text'] = str_replace('_', ' ', substr($preset, 0, -4));
    $i++;
}

array_unshift($arrayTemp, array(
    'value' => 'default',
    'text' => BY_DEFAULT
));

$this->assign('profilesList', $arrayTemp);