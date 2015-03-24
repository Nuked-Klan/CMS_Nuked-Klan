<?php

$arrayPosition = array('left', 'center', 'right');

$arrayImage = array('mainLogo', 'backgroundImage');

$arrayColor = array('blue', 'green', 'orange', 'purple', 'red', 'gold');

$this->assign('adminSettingsError', false);

try {
    if(array_key_exists('backgroundPosition', $_REQUEST)){
        if(in_array($_REQUEST['backgroundPosition'], $arrayPosition)){
            $this->get('cfg')->set('general.backgroundPosition', $_REQUEST['backgroundPosition']);
        }
        else{
            throw new Exception(BAD_BACKGROUND_POSITION);
        }
    }

    if(array_key_exists('mainLogoPosition', $_REQUEST)){
        if(in_array($_REQUEST['mainLogoPosition'], $arrayPosition)){
            $this->get('cfg')->set('general.mainLogoPosition', $_REQUEST['mainLogoPosition']);
        }
        else{
            throw new Exception(BAD_MAIN_LOGO_POSITION);
        }
    }

    if(array_key_exists('mainLogoMargin', $_REQUEST)){
        $margin = intval($_REQUEST['mainLogoMargin']);

        if($margin >= 0 && $margin <= 150){
            $this->get('cfg')->set('general.mainLogoMargin', $margin);
        }
        else{
            throw new Exception(BAD_MAIN_LOGO_MARGIN);
        }
    }

    foreach($arrayImage as $name){
        if(!empty($_FILES[$name.'File']['name'])){
            RL_uploadFile($name.'File');
        }
        else if(array_key_exists($name.'Url', $_REQUEST)){
            $this->get('cfg')->set('general.'.$name, $_REQUEST[$name.'Url']);
        }
    }

    if(array_key_exists('styleColor', $_REQUEST)){
        if(in_array($_REQUEST['styleColor'], $arrayColor)){
            $this->get('cfg')->set('general.color', $_REQUEST['styleColor']);
        }
        else{
            throw new Exception(BAD_TEMPLATE_COLOR);
        }
    }

    if(array_key_exists('mainTitle', $_REQUEST)){
        $this->get('cfg')->set('general.mainTitle', $_REQUEST['mainTitle']);
    }

    $this->get('cfg')->save();
}
catch (Exception $e) {
    $this->assign('adminSettingsError', true);
    $this->assign('errorMessage', $e->getMessage());
}