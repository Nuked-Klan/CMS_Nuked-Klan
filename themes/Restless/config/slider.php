<?php

$arrayColor = array('red', 'green', 'blue');

$i = 1;

$arrayTemp = array();

$dbsSlider = 'SELECT titre, coverage
              FROM '.NEWS_TABLE.'
              WHERE coverage != "" ';
$dbeSlider = mysql_query($dbsSlider);

while($dbrSlider = mysql_fetch_assoc($dbeSlider)){
    $arrayTemp[$i]['title'] = $dbrSlider['titre'];
    $arrayTemp[$i]['src'] = $dbrSlider['coverage'];
    $i++;
}

$this->assign('sliderImages', $arrayTemp);

$count = count($arrayTemp);

$this->assign('totalWidth', $count * 620);

$this->assign('nbSliderImages', $count);