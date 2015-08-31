<?php

$arrayBlocks = array('TopMatch', 'Match', 'Team', 'Forum', 'Download', 'Guestbook', 'Article', 'Gallery', 'Social', 'About', 'Sponsors');

$temp = array();

foreach($arrayBlocks as $block) {
    $active = false;
    if($this->get('cfg')->get('block'.$block.'.active') == 1){
        $active = true;
    }

    $temp[$block] = array(
                        'checked' => $active,
                        'title'   => $this->get('cfg')->get('block'.$block.'.title')
                    );
}

$this->assign('arrayBlocks', $temp);