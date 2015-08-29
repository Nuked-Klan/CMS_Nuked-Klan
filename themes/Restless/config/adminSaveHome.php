<?php

const PROFILES_DIR = 'themes/Restless/presets/';

if(array_key_exists('profileName', $_REQUEST)){
    $configFile = PROFILES_DIR.$_REQUEST['profileName'].'.ini';
    if(file_exists($configFile)){
        $updateCfg = new iniConfigTool($configFile);

        $updateCfg->save('themes/Restless/config.ini');
    }
    else{
        echo 'NO FILE';
    }
}
else{
    echo 'NO PROFILE';
}

