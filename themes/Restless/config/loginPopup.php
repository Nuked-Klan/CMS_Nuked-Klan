<?php
$this->captcha = false;

$captcha = initCaptcha();

if((isset($_SESSION['captcha']) && $_SESSION['captcha'] === true) || $catpcha === true){
    $this->captcha = true;
}