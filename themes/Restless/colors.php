<?php

require_once 'themes/Restless/librairies/iniConfigTool.php';

$cfg = new iniConfigTool('themes/Restless/config.ini');

$currentColor = $cfg->get('general.color');

$arrayBgColor = array(
    'blue' => '#2980B9',
    'green' => '#27AE60',
    'gold' => '#CFA634',
    'orange' => '#F39C3B',
    'red' => '#C0392B',
    'purple' => '#8E44AD'
);

$bgcolor1 = "#F6F5F5";
$bgcolor2 = "#FFFFFF";
$bgcolor3 = "#F39C3B";
$bgcolor4 = "#F6F5F5";

if(array_key_exists($currentColor, $arrayBgColor)){
    $bgcolor3 = $arrayBgColor[$currentColor];
}