<?php

$activeTopMatch = $this->get('cfg')->get('blockTopMatch.active');

$this->assign('activeTopMatch', $activeTopMatch);

$classSliderFull = null;

if($activeTopMatch != 1){
    $classSliderFull = 'class="RL_sliderFull"';
}

$this->assign('classSliderFull', $classSliderFull);