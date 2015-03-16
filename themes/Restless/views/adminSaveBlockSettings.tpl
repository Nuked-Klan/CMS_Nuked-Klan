<?php
    if($this->get('adminBlocksError') === false){
        printNotification(SUCCESS_BLOCK_EDIT, 'index.php?file=Admin&page=theme&op=blocks_management', 'success', false, true);
    }
    else{
        printNotification($this->get('errorMessage'), 'index.php?file=Admin&page=theme&op=blocks_management', 'error', true, false);
    }