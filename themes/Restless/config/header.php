<?php

$this->assign('title', $this->get('cfg')->get('general.mainTitle'));

$mainLogo = $this->get('cfg')->get('general.mainLogo');

$this->assign('displayLogo', false);

if(!empty($mainLogo)){
    $this->assign('mainLogo', $mainLogo);

    $this->assign('displayLogo', true);
}