<?php
    if($this->get('adminModulesError') === false){
        printNotification(SUCCESS_MODULE_EDIT, 'index.php?file=Admin&page=theme&op=modules_management', 'success', false, true);
    }
    else{
        printNotification($this->get('errorMessage'), 'index.php?file=Admin&page=theme&op=modules_management', 'error', true, false);
    }