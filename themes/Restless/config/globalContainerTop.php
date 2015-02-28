<?php

if(in_array($_REQUEST['file'], $GLOBALS['arrayBigModules'])){
    $this->bigClass = 'Big';
}
else{
    $this->bigClass = '';
}