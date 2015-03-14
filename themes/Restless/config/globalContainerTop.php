<?php

$this->assign('bigClass', null);

if (FULLPAGE === true) {
    $this->assign('bigClass', 'Big');
}