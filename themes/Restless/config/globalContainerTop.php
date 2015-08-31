<?php

$this->assign('bigClass', null);

if (FULLPAGE === true) {
    $this->assign('bigClass', 'Big');
}

$blockUnikFull = false;

if($this->get('cfg')->get('blockArticle.fullPage') == 1 && SHOW_ARTICLE){
    $blockUnikFull = true;
}

$this->assign('blockUnikFull', $blockUnikFull);