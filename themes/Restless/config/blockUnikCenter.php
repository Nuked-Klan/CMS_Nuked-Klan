<?php

$this->assign('blockArticleActive', $this->get('cfg')->get('blockArticle.active'));

$maxElements = 3;
if($this->get('cfg')->get('blockArticle.fullPage') == 1){
    $maxElements = 4;
}

$dbsArticle = 'SELECT coverage as image, title, date, artid, autor
                 FROM '.SECTIONS_TABLE.'
                 ORDER BY date DESC
                 LIMIT 0, '.$maxElements;

$dbeArticle = mysql_query($dbsArticle) or die(mysql_error());

$arrayTemp = array();
$i = 0;

while ($dbrArticle = mysql_fetch_assoc($dbeArticle)) {
    $arrayTemp[$i]['image'] = empty($dbrArticle['image']) ? 'themes/Restless/images/no_image_articles.png' : $dbrArticle['image'];
    $arrayTemp[$i]['title'] = $dbrArticle['title'];
    $arrayTemp[$i]['postedBy'] = POSTEDBY.' '.$dbrArticle['autor'].' '.THE.' '.date('d/m/Y', $dbrArticle['date']);
    $arrayTemp[$i]['link'] = 'index.php?file=Sections&op=article&artid='.$dbrArticle['artid'];
    $i++;
}

if($this->get('cfg')->get('blockArticle.fullPage') == 1){
    $classUnikCenter = 'class="RL_blockUnikCenter'.count($arrayTemp).' RL_blockUnikCenterFull"';
}


$this->assign('blockArticleContent', $arrayTemp);

$this->assign('nbArticles', count($this->get('blockArticleContent')));

$this->assign('classUnikCenter', $classUnikCenter);