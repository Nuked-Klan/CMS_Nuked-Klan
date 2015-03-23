<?php
$this->assign('captcha', false);

$captcha = initCaptcha();

if((isset($_SESSION['captcha']) && $_SESSION['captcha'] === true) || $catpcha === true){
    $this->assign('captcha', true);
}