<?php

$i = 0;
$arrayTemp = array();

$elementWidth = 620;
if($this->get('cfg')->get('blockTopMatch.active') != 1){
    $elementWidth = 1040;
}

$dbsSlider = 'SELECT id, titre, coverage
              FROM '.NEWS_TABLE.'
              WHERE coverage != "" ';
$dbeSlider = mysql_query($dbsSlider);

while ($dbrSlider = mysql_fetch_assoc($dbeSlider)) {
    $arrayTemp[$i]['link'] = 'index.php?file=News&amp;op=index_comment&news_id='.$dbrSlider['id'];
    $arrayTemp[$i]['title'] = $dbrSlider['titre'];
    $arrayTemp[$i]['src'] = $dbrSlider['coverage'];
    $arrayTemp[$i]['id'] = 'id="RL_sliderElement'.($i+1).'"';
    $arrayTemp[$i]['current'] = null;
    $i++;
}

$count = count($arrayTemp);

$this->assign('totalWidth', $count * $elementWidth);

$arrayLeft = array();

$maxLeft = intval('-'.($count - 1) * $elementWidth);

$j = 0;
for ($i = 0; $i >= $maxLeft; $i -= $elementWidth) {
    $arrayLeft[$j] = $i;
    $j++;
}

$rand = rand(0, (count($arrayLeft) - 1));

$arrayTemp[$rand]['current'] = 'class="RL_sliderCurrent"';

$this->assign('sliderImages', $arrayTemp);

$this->assign('initLeft', $arrayLeft[$rand]);

$this->assign('nbSliderImages', $count);

$this->assign('elementWidth', $elementWidth);