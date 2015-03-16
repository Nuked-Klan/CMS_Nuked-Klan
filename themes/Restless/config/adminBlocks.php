<?php

$arrayBlocks = array('Match', 'Team', 'Forum', 'Download', 'Guestbook', 'Article', 'Gallery', 'Social');

$temp = array();

foreach($arrayBlocks as $block) {
    $active = false;
    if($this->get('cfg')->get('block'.$block.'.active') == 1){
        $active = true;
    }

    $temp[$block] = $active;
}

$this->assign('arrayBlocks', $temp);