<?php
    if($this->get('adminModulesError') === false){
        printNotification(SUCCESS_MODULE_EDIT, 'success');
        redirect('index.php?file=Admin&page=theme&op=modules_management', 2);
    }
    else{
        printNotification($this->get('errorMessage'), 'error');
        redirect('index.php?file=Admin&page=theme&op=modules_management', 2);
    }